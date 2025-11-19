<?php
/**
 * Webinars & Workshops Service
 * Join live sessions with experts to boost your college admission journey and career prospects
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Webinars extends Student_Services_Base_Service {

    /**
     * Get upcoming webinars
     */
    public function get_upcoming_webinars($filters = array()) {
        // Mock data - in production, this would query the database
        $webinars = array(
            array(
                'id' => 1,
                'title' => 'Cracking IIT JEE Advanced 2024',
                'speaker' => 'Prof. Anand Krishnan',
                'speaker_designation' => 'Former HOD, IIT Delhi',
                'date' => date('Y-m-d', strtotime('+5 days')),
                'time' => '18:00:00',
                'duration_minutes' => 90,
                'category' => 'Entrance Exams',
                'description' => 'Learn effective strategies and time management techniques for JEE Advanced.',
                'max_participants' => 500,
                'registered_count' => 342,
                'is_free' => true,
                'meeting_link' => '',
                'status' => 'upcoming'
            ),
            array(
                'id' => 2,
                'title' => 'Resume Building for Campus Placements',
                'speaker' => 'Neha Gupta',
                'speaker_designation' => 'HR Manager, Amazon',
                'date' => date('Y-m-d', strtotime('+3 days')),
                'time' => '17:00:00',
                'duration_minutes' => 60,
                'category' => 'Career Development',
                'description' => 'Learn how to create an ATS-friendly resume that stands out.',
                'max_participants' => 300,
                'registered_count' => 145,
                'is_free' => true,
                'meeting_link' => '',
                'status' => 'upcoming'
            ),
            array(
                'id' => 3,
                'title' => 'Study Abroad: Application Process & Scholarships',
                'speaker' => 'Dr. Vikram Singh',
                'speaker_designation' => 'Education Consultant',
                'date' => date('Y-m-d', strtotime('+7 days')),
                'time' => '19:00:00',
                'duration_minutes' => 120,
                'category' => 'Study Abroad',
                'description' => 'Complete guide to applying for international universities and securing scholarships.',
                'max_participants' => 400,
                'registered_count' => 278,
                'is_free' => true,
                'meeting_link' => '',
                'status' => 'upcoming'
            ),
            array(
                'id' => 4,
                'title' => 'GATE Preparation Strategy Workshop',
                'speaker' => 'Prof. Ramesh Kumar',
                'speaker_designation' => 'IIT Kharagpur',
                'date' => date('Y-m-d', strtotime('+10 days')),
                'time' => '16:00:00',
                'duration_minutes' => 90,
                'category' => 'Entrance Exams',
                'description' => 'Subject-wise preparation strategy and important topics for GATE 2024.',
                'max_participants' => 600,
                'registered_count' => 423,
                'is_free' => true,
                'meeting_link' => '',
                'status' => 'upcoming'
            )
        );

        // Filter by category
        if (!empty($filters['category'])) {
            $webinars = array_filter($webinars, function($webinar) use ($filters) {
                return $webinar['category'] === $filters['category'];
            });
        }

        return $this->success(array_values($webinars));
    }

    /**
     * Register for webinar
     */
    public function register_webinar($user_id, $webinar_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_webinar_registrations';

        // Check if already registered
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d AND webinar_id = %d",
            $user_id,
            $webinar_id
        ));

        if ($existing) {
            return $this->error('You are already registered for this webinar');
        }

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'webinar_id' => $webinar_id,
                'registered_at' => current_time('mysql'),
                'attendance_status' => 'registered'
            ),
            array('%d', '%d', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to register for webinar');
        }

        return $this->success(array(
            'registration_id' => $wpdb->insert_id,
            'message' => 'Successfully registered! You will receive the meeting link via email.'
        ));
    }

    /**
     * Get user's registered webinars
     */
    public function get_my_webinars($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_webinar_registrations';

        $registrations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY registered_at DESC",
            $user_id
        ));

        return $this->success($registrations);
    }

    /**
     * Get webinar categories
     */
    public function get_categories() {
        $categories = array(
            'Entrance Exams',
            'Career Development',
            'Study Abroad',
            'Skill Development',
            'Personal Development',
            'College Admissions',
            'Financial Planning'
        );

        return $this->success($categories);
    }
}
