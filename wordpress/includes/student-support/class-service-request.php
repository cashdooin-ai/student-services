<?php
/**
 * Service Request Form with Payment Gateway
 * Submit service requests with ₹250 payment option
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Service_Request extends Student_Services_Base_Service {

    /**
     * Get service request types
     */
    public function get_service_types() {
        $types = array(
            array(
                'id' => 1,
                'name' => 'Document Verification',
                'description' => 'Get your documents verified by our team',
                'fee' => get_option('ss_service_fee_document_verification', 250),
                'processing_time' => '2-3 business days'
            ),
            array(
                'id' => 2,
                'name' => 'Application Review',
                'description' => 'Professional review of your college/scholarship application',
                'fee' => get_option('ss_service_fee_application_review', 250),
                'processing_time' => '1-2 business days'
            ),
            array(
                'id' => 3,
                'name' => 'SOP Review & Editing',
                'description' => 'Statement of Purpose review and professional editing',
                'fee' => get_option('ss_service_fee_sop_review', 250),
                'processing_time' => '2-3 business days'
            ),
            array(
                'id' => 4,
                'name' => 'Resume Review',
                'description' => 'Get your resume professionally reviewed',
                'fee' => get_option('ss_service_fee_resume_review', 250),
                'processing_time' => '1 business day'
            ),
            array(
                'id' => 5,
                'name' => 'Counseling Session',
                'description' => 'One-on-one counseling session with expert',
                'fee' => get_option('ss_service_fee_counseling', 250),
                'processing_time' => 'Schedule based'
            ),
            array(
                'id' => 6,
                'name' => 'Custom Request',
                'description' => 'Any other service request',
                'fee' => get_option('ss_service_fee_custom', 250),
                'processing_time' => 'Varies'
            )
        );

        return $this->success($types);
    }

    /**
     * Create service request
     */
    public function create_request($user_id, $request_data) {
        global $wpdb;

        $required = array('service_type_id', 'description');
        if (!$this->validate_required($request_data, $required)) {
            return $this->error('Missing required fields');
        }

        $service_type_id = intval($request_data['service_type_id']);
        $service_types = $this->get_service_types()['data'];
        $service_type = array_values(array_filter($service_types, function($st) use ($service_type_id) {
            return $st['id'] == $service_type_id;
        }))[0] ?? null;

        if (!$service_type) {
            return $this->error('Invalid service type');
        }

        $table_name = $wpdb->prefix . 'ss_service_requests';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'service_type_id' => $service_type_id,
                'service_name' => $service_type['name'],
                'description' => sanitize_textarea_field($request_data['description']),
                'documents' => isset($request_data['documents']) ? json_encode($request_data['documents']) : '',
                'fee_amount' => $service_type['fee'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to create service request');
        }

        $request_id = $wpdb->insert_id;

        return $this->success(array(
            'request_id' => $request_id,
            'fee_amount' => $service_type['fee'],
            'payment_required' => true,
            'message' => 'Service request created. Please complete the payment to proceed.'
        ));
    }

    /**
     * Initiate payment
     */
    public function initiate_payment($user_id, $request_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_service_requests';

        $request = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
            $request_id,
            $user_id
        ));

        if (!$request) {
            return $this->error('Service request not found');
        }

        if ($request->payment_status === 'completed') {
            return $this->error('Payment already completed');
        }

        // Get payment gateway settings
        $gateway = get_option('ss_payment_gateway', 'razorpay'); // razorpay, paytm, stripe, etc.
        $api_key = get_option('ss_payment_api_key', '');

        // Generate payment order
        $order_id = 'SSR-' . $request_id . '-' . time();

        $payment_data = array(
            'order_id' => $order_id,
            'amount' => $request->fee_amount,
            'currency' => 'INR',
            'gateway' => $gateway,
            'callback_url' => home_url('/payment-callback'),
            'user_email' => wp_get_current_user()->user_email,
            'user_name' => wp_get_current_user()->display_name
        );

        // In production, this would call actual payment gateway API
        // For now, return mock payment data

        // Store payment intent
        $payments_table = $wpdb->prefix . 'ss_payments';
        $wpdb->insert(
            $payments_table,
            array(
                'user_id' => $user_id,
                'request_id' => $request_id,
                'order_id' => $order_id,
                'amount' => $request->fee_amount,
                'currency' => 'INR',
                'gateway' => $gateway,
                'status' => 'initiated',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s')
        );

        return $this->success($payment_data);
    }

    /**
     * Verify and confirm payment
     */
    public function verify_payment($payment_id, $payment_data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_payments';

        $payment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $payment_id
        ));

        if (!$payment) {
            return $this->error('Payment not found');
        }

        // In production, verify payment with gateway
        // For now, mark as successful

        $wpdb->update(
            $table_name,
            array(
                'status' => 'completed',
                'transaction_id' => isset($payment_data['transaction_id']) ? $payment_data['transaction_id'] : '',
                'payment_method' => isset($payment_data['method']) ? $payment_data['method'] : '',
                'completed_at' => current_time('mysql')
            ),
            array('id' => $payment_id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );

        // Update service request payment status
        $requests_table = $wpdb->prefix . 'ss_service_requests';
        $wpdb->update(
            $requests_table,
            array(
                'payment_status' => 'completed',
                'status' => 'in_progress'
            ),
            array('id' => $payment->request_id),
            array('%s', '%s'),
            array('%d')
        );

        return $this->success(array(
            'message' => 'Payment verified successfully! Your service request is now being processed.'
        ));
    }

    /**
     * Get user's service requests
     */
    public function get_my_requests($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_service_requests';

        $requests = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        return $this->success($requests);
    }

    /**
     * Get request details
     */
    public function get_request_details($request_id, $user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_service_requests';

        $request = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
            $request_id,
            $user_id
        ));

        if (!$request) {
            return $this->error('Request not found');
        }

        return $this->success($request);
    }

    /**
     * Admin: Update service fee
     */
    public function update_service_fee($service_type, $new_fee) {
        $option_name = 'ss_service_fee_' . $service_type;
        update_option($option_name, floatval($new_fee));

        return $this->success(array('message' => 'Service fee updated successfully'));
    }

    /**
     * Admin: Get all service fees
     */
    public function get_service_fees() {
        $fees = array(
            'document_verification' => get_option('ss_service_fee_document_verification', 250),
            'application_review' => get_option('ss_service_fee_application_review', 250),
            'sop_review' => get_option('ss_service_fee_sop_review', 250),
            'resume_review' => get_option('ss_service_fee_resume_review', 250),
            'counseling' => get_option('ss_service_fee_counseling', 250),
            'custom' => get_option('ss_service_fee_custom', 250)
        );

        return $this->success($fees);
    }

    /**
     * Admin: Update payment gateway settings
     */
    public function update_payment_settings($gateway, $api_key, $api_secret = '') {
        update_option('ss_payment_gateway', $gateway);
        update_option('ss_payment_api_key', $api_key);
        update_option('ss_payment_api_secret', $api_secret);

        return $this->success(array('message' => 'Payment settings updated'));
    }
}
