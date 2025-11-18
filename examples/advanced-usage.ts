/**
 * Advanced Usage Examples
 * Demonstrates advanced features and workflows
 */

import { StudentServices } from '../src';

const services = new StudentServices();

async function demonstrateAdvancedUsage() {
  console.log('=== Student Services Plugin - Advanced Usage Examples ===\n');

  // Example 1: Complete Course Registration Workflow
  console.log('1. Complete Course Registration Workflow');
  const studentId = 'STU-001';
  const courseId = 'CS301';

  // Check prerequisites
  const prereqResult = await services.courseRegistration.validatePrerequisites(studentId, courseId);
  if (prereqResult.success && prereqResult.data?.eligible) {
    console.log('✓ Prerequisites satisfied');

    // Search for the course
    const coursesResult = await services.courseRegistration.searchCourses({
      term: 'Spring 2025',
    });

    // Enroll
    const enrollResult = await services.courseRegistration.enrollInCourse(studentId, courseId);
    if (enrollResult.success) {
      console.log('✓ Successfully enrolled\n');
    }
  } else {
    console.log(`✗ Missing prerequisites: ${prereqResult.data?.missing?.join(', ')}\n`);
  }

  // Example 2: Housing Application Workflow
  console.log('2. Housing Application Workflow');

  // Submit application
  const housingAppResult = await services.housing.submitApplication(studentId, {
    buildingType: 'dorm',
    roommates: ['STU-002'],
    specialNeeds: ['First floor preferred'],
  });

  if (housingAppResult.success) {
    console.log('✓ Housing application submitted');

    // Check status
    const statusResult = await services.housing.getApplicationStatus(studentId);
    if (statusResult.success && statusResult.data) {
      console.log(`Status: ${statusResult.data.status}`);

      if (statusResult.data.assignedRoom) {
        console.log(`Assigned Room: ${statusResult.data.assignedRoom.building} ${statusResult.data.assignedRoom.room}`);
      }
    }
    console.log();
  }

  // Example 3: Financial Aid Planning
  console.log('3. Financial Aid Planning');

  // Check billing
  const billingResult = await services.billing.getAccount(studentId);
  if (billingResult.success && billingResult.data) {
    const balance = billingResult.data.balance;
    console.log(`Current Balance: $${balance.toFixed(2)}`);

    // Search scholarships
    const scholarshipsResult = await services.scholarship.searchScholarships({
      gpa: 3.5,
    });

    if (scholarshipsResult.success && scholarshipsResult.data) {
      let totalPotentialAid = 0;
      console.log('Eligible Scholarships:');
      scholarshipsResult.data.forEach((s) => {
        if (s.status === 'eligible') {
          console.log(`- ${s.name}: $${s.amount}`);
          totalPotentialAid += s.amount;
        }
      });
      console.log(`Total Potential Aid: $${totalPotentialAid.toFixed(2)}`);
    }

    // Set up payment plan if needed
    if (balance > 1000) {
      const planResult = await services.billing.setupPaymentPlan(studentId, balance, 4);
      if (planResult.success && planResult.data) {
        console.log(`Payment Plan Created:`);
        console.log(`- Installments: 4 x $${planResult.data.installmentAmount.toFixed(2)}`);
      }
    }
    console.log();
  }

  // Example 4: Career Development Journey
  console.log('4. Career Development Journey');

  // Search jobs
  const jobsResult = await services.career.searchJobs({
    type: 'internship',
    major: 'Computer Science',
  });

  if (jobsResult.success && jobsResult.data && jobsResult.data.length > 0) {
    const job = jobsResult.data[0];
    console.log(`Found Job: ${job.title} at ${job.company}`);

    // Schedule resume review
    const reviewDate = new Date();
    reviewDate.setDate(reviewDate.getDate() + 5);

    const reviewResult = await services.career.scheduleResumeReview(
      studentId,
      reviewDate,
      'Technical resume for software internships'
    );
    if (reviewResult.success) {
      console.log('✓ Resume review scheduled');
    }

    // Schedule mock interview
    const mockDate = new Date();
    mockDate.setDate(mockDate.getDate() + 10);

    const mockResult = await services.career.scheduleMockInterview(
      studentId,
      'Technical Interview',
      mockDate
    );
    if (mockResult.success) {
      console.log('✓ Mock interview scheduled');
    }

    // Apply for job
    const applyResult = await services.career.applyForJob(studentId, job.id, {
      resume: 'resume.pdf',
      coverLetter: 'cover-letter.pdf',
    });
    if (applyResult.success) {
      console.log('✓ Application submitted\n');
    }
  }

  // Example 5: Wellness Check-in Workflow
  console.log('5. Wellness Check-in Workflow');

  // Get wellness workshops
  const workshopsResult = await services.counseling.getWellnessWorkshops();
  if (workshopsResult.success && workshopsResult.data && workshopsResult.data.length > 0) {
    const workshop = workshopsResult.data[0];
    console.log(`Available Workshop: ${workshop.title}`);

    // Register
    const registerResult = await services.counseling.registerForWorkshop(studentId, workshop.id);
    if (registerResult.success) {
      console.log('✓ Registered for workshop');
    }
  }

  // Get self-help resources
  const resourcesResult = await services.counseling.getSelfHelpResources('Stress Management');
  if (resourcesResult.success && resourcesResult.data) {
    console.log('Self-Help Resources:');
    resourcesResult.data.slice(0, 2).forEach((resource) => {
      console.log(`- ${resource.title} (${resource.type})`);
    });
    console.log();
  }

  // Example 6: Academic Planning with Advisor
  console.log('6. Academic Planning with Advisor');

  // Get degree progress
  const progressResult = await services.academicAdvising.getDegreeProgress(studentId);
  if (progressResult.success && progressResult.data) {
    console.log(`Degree Progress: ${progressResult.data.percentComplete}%`);
    console.log(`Credits: ${progressResult.data.completedCredits}/${progressResult.data.requiredCredits}`);

    // Get course recommendations
    const recsResult = await services.academicAdvising.getCourseRecommendations(studentId);
    if (recsResult.success && recsResult.data) {
      console.log('Recommended Courses:');
      recsResult.data.forEach((rec) => {
        console.log(`- ${rec.recommended.join(', ')}: ${rec.reasoning}`);
      });
    }

    // Schedule advising appointment
    const advDate = new Date();
    advDate.setDate(advDate.getDate() + 7);

    const advResult = await services.academicAdvising.scheduleAppointment(
      studentId,
      'ADV-001',
      advDate,
      'academic'
    );
    if (advResult.success) {
      console.log('✓ Advising appointment scheduled\n');
    }
  }

  // Example 7: Event Planning and Attendance
  console.log('7. Event Planning and Attendance');

  // Join a student organization
  const orgJoinResult = await services.studentOrganizations.joinOrganization(studentId, 'ORG-001');
  if (orgJoinResult.success) {
    console.log('✓ Joined Computer Science Club');
  }

  // Search events
  const campusEventsResult = await services.events.getUpcomingEvents({
    category: 'Academic',
  });

  if (campusEventsResult.success && campusEventsResult.data && campusEventsResult.data.length > 0) {
    const event = campusEventsResult.data[0];

    // Register for event
    const eventRegResult = await services.events.registerForEvent(studentId, event.id);
    if (eventRegResult.success) {
      console.log(`✓ Registered for ${event.name}\n`);
    }
  }

  // Example 8: Complete IT Support Lifecycle
  console.log('8. Complete IT Support Lifecycle');

  // Submit ticket
  const ticketResult = await services.it.submitTicket(
    studentId,
    'software',
    'Need MATLAB license',
    'I need access to MATLAB for my engineering course',
    'medium'
  );

  if (ticketResult.success && ticketResult.data) {
    console.log(`✓ Ticket ${ticketResult.data.id} created`);

    // Check software downloads
    const softwareResult = await services.it.getSoftwareDownloads('Engineering');
    if (softwareResult.success && softwareResult.data) {
      const matlab = softwareResult.data.find((s) => s.name === 'MATLAB');
      if (matlab) {
        console.log(`✓ MATLAB available: ${matlab.downloadUrl}`);
      }
    }
    console.log();
  }

  console.log('=== Advanced Examples Complete ===');
}

// Run examples
demonstrateAdvancedUsage().catch(console.error);
