<?php
/**
 * Plugin Name: School Manager
 * Description: Multi-school (multi-tenant) school management system.
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: school-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

define( 'SM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SM_VERSION', '1.0.0' );

require_once SM_PLUGIN_DIR . 'includes/Autoloader.php';
\SchoolManager\Autoloader::register();

// Activation
register_activation_hook( __FILE__, [ '\SchoolManager\Core\Activator', 'activate' ] );

// Deactivation
register_deactivation_hook( __FILE__, [ '\SchoolManager\Core\Deactivator', 'deactivate' ] );

// Initialize
add_action( 'plugins_loaded', function() {
    \SchoolManager\Core\Bootstrap::init();
});
