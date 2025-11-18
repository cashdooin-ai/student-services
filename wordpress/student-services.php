<?php
/**
 * Plugin Name: Student Services
 * Plugin URI: https://github.com/university/student-services
 * Description: Comprehensive student services plugin for college and university websites - includes academic, financial, housing, health, career services and more.
 * Version: 1.0.0
 * Author: University
 * Author URI: https://university.edu
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: student-services
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('STUDENT_SERVICES_VERSION', '1.0.0');
define('STUDENT_SERVICES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('STUDENT_SERVICES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('STUDENT_SERVICES_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Student Services Plugin Class
 */
class Student_Services_Plugin {

    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * Get instance of the class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files
     */
    private function includes() {
        // Core
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/class-database.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/class-base-service.php';

        // Academic Services
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/academic/class-course-registration.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/academic/class-grade-management.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/academic/class-academic-advising.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/academic/class-library.php';

        // Financial Services
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/financial/class-billing.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/financial/class-scholarship.php';

        // Campus Life Services
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/campus-life/class-housing.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/campus-life/class-dining.php';

        // Health Services
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/health/class-health.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/health/class-counseling.php';

        // Career Services
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/career/class-career.php';

        // Campus Operations
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/campus-ops/class-parking.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/campus-ops/class-security.php';

        // Student Engagement
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/engagement/class-organizations.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/engagement/class-events.php';

        // Administrative Services
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/administrative/class-records.php';
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/administrative/class-it-support.php';

        // Admin
        if (is_admin()) {
            require_once STUDENT_SERVICES_PLUGIN_DIR . 'admin/class-admin.php';
        }

        // Public
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'public/class-public.php';

        // REST API
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/class-rest-api.php';

        // Shortcodes
        require_once STUDENT_SERVICES_PLUGIN_DIR . 'includes/class-shortcodes.php';
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialize plugin
        add_action('plugins_loaded', array($this, 'init'));

        // Load text domain
        add_action('init', array($this, 'load_textdomain'));

        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'public_scripts'));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        Student_Services_Database::create_tables();

        // Create default options
        add_option('student_services_version', STUDENT_SERVICES_VERSION);
        add_option('student_services_settings', array(
            'enable_course_registration' => true,
            'enable_billing' => true,
            'enable_housing' => true,
            'enable_events' => true,
        ));

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize services
        Student_Services_REST_API::init();
        Student_Services_Shortcodes::init();

        if (is_admin()) {
            Student_Services_Admin::init();
        }

        Student_Services_Public::init();
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'student-services',
            false,
            dirname(STUDENT_SERVICES_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function admin_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'student-services') === false) {
            return;
        }

        wp_enqueue_style(
            'student-services-admin',
            STUDENT_SERVICES_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            STUDENT_SERVICES_VERSION
        );

        wp_enqueue_script(
            'student-services-admin',
            STUDENT_SERVICES_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery'),
            STUDENT_SERVICES_VERSION,
            true
        );

        wp_localize_script('student-services-admin', 'studentServicesAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('student_services_admin'),
        ));
    }

    /**
     * Enqueue public scripts and styles
     */
    public function public_scripts() {
        wp_enqueue_style(
            'student-services-public',
            STUDENT_SERVICES_PLUGIN_URL . 'public/css/public.css',
            array(),
            STUDENT_SERVICES_VERSION
        );

        wp_enqueue_script(
            'student-services-public',
            STUDENT_SERVICES_PLUGIN_URL . 'public/js/public.js',
            array('jquery'),
            STUDENT_SERVICES_VERSION,
            true
        );

        wp_localize_script('student-services-public', 'studentServices', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('student-services/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }
}

/**
 * Initialize the plugin
 */
function student_services() {
    return Student_Services_Plugin::get_instance();
}

// Start the plugin
student_services();
