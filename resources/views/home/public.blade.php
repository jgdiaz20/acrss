@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Asian College Dumaguete<br>Room Scheduling System</h1>
                <p>Streamline your academic scheduling with our intelligent, conflict-free timetable solution designed for modern educational institutions.</p>
                <div>
                    <a href="{{ route('login') }}" class="btn btn-primary-hero">
                        Get Started
                    </a>
                </div>
            </div>
            <div class="hero-image-wrapper">
                <img src="{{ asset('images/hero-acd-students.png') }}" alt="Asian College Dumaguete Staff" class="hero-image">
            </div>
        </div>
    </div>
</section>

<!-- Introduction Section -->
<section class="section">
    <div class="container">
        <div class="content-section">
            <div class="content-text">
                <h3>Intelligent Scheduling Made Simple</h3>
                <p>The Asian College Dumaguete Timetable Management System revolutionizes how academic institutions organize and manage class schedules. With advanced conflict detection, real-time updates, and an intuitive interface, administrators can create comprehensive schedules in minutes.</p>
                <p>Our platform provides transparency across all stakeholders—administrators gain control, teachers see their schedules clearly, and students access complete class information with ease.</p>
            </div>
            <div class="content-icon">
                <i class="fas fa-tasks"></i>
            </div>
        </div>
    </div>
</section>

<!-- Core Features Section -->
<section class="section section-light" id="features">
    <div class="container">
        <h2 class="section-title">Powerful Features</h2>
        <p class="section-subtitle">Everything you need to manage academic schedules efficiently and transparently</p>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Smart Timetable Management</h4>
                    <p>Create and organize comprehensive class schedules with an intuitive interface. Assign teachers, rooms, and subjects effortlessly.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Real-time Conflict Detection</h4>
                    <p>Automatically detect and prevent scheduling conflicts involving teachers, rooms, and classes before they happen.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h4>Teacher Dashboard</h4>
                    <p>Teachers access personalized dashboards showing their weekly schedules, today's classes, and upcoming lessons at a glance.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h4>Room Management</h4>
                    <p>Track and optimize room utilization. Manage room capacities, equipment, and lab requirements for better resource allocation.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>Class Schedules</h4>
                    <p>Organize curriculum effectively with detailed class schedules. Track subjects, durations, and all schedule-related information centrally.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4>System Transparency</h4>
                    <p>Public room schedules are available for students and stakeholders, ensuring full transparency across the institution.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Functionality Overview Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title" style="margin-bottom: 3rem;">How It Works</h2>

        <div class="content-section">
            <div class="content-text">
                <h3>Admin Control Center</h3>
                <p>Administrators have complete control over the entire timetable system. Create academic programs, define grade levels, assign subjects to classes, and build the master timetable from a centralized dashboard.</p>
                <p>View real-time statistics including total active schedules, room utilization rates, and system performance metrics.</p>
            </div>
            <div class="content-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
        </div>

        <div class="content-section reverse">
            <div class="content-text">
                <h3>Multi-View Perspectives</h3>
                <p>The system provides different views tailored to each user role. Admins see the full master timetable for all rooms and classes, while teachers view only their assigned schedules, and students access their class information.</p>
                <p>Each perspective is optimized for its specific use case, making navigation and information retrieval intuitive.</p>
            </div>
            <div class="content-icon">
                <i class="fas fa-window-maximize"></i>
            </div>
        </div>

        <div class="content-section">
            <div class="content-text">
                <h3>Conflict Resolution Engine</h3>
                <p>Our advanced conflict detection system checks for overlaps involving teachers, rooms, and class schedules. The system validates scheduling constraints including subject type requirements (lecture vs. laboratory), duration limits, and total hour requirements.</p>
                <p>Administrators are immediately notified of any conflicts and can resolve them before finalizing schedules.</p>
            </div>
            <div class="content-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
        </div>

        <div class="content-section reverse">
            <div class="content-text">
                <h3>Mobile-Responsive Design</h3>
                <p>Access the timetable system from any device. The responsive design ensures that administrators, teachers, and students can view schedules on desktop computers, tablets, and smartphones.</p>
                <p>Responsive layouts adapt automatically, maintaining full functionality across all screen sizes.</p>
            </div>
            <div class="content-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
        </div>
    </div>
</section>

<!-- Goals for ACD Section -->
<section class="section section-light" id="goals">
    <div class="container">
        <h2 class="section-title">Our Goals for Asian College Dumaguete</h2>
        <p class="section-subtitle">Empowering the institution with modern scheduling solutions</p>

        <div class="goals-grid">
            <div class="goal-item">
                <h4>Improve Scheduling Efficiency</h4>
                <p>Reduce manual scheduling time by automating conflict detection and providing intelligent scheduling recommendations.</p>
            </div>

            <div class="goal-item">
                <h4>Eliminate Conflicts</h4>
                <p>Prevent double-booked teachers, rooms, and classes with real-time conflict detection before schedules are finalized.</p>
            </div>

            <div class="goal-item">
                <h4>Enhance Transparency</h4>
                <p>Provide all stakeholders—students, teachers, and administration—with clear visibility into schedules and timetables.</p>
            </div>

            <div class="goal-item">
                <h4>Optimize Resources</h4>
                <p>Better utilize classrooms, laboratories, and equipment by analyzing utilization patterns and improving allocation strategies.</p>
            </div>

            <div class="goal-item">
                <h4>Support Growth</h4>
                <p>Provide scalable infrastructure that grows with the institution, supporting new programs, classes, and academic structures.</p>
            </div>

            <div class="goal-item">
                <h4>Enable Data-Driven Decisions</h4>
                <p>Provide analytics and insights into scheduling patterns, room utilization, and resource allocation for informed decision-making.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call-to-Action Section -->
<section class="section">
    <div class="container">
        <div class="cta-section">
            <h2>Ready to Transform Your Academic Scheduling?</h2>
            <p>Join Asian College Dumaguete in revolutionizing how we manage class schedules and optimize resources.</p>
            <a href="{{ route('login') }}" class="btn btn-cta-primary">
                Login to Access
            </a>
        </div>
    </div>
</section>
@endsection
