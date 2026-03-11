# Laravel School Timetable Management System - User Manual

## Table of Contents

### Chapter 1: About the System
1.1 System Overview
1.2 Organization of the Manual
1.3 System Requirements
1.4 User Access Levels

### Chapter 2: Features
2.1 System Access and Navigation
   - 2.1.1 Administrator Login and Dashboard
   - 2.1.2 Teacher Login and Dashboard
   - 2.1.3 Public Access via QR Codes
2.2 Academic Management Features
   - 2.2.1 Managing Academic Programs
   - 2.2.2 Managing Subjects
   - 2.2.3 Managing School Classes
2.3 Resource Management Features
   - 2.3.1 Managing Rooms
   - 2.3.2 Room QR Code Generation
2.4 User Management Features
   - 2.4.1 Managing Administrators
   - 2.4.2 Managing Teachers
2.5 Scheduling Features
   - 2.5.1 Creating Lessons
   - 2.5.2 Managing Lessons
   - 2.5.3 Conflict Detection and Resolution
   - 2.5.4 Master Timetable Management
2.6 Schedule Viewing Features
   - 2.6.1 Teacher Schedule Views
   - 2.6.2 Calendar View Features
   - 2.6.3 Room Schedule Access
2.7 Public Access Features
   - 2.7.1 QR Code Room Schedule Access
   - 2.7.2 Public Timetable Interface
   - 2.7.3 Room Availability Checking
2.8 System Utilities
   - 2.8.1 Export Functionality
   - 2.8.2 Filtering and Search Features
   - 2.8.3 Mobile Responsive Features

---

## Chapter 1: About the System

### 1.1 System Overview

The Laravel School Timetable Management System is a comprehensive web-based solution designed to streamline educational institution scheduling. The system automates the complex process of creating and managing class schedules while preventing conflicts and ensuring curriculum requirements are met. Key capabilities include intelligent conflict detection, automated hours tracking, room utilization optimization, and public access to schedules through QR codes. This system transforms manual scheduling processes into an efficient, error-free workflow that benefits administrators, teachers, and staff.

### 1.2 Organization of the Manual

This manual is structured into two main chapters to provide comprehensive documentation of all system features:
- **Chapter 1**: Essential system information, requirements, and user access levels for all users
- **Chapter 2**: Complete feature catalog covering all system functionality organized by category, with detailed instructions and step-by-step processes for administrators, teachers, and public users

Chapter 2 serves as a comprehensive feature reference, organizing all system capabilities into logical categories including system access, academic management, resource management, user management, scheduling, schedule viewing, public access, and system utilities. Each section includes clear instructions with step-by-step processes and visual references to ensure successful navigation of the system's features, making this manual suitable for users of all technical levels and access requirements.

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
- Manage academic programs, subjects, rooms, teachers, and administrators
- View all timetables and generate reports
- Configure system settings and user accounts
- Access master timetable and utilization analytics
- Export schedule data and manage system-wide features

#### Teachers
Teachers have limited access focused on their schedules:
- View personal teaching schedules and calendar
- Filter schedules by class or subject
- Access room timetables via QR codes
- View basic lesson information and room availability

#### Public Access
Public users (staff, visitors) can:
- Access room timetables through QR codes without login
- View current and upcoming room schedules
- Check room availability
- Access basic schedule information

---

## Chapter 2: Features

### 2.1 System Access and Navigation

#### 2.1.1 Administrator Login and Dashboard

**Step-by-Step Login Process for Administrators**

1. **Open Web Browser**
   Launch your preferred web browser (Chrome, Firefox, Safari, or Edge).

2. **Navigate to System URL**
   Enter the system URL provided by your institution (e.g., `https://yourschool.edu/schedule`).

3. **Locate Login Section**
   Find the login form on the homepage. It includes fields for email and password.

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

**Administrator Dashboard Overview**

The administrator dashboard provides a centralized view of system status and quick access to main functions:

- **Statistics Overview**: Total rooms, lessons, teachers, and utilization rates
- **Quick Actions**: Direct links to lesson creation, master timetable, and user management
- **Navigation Menu**: Access to all system modules (Academic Management, Resource Management, User Management, Scheduling)
- **Recent Activity**: Latest schedule changes and system updates

```
[Screenshot Placeholder: Administrator Dashboard]
- Description: Main dashboard with statistics panels and quick action buttons
- Location: After successful administrator login
- Elements: Statistics cards, navigation menu, quick action buttons, activity feed
```

**Navigation Menu Structure**

- **Dashboard**: System overview and statistics
- **Academic Management**: Programs, Subjects, School Classes
- **Resource Management**: Rooms, Master Timetable
- **User Management**: Administrators, Teachers
- **Scheduling**: Lessons (Create, View, Edit, Delete)

#### 2.1.2 Teacher Login and Dashboard

**Step-by-Step Login Process for Teachers**

1. **Open Web Browser**
   Launch your preferred web browser (Chrome, Firefox, Safari, or Edge).

2. **Navigate to System URL**
   Enter the system URL provided by your institution.

3. **Enter Teacher Credentials**
   - **Email**: Enter your teacher email address
   - **Password**: Enter your password

4. **Access Teacher Dashboard**
   Upon successful login, you will be redirected to the teacher dashboard.

```
[Screenshot Placeholder: Teacher Login Screen]
- Description: Teacher login form with email and password fields
- Location: Teacher login page
- Elements: Email field, Password field, Login button
```

**Teacher Dashboard Overview**

The teacher dashboard provides personalized schedule information and quick access to teaching-related features:

- **Welcome Message**: Personalized greeting with teacher name
- **Statistics Cards**: 
  - Total classes this week
  - Today's classes count
  - Tomorrow's classes count
- **Today's Schedule**: Detailed view of today's teaching assignments
- **Upcoming Classes**: Preview of next teaching sessions
- **Quick Navigation**: Access to calendar and room schedules

```
[Screenshot Placeholder: Teacher Dashboard]
- Description: Teacher dashboard with schedule statistics and today's classes
- Location: After successful teacher login
- Elements: Welcome message, statistics cards, today's schedule, upcoming classes
```

#### 2.1.3 Public Access via QR Codes

**How QR Codes Work**

Each room in the system has a unique QR code that:
- Links to a public timetable page at `/public/room/{identifier}` 
- Requires no authentication to access
- Shows real-time schedule information
- Is mobile-friendly for easy scanning
- Uses encrypted room identifiers for security

**Step-by-Step QR Code Usage**

1. **Locate QR Code**
   - QR codes are displayed outside each room door
   - Codes may also be available in room directories
   - Each QR code is unique to its room
   - QR codes include room name for easy identification

2. **Scan QR Code**
   - Open camera app on smartphone or tablet
   - Point camera at QR code until it's recognized
   - Tap the notification that appears to open the link
   - Alternatively, use a QR code scanning app

3. **View Public Timetable**
   - Room schedule loads automatically
   - Current day is highlighted by default

```
[Screenshot Placeholder: QR Code Example]
- Description: Sample QR code printed on room door signage
- Location: Physical room entrance or room directory
- Elements: QR code image, room name, instructions for scanning
```

### 2.2 Academic Management Features

#### 2.2.1 Managing Academic Programs

Academic programs organize the educational structure (Senior High School, Diploma Programs, College).

**Creating Academic Programs**

1. **Navigate to Academic Programs**
   - From dashboard, click "Academic Management" → "Academic Programs"

2. **Create New Program**
   - Click "Add Academic Program" button

3. **Fill Program Information**
   - **Program Name**: Enter full program name (e.g., "Bachelor of Science in Information Technology")
   - **Program Code**: Enter short code (e.g., "BSIT")
   - **Program Type**: Select from dropdown (Senior High School, Diploma Program, College)
   - **Description**: Enter program description
   - **Status**: Set as Active or Inactive

4. **Save Program**
   - Click "Save" button to create the program

```
[Screenshot Placeholder: Academic Program Creation Form]
- Description: Form for creating new academic programs
- Location: Academic Management > Academic Programs > Add Academic Program
- Elements: Program name field, code field, type dropdown, description textarea, status toggle, save button
```

**Managing Existing Programs**

1. **View Program List**
   - Navigate to "Academic Management" → "Academic Programs"
   - View all programs with search and filter options

2. **Edit Program**
   - Click "Edit" button next to program
   - Modify program details
   - **Important**: Changing program type affects weekend class restrictions

3. **Delete Program**
   - Click "Delete" button
   - Confirm deletion in popup dialog
   - **Warning**: Deleting programs with associated classes may affect existing schedules

#### 2.2.2 Managing Subjects

Subjects define the courses taught within academic programs.

**Creating Subjects**

1. **Navigate to Subjects**
   - From dashboard, click "Academic Management" → "Subjects"

2. **Create New Subject**
   - Click "Add Subject" button

3. **Fill Subject Information**
   - **Subject Name**: Enter subject name (e.g., "Database Management")
   - **Subject Code**: Enter subject code (e.g., "DBM101")
   - **Credit Hours**: Set credit value (1-3 credits)
   - **Subject Type**: Choose Core or Elective
   - **Lab Requirement**: Toggle if subject requires laboratory facilities
   - **Equipment Requirement**: Toggle if subject needs special equipment
   - **Scheduling Mode**: 
     - Fixed (Pure Lab): Auto-selects laboratory type
     - Lecture (Pure Lecture): Auto-selects lecture type  
     - Flexible: User selects lesson type
   - **Assign Teachers**: Select teachers who can teach this subject

4. **Save Subject**
   - Click "Save" button

```
[Screenshot Placeholder: Subject Creation Form]
- Description: Comprehensive subject creation form with scheduling options
- Location: Academic Management > Subjects > Add Subject
- Elements: Subject fields, credit hours, type toggles, scheduling mode, teacher assignment
```

**Business Rules for Subjects**

- **Credit System**: 1 credit = 3 lab hours or 1 lecture hour
- **Lab Requirements**: Subjects requiring labs must be scheduled in laboratory rooms
- **Teacher Assignment**: Teachers must be assigned to subjects before scheduling lessons

#### 2.2.3 Managing School Classes

School classes represent student groups within academic programs and grade levels.

**Creating School Classes**

1. **Navigate to School Classes**
   - From dashboard, click "Academic Management" → "School Classes"

2. **Create New Class**
   - Click "Add School Class" button

3. **Fill Class Information**
   - **Class Name**: Enter class name (e.g., "BSIT-1A")
   - **Academic Program**: Select from dropdown
   - **Grade Level**: Select appropriate grade level
   - **Max Students**: Set maximum student capacity
   - **Status**: Set as Active or Inactive

4. **Save Class**
   - Click "Save" button

```
[Screenshot Placeholder: School Class Creation Form]
- Description: Form for creating student classes
- Location: Academic Management > School Classes > Add School Class
- Elements: Class name, program dropdown, grade level selector, capacity field, status toggle
```

### 2.3 Resource Management Features

#### 2.3.1 Managing Rooms

Rooms represent physical spaces where classes are conducted.

**Creating Rooms**

1. **Navigate to Rooms**
   - From dashboard, click "Resource Management" → "Rooms"

2. **Create New Room**
   - Click "Add Room" button

3. **Fill Room Information**
   - **Room Name**: Enter room name (e.g., "Computer Lab 1")
   - **Description**: Enter room description
   - **Room Type**: Choose Classroom or Laboratory
   - **Capacity**: Set maximum student capacity
   - **Equipment**: Toggle if room has special equipment
   - **Status**: Set as Active or Inactive

4. **Save Room**
   - Click "Save" button

```
[Screenshot Placeholder: Room Creation Form]
- Description: Form for adding new rooms to the system
- Location: Resource Management > Rooms > Add Room
- Elements: Room name, description, type selector, capacity field, equipment toggle, status selector
```

**Managing Room Features**

- **Room Types**: 
  - Classroom: For lecture-type lessons
  - Laboratory: For lab-type lessons with equipment
- **Equipment**: Rooms with equipment can only be used by subjects requiring equipment
- **Capacity**: System prevents scheduling classes that exceed room capacity

#### 2.3.2 Room QR Code Generation

Each room has a unique QR code for public schedule access.

**Generating Room QR Codes**

1. **Access QR Codes**
   - Navigate to "Resource Management" → "Rooms"
   - Click "All QR Codes" button

2. **View QR Codes**
   - All room QR codes displayed in grid format
   - Each QR code shows room name and information

3. **Print QR Codes**
   - Click "Print" button to generate printable QR codes
   - QR codes can be placed outside rooms for public access

```
[Screenshot Placeholder: Room QR Codes Display]
- Description: Grid showing all room QR codes for printing
- Location: Resource Management > Rooms > All QR Codes
- Elements: QR code images, room names, print button
```

### 2.4 User Management Features

#### 2.4.1 Managing Administrators

Administrators have full system access and management capabilities.

**Creating Administrators**

1. **Navigate to User Management**
   - From dashboard, click "User Management" → "Administrators"

2. **Create New Administrator**
   - Click "Add Administrator" button

3. **Fill Administrator Information**
   - **Name**: Enter full name
   - **Email**: Enter email address (used for login)
   - **Password**: Set initial password
   - **Confirm Password**: Re-enter password
   - **Permissions**: Assign appropriate permissions

4. **Save Administrator**
   - Click "Save" button

```
[Screenshot Placeholder: Administrator Creation Form]
- Description: Form for creating new administrator accounts
- Location: User Management > Administrators > Add Administrator
- Elements: Name field, email field, password fields, permission checkboxes, save button
```

#### 2.4.2 Managing Teachers

Teachers can view schedules and access assigned classes.

**Creating Teachers**

1. **Navigate to Teachers**
   - From dashboard, click "User Management" → "Teachers"

2. **Create New Teacher**
   - Click "Add Teacher" button

3. **Fill Teacher Information**
   - **Name**: Enter full name
   - **Email**: Enter email address (used for login)
   - **Password**: Set initial password
   - **Confirm Password**: Re-enter password
   - **Employee ID**: Enter employee identification number
   - **Department**: Select department (optional)

4. **Assign Subjects**
   - Select subjects this teacher can teach
   - Only assigned subjects appear in lesson creation dropdown

5. **Save Teacher**
   - Click "Save" button

```
[Screenshot Placeholder: Teacher Creation Form]
- Description: Form for creating teacher accounts with subject assignments
- Location: User Management > Teachers > Add Teacher
- Elements: Teacher information fields, subject assignment multi-select, save button
```

### 2.5 Scheduling Features

#### 2.5.1 Creating Lessons

Creating lessons is the core scheduling function that assigns classes to rooms, teachers, and time slots.

**Step-by-Step Lesson Creation Process**

1. **Access Lesson Creation**
   - From dashboard, click "Scheduling" → "Lessons"
   - Click "Add Class Schedule" button

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
   - **Important**: Weekend classes have restrictions based on program type
     - Diploma programs: Can have weekend classes
     - Senior High/College: Monday-Friday only

7. **Configure Time**
   - **Start Time**: Choose lesson start time from dropdown
   - **End Time**: Select lesson end time
   - System automatically calculates duration
   - Times are restricted to school hours (typically 7 AM - 9 PM)

8. **Review Duration Validation**
   - System validates lesson duration based on subject type:
   - **Laboratory Lessons**: 3-5 hours minimum
   - **Lecture Lessons**: 1-3 hours in 30-minute intervals
   - Red alerts appear if duration violates requirements

9. **Review Conflict Detection**
   - System automatically checks for conflicts
   - Green checkmark indicates no conflicts
   - Red alerts show if:
     - Class already scheduled at this time
     - Teacher has another class scheduled
     - Room is already occupied
     - Time outside school hours
     - Duration violates subject requirements

10. **Resolve Conflicts (if any)**
    - If conflicts exist, system provides alternative suggestions
    - Choose different time, room, or teacher as needed
    - Conflict alerts disappear when resolved

11. **Save Lesson**
    - Click "Save Lesson" or "Create Lesson" button
    - Success message confirms lesson creation
    - Lesson appears in master timetable immediately

```
[Screenshot Placeholder: Lesson Creation Form]
- Description: Complete lesson creation form with all fields visible
- Location: Scheduling > Lessons > Add Class Schedule
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

#### 2.5.2 Managing Lessons

Administrators can view, edit, and delete existing lessons.

**Viewing All Lessons**

1. **Navigate to Lessons**
   - From dashboard, click "Scheduling" → "Lessons"
   - View complete list of scheduled lessons

2. **Use Filters and Search**
   - Filter by class, teacher, subject, or weekday
   - Use search box to find specific lessons
   - Adjust number of lessons displayed per page (10, 20, 50, 100)

3. **Column Visibility**
   - Click "Show/Hide Columns" button
   - Toggle which columns are displayed
   - Customize view for different needs

```
[Screenshot Placeholder: Lessons List with Filters]
- Description: Lessons management page with filtering options
- Location: Scheduling > Lessons
- Elements: Filter dropdowns, search box, lessons table, column toggle, pagination
```

**Editing Lessons**

1. **Find Lesson to Edit**
   - Navigate to lessons list
   - Use filters to locate specific lesson

2. **Open Edit Form**
   - Click "Edit" button next to the lesson

3. **Modify Lesson Details**
   - Change class, subject, teacher, room, or time
   - System automatically checks for conflicts
   - Duration validation applies to changes

4. **Save Changes**
   - Click "Update Lesson" button
   - System confirms successful update

**Deleting Lessons**

1. **Select Lesson to Delete**
   - Find lesson in lessons list
   - Click "Delete" button

2. **Confirm Deletion**
   - Confirm deletion in popup dialog
   - Lesson is immediately removed from timetable

#### 2.5.3 Conflict Detection and Resolution

The system continuously monitors for scheduling conflicts and prevents double bookings.

**Automatic Conflict Detection**

- **Real-time Checking**: Conflicts detected during lesson creation and editing
- **Multiple Conflict Types**: Class, teacher, and room conflicts monitored simultaneously
- **Visual Indicators**: Green checkmarks for no conflicts, red alerts for issues

**Conflict Types**

1. **Class Conflicts**
   - Same class scheduled twice at same time
   - Prevents students from being in two places simultaneously

2. **Teacher Conflicts**
   - Teacher assigned to multiple classes simultaneously
   - Ensures teachers can only teach one class at a time

3. **Room Conflicts**
   - Room double-booked for different lessons
   - Prevents physical space conflicts

**Duration Validation Rules**

- **Laboratory Lessons**: Minimum 3 hours, maximum 5 hours
  - Rationale: Lab work requires substantial setup, execution, and cleanup time
- **Lecture Lessons**: Minimum 1 hour, maximum 3 hours
  - Available in 30-minute intervals (1h, 1.5h, 2h, 2.5h, 3h)

**Weekend Class Restrictions**

- **Diploma Programs**: Can schedule classes on Saturday and Sunday
- **Senior High/College Programs**: Monday-Friday only
- System prevents creating weekend classes for restricted program types

#### 2.5.4 Master Timetable Management

The master timetable provides a comprehensive view of all room schedules in a matrix format.

**Accessing the Master Timetable**

1. **Navigate to Master Timetable**
   - From dashboard, click "Resource Management" → "Master Timetable"
   - Or click "Master Timetable" button from lessons page

2. **Select Weekday**
   - Choose day of the week to view
   - Each day shows different schedule matrix

3. **Understanding the Layout**
   - **Columns**: Individual rooms (classrooms and laboratories)
   - **Rows**: Time slots in 30-minute intervals
   - **Cells**: Lesson information or empty slots

```
[Screenshot Placeholder: Master Timetable Overview]
- Description: Grid view showing rooms as columns and time slots as rows
- Location: Resource Management > Master Timetable
- Elements: Room headers, time slot labels, lesson boxes, empty cells, legend
```

**Reading the Master Timetable**

- **Lesson Boxes**: Show subject, teacher, class, and room information
- **Color Coding**: White background with green text for clean appearance
- **Duration Indicators**: Multi-hour lessons span multiple time slots with seamless borders
- **Empty Cells**: Available time slots for scheduling

**Navigation and Features**

1. **Weekday Navigation**
   - Click weekday tabs to view different days
   - Current day highlighted by default

2. **Export Functionality**
   - Click "Export" button
   - Choose format: JSON or CSV
   - Download schedule data for external use

3. **Refresh Data**
   - Click "Refresh" button to update timetable
   - Real-time updates reflect latest changes

```
[Screenshot Placeholder: Master Timetable Export Options]
- Description: Export dropdown with JSON and CSV options
- Location: Master Timetable > Export button
- Elements: Export dropdown, format options, download buttons
```

### 2.6 Schedule Viewing Features

#### 2.6.1 Teacher Schedule Views

**Weekly Schedule View**

1. **Access Weekly Schedule**
   - From dashboard, view "This Week's Classes" section
   - See all scheduled lessons for the current week

2. **Schedule Information Display**
   - **Subject Name**: Course being taught
   - **Class Name**: Student group assigned
   - **Room**: Location of the class
   - **Time**: Start and end times
   - **Duration**: Length of each lesson

3. **Navigate Between Days**
   - Click on specific days to view detailed schedules
   - Use calendar view for monthly planning

```
[Screenshot Placeholder: Teacher Weekly Schedule]
- Description: Weekly view showing all teacher's scheduled classes
- Location: Teacher dashboard > Weekly Schedule
- Elements: Day columns, time slots, lesson boxes, subject information
```

**Daily Schedule Details**

1. **View Today's Classes**
   - From dashboard, check "Today's Classes" section
   - See chronological order of today's lessons

2. **Class Details**
   - **Time Schedule**: Exact start and end times
   - **Room Assignment**: Specific classroom or laboratory
   - **Class Group**: Which student group is being taught
   - **Subject Details**: Course information and requirements

3. **Class Status Indicators**
   - Current class highlighted
   - Upcoming classes shown with countdown
   - Completed classes marked as done

#### 2.6.2 Calendar View Features

**Using the Calendar View**

1. **Access Calendar**
   - Navigate to `/teacher/calendar` URL
   - View weekly calendar with teaching assignments

2. **Calendar Navigation**
   - View entire week schedule at once
   - Days displayed as columns with time slots
   - Current day highlighted for easy reference

3. **Calendar Events**
   - Teaching assignments shown in time slots
   - Subject, class, and room information displayed
   - Color-coded for different types of activities

```
[Screenshot Placeholder: Teacher Calendar View]
- Description: Weekly calendar showing teaching assignments
- Location: /teacher/calendar
- Elements: Weekday columns, time slots, lesson blocks, subject information
```

#### 2.6.3 Room Schedule Access

**Accessing Room Schedules**

1. **Navigate to Room Schedules**
   - From dashboard, access room schedule section
   - Or use QR codes outside rooms for quick access

2. **View Room Information**
   - Room name and capacity
   - Equipment availability
   - Current and upcoming classes
   - Empty time slots

3. **Filter Room Views**
   - Filter by room type (classroom/laboratory)
   - Show specific rooms only
   - Check equipment requirements

```
[Screenshot Placeholder: Room Schedule View for Teachers]
- Description: Room timetable showing current and upcoming classes
- Location: Teacher dashboard > Room Schedules
- Elements: Room information, schedule grid, class details, availability indicators
```

### 2.7 Public Access Features

#### 2.7.1 QR Code Room Schedule Access

**QR Code Features**

- **Mobile Optimized**: Designed for smartphone viewing
- **No Login Required**: Public access without authentication
- **Real-time Data**: Shows current schedule information
- **Secure Access**: Encrypted room identifiers
- **Fast Loading**: Optimized for quick access

#### 2.7.2 Public Timetable Interface

**Interface Navigation**

1. **Room Information Header**
   - Room name and type (Classroom/Laboratory)
   - Room capacity and equipment details
   - Current date and weekday

2. **Weekday Navigation**
   - Click weekday tabs to view different days
   - Current day automatically highlighted
   - Navigate between weeks using arrow buttons

3. **Time Slot Display**
   - All scheduled lessons shown in chronological order
   - Time ranges clearly displayed
   - Empty slots shown as available

**Schedule Information Display**

For each scheduled lesson, the public interface shows:

- **Subject Name**: Course being taught
- **Teacher Name**: Instructor assigned to the class
- **Class Information**: Student group using the room
- **Time Duration**: Start and end times
- **Lesson Type**: Lecture or Laboratory

```
[Screenshot Placeholder: Public Timetable View]
- Description: Mobile-friendly timetable view accessible via QR code
- Location: /public/room/{identifier} (accessed through QR code scan)
- Elements: Room name, weekday tabs, time slots, lesson information, no login required
```

**Mobile-Friendly Features**

- **Responsive Design**: Adapts to different screen sizes
- **Touch Navigation**: Easy tapping on mobile devices
- **Readable Text**: Optimized font sizes for mobile viewing
- **Fast Loading**: Minimal data for quick access
- **Clean Layout**: Uncluttered interface for easy reading

#### 2.7.3 Room Availability Checking

**Checking Current Status**

1. **View Current Time**
   - Current time highlighted on the timetable
   - Ongoing classes clearly marked
   - Upcoming classes shown with countdown

2. **Available Time Slots**
   - Empty slots indicate room availability
   - Time ranges clearly shown
   - Duration of available periods displayed

3. **Room Status Indicators**
   - **Available**: Room currently empty
   - **Occupied**: Class in session
   - **Reserved**: Scheduled for upcoming class

**Planning Room Usage**

1. **Check Future Availability**
   - Navigate to future days using weekday tabs
   - View entire week schedule
   - Identify patterns in room usage

2. **Equipment and Facilities**
   - Room type information (Classroom/Laboratory)
   - Equipment availability indicators
   - Capacity information for planning

**Privacy and Security**

Public access maintains appropriate privacy and security:

- **No Personal Information**: Only schedule data displayed
- **No Student Data**: Personal information protected
- **Limited Access**: Only schedule information available
- **Secure Links**: Encrypted QR code identifiers
- **Time-limited Data**: Current and future schedules only

### 2.8 System Utilities

#### 2.8.1 Export Functionality

**Export Master Timetable**

1. **Access Export Options**
   - Navigate to master timetable
   - Click "Export" button

2. **Choose Export Format**
   - **JSON**: Complete schedule data for system integration
   - **CSV**: Tabular data for spreadsheet applications

3. **Download File**
   - File downloads automatically
   - Use for reports, backups, or external analysis

**Export Data Includes**
- Lesson details (subject, teacher, class, room, time)
- Room information
- Schedule conflicts and resolutions
- System statistics

#### 2.8.2 Filtering and Search Features

**Common Filter Features**

1. **Dropdown Filters**
   - Filter by type, status, or category
   - Multiple filters can be applied simultaneously
   - Filters persist during session

2. **Search Functionality**
   - Global search across multiple fields
   - Real-time search results
   - Search works with filters applied

3. **Mobile Responsive Filters**
   - Collapsible filter panels on mobile devices
   - "Show Filters" button for mobile access
   - Touch-friendly interface

**Filter Examples**

- **Lessons**: Class, teacher, subject, weekday filters
- **Subjects**: Type, lab requirement, equipment requirement, status filters
- **Rooms**: Type, equipment, capacity range filters
- **Users**: Role, status, department filters

#### 2.8.3 Mobile Responsive Features

**Mobile Optimization**

- **Responsive Design**: Adapts to different screen sizes
- **Touch-Friendly Interface**: Large buttons and touch targets
- **Mobile Navigation**: Collapsible menus and filters
- **Optimized Performance**: Fast loading on mobile networks
- **Cross-Device Compatibility**: Works on smartphones, tablets, and desktops

**Mobile-Specific Features**

- **Collapsible Filter Panels**: "Show Filters" button for mobile access
- **Swipe Gestures**: Navigate between days and views
- **Mobile Dashboard**: Optimized layout for small screens
- **QR Code Integration**: Native camera support for scanning

---

## Quick Reference Guide

### Common Tasks Summary

| User Role | Task | Navigation | Key Steps |
|-----------|------|------------|-----------|
| Administrator | Login as Admin | Homepage | Enter email/password → Click Login |
| Administrator | Create Academic Program | Academic Management > Academic Programs > Add | Fill program details → Save |
| Administrator | Create Subject | Academic Management > Subjects > Add | Set subject info → Assign teachers → Save |
| Administrator | Create Room | Resource Management > Rooms > Add | Fill room details → Set capacity → Save |
| Administrator | Create Teacher | User Management > Teachers > Add | Enter teacher info → Assign subjects → Save |
| Administrator | Create Lesson | Scheduling > Lessons > Add Class Schedule | Select class/subject/teacher/room → Set time → Save |
| Administrator | View Master Timetable | Resource Management > Master Timetable | Select weekday → View schedule grid |
| Administrator | Export Schedule | Master Timetable > Export | Choose format → Download |
| Teacher | Login as Teacher | Homepage | Enter email/password → Click Login |
| Teacher | View Schedule | Teacher Dashboard | View weekly/daily schedule |
| Teacher | View Calendar | /teacher/calendar | Navigate to calendar URL |
| Public | Scan QR Code | Physical QR code | Scan with phone → View public schedule |

### Business Rules Summary

#### Duration Rules
- **Laboratory Lessons**: 3-5 hours per session
- **Lecture Lessons**: 1-3 hours per session (30-minute intervals)

#### Weekend Class Rules
- **Diploma Programs**: Can schedule Saturday/Sunday classes
- **Senior High/College**: Monday-Friday only

#### Room Assignment Rules
- **Laboratory Subjects**: Must use laboratory rooms
- **Equipment Requirements**: Room must match subject needs
- **Capacity Limits**: Class size cannot exceed room capacity

#### Conflict Prevention
- **Class Conflicts**: Same class cannot be scheduled twice simultaneously
- **Teacher Conflicts**: Teachers cannot teach multiple classes simultaneously  
- **Room Conflicts**: Rooms cannot host multiple classes simultaneously

---

**Version**: 1.0  
**Last Updated**: [Current Date]  
**System**: Laravel School Timetable Management System


