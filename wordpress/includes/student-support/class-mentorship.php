<?php
/**
 * Mentorship Service
 * Find Your Mentor - Connect with experienced students and professionals
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Mentorship extends Student_Services_Base_Service {

    /**
     * Get available mentors
     */
    public function get_available_mentors($filters = array()) {
        // Mock data - in production, this would query the database
        $mentors = array(
            array(
                'id' => 1,
                'name' => 'Dr. Rajesh Kumar',
                'designation' => 'Senior Software Engineer',
                'company' => 'Google India',
                'expertise' => array('Computer Science', 'Software Engineering', 'Career Guidance'),
                'experience_years' => 12,
                'education' => 'PhD in Computer Science, IIT Delhi',
                'rating' => 4.9,
                'total_mentees' => 45,
                'availability' => 'Available',
                'session_fee' => 0, // Free mentorship
                'bio' => 'Passionate about helping students navigate their tech careers.',
                'image' => ''
            ),
            array(
                'id' => 2,
                'name' => 'Priya Sharma',
                'designation' => 'Product Manager',
                'company' => 'Microsoft',
                'expertise' => array('MBA', 'Product Management', 'Business Strategy'),
                'experience_years' => 8,
                'education' => 'MBA from IIM Ahmedabad',
                'rating' => 4.8,
                'total_mentees' => 32,
                'availability' => 'Available',
                'session_fee' => 0,
                'bio' => 'Helping students transition from engineering to product management.',
                'image' => ''
            ),
            array(
                'id' => 3,
                'name' => 'Amit Patel',
                'designation' => 'Research Scientist',
                'company' => 'ISRO',
                'expertise' => array('Aerospace Engineering', 'Research', 'PhD Guidance'),
                'experience_years' => 15,
                'education' => 'PhD in Aerospace Engineering, IISc Bangalore',
                'rating' => 4.9,
                'total_mentees' => 28,
                'availability' => 'Limited',
                'session_fee' => 0,
                'bio' => 'Guiding students pursuing research in aerospace and related fields.',
                'image' => ''
            ),
            array(
                'id' => 4,
                'name' => 'Sneha Reddy',
                'designation' => 'Final Year Student',
                'company' => 'IIT Bombay',
                'expertise' => array('JEE Preparation', 'Campus Life', 'Academic Tips'),
                'experience_years' => 3,
                'education' => 'BTech Computer Science, IIT Bombay',
                'rating' => 4.7,
                'total_mentees' => 52,
                'availability' => 'Available',
                'session_fee' => 0,
                'bio' => 'Peer mentor helping students with JEE prep and college life.',
                'image' => ''
            )
        );

        // Filter by expertise if provided
        if (!empty($filters['expertise'])) {
            $mentors = array_filter($mentors, function($mentor) use ($filters) {
                return in_array($filters['expertise'], $mentor['expertise']);
            });
        }

        return $this->success(array_values($mentors));
    }

    /**
     * Book mentorship session
     */
    public function book_session($user_id, $mentor_id, $session_data) {
        global $wpdb;

        $required = array('preferred_date', 'preferred_time', 'topic');
        if (!$this->validate_required($session_data, $required)) {
            return $this->error('Missing required fields');
        }

        $table_name = $wpdb->prefix . 'ss_mentorship_sessions';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'mentor_id' => $mentor_id,
                'preferred_date' => sanitize_text_field($session_data['preferred_date']),
                'preferred_time' => sanitize_text_field($session_data['preferred_time']),
                'topic' => sanitize_text_field($session_data['topic']),
                'description' => isset($session_data['description']) ? sanitize_textarea_field($session_data['description']) : '',
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to book session');
        }

        return $this->success(array(
            'session_id' => $wpdb->insert_id,
            'message' => 'Mentorship session request submitted. The mentor will confirm shortly.'
        ));
    }

    /**
     * Get user's mentorship sessions
     */
    public function get_my_sessions($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_mentorship_sessions';

        $sessions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        return $this->success($sessions);
    }

    /**
     * Submit session feedback
     */
    public function submit_feedback($user_id, $session_id, $feedback_data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_mentorship_feedback';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'session_id' => $session_id,
                'rating' => floatval($feedback_data['rating']),
                'feedback_text' => sanitize_textarea_field($feedback_data['feedback_text']),
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%f', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to submit feedback');
        }

        return $this->success(array('message' => 'Feedback submitted successfully'));
    }
}
