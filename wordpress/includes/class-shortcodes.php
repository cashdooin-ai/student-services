<?php
/**
 * Shortcodes for Front-end Display
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Shortcodes {

    public static function init() {
        add_shortcode('student_courses', array(__CLASS__, 'courses_shortcode'));
        add_shortcode('student_grades', array(__CLASS__, 'grades_shortcode'));
        add_shortcode('student_billing', array(__CLASS__, 'billing_shortcode'));
        add_shortcode('student_events', array(__CLASS__, 'events_shortcode'));
        add_shortcode('student_health_appointments', array(__CLASS__, 'health_appointments_shortcode'));
        add_shortcode('student_it_tickets', array(__CLASS__, 'it_tickets_shortcode'));
        add_shortcode('course_search', array(__CLASS__, 'course_search_shortcode'));
        add_shortcode('event_calendar', array(__CLASS__, 'event_calendar_shortcode'));
    }

    /**
     * Display student's enrolled courses
     * Usage: [student_courses]
     */
    public static function courses_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your courses.', 'student-services') . '</p>';
        }

        $service = new Student_Services_Course_Registration();
        $user_id = get_current_user_id();
        $result = $service->get_enrolled_courses($user_id);

        if (is_wp_error($result) || !$result['success']) {
            return '<p>' . __('Error loading courses.', 'student-services') . '</p>';
        }

        $courses = $result['data'];

        if (empty($courses)) {
            return '<p>' . __('You are not enrolled in any courses.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-courses">
            <h3><?php _e('My Courses', 'student-services'); ?></h3>
            <table class="ss-table">
                <thead>
                    <tr>
                        <th><?php _e('Course Code', 'student-services'); ?></th>
                        <th><?php _e('Title', 'student-services'); ?></th>
                        <th><?php _e('Credits', 'student-services'); ?></th>
                        <th><?php _e('Instructor', 'student-services'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td><?php echo esc_html($course->course_code); ?></td>
                            <td><?php echo esc_html($course->title); ?></td>
                            <td><?php echo esc_html($course->credits); ?></td>
                            <td><?php echo esc_html($course->instructor); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display student's grades and GPA
     * Usage: [student_grades]
     */
    public static function grades_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your grades.', 'student-services') . '</p>';
        }

        $service = new Student_Services_Grade_Management();
        $user_id = get_current_user_id();

        $grades_result = $service->get_grades($user_id);
        $gpa_result = $service->calculate_gpa($user_id, true);

        if (is_wp_error($grades_result) || is_wp_error($gpa_result)) {
            return '<p>' . __('Error loading grades.', 'student-services') . '</p>';
        }

        $grades = $grades_result['data'];
        $gpa = $gpa_result['data'];

        ob_start();
        ?>
        <div class="student-services-grades">
            <h3><?php _e('My Grades', 'student-services'); ?></h3>

            <div class="gpa-summary">
                <strong><?php _e('Cumulative GPA:', 'student-services'); ?></strong>
                <span class="gpa-value"><?php echo esc_html(number_format($gpa['gpa'], 2)); ?></span>
                <span class="credits">(<?php echo esc_html($gpa['credits']); ?> <?php _e('credits', 'student-services'); ?>)</span>
            </div>

            <?php if (!empty($grades)) : ?>
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th><?php _e('Course', 'student-services'); ?></th>
                            <th><?php _e('Grade', 'student-services'); ?></th>
                            <th><?php _e('Credits', 'student-services'); ?></th>
                            <th><?php _e('Semester', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade) : ?>
                            <tr>
                                <td><?php echo esc_html($grade->course_name); ?></td>
                                <td><strong><?php echo esc_html($grade->grade); ?></strong></td>
                                <td><?php echo esc_html($grade->credits); ?></td>
                                <td><?php echo esc_html($grade->semester . ' ' . $grade->year); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No grades available yet.', 'student-services'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display billing account
     * Usage: [student_billing]
     */
    public static function billing_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your billing account.', 'student-services') . '</p>';
        }

        $service = new Student_Services_Billing();
        $user_id = get_current_user_id();
        $result = $service->get_account($user_id);

        if (is_wp_error($result) || !$result['success']) {
            return '<p>' . __('Error loading billing information.', 'student-services') . '</p>';
        }

        $account = $result['data'];

        ob_start();
        ?>
        <div class="student-services-billing">
            <h3><?php _e('Billing Account', 'student-services'); ?></h3>

            <div class="balance-summary">
                <div class="balance-amount">
                    <span class="label"><?php _e('Current Balance:', 'student-services'); ?></span>
                    <span class="amount">$<?php echo number_format($account['balance'], 2); ?></span>
                </div>
                <?php if ($account['due_date']) : ?>
                    <div class="due-date">
                        <span class="label"><?php _e('Due Date:', 'student-services'); ?></span>
                        <span class="date"><?php echo esc_html($account['due_date']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <h4><?php _e('Recent Charges', 'student-services'); ?></h4>
            <?php if (!empty($account['charges'])) : ?>
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th><?php _e('Description', 'student-services'); ?></th>
                            <th><?php _e('Amount', 'student-services'); ?></th>
                            <th><?php _e('Date', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($account['charges'] as $charge) : ?>
                            <tr>
                                <td><?php echo esc_html($charge->description); ?></td>
                                <td>$<?php echo number_format($charge->amount, 2); ?></td>
                                <td><?php echo esc_html($charge->charge_date); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No charges found.', 'student-services'); ?></p>
            <?php endif; ?>

            <h4><?php _e('Recent Payments', 'student-services'); ?></h4>
            <?php if (!empty($account['payments'])) : ?>
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th><?php _e('Amount', 'student-services'); ?></th>
                            <th><?php _e('Method', 'student-services'); ?></th>
                            <th><?php _e('Date', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($account['payments'] as $payment) : ?>
                            <tr>
                                <td>$<?php echo number_format($payment->amount, 2); ?></td>
                                <td><?php echo esc_html($payment->payment_method); ?></td>
                                <td><?php echo esc_html($payment->payment_date); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No payments found.', 'student-services'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display upcoming events
     * Usage: [student_events limit="5"]
     */
    public static function events_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'category' => ''
        ), $atts);

        $service = new Student_Services_Events();
        $result = $service->get_upcoming_events(array('category' => $atts['category']));

        if (is_wp_error($result) || !$result['success']) {
            return '<p>' . __('Error loading events.', 'student-services') . '</p>';
        }

        $events = array_slice($result['data'], 0, intval($atts['limit']));

        if (empty($events)) {
            return '<p>' . __('No upcoming events.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-events">
            <h3><?php _e('Upcoming Events', 'student-services'); ?></h3>
            <div class="events-list">
                <?php foreach ($events as $event) : ?>
                    <div class="event-item">
                        <h4><?php echo esc_html($event->name); ?></h4>
                        <p class="event-meta">
                            <span class="date"><?php echo esc_html($event->event_date); ?></span>
                            <span class="location"><?php echo esc_html($event->location); ?></span>
                            <?php if ($event->category) : ?>
                                <span class="category"><?php echo esc_html($event->category); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if ($event->description) : ?>
                            <p><?php echo esc_html($event->description); ?></p>
                        <?php endif; ?>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="ss-register-event" data-event-id="<?php echo esc_attr($event->id); ?>">
                                <?php _e('Register', 'student-services'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display course search
     * Usage: [course_search]
     */
    public static function course_search_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-course-search">
            <h3><?php _e('Course Search', 'student-services'); ?></h3>
            <form id="ss-course-search-form">
                <input type="text" id="ss-course-keyword" placeholder="<?php esc_attr_e('Search courses...', 'student-services'); ?>" />
                <select id="ss-course-semester">
                    <option value=""><?php _e('All Semesters', 'student-services'); ?></option>
                    <option value="Fall">Fall</option>
                    <option value="Spring">Spring</option>
                    <option value="Summer">Summer</option>
                </select>
                <button type="submit"><?php _e('Search', 'student-services'); ?></button>
            </form>
            <div id="ss-course-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Health appointments shortcode
     * Usage: [student_health_appointments]
     */
    public static function health_appointments_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view appointments.', 'student-services') . '</p>';
        }

        $service = new Student_Services_Health();
        $user_id = get_current_user_id();
        $result = $service->get_appointments($user_id);

        if (is_wp_error($result)) {
            return '<p>' . __('Error loading appointments.', 'student-services') . '</p>';
        }

        $appointments = $result['data'];

        ob_start();
        ?>
        <div class="student-services-health-appointments">
            <h3><?php _e('Health Appointments', 'student-services'); ?></h3>
            <?php if (!empty($appointments)) : ?>
                <ul class="appointments-list">
                    <?php foreach ($appointments as $appt) : ?>
                        <li>
                            <?php echo esc_html($appt->appointment_date . ' at ' . $appt->appointment_time); ?> -
                            <?php echo esc_html($appt->provider); ?>
                            (<?php echo esc_html($appt->status); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php _e('No appointments scheduled.', 'student-services'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * IT tickets shortcode
     * Usage: [student_it_tickets]
     */
    public static function it_tickets_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view tickets.', 'student-services') . '</p>';
        }

        $service = new Student_Services_IT_Support();
        $user_id = get_current_user_id();
        $result = $service->get_tickets($user_id);

        if (is_wp_error($result)) {
            return '<p>' . __('Error loading tickets.', 'student-services') . '</p>';
        }

        $tickets = $result['data'];

        ob_start();
        ?>
        <div class="student-services-it-tickets">
            <h3><?php _e('My IT Support Tickets', 'student-services'); ?></h3>
            <?php if (!empty($tickets)) : ?>
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'student-services'); ?></th>
                            <th><?php _e('Subject', 'student-services'); ?></th>
                            <th><?php _e('Category', 'student-services'); ?></th>
                            <th><?php _e('Status', 'student-services'); ?></th>
                            <th><?php _e('Created', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket) : ?>
                            <tr>
                                <td><?php echo esc_html($ticket->id); ?></td>
                                <td><?php echo esc_html($ticket->subject); ?></td>
                                <td><?php echo esc_html($ticket->category); ?></td>
                                <td><?php echo esc_html($ticket->status); ?></td>
                                <td><?php echo esc_html($ticket->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No support tickets found.', 'student-services'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Event calendar shortcode
     * Usage: [event_calendar]
     */
    public static function event_calendar_shortcode($atts) {
        return '<div id="ss-event-calendar"></div>';
    }
}
