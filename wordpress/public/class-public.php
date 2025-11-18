<?php
/**
 * Public-facing functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class Student_Services_Public {

    public static function init() {
        // Add any public hooks here
        add_action('wp_footer', array(__CLASS__, 'add_inline_scripts'));
    }

    public static function add_inline_scripts() {
        if (!is_user_logged_in()) {
            return;
        }
        ?>
        <script>
        // Example: Add event listener for course search
        jQuery(document).ready(function($) {
            $('#ss-course-search-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '<?php echo rest_url('student-services/v1/courses'); ?>',
                    method: 'GET',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                    },
                    data: {
                        keyword: $('#ss-course-keyword').val(),
                        semester: $('#ss-course-semester').val()
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            var html = '<table class="ss-table"><thead><tr><th>Code</th><th>Title</th><th>Credits</th></tr></thead><tbody>';
                            response.data.forEach(function(course) {
                                html += '<tr><td>' + course.course_code + '</td><td>' + course.title + '</td><td>' + course.credits + '</td></tr>';
                            });
                            html += '</tbody></table>';
                            $('#ss-course-results').html(html);
                        }
                    }
                });
            });

            // Event registration
            $('.ss-register-event').on('click', function() {
                var eventId = $(this).data('event-id');

                $.ajax({
                    url: '<?php echo rest_url('student-services/v1/events/register'); ?>',
                    method: 'POST',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                    },
                    data: {
                        event_id: eventId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Successfully registered for event!');
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
}
