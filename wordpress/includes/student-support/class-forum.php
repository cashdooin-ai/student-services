<?php
/**
 * Student Forum Service
 * Connect, share, and learn with fellow students
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Forum extends Student_Services_Base_Service {

    /**
     * Get forum categories
     */
    public function get_categories() {
        $categories = array(
            array('id' => 1, 'name' => 'Entrance Exams', 'description' => 'Discuss JEE, NEET, GATE, CAT and other exams', 'post_count' => 1250),
            array('id' => 2, 'name' => 'College Life', 'description' => 'Share experiences and tips about college life', 'post_count' => 856),
            array('id' => 3, 'name' => 'Career Guidance', 'description' => 'Get advice on internships, placements, and careers', 'post_count' => 642),
            array('id' => 4, 'name' => 'Study Abroad', 'description' => 'Discussions about studying overseas', 'post_count' => 423),
            array('id' => 5, 'name' => 'Scholarships', 'description' => 'Share and discuss scholarship opportunities', 'post_count' => 534),
            array('id' => 6, 'name' => 'Academics', 'description' => 'Help with subjects, projects, and assignments', 'post_count' => 987),
            array('id' => 7, 'name' => 'Technology & Coding', 'description' => 'Programming, tech discussions, and hackathons', 'post_count' => 1102),
            array('id' => 8, 'name' => 'General Discussion', 'description' => 'Everything else', 'post_count' => 765)
        );

        return $this->success($categories);
    }

    /**
     * Get forum posts
     */
    public function get_posts($category_id = null, $limit = 20, $offset = 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_forum_posts';

        if ($category_id) {
            $posts = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE category_id = %d ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $category_id,
                $limit,
                $offset
            ));
        } else {
            $posts = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $limit,
                $offset
            ));
        }

        return $this->success($posts);
    }

    /**
     * Create forum post
     */
    public function create_post($user_id, $post_data) {
        global $wpdb;

        $required = array('category_id', 'title', 'content');
        if (!$this->validate_required($post_data, $required)) {
            return $this->error('Missing required fields');
        }

        $table_name = $wpdb->prefix . 'ss_forum_posts';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'category_id' => intval($post_data['category_id']),
                'title' => sanitize_text_field($post_data['title']),
                'content' => wp_kses_post($post_data['content']),
                'tags' => isset($post_data['tags']) ? sanitize_text_field($post_data['tags']) : '',
                'views' => 0,
                'likes' => 0,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s', '%d', '%d', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to create post');
        }

        return $this->success(array(
            'post_id' => $wpdb->insert_id,
            'message' => 'Post created successfully'
        ));
    }

    /**
     * Get post replies
     */
    public function get_replies($post_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_forum_replies';

        $replies = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE post_id = %d ORDER BY created_at ASC",
            $post_id
        ));

        return $this->success($replies);
    }

    /**
     * Create reply
     */
    public function create_reply($user_id, $post_id, $content) {
        global $wpdb;

        if (empty($content)) {
            return $this->error('Reply content is required');
        }

        $table_name = $wpdb->prefix . 'ss_forum_replies';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'user_id' => $user_id,
                'content' => wp_kses_post($content),
                'likes' => 0,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%d', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to create reply');
        }

        // Update post reply count
        $posts_table = $wpdb->prefix . 'ss_forum_posts';
        $wpdb->query($wpdb->prepare(
            "UPDATE $posts_table SET reply_count = reply_count + 1 WHERE id = %d",
            $post_id
        ));

        return $this->success(array(
            'reply_id' => $wpdb->insert_id,
            'message' => 'Reply posted successfully'
        ));
    }

    /**
     * Search forum posts
     */
    public function search_posts($query) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_forum_posts';
        $search_term = '%' . $wpdb->esc_like($query) . '%';

        $posts = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE title LIKE %s OR content LIKE %s OR tags LIKE %s ORDER BY created_at DESC LIMIT 50",
            $search_term,
            $search_term,
            $search_term
        ));

        return $this->success($posts);
    }

    /**
     * Like/Unlike post
     */
    public function toggle_like($user_id, $post_id) {
        global $wpdb;

        $likes_table = $wpdb->prefix . 'ss_forum_likes';

        // Check if already liked
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $likes_table WHERE user_id = %d AND post_id = %d",
            $user_id,
            $post_id
        ));

        if ($existing) {
            // Unlike
            $wpdb->delete($likes_table, array('user_id' => $user_id, 'post_id' => $post_id));

            $posts_table = $wpdb->prefix . 'ss_forum_posts';
            $wpdb->query($wpdb->prepare(
                "UPDATE $posts_table SET likes = likes - 1 WHERE id = %d",
                $post_id
            ));

            return $this->success(array('action' => 'unliked'));
        } else {
            // Like
            $wpdb->insert($likes_table, array('user_id' => $user_id, 'post_id' => $post_id));

            $posts_table = $wpdb->prefix . 'ss_forum_posts';
            $wpdb->query($wpdb->prepare(
                "UPDATE $posts_table SET likes = likes + 1 WHERE id = %d",
                $post_id
            ));

            return $this->success(array('action' => 'liked'));
        }
    }
}
