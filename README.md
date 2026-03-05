# Laravel School Timetable Management System

A comprehensive web-based timetable management system designed for educational institutions. The system provides centralized management of class schedules, room assignments, teacher allocations, and academic programs with advanced conflict detection and hours tracking capabilities.

## System Overview

The Laravel School Timetable Management System is built on Laravel 10 and implements role-based access control for administrators and teachers. It enables efficient scheduling of lessons while preventing conflicts and ensuring curriculum requirements are met through automated hours tracking.

## Core Features

### User Management & Access Control
- **Administrators**: Full system access to manage all entities
- **Teachers**: View-only access to personal teaching schedules
- **Authentication**: Secure login with rate limiting and session management

### Academic Program Management
- **Program Types**: Senior High School (2 years), College (4 years), Diploma/TESDA (3 years)
- **Grade Level Organization**: Hierarchical structure within programs
- **Program Validation**: Prevents program type changes when weekend lessons exist

### School Class Management
- **Class Organization**: Classes linked to academic programs and grade levels
- **Automatic Display Names**: Generated including program and section information

### Subject Management
- **Subject Types**: Minor and Major subjects
- **Scheduling Modes**: Pure Laboratory, Pure Lecture, or Flexible (mixed)
- **Credit System**: Lecture units (1 hour/unit) and Lab units (3 hours/unit)
- **Hours Tracking**: Automatic calculation of required vs scheduled hours

### Teacher-Subject Assignment
- **Assignment Management**: Link teachers to subjects with experience tracking
- **Validation**: Ensures teachers are properly assigned before scheduling

### Room Management
- **Room Types**: Regular classrooms and Laboratory rooms
- **Equipment Tracking**: Lab equipment availability and capacity management
- **QR Code Generation**: Public access to room timetables via QR codes

### Lesson Scheduling & Conflict Detection
- **Advanced Conflict Detection**: Prevents class, teacher, and room double-booking
- **Time Validation**: School hours enforcement (7 AM - 9 PM)
- **Duration Rules**: Laboratory lessons (3-5 hours), Lecture lessons (1-3 hours)
- **Weekend Restrictions**: Only Diploma programs may schedule Saturday/Sunday classes
- **Inline Editing**: Direct editing from calendar views with real-time validation

### Timetable Views
- **Master Timetable**: Room-based matrix showing all schedules
- **Room Timetables**: Individual room schedules with public access
- **Teacher Calendar**: Personal teaching schedules for teachers
- **Statistics**: Room utilization and availability metrics

### Hours Tracking System
- **Per-Class Tracking**: Separate lecture and laboratory hours monitoring
- **Progress Monitoring**: Remaining hours calculation and completion percentages
- **Curriculum Compliance**: Ensures required hours are met

## Technical Specifications

- **Framework**: Laravel 10
- **PHP Version**: 8.1+
- **Database**: MySQL with Eloquent ORM
- **Frontend**: Bootstrap with responsive design
- **JavaScript**: AJAX for real-time validation and updates
- **Additional Packages**: DataTables for data management, QR code generation

## Installation & Setup

1. Clone the repository
2. Copy [.env.example]
3. Run `composer install`
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed` (includes sample data)
6. Launch the application URL

## Default Credentials

- **Administrator**: admin@admin.com / password
- **Teacher**: teacher@teacher.com / password

## Key Business Rules

- **Laboratory Lessons**: Minimum 3 hours, maximum 5 hours per session
- **Lecture Lessons**: 1-3 hours in 30-minute increments
- **Weekend Classes**: Only allowed for Diploma programs
- **Conflict Prevention**: Real-time detection for classes, teachers, and rooms
- **Credit System**: 1 credit = 1 lecture hour or 3 laboratory hours

## License

Feel free to use and modify for your educational institution's needs.