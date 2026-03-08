# Laravel School Timetable Management System - User Manual

## Table of Contents

### Chapter 1: About the System
1.1 System Overview
1.2 Organization of the Manual
1.3 System Requirements
1.4 User Access Levels

### Chapter 2: Features of the System
2.1 Accessing the Web Application
2.2 Creating Lessons
2.3 Managing the Master Timetable
2.4 Viewing Timetables via QR Codes
2.5 Core Admin Features

---

## Chapter 1: About the System

### 1.1 System Overview

The Laravel School Timetable Management System is a comprehensive web-based solution designed to streamline educational institution scheduling. The system automates the complex process of creating and managing class schedules while preventing conflicts and ensuring curriculum requirements are met. Key capabilities include intelligent conflict detection, automated hours tracking, room utilization optimization, and public access to schedules through QR codes. This system transforms manual scheduling processes into an efficient, error-free workflow that benefits administrators, teachers, students, and staff.

### 1.2 Organization of the Manual

This manual is structured to guide all users through the system's functionality in a logical sequence. Chapter 1 provides essential system information and requirements, while Chapter 2 details the core features with step-by-step instructions. The manual is designed for users of all technical levels, from first-time administrators to public users accessing schedules via QR codes. Each section includes clear instructions and visual references to ensure successful navigation of the system's features.

### 1.3 System Requirements

#### Software Requirements
- Modern web browser (Google Chrome, Mozilla Firefox, Safari, or Microsoft Edge)
- Stable internet connection
- JavaScript enabled in browser
- Cookies enabled for session management

#### Hardware Requirements
- Desktop computer, laptop, tablet, or smartphone
- Minimum screen resolution: 1024x768 pixels
- Printer (optional for printing timetables)
- Camera or QR code scanner (for mobile access to public timetables)

#### Server Requirements (General Guidelines)
- Web server capable of running PHP applications
- Database server for data storage
- Sufficient storage for schedule data and user accounts

### 1.4 User Access Levels

#### Administrators
Administrators have full system access and can:
- Create, edit, and delete lessons and schedules
- Manage academic programs, subjects, rooms, and teachers
- View all timetables and generate reports
- Configure system settings and user accounts
- Access master timetable and utilization analytics

#### Teachers
Teachers have limited access focused on their schedules:
- View personal teaching schedules
- Filter schedules by class or subject
- Access room timetables via QR codes
- View basic lesson information

#### Public Access
Public users (students, staff, visitors) can:
- Access room timetables through QR codes without login
- View current and upcoming room schedules
- Check room availability
- Access basic schedule information

---

## Chapter 2: Features of the System

### 2.1 Accessing the Web Application

#### Step-by-Step Login Process for Administrators

1. **Open Web Browser**
   Launch your preferred web browser (Chrome, Firefox, Safari, or Edge).

2. **Navigate to System URL**
   Enter the system URL provided by your institution (e.g., `https://yourschool.edu/schedule`).

3. **Locate Login Section**
   Find the login form on the homepage. It typically includes fields for email and password.

4. **Enter Credentials**
   - **Email**: Enter your administrator email address
   - **Password**: Enter your password

5. **Click Login Button**
   Click the "Login" or "Sign In" button to authenticate.

6. **Access Dashboard**
   Upon successful login, you will be redirected to the administrator dashboard.

```
[Screenshot Placeholder: Login Screen]
- Description: System login form with email and password fields
- Location: Homepage of the application
- Elements: Email field, Password field, Login button, "Forgot Password" link
```

#### Dashboard Overview

The administrator dashboard provides a centralized view of system status and quick access to main functions:

- **Statistics Overview**: Total rooms, lessons, teachers, and utilization rates
- **Quick Actions**: Direct links to lesson creation, master timetable, and room management
- **Recent Activity**: Latest schedule changes and system updates
- **Navigation Menu**: Access to all system modules

```
[Screenshot Placeholder: Administrator Dashboard]
- Description: Main dashboard with statistics panels and quick action buttons
- Location: After successful administrator login
- Elements: Statistics cards, navigation menu, quick action buttons, activity feed
```

### 2.2 Creating Lessons

Creating lessons is the core function of the system. Follow these steps to schedule classes without conflicts.

#### Step-by-Step Lesson Creation Process

1. **Access Lesson Creation**
   - From the dashboard, click "Lessons" in the navigation menu
   - Click "Add New Lesson" or "Create Lesson" button

2. **Select Class**
   - Choose the class (student group) for this lesson
   - Classes are organized by academic program and grade level
   - Only active classes appear in the dropdown

3. **Choose Subject**
   - Select the subject to be taught
   - Subjects display credit information and requirements
   - System shows if subject requires laboratory facilities

4. **Assign Teacher**
   - Select a teacher from the dropdown list
   - Only teachers assigned to the selected subject will appear
   - Teacher experience and assignment details are displayed

5. **Select Room**
   - Choose appropriate room for the lesson
   - System indicates room type (classroom/laboratory)
   - Room capacity and equipment information is shown
   - Red indicators appear if room doesn't meet subject requirements

6. **Set Weekday**
   - Select the day of the week (Monday to Sunday)
   - Note: Weekend classes may have restrictions based on program type

7. **Configure Time**
   - **Start Time**: Choose lesson start time from dropdown
   - **End Time**: Select lesson end time
   - System automatically calculates duration
   - Times are restricted to school hours (typically 7 AM - 9 PM)

8. **Review Conflict Detection**
   - System automatically checks for conflicts
   - Green checkmark indicates no conflicts
   - Red alerts show if:
     - Class already scheduled at this time
     - Teacher has another class scheduled
     - Room is already occupied
     - Time outside school hours
     - Duration violates subject requirements

9. **Resolve Conflicts (if any)**
   - If conflicts exist, system provides alternative suggestions
   - Choose different time, room, or teacher as needed
   - Conflict alerts disappear when resolved

10. **Save Lesson**
    - Click "Save Lesson" or "Create Lesson" button
    - Success message confirms lesson creation
    - Lesson appears in master timetable immediately

```
[Screenshot Placeholder: Lesson Creation Form]
- Description: Complete lesson creation form with all fields visible
- Location: Lessons > Create New Lesson
- Elements: Class dropdown, Subject dropdown, Teacher dropdown, Room dropdown, Weekday selector, Time selectors, Save button
```

```
[Screenshot Placeholder: Conflict Detection Alert]
- Description: Red alert box showing conflict details and suggestions
- Location: Lesson creation form when conflicts are detected
- Elements: Conflict type, conflicting lesson details, alternative time suggestions
```

```
[Screenshot Placeholder: Successful Lesson Creation]
- Description: Green success message confirming lesson was created
- Location: After saving lesson successfully
- Elements: Success message, lesson summary, redirect options
```

#### Important Notes for Lesson Creation

- **Duration Rules**: Laboratory lessons typically require 3-5 hours, lecture lessons 1-3 hours
- **Weekend Restrictions**: Some program types may not allow weekend classes
- **Teacher Assignment**: Teachers must be assigned to subjects before scheduling
- **Room Requirements**: Laboratory subjects require lab rooms with appropriate equipment

### 2.3 Managing the Master Timetable

The master timetable provides a comprehensive view of all room schedules in a matrix format.

#### Accessing the Master Timetable

1. **Navigate to Master Timetable**
   - From dashboard, click "Master Timetable" in navigation menu
   - Or go to "Room Management" > "Master Timetable"

2. **Understanding the Layout**
   - **Columns**: Individual rooms (classrooms and laboratories)
   - **Rows**: Time slots in 30-minute intervals
   - **Cells**: Lesson information or empty slots

#### Reading the Master Timetable

- **Lesson Boxes**: Show subject, teacher, class, and room information
- **Color Coding**: Different colors indicate lesson types or conflicts
- **Duration Indicators**: Multi-hour lessons span multiple time slots
- **Empty Cells**: Available time slots for scheduling

```
[Screenshot Placeholder: Master Timetable Overview]
- Description: Grid view showing rooms as columns and time slots as rows
- Location: Room Management > Master Timetable
- Elements: Room headers, time slot labels, lesson boxes, empty cells, legend
```

#### Navigation and Filtering

1. **Time Navigation**
   - Scroll vertically to view different time periods
   - Use time range selectors to focus on specific hours

2. **Room Filtering**
   - Filter by room type (classroom/laboratory)
   - Show/hide specific rooms
   - Filter by room capacity or equipment

3. **Statistics Panel**
   - View room utilization percentages
   - See available hours per room
   - Check total occupied/empty slots

```
[Screenshot Placeholder: Room Utilization Indicators]
- Description: Statistics panel showing usage metrics for each room
- Location: Master timetable sidebar or top panel
- Elements: Utilization percentages, available hours, color-coded indicators
```

### 2.4 Viewing Timetables via QR Codes

The system provides public access to room schedules through QR codes, allowing students and staff to view room availability without login credentials.

#### How QR Codes Work

Each room in the system has a unique QR code that:
- Links to a public timetable page for that specific room
- Requires no authentication to access
- Shows real-time schedule information
- Is mobile-friendly for easy scanning

#### Step-by-Step QR Code Usage

1. **Locate QR Code**
   - QR codes are typically displayed outside each room
   - Codes may also be available in room directories
   - Each QR code is unique to its room

2. **Scan QR Code**
   - Open camera app on smartphone or tablet
   - Point camera at QR code until it's recognized
   - Tap the notification that appears to open the link
   - Alternatively, use a QR code scanning app

3. **View Public Timetable**
   - Room schedule loads automatically
   - Current day is highlighted by default
   - Navigate between different weekdays using tabs

4. **Read Schedule Information**
   - **Time Slots**: See all scheduled lessons for the day
   - **Subject Information**: View subject names and types
   - **Teacher Names**: See which teachers are assigned
   - **Class Information**: Identify which classes use the room
   - **Empty Slots**: Identify available time slots

5. **Navigate Between Days**
   - Click weekday tabs to view different days
   - Use navigation arrows to move between weeks
   - Current day is automatically highlighted

```
[Screenshot Placeholder: QR Code Example]
- Description: Sample QR code printed on room door signage
- Location: Physical room entrance or room directory
- Elements: QR code image, room name, instructions for scanning
```

```
[Screenshot Placeholder: Public Timetable View]
- Description: Mobile-friendly timetable view accessible via QR code
- Location: Public URL accessed through QR code scan
- Elements: Room name, weekday tabs, time slots, lesson information, no login required
```

#### Available Information for Public Users

Public timetable access includes:
- Current and upcoming room schedules
- Subject and teacher names
- Class information
- Time slot availability
- Room details (capacity, type)

#### Security and Privacy

Public access is designed to be secure:
- QR codes use encrypted identifiers
- No personal student information is displayed
- Only schedule information is accessible
- System administration features remain protected

### 2.5 Core Admin Features

Administrators have access to essential features for managing the timetable system effectively.

#### Lesson Management

1. **View All Lessons**
   - Navigate to "Lessons" section
   - View complete list of scheduled lessons
   - Use filters to find specific lessons

2. **Edit Existing Lessons**
   - Click "Edit" button next to any lesson
   - Modify lesson details (time, room, teacher, etc.)
   - System checks for conflicts before saving

3. **Delete Lessons**
   - Click "Delete" button to remove lessons
   - Confirm deletion in popup dialog
   - Lesson is immediately removed from timetable

4. **Search and Filter Lessons**
   - Filter by class, teacher, subject, or weekday
   - Use search box to find specific lessons
   - Adjust number of lessons displayed per page

#### Conflict Detection and Resolution

The system continuously monitors for scheduling conflicts:

1. **Automatic Detection**
   - Conflicts are detected in real-time during lesson creation
   - Existing lessons are checked when schedules change
   - Multiple conflict types are monitored simultaneously

2. **Conflict Types**
   - **Class Conflicts**: Same class scheduled twice at same time
   - **Teacher Conflicts**: Teacher assigned to multiple classes simultaneously
   - **Room Conflicts**: Room double-booked for different lessons

3. **Resolution Tools**
   - Alternative time suggestions
   - Available room recommendations
   - Teacher availability checking

#### Export Functionality

Administrators can export timetable data for reporting and sharing:

1. **Export Master Timetable**
   - Click "Export" button in master timetable
   - Choose export format (JSON, CSV options)
   - Download file for external use

2. **Export Room Schedules**
   - Export individual room timetables
   - Select specific date ranges
   - Include or exclude empty time slots

#### Basic System Administration

1. **User Management**
   - Create administrator accounts
   - Manage teacher profiles
   - Assign appropriate access levels

2. **System Configuration**
   - Set school hours and time slot intervals
   - Configure conflict detection rules
   - Manage system-wide settings

3. **Reporting and Analytics**
   - View room utilization statistics
   - Monitor scheduling progress
   - Generate usage reports

---

## Quick Reference Guide

### Common Tasks Summary

| Task | Navigation | Key Steps |
|------|------------|-----------|
| Login as Admin | Homepage | Enter email/password → Click Login |
| Create Lesson | Lessons → Add New | Select class/subject/teacher/room → Set time → Save |
| View Master Timetable | Room Management → Master Timetable | View grid → Navigate time slots |
| Scan QR Code | Physical QR code | Scan with phone → View public schedule |
| Edit Lesson | Lessons → Edit (next to lesson) | Modify details → Check conflicts → Save |

### Troubleshooting Common Issues

**Cannot Login**
- Verify correct email and password
- Check if caps lock is on
- Contact system administrator if needed

**Lesson Creation Shows Conflicts**
- Try different time slot
- Select different room
- Choose alternative teacher
- Check if class already scheduled

**QR Code Not Working**
- Ensure good lighting when scanning
- Hold camera steady
- Try different scanning app
- Verify QR code is not damaged

**Master Timetable Not Loading**
- Refresh the page
- Check internet connection
- Try different browser
- Contact IT support

### Contact Information

For technical support or system questions:
- **IT Department**: [Insert contact information]
- **System Administrator**: [Insert contact information]
- **Training Coordinator**: [Insert contact information]

---

**Version**: 1.0  
**Last Updated**: [Current Date]  
**System**: Laravel School Timetable Management System
