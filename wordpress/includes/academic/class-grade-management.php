<?php
/**
 * Grade Management Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Grade_Management extends Student_Services_Base_Service {

    /**
     * Get grades for a student
     */
    public function get_grades($user_id, $semester = '', $year = '') {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $where = array('user_id' => $user_id);

        if (!empty($semester)) {
            $where['semester'] = $semester;
        }

        if (!empty($year)) {
            $where['year'] = $year;
        }

        $grades_table = $this->get_table('grades');
        $courses_table = $this->get_table('courses');

        $where_clause = array();
        $values = array();

        foreach ($where as $key => $value) {
            $where_clause[] = "g.$key = %s";
            $values[] = $value;
        }

        $sql = "SELECT g.*, c.course_code, c.title as course_name
                FROM $grades_table g
                LEFT JOIN $courses_table c ON g.course_id = c.id
                WHERE " . implode(' AND ', $where_clause) . "
                ORDER BY g.year DESC, g.semester DESC";

        $sql = $this->wpdb->prepare($sql, $values);
        $grades = $this->wpdb->get_results($sql);

        return $this->success($grades);
    }

    /**
     * Calculate GPA
     */
    public function calculate_gpa($user_id, $cumulative = true) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $grades_table = $this->get_table('grades');
        $courses_table = $this->get_table('courses');

        $sql = $this->wpdb->prepare(
            "SELECT SUM(g.grade_point * c.credits) as quality_points,
                    SUM(c.credits) as total_credits
             FROM $grades_table g
             LEFT JOIN $courses_table c ON g.course_id = c.id
             WHERE g.user_id = %d",
            $user_id
        );

        $result = $this->wpdb->get_row($sql);

        if (!$result || $result->total_credits == 0) {
            return $this->success(array(
                'gpa' => 0.0,
                'credits' => 0,
                'quality_points' => 0
            ));
        }

        $gpa = round($result->quality_points / $result->total_credits, 2);

        return $this->success(array(
            'gpa' => $gpa,
            'credits' => $result->total_credits,
            'quality_points' => $result->quality_points
        ));
    }

    /**
     * Add grade (instructor/admin only)
     */
    public function add_grade($data) {
        if (!current_user_can('edit_others_posts')) {
            return $this->error(__('Permission denied', 'student-services'), 'permission_denied', 403);
        }

        $validation = $this->validate_required($data, array('user_id', 'course_id', 'grade'));
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Convert letter grade to grade point
        $grade_points = $this->letter_to_gpa($data['grade']);

        $grade_id = $this->insert('grades', array(
            'user_id' => intval($data['user_id']),
            'course_id' => intval($data['course_id']),
            'grade' => $this->sanitize($data['grade']),
            'grade_point' => $grade_points,
            'semester' => isset($data['semester']) ? $this->sanitize($data['semester']) : '',
            'year' => isset($data['year']) ? intval($data['year']) : date('Y')
        ));

        if (is_wp_error($grade_id)) {
            return $grade_id;
        }

        return $this->success(array('grade_id' => $grade_id), __('Grade added successfully', 'student-services'));
    }

    /**
     * Convert letter grade to GPA
     */
    private function letter_to_gpa($grade) {
        $conversion = array(
            'A' => 4.0, 'A-' => 3.7,
            'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
            'D+' => 1.3, 'D' => 1.0, 'D-' => 0.7,
            'F' => 0.0
        );

        return isset($conversion[$grade]) ? $conversion[$grade] : 0.0;
    }
}
