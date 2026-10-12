<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;

class AccountController {
    public static function create_transaction($request) {
        if (!SM_Permissions::sm_user_can('accounts.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        return rest_ensure_response(['success' => true]);
    }
}
