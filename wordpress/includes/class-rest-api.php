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
}
