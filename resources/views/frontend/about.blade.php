@extends('frontend.master')

@section('home')



    <style>
        .page-header {
            position: relative;
            background: url("{{ asset('frontend/assets/img/hero-carousel/hero-carousel-3.jpg') }}") center center/cover no-repeat;
            min-height: 500px;
            display: flex;
            align-items: center;
        }

        .page-header .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .65);
        }

        .page-header .container {
            z-index: 2;
        }

        .page-header h1 {
            line-height: 1.2;
        }

        .page-header p {
            max-width: 900px;
            margin: auto;
        }

        .card {
            transition: all .35s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .12) !important;
        }
    </style>


    <style>
        /* ======================================
   ORGANIZATIONAL CHART
====================================== */

        .org-chart {
            margin-top: 60px;
        }

        .org-level {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 35px;
        }

        .org-box {
            background: #fff;
            border-radius: 10px;
            padding: 18px;
            width: 240px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
            border-top: 5px solid #608c11;
            transition: .3s;
        }

        .org-box:hover {
            transform: translateY(-6px);
        }

        .org-box h5 {
            margin-bottom: 8px;
            font-weight: 700;
        }

        .org-box p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .org-line {
            width: 2px;
            height: 40px;
            background: #608c11;
            margin: 0 auto;
        }

        .committee-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .08);
            transition: .3s;
        }

        .committee-card:hover {
            transform: translateY(-5px);
        }

        .committee-card i {
            color: #608c11;
            font-size: 35px;
            margin-bottom: 15px;
        }
    </style>

    <style>
        .join-section {
            background: linear-gradient(135deg, #1d4d1d, #2f7d32, #5b8f12);
        }

        .join-section h2 {
            color: #fff;
        }

        .join-section p {
            line-height: 1.8;
        }

        .member-circle {
            width: 280px;
            height: 280px;
            border: 2px solid rgba(255, 255, 255, .2);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
            background: rgba(255, 255, 255, .05);
            backdrop-filter: blur(5px);
        }

        .join-section .btn-warning {
            border-radius: 50px;
            transition: .3s;
        }

        .join-section .btn-warning:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, .25);
        }

        .join-section .btn-outline-light {
            border-radius: 50px;
            transition: .3s;
        }

        .join-section .btn-outline-light:hover {
            transform: translateY(-4px);
        }

        @media(max-width:991px) {

            .member-circle {

                width: 220px;
                height: 220px;
                margin-top: 30px;

            }

        }
    </style>

    <main class="main">

        <!-- Page Hero -->
        <section class="page-header position-relative">

            <!-- Background Overlay -->
            <div class="overlay"></div>

            <div class="container position-relative">

                <div class="row justify-content-center">

                    <div class="col-lg-10 text-center text-white">

                        <span class="badge bg-success px-3 py-2 mb-3">
                            National Association
                        </span>

                        <h1 class="display-3 fw-bold mb-4 text-white">
                            About NACPDEAN
                        </h1>

                        <p class="lead fs-4 mb-4">
                            The National Association of Charcoal Producers, Dealers,
                            Exporters and Afforestation of Nigeria (NACPDEAN) is the
                            recognized national umbrella body dedicated to coordinating,
                            regulating and promoting stakeholders across Nigeria's
                            charcoal value chain while championing sustainable forestry,
                            afforestation and responsible export development.
                        </p>

                        <div class="d-flex justify-content-center flex-wrap gap-3 mt-4">

                            <div class="px-4 py-2 bg-white text-dark rounded-pill shadow-sm">
                                🌳 Sustainable Forestry
                            </div>

                            <div class="px-4 py-2 bg-white text-dark rounded-pill shadow-sm">
                                🌍 Environmental Stewardship
                            </div>

                            <div class="px-4 py-2 bg-white text-dark rounded-pill shadow-sm">
                                📦 Export Promotion
                            </div>

                            <div class="px-4 py-2 bg-white text-dark rounded-pill shadow-sm">
                                🤝 National Industry Coordination
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- =========================
         About NACPDEAN
    ========================== -->
        <section class="py-5">
            <div class="container">

                <div class="row align-items-center g-5">

                    <!-- Image -->
                    <div class="col-lg-5">
                        <img src=" {{ asset('frontend/assets/img/gallery/nacpdeangal1.jpeg') }}" alt="About NACPDEAN"
                            class="img-fluid rounded shadow-lg">
                    </div>

                    <!-- Content -->
                    <div class="col-lg-7">

                        <span class="text-success fw-bold text-uppercase">
                            Who We Are
                        </span>

                        <h2 class="section-title mb-4">
                            About NACPDEAN
                        </h2>

                        <p class="lead">
                            The National Association of Charcoal Producers, Dealers,
                            Exporters and Afforestation of Nigeria (NACPDEAN) is the
                            recognized national umbrella body established to coordinate,
                            regulate, organize, promote and represent stakeholders
                            operating across the charcoal value chain in Nigeria.
                        </p>

                        <p>
                            NACPDEAN was established to provide a unified institutional
                            platform for charcoal producers, dealers, exporters,
                            processors, marketers, investors, transporters, warehouse
                            operators, afforestation practitioners and other
                            stakeholders within the sector.
                        </p>

                        <p>
                            The Association serves as a bridge between industry
                            operators, government institutions, development partners,
                            investors and the international market, ensuring that the
                            charcoal industry contributes meaningfully to economic
                            growth, environmental sustainability, employment generation
                            and foreign exchange earnings.
                        </p>

                        <div class="alert alert-success mt-4">

                            <strong>Legal Recognition</strong>

                            <p class="mb-0 mt-2">
                                The Association was formally incorporated with the
                                Corporate Affairs Commission (CAC) of Nigeria on
                                <strong>6 June 2022</strong> under Registration Number
                                <strong>IT182068</strong>.
                            </p>

                        </div>

                        <p class="mt-4">
                            Today, NACPDEAN stands as the foremost national body
                            championing responsible charcoal production, sustainable
                            forest management, environmental restoration,
                            afforestation, export promotion and industry regulation
                            throughout Nigeria.
                        </p>

                    </div>

                </div>

            </div>
        </section>

        <!-- ==========================================
         VISION & MISSION
    =========================================== -->
        <section class="py-5 bg-light">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Our Direction
                    </span>

                    <h2 class="section-title">
                        Vision & Mission
                    </h2>

                    <p class="text-muted">
                        Guided by a clear vision and driven by a strong mission,
                        NACPDEAN is committed to building a sustainable, globally
                        competitive and environmentally responsible charcoal industry.
                    </p>

                </div>

                <div class="row g-4">

                    <!-- Vision -->

                    <div class="col-lg-5">

                        <div class="card border-0 shadow h-100">

                            <div class="card-body p-5 text-center">

                                <div class="mb-4">

                                    <i class="fa-solid fa-eye fa-4x text-success"></i>

                                </div>

                                <h2 class="mb-4">
                                    Our Vision
                                </h2>

                                <p class="lead mb-0">
                                    To be the leading national institution promoting
                                    sustainable charcoal production, afforestation,
                                    environmental stewardship, export excellence and
                                    economic prosperity in Nigeria and Africa.
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Mission -->

                    <div class="col-lg-7">

                        <div class="card border-0 shadow h-100">

                            <div class="card-body p-5">

                                <div class="text-center mb-4">

                                    <i class="fa-solid fa-bullseye fa-4x text-success mb-3"></i>

                                    <h2>
                                        Our Mission
                                    </h2>

                                </div>

                                <ul class="list-unstyled">

                                    <li class="mb-3">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Promote sustainable and environmentally responsible charcoal production practices.
                                    </li>

                                    <li class="mb-3">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Establish an efficient compliance and licensing framework for industry stakeholders.
                                    </li>

                                    <li class="mb-3">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Protect and restore forest resources through nationwide afforestation initiatives.
                                    </li>

                                    <li class="mb-3">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Facilitate responsible domestic and international charcoal trade.
                                    </li>

                                    <li class="mb-3">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Enhance stakeholder capacity through education, advocacy and strategic partnerships.
                                    </li>

                                    <li class="mb-3">
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Support government efforts in environmental protection, economic development and
                                        resource management.
                                    </li>

                                    <li>
                                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                                        Leverage technology for transparency, accountability and operational efficiency
                                        within the industry.
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- ==========================================
         CORE VALUES
    =========================================== -->
        <section class="py-5">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        What We Stand For
                    </span>

                    <h2 class="section-title">
                        Core Values
                    </h2>

                    <p class="text-muted">
                        Our values guide every decision, partnership and initiative,
                        ensuring that we operate with integrity while promoting
                        sustainable development across Nigeria's charcoal industry.
                    </p>

                </div>

                <div class="row g-4">

                    <!-- Integrity -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card border-0 shadow text-center h-100">

                            <div class="card-body p-5">

                                <i class="fa-solid fa-shield-halved fa-4x text-success mb-4"></i>

                                <h4>
                                    Integrity
                                </h4>

                                <p class="mb-0">
                                    We uphold honesty, professionalism,
                                    accountability and ethical conduct
                                    in all our activities.
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Sustainability -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card border-0 shadow text-center h-100">

                            <div class="card-body p-5">

                                <i class="fa-solid fa-seedling fa-4x text-success mb-4"></i>

                                <h4>
                                    Sustainability
                                </h4>

                                <p class="mb-0">
                                    We promote responsible charcoal production,
                                    afforestation and long-term environmental
                                    conservation.
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Compliance -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card border-0 shadow text-center h-100">

                            <div class="card-body p-5">

                                <i class="fa-solid fa-scale-balanced fa-4x text-success mb-4"></i>

                                <h4>
                                    Compliance
                                </h4>

                                <p class="mb-0">
                                    We encourage adherence to applicable laws,
                                    regulations and industry standards.
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Transparency -->

                    <div class="col-lg-6">

                        <div class="card border-0 shadow text-center h-100">

                            <div class="card-body p-5">

                                <i class="fa-solid fa-handshake fa-4x text-success mb-4"></i>

                                <h4>
                                    Transparency
                                </h4>

                                <p class="mb-0">
                                    We foster openness, trust and accountability
                                    in our governance, partnerships and
                                    stakeholder engagement.
                                </p>

                            </div>

                        </div>

                    </div>

                    <!-- Environmental Responsibility -->

                    <div class="col-lg-6">

                        <div class="card border-0 shadow text-center h-100">

                            <div class="card-body p-5">

                                <i class="fa-solid fa-tree fa-4x text-success mb-4"></i>

                                <h4>
                                    Environmental Responsibility
                                </h4>

                                <p class="mb-0">
                                    We are committed to protecting forest
                                    resources, restoring ecosystems and
                                    safeguarding the environment for future
                                    generations.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- ==========================================
         AFFORESTATION & ENVIRONMENTAL SUSTAINABILITY
    ========================================== -->
        <section class="py-5 bg-light">

            <div class="container">

                <!-- Section Heading -->
                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Sustainable Forestry
                    </span>

                    <h2 class="section-title">
                        Afforestation & Environmental Sustainability
                    </h2>

                    <p class="text-muted mx-auto" style="max-width:900px;">
                        NACPDEAN is committed to promoting responsible charcoal production
                        through sustainable forest management, environmental conservation
                        and nationwide afforestation initiatives.
                    </p>

                </div>

                <!-- Introduction -->
                <div class="row align-items-center g-5">

                    <div class="col-lg-6">

                        <img src="{{ asset('frontend/assets/img/hero-carousel/hero-carousel-3.jpg') }}" class="img-fluid rounded shadow"
                            alt="Afforestation">

                    </div>

                    <div class="col-lg-6">

                        <h3 class="mb-4">
                            Protecting Nigeria's Forest Resources
                        </h3>

                        <p>
                            One of the fundamental reasons for the establishment of
                            NACPDEAN is the urgent need to address deforestation and
                            promote afforestation within Nigeria's charcoal and forest
                            products industry. The Association recognizes that
                            sustainable charcoal production can only be achieved through
                            responsible forest management, environmental conservation,
                            and long-term ecological stewardship.
                        </p>

                        <p>
                            In response to the increasing challenges posed by
                            deforestation, climate change, land degradation, and
                            biodiversity loss, NACPDEAN has developed a comprehensive
                            National Afforestation and Environmental Sustainability
                            Programme. The programme is designed to restore degraded
                            landscapes, conserve ecosystems, mitigate the effects of
                            climate change, and create sustainable economic
                            opportunities for communities across Nigeria.
                        </p>

                        <p>
                            To successfully implement this programme, the Association
                            will establish a nationwide network of plant nurseries
                            dedicated to the production and supply of high-quality,
                            disease-free, and climate-resilient tree seedlings suited
                            to Nigeria's diverse agro-ecological zones. This initiative
                            will ensure quality control, traceability, and
                            sustainability in plantation development while
                            significantly reducing the high costs associated with
                            large-scale seedling procurement and dependence on
                            external suppliers.
                        </p>

                    </div>

                </div>

                <!-- Commitments -->
                <div class="mt-5">

                    <div class="text-center mb-5">

                        <h3>
                            Our Environmental Commitments
                        </h3>

                        <p class="text-muted">
                            To achieve its environmental and sustainability objectives,
                            NACPDEAN is committed to:
                        </p>

                    </div>

                    <div class="row g-4">

                        <div class="col-lg-6">

                            <div class="card border-0 shadow h-100">

                                <div class="card-body">

                                    <ul class="list-unstyled mb-0">

                                        <li class="mb-3">
                                            <i class="fa-solid fa-tree text-success me-2"></i>
                                            Organizing nationwide tree-planting campaigns and community afforestation
                                            programmes.
                                        </li>

                                        <li class="mb-3">
                                            <i class="fa-solid fa-seedling text-success me-2"></i>
                                            Implementing forest regeneration and ecosystem restoration projects.
                                        </li>

                                        <li class="mb-3">
                                            <i class="fa-solid fa-industry text-success me-2"></i>
                                            Establishing commercial, industrial, and community-based plantations.
                                        </li>

                                        <li>
                                            <i class="fa-solid fa-leaf text-success me-2"></i>
                                            Promoting sustainable harvesting practices and responsible forest management.
                                        </li>

                                    </ul>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-6">

                            <div class="card border-0 shadow h-100">

                                <div class="card-body">

                                    <ul class="list-unstyled mb-0">

                                        <li class="mb-3">
                                            <i class="fa-solid fa-earth-africa text-success me-2"></i>
                                            Supporting climate change mitigation and environmental conservation initiatives.
                                        </li>

                                        <li class="mb-3">
                                            <i class="fa-solid fa-bullhorn text-success me-2"></i>
                                            Conducting environmental education, advocacy, and public awareness campaigns.
                                        </li>

                                        <li>
                                            <i class="fa-solid fa-handshake text-success me-2"></i>
                                            Building strategic partnerships with government agencies, development partners,
                                            research institutions, and environmental organizations.
                                        </li>

                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Plantation Model -->
                <div class="mt-5">

                    <div class="card border-0 shadow-lg">

                        <div class="card-body p-5">

                            <div class="text-center mb-5">

                                <h3>
                                    Plantation Development Model
                                </h3>

                                <p class="text-muted">
                                    The Association's plantation development model will
                                    adopt a balanced <strong>60:40 ratio</strong>
                                    comprising:
                                </p>

                            </div>

                            <div class="row g-4">

                                <div class="col-lg-6">

                                    <div class="bg-success text-white rounded p-5 h-100">

                                        <h1 class="display-2 fw-bold text-white">
                                            60%
                                        </h1>

                                        <h4 class="text-white">
                                            Economic Tree Species
                                        </h4>

                                        <p class="mb-0">
                                            To generate income, create employment
                                            opportunities, promote trade, and support
                                            industrial development.
                                        </p>

                                    </div>

                                </div>

                                <div class="col-lg-6">

                                    <div class="bg-dark text-white rounded p-5 h-100">

                                        <h1 class="display-2 fw-bold text-white">
                                            40%
                                        </h1>

                                        <h4 class="text-white">
                                            Indigenous & Environmental Tree Species
                                        </h4>

                                        <p class="mb-0">
                                            To restore ecosystems, conserve
                                            biodiversity, protect watersheds,
                                            improve soil fertility, and strengthen
                                            environmental resilience.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Closing Statement -->
                <div class="mt-5">

                    <div class="alert alert-success p-4">

                        <h4 class="mb-3">
                            Our Commitment to Sustainability
                        </h4>

                        <p>
                            This integrated approach combines ecological restoration
                            with socio-economic development, making afforestation not
                            only an environmental necessity but also a strategic
                            instrument for sustainable national growth and rural
                            livelihood enhancement.
                        </p>

                        <p class="mb-0">
                            NACPDEAN firmly believes that every stakeholder within the
                            charcoal and forest products value chain has a collective
                            responsibility to protect the environment and actively
                            participate in afforestation initiatives to ensure the
                            long-term sustainability of the industry for present and
                            future generations.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ==========================================
         JOIN NACPDEAN
    ========================================== -->

        <section class="py-5">

            <div class="container">

                <div class="join-section shadow-lg rounded-4 overflow-hidden">

                    <div class="row align-items-center">

                        <!-- Left Content -->

                        <div class="col-lg-8 p-5">

                            <span class="badge bg-warning text-dark px-3 py-2 mb-3">
                                Become Part of Nigeria's Largest Charcoal Association
                            </span>

                            <h2 class="display-5 fw-bold text-white mb-4">
                                Join NACPDEAN Today
                            </h2>

                            <p class="lead text-white mb-4">

                                Become a member of the National Association of Charcoal
                                Producers, Dealers, Exporters and Afforestation of Nigeria
                                (NACPDEAN) and join a nationwide network committed to
                                sustainable charcoal production, responsible forest
                                management, export development, environmental conservation,
                                and economic growth.

                            </p>

                            <div class="row text-white mb-4">

                                <div class="col-md-6 mb-3">

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle-check text-warning me-2"></i>
                                        National Recognition
                                    </p>

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle-check text-warning me-2"></i>
                                        Industry Representation
                                    </p>

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle-check text-warning me-2"></i>
                                        Training & Capacity Building
                                    </p>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle-check text-warning me-2"></i>
                                        Export Opportunities
                                    </p>

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle-check text-warning me-2"></i>
                                        Environmental Sustainability
                                    </p>

                                    <p class="mb-2">
                                        <i class="fa-solid fa-circle-check text-warning me-2"></i>
                                        Strategic Partnerships
                                    </p>

                                </div>

                            </div>

                            <a href="membership.html" class="btn btn-warning btn-lg px-5 py-3 fw-semibold me-3">

                                Become a Member

                            </a>

                            <a href="contact.php" class="btn btn-outline-light btn-lg px-5 py-3">

                                Contact Us

                            </a>

                        </div>

                        <!-- Right Side -->

                        <div class="col-lg-4 text-center p-5">

                            <div class="member-circle mx-auto">

                                <i class="fa-solid fa-users fa-5x text-warning mb-4"></i>

                                <h3 class="text-white">

                                    Join Our Community

                                </h3>

                                <p class="text-light">

                                    Together we can build a sustainable charcoal industry,
                                    protect Nigeria's forests, and create lasting economic
                                    opportunities for future generations.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>
@endsection
