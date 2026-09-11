@extends('frontend.master')

@section('home')


    <style>
        /* =========================================================
   NACPDEAN COMPLIANCE PAGE
========================================================= */

        .compliance-page {
            background: #f6f8f3;
            color: #4f5750;
        }


        /* =========================================================
   HERO
========================================================= */

        .compliance-hero {
            position: relative;
            overflow: hidden;
            padding: 90px 20px;
            background:
                linear-gradient(135deg,
                    rgba(35, 55, 14, .97),
                    rgba(96, 140, 17, .92)),
                url('../img/forest-bg.jpg') center/cover no-repeat;
        }

        .compliance-hero::before {
            content: "";
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .08);
            top: -180px;
            right: -100px;
        }

        .compliance-hero::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .07);
            bottom: -150px;
            left: -100px;
        }

        .compliance-hero-content {
            position: relative;
            z-index: 2;
            max-width: 950px;
            margin: auto;
        }

        .compliance-label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 18px;
            border-radius: 40px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .25);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .7px;
            margin-bottom: 22px;
        }

        .compliance-hero h1 {
            font-family: "Poppins", sans-serif;
            color: #fff;
            font-size: 45px;
            line-height: 1.2;
            font-weight: 800;
            margin-bottom: 22px;
        }

        .compliance-hero p {
            max-width: 850px;
            margin: auto;
            color: rgba(255, 255, 255, .9);
            font-size: 17px;
            line-height: 1.8;
        }


        /* =========================================================
   MAIN
========================================================= */

        .compliance-wrapper {
            padding: 70px 0;
        }


        /* =========================================================
   INTRO
========================================================= */

        .compliance-intro {
            display: flex;
            gap: 30px;
            background: #fff;
            padding: 42px;
            border-radius: 22px;
            border: 1px solid #e3e9dc;
            box-shadow: 0 12px 40px rgba(0, 0, 0, .06);
            margin-bottom: 35px;
        }

        .compliance-icon {
            flex-shrink: 0;
            width: 70px;
            height: 70px;
            border-radius: 18px;
            background: rgba(96, 140, 17, .1);
            color: #608C11;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 29px;
        }

        .section-eyebrow {
            display: inline-block;
            color: #608C11;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .compliance-intro h2 {
            font-family: "Poppins", sans-serif;
            color: #263414;
            font-size: 29px;
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 20px;
        }

        .compliance-intro p {
            color: #626a63;
            font-size: 15px;
            line-height: 1.9;
            margin-bottom: 15px;
        }

        .compliance-intro p:last-child {
            margin-bottom: 0;
        }


        /* =========================================================
   NAVIGATION
========================================================= */

        .compliance-nav {
            background: #263414;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 45px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .compliance-nav-title {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .compliance-nav-title i {
            color: #b9d77b;
            margin-right: 8px;
        }

        .compliance-nav-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .compliance-nav-grid a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 30px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .1);
            color: rgba(255, 255, 255, .9);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: .3s ease;
        }

        .compliance-nav-grid a:hover {
            background: #608C11;
            color: #fff;
            transform: translateY(-2px);
        }


        /* =========================================================
   SECTION
========================================================= */

        .compliance-section {
            background: #fff;
            border-radius: 22px;
            border: 1px solid #e3e9dc;
            box-shadow: 0 10px 35px rgba(0, 0, 0, .05);
            margin-bottom: 35px;
            overflow: hidden;
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 28px 35px;
            background: #f7f9f4;
            border-bottom: 1px solid #e4eadf;
        }

        .section-heading>div:last-child {
            flex: 1;
        }

        .section-heading span,
        .foreign-header span {
            display: block;
            color: #608C11;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .section-heading h2 {
            margin: 0;
            color: #263414;
            font-family: "Poppins", sans-serif;
            font-size: 24px;
            font-weight: 800;
        }

        .heading-icon {
            width: 55px;
            height: 55px;
            flex-shrink: 0;
            border-radius: 15px;
            background: rgba(96, 140, 17, .12);
            color: #608C11;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .heading-icon.green {
            background: rgba(52, 125, 70, .12);
            color: #347d46;
        }

        .heading-icon.warning {
            background: rgba(219, 151, 28, .12);
            color: #c48818;
        }

        .heading-icon.danger {
            background: rgba(191, 60, 60, .1);
            color: #b83d3d;
        }

        .section-content {
            padding: 35px;
        }

        .section-content>p {
            color: #5e665f;
            font-size: 15px;
            line-height: 1.9;
        }


        /* =========================================================
   TABLES
========================================================= */

        .fee-table-wrapper {
            overflow-x: auto;
            margin-top: 25px;
        }

        .compliance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 600px;
            overflow: hidden;
            border: 1px solid #e1e7db;
            border-radius: 14px;
        }

        .compliance-table thead th {
            background: #608C11;
            color: #fff;
            padding: 16px 18px;
            font-size: 13px;
            font-weight: 700;
            text-align: left;
        }

        .compliance-table tbody td {
            padding: 16px 18px;
            border-bottom: 1px solid #edf0ea;
            color: #535b54;
            font-size: 14px;
            vertical-align: middle;
        }

        .compliance-table tbody tr:last-child td {
            border-bottom: none;
        }

        .compliance-table tbody tr:nth-child(even) {
            background: #fafbf8;
        }

        .compliance-table tbody tr:hover {
            background: #f3f7ed;
        }

        .compliance-table td:last-child {
            color: #608C11;
            font-weight: 800;
            white-space: nowrap;
        }

        .compliance-table small {
            display: block;
            color: #8a918b;
            font-size: 11px;
            line-height: 1.5;
            margin-top: 5px;
        }


        /* =========================================================
   NOTICES
========================================================= */

        .green-notice {
            display: flex;
            gap: 18px;
            align-items: flex-start;
            background: #f2f7ea;
            border: 1px solid #dce8cd;
            border-left: 5px solid #608C11;
            border-radius: 14px;
            padding: 22px;
            margin: 25px 0;
        }

        .green-notice>i {
            color: #608C11;
            font-size: 24px;
            margin-top: 2px;
        }

        .green-notice strong {
            display: block;
            color: #405d13;
            margin-bottom: 6px;
        }

        .green-notice p {
            color: #5e685b;
            margin: 0;
            font-size: 14px;
            line-height: 1.8;
        }

        .warning-box {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: #fff8e9;
            border: 1px solid #f2dfb1;
            border-left: 5px solid #d79a20;
            border-radius: 13px;
            padding: 18px 20px;
            margin: 22px 0;
        }

        .warning-box>i {
            color: #d2941c;
            margin-top: 3px;
        }

        .warning-box p {
            color: #6e5a31;
            font-size: 14px;
            line-height: 1.7;
            margin: 0;
        }

        .note-box {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            margin-top: 25px;
            padding: 20px;
            background: #f7f9f4;
            border-radius: 13px;
        }

        .note-box i {
            color: #608C11;
            margin-top: 3px;
        }

        .note-box p {
            color: #5e665f;
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
        }


        /* =========================================================
   FEE CARDS
========================================================= */

        .fee-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 25px;
        }

        .fee-card {
            padding: 22px;
            border: 1px solid #e3e9dc;
            border-radius: 15px;
            background: #fff;
            transition: .3s ease;
        }

        .fee-card:hover {
            transform: translateY(-4px);
            border-color: #b7cc91;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        }

        .fee-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: #f0f6e8;
            color: #608C11;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .fee-card span {
            display: block;
            min-height: 40px;
            color: #5d665e;
            font-size: 12px;
            line-height: 1.5;
        }

        .fee-card strong {
            display: block;
            margin-top: 10px;
            color: #608C11;
            font-family: "Poppins", sans-serif;
            font-size: 18px;
        }


        /* =========================================================
   PENALTY GRID
========================================================= */

        .penalty-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 25px;
        }

        .penalty-grid>div {
            padding: 22px;
            background: #fffaf0;
            border: 1px solid #f0dfb9;
            border-radius: 15px;
        }

        .penalty-grid span {
            display: block;
            color: #766747;
            font-size: 12px;
            margin-bottom: 9px;
        }

        .penalty-grid strong {
            color: #bd7d0c;
            font-size: 20px;
            font-weight: 800;
        }

        .danger-grid>div {
            background: #fff6f6;
            border-color: #efd5d5;
        }

        .danger-grid strong {
            color: #b43838;
        }


        /* =========================================================
   ADMIN CHARGE
========================================================= */

        .admin-charge-box {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-top: 30px;
            padding: 25px;
            border-radius: 18px;
            background: linear-gradient(135deg, #263414, #405c17);
            color: #fff;
        }

        .admin-charge-icon {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            border-radius: 15px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .admin-charge-box span {
            color: #cde0a8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-charge-box h3 {
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            margin: 5px 0 8px;
        }

        .admin-charge-box p {
            color: rgba(255, 255, 255, .82);
            margin: 0;
            font-size: 13px;
            line-height: 1.7;
        }


        /* =========================================================
   FOREIGN NATIONALS
========================================================= */

        .foreign-section {
            border-color: #d8dce0;
        }

        .foreign-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 30px 35px;
            background: linear-gradient(135deg, #263747, #314e60);
            color: #fff;
        }

        .foreign-header span {
            color: #b9d7e6;
        }

        .foreign-header h2 {
            color: #fff;
            font-family: "Poppins", sans-serif;
            font-size: 25px;
            font-weight: 800;
            margin: 0;
        }

        .foreign-icon {
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            border-radius: 15px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .foreign-subsection {
            display: grid;
            grid-template-columns: 55px 1fr;
            gap: 20px;
            padding: 35px;
            border-bottom: 1px solid #e8ebed;
        }

        .foreign-subsection:last-child {
            border-bottom: none;
        }

        .subsection-number {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #eef3f6;
            color: #315c72;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        .foreign-subsection h3 {
            color: #263747;
            font-family: "Poppins", sans-serif;
            font-size: 20px;
            font-weight: 750;
            margin-bottom: 15px;
        }

        .foreign-subsection p {
            color: #5f676d;
            font-size: 14px;
            line-height: 1.85;
        }

        .sanction-list {
            list-style: none;
            padding: 0;
            margin: 22px 0 0;
        }

        .sanction-list li {
            display: flex;
            gap: 13px;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid #edf0f2;
            color: #5b646a;
            font-size: 14px;
            line-height: 1.7;
        }

        .sanction-list li:last-child {
            border-bottom: none;
        }

        .sanction-list li i {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #edf5e5;
            color: #608C11;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            margin-top: 2px;
        }

        .sanction-list .major-sanction {
            background: #fff8ed;
            border: 1px solid #f0dfbe;
            border-radius: 12px;
            padding: 16px;
            color: #674f25;
            margin-top: 8px;
        }

        .sanction-list .major-sanction i {
            background: #f6e6c5;
            color: #b67b12;
        }


        /* =========================================================
   ENFORCEMENT
========================================================= */

        .enforcement-section {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background:
                linear-gradient(135deg, #263414, #496b0c);
            padding: 55px;
            color: #fff;
            margin-bottom: 35px;
        }

        .enforcement-section::after {
            content: "\f0e3";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 30px;
            bottom: -70px;
            font-size: 250px;
            color: rgba(255, 255, 255, .045);
        }

        .enforcement-content {
            position: relative;
            z-index: 2;
        }

        .enforcement-icon {
            width: 65px;
            height: 65px;
            border-radius: 17px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            margin-bottom: 22px;
        }

        .enforcement-section .section-eyebrow {
            color: #cde2a7;
        }

        .enforcement-section h2 {
            font-family: "Poppins", sans-serif;
            color: #fff;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .enforcement-section>.enforcement-content>p {
            max-width: 950px;
            color: rgba(255, 255, 255, .88);
            font-size: 15px;
            line-height: 1.9;
        }

        .enforcement-steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 35px;
        }

        .enforcement-step {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 20px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 15px;
        }

        .enforcement-step>span {
            width: 35px;
            height: 35px;
            flex-shrink: 0;
            border-radius: 9px;
            background: rgba(255, 255, 255, .14);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
        }

        .enforcement-step strong {
            display: block;
            color: #fff;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .enforcement-step p {
            color: rgba(255, 255, 255, .7);
            font-size: 11px;
            line-height: 1.6;
            margin: 0;
        }


        /* =========================================================
   FINAL
========================================================= */

        .final-compliance {
            text-align: center;
            background: #fff;
            border: 1px solid #e3e9dc;
            border-radius: 22px;
            padding: 50px 30px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, .05);
        }

        .final-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 18px;
            border-radius: 17px;
            background: #eef5e6;
            color: #608C11;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .final-compliance h2 {
            color: #263414;
            font-family: "Poppins", sans-serif;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .final-compliance p {
            max-width: 800px;
            margin: auto;
            color: #656d66;
            font-size: 15px;
            line-height: 1.9;
        }

        .final-tags {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 25px;
        }

        .final-tags span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 15px;
            border-radius: 30px;
            background: #f2f7eb;
            color: #547713;
            border: 1px solid #dfe9d1;
            font-size: 12px;
            font-weight: 700;
        }


        /* =========================================================
   RESPONSIVE
========================================================= */

        @media (max-width: 991px) {

            .compliance-hero {
                padding: 75px 20px;
            }

            .compliance-hero h1 {
                font-size: 38px;
            }

            .fee-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .penalty-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .enforcement-steps {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 767px) {

            .compliance-wrapper {
                padding: 45px 0;
            }

            .compliance-hero {
                padding: 60px 20px;
            }

            .compliance-hero h1 {
                font-size: 30px;
            }

            .compliance-hero p {
                font-size: 14px;
            }

            .compliance-intro {
                display: block;
                padding: 25px;
            }

            .compliance-icon {
                margin-bottom: 20px;
            }

            .compliance-intro h2 {
                font-size: 23px;
            }

            .section-heading {
                padding: 22px;
                gap: 15px;
            }

            .section-heading h2 {
                font-size: 20px;
            }

            .heading-icon {
                width: 48px;
                height: 48px;
                font-size: 19px;
            }

            .section-content {
                padding: 25px 20px;
            }

            .compliance-nav {
                padding: 20px;
            }

            .compliance-nav-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .compliance-nav-grid a {
                justify-content: center;
            }

            .fee-cards {
                grid-template-columns: 1fr;
            }

            .penalty-grid {
                grid-template-columns: 1fr 1fr;
            }

            .admin-charge-box {
                align-items: flex-start;
            }

            .foreign-header {
                padding: 25px 20px;
            }

            .foreign-header h2 {
                font-size: 20px;
            }

            .foreign-subsection {
                grid-template-columns: 1fr;
                padding: 25px 20px;
                gap: 15px;
            }

            .subsection-number {
                width: 40px;
                height: 40px;
            }

            .enforcement-section {
                padding: 40px 25px;
            }

            .enforcement-section h2 {
                font-size: 26px;
            }

            .enforcement-steps {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 575px) {

            .compliance-nav-grid {
                grid-template-columns: 1fr;
            }

            .penalty-grid {
                grid-template-columns: 1fr;
            }

            .admin-charge-box {
                display: block;
            }

            .admin-charge-icon {
                margin-bottom: 15px;
            }

            .final-compliance {
                padding: 40px 20px;
            }

        }
    </style>

</head>

<main class="main compliance-page">

    <!-- =====================================================
         HERO
    ====================================================== -->
<section class="compliance-hero">

    <div class="container">

        <!-- =================================================
             HERO HEADER
        ================================================== -->
        <div class="compliance-hero-content text-center mb-5" data-aos="fade-up">

            <span class="compliance-label">
                <i class="fas fa-shield-halved"></i>
                NACPDEAN Compliance Framework
            </span>

            <h1 class="mt-3 fw-bold">
                Membership, Afforestation<br class="d-none d-md-block">
                &amp; Regulatory Compliance
            </h1>

            <p class="lead max-w-800 mx-auto mt-3">
                The National Association of Charcoal Producers, Dealers, Exporters and Afforestation of Nigeria (NACPDEAN), in furtherance of its mandate to regulate the charcoal value chain, promote sustainable forest management, strengthen industry self-regulation, ensure compliance with afforestation obligations, and uphold the resolutions and policies of the Federal Government of Nigeria, hereby issues the following Membership Registration, Afforestation Compliance Fees, Regulatory Charges, Enforcement Procedures and Penalties.
            </p>

            <div class="alert alert-info d-inline-block mt-3 px-4 py-2" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                These provisions shall apply to all producers, dealers, suppliers, exporters, transporters and every stakeholder operating within the charcoal industry in Nigeria.
            </div>

        </div>


        <!-- =================================================
             1. MEMBERSHIP REGISTRATION
        ================================================== -->
        <div class="compliance-section mb-5" data-aos="fade-up">
            <div class="section-title-wrapper mb-4">
                <span class="badge bg-success mb-2">Section 01</span>
                <h2>Membership Registration</h2>
                <p class="text-muted">Every individual or company participating in the charcoal value chain shall register under the appropriate membership category before commencing operations.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper mb-3 text-success fs-2">
                                <i class="fas fa-file-export"></i>
                            </div>
                            <h4 class="card-title h5 fw-bold">Exporter</h4>
                            <p class="fs-4 text-success fw-bold my-2">₦50,000 – ₦100,000</p>
                            <p class="card-text small text-muted">₦50,000.00 for existing members and ₦100,000.00 for new members.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper mb-3 text-success fs-2">
                                <i class="fas fa-boxes-packing"></i>
                            </div>
                            <h4 class="card-title h5 fw-bold">Supplier</h4>
                            <p class="fs-4 text-success fw-bold my-2">₦30,000.00</p>
                            <p class="card-text small text-muted">Refers to any individual or company that processes, assembles or prepares charcoal and related products to export standards for exporters.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper mb-3 text-success fs-2">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4 class="card-title h5 fw-bold">Dealer</h4>
                            <p class="fs-4 text-success fw-bold my-2">₦10,000.00</p>
                            <p class="card-text small text-muted">Standard registration rate for registered charcoal dealers.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <div class="icon-wrapper mb-3 text-success fs-2">
                                <i class="fas fa-industry"></i>
                            </div>
                            <h4 class="card-title h5 fw-bold">Producer</h4>
                            <p class="fs-4 text-success fw-bold my-2">₦10,000.00</p>
                            <p class="card-text small text-muted">Standard registration fee for primary charcoal producers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- =================================================
             2. AFFORESTATION FEE PAYMENT
        ================================================== -->
        <div class="compliance-section mb-5" data-aos="fade-up">
            <div class="section-title-wrapper mb-4">
                <span class="badge bg-success mb-2">Section 02</span>
                <h2>Afforestation Fee Payment for All Categories of Members</h2>
                <p class="text-muted">In line with NACPDEAN's Afforestation Compliance Programme and environmental sustainability initiatives, every registered member shall pay the prescribed Afforestation Fee (Tree Planting Fee) according to the category of charcoal being transported or exported.</p>
            </div>

            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Transport / Export Category</th>
                            <th scope="col" class="text-end">Prescribed Afforestation Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-box-archive me-2 text-success"></i> Afforestation Fee for Export Container</td>
                            <td class="text-end fw-bold">₦100,000.00</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-truck-ramp-box me-2 text-success"></i> Afforestation Fee for Trailer Load / Truck</td>
                            <td class="text-end fw-bold">₦50,000.00</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-truck-moving me-2 text-success"></i> Afforestation Fee for 10-Tyre Truck</td>
                            <td class="text-end fw-bold">₦25,000.00</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-truck me-2 text-success"></i> Afforestation Fee for 6-Tyre Truck</td>
                            <td class="text-end fw-bold">₦20,000.00</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-truck-pickup me-2 text-success"></i> Afforestation Fee for Charcoal Attachment to Truck/Trailer</td>
                            <td class="text-end fw-bold">₦15,000.00</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-van-shuttle me-2 text-success"></i> Afforestation Fee for Small Vehicles (20 to 80 Bags)</td>
                            <td class="text-end fw-bold">₦10,000.00</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-car me-2 text-success"></i> Afforestation Fee for Vehicles (5 to 20 Bags)</td>
                            <td class="text-end fw-bold">₦5,000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- =================================================
             3. PENALTIES FOR REGISTRATION & COMPLIANCE VIOLATIONS
        ================================================== -->
        <div class="compliance-section mb-5" data-aos="fade-up">
            <div class="section-title-wrapper mb-4">
                <span class="badge bg-warning text-dark mb-2">Section 03</span>
                <h2>Registration &amp; Operating Penalties</h2>
            </div>

            <div class="row g-4">
                <!-- Operational Right Penalties -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-warning bg-opacity-10 border-0 pt-3">
                            <h3 class="h5 fw-bold text-dark mb-0">
                                <i class="fas fa-user-clock me-2 text-warning"></i>
                                Members Without Operational Right
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Applies to members who previously registered as compliance members but failed to renew or reactivate their operational membership in subsequent years. Operations are restricted until paid.
                            </p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Exporter Penalty <span class="fw-bold">₦100,000.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Supplier Penalty <span class="fw-bold">₦50,000.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Dealer Penalty <span class="fw-bold">₦15,000.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Producer Penalty <span class="fw-bold">₦15,000.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Unregistered Operations Penalties -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-danger bg-opacity-10 border-0 pt-3">
                            <h3 class="h5 fw-bold text-dark mb-0">
                                <i class="fas fa-user-xmark me-2 text-danger"></i>
                                Operating Without Membership
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Applies to any person or company engaged in charcoal activities without valid NACPDEAN registration, violating the Association's Industry Self-Regulation Policy.
                            </p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Exporter Penalty <span class="fw-bold text-danger">₦200,000.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Supplier Penalty <span class="fw-bold text-danger">₦60,000.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Dealer Penalty <span class="fw-bold text-danger">₦20,000.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Producer Penalty <span class="fw-bold text-danger">₦20,000.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- =================================================
             4. PENALTIES FOR NON-PAYMENT OF AFFORESTATION
        ================================================== -->
        <div class="compliance-section mb-5" data-aos="fade-up">
            <div class="section-title-wrapper mb-4">
                <span class="badge bg-danger mb-2">Section 04</span>
                <h2>Penalties for Non-Payment of Afforestation Fees</h2>
            </div>

            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Transport / Export Category</th>
                            <th scope="col" class="text-center text-warning fw-bold">Non-Payment Penalty (Registered Members)</th>
                            <th scope="col" class="text-center text-danger fw-bold">Non-Payment Penalty (Non-Members)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Export Container</td>
                            <td class="text-center">₦150,000.00</td>
                            <td class="text-center text-danger fw-bold">₦200,000.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Trailer Load / Truck</td>
                            <td class="text-center">₦75,000.00</td>
                            <td class="text-center text-danger fw-bold">₦100,000.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">10-Tyre Truck</td>
                            <td class="text-center">₦37,500.00</td>
                            <td class="text-center text-danger fw-bold">₦50,000.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">6-Tyre Truck</td>
                            <td class="text-center">₦30,000.00</td>
                            <td class="text-center text-danger fw-bold">₦40,000.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Truck/Trailer Charcoal Attachment</td>
                            <td class="text-center">₦22,500.00</td>
                            <td class="text-center text-danger fw-bold">₦30,000.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Small Vehicle (20 to 80 Bags)</td>
                            <td class="text-center">₦15,000.00</td>
                            <td class="text-center text-danger fw-bold">₦20,000.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Vehicle (5 to 20 Bags)</td>
                            <td class="text-center">₦7,500.00</td>
                            <td class="text-center text-danger fw-bold">₦10,000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- =================================================
             5. TASK FORCE CHECKPOINT PENALTIES
        ================================================== -->
        <div class="compliance-section mb-5" data-aos="fade-up">
            <div class="section-title-wrapper mb-4">
                <span class="badge bg-secondary mb-2">Section 05</span>
                <h2>Checkpoint Non-Compliance Penalties</h2>
                <p class="text-muted">Applies solely to vehicles, trucks, and trailers stopped or failing to comply at NACPDEAN Task Force checkpoints. Every stakeholder is responsible for informing transporters and drivers before dispatching consignments.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                        <span>Truck Carrying Export Container</span>
                        <span class="fw-bold text-dark">₦50,000.00</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                        <span>Trailer Load / Truck</span>
                        <span class="fw-bold text-dark">₦20,000.00</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                        <span>10-Tyre Truck</span>
                        <span class="fw-bold text-dark">₦20,000.00</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                        <span>6-Tyre Truck</span>
                        <span class="fw-bold text-dark">₦20,000.00</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                        <span>Charcoal Attachment Unit</span>
                        <span class="fw-bold text-dark">₦20,000.00</span>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                        <span>Vehicles (5 to 80 Bags)</span>
                        <span class="fw-bold text-dark">₦5,000.00</span>
                    </div>
                </div>
            </div>

            <!-- Important Executive Note -->
            <div class="alert alert-warning mt-4 p-3 border-warning" role="alert">
                <h5 class="alert-heading fw-bold mb-2">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    Executive Conflict Surcharge
                </h5>
                <p class="mb-0 small text-dark">
                    Where any vehicle, truck or trailer is intercepted transporting charcoal belonging to any <strong>National Executive Member, State Executive Member, National Task Force Member, or State Task Force Member</strong>, an additional administrative charge of <strong>₦20,000.00</strong> shall be imposed. NACPDEAN expects every stakeholder within these categories to properly educate their drivers and transporters on the Association's operational guidelines and ensure full compliance.
                </p>
            </div>
        </div>


        <!-- =================================================
             6. VIOLATIONS INVOLVING FOREIGN NATIONALS
        ================================================== -->
        <div class="compliance-section mb-5" data-aos="fade-up">
            <div class="section-title-wrapper mb-4">
                <span class="badge bg-dark mb-2">Section 06</span>
                <h2 class="text-danger">Penalties for Violations Involving Foreign Nationals</h2>
                <p class="text-muted">Strict compliance regulations in accordance with Federal Government directives prohibiting foreign participation in primary procurement and reserved activities.</p>
            </div>

            <div class="accordion shadow-sm" id="foreignViolationsAccordion">

                <!-- Violation 1 -->
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fas fa-tree-city me-2 text-danger"></i>
                            Unlawful Encroachment into Forests, Farm Gates &amp; Processing Factories
                        </button>
                    </h3>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#foreignViolationsAccordion">
                        <div class="accordion-body">
                            <p class="small text-muted">
                                In accordance with Federal Government resolutions prohibiting foreign nationals from purchasing agricultural produce directly at farm gates and participating in activities reserved for Nigerian citizens, foreign nationals found entering forests, farm gates, owning/operating charcoal processing factories, or physically participating in procurement or deforestation shall face:
                            </p>
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item"><i class="fas fa-handcuffs me-2 text-danger"></i> Immediate apprehension and handover to the <strong>Nigeria Immigration Service (NIS)</strong> and security agencies.</li>
                                <li class="list-group-item"><i class="fas fa-building-flag me-2 text-danger"></i> Formal report submitted to the <strong>Federal Ministry of Foreign Affairs</strong>.</li>
                                <li class="list-group-item"><i class="fas fa-passport me-2 text-danger"></i> Official notification issued to the Embassy or High Commission of the foreign national's country.</li>
                                <li class="list-group-item"><i class="fas fa-ban me-2 text-danger"></i> Confiscation of all charcoal, equipment, machinery, vehicles, materials, and commodities.</li>
                                <li class="list-group-item"><i class="fas fa-gavel me-2 text-danger"></i> Any Nigerian citizen aiding, harbouring, financing, or collaborating with the foreign national shall face disciplinary measures and penalties.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Violation 2 -->
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="fas fa-warehouse me-2 text-danger"></i>
                            Unauthorized Presence of Foreign Nationals at Warehouses
                        </button>
                    </h3>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#foreignViolationsAccordion">
                        <div class="accordion-body">
                            <p class="small text-muted">
                                Where a Nigerian exporter or stakeholder maintains a lawful business relationship with a foreign national, such foreign national <strong>shall not be physically present</strong> at any warehouse, storage facility, charcoal collection centre, or commodity aggregation point. Violations incur:
                            </p>
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item"><i class="fas fa-lock me-2 text-danger"></i> Immediate sealing of the warehouse or facility by NACPDEAN pending investigation.</li>
                                <li class="list-group-item"><i class="fas fa-user-slash me-2 text-danger"></i> Immediate blacklisting of the affected member by the Association.</li>
                                <li class="list-group-item"><i class="fas fa-file-invoice-dollar me-2 text-danger"></i> Payment of a <strong>₦500,000.00 Warehouse Reactivation Fee</strong> before unsealing and removal from blacklist.</li>
                                <li class="list-group-item"><i class="fas fa-boxes-stacked me-2 text-danger"></i> Confiscation of all charcoal commodities connected with the violation.</li>
                                <li class="list-group-item"><i class="fas fa-scale-balanced me-2 text-danger"></i> Referral to the Nigeria Immigration Service, Ministry of Foreign Affairs, and relevant Embassies.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Violation 3 -->
                <div class="accordion-item border-0">
                    <h3 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="fas fa-handshake-slash me-2 text-danger"></i>
                            Unlawful Partnerships with Suppliers, Dealers, or Producers
                        </button>
                    </h3>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#foreignViolationsAccordion">
                        <div class="accordion-body">
                            <p class="small text-muted">
                                Any supplier, dealer, or producer without valid export documentation who enters into an unlawful partnership or business arrangement with a foreign national shall be penalized as follows:
                            </p>
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item"><i class="fas fa-user-shield me-2 text-danger"></i> Foreign national handed over to the Nigeria Immigration Service &amp; diplomatic notification issued.</li>
                                <li class="list-group-item"><i class="fas fa-ban me-2 text-danger"></i> Immediate blacklisting of the Nigerian supplier, dealer, or producer by NACPDEAN.</li>
                                <li class="list-group-item"><i class="fas fa-box me-2 text-danger"></i> Seizure of all goods, equipment, and charcoal commodities connected with the unlawful activity.</li>
                                <li class="list-group-item"><i class="fas fa-money-bill-wave me-2 text-danger"></i> Payment of an administrative penalty of <strong>₦500,000.00</strong> by the Nigerian participant, without prejudice to further legal prosecution under federal laws.</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!-- =================================================
             7. ENFORCEMENT PROCEDURE
        ================================================== -->
        <div class="compliance-section" data-aos="fade-up">
            <div class="card bg-dark text-white p-4 shadow-lg border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-shield-cat fs-2 me-3 text-warning"></i>
                        <h3 class="h4 card-title fw-bold mb-0">Enforcement Procedure for Non-Compliance</h3>
                    </div>
                    <p class="card-text text-light">
                        All defaulters who fail to comply with this payment policy or who refuse to cooperate with members of the NACPDEAN National Task Force or State Task Force during the course of their lawful duties shall have their vehicles, trucks or trailers escorted to the nearest <strong>Nigeria Police Force Station</strong> or the nearest <strong>Nigeria Security and Civil Defence Corps (NSCDC) Command</strong> within the area of operation.
                    </p>
                    <p class="card-text text-light mb-0">
                        The matter shall thereafter be processed in accordance with the applicable laws of the Federal Republic of Nigeria, the Association's Industry Self-Regulation Policy, and all other relevant regulatory procedures. Any stakeholder who obstructs, resists or interferes with the lawful enforcement of this policy shall be liable to any additional sanctions or legal actions deemed appropriate by the competent authorities.
                    </p>
                </div>
            </div>
        </div>

    </div>

</section>


    <!-- =====================================================
         CONTENT
    ====================================================== -->
    <section class="compliance-wrapper">

        <div class="container">


            <!-- =================================================
                 INTRODUCTION
            ================================================== -->
            <div class="compliance-intro" data-aos="fade-up">

                <div class="compliance-icon">
                    <i class="fas fa-scale-balanced"></i>
                </div>

                <div>

                    <span class="section-eyebrow">
                        Regulatory Framework
                    </span>

                    <h2>
                        NACPDEAN Membership Registration,
                        Afforestation Compliance, Regulatory Fees,
                        Enforcement Procedures and Penalties
                    </h2>

                    <p>
                        The <strong>National Association of Charcoal Producers,
                            Dealers, Exporters and Afforestation of Nigeria
                            (NACPDEAN)</strong>, in furtherance of its mandate to
                        regulate the charcoal value chain, promote sustainable
                        forest management, strengthen industry self-regulation,
                        ensure compliance with afforestation obligations, and
                        uphold the resolutions and policies of the Federal
                        Government of Nigeria, hereby issues the following
                        Membership Registration, Afforestation Compliance Fees,
                        Regulatory Charges, Enforcement Procedures and
                        Penalties.
                    </p>

                    <p>
                        These provisions shall apply to all
                        <strong>producers, dealers, suppliers, exporters,
                            transporters and every stakeholder</strong> operating
                        within the charcoal industry in Nigeria.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 QUICK NAVIGATION
            ================================================== -->
            <div class="compliance-nav" data-aos="fade-up">

                <div class="compliance-nav-title">
                    <i class="fas fa-list-check"></i>
                    Compliance Sections
                </div>

                <div class="compliance-nav-grid">

                    <a href="#membership">
                        <i class="fas fa-user-plus"></i>
                        Membership
                    </a>

                    <a href="#afforestation">
                        <i class="fas fa-seedling"></i>
                        Afforestation
                    </a>

                    <a href="#operational-right">
                        <i class="fas fa-ban"></i>
                        Operational Right
                    </a>

                    <a href="#non-membership">
                        <i class="fas fa-user-slash"></i>
                        Non-Membership
                    </a>

                    <a href="#vehicle">
                        <i class="fas fa-truck"></i>
                        Vehicle Compliance
                    </a>

                    <a href="#foreign-national">
                        <i class="fas fa-earth-africa"></i>
                        Foreign Nationals
                    </a>

                    <a href="#enforcement">
                        <i class="fas fa-gavel"></i>
                        Enforcement
                    </a>

                </div>

            </div>


            <!-- =================================================
                 MEMBERSHIP REGISTRATION
            ================================================== -->
            <section id="membership"
                class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading">

                    <div class="heading-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>

                    <div>
                        <span>Section 01</span>
                        <h2>Membership Registration</h2>
                    </div>

                </div>

                <div class="section-content">

                    <p>
                        Every individual or company participating in the
                        charcoal value chain shall register under the
                        appropriate membership category before commencing
                        operations.
                    </p>


                    <div class="fee-table-wrapper">

                        <table class="compliance-table">

                            <thead>
                                <tr>
                                    <th>Membership Category</th>
                                    <th>Existing Member</th>
                                    <th>New Member</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>
                                        <strong>
                                            <i class="fas fa-globe me-2"></i>
                                            Exporter
                                        </strong>
                                    </td>
                                    <td>₦50,000.00</td>
                                    <td>₦100,000.00</td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>
                                            <i class="fas fa-boxes-stacked me-2"></i>
                                            Supplier
                                        </strong>

                                        <small>
                                            Processes, assembles or prepares
                                            charcoal and related products to
                                            export standards.
                                        </small>
                                    </td>
                                    <td colspan="2">₦30,000.00</td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>
                                            <i class="fas fa-store me-2"></i>
                                            Dealer
                                        </strong>
                                    </td>
                                    <td colspan="2">₦10,000.00</td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>
                                            <i class="fas fa-industry me-2"></i>
                                            Producer
                                        </strong>
                                    </td>
                                    <td colspan="2">₦10,000.00</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 AFFORESTATION
            ================================================== -->
            <section id="afforestation"
                class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading">

                    <div class="heading-icon green">
                        <i class="fas fa-seedling"></i>
                    </div>

                    <div>
                        <span>Section 02</span>
                        <h2>Afforestation Fee Payment</h2>
                    </div>

                </div>

                <div class="section-content">

                    <div class="green-notice">

                        <i class="fas fa-tree"></i>

                        <div>

                            <strong>
                                Afforestation Compliance Programme
                            </strong>

                            <p>
                                In line with NACPDEAN's Afforestation Compliance
                                Programme and environmental sustainability
                                initiatives, every registered member shall pay
                                the prescribed Afforestation Fee (Tree Planting
                                Fee) according to the category of charcoal
                                being transported or exported.
                            </p>

                        </div>

                    </div>


                    <div class="fee-cards">

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-container-storage"></i>
                            </div>
                            <span>Export Container</span>
                            <strong>₦100,000.00</strong>
                        </div>

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <span>Charcoal Attachment to Truck/Trailer</span>
                            <strong>₦15,000.00</strong>
                        </div>

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-truck-moving"></i>
                            </div>
                            <span>Trailer Load / Truck</span>
                            <strong>₦50,000.00</strong>
                        </div>

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-truck-pickup"></i>
                            </div>
                            <span>6-Tyre Truck</span>
                            <strong>₦20,000.00</strong>
                        </div>

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <span>10-Tyre Truck</span>
                            <strong>₦25,000.00</strong>
                        </div>

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <span>20–80 Bags</span>
                            <strong>₦10,000.00</strong>
                        </div>

                        <div class="fee-card">
                            <div class="fee-card-icon">
                                <i class="fas fa-car-side"></i>
                            </div>
                            <span>5–20 Bags</span>
                            <strong>₦5,000.00</strong>
                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 OPERATIONAL RIGHT
            ================================================== -->
            <section id="operational-right"
                class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading warning-heading">

                    <div class="heading-icon warning">
                        <i class="fas fa-ban"></i>
                    </div>

                    <div>
                        <span>Section 03</span>
                        <h2>Penalties for Members Without Operational Right</h2>
                    </div>

                </div>

                <div class="section-content">

                    <p>
                        This category applies to members who previously
                        registered with NACPDEAN as compliance members but
                        failed to renew or reactivate their operational
                        membership in subsequent years.
                    </p>

                    <div class="warning-box">
                        <i class="fas fa-triangle-exclamation"></i>

                        <p>
                            Such members shall not be permitted to continue
                            operations until the applicable penalty has been
                            paid.
                        </p>
                    </div>


                    <div class="penalty-grid">

                        <div>
                            <span>Exporter</span>
                            <strong>₦100,000.00</strong>
                        </div>

                        <div>
                            <span>Supplier</span>
                            <strong>₦50,000.00</strong>
                        </div>

                        <div>
                            <span>Dealer</span>
                            <strong>₦15,000.00</strong>
                        </div>

                        <div>
                            <span>Producer</span>
                            <strong>₦15,000.00</strong>
                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 NON MEMBERSHIP
            ================================================== -->
            <section id="non-membership"
                class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading danger-heading">

                    <div class="heading-icon danger">
                        <i class="fas fa-user-slash"></i>
                    </div>

                    <div>
                        <span>Section 04</span>
                        <h2>Operating Without Membership Registration</h2>
                    </div>

                </div>

                <div class="section-content">

                    <p>
                        Any person or company engaged in charcoal production,
                        processing, supply, dealing or export activities
                        without valid registration with NACPDEAN shall be
                        considered to be operating in violation of the
                        Association's Industry Self-Regulation Policy.
                    </p>


                    <div class="penalty-grid danger-grid">

                        <div>
                            <span>Exporter</span>
                            <strong>₦200,000.00</strong>
                        </div>

                        <div>
                            <span>Supplier</span>
                            <strong>₦60,000.00</strong>
                        </div>

                        <div>
                            <span>Dealer</span>
                            <strong>₦20,000.00</strong>
                        </div>

                        <div>
                            <span>Producer</span>
                            <strong>₦20,000.00</strong>
                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 AFFORESTATION MEMBER PENALTIES
            ================================================== -->
            <section class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading warning-heading">

                    <div class="heading-icon warning">
                        <i class="fas fa-tree"></i>
                    </div>

                    <div>
                        <span>Section 05</span>
                        <h2>Non-Payment of Afforestation by Members</h2>
                    </div>

                </div>

                <div class="section-content">

                    <p>
                        This category applies to registered members who possess
                        valid NACPDEAN membership but fail to pay the prescribed
                        Afforestation Compliance Fee (Tree Planting Fee).
                    </p>


                    <div class="fee-table-wrapper">

                        <table class="compliance-table penalty-table">

                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Penalty</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>Export Container</td>
                                    <td>₦150,000.00</td>
                                </tr>

                                <tr>
                                    <td>Truck/Trailer Carrying Charcoal Attachment</td>
                                    <td>₦22,500.00</td>
                                </tr>

                                <tr>
                                    <td>Trailer Load / Truck</td>
                                    <td>₦75,000.00</td>
                                </tr>

                                <tr>
                                    <td>6-Tyre Truck</td>
                                    <td>₦30,000.00</td>
                                </tr>

                                <tr>
                                    <td>10-Tyre Truck</td>
                                    <td>₦37,500.00</td>
                                </tr>

                                <tr>
                                    <td>Small Vehicle — 20–80 Bags</td>
                                    <td>₦15,000.00</td>
                                </tr>

                                <tr>
                                    <td>Vehicle — 5–20 Bags</td>
                                    <td>₦7,500.00</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 NON MEMBER AFFORESTATION PENALTIES
            ================================================== -->
            <section class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading danger-heading">

                    <div class="heading-icon danger">
                        <i class="fas fa-user-shield"></i>
                    </div>

                    <div>
                        <span>Section 06</span>
                        <h2>Non-Payment of Afforestation by Non-Members</h2>
                    </div>

                </div>

                <div class="section-content">

                    <p>
                        This category applies to any individual or company
                        operating within the charcoal industry without
                        NACPDEAN membership and who also fails to comply with
                        the mandatory Afforestation Compliance Policy.
                    </p>


                    <div class="fee-table-wrapper">

                        <table class="compliance-table penalty-table">

                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Penalty</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>Export Container</td>
                                    <td>₦200,000.00</td>
                                </tr>

                                <tr>
                                    <td>Truck/Trailer Carrying Charcoal Attachment</td>
                                    <td>₦30,000.00</td>
                                </tr>

                                <tr>
                                    <td>Trailer Load / Truck</td>
                                    <td>₦100,000.00</td>
                                </tr>

                                <tr>
                                    <td>6-Tyre Truck</td>
                                    <td>₦40,000.00</td>
                                </tr>

                                <tr>
                                    <td>10-Tyre Truck</td>
                                    <td>₦50,000.00</td>
                                </tr>

                                <tr>
                                    <td>Small Vehicle — 20–80 Bags</td>
                                    <td>₦20,000.00</td>
                                </tr>

                                <tr>
                                    <td>Vehicle — 5–20 Bags</td>
                                    <td>₦10,000.00</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 VEHICLE / TASK FORCE
            ================================================== -->
            <section id="vehicle"
                class="compliance-section"
                data-aos="fade-up">

                <div class="section-heading">

                    <div class="heading-icon">
                        <i class="fas fa-truck"></i>
                    </div>

                    <div>
                        <span>Section 07</span>
                        <h2>Vehicle & Task Force Checkpoint Compliance</h2>
                    </div>

                </div>

                <div class="section-content">

                    <p>
                        These penalties apply solely to vehicles, trucks and
                        trailers transporting charcoal on behalf of
                        stakeholders.
                    </p>

                    <div class="green-notice">

                        <i class="fas fa-circle-info"></i>

                        <div>

                            <strong>Important Responsibility</strong>

                            <p>
                                Every stakeholder is responsible for informing
                                transporters and drivers of these regulations
                                before dispatching charcoal consignments.
                            </p>

                        </div>

                    </div>


                    <div class="fee-table-wrapper">

                        <table class="compliance-table">

                            <thead>
                                <tr>
                                    <th>Vehicle / Load Category</th>
                                    <th>Penalty</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>Truck Carrying Export Container</td>
                                    <td>₦50,000.00</td>
                                </tr>

                                <tr>
                                    <td>
                                        Truck/Trailer Carrying Charcoal
                                        Attachment
                                    </td>
                                    <td>₦20,000.00</td>
                                </tr>

                                <tr>
                                    <td>Trailer Load / Truck</td>
                                    <td>₦20,000.00</td>
                                </tr>

                                <tr>
                                    <td>6-Tyre Truck</td>
                                    <td>₦20,000.00</td>
                                </tr>

                                <tr>
                                    <td>10-Tyre Truck</td>
                                    <td>₦20,000.00</td>
                                </tr>

                                <tr>
                                    <td>Vehicles Carrying 5–80 Bags</td>
                                    <td>₦5,000.00</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <!-- Administrative Charge -->
                    <div class="admin-charge-box">

                        <div class="admin-charge-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>

                        <div>

                            <span>
                                Additional Administrative Charge
                            </span>

                            <h3>
                                ₦20,000.00
                            </h3>

                            <p>
                                Where any vehicle, truck or trailer is
                                intercepted transporting charcoal belonging
                                to any National Executive Member, State
                                Executive Member, National Task Force Member
                                or State Task Force Member, an additional
                                administrative charge of ₦20,000.00 shall be
                                imposed.
                            </p>

                        </div>

                    </div>


                    <div class="note-box">

                        <i class="fas fa-bullhorn"></i>

                        <p>
                            <strong>Kindly Note:</strong>
                            NACPDEAN expects every stakeholder within these
                            categories to properly educate their drivers and
                            transporters on the Association's operational
                            guidelines and ensure full compliance.
                        </p>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 FOREIGN NATIONALS
            ================================================== -->
            <section id="foreign-national"
                class="compliance-section foreign-section"
                data-aos="fade-up">

                <div class="foreign-header">

                    <div class="foreign-icon">
                        <i class="fas fa-earth-africa"></i>
                    </div>

                    <div>

                        <span>Section 08</span>

                        <h2>
                            Penalties for Violations Involving
                            Foreign Nationals
                        </h2>

                    </div>

                </div>


                <!-- Encroachment -->
                <div class="foreign-subsection">

                    <div class="subsection-number">
                        01
                    </div>

                    <div>

                        <h3>
                            Unlawful Encroachment by Foreign Nationals
                        </h3>

                        <p>
                            In accordance with the resolutions of the Federal
                            Government of Nigeria prohibiting foreign nationals
                            from purchasing agricultural produce directly at
                            farm gates and participating in activities reserved
                            for Nigerian citizens, any foreign national found
                            entering forests, farm gates, owning or operating
                            charcoal processing factories, or physically
                            participating in charcoal production, charcoal
                            procurement or deforestation activities shall be
                            subject to the following sanctions:
                        </p>


                        <ul class="sanction-list">

                            <li>
                                <i class="fas fa-check"></i>
                                The foreign national shall be apprehended and
                                handed over to the Nigeria Immigration Service
                                (NIS) and other relevant security agencies.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The matter shall be reported to the Federal
                                Ministry of Foreign Affairs.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The Embassy or High Commission of the foreign
                                national's country shall be formally notified.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                All charcoal, equipment, machinery, vehicles,
                                materials and commodities connected with the
                                unlawful activity shall be confiscated.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                Any Nigerian citizen, exporter, supplier,
                                dealer, producer or stakeholder found aiding,
                                harbouring, financing or collaborating with
                                the foreign national shall be subject to
                                disciplinary measures and other penalties
                                prescribed under this policy.
                            </li>

                        </ul>

                    </div>

                </div>


                <!-- Warehouse -->
                <div class="foreign-subsection">

                    <div class="subsection-number">
                        02
                    </div>

                    <div>

                        <h3>
                            Unauthorized Presence of Foreign Nationals
                            at Warehouses
                        </h3>

                        <p>
                            Where a Nigerian exporter or other stakeholder
                            maintains a lawful business relationship with a
                            foreign national, such foreign national shall not
                            be physically present at any warehouse, storage
                            facility, charcoal collection centre or commodity
                            aggregation point.
                        </p>


                        <div class="warning-box">

                            <i class="fas fa-triangle-exclamation"></i>

                            <p>
                                Where a foreign national is found in violation
                                of this provision, the following measures
                                shall apply.
                            </p>

                        </div>


                        <ul class="sanction-list">

                            <li>
                                <i class="fas fa-check"></i>
                                The warehouse or facility shall be sealed by
                                NACPDEAN pending investigation.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The affected member shall be blacklisted by
                                the Association.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                Before the warehouse can be reopened and the
                                member's blacklist status removed, a warehouse
                                reactivation fee of <strong>₦500,000.00</strong>
                                shall be paid.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                All charcoal commodities connected with the
                                violation shall be confiscated.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The matter shall be referred to the Nigeria
                                Immigration Service, the Federal Ministry of
                                Foreign Affairs, the Embassy or High Commission
                                of the foreign national's country and other
                                relevant government agencies for appropriate
                                action.
                            </li>

                        </ul>

                    </div>

                </div>


                <!-- Partnership -->
                <div class="foreign-subsection">

                    <div class="subsection-number">
                        03
                    </div>

                    <div>

                        <h3>
                            Partnership Between Suppliers, Dealers,
                            Producers and Foreign Nationals
                        </h3>

                        <p>
                            Any supplier, dealer or producer who does not
                            possess valid export documentation and is found
                            to have entered into an unlawful partnership or
                            business arrangement with a foreign national in
                            violation of this policy shall be subject to the
                            following sanctions:
                        </p>


                        <ul class="sanction-list">

                            <li>
                                <i class="fas fa-check"></i>
                                The foreign national shall be handed over to
                                the Nigeria Immigration Service.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The matter shall be reported to the Federal
                                Ministry of Foreign Affairs.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The Embassy or High Commission of the foreign
                                national's country shall be officially
                                notified.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                The supplier, dealer or producer shall be
                                blacklisted by NACPDEAN.
                            </li>

                            <li>
                                <i class="fas fa-check"></i>
                                All goods, equipment and charcoal commodities
                                connected with the unlawful activity shall
                                be seized.
                            </li>

                            <li class="major-sanction">
                                <i class="fas fa-naira-sign"></i>

                                The Nigerian supplier, dealer or producer
                                shall pay an administrative penalty of
                                <strong>₦500,000.00</strong>, without prejudice
                                to any additional sanctions, prosecution or
                                legal proceedings under the applicable laws of
                                the Federal Republic of Nigeria.
                            </li>

                        </ul>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 ENFORCEMENT
            ================================================== -->
            <section id="enforcement"
                class="enforcement-section"
                data-aos="fade-up">

                <div class="enforcement-content">

                    <div class="enforcement-icon">
                        <i class="fas fa-gavel"></i>
                    </div>

                    <span class="section-eyebrow">
                        Final Regulatory Provision
                    </span>

                    <h2>
                        Enforcement Procedure for Non-Compliance
                    </h2>

                    <p>
                        All defaulters who fail to comply with this payment
                        policy or who refuse to cooperate with members of the
                        NACPDEAN National Task Force or State Task Force during
                        the course of their lawful duties shall have their
                        vehicles, trucks or trailers escorted to the nearest
                        Nigeria Police Force Station or the nearest Nigeria
                        Security and Civil Defence Corps (NSCDC) Command within
                        the area of operation.
                    </p>

                    <p>
                        The matter shall thereafter be processed in accordance
                        with the applicable laws of the Federal Republic of
                        Nigeria, the Association's Industry Self-Regulation
                        Policy, and all other relevant regulatory procedures.
                    </p>

                    <p>
                        Any stakeholder who obstructs, resists or interferes
                        with the lawful enforcement of this policy shall be
                        liable to any additional sanctions or legal actions
                        deemed appropriate by the competent authorities.
                    </p>


                    <div class="enforcement-steps">

                        <div class="enforcement-step">

                            <span>01</span>

                            <div>
                                <strong>Non-Compliance Identified</strong>
                                <p>
                                    A violation or failure to comply with the
                                    applicable policy is identified.
                                </p>
                            </div>

                        </div>


                        <div class="enforcement-step">

                            <span>02</span>

                            <div>
                                <strong>Task Force Intervention</strong>
                                <p>
                                    NACPDEAN National or State Task Force
                                    carries out its lawful enforcement duties.
                                </p>
                            </div>

                        </div>


                        <div class="enforcement-step">

                            <span>03</span>

                            <div>
                                <strong>Vehicle Escort</strong>
                                <p>
                                    Non-compliant vehicles may be escorted to
                                    the appropriate police or NSCDC facility.
                                </p>
                            </div>

                        </div>


                        <div class="enforcement-step">

                            <span>04</span>

                            <div>
                                <strong>Further Processing</strong>
                                <p>
                                    The matter is processed in accordance with
                                    applicable laws and regulatory procedures.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 FINAL COMPLIANCE MESSAGE
            ================================================== -->
            <div class="final-compliance" data-aos="fade-up">

                <div class="final-icon">
                    <i class="fas fa-shield-heart"></i>
                </div>

                <h2>
                    Compliance Protects the Industry
                </h2>

                <p>
                    NACPDEAN encourages every producer, dealer, supplier,
                    exporter, transporter and stakeholder to understand and
                    comply with the Association's operational guidelines,
                    membership requirements, afforestation obligations and
                    applicable regulatory procedures.
                </p>

                <div class="final-tags">

                    <span>
                        <i class="fas fa-user-check"></i>
                        Register
                    </span>

                    <span>
                        <i class="fas fa-seedling"></i>
                        Afforest
                    </span>

                    <span>
                        <i class="fas fa-file-circle-check"></i>
                        Comply
                    </span>

                    <span>
                        <i class="fas fa-leaf"></i>
                        Sustain
                    </span>

                </div>

            </div>


        </div>

    </section>

</main>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        if (typeof AOS !== "undefined") {

            AOS.init({
                duration: 800,
                once: true,
                offset: 70
            });

        }

    });
</script>

<script src="assets/js/main.js"></script>

@endsection
