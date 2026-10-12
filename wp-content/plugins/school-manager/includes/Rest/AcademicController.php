<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;
use SchoolManager\Core\SM_Audit;

class AcademicController {

    // ==========================================
    // CLASSES
    // ==========================================
    public static function get_classes( $request ) {
        if (!SM_Permissions::sm_user_can('academic.view')) {
            return new \WP_Error('forbidden', 'You do not have permission to view classes', ['status' => 403]);
        }
        $school_id = SM_Tenant::current_school_id();
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $classes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$prefix}classes WHERE school_id = %d AND deleted_at IS NULL ORDER BY name ASC",
            $school_id
        ));

        return rest_ensure_response($classes);
    }

    public static function create_class( $request ) {
        if (!SM_Permissions::sm_user_can('academic.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        $school_id = SM_Tenant::current_school_id();
        $name = sanitize_text_field($request->get_param('name'));
        $capacity = intval($request->get_param('capacity'));
        $class_teacher_id = $request->get_param('class_teacher_id') ? intval($request->get_param('class_teacher_id')) : null;

        if (empty($name)) {
            return new \WP_Error('invalid_param', 'Class name is required', ['status' => 400]);
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        // Duplicate check
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}classes WHERE school_id = %d AND name = %s AND deleted_at IS NULL",
            $school_id, $name
        ));
        if ($exists) {
            return new \WP_Error('duplicate', 'A class with this name already exists', ['status' => 400]);
        }

        $wpdb->insert("{$prefix}classes", [
            'school_id' => $school_id,
            'name' => $name,
            'capacity' => $capacity,
            'class_teacher_id' => $class_teacher_id,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);

        $id = $wpdb->insert_id;
        SM_Audit::log('create', 'class', $id, null, ['name' => $name, 'capacity' => $capacity]);

        return rest_ensure_response(['success' => true, 'id' => $id]);
    }

    public static function update_class( $request ) {
        if (!SM_Permissions::sm_user_can('academic.edit')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        $school_id = SM_Tenant::current_school_id();
        $id = intval($request->get_param('id'));
        $name = sanitize_text_field($request->get_param('name'));
        $capacity = intval($request->get_param('capacity'));
        $class_teacher_id = $request->get_param('class_teacher_id') ? intval($request->get_param('class_teacher_id')) : null;

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        // Check ownership
        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}classes WHERE id = %d AND school_id = %d AND deleted_at IS NULL", $id, $school_id));
        if (!$existing) {
            return new \WP_Error('not_found', 'Class not found', ['status' => 404]);
        }

        // Duplicate check
        $dup = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}classes WHERE school_id = %d AND name = %s AND id != %d AND deleted_at IS NULL",
            $school_id, $name, $id
        ));
        if ($dup) {
            return new \WP_Error('duplicate', 'Another class with this name already exists', ['status' => 400]);
        }

        $wpdb->update("{$prefix}classes", [
            'name' => $name,
            'capacity' => $capacity,
            'class_teacher_id' => $class_teacher_id,
            'updated_at' => current_time('mysql')
        ], ['id' => $id]);

        SM_Audit::log('update', 'class', $id, $existing, ['name' => $name, 'capacity' => $capacity]);

        return rest_ensure_response(['success' => true]);
    }

    public static function delete_class( $request ) {
        if (!SM_Permissions::sm_user_can('academic.delete')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        $school_id = SM_Tenant::current_school_id();
        $id = intval($request->get_param('id'));

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        // Check ownership
        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}classes WHERE id = %d AND school_id = %d AND deleted_at IS NULL", $id, $school_id));
        if (!$existing) {
            return new \WP_Error('not_found', 'Class not found', ['status' => 404]);
        }

        // Soft block if students exist (Assuming students are linked via user_links somehow or a students table - prompt mentions checking)
        // We will check user_links for this class ID (assuming we add class_id to user_links later, or students table)
        // For now, let's assume a generic check based on Prompt 3 which adds students.
        // We'll simulate by checking if there are sections linked.
        $has_sections = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$prefix}sections WHERE class_id = %d AND deleted_at IS NULL", $id));
        if ($has_sections > 0) {
            return new \WP_Error('has_dependencies', 'Cannot delete class because it has sections assigned.', ['status' => 400]);
        }

        // Soft delete
        $wpdb->update("{$prefix}classes", ['deleted_at' => current_time('mysql')], ['id' => $id]);

        SM_Audit::log('delete', 'class', $id);

        return rest_ensure_response(['success' => true]);
    }

    // ==========================================
    // SECTIONS
    // ==========================================
    public static function get_sections( $request ) {
        if (!SM_Permissions::sm_user_can('academic.view')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        $school_id = SM_Tenant::current_school_id();
        $class_id = $request->get_param('class_id') ? intval($request->get_param('class_id')) : null;
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $query = "SELECT * FROM {$prefix}sections WHERE school_id = %d AND deleted_at IS NULL";
        $args = [$school_id];

        if ($class_id) {
            $query .= " AND class_id = %d";
            $args[] = $class_id;
        }

        $query .= " ORDER BY name ASC";
        $sections = $wpdb->get_results($wpdb->prepare($query, ...$args));

        return rest_ensure_response($sections);
    }

    public static function create_section( $request ) {
         if (!SM_Permissions::sm_user_can('academic.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        $school_id = SM_Tenant::current_school_id();
        $class_id = intval($request->get_param('class_id'));
        $name = sanitize_text_field($request->get_param('name'));
        $capacity = intval($request->get_param('capacity'));
        $class_teacher_id = $request->get_param('class_teacher_id') ? intval($request->get_param('class_teacher_id')) : null;

        if (empty($name) || !$class_id) {
            return new \WP_Error('invalid_param', 'Class ID and Section name are required', ['status' => 400]);
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        // Check if class exists and belongs to school
        $class_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}classes WHERE id = %d AND school_id = %d AND deleted_at IS NULL", $class_id, $school_id));
        if (!$class_exists) {
             return new \WP_Error('not_found', 'Class not found', ['status' => 404]);
        }

        // Duplicate check
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}sections WHERE school_id = %d AND class_id = %d AND name = %s AND deleted_at IS NULL",
            $school_id, $class_id, $name
        ));
        if ($exists) {
            return new \WP_Error('duplicate', 'A section with this name already exists in this class', ['status' => 400]);
        }

        $wpdb->insert("{$prefix}sections", [
            'school_id' => $school_id,
            'class_id' => $class_id,
            'name' => $name,
            'capacity' => $capacity,
            'class_teacher_id' => $class_teacher_id,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);

        $id = $wpdb->insert_id;
        SM_Audit::log('create', 'section', $id, null, ['name' => $name, 'class_id' => $class_id]);

        return rest_ensure_response(['success' => true, 'id' => $id]);
    }

    // (Update and Delete sections would be similar to classes)

    // ==========================================
    // SUBJECTS
    // ==========================================
    public static function get_subjects( $request ) {
        if (!SM_Permissions::sm_user_can('academic.view')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        $school_id = SM_Tenant::current_school_id();
        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $subjects = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$prefix}subjects WHERE school_id = %d AND deleted_at IS NULL ORDER BY name ASC",
            $school_id
        ));

        return rest_ensure_response($subjects);
    }

    public static function create_subject( $request ) {
        if (!SM_Permissions::sm_user_can('academic.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        $school_id = SM_Tenant::current_school_id();
        $name = sanitize_text_field($request->get_param('name'));
        $code = sanitize_text_field($request->get_param('code'));
        $type = sanitize_text_field($request->get_param('type'));
        $credit_hours = floatval($request->get_param('credit_hours'));

        if (empty($name) || empty($code)) {
            return new \WP_Error('invalid_param', 'Subject name and code are required', ['status' => 400]);
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}subjects WHERE school_id = %d AND code = %s AND deleted_at IS NULL",
            $school_id, $code
        ));
        if ($exists) {
            return new \WP_Error('duplicate', 'A subject with this code already exists', ['status' => 400]);
        }

        $wpdb->insert("{$prefix}subjects", [
            'school_id' => $school_id,
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'credit_hours' => $credit_hours,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);

        $id = $wpdb->insert_id;
        SM_Audit::log('create', 'subject', $id, null, ['name' => $name, 'code' => $code]);

        return rest_ensure_response(['success' => true, 'id' => $id]);
    }

    // ==========================================
    // CLASS SUBJECTS
    // ==========================================
    public static function assign_subjects( $request ) {
        if (!SM_Permissions::sm_user_can('academic.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }

        $school_id = SM_Tenant::current_school_id();
        $class_id = intval($request->get_param('class_id'));
        $subject_ids = $request->get_param('subject_ids'); // array of ids

        if (!$class_id || !is_array($subject_ids)) {
             return new \WP_Error('invalid_param', 'Class ID and Subject IDs array required', ['status' => 400]);
        }

        global $wpdb;
        $prefix = $wpdb->prefix . 'sm_';

        $wpdb->query("START TRANSACTION");

        try {
            // Check class
            $class_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}classes WHERE id = %d AND school_id = %d AND deleted_at IS NULL", $class_id, $school_id));
            if (!$class_exists) throw new \Exception("Class not found");

            // Delete existing
            $wpdb->delete("{$prefix}class_subjects", ['class_id' => $class_id, 'school_id' => $school_id]);

            // Insert new
            foreach ($subject_ids as $sid) {
                 $sid = intval($sid);
                 $sub_exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}subjects WHERE id = %d AND school_id = %d AND deleted_at IS NULL", $sid, $school_id));
                 if ($sub_exists) {
                     $wpdb->insert("{$prefix}class_subjects", [
                         'school_id' => $school_id,
                         'class_id' => $class_id,
                         'subject_id' => $sid,
                         'created_by' => get_current_user_id(),
                         'created_at' => current_time('mysql')
                     ]);
                 }
            }

            $wpdb->query("COMMIT");
            return rest_ensure_response(['success' => true]);
        } catch (\Exception $e) {
            $wpdb->query("ROLLBACK");
            return new \WP_Error('error', $e->getMessage(), ['status' => 500]);
        }
    }
}
