<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MasterTimetableService;
use App\Services\TimeService;
use App\Services\SchedulingConflictService;
use App\Lesson;
use App\Room;
use App\SchoolClass;
use App\User;
use App\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class MasterTimetableController extends Controller
{
    protected $masterTimetableService;
    protected $timeService;
    protected $schedulingConflictService;

    public function __construct(
        MasterTimetableService $masterTimetableService,
        TimeService $timeService,
        SchedulingConflictService $schedulingConflictService
    ) {
        $this->masterTimetableService = $masterTimetableService;
        $this->timeService = $timeService;
        $this->schedulingConflictService = $schedulingConflictService;
    }

    /**
     * Display the master timetable index page with simple overview
     */
    public function index()
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get basic statistics
        $totalRooms = Room::count();
        $totalLessons = Lesson::count();
        $activeTeachers = User::where('is_teacher', true)->count();
        
        // Count actual conflicts (excluding self-conflicts)
        $totalConflicts = 0;
        $lessons = Lesson::all();
        foreach ($lessons as $lesson) {
            if (!empty($lesson->getConflicts($lesson->id))) {
                $totalConflicts++;
            }
        }

        return view('admin.room-management.master-timetable.index', compact(
            'totalRooms',
            'totalLessons', 
            'activeTeachers',
            'totalConflicts'
        ));
    }

    /**
     * Display master timetable for a specific day
     */
    public function show(Request $request, $weekday)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate weekday
        if (!isset(Lesson::WEEK_DAYS[$weekday])) {
            abort(404, 'Invalid weekday');
        }

        $weekDays = Lesson::WEEK_DAYS;
        $weekdayOptions = $weekDays; // Show all 7 days

        // Generate master timetable data for the specific day
        $timetableData = $this->masterTimetableService->generateMasterTimetableData($weekday);

        return view('admin.room-management.master-timetable.show', compact(
            'timetableData',
            'weekDays',
            'weekdayOptions'
        ));
    }

    /**
     * Get available time slots for a specific room and day (AJAX)
     */
    public function getAvailableTimeSlots(Request $request)
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'weekday' => 'required|integer|between:1,5',
            'duration' => 'integer|min:30|max:240'
        ]);

        $roomId = $request->input('room_id');
        $weekday = $request->input('weekday');
        $duration = $request->input('duration', 60); // Default 1 hour

        $availableSlots = $this->masterTimetableService->getAvailableTimeSlots($roomId, $weekday, $duration);

        return response()->json([
            'success' => true,
            'room' => Room::find($roomId),
            'weekday' => $weekday,
            'weekday_name' => Lesson::WEEK_DAYS[$weekday],
            'duration' => $duration,
            'available_slots' => $availableSlots,
            'total_available' => count($availableSlots)
        ]);
    }

    /**
     * Get room utilization statistics (AJAX)
     */
    public function getRoomUtilization(Request $request)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $weekday = $request->input('weekday');
        
        if ($weekday && !isset(Lesson::WEEK_DAYS[$weekday])) {
            return response()->json([
                'error' => 'Invalid weekday'
            ], 400);
        }

        $stats = $this->masterTimetableService->getRoomUtilizationStats($weekday);

        return response()->json([
            'success' => true,
            'weekday' => $weekday,
            'weekday_name' => $weekday ? Lesson::WEEK_DAYS[$weekday] : 'All Days',
            'statistics' => $stats
        ]);
    }

    /**
     * Get master timetable data as JSON (AJAX)
     */
    public function getTimetableData(Request $request)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'weekday' => 'required|integer|between:1,5'
        ]);

        $weekday = $request->input('weekday');
        $timetableData = $this->masterTimetableService->generateMasterTimetableData($weekday);

        return response()->json([
            'success' => true,
            'data' => $timetableData
        ]);
    }

    /**
     * Get lesson details for inline editing (AJAX)
     */
    public function getLessonDetails(Request $request)
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'lesson_id' => 'required|exists:lessons,id'
        ]);

        $lesson = Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->findOrFail($request->input('lesson_id'));

        return response()->json([
            'success' => true,
            'lesson' => [
                'id' => $lesson->id,
                'class_name' => $lesson->class->display_name ?? 'No Class',
                'teacher_name' => $lesson->teacher->name ?? 'No Teacher',
                'subject_name' => $lesson->subject->name ?? 'No Subject',
                'room_name' => $lesson->room->display_name ?? 'No Room',
                'start_time' => $lesson->start_time,
                'end_time' => $lesson->end_time,
                'weekday' => $lesson->weekday,
                'weekday_name' => Lesson::WEEK_DAYS[$lesson->weekday],
                'duration' => $lesson->difference,
                'has_conflicts' => $lesson->hasConflicts(),
                'conflicts' => $lesson->getConflicts()
            ]
        ]);
    }

    /**
     * Check conflicts for a potential lesson schedule (AJAX)
     */
    public function checkSchedulingConflicts(Request $request)
    {
        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'weekday' => 'required|integer|between:1,5',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date_format:g:i A',
            'end_time' => 'required|date_format:g:i A|after:start_time',
            'class_id' => 'required|exists:school_classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $data = $request->all();
        
        // Convert time format for conflict checking
        $startTime = \Carbon\Carbon::createFromFormat('g:i A', $data['start_time'])->format('H:i:s');
        $endTime = \Carbon\Carbon::createFromFormat('g:i A', $data['end_time'])->format('H:i:s');

        $conflicts = $this->schedulingConflictService->checkConflicts(
            $data['weekday'],
            $startTime,
            $endTime,
            $data['class_id'],
            $data['teacher_id'],
            $data['room_id']
        );

        return response()->json([
            'success' => true,
            'has_conflicts' => !empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Get quick statistics for the master timetable
     */
    public function getQuickStats(Request $request)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $weekday = $request->input('weekday');
        
        $query = Lesson::query();
        if ($weekday) {
            $query->where('weekday', $weekday);
        }

        $totalLessons = $query->count();
        $totalRooms = Room::count();
        $totalClasses = SchoolClass::active()->count();
        $totalTeachers = User::where('is_teacher', true)->count();

        // Get utilization stats
        $utilizationStats = $this->masterTimetableService->getRoomUtilizationStats($weekday);
        $averageUtilization = $utilizationStats->avg('utilization_percentage');

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_lessons' => $totalLessons,
                'total_rooms' => $totalRooms,
                'total_classes' => $totalClasses,
                'total_teachers' => $totalTeachers,
                'average_room_utilization' => round($averageUtilization, 2),
                'weekday' => $weekday,
                'weekday_name' => $weekday ? Lesson::WEEK_DAYS[$weekday] : 'All Days'
            ]
        ]);
    }

    /**
     * Export master timetable data
     */
    public function export(Request $request)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'weekday' => 'required|integer|between:1,5',
            'format' => 'in:json,csv'
        ]);

        $weekday = $request->input('weekday');
        $format = $request->input('format', 'json');
        
        $timetableData = $this->masterTimetableService->generateMasterTimetableData($weekday);

        if ($format === 'csv') {
            return $this->exportToCSV($timetableData);
        }

        return $this->exportToJSON($timetableData);
    }

    /**
     * Export timetable data to clean JSON format focusing on class schedules
     */
    private function exportToJSON($timetableData)
    {
        $filename = 'class_schedules_' . strtolower($timetableData['weekday_name']) . '_' . now()->format('Y-m-d') . '.json';
        
        // Get lessons directly from database for the specific weekday to avoid duplicates
        $lessons = \App\Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->where('weekday', $timetableData['weekday'])
            ->orderBy('start_time')
            ->get();
        
        // Convert lessons to class schedules format
        $classSchedules = [];

        foreach ($lessons as $lesson) {
            // Extract the first number from the room_id using regex
            preg_match('/\d+/', $lesson->room->display_name ?? $lesson->room->name ?? 'first', $matches);
            $floorNumber = isset($matches[0]) ? (int)$matches[0] : 0;
            
            // Convert the number to its word representation
            $floorWords = [
                1 => 'first',
                2 => 'second',
                3 => 'third',
                4 => 'fourth',
                5 => 'fifth',
                6 => 'sixth',
                7 => 'seventh',
                8 => 'eighth',
                9 => 'ninth',
                10 => 'tenth',
                // Add more numbers if necessary
            ];

            // Default to 'No Floor' if the number is not found in the array
            $floor = isset($floorWords[$floorNumber]) ? $floorWords[$floorNumber] : 'first';

            // Constructing the class schedule
            $classSchedules[] = [
                'id' => $lesson->id,
                'room_id' => $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                'room_name' => $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                'day' => $timetableData['weekday_name'],
                'schedule_number' => 1,
                'start_time' => \Carbon\Carbon::parse($lesson->start_time)->format('H:i:s'),
                'end_time' => \Carbon\Carbon::parse($lesson->end_time)->format('H:i:s'),
                'subject' => $lesson->subject->name ?? 'No Subject',
                'section' => $lesson->class->display_name ?? $lesson->class->name ?? 'No Class',
                'instructor' => $lesson->teacher->name ?? 'No Teacher',
                'instructor_email' => $lesson->teacher->email ?? 'Not Available',
                'floor' => $floor,
            ];
        }

        // Clean export structure focused on class schedules
        $exportData = [
            'schedules' => $classSchedules
        ];

        // Convert to JSON string with proper formatting
        $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // Set proper headers for file download
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($jsonContent),
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        return response($jsonContent, 200, $headers);
    }

    /**
     * Export class schedules to CSV format
     */
    private function exportToCSV($timetableData)
    {
        $filename = 'class_schedules_' . strtolower($timetableData['weekday_name']) . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($timetableData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // Write CSV headers for class schedules
            fputcsv($file, [
                'Schedule ID',
                'Day',
                'Start Time',
                'End Time',
                'Duration (Minutes)',
                'Subject Name',
                'Subject Code',
                'Subject Type',
                'Class Name',
                'Teacher Name',
                'Teacher Email',
                'Room Name',
                'Room Type',
                'Room Capacity'
            ]);

            // Get lessons directly from database for the specific weekday to avoid duplicates
            $lessons = \App\Lesson::with(['class', 'teacher', 'room', 'subject'])
                ->where('weekday', $timetableData['weekday'])
                ->orderBy('start_time')
                ->get();

            // Write each lesson as a CSV row
            foreach ($lessons as $lesson) {
                fputcsv($file, [
                    $lesson->id,
                    $timetableData['weekday_name'],
                    $lesson->start_time,
                    $lesson->end_time,
                    $lesson->getDifferenceAttribute(),
                    $lesson->subject->name ?? 'No Subject',
                    $lesson->subject->code ?? '',
                    ($lesson->subject->requires_lab ?? false) ? 'Laboratory' : 'Regular',
                    $lesson->class->display_name ?? $lesson->class->name ?? 'No Class',
                    $lesson->teacher->name ?? 'No Teacher',
                    $lesson->teacher->email ?? 'Not Available',
                    $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                    ($lesson->room->is_lab ?? false) ? 'Laboratory' : 'Regular',
                    $lesson->room->capacity ?? 'Not Specified'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all weekdays timetable data
     */
    public function exportAll(Request $request)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'format' => 'in:json,csv'
        ]);

        $format = $request->input('format', 'json');
        $weekDays = array_slice(Lesson::WEEK_DAYS, 0, 5, true); // Monday to Friday
        
        // Get all lessons for the week (Monday to Friday) directly from database
        $lessons = \App\Lesson::with(['class', 'teacher', 'room', 'subject'])
            ->whereIn('weekday', array_keys($weekDays))
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->get();
        
        // Convert lessons to the expected format
        $allLessons = [];
        foreach ($lessons as $lesson) {
            $allLessons[] = [
                'id' => $lesson->id,
                'subject' => [
                    'id' => $lesson->subject->id ?? null,
                    'name' => $lesson->subject->name ?? 'No Subject',
                    'code' => $lesson->subject->code ?? '',
                    'requires_lab' => $lesson->subject->requires_lab ?? false,
                    'is_active' => $lesson->subject->is_active ?? true
                ],
                'class' => [
                    'id' => $lesson->class->id ?? null,
                    'name' => $lesson->class->name ?? 'No Class',
                    'display_name' => $lesson->class->display_name ?? $lesson->class->name ?? 'No Class',
                    'is_active' => $lesson->class->is_active ?? true
                ],
                'teacher' => [
                    'id' => $lesson->teacher->id ?? null,
                    'name' => $lesson->teacher->name ?? 'No Teacher',
                    'email' => $lesson->teacher->email ?? null,
                    'is_teacher' => $lesson->teacher->is_teacher ?? true
                ],
                'room' => [
                    'id' => $lesson->room->id ?? null,
                    'name' => $lesson->room->name ?? 'No Room',
                    'display_name' => $lesson->room->display_name ?? $lesson->room->name ?? 'No Room',
                    'capacity' => $lesson->room->capacity ?? null,
                    'is_lab' => $lesson->room->is_lab ?? false,
                    'is_active' => $lesson->room->is_active ?? true
                ],
                'schedule' => [
                    'start_time' => $lesson->getRawOriginal('start_time'),
                    'end_time' => $lesson->getRawOriginal('end_time'),
                    'start_time_formatted' => $lesson->start_time,
                    'end_time_formatted' => $lesson->end_time,
                    'duration_minutes' => $lesson->getDifferenceAttribute(),
                    'weekday' => $lesson->weekday,
                    'weekday_name' => Lesson::WEEK_DAYS[$lesson->weekday] ?? 'Unknown'
                ],
                'metadata' => [
                    'created_at' => $lesson->created_at ? $lesson->created_at->toISOString() : null,
                    'updated_at' => $lesson->updated_at ? $lesson->updated_at->toISOString() : null,
                    'has_conflicts' => !empty($lesson->getConflicts($lesson->id)),
                    'conflicts' => $lesson->getConflicts($lesson->id)
                ]
            ];
        }
        
        // Generate daily statistics for the export
        $allTimetableData = [];
        foreach ($weekDays as $weekday => $dayName) {
            $dayLessons = $lessons->where('weekday', $weekday);
            $allTimetableData[$weekday] = [
                'weekday' => $weekday,
                'weekday_name' => $dayName,
                'statistics' => [
                    'occupied_slots' => $dayLessons->count()
                ]
            ];
        }

        if ($format === 'csv') {
            return $this->exportAllToCSV($allTimetableData, $allLessons);
        }

        return $this->exportAllToJSON($allTimetableData, $allLessons);
    }

    /**
     * Export all days to clean JSON format focusing on class schedules
     */
    private function exportAllToJSON($allTimetableData, $allLessons)
    {
        $filename = 'class_schedules_complete_' . now()->format('Y-m-d') . '.json';
        
        // Process all lessons into clean class schedules format
        $allClassSchedules = [];
        foreach ($allLessons as $lesson) {
            $allClassSchedules[] = [
                'schedule_id' => $lesson['id'],
                'subject' => [
                    'name' => $lesson['subject']['name'] ?? 'No Subject',
                    'code' => $lesson['subject']['code'] ?? '',
                    'type' => ($lesson['subject']['requires_lab'] ?? false) ? 'Laboratory' : 'Regular'
                ],
                'class' => [
                    'name' => $lesson['class']['display_name'] ?? $lesson['class']['name'] ?? 'No Class'
                ],
                'teacher' => [
                    'name' => $lesson['teacher']['name'] ?? 'No Teacher',
                    'email' => $lesson['teacher']['email'] ?? 'Not Available'
                ],
                'room' => [
                    'name' => $lesson['room']['display_name'] ?? $lesson['room']['name'] ?? 'No Room',
                    'type' => ($lesson['room']['is_lab'] ?? false) ? 'Laboratory' : 'Regular',
                    'capacity' => $lesson['room']['capacity'] ?? 'Not Specified'
                ],
                'time_slot' => [
                    'day' => $lesson['schedule']['weekday_name'] ?? 'Unknown',
                    'start_time' => $lesson['schedule']['start_time_formatted'] ?? '',
                    'end_time' => $lesson['schedule']['end_time_formatted'] ?? '',
                    'duration_minutes' => $lesson['schedule']['duration_minutes'] ?? 0
                ]
            ];
        }

        // Calculate daily statistics
        $dailyStats = [];
        foreach ($allTimetableData as $weekday => $dayData) {
            $dailyStats[Lesson::WEEK_DAYS[$weekday]] = $dayData['statistics']['occupied_slots'];
        }

        // Clean export structure for complete weekly schedules
        $exportData = [
            'export_info' => [
                'export_type' => 'Complete Weekly Class Schedules',
                'exported_at' => now()->format('Y-m-d H:i:s'),
                'total_schedules' => count($allClassSchedules),
                'total_days' => count($allTimetableData),
                'school_name' => config('app.name', 'School Management System')
            ],
            'daily_statistics' => $dailyStats,
            'class_schedules' => $allClassSchedules
        ];

        // Convert to JSON string with proper formatting
        $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // Set proper headers for file download
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($jsonContent),
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        return response($jsonContent, 200, $headers);
    }

    /**
     * Export all days to CSV format
     */
    private function exportAllToCSV($allTimetableData, $allLessons)
    {
        $filename = 'class_schedules_complete_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($allLessons) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // Write CSV headers for class schedules
            fputcsv($file, [
                'Schedule ID',
                'Day',
                'Start Time',
                'End Time',
                'Duration (Minutes)',
                'Subject Name',
                'Subject Code',
                'Subject Type',
                'Class Name',
                'Teacher Name',
                'Teacher Email',
                'Room Name',
                'Room Type',
                'Room Capacity'
            ]);

            // Write all class schedules (no duplicates since we got them directly from database)
            foreach ($allLessons as $lesson) {
                fputcsv($file, [
                    $lesson['id'],
                    $lesson['schedule']['weekday_name'] ?? 'Unknown',
                    $lesson['schedule']['start_time_formatted'] ?? '',
                    $lesson['schedule']['end_time_formatted'] ?? '',
                    $lesson['schedule']['duration_minutes'] ?? 0,
                    $lesson['subject']['name'] ?? 'No Subject',
                    $lesson['subject']['code'] ?? '',
                    ($lesson['subject']['requires_lab'] ?? false) ? 'Laboratory' : 'Regular',
                    $lesson['class']['display_name'] ?? $lesson['class']['name'] ?? 'No Class',
                    $lesson['teacher']['name'] ?? 'No Teacher',
                    $lesson['teacher']['email'] ?? 'Not Available',
                    $lesson['room']['display_name'] ?? $lesson['room']['name'] ?? 'No Room',
                    ($lesson['room']['is_lab'] ?? false) ? 'Laboratory' : 'Regular',
                    $lesson['room']['capacity'] ?? 'Not Specified'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
