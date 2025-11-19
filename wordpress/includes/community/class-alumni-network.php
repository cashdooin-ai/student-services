<?php
/**
 * Alumni Network
 * Connect with successful alumni from your dream colleges
 * Get career guidance, mentorship, and insights
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Alumni_Network extends Student_Services_Base_Service {

    /**
     * Search alumni
     */
    public function search_alumni($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_profiles';

        $where = array('status = "active"', 'is_verified = 1');

        if (!empty($filters['college'])) {
            $where[] = $wpdb->prepare('college = %s', $filters['college']);
        }

        if (!empty($filters['graduation_year'])) {
            $where[] = $wpdb->prepare('graduation_year = %d', $filters['graduation_year']);
        }

        if (!empty($filters['industry'])) {
            $where[] = $wpdb->prepare('current_industry = %s', $filters['industry']);
        }

        if (!empty($filters['company'])) {
            $where[] = $wpdb->prepare('current_company LIKE %s', '%' . $wpdb->esc_like($filters['company']) . '%');
        }

        if (!empty($filters['location'])) {
            $where[] = $wpdb->prepare('location LIKE %s', '%' . $wpdb->esc_like($filters['location']) . '%');
        }

        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where[] = $wpdb->prepare('(name LIKE %s OR bio LIKE %s OR current_position LIKE %s)', $search, $search, $search);
        }

        if (isset($filters['available_for_mentorship']) && $filters['available_for_mentorship']) {
            $where[] = 'available_for_mentorship = 1';
        }

        $where_sql = implode(' AND ', $where);
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 20;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;

        $alumni = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $alumni
        );
    }

    /**
     * Get alumni profile
     */
    public function get_profile($alumni_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_profiles';

        $profile = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d AND status = 'active'",
            $alumni_id
        ));

        if (!$profile) {
            return array('success' => false, 'message' => 'Profile not found');
        }

        return array(
            'success' => true,
            'data' => $profile
        );
    }

    /**
     * Connect with alumni
     */
    public function connect_with_alumni($user_id, $alumni_id, $message = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_connections';

        // Check if already connected or pending
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND alumni_id = %d",
            $user_id,
            $alumni_id
        ));

        if ($existing) {
            return array('success' => false, 'message' => 'Connection request already exists');
        }

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'alumni_id' => $alumni_id,
            'message' => sanitize_textarea_field($message),
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'connection_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to send connection request');
    }

    /**
     * Get user's connections
     */
    public function get_my_connections($user_id) {
        global $wpdb;
        $connections_table = $wpdb->prefix . 'ss_alumni_connections';
        $alumni_table = $wpdb->prefix . 'ss_alumni_profiles';

        $connections = $wpdb->get_results($wpdb->prepare(
            "SELECT c.*, a.name, a.current_company, a.current_position, a.profile_photo, a.location
            FROM {$connections_table} c
            INNER JOIN {$alumni_table} a ON c.alumni_id = a.id
            WHERE c.user_id = %d AND c.status = 'accepted'
            ORDER BY c.updated_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $connections
        );
    }

    /**
     * Request mentorship
     */
    public function request_mentorship($user_id, $alumni_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_mentorship_requests';

        // Check if alumni is available for mentorship
        $available = $wpdb->get_var($wpdb->prepare(
            "SELECT available_for_mentorship FROM {$wpdb->prefix}ss_alumni_profiles WHERE id = %d",
            $alumni_id
        ));

        if (!$available) {
            return array('success' => false, 'message' => 'Alumni not available for mentorship');
        }

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'alumni_id' => $alumni_id,
            'subject' => sanitize_text_field($data['subject']),
            'message' => sanitize_textarea_field($data['message']),
            'areas_of_interest' => sanitize_text_field($data['areas_of_interest']),
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'request_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to send mentorship request');
    }

    /**
     * Get success stories
     */
    public function get_success_stories($limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_success_stories';

        $stories = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'published' ORDER BY featured DESC, created_at DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $stories
        );
    }

    /**
     * Get featured alumni
     */
    public function get_featured_alumni($limit = 6) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_profiles';

        $alumni = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'active' AND is_verified = 1 AND is_featured = 1 ORDER BY RAND() LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $alumni
        );
    }

    /**
     * Get alumni by college
     */
    public function get_alumni_by_college($college_name, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_profiles';

        $alumni = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE college = %s AND status = 'active' AND is_verified = 1 ORDER BY graduation_year DESC LIMIT %d",
            $college_name,
            $limit
        ));

        return array(
            'success' => true,
            'data' => $alumni
        );
    }

    /**
     * Get industries
     */
    public function get_industries() {
        return array(
            'success' => true,
            'data' => array(
                'Technology', 'Finance & Banking', 'Consulting', 'Healthcare',
                'Education', 'Manufacturing', 'Retail', 'E-commerce',
                'Media & Entertainment', 'Telecommunications', 'Energy',
                'Real Estate', 'Legal', 'Pharmaceuticals', 'Automotive',
                'Aerospace', 'Agriculture', 'Hospitality', 'Non-Profit', 'Government'
            )
        );
    }

    /**
     * Send message to alumni
     */
    public function send_message($user_id, $alumni_id, $message) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_messages';

        // Check if connection exists
        $connected = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}ss_alumni_connections
            WHERE user_id = %d AND alumni_id = %d AND status = 'accepted'",
            $user_id,
            $alumni_id
        ));

        if (!$connected) {
            return array('success' => false, 'message' => 'You must be connected to send messages');
        }

        $result = $wpdb->insert($table, array(
            'sender_id' => $user_id,
            'receiver_id' => $alumni_id,
            'message' => sanitize_textarea_field($message),
            'is_read' => 0,
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'message_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to send message');
    }

    /**
     * Get conversation
     */
    public function get_conversation($user_id, $alumni_id, $limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_alumni_messages';

        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table}
            WHERE (sender_id = %d AND receiver_id = %d) OR (sender_id = %d AND receiver_id = %d)
            ORDER BY created_at DESC
            LIMIT %d",
            $user_id, $alumni_id, $alumni_id, $user_id,
            $limit
        ));

        // Mark messages as read
        $wpdb->update(
            $table,
            array('is_read' => 1),
            array(
                'receiver_id' => $user_id,
                'sender_id' => $alumni_id
            )
        );

        return array(
            'success' => true,
            'data' => array_reverse($messages)
        );
    }
}
