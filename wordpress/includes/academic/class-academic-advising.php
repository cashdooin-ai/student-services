<?php
/**
 * Academic Advising Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Academic_Advising extends Student_Services_Base_Service {

    /**
     * Schedule advising appointment
     */
    public function schedule_appointment($user_id, $data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $validation = $this->validate_required($data, array('date', 'type'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Store in WordPress post meta or custom table
        $appointment_id = wp_insert_post(array(
            'post_type' => 'ss_appointment',
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_title' => sprintf(__('Advising Appointment - %s', 'student-services'), $data['date']),
        ));

        if (is_wp_error($appointment_id)) {
            return $appointment_id;
        }

        update_post_meta($appointment_id, '_appointment_date', $this->sanitize($data['date']));
        update_post_meta($appointment_id, '_appointment_type', $this->sanitize($data['type']));
        update_post_meta($appointment_id, '_advisor_id', isset($data['advisor_id']) ? intval($data['advisor_id']) : 0);
        update_post_meta($appointment_id, '_status', 'scheduled');

        return $this->success(array(
            'appointment_id' => $appointment_id
        ), __('Appointment scheduled successfully', 'student-services'));
    }

    /**
     * Get student appointments
     */
    public function get_appointments($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $args = array(
            'post_type' => 'ss_appointment',
            'author' => $user_id,
            'posts_per_page' => -1,
            'orderby' => 'meta_value',
            'meta_key' => '_appointment_date',
            'order' => 'ASC'
        );

        $appointments = get_posts($args);

        $result = array();
        foreach ($appointments as $appointment) {
            $result[] = array(
                'id' => $appointment->ID,
                'date' => get_post_meta($appointment->ID, '_appointment_date', true),
                'type' => get_post_meta($appointment->ID, '_appointment_type', true),
                'status' => get_post_meta($appointment->ID, '_status', true),
            );
        }

        return $this->success($result);
    }
}
