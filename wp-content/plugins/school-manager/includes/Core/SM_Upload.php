<?php
namespace SchoolManager\Core;

class SM_Upload {
    public static function handle_upload( $file, $allowed_types, $max_size ) {
        $school_id = SM_Tenant::current_school_id();
        if ( ! $school_id ) {
            return new \WP_Error( 'forbidden', 'No school context' );
        }

        if ( $file['size'] > $max_size ) {
            return new \WP_Error( 'file_too_large', 'File size exceeds limit' );
        }

        $type = wp_check_filetype( $file['name'] );
        if ( ! in_array( $type['ext'], $allowed_types ) ) {
            return new \WP_Error( 'invalid_file_type', 'Invalid file type' );
        }

        $upload_dir = WP_CONTENT_DIR . '/sm-private/' . $school_id;
        if ( ! file_exists( $upload_dir ) ) {
            wp_mkdir_p( $upload_dir );
            file_put_contents($upload_dir . '/.htaccess', "deny from all\n");
            file_put_contents($upload_dir . '/index.html', "");
        }

        $ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
        $new_filename = wp_generate_password( 24, false ) . '.' . $ext;
        $dest = $upload_dir . '/' . $new_filename;

        if ( move_uploaded_file( $file['tmp_name'], $dest ) ) {
            return [ 'file' => $new_filename, 'path' => $dest ];
        }

        return new \WP_Error( 'upload_failed', 'Failed to upload file' );
    }

    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    public static function register_routes() {
        register_rest_route( 'sm/v1', '/download/(?P<filename>[a-zA-Z0-9\-\.]+)', [
            'methods'  => 'GET',
            'callback' => [ __CLASS__, 'download_file' ],
            'permission_callback' => [ '\SchoolManager\Rest\SM_Rest', 'check_permission' ]
        ] );
    }

    public static function download_file( $request ) {
        $school_id = SM_Tenant::current_school_id();
        $filename = sanitize_file_name( $request['filename'] );
        $path = WP_CONTENT_DIR . '/sm-private/' . $school_id . '/' . $filename;

        if ( ! file_exists( $path ) ) {
            return new \WP_Error( 'not_found', 'File not found', [ 'status' => 404 ] );
        }

        $mime = wp_check_filetype( $path )['type'];
        if ( ! $mime ) {
            $mime = 'application/octet-stream';
        }

        header( 'Content-Type: ' . $mime );
        header( 'Content-Disposition: inline; filename="' . basename( $path ) . '"' );
        header( 'Content-Length: ' . filesize( $path ) );
        readfile( $path );
        die;
    }
}
