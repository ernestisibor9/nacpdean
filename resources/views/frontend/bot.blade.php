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

    <style>
        .board {
            background-color: #608C11;
            color: #fff;
        }
    </style>

    <main class="main">

        <!-- Hero Section -->
        <section class="hero text-center">
            <div class="container">

                <h1 class="display-4 fw-bold text-white">
                    Board of Trustees
                </h1>

                <p class="lead text-white">
                    Meet the distinguished members of the Board of Trustees of the
                    National Charcoal Producers, Dealers & Exporters Association of Nigeria (NACPDEAN),
                    entrusted with providing strategic direction, governance, and preserving the vision of the Association.
                </p>

            </div>
        </section>

        <div class="container pb-5">

            <div class="card shadow border-0 rounded-4 mt-3">

                <div class="card-header board text-white py-3">

                    <h3 class="mb-0 board">
                        <i class="fas fa-landmark me-2 board"></i>
                        Board of Trustees
                    </h3>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead>

                                <tr>
                                    <th width="80">S/N</th>
                                    <th>Trustee's Name</th>
                                    <th width="220">Designation</th>
                                </tr>

                            </thead>

                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td>Alhaji Dogodaji Nuhu Bello</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Edu Babatunde</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Alhaji Dahir Mohammed Mohammed</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Alhaji Issa Soliu Alamo</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Christopher Obokoh</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>Hon. Sunday Ogungbenro</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>7</td>
                                    <td>Hon. Ali Bukar Jallaba</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>8</td>
                                    <td>Azeez Abiodun Adekunle</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>9</td>
                                    <td>Ogunnoiki Oluwaseun Patrick</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                                <tr>
                                    <td>10</td>
                                    <td>Alhaji Ajani Omotosho Salau</td>
                                    <td><span class="badge bg-success">Board of Trustee</span></td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </main>
@endsection
