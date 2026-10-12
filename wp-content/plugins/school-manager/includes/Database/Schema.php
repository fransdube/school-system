<?php
namespace SchoolManager\Database;

class Schema {
    public static function get_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix . 'sm_';

        $tables = [];

        // ... previous tables from prompt 1 ...
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
        // ... end prompt 1 tables

        // Prompt 2 Tables
        $tables['classes'] = "CREATE TABLE {$prefix}classes (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            capacity int(11) DEFAULT 0,
            class_teacher_id bigint(20) unsigned DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['sections'] = "CREATE TABLE {$prefix}sections (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            capacity int(11) DEFAULT 0,
            class_teacher_id bigint(20) unsigned DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id),
            KEY class_id (class_id)
        ) $charset_collate;";

        $tables['subjects'] = "CREATE TABLE {$prefix}subjects (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            code varchar(50) NOT NULL,
            type varchar(50) NOT NULL DEFAULT 'theory',
            credit_hours decimal(4,2) DEFAULT 0.00,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['class_subjects'] = "CREATE TABLE {$prefix}class_subjects (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id),
            KEY class_id (class_id),
            KEY subject_id (subject_id)
        ) $charset_collate;";

        $tables['teacher_assignments'] = "CREATE TABLE {$prefix}teacher_assignments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            teacher_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            section_id bigint(20) unsigned DEFAULT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 3 Tables (Students)
        $tables['students'] = "CREATE TABLE {$prefix}students (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            family_id bigint(20) unsigned DEFAULT NULL,
            name varchar(255) NOT NULL,
            registration_no varchar(100) NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            section_id bigint(20) unsigned DEFAULT NULL,
            picture varchar(255) DEFAULT NULL,
            date_of_admission date NOT NULL,
            discount_in_fee decimal(5,2) DEFAULT 0.00,
            mobile varchar(50) DEFAULT NULL,
            date_of_birth date DEFAULT NULL,
            gender varchar(20) DEFAULT NULL,
            nic varchar(100) DEFAULT NULL,
            orphan_student tinyint(1) DEFAULT 0,
            caste varchar(100) DEFAULT NULL,
            osc varchar(100) DEFAULT NULL,
            identification_mark varchar(255) DEFAULT NULL,
            previous_school varchar(255) DEFAULT NULL,
            religion varchar(100) DEFAULT NULL,
            blood_group varchar(10) DEFAULT NULL,
            previous_board_roll_no varchar(100) DEFAULT NULL,
            total_siblings int(11) DEFAULT 0,
            disease varchar(255) DEFAULT NULL,
            additional_note text,
            address text,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['student_documents'] = "CREATE TABLE {$prefix}student_documents (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            document_type varchar(100) NOT NULL,
            file_path varchar(255) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id),
            KEY student_id (student_id)
        ) $charset_collate;";

        $tables['student_status_history'] = "CREATE TABLE {$prefix}student_status_history (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            status varchar(50) NOT NULL,
            reason text,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['families'] = "CREATE TABLE {$prefix}families (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            father_name varchar(255) DEFAULT NULL,
            father_nic varchar(100) DEFAULT NULL,
            father_occupation varchar(100) DEFAULT NULL,
            father_education varchar(100) DEFAULT NULL,
            father_mobile varchar(50) DEFAULT NULL,
            father_profession varchar(100) DEFAULT NULL,
            father_income decimal(12,2) DEFAULT 0.00,
            mother_name varchar(255) DEFAULT NULL,
            mother_nic varchar(100) DEFAULT NULL,
            mother_occupation varchar(100) DEFAULT NULL,
            mother_education varchar(100) DEFAULT NULL,
            mother_mobile varchar(50) DEFAULT NULL,
            mother_profession varchar(100) DEFAULT NULL,
            mother_income decimal(12,2) DEFAULT 0.00,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['admission_enquiries'] = "CREATE TABLE {$prefix}admission_enquiries (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_name varchar(255) NOT NULL,
            phone varchar(50) NOT NULL,
            status varchar(50) DEFAULT 'pending',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 4 Tables (Employees)
        $tables['employees'] = "CREATE TABLE {$prefix}employees (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(255) NOT NULL,
            designation_id bigint(20) unsigned DEFAULT NULL,
            department_id bigint(20) unsigned DEFAULT NULL,
            photo varchar(255) DEFAULT NULL,
            qualifications text,
            contract_type varchar(50) DEFAULT NULL,
            bank_details text,
            emergency_contact varchar(255) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['employee_documents'] = "CREATE TABLE {$prefix}employee_documents (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            employee_id bigint(20) unsigned NOT NULL,
            document_type varchar(100) NOT NULL,
            file_path varchar(255) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['departments'] = "CREATE TABLE {$prefix}departments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['designations'] = "CREATE TABLE {$prefix}designations (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 5 Tables (Attendance)
        $tables['student_attendance'] = "CREATE TABLE {$prefix}student_attendance (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            status varchar(20) NOT NULL,
            remarks text,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id),
            KEY date (date)
        ) $charset_collate;";

        $tables['staff_attendance'] = "CREATE TABLE {$prefix}staff_attendance (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            employee_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            check_in time DEFAULT NULL,
            check_out time DEFAULT NULL,
            status varchar(20) NOT NULL,
            remarks text,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['leave_types'] = "CREATE TABLE {$prefix}leave_types (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['leave_requests'] = "CREATE TABLE {$prefix}leave_requests (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            requester_type varchar(20) NOT NULL,
            requester_id bigint(20) unsigned NOT NULL,
            leave_type_id bigint(20) unsigned NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['leave_balances'] = "CREATE TABLE {$prefix}leave_balances (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            employee_id bigint(20) unsigned NOT NULL,
            leave_type_id bigint(20) unsigned NOT NULL,
            balance decimal(5,2) DEFAULT 0.00,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 6 Tables (Fees and Invoices)
        $tables['fee_heads'] = "CREATE TABLE {$prefix}fee_heads (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['fee_structures'] = "CREATE TABLE {$prefix}fee_structures (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            academic_year_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            frequency varchar(50) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['fee_structure_items'] = "CREATE TABLE {$prefix}fee_structure_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            fee_structure_id bigint(20) unsigned NOT NULL,
            fee_head_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['student_discounts'] = "CREATE TABLE {$prefix}student_discounts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            type varchar(50) NOT NULL,
            amount decimal(12,2) NOT NULL,
            reason text,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['invoices'] = "CREATE TABLE {$prefix}invoices (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            total_amount decimal(12,2) NOT NULL,
            paid_amount decimal(12,2) DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'unpaid',
            due_date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['invoice_items'] = "CREATE TABLE {$prefix}invoice_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            invoice_id bigint(20) unsigned NOT NULL,
            fee_head_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['payments'] = "CREATE TABLE {$prefix}payments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            invoice_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            date date NOT NULL,
            method varchar(50) NOT NULL,
            reference varchar(100) DEFAULT NULL,
            collector_id bigint(20) unsigned DEFAULT NULL,
            notes text,
            proof_file varchar(255) DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['receipts'] = "CREATE TABLE {$prefix}receipts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            payment_id bigint(20) unsigned NOT NULL,
            receipt_number varchar(100) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['refunds'] = "CREATE TABLE {$prefix}refunds (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            payment_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['late_fee_rules'] = "CREATE TABLE {$prefix}late_fee_rules (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            type varchar(50) NOT NULL,
            amount decimal(12,2) NOT NULL,
            days int(11) NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['payment_notifications'] = "CREATE TABLE {$prefix}payment_notifications (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            invoice_id bigint(20) unsigned NOT NULL,
            method varchar(50) NOT NULL,
            status varchar(20) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 7 Tables (Accounts)
        $tables['account_categories'] = "CREATE TABLE {$prefix}account_categories (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            type varchar(20) NOT NULL,
            parent_id bigint(20) unsigned DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['bank_accounts'] = "CREATE TABLE {$prefix}bank_accounts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            opening_balance decimal(12,2) DEFAULT 0.00,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['transactions'] = "CREATE TABLE {$prefix}transactions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            type varchar(20) NOT NULL,
            amount decimal(12,2) NOT NULL,
            category_id bigint(20) unsigned NOT NULL,
            method varchar(50) NOT NULL,
            vendor_id bigint(20) unsigned DEFAULT NULL,
            reference varchar(100) DEFAULT NULL,
            receipt_attachment varchar(255) DEFAULT NULL,
            bank_account_id bigint(20) unsigned NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['vendors'] = "CREATE TABLE {$prefix}vendors (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            contact varchar(100) DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['budgets'] = "CREATE TABLE {$prefix}budgets (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            category_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            academic_year_id bigint(20) unsigned NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['account_transfers'] = "CREATE TABLE {$prefix}account_transfers (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            from_account_id bigint(20) unsigned NOT NULL,
            to_account_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 8 Tables (Timetable)
        $tables['periods'] = "CREATE TABLE {$prefix}periods (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            is_break tinyint(1) DEFAULT 0,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['timetable_entries'] = "CREATE TABLE {$prefix}timetable_entries (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            section_id bigint(20) unsigned DEFAULT NULL,
            period_id bigint(20) unsigned NOT NULL,
            day_of_week int(11) NOT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            teacher_id bigint(20) unsigned NOT NULL,
            room_id bigint(20) unsigned DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['rooms'] = "CREATE TABLE {$prefix}rooms (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            capacity int(11) DEFAULT 0,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['substitutions'] = "CREATE TABLE {$prefix}substitutions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            timetable_entry_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            substitute_teacher_id bigint(20) unsigned NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 9 Tables (Homework)
        $tables['homework'] = "CREATE TABLE {$prefix}homework (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            section_id bigint(20) unsigned DEFAULT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            description text NOT NULL,
            due_date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['homework_attachments'] = "CREATE TABLE {$prefix}homework_attachments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            homework_id bigint(20) unsigned NOT NULL,
            file_path varchar(255) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['homework_submissions'] = "CREATE TABLE {$prefix}homework_submissions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            homework_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            file_path varchar(255) DEFAULT NULL,
            comments text,
            is_late tinyint(1) DEFAULT 0,
            marks decimal(5,2) DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 10 Tables (Exams)
        $tables['exam_types'] = "CREATE TABLE {$prefix}exam_types (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            weight decimal(5,2) DEFAULT 0.00,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['exams'] = "CREATE TABLE {$prefix}exams (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            exam_type_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            academic_year_id bigint(20) unsigned NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            is_published tinyint(1) DEFAULT 0,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['exam_schedules'] = "CREATE TABLE {$prefix}exam_schedules (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            exam_id bigint(20) unsigned NOT NULL,
            class_id bigint(20) unsigned NOT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            room_id bigint(20) unsigned DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['marks'] = "CREATE TABLE {$prefix}marks (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            exam_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            marks_obtained decimal(5,2) DEFAULT NULL,
            total_marks decimal(5,2) NOT NULL,
            is_absent tinyint(1) DEFAULT 0,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['results'] = "CREATE TABLE {$prefix}results (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            exam_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            total_marks_obtained decimal(10,2) DEFAULT NULL,
            total_max_marks decimal(10,2) DEFAULT NULL,
            percentage decimal(5,2) DEFAULT NULL,
            grade varchar(20) DEFAULT NULL,
            rank int(11) DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['report_comments'] = "CREATE TABLE {$prefix}report_comments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            exam_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            teacher_id bigint(20) unsigned NOT NULL,
            comment text NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['question_papers'] = "CREATE TABLE {$prefix}question_papers (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            exam_id bigint(20) unsigned NOT NULL,
            subject_id bigint(20) unsigned NOT NULL,
            file_path varchar(255) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['behaviour_records'] = "CREATE TABLE {$prefix}behaviour_records (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            term_id bigint(20) unsigned NOT NULL,
            record text NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['skill_ratings'] = "CREATE TABLE {$prefix}skill_ratings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            term_id bigint(20) unsigned NOT NULL,
            skill varchar(100) NOT NULL,
            rating varchar(20) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['grading_scales'] = "CREATE TABLE {$prefix}grading_scales (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['grades'] = "CREATE TABLE {$prefix}grades (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            grading_scale_id bigint(20) unsigned NOT NULL,
            name varchar(20) NOT NULL,
            min_percentage decimal(5,2) NOT NULL,
            max_percentage decimal(5,2) NOT NULL,
            gpa decimal(4,2) DEFAULT 0.00,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 11 Tables (Payroll)
        $tables['salary_structures'] = "CREATE TABLE {$prefix}salary_structures (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            employee_id bigint(20) unsigned NOT NULL,
            basic_salary decimal(12,2) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['salary_components'] = "CREATE TABLE {$prefix}salary_components (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            salary_structure_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            type varchar(20) NOT NULL,
            amount decimal(12,2) NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['payroll_runs'] = "CREATE TABLE {$prefix}payroll_runs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            month int(11) NOT NULL,
            year int(11) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'draft',
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['payslips'] = "CREATE TABLE {$prefix}payslips (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            payroll_run_id bigint(20) unsigned NOT NULL,
            employee_id bigint(20) unsigned NOT NULL,
            net_salary decimal(12,2) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['payslip_items'] = "CREATE TABLE {$prefix}payslip_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            payslip_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            type varchar(20) NOT NULL,
            amount decimal(12,2) NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['employee_loans'] = "CREATE TABLE {$prefix}employee_loans (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            employee_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['loan_instalments'] = "CREATE TABLE {$prefix}loan_instalments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            loan_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            due_date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'unpaid',
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['salary_payments'] = "CREATE TABLE {$prefix}salary_payments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            payslip_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            date date NOT NULL,
            method varchar(50) NOT NULL,
            reference varchar(100) DEFAULT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 12 Tables (Messaging)
        $tables['message_templates'] = "CREATE TABLE {$prefix}message_templates (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            content text NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['message_queue'] = "CREATE TABLE {$prefix}message_queue (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            recipient_email varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            content text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['message_logs'] = "CREATE TABLE {$prefix}message_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            recipient varchar(255) NOT NULL,
            type varchar(20) NOT NULL,
            status varchar(20) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['notices'] = "CREATE TABLE {$prefix}notices (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            title varchar(255) NOT NULL,
            content text NOT NULL,
            date date NOT NULL,
            target_audience varchar(50) NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['events'] = "CREATE TABLE {$prefix}events (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            start_date date NOT NULL,
            end_date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['inbox_messages'] = "CREATE TABLE {$prefix}inbox_messages (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            sender_id bigint(20) unsigned NOT NULL,
            recipient_id bigint(20) unsigned NOT NULL,
            subject varchar(255) NOT NULL,
            content text NOT NULL,
            is_read tinyint(1) DEFAULT 0,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['contact_optins'] = "CREATE TABLE {$prefix}contact_optins (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            email_optin tinyint(1) DEFAULT 1,
            sms_optin tinyint(1) DEFAULT 1,
            whatsapp_optin tinyint(1) DEFAULT 1,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 13 Tables (Certificates and ID Cards)
        $tables['certificate_templates'] = "CREATE TABLE {$prefix}certificate_templates (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            content text NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['certificates_issued'] = "CREATE TABLE {$prefix}certificates_issued (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            template_id bigint(20) unsigned NOT NULL,
            student_id bigint(20) unsigned NOT NULL,
            serial_number varchar(100) NOT NULL,
            issue_date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['id_card_templates'] = "CREATE TABLE {$prefix}id_card_templates (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            target_type varchar(50) NOT NULL,
            layout text NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['id_cards_issued'] = "CREATE TABLE {$prefix}id_cards_issued (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            template_id bigint(20) unsigned NOT NULL,
            person_type varchar(50) NOT NULL,
            person_id bigint(20) unsigned NOT NULL,
            issue_date date NOT NULL,
            created_by bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        // Prompt 15 Tables (Subscriptions and Super Admin - partial already added, extending)
        $tables['subscription_payments'] = "CREATE TABLE {$prefix}subscription_payments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            subscription_id bigint(20) unsigned NOT NULL,
            amount decimal(12,2) NOT NULL,
            date date NOT NULL,
            method varchar(50) NOT NULL,
            reference varchar(100) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        $tables['coupons'] = "CREATE TABLE {$prefix}coupons (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            code varchar(50) NOT NULL,
            discount_type varchar(20) NOT NULL,
            amount decimal(12,2) NOT NULL,
            expiry_date date DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $tables['support_tickets'] = "CREATE TABLE {$prefix}support_tickets (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            school_id bigint(20) unsigned NOT NULL,
            subject varchar(255) NOT NULL,
            message text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'open',
            created_by bigint(20) unsigned NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY school_id (school_id)
        ) $charset_collate;";

        return $tables;
    }
}
