@extends('frontend.master')

@section('home')


<main class="main">
    <!-- Hero Section -->
    <section id="hero" class="hero section">
        <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
            data-bs-interval="5000">

            <div class="carousel-item">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/hero-carousel/hero-carousel-3.jpg') }}" alt="">

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
                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>
                        </div>

                    </div>
                </div>
            </div>


            <div class="carousel-item">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/hero-carousel/hero-carousel-2.jpg') }}" alt="">

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
                            Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official
                            website of the
                        </p>

                        <div class="hero-buttons">

                            <a href="#"
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

                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>

                        </div>

                    </div>
                </div>
            </div>


            <div class="carousel-item">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/gallery/nacpdeangal1.jpeg') }}" alt="">

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
                        </h1>

                        <p>
                            NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                            DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                        </p>

                        <div class="hero-buttons">

                            <a href="#"
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

                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>

                        </div>

                    </div>
                </div>
            </div>


            <div class="carousel-item">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/gallery/nacpdeangal10.jpg') }}" alt="">

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
                        </h1>

                        <p>
                            NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                            DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                        </p>

                        <div class="hero-buttons">

                            <a href="#"
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

                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>

                        </div>

                    </div>
                </div>
            </div>


            <div class="carousel-item active">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/gallery/nacpdeangal11.jpeg') }}" alt="">

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
                            Dear Esteemed Charcoal Stakeholders, It is my pleasure to welcome you to the official
                            website of the
                        </p>

                        <div class="hero-buttons">

                            <a href="#"
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

                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>

                        </div>

                    </div>
                </div>
            </div>


            <div class="carousel-item">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/gallery/nacpdeangal12.jpeg') }}" alt="">

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
                        </h1>

                        <p>
                            NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                            DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                        </p>

                        <div class="hero-buttons">

                            <a href="#"
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

                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>

                        </div>

                    </div>
                </div>
            </div>


            <div class="carousel-item">
                <!-- Background Image -->
                <img src="{{ asset('frontend/assets/img/hero-carousel/hero-carousel-122.jpg') }}" alt="">

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
                        </h1>

                        <p>
                            NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS,
                            DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA.
                        </p>

                        <div class="hero-buttons">

                            <a href="#"
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

                            <button class="featured-video-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#featuredVideoModal">

                                <i class="bi bi-play-circle-fill"></i>

                                <div>
                                    <span class="title">
                                        WATCH OUR FEATURED VIDEO
                                    </span>

                                    <small>
                                        Learn more about NACPDEAN
                                    </small>
                                </div>

                            </button>

                        </div>

                    </div>
                </div>
            </div>


            <a class="carousel-control-prev"
                href="#hero-carousel"
                role="button"
                data-bs-slide="prev">

                <span class="carousel-control-prev-icon bi bi-chevron-left"
                    aria-hidden="true">
                </span>

            </a>

            <a class="carousel-control-next"
                href="#hero-carousel"
                role="button"
                data-bs-slide="next">

                <span class="carousel-control-next-icon bi bi-chevron-right"
                    aria-hidden="true">
                </span>

            </a>

            <ol class="carousel-indicators"></ol>

        </div>
    </section>
    <!-- /Hero Section -->


    <!-- Featured Services Section -->
    <section id="featured-services" class="featured-services section">

        <div class="container">

            <div class="row gy-4">

                <div class="col-xl-3 col-md-6 d-flex"
                    data-aos="fade-up"
                    data-aos-delay="100">

                    <div class="service-item position-relative">

                        <div class="text-center mb-3">

                            <img src="{{ asset('frontend/assets/img/services/bamboocharcoal.png') }}"
                                alt="Bamboo Charcoal"
                                width="140"
                                height="100">

                        </div>

                        <h4>
                            <a href="" class="stretched-link">
                                Bamboo Charcoal
                            </a>
                        </h4>

                        <p>
                            Bamboo charcoal is a highly porous, eco-friendly carbon material made from mature bamboo
                            plants.
                        </p>

                    </div>

                </div>


                <div class="col-xl-3 col-md-6 d-flex"
                    data-aos="fade-up"
                    data-aos-delay="200">

                    <div class="service-item position-relative">

                        <div class="text-center mb-3">

                            <img src="{{ asset('frontend/assets/img/services/hardwoodcharcoal.png') }}"
                                alt="Hardwood Charcoal"
                                width="140"
                                height="100">

                        </div>

                        <h4>
                            <a href="" class="stretched-link">
                                Hardwood Charcoal
                            </a>
                        </h4>

                        <p>
                            Hardwood charcoal is a type of charcoal made from hardwood trees through a process of
                            pyrolysis.
                        </p>

                    </div>

                </div>


                <div class="col-xl-3 col-md-6 d-flex"
                    data-aos="fade-up"
                    data-aos-delay="300">

                    <div class="service-item position-relative">

                        <div class="text-center mb-3">

                            <img src="{{ asset('frontend/assets/img/services/briquettecharcoal.png') }}"
                                alt="Briquette Charcoal"
                                width="140"
                                height="100">

                        </div>

                        <h4>
                            <a href="" class="stretched-link">
                                Briquette Charcoal
                            </a>
                        </h4>

                        <p>
                            Briquette charcoal is a compressed form of charcoal made by binding charcoal dust or
                            fines with a natural binder.
                        </p>

                    </div>

                </div>


                <div class="col-xl-3 col-md-6 d-flex"
                    data-aos="fade-up"
                    data-aos-delay="400">

                    <div class="service-item position-relative">

                        <div class="text-center mb-3">

                            <img src="{{ asset('frontend/assets/img/services/coconutshellcharcoal.png') }}"
                                alt="Coconut Shell Charcoal"
                                width="140"
                                height="100">

                        </div>

                        <h4>
                            <a href="" class="stretched-link">
                                Coconut Shell Charcoal
                            </a>
                        </h4>

                        <p>
                            Coconut shell charcoal is a type of charcoal made from the shells of coconuts, offering
                            a sustainable and efficient fuel.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>
    <!-- /Featured Services Section -->


    <!-- Call To Action Section -->
    <section id="call-to-action"
        class="call-to-action section accent-background">

        <div class="container">

            <div class="row justify-content-center"
                data-aos="zoom-in"
                data-aos-delay="100">

                <div class="col-xl-10">

                    <div class="text-center">

                        <h3>
                            Need help now? Contact Us
                        </h3>

                        <p>
                            You can contact us now
                        </p>

                        <a class="cta-btn" href="#contact">
                            Contact Us
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>
    <!-- /Call To Action Section -->


    <!-- About Section -->
    <section id="about" class="about section">

        <div class="container section-title"
            data-aos="fade-up">

            <h2>
                About Us
                <br>
            </h2>

            <p>
                National Association of Charcoal Producers, Dealers, Exporters and Afforestation of Nigeria
                (NACPDEAN)
            </p>

        </div>


        <div class="container">

            <div class="row gy-4">

                <div class="col-lg-6 position-relative align-self-start"
                    data-aos="fade-up"
                    data-aos-delay="100">

                    <img src="{{ asset('frontend/assets/img/gallery/nacpdeangal11.jpeg') }}"
                        class="img-fluid"
                        alt="">

                </div>


                <div class="col-lg-6 content"
                    data-aos="fade-up"
                    data-aos-delay="200">

                    <h3>
                        National Association of Charcoal Producers, Dealers, Exporters and Afforestation of Nigeria
                        (NACPDEAN)
                    </h3>

                    <p>
                        The National Association of Charcoal Producers, Dealers, Exporters and Afforestation of
                        Nigeria (NACPDEAN)
                        is the recognized national umbrella body established to coordinate, regulate, organize,
                        promote and
                        represent stakeholders operating across the charcoal value chain in Nigeria.
                    </p>

                    <p>
                        NACPDEAN was established to provide a unified institutional platform for charcoal producers,
                        dealers,
                        exporters, processors, marketers, investors, transporters, warehouse operators,
                        afforestation
                        practitioners and other stakeholders within the sector. The Association serves as a bridge
                        between
                        industry operators, government institutions, development partners, investors and the
                        international market,
                        ensuring that the charcoal industry contributes meaningfully to economic growth,
                        environmental
                        sustainability, employment generation and foreign exchange earnings.
                    </p>

                    <p>

                        <button class="btn btn-success px-4 mt-3"
                            data-bs-toggle="modal"
                            data-bs-target="#aboutModal">

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

        <div class="container"
            data-aos="fade-up"
            data-aos-delay="100">

            <div class="row gy-4">

                <div class="col-lg-3 col-md-6">

                    <div class="stats-item d-flex align-items-center w-100 h-100">

                        <i class="fas fa-users flex-shrink-0"></i>

                        <div>

                            <span data-purecounter-start="0"
                                data-purecounter-end="+37000"
                                data-purecounter-duration="1"
                                class="purecounter">
                            </span>

                            <p>
                                Members And Institutional Partners
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="stats-item d-flex align-items-center w-100 h-100">

                        <i class="fas fa-users flex-shrink-0"></i>

                        <div>

                            <span data-purecounter-start="0"
                                data-purecounter-end="36"
                                data-purecounter-duration="1"
                                class="purecounter">
                            </span>

                            <p>
                                National Executive Council
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="stats-item d-flex align-items-center w-100 h-100">

                        <i class="fas fa-users flex-shrink-0"></i>

                        <div>

                            <span data-purecounter-start="0"
                                data-purecounter-end="888"
                                data-purecounter-duration="1"
                                class="purecounter">
                            </span>

                            <p>
                                State Executives
                            </p>

                        </div>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="stats-item d-flex align-items-center w-100 h-100">

                        <i class="fas fa-award flex-shrink-0"></i>

                        <div>

                            <span data-purecounter-start="0"
                                data-purecounter-end="150"
                                data-purecounter-duration="1"
                                class="purecounter">
                            </span>

                            <p>
                                Awards
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
    <!-- /Stats Section -->


    <!-- Gallery Section -->
    <section id="gallery" class="gallery section">

        <div class="container section-title"
            data-aos="fade-up">

            <h2>
                Our Partners
            </h2>

        </div>


        <div class="container"
            data-aos="fade-up"
            data-aos-delay="100">

            <div class="swiper init-swiper">

                <script type="application/json"
                    class="swiper-config">

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
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery1.png') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery1.png') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery2.png') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery2.png') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery3.jpeg') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery3.jpeg') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery4.jpg') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery4.jpg') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery5.jpg') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery5.jpg') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery6.jpg') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery6.jpg') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery7.jpeg') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery7.jpeg') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery8.jpeg') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery8.jpeg') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery10.png') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery10.png') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>


                    <div class="swiper-slide">
                        <a class="glightbox"
                            data-gallery="images-gallery"
                            href="{{ asset('frontend/assets/img/gallery/gallery11.png') }}">

                            <img src="{{ asset('frontend/assets/img/gallery/gallery11.png') }}"
                                class="img-fluid"
                                alt="">

                        </a>
                    </div>

                </div>


                <div class="swiper-pagination"></div>

            </div>

        </div>

    </section>
    <!-- /Gallery Section -->


    {{-- =========================================================
        BLACKLISTED MEMBERS NOTICE

        IMPORTANT:
        The entire modal is rendered ONLY when there is at least
        one record with status = "blacklisted".
    ========================================================== --}}

    @if(isset($blacklistedMembers) && $blacklistedMembers->isNotEmpty())

        <!-- Blacklisted Notice Modal -->
        <div class="modal fade"
            id="blacklistNotice"
            tabindex="-1"
            aria-labelledby="blacklistNoticeLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-danger">

                    {{-- HEADER --}}
                    <div class="modal-header bg-danger text-white">

                        <h4 class="modal-title text-white"
                            id="blacklistNoticeLabel">

                            <i class="fas fa-triangle-exclamation me-2"></i>

                            Important Notice

                        </h4>

                        <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        </button>

                    </div>


                    {{-- BODY --}}
                    <div class="modal-body text-center p-4">

                        <i class="fas fa-ban text-danger"
                            style="font-size:70px;">
                        </i>


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


                        {{-- CURRENT BLACKLISTED STAKEHOLDERS --}}
                        <div class="alert alert-warning">

                            <strong>
                                Current Blacklisted Stakeholders
                            </strong>

                            <marquee behavior="scroll"
                                direction="left">

                                @foreach($blacklistedMembers as $blacklistedMember)

                                    <span class="mx-2">

                                        {{ $blacklistedMember->member_name }}

                                        @if(!empty($blacklistedMember->company_name))

                                            <span>
                                                ({{ $blacklistedMember->company_name }})
                                            </span>

                                        @endif

                                    </span>

                                    @if(!$loop->last)
                                        <span class="mx-2">
                                            •
                                        </span>
                                    @endif

                                @endforeach

                            </marquee>

                        </div>


                        {{-- DIRECTORY BUTTON --}}
                        <a href="{{ route('blacklisted.members') }}"
                            class="btn btn-danger btn-lg">

                            View Blacklisted Directory

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- AUTOMATICALLY SHOW MODAL --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const blacklistModalElement =
                    document.getElementById('blacklistNotice');

                if (blacklistModalElement) {

                    const blacklistModal =
                        new bootstrap.Modal(blacklistModalElement);

                    blacklistModal.show();

                }

            });
        </script>

    @endif


    <!-- Contact Section -->
    <section id="contact" class="contact section">

        <div class="container section-title"
            data-aos="fade-up">

            <h2>
                Contact
            </h2>

            <p>
                You can contact us at any time
            </p>

        </div>


        <div class="mb-5"
            data-aos="fade-up"
            data-aos-delay="200">

            <iframe
                style="border:0; width: 100%; height: 500px;"
                src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d3940.4281833793552!2d7.470220975018849!3d9.024646891036511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sOld%20Federal%20Secretariat%20Complex%2C%20Area%201%2C%20Garki%2C%20Abuja%2C%20Federal%20Capital%20Territory%2C%20Nigeria!5e0!3m2!1sen!2sng!4v1783522063658!5m2!1sen!2sng"
                width="600"
                height="450"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin">
            </iframe>

        </div>
        <!-- End Google Maps -->


        <div class="container"
            data-aos="fade-up"
            data-aos-delay="100">

            <div class="row gy-4">

                <div class="col-lg-6">

                    <div class="row gy-4">

                        <div class="col-lg-12">

                            <div class="info-item d-flex flex-column justify-content-center align-items-center"
                                data-aos="fade-up"
                                data-aos-delay="200">

                                <i class="bi bi-geo-alt"></i>

                                <h3>
                                    Address
                                </h3>


                                <h6>
                                    National Headquarters – Abuja
                                </h6>

                                <p class="p-4">

                                    Block D complex, Rooms 309–311,
                                    Federal Ministry of Industry, Trade and Investment Old Federal Secretariat
                                    Complex, Area 1, Garki, Abuja,
                                    Federal Capital Territory (FCT),
                                    Nigeria

                                </p>


                                <h6>
                                    Lagos Liaison Office – Lagos
                                </h6>

                                <p class="p-4">

                                    First Floor, NASCO Building,
                                    29 Burma Road
                                    Apapa
                                    Lagos

                                </p>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="info-item d-flex flex-column justify-content-center align-items-center"
                                data-aos="fade-up"
                                data-aos-delay="300">

                                <i class="bi bi-telephone"></i>

                                <h3>
                                    Call Us
                                </h3>

                                <p>
                                    +234 81 456 723 58
                                </p>

                                <p>
                                    +234 80 366 703 60
                                </p>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="info-item d-flex flex-column justify-content-center align-items-center"
                                data-aos="fade-up"
                                data-aos-delay="400">

                                <i class="bi bi-envelope"></i>

                                <h3>
                                    Email Us
                                </h3>

                                <p>
                                    info@nacpdean.org
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-lg-6">

                    <form action="forms/contact.php"
                        method="post"
                        class="php-email-form"
                        data-aos="fade-up"
                        data-aos-delay="500">

                        <div class="row gy-4">

                            <h5 class="text-center">
                                Send Us a Message
                            </h5>


                            <div class="col-md-6">

                                <input type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Your Name"
                                    required="">

                            </div>


                            <div class="col-md-6">

                                <input type="email"
                                    class="form-control"
                                    name="email"
                                    placeholder="Your Email"
                                    required="">

                            </div>


                            <div class="col-md-12">

                                <input type="text"
                                    class="form-control"
                                    name="subject"
                                    placeholder="Subject"
                                    required="">

                            </div>


                            <div class="col-md-12">

                                <textarea class="form-control"
                                    name="message"
                                    rows="4"
                                    placeholder="Message"
                                    required=""></textarea>

                            </div>


                            <div class="col-md-12 text-center">

                                <div class="loading">
                                    Loading
                                </div>

                                <div class="error-message"></div>

                                <div class="sent-message">
                                    Your message has been sent. Thank you!
                                </div>

                                <button type="submit">
                                    Send Message
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>
    <!-- /Contact Section -->


    <!-- WhatsApp Widget -->
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

@endsection
