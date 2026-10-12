<?php
namespace SchoolManager\Core;

class Bootstrap {
    public static function init() {
        \SchoolManager\Database\Migrator::run();

        \SchoolManager\Core\Security::init();
        \SchoolManager\Core\SM_Roles::init();
        \SchoolManager\Core\SM_Upload::init();
        \SchoolManager\Core\SM_Router::init();
        \SchoolManager\Shortcodes\SM_Shortcodes::init();
        \SchoolManager\Rest\SM_Rest::init();

        if ( is_admin() ) {
            \SchoolManager\Core\AdminPages::init();
        }

        // Hide admin bar for non-admins
        add_action('after_setup_theme', function() {
            if (!current_user_can('manage_options') && !is_admin()) {
                show_admin_bar(false);
            }
        });

        // Block wp-admin for non-admins
        add_action('admin_init', function() {
            if (!defined('DOING_AJAX') && !current_user_can('manage_options')) {
                wp_redirect(site_url('/portal/'));
                die;
            }
        });
    }
}
