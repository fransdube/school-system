<?php
namespace SchoolManager\Core;

class SM_Audit {
    public static function log( $action, $entity_type, $entity_id, $old_values = null, $new_values = null ) {
        $school_id = SM_Tenant::current_school_id();
        $user_id = get_current_user_id();

        if ( ! $school_id || ! $user_id ) {
            return;
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $wpdb->insert(
            "{$prefix}audit_logs",
            [
                'school_id' => $school_id,
                'user_id' => $user_id,
                'action' => $action,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'old_values' => $old_values ? maybe_serialize( $old_values ) : null,
                'new_values' => $new_values ? maybe_serialize( $new_values ) : null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'created_at' => current_time( 'mysql' )
            ]
        );
    }
}
