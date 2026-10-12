<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;

class DashboardController {
    public static function get_dashboard( $request ) {
        $school_id = SM_Tenant::current_school_id();
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $school = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}schools WHERE id = %d", $school_id));

        $total_students = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}user_links WHERE school_id = %d AND person_type = 'student'", $school_id));
        $total_employees = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}user_links WHERE school_id = %d AND person_type != 'student' AND person_type != 'admin'", $school_id));

        $revenue = 0; // Requires accounts module tables
        $profit = 0; // Requires accounts module tables

        return rest_ensure_response([
            'school_name' => $school->name,
            'total_students' => (int) $total_students,
            'total_employees' => (int) $total_employees,
            'revenue' => $revenue,
            'profit' => $profit
        ]);
    }
}
