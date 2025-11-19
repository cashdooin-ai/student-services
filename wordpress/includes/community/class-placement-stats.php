<?php
/**
 * College Placement Statistics
 * Compare placement records, salary packages, and top recruiters across colleges
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Placement_Stats extends Student_Services_Base_Service {

    /**
     * Get placement statistics for colleges
     */
    public function get_college_placements($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_placement_stats';

        $where = array('1=1');

        if (!empty($filters['college_id'])) {
            $where[] = $wpdb->prepare('college_id = %d', $filters['college_id']);
        }

        if (!empty($filters['year'])) {
            $where[] = $wpdb->prepare('year = %d', $filters['year']);
        }

        if (!empty($filters['course'])) {
            $where[] = $wpdb->prepare('course = %s', $filters['course']);
        }

        $where_sql = implode(' AND ', $where);
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 20;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;

        $placements = $wpdb->get_results($wpdb->prepare(
            "SELECT ps.*, c.name as college_name, c.location
            FROM {$table} ps
            LEFT JOIN {$wpdb->prefix}ss_colleges c ON ps.college_id = c.id
            WHERE {$where_sql}
            ORDER BY ps.average_package DESC
            LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $placements
        );
    }

    /**
     * Get top recruiters for a college
     */
    public function get_top_recruiters($college_id, $year = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_top_recruiters';

        if ($year) {
            $recruiters = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table} WHERE college_id = %d AND year = %d ORDER BY hiring_count DESC",
                $college_id,
                $year
            ));
        } else {
            $recruiters = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$table} WHERE college_id = %d ORDER BY year DESC, hiring_count DESC",
                $college_id
            ));
        }

        return array(
            'success' => true,
            'data' => $recruiters
        );
    }

    /**
     * Compare multiple colleges
     */
    public function compare_colleges($college_ids, $year = null) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'ss_placement_stats';
        $colleges_table = $wpdb->prefix . 'ss_colleges';

        $ids_placeholder = implode(',', array_fill(0, count($college_ids), '%d'));

        $query = "SELECT ps.*, c.name as college_name, c.location, c.established
                  FROM {$stats_table} ps
                  LEFT JOIN {$colleges_table} c ON ps.college_id = c.id
                  WHERE ps.college_id IN ($ids_placeholder)";

        if ($year) {
            $query .= $wpdb->prepare(" AND ps.year = %d", $year);
            $params = array_merge($college_ids, array($year));
        } else {
            $params = $college_ids;
        }

        $query .= " ORDER BY ps.college_id, ps.year DESC";

        $results = $wpdb->get_results($wpdb->prepare($query, ...$params));

        return array(
            'success' => true,
            'data' => $results
        );
    }

    /**
     * Get placement trends for a college
     */
    public function get_placement_trends($college_id, $years = 5) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_placement_stats';

        $trends = $wpdb->get_results($wpdb->prepare(
            "SELECT year, course,
                    students_placed, total_students,
                    (students_placed * 100.0 / total_students) as placement_percentage,
                    average_package, highest_package, lowest_package
            FROM {$table}
            WHERE college_id = %d
            ORDER BY year DESC, course ASC
            LIMIT %d",
            $college_id,
            $years * 10
        ));

        return array(
            'success' => true,
            'data' => $trends
        );
    }

    /**
     * Get sector-wise placement distribution
     */
    public function get_sector_distribution($college_id, $year) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_sector_placements';

        $sectors = $wpdb->get_results($wpdb->prepare(
            "SELECT sector, students_placed, average_package, top_companies
            FROM {$table}
            WHERE college_id = %d AND year = %d
            ORDER BY students_placed DESC",
            $college_id,
            $year
        ));

        return array(
            'success' => true,
            'data' => $sectors
        );
    }

    /**
     * Get salary distribution
     */
    public function get_salary_distribution($college_id, $year) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_salary_distribution';

        $distribution = $wpdb->get_results($wpdb->prepare(
            "SELECT package_range, student_count, percentage
            FROM {$table}
            WHERE college_id = %d AND year = %d
            ORDER BY package_range ASC",
            $college_id,
            $year
        ));

        return array(
            'success' => true,
            'data' => $distribution
        );
    }

    /**
     * Get colleges list
     */
    public function get_colleges($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_colleges';

        $where = array('status = "active"');

        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where[] = $wpdb->prepare('(name LIKE %s OR location LIKE %s)', $search, $search);
        }

        if (!empty($filters['type'])) {
            $where[] = $wpdb->prepare('type = %s', $filters['type']);
        }

        $where_sql = implode(' AND ', $where);
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 50;

        $colleges = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY name ASC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $colleges
        );
    }

    /**
     * Get top colleges by placement
     */
    public function get_top_colleges($year, $limit = 10) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'ss_placement_stats';
        $colleges_table = $wpdb->prefix . 'ss_colleges';

        $colleges = $wpdb->get_results($wpdb->prepare(
            "SELECT c.*,
                    AVG(ps.average_package) as avg_package,
                    MAX(ps.highest_package) as max_package,
                    AVG((ps.students_placed * 100.0 / ps.total_students)) as avg_placement_rate
            FROM {$colleges_table} c
            INNER JOIN {$stats_table} ps ON c.id = ps.college_id
            WHERE ps.year = %d AND c.status = 'active'
            GROUP BY c.id
            ORDER BY avg_package DESC
            LIMIT %d",
            $year,
            $limit
        ));

        return array(
            'success' => true,
            'data' => $colleges
        );
    }

    /**
     * Subscribe to placement alerts
     */
    public function subscribe_alerts($user_id, $college_ids) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_placement_alerts';

        // Clear existing subscriptions
        $wpdb->delete($table, array('user_id' => $user_id));

        // Add new subscriptions
        foreach ($college_ids as $college_id) {
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'college_id' => intval($college_id),
                'created_at' => current_time('mysql')
            ));
        }

        return array('success' => true, 'message' => 'Subscribed to placement alerts');
    }
}
