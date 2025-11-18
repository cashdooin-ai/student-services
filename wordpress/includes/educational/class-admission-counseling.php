<?php
/**
 * Admission Counseling Service
 * Connect with experienced counselors for personalized college admission guidance
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Admission_Counseling extends Student_Services_Base_Service {

    public function get_available_counselors($specialization = null) {
        $counselors = array(
            array(
                'id' => 1,
                'name' => 'Dr. Sarah Johnson',
                'specialization' => 'Engineering & Technology',
                'experience_years' => 15,
                'rating' => 4.9,
                'total_students' => 500,
                'success_rate' => 95,
                'availability' => 'Available',
                'fee_per_session' => 100,
                'languages' => array('English', 'Hindi')
            ),
            array(
                'id' => 2,
                'name' => 'Prof. Michael Chen',
                'specialization' => 'Business & Management',
                'experience_years' => 12,
                'rating' => 4.8,
                'total_students' => 450,
                'success_rate' => 93,
                'availability' => 'Available',
                'fee_per_session' => 120,
                'languages' => array('English', 'Mandarin')
            ),
            array(
                'id' => 3,
                'name' => 'Dr. Priya Sharma',
                'specialization' => 'Medical & Healthcare',
                'experience_years' => 18,
                'rating' => 4.95,
                'total_students' => 600,
                'success_rate' => 97,
                'availability' => 'Busy',
                'fee_per_session' => 150,
                'languages' => array('English', 'Hindi', 'Tamil')
            )
        );

        if ($specialization) {
            $counselors = array_filter($counselors, function($c) use ($specialization) {
                return stripos($c['specialization'], $specialization) !== false;
            });
        }

        return $this->success(array_values($counselors));
    }

    public function book_session($user_id, $counselor_id, $session_data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $validation = $this->validate_required($session_data, array('preferred_date', 'session_type'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        $session_id = wp_insert_post(array(
            'post_type' => 'ss_counseling_session',
            'post_author' => $user_id,
            'post_title' => 'Counseling Session',
            'post_status' => 'publish'
        ));

        update_post_meta($session_id, '_counselor_id', $counselor_id);
        update_post_meta($session_id, '_session_type', $session_data['session_type']);
        update_post_meta($session_id, '_preferred_date', $session_data['preferred_date']);
        update_post_meta($session_id, '_status', 'scheduled');
        update_post_meta($session_id, '_duration', isset($session_data['duration']) ? $session_data['duration'] : 60);

        return $this->success(array(
            'session_id' => $session_id,
            'counselor_id' => $counselor_id,
            'scheduled_date' => $session_data['preferred_date'],
            'meeting_link' => 'https://meet.university.edu/' . $session_id,
            'status' => 'scheduled'
        ));
    }

    public function get_counseling_packages() {
        return $this->success(array(
            array(
                'id' => 1,
                'name' => 'Basic Package',
                'sessions' => 3,
                'price' => 250,
                'duration_per_session' => 60,
                'features' => array(
                    'College selection guidance',
                    'Application review',
                    'Email support'
                )
            ),
            array(
                'id' => 2,
                'name' => 'Premium Package',
                'sessions' => 6,
                'price' => 450,
                'duration_per_session' => 60,
                'features' => array(
                    'Comprehensive college list',
                    'Essay review & editing',
                    'Interview preparation',
                    'Priority email support',
                    '2 mock interviews'
                ),
                'popular' => true
            ),
            array(
                'id' => 3,
                'name' => 'Complete Package',
                'sessions' => 10,
                'price' => 700,
                'duration_per_session' => 60,
                'features' => array(
                    'End-to-end admission support',
                    'Unlimited application reviews',
                    'SOP/Essay writing assistance',
                    'Interview preparation',
                    '24/7 chat support',
                    'Financial aid guidance',
                    'Visa counseling'
                )
            )
        ));
    }

    public function get_my_sessions($user_id, $status = 'all') {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $args = array(
            'post_type' => 'ss_counseling_session',
            'author' => $user_id,
            'posts_per_page' => -1
        );

        if ($status !== 'all') {
            $args['meta_query'] = array(
                array(
                    'key' => '_status',
                    'value' => $status
                )
            );
        }

        $sessions = get_posts($args);
        $result = array();

        foreach ($sessions as $session) {
            $result[] = array(
                'session_id' => $session->ID,
                'counselor_id' => get_post_meta($session->ID, '_counselor_id', true),
                'session_type' => get_post_meta($session->ID, '_session_type', true),
                'scheduled_date' => get_post_meta($session->ID, '_preferred_date', true),
                'status' => get_post_meta($session->ID, '_status', true),
                'duration' => get_post_meta($session->ID, '_duration', true),
                'notes' => get_post_meta($session->ID, '_session_notes', true)
            );
        }

        return $this->success($result);
    }

    public function submit_question($user_id, $question_data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $validation = $this->validate_required($question_data, array('subject', 'question'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        $question_id = $this->insert('counseling_questions', array(
            'user_id' => $user_id,
            'subject' => $this->sanitize($question_data['subject']),
            'question' => $this->sanitize_textarea($question_data['question']),
            'category' => isset($question_data['category']) ? $this->sanitize($question_data['category']) : 'general',
            'status' => 'pending',
            'created_date' => current_time('mysql')
        ));

        return $this->success(array(
            'question_id' => $question_id,
            'status' => 'pending',
            'estimated_response_time' => '24-48 hours'
        ));
    }

    public function get_faq() {
        return $this->success(array(
            array(
                'question' => 'How do I choose the right college?',
                'answer' => 'Consider factors like program quality, location, cost, campus culture, and career outcomes. Our counselors can help you create a personalized list.',
                'category' => 'College Selection'
            ),
            array(
                'question' => 'When should I start the application process?',
                'answer' => 'Ideally, start 12-18 months before your intended admission date. This gives you time for test prep, essay writing, and gathering recommendations.',
                'category' => 'Application Timeline'
            ),
            array(
                'question' => 'What makes a strong college application?',
                'answer' => 'Strong academics, compelling essays, meaningful extracurriculars, good test scores, and strong recommendations create a well-rounded application.',
                'category' => 'Application Strategy'
            )
        ));
    }

    public function rate_counselor($user_id, $counselor_id, $rating, $review) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $review_id = $this->insert('counselor_reviews', array(
            'user_id' => $user_id,
            'counselor_id' => $counselor_id,
            'rating' => floatval($rating),
            'review_text' => $this->sanitize_textarea($review),
            'review_date' => current_time('mysql')
        ));

        return $this->success(array('review_id' => $review_id));
    }
}
