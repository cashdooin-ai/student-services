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

        add_submenu_page(
            'student-services',
            __('Data Seeder', 'student-services'),
            __('Data Seeder', 'student-services'),
            'manage_options',
            'student-services-seeder',
            array(__CLASS__, 'seeder_page')
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

    public static function seeder_page() {
        global $wpdb;

        // Handle seeding actions
        if (isset($_POST['ss_seeder_nonce']) && wp_verify_nonce($_POST['ss_seeder_nonce'], 'ss_seeder_action')) {
            $action = isset($_POST['seeder_action']) ? $_POST['seeder_action'] : '';

            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-data-seeder.php';

            switch ($action) {
                case 'seed_all':
                    Student_Services_Data_Seeder::seed_all();
                    echo '<div class="notice notice-success"><p>' . __('All data seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'seed_webinars':
                    Student_Services_Data_Seeder::seed_webinars();
                    echo '<div class="notice notice-success"><p>' . __('Webinars data seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'seed_forum':
                    Student_Services_Data_Seeder::seed_forum_posts();
                    echo '<div class="notice notice-success"><p>' . __('Forum data seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'seed_faqs':
                    Student_Services_Data_Seeder::seed_faqs();
                    echo '<div class="notice notice-success"><p>' . __('FAQs data seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'seed_calendar':
                    Student_Services_Data_Seeder::seed_calendar_events();
                    echo '<div class="notice notice-success"><p>' . __('Calendar events seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'seed_scholarships':
                    Student_Services_Data_Seeder::seed_scholarships_data();
                    echo '<div class="notice notice-success"><p>' . __('Scholarships data seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'seed_accommodations':
                    Student_Services_Data_Seeder::seed_accommodations_data();
                    echo '<div class="notice notice-success"><p>' . __('Accommodations data seeded successfully!', 'student-services') . '</p></div>';
                    break;
                case 'clear_all':
                    Student_Services_Data_Seeder::clear_all();
                    echo '<div class="notice notice-success"><p>' . __('All seeded data cleared successfully!', 'student-services') . '</p></div>';
                    break;
            }
        }

        // Get current data counts
        $webinars_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_webinars");
        $forum_posts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_forum_posts");
        $faqs_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_faqs");
        $calendar_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_calendar_events");
        $scholarships_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_scholarships_master");
        $accommodations_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ss_accommodations");

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php _e('Populate your database with realistic demo data for testing and development.', 'student-services'); ?></p>

            <div class="card" style="max-width: 800px;">
                <h2><?php _e('Current Data Status', 'student-services'); ?></h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Service', 'student-services'); ?></th>
                            <th><?php _e('Records', 'student-services'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e('Webinars & Workshops', 'student-services'); ?></td>
                            <td><strong><?php echo intval($webinars_count); ?></strong></td>
                        </tr>
                        <tr>
                            <td><?php _e('Forum Posts', 'student-services'); ?></td>
                            <td><strong><?php echo intval($forum_posts_count); ?></strong></td>
                        </tr>
                        <tr>
                            <td><?php _e('FAQs', 'student-services'); ?></td>
                            <td><strong><?php echo intval($faqs_count); ?></strong></td>
                        </tr>
                        <tr>
                            <td><?php _e('Calendar Events', 'student-services'); ?></td>
                            <td><strong><?php echo intval($calendar_count); ?></strong></td>
                        </tr>
                        <tr>
                            <td><?php _e('Scholarships', 'student-services'); ?></td>
                            <td><strong><?php echo intval($scholarships_count); ?></strong></td>
                        </tr>
                        <tr>
                            <td><?php _e('Accommodations', 'student-services'); ?></td>
                            <td><strong><?php echo intval($accommodations_count); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2><?php _e('Seed Data', 'student-services'); ?></h2>
                <p><?php _e('Click on individual services to seed specific data, or use "Seed All Data" to populate all services at once.', 'student-services'); ?></p>

                <form method="post" action="" style="margin-bottom: 20px;">
                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                    <input type="hidden" name="seeder_action" value="seed_all" />
                    <button type="submit" class="button button-primary button-hero">
                        <span class="dashicons dashicons-database-add" style="margin-top: 3px;"></span>
                        <?php _e('Seed All Data', 'student-services'); ?>
                    </button>
                </form>

                <table class="widefat" style="margin-top: 15px;">
                    <tbody>
                        <tr>
                            <td style="width: 60%;">
                                <strong><?php _e('Webinars & Workshops', 'student-services'); ?></strong>
                                <p class="description"><?php _e('8 webinars with expert speakers', 'student-services'); ?></p>
                            </td>
                            <td>
                                <form method="post" action="" style="display: inline;">
                                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                                    <input type="hidden" name="seeder_action" value="seed_webinars" />
                                    <button type="submit" class="button"><?php _e('Seed Webinars', 'student-services'); ?></button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php _e('Student Forum', 'student-services'); ?></strong>
                                <p class="description"><?php _e('10 forum posts with engagement data', 'student-services'); ?></p>
                            </td>
                            <td>
                                <form method="post" action="" style="display: inline;">
                                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                                    <input type="hidden" name="seeder_action" value="seed_forum" />
                                    <button type="submit" class="button"><?php _e('Seed Forum', 'student-services'); ?></button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php _e('FAQs', 'student-services'); ?></strong>
                                <p class="description"><?php _e('20+ FAQs across 7 categories', 'student-services'); ?></p>
                            </td>
                            <td>
                                <form method="post" action="" style="display: inline;">
                                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                                    <input type="hidden" name="seeder_action" value="seed_faqs" />
                                    <button type="submit" class="button"><?php _e('Seed FAQs', 'student-services'); ?></button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php _e('Academic Calendar', 'student-services'); ?></strong>
                                <p class="description"><?php _e('12 upcoming events and deadlines', 'student-services'); ?></p>
                            </td>
                            <td>
                                <form method="post" action="" style="display: inline;">
                                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                                    <input type="hidden" name="seeder_action" value="seed_calendar" />
                                    <button type="submit" class="button"><?php _e('Seed Calendar', 'student-services'); ?></button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php _e('Scholarships', 'student-services'); ?></strong>
                                <p class="description"><?php _e('10 scholarships (₹1L - ₹6L)', 'student-services'); ?></p>
                            </td>
                            <td>
                                <form method="post" action="" style="display: inline;">
                                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                                    <input type="hidden" name="seeder_action" value="seed_scholarships" />
                                    <button type="submit" class="button"><?php _e('Seed Scholarships', 'student-services'); ?></button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php _e('Accommodations', 'student-services'); ?></strong>
                                <p class="description"><?php _e('8 accommodations (₹7K - ₹15K/month)', 'student-services'); ?></p>
                            </td>
                            <td>
                                <form method="post" action="" style="display: inline;">
                                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                                    <input type="hidden" name="seeder_action" value="seed_accommodations" />
                                    <button type="submit" class="button"><?php _e('Seed Accommodations', 'student-services'); ?></button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px; border-left: 4px solid #dc3232;">
                <h2><?php _e('Clear Data', 'student-services'); ?></h2>
                <p><?php _e('Remove all seeded data from the database. This action cannot be undone.', 'student-services'); ?></p>
                <form method="post" action="" onsubmit="return confirm('<?php _e('Are you sure you want to clear all seeded data? This cannot be undone!', 'student-services'); ?>');">
                    <?php wp_nonce_field('ss_seeder_action', 'ss_seeder_nonce'); ?>
                    <input type="hidden" name="seeder_action" value="clear_all" />
                    <button type="submit" class="button button-secondary">
                        <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                        <?php _e('Clear All Seeded Data', 'student-services'); ?>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}
