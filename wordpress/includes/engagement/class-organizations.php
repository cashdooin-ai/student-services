<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Organizations extends Student_Services_Base_Service {
    public function search_organizations($args = array()) {
        $orgs = $this->get_results('organizations', array('joinable' => 1));
        return $this->success($orgs);
    }
    
    public function join_organization($user_id, $org_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $membership_id = $this->insert('memberships', array(
            'user_id' => $user_id,
            'org_id' => $org_id,
            'position' => 'Member',
            'joined_at' => current_time('mysql')
        ));
        
        return $this->success(array('membership_id' => $membership_id));
    }
}
