<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Home - NACPDEAN</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- Favicons -->
    <link href="{{ asset('frontend/assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('frontend/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <!-- Main CSS File -->
    <link href="{{ asset('frontend/assets/css/main.css') }}" rel="stylesheet">
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
    @include('frontend.body.header')

    @yield('home')


    @include('frontend.body.footer')
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
    <!-- Preloader -->
    <div id="preloader"></div>
    <!-- Welcome Message Modal -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #608C11; color: #fff;">
                    <h5 class="modal-title text-white" id="welcomeModalLabel">
                        Welcome Message From The National President
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
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

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body p-0">

                    <div class="ratio ratio-16x9">

                        <iframe src="https://www.youtube.com/embed/jdXBzUTXpS4" title="Featured Video" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                        </iframe>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
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
    <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <!-- Main JS File -->
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

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
