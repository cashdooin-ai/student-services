<?php
/**
 * REST API Endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_REST_API {

    public static function init() {
        add_action('rest_api_init', array(__CLASS__, 'register_routes'));
    }

    public static function register_routes() {
        $namespace = 'student-services/v1';

        // Course Registration
        register_rest_route($namespace, '/courses', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_courses'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/courses/enroll', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'enroll_course'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Grades
        register_rest_route($namespace, '/grades', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_grades'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/grades/gpa', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_gpa'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Billing
        register_rest_route($namespace, '/billing/account', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_billing_account'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/billing/payment', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'make_payment'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Events
        register_rest_route($namespace, '/events', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_events'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/events/register', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'register_event'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Health Appointments
        register_rest_route($namespace, '/health/appointments', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_health_appointments'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/health/schedule', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'schedule_health_appointment'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // IT Support
        register_rest_route($namespace, '/it/tickets', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_it_tickets'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/it/submit', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_it_ticket'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // College Recommendation
        register_rest_route($namespace, '/college-recommendation/search', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'get_college_recommendations'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/college-recommendation/favorites', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_college_favorites'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/college-recommendation/save-favorite', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'save_college_favorite'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Eligibility Calculator
        register_rest_route($namespace, '/eligibility/calculate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'calculate_eligibility'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Entrance Exam Preparation
        register_rest_route($namespace, '/entrance-exams/list', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_entrance_exams'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/entrance-exams/study-plan', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'get_study_plan'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/entrance-exams/practice', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_practice_test'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Language Proficiency Tests
        register_rest_route($namespace, '/language-tests/list', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_language_tests'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/language-tests/practice', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_language_practice'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Course Discovery
        register_rest_route($namespace, '/course-discovery/search', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'discover_courses'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Interview Preparation
        register_rest_route($namespace, '/interview-prep/questions', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_interview_questions'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/interview-prep/mock-interview', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'book_mock_interview'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Financial Aid Calculator
        register_rest_route($namespace, '/financial-aid/calculate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'calculate_financial_aid'),
            'permission_callback' => '__return_true'
        ));

        // Loan Calculator
        register_rest_route($namespace, '/loan-calculator/calculate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'calculate_emi'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/loan-calculator/compare', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'compare_loan_schemes'),
            'permission_callback' => '__return_true'
        ));

        // College Cost Comparison
        register_rest_route($namespace, '/college-cost/compare', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'compare_college_costs'),
            'permission_callback' => '__return_true'
        ));

        // GPA Calculator
        register_rest_route($namespace, '/gpa-calculator/calculate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'calculate_gpa_new'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/gpa-calculator/convert', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'convert_grade'),
            'permission_callback' => '__return_true'
        ));

        // Admission Counseling
        register_rest_route($namespace, '/counseling/counselors', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_counselors'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/counseling/book', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'book_counseling_session'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/counseling/packages', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_counseling_packages'),
            'permission_callback' => '__return_true'
        ));

        // Mentorship (Find Your Mentor)
        register_rest_route($namespace, '/mentorship/mentors', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_mentors'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/mentorship/book-session', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'book_mentorship_session'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/mentorship/my-sessions', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_my_mentorship_sessions'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/mentorship/submit-feedback', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_mentorship_feedback'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Webinars & Workshops
        register_rest_route($namespace, '/webinars/upcoming', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_upcoming_webinars'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/webinars/register', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'register_webinar'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/webinars/my-webinars', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_my_webinars'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/webinars/categories', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_webinar_categories'),
            'permission_callback' => '__return_true'
        ));

        // Student Forum
        register_rest_route($namespace, '/forum/posts', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_forum_posts'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/forum/post', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'create_forum_post'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/forum/replies/(?P<post_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_forum_replies'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/forum/reply', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'create_forum_reply'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/forum/like', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'toggle_forum_like'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/forum/categories', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_forum_categories'),
            'permission_callback' => '__return_true'
        ));

        // FAQ Search
        register_rest_route($namespace, '/faq/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_faqs'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/faq/categories', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_faq_categories'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/faq/helpful/(?P<faq_id>\d+)', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'mark_faq_helpful'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/faq/submit-question', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_faq_question'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Academic Calendar
        register_rest_route($namespace, '/calendar/events', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_calendar_events'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/calendar/personal-event', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'add_personal_event'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/calendar/my-events', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_personal_events'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/calendar/deadlines', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_upcoming_deadlines'),
            'permission_callback' => '__return_true'
        ));

        // Enhanced Scholarship
        register_rest_route($namespace, '/scholarships/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_scholarships'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/scholarships/recommendations', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'get_scholarship_recommendations'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/scholarships/apply', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'apply_scholarship'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/scholarships/my-applications', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_my_scholarship_applications'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/scholarships/save', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'save_scholarship'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Service Request Form
        register_rest_route($namespace, '/service-request/types', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_service_types'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/service-request/create', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'create_service_request'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/service-request/initiate-payment', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'initiate_service_payment'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/service-request/my-requests', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_my_service_requests'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Document Templates
        register_rest_route($namespace, '/documents/templates', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_document_templates'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/documents/generate', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'generate_document'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/documents/my-documents', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_my_documents'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/documents/update', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'update_document'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Accommodation Finder
        register_rest_route($namespace, '/accommodation/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_accommodations'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/accommodation/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_accommodation_details'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/accommodation/request-visit', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'request_accommodation_visit'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/accommodation/wishlist', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'save_accommodation_wishlist'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/accommodation/my-wishlist', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_accommodation_wishlist'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // CollegeKampus Blog (v4.0.0)
        register_rest_route($namespace, '/blog/posts', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_blog_posts'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/blog/post/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_blog_post'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/blog/like', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'like_blog_post'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/blog/comment', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'add_blog_comment'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/blog/bookmark', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'bookmark_blog_post'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Study Abroad Programs (v4.0.0)
        register_rest_route($namespace, '/study-abroad/countries', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_study_abroad_countries'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/study-abroad/universities', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_universities'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/study-abroad/programs/(?P<university_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_study_abroad_programs'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/study-abroad/apply', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_study_abroad_application'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/study-abroad/wishlist', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'save_study_abroad_wishlist'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Placement Statistics (v4.0.0)
        register_rest_route($namespace, '/placements/stats', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_placement_stats'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/placements/recruiters/(?P<college_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_top_recruiters'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/placements/compare', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'compare_placements'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/placements/trends/(?P<college_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_placement_trends'),
            'permission_callback' => '__return_true'
        ));

        // Alumni Network (v4.0.0)
        register_rest_route($namespace, '/alumni/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_alumni'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/alumni/connect', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'connect_with_alumni'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/alumni/mentorship', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'request_alumni_mentorship'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/alumni/message', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'send_alumni_message'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/alumni/success-stories', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_alumni_success_stories'),
            'permission_callback' => '__return_true'
        ));

        // Student Testimonials (v4.0.0)
        register_rest_route($namespace, '/testimonials', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_testimonials'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/testimonials/submit', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'submit_testimonial'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/testimonials/like', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'like_testimonial'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Referral & Rewards (v4.0.0)
        register_rest_route($namespace, '/referral/code', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_referral_code'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/referral/apply', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'apply_referral_code'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/referral/points', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_points_balance'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/referral/rewards', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_rewards_catalog'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/referral/redeem', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'redeem_reward'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Job Board (v4.0.0)
        register_rest_route($namespace, '/jobs/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'search_jobs'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/jobs/apply', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'apply_for_job'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/jobs/save', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'save_job'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/jobs/my-applications', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_my_job_applications'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/jobs/alerts/subscribe', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'subscribe_job_alerts'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Student Dashboard (v4.0.0)
        register_rest_route($namespace, '/dashboard/overview', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_student_dashboard'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/dashboard/profile', array(
            'methods' => 'PUT',
            'callback' => array(__CLASS__, 'update_student_profile'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/dashboard/activity', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'log_student_activity'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Employer Dashboard (v4.0.0)
        register_rest_route($namespace, '/employer/register', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'register_employer'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/employer/overview', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_employer_dashboard'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/employer/post-job', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'post_job'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/employer/applications/(?P<job_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_job_applications'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/employer/application/update', array(
            'methods' => 'PUT',
            'callback' => array(__CLASS__, 'update_application_status'),
            'permission_callback' => 'is_user_logged_in'
        ));

        // Subscription Plans (v4.0.0)
        register_rest_route($namespace, '/subscriptions/plans', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_subscription_plans'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($namespace, '/subscriptions/subscribe', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'subscribe_to_plan'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/subscriptions/check-access', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'check_feature_access'),
            'permission_callback' => 'is_user_logged_in'
        ));

        register_rest_route($namespace, '/subscriptions/cancel', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'cancel_subscription'),
            'permission_callback' => 'is_user_logged_in'
        ));
    }

    // Course Registration Callbacks
    public static function get_courses($request) {
        $service = new Student_Services_Course_Registration();
        $result = $service->search_courses($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function enroll_course($request) {
        $service = new Student_Services_Course_Registration();
        $user_id = get_current_user_id();
        $course_id = $request->get_param('course_id');

        $result = $service->enroll($user_id, $course_id);

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Grades Callbacks
    public static function get_grades($request) {
        $service = new Student_Services_Grade_Management();
        $user_id = get_current_user_id();

        $result = $service->get_grades($user_id,
            $request->get_param('semester'),
            $request->get_param('year')
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function get_gpa($request) {
        $service = new Student_Services_Grade_Management();
        $user_id = get_current_user_id();

        $result = $service->calculate_gpa($user_id, true);

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Billing Callbacks
    public static function get_billing_account($request) {
        $service = new Student_Services_Billing();
        $user_id = get_current_user_id();

        $result = $service->get_account($user_id);

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function make_payment($request) {
        $service = new Student_Services_Billing();
        $user_id = get_current_user_id();

        $result = $service->make_payment($user_id,
            $request->get_param('amount'),
            $request->get_param('method')
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Events Callbacks
    public static function get_events($request) {
        $service = new Student_Services_Events();
        $result = $service->get_upcoming_events($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function register_event($request) {
        $service = new Student_Services_Events();
        $user_id = get_current_user_id();

        $result = $service->register_for_event($user_id, $request->get_param('event_id'));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Health Callbacks
    public static function get_health_appointments($request) {
        $service = new Student_Services_Health();
        $user_id = get_current_user_id();

        $result = $service->get_appointments($user_id);

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function schedule_health_appointment($request) {
        $service = new Student_Services_Health();
        $user_id = get_current_user_id();

        $result = $service->schedule_appointment($user_id, array(
            'type' => $request->get_param('type'),
            'date' => $request->get_param('date'),
            'reason' => $request->get_param('reason')
        ));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // IT Support Callbacks
    public static function get_it_tickets($request) {
        $service = new Student_Services_IT_Support();
        $user_id = get_current_user_id();

        $result = $service->get_tickets($user_id);

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function submit_it_ticket($request) {
        $service = new Student_Services_IT_Support();
        $user_id = get_current_user_id();

        $result = $service->submit_ticket($user_id, array(
            'category' => $request->get_param('category'),
            'subject' => $request->get_param('subject'),
            'description' => $request->get_param('description'),
            'priority' => $request->get_param('priority')
        ));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // College Recommendation Callbacks
    public static function get_college_recommendations($request) {
        $service = new Student_Services_College_Recommendation();
        $user_id = get_current_user_id();

        $result = $service->get_recommendations($user_id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function get_college_favorites($request) {
        $service = new Student_Services_College_Recommendation();
        $user_id = get_current_user_id();

        $result = $service->get_favorites($user_id);

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function save_college_favorite($request) {
        $service = new Student_Services_College_Recommendation();
        $user_id = get_current_user_id();

        $result = $service->save_favorite($user_id, $request->get_param('college_id'));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Eligibility Calculator Callbacks
    public static function calculate_eligibility($request) {
        $service = new Student_Services_Eligibility_Calculator();
        $user_id = get_current_user_id();

        $result = $service->calculate_eligibility($user_id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Entrance Exam Preparation Callbacks
    public static function get_entrance_exams($request) {
        $service = new Student_Services_Entrance_Exam_Prep();
        $result = $service->get_available_exams();

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function get_study_plan($request) {
        $service = new Student_Services_Entrance_Exam_Prep();
        $user_id = get_current_user_id();

        $result = $service->get_study_plan(
            $request->get_param('exam_id'),
            $request->get_param('preparation_months')
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function submit_practice_test($request) {
        $service = new Student_Services_Entrance_Exam_Prep();
        $user_id = get_current_user_id();

        $result = $service->submit_practice_test($user_id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Language Proficiency Tests Callbacks
    public static function get_language_tests($request) {
        $service = new Student_Services_Language_Proficiency();
        $result = $service->get_test_info($request->get_param('test_type'));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function submit_language_practice($request) {
        $service = new Student_Services_Language_Proficiency();
        $user_id = get_current_user_id();

        $result = $service->submit_practice_test($user_id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Course Discovery Callbacks
    public static function discover_courses($request) {
        $service = new Student_Services_Course_Discovery();
        $user_id = get_current_user_id();

        $result = $service->get_recommended_courses($user_id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Interview Preparation Callbacks
    public static function get_interview_questions($request) {
        $service = new Student_Services_Interview_Prep();
        $result = $service->get_questions($request->get_param('category'));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function book_mock_interview($request) {
        $service = new Student_Services_Interview_Prep();
        $user_id = get_current_user_id();

        $result = $service->book_mock_interview($user_id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Financial Aid Calculator Callbacks
    public static function calculate_financial_aid($request) {
        $service = new Student_Services_Financial_Aid_Calculator();
        $result = $service->calculate_financial_need($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Loan Calculator Callbacks
    public static function calculate_emi($request) {
        $service = new Student_Services_Loan_Calculator();
        $result = $service->calculate_emi(
            $request->get_param('loan_amount'),
            $request->get_param('interest_rate'),
            $request->get_param('tenure_months')
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function compare_loan_schemes($request) {
        $service = new Student_Services_Loan_Calculator();
        $result = $service->compare_loan_schemes($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // College Cost Comparison Callbacks
    public static function compare_college_costs($request) {
        $service = new Student_Services_College_Cost_Comparison();
        $result = $service->compare_colleges($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // GPA Calculator Callbacks
    public static function calculate_gpa_new($request) {
        $service = new Student_Services_GPA_Calculator();
        $result = $service->calculate_gpa(
            $request->get_param('grades'),
            $request->get_param('scale')
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function convert_grade($request) {
        $service = new Student_Services_GPA_Calculator();
        $result = $service->convert_grade(
            $request->get_param('grade'),
            $request->get_param('from_system'),
            $request->get_param('to_system')
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Admission Counseling Callbacks
    public static function get_counselors($request) {
        $service = new Student_Services_Admission_Counseling();
        $result = $service->get_available_counselors($request->get_param('specialization'));

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function book_counseling_session($request) {
        $service = new Student_Services_Admission_Counseling();
        $user_id = get_current_user_id();

        $result = $service->book_session(
            $user_id,
            $request->get_param('counselor_id'),
            $request->get_params()
        );

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    public static function get_counseling_packages($request) {
        $service = new Student_Services_Admission_Counseling();
        $result = $service->get_counseling_packages();

        if (is_wp_error($result)) {
            return $result;
        }

        return new WP_REST_Response($result, 200);
    }

    // Mentorship Callbacks
    public static function get_mentors($request) {
        $service = new Student_Services_Mentorship();
        $result = $service->get_available_mentors($request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function book_mentorship_session($request) {
        $service = new Student_Services_Mentorship();
        $user_id = get_current_user_id();

        $result = $service->book_session(
            $user_id,
            $request->get_param('mentor_id'),
            $request->get_params()
        );

        return new WP_REST_Response($result, 200);
    }

    public static function get_my_mentorship_sessions($request) {
        $service = new Student_Services_Mentorship();
        $user_id = get_current_user_id();

        $result = $service->get_my_sessions($user_id);

        return new WP_REST_Response($result, 200);
    }

    public static function submit_mentorship_feedback($request) {
        $service = new Student_Services_Mentorship();
        $user_id = get_current_user_id();

        $result = $service->submit_feedback(
            $user_id,
            $request->get_param('session_id'),
            $request->get_params()
        );

        return new WP_REST_Response($result, 200);
    }

    // Webinars & Workshops Callbacks
    public static function get_upcoming_webinars($request) {
        $service = new Student_Services_Webinars();
        $result = $service->get_upcoming_webinars($request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function register_webinar($request) {
        $service = new Student_Services_Webinars();
        $user_id = get_current_user_id();

        $result = $service->register_webinar($user_id, $request->get_param('webinar_id'));

        return new WP_REST_Response($result, 200);
    }

    public static function get_my_webinars($request) {
        $service = new Student_Services_Webinars();
        $user_id = get_current_user_id();

        $result = $service->get_my_webinars($user_id);

        return new WP_REST_Response($result, 200);
    }

    public static function get_webinar_categories($request) {
        $service = new Student_Services_Webinars();
        $result = $service->get_categories();

        return new WP_REST_Response($result, 200);
    }

    // Student Forum Callbacks
    public static function get_forum_posts($request) {
        $service = new Student_Services_Forum();
        $result = $service->get_posts(
            $request->get_param('category_id'),
            $request->get_param('limit') ?: 20,
            $request->get_param('offset') ?: 0
        );

        return new WP_REST_Response($result, 200);
    }

    public static function create_forum_post($request) {
        $service = new Student_Services_Forum();
        $user_id = get_current_user_id();

        $result = $service->create_post($user_id, $request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function get_forum_replies($request) {
        $service = new Student_Services_Forum();
        $post_id = $request->get_param('post_id');

        $result = $service->get_replies($post_id);

        return new WP_REST_Response($result, 200);
    }

    public static function create_forum_reply($request) {
        $service = new Student_Services_Forum();
        $user_id = get_current_user_id();

        $result = $service->create_reply(
            $user_id,
            $request->get_param('post_id'),
            $request->get_param('content')
        );

        return new WP_REST_Response($result, 200);
    }

    public static function toggle_forum_like($request) {
        $service = new Student_Services_Forum();
        $user_id = get_current_user_id();

        $result = $service->toggle_like($user_id, $request->get_param('post_id'));

        return new WP_REST_Response($result, 200);
    }

    public static function get_forum_categories($request) {
        $service = new Student_Services_Forum();
        $result = $service->get_categories();

        return new WP_REST_Response($result, 200);
    }

    // FAQ Search Callbacks
    public static function search_faqs($request) {
        $service = new Student_Services_FAQ();
        $query = $request->get_param('q');

        if ($query) {
            $result = $service->search_faqs($query);
        } else {
            $result = $service->get_faqs($request->get_param('category_id'));
        }

        return new WP_REST_Response($result, 200);
    }

    public static function get_faq_categories($request) {
        $service = new Student_Services_FAQ();
        $result = $service->get_categories();

        return new WP_REST_Response($result, 200);
    }

    public static function mark_faq_helpful($request) {
        $service = new Student_Services_FAQ();
        $faq_id = $request->get_param('faq_id');

        $result = $service->mark_helpful($faq_id);

        return new WP_REST_Response($result, 200);
    }

    public static function submit_faq_question($request) {
        $service = new Student_Services_FAQ();
        $user_id = get_current_user_id();

        $result = $service->submit_question($user_id, $request->get_params());

        return new WP_REST_Response($result, 200);
    }

    // Academic Calendar Callbacks
    public static function get_calendar_events($request) {
        $service = new Student_Services_Academic_Calendar();
        $result = $service->get_calendar_events(
            $request->get_param('start_date'),
            $request->get_param('end_date')
        );

        return new WP_REST_Response($result, 200);
    }

    public static function add_personal_event($request) {
        $service = new Student_Services_Academic_Calendar();
        $user_id = get_current_user_id();

        $result = $service->add_personal_event($user_id, $request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function get_personal_events($request) {
        $service = new Student_Services_Academic_Calendar();
        $user_id = get_current_user_id();

        $result = $service->get_personal_events(
            $user_id,
            $request->get_param('start_date'),
            $request->get_param('end_date')
        );

        return new WP_REST_Response($result, 200);
    }

    public static function get_upcoming_deadlines($request) {
        $service = new Student_Services_Academic_Calendar();
        $result = $service->get_upcoming_deadlines($request->get_param('days') ?: 7);

        return new WP_REST_Response($result, 200);
    }

    // Enhanced Scholarship Callbacks
    public static function search_scholarships($request) {
        $service = new Student_Services_Enhanced_Scholarship();
        $result = $service->search_scholarships($request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function get_scholarship_recommendations($request) {
        $service = new Student_Services_Enhanced_Scholarship();
        $user_id = get_current_user_id();

        $result = $service->get_recommendations($user_id, $request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function apply_scholarship($request) {
        $service = new Student_Services_Enhanced_Scholarship();
        $user_id = get_current_user_id();

        $result = $service->apply_scholarship(
            $user_id,
            $request->get_param('scholarship_id'),
            $request->get_params()
        );

        return new WP_REST_Response($result, 200);
    }

    public static function get_my_scholarship_applications($request) {
        $service = new Student_Services_Enhanced_Scholarship();
        $user_id = get_current_user_id();

        $result = $service->get_my_applications($user_id);

        return new WP_REST_Response($result, 200);
    }

    public static function save_scholarship($request) {
        $service = new Student_Services_Enhanced_Scholarship();
        $user_id = get_current_user_id();

        $result = $service->save_scholarship($user_id, $request->get_param('scholarship_id'));

        return new WP_REST_Response($result, 200);
    }

    // Service Request Form Callbacks
    public static function get_service_types($request) {
        $service = new Student_Services_Service_Request();
        $result = $service->get_service_types();

        return new WP_REST_Response($result, 200);
    }

    public static function create_service_request($request) {
        $service = new Student_Services_Service_Request();
        $user_id = get_current_user_id();

        $result = $service->create_request($user_id, $request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function initiate_service_payment($request) {
        $service = new Student_Services_Service_Request();
        $user_id = get_current_user_id();

        $result = $service->initiate_payment($user_id, $request->get_param('request_id'));

        return new WP_REST_Response($result, 200);
    }

    public static function get_my_service_requests($request) {
        $service = new Student_Services_Service_Request();
        $user_id = get_current_user_id();

        $result = $service->get_my_requests($user_id);

        return new WP_REST_Response($result, 200);
    }

    // Document Templates Callbacks
    public static function get_document_templates($request) {
        $service = new Student_Services_Document_Templates();
        $result = $service->get_templates();

        return new WP_REST_Response($result, 200);
    }

    public static function generate_document($request) {
        $service = new Student_Services_Document_Templates();
        $user_id = get_current_user_id();

        $result = $service->generate_document(
            $user_id,
            $request->get_param('template_id'),
            $request->get_params()
        );

        return new WP_REST_Response($result, 200);
    }

    public static function get_my_documents($request) {
        $service = new Student_Services_Document_Templates();
        $user_id = get_current_user_id();

        $result = $service->get_my_documents($user_id);

        return new WP_REST_Response($result, 200);
    }

    public static function update_document($request) {
        $service = new Student_Services_Document_Templates();
        $user_id = get_current_user_id();

        $result = $service->update_document(
            $request->get_param('document_id'),
            $user_id,
            $request->get_param('content')
        );

        return new WP_REST_Response($result, 200);
    }

    // Accommodation Finder Callbacks
    public static function search_accommodations($request) {
        $service = new Student_Services_Accommodation();
        $result = $service->search_accommodations($request->get_params());

        return new WP_REST_Response($result, 200);
    }

    public static function get_accommodation_details($request) {
        $service = new Student_Services_Accommodation();
        $accommodation_id = $request->get_param('id');

        $result = $service->get_accommodation($accommodation_id);

        return new WP_REST_Response($result, 200);
    }

    public static function request_accommodation_visit($request) {
        $service = new Student_Services_Accommodation();
        $user_id = get_current_user_id();

        $result = $service->request_visit(
            $user_id,
            $request->get_param('accommodation_id'),
            $request->get_params()
        );

        return new WP_REST_Response($result, 200);
    }

    public static function save_accommodation_wishlist($request) {
        $service = new Student_Services_Accommodation();
        $user_id = get_current_user_id();

        $result = $service->save_to_wishlist($user_id, $request->get_param('accommodation_id'));

        return new WP_REST_Response($result, 200);
    }

    public static function get_accommodation_wishlist($request) {
        $service = new Student_Services_Accommodation();
        $user_id = get_current_user_id();

        $result = $service->get_wishlist($user_id);

        return new WP_REST_Response($result, 200);
    }

    // CollegeKampus Blog Callbacks (v4.0.0)
    public static function get_blog_posts($request) {
        $service = new Student_Services_Blog();
        $result = $service->get_posts($request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function get_blog_post($request) {
        $service = new Student_Services_Blog();
        $result = $service->get_post($request->get_param('id'));
        return new WP_REST_Response($result, 200);
    }

    public static function like_blog_post($request) {
        $service = new Student_Services_Blog();
        $user_id = get_current_user_id();
        $result = $service->like_post($user_id, $request->get_param('post_id'));
        return new WP_REST_Response($result, 200);
    }

    public static function add_blog_comment($request) {
        $service = new Student_Services_Blog();
        $user_id = get_current_user_id();
        $result = $service->add_comment($user_id, $request->get_param('post_id'), $request->get_param('content'));
        return new WP_REST_Response($result, 200);
    }

    public static function bookmark_blog_post($request) {
        $service = new Student_Services_Blog();
        $user_id = get_current_user_id();
        $result = $service->bookmark_post($user_id, $request->get_param('post_id'));
        return new WP_REST_Response($result, 200);
    }

    // Study Abroad Callbacks (v4.0.0)
    public static function get_study_abroad_countries($request) {
        $service = new Student_Services_Study_Abroad();
        $result = $service->get_countries();
        return new WP_REST_Response(array('success' => true, 'data' => $result), 200);
    }

    public static function search_universities($request) {
        $service = new Student_Services_Study_Abroad();
        $result = $service->search_universities($request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function get_study_abroad_programs($request) {
        $service = new Student_Services_Study_Abroad();
        $result = $service->get_programs($request->get_param('university_id'), $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function submit_study_abroad_application($request) {
        $service = new Student_Services_Study_Abroad();
        $user_id = get_current_user_id();
        $result = $service->submit_application($user_id, $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function save_study_abroad_wishlist($request) {
        $service = new Student_Services_Study_Abroad();
        $user_id = get_current_user_id();
        $result = $service->save_to_wishlist($user_id, $request->get_param('university_id'));
        return new WP_REST_Response($result, 200);
    }

    // Placement Statistics Callbacks (v4.0.0)
    public static function get_placement_stats($request) {
        $service = new Student_Services_Placement_Stats();
        $result = $service->get_college_placements($request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function get_top_recruiters($request) {
        $service = new Student_Services_Placement_Stats();
        $result = $service->get_top_recruiters($request->get_param('college_id'));
        return new WP_REST_Response($result, 200);
    }

    public static function compare_placements($request) {
        $service = new Student_Services_Placement_Stats();
        $result = $service->compare_colleges($request->get_param('college_ids'));
        return new WP_REST_Response($result, 200);
    }

    public static function get_placement_trends($request) {
        $service = new Student_Services_Placement_Stats();
        $result = $service->get_placement_trends($request->get_param('college_id'));
        return new WP_REST_Response($result, 200);
    }

    // Alumni Network Callbacks (v4.0.0)
    public static function search_alumni($request) {
        $service = new Student_Services_Alumni_Network();
        $result = $service->search_alumni($request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function connect_with_alumni($request) {
        $service = new Student_Services_Alumni_Network();
        $user_id = get_current_user_id();
        $result = $service->connect_with_alumni($user_id, $request->get_param('alumni_id'), $request->get_param('message'));
        return new WP_REST_Response($result, 200);
    }

    public static function request_alumni_mentorship($request) {
        $service = new Student_Services_Alumni_Network();
        $user_id = get_current_user_id();
        $result = $service->request_mentorship($user_id, $request->get_param('alumni_id'), $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function send_alumni_message($request) {
        $service = new Student_Services_Alumni_Network();
        $user_id = get_current_user_id();
        $result = $service->send_message($user_id, $request->get_param('alumni_id'), $request->get_param('message'));
        return new WP_REST_Response($result, 200);
    }

    public static function get_alumni_success_stories($request) {
        $service = new Student_Services_Alumni_Network();
        $result = $service->get_success_stories($request->get_param('limit') ?? 10);
        return new WP_REST_Response($result, 200);
    }

    // Testimonials Callbacks (v4.0.0)
    public static function get_testimonials($request) {
        $service = new Student_Services_Testimonials();
        $result = $service->get_testimonials($request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function submit_testimonial($request) {
        $service = new Student_Services_Testimonials();
        $user_id = get_current_user_id();
        $result = $service->submit_testimonial($user_id, $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function like_testimonial($request) {
        $service = new Student_Services_Testimonials();
        $user_id = get_current_user_id();
        $result = $service->like_testimonial($user_id, $request->get_param('testimonial_id'));
        return new WP_REST_Response($result, 200);
    }

    // Referral & Rewards Callbacks (v4.0.0)
    public static function get_referral_code($request) {
        $service = new Student_Services_Referral_Rewards();
        $user_id = get_current_user_id();
        $result = $service->get_referral_code($user_id);
        return new WP_REST_Response(array('success' => true, 'code' => $result), 200);
    }

    public static function apply_referral_code($request) {
        $service = new Student_Services_Referral_Rewards();
        $user_id = get_current_user_id();
        $result = $service->apply_referral_code($user_id, $request->get_param('code'));
        return new WP_REST_Response($result, 200);
    }

    public static function get_points_balance($request) {
        $service = new Student_Services_Referral_Rewards();
        $user_id = get_current_user_id();
        $result = $service->get_points_balance($user_id);
        return new WP_REST_Response(array('success' => true, 'points' => $result), 200);
    }

    public static function get_rewards_catalog($request) {
        $service = new Student_Services_Referral_Rewards();
        $result = $service->get_rewards_catalog();
        return new WP_REST_Response(array('success' => true, 'data' => $result), 200);
    }

    public static function redeem_reward($request) {
        $service = new Student_Services_Referral_Rewards();
        $user_id = get_current_user_id();
        $result = $service->redeem_reward($user_id, $request->get_param('reward_id'), $request->get_param('points_required'));
        return new WP_REST_Response($result, 200);
    }

    // Job Board Callbacks (v4.0.0)
    public static function search_jobs($request) {
        $service = new Student_Services_Job_Board();
        $result = $service->search_jobs($request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function apply_for_job($request) {
        $service = new Student_Services_Job_Board();
        $user_id = get_current_user_id();
        $result = $service->apply_for_job($user_id, $request->get_param('job_id'), $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function save_job($request) {
        $service = new Student_Services_Job_Board();
        $user_id = get_current_user_id();
        $result = $service->save_job($user_id, $request->get_param('job_id'));
        return new WP_REST_Response($result, 200);
    }

    public static function get_my_job_applications($request) {
        $service = new Student_Services_Job_Board();
        $user_id = get_current_user_id();
        $result = $service->get_my_applications($user_id, $request->get_param('status'));
        return new WP_REST_Response($result, 200);
    }

    public static function subscribe_job_alerts($request) {
        $service = new Student_Services_Job_Board();
        $user_id = get_current_user_id();
        $result = $service->subscribe_to_alerts($user_id, $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    // Student Dashboard Callbacks (v4.0.0)
    public static function get_student_dashboard($request) {
        $service = new Student_Services_Student_Dashboard();
        $user_id = get_current_user_id();
        $result = $service->get_dashboard_overview($user_id);
        return new WP_REST_Response($result, 200);
    }

    public static function update_student_profile($request) {
        $service = new Student_Services_Student_Dashboard();
        $user_id = get_current_user_id();
        $result = $service->update_profile($user_id, $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function log_student_activity($request) {
        $service = new Student_Services_Student_Dashboard();
        $user_id = get_current_user_id();
        $result = $service->log_activity($user_id, $request->get_param('type'), $request->get_param('description'), $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    // Employer Dashboard Callbacks (v4.0.0)
    public static function register_employer($request) {
        $service = new Student_Services_Employer_Dashboard();
        $user_id = get_current_user_id();
        $result = $service->register_employer($user_id, $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function get_employer_dashboard($request) {
        $service = new Student_Services_Employer_Dashboard();
        $user_id = get_current_user_id();

        // Get employer ID from user
        global $wpdb;
        $employer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ss_employers WHERE user_id = %d",
            $user_id
        ));

        if (!$employer) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Employer not found'), 404);
        }

        $result = $service->get_employer_overview($employer->id);
        return new WP_REST_Response($result, 200);
    }

    public static function post_job($request) {
        $service = new Student_Services_Employer_Dashboard();
        $user_id = get_current_user_id();

        global $wpdb;
        $employer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ss_employers WHERE user_id = %d",
            $user_id
        ));

        if (!$employer) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Employer not found'), 404);
        }

        $result = $service->post_job($employer->id, $request->get_params());
        return new WP_REST_Response($result, 200);
    }

    public static function get_job_applications($request) {
        $service = new Student_Services_Employer_Dashboard();
        $user_id = get_current_user_id();

        global $wpdb;
        $employer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ss_employers WHERE user_id = %d",
            $user_id
        ));

        if (!$employer) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Employer not found'), 404);
        }

        $result = $service->get_job_applications($employer->id, $request->get_param('job_id'), $request->get_param('status'));
        return new WP_REST_Response($result, 200);
    }

    public static function update_application_status($request) {
        $service = new Student_Services_Employer_Dashboard();
        $user_id = get_current_user_id();

        global $wpdb;
        $employer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ss_employers WHERE user_id = %d",
            $user_id
        ));

        if (!$employer) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Employer not found'), 404);
        }

        $result = $service->update_application_status($employer->id, $request->get_param('application_id'), $request->get_param('status'), $request->get_param('notes'));
        return new WP_REST_Response($result, 200);
    }

    // Subscription Plans Callbacks (v4.0.0)
    public static function get_subscription_plans($request) {
        $service = new Student_Services_Subscription_Plans();
        $result = $service->get_plans();
        return new WP_REST_Response(array('success' => true, 'data' => $result), 200);
    }

    public static function subscribe_to_plan($request) {
        $service = new Student_Services_Subscription_Plans();
        $user_id = get_current_user_id();
        $result = $service->subscribe($user_id, $request->get_param('plan_id'), $request->get_param('billing_cycle'));
        return new WP_REST_Response($result, 200);
    }

    public static function check_feature_access($request) {
        $service = new Student_Services_Subscription_Plans();
        $user_id = get_current_user_id();
        $result = $service->has_feature_access($user_id, $request->get_param('feature'));
        return new WP_REST_Response(array('success' => true, 'has_access' => $result), 200);
    }

    public static function cancel_subscription($request) {
        $service = new Student_Services_Subscription_Plans();
        $user_id = get_current_user_id();
        $result = $service->cancel_subscription($user_id);
        return new WP_REST_Response($result, 200);
    }
}
