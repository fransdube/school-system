<?php
namespace SchoolManager\Database;

class Migrator {
    const DB_VERSION = '1.0.0';

    public static function run() {
        $installed_version = get_option( 'sm_db_version' );

        if ( $installed_version !== self::DB_VERSION ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $tables = Schema::get_tables();

            foreach ( $tables as $sql ) {
                dbDelta( $sql );
            }

            self::seed_initial_data();

            update_option( 'sm_db_version', self::DB_VERSION );
        }
    }

    private static function seed_initial_data() {
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        // Seed basic free plan if not exists
        $plan = $wpdb->get_row( "SELECT * FROM {$prefix}plans WHERE name = 'Free'" );
        if ( ! $plan ) {
            $wpdb->insert(
                "{$prefix}plans",
                [
                    'name' => 'Free',
                    'price' => 0.00,
                    'student_limit' => 50,
                    'staff_limit' => 5,
                    'created_at' => current_time( 'mysql' ),
                    'updated_at' => current_time( 'mysql' )
                ]
            );
            $plan_id = $wpdb->insert_id;

            // basic features
            $features = ['dashboard', 'general_settings'];
            foreach ($features as $f) {
                $wpdb->insert("{$prefix}plan_features", ['plan_id' => $plan_id, 'feature_key' => $f]);
            }
        }
    }
}
