# Student Services Plugin

A comprehensive TypeScript/JavaScript plugin providing various student services for college and university students.

## Overview

This plugin provides a unified interface to access 18 different categories of student services, including academic, financial, campus life, health & wellness, career development, campus operations, student engagement, and administrative services.

## Features

### 📚 Academic Services
- **Course Registration** - Search courses, enroll, drop, waitlist management
- **Grade Management** - View grades, calculate GPA, grade appeals
- **Academic Advising** - Schedule appointments, degree progress tracking, course recommendations
- **Library Services** - Book search/checkout, study room booking, interlibrary loans

### 💰 Financial Services
- **Billing & Payments** - View balance, make payments, setup payment plans
- **Scholarships & Financial Aid** - Search scholarships, track applications, FAFSA status

### 🏠 Campus Life Services
- **Housing** - Applications, room selection, maintenance requests
- **Dining** - Meal plans, menus, dietary accommodations, dining dollars

### 🏥 Health & Wellness Services
- **Health Services** - Appointments, health records, vaccinations, prescriptions
- **Counseling** - Mental health appointments, wellness workshops, crisis support

### 🎯 Career & Professional Development
- **Career Services** - Job search, resume reviews, career fairs, mock interviews
- **Alumni Network** - Mentorship matching, networking events

### 🚗 Campus Operations
- **Parking & Transportation** - Permits, shuttle tracking, violation appeals
- **Campus Security** - Incident reporting, safety escorts, emergency alerts

### 🎭 Student Engagement
- **Student Organizations** - Club directory, membership, budget requests
- **Event Management** - Campus events, ticketing, registrations

### 📄 Administrative Services
- **Records & Documentation** - Transcripts, enrollment verification, ID cards
- **IT Services** - Help desk tickets, software downloads, password resets

## Installation

```bash
npm install @university/student-services-plugin
```

## Quick Start

```typescript
import { StudentServices } from '@university/student-services-plugin';

// Initialize the plugin
const services = new StudentServices({
  apiUrl: 'https://api.university.edu',
  apiKey: 'your-api-key',
  timeout: 5000,
});

// Example: Search and enroll in courses
const courses = await services.courseRegistration.searchCourses({
  term: 'Fall 2024',
  subject: 'CS',
  availableOnly: true,
});

if (courses.success) {
  console.log(`Found ${courses.data.length} courses`);

  // Enroll in a course
  const result = await services.courseRegistration.enrollInCourse(
    'student-id',
    courses.data[0].id
  );

  if (result.success) {
    console.log('Enrollment successful!');
  }
}

// Example: Check billing and search scholarships
const account = await services.billing.getAccount('student-id');
const scholarships = await services.scholarship.searchScholarships({
  major: 'Computer Science',
  gpa: 3.5,
});

// Example: Schedule health appointment
const appointment = await services.health.scheduleAppointment(
  'student-id',
  'general',
  new Date('2024-12-01'),
  'Annual checkup'
);
```

## Documentation

- [Complete API Documentation](docs/API.md)
- [Basic Usage Examples](examples/basic-usage.ts)
- [Advanced Usage Examples](examples/advanced-usage.ts)

## Service Categories

### Academic Services
```typescript
services.courseRegistration  // Course enrollment and management
services.gradeManagement     // Grades and GPA tracking
services.academicAdvising    // Academic planning and advising
services.library             // Library resources and services
```

### Financial Services
```typescript
services.billing             // Tuition and payments
services.scholarship         // Scholarships and financial aid
```

### Campus Life Services
```typescript
services.housing             // Residence hall management
services.dining              // Meal plans and dining services
```

### Health & Wellness Services
```typescript
services.health              // Medical services
services.counseling          // Mental health and wellness
```

### Career Services
```typescript
services.career              // Job search and career development
```

### Campus Operations Services
```typescript
services.parking             // Parking and transportation
services.security            // Campus security and safety
```

### Student Engagement Services
```typescript
services.studentOrganizations // Clubs and organizations
services.events              // Campus events and activities
```

### Administrative Services
```typescript
services.records             // Official documents and records
services.it                  // IT support and services
```

## Response Format

All service methods return a consistent response format:

```typescript
interface ServiceResponse<T> {
  success: boolean;   // Whether the operation succeeded
  data?: T;          // The response data (if successful)
  error?: string;    // Error message (if failed)
  message?: string;  // Additional information
}
```

## Configuration

```typescript
interface ServiceConfig {
  apiUrl?: string;        // Base URL for API calls
  apiKey?: string;        // API authentication key
  timeout?: number;       // Request timeout in milliseconds (default: 5000)
  retryAttempts?: number; // Number of retry attempts (default: 3)
}
```

## Error Handling

```typescript
const result = await services.courseRegistration.enrollInCourse(studentId, courseId);

if (result.success) {
  // Handle success
  console.log('Enrolled successfully');
} else {
  // Handle error
  console.error('Enrollment failed:', result.error);
}
```

## TypeScript Support

This plugin is written in TypeScript and includes full type definitions for all services, methods, and data structures.

## Examples

See the [examples](examples/) directory for:
- Basic usage examples
- Advanced workflow examples
- Integration patterns

## Development

```bash
# Install dependencies
npm install

# Build the project
npm run build

# Run examples
npm run dev
```

## License

MIT

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For issues and questions, please open an issue on GitHub.
