<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;

class SM_Rest {
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        // Auth routes
        register_rest_route( 'sm/v1', '/signup', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AuthController', 'signup' ],
            'permission_callback' => '__return_true'
        ] );

        register_rest_route( 'sm/v1', '/verify-email', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\AuthController', 'verify_email' ],
            'permission_callback' => '__return_true'
        ] );

        register_rest_route( 'sm/v1', '/login', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AuthController', 'login' ],
            'permission_callback' => '__return_true'
        ] );

        // Portal routes
        register_rest_route( 'sm/v1', '/dashboard', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\DashboardController', 'get_dashboard' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        register_rest_route( 'sm/v1', '/settings', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\SettingsController', 'get_settings' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );
    }

    public static function check_permission() {
        if ( ! is_user_logged_in() ) {
            return new \WP_Error( 'unauthorized', 'You must be logged in', [ 'status' => 401 ] );
        }

        if ( ! SM_Tenant::current_school_id() ) {
            return new \WP_Error( 'forbidden', 'No school context', [ 'status' => 403 ] );
        }

        return true;
    }
}
