# Student Services WordPress Plugin

A comprehensive WordPress plugin providing various student services for college and university websites.

## Description

The Student Services plugin provides a complete solution for managing student-related services on your WordPress website. It includes features for academic management, financial services, housing, health services, career development, and more.

## Features

### 📚 Academic Services
- **Course Registration** - Search, enroll, drop courses
- **Grade Management** - View grades, calculate GPA
- **Academic Advising** - Schedule appointments, track degree progress
- **Library Services** - Search catalog, checkout resources

### 💰 Financial Services
- **Billing & Payments** - View balance, make payments, payment plans
- **Scholarships** - Search and apply for scholarships

### 🏠 Campus Life
- **Housing** - Applications, maintenance requests
- **Dining** - Meal plans, dining hall menus

### 🏥 Health & Wellness
- **Health Services** - Schedule appointments, health records
- **Counseling** - Mental health appointments, crisis resources

### 🎯 Career Services
- **Job Search** - Browse and apply for jobs and internships
- **Career Development** - Resume reviews, career fairs

### 🚗 Campus Operations
- **Parking** - Permits, shuttle tracking
- **Security** - Incident reporting, safety escorts

### 🎭 Student Engagement
- **Organizations** - Browse and join student clubs
- **Events** - Campus events calendar and registration

### 📄 Administrative
- **Records** - Transcript requests, enrollment verification
- **IT Support** - Submit and track support tickets

## Installation

### Method 1: WordPress Admin

1. Download the plugin ZIP file
2. Go to WordPress Admin > Plugins > Add New
3. Click "Upload Plugin"
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"

### Method 2: FTP Upload

1. Extract the ZIP file
2. Upload the `student-services` folder to `/wp-content/plugins/`
3. Go to WordPress Admin > Plugins
4. Find "Student Services" and click "Activate"

### Method 3: From Source

```bash
cd wp-content/plugins
git clone https://github.com/university/student-services.git
```

Then activate from WordPress Admin.

## Database Setup

The plugin automatically creates all necessary database tables upon activation. Tables include:

- `wp_ss_courses` - Course listings
- `wp_ss_enrollments` - Student course enrollments
- `wp_ss_grades` - Student grades
- `wp_ss_billing` - Billing accounts
- `wp_ss_charges` - Billing charges
- `wp_ss_payments` - Payment records
- `wp_ss_scholarships` - Scholarship listings
- `wp_ss_housing` - Housing applications
- `wp_ss_maintenance` - Maintenance requests
- `wp_ss_events` - Campus events
- `wp_ss_event_registrations` - Event registrations
- `wp_ss_organizations` - Student organizations
- `wp_ss_memberships` - Organization memberships
- `wp_ss_health_appointments` - Health appointments
- `wp_ss_it_tickets` - IT support tickets
- `wp_ss_jobs` - Job postings

## Configuration

### Settings

Go to **Student Services > Settings** to configure:

- Enable/disable specific services
- Configure service-specific options
- Set permissions and access controls

## Usage

### Admin Features

Navigate to **Student Services** in the WordPress admin menu to access:

- **Dashboard** - Overview and quick stats
- **Courses** - Manage course listings
- **Billing** - View billing and payments
- **Events** - Manage campus events
- **Settings** - Configure the plugin

### Shortcodes

Display student services on any page or post using shortcodes:

#### My Courses
```
[student_courses]
```
Displays the logged-in student's enrolled courses.

#### My Grades
```
[student_grades]
```
Shows grades and GPA for the logged-in student.

#### Billing Account
```
[student_billing]
```
Displays billing balance, charges, and payments.

#### Upcoming Events
```
[student_events limit="5"]
```
Shows upcoming campus events. Optional `limit` and `category` parameters.

#### Course Search
```
[course_search]
```
Displays an interactive course search form.

#### Health Appointments
```
[student_health_appointments]
```
Shows student's health appointments.

#### IT Support Tickets
```
[student_it_tickets]
```
Displays student's IT support tickets.

#### Event Calendar
```
[event_calendar]
```
Displays an event calendar.

### REST API

The plugin provides a comprehensive REST API at `https://yoursite.com/wp-json/student-services/v1/`

#### Endpoints

**Courses**
- `GET /courses` - Search courses
- `POST /courses/enroll` - Enroll in a course

**Grades**
- `GET /grades` - Get student grades
- `GET /grades/gpa` - Calculate GPA

**Billing**
- `GET /billing/account` - Get billing account
- `POST /billing/payment` - Make a payment

**Events**
- `GET /events` - Get upcoming events
- `POST /events/register` - Register for an event

**Health**
- `GET /health/appointments` - Get appointments
- `POST /health/schedule` - Schedule appointment

**IT Support**
- `GET /it/tickets` - Get support tickets
- `POST /it/submit` - Submit new ticket

#### API Example

```javascript
// Get student's courses
fetch('https://yoursite.com/wp-json/student-services/v1/courses', {
    headers: {
        'X-WP-Nonce': wpApiSettings.nonce
    }
})
.then(response => response.json())
.then(data => console.log(data));

// Enroll in a course
fetch('https://yoursite.com/wp-json/student-services/v1/courses/enroll', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': wpApiSettings.nonce
    },
    body: JSON.stringify({
        course_id: 123
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## PHP Usage

### Using Services in Your Theme

```php
// Get course registration service
$course_service = new Student_Services_Course_Registration();

// Search for courses
$result = $course_service->search_courses(array(
    'semester' => 'Fall',
    'year' => 2024
));

if ($result['success']) {
    $courses = $result['data'];
    foreach ($courses as $course) {
        echo $course->title;
    }
}

// Enroll student in a course
$user_id = get_current_user_id();
$course_id = 123;
$result = $course_service->enroll($user_id, $course_id);

if ($result['success']) {
    echo 'Enrollment successful!';
}
```

### Using Other Services

```php
// Grade Management
$grade_service = new Student_Services_Grade_Management();
$grades = $grade_service->get_grades($user_id);
$gpa = $grade_service->calculate_gpa($user_id, true);

// Billing
$billing_service = new Student_Services_Billing();
$account = $billing_service->get_account($user_id);
$payment = $billing_service->make_payment($user_id, 500.00, 'credit');

// Events
$events_service = new Student_Services_Events();
$events = $events_service->get_upcoming_events();
$registration = $events_service->register_for_event($user_id, $event_id);

// Health
$health_service = new Student_Services_Health();
$appointments = $health_service->get_appointments($user_id);

// IT Support
$it_service = new Student_Services_IT_Support();
$tickets = $it_service->get_tickets($user_id);
```

## User Roles and Permissions

The plugin respects WordPress user roles:

- **Administrators** - Full access to all features
- **Editors** - Can manage courses, events, and view billing
- **Logged-in Users** - Can view their own data and enroll in services
- **Guests** - Can view public event listings only

## Hooks and Filters

### Actions

```php
// After enrollment
do_action('student_services_after_enrollment', $user_id, $course_id);

// After payment
do_action('student_services_after_payment', $user_id, $payment_id, $amount);

// After event registration
do_action('student_services_after_event_registration', $user_id, $event_id);
```

### Filters

```php
// Modify course search results
apply_filters('student_services_course_results', $courses, $args);

// Modify GPA calculation
apply_filters('student_services_gpa_calculation', $gpa, $user_id);
```

## Troubleshooting

### Database Tables Not Created

If tables are not created on activation:

1. Deactivate the plugin
2. Delete the plugin
3. Reinstall and reactivate
4. Or manually run the activation hook:
```php
Student_Services_Database::create_tables();
```

### Shortcodes Not Working

- Make sure you're logged in (most shortcodes require authentication)
- Check that the plugin is activated
- Verify the shortcode syntax is correct

### REST API 404 Errors

- Go to Settings > Permalinks and click "Save Changes"
- This will flush rewrite rules

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

## Changelog

### 1.0.0
- Initial release
- 18 service categories implemented
- REST API endpoints
- Admin interface
- Shortcodes for front-end display
- Database schema and management

## Support

For support, please:

1. Check the documentation
2. Search existing issues on GitHub
3. Open a new issue if needed

## License

GPL v2 or later

## Credits

Developed by University IT Department

## Contributing

Contributions are welcome! Please fork the repository and submit pull requests.
