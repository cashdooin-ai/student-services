<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Events extends Student_Services_Base_Service {
    public function get_upcoming_events($args = array()) {
        $table = $this->get_table('events');
        $sql = "SELECT * FROM $table WHERE event_date >= CURDATE() ORDER BY event_date ASC";
        $events = $this->wpdb->get_results($sql);
        return $this->success($events);
    }
    
    public function register_for_event($user_id, $event_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $registration_id = $this->insert('event_registrations', array(
            'user_id' => $user_id,
            'event_id' => $event_id,
            'ticket_number' => 'TKT-' . time() . '-' . $user_id,
            'registered_at' => current_time('mysql')
        ));
        
        return $this->success(array('registration_id' => $registration_id));
    }
}
