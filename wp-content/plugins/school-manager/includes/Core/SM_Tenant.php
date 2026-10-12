<?php
namespace SchoolManager\Core;

class SM_Tenant {
    public static function current_school_id() {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return 0;
        }

        // Fetch from user meta
        $school_id = get_user_meta( $user_id, 'sm_school_id', true );
        return (int) $school_id;
    }
}
