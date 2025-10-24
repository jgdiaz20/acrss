# Admin User Sequence Diagram

## Admin User Creation Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Web Browser
    participant Controller as UsersController
    participant Validation as Request Validation
    participant Model as User Model
    participant Role as Role Model
    participant DB as Database
    participant Cache as Cache Service

    Admin->>Browser: Access user creation form
    Browser->>Controller: GET /admin/users/create
    Controller->>Role: Get available roles
    Role->>DB: Query roles
    DB-->>Role: Return roles
    Role-->>Controller: Return roles data
    Controller->>Browser: Return creation form

    Admin->>Browser: Fill form and submit
    Browser->>Controller: POST /admin/users
    Controller->>Validation: Validate user data
    Validation-->>Controller: Validation result
    
    alt Validation successful
        Controller->>Model: Create new user
        Model->>DB: Insert user record
        DB-->>Model: Return created user
        Model-->>Controller: Return user instance
        
        Controller->>Role: Sync user roles
        Role->>DB: Insert role assignments
        DB-->>Role: Confirm assignments
        Role-->>Controller: Roles synced
        
        Controller->>Cache: Invalidate user caches
        Cache-->>Controller: Cache cleared
        
        Controller->>Browser: Redirect with success message
        Browser-->>Admin: Show success notification
    else Validation failed
        Controller->>Browser: Return form with errors
        Browser-->>Admin: Display validation errors
    end
```

## Admin Lesson Creation with Conflict Detection Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Web Browser
    participant Controller as LessonsController
    participant Validation as ValidationController
    participant Lesson as Lesson Model
    participant Conflict as ConflictService
    participant DB as Database
    participant Cache as CacheService

    Admin->>Browser: Access lesson creation form
    Browser->>Controller: GET /admin/lessons/create
    Controller->>Browser: Return creation form

    Admin->>Browser: Fill form and submit
    Browser->>Controller: POST /admin/lessons
    Controller->>Validation: Check for conflicts
    
    Validation->>Lesson: Query overlapping lessons
    Lesson->>DB: Search for conflicts
    DB-->>Lesson: Return conflicting lessons
    Lesson-->>Validation: Return conflict data
    
    Validation->>Conflict: Analyze conflicts
    Conflict-->>Validation: Return conflict details
    Validation-->>Controller: Return conflict results
    
    alt Conflicts found
        Controller->>Browser: Return conflicts
        Browser-->>Admin: Display conflict details
        
        Admin->>Browser: Resolve conflicts
        Browser->>Controller: POST /admin/lessons (resolved)
        Controller->>Validation: Re-check conflicts
        Validation-->>Controller: No conflicts
    end
    
    Controller->>Lesson: Create lesson
    Lesson->>DB: Insert lesson record
    DB-->>Lesson: Return created lesson
    Lesson-->>Controller: Return lesson instance
    
    Controller->>Cache: Invalidate lesson caches
    Cache-->>Controller: Cache cleared
    
    Controller->>Browser: Redirect with success
    Browser-->>Admin: Show success notification
```

## Admin Master Timetable View Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Web Browser
    participant Controller as MasterTimetableController
    participant Service as MasterTimetableService
    participant Lesson as Lesson Model
    participant Room as Room Model
    participant DB as Database

    Admin->>Browser: Access master timetable
    Browser->>Controller: GET /admin/master-timetable
    Controller->>Service: Generate timetable data
    Service->>Lesson: Query all lessons
    Lesson->>DB: Fetch lesson data
    DB-->>Lesson: Return lessons
    Lesson-->>Service: Return lesson data
    
    Service->>Room: Query all rooms
    Room->>DB: Fetch room data
    DB-->>Room: Return rooms
    Room-->>Service: Return room data
    
    Service->>Service: Process timetable data
    Service-->>Controller: Return processed data
    Controller->>Browser: Return timetable view
    Browser-->>Admin: Display master timetable

    Admin->>Browser: Apply filters
    Browser->>Controller: GET /admin/master-timetable?filters
    Controller->>Service: Generate filtered data
    Service->>Lesson: Query filtered lessons
    Lesson->>DB: Fetch filtered data
    DB-->>Lesson: Return filtered lessons
    Lesson-->>Service: Return filtered data
    Service-->>Controller: Return filtered timetable
    Controller->>Browser: Return filtered view
    Browser-->>Admin: Display filtered timetable
```

## Admin Room Management Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Web Browser
    participant Controller as RoomsController
    participant Room as Room Model
    participant QRService as QRCodeService
    participant DB as Database
    participant Cache as CacheService

    Admin->>Browser: Access room management
    Browser->>Controller: GET /admin/room-management/rooms
    Controller->>Room: Query rooms with filters
    Room->>DB: Fetch room data
    DB-->>Room: Return rooms
    Room-->>Controller: Return room data
    Controller->>Browser: Return room list
    Browser-->>Admin: Display room management

    Admin->>Browser: Create new room
    Browser->>Controller: GET /admin/room-management/rooms/create
    Controller->>Browser: Return creation form

    Admin->>Browser: Submit room data
    Browser->>Controller: POST /admin/room-management/rooms
    Controller->>Room: Create room
    Room->>DB: Insert room record
    DB-->>Room: Return created room
    Room-->>Controller: Return room instance
    
    Controller->>QRService: Generate QR code
    QRService-->>Controller: Return QR code
    Controller->>Cache: Invalidate room caches
    Cache-->>Controller: Cache cleared
    
    Controller->>Browser: Redirect with success
    Browser-->>Admin: Show success notification
```

## Admin Subject Management with Teacher Assignment Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Web Browser
    participant Controller as SubjectsController
    participant Subject as Subject Model
    participant User as User Model
    participant TeacherSubject as TeacherSubject Model
    participant DB as Database
    participant Cache as CacheService

    Admin->>Browser: Access subject management
    Browser->>Controller: GET /admin/subjects
    Controller->>Subject: Query subjects with filters
    Subject->>DB: Fetch subject data
    DB-->>Subject: Return subjects
    Subject-->>Controller: Return subject data
    Controller->>Browser: Return subject list
    Browser-->>Admin: Display subject management

    Admin->>Browser: Assign teachers to subject
    Browser->>Controller: GET /admin/subjects/{id}/assign-teachers
    Controller->>Subject: Load subject details
    Subject->>DB: Fetch subject data
    DB-->>Subject: Return subject
    Subject-->>Controller: Return subject instance
    
    Controller->>User: Query teachers
    User->>DB: Fetch teacher data
    DB-->>User: Return teachers
    User-->>Controller: Return teacher data
    
    Controller->>Browser: Return assignment form
    Browser-->>Admin: Display teacher assignment form

    Admin->>Browser: Submit teacher assignments
    Browser->>Controller: POST /admin/subjects/{id}/assign-teachers
    Controller->>Subject: Detach existing teachers
    Subject->>TeacherSubject: Remove existing assignments
    TeacherSubject->>DB: Delete existing assignments
    DB-->>TeacherSubject: Confirm deletions
    TeacherSubject-->>Subject: Assignments removed
    Subject-->>Controller: Teachers detached
    
    Controller->>Subject: Attach new teachers
    Subject->>TeacherSubject: Create new assignments
    TeacherSubject->>DB: Insert new assignments
    DB-->>TeacherSubject: Confirm insertions
    TeacherSubject-->>Subject: Assignments created
    Subject-->>Controller: Teachers attached
    
    Controller->>Cache: Invalidate assignment caches
    Cache-->>Controller: Cache cleared
    
    Controller->>Browser: Redirect with success
    Browser-->>Admin: Show success notification
```

## Admin System Validation Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Web Browser
    participant Controller as ValidationController
    participant Lesson as Lesson Model
    participant Room as Room Model
    participant User as User Model
    participant DB as Database

    Admin->>Browser: Request conflict check
    Browser->>Controller: POST /admin/validation/check-conflicts
    Controller->>Lesson: Query overlapping lessons
    Lesson->>DB: Search for conflicts
    DB-->>Lesson: Return conflicting lessons
    Lesson-->>Controller: Return conflict data
    
    loop For each conflicting lesson
        Controller->>Room: Get room details
        Room->>DB: Fetch room data
        DB-->>Room: Return room
        Room-->>Controller: Return room details
        
        Controller->>User: Get teacher details
        User->>DB: Fetch teacher data
        DB-->>User: Return teacher
        User-->>Controller: Return teacher details
        
        Controller->>Controller: Analyze conflict type
    end
    
    Controller->>Browser: Return conflict details
    Browser-->>Admin: Display conflict information

    Admin->>Browser: Request alternative times
    Browser->>Controller: POST /admin/validation/alternative-times
    Controller->>Controller: Generate time slots
    Controller->>Lesson: Check slot availability
    Lesson->>DB: Query slot conflicts
    DB-->>Lesson: Return availability data
    Lesson-->>Controller: Return availability
    Controller->>Controller: Calculate confidence scores
    Controller->>Browser: Return alternative times
    Browser-->>Admin: Display alternative options
```
