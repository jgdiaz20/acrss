<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo e($room->name); ?> Timetable - Laravel School</title>
    
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
        
        /* Timetable Container - Matching Admin Design */
        .timetable-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 0;
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
        
        /* Timetable Grid - Matching Admin Design */
        .timetable-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0;
            border: 1px solid #e1e5e9;
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
        
        .timetable-day-header:first-child {
            background: #e9ecef;
            border-right: 2px solid #dee2e6;
        }
        
        .timetable-day-column {
            min-height: 600px;
            background: white;
            border-right: 1px solid #e1e5e9;
            padding: 10px;
        }
        
        .timetable-day-column:last-child {
            border-right: none;
        }
        
        .timetable-day-column.weekend {
            background: #f8f9fa;
        }
        
        /* Class Box Styling - Matching Admin Design */
        /* Enhanced base styles for better mobile readability */
        .class-box {
            background: white;
            border: 2px solid #28a745;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .class-box:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }
        
        .class-subject {
            font-weight: 700;
            color: #28a745;
            font-size: 14px;
            margin-bottom: 4px;
            line-height: 1.3;
            text-align: center;
        }
        
        .class-time {
            color: #495057;
            font-size: 12px;
            margin-bottom: 3px;
            font-weight: 600;
            text-align: center;
            background: #f8f9fa;
            padding: 3px 6px;
            border-radius: 4px;
        }
        
        .class-instructor {
            color: #6c757d;
            font-size: 11px;
            margin-bottom: 2px;
            text-align: center;
            font-weight: 500;
        }
        
        .class-room {
            color: #6c757d;
            font-size: 11px;
            text-align: center;
            font-weight: 500;
        }
        
        .not-scheduled-box {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 20px;
            font-size: 14px;
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
        
        /* Mobile Responsive Design - Fixed Static Layout */
        @media (max-width: 1200px) {
            .timetable-container {
                overflow-x: auto;
                overflow-y: auto;
            }
            
            .timetable-grid {
                min-width: 700px;
                grid-template-columns: repeat(7, 120px);
                width: max-content;
            }
            
            .class-box {
                padding: 10px;
            }
            
            .class-subject {
                font-size: 13px;
            }
        }
        
        @media (max-width: 768px) {
            .mobile-nav {
                display: block;
            }
            
            .timetable-header {
                display: none;
            }
            
            /* Fixed static mobile grid layout with proper alignment */
            .timetable-container {
                overflow-x: auto;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                max-height: 80vh;
            }
            
            .timetable-grid {
                min-width: 600px;
                grid-template-columns: repeat(7, 100px);
                width: max-content;
                font-size: 12px;
                gap: 1px;
                border: 2px solid #dee2e6;
            }
            
            .timetable-day-header {
                padding: 12px 8px;
                font-size: 11px;
                font-weight: 700;
                text-align: center;
                line-height: 1.3;
                word-break: break-word;
                background: #e9ecef;
                color: #495057;
                border-bottom: 2px solid #dee2e6;
                border-right: 1px solid #e1e5e9;
                min-width: 100px;
                width: 100px;
            }
            
            
            .timetable-day-column {
                min-height: 500px;
                padding: 8px 6px;
                display: flex;
                flex-direction: column;
                gap: 6px;
                background: white;
                border-right: 1px solid #e1e5e9;
                min-width: 100px;
                width: 100px;
            }
            
            .timetable-day-column:last-child {
                border-right: none;
            }
            
            .timetable-day-column.weekend {
                background: #f8f9fa;
            }
            
            /* Improved class box styling for mobile readability */
            .class-box {
                padding: 12px 8px;
                margin-bottom: 0;
                border-radius: 6px;
                min-height: 85px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                background: white;
                border: 2px solid #28a745;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.2s ease;
                width: 100%;
                box-sizing: border-box;
            }
            
            .class-box:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                transform: translateY(-1px);
            }
            
            .class-subject {
                font-size: 13px;
                margin-bottom: 4px;
                line-height: 1.3;
                font-weight: 700;
                color: #28a745;
                word-break: break-word;
                text-align: center;
            }
            
            .class-time {
                font-size: 11px;
                margin-bottom: 3px;
                font-weight: 600;
                color: #495057;
                text-align: center;
                background: #f8f9fa;
                padding: 3px 5px;
                border-radius: 4px;
                width: 100%;
                box-sizing: border-box;
            }
            
            .class-instructor {
                font-size: 10px;
                line-height: 1.2;
                color: #6c757d;
                word-break: break-word;
                text-align: center;
                font-weight: 500;
            }
            
            .class-room {
                font-size: 10px;
                line-height: 1.2;
                color: #6c757d;
                word-break: break-word;
                text-align: center;
                font-weight: 500;
            }
            
            .not-scheduled-box {
                padding: 25px 10px;
                font-size: 11px;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 80px;
                background: #f8f9fa;
                border: 1px dashed #dee2e6;
                border-radius: 6px;
                color: #6c757d;
                font-style: italic;
                width: 100%;
                box-sizing: border-box;
            }
            
            .school-info {
                padding: 15px;
            }
            
            .school-name {
                font-size: 1rem;
            }
            
            .school-description {
                font-size: 0.8rem;
            }
            
            /* Mobile controls improvements */
            .mobile-controls {
                flex-wrap: wrap;
                gap: 8px;
                padding: 12px 15px;
            }
            
            .mobile-controls .btn {
                flex: 1;
                min-width: 80px;
                padding: 10px 12px;
                font-size: 0.85rem;
            }
            
            .mobile-controls .btn-text {
                display: inline;
            }
        }
        
        @media (max-width: 480px) {
            /* Fixed static layout for small screens */
            .timetable-container {
                overflow-x: auto;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                max-height: 75vh;
            }
            
            .timetable-grid {
                min-width: 500px;
                grid-template-columns: repeat(7, 90px);
                width: max-content;
                gap: 1px;
                border: 2px solid #dee2e6;
            }
            
            .timetable-day-header {
                padding: 10px 6px;
                font-size: 10px;
                font-weight: 700;
                line-height: 1.2;
                background: #e9ecef;
                border-bottom: 2px solid #dee2e6;
                min-width: 90px;
                width: 90px;
            }
            
            
            .timetable-day-column {
                min-height: 450px;
                padding: 6px 4px;
                gap: 5px;
                background: white;
                border-right: 1px solid #e1e5e9;
                min-width: 90px;
                width: 90px;
            }
            
            .timetable-day-column.weekend {
                background: #f8f9fa;
            }
            
            /* Optimized class boxes for small mobile screens */
            .class-box {
                padding: 10px 6px;
                min-height: 70px;
                border-radius: 5px;
                border: 2px solid #28a745;
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                width: 100%;
                box-sizing: border-box;
            }
            
            .class-subject {
                font-size: 12px;
                margin-bottom: 3px;
                font-weight: 700;
                color: #28a745;
                text-align: center;
                line-height: 1.2;
            }
            
            .class-time {
                font-size: 10px;
                margin-bottom: 2px;
                font-weight: 600;
                color: #495057;
                text-align: center;
                background: #f8f9fa;
                padding: 2px 3px;
                border-radius: 3px;
            }
            
            .class-instructor {
                font-size: 9px;
                line-height: 1.1;
                color: #6c757d;
                text-align: center;
                font-weight: 500;
            }
            
            .class-room {
                font-size: 9px;
                line-height: 1.1;
                color: #6c757d;
                text-align: center;
                font-weight: 500;
            }
            
            .not-scheduled-box {
                padding: 20px 8px;
                font-size: 10px;
                min-height: 70px;
                background: #f8f9fa;
                border: 1px dashed #dee2e6;
                border-radius: 5px;
                width: 100%;
                box-sizing: border-box;
            }
            
            /* Mobile navigation improvements */
            .mobile-nav {
                padding: 12px 15px;
            }
            
            .mobile-nav h1 {
                font-size: 1.3rem;
            }
            
            .mobile-nav .room-info {
                font-size: 0.85rem;
            }
            
            /* Mobile controls for small screens */
            .mobile-controls {
                padding: 10px 15px;
            }
            
            .mobile-controls .btn {
                padding: 8px 10px;
                font-size: 0.8rem;
                min-width: 70px;
            }
            
            .mobile-controls .btn-text {
                display: none;
            }
        }
        
        @media (max-width: 360px) {
            /* Fixed static layout for very small screens */
            .timetable-container {
                overflow-x: auto;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                max-height: 70vh;
            }
            
            .timetable-grid {
                min-width: 450px;
                grid-template-columns: repeat(7, 80px);
                width: max-content;
                gap: 1px;
                border: 2px solid #dee2e6;
            }
            
            .timetable-day-header {
                padding: 8px 4px;
                font-size: 9px;
                font-weight: 700;
                line-height: 1.1;
                background: #e9ecef;
                border-bottom: 2px solid #dee2e6;
                min-width: 80px;
                width: 80px;
            }
            
            
            .timetable-day-column {
                padding: 4px 3px;
                min-height: 400px;
                gap: 4px;
                background: white;
                border-right: 1px solid #e1e5e9;
                min-width: 80px;
                width: 80px;
            }
            
            .timetable-day-column.weekend {
                background: #f8f9fa;
            }
            
            /* Compact but readable class boxes for very small screens */
            .class-box {
                padding: 8px 5px;
                min-height: 65px;
                border-radius: 4px;
                border: 2px solid #28a745;
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                width: 100%;
                box-sizing: border-box;
            }
            
            .class-subject {
                font-size: 11px;
                margin-bottom: 2px;
                font-weight: 700;
                color: #28a745;
                text-align: center;
                line-height: 1.1;
            }
            
            .class-time {
                font-size: 9px;
                margin-bottom: 2px;
                font-weight: 600;
                color: #495057;
                text-align: center;
                background: #f8f9fa;
                padding: 1px 2px;
                border-radius: 2px;
            }
            
            .class-instructor {
                font-size: 8px;
                line-height: 1;
                color: #6c757d;
                text-align: center;
                font-weight: 500;
            }
            
            .class-room {
                font-size: 8px;
                line-height: 1;
                color: #6c757d;
                text-align: center;
                font-weight: 500;
            }
            
            .not-scheduled-box {
                padding: 15px 5px;
                font-size: 9px;
                min-height: 65px;
                background: #f8f9fa;
                border: 1px dashed #dee2e6;
                border-radius: 4px;
                width: 100%;
                box-sizing: border-box;
            }
            
            /* Mobile controls for very small screens */
            .mobile-controls {
                padding: 8px 12px;
            }
            
            .mobile-controls .btn {
                padding: 6px 8px;
                font-size: 0.75rem;
                min-width: 60px;
            }
            
            .mobile-controls .btn-text {
                display: none;
            }
        }
        
        /* Print Styles */
        @media  print {
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
        <h1><?php echo e($room->name); ?></h1>
        <div class="room-info">
            <?php if($room->description): ?>
                <?php echo e($room->description); ?>

            <?php endif; ?>
            <?php if($room->capacity): ?>
                • Capacity: <?php echo e($room->capacity); ?> students
            <?php endif; ?>
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
                <h2 class="timetable-title"><?php echo e($room->name); ?> Timetable</h2>
                <?php if($room->description): ?>
                    <p class="timetable-info"><?php echo e($room->description); ?></p>
                <?php endif; ?>
                <div class="print-only" style="margin-top: 10px; font-size: 12px; opacity: 0.8;">
                    <p>Printed on: <?php echo e(date('F j, Y \a\t g:i A')); ?></p>
                    <?php if($room->capacity): ?>
                        <p>Capacity: <?php echo e($room->capacity); ?> students</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Timetable Grid -->
            <div class="timetable-grid">
                <!-- Day headers only -->
                <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNumber => $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="timetable-day-header"><?php echo e($dayName); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Day columns with lessons -->
                <?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNumber => $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="timetable-day-column <?php echo e(($dayNumber == 6 || $dayNumber == 7) ? 'weekend' : ''); ?>" data-day="<?php echo e($dayNumber); ?>">
                        <?php if(isset($calendarData[$dayNumber]) && count($calendarData[$dayNumber]) > 0): ?>
                            <?php $__currentLoopData = $calendarData[$dayNumber]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lessonIndex => $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="lesson-container">
                                    <div class="class-box" onclick="showLessonDetails('<?php echo e($lesson['subject_name']); ?>', '<?php echo e($lesson['start_time']); ?>', '<?php echo e($lesson['end_time']); ?>', '<?php echo e($lesson['teacher_name']); ?>', '<?php echo e($lesson['class_name']); ?>')">
                                        <div class="class-subject"><?php echo e($lesson['subject_name']); ?></div>
                                        <div class="class-time"><?php echo e($lesson['start_time']); ?> - <?php echo e($lesson['end_time']); ?></div>
                                        <div class="class-instructor"><?php echo e($lesson['teacher_name']); ?></div>
                                        <div class="class-room"><?php echo e($lesson['class_name']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="not-scheduled-box">
                                <?php echo e(($dayNumber == 6 || $dayNumber == 7) ? 'Weekend' : ''); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- School Information -->
            <div class="school-info">
                <div class="school-name">Laravel School Timetable Calendar</div>
                <div class="school-description">
                    <i class="fas fa-info-circle mr-1"></i>
                    Scan QR code to view this timetable anytime • Last updated: <?php echo e(date('M j, Y')); ?>

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
                    title: '<?php echo e($room->name); ?> Timetable',
                    text: 'View the timetable for <?php echo e($room->name); ?>',
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
<?php /**PATH C:\Users\jimbo\Desktop\Laravel_Timetable\Laravel-School-Timetable-Calendar\resources\views/public/room-timetable.blade.php ENDPATH**/ ?>