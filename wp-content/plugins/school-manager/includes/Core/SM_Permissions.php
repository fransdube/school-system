<?php
namespace SchoolManager\Core;

class SM_Permissions {
    public static function sm_user_can( $permission ) {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return false;
        }

        $user = get_userdata( $user_id );
        if ( in_array( 'sm_super_admin', (array) $user->roles ) ) {
            return true;
        }

        if ( in_array( 'sm_school_admin', (array) $user->roles ) ) {
            return true;
        }

        $school_id = SM_Tenant::current_school_id();
        if (!$school_id) return false;

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $role_keys = array_intersect(array_keys(wp_roles()->roles), (array) $user->roles);
        if (empty($role_keys)) return false;

        // Remove 'sm_' prefix to match our custom table role_key if we did it that way,
        // or just match exact if we store exact. For now, assume exact matching.
        $placeholders = implode(',', array_fill(0, count($role_keys), '%s'));

        $query = $wpdb->prepare("
            SELECT COUNT(*) FROM {$prefix}role_permissions rp
            JOIN {$prefix}roles r ON rp.role_id = r.id
            WHERE r.school_id = %d AND r.role_key IN ($placeholders) AND rp.permission = %s
        ", array_merge([$school_id], $role_keys, [$permission]));

        return $wpdb->get_var($query) > 0;
    }

    public static function sm_school_can( $module ) {
        $school_id = SM_Tenant::current_school_id();
        if (!$school_id) return false;

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $plan = $wpdb->get_row($wpdb->prepare("
            SELECT p.* FROM {$prefix}subscriptions s
            JOIN {$prefix}plans p ON s.plan_id = p.id
            WHERE s.school_id = %d AND s.status = 'active'
        ", $school_id));

        if (!$plan) return false;

        $features = $wpdb->get_col($wpdb->prepare("
            SELECT feature_key FROM {$prefix}plan_features WHERE plan_id = %d
        ", $plan->id));

        return in_array($module, $features);
    }

    public static function sm_within_limit($type) {
        $school_id = SM_Tenant::current_school_id();
        if (!$school_id) return false;

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $plan = $wpdb->get_row($wpdb->prepare("
            SELECT p.* FROM {$prefix}subscriptions s
            JOIN {$prefix}plans p ON s.plan_id = p.id
            WHERE s.school_id = %d AND s.status = 'active'
        ", $school_id));

        if (!$plan) return false;

        if ($type === 'students') {
            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}user_links WHERE school_id = %d AND person_type = 'student'", $school_id));
            return $count < $plan->student_limit;
        }

        if ($type === 'staff') {
            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}user_links WHERE school_id = %d AND person_type != 'student'", $school_id));
            return $count < $plan->staff_limit;
        }

        return true;
    }
}
