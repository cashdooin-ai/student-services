<?php
/**
 * Job Board
 * Part-time jobs, internships, and freelance opportunities for students
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Job_Board extends Student_Services_Base_Service {

    /**
     * Search jobs
     */
    public function search_jobs($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        $where = array('status = "active"', 'expiry_date >= CURDATE()');

        if (!empty($filters['type'])) {
            $where[] = $wpdb->prepare('job_type = %s', $filters['type']);
        }

        if (!empty($filters['category'])) {
            $where[] = $wpdb->prepare('category = %s', $filters['category']);
        }

        if (!empty($filters['location'])) {
            $where[] = $wpdb->prepare('(location LIKE %s OR remote = 1)', '%' . $wpdb->esc_like($filters['location']) . '%');
        }

        if (isset($filters['remote']) && $filters['remote']) {
            $where[] = 'remote = 1';
        }

        if (!empty($filters['skills'])) {
            $where[] = $wpdb->prepare('skills_required LIKE %s', '%' . $wpdb->esc_like($filters['skills']) . '%');
        }

        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where[] = $wpdb->prepare('(title LIKE %s OR description LIKE %s OR company_name LIKE %s)', $search, $search, $search);
        }

        if (isset($filters['salary_min']) && $filters['salary_min'] > 0) {
            $where[] = $wpdb->prepare('salary_min >= %d', $filters['salary_min']);
        }

        if (isset($filters['featured']) && $filters['featured']) {
            $where[] = 'is_featured = 1';
        }

        $where_sql = implode(' AND ', $where);
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 20;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;

        // Featured jobs first
        $order_by = isset($filters['featured']) && $filters['featured']
            ? "ORDER BY is_featured DESC, created_at DESC"
            : "ORDER BY created_at DESC";

        $jobs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_sql} {$order_by} LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $jobs
        );
    }

    /**
     * Get job details
     */
    public function get_job($job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d AND status = 'active'",
            $job_id
        ));

        if (!$job) {
            return array('success' => false, 'message' => 'Job not found');
        }

        // Increment views
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET views = views + 1 WHERE id = %d",
            $job_id
        ));

        return array(
            'success' => true,
            'data' => $job
        );
    }

    /**
     * Apply for job
     */
    public function apply_for_job($user_id, $job_id, $data) {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'ss_job_applications';
        $jobs_table = $wpdb->prefix . 'ss_jobs';

        // Check if already applied
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$applications_table} WHERE user_id = %d AND job_id = %d",
            $user_id,
            $job_id
        ));

        if ($existing) {
            return array('success' => false, 'message' => 'You have already applied for this job');
        }

        // Check if job exists and is active
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$jobs_table} WHERE id = %d AND status = 'active' AND expiry_date >= CURDATE()",
            $job_id
        ));

        if (!$job) {
            return array('success' => false, 'message' => 'Job not available');
        }

        $result = $wpdb->insert($applications_table, array(
            'user_id' => $user_id,
            'job_id' => $job_id,
            'employer_id' => $job->employer_id,
            'full_name' => sanitize_text_field($data['full_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'cover_letter' => sanitize_textarea_field($data['cover_letter']),
            'resume_url' => esc_url_raw($data['resume_url']),
            'portfolio_url' => isset($data['portfolio_url']) ? esc_url_raw($data['portfolio_url']) : '',
            'expected_salary' => isset($data['expected_salary']) ? intval($data['expected_salary']) : null,
            'available_from' => isset($data['available_from']) ? sanitize_text_field($data['available_from']) : '',
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            // Increment applications count
            $wpdb->query($wpdb->prepare(
                "UPDATE {$jobs_table} SET applications_count = applications_count + 1 WHERE id = %d",
                $job_id
            ));

            return array('success' => true, 'application_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to submit application');
    }

    /**
     * Get user's applications
     */
    public function get_my_applications($user_id, $status = null) {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'ss_job_applications';
        $jobs_table = $wpdb->prefix . 'ss_jobs';

        $where = $wpdb->prepare('a.user_id = %d', $user_id);
        if ($status) {
            $where .= $wpdb->prepare(' AND a.status = %s', $status);
        }

        $applications = $wpdb->get_results(
            "SELECT a.*, j.title as job_title, j.company_name, j.location, j.job_type, j.company_logo
            FROM {$applications_table} a
            INNER JOIN {$jobs_table} j ON a.job_id = j.id
            WHERE {$where}
            ORDER BY a.created_at DESC"
        );

        return array(
            'success' => true,
            'data' => $applications
        );
    }

    /**
     * Save job to favorites
     */
    public function save_job($user_id, $job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_job_favorites';

        // Check if already saved
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND job_id = %d",
            $user_id,
            $job_id
        ));

        if ($existing) {
            $wpdb->delete($table, array('id' => $existing));
            return array('success' => true, 'action' => 'removed');
        } else {
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'job_id' => $job_id,
                'created_at' => current_time('mysql')
            ));
            return array('success' => true, 'action' => 'added');
        }
    }

    /**
     * Get saved jobs
     */
    public function get_saved_jobs($user_id) {
        global $wpdb;
        $favorites_table = $wpdb->prefix . 'ss_job_favorites';
        $jobs_table = $wpdb->prefix . 'ss_jobs';

        $jobs = $wpdb->get_results($wpdb->prepare(
            "SELECT j.* FROM {$jobs_table} j
            INNER JOIN {$favorites_table} f ON j.id = f.job_id
            WHERE f.user_id = %d AND j.status = 'active'
            ORDER BY f.created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $jobs
        );
    }

    /**
     * Get job categories
     */
    public function get_categories() {
        return array(
            'success' => true,
            'data' => array(
                array('id' => 'technology', 'name' => 'Technology & IT', 'icon' => '💻', 'count' => 150),
                array('id' => 'marketing', 'name' => 'Marketing & Sales', 'icon' => '📈', 'count' => 80),
                array('id' => 'design', 'name' => 'Design & Creative', 'icon' => '🎨', 'count' => 60),
                array('id' => 'content', 'name' => 'Content Writing', 'icon' => '✍️', 'count' => 45),
                array('id' => 'teaching', 'name' => 'Teaching & Tutoring', 'icon' => '👨‍🏫', 'count' => 70),
                array('id' => 'finance', 'name' => 'Finance & Accounting', 'icon' => '💰', 'count' => 40),
                array('id' => 'customer-service', 'name' => 'Customer Service', 'icon' => '📞', 'count' => 55),
                array('id' => 'data-entry', 'name' => 'Data Entry', 'icon' => '⌨️', 'count' => 35),
                array('id' => 'research', 'name' => 'Research', 'icon' => '🔬', 'count' => 30),
                array('id' => 'others', 'name' => 'Others', 'icon' => '📋', 'count' => 25),
            )
        );
    }

    /**
     * Get job types
     */
    public function get_job_types() {
        return array(
            'success' => true,
            'data' => array(
                array('id' => 'part-time', 'name' => 'Part-Time', 'icon' => '⏰'),
                array('id' => 'internship', 'name' => 'Internship', 'icon' => '🎓'),
                array('id' => 'freelance', 'name' => 'Freelance', 'icon' => '💼'),
                array('id' => 'full-time', 'name' => 'Full-Time', 'icon' => '🏢'),
                array('id' => 'contract', 'name' => 'Contract', 'icon' => '📝'),
            )
        );
    }

    /**
     * Get featured jobs
     */
    public function get_featured_jobs($limit = 6) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        $jobs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'active' AND is_featured = 1 AND expiry_date >= CURDATE() ORDER BY created_at DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $jobs
        );
    }

    /**
     * Get recommended jobs for user
     */
    public function get_recommended_jobs($user_id, $limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        // Get user's profile data to recommend based on skills, interests
        // For now, return recent jobs matching common student criteria
        $jobs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table}
            WHERE status = 'active'
            AND expiry_date >= CURDATE()
            AND (job_type IN ('part-time', 'internship', 'freelance'))
            ORDER BY created_at DESC
            LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $jobs
        );
    }

    /**
     * Get job alerts
     */
    public function subscribe_to_alerts($user_id, $criteria) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_job_alerts';

        // Check if alert already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        if ($existing) {
            // Update existing alert
            $wpdb->update(
                $table,
                array(
                    'job_types' => sanitize_text_field($criteria['job_types']),
                    'categories' => sanitize_text_field($criteria['categories']),
                    'locations' => sanitize_text_field($criteria['locations']),
                    'keywords' => sanitize_text_field($criteria['keywords']),
                    'frequency' => sanitize_text_field($criteria['frequency']),
                    'updated_at' => current_time('mysql')
                ),
                array('id' => $existing)
            );
        } else {
            // Create new alert
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'job_types' => sanitize_text_field($criteria['job_types']),
                'categories' => sanitize_text_field($criteria['categories']),
                'locations' => sanitize_text_field($criteria['locations']),
                'keywords' => sanitize_text_field($criteria['keywords']),
                'frequency' => sanitize_text_field($criteria['frequency']),
                'is_active' => 1,
                'created_at' => current_time('mysql')
            ));
        }

        return array('success' => true, 'message' => 'Job alerts configured successfully');
    }

    /**
     * Get similar jobs
     */
    public function get_similar_jobs($job_id, $limit = 5) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_jobs';

        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $job_id
        ));

        if (!$job) {
            return array('success' => false, 'message' => 'Job not found');
        }

        // Get jobs with same category or type
        $similar = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table}
            WHERE id != %d
            AND status = 'active'
            AND expiry_date >= CURDATE()
            AND (category = %s OR job_type = %s)
            ORDER BY created_at DESC
            LIMIT %d",
            $job_id,
            $job->category,
            $job->job_type,
            $limit
        ));

        return array(
            'success' => true,
            'data' => $similar
        );
    }
}
