<?php
namespace SchoolManager\Core;

class Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
