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
}
