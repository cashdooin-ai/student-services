<?php
/**
 * Referral & Rewards System
 * Share your unique referral code with friends and earn reward points
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Referral_Rewards extends Student_Services_Base_Service {

    /**
     * Get user's referral code
     */
    public function get_referral_code($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_referral_codes';

        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        if (!$code) {
            // Generate new code
            $referral_code = $this->generate_unique_code($user_id);
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'code' => $referral_code,
                'created_at' => current_time('mysql')
            ));

            $code = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table} WHERE user_id = %d",
                $user_id
            ));
        }

        return array(
            'success' => true,
            'data' => $code
        );
    }

    /**
     * Generate unique referral code
     */
    private function generate_unique_code($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_referral_codes';

        do {
            $code = 'REF' . strtoupper(substr(md5($user_id . time() . rand()), 0, 8));
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$table} WHERE code = %s",
                $code
            ));
        } while ($exists);

        return $code;
    }

    /**
     * Apply referral code
     */
    public function apply_referral_code($user_id, $code) {
        global $wpdb;
        $codes_table = $wpdb->prefix . 'ss_referral_codes';
        $referrals_table = $wpdb->prefix . 'ss_referrals';

        // Check if code exists
        $referrer_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$codes_table} WHERE code = %s",
            $code
        ));

        if (!$referrer_data) {
            return array('success' => false, 'message' => 'Invalid referral code');
        }

        if ($referrer_data->user_id == $user_id) {
            return array('success' => false, 'message' => 'You cannot use your own referral code');
        }

        // Check if user already used a referral code
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$referrals_table} WHERE referred_user_id = %d",
            $user_id
        ));

        if ($existing) {
            return array('success' => false, 'message' => 'You have already used a referral code');
        }

        // Add referral
        $result = $wpdb->insert($referrals_table, array(
            'referrer_user_id' => $referrer_data->user_id,
            'referred_user_id' => $user_id,
            'code_used' => $code,
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            // Award initial bonus points to both users
            $this->add_reward_points($user_id, 50, 'signup_bonus', 'Sign-up bonus for using referral code');
            $this->add_reward_points($referrer_data->user_id, 100, 'referral_signup', 'Friend signed up using your referral code');

            return array('success' => true, 'message' => 'Referral code applied! You earned 50 points!');
        }

        return array('success' => false, 'message' => 'Failed to apply referral code');
    }

    /**
     * Get user's referrals
     */
    public function get_my_referrals($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_referrals';

        $referrals = $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, u.display_name as referred_user_name, u.user_email
            FROM {$table} r
            LEFT JOIN {$wpdb->users} u ON r.referred_user_id = u.ID
            WHERE r.referrer_user_id = %d
            ORDER BY r.created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $referrals
        );
    }

    /**
     * Get reward points balance
     */
    public function get_points_balance($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_reward_points';

        $total_points = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(points), 0) FROM {$table} WHERE user_id = %d",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => array(
                'total_points' => intval($total_points),
                'currency_value' => round(intval($total_points) * 0.10, 2) // 1 point = ₹0.10
            )
        );
    }

    /**
     * Get points history
     */
    public function get_points_history($user_id, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_reward_points';

        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC LIMIT %d",
            $user_id,
            $limit
        ));

        return array(
            'success' => true,
            'data' => $history
        );
    }

    /**
     * Add reward points
     */
    public function add_reward_points($user_id, $points, $type, $description) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_reward_points';

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'points' => intval($points),
            'type' => sanitize_text_field($type),
            'description' => sanitize_text_field($description),
            'created_at' => current_time('mysql')
        ));

        return $result !== false;
    }

    /**
     * Get rewards catalog
     */
    public function get_rewards_catalog() {
        return array(
            'success' => true,
            'data' => array(
                array(
                    'id' => 1,
                    'name' => 'Service Fee Discount - ₹50',
                    'points_required' => 500,
                    'discount_amount' => 50,
                    'type' => 'discount',
                    'icon' => '💰'
                ),
                array(
                    'id' => 2,
                    'name' => 'Service Fee Discount - ₹100',
                    'points_required' => 1000,
                    'discount_amount' => 100,
                    'type' => 'discount',
                    'icon' => '💰'
                ),
                array(
                    'id' => 3,
                    'name' => 'Free Document Review',
                    'points_required' => 750,
                    'type' => 'service',
                    'icon' => '📄'
                ),
                array(
                    'id' => 4,
                    'name' => 'Premium Counseling Session',
                    'points_required' => 1500,
                    'type' => 'service',
                    'icon' => '💼'
                ),
                array(
                    'id' => 5,
                    'name' => 'Scholarship Application Review',
                    'points_required' => 800,
                    'type' => 'service',
                    'icon' => '🎓'
                ),
                array(
                    'id' => 6,
                    'name' => 'Amazon Gift Card - ₹500',
                    'points_required' => 5000,
                    'type' => 'gift_card',
                    'icon' => '🎁'
                ),
                array(
                    'id' => 7,
                    'name' => 'Amazon Gift Card - ₹1000',
                    'points_required' => 10000,
                    'type' => 'gift_card',
                    'icon' => '🎁'
                ),
            )
        );
    }

    /**
     * Redeem reward
     */
    public function redeem_reward($user_id, $reward_id, $points_required) {
        global $wpdb;

        // Check if user has enough points
        $balance = $this->get_points_balance($user_id);
        if ($balance['data']['total_points'] < $points_required) {
            return array('success' => false, 'message' => 'Insufficient points');
        }

        // Deduct points
        $points_table = $wpdb->prefix . 'ss_reward_points';
        $wpdb->insert($points_table, array(
            'user_id' => $user_id,
            'points' => -$points_required,
            'type' => 'redemption',
            'description' => 'Redeemed reward #' . $reward_id,
            'created_at' => current_time('mysql')
        ));

        // Add to redemptions history
        $redemptions_table = $wpdb->prefix . 'ss_reward_redemptions';
        $wpdb->insert($redemptions_table, array(
            'user_id' => $user_id,
            'reward_id' => $reward_id,
            'points_spent' => $points_required,
            'status' => 'pending',
            'created_at' => current_time('mysql')
        ));

        return array(
            'success' => true,
            'redemption_id' => $wpdb->insert_id,
            'message' => 'Reward redeemed successfully! You will receive it within 24-48 hours.'
        );
    }

    /**
     * Get redemption history
     */
    public function get_redemption_history($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_reward_redemptions';

        $redemptions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $redemptions
        );
    }

    /**
     * Get referral stats
     */
    public function get_referral_stats($user_id) {
        global $wpdb;
        $referrals_table = $wpdb->prefix . 'ss_referrals';

        $stats = array(
            'total_referrals' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$referrals_table} WHERE referrer_user_id = %d",
                $user_id
            )),
            'completed_referrals' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$referrals_table} WHERE referrer_user_id = %d AND status = 'completed'",
                $user_id
            )),
            'pending_referrals' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$referrals_table} WHERE referrer_user_id = %d AND status = 'pending'",
                $user_id
            )),
        );

        return array(
            'success' => true,
            'data' => $stats
        );
    }
}
