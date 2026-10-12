<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;

class PayrollController {
    public static function create_payroll_run($request) {
        if (!SM_Permissions::sm_user_can('payroll.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        return rest_ensure_response(['success' => true]);
    }
}
