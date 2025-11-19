<?php
/**
 * Academic Calendar Service
 * Track important dates, deadlines, and events
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Academic_Calendar extends Student_Services_Base_Service {

    /**
     * Get calendar events
     */
    public function get_calendar_events($start_date = null, $end_date = null) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_calendar_events';

        if ($start_date && $end_date) {
            $events = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE event_date BETWEEN %s AND %s ORDER BY event_date ASC",
                $start_date,
                $end_date
            ));
        } else {
            // Get events for next 3 months
            $start = current_time('Y-m-d');
            $end = date('Y-m-d', strtotime('+3 months'));

            $events = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE event_date BETWEEN %s AND %s ORDER BY event_date ASC",
                $start,
                $end
            ));
        }

        // Mock data if database is empty
        if (empty($events)) {
            $events = $this->get_mock_events();
        }

        return $this->success($events);
    }

    /**
     * Get mock calendar events
     */
    private function get_mock_events() {
        return array(
            array(
                'id' => 1,
                'title' => 'JEE Main 2024 Registration Opens',
                'event_date' => date('Y-m-d', strtotime('+10 days')),
                'event_type' => 'deadline',
                'category' => 'Entrance Exams',
                'description' => 'Last date to register for JEE Main January session',
                'is_important' => 1,
                'reminder_enabled' => 1
            ),
            array(
                'id' => 2,
                'title' => 'Mid-Semester Exams Begin',
                'event_date' => date('Y-m-d', strtotime('+15 days')),
                'event_type' => 'exam',
                'category' => 'Academics',
                'description' => 'Mid-semester examinations for all programs',
                'is_important' => 1,
                'reminder_enabled' => 1
            ),
            array(
                'id' => 3,
                'title' => 'NEET 2024 Application Deadline',
                'event_date' => date('Y-m-d', strtotime('+20 days')),
                'event_type' => 'deadline',
                'category' => 'Entrance Exams',
                'description' => 'Last date to submit NEET application form',
                'is_important' => 1,
                'reminder_enabled' => 1
            ),
            array(
                'id' => 4,
                'title' => 'Tech Fest 2024',
                'event_date' => date('Y-m-d', strtotime('+25 days')),
                'event_type' => 'event',
                'category' => 'Campus Events',
                'description' => 'Annual technical festival with competitions and workshops',
                'is_important' => 0,
                'reminder_enabled' => 0
            ),
            array(
                'id' => 5,
                'title' => 'Scholarship Application Deadline',
                'event_date' => date('Y-m-d', strtotime('+30 days')),
                'event_type' => 'deadline',
                'category' => 'Financial Aid',
                'description' => 'Last date for merit scholarship applications',
                'is_important' => 1,
                'reminder_enabled' => 1
            ),
            array(
                'id' => 6,
                'title' => 'Summer Internship Applications Open',
                'event_date' => date('Y-m-d', strtotime('+35 days')),
                'event_type' => 'opportunity',
                'category' => 'Career',
                'description' => 'Companies start accepting summer internship applications',
                'is_important' => 1,
                'reminder_enabled' => 1
            ),
            array(
                'id' => 7,
                'title' => 'GATE 2024 Exam',
                'event_date' => date('Y-m-d', strtotime('+40 days')),
                'event_type' => 'exam',
                'category' => 'Entrance Exams',
                'description' => 'GATE examination day',
                'is_important' => 1,
                'reminder_enabled' => 1
            )
        );
    }

    /**
     * Add personal event
     */
    public function add_personal_event($user_id, $event_data) {
        global $wpdb;

        $required = array('title', 'event_date');
        if (!$this->validate_required($event_data, $required)) {
            return $this->error('Missing required fields');
        }

        $table_name = $wpdb->prefix . 'ss_personal_calendar';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'title' => sanitize_text_field($event_data['title']),
                'event_date' => sanitize_text_field($event_data['event_date']),
                'event_time' => isset($event_data['event_time']) ? sanitize_text_field($event_data['event_time']) : null,
                'description' => isset($event_data['description']) ? sanitize_textarea_field($event_data['description']) : '',
                'event_type' => isset($event_data['event_type']) ? sanitize_text_field($event_data['event_type']) : 'other',
                'reminder_enabled' => isset($event_data['reminder_enabled']) ? 1 : 0,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to add event');
        }

        return $this->success(array(
            'event_id' => $wpdb->insert_id,
            'message' => 'Event added to your calendar'
        ));
    }

    /**
     * Get user's personal events
     */
    public function get_personal_events($user_id, $start_date = null, $end_date = null) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_personal_calendar';

        if ($start_date && $end_date) {
            $events = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND event_date BETWEEN %s AND %s ORDER BY event_date ASC",
                $user_id,
                $start_date,
                $end_date
            ));
        } else {
            $events = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d ORDER BY event_date ASC",
                $user_id
            ));
        }

        return $this->success($events);
    }

    /**
     * Get upcoming deadlines
     */
    public function get_upcoming_deadlines($days = 7) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_calendar_events';
        $start_date = current_time('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+$days days"));

        $deadlines = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE event_type = 'deadline' AND event_date BETWEEN %s AND %s ORDER BY event_date ASC",
            $start_date,
            $end_date
        ));

        // Mock data if empty
        if (empty($deadlines)) {
            $all_events = $this->get_mock_events();
            $deadlines = array_filter($all_events, function($event) {
                return $event['event_type'] === 'deadline';
            });
        }

        return $this->success(array_values($deadlines));
    }

    /**
     * Set reminder for event
     */
    public function set_reminder($user_id, $event_id, $reminder_days = 1) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_calendar_reminders';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'event_id' => $event_id,
                'reminder_days' => $reminder_days,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to set reminder');
        }

        return $this->success(array('message' => 'Reminder set successfully'));
    }
}
