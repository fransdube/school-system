<?php
namespace SchoolManager\Core;

class Security {
    public static function init() {
        // Disable XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');

        // Add security headers
        add_action('send_headers', [__CLASS__, 'add_security_headers']);
    }

    public static function add_security_headers() {
        if (!is_admin()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
}
