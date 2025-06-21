<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ovum</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="custom-icon me-2">
                <img src="{{asset('assets/images/ovumlogo.svg')}}" alt="icon" />
                </span>
                Ovum
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#doctors">For Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pipeline">Research</a>
                    </li>
                </ul>
                <a href="{{route('login')}}" class="btn btn-doctor-portal">
                    <i class="fas fa-user-md me-2"></i>Doctor Portal
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Track Your Cycle,<br>Connect with Care</h1>
                    <p class="hero-subtitle">
                        The intelligent menstrual health platform that connects you with your gynecologist 
                        for personalized care, symptom tracking, and data-driven insights.
                    </p>
                    <div class="cta-buttons">
                        <button type="button" class="btn btn-primary-custom" onclick="downloadApk();">
                            <i class="fas fa-download me-2"></i>Download App
                        </button>
                        <a href="#features" class="btn btn-secondary-custom">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="phone-mockup">
                        <div class="phone-frame">
                            <div class="phone-screen">
                                <div class="app-screenshot text-center position-relative h-100 w-100">
                                    <div id="appCarousel" class="carousel slide h-100 w-100" data-bs-ride="carousel">
                                        <div class="carousel-inner h-100 w-100">
                                            <div class="carousel-item active h-100 w-100">
                                                <img src="{{ asset('assets/screenshots/mobile-splash.jpg') }}" class="d-block w-100 h-100" alt="Ovum App Home Screenshot" style="object-fit: cover; border-radius: 20px;">
                                            </div>
                                            <div class="carousel-item h-100 w-100">
                                                <img src="{{ asset('assets/screenshots/mobile-calendar.jpg') }}" class="d-block w-100 h-100" alt="Ovum App Logger Screenshot" style="object-fit: cover; border-radius: 20px;">
                                            </div>
                                            <div class="carousel-item h-100 w-100">
                                                <img src="{{ asset('assets/screenshots/mobile-logger.jpg') }}" class="d-block w-100 h-100" alt="Ovum App Assistant Screenshot" style="object-fit: cover; border-radius: 20px;">
                                            </div>
                                            <div class="carousel-item h-100 w-100">
                                                <img src="{{ asset('assets/screenshots/mobile-home-page.jpg') }}" class="d-block w-100 h-100" alt="Ovum App Calendar Screenshot" style="object-fit: cover; border-radius: 20px;">
                                            </div>
                                            <div class="carousel-item h-100 w-100">
                                                <img src="{{ asset('assets/screenshots/mobile-assistant.jpg') }}" class="d-block w-100 h-100" alt="Ovum App Splash Screenshot" style="object-fit: cover; border-radius: 20px;">
                                            </div>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#appCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#appCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="floating-elements">
            <i class="fas fa-heart floating-icon" style="left: 10%; animation-delay: 0s;"></i>
            <i class="fas fa-calendar floating-icon" style="left: 20%; animation-delay: 3s;"></i>
            <i class="fas fa-chart-line floating-icon" style="left: 80%; animation-delay: 6s;"></i>
            <i class="fas fa-stethoscope floating-icon" style="left: 90%; animation-delay: 9s;"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3" style="color: var(--triadic-color);">
                        Personal Health Companion
                    </h2>
                    <p class="lead text-muted">
                        Comprehensive menstrual health tracking with professional medical integration
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--triadic-color);">Smart Cycle Tracking</h4>
                        <p class="text-muted">
                            Intelligent period prediction with symptom logging, mood tracking, 
                            and personalized insights based on your unique patterns.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in-up" style="animation-delay: 0.2s;">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--triadic-color);">Doctor Integration</h4>
                        <p class="text-muted">
                            Seamlessly share your cycle data with your gynecologist for 
                            professional monitoring, consultations, and appointment scheduling.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in-up" style="animation-delay: 0.4s;">
                        <div class="feature-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--triadic-color);">Anomaly Detection</h4>
                        <p class="text-muted">
                            Advanced algorithms identify irregularities in your cycle, 
                            alerting both you and your healthcare provider when needed.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in-up" style="animation-delay: 0.6s;">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--triadic-color);">Data Insights</h4>
                        <p class="text-muted">
                            Comprehensive analytics and reports that help you understand 
                            your reproductive health trends over time.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in-up" style="animation-delay: 0.8s;">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--triadic-color);">Smart Reminders</h4>
                        <p class="text-muted">
                            Personalized notifications for period predictions, medication reminders, 
                            and appointment scheduling.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in-up" style="animation-delay: 1s;">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="mb-3" style="color: var(--triadic-color);">Secure & Private</h4>
                        <p class="text-muted">
                            Health data confidential to its owner, with complete control 
                            over what they share with your healthcare providers via OTP verification.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctor Integration Section -->
    <section class="doctor-section" id="doctors">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="doctor-card">
                        <h2 class="display-6 fw-bold mb-4" style="color: var(--triadic-color);">
                            For Healthcare Professionals
                        </h2>
                        <p class="lead mb-4">
                            Access your patients' menstrual health data through our secure web portal, 
                            enabling better care and informed medical decisions.
                        </p>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                        style="width: 40px; height: 40px; background: var(--complementary-color) !important;">
                                        <i class="fas fa-chart-bar text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Real-time Patient Monitoring</h6>
                                        <small class="text-muted">Track patient cycles and symptoms continuously</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                        style="width: 40px; height: 40px; background: var(--success-color) !important;">
                                        <i class="fas fa-calendar-plus text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Integrated Scheduling</h6>
                                        <small class="text-muted">Seamless appointment booking and management</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                        style="width: 40px; height: 40px; background: var(--warning-color) !important;">
                                        <i class="fas fa-clipboard-check text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Clinical Decision Support</h6>
                                        <small class="text-muted">AI-powered insights for better diagnoses</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{route('login')}}" class="btn btn-primary-custom">
                            <i class="fas fa-sign-in-alt me-2"></i>Access Doctor Portal
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="text-center">
                        <div class="bg-white rounded-3 p-4 shadow-lg" style="border: 3px dashed var(--primary-color);">
                            <div id="doctorCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="{{ asset('assets/screenshots/doctor-dashboard.jpg') }}" class="d-block w-100 img-fluid rounded shadow mb-3" alt="Ovum Doctor Portal Dashboard" style="max-height: 350px; object-fit: cover;">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{ asset('assets/screenshots/doctor-labs.jpg') }}" class="d-block w-100 img-fluid rounded shadow mb-3" alt="Ovum Doctor Labs Screenshot" style="max-height: 350px; object-fit: cover;">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{ asset('assets/screenshots/doctor-patientinfo.jpg') }}" class="d-block w-100 img-fluid rounded shadow mb-3" alt="Ovum Doctor Patient Info Screenshot" style="max-height: 350px; object-fit: cover;">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#doctorCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#doctorCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                            <h5 style="color: var(--triadic-color);">Doctor Portal Dashboard</h5>
                            <p class="text-muted">Dashboard preview</p>
                            <div class="row g-2 mt-3">
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <i class="fas fa-users text-muted"></i>
                                        <small class="d-block text-muted">Patients</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <i class="fas fa-chart-pie text-muted"></i>
                                        <small class="d-block text-muted">Analytics</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <i class="fas fa-calendar text-muted"></i>
                                        <small class="d-block text-muted">Schedule</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Pipeline Section -->
    <section class="pipeline-section" id="pipeline">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Research-Driven Data Pipeline</h2>
                    <p class="lead">
                        Contributing to gynecological research through secure, anonymized data collection 
                        that advances women's health understanding globally.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="pipeline-step">
                        <i class="fas fa-mobile-alt mb-3" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                        <h5 class="mb-3">Data Collection</h5>
                        <p class="mb-0">
                            Users track their cycles, symptoms, and health patterns through the mobile app
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-1 col-md-12 d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-right pipeline-arrow d-none d-lg-block"></i>
                    <i class="fas fa-arrow-down pipeline-arrow d-lg-none"></i>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="pipeline-step">
                        <i class="fas fa-user-md mb-3" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                        <h5 class="mb-3">Medical Review</h5>
                        <p class="mb-0">
                            Healthcare providers analyze and label patient data through the web portal
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-1 col-md-12 d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-right pipeline-arrow d-none d-lg-block"></i>
                    <i class="fas fa-arrow-down pipeline-arrow d-lg-none"></i>
                </div>
                
                <div class="col-lg-4 col-md-12">
                    <div class="pipeline-step">
                        <i class="fas fa-database mb-3" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                        <h5 class="mb-3">Research Database</h5>
                        <p class="mb-0">
                            Anonymized data contributes to advancing gynecological research and improving 
                            women's healthcare outcomes worldwide
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="bg-white bg-opacity-10 rounded-3 p-4 backdrop-blur">
                        <h4 class="mb-3">Join the Research Community</h4>
                        <p class="mb-4">
                            Your participation helps create better healthcare solutions for women everywhere. 
                            All data is completely anonymized and used solely for advancing medical research.
                        </p>
                        <a href="#" class="btn btn-light btn-lg">
                            <i class="fas fa-microscope me-2"></i>Learn More About Our Research
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light pt-5 pb-4">
        <div class="container text-center">
            <div class="row text-center">

                <!-- Ovum Overview -->
                <div class="col-md-5 mb-4">
                    <h5 class="text-uppercase fw-bold text-white">About Ovum</h5>
                    <p class="text-light small mt-3">
                        <strong>A Menstrual</strong> health tracking system developed through dedicated research at
                        <span class="text-white">Mbarara University of Science and Technology (MUST)</span>,
                        and piloted at <span class="text-white">Mbarara Regional Referral Hospital</span>.
                        <br/>
                        It is an efficient data pipeline application that supports AI-Driven inferencing for Menstrual Health and provides women with intelligent reproductive insights based on personalized health data.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="col-md-3 mb-4">
                    <h5 class="text-uppercase fw-bold text-white">Quick Links</h5>
                    <ul class="list-unstyled mt-3">
                        <li><a href="#features" class="text-light text-decoration-none d-block mb-2">Features</a></li>
                        <li><a href="#pipeline" class="text-light text-decoration-none d-block mb-2">Research Background</a></li>
                        <li><a href="#doctors" class="text-light text-decoration-none d-block mb-2">For Doctors</a></li>
                        <li><a href="#faq" class="text-light text-decoration-none d-block">FAQs</a></li>
                    </ul>
                </div>

                <!-- Logo + Motto -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-bold">Ovum</h5>
                    <p class="fst-italic text-light mt-2">"Improving AI-Driven soultions with large quality data"</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="text-light me-3 fs-4 social-icon-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3 fs-4 social-icon-link"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="text-light me-3 fs-4 social-icon-link"><i class="fab fa-github"></i></a>
                        <a href="#" class="text-light me-3 fs-4 social-icon-link"><i class="fas fa-envelope"></i></a>
                        <a href="#" class="text-light fs-4 social-icon-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

            </div>

            <hr class="border-secondary">
            <div class="row">
                <div class="col-12">
                    <p> Ovum is still in the pipeline for production and all contributions are welcome.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // You can call this function to trigger the download
        function downloadApk(fileName = 'app-release.apk') {
            // Construct the URL with query parameters
            const url = `https://gyn.lockfreed.com/download.php?action=download&file=${encodeURIComponent(fileName)}`;
            // Create a temporary link element
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName; // This hints the browser to download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>