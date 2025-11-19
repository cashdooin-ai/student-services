<?php
/**
 * Student Testimonials & Success Stories
 * Real stories from students who achieved their dreams
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Testimonials extends Student_Services_Base_Service {

    /**
     * Get testimonials
     */
    public function get_testimonials($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        $where = array('status = "approved"');

        if (!empty($filters['category'])) {
            $where[] = $wpdb->prepare('category = %s', $filters['category']);
        }

        if (!empty($filters['college'])) {
            $where[] = $wpdb->prepare('college LIKE %s', '%' . $wpdb->esc_like($filters['college']) . '%');
        }

        if (!empty($filters['service_used'])) {
            $where[] = $wpdb->prepare('services_used LIKE %s', '%' . $wpdb->esc_like($filters['service_used']) . '%');
        }

        if (isset($filters['featured']) && $filters['featured']) {
            $where[] = 'is_featured = 1';
        }

        $where_sql = implode(' AND ', $where);
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 12;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;

        $testimonials = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY is_featured DESC, created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $testimonials
        );
    }

    /**
     * Get single testimonial
     */
    public function get_testimonial($testimonial_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        $testimonial = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d AND status = 'approved'",
            $testimonial_id
        ));

        if (!$testimonial) {
            return array('success' => false, 'message' => 'Testimonial not found');
        }

        // Increment views
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET views = views + 1 WHERE id = %d",
            $testimonial_id
        ));

        return array(
            'success' => true,
            'data' => $testimonial
        );
    }

    /**
     * Submit testimonial
     */
    public function submit_testimonial($user_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'student_name' => sanitize_text_field($data['student_name']),
            'college' => sanitize_text_field($data['college']),
            'course' => sanitize_text_field($data['course']),
            'admission_year' => intval($data['admission_year']),
            'category' => sanitize_text_field($data['category']),
            'title' => sanitize_text_field($data['title']),
            'testimonial_text' => sanitize_textarea_field($data['testimonial_text']),
            'services_used' => sanitize_text_field($data['services_used']),
            'rating' => intval($data['rating']),
            'student_photo' => esc_url_raw($data['student_photo']),
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'testimonial_id' => $wpdb->insert_id, 'message' => 'Thank you! Your testimonial is under review.');
        }

        return array('success' => false, 'message' => 'Failed to submit testimonial');
    }

    /**
     * Get categories
     */
    public function get_categories() {
        return array(
            'success' => true,
            'data' => array(
                array('id' => 'college-admission', 'name' => 'College Admission Success', 'icon' => '🎓'),
                array('id' => 'scholarship', 'name' => 'Scholarship Success', 'icon' => '💰'),
                array('id' => 'study-abroad', 'name' => 'Study Abroad Journey', 'icon' => '✈️'),
                array('id' => 'career-guidance', 'name' => 'Career Guidance', 'icon' => '💼'),
                array('id' => 'entrance-exam', 'name' => 'Entrance Exam Success', 'icon' => '📝'),
                array('id' => 'placement', 'name' => 'Placement Success', 'icon' => '🎯'),
                array('id' => 'overall-experience', 'name' => 'Overall Experience', 'icon' => '⭐'),
            )
        );
    }

    /**
     * Get featured testimonials
     */
    public function get_featured_testimonials($limit = 6) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        $testimonials = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'approved' AND is_featured = 1 ORDER BY created_at DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $testimonials
        );
    }

    /**
     * Get success statistics
     */
    public function get_success_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        $stats = array(
            'total_students_helped' => $wpdb->get_var("SELECT COUNT(DISTINCT user_id) FROM {$table} WHERE status = 'approved'"),
            'total_testimonials' => $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status = 'approved'"),
            'average_rating' => $wpdb->get_var("SELECT AVG(rating) FROM {$table} WHERE status = 'approved'"),
            'colleges_covered' => $wpdb->get_var("SELECT COUNT(DISTINCT college) FROM {$table} WHERE status = 'approved'"),
        );

        // Get category breakdown
        $category_stats = $wpdb->get_results(
            "SELECT category, COUNT(*) as count FROM {$table} WHERE status = 'approved' GROUP BY category"
        );

        $stats['by_category'] = $category_stats;

        return array(
            'success' => true,
            'data' => $stats
        );
    }

    /**
     * Like testimonial
     */
    public function like_testimonial($user_id, $testimonial_id) {
        global $wpdb;
        $likes_table = $wpdb->prefix . 'ss_testimonial_likes';
        $testimonials_table = $wpdb->prefix . 'ss_testimonials';

        // Check if already liked
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$likes_table} WHERE user_id = %d AND testimonial_id = %d",
            $user_id,
            $testimonial_id
        ));

        if ($existing) {
            // Unlike
            $wpdb->delete($likes_table, array('id' => $existing));
            $wpdb->query($wpdb->prepare(
                "UPDATE {$testimonials_table} SET likes = likes - 1 WHERE id = %d",
                $testimonial_id
            ));
            return array('success' => true, 'action' => 'unliked');
        } else {
            // Like
            $wpdb->insert($likes_table, array(
                'user_id' => $user_id,
                'testimonial_id' => $testimonial_id,
                'created_at' => current_time('mysql')
            ));
            $wpdb->query($wpdb->prepare(
                "UPDATE {$testimonials_table} SET likes = likes + 1 WHERE id = %d",
                $testimonial_id
            ));
            return array('success' => true, 'action' => 'liked');
        }
    }

    /**
     * Get video testimonials
     */
    public function get_video_testimonials($limit = 6) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        $testimonials = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'approved' AND video_url != '' AND video_url IS NOT NULL ORDER BY created_at DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $testimonials
        );
    }

    /**
     * Share testimonial
     */
    public function share_testimonial($testimonial_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_testimonials';

        // Increment shares count
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET shares = shares + 1 WHERE id = %d",
            $testimonial_id
        ));

        return array('success' => true);
    }
}
