<?php
namespace SchoolManager\Core;

class SM_Router {
    public static function init() {
        add_action('template_redirect', [__CLASS__, 'handle_routing']);
    }

    public static function handle_routing() {
        // Future client side routing endpoints
    }
}
