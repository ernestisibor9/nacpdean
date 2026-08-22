@extends('frontend.master')

@section('home')
    <style>
        /* =====================================================
           PAGE
        ===================================================== */

        .partnership-page {
            background: #f7f9f4 !important;
            color: #59615a !important;
            min-height: 100vh;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .partnership-hero {
            position: relative;
            background:
                linear-gradient(135deg,
                    rgba(47, 75, 8, .95),
                    rgba(96, 140, 17, .88)),
                url("{{ asset('frontend/assets/img/forest-bg.jpg') }}") center/cover no-repeat;

            padding: 90px 20px;
            overflow: hidden;
            color: #ffffff !important;
        }

        .partnership-hero::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
            top: -100px;
            right: -80px;
        }

        .partnership-hero::after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
            bottom: -100px;
            left: -50px;
        }

        .partnership-hero .container {
            position: relative;
            z-index: 2;
        }

        .partnership-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 8px 18px;

            border-radius: 30px;

            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);

            color: #ffffff !important;

            font-size: 13px;
            font-weight: 600;
            letter-spacing: .5px;

            margin-bottom: 20px;
        }

        .partnership-hero h1 {
            font-family: "Poppins", sans-serif;

            font-size: 46px;
            font-weight: 800;
            line-height: 1.2;

            margin-bottom: 20px;

            color: #ffffff !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .partnership-hero p {
            max-width: 850px;

            margin: auto;

            font-size: 17px;
            line-height: 1.8;

            color: rgba(255, 255, 255, .92) !important;

            visibility: visible !important;
            opacity: 1 !important;
        }


        /* =====================================================
           MAIN WRAPPER
        ===================================================== */

        .partnership-wrapper {
            padding: 70px 0;

            color: #59615a !important;

            visibility: visible !important;
            opacity: 1 !important;
        }


        /* =====================================================
           INTRODUCTION CARD
        ===================================================== */

        .partnership-intro {
            background: #ffffff !important;

            border-radius: 22px;

            padding: 45px;

            box-shadow: 0 12px 40px rgba(0, 0, 0, .07);

            border: 1px solid #e9eee2;

            margin-bottom: 60px;

            color: #59615a !important;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .section-icon {
            width: 60px;
            height: 60px;

            border-radius: 16px;

            background: rgba(96, 140, 17, .1);

            color: #608C11 !important;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 25px;

            margin-bottom: 20px;
        }

        .partnership-title {
            color: #263414 !important;

            font-family: "Poppins", sans-serif;

            font-size: 30px;
            font-weight: 800;

            margin-bottom: 18px;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partnership-intro p {
            color: #59615a !important;

            font-size: 16px;

            line-height: 1.9;

            margin-bottom: 18px;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partnership-intro strong {
            color: #263414 !important;
        }


        /* =====================================================
           HIGHLIGHT
        ===================================================== */

        .partnership-highlight {
            background:
                linear-gradient(135deg,
                    #608C11,
                    #496b0c) !important;

            border-radius: 20px;

            padding: 30px;

            color: #ffffff !important;

            margin-top: 30px;
        }

        .partnership-highlight .highlight-icon {
            font-size: 35px;

            margin-bottom: 15px;

            color: #ffffff !important;
        }

        .partnership-highlight h4 {
            font-weight: 700;

            margin-bottom: 12px;

            color: #ffffff !important;
        }

        .partnership-highlight p {
            color: rgba(255, 255, 255, .9) !important;

            margin: 0;

            line-height: 1.8;
        }


        /* =====================================================
           PARTNER SECTION HEADING
        ===================================================== */

        .partner-section-heading {
            text-align: center;

            margin-bottom: 45px;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partner-section-heading .small-label {
            display: inline-block;

            color: #608C11 !important;

            font-weight: 700;

            font-size: 13px;

            text-transform: uppercase;

            letter-spacing: 1.5px;

            margin-bottom: 10px;
        }

        .partner-section-heading h2 {
            font-family: "Poppins", sans-serif;

            color: #263414 !important;

            font-size: 34px;

            font-weight: 800;

            margin-bottom: 12px;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partner-section-heading p {
            max-width: 750px;

            margin: auto;

            color: #69706a !important;

            line-height: 1.8;

            visibility: visible !important;
            opacity: 1 !important;
        }


        /* =====================================================
           PARTNER CARD
        ===================================================== */

        .partner-card {
            background: #ffffff !important;

            height: 100%;

            border-radius: 24px;

            overflow: hidden;

            border: 1px solid #e6ebdf;

            box-shadow: 0 10px 35px rgba(0, 0, 0, .07);

            transition: all .35s ease;

            color: #59615a !important;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partner-card:hover {
            transform: translateY(-8px);

            box-shadow: 0 20px 50px rgba(0, 0, 0, .12);
        }


        /* =====================================================
           PARTNER CARD HEADER
        ===================================================== */

        .partner-card-header {
            position: relative;

            padding: 35px;

            background:
                linear-gradient(135deg,
                    #608C11,
                    #496b0c) !important;

            color: #ffffff !important;

            min-height: 220px;
        }

        .partner-card-header.geozeerah {
            background:
                linear-gradient(135deg,
                    #315c72,
                    #1e4052) !important;
        }

        .partner-card-icon {
            width: 65px;
            height: 65px;

            border-radius: 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(255, 255, 255, .15);

            border: 1px solid rgba(255, 255, 255, .25);

            font-size: 27px;

            margin-bottom: 22px;

            color: #ffffff !important;
        }

        .partner-card-header h3 {
            font-family: "Poppins", sans-serif;

            font-weight: 800;

            font-size: 25px;

            margin-bottom: 10px;

            color: #ffffff !important;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partner-card-header p {
            margin: 0;

            color: rgba(255, 255, 255, .88) !important;

            line-height: 1.6;

            font-size: 14px;
        }


        /* =====================================================
           PARTNER CARD BODY
        ===================================================== */

        .partner-card-body {
            padding: 35px;

            background: #ffffff !important;

            color: #59615a !important;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partner-card-body>p {
            color: #59615a !important;

            line-height: 1.85;

            font-size: 15px;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partner-card-body strong {
            color: #263414 !important;
        }


        /* =====================================================
           EXPERTISE
        ===================================================== */

        .expertise-title {
            font-size: 17px;

            font-weight: 700;

            color: #263414 !important;

            margin-top: 25px;

            margin-bottom: 15px;
        }

        .expertise-list {
            display: flex;

            flex-wrap: wrap;

            gap: 9px;

            padding: 0;

            margin: 0;

            list-style: none;
        }

        .expertise-list li {
            background: #f3f7ed !important;

            color: #52770f !important;

            border: 1px solid #e0e9d4;

            padding: 8px 12px;

            border-radius: 30px;

            font-size: 12px;

            font-weight: 600;
        }


        /* =====================================================
           LEADERSHIP
        ===================================================== */

        .leadership-box {
            margin-top: 30px;

            padding: 25px;

            border-radius: 18px;

            background: #f8faf6 !important;

            border: 1px solid #e4eadc;

            color: #626962 !important;
        }

        .leadership-label {
            display: flex;

            align-items: center;

            gap: 10px;

            color: #608C11 !important;

            font-size: 12px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1px;

            margin-bottom: 12px;
        }

        .leadership-name {
            color: #263414 !important;

            font-family: "Poppins", sans-serif;

            font-size: 19px;

            font-weight: 800;

            margin-bottom: 8px;
        }

        .leadership-role {
            color: #608C11 !important;

            font-size: 13px;

            font-weight: 600;

            margin-bottom: 15px;
        }

        .leadership-box p {
            color: #626962 !important;

            font-size: 14px;

            line-height: 1.8;

            margin-bottom: 0;
        }


        /* =====================================================
           COLLABORATION
        ===================================================== */

        .collaboration-section {
            margin-top: 70px;

            position: relative;

            overflow: hidden;

            border-radius: 25px;

            padding: 60px 50px;

            background:
                linear-gradient(135deg,
                    rgba(38, 55, 20, .97),
                    rgba(96, 140, 17, .94)) !important;

            color: #ffffff !important;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .collaboration-section::after {
            content: "\f06c";

            font-family: "Font Awesome 6 Free";

            font-weight: 900;

            position: absolute;

            right: 35px;

            bottom: -45px;

            font-size: 220px;

            color: rgba(255, 255, 255, .05);
        }

        .collaboration-section .content {
            position: relative;

            z-index: 2;
        }

        .collaboration-section .small-label {
            color: #dcecc1 !important;

            font-size: 13px;

            font-weight: 700;

            letter-spacing: 1.5px;

            text-transform: uppercase;
        }

        .collaboration-section h2 {
            font-family: "Poppins", sans-serif;

            font-size: 34px;

            font-weight: 800;

            margin: 12px 0 20px;

            color: #ffffff !important;
        }

        .collaboration-section p {
            max-width: 900px;

            color: rgba(255, 255, 255, .9) !important;

            font-size: 16px;

            line-height: 1.9;

            margin-bottom: 18px;
        }

        .collaboration-section strong {
            color: #ffffff !important;
        }


        /* =====================================================
           COMMITMENT POINTS
        ===================================================== */

        .commitment-grid {
            margin-top: 30px;
        }

        .commitment-item {
            display: flex;

            gap: 14px;

            align-items: flex-start;

            margin-bottom: 20px;
        }

        .commitment-item .icon {
            flex-shrink: 0;

            width: 38px;
            height: 38px;

            border-radius: 10px;

            background: rgba(255, 255, 255, .12);

            display: flex;

            align-items: center;

            justify-content: center;

            color: #ffffff !important;
        }

        .commitment-item span {
            color: rgba(255, 255, 255, .9) !important;

            font-size: 14px;

            line-height: 1.6;
        }


        /* =====================================================
           FINAL STATEMENT
        ===================================================== */

        .partnership-final {
            visibility: visible !important;
            opacity: 1 !important;
        }

        .partnership-final h3 {
            color: #263414 !important;

            visibility: visible !important;
            opacity: 1 !important;
        }

        .partnership-final p {
            max-width: 800px;

            margin-left: auto;
            margin-right: auto;

            color: #69706a !important;

            line-height: 1.9;

            visibility: visible !important;
            opacity: 1 !important;
        }


        /* =====================================================
           IMPORTANT AOS FIX

           If AOS is causing content to remain invisible,
           this guarantees the page content remains visible.
        ===================================================== */

        [data-aos] {
            visibility: visible !important;
        }

        [data-aos].aos-animate {
            opacity: 1 !important;
            transform: none !important;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 991px) {

            .partnership-hero {
                padding: 75px 20px;
            }

            .partnership-hero h1 {
                font-size: 38px;
            }

            .partnership-intro {
                padding: 35px;
            }

            .partner-card-header {
                min-height: auto;
            }

        }


        @media (max-width: 767px) {

            .partnership-hero {
                padding: 60px 20px;
            }

            .partnership-hero h1 {
                font-size: 30px;
            }

            .partnership-hero p {
                font-size: 15px;
            }

            .partnership-wrapper {
                padding: 45px 0;
            }

            .partnership-intro {
                padding: 25px;
                border-radius: 18px;
            }

            .partnership-title {
                font-size: 24px;
            }

            .partner-section-heading h2 {
                font-size: 27px;
            }

            .partner-card-header,
            .partner-card-body {
                padding: 25px;
            }

            .collaboration-section {
                padding: 40px 25px;
                border-radius: 20px;
            }

            .collaboration-section h2 {
                font-size: 27px;
            }

            .collaboration-section::after {
                font-size: 140px;
            }

        }
    </style>

        <main class="main partnership-page">


        <!-- =====================================================
         HERO
    ====================================================== -->

        <section class="partnership-hero text-center">

            <div class="container">

                <span class="partnership-label">
                    <i class="fas fa-handshake"></i>
                    Strategic Partnerships
                </span>

                <h1>
                    Professionalism, Sustainability
                    <br class="d-none d-md-block">
                    &amp; Best Practices
                </h1>

                <p>
                    NACPDEAN is strengthening professional capacity,
                    sustainable forest management and environmental
                    stewardship through strategic partnerships with
                    reputable organisations and technical experts.
                </p>

            </div>

        </section>


        <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

        <section class="partnership-wrapper">

            <div class="container">


                <!-- =================================================
                 INTRODUCTION
            ================================================== -->

                <div class="partnership-intro" data-aos="fade-up">

                    <div class="section-icon">
                        <i class="fas fa-leaf"></i>
                    </div>

                    <h2 class="partnership-title">
                        STRATEGIC PARTNERSHIP FOR PROFESSIONALISM, SUSTAINABILITY AND BEST PRACTICES IN FOREST MANAGEMENT AND AFFORESTATION
                    </h2>

                    <p>
                        In furtherance of its commitment to strengthening
                        professionalism, sustainability, technical capacity,
                        and the adoption of internationally recognised best
                        practices in forest management and afforestation, the
                        <strong>
                            National Association of Charcoal Producers,
                            Dealers, Exporters and Afforestation of Nigeria
                            (NACPDEAN)
                        </strong>
                        has established strategic partnerships with two
                        reputable consultants and organisations:
                        <strong>Save Sahara Network (SSN)</strong> and
                        <strong>Geozeerah Global Ventures Limited</strong>.
                    </p>

                    <p>
                        This strategic collaboration is aimed at enhancing
                        NACPDEAN's capacity to promote sustainable forest
                        management, responsible forestry practices, biodiversity
                        conservation, environmental restoration, plantation
                        development and management, tree nursery establishment,
                        afforestation, reforestation, and community-driven
                        environmental initiatives across Nigeria.
                    </p>

                    <p>
                        Through these partnerships, NACPDEAN will leverage the
                        technical expertise, professional experience, research
                        capacity, and practical field knowledge of its strategic
                        partners to develop and implement sustainable solutions
                        to deforestation, forest degradation, biodiversity loss,
                        and other environmental challenges affecting Nigeria's
                        forest resources.
                    </p>

                    <p>
                        The collaboration will also support the development of
                        sustainable afforestation programmes, forest and
                        plantation management systems, nursery development,
                        seedling production, environmental monitoring,
                        community engagement, capacity building, and the
                        promotion of responsible practices throughout the
                        charcoal and forest-product value chain.
                    </p>


                    <!-- Highlight -->

                    <div class="partnership-highlight">

                        <div class="highlight-icon">
                            <i class="fas fa-tree"></i>
                        </div>

                        <h4>
                            Our Shared Environmental Commitment
                        </h4>

                        <p>
                            Together, the partners are committed to promoting a
                            coordinated and professional approach to sustainable
                            forest management, environmental protection,
                            ecosystem restoration, climate resilience,
                            biodiversity conservation, and community
                            empowerment, while supporting relevant national and
                            international environmental sustainability standards
                            and objectives.
                        </p>

                    </div>

                </div>


                <!-- =================================================
                 PARTNER INTRO
            ================================================== -->

                <div class="partner-section-heading" data-aos="fade-up">

                    <span class="small-label">
                        Our Strategic Partners
                    </span>

                    <h2>
                        Technical Partners for Sustainable Development
                    </h2>

                    <p>
                        NACPDEAN works with experienced environmental,
                        forestry and geospatial professionals to strengthen
                        technical capacity and promote responsible practices
                        across Nigeria.
                    </p>

                </div>


                <!-- =================================================
                 PARTNER CARDS
            ================================================== -->

                <div class="row g-4">


                    <!-- =================================================
                     SAVE SAHARA NETWORK
                ================================================== -->

                    <div class="col-lg-6" data-aos="fade-right">

                        <div class="partner-card">

                            <div class="partner-card-header">

                                <div class="partner-card-icon">
                                    <i class="fas fa-seedling"></i>
                                </div>

                                <h3>
                                    Save Sahara Network
                                </h3>

                                <p>
                                    Environmental protection, biodiversity
                                    conservation and ecosystem restoration.
                                </p>

                            </div>


                            <div class="partner-card-body">

                                <h4 class="expertise-title">
                                    About Save Sahara Network (SSN)
                                </h4>

                                <p>
                                    <strong>Save Sahara Network (SSN)</strong>
                                    is a registered non-profit and non-governmental organisation dedicated to environmental protection, biodiversity conservation, ecosystem restoration, and community empowerment for sustainable development.
                                </p>

                                <p>
                                    Guided by its vision of
                                    <strong>
                                        "Protecting Nature for Empowerment and Sustainable Development"
                                    </strong>
                                    and its mission of
                                    <strong>
                                        "Saving Nature and Empowering People at the Grassroots,"
                                    </strong>
                                    SSN is committed to protecting, conserving, restoring, and promoting the sustainable use of biodiversity and natural resources.
                                </p>

                                <p>
                                    The organisation works with communities and other stakeholders to promote grassroots participation in environmental conservation and sustainable development. Through its programmes and interventions, SSN supports local communities in contributing meaningfully to the achievement of the United Nations Sustainable Development Goals (SDGs).
                                </p>

                                <p>
                                    SSN delivers its objectives through grassroots environmental projects, afforestation and ecosystem restoration initiatives, capacity-building programmes, scientific forums on environmental issues, research, advocacy, environmental awareness campaigns, and practical conservation interventions.
                                </p>

                                <p>
                                    The organisation also provides opportunities for both professionals and non-professionals to actively participate in environmental protection and conservation efforts, fostering a culture of responsible interaction with nature while promoting sustainable livelihoods and community development.
                                </p>


                                <h4 class="expertise-title">
                                    Key Areas of Work
                                </h4>

                                <ul class="expertise-list">

                                    <li>Afforestation &amp; Ecosystem Restoration</li>
                                    <li>Biodiversity Conservation</li>
                                    <li>Grassroots Environmental Projects</li>
                                    <li>Environmental Research &amp; Advocacy</li>
                                    <li>Capacity-Building Programmes</li>
                                    <li>Scientific Forums on Environmental Issues</li>
                                    <li>Awareness Campaigns</li>
                                    <li>Sustainable Livelihoods &amp; Community Development</li>

                                </ul>


                                <!-- Leadership -->

                                <div class="leadership-box">

                                    <div class="leadership-label">
                                        <i class="fas fa-user-tie"></i>
                                        Professional and Technical Leadership
                                    </div>

                                    <div class="leadership-name">
                                        Professor Folaranmi Babalola
                                    </div>

                                    <div class="leadership-role">
                                        Forest Socioeconomics &amp;
                                        Environmental Forestry
                                    </div>

                                    <p>
                                        Save Sahara Network (SSN) is supported by Professor Folaranmi Babalola, a distinguished authority in Forest Socioeconomics and Environmental Forestry and a professional associated with the Forest Stewardship Council (FSC).
                                    </p>

                                    <p class="mt-3">
                                        His expertise, leadership, and commitment to sustainable forest management provide valuable strategic and technical guidance to the organisation's conservation, afforestation, environmental restoration, and community development programmes. His professional experience further strengthens the capacity of this collaboration to promote responsible forest management, internationally recognised sustainability principles, community participation, biodiversity conservation, and practical solutions for the protection and restoration of Nigeria's forest resources.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                     GEOZEEERAH
                ================================================== -->

                    <div class="col-lg-6" data-aos="fade-left">

                        <div class="partner-card">

                            <div class="partner-card-header geozeerah">

                                <div class="partner-card-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>

                                <h3>
                                    Geozeerah Global Ventures Limited
                                </h3>

                                <p>
                                    GIS, Remote Sensing, Forestry, Mapping
                                    and Sustainable Land Management.
                                </p>

                            </div>


                            <div class="partner-card-body">

                                <h4 class="expertise-title">
                                    About Geozeerah Global Ventures Limited
                                </h4>

                                <p>
                                    <strong>
                                        Geozeerah Global Ventures Limited
                                    </strong>
                                    is a professional and innovative company specialising in the application of
                                    <strong>
                                        Geographic Information Systems (GIS) and Remote Sensing
                                    </strong>
                                    for sustainable agriculture, forestry, natural resource management, environmental monitoring, and sustainable land-use planning.
                                </p>

                                <p>
                                    The company provides professional and technical services in GIS and spatial analysis, remote sensing, general mapping, land and aerial surveys, photogrammetry, drone-based surveillance, aerial data acquisition, boundary demarcation, and manpower supply.
                                </p>

                                <p>
                                    Geozeerah Global Ventures Limited also specialises in plantation establishment and management, forest plantation development, tree nursery establishment and management, seedling production and supply, afforestation, reforestation, and environmental restoration services.
                                </p>

                                <p>
                                    Through the application of modern geospatial technologies and sound forestry practices, the company supports effective planning, assessment, monitoring, and management of forest resources, plantations, and environmental restoration projects.
                                </p>

                                <p>
                                    The company's commitment is to provide sustainable, innovative, and technically sound solutions that support responsible natural resource management, successful forest and plantation development, ecosystem restoration, and long-term environmental sustainability.
                                </p>


                                <h4 class="expertise-title">
                                    Key Areas of Expertise
                                </h4>

                                <ul class="expertise-list">

                                    <li>GIS &amp; Spatial Analysis</li>
                                    <li>Remote Sensing &amp; General Mapping</li>
                                    <li>Land &amp; Aerial Surveys / Photogrammetry</li>
                                    <li>Drone-Based Surveillance &amp; Data Acquisition</li>
                                    <li>Boundary Demarcation &amp; Manpower Supply</li>
                                    <li>Plantation Establishment &amp; Management</li>
                                    <li>Tree Nursery Development &amp; Seedling Supply</li>
                                    <li>Afforestation, Reforestation &amp; Environmental Restoration</li>

                                </ul>


                                <!-- Leadership -->

                                <div class="leadership-box">

                                    <div class="leadership-label">
                                        <i class="fas fa-user-tie"></i>
                                        Professional Leadership
                                    </div>

                                    <div class="leadership-name">
                                        Muhali Musa Olatunbosun
                                    </div>

                                    <div class="leadership-role">
                                        Forester • GIS &amp; Remote Sensing
                                        Specialist
                                    </div>

                                    <p>
                                        Geozeerah Global Ventures Limited is led by Muhali Musa Olatunbosun, a seasoned and experienced Forester with sound professional expertise in GIS and Remote Sensing applications, forestry, plantation establishment and management, tree nursery development, sustainable natural resource management, and environmental conservation.
                                    </p>

                                    <p class="mt-3">
                                        His professional knowledge and practical experience strengthen the company's capacity to provide technical support for forest and plantation planning, tree nursery establishment, seedling production, geospatial mapping, forest resource assessment, environmental monitoring, afforestation, reforestation, and other sustainable land and natural resource management programmes.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                 COLLABORATIVE COMMITMENT
            ================================================== -->

                <div class="collaboration-section" data-aos="fade-up">

                    <div class="content">

                        <span class="small-label">
                            Working Together
                        </span>

                        <h2>
                            Collaborative Commitment
                        </h2>

                        <p>
                            Through this strategic partnership,
                            <strong>
                                NACPDEAN, Save Sahara Network (SSN), and
                                Geozeerah Global Ventures Limited
                            </strong>
                            will work collaboratively to advance
                            professionalism, sustainability, innovation,
                            and best practices in forest management and
                            afforestation across Nigeria.
                        </p>

                        <p>
                            The partnership represents an important step
                            towards strengthening technical capacity and
                            professional expertise within the charcoal and
                            forestry sector, promoting responsible and sustainable
                            environmental practices, establishing and
                            managing sustainable forest plantations,
                            developing tree nurseries, restoring degraded
                            landscapes, conserving biodiversity, and
                            empowering communities to participate actively in the protection, sustainable management, and restoration of Nigeria's forests and natural environment.
                        </p>


                        <div class="row commitment-grid">


                            <div class="col-md-6">

                                <div class="commitment-item">

                                    <div class="icon">
                                        <i class="fas fa-tree"></i>
                                    </div>

                                    <span>
                                        Sustainable forest management and
                                        responsible forestry practices.
                                    </span>

                                </div>


                                <div class="commitment-item">

                                    <div class="icon">
                                        <i class="fas fa-seedling"></i>
                                    </div>

                                    <span>
                                        Afforestation, reforestation and
                                        plantation development.
                                    </span>

                                </div>


                                <div class="commitment-item">

                                    <div class="icon">
                                        <i class="fas fa-globe-africa"></i>
                                    </div>

                                    <span>
                                        Biodiversity conservation and
                                        ecosystem restoration.
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="commitment-item">

                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>

                                    <span>
                                        Community empowerment and grassroots
                                        participation.
                                    </span>

                                </div>


                                <div class="commitment-item">

                                    <div class="icon">
                                        <i class="fas fa-map"></i>
                                    </div>

                                    <span>
                                        Modern geospatial technologies and
                                        environmental monitoring.
                                    </span>

                                </div>


                                <div class="commitment-item">

                                    <div class="icon">
                                        <i class="fas fa-award"></i>
                                    </div>

                                    <span>
                                        Professional standards and
                                        internationally recognised best
                                        practices.
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                 FINAL STATEMENT
            ================================================== -->

                <div class="text-center mt-5 pt-3 partnership-final"
                    data-aos="fade-up">

                    <div class="section-icon mx-auto">
                        <i class="fas fa-handshake"></i>
                    </div>

                    <h3 class="fw-bold">
                        Building a Greener and More Sustainable Nigeria
                    </h3>

                    <p>
                        NACPDEAN remains committed to working with credible
                        technical partners, professionals, communities and
                        stakeholders to promote sustainable forestry,
                        responsible charcoal production, environmental
                        protection and the restoration of Nigeria's natural
                        resources.
                    </p>

                </div>


            </div>

        </section>

    </main>

@endsection
