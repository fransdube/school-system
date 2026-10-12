<?php
namespace SchoolManager\Core;

class SM_Settings {
    public static function get( $key, $default = null ) {
        $school_id = SM_Tenant::current_school_id();
        if ( ! $school_id ) {
            return $default;
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT setting_value FROM {$prefix}school_settings WHERE school_id = %d AND setting_key = %s",
            $school_id, $key
        ) );

        if ( $value === null ) {
            return $default;
        }

        return maybe_unserialize( $value );
    }

    public static function set( $key, $value ) {
        $school_id = SM_Tenant::current_school_id();
        if ( ! $school_id ) {
            return false;
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $serialized_value = maybe_serialize( $value );

        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$prefix}school_settings WHERE school_id = %d AND setting_key = %s",
            $school_id, $key
        ) );

        if ( $existing ) {
            $wpdb->update(
                "{$prefix}school_settings",
                [ 'setting_value' => $serialized_value, 'updated_at' => current_time( 'mysql' ) ],
                [ 'id' => $existing ]
            );
        } else {
            $wpdb->insert(
                "{$prefix}school_settings",
                [
                    'school_id' => $school_id,
                    'setting_key' => $key,
                    'setting_value' => $serialized_value,
                    'created_at' => current_time( 'mysql' ),
                    'updated_at' => current_time( 'mysql' )
                ]
            );
        }

        return true;
    }
}
