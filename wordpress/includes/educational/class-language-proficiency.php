<?php
/**
 * Language Proficiency Tests Service
 * Prepare for IELTS, TOEFL, PTE and other English proficiency tests
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Language_Proficiency extends Student_Services_Base_Service {

    /**
     * Get available language tests
     */
    public function get_available_tests() {
        $tests = array(
            array(
                'id' => 'ielts',
                'name' => 'IELTS',
                'full_name' => 'International English Language Testing System',
                'sections' => array('Listening', 'Reading', 'Writing', 'Speaking'),
                'duration' => '2 hours 45 minutes',
                'score_range' => '0-9 bands',
                'validity' => '2 years',
                'accepted_countries' => array('UK', 'Australia', 'Canada', 'New Zealand'),
                'practice_tests' => 20,
                'study_materials' => 85
            ),
            array(
                'id' => 'toefl',
                'name' => 'TOEFL',
                'full_name' => 'Test of English as a Foreign Language',
                'sections' => array('Reading', 'Listening', 'Speaking', 'Writing'),
                'duration' => '3 hours',
                'score_range' => '0-120',
                'validity' => '2 years',
                'accepted_countries' => array('USA', 'Canada', 'UK'),
                'practice_tests' => 18,
                'study_materials' => 75
            ),
            array(
                'id' => 'pte',
                'name' => 'PTE Academic',
                'full_name' => 'Pearson Test of English Academic',
                'sections' => array('Speaking & Writing', 'Reading', 'Listening'),
                'duration' => '2 hours',
                'score_range' => '10-90',
                'validity' => '2 years',
                'accepted_countries' => array('UK', 'Australia', 'New Zealand', 'USA'),
                'practice_tests' => 15,
                'study_materials' => 60
            ),
            array(
                'id' => 'duolingo',
                'name' => 'Duolingo English Test',
                'full_name' => 'Duolingo English Test',
                'sections' => array('Quick Setup', 'Adaptive Test', 'Video Interview', 'Writing Sample'),
                'duration' => '1 hour',
                'score_range' => '10-160',
                'validity' => '2 years',
                'accepted_countries' => array('USA', 'Canada', 'UK'),
                'practice_tests' => 10,
                'study_materials' => 40
            )
        );

        return $this->success($tests);
    }

    /**
     * Get study plan for language test
     */
    public function get_study_plan($test_id, $target_score, $preparation_weeks = 12) {
        $plans = array(
            'ielts' => array(
                'target_band' => $target_score,
                'weekly_hours' => 15,
                'sections' => array(
                    array(
                        'name' => 'Listening',
                        'current_band' => 6.0,
                        'target_band' => $target_score,
                        'hours_per_week' => 4,
                        'improvement_tips' => array(
                            'Practice with various accents',
                            'Listen to podcasts daily',
                            'Take notes while listening'
                        )
                    ),
                    array(
                        'name' => 'Reading',
                        'current_band' => 6.5,
                        'target_band' => $target_score,
                        'hours_per_week' => 4,
                        'improvement_tips' => array(
                            'Read academic articles',
                            'Practice skimming and scanning',
                            'Expand vocabulary'
                        )
                    ),
                    array(
                        'name' => 'Writing',
                        'current_band' => 5.5,
                        'target_band' => $target_score,
                        'hours_per_week' => 4,
                        'improvement_tips' => array(
                            'Practice essay structures',
                            'Get feedback on writing',
                            'Learn formal vocabulary'
                        )
                    ),
                    array(
                        'name' => 'Speaking',
                        'current_band' => 6.0,
                        'target_band' => $target_score,
                        'hours_per_week' => 3,
                        'improvement_tips' => array(
                            'Practice speaking daily',
                            'Record and review',
                            'Join conversation groups'
                        )
                    )
                )
            ),
            'toefl' => array(
                'target_score' => $target_score,
                'weekly_hours' => 15,
                'sections' => array(
                    array(
                        'name' => 'Reading',
                        'target_score' => $target_score / 4,
                        'hours_per_week' => 4
                    ),
                    array(
                        'name' => 'Listening',
                        'target_score' => $target_score / 4,
                        'hours_per_week' => 4
                    ),
                    array(
                        'name' => 'Speaking',
                        'target_score' => $target_score / 4,
                        'hours_per_week' => 4
                    ),
                    array(
                        'name' => 'Writing',
                        'target_score' => $target_score / 4,
                        'hours_per_week' => 3
                    )
                )
            )
        );

        $plan = isset($plans[$test_id]) ? $plans[$test_id] : $plans['ielts'];

        return $this->success(array(
            'test_id' => $test_id,
            'preparation_weeks' => $preparation_weeks,
            'target_score' => $target_score,
            'study_plan' => $plan,
            'weekly_schedule' => $this->generate_weekly_schedule($plan['weekly_hours'])
        ));
    }

    /**
     * Generate weekly schedule
     */
    private function generate_weekly_schedule($weekly_hours) {
        $daily_hours = round($weekly_hours / 7, 1);

        return array(
            'monday' => array('Listening Practice', 'Vocabulary Building'),
            'tuesday' => array('Reading Practice', 'Grammar Exercises'),
            'wednesday' => array('Writing Practice', 'Speaking Practice'),
            'thursday' => array('Mock Test', 'Error Analysis'),
            'friday' => array('Reading & Listening', 'Vocabulary Review'),
            'saturday' => array('Full Practice Test', 'Review Weak Areas'),
            'sunday' => array('Speaking Practice', 'Weekly Progress Review')
        );
    }

    /**
     * Get practice tests
     */
    public function get_practice_tests($test_id, $section = null) {
        $tests = array(
            array(
                'id' => 1,
                'test_id' => $test_id,
                'title' => 'Full Length Practice Test 1',
                'section' => 'all',
                'duration' => 180,
                'questions' => 100,
                'difficulty' => 'medium',
                'attempts' => 0
            ),
            array(
                'id' => 2,
                'test_id' => $test_id,
                'title' => 'Listening Section Practice',
                'section' => 'listening',
                'duration' => 40,
                'questions' => 40,
                'difficulty' => 'medium',
                'attempts' => 0
            ),
            array(
                'id' => 3,
                'test_id' => $test_id,
                'title' => 'Reading Comprehension Test',
                'section' => 'reading',
                'duration' => 60,
                'questions' => 40,
                'difficulty' => 'hard',
                'attempts' => 0
            )
        );

        if ($section) {
            $tests = array_filter($tests, function($t) use ($section) {
                return $t['section'] === $section || $t['section'] === 'all';
            });
        }

        return $this->success(array_values($tests));
    }

    /**
     * Submit practice test
     */
    public function submit_test($user_id, $test_id, $answers) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        // Mock scoring
        $total = count($answers);
        $correct = rand(floor($total * 0.65), floor($total * 0.95));
        $score = ($correct / $total) * 100;

        $this->insert('language_test_attempts', array(
            'user_id' => $user_id,
            'test_id' => $test_id,
            'score' => $score,
            'correct_answers' => $correct,
            'total_questions' => $total,
            'attempt_date' => current_time('mysql')
        ));

        return $this->success(array(
            'score' => round($score, 2),
            'correct' => $correct,
            'total' => $total,
            'band_score' => $this->calculate_band_score($score),
            'feedback' => $this->generate_feedback($score)
        ));
    }

    /**
     * Calculate band score
     */
    private function calculate_band_score($percentage) {
        if ($percentage >= 90) return 9.0;
        if ($percentage >= 85) return 8.5;
        if ($percentage >= 80) return 8.0;
        if ($percentage >= 75) return 7.5;
        if ($percentage >= 70) return 7.0;
        if ($percentage >= 65) return 6.5;
        if ($percentage >= 60) return 6.0;
        return 5.5;
    }

    /**
     * Generate feedback
     */
    private function generate_feedback($score) {
        if ($score >= 85) {
            return 'Excellent performance! You are well-prepared for the test.';
        } elseif ($score >= 70) {
            return 'Good progress! Focus on weak areas to improve further.';
        } elseif ($score >= 60) {
            return 'Average performance. More practice needed in all sections.';
        } else {
            return 'Needs improvement. Focus on fundamentals and practice regularly.';
        }
    }

    /**
     * Get vocabulary builder
     */
    public function get_vocabulary($level = 'intermediate', $limit = 50) {
        // Mock vocabulary data
        $vocab = array();
        for ($i = 1; $i <= $limit; $i++) {
            $vocab[] = array(
                'word' => 'Word' . $i,
                'definition' => 'Definition of word ' . $i,
                'example' => 'Example sentence using word ' . $i,
                'level' => $level,
                'frequency' => 'common'
            );
        }

        return $this->success($vocab);
    }

    /**
     * Track progress
     */
    public function get_progress($user_id, $test_id) {
        $permission = $this->check_permission($user_id);
        if (is_wp_error($permission)) {
            return $permission;
        }

        $table = $this->get_table('language_test_attempts');
        $sql = $this->wpdb->prepare(
            "SELECT
                COUNT(*) as total_attempts,
                AVG(score) as avg_score,
                MAX(score) as best_score
            FROM $table
            WHERE user_id = %d",
            $user_id
        );

        $stats = $this->wpdb->get_row($sql);

        return $this->success(array(
            'total_tests' => $stats->total_attempts ?? 0,
            'average_score' => round($stats->avg_score ?? 0, 2),
            'best_score' => round($stats->best_score ?? 0, 2),
            'estimated_band' => $this->calculate_band_score($stats->best_score ?? 0),
            'study_hours' => rand(20, 100),
            'vocabulary_learned' => rand(100, 500)
        ));
    }
}
