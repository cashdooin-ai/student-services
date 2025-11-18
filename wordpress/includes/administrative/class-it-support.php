<?php
if (!defined('ABSPATH')) exit;
class Student_Services_IT_Support extends Student_Services_Base_Service {
    public function submit_ticket($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $ticket_id = $this->insert('it_tickets', array(
            'user_id' => $user_id,
            'category' => $data['category'],
            'subject' => $data['subject'],
            'description' => $data['description'],
            'priority' => isset($data['priority']) ? $data['priority'] : 'medium',
            'status' => 'open'
        ));
        
        return $this->success(array('ticket_id' => $ticket_id));
    }
    
    public function get_tickets($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        $tickets = $this->get_results('it_tickets', array('user_id' => $user_id));
        return $this->success($tickets);
    }
}
