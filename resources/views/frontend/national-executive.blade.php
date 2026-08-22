@extends('frontend.master')

@section('home')
    <style>
        /*=========================
        MEMBER CARD
    ==========================*/

        .member-card {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .08);
            transition: .35s ease;
            border: 1px solid #ececec;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .member-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .15);
        }


        /*=========================
          MEMBER IMAGE
    ==========================*/

        .member-image {
            width: 100%;
            height: 320px;
            overflow: hidden;
            background: #f5f5f5;
            position: relative;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
            transition: .6s;
        }

        .member-card:hover .member-image img {
            transform: scale(1.08);
        }

        /* Dark gradient overlay */

        .member-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top,
                    rgba(0, 0, 0, .25),
                    rgba(0, 0, 0, 0));
        }


        /*=========================
          MEMBER BODY
    ==========================*/

        .member-body {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }


        /*=========================
          BADGE
    ==========================*/

        .member-badge {
            background: #608C11;
            color: #fff;
            border-radius: 30px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            align-self: center;
            margin-bottom: 18px;
        }


        /*=========================
          NAME
    ==========================*/

        .member-name {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
        }


        /*=========================
          POSITION
    ==========================*/

        .member-position {
            text-align: center;
            color: #608C11;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }


        /*=========================
          INFO
    ==========================*/

        .member-info {
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 18px 0;
            margin-bottom: 22px;
        }

        .member-info p {
            margin-bottom: 12px;
            color: #555;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .member-info p:last-child {
            margin-bottom: 0;
        }

        .member-info i {
            width: 20px;
            text-align: center;
            color: #608C11;
            font-size: 15px;
        }


        /*=========================
          BUTTON
    ==========================*/

        .btn-main {
            background: #608C11;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: .3s;
            margin-top: auto;
        }

        .btn-main:hover {
            background: #496b0c;
            color: #fff;
        }


        /*=========================
          ACTIVE STATUS
    ==========================*/

        .text-success {
            color: #2e7d32 !important;
            font-weight: 600;
        }


        /*
    ============  IMAGE PHOTO  =============
            */
        .member-image {
            width: 100%;
            aspect-ratio: 4/5;
            overflow: hidden;
            background: #f5f5f5;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
        }


        /*=========================
          RESPONSIVE
    ==========================*/

        @media (max-width:991px) {

            .member-image {
                height: 280px;
            }

        }

        @media (max-width:767px) {

            .member-image {
                height: 260px;
            }

            .member-name {
                font-size: 20px;
            }

        }

        @media (max-width:575px) {

            .member-image {
                height: 240px;
            }

            .member-body {
                padding: 20px;
            }

            .member-name {
                font-size: 18px;
            }

            .member-position {
                font-size: 15px;
            }

        }
    </style>

    <style>
        .page-link {
            color: #608C11;
        }

        .page-item.active .page-link {
            background: #608C11;
            border-color: #608C11;
        }

        .page-link:hover {
            color: #608C11;
        }
    </style>


    <main class="main">

        <!-- Hero Section -->
        <section class="hero text-center">
            <div class="container">
                <h1 class="display-5 fw-bold text-white">
                    National Executive Members
                </h1>
                <p>Search and browse registered members</p>
            </div>
        </section>

        <div class="container">

            <!-- Search -->
            <div class="search">
                <div class="row g-3">

                    <div class="col-md-8">
                        <input type="text" id="search" class="form-control form-control-lg"
                            placeholder="Search by name or membership number">
                    </div>

                    <div class="col-md-4">
                        <select id="cat" class="form-select form-select-lg">
                            <option value="">All Categories</option>
                            <option>Executive</option>
                            <option>Exporter</option>
                            <option>Supplier</option>
                            <option>Dealer</option>
                            <option>Producer</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="container mt-4">

                <!-- Members Grid -->
                <div class="row g-4" id="grid">
                    <!-- ================= MEMBER 1 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="edu babatunde" data-num="nmn-exp-lg-0001-n01"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/edu_babatunde_national_president.jpg') }}" alt="Edu Babatunde"
                                    class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Edu Babatunde</h4>

                                <p class="member-position">
                                    National President
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-LG-0001-N01</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100 profile-btn" data-name="Edu Babatunde"
                                    data-position="National President"
                                    data-image="{{ asset('frontend/assets/img/members/edu_babatunde_national_president.jpg') }}"
                                    data-phone="08145672358" data-email="presidentnacpdean@gmail.com"
                                    data-bio="Mr. Edu Babatunde currently serves as the National President of the NACPDEAN. Re-elected for a second term on April 15, 2026, he previously served as President from 2021 to December 2025, reflecting his exemplary leadership, vision, and unwavering commitment to the Association's growth and development.

He is widely recognized as a pioneer founder and key architect behind the establishment and formation of NACPDEAN, having played a pivotal role in laying the foundation for the Association's growth, institutional development, and nationwide expansion.

With over 15 years of experience in charcoal export, agro-commodity trading, and international business development, Babatunde is a distinguished accomplished exporter of agro-commodities and business executive renowned for his professionalism, integrity, and expertise in sustainable charcoal production and global marketing.

Under his leadership, NACPDEAN has strengthened industry standards, enhanced regulatory compliance, championed afforestation initiatives, and forged strategic partnerships that continue to advance Nigeria's charcoal and agro-commodity sectors globally."
                                    data-bs-toggle="modal" data-bs-target="#profileModal">

                                    View Profile

                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 2 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="abdulsalami rijana" data-num="nmn-exp-kd-0002-n02"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/abdulasalami_abubakar_rijana__national_deputy_president.png') }}"
                                    alt="Abdulsalami Rijana" class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Abdulsalami Rijana</h4>

                                <p class="member-position">
                                    National Deputy President
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-KD-0002-N02</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>
                                <button class="btn btn-main w-100 profile-btn" data-name="Abdulsalami Rijana"
                                    data-position="National Deputy President"
                                    data-image="{{ asset('frontend/assets/img/members/abdulasalami_abubakar_rijana__national_deputy_president.png') }}"
                                    data-phone="08036791180, 09020259595" data-email="abdulsalami.abubakar14@gmail.com"
                                    data-bio="Alhaji Abdulsalami Abubakar Rijana currently serves as the National Deputy President of the NACPDEAN. He previously served as the National Organizing Secretary, demonstrating outstanding leadership and commitment to the Association's growth and development.

A native of Rijana Town in Kachia Local Government Area of Kaduna State, Alhaji Rijana is a seasoned entrepreneur who has been actively engaged in the charcoal business since 2014, making it his primary occupation and source of livelihood. He is also recognized as one of the active figures involved in the establishment and formation of NACPDEAN.

Professionally, he began his career with Kaduna State Textile as a Clerical Officer and later worked with Arab Bank and Flour Mills Nigeria. He also held several strategic political appointments, including advisory roles within Kaduna State and Kachia Local Government."
                                    data-bs-toggle="modal" data-bs-target="#profileModal">

                                    View Profile

                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 3 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="kayode animashaun" data-num="nmn-exp-oy-0102"
                        data-cat="Exporter">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/kayode_usman_animashaun_national_zonal_vice_president_south_west.png') }}"
                                    alt="Kayode Animashaun" class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Exporter</span>

                                <h4 class="member-name">Kayode Animashaun</h4>

                                <p class="member-position">
                                    National Vice President (South-West)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-OY-0102</p>
                                    <p><i class="fas fa-box"></i> Export Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100 profile-btn" data-name="Kayode Animashaun"
                                    data-position="National Vice President (South-West)"
                                    data-image="assets/img/members/kayode_usman_animashaun_national_zonal_vice_president_south_west.png"
                                    data-phone="08060161267" data-email="ocelexport@gmail.com"
                                    data-bio="Mr. Kayode Usman Animashaun currently serves as the National Vice President (South-West) of NACPDEAN, a position he also held during the previous tenure. His continued election reflects his outstanding leadership, dedication, and commitment to the growth of the Association.

Mr. Animashaun is a distinguished exporter and one of the founding members of NACPDEAN, having played a significant role in the establishment and development of the Association. With over 15 years of experience in agricultural commodities export, he has established himself as a leading figure in Nigeria's charcoal export industry.

As a major charcoal exporter, he operates strategically located warehouses in Oyo, Kwara, Bauchi, and Jos, enabling efficient sourcing, storage, and distribution of products to both local and international markets. His professionalism, industry expertise, and business acumen continue to contribute significantly to the advancement of Nigeria's charcoal export sector.
"
                                    data-bs-toggle="modal" data-bs-target="#profileModal">

                                    View Profile

                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 4 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="chiedozie onyekwere" data-num="nmn-exp-an-0032"
                        data-cat="Exporter">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/chiedozie_Onyekwere_national_zonal_vice_president_south-east.jpg') }}"
                                    alt="Chiedozie Onyekwere" class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Exporter</span>

                                <h4 class="member-name">Chiedozie Onyekwere</h4>

                                <p class="member-position">
                                    National Zonal Vice President (South-East)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-AN-0032</p>
                                    <p><i class="fas fa-box"></i> Export Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 5 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="gabriel ikpide" data-num="nmn-dea-de-0023"
                        data-cat="Dealer">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/garbiel_nwachukwu_ikpide_national_vice_president_south_south.jpg') }}"
                                    alt="Gabriel Ikpide" class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Dealer</span>

                                <h4 class="member-name">Gabriel Ikpide</h4>

                                <p class="member-position">
                                    National Vice President (South-South)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-DE-0023</p>
                                    <p><i class="fas fa-store"></i> Dealer Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 6 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="atanda raman" data-num="nmn-slr-kw-0008"
                        data-cat="Supplier">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/alhaji_atanda__abdullateef_raman_national_vice_president_north_central.jpg') }}"
                                    alt="Atanda Raman" class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Supplier</span>

                                <h4 class="member-name">Atanda Raman</h4>

                                <p class="member-position">
                                    National Vice President (North-Central)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-SLR-KW-0008</p>
                                    <p><i class="fas fa-truck"></i> Supplier Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 7 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="usman tukur" data-num="nmn-prd-bo-0010"
                        data-cat="Producer">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/alh_usman_muhaamed_tukur_national_vice_president_north_east.png') }}"
                                    alt="Usman Tukur" class="img-fluid" width="600px" height="750px">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Producer</span>

                                <h4 class="member-name">Usman Tukur</h4>

                                <p class="member-position">
                                    National Zonal Vice President (North-East)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-PRD-BO-0010</p>
                                    <p><i class="fas fa-leaf"></i> Producer Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 8 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="egbekile tayo john" data-num="nmn-prd-kd-0011"
                        data-cat="Producer">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Egbekile Tayo John">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Producer</span>

                                <h4 class="member-name">Egbekile Tayo John</h4>

                                <p class="member-position">
                                    National Treasurer
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-PRD-KD-0011</p>
                                    <p><i class="fas fa-leaf"></i> Producer Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>

                    </div>

                    <!-- ================= MEMBER 9 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="ekundayo shade bello" data-num="nmn-slr-fc-0022"
                        data-cat="Supplier">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Ekundayo Shade Bello">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Supplier</span>

                                <h4 class="member-name">Ekundayo Shade Bello</h4>

                                <p class="member-position">
                                    National Deputy Dealers Coordinator (South)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-SLR-FC-0022</p>
                                    <p><i class="fas fa-truck"></i> Supplier Category</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 10 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="tasiu abdullahi" data-num="nmn-dea-kn-0012"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Tasiu Abdullahi" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Tasiu Abdullahi</h4>

                                <p class="member-position">
                                    National Zonal Vice President (North-West)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-KN-0012</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 11 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="idowu oaikhenam" data-num="nmn-exp-ed-0013"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Idowu Oaikhenam" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Idowu Oaikhenam</h4>

                                <p class="member-position">
                                    National Zonal Adviser (South-South)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-ED-0013</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 12 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="dada daniel owolabi" data-num="nmn-exp-og-0014"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Dada Daniel Owolabi"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Dada Daniel Owolabi</h4>

                                <p class="member-position">
                                    National Financial Secretary
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-OG-0014</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 13 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="favour aiyedogbon" data-num="nmn-dea-kw-0015"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Favour Aiyedogbon"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Favour Aiyedogbon</h4>

                                <p class="member-position">
                                    National Women Leader
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-KW-0015</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 14 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="rahama usman" data-num="nmn-exp-kn-0016"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Rahama Usman" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Rahama Saidu Usman</h4>

                                <p class="member-position">
                                    National Deputy Women Leader (North)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-KN-0016</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 15 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="tawakalit azeez" data-num="nmn-dea-oy-0017"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Tawakalit Azeez" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Azeez Tawakalit Rantiade</h4>

                                <p class="member-position">
                                    National Assistant Women Leader (South)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-OY-0017</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 16 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="tijani sulaimon" data-num="nmn-exp-kn-0018"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Tijani Sulaimon" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Tijani Abdullahi Sulaimon</h4>

                                <p class="member-position">
                                    National Organizing Secretary
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-KN-0018</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 17 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="omotosho emmanuel" data-num="nmn-exp-lg-0019"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Omotosho Emmanuel"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Omotosho Oladepo Emmanuel</h4>

                                <p class="member-position">
                                    National Deputy Organizing Secretary
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-LG-0019</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 18 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="adelere james" data-num="nmn-exp-oy-0020"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Adelere James" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Adelere Taiwo James</h4>

                                <p class="member-position">
                                    National Zonal Adviser (South-West)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-OY-0020</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">View Profile</button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 19 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="rasheed adekunle olayiwola"
                        data-num="nmn-dea-og-0021" data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Rasheed Adekunle Olayiwola"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Rasheed Adekunle Olayiwola</h4>

                                <p class="member-position">
                                    National Dealers Coordinator (South)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-OG-0021</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 20 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="olorunnisola ifedayo ajiboye"
                        data-num="nmn-exp-og-0022" data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Olorunnisola Ifedayo Ajiboye"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Olorunnisola Ifedayo Ajiboye</h4>

                                <p class="member-position">
                                    National Adviser
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-OG-0022</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 21 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="abdullahi omotosho kareem"
                        data-num="nmn-slr-kw-0023" data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Abdullahi Omotosho Kareem"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Alhaji Abdullahi Omotosho Kareem</h4>

                                <p class="member-position">
                                    National Deputy Adviser II
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-SLR-KW-0023</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 22 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="salihu ibrahim gorko" data-num="nmn-exp-fc-0024"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Salihu Ibrahim Gorko"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Salihu Ibrahim Gorko</h4>

                                <p class="member-position">
                                    National Protocol Officer
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-FC-0024</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 23 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="ifeanyi obietoh" data-num="nmn-exp-an-0025"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Ifeanyi Obietoh" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mr. Ifeanyi Obietoh</h4>

                                <p class="member-position">
                                    National Deputy Protocol Officer
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-AN-0025</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 24 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="rahmon adebayo adesope" data-num="nmn-exp-oy-0026"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Rahmon Adebayo Adesope"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Rahmon Adebayo Adesope</h4>

                                <p class="member-position">
                                    National Deputy Director of Afforestation
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-OY-0026</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 25 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="biola omosowon" data-num="nmn-exp-lg-0027"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Biola Omosowon" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Biola Omosowon</h4>

                                <p class="member-position">
                                    National Publicity Secretary
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-LG-0027</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 26 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="kingsley anikwe" data-num="nmn-exp-an-0028"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Dr. Kingsley Anikwe"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Dr. Kingsley Anikwe</h4>

                                <p class="member-position">
                                    National Deputy Publicity Secretary
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-AN-0028</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 27 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="bukar fukumari" data-num="nmn-dea-bo-0029"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Alhaji Bukar Fukumari"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Alhaji Bukar Fukumari</h4>

                                <p class="member-position">
                                    National Deputy Dealers Coordinator (North)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-BO-0029</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 28 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="kelechi obasi" data-num="nmn-exp-im-0030"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Mr. Kelechi Obasi"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mr. Kelechi Obasi</h4>

                                <p class="member-position">
                                    National Zonal Adviser (South-East)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-IM-0030</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 29 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="ojo oluwasola emmanuel" data-num="nmn-exp-ek-0031"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Ojo Oluwasola Emmanuel"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mr. Ojo Oluwasola Emmanuel</h4>

                                <p class="member-position">
                                    National Deputy Secretary-General
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-EK-0031</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 30 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="mohammed alhassan mamman" data-num="nmn-dea-ng-0032"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Mohammed Alhassan Mamman"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mohammed Alhassan Mamman</h4>

                                <p class="member-position">
                                    National Deputy Financial Secretary
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-NG-0032</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 31 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="musa shuaibu" data-num="nmn-dea-kd-0033"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Musa Shuaibu" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mr. Musa Shuaibu</h4>

                                <p class="member-position">
                                    National Zonal Adviser (North-West)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-KD-0033</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 32 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="abdulmumini umar" data-num="nmn-dea-ba-0034"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Abdulmumini Umar" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Alhaji Abdulmumini Umar</h4>

                                <p class="member-position">
                                    National Zonal Adviser (North-East)
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-BA-0034</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 33 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="dantata adamu" data-num="nmn-dea-yo-0035"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Dantata Adamu" class="img-fluid"
                                    width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mr. Dantata Adamu</h4>

                                <p class="member-position">
                                    National Auditor
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-DEA-YO-0035</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>

                    <!-- ================= MEMBER 34 ================= -->
                    <div class="col-lg-4 col-md-6 member" data-name="egbekile tayo john" data-num="nmn-exp-ed-0036"
                        data-cat="Executive">

                        <div class="member-card">

                            <div class="member-image">
                                <img src="{{ asset('frontend/assets/img/members/default-member.jpg') }}" alt="Egbekile Tayo John"
                                    class="img-fluid" width="600" height="750">
                            </div>

                            <div class="member-body">

                                <span class="badge member-badge">Executive</span>

                                <h4 class="member-name">Mr. Egbekile Tayo John</h4>

                                <p class="member-position">
                                    National Treasurer
                                </p>

                                <div class="member-info">
                                    <p><i class="fas fa-id-card"></i> NMN-EXP-ED-0036</p>
                                    <p><i class="fas fa-user-tie"></i> Executive Member</p>
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Active Member</p>
                                </div>

                                <button class="btn btn-main w-100">
                                    View Profile
                                </button>

                            </div>

                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-center mt-5">
                    <nav>
                        <ul class="pagination" id="pagination"></ul>
                    </nav>
                </div>

            </div>

        </div>

    </main>


    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

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

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <p>Dear Esteemed Charcoal Stakeholders,</p>

                    <p>
                        It is my pleasure to welcome you to the official website of the
                        <strong>National Association of Charcoal Producers, Dealers,
                            Exporters and Afforestation of Nigeria (NACPDEAN).</strong>
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
                    <p class="mb-0"><strong>National President</strong></p>
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



    <div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title text-white">
                        About NACPDEAN
                    </h4>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

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
                        <strong>IT182068</strong>.
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

                    <button class="btn btn-danger"
                        data-bs-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content">

                <div class="modal-header">

                    <h4 id="modalName"></h4>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-4">

                            <img
                                id="modalImage"
                                class="img-fluid rounded shadow">

                        </div>

                        <div class="col-md-8">

                            <h5 id="modalPosition"
                                class="text-success fw-bold"></h5>

                            <hr>

                            <p id="modalBio"></p>

                            <hr>

                            <p>
                                <strong>Phone:</strong>
                                <span id="modalPhone"></span>
                            </p>

                            <p>
                                <strong>Email:</strong>
                                <span id="modalEmail"></span>
                            </p>

                        </div>

                    </div>

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


    <script>
        document.querySelectorAll(".profile-btn").forEach(button => {

            button.addEventListener("click", function() {

                document.getElementById("modalName").textContent =
                    this.dataset.name;

                document.getElementById("modalPosition").textContent =
                    this.dataset.position;

                document.getElementById("modalBio").textContent =
                    this.dataset.bio;

                document.getElementById("modalPhone").textContent =
                    this.dataset.phone;

                document.getElementById("modalEmail").textContent =
                    this.dataset.email;

                document.getElementById("modalImage").src =
                    this.dataset.image;

            });

        });
    </script>



    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        const cardsPerPage = 9; // Number of cards per page

        const cards = document.querySelectorAll("#grid .member");
        const pagination = document.getElementById("pagination");

        let currentPage = 1;

        function showPage(page) {

            currentPage = page;

            const start = (page - 1) * cardsPerPage;
            const end = start + cardsPerPage;

            cards.forEach((card, index) => {

                if (index >= start && index < end) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }

            });

            createPagination();

            // Scroll back to the cards
            document.getElementById("grid").scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

        }

        function createPagination() {

            pagination.innerHTML = "";

            const totalPages = Math.ceil(cards.length / cardsPerPage);

            // Previous Button
            let prev = document.createElement("li");
            prev.className = `page-item ${currentPage===1?'disabled':''}`;

            prev.innerHTML = `<a class="page-link" href="#">Previous</a>`;

            prev.onclick = function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    showPage(currentPage - 1);
                }
            };

            pagination.appendChild(prev);

            // Page Numbers
            for (let i = 1; i <= totalPages; i++) {

                let li = document.createElement("li");

                li.className = `page-item ${i===currentPage?'active':''}`;

                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;

                li.onclick = function(e) {
                    e.preventDefault();
                    showPage(i);
                };

                pagination.appendChild(li);

            }

            // Next Button
            let next = document.createElement("li");
            next.className = `page-item ${currentPage===totalPages?'disabled':''}`;

            next.innerHTML = `<a class="page-link" href="#">Next</a>`;

            next.onclick = function(e) {
                e.preventDefault();

                if (currentPage < totalPages) {
                    showPage(currentPage + 1);
                }

            };

            pagination.appendChild(next);

        }

        // Load first page
        showPage(1);
    </script>


</body>

</html>

@endsection
