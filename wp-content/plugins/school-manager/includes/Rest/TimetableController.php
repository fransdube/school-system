<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Permissions;

class TimetableController {
    public static function get_timetable($request) {
        if (!SM_Permissions::sm_user_can('timetable.view')) {
            return new \WP_Error('forbidden', 'Permission denied', ['status' => 403]);
        }
        return rest_ensure_response([]);
    }
}
