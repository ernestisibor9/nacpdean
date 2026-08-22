<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Home - NACPDEAN</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <!-- =======================================================
  * Template Name: Medicio
  * Template URL: https://bootstrapmade.com/medicio-free-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
    <style>
        /* Topbar Social Icons */
        .social-icons a {
            color: #ffffff;
            font-size: 18px;
            transition: all .3s ease;
            text-decoration: none;
        }

        .social-icons a:hover {
            color: #ffffff;
            transform: translateY(-3px);
        }
    </style>

    <style>
        .whatsapp-widget {
            position: fixed;
            bottom: 25px;
            left: 25px;
            z-index: 9999;
        }

        .whatsapp-widget a {
            width: 65px;
            height: 65px;
            background: #25D366;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            text-decoration: none;
            font-size: 35px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .3);
            transition: .3s;
        }

        .whatsapp-widget a:hover {
            transform: scale(1.08);
        }

        .whatsapp-text {
            position: absolute;
            right: 80px;
            bottom: 10px;
            background: #fff;
            padding: 12px 15px;
            border-radius: 10px;
            width: 200px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .15);
            font-size: 14px;
            line-height: 1.4;
        }

        @media(max-width:768px) {
            .whatsapp-text {
                display: none;
            }
        }
    </style>

    <style>
        .featured-video-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 35px;
        }

        .featured-video-btn {

            display: flex;
            align-items: center;
            gap: 18px;

            padding: 18px 30px;

            background: #608C11;
            color: #fff;

            border: none;
            border-radius: 60px;

            font-size: 22px;
            font-weight: 700;

            box-shadow: 0 10px 30px rgba(0, 0, 0, .35);

            transition: .3s;

            min-width: 420px;
            cursor: pointer;
        }

        .featured-video-btn:hover {

            transform: translateY(-5px);
            background: #74a91a;

        }

        .featured-video-btn i {

            font-size: 52px;

        }

        .featured-video-btn .title {

            display: block;
            font-size: 20px;
            font-weight: 700;

        }

        .featured-video-btn small {

            display: block;
            font-size: 14px;
            color: #e8e8e8;

        }

        @media(max-width:768px) {

            .featured-video-btn {

                min-width: 100%;
                font-size: 18px;
                padding: 18px;

            }

            .featured-video-btn i {

                font-size: 40px;

            }

        }
    </style>

</head>

<body class="index-page">
    <?php include 'template/header.php'; ?>

    <main class="main">
        <!-- Hero Section -->
        <section id="hero" class="hero section">
            <div
                id="hero-carousel"
                class="carousel slide carousel-fade"
                data-bs-ride="carousel"
                data-bs-interval="5000">
                <div class="carousel-item">
                    <!-- Background Image -->
                    <img src="assets/img/hero-carousel/hero-carousel-3.jpg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                Welcome to
                                <span style="color: #608C11;">NACPDEAN</span>
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <p class="sub-text">
                                Promoting Sustainable Charcoal Production,
                                Environmental Responsibility, Trade Compliance,
                                and Economic Development.
                            </p>
                            <div class="hero-buttons">
                                <a href="#about" class="btn-main">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <!-- Background Image -->
                    <img src="assets/img/hero-carousel/hero-carousel-2.jpg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                Welcome Message
                                <span style="color: #608C11;">From President</span>
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <p class="sub-text">
                                Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official website of the
                            </p>
                            <div class="hero-buttons">
                                <a
                                    href="#"
                                    class="btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#welcomeModal">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <!-- Background Image -->
                    <img src="assets/img/gallery/nacpdeangal1.jpeg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                NACPDEAN
                                <!-- <span style="color: #608C11;">Charcoal</span> -->
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <!-- <p class="sub-text">
                                    Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official website of the
                                </p> -->
                            <div class="hero-buttons">
                                <a
                                    href="#"
                                    class="btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#welcomeModal">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <!-- Background Image -->
                    <img src="assets/img/gallery/nacpdeangal10.jpg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                NACPDEAN
                                <!-- <span style="color: #608C11;">Charcoal</span> -->
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <!-- <p class="sub-text">
                                    Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official website of the
                                </p> -->
                            <div class="hero-buttons">
                                <a
                                    href="#"
                                    class="btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#welcomeModal">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item active">
                    <!-- Background Image -->
                    <img src="assets/img/gallery/nacpdeangal11.jpeg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                Welcome Message
                                <span style="color: #608C11;">From President</span>
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <p class="sub-text">
                                Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official website of the
                            </p>
                            <div class="hero-buttons">
                                <a
                                    href="#"
                                    class="btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#welcomeModal">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <!-- Background Image -->
                    <img src="assets/img/gallery/nacpdeangal12.jpeg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                NACPDEAN
                                <!-- <span style="color: #608C11;">Charcoal</span> -->
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <!-- <p class="sub-text">
                                    Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official website of the
                                </p> -->
                            <div class="hero-buttons">
                                <a
                                    href="#"
                                    class="btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#welcomeModal">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <!-- Background Image -->
                    <img src="assets/img/hero-carousel/hero-carousel-122.jpg" alt="">
                    <!-- Dark Overlay -->
                    <div class="hero-overlay"></div>
                    <!-- Text Content -->
                    <div class="container hero-content">
                        <div class="hero-text">
                            <span class="hero-badge">
                                Official Website
                            </span>
                            <h1 class="text-white">
                                NACPDEAN
                                <!-- <span style="color: #608C11;">Charcoal</span> -->
                            </h1>
                            <p>
                                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                                DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                            </p>
                            <!-- <p class="sub-text">
                                    Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official website of the
                                </p> -->
                            <div class="hero-buttons">
                                <a
                                    href="#"
                                    class="btn-main"
                                    data-bs-toggle="modal"
                                    data-bs-target="#welcomeModal">
                                    Read More
                                </a>
                                <a href="#contact" class="btn-outline">
                                    Contact Us
                                </a>
                            </div>
                            <div class="featured-video-wrapper mt-4">
                                <button
                                    class="featured-video-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#featuredVideoModal">

                                    <i class="bi bi-play-circle-fill"></i>

                                    <div>
                                        <span class="title">WATCH OUR FEATURED VIDEO</span>
                                        <small>Learn more about NACPDEAN</small>
                                    </div>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <a
                    class="carousel-control-prev"
                    href="#hero-carousel"
                    role="button"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
                </a>
                <a
                    class="carousel-control-next"
                    href="#hero-carousel"
                    role="button"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
                </a>
                <ol class="carousel-indicators"></ol>
            </div>
        </section>
        <!-- /Hero Section -->
        <!-- Featured Services Section -->
        <section id="featured-services" class="featured-services section">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item position-relative">
                            <div class="text-center mb-3">
                                <img
                                    src="assets/img/services/bamboocharcoal.png"
                                    alt="Bamboo Charcoal"
                                    width="140px"
                                    height="100px">
                            </div>
                            <h4>
                                <a href="" class="stretched-link">Bamboo Charcoal</a>
                            </h4>
                            <p>Bamboo charcoal is a highly porous, eco-friendly carbon material made from mature bamboo plants.</p>
                        </div>
                    </div>
                    <!-- End Service Item -->
                    <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item position-relative">
                            <div class="text-center mb-3">
                                <img
                                    src="assets/img/services/hardwoodcharcoal.png"
                                    alt="Hardwood Charcoal"
                                    width="140px"
                                    height="100px">
                            </div>
                            <h4>
                                <a href="" class="stretched-link">Hardwood Charcoal</a>
                            </h4>
                            <p>Hardwood charcoal is a type of charcoal made from hardwood trees through a process of pyrolysis.</p>
                        </div>
                    </div>
                    <!-- End Service Item -->
                    <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item position-relative">
                            <div class="text-center mb-3">
                                <img
                                    src="assets/img/services/briquettecharcoal.png"
                                    alt="Briquette Charcoal"
                                    width="140px"
                                    height="100px">
                            </div>
                            <h4>
                                <a href="" class="stretched-link">Briquette Charcoal</a>
                            </h4>
                            <p>
                                Briquette charcoal is a compressed form of charcoal made by binding charcoal dust or fines with a
                                natural binder.
                            </p>
                        </div>
                    </div>
                    <!-- End Service Item -->
                    <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-item position-relative">
                            <div class="text-center mb-3">
                                <img
                                    src="assets/img/services/coconutshellcharcoal.png"
                                    alt="Coconut Shell Charcoal"
                                    width="140px"
                                    height="100px">
                            </div>
                            <h4>
                                <a href="" class="stretched-link">Coconut Shell Charcoal</a>
                            </h4>
                            <p>
                                Coconut shell charcoal is a type of charcoal made from the shells of coconuts, offering a sustainable
                                and efficient fuel.
                            </p>
                        </div>
                    </div>
                    <!-- End Service Item -->
                </div>
            </div>
        </section>
        <!-- /Featured Services Section -->
        <!-- Call To Action Section -->
        <section id="call-to-action" class="call-to-action section accent-background">
            <div class="container">
                <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="col-xl-10">
                        <div class="text-center">
                            <h3>Need help now? Contact Us</h3>
                            <p>You can contact us now</p>
                            <a class="cta-btn" href="#contact">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Call To Action Section -->
        <!-- About Section -->
        <section id="about" class="about section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>About Us
                    <br>
                </h2>
                <p>National Association of Charcoal Producers, Dealers, Exporters and Afforestation of Nigeria (NACPDEAN)</p>
            </div>
            <!-- End Section Title -->
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
                        <img src="assets/img/gallery/nacpdeangal11.jpeg" class="img-fluid" alt="">
                    </div>
                    <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
                        <h3>
                            National Association of Charcoal Producers, Dealers, Exporters and Afforestation of Nigeria (NACPDEAN)
                        </h3>
                        <p>
                            The National Association of Charcoal Producers, Dealers, Exporters and Afforestation of Nigeria (NACPDEAN)
                            is the recognized national umbrella body established to coordinate, regulate, organize, promote and
                            represent stakeholders operating across the charcoal value chain in Nigeria.
                        </p>
                        <p>
                            NACPDEAN was established to provide a unified institutional platform for charcoal producers, dealers,
                            exporters, processors, marketers, investors, transporters, warehouse operators, afforestation
                            practitioners and other stakeholders within the sector. The Association serves as a bridge between
                            industry operators, government institutions, development partners, investors and the international market,
                            ensuring that the charcoal industry contributes meaningfully to economic growth, environmental
                            sustainability, employment generation and foreign exchange earnings.
                        </p>
                        <p>
                            <button class="btn btn-success px-4 mt-3" data-bs-toggle="modal" data-bs-target="#aboutModal">
                                Read More
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- /About Section -->
        <!-- Stats Section -->
        <section id="stats" class="stats section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="fas fa-users flex-shrink-0"></i>
                            <div>
                                <span
                                    data-purecounter-start="0"
                                    data-purecounter-end="+37000"
                                    data-purecounter-duration="1"
                                    class="purecounter"></span>
                                </span>
                                <p>Members And Institutional Partners</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Stats Item -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="fas fa-users flex-shrink-0"></i>
                            <div>
                                <span
                                    data-purecounter-start="0"
                                    data-purecounter-end="36"
                                    data-purecounter-duration="1"
                                    class="purecounter"></span>
                                <p>National Executive Council</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Stats Item -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="fas fa-users flex-shrink-0"></i>
                            <div>
                                <span
                                    data-purecounter-start="0"
                                    data-purecounter-end="888"
                                    data-purecounter-duration="1"
                                    class="purecounter"></span>
                                <p>State Executives</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Stats Item -->
                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item d-flex align-items-center w-100 h-100">
                            <i class="fas fa-award flex-shrink-0"></i>
                            <div>
                                <span
                                    data-purecounter-start="0"
                                    data-purecounter-end="150"
                                    data-purecounter-duration="1"
                                    class="purecounter"></span>
                                <p>Awards</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Stats Item -->
                </div>
            </div>
        </section>
        <!-- /Stats Section -->
        <!-- Features Section -->
        <!-- Services Section -->
        <!-- /Services Section -->
        <!-- Appointment Section -->
        <!-- Doctors Section -->
        <!-- /Doctors Section -->
        <!-- Gallery Section -->
        <section id="gallery" class="gallery section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Our Partners</h2>
            </div>
            <!-- End Section Title -->
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
                        {
                            "loop": true,
                            "speed": 600,
                            "autoplay": {
                                "delay": 5000
                            },
                            "slidesPerView": "auto",
                            "centeredSlides": true,
                            "pagination": {
                                "el": ".swiper-pagination",
                                "type": "bullets",
                                "clickable": true
                            },
                            "breakpoints": {
                                "320": {
                                    "slidesPerView": 1,
                                    "spaceBetween": 0
                                },
                                "768": {
                                    "slidesPerView": 3,
                                    "spaceBetween": 20
                                },
                                "1200": {
                                    "slidesPerView": 5,
                                    "spaceBetween": 20
                                }
                            }
                        }
                    </script>
                    <div class="swiper-wrapper align-items-center">
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery1.png">
                                <img src="assets/img/gallery/gallery1.png" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery2.png">
                                <img src="assets/img/gallery/gallery2.png" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery3.jpeg">
                                <img src="assets/img/gallery/gallery3.jpeg" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery4.jpg">
                                <img src="assets/img/gallery/gallery4.jpg" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery5.jpg">
                                <img src="assets/img/gallery/gallery5.jpg" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery6.jpg">
                                <img src="assets/img/gallery/gallery6.jpg" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery7.jpeg">
                                <img src="assets/img/gallery/gallery7.jpeg" class="img-fluid" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery8.jpeg">
                                <img src="assets/img/gallery/gallery8.jpeg" class="img-fluid" alt="">
                            </a>
                        </div>
                                                <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery10.png">
                                <img src="assets/img/gallery/gallery10.png" class="img-fluid" alt="">
                            </a>
                        </div>
                                                <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="assets/img/gallery/gallery11.png">
                                <img src="assets/img/gallery/gallery11.png" class="img-fluid" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>

        <!-- /Blacklisted Section -->
        <!-- Blacklisted Notice Modal -->
        <div class="modal fade" id="blacklistNotice" tabindex="-1">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-danger">

                    <div class="modal-header bg-danger text-white">

                        <h4 class="modal-title text-white">
                            <i class="fas fa-triangle-exclamation me-2"></i>
                            Important Notice
                        </h4>

                        <button class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body text-center p-4">

                        <i class="fas fa-ban text-danger"
                            style="font-size:70px;"></i>

                        <h3 class="mt-3 mb-3">
                            Blacklisted Stakeholders Alert
                        </h3>

                        <p class="lead">

                            The Association has published a list of
                            <strong>blacklisted stakeholders</strong>.

                            Members and the general public are advised to
                            exercise caution before engaging in any business
                            transactions with the individuals or companies listed.

                        </p>

                        <div class="alert alert-warning">

                            <strong>Current Blacklisted Stakeholders</strong>

                            <marquee behavior="scroll" direction="left">

                                John Doe • XYZ Charcoal Ltd • ABC Export Company •
                                Jane Smith • Global Agro Export Ltd

                            </marquee>

                        </div>

                        <a href="blacklisted-members.php"
                            class="btn btn-danger btn-lg">

                            View Blacklisted Directory

                        </a>

                    </div>

                </div>

            </div>

        </div>



        <!-- /Gallery Section -->
        <!-- /Pricing Section -->
        <!-- Faq Section -->
        <!-- /Faq Section -->
        <!-- Contact Section -->
        <section id="contact" class="contact section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Contact</h2>
                <p>You can contact us at any time</p>
            </div>
            <!-- End Section Title -->
            <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
                <!-- <iframe
                    style="border:0; width: 100%; height: 370px;"
                    src="https://www.google.com/maps/embed?pb=!1m26!1m12!1m3!1d4044911.7464288296!2d2.7697597229379363!3d8.090352740841778!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m11!3e6!4m3!3m2!1d6.5994752!2d3.3488895999999997!4m5!1s0x104e0b7d5a2bb2bb%3A0x1c6f74e4d273b0c4!2sOld%20Federal%20Secretariat%2C%202FGC%2B6Q8%2C%20Aliyu%20Dikko%20St%2C%20Durumi%2C%20Abuja%20900103%2C%20Federal%20Capital%20Territory!3m2!1d9.025538899999999!2d7.471944499999999!5e0!3m2!1sen!2sng!4v1782737876420!5m2!1sen!2sng"
                    frameborder="0"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                <iframe
                    style="border:0; width: 100%; height: 500px;"
                    src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d3940.4281833793552!2d7.470220975018849!3d9.024646891036511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sOld%20Federal%20Secretariat%20Complex%2C%20Area%201%2C%20Garki%2C%20Abuja%2C%20Federal%20Capital%20Territory%2C%20Nigeria!5e0!3m2!1sen!2sng!4v1783522063658!5m2!1sen!2sng" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
            <!-- End Google Maps -->
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                    <div class="col-lg-6 ">
                        <div class="row gy-4">
                            <div class="col-lg-12">
                                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
                                    <i class="bi bi-geo-alt"></i>
                                    <h3>Address</h3>
                                    <h6>National Headquarters – Abuja</h6>
                                    <p class="p-4">
                                        Block D complex, Rooms 309–311,
                                        Federal Ministry of Industry, Trade and Investment Old Federal Secretariat Complex, Area 1, Garki, Abuja,
                                        Federal Capital Territory (FCT),
                                        Nigeria
                                    </p>
                                    <h6>Lagos Liaison Office – Lagos</h6>
                                    <p class="p-4">
                                        First Floor, NASCO Building,
                                        29 Burma Road
                                        Apapa
                                        Lagos
                                    </p>
                                </div>
                            </div>
                            <!-- End Info Item -->
                            <div class="col-md-6">
                                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
                                    <i class="bi bi-telephone"></i>
                                    <h3>Call Us</h3>
                                    <p>+234 81 456 723 58</p>
                                    <p>+234 80 366 703 60</p>
                                </div>
                            </div>
                            <!-- End Info Item -->
                            <div class="col-md-6">
                                <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
                                    <i class="bi bi-envelope"></i>
                                    <h3>Email Us</h3>
                                    <p>info@nacpdean.org</p>
                                </div>
                            </div>
                            <!-- End Info Item -->
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form
                            action="forms/contact.php"
                            method="post"
                            class="php-email-form"
                            data-aos="fade-up"
                            data-aos-delay="500">
                            <div class="row gy-4">
                                <h5 class="text-center">Send Us a Message</h5>
                                <div class="col-md-6">
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        placeholder="Your Name"
                                        required="">
                                </div>
                                <div class="col-md-6 ">
                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        placeholder="Your Email"
                                        required="">
                                </div>
                                <div class="col-md-12">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="subject"
                                        placeholder="Subject"
                                        required="">
                                </div>
                                <div class="col-md-12">
                                    <textarea
                                        class="form-control"
                                        name="message"
                                        rows="4"
                                        placeholder="Message"
                                        required=""></textarea>
                                </div>
                                <div class="col-md-12 text-center">
                                    <div class="loading">Loading</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">Your message has been sent. Thank you!</div>
                                    <button type="submit">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- End Contact Form -->
                </div>
            </div>
        </section>
        <!-- /Contact Section -->

        <div class="whatsapp-widget">

            <div class="whatsapp-text">
                👋 Hi there!<br>
                Need help? Chat with us.
            </div>

            <a href="https://wa.me/2348145672358?text=Hello%20I%20would%20like%20to%20know%20more."
                target="_blank">
                <i class="fab fa-whatsapp"></i>
            </a>

        </div>

    </main>

    <?php include 'template/footer.php'; ?>
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
    <!-- Preloader -->
    <div id="preloader"></div>
    <!-- Welcome Message Modal -->
    <div
        class="modal fade"
        id="welcomeModal"
        tabindex="-1"
        aria-labelledby="welcomeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #608C11; color: #fff;">
                    <h5 class="modal-title text-white" id="welcomeModalLabel">
                        Welcome Message From The National President
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Dear Esteemed Charcoal Stakeholders,</p>
                    <p>
                        It is my pleasure to welcome you to the official website of the
                        <strong>
                            National Association of Charcoal Producers, Dealers,
                            Exporters and Afforestation of Nigeria (NACPDEAN).
                        </strong>
                    </p>
                    <p>
                        NACPDEAN was established to provide leadership, coordination,
                        and representation for stakeholders within Nigeria's charcoal
                        industry while ensuring that commercial activities are conducted
                        in accordance with national regulations, industry self-regulations,
                        environmental standards, and international best practices.
                    </p>
                    <p>
                        As an Association, we recognize the significant role the charcoal
                        industry plays in job creation, foreign exchange generation,
                        rural development, and economic empowerment. We equally recognize
                        our collective responsibility to protect and restore the
                        environment through sustainable production methods and aggressive
                        afforestation initiatives.
                    </p>
                    <p>
                        Our commitment is to build a transparent, compliant, and globally
                        competitive charcoal industry that balances economic prosperity
                        with environmental sustainability. Through our Digital Compliance
                        Infrastructure, Licensing Framework, Afforestation Programs, and
                        Stakeholder Engagement Initiatives, we are laying the foundation
                        for a modern industry capable of meeting both national and
                        international expectations.
                    </p>
                    <p>
                        I invite you to explore our platform, participate in our
                        programs, and join us in building a sustainable future for
                        generations to come.
                    </p>
                    <p>
                        Thank you for your support and partnership.
                    </p>
                    <hr>
                    <h5 class="mb-0">Edu Babatunde</h5>
                    <p class="mb-0">
                        <strong>National President</strong>
                    </p>
                    <p>
                        National Association of Charcoal Producers, Dealers,
                        Exporters and Afforestation of Nigeria (NACPDEAN)
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="featuredVideoModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Featured Video
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body p-0">

                    <div class="ratio ratio-16x9">

                        <iframe
                            src="https://www.youtube.com/embed/jdXBzUTXpS4"
                            title="Featured Video"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen>
                        </iframe>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="aboutModal"
        tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-white">
                        About NACPDEAN
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="text-success mb-3">
                        Brief Introduction of NACPDEAN
                    </h5>
                    <p>
                        The National Association of Charcoal Producers,
                        Dealers, Exporters and Afforestation of Nigeria
                        (NACPDEAN) is the recognized national umbrella body
                        established to coordinate, regulate, organize,
                        promote and represent stakeholders operating across
                        the charcoal value chain in Nigeria.
                    </p>
                    <p>
                        NACPDEAN was established to provide a unified
                        institutional platform for charcoal producers,
                        dealers, exporters, processors, marketers,
                        investors, transporters, warehouse operators,
                        afforestation practitioners and other stakeholders
                        within the sector.
                    </p>
                    <p>
                        The Association serves as a bridge between industry
                        operators, government institutions, development
                        partners, investors and the international market,
                        ensuring that the charcoal industry contributes
                        meaningfully to economic growth, environmental
                        sustainability, employment generation and foreign
                        exchange earnings.
                    </p>
                    <p>
                        The Association was formally incorporated with the
                        Corporate Affairs Commission (CAC) of Nigeria on the
                        6th day of June 2022 with Registration Number
                        <strong>IT182068</strong>
                        .
                    </p>
                    <p>
                        Today, NACPDEAN stands as the foremost national body
                        championing responsible charcoal production,
                        sustainable forest management, environmental
                        restoration, afforestation, export promotion and
                        industry regulation throughout Nigeria.
                    </p>
                    <p>
                        NACPDEAN was established as a strategic response to
                        the challenges facing Nigeria's charcoal industry and
                        to provide a sustainable pathway for industry growth,
                        environmental protection and economic development.
                    </p>
                    <p>
                        By bringing producers, dealers, exporters,
                        processors, marketers and all stakeholders under one
                        umbrella, NACPDEAN has created a platform for
                        accountability, regulation, environmental
                        responsibility and industry advancement.
                    </p>
                    <p>
                        The Association remains committed to supporting the
                        Federal Government's objectives on environmental
                        sustainability, economic diversification, non-oil
                        export promotion, afforestation and foreign exchange
                        generation while ensuring that Nigeria's charcoal
                        industry develops responsibly, transparently and
                        sustainably for present and future generations.
                    </p>
                    <hr>
                    <h5 class="text-success">
                        Vision Statement
                    </h5>
                    <p>
                        To become Africa's leading platform for sustainable
                        charcoal production, environmental conservation,
                        afforestation and internationally compliant charcoal
                        trade practices.
                    </p>
                    <hr>
                    <h5 class="text-success">
                        Mission Statement
                    </h5>
                    <p>
                        To organize, regulate, represent and empower
                        stakeholders within the charcoal value chain through
                        sustainable resource management, afforestation
                        initiatives, environmental responsibility, industry
                        compliance, trade facilitation and strategic
                        partnerships that contribute to national
                        development.
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            if (!sessionStorage.getItem("blacklistNoticeShown")) {

                let modal = new bootstrap.Modal(
                    document.getElementById("blacklistNotice")
                );

                modal.show();

                sessionStorage.setItem("blacklistNoticeShown", "yes");

            }

        });
    </script>

</body>

</html>