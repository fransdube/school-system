<?php
namespace SchoolManager\Core;

class SM_Roles {
    public static function init() {
        self::register_wp_roles();
    }

    private static function register_wp_roles() {
        add_role( 'sm_super_admin', 'Super Admin', [ 'read' => true, 'manage_options' => true ] );
        add_role( 'sm_school_admin', 'School Admin', [ 'read' => true ] );
        add_role( 'sm_accountant', 'Accountant', [ 'read' => true ] );
        add_role( 'sm_teacher', 'Teacher', [ 'read' => true ] );
        add_role( 'sm_staff', 'Staff', [ 'read' => true ] );
        add_role( 'sm_parent', 'Parent', [ 'read' => true ] );
        add_role( 'sm_student', 'Student', [ 'read' => true ] );
    }
}
