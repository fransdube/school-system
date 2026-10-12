<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;
use SchoolManager\Core\SM_Audit;

class EmployeeController {

    public static function get_employees( $request ) {
        if (!SM_Permissions::sm_user_can('employees.view')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        $school_id = SM_Tenant::current_school_id();
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $employees = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$prefix}employees WHERE school_id = %d AND deleted_at IS NULL",
            $school_id
        ));

        return rest_ensure_response($employees);
    }

    public static function create_employee( $request ) {
        if (!SM_Permissions::sm_user_can('employees.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        if (!SM_Permissions::sm_within_limit('staff')) {
            return new \WP_Error('limit_reached', 'Plan limit reached for staff. Please upgrade.', ['status' => 400]);
        }

        $school_id = SM_Tenant::current_school_id();
        $name = sanitize_text_field($request->get_param('name'));

        if (empty($name)) {
            return new \WP_Error('invalid_param', 'Name required', ['status' => 400]);
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $wpdb->insert("{$prefix}employees", [
            'school_id' => $school_id,
            'name' => $name,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);

        $id = $wpdb->insert_id;
        SM_Audit::log('create', 'employee', $id, null, ['name' => $name]);

        return rest_ensure_response(['success' => true, 'id' => $id]);
    }
}
