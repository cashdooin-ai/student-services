<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Parking extends Student_Services_Base_Service {
    public function purchase_permit($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $permit_id = wp_insert_post(array(
            'post_type' => 'ss_parking_permit',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf('Parking Permit - %s', $data['type']),
        ));
        
        update_post_meta($permit_id, '_vehicle_info', wp_json_encode($data['vehicle']));
        update_post_meta($permit_id, '_permit_type', $data['type']);
        
        return $this->success(array('permit_id' => $permit_id));
    }
}
