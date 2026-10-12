<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;
use SchoolManager\Core\SM_Audit;

class StudentController {

    public static function get_students( $request ) {
        if (!SM_Permissions::sm_user_can('students.view')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        $school_id = SM_Tenant::current_school_id();
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $students = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$prefix}students WHERE school_id = %d AND deleted_at IS NULL",
            $school_id
        ));

        return rest_ensure_response($students);
    }

    public static function create_student( $request ) {
        if (!SM_Permissions::sm_user_can('students.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        if (!SM_Permissions::sm_within_limit('students')) {
            return new \WP_Error('limit_reached', 'Plan limit reached for students. Please upgrade.', ['status' => 400]);
        }

        $school_id = SM_Tenant::current_school_id();
        $name = sanitize_text_field($request->get_param('name'));
        $reg_no = sanitize_text_field($request->get_param('registration_no'));
        $class_id = intval($request->get_param('class_id'));
        $date_of_admission = sanitize_text_field($request->get_param('date_of_admission'));
        $discount = floatval($request->get_param('discount_in_fee'));

        // ... other fields would be extracted here ...

        if (empty($name) || empty($reg_no) || !$class_id || empty($date_of_admission)) {
            return new \WP_Error('invalid_param', 'Required fields missing', ['status' => 400]);
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        // Check Reg No uniqueness
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}students WHERE school_id = %d AND registration_no = %s AND deleted_at IS NULL",
            $school_id, $reg_no
        ));
        if ($exists) {
            return new \WP_Error('duplicate', 'Registration number already exists', ['status' => 400]);
        }

        $wpdb->insert("{$prefix}students", [
            'school_id' => $school_id,
            'name' => $name,
            'registration_no' => $reg_no,
            'class_id' => $class_id,
            'date_of_admission' => $date_of_admission,
            'discount_in_fee' => $discount,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);

        $id = $wpdb->insert_id;
        SM_Audit::log('create', 'student', $id, null, ['name' => $name, 'reg_no' => $reg_no]);

        return rest_ensure_response(['success' => true, 'id' => $id]);
    }
}
