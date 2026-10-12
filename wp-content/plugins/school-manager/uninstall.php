<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Opt-in data removal only.
$remove_data = get_option( 'sm_remove_data_on_uninstall' );
if ( $remove_data ) {
    global $wpdb;
    $tables = [
        "{$wpdb->prefix}sm_schools",
        "{$wpdb->prefix}sm_user_links",
        "{$wpdb->prefix}sm_roles",
        "{$wpdb->prefix}sm_role_permissions",
        "{$wpdb->prefix}sm_school_settings",
        "{$wpdb->prefix}sm_academic_years",
        "{$wpdb->prefix}sm_terms",
        "{$wpdb->prefix}sm_audit_logs",
        "{$wpdb->prefix}sm_login_history",
        "{$wpdb->prefix}sm_plans",
        "{$wpdb->prefix}sm_plan_features",
        "{$wpdb->prefix}sm_subscriptions",
    ];

    foreach ( $tables as $table ) {
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
    }

    delete_option( 'sm_db_version' );
    delete_option( 'sm_remove_data_on_uninstall' );
}
