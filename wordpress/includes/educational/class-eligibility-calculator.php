<?php
/**
 * Eligibility Calculator Service
 * Find out which colleges you're eligible for based on academic scores
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Eligibility_Calculator extends Student_Services_Base_Service {

    /**
     * Calculate eligibility for colleges
     */
    public function calculate_eligibility($scores) {
        $validation = $this->validate_required($scores, array('percentage'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        $percentage = floatval($scores['percentage']);
        $exam_scores = isset($scores['exam_scores']) ? $scores['exam_scores'] : array();

        $eligible_colleges = $this->find_eligible_colleges($percentage, $exam_scores);

        return $this->success(array(
            'total_eligible' => count($eligible_colleges),
            'colleges' => $eligible_colleges,
            'eligibility_summary' => $this->generate_summary($percentage, count($eligible_colleges))
        ));
    }

    /**
     * Find eligible colleges based on scores
     */
    private function find_eligible_colleges($percentage, $exam_scores) {
        // Mock college database with eligibility criteria
        $all_colleges = array(
            array(
                'name' => 'Premier University',
                'min_percentage' => 90,
                'min_exam_score' => 85,
                'exam_required' => 'JEE',
                'difficulty' => 'High',
                'acceptance_rate' => 15
            ),
            array(
                'name' => 'State Engineering College',
                'min_percentage' => 80,
                'min_exam_score' => 75,
                'exam_required' => 'JEE',
                'difficulty' => 'Medium',
                'acceptance_rate' => 35
            ),
            array(
                'name' => 'City University',
                'min_percentage' => 70,
                'min_exam_score' => 65,
                'exam_required' => 'CUET',
                'difficulty' => 'Medium',
                'acceptance_rate' => 45
            ),
            array(
                'name' => 'Regional College',
                'min_percentage' => 60,
                'min_exam_score' => 55,
                'exam_required' => 'CUET',
                'difficulty' => 'Low',
                'acceptance_rate' => 65
            ),
            array(
                'name' => 'Open University',
                'min_percentage' => 50,
                'min_exam_score' => 0,
                'exam_required' => 'None',
                'difficulty' => 'Low',
                'acceptance_rate' => 85
            )
        );

        $eligible = array();

        foreach ($all_colleges as $college) {
            $is_eligible = $percentage >= $college['min_percentage'];

            // Check exam score if exam is required
            if ($college['exam_required'] !== 'None' && isset($exam_scores[$college['exam_required']])) {
                $exam_score = floatval($exam_scores[$college['exam_required']]);
                $is_eligible = $is_eligible && ($exam_score >= $college['min_exam_score']);
            }

            if ($is_eligible) {
                $college['your_percentage'] = $percentage;
                $college['eligibility_status'] = 'Eligible';

                // Calculate admission probability
                $over_percentage = $percentage - $college['min_percentage'];
                $probability = min(95, $college['acceptance_rate'] + ($over_percentage * 2));
                $college['admission_probability'] = round($probability) . '%';

                $eligible[] = $college;
            }
        }

        return $eligible;
    }

    /**
     * Generate eligibility summary
     */
    private function generate_summary($percentage, $eligible_count) {
        if ($percentage >= 90) {
            $category = 'Excellent';
            $message = 'You are eligible for top-tier universities!';
        } elseif ($percentage >= 80) {
            $category = 'Very Good';
            $message = 'You have strong eligibility for quality institutions.';
        } elseif ($percentage >= 70) {
            $category = 'Good';
            $message = 'You are eligible for several good colleges.';
        } elseif ($percentage >= 60) {
            $category = 'Average';
            $message = 'You have options among regional colleges.';
        } else {
            $category = 'Fair';
            $message = 'Consider improving scores for better options.';
        }

        return array(
            'category' => $category,
            'message' => $message,
            'eligible_count' => $eligible_count,
            'percentage' => $percentage
        );
    }

    /**
     * Get detailed eligibility report
     */
    public function get_detailed_report($user_id, $scores) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $eligibility = $this->calculate_eligibility($scores);

        if (!$eligibility['success']) {
            return $eligibility;
        }

        // Save calculation to history
        $this->insert('eligibility_calculations', array(
            'user_id' => $user_id,
            'percentage' => floatval($scores['percentage']),
            'exam_scores' => wp_json_encode(isset($scores['exam_scores']) ? $scores['exam_scores'] : array()),
            'eligible_count' => $eligibility['data']['total_eligible'],
            'calculation_date' => current_time('mysql')
        ));

        return $eligibility;
    }

    /**
     * Compare with peers
     */
    public function compare_with_peers($percentage) {
        $table = $this->get_table('eligibility_calculations');

        $sql = $this->wpdb->prepare(
            "SELECT
                COUNT(*) as total_students,
                COUNT(CASE WHEN percentage >= %f THEN 1 END) as students_below
            FROM $table
            WHERE calculation_date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)",
            $percentage
        );

        $result = $this->wpdb->get_row($sql);

        if ($result && $result->total_students > 0) {
            $percentile = ($result->students_below / $result->total_students) * 100;
        } else {
            $percentile = 50; // Default if no data
        }

        return $this->success(array(
            'your_percentage' => $percentage,
            'percentile' => round($percentile),
            'total_comparisons' => $result->total_students ?? 0,
            'message' => sprintf('You performed better than %d%% of students', round($percentile))
        ));
    }
}
