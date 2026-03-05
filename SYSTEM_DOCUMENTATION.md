# Laravel School Timetable Calendar System - System Documentation

## System Description

The Laravel School Timetable Calendar is a comprehensive web-based timetable management system designed for educational institutions. It provides a centralized platform for managing class schedules, room assignments, teacher allocations, and academic programs. The system is built using Laravel 8 framework and implements role-based access control with support for administrators and teachers.

## Purpose

The primary purpose of this system is to:

1. **Schedule Management**: Enable administrators to create, edit, and manage class schedules (lessons) with proper conflict detection
2. **Resource Allocation**: Efficiently assign teachers, rooms, and subjects to classes while preventing scheduling conflicts
3. **Academic Program Management**: Organize classes under academic programs (Senior High School, College, Diploma/TESDA) with grade levels
4. **Room Management**: Track and manage classroom and laboratory facilities with capacity and equipment information
5. **Hours Tracking**: Monitor and track lecture and laboratory hours for subjects to ensure curriculum requirements are met
6. **Public Access**: Provide public access to room timetables via QR codes without requiring authentication
7. **Teacher Dashboard**: Allow teachers to view their personal teaching schedules

## Core Features

### 1. User Management & Authentication

- **Role-Based Access Control**: 
  - Administrators (`is_admin`): Full system access
  - Teachers (`is_teacher`): View-only access to their schedules
  - Student role has been removed from the system
- **Authentication**: 
  - Login with rate limiting (5 attempts per minute)
  - Password reset and email verification disabled
  - Session management with graceful handling of expired sessions

### 2. Academic Program Management

- **Program Types**:
  - Senior High School (2-year duration)
  - College (4-year duration)
  - Diploma Program/TESDA (3-year duration)
- **Grade Levels**: Hierarchical organization of grade levels within programs
- **Program Attributes**: Code, name, description, type, duration, active status

### 3. School Class Management

- **Class Organization**: Classes belong to academic programs and optionally to grade levels
- **Class Attributes**: Name, section, program association, active status
- **Display Names**: Automatic generation of display names including program and section information

### 4. Subject Management

- **Subject Types**: 
  - Minor Subject
  - Major Subject
- **Scheduling Modes**:
  - Lab (Pure Laboratory)
  - Lecture (Pure Lecture)
  - Flexible (Mixed)
- **Credit System**:
  - Total credits
  - Lecture units (1 hour per unit)
  - Lab units (3 hours per unit)
- **Lab Requirements**: 
  - Requires lab facility flag
  - Requires equipment flag
  - Equipment requirements description
- **Hours Tracking**:
  - Automatic calculation of total lecture hours (lecture_units × 1)
  - Automatic calculation of total lab hours (lab_units × 3)
  - Tracking of scheduled hours per class
  - Remaining hours calculation
  - Scheduling progress percentage

### 5. Teacher-Subject Assignment

- **Assignment Management**: Link teachers to subjects they can teach
- **Assignment Attributes**: 
  - Primary assignment flag
  - Experience years
  - Active status
  - Notes
- **Validation**: Ensures teachers are assigned to subjects before scheduling lessons

### 6. Room Management

- **Room Types**:
  - Regular classrooms
  - Laboratory rooms (`is_lab` flag)
- **Room Attributes**: 
  - Name, description
  - Capacity
  - Lab equipment availability (`has_equipment` flag)
- **QR Code Generation**: 
  - Unique identifiers for each room
  - Public access URLs via QR codes
  - Multiple QR code API fallback system (QuickChart, Google Charts, QR Server)

### 7. Lesson/Schedule Management

- **Lesson Attributes**:
  - Weekday (1-7: Monday-Sunday)
  - Start time and end time
  - Duration in hours (calculated and stored)
  - Lesson type (lecture or laboratory)
  - Associated class, teacher, room, and subject
- **Conflict Detection**:
  - Class conflicts: Prevents double-booking of classes
  - Teacher conflicts: Prevents teachers from teaching multiple classes simultaneously
  - Room conflicts: Prevents room double-booking
  - Real-time conflict checking via validation API endpoints
- **Time Validation**:
  - School hours validation (7 AM to 9 PM)
  - Time availability checking
  - End time must be after start time
- **Inline Editing**: 
  - Direct editing of lessons from calendar views
  - AJAX-based form data retrieval
  - Conflict checking before updates

### 8. Master Timetable View

- **Room-Based Matrix**: Displays all rooms as columns with time slots as rows
- **Time Slots**: 30-minute intervals from 7 AM to 9 PM
- **Visual Indicators**:
  - Conflict highlighting
  - Lab lesson indicators
  - Duration-based styling (long/short lessons)
- **Statistics**:
  - Room utilization percentages
  - Available hours per room
  - Total occupied/empty slots
  - Rooms with/without lessons
- **Export Functionality**: Export timetable data

### 9. Room Timetable View

- **Individual Room Schedules**: View schedule for a specific room
- **Calendar Format**: Weekly view showing all lessons in a room
- **Public Access**: Accessible via QR code without authentication

### 10. Teacher Calendar

- **Personal Schedule**: Teachers can view their own teaching schedule
- **Weekly View**: Calendar showing all lessons assigned to the teacher
- **Filtering**: Filter by class if needed

### 11. Hours Tracking System

- **Per-Class Tracking**: Track scheduled hours for each subject-class combination
- **Lecture vs Lab Separation**: Separate tracking for lecture and laboratory hours
- **Progress Monitoring**: 
  - Remaining hours calculation
  - Progress percentage
  - Full scheduling status
- **Optimized Queries**: Cached calculations to prevent redundant database queries

### 12. Validation & Conflict Resolution

- **Pre-Save Validation**: 
  - Teacher-subject assignment validation
  - Time availability checking
  - Conflict detection
  - School hours validation
- **Alternative Time Suggestions**: API endpoint to suggest alternative time slots
- **Available Rooms**: API endpoint to find available rooms for a time slot
- **Teacher Availability**: Check teacher availability for specific times

### 13. Search & Filtering

- **Lesson Filtering**: 
  - By class
  - By teacher
  - By subject
  - By weekday
  - Text search across class, teacher, subject, room names
- **Pagination**: Configurable items per page (10, 20, 50, 100)
- **Session Persistence**: Filters persist across page navigation

## How the System Works

### Architecture Overview

The system follows a standard Laravel MVC (Model-View-Controller) architecture with service classes for complex business logic.

### Data Flow

1. **User Authentication**:
   - User logs in through the authentication system
   - Role-based middleware (`AuthGates`) checks permissions
   - Users are redirected to appropriate dashboards based on role

2. **Lesson Creation Process**:
   - Admin selects class, subject, teacher, room, weekday, and time
   - System validates:
     - Teacher is assigned to the subject
     - Time slot is within school hours (7 AM - 9 PM)
     - No conflicts exist (class, teacher, or room already booked)
   - Duration is automatically calculated from start and end times
   - Lesson type (lecture/lab) is set based on subject requirements
   - Lesson is saved to database
   - Hours tracking is updated for the subject-class combination

3. **Conflict Detection**:
   - `SchedulingConflictService` checks for overlapping time slots
   - Validates that class, teacher, and room are not double-booked
   - Returns detailed conflict information including conflicting lesson details
   - Prevents saving if conflicts exist

4. **Hours Tracking**:
   - When a lesson is created/updated, the system calculates duration
   - Duration is stored in `duration_hours` field
   - Subject model calculates:
     - Scheduled hours per class (lecture and lab separately)
     - Remaining hours needed
     - Progress percentage
   - Calculations are cached to optimize performance

5. **Master Timetable Generation**:
   - `MasterTimetableService` generates a matrix of all rooms and time slots
   - For each time slot, checks which rooms have lessons
   - Formats lesson data for display
   - Calculates statistics (utilization, available hours)
   - Applies CSS classes for visual indicators

6. **QR Code Generation**:
   - `QRCodeService` generates unique identifiers using SHA-256 hash
   - Creates public URLs for room timetables
   - Generates QR code images using external APIs with fallback system
   - Public controller validates identifiers and displays room schedules

### Key Services

1. **MasterTimetableService**: 
   - Generates master timetable data
   - Calculates room utilization
   - Provides available time slots

2. **SchedulingConflictService**: 
   - Detects scheduling conflicts
   - Validates teacher-subject assignments
   - Provides comprehensive validation results

3. **TeacherCalendarService**: 
   - Generates teacher-specific calendar data
   - Filters lessons by teacher

4. **RoomCalendarService**: 
   - Generates room-specific calendar data
   - Used for both admin and public views

5. **QRCodeService**: 
   - Generates room identifiers
   - Creates QR code images
   - Manages fallback API systems

6. **TimeService**: 
   - Generates time ranges (7 AM - 9 PM, 30-minute intervals)
   - Handles time formatting

7. **CacheInvalidationService**: 
   - Manages cache invalidation for optimized performance

### Database Schema

**Core Tables**:
- `users`: User accounts with role flags (is_admin, is_teacher)
- `lessons`: Schedule entries with weekday, times, duration, type
- `school_classes`: Classes organized by program and grade level
- `subjects`: Subject definitions with credits, units, and requirements
- `rooms`: Room information with lab and equipment flags
- `academic_programs`: Program definitions (Senior High, College, Diploma)
- `grade_levels`: Grade level definitions within programs
- `teacher_subjects`: Many-to-many relationship between teachers and subjects
- `roles` and `permissions`: Role-based access control (legacy support)

**Relationships**:
- Lessons belong to: Class, Teacher (User), Room, Subject
- Classes belong to: Academic Program, Grade Level (optional)
- Teachers have many Subjects through TeacherSubject pivot
- Subjects have many Teachers through TeacherSubject pivot
- Programs have many Grade Levels and Classes

### Business Rules

1. **Scheduling Rules**:
   - A class cannot have two lessons at the same time
   - A teacher cannot teach two classes simultaneously
   - A room cannot host two lessons at the same time
   - Lessons must be within school hours (7 AM - 9 PM)
   - End time must be after start time

2. **Teacher Assignment Rules**:
   - Teachers must be assigned to a subject before scheduling lessons
   - Only active teacher-subject assignments are valid
   - Teacher must have `is_teacher` flag set to true

3. **Hours Tracking Rules**:
   - Lecture units: 1 hour per unit
   - Lab units: 3 hours per unit
   - Duration is calculated from start and end times
   - Duration is rounded to nearest 30 minutes
   - Hours are tracked separately for lecture and lab types

4. **Subject Requirements**:
   - Subjects with `requires_lab = true` should be scheduled in lab rooms
   - Subjects with `requires_equipment = true` need rooms with equipment
   - Scheduling mode determines if subject can be lecture, lab, or flexible

5. **Active Status**:
   - Only active classes can be scheduled
   - Only active subjects can be assigned
   - Only active teacher-subject assignments are valid

### User Interfaces

1. **Admin Dashboard**: Overview with statistics (rooms, lessons, teachers)
2. **Lesson Management**: CRUD interface with filtering and search
3. **Master Timetable**: Room-based matrix view with conflict indicators
4. **Room Timetables**: Individual room schedule views
5. **Teacher Calendar**: Personal schedule view for teachers
6. **Public Room View**: QR code-accessible room schedules (no login required)

### API Endpoints

**Validation APIs** (Admin only):
- `POST /admin/validation/check-conflicts`: Check for scheduling conflicts
- `POST /admin/validation/available-rooms`: Get available rooms for time slot
- `GET /admin/validation/subjects/{subject}/teachers`: Get teachers for subject
- `POST /admin/validation/teacher-availability`: Check teacher availability
- `POST /admin/validation/alternative-times`: Get alternative time suggestions

**Lesson Inline Editing APIs** (Admin only):
- `GET /admin/lessons/form-data`: Get form data for inline editing
- `GET /admin/lessons/{id}/details`: Get lesson details
- `POST /admin/lessons/inline`: Create lesson via inline form
- `PUT /admin/lessons/{id}/inline`: Update lesson via inline form
- `DELETE /admin/lessons/{id}/inline`: Delete lesson
- `POST /admin/lessons/check-conflicts`: Check conflicts for inline editing

**Master Timetable APIs** (Admin only):
- `GET /admin/room-management/master-timetable/available-slots`: Get available time slots
- `GET /admin/room-management/master-timetable/room-utilization`: Get room utilization stats
- `GET /admin/room-management/master-timetable/timetable-data`: Get timetable data
- `GET /admin/room-management/master-timetable/lesson-details`: Get lesson details
- `POST /admin/room-management/master-timetable/check-conflicts`: Check scheduling conflicts
- `GET /admin/room-management/master-timetable/quick-stats`: Get quick statistics
- `GET /admin/room-management/master-timetable/export`: Export timetable
- `GET /admin/room-management/master-timetable/export-all`: Export all timetables

### Security Features

1. **Authentication**: Laravel's built-in authentication system
2. **Authorization**: Gate-based permission checking
3. **Rate Limiting**: Login attempts limited to 5 per minute
4. **CSRF Protection**: Enabled for all forms
5. **Role-Based Access**: Middleware checks user roles
6. **QR Code Security**: SHA-256 hashed identifiers prevent guessing

### Performance Optimizations

1. **Query Optimization**: 
   - Eager loading relationships (with())
   - Indexed database columns
   - Cached hours calculations

2. **Caching**: 
   - Instance-level caching for hours tracking
   - Cache invalidation service for managing cache

3. **Efficient Queries**: 
   - Single query for hours data per class
   - Grouped queries for timetable generation

## Technical Stack

- **Framework**: Laravel 8
- **Database**: MySQL/PostgreSQL (via Laravel migrations)
- **Frontend**: Blade templates with JavaScript/jQuery
- **Authentication**: Laravel Passport (API tokens)
- **Validation**: Custom validation rules and form requests
- **Time Handling**: Carbon library
- **QR Codes**: External APIs (QuickChart, Google Charts, QR Server)

## Current Implementation Status

All features described above are currently implemented and functional. The system includes:

✅ User authentication and role management  
✅ Academic program and grade level management  
✅ School class management  
✅ Subject management with credit system  
✅ Teacher-subject assignment system  
✅ Room management with lab/equipment tracking  
✅ Lesson scheduling with conflict detection  
✅ Hours tracking (lecture and lab)  
✅ Master timetable view  
✅ Room timetable views  
✅ Teacher calendar  
✅ Public room access via QR codes  
✅ Inline lesson editing  
✅ Search and filtering  
✅ Validation APIs  
✅ Export functionality  

The system is production-ready with comprehensive validation, error handling, and user-friendly interfaces.
