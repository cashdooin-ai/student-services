<?php
/**
 * Student Loan & EMI Calculator
 * Calculate education loan EMI and compare loan schemes
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Loan_Calculator extends Student_Services_Base_Service {

    public function calculate_emi($loan_amount, $interest_rate, $tenure_months) {
        $principal = floatval($loan_amount);
        $rate = floatval($interest_rate) / 12 / 100; // Monthly interest rate
        $tenure = intval($tenure_months);

        // EMI = [P x R x (1+R)^N] / [(1+R)^N-1]
        $emi = ($principal * $rate * pow(1 + $rate, $tenure)) / (pow(1 + $rate, $tenure) - 1);

        $total_payment = $emi * $tenure;
        $total_interest = $total_payment - $principal;

        return $this->success(array(
            'monthly_emi' => round($emi, 2),
            'total_payment' => round($total_payment, 2),
            'total_interest' => round($total_interest, 2),
            'principal' => $principal,
            'interest_rate' => $interest_rate,
            'tenure_months' => $tenure,
            'tenure_years' => round($tenure / 12, 1)
        ));
    }

    public function get_loan_schemes() {
        return $this->success(array(
            array(
                'id' => 1,
                'bank' => 'State Bank',
                'scheme_name' => 'Education Loan - Standard',
                'max_amount' => 1000000,
                'interest_rate' => 8.5,
                'processing_fee' => 1.0,
                'tenure_max' => 180, // 15 years
                'collateral_required' => 'Above 750000',
                'features' => array('Moratorium period', 'Tax benefits', 'No prepayment penalty')
            ),
            array(
                'id' => 2,
                'bank' => 'National Bank',
                'scheme_name' => 'Student Loan - Premium',
                'max_amount' => 2000000,
                'interest_rate' => 9.0,
                'processing_fee' => 0.5,
                'tenure_max' => 180,
                'collateral_required' => 'Above 1000000',
                'features' => array('Study abroad coverage', 'Grace period', 'Parent co-borrower')
            ),
            array(
                'id' => 3,
                'bank' => 'Education Finance Corp',
                'scheme_name' => 'Quick Edu Loan',
                'max_amount' => 500000,
                'interest_rate' => 10.5,
                'processing_fee' => 2.0,
                'tenure_max' => 120, // 10 years
                'collateral_required' => 'Not required',
                'features' => array('Fast approval', 'Minimal documentation', 'Online processing')
            )
        ));
    }

    public function compare_loan_schemes($loan_amount, $tenure_months) {
        $schemes_result = $this->get_loan_schemes();
        $schemes = $schemes_result['data'];
        $comparison = array();

        foreach ($schemes as $scheme) {
            if ($loan_amount <= $scheme['max_amount']) {
                $emi_result = $this->calculate_emi($loan_amount, $scheme['interest_rate'], $tenure_months);
                $emi_data = $emi_result['data'];

                $processing_fee = ($scheme['processing_fee'] / 100) * $loan_amount;

                $comparison[] = array(
                    'bank' => $scheme['bank'],
                    'scheme' => $scheme['scheme_name'],
                    'monthly_emi' => $emi_data['monthly_emi'],
                    'total_interest' => $emi_data['total_interest'],
                    'total_payment' => $emi_data['total_payment'],
                    'processing_fee' => round($processing_fee, 2),
                    'total_cost' => round($emi_data['total_payment'] + $processing_fee, 2),
                    'interest_rate' => $scheme['interest_rate'],
                    'collateral' => $scheme['collateral_required']
                );
            }
        }

        // Sort by total cost
        usort($comparison, function($a, $b) {
            return $a['total_cost'] <=> $b['total_cost'];
        });

        return $this->success($comparison);
    }

    public function get_amortization_schedule($loan_amount, $interest_rate, $tenure_months) {
        $principal = floatval($loan_amount);
        $rate = floatval($interest_rate) / 12 / 100;
        $tenure = intval($tenure_months);

        $emi_result = $this->calculate_emi($loan_amount, $interest_rate, $tenure_months);
        $emi = $emi_result['data']['monthly_emi'];

        $schedule = array();
        $balance = $principal;

        for ($month = 1; $month <= min($tenure, 12); $month++) { // First 12 months only
            $interest_payment = $balance * $rate;
            $principal_payment = $emi - $interest_payment;
            $balance -= $principal_payment;

            $schedule[] = array(
                'month' => $month,
                'emi' => round($emi, 2),
                'principal' => round($principal_payment, 2),
                'interest' => round($interest_payment, 2),
                'balance' => round($balance, 2)
            );
        }

        return $this->success($schedule);
    }

    public function calculate_with_moratorium($loan_amount, $interest_rate, $tenure_months, $moratorium_months) {
        // During moratorium, only interest is paid (simple interest)
        $principal = floatval($loan_amount);
        $rate = floatval($interest_rate) / 12 / 100;
        $moratorium = intval($moratorium_months);

        $interest_during_moratorium = $principal * $rate * $moratorium;
        $total_principal_after_moratorium = $principal + $interest_during_moratorium;

        // Calculate EMI on increased principal
        $actual_tenure = $tenure_months - $moratorium;
        $emi_result = $this->calculate_emi($total_principal_after_moratorium, $interest_rate, $actual_tenure);

        return $this->success(array(
            'original_loan' => $principal,
            'moratorium_months' => $moratorium,
            'interest_during_moratorium' => round($interest_during_moratorium, 2),
            'principal_after_moratorium' => round($total_principal_after_moratorium, 2),
            'emi_after_moratorium' => $emi_result['data']['monthly_emi'],
            'total_payment' => $emi_result['data']['total_payment'],
            'total_interest' => $emi_result['data']['total_interest']
        ));
    }

    public function save_calculation($user_id, $calculation_data) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $calc_id = $this->insert('loan_calculations', array(
            'user_id' => $user_id,
            'loan_amount' => $calculation_data['principal'],
            'interest_rate' => $calculation_data['interest_rate'],
            'tenure_months' => $calculation_data['tenure_months'],
            'monthly_emi' => $calculation_data['monthly_emi'],
            'calculation_date' => current_time('mysql')
        ));

        return $this->success(array('calculation_id' => $calc_id));
    }
}
