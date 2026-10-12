<?php
namespace SchoolManager\Shortcodes;

class SM_Shortcodes {
    public static function init() {
        add_shortcode( 'sm_signup', [ __CLASS__, 'signup_form' ] );
        add_shortcode( 'sm_login', [ __CLASS__, 'login_form' ] );
        add_shortcode( 'sm_portal', [ __CLASS__, 'portal_shell' ] );
    }

    public static function signup_form() {
        ob_start();
        ?>
        <div class="sm-signup-form">
            <h2>School Sign Up</h2>
            <form id="sm-signup-form" method="post">
                <input type="text" name="school_name" placeholder="School Name" required>
                <input type="text" name="admin_name" placeholder="Admin Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="password" name="password" placeholder="Password" required>
                <label>
                    <input type="checkbox" name="terms" required> I agree to terms
                </label>
                <?php wp_nonce_field( 'sm_signup_action', 'sm_signup_nonce' ); ?>
                <button type="submit">Sign Up</button>
            </form>
            <div id="sm-signup-message"></div>
        </div>
        <script>
        document.getElementById('sm-signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('<?php echo rest_url('sm/v1/signup'); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('sm-signup-message').innerText = data.message || data.code;
                if (data.success) {
                    setTimeout(() => window.location.href = '<?php echo site_url('/school-login/'); ?>', 2000);
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    public static function login_form() {
        if ( is_user_logged_in() ) {
            return '<p>You are already logged in. <a href="' . site_url('/portal/') . '">Go to Portal</a></p>';
        }
        ob_start();
        ?>
        <div class="sm-login-form">
            <h2>Login</h2>
            <form id="sm-login-form" method="post">
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                    <option value="student">Student</option>
                </select>
                <input type="text" name="username" placeholder="Username / Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <?php wp_nonce_field( 'sm_login_action', 'sm_login_nonce' ); ?>
                <button type="submit">Login</button>
            </form>
            <div id="sm-login-message"></div>
            <a href="#">Forgot Password?</a> | <a href="<?php echo site_url('/school-signup/'); ?>">Sign Up</a>
        </div>
        <script>
        document.getElementById('sm-login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('<?php echo rest_url('sm/v1/login'); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '<?php echo site_url('/portal/'); ?>';
                } else {
                    document.getElementById('sm-login-message').innerText = data.message || 'Login failed';
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    public static function portal_shell() {
        if ( ! is_user_logged_in() ) {
            wp_redirect( site_url('/school-login/') );
            die;
        }

        // Just output a div to mount our JS app
        return '<div id="sm-portal-app">Loading Portal...</div>';
    }
}
