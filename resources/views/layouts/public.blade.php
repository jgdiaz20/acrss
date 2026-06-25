<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Timetable System') }} - Asian College Dumaguete</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.2.0/css/all.min.css">

    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --text-dark: #1a1a1a;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text-dark);
            background: var(--white);
            line-height: 1.6;
            padding-top: 85px;
        }

        /* Navbar */
        .navbar-custom {
            /* 1. Makes navbar float on top of the content so the background blur is visible */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;

            /* 2. Frosted glass properties: Translucent white + heavy blur */
            background: rgba(255, 255, 255, 0.65); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px); /* Safari support */

            /* 3. Subtle styles for the glass aesthetic */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4); /* Light glass border */
            padding: 0.8rem 0; /* Slightly tighter padding for an elegant look */
        }

        /* Ensure the navbar links use darker text to contrast beautifully against the blur */
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-dark) !important;
        }

        .navbar-brand img {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .nav-link {
            color: #4a5568 !important; /* Slightly darker than muted for optimal accessibility */
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            margin: 0 0.75rem;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.25);
            text-decoration: none;
        }

.hero-section {
    position: relative; /* Establishes a stacking context */
    color: white;
    padding: 4rem 0;
    animation: fadeInDown 0.8s ease-out;
    overflow: hidden; /* Prevents blurred edges from leaking out */
}

    /* Background Layer */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        
        /* 1. The Background Image */
        background: url('{{ asset('images/asian-college-building.jpg') }}') no-repeat center center/cover;
        
        /* 2. Reduced Brightness & Dark Overlay (Adjust 0.5 to tweak darkness) */
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('{{ asset('images/asian-college-building.jpg') }}');
        
        /* 3. The Blur Effect */
        filter: blur(8px); 
        
        /* 4. Zoom slightly to hide raw/sharp edges caused by the blur */
        transform: scale(1.05); 
        
        /* Keeps background behind your text contents */
        z-index: 1; 
    }

    /* Foreground Layer Container */
    .hero-section .container {
        position: relative;
        z-index: 2; /* Forces text/buttons to stay crisp on top of the blur */
    }


        .hero-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 4rem;
        }

        .hero-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            font-weight: 300;
            line-height: 1.7;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            min-height: 400px;
            object-fit: cover;
            border-radius: 12px;
            animation: fadeInUp 0.8s ease-out;
        }
        .hero-image-wrapper {
            align-self: flex-end;
            margin-bottom: -4rem; /* Match this exactly to your .hero-section bottom padding */
        }       

        .btn-primary-hero {
            background: white;
            color: var(--primary);
            padding: 0.9rem 2.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            font-size: 1rem;
            display: inline-block;
        }

        .btn-primary-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            color: var(--primary);
            text-decoration: none;
        }

        /* Sections */
        .section {
            padding: 6rem 0;
        }

        .section-light {
            background: var(--bg-light);
        }

        .section-title {
            font-size: 2.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
            text-align: center;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            font-size: 1.05rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 3.5rem;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
            font-weight: 300;
            line-height: 1.7;
        }

        /* Feature Cards - Improved Consistency */
        .feature-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.4s; }
        .feature-card:nth-child(5) { animation-delay: 0.5s; }
        .feature-card:nth-child(6) { animation-delay: 0.6s; }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .feature-card h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
            line-height: 1.4;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin: 0;
            flex-grow: 1;
        }

        /* Content Section */
        .content-section {
            display: flex;
            align-items: center;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        .content-section.reverse {
            flex-direction: row-reverse;
        }

        .content-text h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .content-text p {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .content-icon {
            font-size: 5rem;
            color: var(--primary);
            opacity: 0.1;
            flex-shrink: 0;
        }

        /* Goals Section - Improved Design */
        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .goal-item {
            padding: 2rem;
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            animation: slideInLeft 0.6s ease-out;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .goal-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .goal-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            border-color: var(--primary);
        }

        .goal-item h4 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            font-size: 1.15rem;
        }

        .goal-item p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 5rem 0;
            text-align: center;
            border-radius: 12px;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            font-weight: 300;
        }

        .btn-cta-primary {
            background: white;
            color: var(--primary);
            padding: 0.9rem 2.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .btn-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            color: var(--primary);
            text-decoration: none;
        }

        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0;
            text-align: center;
            font-size: 0.95rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer p {
            margin-bottom: 0.5rem;
            opacity: 0.8;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .hero-container {
                gap: 2rem;
            }

            .hero-section h1 {
                font-size: 3rem;
            }

            .hero-image {
                min-height: 350px;
            }
        }

        @media (max-width: 992px) {
            .hero-section {
                padding: 3rem 0;
            }

            .hero-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-content {
                text-align: center;
            }

            .hero-section h1 {
                font-size: 2.5rem;
            }

            .hero-section p {
                font-size: 1rem;
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-image {
                display: none;
            }

            .hero-section p {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-section p {
                max-width: 100%;
            }

            .hero-section p {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-section {
                text-align: center;
            }

            .navbar-brand span {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .section {
                padding: 3rem 0;
            }

            .hero-section {
                padding: 2rem 0;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section p {
                font-size: 0.95rem;
            }

            .hero-image {
                display: none;
            }

            .btn-primary-hero {
                padding: 0.75rem 2rem;
                font-size: 0.95rem;
            }

            .navbar-brand img {
                height: 45px;
            }

            .goals-grid {
                grid-template-columns: 1fr;
            }

            .cta-section h2 {
                font-size: 1.75rem;
            }

            .content-section {
                flex-direction: column;
                gap: 2rem;
            }

            .content-section.reverse {
                flex-direction: column;
            }

            .content-icon {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }

            .hero-section p {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            .hero-image {
                min-height: 200px;
            }

            .btn-primary-hero {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }

            .section-title {
                font-size: 1.75rem;
            }

            .section-subtitle {
                font-size: 0.95rem;
            }

            .feature-card {
                padding: 1.5rem 1rem;
            }

            .feature-card h4 {
                font-size: 1.1rem;
            }

            .feature-card p {
                font-size: 0.9rem;
            }

            .content-text h3 {
                font-size: 1.5rem;
            }

            .content-text p {
                font-size: 0.95rem;
            }

            .cta-section {
                padding: 3rem 0;
            }

            .cta-section h2 {
                font-size: 1.5rem;
            }

            .cta-section p {
                font-size: 1rem;
            }

            .nav-link {
                margin: 0 0.25rem;
            }
        }
    </style>

    @yield('extra-css')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home.public') }}">
                <img src="{{ asset('images/ACRSS LOGO_NEW (3).svg') }}" alt="ACD Logo" class="logo-image">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#goals">Goals</a>
                    </li>
                    <li class="nav-item ml-2">
                        <a href="{{ route('login') }}" class="btn btn-login">
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Asian College Dumaguete - Timetable Management System</p>
            <p>All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    <script>
        // Scroll animation observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .goal-item').forEach(el => {
            observer.observe(el);
        });
    </script>

    @yield('extra-js')
</body>
</html>
