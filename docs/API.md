# Student Services Plugin - API Documentation

## Table of Contents

1. [Installation](#installation)
2. [Getting Started](#getting-started)
3. [Academic Services](#academic-services)
4. [Financial Services](#financial-services)
5. [Campus Life Services](#campus-life-services)
6. [Health & Wellness Services](#health--wellness-services)
7. [Career Services](#career-services)
8. [Campus Operations Services](#campus-operations-services)
9. [Student Engagement Services](#student-engagement-services)
10. [Administrative Services](#administrative-services)
11. [Error Handling](#error-handling)

## Installation

```bash
npm install @university/student-services-plugin
```

## Getting Started

```typescript
import { StudentServices } from '@university/student-services-plugin';

// Initialize with configuration
const services = new StudentServices({
  apiUrl: 'https://api.university.edu',
  apiKey: 'your-api-key',
  timeout: 5000,
  retryAttempts: 3
});

// Use any service
const result = await services.courseRegistration.searchCourses({
  term: 'Fall 2024'
});

if (result.success) {
  console.log(result.data);
} else {
  console.error(result.error);
}
```

---

## Academic Services

### Course Registration Service

**Search Courses**
```typescript
await services.courseRegistration.searchCourses({
  term?: string;
  subject?: string;
  level?: string;
  instructor?: string;
  availableOnly?: boolean;
});
```

**Enroll in Course**
```typescript
await services.courseRegistration.enrollInCourse(studentId, courseId);
```

**Drop Course**
```typescript
await services.courseRegistration.dropCourse(studentId, courseId);
```

**Join Waitlist**
```typescript
await services.courseRegistration.joinWaitlist(studentId, courseId);
```

**Validate Prerequisites**
```typescript
await services.courseRegistration.validatePrerequisites(studentId, courseId);
```

**Get Enrolled Courses**
```typescript
await services.courseRegistration.getEnrolledCourses(studentId);
```

### Grade Management Service

**Get Grades**
```typescript
await services.gradeManagement.getGrades(studentId, semester?, year?);
```

**Calculate GPA**
```typescript
await services.gradeManagement.calculateGPA(studentId, cumulative = false);
```

**Get Grade Distribution**
```typescript
await services.gradeManagement.getGradeDistribution(courseId);
```

**Submit Grade Appeal**
```typescript
await services.gradeManagement.submitGradeAppeal(studentId, courseId, reason);
```

**Get Academic Standing**
```typescript
await services.gradeManagement.getAcademicStanding(studentId);
```

### Academic Advising Service

**Schedule Appointment**
```typescript
await services.academicAdvising.scheduleAppointment(
  studentId,
  advisorId,
  date,
  type: 'academic' | 'career' | 'general'
);
```

**Get Appointments**
```typescript
await services.academicAdvising.getAppointments(studentId);
```

**Cancel Appointment**
```typescript
await services.academicAdvising.cancelAppointment(appointmentId);
```

**Get Degree Progress**
```typescript
await services.academicAdvising.getDegreeProgress(studentId);
```

**Get Course Recommendations**
```typescript
await services.academicAdvising.getCourseRecommendations(studentId);
```

**Declare Major**
```typescript
await services.academicAdvising.declareMajor(studentId, majorCode, minorCode?);
```

### Library Service

**Search Catalog**
```typescript
await services.library.searchCatalog({
  keyword?: string;
  title?: string;
  author?: string;
  isbn?: string;
  type?: 'book' | 'journal' | 'digital' | 'media';
});
```

**Checkout Resource**
```typescript
await services.library.checkoutResource(studentId, resourceId);
```

**Renew Resource**
```typescript
await services.library.renewResource(studentId, resourceId);
```

**Reserve Resource**
```typescript
await services.library.reserveResource(studentId, resourceId);
```

**Book Study Room**
```typescript
await services.library.bookStudyRoom(studentId, roomId, date, startTime, duration);
```

**Get Available Study Rooms**
```typescript
await services.library.getAvailableStudyRooms(date, startTime);
```

**Request Interlibrary Loan**
```typescript
await services.library.requestInterlibrary(studentId, resourceInfo);
```

---

## Financial Services

### Billing Service

**Get Account**
```typescript
await services.billing.getAccount(studentId);
```

**Make Payment**
```typescript
await services.billing.makePayment(
  studentId,
  amount,
  method: 'credit' | 'debit' | 'ach' | 'cash' | 'check'
);
```

**Setup Payment Plan**
```typescript
await services.billing.setupPaymentPlan(studentId, totalAmount, numberOfPayments);
```

**Get Payment History**
```typescript
await services.billing.getPaymentHistory(studentId);
```

**Get Billing Statement**
```typescript
await services.billing.getBillingStatement(studentId, semester, year);
```

**Request Late Fee Waiver**
```typescript
await services.billing.requestLateFeeWaiver(studentId, chargeId, reason);
```

### Scholarship Service

**Search Scholarships**
```typescript
await services.scholarship.searchScholarships({
  major?: string;
  gpa?: number;
  year?: number;
  amount?: { min?: number; max?: number };
});
```

**Apply for Scholarship**
```typescript
await services.scholarship.applyForScholarship(studentId, scholarshipId, application);
```

**Get Application Status**
```typescript
await services.scholarship.getApplicationStatus(applicationId);
```

**Get Awarded Scholarships**
```typescript
await services.scholarship.getAwardedScholarships(studentId);
```

**Get Financial Aid Summary**
```typescript
await services.scholarship.getFinancialAidSummary(studentId, year);
```

**Get FAFSA Status**
```typescript
await services.scholarship.getFAFSAStatus(studentId);
```

**Accept Financial Aid**
```typescript
await services.scholarship.acceptFinancialAid(studentId, aidId, amount);
```

---

## Campus Life Services

### Housing Service

**Submit Application**
```typescript
await services.housing.submitApplication(studentId, preferences);
```

**Get Application Status**
```typescript
await services.housing.getApplicationStatus(studentId);
```

**Get Available Rooms**
```typescript
await services.housing.getAvailableRooms(buildingType?);
```

**Select Room**
```typescript
await services.housing.selectRoom(studentId, building, room);
```

**Submit Maintenance Request**
```typescript
await services.housing.submitMaintenanceRequest(
  studentId,
  building,
  room,
  issue,
  priority: 'low' | 'medium' | 'high' | 'emergency'
);
```

**Get Maintenance Status**
```typescript
await services.housing.getMaintenanceStatus(requestId);
```

**Request Roommate Change**
```typescript
await services.housing.requestRoommateChange(studentId, reason);
```

### Dining Service

**Get Meal Plans**
```typescript
await services.dining.getMealPlans();
```

**Get Student Meal Plan**
```typescript
await services.dining.getStudentMealPlan(studentId);
```

**Change Meal Plan**
```typescript
await services.dining.changeMealPlan(studentId, planId);
```

**Get Dining Halls**
```typescript
await services.dining.getDiningHalls();
```

**Get Menu**
```typescript
await services.dining.getMenu(diningHallId, date?);
```

**Register Dietary Restrictions**
```typescript
await services.dining.registerDietaryRestrictions(studentId, restrictions);
```

**Add Dining Dollars**
```typescript
await services.dining.addDiningDollars(studentId, amount);
```

---

## Health & Wellness Services

### Health Service

**Schedule Appointment**
```typescript
await services.health.scheduleAppointment(
  studentId,
  type: 'general' | 'mental-health' | 'specialist',
  preferredDate,
  reason
);
```

**Get Appointments**
```typescript
await services.health.getAppointments(studentId);
```

**Cancel Appointment**
```typescript
await services.health.cancelAppointment(appointmentId);
```

**Get Health Records**
```typescript
await services.health.getHealthRecords(studentId);
```

**Update Emergency Contact**
```typescript
await services.health.updateEmergencyContact(studentId, contact);
```

**Add Vaccination**
```typescript
await services.health.addVaccination(studentId, vaccination);
```

**Request Prescription Refill**
```typescript
await services.health.requestPrescriptionRefill(studentId, medicationName, prescriptionNumber);
```

**Get Insurance Info**
```typescript
await services.health.getInsuranceInfo(studentId);
```

### Counseling Service

**Schedule Appointment**
```typescript
await services.counseling.scheduleAppointment(
  studentId,
  type: 'individual' | 'group' | 'crisis',
  preferredDate?,
  urgent?
);
```

**Get Crisis Resources**
```typescript
await services.counseling.getCrisisResources();
```

**Get Wellness Workshops**
```typescript
await services.counseling.getWellnessWorkshops();
```

**Register for Workshop**
```typescript
await services.counseling.registerForWorkshop(studentId, workshopId);
```

**Request Peer Support**
```typescript
await services.counseling.requestPeerSupport(studentId, interests, preferences?);
```

**Get Self-Help Resources**
```typescript
await services.counseling.getSelfHelpResources(category?);
```

---

## Career Services

### Career Service

**Search Jobs**
```typescript
await services.career.searchJobs({
  type?: 'internship' | 'part-time' | 'full-time' | 'co-op';
  major?: string;
  location?: string;
  keyword?: string;
});
```

**Apply for Job**
```typescript
await services.career.applyForJob(studentId, jobId, application);
```

**Get Application Status**
```typescript
await services.career.getApplicationStatus(applicationId);
```

**Schedule Resume Review**
```typescript
await services.career.scheduleResumeReview(studentId, preferredDate, focusArea?);
```

**Get Career Fairs**
```typescript
await services.career.getCareerFairs();
```

**Register for Event**
```typescript
await services.career.registerForEvent(studentId, eventId);
```

**Get Interview Resources**
```typescript
await services.career.getInterviewResources();
```

**Schedule Mock Interview**
```typescript
await services.career.scheduleMockInterview(studentId, interviewType, preferredDate);
```

**Request Alumni Mentor**
```typescript
await services.career.requestAlumniMentor(studentId, preferences);
```

---

## Campus Operations Services

### Parking Service

**Purchase Permit**
```typescript
await services.parking.purchasePermit(
  studentId,
  vehicleInfo,
  type: 'resident' | 'commuter' | 'reserved',
  startDate,
  endDate
);
```

**Get Permits**
```typescript
await services.parking.getPermits(studentId);
```

**Update Vehicle**
```typescript
await services.parking.updateVehicle(studentId, permitId, vehicleInfo);
```

**Get Available Lots**
```typescript
await services.parking.getAvailableLots(type?);
```

**Appeal Violation**
```typescript
await services.parking.appealViolation(studentId, violationNumber, reason, evidence?);
```

**Get Violations**
```typescript
await services.parking.getViolations(studentId);
```

**Get Shuttle Routes**
```typescript
await services.parking.getShuttleRoutes();
```

**Track Shuttle**
```typescript
await services.parking.trackShuttle(routeId);
```

### Security Service

**Report Incident**
```typescript
await services.security.reportIncident(
  studentId,
  type: 'theft' | 'vandalism' | 'safety' | 'other',
  location,
  description,
  anonymous?
);
```

**Get Incident Status**
```typescript
await services.security.getIncidentStatus(incidentId);
```

**Request Safety Escort**
```typescript
await services.security.requestSafetyEscort(studentId, pickupLocation, dropoffLocation, requestedTime?);
```

**Get Emergency Contacts**
```typescript
await services.security.getEmergencyContacts();
```

**Report Lost Item**
```typescript
await services.security.reportLostItem(studentId, itemDescription, lastSeenLocation, lastSeenDate);
```

**Search Lost and Found**
```typescript
await services.security.searchLostAndFound(keyword);
```

**Get Safety Alerts**
```typescript
await services.security.getSafetyAlerts(startDate?);
```

**Register for Alerts**
```typescript
await services.security.registerForAlerts(studentId, contactMethods);
```

---

## Student Engagement Services

### Student Organization Service

**Search Organizations**
```typescript
await services.studentOrganizations.searchOrganizations({
  category?: string;
  keyword?: string;
  joinable?: boolean;
});
```

**Get Organization Details**
```typescript
await services.studentOrganizations.getOrganizationDetails(organizationId);
```

**Join Organization**
```typescript
await services.studentOrganizations.joinOrganization(studentId, organizationId);
```

**Leave Organization**
```typescript
await services.studentOrganizations.leaveOrganization(studentId, organizationId);
```

**Get Student Organizations**
```typescript
await services.studentOrganizations.getStudentOrganizations(studentId);
```

**Create Organization**
```typescript
await services.studentOrganizations.createOrganization(studentId, organizationInfo);
```

**Request Budget**
```typescript
await services.studentOrganizations.requestBudget(organizationId, amount, purpose, breakdown);
```

### Event Service

**Get Upcoming Events**
```typescript
await services.events.getUpcomingEvents(filters?);
```

**Get Event Details**
```typescript
await services.events.getEventDetails(eventId);
```

**Register for Event**
```typescript
await services.events.registerForEvent(studentId, eventId);
```

**Unregister from Event**
```typescript
await services.events.unregisterFromEvent(studentId, eventId);
```

**Get Student Events**
```typescript
await services.events.getStudentEvents(studentId);
```

**Create Event**
```typescript
await services.events.createEvent(organizerId, eventInfo);
```

**Purchase Ticket**
```typescript
await services.events.purchaseTicket(studentId, eventId, quantity);
```

**Check In to Event**
```typescript
await services.events.checkInToEvent(ticketNumber);
```

---

## Administrative Services

### Records Service

**Request Transcript**
```typescript
await services.records.requestTranscript(
  studentId,
  type: 'official' | 'unofficial',
  deliveryMethod: 'electronic' | 'mail',
  recipient
);
```

**Get Transcript Status**
```typescript
await services.records.getTranscriptStatus(requestId);
```

**Request Enrollment Verification**
```typescript
await services.records.requestEnrollmentVerification(studentId, purpose);
```

**Order Diploma**
```typescript
await services.records.orderDiploma(studentId, options);
```

**Request ID Card**
```typescript
await services.records.requestIDCard(
  studentId,
  reason: 'lost' | 'stolen' | 'damaged' | 'new'
);
```

**Update Personal Info**
```typescript
await services.records.updatePersonalInfo(studentId, updates);
```

**Request Name Change**
```typescript
await services.records.requestNameChange(studentId, newName, legalDocuments);
```

**Get Academic History**
```typescript
await services.records.getAcademicHistory(studentId);
```

### IT Service

**Submit Ticket**
```typescript
await services.it.submitTicket(
  studentId,
  category: 'account' | 'network' | 'software' | 'hardware' | 'other',
  subject,
  description,
  priority?
);
```

**Get Ticket Status**
```typescript
await services.it.getTicketStatus(ticketId);
```

**Get Student Tickets**
```typescript
await services.it.getStudentTickets(studentId, status?);
```

**Reset Password**
```typescript
await services.it.resetPassword(studentId, accountType);
```

**Get Software Downloads**
```typescript
await services.it.getSoftwareDownloads(category?);
```

**Request VPN Access**
```typescript
await services.it.requestVPNAccess(studentId, reason);
```

**Get Network Status**
```typescript
await services.it.getNetworkStatus();
```

**Search Knowledge Base**
```typescript
await services.it.searchKnowledgeBase(query);
```

**Schedule Appointment**
```typescript
await services.it.scheduleAppointment(studentId, issueType, preferredDate, preferredTime);
```

---

## Error Handling

All service methods return a `ServiceResponse<T>` object:

```typescript
interface ServiceResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}
```

**Example:**
```typescript
const result = await services.courseRegistration.enrollInCourse('STU-001', 'CS101');

if (result.success) {
  console.log('Success:', result.message);
  console.log('Data:', result.data);
} else {
  console.error('Error:', result.error);
}
```

**Error Types:**
- Validation errors (missing required fields)
- Network errors (timeout, connection issues)
- Business logic errors (insufficient permissions, prerequisites not met)
- Server errors (5xx responses)

All errors are caught and returned in a consistent format for easy handling.
