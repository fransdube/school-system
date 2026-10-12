<?php
namespace SchoolManager\Rest;

use SchoolManager\Core\SM_Tenant;
use SchoolManager\Core\SM_Settings;

class SettingsController {
    public static function get_settings( $request ) {
        $school_id = SM_Tenant::current_school_id();

        return rest_ensure_response([
            'profile' => SM_Settings::get('institute_profile', []),
        ]);
    }
}
