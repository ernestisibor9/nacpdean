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
         Historical Background
    ========================== -->
        <section class="py-5 bg-light">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Our Journey
                    </span>

                    <h2 class="section-title">
                        Historical Background & Rationale for Establishment
                    </h2>

                    <p class="text-muted">
                        Understanding why NACPDEAN was established and the
                        challenges that led to the creation of a unified
                        national body for Nigeria's charcoal industry.
                    </p>

                </div>

                <!-- Introduction -->

                <div class="row mb-5">

                    <div class="col-lg-12">

                        <p>
                            The establishment of NACPDEAN was necessitated by the
                            growing challenges confronting Nigeria's charcoal
                            industry over several years.
                        </p>

                        <p>
                            Prior to the formation of the Association, the charcoal
                            sector operated largely without an organized national
                            structure, resulting in numerous operational,
                            environmental and regulatory challenges. The absence
                            of a coordinating body created opportunities for
                            illegal trading, unregulated production, poor data
                            management and unsustainable exploitation of forest
                            resources.
                        </p>

                    </div>

                </div>

                <!-- Industry Challenges -->

                <h3 class="mb-4">
                    Major Challenges Facing the Industry
                </h3>

                <div class="row g-4">

                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body">
                                <h5 class="text-success">
                                    🌳 Increasing Deforestation
                                </h5>
                                <p>
                                    Nigeria experienced increasing pressure on its
                                    forest resources due to indiscriminate tree
                                    felling and unsustainable harvesting
                                    practices. In many instances, charcoal
                                    production activities were conducted without
                                    adequate replanting programmes, leading to
                                    concerns about environmental degradation and
                                    forest depletion.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body">
                                <h5 class="text-success">
                                    🤝 Absence of Industry Coordination
                                </h5>
                                <p>
                                    There was no unified national platform to
                                    coordinate stakeholders across the charcoal
                                    value chain. Producers, dealers, exporters
                                    and marketers operated independently without
                                    common standards, accountability mechanisms
                                    or industry regulations.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body">
                                <h5 class="text-success">
                                    ⚠ Illegal Trading Activities
                                </h5>
                                <p>
                                    The lack of regulation encouraged the
                                    emergence of illegal operators who engaged in
                                    unauthorized production, trading and export
                                    activities. This created reputational
                                    challenges for legitimate businesses and
                                    hindered government efforts to properly
                                    monitor the sector.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body">
                                <h5 class="text-success">
                                    📊 Inadequate Industry Data
                                </h5>
                                <p>
                                    There was no comprehensive database of
                                    operators, production volumes, export
                                    statistics or industry participants. The
                                    absence of reliable data made policy
                                    formulation, planning and regulation
                                    extremely difficult.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body">
                                <h5 class="text-success">
                                    🌱 Lack of Afforestation
                                </h5>
                                <p>
                                    One of the major concerns identified within
                                    the sector was the absence of organized
                                    afforestation programmes. There was little
                                    effort toward replacing harvested trees,
                                    resulting in concerns about sustainability
                                    and environmental conservation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body">
                                <h5 class="text-success">
                                    ⚖ Export Regulation Challenges
                                </h5>
                                <p>
                                    The absence of a recognized national body
                                    made it difficult for government agencies to
                                    effectively engage industry stakeholders and
                                    implement regulatory reforms.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Government Response -->

                <div class="mt-5">

                    <h3 class="mb-4">
                        Government Response
                    </h3>

                    <p>
                        As concerns regarding deforestation increased, the Federal
                        Government of Nigeria, through the Federal Ministry of
                        Environment, took administrative measures aimed at
                        addressing the situation.
                    </p>

                    <p>
                        Consequently, an administrative ban on charcoal
                        exportation was enforced through the Nigeria Customs
                        Service on <strong>25 May 2021</strong>. The action was
                        based on concerns that charcoal export activities were
                        contributing significantly to deforestation and
                        environmental degradation.
                    </p>

                    <p>
                        However, subsequent engagements revealed that the absence
                        of proper industry coordination, stakeholder regulation
                        and sustainable forestry programmes was the underlying
                        challenge rather than export activities alone.
                    </p>

                    <p>
                        Recognizing the economic importance of the charcoal
                        industry and its contribution to non-oil exports,
                        employment generation and foreign exchange earnings, the
                        Federal Ministry of Industry, Trade and Investment
                        initiated consultations with stakeholders and relevant
                        government institutions on the need to establish a
                        national commodity association for the charcoal sector.
                    </p>

                </div>

                <!-- Founding Institutions -->

                <div class="mt-5">

                    <h3 class="mb-4">
                        Founding Institutions
                    </h3>

                    <div class="row">

                        <div class="col-md-6">

                            <ul class="list-group list-group-flush">

                                <li class="list-group-item">Federal Ministry of Industry, Trade and Investment</li>

                                <li class="list-group-item">Federal Ministry of Environment</li>

                                <li class="list-group-item">Federal Ministry of Agriculture and Rural Development</li>

                                <li class="list-group-item">Federation of Agricultural Commodity Associations of Nigeria
                                    (FACAN)</li>

                            </ul>

                        </div>

                        <div class="col-md-6">

                            <ul class="list-group list-group-flush">

                                <li class="list-group-item">Nigerian Export Promotion Council (NEPC)</li>

                                <li class="list-group-item">Raw Materials Research and Development Council (RMRDC)</li>

                                <li class="list-group-item">Corporate Affairs Commission (CAC)</li>

                                <li class="list-group-item">Other Relevant Stakeholders and Industry Operators</li>

                            </ul>

                        </div>

                    </div>

                    <div class="alert alert-success mt-4">

                        The Association was officially formed on
                        <strong>28 August 2021</strong> through collaborative
                        efforts involving government institutions and industry
                        stakeholders to create a structured, accountable and
                        sustainable framework for Nigeria's charcoal industry.

                    </div>

                </div>

            </div>

        </section>

        <!-- ======================================
         First National Election & Inauguration
    ======================================= -->
        <section class="py-5 bg-light">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Leadership Journey
                    </span>

                    <h2 class="section-title">
                        First National Election & Inauguration
                    </h2>

                    <p class="text-muted">
                        Key milestones in the establishment of the Association's
                        leadership and national recognition.
                    </p>

                </div>

                <div class="timeline">

                    <!-- Timeline Item -->

                    <div class="card border-0 shadow mb-4">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-3">

                                    <h3 class="text-success fw-bold">
                                        3 Dec 2021
                                    </h3>

                                </div>

                                <div class="col-md-9">

                                    <h4>
                                        Maiden National Election
                                    </h4>

                                    <p class="mb-0">
                                        Following its establishment, NACPDEAN conducted
                                        its maiden national election on
                                        <strong>3rd December 2021</strong>.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Timeline Item -->

                    <div class="card border-0 shadow mb-4">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-3">

                                    <h3 class="text-success fw-bold">
                                        First President
                                    </h3>

                                </div>

                                <div class="col-md-9">

                                    <h4>
                                        Election of the First National Executive
                                    </h4>

                                    <p class="mb-0">
                                        The election produced
                                        <strong>Mr. Edu Babatunde</strong> as the first
                                        National President of the Association alongside
                                        the first National Executive Committee.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Timeline Item -->

                    <div class="card border-0 shadow mb-4">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-3">

                                    <h3 class="text-success fw-bold">
                                        11 Dec 2021
                                    </h3>

                                </div>

                                <div class="col-md-9">

                                    <h4>
                                        Official Inauguration
                                    </h4>

                                    <p class="mb-0">
                                        The elected leadership was formally inaugurated
                                        on <strong>11th December 2021</strong> and
                                        charged with the responsibility of building a
                                        sustainable, accountable and nationally
                                        recognized institution capable of transforming
                                        the charcoal industry.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Timeline Item -->

                    <div class="card border-0 shadow">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-3">

                                    <h3 class="text-success fw-bold">
                                        July 2024
                                    </h3>

                                </div>

                                <div class="col-md-9">

                                    <h4>
                                        Official National Launch
                                    </h4>

                                    <p class="mb-0">
                                        NACPDEAN was officially launched nationally,
                                        introducing the Association to government
                                        institutions, development partners, investors
                                        and stakeholders across Nigeria.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- ==========================================
         LEGAL STATUS & REGISTRATION
    =========================================== -->
        <section class="py-5 bg-light">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Official Recognition
                    </span>

                    <h2 class="section-title">
                        Legal Status & Registration
                    </h2>

                    <p class="text-muted">
                        NACPDEAN is a legally recognized national association
                        committed to promoting responsible charcoal production,
                        environmental sustainability, and industry development
                        throughout Nigeria.
                    </p>

                </div>

                <div class="row g-4">

                    <!-- Left Content -->

                    <div class="col-lg-7">

                        <p>
                            The National Association of Charcoal Producers,
                            Dealers, Exporters and Afforestation of Nigeria
                            (NACPDEAN) is a duly registered national association
                            established to promote, regulate, coordinate and
                            protect the interests of stakeholders within the
                            charcoal production, marketing and export value
                            chain in Nigeria.
                        </p>

                        <p>
                            The Association is authorized to conduct its
                            activities throughout the Federal Republic of
                            Nigeria and operates under its Constitution and
                            approved operational guidelines.
                        </p>

                        <p>
                            NACPDEAN is committed to ensuring compliance with
                            applicable laws, environmental sustainability
                            standards and ethical business practices while
                            maintaining legal standing to enter partnerships,
                            receive memberships, issue operational compliance
                            documents and undertake activities consistent with
                            its objectives and the laws of the Federal Republic
                            of Nigeria.
                        </p>

                    </div>

                    <!-- Right Card -->

                    <div class="col-lg-5">

                        <div class="card border-0 shadow">

                            <div class="card-body">

                                <h4 class="text-success mb-4">
                                    Registration Details
                                </h4>

                                <div class="mb-3">
                                    <strong>
                                        Corporate Affairs Commission (CAC)
                                    </strong>
                                    <br>
                                    Federal Republic of Nigeria
                                </div>

                                <hr>

                                <div class="mb-3">

                                    <strong>
                                        Registration Number
                                    </strong>

                                    <h4 class="text-success">
                                        IT182068
                                    </h4>

                                </div>

                                <hr>

                                <div class="mb-3">

                                    <strong>
                                        Date of Incorporation
                                    </strong>

                                    <p class="mb-0">
                                        6 June 2022
                                    </p>

                                </div>

                                <hr>

                                <div>

                                    <strong>
                                        Operational Coverage
                                    </strong>

                                    <p class="mb-0">
                                        Entire Federal Republic of Nigeria
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Features -->

                <div class="row text-center mt-5 g-4">

                    <div class="col-md-3">

                        <div class="card border-0 h-100 shadow-sm">

                            <div class="card-body">

                                <i class="fa-solid fa-scale-balanced fa-2x text-success mb-3"></i>

                                <h5>Legal Recognition</h5>

                                <p class="small mb-0">
                                    Registered national association under Nigerian law.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="card border-0 h-100 shadow-sm">

                            <div class="card-body">

                                <i class="fa-solid fa-handshake fa-2x text-success mb-3"></i>

                                <h5>Partnerships</h5>

                                <p class="small mb-0">
                                    Authorized to establish partnerships and collaborations.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="card border-0 h-100 shadow-sm">

                            <div class="card-body">

                                <i class="fa-solid fa-file-signature fa-2x text-success mb-3"></i>

                                <h5>Compliance</h5>

                                <p class="small mb-0">
                                    Issues operational compliance documentation.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="card border-0 h-100 shadow-sm">

                            <div class="card-body">

                                <i class="fa-solid fa-globe-africa fa-2x text-success mb-3"></i>

                                <h5>Nationwide</h5>

                                <p class="small mb-0">
                                    Authorized to operate across all states in Nigeria.
                                </p>

                            </div>

                        </div>

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
