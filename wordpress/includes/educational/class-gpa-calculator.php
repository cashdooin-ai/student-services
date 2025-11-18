<?php
/**
 * GPA Calculator & Grade Converter
 * Calculate GPA and convert between different grading systems
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_GPA_Calculator extends Student_Services_Base_Service {

    public function calculate_gpa($grades, $scale = '4.0') {
        $total_points = 0;
        $total_credits = 0;

        foreach ($grades as $grade) {
            $credits = floatval($grade['credits']);
            $grade_point = $this->get_grade_point($grade['grade'], $scale);

            $total_points += $grade_point * $credits;
            $total_credits += $credits;
        }

        $gpa = $total_credits > 0 ? $total_points / $total_credits : 0;

        return $this->success(array(
            'gpa' => round($gpa, 2),
            'total_credits' => $total_credits,
            'quality_points' => round($total_points, 2),
            'scale' => $scale,
            'letter_grade' => $this->gpa_to_letter($gpa),
            'percentage' => $this->gpa_to_percentage($gpa, $scale)
        ));
    }

    private function get_grade_point($grade, $scale = '4.0') {
        $grade = strtoupper(trim($grade));

        if ($scale === '4.0') {
            $conversion = array(
                'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
                'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
                'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
                'D+' => 1.3, 'D' => 1.0, 'D-' => 0.7,
                'F' => 0.0
            );
        } elseif ($scale === '10.0') {
            $conversion = array(
                'A+' => 10.0, 'A' => 9.0, 'A-' => 8.5,
                'B+' => 8.0, 'B' => 7.0, 'B-' => 6.5,
                'C+' => 6.0, 'C' => 5.0, 'C-' => 4.5,
                'D' => 4.0, 'F' => 0.0
            );
        } else {
            $conversion = array('A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0);
        }

        return isset($conversion[$grade]) ? $conversion[$grade] : 0.0;
    }

    private function gpa_to_letter($gpa) {
        if ($gpa >= 3.7) return 'A';
        if ($gpa >= 3.3) return 'A-';
        if ($gpa >= 3.0) return 'B+';
        if ($gpa >= 2.7) return 'B';
        if ($gpa >= 2.3) return 'B-';
        if ($gpa >= 2.0) return 'C+';
        if ($gpa >= 1.7) return 'C';
        if ($gpa >= 1.0) return 'D';
        return 'F';
    }

    private function gpa_to_percentage($gpa, $scale = '4.0') {
        if ($scale === '4.0') {
            return round(($gpa / 4.0) * 100, 2);
        } elseif ($scale === '10.0') {
            return round(($gpa / 10.0) * 100, 2);
        }
        return round($gpa * 25, 2);
    }

    public function convert_grade($grade, $from_system, $to_system) {
        // Convert to percentage first
        $percentage = $this->to_percentage($grade, $from_system);

        // Convert from percentage to target system
        $converted = $this->from_percentage($percentage, $to_system);

        return $this->success(array(
            'original_grade' => $grade,
            'original_system' => $from_system,
            'converted_grade' => $converted,
            'target_system' => $to_system,
            'percentage_equivalent' => $percentage
        ));
    }

    private function to_percentage($grade, $system) {
        switch ($system) {
            case 'percentage':
                return floatval($grade);
            case 'gpa_4':
                return (floatval($grade) / 4.0) * 100;
            case 'gpa_10':
                return (floatval($grade) / 10.0) * 100;
            case 'letter':
                $conversions = array(
                    'A+' => 97, 'A' => 93, 'A-' => 90,
                    'B+' => 87, 'B' => 83, 'B-' => 80,
                    'C+' => 77, 'C' => 73, 'C-' => 70,
                    'D+' => 67, 'D' => 63, 'F' => 50
                );
                return $conversions[strtoupper($grade)] ?? 75;
            default:
                return floatval($grade);
        }
    }

    private function from_percentage($percentage, $system) {
        switch ($system) {
            case 'percentage':
                return round($percentage, 2);
            case 'gpa_4':
                return round(($percentage / 100) * 4.0, 2);
            case 'gpa_10':
                return round(($percentage / 100) * 10.0, 2);
            case 'letter':
                if ($percentage >= 93) return 'A';
                if ($percentage >= 90) return 'A-';
                if ($percentage >= 87) return 'B+';
                if ($percentage >= 83) return 'B';
                if ($percentage >= 80) return 'B-';
                if ($percentage >= 77) return 'C+';
                if ($percentage >= 73) return 'C';
                if ($percentage >= 70) return 'C-';
                if ($percentage >= 60) return 'D';
                return 'F';
            default:
                return round($percentage, 2);
        }
    }

    public function calculate_cumulative_gpa($previous_gpa, $previous_credits, $current_grades) {
        $current_result = $this->calculate_gpa($current_grades);
        $current_data = $current_result['data'];

        $previous_points = floatval($previous_gpa) * floatval($previous_credits);
        $total_points = $previous_points + $current_data['quality_points'];
        $total_credits = floatval($previous_credits) + $current_data['total_credits'];

        $cumulative_gpa = $total_credits > 0 ? $total_points / $total_credits : 0;

        return $this->success(array(
            'previous_gpa' => floatval($previous_gpa),
            'previous_credits' => floatval($previous_credits),
            'current_semester_gpa' => $current_data['gpa'],
            'current_semester_credits' => $current_data['total_credits'],
            'cumulative_gpa' => round($cumulative_gpa, 2),
            'total_credits' => $total_credits
        ));
    }

    public function save_calculation($user_id, $calculation_data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $calc_id = $this->insert('gpa_calculations', array(
            'user_id' => $user_id,
            'gpa' => $calculation_data['gpa'],
            'total_credits' => $calculation_data['total_credits'],
            'grades_data' => wp_json_encode($calculation_data),
            'calculation_date' => current_time('mysql')
        ));

        return $this->success(array('calculation_id' => $calc_id));
    }
}
