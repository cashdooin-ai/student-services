<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Health extends Student_Services_Base_Service {
    public function schedule_appointment($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $appt_id = $this->insert('health_appointments', array(
            'user_id' => $user_id,
            'appointment_type' => $data['type'],
            'provider' => isset($data['provider']) ? $data['provider'] : 'Dr. Smith',
            'appointment_date' => $data['date'],
            'appointment_time' => isset($data['time']) ? $data['time'] : '14:00',
            'reason' => $data['reason'],
            'status' => 'scheduled'
        ));
        
        return $this->success(array('appointment_id' => $appt_id));
    }
    
    public function get_appointments($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        $appointments = $this->get_results('health_appointments', array('user_id' => $user_id));
        return $this->success($appointments);
    }
}
