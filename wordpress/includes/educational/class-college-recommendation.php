<?php
/**
 * AI College Recommendation Service
 * Let AI analyze preferences and find perfect colleges
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_College_Recommendation extends Student_Services_Base_Service {

    /**
     * Get college recommendations based on student preferences
     */
    public function get_recommendations($user_id, $preferences) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $validation = $this->validate_required($preferences, array('academic_score', 'preferred_location'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        // AI-based recommendation logic (mock for now)
        $recommendations = $this->analyze_preferences($preferences);

        // Store search history
        $this->insert('college_searches', array(
            'user_id' => $user_id,
            'preferences' => wp_json_encode($preferences),
            'results_count' => count($recommendations),
            'search_date' => current_time('mysql')
        ));

        return $this->success($recommendations);
    }

    /**
     * Analyze preferences and generate recommendations
     */
    private function analyze_preferences($preferences) {
        $score = floatval($preferences['academic_score']);
        $location = $this->sanitize($preferences['preferred_location']);
        $budget = isset($preferences['budget']) ? floatval($preferences['budget']) : null;
        $field = isset($preferences['field_of_study']) ? $this->sanitize($preferences['field_of_study']) : '';

        // Mock AI recommendations based on criteria
        $colleges = array(
            array(
                'id' => 1,
                'name' => 'University of Excellence',
                'location' => $location,
                'match_score' => 95,
                'tuition_fee' => 50000,
                'acceptance_rate' => 65,
                'programs' => array('Computer Science', 'Engineering', 'Business'),
                'min_score_required' => $score - 10,
                'why_recommended' => 'Strong programs in your field with high placement rates'
            ),
            array(
                'id' => 2,
                'name' => 'Tech Institute',
                'location' => $location,
                'match_score' => 88,
                'tuition_fee' => 45000,
                'acceptance_rate' => 70,
                'programs' => array('Engineering', 'Computer Science'),
                'min_score_required' => $score - 15,
                'why_recommended' => 'Excellent research facilities and industry connections'
            ),
            array(
                'id' => 3,
                'name' => 'State University',
                'location' => $location,
                'match_score' => 82,
                'tuition_fee' => 30000,
                'acceptance_rate' => 75,
                'programs' => array('Liberal Arts', 'Sciences', 'Business'),
                'min_score_required' => $score - 20,
                'why_recommended' => 'Affordable option with diverse programs'
            )
        );

        // Filter by budget if provided
        if ($budget) {
            $colleges = array_filter($colleges, function($college) use ($budget) {
                return $college['tuition_fee'] <= $budget;
            });
        }

        return array_values($colleges);
    }

    /**
     * Save college to favorites
     */
    public function save_favorite($user_id, $college_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $favorite_id = $this->insert('college_favorites', array(
            'user_id' => $user_id,
            'college_id' => $college_id,
            'saved_date' => current_time('mysql')
        ));

        return $this->success(array('favorite_id' => $favorite_id));
    }

    /**
     * Get saved favorites
     */
    public function get_favorites($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $favorites = $this->get_results('college_favorites', array('user_id' => $user_id));
        return $this->success($favorites);
    }

    /**
     * Get search history
     */
    public function get_search_history($user_id, $limit = 10) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $table = $this->get_table('college_searches');
        $sql = $this->wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY search_date DESC LIMIT %d",
            $user_id,
            $limit
        );

        $history = $this->wpdb->get_results($sql);
        return $this->success($history);
    }
}
