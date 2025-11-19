<?php
/**
 * Accommodation Finder Service
 * Find Your Perfect Accommodation - Verified hostels, PGs, and flats near college
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Accommodation extends Student_Services_Base_Service {

    /**
     * Search accommodations
     */
    public function search_accommodations($filters = array()) {
        // Mock accommodation data
        $accommodations = array(
            array(
                'id' => 1,
                'name' => 'Green Valley Boys Hostel',
                'type' => 'Hostel',
                'gender' => 'Boys',
                'location' => 'Sector 15, Near IIT Delhi',
                'distance_from_college' => '500m',
                'rent_per_month' => 8000,
                'sharing_type' => 'Triple Sharing',
                'facilities' => array('WiFi', 'Mess', 'Laundry', 'AC', 'Study Room', 'Security'),
                'available_from' => date('Y-m-d', strtotime('+15 days')),
                'verified' => true,
                'rating' => 4.5,
                'reviews_count' => 45,
                'images' => array(),
                'contact_person' => 'Mr. Sharma',
                'contact_number' => '+91 98765 43210',
                'featured' => true
            ),
            array(
                'id' => 2,
                'name' => 'Sunrise PG for Girls',
                'type' => 'PG',
                'gender' => 'Girls',
                'location' => 'Malviya Nagar, Near DU South Campus',
                'distance_from_college' => '1.2km',
                'rent_per_month' => 12000,
                'sharing_type' => 'Double Sharing',
                'facilities' => array('WiFi', '3 Meals', 'AC', 'Geyser', 'Laundry', '24/7 Security', 'CCTV'),
                'available_from' => date('Y-m-d'),
                'verified' => true,
                'rating' => 4.7,
                'reviews_count' => 32,
                'images' => array(),
                'contact_person' => 'Mrs. Verma',
                'contact_number' => '+91 98765 43211',
                'featured' => true
            ),
            array(
                'id' => 3,
                'name' => 'Student Apartments',
                'type' => 'Flat',
                'gender' => 'Co-ed',
                'location' => 'Kalkaji, Near NIFT',
                'distance_from_college' => '800m',
                'rent_per_month' => 15000,
                'sharing_type' => '2 BHK',
                'facilities' => array('WiFi', 'Fully Furnished', 'Kitchen', 'Parking', 'Power Backup'),
                'available_from' => date('Y-m-d', strtotime('+7 days')),
                'verified' => true,
                'rating' => 4.3,
                'reviews_count' => 18,
                'images' => array(),
                'contact_person' => 'Mr. Kumar',
                'contact_number' => '+91 98765 43212',
                'featured' => false
            ),
            array(
                'id' => 4,
                'name' => 'Metro View Hostel',
                'type' => 'Hostel',
                'gender' => 'Boys',
                'location' => 'Rajouri Garden, Near DTU',
                'distance_from_college' => '300m',
                'rent_per_month' => 7000,
                'sharing_type' => 'Four Sharing',
                'facilities' => array('WiFi', 'Mess', 'Common Room', 'Gym', 'Study Room'),
                'available_from' => date('Y-m-d', strtotime('+20 days')),
                'verified' => true,
                'rating' => 4.2,
                'reviews_count' => 56,
                'images' => array(),
                'contact_person' => 'Mr. Gupta',
                'contact_number' => '+91 98765 43213',
                'featured' => false
            ),
            array(
                'id' => 5,
                'name' => 'Elite PG & Hostel',
                'type' => 'PG',
                'gender' => 'Girls',
                'location' => 'Satya Niketan, Near Venkateshwara College',
                'distance_from_college' => '400m',
                'rent_per_month' => 10000,
                'sharing_type' => 'Triple Sharing',
                'facilities' => array('WiFi', '2 Meals', 'AC', 'Laundry', 'Warden', 'CCTV'),
                'available_from' => date('Y-m-d'),
                'verified' => true,
                'rating' => 4.6,
                'reviews_count' => 41,
                'images' => array(),
                'contact_person' => 'Mrs. Singh',
                'contact_number' => '+91 98765 43214',
                'featured' => true
            )
        );

        // Apply filters
        if (!empty($filters['type'])) {
            $accommodations = array_filter($accommodations, function($a) use ($filters) {
                return strtolower($a['type']) === strtolower($filters['type']);
            });
        }

        if (!empty($filters['gender'])) {
            $accommodations = array_filter($accommodations, function($a) use ($filters) {
                return $a['gender'] === $filters['gender'] || $a['gender'] === 'Co-ed';
            });
        }

        if (!empty($filters['max_rent'])) {
            $accommodations = array_filter($accommodations, function($a) use ($filters) {
                return $a['rent_per_month'] <= $filters['max_rent'];
            });
        }

        if (!empty($filters['location'])) {
            $accommodations = array_filter($accommodations, function($a) use ($filters) {
                return stripos($a['location'], $filters['location']) !== false;
            });
        }

        // Sort by featured first, then rating
        usort($accommodations, function($a, $b) {
            if ($a['featured'] && !$b['featured']) return -1;
            if (!$a['featured'] && $b['featured']) return 1;
            return $b['rating'] <=> $a['rating'];
        });

        return $this->success(array_values($accommodations));
    }

    /**
     * Get accommodation details
     */
    public function get_accommodation($accommodation_id) {
        $all_accommodations = $this->search_accommodations()['data'];

        $accommodation = array_values(array_filter($all_accommodations, function($a) use ($accommodation_id) {
            return $a['id'] == $accommodation_id;
        }))[0] ?? null;

        if (!$accommodation) {
            return $this->error('Accommodation not found');
        }

        return $this->success($accommodation);
    }

    /**
     * Request visit/tour
     */
    public function request_visit($user_id, $accommodation_id, $visit_data) {
        global $wpdb;

        $required = array('preferred_date', 'preferred_time');
        if (!$this->validate_required($visit_data, $required)) {
            return $this->error('Missing required fields');
        }

        $table_name = $wpdb->prefix . 'ss_accommodation_visits';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'accommodation_id' => $accommodation_id,
                'preferred_date' => sanitize_text_field($visit_data['preferred_date']),
                'preferred_time' => sanitize_text_field($visit_data['preferred_time']),
                'notes' => isset($visit_data['notes']) ? sanitize_textarea_field($visit_data['notes']) : '',
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to submit visit request');
        }

        return $this->success(array(
            'visit_id' => $wpdb->insert_id,
            'message' => 'Visit request submitted successfully! The owner will contact you shortly.'
        ));
    }

    /**
     * Submit review
     */
    public function submit_review($user_id, $accommodation_id, $review_data) {
        global $wpdb;

        $required = array('rating', 'review_text');
        if (!$this->validate_required($review_data, $required)) {
            return $this->error('Missing required fields');
        }

        $table_name = $wpdb->prefix . 'ss_accommodation_reviews';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'accommodation_id' => $accommodation_id,
                'rating' => floatval($review_data['rating']),
                'review_text' => sanitize_textarea_field($review_data['review_text']),
                'pros' => isset($review_data['pros']) ? sanitize_textarea_field($review_data['pros']) : '',
                'cons' => isset($review_data['cons']) ? sanitize_textarea_field($review_data['cons']) : '',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%f', '%s', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to submit review');
        }

        return $this->success(array(
            'review_id' => $wpdb->insert_id,
            'message' => 'Review submitted successfully'
        ));
    }

    /**
     * Get reviews for accommodation
     */
    public function get_reviews($accommodation_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_accommodation_reviews';

        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE accommodation_id = %d ORDER BY created_at DESC",
            $accommodation_id
        ));

        return $this->success($reviews);
    }

    /**
     * Save accommodation to wishlist
     */
    public function save_to_wishlist($user_id, $accommodation_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_accommodation_wishlist';

        // Check if already saved
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d AND accommodation_id = %d",
            $user_id,
            $accommodation_id
        ));

        if ($existing) {
            return $this->error('Already in wishlist');
        }

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'accommodation_id' => $accommodation_id,
                'saved_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to save');
        }

        return $this->success(array('message' => 'Added to wishlist'));
    }

    /**
     * Get user's wishlist
     */
    public function get_wishlist($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_accommodation_wishlist';

        $wishlist = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY saved_at DESC",
            $user_id
        ));

        return $this->success($wishlist);
    }

    /**
     * Get accommodation filters
     */
    public function get_filters() {
        $filters = array(
            'types' => array('Hostel', 'PG', 'Flat'),
            'gender' => array('Boys', 'Girls', 'Co-ed'),
            'sharing_types' => array('Single', 'Double Sharing', 'Triple Sharing', 'Four Sharing', '2 BHK', '3 BHK'),
            'rent_ranges' => array(
                array('label' => 'Under ₹5,000', 'max' => 5000),
                array('label' => '₹5,000 - ₹10,000', 'min' => 5000, 'max' => 10000),
                array('label' => '₹10,000 - ₹15,000', 'min' => 10000, 'max' => 15000),
                array('label' => 'Above ₹15,000', 'min' => 15000)
            ),
            'facilities' => array('WiFi', 'Mess', 'Laundry', 'AC', 'Gym', 'Study Room', 'Security', 'CCTV', 'Kitchen', 'Parking')
        );

        return $this->success($filters);
    }
}
