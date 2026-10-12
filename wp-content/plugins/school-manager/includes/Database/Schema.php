<?php
namespace SchoolManager\Database;

class Schema {
    public static function get_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix . 'sm_';

        $tables = [];

        $tables['schools'] = "CREATE TABLE {$prefix}schools (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            admin_email varchar(255) NOT NULL,
            phone varchar(50) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $tables['user_links'] = "CREATE TABLE {$prefix}user_links (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            wp_user_id bigint(20) unsigned NOT NULL,
            school_id bigint(20) unsigned NOT NULL,
            person_type varchar(50) NOT NULL,
            person_id bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY wp_user_id (wp_user_id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['roles'] = "CREATE TABLE {$prefix}roles (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            role_key varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            is_custom tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['role_permissions'] = "CREATE TABLE {$prefix}role_permissions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            role_id bigint(20) unsigned NOT NULL,
            permission varchar(100) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY role_id (role_id)
        ) $charset_collate;";

        $tables['school_settings'] = "CREATE TABLE {$prefix}school_settings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            setting_key varchar(100) NOT NULL,
            setting_value longtext,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['academic_years'] = "CREATE TABLE {$prefix}academic_years (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            is_current tinyint(1) NOT NULL DEFAULT 0,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['terms'] = "CREATE TABLE {$prefix}terms (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            academic_year_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['audit_logs'] = "CREATE TABLE {$prefix}audit_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            action varchar(100) NOT NULL,
            entity_type varchar(100) NOT NULL,
            entity_id bigint(20) unsigned NOT NULL,
            old_values longtext,
            new_values longtext,
            ip_address varchar(45) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['login_history'] = "CREATE TABLE {$prefix}login_history (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            school_id bigint(20) unsigned DEFAULT NULL,
            status varchar(20) NOT NULL,
            ip_address varchar(45) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $tables['plans'] = "CREATE TABLE {$prefix}plans (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            price decimal(12,2) NOT NULL DEFAULT 0.00,
            student_limit int(11) NOT NULL DEFAULT 0,
            staff_limit int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $tables['plan_features'] = "CREATE TABLE {$prefix}plan_features (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            plan_id bigint(20) unsigned NOT NULL,
            feature_key varchar(100) NOT NULL,
            PRIMARY KEY  (id),
            KEY plan_id (plan_id)
        ) $charset_collate;";

        $tables['subscriptions'] = "CREATE TABLE {$prefix}subscriptions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            plan_id bigint(20) unsigned NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            start_date datetime NOT NULL,
            end_date datetime DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        return $tables;
    }
}
