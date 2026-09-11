@extends('frontend.master')

@section('home')

<style>

/* =========================================================
   NACPDEAN CONTACT PAGE
========================================================= */

.contact-page {
    background: #f7f9f5;
    color: #26321f;
}


/* =========================================================
   HERO
========================================================= */

.contact-hero {
    position: relative;
    overflow: hidden;
    padding: 110px 0 125px;
    background:
        linear-gradient(
            135deg,
            rgba(38, 65, 16, .97),
            rgba(96, 140, 17, .94)
        );
}

.contact-hero::before {
    content: "";
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    top: -180px;
    right: -100px;
}

.contact-hero::after {
    content: "";
    position: absolute;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
    bottom: -150px;
    left: -80px;
}

.contact-hero .container {
    position: relative;
    z-index: 2;
}

.contact-badge {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 9px 18px;
    margin-bottom: 22px;
    border-radius: 50px;
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .5px;
    backdrop-filter: blur(5px);
}

.contact-badge i {
    color: #d9ef9e;
}

.contact-hero h1 {
    font-size: clamp(38px, 5vw, 64px);
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 22px;
}

.contact-hero p {
    max-width: 800px;
    margin: auto;
    color: rgba(255,255,255,.88);
    font-size: 17px;
    line-height: 1.8;
}

.hero-wave {
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 55px;
    background: #f7f9f5;
    clip-path: ellipse(70% 55% at 50% 100%);
}


/* =========================================================
   SECTION TITLES
========================================================= */

.contact-page .section-title {
    margin-bottom: 45px;
}

.contact-page .section-title h2 {
    font-size: 36px;
    font-weight: 800;
    color: #25351b;
    margin-bottom: 12px;
}

.contact-page .section-title p {
    color: #6b7565;
    font-size: 16px;
    margin-bottom: 0;
}


/* =========================================================
   CONTACT INFORMATION
========================================================= */

.contact-info {
    padding: 75px 0 85px;
}

.contact-card {
    height: 100%;
    padding: 38px 30px;
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e5eadf;
    box-shadow: 0 12px 35px rgba(42, 61, 30, .07);
    text-align: center;
    transition: all .3s ease;
}

.contact-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 45px rgba(42, 61, 30, .13);
    border-color: rgba(96,140,17,.3);
}

.icon-box {
    width: 70px;
    height: 70px;
    margin: 0 auto 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: #eef5e5;
    color: #608c11;
    font-size: 27px;
}

.contact-card h4 {
    color: #28361f;
    font-size: 20px;
    font-weight: 750;
    margin-bottom: 15px;
}

.contact-card p {
    color: #6d7569;
    line-height: 1.8;
    margin-bottom: 0;
}

.contact-card a {
    color: #608c11;
    font-weight: 700;
    text-decoration: none;
}

.contact-card a:hover {
    color: #45690a;
}


/* =========================================================
   FORM SECTION
========================================================= */

.contact-form-section {
    padding: 90px 0;
    background: #fff;
}

.form-wrapper {
    padding: 45px;
    background: #f8faf6;
    border: 1px solid #e4eade;
    border-radius: 25px;
    box-shadow: 0 15px 45px rgba(42,61,30,.06);
}

.form-wrapper .section-title {
    margin-bottom: 30px;
}

.form-wrapper .section-title h2 {
    font-size: 32px;
}

.form-control,
.form-select {
    min-height: 55px;
    border-radius: 12px;
    border: 1px solid #dfe5d9;
    background: #fff;
    padding: 13px 17px;
    color: #27321f;
    box-shadow: none;
    transition: all .25s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #608c11;
    box-shadow: 0 0 0 4px rgba(96,140,17,.10);
}

textarea.form-control {
    min-height: 145px;
    resize: vertical;
}

.form-control::placeholder {
    color: #9aa294;
}

.btn-contact {
    border: 0;
    min-height: 54px;
    padding: 13px 28px;
    border-radius: 12px;
    background: #608c11;
    color: #fff;
    font-weight: 700;
    transition: all .3s ease;
    box-shadow: 0 10px 22px rgba(96,140,17,.22);
}

.btn-contact:hover {
    background: #4c720b;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(96,140,17,.28);
}


/* =========================================================
   CONTACT IMAGE
========================================================= */

.contact-image {
    position: relative;
    padding: 12px;
}

.contact-image img {
    width: 100%;
    min-height: 520px;
    object-fit: cover;
    border-radius: 25px !important;
    box-shadow: 0 20px 50px rgba(0,0,0,.15) !important;
}

.contact-image::before {
    content: "";
    position: absolute;
    width: 100px;
    height: 100px;
    background: #608c11;
    border-radius: 20px;
    top: -5px;
    right: -5px;
    z-index: 0;
}

.contact-image::after {
    content: "";
    position: absolute;
    width: 100px;
    height: 100px;
    background: #dfecc9;
    border-radius: 20px;
    bottom: -5px;
    left: -5px;
    z-index: 0;
}

.contact-image img {
    position: relative;
    z-index: 1;
}

.floating-box {
    position: absolute;
    z-index: 3;
    left: -10px;
    bottom: 40px;
    display: flex;
    align-items: center;
    gap: 15px;
    max-width: 270px;
    padding: 18px 22px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(0,0,0,.16);
}

.floating-box > i {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: #eef5e5;
    color: #608c11;
    font-size: 21px;
}

.floating-box h5 {
    margin: 0 0 3px;
    color: #27351e;
    font-weight: 750;
}

.floating-box p {
    margin: 0;
    color: #788071;
    font-size: 13px;
}


/* =========================================================
   MAP
========================================================= */

.map-section {
    padding: 90px 0;
    background: #f7f9f5;
}

.map-wrapper {
    position: relative;
    min-height: 400px;
    overflow: hidden;
    border-radius: 24px;
    background:
        linear-gradient(
            135deg,
            #edf3e8,
            #f9faf7
        );
    border: 1px solid #dfe7d8;
    box-shadow: 0 15px 40px rgba(42,61,30,.08);
}

.map-placeholder {
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 30px;
}

.map-placeholder .map-icon {
    width: 75px;
    height: 75px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    border-radius: 50%;
    background: #fff;
    color: #608c11;
    font-size: 30px;
    box-shadow: 0 10px 30px rgba(42,61,30,.10);
}

.map-placeholder h4 {
    color: #29371f;
    font-weight: 750;
    margin-bottom: 8px;
}

.map-placeholder p {
    max-width: 500px;
    color: #747c6e;
    margin-bottom: 22px;
}

.map-btn {
    border: 1px solid #608c11;
    border-radius: 10px;
    padding: 12px 22px;
    color: #608c11;
    background: #fff;
    font-weight: 700;
    transition: all .25s ease;
}

.map-btn:hover {
    background: #608c11;
    color: #fff;
}

#mapWrapper iframe {
    display: block;
    width: 100%;
    height: 420px;
    border: 0;
}


/* =========================================================
   FAQ
========================================================= */

.faq-section {
    padding: 90px 0;
    background: #fff;
}

.faq-section .section-title {
    text-align: center;
}

#faqAccordion {
    max-width: 900px;
    margin: 0 auto;
}

#faqAccordion .accordion-item {
    margin-bottom: 15px;
    border: 1px solid #e3e9de;
    border-radius: 15px;
    overflow: hidden;
    background: #fff;
}

#faqAccordion .accordion-button {
    padding: 21px 24px;
    color: #29371f;
    background: #fff;
    font-weight: 700;
    box-shadow: none;
}

#faqAccordion .accordion-button:not(.collapsed) {
    color: #608c11;
    background: #f5f8f1;
}

#faqAccordion .accordion-button:focus {
    box-shadow: none;
}

#faqAccordion .accordion-body {
    padding: 0 24px 23px;
    color: #6d7569;
    line-height: 1.8;
}


/* =========================================================
   CTA
========================================================= */

.contact-cta {
    position: relative;
    overflow: hidden;
    padding: 75px 0;
    background:
        linear-gradient(
            135deg,
            #263f13,
            #608c11
        );
}

.contact-cta::before {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    right: -100px;
    top: -160px;
}

.contact-cta .container {
    position: relative;
    z-index: 2;
}

.contact-cta h2 {
    font-size: 34px;
    font-weight: 800;
    margin-bottom: 12px;
}

.contact-cta p {
    color: rgba(255,255,255,.82);
    max-width: 750px;
    margin-bottom: 0;
    line-height: 1.8;
}

.btn-join {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 14px 26px;
    border-radius: 12px;
    background: #fff;
    color: #4d720b;
    font-weight: 750;
    text-decoration: none;
    transition: all .3s ease;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
}

.btn-join:hover {
    color: #4d720b;
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0,0,0,.20);
}

.btn-join i {
    transition: transform .25s ease;
}

.btn-join:hover i {
    transform: translateX(4px);
}


/* =========================================================
   MODALS
========================================================= */

.modal-content {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 25px 70px rgba(0,0,0,.20);
}

.modal-header {
    background: #608c11;
    border: 0;
    padding: 20px 25px;
}

.modal-header .modal-title {
    font-weight: 750;
}

.modal-body {
    padding: 30px;
    color: #626b5e;
    line-height: 1.8;
}

.modal-body h5 {
    font-weight: 750;
}

.modal-footer {
    border-top: 1px solid #edf0ea;
    padding: 18px 25px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991px) {

    .contact-hero {
        padding: 85px 0 105px;
    }

    .contact-form-section {
        padding: 70px 0;
    }

    .form-wrapper {
        padding: 32px 25px;
    }

    .contact-image img {
        min-height: 420px;
    }

    .floating-box {
        left: 15px;
    }

    .contact-cta {
        text-align: center;
    }

    .contact-cta p {
        margin: 0 auto 25px;
    }

}

@media (max-width: 767px) {

    .contact-hero {
        padding: 70px 0 90px;
    }

    .contact-hero h1 {
        font-size: 38px;
    }

    .contact-hero p {
        font-size: 15px;
    }

    .contact-info,
    .map-section,
    .faq-section {
        padding: 65px 0;
    }

    .contact-page .section-title h2 {
        font-size: 29px;
    }

    .form-wrapper {
        padding: 25px 20px;
    }

    .contact-image {
        margin-top: 15px;
    }

    .contact-image img {
        min-height: 350px;
    }

    .floating-box {
        position: relative;
        left: auto;
        bottom: auto;
        margin: -45px 20px 0;
    }

    .contact-cta h2 {
        font-size: 28px;
    }

}

</style>

<div class="contact-page">

```
{{-- =========================================================
    CONTACT HERO
========================================================== --}}

<section class="contact-hero">

    <div class="container">

        <div class="row justify-content-center text-center">

            <div class="col-lg-9">

                <span class="contact-badge">

                    <i class="bi bi-telephone-fill"></i>

                    Contact NACPDEAN

                </span>

                <h1 class="text-white">

                    We'd Love To Hear From You

                </h1>

                <p>

                    Get in touch with the National Association of Charcoal
                    Producers, Dealers, Exporters and Afforestation of Nigeria.
                    Our team is ready to answer your enquiries, membership
                    requests, partnerships and investment opportunities.

                </p>

            </div>

        </div>

    </div>

    <div class="hero-wave"></div>

</section>


{{-- =========================================================
    CONTACT INFORMATION
========================================================== --}}

<section class="contact-info">

    <div class="container">

        <div class="section-title text-center">

            <h2>Get In Touch With Us</h2>

            <p>
                Our team is available to assist with your enquiries
                and provide the information you need.
            </p>

        </div>


        <div class="row g-4">

            <div class="col-lg-4 col-md-6">

                <div class="contact-card">

                    <div class="icon-box">

                        <i class="bi bi-geo-alt-fill"></i>

                    </div>

                    <h4>Office Address</h4>

                    <p>

                        Block D Complex, Rooms 309–311,<br>

                        Federal Ministry of Industry, Trade and Investment,<br>

                        Old Federal Secretariat Complex,<br>

                        Area 1, Garki, Abuja,<br>

                        Federal Capital Territory, Nigeria.

                    </p>

                </div>

            </div>


            <div class="col-lg-4 col-md-6">

                <div class="contact-card">

                    <div class="icon-box">

                        <i class="bi bi-envelope-fill"></i>

                    </div>

                    <h4>Email Us</h4>

                    <p>

                        For general enquiries and information,
                        please contact us by email.

                        <br><br>

                        <a href="mailto:info@nacpdean.org">

                            info@nacpdean.org

                        </a>

                    </p>

                </div>

            </div>


            <div class="col-lg-4 col-md-6">

                <div class="contact-card">

                    <div class="icon-box">

                        <i class="bi bi-telephone-fill"></i>

                    </div>

                    <h4>Call Us</h4>

                    <p>

                        <a href="tel:+2348145672358">

                            +234 81 456 723 58

                        </a>

                        <br><br>

                        Monday - Saturday<br>

                        8:00 AM - 5:00 PM

                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    CONTACT FORM
========================================================== --}}

<section class="contact-form-section">

    <div class="container">

        <div class="row g-5 align-items-center">


            {{-- FORM --}}

            <div class="col-lg-6">

                <div class="form-wrapper">

                    <div class="section-title text-start">

                        <h2>Send Us A Message</h2>

                        <p>

                            Complete the form below and one of our
                            representatives will get back to you
                            as soon as possible.

                        </p>

                    </div>


                    <form>

                        <div class="row">

                            <div class="col-md-6 mb-4">

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Full Name"
                                    required>

                            </div>


                            <div class="col-md-6 mb-4">

                                <input
                                    type="email"
                                    class="form-control"
                                    placeholder="Email Address"
                                    required>

                            </div>


                            <div class="col-md-6 mb-4">

                                <input
                                    type="tel"
                                    class="form-control"
                                    placeholder="Phone Number">

                            </div>


                            <div class="col-md-6 mb-4">

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Subject">

                            </div>


                            <div class="col-12 mb-4">

                                <select class="form-select">

                                    <option selected disabled>
                                        Select Enquiry Type
                                    </option>

                                    <option>Membership</option>

                                    <option>
                                        Export Information
                                    </option>

                                    <option>
                                        Afforestation
                                    </option>

                                    <option>
                                        Investment
                                    </option>

                                    <option>
                                        General Enquiry
                                    </option>

                                    <option>
                                        Complaint
                                    </option>

                                </select>

                            </div>


                            <div class="col-12 mb-4">

                                <textarea
                                    rows="6"
                                    class="form-control"
                                    placeholder="Write your message..."></textarea>

                            </div>


                            <div class="col-12">

                                <button
                                    type="submit"
                                    class="btn-contact">

                                    <i class="bi bi-send-fill me-2"></i>

                                    Send Message

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            {{-- IMAGE --}}

            <div class="col-lg-6">

                <div class="contact-image">

                    <img
                        src="{{ asset('assets/img/gallery/nacpdeangal20.jpeg') }}"
                        class="img-fluid"
                        alt="Contact NACPDEAN"
                        width="800"
                        height="600">

                    <div class="floating-box">

                        <i class="bi bi-chat-dots-fill"></i>

                        <div>

                            <h5>Need Help?</h5>

                            <p>
                                We're available to assist you.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    MAP
========================================================== --}}

<section class="map-section">

    <div class="container">

        <div class="section-title text-center">

            <h2>Visit Our Office</h2>

            <p>
                Locate our national headquarters in Abuja.
            </p>

        </div>


        <div
            id="mapWrapper"
            class="map-wrapper">

            <div class="map-placeholder">

                <div class="map-icon">

                    <i class="bi bi-geo-alt-fill"></i>

                </div>

                <h4>NACPDEAN National Headquarters</h4>

                <p>

                    Block D Complex, Rooms 309–311,
                    Federal Ministry of Industry, Trade and Investment,
                    Area 1, Garki, Abuja.

                </p>

                <button
                    type="button"
                    id="loadMapBtn"
                    class="map-btn">

                    <i class="bi bi-map me-2"></i>

                    Load Interactive Map

                </button>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    FAQ
========================================================== --}}

<section class="faq-section">

    <div class="container">

        <div class="section-title">

            <h2>Frequently Asked Questions</h2>

            <p>
                Answers to common questions about NACPDEAN.
            </p>

        </div>


        <div
            class="accordion"
            id="faqAccordion">


            <div class="accordion-item">

                <h2 class="accordion-header">

                    <button
                        class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#faq1"
                        aria-expanded="true">

                        How do I become a member?

                    </button>

                </h2>


                <div
                    id="faq1"
                    class="accordion-collapse collapse show"
                    data-bs-parent="#faqAccordion">

                    <div class="accordion-body">

                        Complete the online membership application
                        form, submit the required documents and make
                        the prescribed membership payment.

                    </div>

                </div>

            </div>


            <div class="accordion-item">

                <h2 class="accordion-header">

                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#faq2"
                        aria-expanded="false">

                        Do exporters require NEPC registration?

                    </button>

                </h2>


                <div
                    id="faq2"
                    class="accordion-collapse collapse"
                    data-bs-parent="#faqAccordion">

                    <div class="accordion-body">

                        Yes. Exporters are expected to possess valid
                        NEPC registration and other export documentation
                        where applicable.

                    </div>

                </div>

            </div>


            <div class="accordion-item">

                <h2 class="accordion-header">

                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#faq3"
                        aria-expanded="false">

                        Does NACPDEAN support afforestation projects?

                    </button>

                </h2>


                <div
                    id="faq3"
                    class="accordion-collapse collapse"
                    data-bs-parent="#faqAccordion">

                    <div class="accordion-body">

                        Yes. Afforestation and environmental
                        sustainability are among the Association's
                        core objectives.

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    CALL TO ACTION
========================================================== --}}

<section class="contact-cta">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <h2 class="text-white">

                    Become Part Of Nigeria's Largest Charcoal Association

                </h2>

                <p>

                    Join producers, exporters, dealers and investors
                    committed to sustainable charcoal production and
                    environmental conservation.

                </p>

            </div>


            <div class="col-lg-4 text-lg-end">

                <a
                    href="#"
                    class="btn-join">

                    Join NACPDEAN

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>

        </div>

    </div>

</section>
```

</div>

{{-- =========================================================
WELCOME MODAL
========================================================= --}}

<div
    class="modal fade"
    id="welcomeModal"
    tabindex="-1"
    aria-labelledby="welcomeModalLabel"
    aria-hidden="true">

```
<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

    <div class="modal-content">

        <div class="modal-header">

            <h5
                class="modal-title text-white"
                id="welcomeModalLabel">

                Welcome Message From The National President

            </h5>

            <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Close"></button>

        </div>


        <div class="modal-body">

            <p>
                Dear Esteemed Charcoal Stakeholders,
            </p>

            <p>

                It is my pleasure to welcome you to the official website
                of the
                <strong>
                    National Association of Charcoal Producers, Dealers,
                    Exporters and Afforestation of Nigeria (NACPDEAN).
                </strong>

            </p>

            <p>

                NACPDEAN was established to provide leadership,
                coordination, and representation for stakeholders within
                Nigeria's charcoal industry while ensuring that commercial
                activities are conducted in accordance with national
                regulations, industry self-regulations, environmental
                standards, and international best practices.

            </p>

            <p>

                As an Association, we recognize the significant role the
                charcoal industry plays in job creation, foreign exchange
                generation, rural development, and economic empowerment.
                We equally recognize our collective responsibility to
                protect and restore the environment through sustainable
                production methods and aggressive afforestation initiatives.

            </p>

            <p>

                Our commitment is to build a transparent, compliant, and
                globally competitive charcoal industry that balances
                economic prosperity with environmental sustainability.

            </p>

            <p>

                I invite you to explore our platform, participate in our
                programs, and join us in building a sustainable future
                for generations to come.

            </p>

            <p>
                Thank you for your support and partnership.
            </p>

            <hr>

            <h5 class="mb-0">
                Edu Babatunde
            </h5>

            <p class="mb-0">

                <strong>National President</strong>

            </p>

            <p>

                National Association of Charcoal Producers, Dealers,
                Exporters and Afforestation of Nigeria (NACPDEAN)

            </p>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-danger"
                data-bs-dismiss="modal">

                Close

            </button>

        </div>

    </div>

</div>
```

</div>

{{-- =========================================================
ABOUT MODAL
========================================================= --}}

<div
    class="modal fade"
    id="aboutModal"
    tabindex="-1"
    aria-labelledby="aboutModalLabel"
    aria-hidden="true">

```
<div class="modal-dialog modal-xl modal-dialog-scrollable">

    <div class="modal-content">

        <div class="modal-header">

            <h4
                class="modal-title text-white"
                id="aboutModalLabel">

                About NACPDEAN

            </h4>

            <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Close"></button>

        </div>


        <div class="modal-body">

            <h5 class="text-success mb-3">
                Brief Introduction of NACPDEAN
            </h5>

            <p>

                The National Association of Charcoal Producers, Dealers,
                Exporters and Afforestation of Nigeria (NACPDEAN) is the
                recognized national umbrella body established to
                coordinate, regulate, organize, promote and represent
                stakeholders operating across the charcoal value chain
                in Nigeria.

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
                growth, environmental sustainability, employment
                generation and foreign exchange earnings.

            </p>

            <p>

                The Association was formally incorporated with the
                Corporate Affairs Commission (CAC) of Nigeria on the
                6th day of June 2022 with Registration Number
                <strong>IT182068</strong>.

            </p>

            <p>

                Today, NACPDEAN stands as the foremost national body
                championing responsible charcoal production, sustainable
                forest management, environmental restoration,
                afforestation, export promotion and industry regulation
                throughout Nigeria.

            </p>

            <p>

                NACPDEAN was established as a strategic response to the
                challenges facing Nigeria's charcoal industry and to
                provide a sustainable pathway for industry growth,
                environmental protection and economic development.

            </p>

            <p>

                By bringing producers, dealers, exporters, processors,
                marketers and all stakeholders under one umbrella,
                NACPDEAN has created a platform for accountability,
                regulation, environmental responsibility and industry
                advancement.

            </p>

            <p>

                The Association remains committed to supporting the
                Federal Government's objectives on environmental
                sustainability, economic diversification, non-oil export
                promotion, afforestation and foreign exchange generation
                while ensuring that Nigeria's charcoal industry develops
                responsibly, transparently and sustainably for present
                and future generations.

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

                To organize, regulate, represent and empower stakeholders
                within the charcoal value chain through sustainable
                resource management, afforestation initiatives,
                environmental responsibility, industry compliance,
                trade facilitation and strategic partnerships that
                contribute to national development.

            </p>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-danger"
                data-bs-dismiss="modal">

                Close

            </button>

        </div>

    </div>

</div>
```

</div>

{{-- =========================================================
SCROLL TOP
========================================================= --}}

<a
 href="#"
 id="scroll-top"
 class="scroll-top d-flex align-items-center justify-content-center">

```
<i class="bi bi-arrow-up-short"></i>
```

</a>

{{-- =========================================================
MAP SCRIPT
Manual load only
========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const loadMapBtn = document.getElementById('loadMapBtn');
    const mapWrapper = document.getElementById('mapWrapper');

    if (!loadMapBtn || !mapWrapper) {
        return;
    }

    loadMapBtn.addEventListener('click', function () {

        mapWrapper.innerHTML = `
            <iframe
                src="https://www.google.com/maps?q=Area+1+Garki+Abuja&output=embed"
                title="NACPDEAN Office Location"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        `;

    });

});

</script>

@endsection
