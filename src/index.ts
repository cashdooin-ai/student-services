/**
 * Student Services Plugin
 * Comprehensive plugin for various student services for college and university students
 */

// Export types
export * from './types';

// Export base service
export { BaseService } from './services/BaseService';

// Academic Services
export { CourseRegistrationService } from './services/academic/CourseRegistrationService';
export { GradeManagementService } from './services/academic/GradeManagementService';
export { AcademicAdvisingService } from './services/academic/AcademicAdvisingService';
export { LibraryService } from './services/academic/LibraryService';

// Financial Services
export { BillingService } from './services/financial/BillingService';
export { ScholarshipService } from './services/financial/ScholarshipService';

// Campus Life Services
export { HousingService } from './services/campus-life/HousingService';
export { DiningService } from './services/campus-life/DiningService';

// Health & Wellness Services
export { HealthService } from './services/health/HealthService';
export { CounselingService } from './services/health/CounselingService';

// Career Services
export { CareerService } from './services/career/CareerService';

// Campus Operations Services
export { ParkingService } from './services/campus-ops/ParkingService';
export { SecurityService } from './services/campus-ops/SecurityService';

// Student Engagement Services
export { StudentOrganizationService } from './services/engagement/StudentOrganizationService';
export { EventService } from './services/engagement/EventService';

// Administrative Services
export { RecordsService } from './services/administrative/RecordsService';
export { ITService } from './services/administrative/ITService';

import { ServiceConfig } from './types';
import { CourseRegistrationService } from './services/academic/CourseRegistrationService';
import { GradeManagementService } from './services/academic/GradeManagementService';
import { AcademicAdvisingService } from './services/academic/AcademicAdvisingService';
import { LibraryService } from './services/academic/LibraryService';
import { BillingService } from './services/financial/BillingService';
import { ScholarshipService } from './services/financial/ScholarshipService';
import { HousingService } from './services/campus-life/HousingService';
import { DiningService } from './services/campus-life/DiningService';
import { HealthService } from './services/health/HealthService';
import { CounselingService } from './services/health/CounselingService';
import { CareerService } from './services/career/CareerService';
import { ParkingService } from './services/campus-ops/ParkingService';
import { SecurityService } from './services/campus-ops/SecurityService';
import { StudentOrganizationService } from './services/engagement/StudentOrganizationService';
import { EventService } from './services/engagement/EventService';
import { RecordsService } from './services/administrative/RecordsService';
import { ITService } from './services/administrative/ITService';

/**
 * Main StudentServices class that provides access to all services
 */
export class StudentServices {
  // Academic Services
  public courseRegistration: CourseRegistrationService;
  public gradeManagement: GradeManagementService;
  public academicAdvising: AcademicAdvisingService;
  public library: LibraryService;

  // Financial Services
  public billing: BillingService;
  public scholarship: ScholarshipService;

  // Campus Life Services
  public housing: HousingService;
  public dining: DiningService;

  // Health & Wellness Services
  public health: HealthService;
  public counseling: CounselingService;

  // Career Services
  public career: CareerService;

  // Campus Operations Services
  public parking: ParkingService;
  public security: SecurityService;

  // Student Engagement Services
  public studentOrganizations: StudentOrganizationService;
  public events: EventService;

  // Administrative Services
  public records: RecordsService;
  public it: ITService;

  constructor(config: ServiceConfig = {}) {
    // Initialize Academic Services
    this.courseRegistration = new CourseRegistrationService(config);
    this.gradeManagement = new GradeManagementService(config);
    this.academicAdvising = new AcademicAdvisingService(config);
    this.library = new LibraryService(config);

    // Initialize Financial Services
    this.billing = new BillingService(config);
    this.scholarship = new ScholarshipService(config);

    // Initialize Campus Life Services
    this.housing = new HousingService(config);
    this.dining = new DiningService(config);

    // Initialize Health & Wellness Services
    this.health = new HealthService(config);
    this.counseling = new CounselingService(config);

    // Initialize Career Services
    this.career = new CareerService(config);

    // Initialize Campus Operations Services
    this.parking = new ParkingService(config);
    this.security = new SecurityService(config);

    // Initialize Student Engagement Services
    this.studentOrganizations = new StudentOrganizationService(config);
    this.events = new EventService(config);

    // Initialize Administrative Services
    this.records = new RecordsService(config);
    this.it = new ITService(config);
  }
}

// Export default instance
export default StudentServices;
