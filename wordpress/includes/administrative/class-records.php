<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Records extends Student_Services_Base_Service {
    public function request_transcript($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $request_id = wp_insert_post(array(
            'post_type' => 'ss_transcript_request',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf('Transcript Request - %s', $data['type']),
        ));
        
        update_post_meta($request_id, '_transcript_type', $data['type']);
        update_post_meta($request_id, '_delivery_method', $data['delivery_method']);
        update_post_meta($request_id, '_recipient', $data['recipient']);
        update_post_meta($request_id, '_status', 'processing');
        
        return $this->success(array('request_id' => $request_id));
    }
}
