/**
 * Basic Usage Examples
 * Demonstrates how to use the Student Services Plugin
 */

import { StudentServices } from '../src';

// Initialize the plugin
const services = new StudentServices({
  apiUrl: 'https://api.university.edu',
  apiKey: 'your-api-key',
  timeout: 5000,
});

async function demonstrateBasicUsage() {
  console.log('=== Student Services Plugin - Basic Usage Examples ===\n');

  // Example 1: Search and Enroll in Courses
  console.log('1. Course Registration');
  const coursesResult = await services.courseRegistration.searchCourses({
    term: 'Fall 2024',
    subject: 'CS',
    availableOnly: true,
  });

  if (coursesResult.success && coursesResult.data) {
    console.log(`Found ${coursesResult.data.length} courses`);
    const course = coursesResult.data[0];
    console.log(`- ${course.code}: ${course.title}`);

    // Enroll in the course
    const enrollResult = await services.courseRegistration.enrollInCourse('STU-001', course.id);
    console.log(`Enrollment ${enrollResult.success ? 'successful' : 'failed'}\n`);
  }

  // Example 2: View Grades and Calculate GPA
  console.log('2. Grade Management');
  const gradesResult = await services.gradeManagement.getGrades('STU-001', 'Fall', 2024);
  if (gradesResult.success && gradesResult.data) {
    console.log(`Grades for Fall 2024:`);
    gradesResult.data.forEach((grade) => {
      console.log(`- ${grade.courseName}: ${grade.grade}`);
    });
  }

  const gpaResult = await services.gradeManagement.calculateGPA('STU-001', true);
  if (gpaResult.success && gpaResult.data) {
    console.log(`Cumulative GPA: ${gpaResult.data.gpa}\n`);
  }

  // Example 3: Check Billing Account
  console.log('3. Billing');
  const accountResult = await services.billing.getAccount('STU-001');
  if (accountResult.success && accountResult.data) {
    console.log(`Account Balance: $${accountResult.data.balance.toFixed(2)}`);
    console.log(`Due Date: ${accountResult.data.dueDate?.toLocaleDateString()}\n`);
  }

  // Example 4: Search Scholarships
  console.log('4. Scholarships');
  const scholarshipsResult = await services.scholarship.searchScholarships({
    major: 'Computer Science',
    gpa: 3.5,
  });
  if (scholarshipsResult.success && scholarshipsResult.data) {
    console.log(`Found ${scholarshipsResult.data.length} scholarships:`);
    scholarshipsResult.data.forEach((scholarship) => {
      console.log(`- ${scholarship.name}: $${scholarship.amount}`);
      console.log(`  Deadline: ${scholarship.deadline.toLocaleDateString()}`);
    });
    console.log();
  }

  // Example 5: Schedule Health Appointment
  console.log('5. Health Services');
  const appointmentDate = new Date();
  appointmentDate.setDate(appointmentDate.getDate() + 7);

  const appointmentResult = await services.health.scheduleAppointment(
    'STU-001',
    'general',
    appointmentDate,
    'Annual checkup'
  );
  if (appointmentResult.success && appointmentResult.data) {
    console.log(`Appointment scheduled:`);
    console.log(`- Provider: ${appointmentResult.data.provider}`);
    console.log(`- Date: ${appointmentResult.data.date.toLocaleDateString()}`);
    console.log(`- Time: ${appointmentResult.data.time}\n`);
  }

  // Example 6: Search Campus Events
  console.log('6. Campus Events');
  const eventsResult = await services.events.getUpcomingEvents({
    category: 'Entertainment',
  });
  if (eventsResult.success && eventsResult.data) {
    console.log(`Upcoming Entertainment Events:`);
    eventsResult.data.forEach((event) => {
      console.log(`- ${event.name}`);
      console.log(`  Date: ${event.date.toLocaleDateString()} at ${event.time}`);
      console.log(`  Location: ${event.location}`);
    });
    console.log();
  }

  // Example 7: Submit IT Ticket
  console.log('7. IT Support');
  const ticketResult = await services.it.submitTicket(
    'STU-001',
    'network',
    'Cannot connect to WiFi',
    'I am unable to connect to the campus WiFi network in the library',
    'medium'
  );
  if (ticketResult.success && ticketResult.data) {
    console.log(`IT Ticket submitted:`);
    console.log(`- Ticket ID: ${ticketResult.data.id}`);
    console.log(`- Status: ${ticketResult.data.status}\n`);
  }

  // Example 8: Request Safety Escort
  console.log('8. Campus Security');
  const escortResult = await services.security.requestSafetyEscort(
    'STU-001',
    'Library',
    'North Residence Hall'
  );
  if (escortResult.success && escortResult.data) {
    console.log(`Safety escort requested:`);
    console.log(`- Request ID: ${escortResult.data.id}`);
    console.log(`- Status: ${escortResult.data.status}\n`);
  }

  console.log('=== Examples Complete ===');
}

// Run examples
demonstrateBasicUsage().catch(console.error);
