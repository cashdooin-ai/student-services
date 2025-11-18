<?php
if (!defined('ABSPATH')) exit;
class Student_Services_Dining extends Student_Services_Base_Service {
    public function get_meal_plans() {
        $plans = array(
            array('id' => 1, 'name' => 'Unlimited Plan', 'meals_per_week' => -1, 'price' => 2500),
            array('id' => 2, 'name' => '14 Meals Per Week', 'meals_per_week' => 14, 'price' => 2200),
            array('id' => 3, 'name' => '10 Meals Per Week', 'meals_per_week' => 10, 'price' => 1900)
        );
        return $this->success($plans);
    }
    
    public function get_student_plan($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) return $permission;
        $plan = get_user_meta($user_id, '_meal_plan', true);
        return $this->success($plan);
    }
}
