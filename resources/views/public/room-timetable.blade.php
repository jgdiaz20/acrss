<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>{{ $room->name }} Timetable - Laravel School</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        /* Mobile Navigation */
        .mobile-nav {
            background: #28a745;
            color: white;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .mobile-nav h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }
        
        .mobile-nav .room-info {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .mobile-controls {
            background: white;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-controls .btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .mobile-controls .btn-text {
            display: inline;
        }
        
        /* Timetable Container - Matching Teacher Calendar Design */
        .timetable-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px;
        }
        
        /* Timetable Scroll Wrapper - Fixed width, horizontal scroll */
        .timetable-scroll-wrapper {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }
        
        .timetable-container-fixed {
            min-width: 1000px;
            width: 100%;
        }
        
        .timetable-header {
            background: white;
            color: #333;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .timetable-title {
            color: #28a745 !important;
            font-weight: bold !important;
            font-size: 24px !important;
            margin: 0 0 10px 0 !important;
        }
        
        .timetable-info {
            color: #495057 !important;
            font-size: 14px !important;
            margin: 0 !important;
            font-weight: 500 !important;
        }
        
        .timetable-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0;
            border: 1px solid #e1e5e9;
            background: white;
        }
        
        .timetable-day-header {
            background: #f8f9fa;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            border-right: 1px solid #e1e5e9;
            border-bottom: 1px solid #e1e5e9;
        }
        
        
        .timetable-day-column {
            min-height: 600px;
            background: white;
            border-right: 1px solid #e1e5e9;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        
        
        .timetable-day-column:last-child {
            border-right: none;
        }
        
       
        
        /* Teacher Timetable Class Box Styling - Fixed Dimensions for Consistency */
        .room-timetable-class-box {
            background: white !important;
            border: 1px solid #d1d3d4 !important;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 140px;
            height: 85px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            box-sizing: border-box;
        }
        
        .room-timetable-class-box .class-subject {
            color: #28a745 !important;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 3px;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .room-timetable-class-box .class-time {
            color: #495057 !important;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 2px;
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .room-timetable-class-box .class-instructor {
            color: #6c757d !important;
            font-size: 10px;
            margin-bottom: 2px;
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .room-timetable-class-box .class-room {
            color: #6c757d !important;
            font-size: 10px;
            font-weight: 500;
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .not-scheduled-box {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }
        
        /* Scrollbar Styling */
        .timetable-scroll-wrapper::-webkit-scrollbar {
            height: 8px;
        }
        
        .timetable-scroll-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .timetable-scroll-wrapper::-webkit-scrollbar-thumb {
            background: #28a745;
            border-radius: 4px;
        }
        
        .timetable-scroll-wrapper::-webkit-scrollbar-thumb:hover {
            background: #218838;
        }
        
        /* Print Styles */
        @media print {
            .mobile-nav,
            .mobile-controls {
                display: none !important;
            }
            
            body {
                background: white;
                font-size: 12pt;
            }
            
            .timetable-container {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
            }
            
            .timetable-header {
                background: white !important;
                color: #333 !important;
            }
            
            .timetable-title {
                color: #28a745 !important;
            }
            
            .timetable-scroll-wrapper,
            .timetable-container-fixed {
                overflow: visible !important;
                min-width: auto !important;
            }
            
            .timetable-grid {
                display: grid !important;
                grid-template-columns: repeat(7, 1fr) !important;
                gap: 1px !important;
                border: 2px solid #333 !important;
            }
            
            .timetable-day-header {
                background: #f5f5f5 !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .timetable-day-column {
                min-height: 400px !important;
                background: white !important;
                border: 1px solid #333 !important;
                padding: 8px !important;
                page-break-inside: avoid;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: flex-start !important;
            }
            
            .timetable-day-column.weekend {
                background: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .class-box {
                background: white !important;
                border: 1px solid #666 !important;
                border-radius: 3px !important;
                padding: 8px !important;
                margin-bottom: 6px !important;
                page-break-inside: avoid;
                width: auto !important;
                height: auto !important;
                min-height: 60px !important;
            }
            
            .class-subject,
            .class-time,
            .class-instructor,
            .class-room {
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: clip !important;
            }
            
            .class-subject {
                color: #28a745 !important;
                font-weight: bold !important;
            }
            
            .school-info {
                background: #e3f2fd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            
        }
         
        /* School Information */
        .school-info {
            background-color: #e3f2fd;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .school-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 5px;
        }
        
        .school-description {
            color: #666;
            font-size: 0.9rem;
        }
        
        
        /* Touch-friendly interactions */
        .class-box:active {
            transform: scale(0.98);
        }
        
        /* Loading state */
        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .loading i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Mobile Navigation -->
    <div class="mobile-nav">
        <h1>{{ $room->name }}</h1>
        <div class="room-info">
            @if($room->description)
                {{ $room->description }}
            @endif
            @if($room->capacity)
                • Capacity: {{ $room->capacity }} 
            @endif
        </div>
    </div>

    <!-- Mobile Controls -->
    <div class="mobile-controls">
        <button class="btn btn-outline-primary btn-sm" onclick="refreshTimetable()">
            <i class="fas fa-sync-alt"></i> <span class="btn-text">Refresh</span>
        </button>
        <button class="btn btn-success btn-sm" onclick="printTimetable()">
            <i class="fas fa-print"></i> <span class="btn-text">Print</span>
        </button>
        <button class="btn btn-info btn-sm" onclick="shareTimetable()">
            <i class="fas fa-share"></i> <span class="btn-text">Share</span>
        </button>
    </div>

    <div class="container-fluid">
        <div class="timetable-container">
            <!-- Desktop Header -->
            <div class="timetable-header">
                <h2 class="timetable-title">{{ $room->name }} Timetable</h2>
                @if($room->description)
                    <p class="timetable-info">{{ $room->description }}</p>
                @endif
                </div>
            </div>

            <!-- Timetable Grid -->
            <div class="timetable-scroll-wrapper">
                <div class="timetable-container-fixed">
                    <div class="timetable-grid">
                    <!-- Day headers -->
                    @foreach($weekDays as $day)
                        <div class="timetable-day-header">{{ $day }}</div>
                    @endforeach

                    <!-- Day columns with lessons -->
                    @foreach($weekDays as $index => $day)
                        <div class="timetable-day-column {{ ($index == 6 || $index == 7) ? 'weekend' : '' }}">
                            @if(isset($calendarData[$index]) && count($calendarData[$index]) > 0)
                                @foreach($calendarData[$index] as $lesson)
                                    <div class="class-box room-timetable-class-box">
                                        <div class="class-subject">
                                            {{ $lesson['subject_code'] }}
                                            @if(isset($lesson['lesson_type']) && $lesson['lesson_type'] === 'laboratory')
                                                <i class="fas fa-flask ml-1" title="Laboratory"></i>
                                            @elseif(isset($lesson['lesson_type']))
                                                <i class="fas fa-chalkboard-teacher ml-1" title="Lecture"></i>
                                            @endif
                                        </div>
                                        <div class="class-time">{{ $lesson['start_time'] }} - {{ $lesson['end_time'] }}</div>
                                        <div class="class-instructor">{{ $lesson['teacher_name'] }}</div>
                                        <div class="class-room">{{ $lesson['class_name'] }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="not-scheduled-box">
                                    Available
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>

            <!-- School Information -->
            <div class="school-info">
                <div class="school-name">Asian College Room Schedules</div>
                <div class="school-description">
                    <i class="fas fa-info-circle mr-1"></i>
                    Scan QR code to view this timetable anytime • Last updated: {{ date('M j, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Lesson Details Modal -->
    <div class="modal fade" id="lessonModal" tabindex="-1" role="dialog" aria-labelledby="lessonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lessonModalLabel">Lesson Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="lessonDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    
    <script>
        // Print functionality
        function printTimetable() {
            window.print();
        }
        
        // Refresh functionality
        function refreshTimetable() {
            location.reload();
        }
        
        // Share functionality
        function shareTimetable() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $room->name }} Timetable',
                    text: 'View the timetable for {{ $room->name }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('Timetable URL copied to clipboard!');
                });
            }
        }
        
        // Show lesson details
        function showLessonDetails(subject, startTime, endTime, teacher, className) {
            const details = `
                <div class="lesson-detail-item">
                    <strong>Subject:</strong> ${subject}
                </div>
                <div class="lesson-detail-item">
                    <strong>Time:</strong> ${startTime} - ${endTime}
                </div>
                <div class="lesson-detail-item">
                    <strong>Teacher:</strong> ${teacher}
                </div>
                <div class="lesson-detail-item">
                    <strong>Class:</strong> ${className}
                </div>
            `;
            
            $('#lessonDetails').html(details);
            $('#lessonModal').modal('show');
        }
        
        // Auto-refresh every 10 minutes (optional)
        // setInterval(function() {
        //     location.reload();
        // }, 600000);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printTimetable();
            }
            if (e.key === 'r' && e.ctrlKey) {
                e.preventDefault();
                refreshTimetable();
            }
        });
        
        // Touch-friendly interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add touch feedback to class boxes
            const classBoxes = document.querySelectorAll('.class-box');
            classBoxes.forEach(box => {
                box.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                box.addEventListener('touchend', function() {
                    this.style.transform = '';
                });
            });
        });
        
        // PWA-like behavior
        if ('serviceWorker' in navigator) {
            // Register service worker for offline functionality (optional)
            // navigator.serviceWorker.register('/sw.js');
        }
    </script>
</body>
</html>
