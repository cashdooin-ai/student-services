<?php
/**
 * Course Registration Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Course_Registration extends Student_Services_Base_Service {

    /**
     * Search for courses
     */
    public function search_courses($args = array()) {
        $defaults = array(
            'semester' => '',
            'year' => '',
            'subject' => '',
            'instructor' => '',
            'available_only' => false
        );

        $args = wp_parse_args($args, $defaults);

        $table = $this->get_table('courses');
        $where = array();
        $values = array();

        if (!empty($args['semester'])) {
            $where[] = 'semester = %s';
            $values[] = $args['semester'];
        }

        if (!empty($args['year'])) {
            $where[] = 'year = %d';
            $values[] = $args['year'];
        }

        if (!empty($args['subject'])) {
            $where[] = 'course_code LIKE %s';
            $values[] = $args['subject'] . '%';
        }

        if (!empty($args['instructor'])) {
            $where[] = 'instructor LIKE %s';
            $values[] = '%' . $args['instructor'] . '%';
        }

        if ($args['available_only']) {
            $where[] = 'enrolled < capacity';
        }

        $sql = "SELECT * FROM $table";

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY course_code ASC';

        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }

        $courses = $this->wpdb->get_results($sql);

        return $this->success($courses);
    }

    /**
     * Enroll in a course
     */
    public function enroll($user_id, $course_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        // Check if course exists
        $course = $this->get_row('courses', array('id' => $course_id));
        if (!$course) {
            return $this->error(__('Course not found', 'student-services'));
        }

        // Check if already enrolled
        $existing = $this->get_row('enrollments', array(
            'user_id' => $user_id,
            'course_id' => $course_id,
            'status' => 'enrolled'
        ));

        if ($existing) {
            return $this->error(__('Already enrolled in this course', 'student-services'));
        }

        // Check capacity
        if ($course->enrolled >= $course->capacity) {
            return $this->error(__('Course is full', 'student-services'));
        }

        // Enroll student
        $enrollment_id = $this->insert('enrollments', array(
            'user_id' => $user_id,
            'course_id' => $course_id,
            'status' => 'enrolled',
            'enrolled_at' => current_time('mysql')
        ));

        if (is_wp_error($enrollment_id)) {
            return $enrollment_id;
        }

        // Update enrolled count
        $this->update('courses',
            array('enrolled' => $course->enrolled + 1),
            array('id' => $course_id)
        );

        return $this->success(array(
            'enrollment_id' => $enrollment_id,
            'course' => $course
        ), __('Successfully enrolled in course', 'student-services'));
    }

    /**
     * Drop a course
     */
    public function drop($user_id, $course_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $enrollment = $this->get_row('enrollments', array(
            'user_id' => $user_id,
            'course_id' => $course_id,
            'status' => 'enrolled'
        ));

        if (!$enrollment) {
            return $this->error(__('Not enrolled in this course', 'student-services'));
        }

        // Update enrollment status
        $this->update('enrollments',
            array(
                'status' => 'dropped',
                'dropped_at' => current_time('mysql')
            ),
            array('id' => $enrollment->id)
        );

        // Decrease enrolled count
        $course = $this->get_row('courses', array('id' => $course_id));
        $this->update('courses',
            array('enrolled' => max(0, $course->enrolled - 1)),
            array('id' => $course_id)
        );

        return $this->success(null, __('Successfully dropped course', 'student-services'));
    }

    /**
     * Get enrolled courses for a student
     */
    public function get_enrolled_courses($user_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $courses_table = $this->get_table('courses');
        $enrollments_table = $this->get_table('enrollments');

        $sql = $this->wpdb->prepare(
            "SELECT c.*, e.enrolled_at, e.status
            FROM $courses_table c
            INNER JOIN $enrollments_table e ON c.id = e.course_id
            WHERE e.user_id = %d AND e.status = 'enrolled'
            ORDER BY c.course_code",
            $user_id
        );

        $courses = $this->wpdb->get_results($sql);

        return $this->success($courses);
    }

    /**
     * Add a new course (admin only)
     */
    public function add_course($data) {
        if (!current_user_can('manage_options')) {
            return $this->error(__('Permission denied', 'student-services'), 'permission_denied', 403);
        }

        $validation = $this->validate_required($data, array('course_code', 'title', 'credits'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        $course_id = $this->insert('courses', array(
            'course_code' => $this->sanitize($data['course_code']),
            'title' => $this->sanitize($data['title']),
            'credits' => intval($data['credits']),
            'instructor' => isset($data['instructor']) ? $this->sanitize($data['instructor']) : '',
            'capacity' => isset($data['capacity']) ? intval($data['capacity']) : 30,
            'enrolled' => 0,
            'semester' => isset($data['semester']) ? $this->sanitize($data['semester']) : '',
            'year' => isset($data['year']) ? intval($data['year']) : date('Y'),
            'description' => isset($data['description']) ? $this->sanitize_textarea($data['description']) : '',
            'prerequisites' => isset($data['prerequisites']) ? wp_json_encode($data['prerequisites']) : ''
        ));

        if (is_wp_error($course_id)) {
            return $course_id;
        }

        return $this->success(array('course_id' => $course_id), __('Course added successfully', 'student-services'));
    }
}
