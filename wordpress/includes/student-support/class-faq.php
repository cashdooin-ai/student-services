<?php
/**
 * FAQ Service
 * Search FAQs about our services
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_FAQ extends Student_Services_Base_Service {

    /**
     * Get FAQ categories
     */
    public function get_categories() {
        $categories = array(
            array('id' => 1, 'name' => 'General', 'icon' => 'info'),
            array('id' => 2, 'name' => 'Admissions', 'icon' => 'school'),
            array('id' => 3, 'name' => 'Scholarships', 'icon' => 'money'),
            array('id' => 4, 'name' => 'Exams', 'icon' => 'assignment'),
            array('id' => 5, 'name' => 'Career Services', 'icon' => 'work'),
            array('id' => 6, 'name' => 'Financial Aid', 'icon' => 'account_balance'),
            array('id' => 7, 'name' => 'Technical Support', 'icon' => 'support')
        );

        return $this->success($categories);
    }

    /**
     * Get FAQs
     */
    public function get_faqs($category_id = null) {
        // Mock data - in production, this would query the database
        $faqs = array(
            // General
            array(
                'id' => 1,
                'category_id' => 1,
                'question' => 'What services do you provide?',
                'answer' => 'We provide comprehensive student services including college recommendations, entrance exam preparation, scholarship search, financial aid calculators, GPA calculators, admission counseling, mentorship programs, webinars, and much more.',
                'helpful_count' => 145
            ),
            array(
                'id' => 2,
                'category_id' => 1,
                'question' => 'Is this service free?',
                'answer' => 'Most of our services are completely free. Some premium features like personalized counseling packages may have fees, but we offer transparent pricing.',
                'helpful_count' => 89
            ),

            // Admissions
            array(
                'id' => 3,
                'category_id' => 2,
                'question' => 'How does the AI college recommendation work?',
                'answer' => 'Our AI analyzes your academic scores, location preferences, field of study, and budget to match you with the most suitable colleges. It considers factors like acceptance rates, placement statistics, and student reviews.',
                'helpful_count' => 234
            ),
            array(
                'id' => 4,
                'category_id' => 2,
                'question' => 'Can I get admission counseling?',
                'answer' => 'Yes! We have experienced counselors who can guide you through the admission process. You can book one-on-one sessions or choose from our counseling packages.',
                'helpful_count' => 167
            ),

            // Scholarships
            array(
                'id' => 5,
                'category_id' => 3,
                'question' => 'How can I find scholarships?',
                'answer' => 'Use our scholarship search tool to discover thousands of scholarships. You can filter by eligibility, amount, and deadline. Our AI can also provide personalized recommendations based on your profile.',
                'helpful_count' => 312
            ),
            array(
                'id' => 6,
                'category_id' => 3,
                'question' => 'Can I track my scholarship applications?',
                'answer' => 'Absolutely! Our platform allows you to track all your scholarship applications in one place, with status updates and deadline reminders.',
                'helpful_count' => 198
            ),

            // Exams
            array(
                'id' => 7,
                'category_id' => 4,
                'question' => 'Which entrance exams do you cover?',
                'answer' => 'We provide comprehensive preparation materials for JEE Main, NEET, GATE, CLAT, CUET, and other major entrance exams. Each exam has study plans, practice tests, and progress tracking.',
                'helpful_count' => 276
            ),
            array(
                'id' => 8,
                'category_id' => 4,
                'question' => 'Are practice tests available?',
                'answer' => 'Yes! We offer unlimited practice tests for all major entrance exams with detailed solutions and performance analytics.',
                'helpful_count' => 243
            ),

            // Career Services
            array(
                'id' => 9,
                'category_id' => 5,
                'question' => 'Can I connect with mentors?',
                'answer' => 'Yes! Our mentorship program connects you with experienced professionals and senior students who can guide your academic and career journey.',
                'helpful_count' => 189
            ),
            array(
                'id' => 10,
                'category_id' => 5,
                'question' => 'Do you offer interview preparation?',
                'answer' => 'We provide comprehensive interview preparation including practice questions, mock interviews, and expert tips for technical, HR, and behavioral interviews.',
                'helpful_count' => 156
            )
        );

        if ($category_id) {
            $faqs = array_filter($faqs, function($faq) use ($category_id) {
                return $faq['category_id'] == $category_id;
            });
        }

        return $this->success(array_values($faqs));
    }

    /**
     * Search FAQs
     */
    public function search_faqs($query) {
        global $wpdb;

        if (empty($query)) {
            return $this->error('Search query is required');
        }

        $table_name = $wpdb->prefix . 'ss_faqs';
        $search_term = '%' . $wpdb->esc_like($query) . '%';

        $faqs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE question LIKE %s OR answer LIKE %s ORDER BY helpful_count DESC LIMIT 20",
            $search_term,
            $search_term
        ));

        // If no results in database, search mock data
        if (empty($faqs)) {
            $all_faqs = $this->get_faqs()['data'];
            $faqs = array_filter($all_faqs, function($faq) use ($query) {
                return stripos($faq['question'], $query) !== false || stripos($faq['answer'], $query) !== false;
            });
        }

        return $this->success(array_values($faqs));
    }

    /**
     * Mark FAQ as helpful
     */
    public function mark_helpful($faq_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_faqs';

        $updated = $wpdb->query($wpdb->prepare(
            "UPDATE $table_name SET helpful_count = helpful_count + 1 WHERE id = %d",
            $faq_id
        ));

        if ($updated === false) {
            return $this->error('Failed to update');
        }

        return $this->success(array('message' => 'Thank you for your feedback!'));
    }

    /**
     * Submit new question
     */
    public function submit_question($user_id, $question_data) {
        global $wpdb;

        $required = array('question');
        if (!$this->validate_required($question_data, $required)) {
            return $this->error('Question is required');
        }

        $table_name = $wpdb->prefix . 'ss_faq_submissions';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'question' => sanitize_textarea_field($question_data['question']),
                'category_id' => isset($question_data['category_id']) ? intval($question_data['category_id']) : 1,
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%d', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to submit question');
        }

        return $this->success(array(
            'message' => 'Your question has been submitted. Our team will answer it soon!'
        ));
    }
}
