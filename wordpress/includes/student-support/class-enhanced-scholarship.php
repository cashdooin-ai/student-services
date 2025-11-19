<?php
/**
 * Enhanced Scholarship Service
 * Find Your Perfect Scholarship - AI-powered scholarship search and application tracking
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Enhanced_Scholarship extends Student_Services_Base_Service {

    /**
     * Search scholarships
     */
    public function search_scholarships($filters = array()) {
        // Mock scholarship data
        $scholarships = array(
            array(
                'id' => 1,
                'name' => 'National Merit Scholarship',
                'provider' => 'Government of India',
                'amount' => 500000,
                'type' => 'Merit-based',
                'eligibility' => array(
                    'min_percentage' => 85,
                    'income_limit' => 800000,
                    'class' => '12th Pass'
                ),
                'deadline' => date('Y-m-d', strtotime('+30 days')),
                'description' => 'Full tuition scholarship for meritorious students',
                'benefits' => array('Full tuition', 'Books allowance', 'Hostel fees'),
                'application_link' => '#',
                'applicants_count' => 12450
            ),
            array(
                'id' => 2,
                'name' => 'Women in STEM Scholarship',
                'provider' => 'Tech Foundation',
                'amount' => 300000,
                'type' => 'Gender-specific',
                'eligibility' => array(
                    'min_percentage' => 75,
                    'gender' => 'Female',
                    'field' => 'STEM'
                ),
                'deadline' => date('Y-m-d', strtotime('+45 days')),
                'description' => 'Empowering women pursuing careers in Science, Technology, Engineering, and Mathematics',
                'benefits' => array('Tuition support', 'Mentorship program', 'Internship opportunities'),
                'application_link' => '#',
                'applicants_count' => 5670
            ),
            array(
                'id' => 3,
                'name' => 'SC/ST Scholarship Program',
                'provider' => 'Ministry of Social Justice',
                'amount' => 400000,
                'type' => 'Category-based',
                'eligibility' => array(
                    'category' => 'SC/ST',
                    'income_limit' => 250000
                ),
                'deadline' => date('Y-m-d', strtotime('+60 days')),
                'description' => 'Financial assistance for SC/ST students pursuing higher education',
                'benefits' => array('Full fees', 'Living allowance', 'Study materials'),
                'application_link' => '#',
                'applicants_count' => 23890
            ),
            array(
                'id' => 4,
                'name' => 'Sports Excellence Scholarship',
                'provider' => 'Sports Authority of India',
                'amount' => 250000,
                'type' => 'Sports-based',
                'eligibility' => array(
                    'sports_achievement' => 'State/National level',
                    'min_percentage' => 60
                ),
                'deadline' => date('Y-m-d', strtotime('+20 days')),
                'description' => 'Supporting student-athletes in balancing academics and sports',
                'benefits' => array('Tuition fees', 'Sports equipment', 'Training support'),
                'application_link' => '#',
                'applicants_count' => 3240
            ),
            array(
                'id' => 5,
                'name' => 'Need-Based Financial Aid',
                'provider' => 'Education Trust',
                'amount' => 200000,
                'type' => 'Need-based',
                'eligibility' => array(
                    'income_limit' => 300000,
                    'min_percentage' => 70
                ),
                'deadline' => date('Y-m-d', strtotime('+50 days')),
                'description' => 'Financial assistance for economically disadvantaged students',
                'benefits' => array('Partial tuition', 'Books', 'Transportation allowance'),
                'application_link' => '#',
                'applicants_count' => 18930
            )
        );

        // Apply filters
        if (!empty($filters['min_amount'])) {
            $scholarships = array_filter($scholarships, function($s) use ($filters) {
                return $s['amount'] >= $filters['min_amount'];
            });
        }

        if (!empty($filters['type'])) {
            $scholarships = array_filter($scholarships, function($s) use ($filters) {
                return $s['type'] === $filters['type'];
            });
        }

        return $this->success(array_values($scholarships));
    }

    /**
     * Get AI-powered scholarship recommendations
     */
    public function get_recommendations($user_id, $profile) {
        // Analyze user profile and recommend scholarships
        $all_scholarships = $this->search_scholarships()['data'];

        $recommended = array();
        foreach ($all_scholarships as $scholarship) {
            $match_score = $this->calculate_match_score($profile, $scholarship);

            if ($match_score >= 60) { // At least 60% match
                $scholarship['match_score'] = $match_score;
                $scholarship['match_reasons'] = $this->get_match_reasons($profile, $scholarship);
                $recommended[] = $scholarship;
            }
        }

        // Sort by match score
        usort($recommended, function($a, $b) {
            return $b['match_score'] - $a['match_score'];
        });

        return $this->success($recommended);
    }

    /**
     * Calculate match score between user profile and scholarship
     */
    private function calculate_match_score($profile, $scholarship) {
        $score = 0;
        $max_score = 0;

        // Check percentage eligibility
        if (isset($scholarship['eligibility']['min_percentage'])) {
            $max_score += 30;
            if (isset($profile['percentage']) && $profile['percentage'] >= $scholarship['eligibility']['min_percentage']) {
                $score += 30;
            }
        }

        // Check income eligibility
        if (isset($scholarship['eligibility']['income_limit'])) {
            $max_score += 25;
            if (isset($profile['family_income']) && $profile['family_income'] <= $scholarship['eligibility']['income_limit']) {
                $score += 25;
            }
        }

        // Check category
        if (isset($scholarship['eligibility']['category'])) {
            $max_score += 20;
            if (isset($profile['category']) && $profile['category'] === $scholarship['eligibility']['category']) {
                $score += 20;
            }
        }

        // Check gender
        if (isset($scholarship['eligibility']['gender'])) {
            $max_score += 15;
            if (isset($profile['gender']) && $profile['gender'] === $scholarship['eligibility']['gender']) {
                $score += 15;
            }
        }

        // Check field of study
        if (isset($scholarship['eligibility']['field'])) {
            $max_score += 10;
            if (isset($profile['field']) && stripos($profile['field'], $scholarship['eligibility']['field']) !== false) {
                $score += 10;
            }
        }

        return $max_score > 0 ? round(($score / $max_score) * 100) : 0;
    }

    /**
     * Get reasons why scholarship matches user
     */
    private function get_match_reasons($profile, $scholarship) {
        $reasons = array();

        if (isset($profile['percentage']) && isset($scholarship['eligibility']['min_percentage'])) {
            if ($profile['percentage'] >= $scholarship['eligibility']['min_percentage']) {
                $reasons[] = "Your academic performance meets the requirement";
            }
        }

        if (isset($profile['family_income']) && isset($scholarship['eligibility']['income_limit'])) {
            if ($profile['family_income'] <= $scholarship['eligibility']['income_limit']) {
                $reasons[] = "You qualify based on family income";
            }
        }

        if (isset($profile['category']) && isset($scholarship['eligibility']['category'])) {
            if ($profile['category'] === $scholarship['eligibility']['category']) {
                $reasons[] = "Category-specific scholarship for you";
            }
        }

        return $reasons;
    }

    /**
     * Apply for scholarship
     */
    public function apply_scholarship($user_id, $scholarship_id, $application_data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_scholarship_applications';

        // Check if already applied
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d AND scholarship_id = %d",
            $user_id,
            $scholarship_id
        ));

        if ($existing) {
            return $this->error('You have already applied for this scholarship');
        }

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'scholarship_id' => $scholarship_id,
                'application_data' => json_encode($application_data),
                'status' => 'submitted',
                'applied_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to submit application');
        }

        return $this->success(array(
            'application_id' => $wpdb->insert_id,
            'message' => 'Scholarship application submitted successfully!'
        ));
    }

    /**
     * Get user's scholarship applications
     */
    public function get_my_applications($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_scholarship_applications';

        $applications = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY applied_at DESC",
            $user_id
        ));

        return $this->success($applications);
    }

    /**
     * Update application status
     */
    public function update_application_status($application_id, $status, $notes = '') {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_scholarship_applications';

        $updated = $wpdb->update(
            $table_name,
            array(
                'status' => $status,
                'status_notes' => $notes,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $application_id),
            array('%s', '%s', '%s'),
            array('%d')
        );

        if ($updated === false) {
            return $this->error('Failed to update status');
        }

        return $this->success(array('message' => 'Application status updated'));
    }

    /**
     * Save scholarship for later
     */
    public function save_scholarship($user_id, $scholarship_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_saved_scholarships';

        // Check if already saved
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d AND scholarship_id = %d",
            $user_id,
            $scholarship_id
        ));

        if ($existing) {
            return $this->error('Scholarship already saved');
        }

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'scholarship_id' => $scholarship_id,
                'saved_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to save scholarship');
        }

        return $this->success(array('message' => 'Scholarship saved successfully'));
    }

    /**
     * Get saved scholarships
     */
    public function get_saved_scholarships($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_saved_scholarships';

        $saved = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY saved_at DESC",
            $user_id
        ));

        return $this->success($saved);
    }
}
