<?php
/**
 * Subscription Plans
 * Premium features and paid tiers
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Subscription_Plans extends Student_Services_Base_Service {

    /**
     * Get available plans
     */
    public function get_plans() {
        return array(
            'success' => true,
            'data' => array(
                array(
                    'id' => 'free',
                    'name' => 'Free Plan',
                    'price' => 0,
                    'billing_cycle' => 'lifetime',
                    'features' => array(
                        'Basic college search',
                        'Access to blog and news',
                        'Limited scholarships search',
                        'Basic job listings',
                        '50 reward points signup bonus',
                        'Community forum access'
                    ),
                    'limitations' => array(
                        'job_applications_per_month' => 5,
                        'scholarship_applications_per_month' => 3,
                        'document_templates' => 2
                    )
                ),
                array(
                    'id' => 'student_basic',
                    'name' => 'Student Basic',
                    'price' => 299,
                    'billing_cycle' => 'monthly',
                    'price_yearly' => 2999,
                    'popular' => false,
                    'features' => array(
                        'Everything in Free',
                        'Unlimited job applications',
                        'Priority application review',
                        'Resume builder',
                        'All document templates',
                        '10 scholarship applications/month',
                        'Email notifications',
                        'Save unlimited favorites'
                    ),
                    'limitations' => array(
                        'scholarship_applications_per_month' => 10
                    )
                ),
                array(
                    'id' => 'student_premium',
                    'name' => 'Student Premium',
                    'price' => 599,
                    'billing_cycle' => 'monthly',
                    'price_yearly' => 5999,
                    'popular' => true,
                    'features' => array(
                        'Everything in Basic',
                        'AI-powered recommendations',
                        'Unlimited scholarship applications',
                        'Priority counseling support',
                        'Dedicated career advisor',
                        'Exclusive webinars & workshops',
                        'Study abroad assistance',
                        'Alumni network access',
                        'Certificate verification service',
                        'Interview preparation tools',
                        '500 bonus reward points',
                        'Ad-free experience'
                    ),
                    'limitations' => array()
                ),
                array(
                    'id' => 'employer_starter',
                    'name' => 'Employer Starter',
                    'price' => 999,
                    'billing_cycle' => 'monthly',
                    'price_yearly' => 9999,
                    'for_employers' => true,
                    'features' => array(
                        'Post 5 jobs/month',
                        'Basic applicant tracking',
                        'Job listings visible for 30 days',
                        'Email support',
                        'Basic analytics'
                    ),
                    'limitations' => array(
                        'jobs_per_month' => 5
                    )
                ),
                array(
                    'id' => 'employer_pro',
                    'name' => 'Employer Professional',
                    'price' => 2499,
                    'billing_cycle' => 'monthly',
                    'price_yearly' => 24999,
                    'for_employers' => true,
                    'popular_employer' => true,
                    'features' => array(
                        'Post unlimited jobs',
                        'Featured job listings',
                        'Advanced applicant tracking',
                        'Job listings visible for 60 days',
                        'Priority support',
                        'Detailed analytics & insights',
                        'Branded company page',
                        'Resume database access',
                        'Bulk job posting',
                        'API access'
                    ),
                    'limitations' => array()
                )
            )
        );
    }

    /**
     * Get user's current subscription
     */
    public function get_user_subscription($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_subscriptions';

        $subscription = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d AND status = 'active' ORDER BY id DESC LIMIT 1",
            $user_id
        ));

        if (!$subscription) {
            return array(
                'success' => true,
                'data' => array(
                    'plan_id' => 'free',
                    'plan_name' => 'Free Plan',
                    'status' => 'active'
                )
            );
        }

        return array(
            'success' => true,
            'data' => $subscription
        );
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe($user_id, $plan_id, $billing_cycle = 'monthly') {
        global $wpdb;
        $subscriptions_table = $wpdb->prefix . 'ss_subscriptions';

        // Get plan details
        $plans = $this->get_plans();
        $selected_plan = null;
        foreach ($plans['data'] as $plan) {
            if ($plan['id'] === $plan_id) {
                $selected_plan = $plan;
                break;
            }
        }

        if (!$selected_plan) {
            return array('success' => false, 'message' => 'Invalid plan');
        }

        if ($plan_id === 'free') {
            return array('success' => false, 'message' => 'You are already on the free plan');
        }

        // Calculate price based on billing cycle
        $price = $billing_cycle === 'yearly' && isset($selected_plan['price_yearly'])
            ? $selected_plan['price_yearly']
            : $selected_plan['price'];

        // Deactivate current subscription
        $wpdb->update(
            $subscriptions_table,
            array('status' => 'cancelled'),
            array('user_id' => $user_id, 'status' => 'active')
        );

        // Calculate expiry date
        $start_date = current_time('mysql');
        $expiry_date = $billing_cycle === 'yearly'
            ? date('Y-m-d H:i:s', strtotime('+1 year'))
            : date('Y-m-d H:i:s', strtotime('+1 month'));

        // Create new subscription
        $result = $wpdb->insert($subscriptions_table, array(
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'plan_name' => $selected_plan['name'],
            'billing_cycle' => $billing_cycle,
            'price' => $price,
            'status' => 'pending',
            'start_date' => $start_date,
            'expiry_date' => $expiry_date,
            'auto_renew' => 1,
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            $subscription_id = $wpdb->insert_id;

            // Create payment record
            $payments_table = $wpdb->prefix . 'ss_subscription_payments';
            $wpdb->insert($payments_table, array(
                'subscription_id' => $subscription_id,
                'user_id' => $user_id,
                'amount' => $price,
                'payment_method' => 'pending',
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ));

            return array(
                'success' => true,
                'subscription_id' => $subscription_id,
                'payment_required' => true,
                'amount' => $price
            );
        }

        return array('success' => false, 'message' => 'Failed to create subscription');
    }

    /**
     * Cancel subscription
     */
    public function cancel_subscription($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_subscriptions';

        $result = $wpdb->update(
            $table,
            array(
                'status' => 'cancelled',
                'cancelled_at' => current_time('mysql')
            ),
            array('user_id' => $user_id, 'status' => 'active')
        );

        if ($result) {
            return array('success' => true, 'message' => 'Subscription cancelled. You will continue to have access until the end of your billing period.');
        }

        return array('success' => false, 'message' => 'Failed to cancel subscription');
    }

    /**
     * Check feature access
     */
    public function has_feature_access($user_id, $feature) {
        $subscription = $this->get_user_subscription($user_id);
        $plan_id = $subscription['data']['plan_id'] ?? 'free';

        // Define feature access matrix
        $feature_matrix = array(
            'unlimited_jobs' => array('student_basic', 'student_premium'),
            'unlimited_scholarships' => array('student_premium'),
            'ai_recommendations' => array('student_premium'),
            'alumni_network' => array('student_premium'),
            'career_advisor' => array('student_premium'),
            'featured_jobs' => array('employer_pro'),
            'unlimited_job_posts' => array('employer_pro'),
            'resume_database' => array('employer_pro')
        );

        if (!isset($feature_matrix[$feature])) {
            return true; // Feature doesn't require premium
        }

        return in_array($plan_id, $feature_matrix[$feature]);
    }

    /**
     * Get usage limits for user
     */
    public function get_usage_limits($user_id) {
        $subscription = $this->get_user_subscription($user_id);
        $plan_id = $subscription['data']['plan_id'] ?? 'free';

        $limits = array(
            'free' => array(
                'job_applications_per_month' => 5,
                'scholarship_applications_per_month' => 3,
                'document_templates' => 2
            ),
            'student_basic' => array(
                'job_applications_per_month' => -1, // unlimited
                'scholarship_applications_per_month' => 10,
                'document_templates' => -1
            ),
            'student_premium' => array(
                'job_applications_per_month' => -1,
                'scholarship_applications_per_month' => -1,
                'document_templates' => -1
            ),
            'employer_starter' => array(
                'jobs_per_month' => 5
            ),
            'employer_pro' => array(
                'jobs_per_month' => -1
            )
        );

        return array(
            'success' => true,
            'data' => $limits[$plan_id] ?? $limits['free']
        );
    }

    /**
     * Check if user exceeded limit
     */
    public function check_limit($user_id, $action) {
        global $wpdb;

        $limits = $this->get_usage_limits($user_id);
        $limit = $limits['data'][$action] ?? 0;

        if ($limit === -1) {
            return true; // Unlimited
        }

        // Count current usage this month
        $start_of_month = date('Y-m-01 00:00:00');
        $count = 0;

        switch ($action) {
            case 'job_applications_per_month':
                $count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_job_applications WHERE user_id = %d AND created_at >= %s",
                    $user_id,
                    $start_of_month
                ));
                break;
            case 'scholarship_applications_per_month':
                $count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}ss_scholarship_applications WHERE user_id = %d AND created_at >= %s",
                    $user_id,
                    $start_of_month
                ));
                break;
            case 'jobs_per_month':
                $employer_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}ss_employers WHERE user_id = %d",
                    $user_id
                ));
                if ($employer_id) {
                    $count = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->prefix}ss_jobs WHERE employer_id = %d AND created_at >= %s",
                        $employer_id,
                        $start_of_month
                    ));
                }
                break;
        }

        return $count < $limit;
    }

    /**
     * Get subscription history
     */
    public function get_subscription_history($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_subscriptions';

        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $history
        );
    }

    /**
     * Get payment history
     */
    public function get_payment_history($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_subscription_payments';

        $payments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $payments
        );
    }
}
