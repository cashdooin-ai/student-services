<?php
/**
 * Library Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Library extends Student_Services_Base_Service {

    /**
     * Search library catalog
     */
    public function search_catalog($args = array()) {
        // This would integrate with your library system
        // For now, returning a simple post type query

        $defaults = array(
            'keyword' => '',
            'title' => '',
            'author' => '',
            'type' => ''
        );

        $args = wp_parse_args($args, $defaults);

        $query_args = array(
            'post_type' => 'ss_library_item',
            'posts_per_page' => 20,
            's' => $args['keyword']
        );

        if (!empty($args['type'])) {
            $query_args['meta_query'][] = array(
                'key' => '_resource_type',
                'value' => $args['type']
            );
        }

        $query = new WP_Query($query_args);
        $resources = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $resources[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'author' => get_post_meta(get_the_ID(), '_author', true),
                    'type' => get_post_meta(get_the_ID(), '_resource_type', true),
                    'available' => get_post_meta(get_the_ID(), '_available', true) == '1'
                );
            }
            wp_reset_postdata();
        }

        return $this->success($resources);
    }

    /**
     * Checkout resource
     */
    public function checkout($user_id, $resource_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        // Check if available
        $available = get_post_meta($resource_id, '_available', true);
        if ($available != '1') {
            return $this->error(__('Resource not available', 'student-services'));
        }

        // Set as checked out
        update_post_meta($resource_id, '_available', '0');
        update_post_meta($resource_id, '_checked_out_by', $user_id);
        update_post_meta($resource_id, '_checkout_date', current_time('mysql'));

        $due_date = date('Y-m-d', strtotime('+21 days'));
        update_post_meta($resource_id, '_due_date', $due_date);

        return $this->success(array(
            'due_date' => $due_date
        ), __('Resource checked out successfully', 'student-services'));
    }
}
