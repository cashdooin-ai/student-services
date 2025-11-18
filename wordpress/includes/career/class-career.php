<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Career extends Student_Services_Base_Service {
    public function search_jobs($args = array()) {
        $jobs = $this->get_results('jobs', array('status' => 'active'));
        return $this->success($jobs);
    }
    
    public function apply_for_job($user_id, $job_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $app_id = wp_insert_post(array(
            'post_type' => 'ss_job_application',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf('Job Application %d', $job_id),
        ));
        
        update_post_meta($app_id, '_job_id', $job_id);
        update_post_meta($app_id, '_status', 'submitted');
        
        return $this->success(array('application_id' => $app_id));
    }
}
