<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;

class FeeController {
    public static function create_invoice($request) {
        if (!SM_Permissions::sm_user_can('fees.add')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        return rest_ensure_response(['success' => true]);
    }
}
