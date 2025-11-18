<?php
/**
 * College Cost Comparison Service
 * Compare total cost of attendance across multiple colleges
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_College_Cost_Comparison extends Student_Services_Base_Service {

    public function compare_colleges($colleges_data) {
        $comparisons = array();

        foreach ($colleges_data as $college) {
            $total_cost = $this->calculate_total_cost($college);
            $comparisons[] = array(
                'college_name' => $college['name'],
                'location' => isset($college['location']) ? $college['location'] : '',
                'costs' => $total_cost,
                'roi' => $this->calculate_roi($total_cost['four_year_total'], isset($college['avg_salary']) ? $college['avg_salary'] : 50000)
            );
        }

        // Sort by total cost
        usort($comparisons, function($a, $b) {
            return $a['costs']['four_year_total'] <=> $b['costs']['four_year_total'];
        });

        return $this->success(array(
            'comparisons' => $comparisons,
            'cheapest' => $comparisons[0]['college_name'] ?? '',
            'most_expensive' => end($comparisons)['college_name'] ?? '',
            'average_cost' => $this->calculate_average_cost($comparisons)
        ));
    }

    private function calculate_total_cost($college) {
        $tuition = isset($college['tuition']) ? floatval($college['tuition']) : 0;
        $room_board = isset($college['room_board']) ? floatval($college['room_board']) : ($tuition * 0.4);
        $books = isset($college['books']) ? floatval($college['books']) : 2000;
        $personal = isset($college['personal']) ? floatval($college['personal']) : 3000;
        $transportation = isset($college['transportation']) ? floatval($college['transportation']) : 1500;
        $fees = isset($college['fees']) ? floatval($college['fees']) : 1000;

        $annual_cost = $tuition + $room_board + $books + $personal + $transportation + $fees;
        $four_year_total = $annual_cost * 4;

        return array(
            'tuition' => round($tuition, 2),
            'room_board' => round($room_board, 2),
            'books_supplies' => round($books, 2),
            'personal_expenses' => round($personal, 2),
            'transportation' => round($transportation, 2),
            'fees' => round($fees, 2),
            'annual_total' => round($annual_cost, 2),
            'four_year_total' => round($four_year_total, 2)
        );
    }

    private function calculate_roi($total_cost, $avg_salary) {
        $payback_years = $total_cost / $avg_salary;
        $lifetime_earnings = $avg_salary * 40; // 40 years career
        $roi_percentage = (($lifetime_earnings - $total_cost) / $total_cost) * 100;

        return array(
            'payback_period_years' => round($payback_years, 1),
            'avg_starting_salary' => $avg_salary,
            'lifetime_earnings' => round($lifetime_earnings, 2),
            'roi_percentage' => round($roi_percentage, 1)
        );
    }

    private function calculate_average_cost($comparisons) {
        if (empty($comparisons)) {
            return 0;
        }

        $total = array_sum(array_column(array_column($comparisons, 'costs'), 'four_year_total'));
        return round($total / count($comparisons), 2);
    }

    public function get_financial_aid_impact($college_data, $estimated_aid) {
        $total_cost = $this->calculate_total_cost($college_data);
        $aid = floatval($estimated_aid);

        return $this->success(array(
            'sticker_price' => $total_cost['four_year_total'],
            'estimated_aid' => $aid,
            'net_price' => round($total_cost['four_year_total'] - $aid, 2),
            'savings_percentage' => round(($aid / $total_cost['four_year_total']) * 100, 1),
            'annual_net_cost' => round(($total_cost['four_year_total'] - $aid) / 4, 2)
        ));
    }

    public function save_comparison($user_id, $comparison_data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $comp_id = $this->insert('cost_comparisons', array(
            'user_id' => $user_id,
            'colleges_compared' => wp_json_encode($comparison_data),
            'comparison_date' => current_time('mysql')
        ));

        return $this->success(array('comparison_id' => $comp_id));
    }
}
