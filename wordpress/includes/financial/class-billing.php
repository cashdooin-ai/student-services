<?php
/**
 * Billing Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Billing extends Student_Services_Base_Service {

    /**
     * Get billing account
     */
    public function get_account($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $account = $this->get_row('billing', array('user_id' => $user_id));

        if (!$account) {
            // Create account if doesn't exist
            $this->insert('billing', array(
                'user_id' => $user_id,
                'balance' => 0.00,
                'due_date' => null
            ));
            $account = $this->get_row('billing', array('user_id' => $user_id));
        }

        // Get charges
        $charges = $this->get_results('charges', array('user_id' => $user_id));

        // Get payments
        $payments = $this->get_results('payments', array('user_id' => $user_id));

        return $this->success(array(
            'balance' => floatval($account->balance),
            'due_date' => $account->due_date,
            'charges' => $charges,
            'payments' => $payments
        ));
    }

    /**
     * Make payment
     */
    public function make_payment($user_id, $amount, $method = 'credit') {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        if ($amount <= 0) {
            return $this->error(__('Payment amount must be greater than zero', 'student-services'));
        }

        // Record payment
        $payment_id = $this->insert('payments', array(
            'user_id' => $user_id,
            'amount' => $amount,
            'payment_method' => $method,
            'status' => 'completed',
            'payment_date' => current_time('mysql')
        ));

        // Update balance
        $account = $this->get_row('billing', array('user_id' => $user_id));
        $new_balance = max(0, $account->balance - $amount);

        $this->update('billing',
            array('balance' => $new_balance),
            array('user_id' => $user_id)
        );

        return $this->success(array(
            'payment_id' => $payment_id,
            'new_balance' => $new_balance
        ), __('Payment processed successfully', 'student-services'));
    }

    /**
     * Add charge (admin only)
     */
    public function add_charge($user_id, $description, $amount, $category = 'tuition') {
        if (!current_user_can('manage_options')) {
            return $this->error(__('Permission denied', 'student-services'), 'permission_denied', 403);
        }

        $charge_id = $this->insert('charges', array(
            'user_id' => $user_id,
            'description' => $description,
            'amount' => $amount,
            'category' => $category,
            'charge_date' => current_time('mysql')
        ));

        // Update balance
        $account = $this->get_row('billing', array('user_id' => $user_id));
        $new_balance = $account->balance + $amount;

        $this->update('billing',
            array('balance' => $new_balance),
            array('user_id' => $user_id)
        );

        return $this->success(array('charge_id' => $charge_id), __('Charge added successfully', 'student-services'));
    }
}
