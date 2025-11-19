<?php
/**
 * Study Abroad Programs
 * Explore world-class universities and programs across the globe
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Study_Abroad extends Student_Services_Base_Service {

    /**
     * Get countries
     */
    public function get_countries() {
        return array(
            'success' => true,
            'data' => array(
                array('id' => 'usa', 'name' => 'United States', 'flag' => '🇺🇸', 'universities' => 150),
                array('id' => 'uk', 'name' => 'United Kingdom', 'flag' => '🇬🇧', 'universities' => 120),
                array('id' => 'canada', 'name' => 'Canada', 'flag' => '🇨🇦', 'universities' => 90),
                array('id' => 'australia', 'name' => 'Australia', 'flag' => '🇦🇺', 'universities' => 75),
                array('id' => 'germany', 'name' => 'Germany', 'flag' => '🇩🇪', 'universities' => 60),
                array('id' => 'france', 'name' => 'France', 'flag' => '🇫🇷', 'universities' => 50),
                array('id' => 'singapore', 'name' => 'Singapore', 'flag' => '🇸🇬', 'universities' => 30),
                array('id' => 'netherlands', 'name' => 'Netherlands', 'flag' => '🇳🇱', 'universities' => 45),
                array('id' => 'ireland', 'name' => 'Ireland', 'flag' => '🇮🇪', 'universities' => 35),
                array('id' => 'new-zealand', 'name' => 'New Zealand', 'flag' => '🇳🇿', 'universities' => 28),
            )
        );
    }

    /**
     * Search universities
     */
    public function search_universities($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_universities';

        $where = array('status = "active"');

        if (!empty($filters['country'])) {
            $where[] = $wpdb->prepare('country = %s', $filters['country']);
        }

        if (!empty($filters['level'])) {
            $where[] = $wpdb->prepare('level LIKE %s', '%' . $wpdb->esc_like($filters['level']) . '%');
        }

        if (!empty($filters['field_of_study'])) {
            $where[] = $wpdb->prepare('fields_of_study LIKE %s', '%' . $wpdb->esc_like($filters['field_of_study']) . '%');
        }

        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where[] = $wpdb->prepare('(name LIKE %s OR description LIKE %s)', $search, $search);
        }

        if (isset($filters['ranking_min']) && $filters['ranking_min'] > 0) {
            $where[] = $wpdb->prepare('world_ranking >= %d', $filters['ranking_min']);
        }

        if (isset($filters['ranking_max']) && $filters['ranking_max'] > 0) {
            $where[] = $wpdb->prepare('world_ranking <= %d', $filters['ranking_max']);
        }

        $where_sql = implode(' AND ', $where);
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 20;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;

        $universities = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY world_ranking ASC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $universities
        );
    }

    /**
     * Get university details
     */
    public function get_university($university_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_universities';

        $university = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d AND status = 'active'",
            $university_id
        ));

        if (!$university) {
            return array('success' => false, 'message' => 'University not found');
        }

        return array(
            'success' => true,
            'data' => $university
        );
    }

    /**
     * Get programs for university
     */
    public function get_programs($university_id, $filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_study_abroad_programs';

        $where = array(
            $wpdb->prepare('university_id = %d', $university_id),
            'status = "active"'
        );

        if (!empty($filters['level'])) {
            $where[] = $wpdb->prepare('level = %s', $filters['level']);
        }

        if (!empty($filters['field'])) {
            $where[] = $wpdb->prepare('field_of_study = %s', $filters['field']);
        }

        $where_sql = implode(' AND ', $where);

        $programs = $wpdb->get_results("SELECT * FROM {$table} WHERE {$where_sql} ORDER BY name ASC");

        return array(
            'success' => true,
            'data' => $programs
        );
    }

    /**
     * Save university to wishlist
     */
    public function save_to_wishlist($user_id, $university_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_study_abroad_wishlist';

        // Check if already saved
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND university_id = %d",
            $user_id,
            $university_id
        ));

        if ($existing) {
            $wpdb->delete($table, array('id' => $existing));
            return array('success' => true, 'action' => 'removed');
        } else {
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'university_id' => $university_id,
                'created_at' => current_time('mysql')
            ));
            return array('success' => true, 'action' => 'added');
        }
    }

    /**
     * Get user's wishlist
     */
    public function get_wishlist($user_id) {
        global $wpdb;
        $wishlist_table = $wpdb->prefix . 'ss_study_abroad_wishlist';
        $universities_table = $wpdb->prefix . 'ss_universities';

        $universities = $wpdb->get_results($wpdb->prepare(
            "SELECT u.* FROM {$universities_table} u
            INNER JOIN {$wishlist_table} w ON u.id = w.university_id
            WHERE w.user_id = %d AND u.status = 'active'
            ORDER BY w.created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $universities
        );
    }

    /**
     * Submit application
     */
    public function submit_application($user_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_study_abroad_applications';

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'university_id' => intval($data['university_id']),
            'program_id' => isset($data['program_id']) ? intval($data['program_id']) : null,
            'full_name' => sanitize_text_field($data['full_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'academic_level' => sanitize_text_field($data['academic_level']),
            'field_of_interest' => sanitize_text_field($data['field_of_interest']),
            'intended_intake' => sanitize_text_field($data['intended_intake']),
            'message' => sanitize_textarea_field($data['message']),
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'application_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to submit application');
    }

    /**
     * Get user's applications
     */
    public function get_my_applications($user_id) {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'ss_study_abroad_applications';
        $universities_table = $wpdb->prefix . 'ss_universities';

        $applications = $wpdb->get_results($wpdb->prepare(
            "SELECT a.*, u.name as university_name, u.country, u.logo_url
            FROM {$applications_table} a
            LEFT JOIN {$universities_table} u ON a.university_id = u.id
            WHERE a.user_id = %d
            ORDER BY a.created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $applications
        );
    }

    /**
     * Get popular destinations
     */
    public function get_popular_destinations($limit = 6) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_universities';

        $countries = $wpdb->get_results($wpdb->prepare(
            "SELECT country, COUNT(*) as university_count
            FROM {$table}
            WHERE status = 'active'
            GROUP BY country
            ORDER BY university_count DESC
            LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $countries
        );
    }
}
