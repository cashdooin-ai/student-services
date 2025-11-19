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

        // Mentorship Sessions table
        $table_name = $wpdb->prefix . 'ss_mentorship_sessions';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            mentor_id bigint(20) NOT NULL,
            preferred_date date,
            preferred_time time,
            topic varchar(255),
            description text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Mentorship Feedback table
        $table_name = $wpdb->prefix . 'ss_mentorship_feedback';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            session_id bigint(20) NOT NULL,
            rating decimal(3,2),
            feedback_text text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Webinar Registrations table
        $table_name = $wpdb->prefix . 'ss_webinar_registrations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            webinar_id bigint(20) NOT NULL,
            registered_at datetime DEFAULT CURRENT_TIMESTAMP,
            attendance_status varchar(20) DEFAULT 'registered',
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Forum Posts table
        $table_name = $wpdb->prefix . 'ss_forum_posts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            category_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            content text NOT NULL,
            tags varchar(255),
            views int(11) DEFAULT 0,
            likes int(11) DEFAULT 0,
            reply_count int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Forum Replies table
        $table_name = $wpdb->prefix . 'ss_forum_replies';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            content text NOT NULL,
            likes int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Forum Likes table
        $table_name = $wpdb->prefix . 'ss_forum_likes';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            post_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY post_id (post_id)
        ) $charset_collate;";
        dbDelta($sql);

        // FAQs table
        $table_name = $wpdb->prefix . 'ss_faqs';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            category_id bigint(20) NOT NULL,
            question text NOT NULL,
            answer text NOT NULL,
            helpful_count int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // FAQ Submissions table
        $table_name = $wpdb->prefix . 'ss_faq_submissions';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            question text NOT NULL,
            category_id bigint(20),
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Calendar Events table
        $table_name = $wpdb->prefix . 'ss_calendar_events';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            event_date date NOT NULL,
            event_time time,
            event_type varchar(50),
            category varchar(100),
            description text,
            is_important tinyint(1) DEFAULT 0,
            reminder_enabled tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Personal Calendar table
        $table_name = $wpdb->prefix . 'ss_personal_calendar';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            title varchar(255) NOT NULL,
            event_date date NOT NULL,
            event_time time,
            description text,
            event_type varchar(50),
            reminder_enabled tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Calendar Reminders table
        $table_name = $wpdb->prefix . 'ss_calendar_reminders';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            event_id bigint(20) NOT NULL,
            reminder_days int(11) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Scholarship Applications table
        $table_name = $wpdb->prefix . 'ss_scholarship_applications';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            scholarship_id bigint(20) NOT NULL,
            application_data text,
            status varchar(20) DEFAULT 'submitted',
            status_notes text,
            applied_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Saved Scholarships table
        $table_name = $wpdb->prefix . 'ss_saved_scholarships';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            scholarship_id bigint(20) NOT NULL,
            saved_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Service Requests table
        $table_name = $wpdb->prefix . 'ss_service_requests';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            service_type_id bigint(20) NOT NULL,
            service_name varchar(255) NOT NULL,
            description text,
            documents text,
            fee_amount decimal(10,2),
            payment_status varchar(20) DEFAULT 'pending',
            status varchar(20) DEFAULT 'pending',
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
            request_id bigint(20),
            order_id varchar(100),
            amount decimal(10,2),
            currency varchar(10) DEFAULT 'INR',
            gateway varchar(50),
            transaction_id varchar(255),
            payment_method varchar(50),
            status varchar(20) DEFAULT 'initiated',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Generated Documents table
        $table_name = $wpdb->prefix . 'ss_generated_documents';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            template_id bigint(20) NOT NULL,
            template_name varchar(255),
            content longtext,
            data text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Accommodation Visits table
        $table_name = $wpdb->prefix . 'ss_accommodation_visits';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            accommodation_id bigint(20) NOT NULL,
            preferred_date date,
            preferred_time time,
            notes text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Accommodation Reviews table
        $table_name = $wpdb->prefix . 'ss_accommodation_reviews';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            accommodation_id bigint(20) NOT NULL,
            rating decimal(3,2),
            review_text text,
            pros text,
            cons text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Accommodation Wishlist table
        $table_name = $wpdb->prefix . 'ss_accommodation_wishlist';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            accommodation_id bigint(20) NOT NULL,
            saved_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // ============ v4.0.0 Community Services Tables ============

        // Blog Posts table
        $table_name = $wpdb->prefix . 'ss_blog_posts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            excerpt text,
            content longtext,
            category varchar(50),
            author_id bigint(20),
            featured_image varchar(500),
            tags text,
            views int(11) DEFAULT 0,
            likes int(11) DEFAULT 0,
            comments int(11) DEFAULT 0,
            featured tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'draft',
            published_date datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY category (category),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Blog Comments table
        $table_name = $wpdb->prefix . 'ss_blog_comments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            content text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Blog Likes table
        $table_name = $wpdb->prefix . 'ss_blog_likes';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            post_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_post (user_id, post_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Blog Bookmarks table
        $table_name = $wpdb->prefix . 'ss_blog_bookmarks';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            post_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_post (user_id, post_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Universities table
        $table_name = $wpdb->prefix . 'ss_universities';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            country varchar(100),
            location varchar(255),
            website varchar(500),
            logo_url varchar(500),
            description text,
            established int(11),
            world_ranking int(11),
            fields_of_study text,
            level varchar(100),
            tuition_fee_range varchar(100),
            application_fee decimal(10,2),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY country (country)
        ) $charset_collate;";
        dbDelta($sql);

        // Study Abroad Programs table
        $table_name = $wpdb->prefix . 'ss_study_abroad_programs';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            university_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            level varchar(50),
            field_of_study varchar(100),
            duration varchar(50),
            tuition_fee decimal(10,2),
            intake_periods varchar(100),
            requirements text,
            description text,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY university_id (university_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Study Abroad Applications table
        $table_name = $wpdb->prefix . 'ss_study_abroad_applications';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            university_id bigint(20) NOT NULL,
            program_id bigint(20),
            full_name varchar(255),
            email varchar(255),
            phone varchar(50),
            academic_level varchar(50),
            field_of_interest varchar(100),
            intended_intake varchar(50),
            message text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Study Abroad Wishlist table
        $table_name = $wpdb->prefix . 'ss_study_abroad_wishlist';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            university_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_university (user_id, university_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Colleges table
        $table_name = $wpdb->prefix . 'ss_colleges';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            location varchar(255),
            type varchar(50),
            established int(11),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Placement Statistics table
        $table_name = $wpdb->prefix . 'ss_placement_stats';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            college_id bigint(20) NOT NULL,
            year int(11) NOT NULL,
            course varchar(100),
            students_placed int(11),
            total_students int(11),
            average_package decimal(10,2),
            highest_package decimal(10,2),
            lowest_package decimal(10,2),
            median_package decimal(10,2),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY college_year (college_id, year)
        ) $charset_collate;";
        dbDelta($sql);

        // Top Recruiters table
        $table_name = $wpdb->prefix . 'ss_top_recruiters';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            college_id bigint(20) NOT NULL,
            year int(11) NOT NULL,
            company_name varchar(255),
            hiring_count int(11),
            average_package decimal(10,2),
            logo_url varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY college_year (college_id, year)
        ) $charset_collate;";
        dbDelta($sql);

        // Sector Placements table
        $table_name = $wpdb->prefix . 'ss_sector_placements';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            college_id bigint(20) NOT NULL,
            year int(11) NOT NULL,
            sector varchar(100),
            students_placed int(11),
            average_package decimal(10,2),
            top_companies text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Salary Distribution table
        $table_name = $wpdb->prefix . 'ss_salary_distribution';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            college_id bigint(20) NOT NULL,
            year int(11) NOT NULL,
            package_range varchar(50),
            student_count int(11),
            percentage decimal(5,2),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Placement Alerts table
        $table_name = $wpdb->prefix . 'ss_placement_alerts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            college_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Alumni Profiles table
        $table_name = $wpdb->prefix . 'ss_alumni_profiles';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20),
            name varchar(255) NOT NULL,
            email varchar(255),
            college varchar(255),
            graduation_year int(11),
            degree varchar(100),
            current_company varchar(255),
            current_position varchar(255),
            current_industry varchar(100),
            location varchar(255),
            bio text,
            profile_photo varchar(500),
            linkedin_url varchar(500),
            available_for_mentorship tinyint(1) DEFAULT 0,
            is_verified tinyint(1) DEFAULT 0,
            is_featured tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY college (college),
            KEY industry (current_industry)
        ) $charset_collate;";
        dbDelta($sql);

        // Alumni Connections table
        $table_name = $wpdb->prefix . 'ss_alumni_connections';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            alumni_id bigint(20) NOT NULL,
            message text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Mentorship Requests table (Alumni)
        $table_name = $wpdb->prefix . 'ss_mentorship_requests';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            alumni_id bigint(20) NOT NULL,
            subject varchar(255),
            message text,
            areas_of_interest text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Alumni Messages table
        $table_name = $wpdb->prefix . 'ss_alumni_messages';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sender_id bigint(20) NOT NULL,
            receiver_id bigint(20) NOT NULL,
            message text,
            is_read tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY conversation (sender_id, receiver_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Alumni Success Stories table
        $table_name = $wpdb->prefix . 'ss_alumni_success_stories';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            alumni_id bigint(20) NOT NULL,
            title varchar(255),
            story text,
            featured tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'draft',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Testimonials table
        $table_name = $wpdb->prefix . 'ss_testimonials';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            student_name varchar(255),
            college varchar(255),
            course varchar(255),
            admission_year int(11),
            category varchar(50),
            title varchar(255),
            testimonial_text text,
            services_used text,
            rating int(11),
            student_photo varchar(500),
            video_url varchar(500),
            views int(11) DEFAULT 0,
            likes int(11) DEFAULT 0,
            shares int(11) DEFAULT 0,
            is_featured tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY category (category)
        ) $charset_collate;";
        dbDelta($sql);

        // Testimonial Likes table
        $table_name = $wpdb->prefix . 'ss_testimonial_likes';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            testimonial_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_testimonial (user_id, testimonial_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Referral Codes table
        $table_name = $wpdb->prefix . 'ss_referral_codes';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            code varchar(50) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Referrals table
        $table_name = $wpdb->prefix . 'ss_referrals';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            referrer_user_id bigint(20) NOT NULL,
            referred_user_id bigint(20) NOT NULL,
            code_used varchar(50),
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY referrer (referrer_user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Reward Points table
        $table_name = $wpdb->prefix . 'ss_reward_points';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            points int(11) NOT NULL,
            type varchar(50),
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Reward Redemptions table
        $table_name = $wpdb->prefix . 'ss_reward_redemptions';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            reward_id int(11) NOT NULL,
            points_spent int(11),
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Jobs table
        $table_name = $wpdb->prefix . 'ss_jobs';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employer_id bigint(20) NOT NULL,
            company_name varchar(255),
            company_logo varchar(500),
            title varchar(255) NOT NULL,
            description text,
            responsibilities text,
            requirements text,
            job_type varchar(50),
            category varchar(100),
            location varchar(255),
            remote tinyint(1) DEFAULT 0,
            salary_min decimal(10,2),
            salary_max decimal(10,2),
            salary_period varchar(20),
            experience_required varchar(50),
            skills_required text,
            education_required varchar(255),
            benefits text,
            application_deadline date,
            expiry_date date,
            contact_email varchar(255),
            contact_phone varchar(50),
            views int(11) DEFAULT 0,
            applications_count int(11) DEFAULT 0,
            is_featured tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY job_type (job_type),
            KEY category (category),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Job Applications table
        $table_name = $wpdb->prefix . 'ss_job_applications';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            job_id bigint(20) NOT NULL,
            employer_id bigint(20) NOT NULL,
            full_name varchar(255),
            email varchar(255),
            phone varchar(50),
            cover_letter text,
            resume_url varchar(500),
            portfolio_url varchar(500),
            expected_salary decimal(10,2),
            available_from varchar(50),
            status varchar(20) DEFAULT 'pending',
            employer_notes text,
            reviewed_at datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY job_id (job_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Job Favorites table
        $table_name = $wpdb->prefix . 'ss_job_favorites';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            job_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_job (user_id, job_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Job Alerts table
        $table_name = $wpdb->prefix . 'ss_job_alerts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            job_types varchar(255),
            categories varchar(255),
            locations varchar(255),
            keywords varchar(255),
            frequency varchar(20),
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Student Profiles table
        $table_name = $wpdb->prefix . 'ss_student_profiles';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            full_name varchar(255),
            phone varchar(50),
            date_of_birth date,
            gender varchar(20),
            current_education varchar(255),
            graduation_year int(11),
            interests text,
            career_goals text,
            skills text,
            resume_url varchar(500),
            profile_photo varchar(500),
            bio text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // User Activities table
        $table_name = $wpdb->prefix . 'ss_user_activities';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            activity_type varchar(50),
            description text,
            meta_data text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Notifications table
        $table_name = $wpdb->prefix . 'ss_notifications';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            title varchar(255),
            message text,
            type varchar(50),
            is_read tinyint(1) DEFAULT 0,
            action_url varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Employers table
        $table_name = $wpdb->prefix . 'ss_employers';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            company_name varchar(255) NOT NULL,
            company_website varchar(500),
            company_email varchar(255),
            company_phone varchar(50),
            company_description text,
            industry varchar(100),
            company_size varchar(50),
            headquarters varchar(255),
            founded_year int(11),
            company_logo varchar(500),
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Subscriptions table
        $table_name = $wpdb->prefix . 'ss_subscriptions';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            plan_id varchar(50) NOT NULL,
            plan_name varchar(255),
            billing_cycle varchar(20),
            price decimal(10,2),
            status varchar(20) DEFAULT 'active',
            start_date datetime,
            expiry_date datetime,
            auto_renew tinyint(1) DEFAULT 1,
            cancelled_at datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql);

        // Subscription Payments table
        $table_name = $wpdb->prefix . 'ss_subscription_payments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            subscription_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            amount decimal(10,2) NOT NULL,
            payment_method varchar(50),
            transaction_id varchar(255),
            status varchar(20) DEFAULT 'pending',
            payment_date datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY subscription_id (subscription_id)
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
            'ss_counselor_reviews',
            'ss_mentorship_sessions',
            'ss_mentorship_feedback',
            'ss_webinar_registrations',
            'ss_forum_posts',
            'ss_forum_replies',
            'ss_forum_likes',
            'ss_faqs',
            'ss_faq_submissions',
            'ss_calendar_events',
            'ss_personal_calendar',
            'ss_calendar_reminders',
            'ss_scholarship_applications',
            'ss_saved_scholarships',
            'ss_service_requests',
            'ss_payments',
            'ss_generated_documents',
            'ss_accommodation_visits',
            'ss_accommodation_reviews',
            'ss_accommodation_wishlist',
            // v4.0.0 tables
            'ss_blog_posts',
            'ss_blog_comments',
            'ss_blog_likes',
            'ss_blog_bookmarks',
            'ss_universities',
            'ss_study_abroad_programs',
            'ss_study_abroad_applications',
            'ss_study_abroad_wishlist',
            'ss_colleges',
            'ss_placement_stats',
            'ss_top_recruiters',
            'ss_sector_placements',
            'ss_salary_distribution',
            'ss_placement_alerts',
            'ss_alumni_profiles',
            'ss_alumni_connections',
            'ss_mentorship_requests',
            'ss_alumni_messages',
            'ss_alumni_success_stories',
            'ss_testimonials',
            'ss_testimonial_likes',
            'ss_referral_codes',
            'ss_referrals',
            'ss_reward_points',
            'ss_reward_redemptions',
            'ss_job_applications',
            'ss_job_favorites',
            'ss_job_alerts',
            'ss_student_profiles',
            'ss_user_activities',
            'ss_notifications',
            'ss_employers',
            'ss_subscriptions',
            'ss_subscription_payments'
        );

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
        }
    }
}
