<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Housing extends Student_Services_Base_Service {
    public function submit_application($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $app_id = $this->insert('housing', array(
            'user_id' => $user_id,
            'building_type' => $data['building_type'],
            'status' => 'pending',
            'preferences' => wp_json_encode($data),
            'applied_at' => current_time('mysql')
        ));
        
        return $this->success(array('application_id' => $app_id));
    }
    
    public function get_application($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        $app = $this->get_row('housing', array('user_id' => $user_id));
        return $this->success($app);
    }
    
    public function submit_maintenance($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $request_id = $this->insert('maintenance', array(
            'user_id' => $user_id,
            'building' => $data['building'],
            'room' => $data['room'],
            'issue' => $data['issue'],
            'priority' => isset($data['priority']) ? $data['priority'] : 'medium',
            'status' => 'submitted'
        ));
        
        return $this->success(array('request_id' => $request_id));
    }
}
