# Admin User Activity Diagram

## Admin User Management Activity Flow

```mermaid
activityDiagram
    start
    :Admin logs in;
    :System validates credentials;
    
    if (Authentication successful?) then (yes)
        :Load admin dashboard;
        :Display system overview;
        
        :Admin selects management area;
        
        if (User Management) then (yes)
            :View user list;
            :Filter by role;
            
            if (Create new user?) then (yes)
                :Fill user creation form;
                :Select role (Admin/Teacher/Student);
                :Assign class if applicable;
                :Validate user data;
                
                if (Data valid?) then (yes)
                    :Create user account;
                    :Set role flags;
                    :Assign permissions;
                    :Send success notification;
                else (no)
                    :Display validation errors;
                    :Return to form;
                endif
            endif
            
            if (Edit existing user?) then (yes)
                :Load user details;
                :Modify user information;
                :Update role assignments;
                :Validate changes;
                
                if (Changes valid?) then (yes)
                    :Save user updates;
                    :Update role flags;
                    :Send success notification;
                else (no)
                    :Display validation errors;
                    :Return to form;
                endif
            endif
            
            if (Delete user?) then (yes)
                :Check for dependencies;
                
                if (Has active lessons or assignments?) then (yes)
                    :Display dependency error;
                    :Prevent deletion;
                else (no)
                    :Confirm deletion;
                    :Remove user account;
                    :Send success notification;
                endif
            endif
        endif
        
        if (Lesson Management) then (yes)
            :View lesson list;
            :Apply filters;
            
            if (Create new lesson?) then (yes)
                :Select class;
                :Select teacher;
                :Select subject;
                :Select room;
                :Set time slot;
                :Check for conflicts;
                
                if (Conflicts detected?) then (yes)
                    :Display conflict details;
                    :Suggest alternative times;
                    :Allow conflict resolution;
                endif
                
                if (No conflicts or resolved?) then (yes)
                    :Create lesson;
                    :Update timetables;
                    :Invalidate caches;
                    :Send success notification;
                endif
            endif
            
            if (Edit existing lesson?) then (yes)
                :Load lesson details;
                :Modify lesson information;
                :Check for new conflicts;
                
                if (New conflicts detected?) then (yes)
                    :Display conflict details;
                    :Suggest alternatives;
                endif
                
                if (No conflicts or resolved?) then (yes)
                    :Save lesson updates;
                    :Update timetables;
                    :Send success notification;
                endif
            endif
        endif
        
        if (Room Management) then (yes)
            :View room list;
            :Apply filters;
            
            if (Create new room?) then (yes)
                :Fill room details;
                :Set capacity;
                :Configure equipment;
                :Validate room data;
                
                if (Data valid?) then (yes)
                    :Create room;
                    :Generate QR code;
                    :Send success notification;
                endif
            endif
            
            if (Edit existing room?) then (yes)
                :Load room details;
                :Modify room information;
                :Update equipment settings;
                :Save changes;
                :Send success notification;
            endif
        endif
        
        if (Subject Management) then (yes)
            :View subject list;
            :Apply filters;
            
            if (Create new subject?) then (yes)
                :Fill subject details;
                :Set subject requirements;
                :Validate subject data;
                
                if (Data valid?) then (yes)
                    :Create subject;
                    :Send success notification;
                endif
            endif
            
            if (Assign teachers to subject?) then (yes)
                :Load subject details;
                :Select teachers;
                :Set assignment details;
                :Save assignments;
                :Send success notification;
            endif
        endif
        
        if (Master Timetable) then (yes)
            :View master timetable;
            :Apply filters;
            :Check for conflicts;
            :View room utilization;
            
            if (Export timetable?) then (yes)
                :Select export format;
                :Generate export file;
                :Download file;
            endif
        endif
        
    else (no)
        :Display login error;
        :Return to login page;
    endif
    
    stop
```

## Admin Conflict Resolution Activity Flow

```mermaid
activityDiagram
    start
    :Admin attempts to schedule lesson;
    :System checks for conflicts;
    
    if (Conflicts detected?) then (yes)
        :Display conflict details;
        :Show conflicting lessons;
        
        if (Teacher conflict?) then (yes)
            :Highlight teacher availability;
            :Suggest alternative teachers;
            :Show teacher schedule;
        endif
        
        if (Room conflict?) then (yes)
            :Highlight room availability;
            :Suggest alternative rooms;
            :Show room schedule;
        endif
        
        if (Class conflict?) then (yes)
            :Highlight class schedule;
            :Suggest alternative times;
            :Show class timetable;
        endif
        
        :Admin reviews conflicts;
        
        if (Admin resolves conflicts?) then (yes)
            :Select alternative resources;
            :Confirm new schedule;
            :System validates new schedule;
            
            if (New schedule valid?) then (yes)
                :Create lesson;
                :Update timetables;
                :Send success notification;
            else (no)
                :Display new conflicts;
                :Return to resolution;
            endif
        else (no)
            :Cancel lesson creation;
            :Return to lesson list;
        endif
    else (no)
        :Create lesson successfully;
        :Update timetables;
        :Send success notification;
    endif
    
    stop
```

## Admin System Administration Activity Flow

```mermaid
activityDiagram
    start
    :Admin accesses system administration;
    :Select administration area;
    
    if (Role Management) then (yes)
        :View role list;
        
        if (Create new role?) then (yes)
            :Define role name;
            :Select permissions;
            :Validate role data;
            
            if (Data valid?) then (yes)
                :Create role;
                :Assign permissions;
                :Send success notification;
            endif
        endif
        
        if (Edit existing role?) then (yes)
            :Load role details;
            :Modify permissions;
            :Save changes;
            :Send success notification;
        endif
    endif
    
    if (Permission Management) then (yes)
        :View permission list;
        
        if (Create new permission?) then (yes)
            :Define permission name;
            :Set permission description;
            :Create permission;
            :Send success notification;
        endif
        
        if (Edit existing permission?) then (yes)
            :Load permission details;
            :Modify permission;
            :Save changes;
            :Send success notification;
        endif
    endif
    
    if (Academic Program Management) then (yes)
        :View program list;
        
        if (Create new program?) then (yes)
            :Select program type;
            :Define program details;
            :Create program;
            :Auto-create grade levels;
            :Send success notification;
        endif
        
        if (Edit existing program?) then (yes)
            :Load program details;
            :Modify program information;
            :Update grade levels if needed;
            :Save changes;
            :Send success notification;
        endif
    endif
    
    stop
```
