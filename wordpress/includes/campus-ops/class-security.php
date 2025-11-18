<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Security extends Student_Services_Base_Service {
    public function report_incident($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $incident_id = wp_insert_post(array(
            'post_type' => 'ss_security_incident',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf('Incident - %s', $data['type']),
            'post_content' => $data['description']
        ));
        
        update_post_meta($incident_id, '_location', $data['location']);
        update_post_meta($incident_id, '_incident_type', $data['type']);
        update_post_meta($incident_id, '_status', 'reported');
        
        return $this->success(array('incident_id' => $incident_id));
    }
    
    public function request_escort($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $escort_id = wp_insert_post(array(
            'post_type' => 'ss_safety_escort',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf('Safety Escort Request'),
        ));
        
        update_post_meta($escort_id, '_pickup', $data['pickup']);
        update_post_meta($escort_id, '_dropoff', $data['dropoff']);
        update_post_meta($escort_id, '_status', 'requested');
        
        return $this->success(array('escort_id' => $escort_id));
    }
}
