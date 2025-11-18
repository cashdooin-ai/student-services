<?php
/**
 * Course Discovery Service
 * Explore curated courses tailored to goals and interests with AI recommendations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Course_Discovery extends Student_Services_Base_Service {

    public function get_recommended_courses($user_id, $preferences) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $field = isset($preferences['field']) ? $this->sanitize($preferences['field']) : '';
        $level = isset($preferences['level']) ? $this->sanitize($preferences['level']) : 'undergraduate';
        $budget = isset($preferences['budget']) ? floatval($preferences['budget']) : 0;

        $courses = $this->find_matching_courses($field, $level, $budget);

        return $this->success(array(
            'total_courses' => count($courses),
            'courses' => $courses,
            'personalized_message' => $this->generate_recommendation_message($field, count($courses))
        ));
    }

    private function find_matching_courses($field, $level, $budget) {
        return array(
            array(
                'id' => 1,
                'name' => 'Computer Science',
                'field' => $field,
                'level' => $level,
                'duration' => '4 years',
                'avg_fee_per_year' => 50000,
                'career_prospects' => 'Excellent',
                'avg_salary' => 80000,
                'top_colleges' => array('MIT', 'Stanford', 'CMU'),
                'match_score' => 95
            ),
            array(
                'id' => 2,
                'name' => 'Data Science',
                'field' => $field,
                'level' => $level,
                'duration' => '2 years',
                'avg_fee_per_year' => 45000,
                'career_prospects' => 'Excellent',
                'avg_salary' => 90000,
                'top_colleges' => array('UC Berkeley', 'Harvard', 'MIT'),
                'match_score' => 92
            )
        );
    }

    private function generate_recommendation_message($field, $count) {
        return sprintf('Based on your interest in %s, we found %d excellent courses for you!', $field, $count);
    }

    public function get_course_details($course_id) {
        return $this->success(array(
            'id' => $course_id,
            'name' => 'Computer Science',
            'description' => 'Comprehensive program covering software development, algorithms, and systems',
            'curriculum' => array('Programming', 'Data Structures', 'Algorithms', 'Databases'),
            'career_paths' => array('Software Engineer', 'Data Scientist', 'System Architect'),
            'colleges_offering' => 150,
            'avg_placement_rate' => 95
        ));
    }
}
