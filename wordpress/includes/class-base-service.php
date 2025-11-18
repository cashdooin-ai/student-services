<?php
/**
 * Base Service Class
 * All service classes extend this base class
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class Student_Services_Base_Service {

    /**
     * WordPress database object
     */
    protected $wpdb;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * Get current user ID
     */
    protected function get_current_user_id() {
        return get_current_user_id();
    }

    /**
     * Check if user is logged in
     */
    protected function is_user_logged_in() {
        return is_user_logged_in();
    }

    /**
     * Check user capability
     */
    protected function current_user_can($capability) {
        return current_user_can($capability);
    }

    /**
     * Validate required fields
     */
    protected function validate_required($data, $fields) {
        $missing = array();

        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return new WP_Error(
                'missing_fields',
                sprintf(__('Missing required fields: %s', 'student-services'), implode(', ', $missing)),
                array('status' => 400)
            );
        }

        return true;
    }

    /**
     * Return success response
     */
    protected function success($data = null, $message = '') {
        return array(
            'success' => true,
            'data' => $data,
            'message' => $message
        );
    }

    /**
     * Return error response
     */
    protected function error($message, $code = 'error', $status = 400) {
        return new WP_Error($code, $message, array('status' => $status));
    }

    /**
     * Sanitize text field
     */
    protected function sanitize($value) {
        return sanitize_text_field($value);
    }

    /**
     * Sanitize textarea
     */
    protected function sanitize_textarea($value) {
        return sanitize_textarea_field($value);
    }

    /**
     * Sanitize email
     */
    protected function sanitize_email($value) {
        return sanitize_email($value);
    }

    /**
     * Get table name with prefix
     */
    protected function get_table($table) {
        return $this->wpdb->prefix . 'ss_' . $table;
    }

    /**
     * Insert data into table
     */
    protected function insert($table, $data) {
        $result = $this->wpdb->insert(
            $this->get_table($table),
            $data
        );

        if ($result === false) {
            return $this->error(__('Database insert failed', 'student-services'));
        }

        return $this->wpdb->insert_id;
    }

    /**
     * Update data in table
     */
    protected function update($table, $data, $where) {
        $result = $this->wpdb->update(
            $this->get_table($table),
            $data,
            $where
        );

        if ($result === false) {
            return $this->error(__('Database update failed', 'student-services'));
        }

        return $result;
    }

    /**
     * Delete from table
     */
    protected function delete($table, $where) {
        $result = $this->wpdb->delete(
            $this->get_table($table),
            $where
        );

        if ($result === false) {
            return $this->error(__('Database delete failed', 'student-services'));
        }

        return $result;
    }

    /**
     * Get single row from table
     */
    protected function get_row($table, $where, $output = OBJECT) {
        $table_name = $this->get_table($table);

        $where_clause = array();
        $values = array();

        foreach ($where as $key => $value) {
            $where_clause[] = "`$key` = %s";
            $values[] = $value;
        }

        $sql = "SELECT * FROM $table_name WHERE " . implode(' AND ', $where_clause);
        $sql = $this->wpdb->prepare($sql, $values);

        return $this->wpdb->get_row($sql, $output);
    }

    /**
     * Get multiple rows from table
     */
    protected function get_results($table, $where = array(), $output = OBJECT) {
        $table_name = $this->get_table($table);

        if (empty($where)) {
            $sql = "SELECT * FROM $table_name";
            return $this->wpdb->get_results($sql, $output);
        }

        $where_clause = array();
        $values = array();

        foreach ($where as $key => $value) {
            $where_clause[] = "`$key` = %s";
            $values[] = $value;
        }

        $sql = "SELECT * FROM $table_name WHERE " . implode(' AND ', $where_clause);
        $sql = $this->wpdb->prepare($sql, $values);

        return $this->wpdb->get_results($sql, $output);
    }

    /**
     * Check if user has permission for this service
     */
    protected function check_permission($user_id = null) {
        if (!$this->is_user_logged_in()) {
            return $this->error(__('You must be logged in', 'student-services'), 'not_logged_in', 401);
        }

        $current_user_id = $this->get_current_user_id();

        // Admin can do everything
        if ($this->current_user_can('manage_options')) {
            return true;
        }

        // If user_id is specified, check if it matches current user
        if ($user_id !== null && $user_id != $current_user_id) {
            return $this->error(__('Permission denied', 'student-services'), 'permission_denied', 403);
        }

        return true;
    }

    /**
     * Log activity
     */
    protected function log($message, $data = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[Student Services] %s: %s', $message, print_r($data, true)));
        }
    }
}
