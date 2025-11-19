<?php
/**
 * Student Dashboard
 * Personalized student portal with all services in one place
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Student_Dashboard extends Student_Services_Base_Service {

    /**
     * Get dashboard overview
     */
    public function get_dashboard_overview($user_id) {
        global $wpdb;

        $data = array(
            'profile' => $this->get_profile_completion($user_id),
            'applications' => $this->get_application_stats($user_id),
            'activities' => $this->get_recent_activities($user_id, 5),
            'upcoming_events' => $this->get_upcoming_events($user_id, 5),
            'recommendations' => $this->get_recommendations($user_id, 5),
            'notifications' => $this->get_notifications($user_id, 5),
            'reward_points' => $this->get_reward_points($user_id),
        );

        return array(
            'success' => true,
            'data' => $data
        );
    }

    /**
     * Get profile completion
     */
    private function get_profile_completion($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_student_profiles';

        $profile = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        if (!$profile) {
            return array(
                'percentage' => 0,
                'missing_fields' => array('basic_info', 'education', 'preferences', 'documents')
            );
        }

        $total_fields = 20;
        $completed = 0;
        $missing = array();

        // Check basic info
        if (!empty($profile->full_name) && !empty($profile->phone) && !empty($profile->date_of_birth)) {
            $completed += 5;
        } else {
            $missing[] = 'basic_info';
        }

        // Check education
        if (!empty($profile->current_education) && !empty($profile->graduation_year)) {
            $completed += 5;
        } else {
            $missing[] = 'education';
        }

        // Check preferences
        if (!empty($profile->interests) && !empty($profile->career_goals)) {
            $completed += 5;
        } else {
            $missing[] = 'preferences';
        }

        // Check documents
        if (!empty($profile->resume_url) && !empty($profile->profile_photo)) {
            $completed += 5;
        } else {
            $missing[] = 'documents';
        }

        return array(
            'percentage' => ($completed / $total_fields) * 100,
            'missing_fields' => $missing
        );
    }

    /**
     * Get application statistics
     */
    private function get_application_stats($user_id) {
        global $wpdb;

        return array(
            'jobs' => array(
                'total' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_job_applications WHERE user_id = %d",
                    $user_id
                )),
                'pending' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_job_applications WHERE user_id = %d AND status = 'pending'",
                    $user_id
                )),
                'accepted' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_job_applications WHERE user_id = %d AND status = 'accepted'",
                    $user_id
                ))
            ),
            'scholarships' => array(
                'total' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_scholarship_applications WHERE user_id = %d",
                    $user_id
                )),
                'pending' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_scholarship_applications WHERE user_id = %d AND status = 'pending'",
                    $user_id
                ))
            ),
            'universities' => array(
                'total' => $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_study_abroad_applications WHERE user_id = %d",
                    $user_id
                ))
            )
        );
    }

    /**
     * Get recent activities
     */
    private function get_recent_activities($user_id, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_user_activities';

        $activities = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
            $user_id,
            $limit
        ));

        return $activities;
    }

    /**
     * Get upcoming events
     */
    private function get_upcoming_events($user_id, $limit = 5) {
        global $wpdb;
        $registrations_table = $wpdb->prefix . 'ss_webinar_registrations';
        $webinars_table = $wpdb->prefix . 'ss_webinars';

        $events = $wpdb->get_results($wpdb->prepare(
            "SELECT w.* FROM {$webinars_table} w
            INNER JOIN {$registrations_table} r ON w.id = r.webinar_id
            WHERE r.user_id = %d
            AND w.scheduled_date >= NOW()
            ORDER BY w.scheduled_date ASC
            LIMIT %d",
            $user_id,
            $limit
        ));

        return $events;
    }

    /**
     * Get recommendations
     */
    private function get_recommendations($user_id, $limit = 5) {
        global $wpdb;

        // Get based on user's profile and activities
        $recommendations = array(
            'jobs' => $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}ss_jobs
                WHERE status = 'active'
                AND job_type IN ('part-time', 'internship')
                ORDER BY created_at DESC
                LIMIT %d",
                $limit
            )),
            'scholarships' => $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}ss_scholarships_master
                WHERE deadline >= CURDATE()
                ORDER BY amount DESC
                LIMIT %d",
                $limit
            ))
        );

        return $recommendations;
    }

    /**
     * Get notifications
     */
    private function get_notifications($user_id, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_notifications';

        $notifications = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
            $user_id,
            $limit
        ));

        return $notifications;
    }

    /**
     * Get reward points
     */
    private function get_reward_points($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_reward_points';

        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(points), 0) FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        return array(
            'total' => intval($total),
            'value' => round(intval($total) * 0.10, 2)
        );
    }

    /**
     * Update student profile
     */
    public function update_profile($user_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_student_profiles';

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        $profile_data = array(
            'user_id' => $user_id,
            'full_name' => sanitize_text_field($data['full_name']),
            'phone' => sanitize_text_field($data['phone']),
            'date_of_birth' => sanitize_text_field($data['date_of_birth']),
            'gender' => sanitize_text_field($data['gender']),
            'current_education' => sanitize_text_field($data['current_education']),
            'graduation_year' => intval($data['graduation_year']),
            'interests' => sanitize_textarea_field($data['interests']),
            'career_goals' => sanitize_textarea_field($data['career_goals']),
            'skills' => sanitize_text_field($data['skills']),
            'resume_url' => isset($data['resume_url']) ? esc_url_raw($data['resume_url']) : '',
            'profile_photo' => isset($data['profile_photo']) ? esc_url_raw($data['profile_photo']) : '',
            'bio' => isset($data['bio']) ? sanitize_textarea_field($data['bio']) : '',
            'updated_at' => current_time('mysql')
        );

        if ($existing) {
            $result = $wpdb->update($table, $profile_data, array('id' => $existing));
        } else {
            $profile_data['created_at'] = current_time('mysql');
            $result = $wpdb->insert($table, $profile_data);
        }

        if ($result !== false) {
            return array('success' => true, 'message' => 'Profile updated successfully');
        }

        return array('success' => false, 'message' => 'Failed to update profile');
    }

    /**
     * Get student profile
     */
    public function get_profile($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_student_profiles';

        $profile = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $profile
        );
    }

    /**
     * Log user activity
     */
    public function log_activity($user_id, $type, $description, $data = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_user_activities';

        $wpdb->insert($table, array(
            'user_id' => $user_id,
            'activity_type' => sanitize_text_field($type),
            'description' => sanitize_text_field($description),
            'meta_data' => json_encode($data),
            'created_at' => current_time('mysql')
        ));

        return true;
    }
}
