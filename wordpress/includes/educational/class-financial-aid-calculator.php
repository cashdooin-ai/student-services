<?php
/**
 * Financial Aid Calculator
 * Plan education financing with comprehensive calculator
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Financial_Aid_Calculator extends Student_Services_Base_Service {

    public function calculate_financial_need($data) {
        $validation = $this->validate_required($data, array('tuition_fee', 'family_income'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        $tuition = floatval($data['tuition_fee']);
        $family_income = floatval($data['family_income']);
        $siblings_in_college = isset($data['siblings_in_college']) ? intval($data['siblings_in_college']) : 0;
        $family_size = isset($data['family_size']) ? intval($data['family_size']) : 4;

        // Calculate Expected Family Contribution (EFC)
        $efc = $this->calculate_efc($family_income, $family_size, $siblings_in_college);

        // Calculate total cost of attendance
        $total_cost = $tuition;
        $living_expenses = isset($data['living_expenses']) ? floatval($data['living_expenses']) : ($tuition * 0.4);
        $books_supplies = isset($data['books_supplies']) ? floatval($data['books_supplies']) : 2000;
        $personal_expenses = isset($data['personal_expenses']) ? floatval($data['personal_expenses']) : 3000;

        $total_cost_of_attendance = $tuition + $living_expenses + $books_supplies + $personal_expenses;

        // Financial need
        $financial_need = max(0, $total_cost_of_attendance - $efc);

        // Estimated aid package
        $aid_package = $this->generate_aid_package($financial_need, $tuition);

        return $this->success(array(
            'total_cost_of_attendance' => round($total_cost_of_attendance, 2),
            'expected_family_contribution' => round($efc, 2),
            'financial_need' => round($financial_need, 2),
            'aid_package' => $aid_package,
            'out_of_pocket' => round($total_cost_of_attendance - $aid_package['total_aid'], 2),
            'breakdown' => array(
                'tuition' => $tuition,
                'living_expenses' => $living_expenses,
                'books_supplies' => $books_supplies,
                'personal_expenses' => $personal_expenses
            )
        ));
    }

    private function calculate_efc($income, $family_size, $siblings) {
        $base_efc = $income * 0.22; // Simplified EFC calculation
        
        // Adjust for family size
        $family_adjustment = ($family_size - 4) * 0.05;
        $base_efc = $base_efc * (1 - $family_adjustment);

        // Adjust for siblings in college
        if ($siblings > 0) {
            $base_efc = $base_efc / ($siblings + 1);
        }

        return max(0, $base_efc);
    }

    private function generate_aid_package($financial_need, $tuition) {
        // Simplified aid package
        $grants = min($financial_need * 0.4, $tuition * 0.5);
        $scholarships = min($financial_need * 0.2, $tuition * 0.3);
        $work_study = min($financial_need * 0.1, 4000);
        $loans = max(0, $financial_need - $grants - $scholarships - $work_study);

        return array(
            'grants' => round($grants, 2),
            'scholarships' => round($scholarships, 2),
            'work_study' => round($work_study, 2),
            'loans' => round($loans, 2),
            'total_aid' => round($grants + $scholarships + $work_study + $loans, 2)
        );
    }

    public function compare_colleges_financial($colleges_data) {
        $results = array();

        foreach ($colleges_data as $college) {
            $calculation = $this->calculate_financial_need($college);
            $results[] = array(
                'college_name' => $college['name'],
                'total_cost' => $calculation['data']['total_cost_of_attendance'],
                'financial_aid' => $calculation['data']['aid_package']['total_aid'],
                'net_cost' => $calculation['data']['out_of_pocket'],
                'loan_required' => $calculation['data']['aid_package']['loans']
            );
        }

        // Sort by net cost
        usort($results, function($a, $b) {
            return $a['net_cost'] <=> $b['net_cost'];
        });

        return $this->success($results);
    }

    public function save_calculation($user_id, $calculation_data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $calc_id = $this->insert('financial_calculations', array(
            'user_id' => $user_id,
            'calculation_data' => wp_json_encode($calculation_data),
            'total_cost' => $calculation_data['total_cost_of_attendance'],
            'financial_need' => $calculation_data['financial_need'],
            'calculation_date' => current_time('mysql')
        ));

        return $this->success(array('calculation_id' => $calc_id));
    }
}
