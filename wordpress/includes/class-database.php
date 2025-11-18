<?php
/**
 * Database Schema and Installation
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Database {

    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Courses table
        $table_name = $wpdb->prefix . 'ss_courses';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            course_code varchar(20) NOT NULL,
            title varchar(255) NOT NULL,
            credits int(11) NOT NULL,
            instructor varchar(255),
            capacity int(11) DEFAULT 30,
            enrolled int(11) DEFAULT 0,
            semester varchar(50),
            year int(11),
            description text,
            prerequisites text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY course_code (course_code)
        ) $charset_collate;";
        dbDelta($sql);

        // Course Enrollments table
        $table_name = $wpdb->prefix . 'ss_enrollments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'enrolled',
            enrolled_at datetime DEFAULT CURRENT_TIMESTAMP,
            dropped_at datetime,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY course_id (course_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Grades table
        $table_name = $wpdb->prefix . 'ss_grades';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            grade varchar(5),
            grade_point decimal(3,2),
            semester varchar(50),
            year int(11),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY course_id (course_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Billing Accounts table
        $table_name = $wpdb->prefix . 'ss_billing';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            balance decimal(10,2) DEFAULT 0.00,
            due_date date,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Charges table
        $table_name = $wpdb->prefix . 'ss_charges';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            description varchar(255) NOT NULL,
            amount decimal(10,2) NOT NULL,
            category varchar(50),
            charge_date date,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Payments table
        $table_name = $wpdb->prefix . 'ss_payments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            amount decimal(10,2) NOT NULL,
            payment_method varchar(20),
            status varchar(20) DEFAULT 'completed',
            payment_date date,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Scholarships table
        $table_name = $wpdb->prefix . 'ss_scholarships';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            amount decimal(10,2) NOT NULL,
            deadline date,
            eligibility text,
            requirements text,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Housing Applications table
        $table_name = $wpdb->prefix . 'ss_housing';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            building_type varchar(50),
            building varchar(100),
            room varchar(50),
            status varchar(20) DEFAULT 'pending',
            preferences text,
            applied_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Maintenance Requests table
        $table_name = $wpdb->prefix . 'ss_maintenance';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            building varchar(100),
            room varchar(50),
            issue text NOT NULL,
            priority varchar(20) DEFAULT 'medium',
            status varchar(20) DEFAULT 'submitted',
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Events table
        $table_name = $wpdb->prefix . 'ss_events';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            organizer varchar(255),
            event_date date,
            event_time time,
            location varchar(255),
            description text,
            category varchar(50),
            capacity int(11),
            ticket_required tinyint(1) DEFAULT 0,
            ticket_price decimal(10,2) DEFAULT 0.00,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Event Registrations table
        $table_name = $wpdb->prefix . 'ss_event_registrations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            event_id bigint(20) NOT NULL,
            ticket_number varchar(50),
            registered_at datetime DEFAULT CURRENT_TIMESTAMP,
            checked_in tinyint(1) DEFAULT 0,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY event_id (event_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Organizations table
        $table_name = $wpdb->prefix . 'ss_organizations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            category varchar(100),
            description text,
            president varchar(255),
            email varchar(255),
            meeting_schedule varchar(255),
            member_count int(11) DEFAULT 0,
            joinable tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Organization Memberships table
        $table_name = $wpdb->prefix . 'ss_memberships';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            org_id bigint(20) NOT NULL,
            position varchar(100) DEFAULT 'Member',
            joined_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY org_id (org_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Health Appointments table
        $table_name = $wpdb->prefix . 'ss_health_appointments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            appointment_type varchar(50),
            provider varchar(255),
            appointment_date date,
            appointment_time time,
            reason text,
            status varchar(20) DEFAULT 'scheduled',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // IT Tickets table
        $table_name = $wpdb->prefix . 'ss_it_tickets';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            category varchar(50),
            subject varchar(255) NOT NULL,
            description text NOT NULL,
            priority varchar(20) DEFAULT 'medium',
            status varchar(20) DEFAULT 'open',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            resolved_at datetime,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Job Postings table
        $table_name = $wpdb->prefix . 'ss_jobs';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            company varchar(255) NOT NULL,
            job_type varchar(50),
            location varchar(255),
            description text,
            requirements text,
            salary varchar(100),
            posted_date date,
            deadline date,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // College Searches table (AI College Recommendation)
        $table_name = $wpdb->prefix . 'ss_college_searches';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            preferences text,
            results_count int(11),
            search_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // College Favorites table
        $table_name = $wpdb->prefix . 'ss_college_favorites';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            college_id bigint(20) NOT NULL,
            saved_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Eligibility Calculations table
        $table_name = $wpdb->prefix . 'ss_eligibility_calculations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            scores_data text,
            eligible_colleges text,
            calculation_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Practice Tests table (Entrance Exam Preparation)
        $table_name = $wpdb->prefix . 'ss_practice_tests';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            exam_id varchar(50) NOT NULL,
            test_type varchar(50),
            score decimal(5,2),
            total_marks decimal(5,2),
            time_taken int(11),
            test_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Test Attempts table
        $table_name = $wpdb->prefix . 'ss_test_attempts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            test_id bigint(20) NOT NULL,
            answers text,
            score decimal(5,2),
            percentage decimal(5,2),
            attempt_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY test_id (test_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Language Test Attempts table
        $table_name = $wpdb->prefix . 'ss_language_test_attempts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            test_type varchar(50),
            section varchar(100),
            score decimal(5,2),
            band_score decimal(3,1),
            test_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Mock Interviews table
        $table_name = $wpdb->prefix . 'ss_mock_interviews';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            interview_type varchar(50),
            scheduled_date datetime,
            interviewer varchar(255),
            status varchar(20) DEFAULT 'scheduled',
            feedback text,
            rating decimal(3,2),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Interview Questions table
        $table_name = $wpdb->prefix . 'ss_interview_questions';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            category varchar(50),
            question_id int(11),
            user_answer text,
            practice_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Financial Calculations table
        $table_name = $wpdb->prefix . 'ss_financial_calculations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            calculation_data text,
            total_cost decimal(12,2),
            financial_need decimal(12,2),
            calculation_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Loan Calculations table
        $table_name = $wpdb->prefix . 'ss_loan_calculations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            loan_amount decimal(12,2),
            interest_rate decimal(5,2),
            tenure_months int(11),
            emi decimal(12,2),
            calculation_data text,
            calculation_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Cost Comparisons table
        $table_name = $wpdb->prefix . 'ss_cost_comparisons';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            comparison_data text,
            colleges_compared int(11),
            comparison_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // GPA Calculations table
        $table_name = $wpdb->prefix . 'ss_gpa_calculations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            gpa decimal(4,2),
            total_credits decimal(5,2),
            grades_data text,
            calculation_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Counseling Questions table
        $table_name = $wpdb->prefix . 'ss_counseling_questions';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            subject varchar(255),
            question text,
            category varchar(100),
            status varchar(20) DEFAULT 'pending',
            answer text,
            created_date datetime DEFAULT CURRENT_TIMESTAMP,
            answered_date datetime,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Counselor Reviews table
        $table_name = $wpdb->prefix . 'ss_counselor_reviews';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            counselor_id bigint(20) NOT NULL,
            rating decimal(3,2),
            review_text text,
            review_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY counselor_id (counselor_id)
        ) $charset_collate;";
        dbDelta($sql);
    }

    /**
     * Drop all plugin tables (used on uninstall)
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = array(
            'ss_courses',
            'ss_enrollments',
            'ss_grades',
            'ss_billing',
            'ss_charges',
            'ss_payments',
            'ss_scholarships',
            'ss_housing',
            'ss_maintenance',
            'ss_events',
            'ss_event_registrations',
            'ss_organizations',
            'ss_memberships',
            'ss_health_appointments',
            'ss_it_tickets',
            'ss_jobs',
            'ss_college_searches',
            'ss_college_favorites',
            'ss_eligibility_calculations',
            'ss_practice_tests',
            'ss_test_attempts',
            'ss_language_test_attempts',
            'ss_mock_interviews',
            'ss_interview_questions',
            'ss_financial_calculations',
            'ss_loan_calculations',
            'ss_cost_comparisons',
            'ss_gpa_calculations',
            'ss_counseling_questions',
            'ss_counselor_reviews'
        );

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }
    }
}
