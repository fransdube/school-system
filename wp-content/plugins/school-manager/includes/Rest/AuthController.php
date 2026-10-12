<?php
namespace SchoolManager\Rest;

class AuthController {
    public static function signup( $request ) {
        $params = $request->get_params();

        // Rate limit by IP (simple option based approach for now)
        $ip = $_SERVER['REMOTE_ADDR'];
        $transient_key = 'sm_signup_limit_' . md5($ip);
        if (get_transient($transient_key)) {
             return new \WP_Error( 'rate_limited', 'Too many requests. Try again later.', [ 'status' => 429 ] );
        }
        set_transient($transient_key, 1, 60);

        // Honeypot check (assume form includes a hidden field named 'website')
        if ( !empty($params['website']) ) {
            return new \WP_Error( 'spam_detected', 'Spam detected.', [ 'status' => 400 ] );
        }

        // Prevent duplicate emails
        if ( email_exists( $params['email'] ) ) {
            return new \WP_Error( 'email_exists', 'Email is already registered.', [ 'status' => 400 ] );
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $wpdb->query( 'START TRANSACTION' );

        try {
            // 1. Create School
            $wpdb->insert(
                "{$prefix}schools",
                [
                    'name' => sanitize_text_field( $params['school_name'] ),
                    'admin_email' => sanitize_email( $params['email'] ),
                    'phone' => sanitize_text_field( $params['phone'] ),
                    'status' => 'pending_verification',
                    'created_at' => current_time( 'mysql' ),
                    'updated_at' => current_time( 'mysql' )
                ]
            );
            $school_id = $wpdb->insert_id;

            // 2. Create WP User
            $user_id = wp_insert_user( [
                'user_login' => $params['email'],
                'user_email' => $params['email'],
                'user_pass'  => $params['password'],
                'first_name' => sanitize_text_field( $params['admin_name'] ),
                'role'       => 'sm_school_admin'
            ] );

            if ( is_wp_error( $user_id ) ) {
                throw new \Exception( $user_id->get_error_message() );
            }

            // 3. Link user to school
            update_user_meta( $user_id, 'sm_school_id', $school_id );

            // Generate verification token
            $token = wp_generate_password(20, false);
            update_user_meta( $user_id, 'sm_verification_token', $token );

            $wpdb->insert(
                "{$prefix}user_links",
                [
                    'wp_user_id' => $user_id,
                    'school_id' => $school_id,
                    'person_type' => 'admin',
                    'created_at' => current_time( 'mysql' )
                ]
            );

            // 4. Create Subscription
            $free_plan = $wpdb->get_row( "SELECT id FROM {$prefix}plans WHERE name = 'Free'" );
            if ( $free_plan ) {
                $wpdb->insert(
                    "{$prefix}subscriptions",
                    [
                        'school_id' => $school_id,
                        'plan_id' => $free_plan->id,
                        'status' => 'active',
                        'start_date' => current_time( 'mysql' ),
                        'created_at' => current_time( 'mysql' ),
                        'updated_at' => current_time( 'mysql' )
                    ]
                );
            }

            $wpdb->query( 'COMMIT' );

            // Send email
            $verify_link = rest_url('sm/v1/verify-email?token=' . $token . '&user_id=' . $user_id);
            wp_mail($params['email'], 'Verify your School Manager Account', "Click here to verify: $verify_link");

            return rest_ensure_response( [ 'success' => true, 'message' => 'School created successfully. Please check your email to verify your account.' ] );

        } catch ( \Exception $e ) {
            $wpdb->query( 'ROLLBACK' );
            return new \WP_Error( 'signup_failed', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    public static function verify_email( $request ) {
        $user_id = intval($request->get_param('user_id'));
        $token = $request->get_param('token');

        $saved_token = get_user_meta($user_id, 'sm_verification_token', true);
        if ($saved_token && $saved_token === $token) {
            delete_user_meta($user_id, 'sm_verification_token');

            global $wpdb;
            $prefix = $wpdb->prefix . 'sm_';
            $school_id = get_user_meta($user_id, 'sm_school_id', true);
            $wpdb->update("{$prefix}schools", ['status' => 'active', 'updated_at' => current_time('mysql')], ['id' => $school_id]);

            wp_redirect(site_url('/school-login/?verified=1'));
            die;
        }

        wp_die('Invalid or expired token.');
    }

    public static function login( $request ) {
        $params = $request->get_params();
        $username = $params['username'];

        // Lockout check
        $lockout_key = 'sm_lockout_' . sanitize_title($username);
        $attempts = get_transient($lockout_key) ?: 0;
        if ($attempts >= 5) {
            return new \WP_Error( 'locked_out', 'Too many failed attempts. Try again in 15 minutes.', [ 'status' => 429 ] );
        }

        $user_obj = get_user_by('login', $username) ?: get_user_by('email', $username);

        if ($user_obj) {
            $school_id = get_user_meta($user_obj->ID, 'sm_school_id', true);
            if ($school_id) {
                global $wpdb;
                $prefix = $wpdb->prefix . 'sm_';
                $school_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$prefix}schools WHERE id = %d", $school_id));
                if ($school_status === 'suspended') {
                    return new \WP_Error( 'suspended', 'Your school account is suspended.', [ 'status' => 403 ] );
                }
                if ($school_status === 'pending_verification') {
                    return new \WP_Error( 'unverified', 'Please verify your email address first.', [ 'status' => 403 ] );
                }
            }
        }

        $creds = [
            'user_login'    => $username,
            'user_password' => $params['password'],
            'remember'      => ! empty( $params['remember'] )
        ];

        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            set_transient($lockout_key, $attempts + 1, 15 * 60);
            return new \WP_Error( 'login_failed', $user->get_error_message(), [ 'status' => 401 ] );
        }

        delete_transient($lockout_key);

        // Log login history
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';
        $school_id = get_user_meta($user->ID, 'sm_school_id', true);

        $wpdb->insert(
            "{$prefix}login_history",
            [
                'user_id' => $user->ID,
                'school_id' => $school_id ? $school_id : null,
                'status' => 'success',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'created_at' => current_time( 'mysql' )
            ]
        );

        return rest_ensure_response( [ 'success' => true, 'message' => 'Login successful' ] );
    }
}
