<?php
namespace SchoolManager\Core;

class Activator {
    public static function activate() {
        \SchoolManager\Database\Migrator::run();

        // Create pages
        self::create_page( 'School Login', '[sm_login]', 'school-login' );
        self::create_page( 'School Signup', '[sm_signup]', 'school-signup' );
        self::create_page( 'Portal', '[sm_portal]', 'portal' );

        flush_rewrite_rules();
    }

    private static function create_page( $title, $content, $slug ) {
        $page = get_page_by_path( $slug );
        if ( ! $page ) {
            wp_insert_post( [
                'post_title'   => $title,
                'post_content' => $content,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ] );
        }
    }
}
