@extends('frontend.master')

@section('home')


  <style>
    .page-header {
      position: relative;
      background: url(" {{ asset('frontend/assets/img/hero-carousel/hero-carousel-3.jpg') }} ") center center/cover no-repeat;
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


        <!-- ===========================================
     ORGANIZATIONAL STRUCTURE
    =========================================== -->

        <section class="py-5 bg-light">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Leadership Structure
                    </span>

                    <h2 class="section-title">
                        Organizational Structure
                    </h2>

                    <p class="text-muted">
                        NACPDEAN operates through a structured leadership system
                        designed to ensure effective governance, transparency,
                        accountability and nationwide coordination of the charcoal
                        industry.
                    </p>

                </div>

                <div class="org-chart">

                    <!-- Governing Council -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-building-columns fa-2x text-success mb-3"></i>

                            <h5>Governing Council</h5>

                            <p>
                                Highest policy-making organ of the Association.
                            </p>

                        </div>

                    </div>

                    <div class="org-line"></div>

                    <!-- Patrons + BOT -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-user-tie fa-2x text-success mb-3"></i>

                            <h5>Patrons</h5>

                            <p>
                                Provide guidance and institutional support.
                            </p>

                        </div>

                        <div class="org-box">

                            <i class="fa-solid fa-users-gear fa-2x text-success mb-3"></i>

                            <h5>Board of Trustees</h5>

                            <p>
                                (B.O.T)
                            </p>

                        </div>

                    </div>

                    <div class="org-line"></div>

                    <!-- NEC -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-landmark fa-2x text-success mb-3"></i>

                            <h5>
                                National Executive Council
                            </h5>

                            <p>
                                (N.E.C)
                            </p>

                        </div>

                    </div>

                    <div class="org-line"></div>

                    <!-- NWC -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-users fa-2x text-success mb-3"></i>

                            <h5>
                                National Working Committee
                            </h5>

                            <p>
                                (N.W.C)
                            </p>

                        </div>

                    </div>

                    <div class="org-line"></div>

                    <!-- General Council -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-network-wired fa-2x text-success mb-3"></i>

                            <h5>
                                General Council
                            </h5>

                            <p>
                                (G.C)
                            </p>

                        </div>

                    </div>

                    <div class="org-line"></div>

                    <!-- SEC -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-map-location-dot fa-2x text-success mb-3"></i>

                            <h5>
                                State Executive Committee
                            </h5>

                            <p>
                                (S.E.C)
                            </p>

                        </div>

                    </div>

                    <div class="org-line"></div>

                    <!-- LEC -->

                    <div class="org-level">

                        <div class="org-box">

                            <i class="fa-solid fa-location-dot fa-2x text-success mb-3"></i>

                            <h5>
                                Local Government Executive Committee
                            </h5>

                            <p>
                                (L.E.C)
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- ===========================================
    STANDING COMMITTEES
    =========================================== -->

        <section class="py-5">

            <div class="container">

                <div class="text-center mb-5">

                    <h2 class="section-title">
                        Standing Committees
                    </h2>

                    <p class="text-muted">

                        Specialized committees established to support the
                        implementation of the Association's objectives.

                    </p>

                </div>

                <div class="row g-4">

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-tree"></i>

                            <h5>
                                Afforestation Committee
                            </h5>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-coins"></i>

                            <h5>
                                Finance Committee
                            </h5>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-shield-halved"></i>

                            <h5>
                                Task-force & Compliance Committee
                            </h5>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-ship"></i>

                            <h5>
                                Trade & Export Committee
                            </h5>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-id-card"></i>

                            <h5>
                                Membership Committee
                            </h5>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-microscope"></i>

                            <h5>
                                Research & Development Committee
                            </h5>

                        </div>

                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-bullhorn"></i>

                            <h5>
                                Communication & Public Relations Committee
                            </h5>

                        </div>
                    </div>

                    <div class="col-lg-4">

                        <div class="committee-card text-center">

                            <i class="fa-solid fa-microscope"></i>

                            <h5>
                                Logistics and Regulatory Engagement Committee
                            </h5>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- ===========================================
    SCOPE OF OPERATIONS
    =========================================== -->

        <section class="py-5 bg-light">

            <div class="container">

                <div class="text-center mb-5">

                    <span class="text-success fw-bold text-uppercase">
                        Nationwide Operations
                    </span>

                    <h2 class="section-title">

                        Scope of Operations

                    </h2>

                    <p class="text-muted">

                        NACPDEAN operates across the entire charcoal value chain,
                        bringing together stakeholders involved in production,
                        processing, transportation, export and environmental
                        sustainability throughout Nigeria.

                    </p>

                </div>

                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-fire"></i>
                            <h5>Charcoal Producers</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-store"></i>
                            <h5>Charcoal Dealers</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-ship"></i>
                            <h5>Charcoal Exporters</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-industry"></i>
                            <h5>Charcoal Processors</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <h5>Charcoal Marketers</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-layer-group"></i>
                            <h5>Aggregators</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-truck"></i>
                            <h5>Transporters</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-warehouse"></i>
                            <h5>Warehouse Operators</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-chart-line"></i>
                            <h5>Investors</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-seedling"></i>
                            <h5>Afforestation Practitioners</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-tree"></i>
                            <h5>Forestry Stakeholders</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-people-group"></i>
                            <h5>Cooperatives</h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-handshake"></i>
                            <h5>Community-Based Organizations</h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="committee-card text-center">
                            <i class="fa-solid fa-earth-africa"></i>
                            <h5>International Buyers & Trade Partners</h5>
                        </div>
                    </div>

                </div>

                <div class="alert alert-success text-center mt-5">

                    <i class="fa-solid fa-location-dot me-2"></i>

                    <strong>Operational Coverage:</strong>

                    The Association operates throughout the
                    Federal Republic of Nigeria, serving stakeholders
                    across all states and the Federal Capital Territory.

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
