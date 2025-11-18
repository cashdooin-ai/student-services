/**
 * Core Types for Student Services Plugin
 */

export interface Student {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  studentId: string;
  enrollmentStatus: 'full-time' | 'part-time' | 'graduated' | 'inactive';
  major?: string;
  minor?: string;
  year?: number;
  gpa?: number;
}

export interface ServiceConfig {
  apiUrl?: string;
  apiKey?: string;
  timeout?: number;
  retryAttempts?: number;
}

export interface ServiceResponse<T = any> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}

// Academic Services Types
export interface Course {
  id: string;
  code: string;
  title: string;
  credits: number;
  instructor: string;
  schedule: CourseSchedule[];
  capacity: number;
  enrolled: number;
  prerequisites?: string[];
  description?: string;
}

export interface CourseSchedule {
  day: string;
  startTime: string;
  endTime: string;
  location: string;
}

export interface Grade {
  courseId: string;
  courseName: string;
  grade: string;
  credits: number;
  semester: string;
  year: number;
  gradePoint?: number;
}

export interface AdvisingAppointment {
  id: string;
  advisorName: string;
  date: Date;
  time: string;
  duration: number;
  type: 'academic' | 'career' | 'general';
  status: 'scheduled' | 'completed' | 'cancelled';
  notes?: string;
}

export interface LibraryResource {
  id: string;
  title: string;
  author: string;
  type: 'book' | 'journal' | 'digital' | 'media';
  available: boolean;
  location?: string;
  dueDate?: Date;
}

// Financial Services Types
export interface BillingAccount {
  studentId: string;
  balance: number;
  charges: Charge[];
  payments: Payment[];
  dueDate?: Date;
}

export interface Charge {
  id: string;
  description: string;
  amount: number;
  date: Date;
  category: string;
}

export interface Payment {
  id: string;
  amount: number;
  date: Date;
  method: 'credit' | 'debit' | 'ach' | 'cash' | 'check';
  status: 'pending' | 'completed' | 'failed';
}

export interface Scholarship {
  id: string;
  name: string;
  amount: number;
  deadline: Date;
  eligibility: string[];
  requirements: string[];
  status?: 'eligible' | 'applied' | 'awarded' | 'declined';
}

export interface FinancialAid {
  studentId: string;
  totalAward: number;
  grants: number;
  loans: number;
  workStudy: number;
  scholarships: number;
  year: string;
}

// Housing Services Types
export interface HousingApplication {
  id: string;
  studentId: string;
  preferences: HousingPreferences;
  status: 'pending' | 'approved' | 'denied' | 'waitlist';
  assignedRoom?: RoomAssignment;
}

export interface HousingPreferences {
  buildingType: 'dorm' | 'apartment' | 'suite';
  roommates?: string[];
  specialNeeds?: string[];
}

export interface RoomAssignment {
  building: string;
  room: string;
  floor: number;
  capacity: number;
  amenities: string[];
}

export interface MaintenanceRequest {
  id: string;
  studentId: string;
  building: string;
  room: string;
  issue: string;
  priority: 'low' | 'medium' | 'high' | 'emergency';
  status: 'submitted' | 'in-progress' | 'completed';
  submittedDate: Date;
  completedDate?: Date;
}

// Dining Services Types
export interface MealPlan {
  id: string;
  name: string;
  mealsPerWeek: number;
  diningDollars: number;
  price: number;
}

export interface DiningHall {
  id: string;
  name: string;
  location: string;
  hours: OperatingHours[];
  menu?: MenuItem[];
}

export interface OperatingHours {
  day: string;
  openTime: string;
  closeTime: string;
}

export interface MenuItem {
  name: string;
  category: string;
  calories?: number;
  allergens?: string[];
  dietary?: string[];
}

// Health Services Types
export interface HealthAppointment {
  id: string;
  studentId: string;
  provider: string;
  date: Date;
  time: string;
  type: 'general' | 'mental-health' | 'specialist';
  reason: string;
  status: 'scheduled' | 'completed' | 'cancelled' | 'no-show';
}

export interface HealthRecord {
  studentId: string;
  vaccinations: Vaccination[];
  allergies: string[];
  medications: Medication[];
  emergencyContact: EmergencyContact;
}

export interface Vaccination {
  name: string;
  date: Date;
  provider: string;
  nextDue?: Date;
}

export interface Medication {
  name: string;
  dosage: string;
  frequency: string;
  prescribedBy: string;
}

export interface EmergencyContact {
  name: string;
  relationship: string;
  phone: string;
  email?: string;
}

// Career Services Types
export interface JobPosting {
  id: string;
  title: string;
  company: string;
  type: 'internship' | 'part-time' | 'full-time' | 'co-op';
  description: string;
  requirements: string[];
  location: string;
  salary?: string;
  postedDate: Date;
  deadline?: Date;
}

export interface CareerEvent {
  id: string;
  name: string;
  type: 'career-fair' | 'workshop' | 'networking' | 'info-session';
  date: Date;
  location: string;
  companies?: string[];
  registrationRequired: boolean;
  capacity?: number;
}

// Parking & Transportation Types
export interface ParkingPermit {
  id: string;
  studentId: string;
  vehicleInfo: VehicleInfo;
  type: 'resident' | 'commuter' | 'reserved';
  lot: string;
  startDate: Date;
  endDate: Date;
  cost: number;
}

export interface VehicleInfo {
  make: string;
  model: string;
  year: number;
  color: string;
  licensePlate: string;
  state: string;
}

export interface ShuttleRoute {
  id: string;
  name: string;
  stops: ShuttleStop[];
  schedule: string;
  activeHours: string;
}

export interface ShuttleStop {
  name: string;
  location: string;
  arrivalTimes: string[];
}

// Security Services Types
export interface SecurityIncident {
  id: string;
  type: 'theft' | 'vandalism' | 'safety' | 'other';
  location: string;
  description: string;
  reportedBy: string;
  date: Date;
  status: 'reported' | 'investigating' | 'resolved';
}

export interface SafetyEscort {
  id: string;
  studentId: string;
  pickupLocation: string;
  dropoffLocation: string;
  requestedTime: Date;
  status: 'requested' | 'assigned' | 'in-progress' | 'completed';
}

// Student Organizations Types
export interface StudentOrganization {
  id: string;
  name: string;
  category: string;
  description: string;
  president: string;
  email: string;
  meetingSchedule?: string;
  memberCount: number;
  joinable: boolean;
}

export interface CampusEvent {
  id: string;
  name: string;
  organizer: string;
  date: Date;
  time: string;
  location: string;
  description: string;
  category: string;
  ticketRequired: boolean;
  ticketPrice?: number;
  capacity?: number;
  registered?: number;
}

// Administrative Services Types
export interface TranscriptRequest {
  id: string;
  studentId: string;
  type: 'official' | 'unofficial';
  deliveryMethod: 'electronic' | 'mail';
  recipient: string;
  requestDate: Date;
  status: 'processing' | 'sent' | 'delivered';
  fee?: number;
}

export interface ITTicket {
  id: string;
  studentId: string;
  category: 'account' | 'network' | 'software' | 'hardware' | 'other';
  subject: string;
  description: string;
  priority: 'low' | 'medium' | 'high';
  status: 'open' | 'in-progress' | 'resolved' | 'closed';
  createdDate: Date;
  resolvedDate?: Date;
}

export interface EnrollmentVerification {
  studentId: string;
  semester: string;
  year: number;
  status: string;
  credits: number;
  issuedDate: Date;
}
