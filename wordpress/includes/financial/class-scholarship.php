<?php
/**
 * Scholarship Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Scholarship extends Student_Services_Base_Service {

    /**
     * Search scholarships
     */
    public function search_scholarships($args = array()) {
        $scholarships = $this->get_results('scholarships', array('status' => 'active'));
        return $this->success($scholarships);
    }

    /**
     * Apply for scholarship
     */
    public function apply($user_id, $scholarship_id, $data = array()) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        // Store application as post meta
        $application_id = wp_insert_post(array(
            'post_type' => 'ss_scholarship_app',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf(__('Scholarship Application %d', 'student-services'), $scholarship_id),
        ));

        update_post_meta($application_id, '_scholarship_id', $scholarship_id);
        update_post_meta($application_id, '_status', 'submitted');
        update_post_meta($application_id, '_submitted_date', current_time('mysql'));

        if (isset($data['essay'])) {
            update_post_meta($application_id, '_essay', wp_kses_post($data['essay']));
        }

        return $this->success(array(
            'application_id' => $application_id
        ), __('Application submitted successfully', 'student-services'));
    }
}
