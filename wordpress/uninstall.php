<?php
/**
 * Uninstall Student Services Plugin
 * Fired when the plugin is uninstalled
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Include database class
require_once plugin_dir_path(__FILE__) . 'includes/class-database.php';

// Drop all plugin tables
Student_Services_Database::drop_tables();

// Delete plugin options
delete_option('student_services_version');
delete_option('student_services_settings');

// Delete all plugin post types
$post_types = array(
    'ss_appointment',
    'ss_scholarship_app',
    'ss_library_item',
    'ss_parking_permit',
    'ss_security_incident',
    'ss_safety_escort',
    'ss_job_application',
    'ss_transcript_request'
);

foreach ($post_types as $post_type) {
    $posts = get_posts(array(
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'any'
    ));

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Clear any cached data
wp_cache_flush();
