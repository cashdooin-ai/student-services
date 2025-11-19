<?php
/**
 * Document Templates Service
 * Create professional documents for college applications
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Document_Templates extends Student_Services_Base_Service {

    /**
     * Get available templates
     */
    public function get_templates() {
        $templates = array(
            array(
                'id' => 1,
                'name' => 'Statement of Purpose (SOP)',
                'category' => 'Application Documents',
                'description' => 'Professional SOP template for college/university applications',
                'fields' => array('name', 'program', 'university', 'academic_background', 'career_goals', 'why_program', 'why_university'),
                'sample_available' => true,
                'downloads' => 12450
            ),
            array(
                'id' => 2,
                'name' => 'Letter of Recommendation Request',
                'category' => 'Application Documents',
                'description' => 'Template to request LOR from professors/mentors',
                'fields' => array('student_name', 'professor_name', 'course_name', 'relationship', 'purpose'),
                'sample_available' => true,
                'downloads' => 8920
            ),
            array(
                'id' => 3,
                'name' => 'Student Resume',
                'category' => 'Career Documents',
                'description' => 'ATS-friendly resume template for students',
                'fields' => array('name', 'email', 'phone', 'education', 'experience', 'skills', 'projects', 'achievements'),
                'sample_available' => true,
                'downloads' => 15680
            ),
            array(
                'id' => 4,
                'name' => 'Cover Letter',
                'category' => 'Career Documents',
                'description' => 'Professional cover letter template for internships/jobs',
                'fields' => array('name', 'company', 'position', 'introduction', 'body', 'conclusion'),
                'sample_available' => true,
                'downloads' => 10250
            ),
            array(
                'id' => 5,
                'name' => 'Scholarship Application Essay',
                'category' => 'Scholarship Documents',
                'description' => 'Essay template for scholarship applications',
                'fields' => array('name', 'scholarship_name', 'academic_achievements', 'financial_need', 'future_goals'),
                'sample_available' => true,
                'downloads' => 9340
            ),
            array(
                'id' => 6,
                'name' => 'Leave Application',
                'category' => 'Academic Documents',
                'description' => 'Formal leave application template',
                'fields' => array('name', 'roll_number', 'class', 'reason', 'from_date', 'to_date'),
                'sample_available' => true,
                'downloads' => 7120
            ),
            array(
                'id' => 7,
                'name' => 'Internship Completion Certificate',
                'category' => 'Career Documents',
                'description' => 'Template for internship completion certificate',
                'fields' => array('intern_name', 'company', 'duration', 'role', 'performance'),
                'sample_available' => true,
                'downloads' => 5890
            ),
            array(
                'id' => 8,
                'name' => 'Research Proposal',
                'category' => 'Academic Documents',
                'description' => 'Template for academic research proposals',
                'fields' => array('title', 'researcher_name', 'introduction', 'objectives', 'methodology', 'timeline'),
                'sample_available' => true,
                'downloads' => 4670
            )
        );

        return $this->success($templates);
    }

    /**
     * Get template by ID
     */
    public function get_template($template_id) {
        $templates = $this->get_templates()['data'];

        $template = array_values(array_filter($templates, function($t) use ($template_id) {
            return $t['id'] == $template_id;
        }))[0] ?? null;

        if (!$template) {
            return $this->error('Template not found');
        }

        // Add template content
        $template['content'] = $this->get_template_content($template_id);

        return $this->success($template);
    }

    /**
     * Get template content
     */
    private function get_template_content($template_id) {
        $templates_content = array(
            1 => "STATEMENT OF PURPOSE\n\n[Your Name]\n[Program Name]\n[University Name]\n\nDear Admissions Committee,\n\nI am writing to express my strong interest in pursuing [Program Name] at [University Name]. With a solid foundation in [Your Field] and a deep passion for [Specific Interest], I am confident that this program will provide me with the knowledge and skills necessary to achieve my career goals.\n\nACADEMIC BACKGROUND\n[Describe your academic journey, relevant coursework, and achievements]\n\nWHY THIS PROGRAM\n[Explain why you're interested in this specific program and how it aligns with your goals]\n\nWHY THIS UNIVERSITY\n[Explain what attracts you to this particular university]\n\nCAREER GOALS\n[Describe your short-term and long-term career objectives]\n\nCONCLUSION\nI am excited about the prospect of contributing to and learning from the diverse academic community at [University Name]. I look forward to the opportunity to pursue my academic and professional goals at your esteemed institution.\n\nThank you for considering my application.\n\nSincerely,\n[Your Name]",

            2 => "LETTER OF RECOMMENDATION REQUEST\n\nDear Professor [Professor Name],\n\nI hope this email finds you well. I am writing to request a letter of recommendation for my application to [Program/University/Opportunity].\n\nAs you may recall, I was a student in your [Course Name] course during [Semester/Year], where I [mention specific achievements or experiences]. Your guidance and mentorship during that time significantly influenced my academic and professional development.\n\nThe application deadline is [Date], and the letter should address [specific requirements if any]. I would be happy to provide you with my resume, transcript, and any other materials that might be helpful in writing the letter.\n\nI understand that writing a letter of recommendation requires time and effort, and I truly appreciate your consideration of my request.\n\nThank you very much for your time and support.\n\nBest regards,\n[Your Name]\n[Contact Information]",

            3 => "[Your Name]\n[Email] | [Phone] | [LinkedIn] | [Portfolio]\n\nEDUCATION\n[Degree] in [Major] - [Expected Graduation]\n[University Name], [Location]\nGPA: [Your GPA] | Relevant Coursework: [List courses]\n\nEXPERIENCE\n[Job Title] - [Company Name] | [Dates]\n• [Achievement/Responsibility 1]\n• [Achievement/Responsibility 2]\n• [Achievement/Responsibility 3]\n\nPROJECTS\n[Project Name] | [Technologies Used]\n• [Description and impact]\n• [Key achievement]\n\nSKILLS\nTechnical: [List technical skills]\nLanguages: [Programming languages]\nTools: [Software/Tools]\n\nACHIEVEMENTS\n• [Achievement 1]\n• [Achievement 2]\n• [Achievement 3]"
        );

        return $templates_content[$template_id] ?? 'Template content not available.';
    }

    /**
     * Generate document from template
     */
    public function generate_document($user_id, $template_id, $data) {
        global $wpdb;

        $template = $this->get_template($template_id);

        if (!$template['success']) {
            return $template;
        }

        $template_data = $template['data'];
        $content = $template_data['content'];

        // Replace placeholders with user data
        foreach ($data as $key => $value) {
            $placeholder = '[' . str_replace('_', ' ', ucwords($key, '_')) . ']';
            $content = str_replace($placeholder, $value, $content);
        }

        // Save generated document
        $table_name = $wpdb->prefix . 'ss_generated_documents';

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'template_id' => $template_id,
                'template_name' => $template_data['name'],
                'content' => $content,
                'data' => json_encode($data),
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            return $this->error('Failed to generate document');
        }

        return $this->success(array(
            'document_id' => $wpdb->insert_id,
            'content' => $content,
            'message' => 'Document generated successfully'
        ));
    }

    /**
     * Get user's generated documents
     */
    public function get_my_documents($user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_generated_documents';

        $documents = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ));

        return $this->success($documents);
    }

    /**
     * Update generated document
     */
    public function update_document($document_id, $user_id, $content) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_generated_documents';

        $updated = $wpdb->update(
            $table_name,
            array(
                'content' => $content,
                'updated_at' => current_time('mysql')
            ),
            array(
                'id' => $document_id,
                'user_id' => $user_id
            ),
            array('%s', '%s'),
            array('%d', '%d')
        );

        if ($updated === false) {
            return $this->error('Failed to update document');
        }

        return $this->success(array('message' => 'Document updated successfully'));
    }

    /**
     * Download document as PDF
     */
    public function download_document($document_id, $user_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ss_generated_documents';

        $document = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
            $document_id,
            $user_id
        ));

        if (!$document) {
            return $this->error('Document not found');
        }

        // In production, generate PDF using library like TCPDF or FPDF
        // For now, return document data

        return $this->success(array(
            'document' => $document,
            'download_url' => '#' // Would be actual PDF URL
        ));
    }

    /**
     * Search templates
     */
    public function search_templates($query) {
        $all_templates = $this->get_templates()['data'];

        $results = array_filter($all_templates, function($template) use ($query) {
            return stripos($template['name'], $query) !== false ||
                   stripos($template['description'], $query) !== false ||
                   stripos($template['category'], $query) !== false;
        });

        return $this->success(array_values($results));
    }
}
