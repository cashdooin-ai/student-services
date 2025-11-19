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
}
