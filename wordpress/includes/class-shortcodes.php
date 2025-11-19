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

        // Educational Services Shortcodes
        add_shortcode('college_recommendation', array(__CLASS__, 'college_recommendation_shortcode'));
        add_shortcode('eligibility_calculator', array(__CLASS__, 'eligibility_calculator_shortcode'));
        add_shortcode('entrance_exam_prep', array(__CLASS__, 'entrance_exam_prep_shortcode'));
        add_shortcode('language_test_prep', array(__CLASS__, 'language_test_prep_shortcode'));
        add_shortcode('course_discovery', array(__CLASS__, 'course_discovery_shortcode'));
        add_shortcode('interview_prep', array(__CLASS__, 'interview_prep_shortcode'));
        add_shortcode('financial_aid_calculator', array(__CLASS__, 'financial_aid_calculator_shortcode'));
        add_shortcode('emi_calculator', array(__CLASS__, 'emi_calculator_shortcode'));
        add_shortcode('college_cost_comparison', array(__CLASS__, 'college_cost_comparison_shortcode'));
        add_shortcode('gpa_calculator', array(__CLASS__, 'gpa_calculator_shortcode'));
        add_shortcode('admission_counseling', array(__CLASS__, 'admission_counseling_shortcode'));

        // New Student Support Services Shortcodes
        add_shortcode('find_mentor', array(__CLASS__, 'mentorship_shortcode'));
        add_shortcode('webinars_workshops', array(__CLASS__, 'webinars_shortcode'));
        add_shortcode('student_forum', array(__CLASS__, 'forum_shortcode'));
        add_shortcode('faq_search', array(__CLASS__, 'faq_shortcode'));
        add_shortcode('academic_calendar', array(__CLASS__, 'calendar_shortcode'));
        add_shortcode('scholarship_search', array(__CLASS__, 'scholarship_shortcode'));
        add_shortcode('service_request_form', array(__CLASS__, 'service_request_shortcode'));
        add_shortcode('document_templates', array(__CLASS__, 'document_templates_shortcode'));
        add_shortcode('accommodation_finder', array(__CLASS__, 'accommodation_shortcode'));

        // Community Services Shortcodes (v4.0.0)
        add_shortcode('college_blog', array(__CLASS__, 'blog_shortcode'));
        add_shortcode('study_abroad', array(__CLASS__, 'study_abroad_shortcode'));
        add_shortcode('placement_stats', array(__CLASS__, 'placement_stats_shortcode'));
        add_shortcode('alumni_network', array(__CLASS__, 'alumni_network_shortcode'));
        add_shortcode('student_testimonials', array(__CLASS__, 'testimonials_shortcode'));
        add_shortcode('referral_rewards', array(__CLASS__, 'referral_rewards_shortcode'));
        add_shortcode('job_board', array(__CLASS__, 'job_board_shortcode'));
        add_shortcode('student_dashboard', array(__CLASS__, 'student_dashboard_shortcode'));
        add_shortcode('employer_dashboard', array(__CLASS__, 'employer_dashboard_shortcode'));
        add_shortcode('subscription_plans', array(__CLASS__, 'subscription_plans_shortcode'));
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

    /**
     * College Recommendation shortcode
     * Usage: [college_recommendation]
     */
    public static function college_recommendation_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-college-recommendation">
            <h3><?php _e('AI College Recommendation', 'student-services'); ?></h3>
            <p><?php _e('Let our AI analyze your preferences and find the perfect colleges for you.', 'student-services'); ?></p>
            <form id="ss-college-recommendation-form">
                <input type="number" name="academic_score" placeholder="<?php esc_attr_e('Academic Score (%)', 'student-services'); ?>" required />
                <input type="text" name="preferred_location" placeholder="<?php esc_attr_e('Preferred Location', 'student-services'); ?>" required />
                <input type="text" name="field_of_study" placeholder="<?php esc_attr_e('Field of Study', 'student-services'); ?>" />
                <input type="number" name="budget" placeholder="<?php esc_attr_e('Budget (optional)', 'student-services'); ?>" />
                <button type="submit"><?php _e('Find Colleges', 'student-services'); ?></button>
            </form>
            <div id="ss-college-recommendation-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Eligibility Calculator shortcode
     * Usage: [eligibility_calculator]
     */
    public static function eligibility_calculator_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-eligibility-calculator">
            <h3><?php _e('Eligibility Calculator', 'student-services'); ?></h3>
            <p><?php _e('Find out which colleges you\'re eligible for based on your academic scores.', 'student-services'); ?></p>
            <form id="ss-eligibility-calculator-form">
                <input type="number" name="percentage_10th" placeholder="<?php esc_attr_e('10th Percentage', 'student-services'); ?>" required />
                <input type="number" name="percentage_12th" placeholder="<?php esc_attr_e('12th Percentage', 'student-services'); ?>" required />
                <input type="number" name="entrance_exam_score" placeholder="<?php esc_attr_e('Entrance Exam Score (optional)', 'student-services'); ?>" />
                <button type="submit"><?php _e('Calculate Eligibility', 'student-services'); ?></button>
            </form>
            <div id="ss-eligibility-calculator-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Entrance Exam Preparation shortcode
     * Usage: [entrance_exam_prep]
     */
    public static function entrance_exam_prep_shortcode($atts) {
        $service = new Student_Services_Entrance_Exam_Prep();
        $result = $service->get_available_exams();
        $exams = $result['data'];

        ob_start();
        ?>
        <div class="student-services-entrance-exam-prep">
            <h3><?php _e('Entrance Exam Preparation', 'student-services'); ?></h3>
            <p><?php _e('Comprehensive resources, practice tests, and study plans for all major entrance exams.', 'student-services'); ?></p>
            <div class="exams-grid">
                <?php foreach ($exams as $exam) : ?>
                    <div class="exam-card">
                        <h4><?php echo esc_html($exam['name']); ?></h4>
                        <p><?php echo esc_html($exam['full_name']); ?></p>
                        <p class="exam-duration"><?php _e('Duration:', 'student-services'); ?> <?php echo esc_html($exam['duration_minutes']); ?> <?php _e('minutes', 'student-services'); ?></p>
                        <button class="view-study-plan" data-exam-id="<?php echo esc_attr($exam['id']); ?>">
                            <?php _e('View Study Plan', 'student-services'); ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Language Test Preparation shortcode
     * Usage: [language_test_prep]
     */
    public static function language_test_prep_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-language-test-prep">
            <h3><?php _e('Language Proficiency Tests', 'student-services'); ?></h3>
            <p><?php _e('Prepare for IELTS, TOEFL, PTE, and other English proficiency tests.', 'student-services'); ?></p>
            <div class="language-tests-grid">
                <div class="test-card">
                    <h4>IELTS</h4>
                    <p><?php _e('International English Language Testing System', 'student-services'); ?></p>
                    <button class="view-test-info" data-test-type="ielts"><?php _e('View Details', 'student-services'); ?></button>
                </div>
                <div class="test-card">
                    <h4>TOEFL</h4>
                    <p><?php _e('Test of English as a Foreign Language', 'student-services'); ?></p>
                    <button class="view-test-info" data-test-type="toefl"><?php _e('View Details', 'student-services'); ?></button>
                </div>
                <div class="test-card">
                    <h4>PTE</h4>
                    <p><?php _e('Pearson Test of English', 'student-services'); ?></p>
                    <button class="view-test-info" data-test-type="pte"><?php _e('View Details', 'student-services'); ?></button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Course Discovery shortcode
     * Usage: [course_discovery]
     */
    public static function course_discovery_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-course-discovery">
            <h3><?php _e('Discover Your Perfect Course', 'student-services'); ?></h3>
            <p><?php _e('Explore curated courses tailored to your goals and interests.', 'student-services'); ?></p>
            <form id="ss-course-discovery-form">
                <input type="text" name="field_of_interest" placeholder="<?php esc_attr_e('Field of Interest', 'student-services'); ?>" required />
                <select name="course_level">
                    <option value=""><?php _e('Course Level', 'student-services'); ?></option>
                    <option value="undergraduate">Undergraduate</option>
                    <option value="postgraduate">Postgraduate</option>
                    <option value="certification">Certification</option>
                </select>
                <input type="number" name="budget" placeholder="<?php esc_attr_e('Budget (optional)', 'student-services'); ?>" />
                <button type="submit"><?php _e('Discover Courses', 'student-services'); ?></button>
            </form>
            <div id="ss-course-discovery-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Interview Preparation shortcode
     * Usage: [interview_prep]
     */
    public static function interview_prep_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-interview-prep">
            <h3><?php _e('Ace Your Interviews', 'student-services'); ?></h3>
            <p><?php _e('Comprehensive interview preparation with practice questions and mock interviews.', 'student-services'); ?></p>
            <div class="interview-categories">
                <button class="category-btn" data-category="technical"><?php _e('Technical', 'student-services'); ?></button>
                <button class="category-btn" data-category="hr"><?php _e('HR', 'student-services'); ?></button>
                <button class="category-btn" data-category="behavioral"><?php _e('Behavioral', 'student-services'); ?></button>
                <button class="category-btn" data-category="case-study"><?php _e('Case Study', 'student-services'); ?></button>
            </div>
            <div id="ss-interview-questions"></div>
            <?php if (is_user_logged_in()) : ?>
                <div class="mock-interview-section">
                    <h4><?php _e('Book a Mock Interview', 'student-services'); ?></h4>
                    <button id="ss-book-mock-interview"><?php _e('Schedule Mock Interview', 'student-services'); ?></button>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Financial Aid Calculator shortcode
     * Usage: [financial_aid_calculator]
     */
    public static function financial_aid_calculator_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-financial-aid-calculator">
            <h3><?php _e('Financial Aid Calculator', 'student-services'); ?></h3>
            <p><?php _e('Plan your education financing with our comprehensive calculator.', 'student-services'); ?></p>
            <form id="ss-financial-aid-calculator-form">
                <input type="number" name="tuition_fee" placeholder="<?php esc_attr_e('Annual Tuition Fee', 'student-services'); ?>" required />
                <input type="number" name="family_income" placeholder="<?php esc_attr_e('Family Income', 'student-services'); ?>" required />
                <input type="number" name="family_size" placeholder="<?php esc_attr_e('Family Size', 'student-services'); ?>" value="4" />
                <input type="number" name="siblings_in_college" placeholder="<?php esc_attr_e('Siblings in College', 'student-services'); ?>" value="0" />
                <button type="submit"><?php _e('Calculate Financial Aid', 'student-services'); ?></button>
            </form>
            <div id="ss-financial-aid-calculator-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * EMI Calculator shortcode
     * Usage: [emi_calculator]
     */
    public static function emi_calculator_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-emi-calculator">
            <h3><?php _e('Student Loan & EMI Calculator', 'student-services'); ?></h3>
            <p><?php _e('Calculate your education loan EMI and compare loan schemes.', 'student-services'); ?></p>
            <form id="ss-emi-calculator-form">
                <input type="number" name="loan_amount" placeholder="<?php esc_attr_e('Loan Amount', 'student-services'); ?>" required />
                <input type="number" name="interest_rate" placeholder="<?php esc_attr_e('Interest Rate (%)', 'student-services'); ?>" step="0.1" required />
                <input type="number" name="tenure_months" placeholder="<?php esc_attr_e('Tenure (Months)', 'student-services'); ?>" required />
                <button type="submit"><?php _e('Calculate EMI', 'student-services'); ?></button>
            </form>
            <div id="ss-emi-calculator-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * College Cost Comparison shortcode
     * Usage: [college_cost_comparison]
     */
    public static function college_cost_comparison_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-college-cost-comparison">
            <h3><?php _e('College Cost Comparison', 'student-services'); ?></h3>
            <p><?php _e('Compare the total cost of attendance across multiple colleges.', 'student-services'); ?></p>
            <div id="ss-college-cost-comparison-form">
                <div class="college-comparison-inputs">
                    <button id="ss-add-college-comparison"><?php _e('Add College', 'student-services'); ?></button>
                </div>
                <button id="ss-compare-colleges"><?php _e('Compare Colleges', 'student-services'); ?></button>
            </div>
            <div id="ss-college-cost-comparison-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * GPA Calculator shortcode
     * Usage: [gpa_calculator]
     */
    public static function gpa_calculator_shortcode($atts) {
        ob_start();
        ?>
        <div class="student-services-gpa-calculator">
            <h3><?php _e('GPA Calculator & Grade Converter', 'student-services'); ?></h3>
            <p><?php _e('Calculate your GPA and convert between different grading systems.', 'student-services'); ?></p>

            <div class="gpa-calculator-section">
                <h4><?php _e('Calculate GPA', 'student-services'); ?></h4>
                <form id="ss-gpa-calculator-form">
                    <select name="scale">
                        <option value="4.0">4.0 Scale</option>
                        <option value="10.0">10.0 Scale</option>
                    </select>
                    <div id="ss-gpa-courses"></div>
                    <button type="button" id="ss-add-course-gpa"><?php _e('Add Course', 'student-services'); ?></button>
                    <button type="submit"><?php _e('Calculate GPA', 'student-services'); ?></button>
                </form>
                <div id="ss-gpa-calculator-results"></div>
            </div>

            <div class="grade-converter-section">
                <h4><?php _e('Grade Converter', 'student-services'); ?></h4>
                <form id="ss-grade-converter-form">
                    <input type="text" name="grade" placeholder="<?php esc_attr_e('Grade', 'student-services'); ?>" required />
                    <select name="from_system">
                        <option value="percentage">Percentage</option>
                        <option value="gpa_4">GPA (4.0)</option>
                        <option value="gpa_10">GPA (10.0)</option>
                        <option value="letter">Letter Grade</option>
                    </select>
                    <select name="to_system">
                        <option value="percentage">Percentage</option>
                        <option value="gpa_4">GPA (4.0)</option>
                        <option value="gpa_10">GPA (10.0)</option>
                        <option value="letter">Letter Grade</option>
                    </select>
                    <button type="submit"><?php _e('Convert', 'student-services'); ?></button>
                </form>
                <div id="ss-grade-converter-results"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Admission Counseling shortcode
     * Usage: [admission_counseling]
     */
    public static function admission_counseling_shortcode($atts) {
        $service = new Student_Services_Admission_Counseling();
        $counselors_result = $service->get_available_counselors();
        $packages_result = $service->get_counseling_packages();

        $counselors = $counselors_result['data'];
        $packages = $packages_result['data'];

        ob_start();
        ?>
        <div class="student-services-admission-counseling">
            <h3><?php _e('Admission Counseling', 'student-services'); ?></h3>
            <p><?php _e('Connect with experienced counselors for personalized college admission guidance.', 'student-services'); ?></p>

            <div class="counseling-packages">
                <h4><?php _e('Counseling Packages', 'student-services'); ?></h4>
                <div class="packages-grid">
                    <?php foreach ($packages as $package) : ?>
                        <div class="package-card <?php echo isset($package['popular']) && $package['popular'] ? 'popular' : ''; ?>">
                            <h5><?php echo esc_html($package['name']); ?></h5>
                            <p class="price">$<?php echo esc_html($package['price']); ?></p>
                            <p class="sessions"><?php echo esc_html($package['sessions']); ?> <?php _e('Sessions', 'student-services'); ?></p>
                            <ul>
                                <?php foreach ($package['features'] as $feature) : ?>
                                    <li><?php echo esc_html($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="available-counselors">
                <h4><?php _e('Our Counselors', 'student-services'); ?></h4>
                <div class="counselors-grid">
                    <?php foreach ($counselors as $counselor) : ?>
                        <div class="counselor-card">
                            <h5><?php echo esc_html($counselor['name']); ?></h5>
                            <p class="specialization"><?php echo esc_html($counselor['specialization']); ?></p>
                            <p class="experience"><?php echo esc_html($counselor['experience_years']); ?> <?php _e('years experience', 'student-services'); ?></p>
                            <p class="rating">⭐ <?php echo esc_html($counselor['rating']); ?> (<?php echo esc_html($counselor['total_students']); ?> <?php _e('students', 'student-services'); ?>)</p>
                            <p class="fee">$<?php echo esc_html($counselor['fee_per_session']); ?> <?php _e('per session', 'student-services'); ?></p>
                            <p class="availability <?php echo strtolower($counselor['availability']); ?>"><?php echo esc_html($counselor['availability']); ?></p>
                            <?php if (is_user_logged_in() && $counselor['availability'] === 'Available') : ?>
                                <button class="book-counselor" data-counselor-id="<?php echo esc_attr($counselor['id']); ?>">
                                    <?php _e('Book Session', 'student-services'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display mentorship listing
     * Usage: [find_mentor]
     */
    public static function mentorship_shortcode($atts) {
        $service = new Student_Services_Mentorship();
        $result = $service->get_available_mentors();

        if (!$result['success']) {
            return '<p>' . __('Error loading mentors.', 'student-services') . '</p>';
        }

        $mentors = $result['data'];

        ob_start();
        ?>
        <div class="student-services-mentorship">
            <h3><?php _e('Find Your Mentor', 'student-services'); ?></h3>
            <p><?php _e('Connect with experienced professionals and students.', 'student-services'); ?></p>
            <div class="mentors-grid">
                <?php foreach ($mentors as $mentor) : ?>
                    <div class="mentor-card">
                        <h4><?php echo esc_html($mentor['name']); ?></h4>
                        <p class="company"><?php echo esc_html($mentor['current_company']); ?></p>
                        <p class="expertise"><?php echo esc_html($mentor['expertise_area']); ?></p>
                        <p class="rating">⭐ <?php echo esc_html($mentor['rating']); ?></p>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="book-mentor" data-mentor-id="<?php echo esc_attr($mentor['id']); ?>">
                                <?php _e('Book Session', 'student-services'); ?>
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
     * Display upcoming webinars
     * Usage: [webinars_workshops]
     */
    public static function webinars_shortcode($atts) {
        $service = new Student_Services_Webinars();
        $result = $service->get_upcoming_webinars();

        if (!$result['success']) {
            return '<p>' . __('Error loading webinars.', 'student-services') . '</p>';
        }

        $webinars = $result['data'];

        ob_start();
        ?>
        <div class="student-services-webinars">
            <h3><?php _e('Upcoming Webinars & Workshops', 'student-services'); ?></h3>
            <div class="webinars-list">
                <?php foreach ($webinars as $webinar) : ?>
                    <div class="webinar-card">
                        <h4><?php echo esc_html($webinar['title']); ?></h4>
                        <p class="speaker"><?php _e('By:', 'student-services'); ?> <?php echo esc_html($webinar['speaker_name']); ?></p>
                        <p class="date"><?php echo esc_html(date('F j, Y g:i A', strtotime($webinar['scheduled_date']))); ?></p>
                        <p class="category"><?php echo esc_html($webinar['category']); ?></p>
                        <p class="capacity"><?php echo esc_html($webinar['registered_count']); ?>/<?php echo esc_html($webinar['capacity']); ?> <?php _e('registered', 'student-services'); ?></p>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="register-webinar" data-webinar-id="<?php echo esc_attr($webinar['id']); ?>">
                                <?php _e('Register Now', 'student-services'); ?>
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
     * Display student forum
     * Usage: [student_forum]
     */
    public static function forum_shortcode($atts) {
        $service = new Student_Services_Forum();
        $posts_result = $service->get_posts();
        $categories_result = $service->get_categories();

        if (!$posts_result['success'] || !$categories_result['success']) {
            return '<p>' . __('Error loading forum.', 'student-services') . '</p>';
        }

        $posts = $posts_result['data'];
        $categories = $categories_result['data'];

        ob_start();
        ?>
        <div class="student-services-forum">
            <h3><?php _e('Student Forum', 'student-services'); ?></h3>

            <?php if (is_user_logged_in()) : ?>
                <button class="create-post-btn"><?php _e('Create New Post', 'student-services'); ?></button>
            <?php endif; ?>

            <div class="forum-categories">
                <?php foreach ($categories as $category) : ?>
                    <button class="category-filter" data-category="<?php echo esc_attr($category['id']); ?>">
                        <?php echo esc_html($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="forum-posts">
                <?php foreach ($posts as $post) : ?>
                    <div class="forum-post">
                        <h4><?php echo esc_html($post->title); ?></h4>
                        <p><?php echo esc_html(wp_trim_words($post->content, 30)); ?></p>
                        <div class="post-meta">
                            <span><?php echo esc_html($post->views); ?> <?php _e('views', 'student-services'); ?></span>
                            <span><?php echo esc_html($post->likes); ?> <?php _e('likes', 'student-services'); ?></span>
                            <span><?php echo esc_html($post->replies); ?> <?php _e('replies', 'student-services'); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display FAQ search
     * Usage: [faq_search]
     */
    public static function faq_shortcode($atts) {
        $service = new Student_Services_FAQ();
        $categories_result = $service->get_categories();
        $faqs_result = $service->get_faqs();

        if (!$categories_result['success'] || !$faqs_result['success']) {
            return '<p>' . __('Error loading FAQs.', 'student-services') . '</p>';
        }

        $categories = $categories_result['data'];
        $faqs = $faqs_result['data'];

        ob_start();
        ?>
        <div class="student-services-faq">
            <h3><?php _e('Frequently Asked Questions', 'student-services'); ?></h3>

            <div class="faq-search-box">
                <input type="text" id="faq-search" placeholder="<?php _e('Search FAQs...', 'student-services'); ?>">
            </div>

            <div class="faq-categories">
                <?php foreach ($categories as $category) : ?>
                    <button class="faq-category" data-category="<?php echo esc_attr($category['id']); ?>">
                        <?php echo esc_html($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="faq-list">
                <?php foreach ($faqs as $faq) : ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <h4><?php echo esc_html($faq->question); ?></h4>
                        </div>
                        <div class="faq-answer">
                            <p><?php echo esc_html($faq->answer); ?></p>
                            <button class="helpful-btn" data-faq-id="<?php echo esc_attr($faq->id); ?>">
                                <?php _e('Helpful', 'student-services'); ?> (<?php echo esc_html($faq->helpful_count); ?>)
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display academic calendar
     * Usage: [academic_calendar]
     */
    public static function calendar_shortcode($atts) {
        $service = new Student_Services_Academic_Calendar();
        $result = $service->get_calendar_events();

        if (!$result['success']) {
            return '<p>' . __('Error loading calendar.', 'student-services') . '</p>';
        }

        $events = $result['data'];

        ob_start();
        ?>
        <div class="student-services-calendar">
            <h3><?php _e('Academic Calendar', 'student-services'); ?></h3>
            <div class="calendar-events">
                <?php foreach ($events as $event) : ?>
                    <div class="calendar-event <?php echo esc_attr($event->importance); ?>">
                        <h4><?php echo esc_html($event->title); ?></h4>
                        <p class="event-date"><?php echo esc_html(date('F j, Y', strtotime($event->event_date))); ?></p>
                        <p><?php echo esc_html($event->description); ?></p>
                        <span class="event-type"><?php echo esc_html($event->event_type); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display scholarship search
     * Usage: [scholarship_search]
     */
    public static function scholarship_shortcode($atts) {
        $service = new Student_Services_Enhanced_Scholarship();
        $result = $service->search_scholarships();

        if (!$result['success']) {
            return '<p>' . __('Error loading scholarships.', 'student-services') . '</p>';
        }

        $scholarships = $result['data'];

        ob_start();
        ?>
        <div class="student-services-scholarships">
            <h3><?php _e('Find Your Perfect Scholarship', 'student-services'); ?></h3>
            <div class="scholarships-grid">
                <?php foreach ($scholarships as $scholarship) : ?>
                    <div class="scholarship-card">
                        <h4><?php echo esc_html($scholarship['name']); ?></h4>
                        <p class="provider"><?php echo esc_html($scholarship['provider']); ?></p>
                        <p class="amount">₹<?php echo esc_html(number_format($scholarship['amount'])); ?></p>
                        <p class="type"><?php echo esc_html($scholarship['type']); ?></p>
                        <p class="deadline"><?php _e('Deadline:', 'student-services'); ?> <?php echo esc_html(date('F j, Y', strtotime($scholarship['deadline']))); ?></p>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="apply-scholarship" data-scholarship-id="<?php echo esc_attr($scholarship['id']); ?>">
                                <?php _e('Apply Now', 'student-services'); ?>
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
     * Display service request form
     * Usage: [service_request_form]
     */
    public static function service_request_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to submit a service request.', 'student-services') . '</p>';
        }

        $service = new Student_Services_Service_Request();
        $types_result = $service->get_service_types();

        if (!$types_result['success']) {
            return '<p>' . __('Error loading service types.', 'student-services') . '</p>';
        }

        $service_types = $types_result['data'];

        ob_start();
        ?>
        <div class="student-services-request-form">
            <h3><?php _e('Service Request Form', 'student-services'); ?></h3>
            <p><?php _e('All services require a ₹250 processing fee', 'student-services'); ?></p>

            <form id="service-request-form">
                <div class="form-group">
                    <label for="service_type"><?php _e('Service Type', 'student-services'); ?></label>
                    <select name="service_type_id" id="service_type" required>
                        <option value=""><?php _e('Select service...', 'student-services'); ?></option>
                        <?php foreach ($service_types as $type) : ?>
                            <option value="<?php echo esc_attr($type['id']); ?>" data-fee="<?php echo esc_attr($type['fee']); ?>">
                                <?php echo esc_html($type['name']); ?> - ₹<?php echo esc_html($type['fee']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description"><?php _e('Description', 'student-services'); ?></label>
                    <textarea name="description" id="description" rows="5" required></textarea>
                </div>

                <button type="submit" class="submit-btn"><?php _e('Submit Request & Pay', 'student-services'); ?></button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display document templates
     * Usage: [document_templates]
     */
    public static function document_templates_shortcode($atts) {
        $service = new Student_Services_Document_Templates();
        $result = $service->get_templates();

        if (!$result['success']) {
            return '<p>' . __('Error loading templates.', 'student-services') . '</p>';
        }

        $templates = $result['data'];

        ob_start();
        ?>
        <div class="student-services-templates">
            <h3><?php _e('Document Templates', 'student-services'); ?></h3>
            <p><?php _e('Create professional documents for your college applications', 'student-services'); ?></p>

            <div class="templates-grid">
                <?php foreach ($templates as $template) : ?>
                    <div class="template-card">
                        <h4><?php echo esc_html($template['name']); ?></h4>
                        <p class="category"><?php echo esc_html($template['category']); ?></p>
                        <p><?php echo esc_html($template['description']); ?></p>
                        <p class="downloads"><?php echo esc_html($template['downloads']); ?> <?php _e('downloads', 'student-services'); ?></p>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="use-template" data-template-id="<?php echo esc_attr($template['id']); ?>">
                                <?php _e('Use Template', 'student-services'); ?>
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
     * Display accommodation finder
     * Usage: [accommodation_finder]
     */
    public static function accommodation_shortcode($atts) {
        $service = new Student_Services_Accommodation();
        $result = $service->search_accommodations();

        if (!$result['success']) {
            return '<p>' . __('Error loading accommodations.', 'student-services') . '</p>';
        }

        $accommodations = $result['data'];

        ob_start();
        ?>
        <div class="student-services-accommodation">
            <h3><?php _e('Find Your Perfect Accommodation', 'student-services'); ?></h3>
            <p><?php _e('Verified hostels, PGs, and flats near your college', 'student-services'); ?></p>

            <div class="accommodations-grid">
                <?php foreach ($accommodations as $accommodation) : ?>
                    <div class="accommodation-card <?php echo $accommodation['verified'] ? 'verified' : ''; ?>">
                        <?php if ($accommodation['featured']) : ?>
                            <span class="featured-badge"><?php _e('Featured', 'student-services'); ?></span>
                        <?php endif; ?>
                        <h4><?php echo esc_html($accommodation['name']); ?></h4>
                        <p class="type"><?php echo esc_html($accommodation['type']); ?> - <?php echo esc_html($accommodation['gender']); ?></p>
                        <p class="location"><?php echo esc_html($accommodation['location']); ?></p>
                        <p class="distance"><?php echo esc_html($accommodation['distance_from_college']); ?> <?php _e('from college', 'student-services'); ?></p>
                        <p class="rent">₹<?php echo esc_html(number_format($accommodation['rent_per_month'])); ?>/<?php _e('month', 'student-services'); ?></p>
                        <p class="sharing"><?php echo esc_html($accommodation['sharing_type']); ?></p>
                        <p class="rating">⭐ <?php echo esc_html($accommodation['rating']); ?> (<?php echo esc_html($accommodation['reviews_count']); ?> <?php _e('reviews', 'student-services'); ?>)</p>
                        <div class="facilities">
                            <?php foreach ($accommodation['facilities'] as $facility) : ?>
                                <span class="facility-tag"><?php echo esc_html($facility); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="request-visit" data-accommodation-id="<?php echo esc_attr($accommodation['id']); ?>">
                                <?php _e('Request Visit', 'student-services'); ?>
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
     * Display CollegeKampus Blog
     * Usage: [college_blog]
     */
    public static function blog_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'limit' => 10
        ), $atts);

        $service = new Student_Services_Blog();
        $filters = array('limit' => intval($atts['limit']));
        if (!empty($atts['category'])) {
            $filters['category'] = sanitize_text_field($atts['category']);
        }

        $result = $service->get_posts($filters);

        if (!$result['success']) {
            return '<p>' . __('Error loading blog posts.', 'student-services') . '</p>';
        }

        $posts = $result['data'];
        $categories = $service->get_categories();

        ob_start();
        ?>
        <div class="student-services-blog">
            <h3><?php _e('CollegeKampus Blog', 'student-services'); ?></h3>
            <p><?php _e('Admission tips, college news, and career guidance', 'student-services'); ?></p>

            <div class="blog-categories">
                <?php foreach ($categories as $cat) : ?>
                    <a href="#" class="category-filter" data-category="<?php echo esc_attr($cat); ?>">
                        <?php echo esc_html($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="blog-posts">
                <?php foreach ($posts as $post) : ?>
                    <article class="blog-post">
                        <h4><?php echo esc_html($post->title); ?></h4>
                        <p class="meta">
                            <span class="category"><?php echo esc_html($post->category); ?></span> •
                            <span class="author"><?php echo esc_html($post->author_name); ?></span> •
                            <span class="date"><?php echo esc_html(date('M d, Y', strtotime($post->published_at))); ?></span>
                        </p>
                        <p><?php echo esc_html(substr($post->content, 0, 200)) . '...'; ?></p>
                        <p class="stats">
                            👁️ <?php echo esc_html(number_format($post->views)); ?> •
                            ❤️ <?php echo esc_html(number_format($post->likes_count)); ?> •
                            💬 <?php echo esc_html(number_format($post->comments_count)); ?>
                        </p>
                        <a href="?post_id=<?php echo esc_attr($post->id); ?>" class="read-more"><?php _e('Read More', 'student-services'); ?></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Study Abroad Programs
     * Usage: [study_abroad]
     */
    public static function study_abroad_shortcode($atts) {
        $service = new Student_Services_Study_Abroad();
        $countries = $service->get_countries();
        $universities = $service->search_universities(array('limit' => 10));

        if (!$universities['success']) {
            return '<p>' . __('Error loading study abroad programs.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-study-abroad">
            <h3><?php _e('Study Abroad Programs', 'student-services'); ?></h3>
            <p><?php _e('Explore world-class universities and programs across the globe', 'student-services'); ?></p>

            <div class="countries-grid">
                <?php foreach ($countries as $country) : ?>
                    <div class="country-card" data-country="<?php echo esc_attr($country['code']); ?>">
                        <span class="flag"><?php echo esc_html($country['flag']); ?></span>
                        <h4><?php echo esc_html($country['name']); ?></h4>
                        <p><?php echo esc_html($country['universities_count']); ?> <?php _e('Universities', 'student-services'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="universities-list">
                <h4><?php _e('Featured Universities', 'student-services'); ?></h4>
                <?php foreach ($universities['data'] as $uni) : ?>
                    <div class="university-card">
                        <h5><?php echo esc_html($uni->name); ?></h5>
                        <p><?php echo esc_html($uni->city); ?>, <?php echo esc_html($uni->country); ?></p>
                        <p>🏆 QS Rank: <?php echo esc_html($uni->qs_ranking); ?></p>
                        <p>💰 Tuition: <?php echo esc_html($uni->tuition_fees_range); ?></p>
                        <p>📚 <?php echo esc_html($uni->programs_count); ?> <?php _e('programs available', 'student-services'); ?></p>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="view-programs" data-university-id="<?php echo esc_attr($uni->id); ?>">
                                <?php _e('View Programs', 'student-services'); ?>
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
     * Display Placement Statistics
     * Usage: [placement_stats]
     */
    public static function placement_stats_shortcode($atts) {
        $service = new Student_Services_Placement_Stats();
        $placements = $service->get_college_placements(array('limit' => 10));

        if (!$placements['success']) {
            return '<p>' . __('Error loading placement statistics.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-placement-stats">
            <h3><?php _e('College Placement Statistics', 'student-services'); ?></h3>
            <p><?php _e('Compare placement records, salary packages, and top recruiters', 'student-services'); ?></p>

            <div class="placements-table">
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th><?php _e('College', 'student-services'); ?></th>
                            <th><?php _e('Year', 'student-services'); ?></th>
                            <th><?php _e('Students Placed', 'student-services'); ?></th>
                            <th><?php _e('Placement %', 'student-services'); ?></th>
                            <th><?php _e('Avg Package', 'student-services'); ?></th>
                            <th><?php _e('Highest Package', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($placements['data'] as $stat) : ?>
                            <tr>
                                <td><?php echo esc_html($stat->college_name); ?></td>
                                <td><?php echo esc_html($stat->year); ?></td>
                                <td><?php echo esc_html(number_format($stat->students_placed)); ?></td>
                                <td><?php echo esc_html(number_format($stat->placement_percentage, 1)); ?>%</td>
                                <td>₹<?php echo esc_html(number_format($stat->average_package / 100000, 2)); ?> LPA</td>
                                <td>₹<?php echo esc_html(number_format($stat->highest_package / 100000, 2)); ?> LPA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Alumni Network
     * Usage: [alumni_network]
     */
    public static function alumni_network_shortcode($atts) {
        $service = new Student_Services_Alumni_Network();
        $alumni = $service->search_alumni(array('limit' => 12));

        if (!$alumni['success']) {
            return '<p>' . __('Error loading alumni network.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-alumni-network">
            <h3><?php _e('Alumni Network', 'student-services'); ?></h3>
            <p><?php _e('Connect with successful alumni for career guidance and mentorship', 'student-services'); ?></p>

            <div class="alumni-grid">
                <?php foreach ($alumni['data'] as $person) : ?>
                    <div class="alumni-card">
                        <div class="alumni-header">
                            <h4><?php echo esc_html($person->full_name); ?></h4>
                            <p class="batch"><?php _e('Batch', 'student-services'); ?> <?php echo esc_html($person->graduation_year); ?></p>
                        </div>
                        <p class="current-position"><?php echo esc_html($person->current_position); ?></p>
                        <p class="company"><?php echo esc_html($person->current_company); ?></p>
                        <p class="location">📍 <?php echo esc_html($person->location); ?></p>
                        <p class="industry"><?php echo esc_html($person->industry); ?></p>
                        <?php if ($person->open_to_mentorship) : ?>
                            <span class="mentor-badge">✅ <?php _e('Open to Mentorship', 'student-services'); ?></span>
                        <?php endif; ?>
                        <?php if (is_user_logged_in()) : ?>
                            <button class="connect-alumni" data-alumni-id="<?php echo esc_attr($person->user_id); ?>">
                                <?php _e('Connect', 'student-services'); ?>
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
     * Display Student Testimonials
     * Usage: [student_testimonials]
     */
    public static function testimonials_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'limit' => 10
        ), $atts);

        $service = new Student_Services_Testimonials();
        $filters = array('limit' => intval($atts['limit']), 'status' => 'approved');
        if (!empty($atts['category'])) {
            $filters['category'] = sanitize_text_field($atts['category']);
        }

        $testimonials = $service->get_testimonials($filters);
        $categories = $service->get_categories();

        if (!$testimonials['success']) {
            return '<p>' . __('Error loading testimonials.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-testimonials">
            <h3><?php _e('Student Success Stories', 'student-services'); ?></h3>
            <p><?php _e('Real stories from students who achieved their dreams', 'student-services'); ?></p>

            <div class="testimonial-categories">
                <?php foreach ($categories as $cat) : ?>
                    <a href="#" class="category-filter" data-category="<?php echo esc_attr($cat); ?>">
                        <?php echo esc_html($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="testimonials-grid">
                <?php foreach ($testimonials['data'] as $testimonial) : ?>
                    <div class="testimonial-card">
                        <div class="testimonial-header">
                            <h4><?php echo esc_html($testimonial->student_name); ?></h4>
                            <p class="category"><?php echo esc_html($testimonial->category); ?></p>
                        </div>
                        <div class="rating">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                                <?php echo $i < $testimonial->rating ? '⭐' : '☆'; ?>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text"><?php echo esc_html($testimonial->testimonial_text); ?></p>
                        <?php if (!empty($testimonial->achievement)) : ?>
                            <p class="achievement">🏆 <?php echo esc_html($testimonial->achievement); ?></p>
                        <?php endif; ?>
                        <p class="meta">
                            ❤️ <?php echo esc_html(number_format($testimonial->likes_count)); ?> •
                            <span class="date"><?php echo esc_html(date('M Y', strtotime($testimonial->submitted_at))); ?></span>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Referral & Rewards
     * Usage: [referral_rewards]
     */
    public static function referral_rewards_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your referral rewards.', 'student-services') . '</p>';
        }

        $user_id = get_current_user_id();
        $service = new Student_Services_Referral_Rewards();

        $referral_code = $service->get_referral_code($user_id);
        $points_balance = $service->get_points_balance($user_id);
        $rewards_catalog = $service->get_rewards_catalog();

        ob_start();
        ?>
        <div class="student-services-referral-rewards">
            <h3><?php _e('Referral & Rewards Program', 'student-services'); ?></h3>
            <p><?php _e('Share your referral code and earn reward points', 'student-services'); ?></p>

            <div class="referral-overview">
                <div class="referral-code-section">
                    <h4><?php _e('Your Referral Code', 'student-services'); ?></h4>
                    <div class="code-display">
                        <code><?php echo esc_html($referral_code); ?></code>
                        <button class="copy-code" data-code="<?php echo esc_attr($referral_code); ?>">
                            <?php _e('Copy', 'student-services'); ?>
                        </button>
                    </div>
                    <p><?php _e('Share this code with friends. When they sign up, you both earn points!', 'student-services'); ?></p>
                </div>

                <div class="points-balance">
                    <h4><?php _e('Your Points Balance', 'student-services'); ?></h4>
                    <p class="points-display"><?php echo esc_html(number_format($points_balance)); ?> <?php _e('Points', 'student-services'); ?></p>
                    <p class="points-value"><?php _e('Value:', 'student-services'); ?> ₹<?php echo esc_html(number_format($points_balance * 0.10, 2)); ?></p>
                    <p class="conversion-rate"><?php _e('1 Point = ₹0.10', 'student-services'); ?></p>
                </div>
            </div>

            <div class="rewards-catalog">
                <h4><?php _e('Redeem Your Points', 'student-services'); ?></h4>
                <div class="rewards-grid">
                    <?php foreach ($rewards_catalog as $reward) : ?>
                        <div class="reward-card <?php echo $points_balance >= $reward['points_required'] ? 'available' : 'locked'; ?>">
                            <h5><?php echo esc_html($reward['reward_name']); ?></h5>
                            <p><?php echo esc_html($reward['description']); ?></p>
                            <p class="points-required"><?php echo esc_html(number_format($reward['points_required'])); ?> <?php _e('Points', 'student-services'); ?></p>
                            <?php if ($points_balance >= $reward['points_required']) : ?>
                                <button class="redeem-reward" data-reward-id="<?php echo esc_attr($reward['id']); ?>" data-points="<?php echo esc_attr($reward['points_required']); ?>">
                                    <?php _e('Redeem', 'student-services'); ?>
                                </button>
                            <?php else : ?>
                                <button class="redeem-reward" disabled>
                                    <?php _e('Not Enough Points', 'student-services'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Job Board
     * Usage: [job_board]
     */
    public static function job_board_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => '',
            'limit' => 20
        ), $atts);

        $service = new Student_Services_Job_Board();
        $filters = array('limit' => intval($atts['limit']), 'status' => 'active');
        if (!empty($atts['type'])) {
            $filters['job_type'] = sanitize_text_field($atts['type']);
        }

        $jobs = $service->search_jobs($filters);
        $categories = $service->get_categories();
        $job_types = $service->get_job_types();

        if (!$jobs['success']) {
            return '<p>' . __('Error loading jobs.', 'student-services') . '</p>';
        }

        ob_start();
        ?>
        <div class="student-services-job-board">
            <h3><?php _e('Job Board', 'student-services'); ?></h3>
            <p><?php _e('Part-time jobs, internships, and freelance opportunities for students', 'student-services'); ?></p>

            <div class="job-filters">
                <div class="filter-group">
                    <label><?php _e('Job Type:', 'student-services'); ?></label>
                    <select class="job-type-filter">
                        <option value=""><?php _e('All Types', 'student-services'); ?></option>
                        <?php foreach ($job_types as $type) : ?>
                            <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html(ucfirst($type)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><?php _e('Category:', 'student-services'); ?></label>
                    <select class="job-category-filter">
                        <option value=""><?php _e('All Categories', 'student-services'); ?></option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="jobs-list">
                <?php foreach ($jobs['data'] as $job) : ?>
                    <div class="job-card <?php echo $job->featured ? 'featured' : ''; ?>">
                        <?php if ($job->featured) : ?>
                            <span class="featured-badge">⭐ <?php _e('Featured', 'student-services'); ?></span>
                        <?php endif; ?>
                        <div class="job-header">
                            <h4><?php echo esc_html($job->title); ?></h4>
                            <p class="company"><?php echo esc_html($job->company_name); ?></p>
                        </div>
                        <div class="job-details">
                            <span class="job-type"><?php echo esc_html(ucfirst($job->job_type)); ?></span>
                            <span class="location">📍 <?php echo esc_html($job->location); ?></span>
                            <span class="salary">💰 ₹<?php echo esc_html(number_format($job->salary_min)); ?> - ₹<?php echo esc_html(number_format($job->salary_max)); ?></span>
                        </div>
                        <p class="description"><?php echo esc_html(substr($job->description, 0, 150)) . '...'; ?></p>
                        <p class="meta">
                            <span class="category"><?php echo esc_html($job->category); ?></span> •
                            <span class="posted"><?php echo esc_html(human_time_diff(strtotime($job->posted_at), current_time('timestamp'))); ?> <?php _e('ago', 'student-services'); ?></span>
                        </p>
                        <?php if (is_user_logged_in()) : ?>
                            <div class="job-actions">
                                <button class="apply-job" data-job-id="<?php echo esc_attr($job->id); ?>">
                                    <?php _e('Apply Now', 'student-services'); ?>
                                </button>
                                <button class="save-job" data-job-id="<?php echo esc_attr($job->id); ?>">
                                    <?php _e('Save', 'student-services'); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Student Dashboard
     * Usage: [student_dashboard]
     */
    public static function student_dashboard_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your dashboard.', 'student-services') . '</p>';
        }

        $user_id = get_current_user_id();
        $service = new Student_Services_Student_Dashboard();
        $overview = $service->get_dashboard_overview($user_id);

        if (!$overview['success']) {
            return '<p>' . __('Error loading dashboard.', 'student-services') . '</p>';
        }

        $data = $overview['data'];

        ob_start();
        ?>
        <div class="student-services-dashboard">
            <h3><?php _e('Student Dashboard', 'student-services'); ?></h3>
            <p><?php _e('Welcome back,', 'student-services'); ?> <?php echo esc_html(wp_get_current_user()->display_name); ?>!</p>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h4><?php _e('Profile Completion', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['profile_completion']); ?>%</p>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo esc_attr($data['profile_completion']); ?>%"></div>
                    </div>
                </div>

                <div class="stat-card">
                    <h4><?php _e('Job Applications', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['applications']['jobs']); ?></p>
                </div>

                <div class="stat-card">
                    <h4><?php _e('Scholarship Applications', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['applications']['scholarships']); ?></p>
                </div>

                <div class="stat-card">
                    <h4><?php _e('University Applications', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['applications']['universities']); ?></p>
                </div>
            </div>

            <div class="dashboard-sections">
                <div class="recent-activities">
                    <h4><?php _e('Recent Activities', 'student-services'); ?></h4>
                    <ul class="activity-list">
                        <?php foreach ($data['recent_activities'] as $activity) : ?>
                            <li>
                                <span class="activity-icon">📌</span>
                                <span class="activity-description"><?php echo esc_html($activity['description']); ?></span>
                                <span class="activity-time"><?php echo esc_html(human_time_diff(strtotime($activity['created_at']), current_time('timestamp'))); ?> <?php _e('ago', 'student-services'); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="upcoming-events">
                    <h4><?php _e('Upcoming Events', 'student-services'); ?></h4>
                    <ul class="event-list">
                        <?php foreach ($data['upcoming_events'] as $event) : ?>
                            <li>
                                <span class="event-date"><?php echo esc_html(date('M d', strtotime($event['event_date']))); ?></span>
                                <span class="event-title"><?php echo esc_html($event['title']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="recommendations">
                    <h4><?php _e('Recommended for You', 'student-services'); ?></h4>
                    <ul class="recommendation-list">
                        <?php foreach ($data['recommendations'] as $rec) : ?>
                            <li>
                                <span class="rec-type"><?php echo esc_html($rec['type']); ?></span>
                                <span class="rec-title"><?php echo esc_html($rec['title']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Employer Dashboard
     * Usage: [employer_dashboard]
     */
    public static function employer_dashboard_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your employer dashboard.', 'student-services') . '</p>';
        }

        $user_id = get_current_user_id();
        $service = new Student_Services_Employer_Dashboard();

        // Check if employer exists
        global $wpdb;
        $employer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ss_employers WHERE user_id = %d",
            $user_id
        ));

        if (!$employer) {
            return '<p>' . __('Please register as an employer to access this dashboard.', 'student-services') . ' <a href="#">' . __('Register Now', 'student-services') . '</a></p>';
        }

        $overview = $service->get_employer_overview($employer->id);

        if (!$overview['success']) {
            return '<p>' . __('Error loading employer dashboard.', 'student-services') . '</p>';
        }

        $data = $overview['data'];

        ob_start();
        ?>
        <div class="student-services-employer-dashboard">
            <h3><?php _e('Employer Dashboard', 'student-services'); ?></h3>
            <p><?php _e('Welcome,', 'student-services'); ?> <?php echo esc_html($employer->company_name); ?>!</p>

            <div class="employer-stats">
                <div class="stat-card">
                    <h4><?php _e('Active Jobs', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['stats']['active_jobs']); ?></p>
                </div>

                <div class="stat-card">
                    <h4><?php _e('Total Applications', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['stats']['total_applications']); ?></p>
                </div>

                <div class="stat-card">
                    <h4><?php _e('Pending Review', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html($data['stats']['pending_applications']); ?></p>
                </div>

                <div class="stat-card">
                    <h4><?php _e('Profile Views', 'student-services'); ?></h4>
                    <p class="stat-value"><?php echo esc_html(number_format($data['stats']['profile_views'])); ?></p>
                </div>
            </div>

            <div class="employer-actions">
                <button class="btn-primary post-new-job"><?php _e('Post New Job', 'student-services'); ?></button>
                <button class="btn-secondary view-applications"><?php _e('View All Applications', 'student-services'); ?></button>
            </div>

            <div class="recent-jobs">
                <h4><?php _e('Your Recent Job Posts', 'student-services'); ?></h4>
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th><?php _e('Job Title', 'student-services'); ?></th>
                            <th><?php _e('Posted', 'student-services'); ?></th>
                            <th><?php _e('Applications', 'student-services'); ?></th>
                            <th><?php _e('Status', 'student-services'); ?></th>
                            <th><?php _e('Actions', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['recent_jobs'] as $job) : ?>
                            <tr>
                                <td><?php echo esc_html($job['title']); ?></td>
                                <td><?php echo esc_html(human_time_diff(strtotime($job['posted_at']), current_time('timestamp'))); ?> <?php _e('ago', 'student-services'); ?></td>
                                <td><?php echo esc_html($job['applications_count']); ?></td>
                                <td><span class="status-badge status-<?php echo esc_attr($job['status']); ?>"><?php echo esc_html(ucfirst($job['status'])); ?></span></td>
                                <td>
                                    <button class="view-job-details" data-job-id="<?php echo esc_attr($job['id']); ?>"><?php _e('View', 'student-services'); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Display Subscription Plans
     * Usage: [subscription_plans]
     */
    public static function subscription_plans_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'all' // all, student, employer
        ), $atts);

        $service = new Student_Services_Subscription_Plans();
        $plans = $service->get_plans();

        ob_start();
        ?>
        <div class="student-services-subscription-plans">
            <h3><?php _e('Subscription Plans', 'student-services'); ?></h3>
            <p><?php _e('Choose the plan that best fits your needs', 'student-services'); ?></p>

            <div class="billing-toggle">
                <button class="billing-monthly active" data-billing="monthly"><?php _e('Monthly', 'student-services'); ?></button>
                <button class="billing-yearly" data-billing="yearly"><?php _e('Yearly (Save 16%)', 'student-services'); ?></button>
            </div>

            <div class="plans-grid">
                <?php foreach ($plans as $plan_id => $plan) : ?>
                    <?php
                    if ($atts['type'] !== 'all') {
                        if ($atts['type'] === 'student' && strpos($plan_id, 'employer') !== false) continue;
                        if ($atts['type'] === 'employer' && strpos($plan_id, 'student') !== false && $plan_id !== 'free') continue;
                    }
                    ?>
                    <div class="plan-card <?php echo isset($plan['popular']) && $plan['popular'] ? 'popular' : ''; ?>">
                        <?php if (isset($plan['popular']) && $plan['popular']) : ?>
                            <span class="popular-badge"><?php _e('Most Popular', 'student-services'); ?></span>
                        <?php endif; ?>

                        <h4><?php echo esc_html($plan['name']); ?></h4>

                        <div class="plan-price">
                            <span class="price monthly-price">
                                ₹<?php echo esc_html(number_format($plan['price'])); ?>
                                <span class="period">/<?php _e('month', 'student-services'); ?></span>
                            </span>
                            <?php if ($plan['price'] > 0 && isset($plan['yearly_price'])) : ?>
                                <span class="price yearly-price" style="display:none;">
                                    ₹<?php echo esc_html(number_format($plan['yearly_price'])); ?>
                                    <span class="period">/<?php _e('year', 'student-services'); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>

                        <ul class="plan-features">
                            <?php foreach ($plan['features'] as $feature) : ?>
                                <li>✅ <?php echo esc_html($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <?php if (isset($plan['limitations']) && !empty($plan['limitations'])) : ?>
                            <ul class="plan-limitations">
                                <?php foreach ($plan['limitations'] as $key => $value) : ?>
                                    <li><?php echo esc_html(str_replace('_', ' ', ucfirst($key))); ?>: <?php echo esc_html($value); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (is_user_logged_in()) : ?>
                            <button class="subscribe-plan" data-plan-id="<?php echo esc_attr($plan_id); ?>" data-price="<?php echo esc_attr($plan['price']); ?>">
                                <?php echo $plan['price'] > 0 ? __('Subscribe Now', 'student-services') : __('Current Plan', 'student-services'); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo wp_login_url(); ?>" class="subscribe-plan">
                                <?php _e('Log in to Subscribe', 'student-services'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
