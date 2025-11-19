<?php
/**
 * Data Seeder
 * Seed realistic data for all services
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Data_Seeder {

    /**
     * Seed all data
     */
    public static function seed_all() {
        global $wpdb;

        $results = array();

        // Seed in order due to dependencies
        $results['webinars'] = self::seed_webinars();
        $results['forum_posts'] = self::seed_forum_posts();
        $results['faqs'] = self::seed_faqs();
        $results['calendar_events'] = self::seed_calendar_events();
        $results['scholarships'] = self::seed_scholarships_data();
        $results['accommodations'] = self::seed_accommodations_data();

        return $results;
    }

    /**
     * Seed Webinars
     */
    public static function seed_webinars() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ss_webinars';

        // Create webinars table if not exists
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            speaker varchar(255),
            speaker_designation varchar(255),
            date date,
            time time,
            duration_minutes int(11),
            category varchar(100),
            description text,
            max_participants int(11),
            registered_count int(11) DEFAULT 0,
            is_free tinyint(1) DEFAULT 1,
            meeting_link varchar(255),
            status varchar(20) DEFAULT 'upcoming',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $webinars = array(
            array(
                'title' => 'Cracking IIT JEE Advanced 2024',
                'speaker' => 'Prof. Anand Krishnan',
                'speaker_designation' => 'Former HOD, IIT Delhi',
                'date' => date('Y-m-d', strtotime('+5 days')),
                'time' => '18:00:00',
                'duration_minutes' => 90,
                'category' => 'Entrance Exams',
                'description' => 'Learn effective strategies and time management techniques for JEE Advanced. Topics include problem-solving approaches, important chapters, and last-minute preparation tips.',
                'max_participants' => 500,
                'registered_count' => 342,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'Resume Building for Campus Placements',
                'speaker' => 'Neha Gupta',
                'speaker_designation' => 'HR Manager, Amazon India',
                'date' => date('Y-m-d', strtotime('+3 days')),
                'time' => '17:00:00',
                'duration_minutes' => 60,
                'category' => 'Career Development',
                'description' => 'Learn how to create an ATS-friendly resume that stands out. Understand what recruiters look for and common mistakes to avoid.',
                'max_participants' => 300,
                'registered_count' => 145,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example2',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'Study Abroad: Application Process & Scholarships',
                'speaker' => 'Dr. Vikram Singh',
                'speaker_designation' => 'Education Consultant',
                'date' => date('Y-m-d', strtotime('+7 days')),
                'time' => '19:00:00',
                'duration_minutes' => 120,
                'category' => 'Study Abroad',
                'description' => 'Complete guide to applying for international universities. Learn about GRE/GMAT preparation, SOP writing, LORs, and scholarship opportunities.',
                'max_participants' => 400,
                'registered_count' => 278,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example3',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'GATE 2024 Preparation Strategy',
                'speaker' => 'Prof. Ramesh Kumar',
                'speaker_designation' => 'Professor, IIT Kharagpur',
                'date' => date('Y-m-d', strtotime('+10 days')),
                'time' => '16:00:00',
                'duration_minutes' => 90,
                'category' => 'Entrance Exams',
                'description' => 'Subject-wise preparation strategy and important topics for GATE 2024. Includes tips for numerical problems and time management.',
                'max_participants' => 600,
                'registered_count' => 423,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example4',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'Machine Learning Career Path',
                'speaker' => 'Arun Sharma',
                'speaker_designation' => 'ML Engineer, Google',
                'date' => date('Y-m-d', strtotime('+12 days')),
                'time' => '18:30:00',
                'duration_minutes' => 75,
                'category' => 'Career Development',
                'description' => 'Roadmap to becoming an ML engineer. Learn about essential skills, projects, and how to break into the field.',
                'max_participants' => 350,
                'registered_count' => 298,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example5',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'NEET 2024: Last Month Strategy',
                'speaker' => 'Dr. Priya Malhotra',
                'speaker_designation' => 'MBBS, AIIMS Delhi',
                'date' => date('Y-m-d', strtotime('+8 days')),
                'time' => '17:30:00',
                'duration_minutes' => 60,
                'category' => 'Entrance Exams',
                'description' => 'Revision strategies and important topics for NEET. Focus on Biology, Chemistry, and Physics high-weightage chapters.',
                'max_participants' => 500,
                'registered_count' => 467,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example6',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'Starting Your First Startup',
                'speaker' => 'Rohan Verma',
                'speaker_designation' => 'Founder, TechStart India',
                'date' => date('Y-m-d', strtotime('+15 days')),
                'time' => '19:00:00',
                'duration_minutes' => 90,
                'category' => 'Entrepreneurship',
                'description' => 'Learn the basics of starting a startup while in college. Funding options, building a team, and avoiding common pitfalls.',
                'max_participants' => 250,
                'registered_count' => 189,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example7',
                'status' => 'upcoming'
            ),
            array(
                'title' => 'CAT 2024 Preparation Masterclass',
                'speaker' => 'Amit Verma',
                'speaker_designation' => 'CAT 99.9%ile, IIM Ahmedabad Alumni',
                'date' => date('Y-m-d', strtotime('+6 days')),
                'time' => '18:00:00',
                'duration_minutes' => 90,
                'category' => 'Entrance Exams',
                'description' => 'Section-wise strategies for CAT. VARC, DILR, and QA preparation tips from a 99.9 percentiler.',
                'max_participants' => 400,
                'registered_count' => 356,
                'is_free' => 1,
                'meeting_link' => 'https://meet.google.com/example8',
                'status' => 'upcoming'
            )
        );

        $inserted = 0;
        foreach ($webinars as $webinar) {
            $result = $wpdb->insert($table_name, $webinar);
            if ($result) $inserted++;
        }

        return array('inserted' => $inserted, 'total' => count($webinars));
    }

    /**
     * Seed Forum Posts
     */
    public static function seed_forum_posts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ss_forum_posts';

        $posts = array(
            array(
                'user_id' => 1,
                'category_id' => 1, // Entrance Exams
                'title' => 'JEE Main 2024 - Best books for Physics?',
                'content' => 'Hi everyone! I\'m preparing for JEE Main 2024 and looking for recommendations for Physics books. Currently using HC Verma but looking for more practice problems. Any suggestions?',
                'tags' => 'JEE, Physics, Books',
                'views' => 245,
                'likes' => 12,
                'reply_count' => 8
            ),
            array(
                'user_id' => 1,
                'category_id' => 2, // College Life
                'title' => 'First Year at IIT - What to expect?',
                'content' => 'I just got admission to IIT Bombay for CSE. Can current students share their experience? What should I prepare for? Any tips for freshers?',
                'tags' => 'IIT, First Year, Tips',
                'views' => 532,
                'likes' => 45,
                'reply_count' => 23
            ),
            array(
                'user_id' => 1,
                'category_id' => 3, // Career Guidance
                'title' => 'Product Manager vs Software Engineer - Career path?',
                'content' => 'I\'m confused between becoming a PM or staying technical as an SWE. Currently in 3rd year CSE. Can someone share their experiences or insights?',
                'tags' => 'Career, PM, SWE',
                'views' => 418,
                'likes' => 28,
                'reply_count' => 15
            ),
            array(
                'user_id' => 1,
                'category_id' => 4, // Study Abroad
                'title' => 'GRE score for top US universities?',
                'content' => 'What GRE score is competitive for MS in CS at universities like Stanford, MIT, CMU? Also, how important are research papers vs GPA?',
                'tags' => 'GRE, MS, USA',
                'views' => 389,
                'likes' => 19,
                'reply_count' => 12
            ),
            array(
                'user_id' => 1,
                'category_id' => 5, // Scholarships
                'title' => 'Post-matric scholarship for SC students - Application process?',
                'content' => 'Can someone guide me through the post-matric scholarship application process? What documents are required and what\'s the timeline?',
                'tags' => 'Scholarship, SC, Post-matric',
                'views' => 267,
                'likes' => 34,
                'reply_count' => 18
            ),
            array(
                'user_id' => 1,
                'category_id' => 6, // Academics
                'title' => 'Data Structures project ideas for resume?',
                'content' => 'Looking for interesting DSA project ideas that would look good on resume. Already done basic implementations. Need intermediate-advanced level suggestions.',
                'tags' => 'DSA, Projects, Resume',
                'views' => 512,
                'likes' => 56,
                'reply_count' => 31
            ),
            array(
                'user_id' => 1,
                'category_id' => 7, // Technology
                'title' => 'Web Development roadmap for 2024?',
                'content' => 'Complete beginner here. What\'s the recommended path to learn web development in 2024? React or Angular? Which backend framework?',
                'tags' => 'WebDev, Roadmap, 2024',
                'views' => 678,
                'likes' => 67,
                'reply_count' => 42
            ),
            array(
                'user_id' => 1,
                'category_id' => 1, // Entrance Exams
                'title' => 'NEET Biology - High weightage topics?',
                'content' => 'With 2 months left for NEET, which Biology topics should I prioritize? Already done with basic concepts. Need to focus on high-scoring chapters.',
                'tags' => 'NEET, Biology, Strategy',
                'views' => 445,
                'likes' => 38,
                'reply_count' => 16
            ),
            array(
                'user_id' => 1,
                'category_id' => 3, // Career Guidance
                'title' => 'Internship vs Research in 2nd year?',
                'content' => 'Should I go for a summer internship or participate in research projects with professors? Planning to do MS later. What would be more beneficial?',
                'tags' => 'Internship, Research, Career',
                'views' => 356,
                'likes' => 29,
                'reply_count' => 14
            ),
            array(
                'user_id' => 1,
                'category_id' => 2, // College Life
                'title' => 'Hostel life - Tips for introverts?',
                'content' => 'Moving to hostel next month. I\'m quite introverted and worried about adjusting. Any tips from fellow introverts on managing hostel life?',
                'tags' => 'Hostel, Introvert, Tips',
                'views' => 234,
                'likes' => 42,
                'reply_count' => 27
            )
        );

        $inserted = 0;
        foreach ($posts as $post) {
            $result = $wpdb->insert($table_name, $post);
            if ($result) $inserted++;
        }

        return array('inserted' => $inserted, 'total' => count($posts));
    }

    /**
     * Seed FAQs
     */
    public static function seed_faqs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ss_faqs';

        $faqs = array(
            // General
            array('category_id' => 1, 'question' => 'What services do you provide?', 'answer' => 'We provide comprehensive student services including college recommendations, entrance exam preparation, scholarship search, financial aid calculators, GPA calculators, admission counseling, mentorship programs, webinars, document templates, accommodation finder, and much more.', 'helpful_count' => 145),
            array('category_id' => 1, 'question' => 'Is this service free?', 'answer' => 'Most of our services are completely free. Some premium features like personalized counseling packages and service requests may have fees (starting from ₹250), but we offer transparent pricing.', 'helpful_count' => 89),
            array('category_id' => 1, 'question' => 'How do I create an account?', 'answer' => 'Simply click on the "Sign Up" button and fill in your details. You can also sign up using your Google or Facebook account for quick registration.', 'helpful_count' => 76),

            // Admissions
            array('category_id' => 2, 'question' => 'How does the AI college recommendation work?', 'answer' => 'Our AI analyzes your academic scores, location preferences, field of study, budget, and career goals to match you with the most suitable colleges. It considers factors like acceptance rates, placement statistics, faculty quality, and student reviews to provide personalized recommendations.', 'helpful_count' => 234),
            array('category_id' => 2, 'question' => 'Can I get admission counseling?', 'answer' => 'Yes! We have experienced counselors who can guide you through the admission process. You can book one-on-one sessions or choose from our counseling packages (Basic, Premium, Complete). Sessions are available both online and offline.', 'helpful_count' => 167),
            array('category_id' => 2, 'question' => 'What is the eligibility calculator?', 'answer' => 'The eligibility calculator analyzes your academic scores (10th, 12th, entrance exams) and tells you which colleges you\'re eligible for. It compares your scores with minimum requirements and provides admission probability percentages.', 'helpful_count' => 198),

            // Scholarships
            array('category_id' => 3, 'question' => 'How can I find scholarships?', 'answer' => 'Use our scholarship search tool to discover thousands of scholarships worth crores. You can filter by eligibility, amount, deadline, and category. Our AI can also provide personalized recommendations based on your academic profile, financial situation, and achievements.', 'helpful_count' => 312),
            array('category_id' => 3, 'question' => 'Can I track my scholarship applications?', 'answer' => 'Absolutely! Our platform allows you to track all your scholarship applications in one place, with status updates, deadline reminders, and notification alerts. You can also save scholarships for later reference.', 'helpful_count' => 198),
            array('category_id' => 3, 'question' => 'What documents are needed for scholarship applications?', 'answer' => 'Common documents include: Academic transcripts, Income certificate, Caste certificate (if applicable), Aadhaar card, Bank details, Passport-size photographs, and Admission proof. Specific requirements vary by scholarship.', 'helpful_count' => 156),

            // Exams
            array('category_id' => 4, 'question' => 'Which entrance exams do you cover?', 'answer' => 'We provide comprehensive preparation materials for JEE Main, JEE Advanced, NEET, GATE, CAT, CLAT, CUET, and other major entrance exams. Each exam has detailed study plans, practice tests, mock exams, and progress tracking.', 'helpful_count' => 276),
            array('category_id' => 4, 'question' => 'Are practice tests available?', 'answer' => 'Yes! We offer unlimited practice tests for all major entrance exams with detailed solutions, performance analytics, and peer comparison. Tests are designed by subject experts and follow the latest exam patterns.', 'helpful_count' => 243),
            array('category_id' => 4, 'question' => 'How do I access study materials?', 'answer' => 'After selecting your target exam, you can access study materials from the dashboard. Materials include chapter-wise notes, video lectures, formula sheets, previous year papers, and topic-wise practice questions.', 'helpful_count' => 189),

            // Career Services
            array('category_id' => 5, 'question' => 'Can I connect with mentors?', 'answer' => 'Yes! Our mentorship program connects you with experienced professionals and senior students from top colleges/companies. Mentors provide guidance on academics, career planning, skill development, and interview preparation. Most mentorship sessions are free.', 'helpful_count' => 189),
            array('category_id' => 5, 'question' => 'Do you offer interview preparation?', 'answer' => 'We provide comprehensive interview preparation including practice questions (technical, HR, behavioral, case study), mock interviews with feedback, resume review, and expert tips. You can also book one-on-one mock interview sessions.', 'helpful_count' => 156),
            array('category_id' => 5, 'question' => 'How can I improve my resume?', 'answer' => 'Use our resume builder to create ATS-friendly resumes. We also offer professional resume review services (₹250) where experts provide detailed feedback and suggestions for improvement.', 'helpful_count' => 178),

            // Financial Aid
            array('category_id' => 6, 'question' => 'How does the EMI calculator work?', 'answer' => 'Enter your loan amount, interest rate, and tenure to calculate monthly EMI. The calculator also shows total interest payable, amortization schedule, and allows you to compare different loan schemes side-by-side.', 'helpful_count' => 134),
            array('category_id' => 6, 'question' => 'What is EFC in financial aid?', 'answer' => 'EFC (Expected Family Contribution) is the amount your family is expected to contribute toward education costs. Our calculator estimates EFC based on family income, assets, family size, and other factors to help you understand your financial need.', 'helpful_count' => 98),
            array('category_id' => 6, 'question' => 'Can I compare college costs?', 'answer' => 'Yes! Use our college cost comparison tool to compare total cost of attendance (tuition, fees, accommodation, living expenses) across multiple colleges. The tool also calculates ROI and payback period based on average salaries.', 'helpful_count' => 167),

            // Technical Support
            array('category_id' => 7, 'question' => 'I forgot my password. How do I reset it?', 'answer' => 'Click on "Forgot Password" on the login page. Enter your registered email address and you\'ll receive a password reset link. The link is valid for 24 hours.', 'helpful_count' => 45),
            array('category_id' => 7, 'question' => 'Can I access the platform on mobile?', 'answer' => 'Yes! Our platform is fully mobile-responsive and works on all devices. We also have Android and iOS apps available for download (coming soon).', 'helpful_count' => 92),
            array('category_id' => 7, 'question' => 'How do I report a bug or issue?', 'answer' => 'You can report bugs through the "Support" section or email us at support@studentservices.com. Please include screenshots and steps to reproduce the issue for faster resolution.', 'helpful_count' => 34)
        );

        $inserted = 0;
        foreach ($faqs as $faq) {
            $result = $wpdb->insert($table_name, $faq);
            if ($result) $inserted++;
        }

        return array('inserted' => $inserted, 'total' => count($faqs));
    }

    /**
     * Seed Calendar Events
     */
    public static function seed_calendar_events() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ss_calendar_events';

        $events = array(
            array('title' => 'JEE Main 2024 Registration Opens', 'event_date' => date('Y-m-d', strtotime('+10 days')), 'event_time' => NULL, 'event_type' => 'deadline', 'category' => 'Entrance Exams', 'description' => 'Last date to register for JEE Main January session', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'NEET 2024 Application Deadline', 'event_date' => date('Y-m-d', strtotime('+20 days')), 'event_time' => NULL, 'event_type' => 'deadline', 'category' => 'Entrance Exams', 'description' => 'Last date to submit NEET application form', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'Mid-Semester Exams Begin', 'event_date' => date('Y-m-d', strtotime('+15 days')), 'event_time' => '09:00:00', 'event_type' => 'exam', 'category' => 'Academics', 'description' => 'Mid-semester examinations for all programs', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'Tech Fest 2024', 'event_date' => date('Y-m-d', strtotime('+25 days')), 'event_time' => '10:00:00', 'event_type' => 'event', 'category' => 'Campus Events', 'description' => 'Annual technical festival with competitions and workshops', 'is_important' => 0, 'reminder_enabled' => 0),
            array('title' => 'Scholarship Application Deadline', 'event_date' => date('Y-m-d', strtotime('+30 days')), 'event_time' => NULL, 'event_type' => 'deadline', 'category' => 'Financial Aid', 'description' => 'Last date for merit scholarship applications', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'Summer Internship Applications Open', 'event_date' => date('Y-m-d', strtotime('+35 days')), 'event_time' => NULL, 'event_type' => 'opportunity', 'category' => 'Career', 'description' => 'Companies start accepting summer internship applications', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'GATE 2024 Exam', 'event_date' => date('Y-m-d', strtotime('+40 days')), 'event_time' => '09:00:00', 'event_type' => 'exam', 'category' => 'Entrance Exams', 'description' => 'GATE examination day - All the best!', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'CAT 2024 Registration Begins', 'event_date' => date('Y-m-d', strtotime('+45 days')), 'event_time' => NULL, 'event_type' => 'registration', 'category' => 'Entrance Exams', 'description' => 'CAT registration portal opens', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'Placement Drive - Amazon', 'event_date' => date('Y-m-d', strtotime('+50 days')), 'event_time' => '14:00:00', 'event_type' => 'opportunity', 'category' => 'Career', 'description' => 'Amazon campus recruitment drive for SDE roles', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'Cultural Fest - Rendezvous', 'event_date' => date('Y-m-d', strtotime('+55 days')), 'event_time' => '10:00:00', 'event_type' => 'event', 'category' => 'Campus Events', 'description' => 'Three-day cultural extravaganza with celebrity performances', 'is_important' => 0, 'reminder_enabled' => 0),
            array('title' => 'Last Date: Semester Fee Payment', 'event_date' => date('Y-m-d', strtotime('+12 days')), 'event_time' => NULL, 'event_type' => 'deadline', 'category' => 'Academics', 'description' => 'Late fee will be applicable after this date', 'is_important' => 1, 'reminder_enabled' => 1),
            array('title' => 'CUET 2024 Application Opens', 'event_date' => date('Y-m-d', strtotime('+18 days')), 'event_time' => NULL, 'event_type' => 'registration', 'category' => 'Entrance Exams', 'description' => 'Common University Entrance Test registration begins', 'is_important' => 1, 'reminder_enabled' => 1)
        );

        $inserted = 0;
        foreach ($events as $event) {
            $result = $wpdb->insert($table_name, $event);
            if ($result) $inserted++;
        }

        return array('inserted' => $inserted, 'total' => count($events));
    }

    /**
     * Seed Scholarships Data
     */
    public static function seed_scholarships_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ss_scholarships_master';

        // Create scholarships master table if not exists
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            provider varchar(255),
            amount decimal(12,2),
            type varchar(100),
            eligibility text,
            deadline date,
            description text,
            benefits text,
            application_link varchar(255),
            applicants_count int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $scholarships = array(
            array(
                'name' => 'National Merit Scholarship',
                'provider' => 'Government of India',
                'amount' => 500000,
                'type' => 'Merit-based',
                'eligibility' => 'Min 85% in 12th, Family income < ₹8 lakh',
                'deadline' => date('Y-m-d', strtotime('+30 days')),
                'description' => 'Full tuition scholarship for meritorious students pursuing undergraduate programs in engineering, medicine, or science.',
                'benefits' => 'Full tuition fees, Books allowance (₹10,000/year), Hostel fees',
                'application_link' => 'https://scholarships.gov.in',
                'applicants_count' => 12450
            ),
            array(
                'name' => 'Women in STEM Scholarship',
                'provider' => 'Tech Foundation India',
                'amount' => 300000,
                'type' => 'Gender-specific',
                'eligibility' => 'Female students, Min 75% in 12th, STEM field',
                'deadline' => date('Y-m-d', strtotime('+45 days')),
                'description' => 'Empowering women pursuing careers in Science, Technology, Engineering, and Mathematics. Includes mentorship program with industry experts.',
                'benefits' => 'Tuition support, Mentorship program, Internship opportunities at partner companies',
                'application_link' => 'https://techfoundation.org/stem-scholarship',
                'applicants_count' => 5670
            ),
            array(
                'name' => 'SC/ST Post-Matric Scholarship',
                'provider' => 'Ministry of Social Justice and Empowerment',
                'amount' => 400000,
                'type' => 'Category-based',
                'eligibility' => 'SC/ST category, Family income < ₹2.5 lakh',
                'deadline' => date('Y-m-d', strtotime('+60 days')),
                'description' => 'Financial assistance for SC/ST students pursuing higher education. Renewable annually based on academic performance.',
                'benefits' => 'Full tuition and fees, Maintenance allowance (₹1,200/month), Study material allowance',
                'application_link' => 'https://scholarships.gov.in/postmatric',
                'applicants_count' => 23890
            ),
            array(
                'name' => 'Sports Excellence Scholarship',
                'provider' => 'Sports Authority of India',
                'amount' => 250000,
                'type' => 'Sports-based',
                'eligibility' => 'State/National level sports achievement, Min 60% in academics',
                'deadline' => date('Y-m-d', strtotime('+20 days')),
                'description' => 'Supporting student-athletes in balancing academics and sports excellence. Priority admission to SAI coaching centers.',
                'benefits' => 'Tuition fees, Sports equipment grant, Training support, Nutrition allowance',
                'application_link' => 'https://sportsauthorityofindia.nic.in/scholarships',
                'applicants_count' => 3240
            ),
            array(
                'name' => 'Need-Based Financial Aid',
                'provider' => 'Education Trust of India',
                'amount' => 200000,
                'type' => 'Need-based',
                'eligibility' => 'Family income < ₹3 lakh, Min 70% in academics',
                'deadline' => date('Y-m-d', strtotime('+50 days')),
                'description' => 'Financial assistance for economically disadvantaged students with good academic record. Preference for first-generation learners.',
                'benefits' => 'Partial tuition fees, Books and supplies, Transportation allowance',
                'application_link' => 'https://educationtrust.org.in/financial-aid',
                'applicants_count' => 18930
            ),
            array(
                'name' => 'OBC Merit Scholarship',
                'provider' => 'Ministry of Social Justice',
                'amount' => 350000,
                'type' => 'Category-based',
                'eligibility' => 'OBC (Non-creamy layer), Min 80% in 12th, Family income < ₹6 lakh',
                'deadline' => date('Y-m-d', strtotime('+40 days')),
                'description' => 'Merit-cum-means scholarship for OBC students. Covers all professional courses including engineering, medicine, and management.',
                'benefits' => 'Tuition fees, Hostel charges, Laptop/tablet grant (one-time)',
                'application_link' => 'https://scholarships.gov.in/obc-merit',
                'applicants_count' => 15678
            ),
            array(
                'name' => 'Minority Community Scholarship',
                'provider' => 'Ministry of Minority Affairs',
                'amount' => 300000,
                'type' => 'Community-based',
                'eligibility' => 'Minority community student, Min 70% in previous class, Family income < ₹5 lakh',
                'deadline' => date('Y-m-d', strtotime('+35 days')),
                'description' => 'Pre and post-matric scholarship for students from minority communities. Applicable for all streams.',
                'benefits' => 'Tuition fees, Maintenance allowance, Study tour support',
                'application_link' => 'https://scholarships.gov.in/minority',
                'applicants_count' => 9876
            ),
            array(
                'name' => 'Prime Minister Scholarship Scheme',
                'provider' => 'Government of India',
                'amount' => 600000,
                'type' => 'Merit-based',
                'eligibility' => 'Children/widows of Armed Forces/Paramilitary personnel, Min 75% in 12th',
                'deadline' => date('Y-m-d', strtotime('+25 days')),
                'description' => 'Special scholarship for wards of armed forces and paramilitary personnel. Covers technical and professional courses.',
                'benefits' => 'Full tuition, Hostel fees, Laptop, Annual book grant',
                'application_link' => 'https://ksb.gov.in',
                'applicants_count' => 4532
            ),
            array(
                'name' => 'State Merit Scholarship',
                'provider' => 'State Government',
                'amount' => 150000,
                'type' => 'Merit-based',
                'eligibility' => 'State board students, Top 5% in state exams',
                'deadline' => date('Y-m-d', strtotime('+55 days')),
                'description' => 'State government scholarship for meritorious students. Preference for students studying in government institutions.',
                'benefits' => 'Partial tuition fees, Books allowance, Exam fees',
                'application_link' => 'https://stateportal.gov.in/scholarships',
                'applicants_count' => 11234
            ),
            array(
                'name' => 'Single Girl Child Scholarship',
                'provider' => 'CBSE',
                'amount' => 100000,
                'type' => 'Gender-specific',
                'eligibility' => 'Single girl child, CBSE student, Min 60% in 10th/12th',
                'deadline' => date('Y-m-d', strtotime('+65 days')),
                'description' => 'Tuition fee waiver for single girl child studying in CBSE affiliated schools/colleges.',
                'benefits' => 'Tuition fee waiver (up to ₹1 lakh), Recognition certificate',
                'application_link' => 'https://cbse.nic.in/scholarships',
                'applicants_count' => 6789
            )
        );

        $inserted = 0;
        foreach ($scholarships as $scholarship) {
            $result = $wpdb->insert($table_name, $scholarship);
            if ($result) $inserted++;
        }

        return array('inserted' => $inserted, 'total' => count($scholarships));
    }

    /**
     * Seed Accommodations Data
     */
    public static function seed_accommodations_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ss_accommodations';

        // Create accommodations table if not exists
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            type varchar(50),
            gender varchar(20),
            location varchar(255),
            distance_from_college varchar(50),
            rent_per_month decimal(10,2),
            sharing_type varchar(50),
            facilities text,
            available_from date,
            verified tinyint(1) DEFAULT 0,
            rating decimal(3,2),
            reviews_count int(11) DEFAULT 0,
            contact_person varchar(255),
            contact_number varchar(20),
            featured tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $accommodations = array(
            array(
                'name' => 'Green Valley Boys Hostel',
                'type' => 'Hostel',
                'gender' => 'Boys',
                'location' => 'Sector 15, Near IIT Delhi',
                'distance_from_college' => '500m',
                'rent_per_month' => 8000,
                'sharing_type' => 'Triple Sharing',
                'facilities' => 'WiFi, Mess, Laundry, AC, Study Room, Security, 24/7 Water',
                'available_from' => date('Y-m-d', strtotime('+15 days')),
                'verified' => 1,
                'rating' => 4.5,
                'reviews_count' => 45,
                'contact_person' => 'Mr. Sharma',
                'contact_number' => '+91 98765 43210',
                'featured' => 1
            ),
            array(
                'name' => 'Sunrise PG for Girls',
                'type' => 'PG',
                'gender' => 'Girls',
                'location' => 'Malviya Nagar, Near DU South Campus',
                'distance_from_college' => '1.2km',
                'rent_per_month' => 12000,
                'sharing_type' => 'Double Sharing',
                'facilities' => 'WiFi, 3 Meals, AC, Geyser, Laundry, 24/7 Security, CCTV, Power Backup',
                'available_from' => date('Y-m-d'),
                'verified' => 1,
                'rating' => 4.7,
                'reviews_count' => 32,
                'contact_person' => 'Mrs. Verma',
                'contact_number' => '+91 98765 43211',
                'featured' => 1
            ),
            array(
                'name' => 'Student Apartments',
                'type' => 'Flat',
                'gender' => 'Co-ed',
                'location' => 'Kalkaji, Near NIFT',
                'distance_from_college' => '800m',
                'rent_per_month' => 15000,
                'sharing_type' => '2 BHK',
                'facilities' => 'WiFi, Fully Furnished, Kitchen, Parking, Power Backup, Gym',
                'available_from' => date('Y-m-d', strtotime('+7 days')),
                'verified' => 1,
                'rating' => 4.3,
                'reviews_count' => 18,
                'contact_person' => 'Mr. Kumar',
                'contact_number' => '+91 98765 43212',
                'featured' => 0
            ),
            array(
                'name' => 'Metro View Hostel',
                'type' => 'Hostel',
                'gender' => 'Boys',
                'location' => 'Rajouri Garden, Near DTU',
                'distance_from_college' => '300m',
                'rent_per_month' => 7000,
                'sharing_type' => 'Four Sharing',
                'facilities' => 'WiFi, Mess, Common Room, Gym, Study Room, Laundry',
                'available_from' => date('Y-m-d', strtotime('+20 days')),
                'verified' => 1,
                'rating' => 4.2,
                'reviews_count' => 56,
                'contact_person' => 'Mr. Gupta',
                'contact_number' => '+91 98765 43213',
                'featured' => 0
            ),
            array(
                'name' => 'Elite PG & Hostel',
                'type' => 'PG',
                'gender' => 'Girls',
                'location' => 'Satya Niketan, Near Venkateshwara College',
                'distance_from_college' => '400m',
                'rent_per_month' => 10000,
                'sharing_type' => 'Triple Sharing',
                'facilities' => 'WiFi, 2 Meals, AC, Laundry, Warden, CCTV, Geyser',
                'available_from' => date('Y-m-d'),
                'verified' => 1,
                'rating' => 4.6,
                'reviews_count' => 41,
                'contact_person' => 'Mrs. Singh',
                'contact_number' => '+91 98765 43214',
                'featured' => 1
            ),
            array(
                'name' => 'Campus Heights PG',
                'type' => 'PG',
                'gender' => 'Boys',
                'location' => 'Mukherjee Nagar, Near North Campus DU',
                'distance_from_college' => '600m',
                'rent_per_month' => 9000,
                'sharing_type' => 'Double Sharing',
                'facilities' => 'WiFi, 3 Meals, AC, Study Table, Almirah, Laundry, Security',
                'available_from' => date('Y-m-d', strtotime('+10 days')),
                'verified' => 1,
                'rating' => 4.4,
                'reviews_count' => 28,
                'contact_person' => 'Mr. Patel',
                'contact_number' => '+91 98765 43215',
                'featured' => 0
            ),
            array(
                'name' => 'Comfort Living Hostel',
                'type' => 'Hostel',
                'gender' => 'Co-ed',
                'location' => 'Vasant Kunj, Near IITD Extension',
                'distance_from_college' => '2km',
                'rent_per_month' => 11000,
                'sharing_type' => 'Single Room',
                'facilities' => 'WiFi, Mess, AC, Attached Bathroom, Study Room, Gym, CCTV',
                'available_from' => date('Y-m-d', strtotime('+5 days')),
                'verified' => 1,
                'rating' => 4.8,
                'reviews_count' => 23,
                'contact_person' => 'Ms. Reddy',
                'contact_number' => '+91 98765 43216',
                'featured' => 1
            ),
            array(
                'name' => 'Budget Stay PG',
                'type' => 'PG',
                'gender' => 'Boys',
                'location' => 'GTB Nagar, Near North Campus',
                'distance_from_college' => '700m',
                'rent_per_month' => 6500,
                'sharing_type' => 'Triple Sharing',
                'facilities' => 'WiFi, 2 Meals, Geyser, Laundry, Study Room',
                'available_from' => date('Y-m-d'),
                'verified' => 1,
                'rating' => 4.0,
                'reviews_count' => 34,
                'contact_person' => 'Mr. Yadav',
                'contact_number' => '+91 98765 43217',
                'featured' => 0
            )
        );

        $inserted = 0;
        foreach ($accommodations as $accommodation) {
            $result = $wpdb->insert($table_name, $accommodation);
            if ($result) $inserted++;
        }

        return array('inserted' => $inserted, 'total' => count($accommodations));
    }

    /**
     * Clear all seeded data
     */
    public static function clear_all() {
        global $wpdb;

        $tables = array(
            'ss_webinars',
            'ss_forum_posts',
            'ss_faqs',
            'ss_calendar_events',
            'ss_scholarships_master',
            'ss_accommodations'
        );

        $results = array();
        foreach ($tables as $table) {
            $table_name = $wpdb->prefix . $table;
            $deleted = $wpdb->query("TRUNCATE TABLE $table_name");
            $results[$table] = $deleted !== false ? 'cleared' : 'error';
        }

        return $results;
    }
}
