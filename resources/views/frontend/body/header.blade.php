{{-- =========================================================
     NACPDEAN FRONTEND HEADER
     resources/views/frontend/header.blade.php

     ONLY EXISTING LARAVEL ROUTES ARE USED.
========================================================= --}}

@php
    use App\Models\BlacklistedMember;

    /*
    |--------------------------------------------------------------------------
    | Check if there are any blacklisted members
    |--------------------------------------------------------------------------
    |
    | The blacklist news/marquee will only be displayed when at least
    | one blacklisted member exists in the database.
    |
    */
    $hasBlacklistedMembers = BlacklistedMember::exists();
@endphp


<header id="header" class="header sticky-top">

    {{-- =====================================================
         TOP BAR
    ====================================================== --}}
    <div class="topbar d-flex align-items-center">

        <div class="container d-flex justify-content-center justify-content-md-between">

            {{-- SOCIAL ICONS --}}
            <div class="d-flex align-items-center social-icons">

                {{-- Facebook --}}
                <a href="https://www.facebook.com/profile.php?id=61591564350891"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="Facebook">

                    <i class="bi bi-facebook"></i>

                </a>


                {{-- Instagram --}}
                <a href="https://www.instagram.com/nacpdean"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="Instagram">

                    <i class="bi bi-instagram"></i>

                </a>


                {{-- X --}}
                <a href="https://x.com/nacpdean"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="Twitter">

                    <i class="bi bi-twitter-x"></i>

                </a>


                {{-- LinkedIn --}}
                <a href="https://www.linkedin.com/in/nacpdean-nacpdean-17187041b/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="LinkedIn">

                    <i class="bi bi-linkedin"></i>

                </a>


                {{-- WhatsApp --}}
                <a href="https://wa.me/2348145672358"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="WhatsApp">

                    <i class="bi bi-whatsapp"></i>

                </a>

            </div>


            {{-- PHONE --}}
            <div class="d-none d-md-flex align-items-center">

                <i class="bi bi-phone me-1"></i>

                Call us now +234 81 456 723 58

            </div>

        </div>

    </div>
    {{-- End Top Bar --}}



    {{-- =====================================================
         BRANDING
    ====================================================== --}}
    <div class="branding d-flex align-items-center">

        <div class="container position-relative d-flex align-items-center justify-content-end">


            {{-- =================================================
                 LOGO
            ================================================== --}}
            <a href="{{ route('index') }}"
                class="logo d-flex align-items-center me-auto">

                <img src="{{ asset('frontend/assets/img/logo.png') }}"
                    alt="NACPDEAN Logo">

            </a>



            {{-- =================================================
                 NAVIGATION
            ================================================== --}}
            <nav id="navmenu" class="navmenu">

                <ul>


                    {{-- =================================================
                         HOME
                    ================================================== --}}
                    <li>

                        <a href="{{ route('index') }}"
                            class="{{ request()->routeIs('index') ? 'active' : '' }}">

                            Home

                        </a>

                    </li>



                    {{-- =================================================
                         ABOUT US
                    ================================================== --}}
                    <li class="dropdown">

                        <a href="#"
                            class="{{ request()->routeIs('about', 'bot', 'history', 'association', 'partnership') ? 'active' : '' }}">

                            <span>About Us</span>

                            <i class="bi bi-chevron-down toggle-dropdown"></i>

                        </a>


                        <ul>

                            <li>

                                <a href="{{ route('about') }}"
                                    class="{{ request()->routeIs('about') ? 'active' : '' }}">

                                    About Us

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('bot') }}"
                                    class="{{ request()->routeIs('bot') ? 'active' : '' }}">

                                    Board of Trustees

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('history') }}"
                                    class="{{ request()->routeIs('history') ? 'active' : '' }}">

                                    History

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('association') }}"
                                    class="{{ request()->routeIs('association') ? 'active' : '' }}">

                                    Association

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('partnership') }}"
                                    class="{{ request()->routeIs('partnership') ? 'active' : '' }}">

                                    Partnership with SSN and GGLV

                                </a>

                            </li>


                            <li>

                                <a href="#"
                                    class="{{ request()->routeIs('partnership') ? 'active' : '' }}">

                                    Stakeholders Data

                                </a>

                            </li>

                        </ul>

                    </li>



                    {{-- =================================================
                         EXECUTIVE
                    ================================================== --}}
                    <li class="dropdown">

                        <a href="#"
                            class="{{ request()->routeIs('national-executive', 'state-executive') ? 'active' : '' }}">

                            <span>Executive</span>

                            <i class="bi bi-chevron-down toggle-dropdown"></i>

                        </a>


                        <ul>

                            <li>

                                <a href="{{ route('national-executive') }}"
                                    class="{{ request()->routeIs('national-executive') ? 'active' : '' }}">

                                    National Executive

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('state-executive') }}"
                                    class="{{ request()->routeIs('state-executive') ? 'active' : '' }}">

                                    State Executive

                                </a>

                            </li>

                        </ul>

                    </li>



                    {{-- =================================================
                         MEMBERSHIP
                    ================================================== --}}
                    <li class="dropdown">

                        <a href="#">

                            <span>Membership</span>

                            <i class="bi bi-chevron-down toggle-dropdown"></i>

                        </a>


                        <ul>

                            <li>

                                <a href="{{ route('exporters') }}">

                                    Exporter

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('suppliers') }}">

                                    Suppliers

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('dealers') }}">

                                    Dealers

                                </a>

                            </li>


                            <li>

                                <a href="{{ route('producers') }}">

                                    Producers

                                </a>

                            </li>

                        </ul>

                    </li>



                    {{-- =================================================
                         GALLERY
                    ================================================== --}}
                    <li>

                        <a href="#">

                            Gallery

                        </a>

                    </li>



                    {{-- =================================================
                         CONTACT
                    ================================================== --}}
                    <li>

                        <a href="{{ route('contact') }}">

                            Contact

                        </a>

                    </li>



                    {{-- =================================================
                         COMPLIANCE
                    ================================================== --}}
                    <li>

                        <a href="{{route('compliance')}}">

                            Compliance

                        </a>

                    </li>



                    {{-- =================================================
                         BLACKLISTED
                    ================================================== --}}
                    <li>

                        <a href="{{ route('blacklisted-members') }}"
                            class="{{ request()->routeIs('blacklisted-members') ? 'active' : '' }}">

                            Blacklisted

                        </a>

                    </li>

                </ul>



                {{-- MOBILE NAVIGATION --}}
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>

            </nav>



            {{-- =================================================
                 JOIN NOW
            ================================================== --}}
            @auth

                @if (auth()->user()->role === 'admin')

                    <a class="cta-btn"
                        href="{{ route('admin.admin_dashboard') }}">

                        Dashboard

                    </a>

                @elseif(auth()->user()->role === 'member')

                    <a class="cta-btn"
                        href="{{ route('member.member_dashboard') }}">

                        Dashboard

                    </a>

                @endif

            @else

                <a class="cta-btn"
                    href="{{ route('register') }}">

                    Join Now

                </a>

            @endauth

        </div>

    </div>

</header>



{{-- =========================================================
     BLACKLIST NEWS

     ONLY DISPLAY WHEN THERE IS AT LEAST ONE
     BLACKLISTED MEMBER IN THE DATABASE.
========================================================= --}}

@if($hasBlacklistedMembers)

    <div class="blacklist-news">

        <span class="title">

            <i class="fas fa-exclamation-triangle"></i>

            BLACKLISTED STAKEHOLDERS

        </span>


        <marquee
            behavior="scroll"
            direction="left"
            scrollamount="7">

            BLACKLISTED STAKEHOLDERS ARE LISTED IN THE
            NACPDEAN DIRECTORY.

            &nbsp;&nbsp; | &nbsp;&nbsp;

            Members are advised to exercise caution when
            dealing with listed stakeholders.

            &nbsp;&nbsp; | &nbsp;&nbsp;

            <a href="{{ route('blacklisted-members') }}"
                style="color: inherit; text-decoration: underline; font-weight: 700;">

                Click here to view the complete
                Blacklisted Stakeholders Directory.

            </a>

        </marquee>

    </div>

@endif
