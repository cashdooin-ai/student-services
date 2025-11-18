<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Counseling extends Student_Services_Base_Service {
    public function schedule_appointment($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        
        $appt_id = $this->insert('health_appointments', array(
            'user_id' => $user_id,
            'appointment_type' => 'counseling',
            'provider' => 'Counselor',
            'appointment_date' => $data['date'],
            'reason' => isset($data['reason']) ? $data['reason'] : 'Counseling session',
            'status' => 'scheduled'
        ));
        
        return $this->success(array('appointment_id' => $appt_id));
    }
    
    public function get_crisis_resources() {
        $resources = array(
            array('name' => 'Campus Crisis Hotline', 'phone' => '555-HELP', 'availability' => '24/7'),
            array('name' => 'National Suicide Prevention Lifeline', 'phone' => '988', 'availability' => '24/7')
        );
        return $this->success($resources);
    }
}
