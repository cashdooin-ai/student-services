<?php
/**
 * Blog & News System
 * Admission tips, college news, and career guidance
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Blog extends Student_Services_Base_Service {

    /**
     * Get blog categories
     */
    public function get_categories() {
        return array(
            'success' => true,
            'data' => array(
                array('id' => 'admission-tips', 'name' => 'Admission Tips', 'icon' => '🎓'),
                array('id' => 'college-news', 'name' => 'College News', 'icon' => '📰'),
                array('id' => 'career-guidance', 'name' => 'Career Guidance', 'icon' => '💼'),
                array('id' => 'study-tips', 'name' => 'Study Tips', 'icon' => '📚'),
                array('id' => 'exam-updates', 'name' => 'Exam Updates', 'icon' => '📝'),
                array('id' => 'study-abroad', 'name' => 'Study Abroad', 'icon' => '✈️'),
                array('id' => 'scholarship-news', 'name' => 'Scholarship News', 'icon' => '💰'),
                array('id' => 'campus-life', 'name' => 'Campus Life', 'icon' => '🏫'),
            )
        );
    }

    /**
     * Get blog posts
     */
    public function get_posts($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_posts';

        $where = array('status = "published"');

        if (!empty($filters['category'])) {
            $where[] = $wpdb->prepare('category = %s', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where[] = $wpdb->prepare('(title LIKE %s OR content LIKE %s OR excerpt LIKE %s)', $search, $search, $search);
        }

        $limit = isset($filters['limit']) ? intval($filters['limit']) : 10;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;

        $where_sql = implode(' AND ', $where);

        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY published_date DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $posts
        );
    }

    /**
     * Get single blog post
     */
    public function get_post($post_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_posts';

        $post = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d AND status = 'published'",
            $post_id
        ));

        if (!$post) {
            return array('success' => false, 'message' => 'Post not found');
        }

        // Increment views
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET views = views + 1 WHERE id = %d",
            $post_id
        ));

        return array(
            'success' => true,
            'data' => $post
        );
    }

    /**
     * Get featured posts
     */
    public function get_featured_posts($limit = 5) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_posts';

        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'published' AND featured = 1 ORDER BY published_date DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $posts
        );
    }

    /**
     * Get popular posts
     */
    public function get_popular_posts($limit = 5) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_posts';

        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'published' ORDER BY views DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $posts
        );
    }

    /**
     * Get recent posts
     */
    public function get_recent_posts($limit = 5) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_posts';

        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = 'published' ORDER BY published_date DESC LIMIT %d",
            $limit
        ));

        return array(
            'success' => true,
            'data' => $posts
        );
    }

    /**
     * Like a post
     */
    public function like_post($user_id, $post_id) {
        global $wpdb;
        $likes_table = $wpdb->prefix . 'ss_blog_likes';
        $posts_table = $wpdb->prefix . 'ss_blog_posts';

        // Check if already liked
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$likes_table} WHERE user_id = %d AND post_id = %d",
            $user_id,
            $post_id
        ));

        if ($existing) {
            // Unlike
            $wpdb->delete($likes_table, array('id' => $existing));
            $wpdb->query($wpdb->prepare(
                "UPDATE {$posts_table} SET likes = likes - 1 WHERE id = %d",
                $post_id
            ));
            return array('success' => true, 'action' => 'unliked');
        } else {
            // Like
            $wpdb->insert($likes_table, array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'created_at' => current_time('mysql')
            ));
            $wpdb->query($wpdb->prepare(
                "UPDATE {$posts_table} SET likes = likes + 1 WHERE id = %d",
                $post_id
            ));
            return array('success' => true, 'action' => 'liked');
        }
    }

    /**
     * Add comment to post
     */
    public function add_comment($user_id, $post_id, $content) {
        global $wpdb;
        $comments_table = $wpdb->prefix . 'ss_blog_comments';
        $posts_table = $wpdb->prefix . 'ss_blog_posts';

        $result = $wpdb->insert($comments_table, array(
            'post_id' => $post_id,
            'user_id' => $user_id,
            'content' => sanitize_textarea_field($content),
            'status' => 'approved',
            'created_at' => current_time('mysql')
        ));

        if ($result) {
            // Increment comment count
            $wpdb->query($wpdb->prepare(
                "UPDATE {$posts_table} SET comments = comments + 1 WHERE id = %d",
                $post_id
            ));

            return array('success' => true, 'comment_id' => $wpdb->insert_id);
        }

        return array('success' => false, 'message' => 'Failed to add comment');
    }

    /**
     * Get comments for post
     */
    public function get_comments($post_id, $limit = 20, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_comments';

        $comments = $wpdb->get_results($wpdb->prepare(
            "SELECT c.*, u.display_name as user_name
            FROM {$table} c
            LEFT JOIN {$wpdb->users} u ON c.user_id = u.ID
            WHERE c.post_id = %d AND c.status = 'approved'
            ORDER BY c.created_at DESC
            LIMIT %d OFFSET %d",
            $post_id,
            $limit,
            $offset
        ));

        return array(
            'success' => true,
            'data' => $comments
        );
    }

    /**
     * Bookmark post
     */
    public function bookmark_post($user_id, $post_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'ss_blog_bookmarks';

        // Check if already bookmarked
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND post_id = %d",
            $user_id,
            $post_id
        ));

        if ($existing) {
            // Remove bookmark
            $wpdb->delete($table, array('id' => $existing));
            return array('success' => true, 'action' => 'removed');
        } else {
            // Add bookmark
            $wpdb->insert($table, array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'created_at' => current_time('mysql')
            ));
            return array('success' => true, 'action' => 'added');
        }
    }

    /**
     * Get user's bookmarked posts
     */
    public function get_bookmarked_posts($user_id) {
        global $wpdb;
        $bookmarks_table = $wpdb->prefix . 'ss_blog_bookmarks';
        $posts_table = $wpdb->prefix . 'ss_blog_posts';

        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT p.* FROM {$posts_table} p
            INNER JOIN {$bookmarks_table} b ON p.id = b.post_id
            WHERE b.user_id = %d AND p.status = 'published'
            ORDER BY b.created_at DESC",
            $user_id
        ));

        return array(
            'success' => true,
            'data' => $posts
        );
    }
}
