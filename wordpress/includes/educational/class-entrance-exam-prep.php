<?php
/**
 * Entrance Exam Preparation Service
 * Resources, practice tests, and study plans for GATE, CLAT, CUET, NEET, JEE Main
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Entrance_Exam_Prep extends Student_Services_Base_Service {

    /**
     * Get available exams
     */
    public function get_available_exams() {
        $exams = array(
            array(
                'id' => 'jee-main',
                'name' => 'JEE Main',
                'full_name' => 'Joint Entrance Examination - Main',
                'category' => 'Engineering',
                'duration' => '3 hours',
                'total_marks' => 300,
                'sections' => array('Physics', 'Chemistry', 'Mathematics'),
                'practice_tests_available' => 25,
                'study_materials' => 150
            ),
            array(
                'id' => 'neet',
                'name' => 'NEET',
                'full_name' => 'National Eligibility cum Entrance Test',
                'category' => 'Medical',
                'duration' => '3 hours 20 minutes',
                'total_marks' => 720,
                'sections' => array('Physics', 'Chemistry', 'Biology'),
                'practice_tests_available' => 30,
                'study_materials' => 200
            ),
            array(
                'id' => 'gate',
                'name' => 'GATE',
                'full_name' => 'Graduate Aptitude Test in Engineering',
                'category' => 'Engineering (PG)',
                'duration' => '3 hours',
                'total_marks' => 100,
                'sections' => array('General Aptitude', 'Technical Subject'),
                'practice_tests_available' => 20,
                'study_materials' => 180
            ),
            array(
                'id' => 'clat',
                'name' => 'CLAT',
                'full_name' => 'Common Law Admission Test',
                'category' => 'Law',
                'duration' => '2 hours',
                'total_marks' => 150,
                'sections' => array('English', 'GK & Current Affairs', 'Legal Reasoning', 'Logical Reasoning', 'Quantitative Techniques'),
                'practice_tests_available' => 15,
                'study_materials' => 120
            ),
            array(
                'id' => 'cuet',
                'name' => 'CUET',
                'full_name' => 'Common University Entrance Test',
                'category' => 'University Admission',
                'duration' => '2 hours (per section)',
                'total_marks' => 'Varies by section',
                'sections' => array('Languages', 'Domain Subjects', 'General Test'),
                'practice_tests_available' => 18,
                'study_materials' => 140
            )
        );

        return $this->success($exams);
    }

    /**
     * Get study plan for exam
     */
    public function get_study_plan($exam_id, $preparation_months = 6) {
        $plans = array(
            'jee-main' => array(
                'total_topics' => 90,
                'weekly_hours' => 40,
                'phases' => array(
                    array(
                        'phase' => 'Foundation (Months 1-2)',
                        'focus' => 'Core concepts and fundamentals',
                        'topics' => array('Basic Mathematics', 'Physics Fundamentals', 'Chemistry Basics')
                    ),
                    array(
                        'phase' => 'Advanced (Months 3-4)',
                        'focus' => 'Advanced topics and problem solving',
                        'topics' => array('Calculus', 'Mechanics', 'Organic Chemistry')
                    ),
                    array(
                        'phase' => 'Practice (Months 5-6)',
                        'focus' => 'Mock tests and revision',
                        'topics' => array('Full syllabus mock tests', 'Weak area improvement')
                    )
                )
            ),
            'neet' => array(
                'total_topics' => 97,
                'weekly_hours' => 45,
                'phases' => array(
                    array(
                        'phase' => 'Foundation (Months 1-2)',
                        'focus' => 'NCERT and basic concepts',
                        'topics' => array('Cell Biology', 'Basic Physics', 'Inorganic Chemistry')
                    ),
                    array(
                        'phase' => 'Advanced (Months 3-4)',
                        'focus' => 'Advanced topics',
                        'topics' => array('Human Physiology', 'Optics', 'Organic Chemistry')
                    ),
                    array(
                        'phase' => 'Practice (Months 5-6)',
                        'focus' => 'Mock tests and revision',
                        'topics' => array('Full mock tests', 'Previous year papers')
                    )
                )
            )
        );

        $plan = isset($plans[$exam_id]) ? $plans[$exam_id] : $plans['jee-main'];

        return $this->success(array(
            'exam_id' => $exam_id,
            'preparation_months' => $preparation_months,
            'study_plan' => $plan,
            'daily_schedule' => $this->generate_daily_schedule($plan['weekly_hours'])
        ));
    }

    /**
     * Generate daily schedule
     */
    private function generate_daily_schedule($weekly_hours) {
        $daily_hours = round($weekly_hours / 7, 1);

        return array(
            'total_daily_hours' => $daily_hours,
            'schedule' => array(
                array('time' => '6:00 AM - 8:00 AM', 'activity' => 'Morning Study Session', 'duration' => '2 hours'),
                array('time' => '9:00 AM - 12:00 PM', 'activity' => 'Core Subject Study', 'duration' => '3 hours'),
                array('time' => '2:00 PM - 4:00 PM', 'activity' => 'Practice Problems', 'duration' => '2 hours'),
                array('time' => '7:00 PM - 9:00 PM', 'activity' => 'Revision & Mock Tests', 'duration' => '2 hours')
            )
        );
    }

    /**
     * Get practice tests
     */
    public function get_practice_tests($exam_id, $difficulty = 'all') {
        $permission = $this->check_permission(null);
        if (is_wp_error($permission) && $permission->get_error_code() !== 'not_logged_in') {
            return $permission;
        }

        $table = $this->get_table('practice_tests');
        $where = array('exam_id' => $exam_id);

        if ($difficulty !== 'all') {
            $where['difficulty'] = $difficulty;
        }

        $tests = $this->get_results('practice_tests', $where);

        if (empty($tests)) {
            // Return mock data if no tests in database
            $tests = $this->get_mock_practice_tests($exam_id);
        }

        return $this->success($tests);
    }

    /**
     * Get mock practice tests
     */
    private function get_mock_practice_tests($exam_id) {
        return array(
            array(
                'id' => 1,
                'title' => 'Full Length Mock Test 1',
                'exam_id' => $exam_id,
                'difficulty' => 'medium',
                'questions' => 90,
                'duration' => 180,
                'attempts' => 0,
                'avg_score' => 0
            ),
            array(
                'id' => 2,
                'title' => 'Topic-wise Test: Mathematics',
                'exam_id' => $exam_id,
                'difficulty' => 'easy',
                'questions' => 30,
                'duration' => 60,
                'attempts' => 0,
                'avg_score' => 0
            ),
            array(
                'id' => 3,
                'title' => 'Advanced Problem Set',
                'exam_id' => $exam_id,
                'difficulty' => 'hard',
                'questions' => 50,
                'duration' => 120,
                'attempts' => 0,
                'avg_score' => 0
            )
        );
    }

    /**
     * Submit practice test
     */
    public function submit_practice_test($user_id, $test_id, $answers) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        // Calculate score (mock)
        $total_questions = count($answers);
        $correct = rand(floor($total_questions * 0.6), $total_questions);
        $score = ($correct / $total_questions) * 100;

        // Save attempt
        $attempt_id = $this->insert('test_attempts', array(
            'user_id' => $user_id,
            'test_id' => $test_id,
            'answers' => wp_json_encode($answers),
            'score' => $score,
            'correct_answers' => $correct,
            'total_questions' => $total_questions,
            'time_taken' => rand(60, 180),
            'attempt_date' => current_time('mysql')
        ));

        return $this->success(array(
            'attempt_id' => $attempt_id,
            'score' => round($score, 2),
            'correct' => $correct,
            'total' => $total_questions,
            'percentage' => round($score, 2),
            'rank' => 'Top 25%' // Mock rank
        ));
    }

    /**
     * Get study materials
     */
    public function get_study_materials($exam_id, $subject = null) {
        $materials = array(
            array(
                'id' => 1,
                'title' => 'Complete Mathematics Guide',
                'subject' => 'Mathematics',
                'type' => 'PDF',
                'pages' => 350,
                'downloads' => 15420,
                'rating' => 4.5
            ),
            array(
                'id' => 2,
                'title' => 'Physics Video Lectures',
                'subject' => 'Physics',
                'type' => 'Video',
                'duration' => '45 hours',
                'views' => 28500,
                'rating' => 4.7
            ),
            array(
                'id' => 3,
                'title' => 'Chemistry Formula Sheet',
                'subject' => 'Chemistry',
                'type' => 'PDF',
                'pages' => 50,
                'downloads' => 32100,
                'rating' => 4.8
            )
        );

        if ($subject) {
            $materials = array_filter($materials, function($m) use ($subject) {
                return $m['subject'] === $subject;
            });
        }

        return $this->success(array_values($materials));
    }

    /**
     * Track study progress
     */
    public function track_progress($user_id, $exam_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $table = $this->get_table('test_attempts');
        $sql = $this->wpdb->prepare(
            "SELECT
                COUNT(*) as total_attempts,
                AVG(score) as avg_score,
                MAX(score) as best_score,
                MIN(score) as lowest_score
            FROM $table
            WHERE user_id = %d",
            $user_id
        );

        $stats = $this->wpdb->get_row($sql);

        return $this->success(array(
            'total_tests_attempted' => $stats->total_attempts ?? 0,
            'average_score' => round($stats->avg_score ?? 0, 2),
            'best_score' => round($stats->best_score ?? 0, 2),
            'improvement' => '+5.2%', // Mock
            'study_hours' => rand(50, 200),
            'topics_completed' => rand(20, 60)
        ));
    }
}
