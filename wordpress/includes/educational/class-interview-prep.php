<?php
/**
 * Interview Preparation Service
 * Comprehensive interview preparation with practice questions, tips, and mock interviews
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Interview_Prep extends Student_Services_Base_Service {

    public function get_interview_categories() {
        return $this->success(array(
            array('id' => 'technical', 'name' => 'Technical Interviews', 'questions' => 500),
            array('id' => 'hr', 'name' => 'HR Interviews', 'questions' => 200),
            array('id' => 'behavioral', 'name' => 'Behavioral Interviews', 'questions' => 150),
            array('id' => 'case-study', 'name' => 'Case Study Interviews', 'questions' => 100),
            array('id' => 'group', 'name' => 'Group Discussion', 'questions' => 80)
        ));
    }

    public function get_practice_questions($category, $difficulty = 'medium', $limit = 20) {
        $questions = array();
        for ($i = 1; $i <= $limit; $i++) {
            $questions[] = array(
                'id' => $i,
                'question' => 'Sample question ' . $i . ' for ' . $category,
                'category' => $category,
                'difficulty' => $difficulty,
                'tips' => 'Structure your answer using STAR method',
                'sample_answer' => 'This is a sample answer...'
            );
        }
        return $this->success($questions);
    }

    public function schedule_mock_interview($user_id, $interview_type, $preferred_date) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $mock_id = wp_insert_post(array(
            'post_type' => 'ss_mock_interview',
            'post_author' => $user_id,
            'post_title' => 'Mock Interview - ' . $interview_type,
            'post_status' => 'publish'
        ));

        update_post_meta($mock_id, '_interview_type', $interview_type);
        update_post_meta($mock_id, '_preferred_date', $preferred_date);
        update_post_meta($mock_id, '_status', 'scheduled');

        return $this->success(array(
            'mock_id' => $mock_id,
            'interviewer' => 'Expert Interviewer',
            'date' => $preferred_date,
            'platform' => 'Zoom'
        ));
    }

    public function get_interview_tips($category) {
        $tips = array(
            'technical' => array(
                'Practice coding daily',
                'Explain your thought process clearly',
                'Ask clarifying questions',
                'Test your code thoroughly'
            ),
            'hr' => array(
                'Research the company thoroughly',
                'Prepare STAR method answers',
                'Be honest and authentic',
                'Show enthusiasm for the role'
            )
        );

        return $this->success($tips[$category] ?? $tips['hr']);
    }

    public function track_preparation($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        return $this->success(array(
            'questions_practiced' => rand(50, 200),
            'mock_interviews' => rand(2, 10),
            'hours_practiced' => rand(10, 50),
            'confidence_level' => rand(60, 95) . '%'
        ));
    }
}
