<?php
/**
 * Admin Interface
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Admin {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
    }

    public static function add_admin_menu() {
        add_menu_page(
            __('Student Services', 'student-services'),
            __('Student Services', 'student-services'),
            'manage_options',
            'student-services',
            array(__CLASS__, 'dashboard_page'),
            'dashicons-welcome-learn-more',
            30
        );

        add_submenu_page(
            'student-services',
            __('Courses', 'student-services'),
            __('Courses', 'student-services'),
            'manage_options',
            'student-services-courses',
            array(__CLASS__, 'courses_page')
        );

        add_submenu_page(
            'student-services',
            __('Billing', 'student-services'),
            __('Billing', 'student-services'),
            'manage_options',
            'student-services-billing',
            array(__CLASS__, 'billing_page')
        );

        add_submenu_page(
            'student-services',
            __('Events', 'student-services'),
            __('Events', 'student-services'),
            'manage_options',
            'student-services-events',
            array(__CLASS__, 'events_page')
        );

        add_submenu_page(
            'student-services',
            __('Settings', 'student-services'),
            __('Settings', 'student-services'),
            'manage_options',
            'student-services-settings',
            array(__CLASS__, 'settings_page')
        );
    }

    public static function dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="student-services-dashboard">
                <div class="card">
                    <h2><?php _e('Quick Stats', 'student-services'); ?></h2>
                    <?php
                    global $wpdb;
                    $courses_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_courses");
                    $enrollments_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_enrollments WHERE status = 'enrolled'");
                    $events_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_events WHERE status = 'active'");
                    ?>
                    <ul>
                        <li><strong><?php _e('Total Courses:', 'student-services'); ?></strong> <?php echo $courses_count; ?></li>
                        <li><strong><?php _e('Active Enrollments:', 'student-services'); ?></strong> <?php echo $enrollments_count; ?></li>
                        <li><strong><?php _e('Upcoming Events:', 'student-services'); ?></strong> <?php echo $events_count; ?></li>
                    </ul>
                </div>

                <div class="card">
                    <h2><?php _e('Quick Links', 'student-services'); ?></h2>
                    <p><a href="<?php echo admin_url('admin.php?page=student-services-courses'); ?>"><?php _e('Manage Courses', 'student-services'); ?></a></p>
                    <p><a href="<?php echo admin_url('admin.php?page=student-services-events'); ?>"><?php _e('Manage Events', 'student-services'); ?></a></p>
                    <p><a href="<?php echo admin_url('admin.php?page=student-services-billing'); ?>"><?php _e('View Billing', 'student-services'); ?></a></p>
                    <p><a href="<?php echo admin_url('admin.php?page=student-services-settings'); ?>"><?php _e('Settings', 'student-services'); ?></a></p>
                </div>

                <div class="card">
                    <h2><?php _e('Documentation', 'student-services'); ?></h2>
                    <p><?php _e('Use the REST API to integrate with external systems:', 'student-services'); ?></p>
                    <code><?php echo rest_url('student-services/v1/'); ?></code>
                    <p><a href="https://github.com/university/student-services" target="_blank"><?php _e('View Documentation', 'student-services'); ?></a></p>
                </div>
            </div>
        </div>
        <?php
    }

    public static function courses_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <h2><?php _e('Add New Course', 'student-services'); ?></h2>
            <form method="post" action="">
                <?php wp_nonce_field('ss_add_course', 'ss_course_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="course_code"><?php _e('Course Code', 'student-services'); ?></label></th>
                        <td><input type="text" name="course_code" id="course_code" required /></td>
                    </tr>
                    <tr>
                        <th><label for="title"><?php _e('Course Title', 'student-services'); ?></label></th>
                        <td><input type="text" name="title" id="title" class="regular-text" required /></td>
                    </tr>
                    <tr>
                        <th><label for="credits"><?php _e('Credits', 'student-services'); ?></label></th>
                        <td><input type="number" name="credits" id="credits" min="1" max="6" required /></td>
                    </tr>
                    <tr>
                        <th><label for="instructor"><?php _e('Instructor', 'student-services'); ?></label></th>
                        <td><input type="text" name="instructor" id="instructor" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="capacity"><?php _e('Capacity', 'student-services'); ?></label></th>
                        <td><input type="number" name="capacity" id="capacity" value="30" /></td>
                    </tr>
                </table>

                <?php submit_button(__('Add Course', 'student-services')); ?>
            </form>

            <?php
            if (isset($_POST['ss_course_nonce']) && wp_verify_nonce($_POST['ss_course_nonce'], 'ss_add_course')) {
                $service = new Student_Services_Course_Registration();
                $result = $service->add_course($_POST);

                if (!is_wp_error($result)) {
                    echo '<div class="notice notice-success"><p>' . __('Course added successfully!', 'student-services') . '</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
                }
            }
            ?>

            <h2><?php _e('Existing Courses', 'student-services'); ?></h2>
            <?php
            global $wpdb;
            $courses = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ss_courses ORDER BY course_code");

            if ($courses) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . __('Code', 'student-services') . '</th>';
                echo '<th>' . __('Title', 'student-services') . '</th>';
                echo '<th>' . __('Credits', 'student-services') . '</th>';
                echo '<th>' . __('Instructor', 'student-services') . '</th>';
                echo '<th>' . __('Enrolled/Capacity', 'student-services') . '</th>';
                echo '</tr></thead><tbody>';

                foreach ($courses as $course) {
                    echo '<tr>';
                    echo '<td>' . esc_html($course->course_code) . '</td>';
                    echo '<td>' . esc_html($course->title) . '</td>';
                    echo '<td>' . esc_html($course->credits) . '</td>';
                    echo '<td>' . esc_html($course->instructor) . '</td>';
                    echo '<td>' . esc_html($course->enrolled) . '/' . esc_html($course->capacity) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>' . __('No courses found.', 'student-services') . '</p>';
            }
            ?>
        </div>
        <?php
    }

    public static function billing_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php _e('Manage student billing and payments.', 'student-services'); ?></p>

            <h2><?php _e('Recent Payments', 'student-services'); ?></h2>
            <?php
            global $wpdb;
            $payments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ss_payments ORDER BY created_at DESC LIMIT 20");

            if ($payments) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . __('User ID', 'student-services') . '</th>';
                echo '<th>' . __('Amount', 'student-services') . '</th>';
                echo '<th>' . __('Method', 'student-services') . '</th>';
                echo '<th>' . __('Status', 'student-services') . '</th>';
                echo '<th>' . __('Date', 'student-services') . '</th>';
                echo '</tr></thead><tbody>';

                foreach ($payments as $payment) {
                    echo '<tr>';
                    echo '<td>' . esc_html($payment->user_id) . '</td>';
                    echo '<td>$' . esc_html(number_format($payment->amount, 2)) . '</td>';
                    echo '<td>' . esc_html($payment->payment_method) . '</td>';
                    echo '<td>' . esc_html($payment->status) . '</td>';
                    echo '<td>' . esc_html($payment->created_at) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>' . __('No payments found.', 'student-services') . '</p>';
            }
            ?>
        </div>
        <?php
    }

    public static function events_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php _e('Manage campus events.', 'student-services'); ?></p>

            <?php
            global $wpdb;
            $events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ss_events ORDER BY event_date DESC");

            if ($events) {
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr>';
                echo '<th>' . __('Name', 'student-services') . '</th>';
                echo '<th>' . __('Date', 'student-services') . '</th>';
                echo '<th>' . __('Location', 'student-services') . '</th>';
                echo '<th>' . __('Category', 'student-services') . '</th>';
                echo '<th>' . __('Status', 'student-services') . '</th>';
                echo '</tr></thead><tbody>';

                foreach ($events as $event) {
                    echo '<tr>';
                    echo '<td>' . esc_html($event->name) . '</td>';
                    echo '<td>' . esc_html($event->event_date) . '</td>';
                    echo '<td>' . esc_html($event->location) . '</td>';
                    echo '<td>' . esc_html($event->category) . '</td>';
                    echo '<td>' . esc_html($event->status) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            } else {
                echo '<p>' . __('No events found.', 'student-services') . '</p>';
            }
            ?>
        </div>
        <?php
    }

    public static function settings_page() {
        if (isset($_POST['ss_settings_nonce']) && wp_verify_nonce($_POST['ss_settings_nonce'], 'ss_save_settings')) {
            $settings = array(
                'enable_course_registration' => isset($_POST['enable_course_registration']),
                'enable_billing' => isset($_POST['enable_billing']),
                'enable_housing' => isset($_POST['enable_housing']),
                'enable_events' => isset($_POST['enable_events']),
            );
            update_option('student_services_settings', $settings);
            echo '<div class="notice notice-success"><p>' . __('Settings saved!', 'student-services') . '</p></div>';
        }

        $settings = get_option('student_services_settings', array());
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('ss_save_settings', 'ss_settings_nonce'); ?>

                <h2><?php _e('Enable/Disable Services', 'student-services'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Course Registration', 'student-services'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_course_registration" value="1" <?php checked(isset($settings['enable_course_registration']) && $settings['enable_course_registration']); ?> />
                                <?php _e('Enable course registration', 'student-services'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Billing', 'student-services'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_billing" value="1" <?php checked(isset($settings['enable_billing']) && $settings['enable_billing']); ?> />
                                <?php _e('Enable billing and payments', 'student-services'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Housing', 'student-services'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_housing" value="1" <?php checked(isset($settings['enable_housing']) && $settings['enable_housing']); ?> />
                                <?php _e('Enable housing services', 'student-services'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Events', 'student-services'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_events" value="1" <?php checked(isset($settings['enable_events']) && $settings['enable_events']); ?> />
                                <?php _e('Enable event management', 'student-services'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
