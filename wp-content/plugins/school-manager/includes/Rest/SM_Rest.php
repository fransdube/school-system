<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;

class SM_Rest {
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        // Auth routes
        register_rest_route( 'sm/v1', '/signup', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AuthController', 'signup' ],
            'permission_callback' => '__return_true'
        ] );

        register_rest_route( 'sm/v1', '/verify-email', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\AuthController', 'verify_email' ],
            'permission_callback' => '__return_true'
        ] );

        register_rest_route( 'sm/v1', '/login', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AuthController', 'login' ],
            'permission_callback' => '__return_true'
        ] );

        // Portal routes
        register_rest_route( 'sm/v1', '/dashboard', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\DashboardController', 'get_dashboard' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        register_rest_route( 'sm/v1', '/settings', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\SettingsController', 'get_settings' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Academic routes
        register_rest_route( 'sm/v1', '/academic/classes', [
            [
                'methods'  => 'GET',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'get_classes' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ],
            [
                'methods'  => 'POST',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'create_class' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ]
        ] );

        register_rest_route( 'sm/v1', '/academic/classes/(?P<id>\d+)', [
            [
                'methods'  => 'PUT',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'update_class' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ],
            [
                'methods'  => 'DELETE',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'delete_class' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ]
        ] );

        register_rest_route( 'sm/v1', '/academic/sections', [
            [
                'methods'  => 'GET',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'get_sections' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ],
            [
                'methods'  => 'POST',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'create_section' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ]
        ] );

        register_rest_route( 'sm/v1', '/academic/subjects', [
            [
                'methods'  => 'GET',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'get_subjects' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ],
            [
                'methods'  => 'POST',
                'callback' => [ '\SchoolManager\Rest\AcademicController', 'create_subject' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ]
        ] );

        register_rest_route( 'sm/v1', '/academic/assign-subjects', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AcademicController', 'assign_subjects' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Students routes
        register_rest_route( 'sm/v1', '/students', [
            [
                'methods'  => 'GET',
                'callback' => [ '\SchoolManager\Rest\StudentController', 'get_students' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ],
            [
                'methods'  => 'POST',
                'callback' => [ '\SchoolManager\Rest\StudentController', 'create_student' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ]
        ] );

        // Employees routes
        register_rest_route( 'sm/v1', '/employees', [
            [
                'methods'  => 'GET',
                'callback' => [ '\SchoolManager\Rest\EmployeeController', 'get_employees' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ],
            [
                'methods'  => 'POST',
                'callback' => [ '\SchoolManager\Rest\EmployeeController', 'create_employee' ],
                'permission_callback' => [ __CLASS__, 'check_permission' ]
            ]
        ] );

        // Attendance routes
        register_rest_route( 'sm/v1', '/attendance', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AttendanceController', 'mark_student_attendance' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Fees routes
        register_rest_route( 'sm/v1', '/fees', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\FeeController', 'create_invoice' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Accounts routes
        register_rest_route( 'sm/v1', '/accounts', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\AccountController', 'create_transaction' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Timetable routes
        register_rest_route( 'sm/v1', '/timetable', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\TimetableController', 'get_timetable' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Homework routes
        register_rest_route( 'sm/v1', '/homework', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\HomeworkController', 'create_homework' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Exam routes
        register_rest_route( 'sm/v1', '/exams', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\ExamController', 'create_exam' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Payroll routes
        register_rest_route( 'sm/v1', '/payroll', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\PayrollController', 'create_payroll_run' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Messaging routes
        register_rest_route( 'sm/v1', '/messaging', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\MessageController', 'send_message' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Certificates routes
        register_rest_route( 'sm/v1', '/certificates', [
            'methods'  => 'POST',
            'callback' => [ '\SchoolManager\Rest\CertificateController', 'create_certificate' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

        // Reports routes
        register_rest_route( 'sm/v1', '/reports', [
            'methods'  => 'GET',
            'callback' => [ '\SchoolManager\Rest\ReportController', 'get_report' ],
            'permission_callback' => [ __CLASS__, 'check_permission' ]
        ] );

    }

    public static function check_permission() {
        if ( ! is_user_logged_in() ) {
            return new \WP_Error( 'unauthorized', 'You must be logged in', [ 'status' => 401 ] );
        }

        if ( ! SM_Tenant::current_school_id() ) {
            return new \WP_Error( 'forbidden', 'No school context', [ 'status' => 403 ] );
        }

        return true;
    }
}
