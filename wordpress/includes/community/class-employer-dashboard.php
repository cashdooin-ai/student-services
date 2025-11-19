<?php
/**
 * Employer Dashboard
 * For companies posting jobs and managing applications
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Employer_Dashboard extends Student_Services_Base_Service {

    /**
     * Register employer
     */
    public function register_employer($user_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_employers';

        // Check if already registered
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        if ($existing) {
            return array('success' => false, 'message' => 'Employer profile already exists');
        }

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'company_name' => sanitize_text_field($data['company_name']),
            'company_website' => esc_url_raw($data['company_website']),
            'company_email' => sanitize_email($data['company_email']),
            'company_phone' => sanitize_text_field($data['company_phone']),
            'company_description' => sanitize_textarea_field($data['company_description']),
            'industry' => sanitize_text_field($data['industry']),
            'company_size' => sanitize_text_field($data['company_size']),
            'headquarters' => sanitize_text_field($data['headquarters']),
            'founded_year' => isset($data['founded_year']) ? intval($data['founded_year']) : null,
            'company_logo' => isset($data['company_logo']) ? esc_url_raw($data['company_logo']) : '',
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'employer_id' => $wpdb->insert_id, 'message' => 'Employer profile created. Awaiting verification.');
        }

        return array('success' => false, 'message' => 'Failed to create employer profile');
    }

    /**
     * Get employer dashboard overview
     */
    public function get_employer_overview($employer_id) {
        global $wpdb;

        $stats = array(
            'active_jobs' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}ss_jobs WHERE employer_id = %d AND status = 'active' AND expiry_date >= CURDATE()",
                $employer_id
            )),
            'total_applications' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}ss_job_applications WHERE employer_id = %d",
                $employer_id
            )),
            'pending_applications' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}ss_job_applications WHERE employer_id = %d AND status = 'pending'",
                $employer_id
            )),
            'total_views' => $wpdb->get_var($wpdb->prepare(
                "SELECT COALESCE(SUM(views), 0) FROM {$wpdb->prefix}ss_jobs WHERE employer_id = %d",
                $employer_id
            ))
        );

        return array(
            'success' => true,
            'data' => $stats
        );
    }

    /**
     * Post a job
     */
    public function post_job($employer_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        // Get employer details
        $employer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ss_employers WHERE id = %d AND status = 'active'",
            $employer_id
        ));

        if (!$employer) {
            return array('success' => false, 'message' => 'Employer not verified');
        }

        $expiry_days = isset($data['duration']) ? intval($data['duration']) : 30;

        $result = $wpdb->insert($table, array(
            'employer_id' => $employer_id,
            'company_name' => $employer->company_name,
            'company_logo' => $employer->company_logo,
            'title' => sanitize_text_field($data['title']),
            'description' => wp_kses_post($data['description']),
            'responsibilities' => wp_kses_post($data['responsibilities']),
            'requirements' => wp_kses_post($data['requirements']),
            'job_type' => sanitize_text_field($data['job_type']),
            'category' => sanitize_text_field($data['category']),
            'location' => sanitize_text_field($data['location']),
            'remote' => isset($data['remote']) ? 1 : 0,
            'salary_min' => isset($data['salary_min']) ? intval($data['salary_min']) : null,
            'salary_max' => isset($data['salary_max']) ? intval($data['salary_max']) : null,
            'salary_period' => isset($data['salary_period']) ? sanitize_text_field($data['salary_period']) : 'monthly',
            'experience_required' => isset($data['experience_required']) ? sanitize_text_field($data['experience_required']) : 'Fresher',
            'skills_required' => sanitize_text_field($data['skills_required']),
            'education_required' => isset($data['education_required']) ? sanitize_text_field($data['education_required']) : '',
            'benefits' => isset($data['benefits']) ? sanitize_textarea_field($data['benefits']) : '',
            'application_deadline' => isset($data['application_deadline']) ? sanitize_text_field($data['application_deadline']) : date('Y-m-d', strtotime('+30 days')),
            'expiry_date' => date('Y-m-d', strtotime('+' . $expiry_days . ' days')),
            'contact_email' => sanitize_email($data['contact_email']),
            'contact_phone' => isset($data['contact_phone']) ? sanitize_text_field($data['contact_phone']) : '',
            'is_featured' => 0,
            'status' => 'active',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            return array('success' => true, 'job_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to post job');
    }

    /**
     * Get employer's posted jobs
     */
    public function get_my_jobs($employer_id, $status = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        $where = $wpdb->prepare('employer_id = %d', $employer_id);
        if ($status) {
            $where .= $wpdb->prepare(' AND status = %s', $status);
        }

        $jobs = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE {$where} ORDER BY created_at DESC"
        );

        return array(
            'success' => true,
            'data' => $jobs
        );
    }

    /**
     * Get applications for a job
     */
    public function get_job_applications($employer_id, $job_id, $status = null) {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'ss_job_applications';
        $jobs_table = $wpdb->prefix . 'ss_jobs';

        // Verify job belongs to employer
        $job = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$jobs_table} WHERE id = %d AND employer_id = %d",
            $job_id,
            $employer_id
        ));

        if (!$job) {
            return array('success' => false, 'message' => 'Unauthorized');
        }

        $where = $wpdb->prepare('a.job_id = %d', $job_id);
        if ($status) {
            $where .= $wpdb->prepare(' AND a.status = %s', $status);
        }

        $applications = $wpdb->get_results(
            "SELECT a.*, u.display_name as applicant_name, u.user_email
            FROM {$applications_table} a
            LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
            WHERE {$where}
            ORDER BY a.created_at DESC"
        );

        return array(
            'success' => true,
            'data' => $applications
        );
    }

    /**
     * Update application status
     */
    public function update_application_status($employer_id, $application_id, $new_status, $notes = '') {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'ss_job_applications';

        // Verify application belongs to employer's job
        $application = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$applications_table} WHERE id = %d AND employer_id = %d",
            $application_id,
            $employer_id
        ));

        if (!$application) {
            return array('success' => false, 'message' => 'Unauthorized');
        }

        $result = $wpdb->update(
            $applications_table,
            array(
                'status' => $new_status,
                'employer_notes' => sanitize_textarea_field($notes),
                'reviewed_at' => current_time('mysql')
            ),
            array('id' => $application_id)
        );

        if ($result !== false) {
            return array('success' => true, 'message' => 'Application status updated');
        }

        return array('success' => false, 'message' => 'Failed to update status');
    }

    /**
     * Edit/Update job
     */
    public function update_job($employer_id, $job_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        // Verify job belongs to employer
        $job = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE id = %d AND employer_id = %d",
            $job_id,
            $employer_id
        ));

        if (!$job) {
            return array('success' => false, 'message' => 'Unauthorized');
        }

        $update_data = array(
            'title' => sanitize_text_field($data['title']),
            'description' => wp_kses_post($data['description']),
            'responsibilities' => wp_kses_post($data['responsibilities']),
            'requirements' => wp_kses_post($data['requirements']),
            'job_type' => sanitize_text_field($data['job_type']),
            'category' => sanitize_text_field($data['category']),
            'location' => sanitize_text_field($data['location']),
            'remote' => isset($data['remote']) ? 1 : 0,
            'salary_min' => isset($data['salary_min']) ? intval($data['salary_min']) : null,
            'salary_max' => isset($data['salary_max']) ? intval($data['salary_max']) : null,
            'skills_required' => sanitize_text_field($data['skills_required']),
            'updated_at' => current_time('mysql')
        );

        $result = $wpdb->update($table, $update_data, array('id' => $job_id));

        if ($result !== false) {
            return array('success' => true, 'message' => 'Job updated successfully');
        }

        return array('success' => false, 'message' => 'Failed to update job');
    }

    /**
     * Delete/Close job
     */
    public function close_job($employer_id, $job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        $result = $wpdb->update(
            $table,
            array('status' => 'closed'),
            array('id' => $job_id, 'employer_id' => $employer_id)
        );

        if ($result) {
            return array('success' => true, 'message' => 'Job closed successfully');
        }

        return array('success' => false, 'message' => 'Failed to close job');
    }

    /**
     * Get employer profile
     */
    public function get_employer_profile($employer_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_employers';

        $profile = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $employer_id
        ));

        return array(
            'success' => true,
            'data' => $profile
        );
    }

    /**
     * Update employer profile
     */
    public function update_employer_profile($employer_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_employers';

        $result = $wpdb->update($table, array(
            'company_name' => sanitize_text_field($data['company_name']),
            'company_website' => esc_url_raw($data['company_website']),
            'company_description' => sanitize_textarea_field($data['company_description']),
            'industry' => sanitize_text_field($data['industry']),
            'company_size' => sanitize_text_field($data['company_size']),
            'headquarters' => sanitize_text_field($data['headquarters']),
            'company_logo' => isset($data['company_logo']) ? esc_url_raw($data['company_logo']) : '',
            'updated_at' => current_time('mysql')
        ), array('id' => $employer_id));

        if ($result !== false) {
            return array('success' => true, 'message' => 'Profile updated successfully');
        }

        return array('success' => false, 'message' => 'Failed to update profile');
    }
}
