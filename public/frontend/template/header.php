<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header id="header" class="header sticky-top">
    <div class="topbar d-flex align-items-center">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="d-flex align-items-center social-icons">

                <!-- Facebook -->
                <a href="https://www.facebook.com/profile.php?id=61591564350891"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>

                <!-- Instagram -->
                <a href="https://www.instagram.com/nacpdean"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>

                <!-- X (Twitter) -->
                <a href="https://x.com/nacpdean"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="Twitter">
                    <i class="bi bi-twitter-x"></i>
                </a>

                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/in/nacpdean-nacpdean-17187041b/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="me-3"
                    aria-label="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/2348145672358"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                </a>

            </div>
            <div class="d-none d-md-flex align-items-center">
                <i class="bi bi-phone me-1"></i>
                Call us now +234 81 456 723 58
            </div>
        </div>
    </div>
    <!-- End Top Bar -->
    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-end">
            <a href="index.php" class="logo d-flex align-items-center me-auto">
                <img src="assets/img/logo.png" alt="">
                <!-- Uncomment the line below if you also wish to use a text logo -->
                <!-- <h1 class="sitename">Medicio</h1>  -->
            </a>
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li>
                        <a href="index.php"
                            class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">
                            Home
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="#"
                            class="<?= in_array($currentPage, ['about2.php', 'history.php', 'association.php']) ? 'active' : '' ?>">
                            <span>About Us</span>
                            <i class="bi bi-chevron-down toggle-dropdown"></i>
                        </a>

                        <ul>
                            <li><a href="about2.php">About Us</a></li>
                            <li><a href="bot.php">Board of Trustees</a></li>
                            <li><a href="history.php">History</a></li>
                            <li><a href="association.php">Association</a></li>
                            <li><a href="partnership.php">Partnership with SSN and GGLV</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#"
                            class="<?= in_array($currentPage, ['national-executive.php', 'state-executive.php']) ? 'active' : '' ?>">
                            <span>Executive</span>
                            <i class="bi bi-chevron-down toggle-dropdown"></i>
                        </a>

                        <ul>
                            <li><a href="national-executive.php">National Executive </a></li>
                            <li><a href="state-executive.php">State Executive </a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#"
                            class="<?= in_array($currentPage, ['national-members.php', 'state-members.php']) ? 'active' : '' ?>">
                            <span>Membership</span>
                            <i class="bi bi-chevron-down toggle-dropdown"></i>
                        </a>

                        <ul>
                            <li><a href="exporters.php">Exporter</a></li>
                            <li><a href="suppliers.php">Suppliers</a></li>
                            <li><a href="dealers.php">Dealers</a></li>
                            <li><a href="producers.php">Producers</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="gallery.php"
                            class="<?= ($currentPage == 'gallery.php') ? 'active' : '' ?>">
                            Gallery
                        </a>
                    </li>
                    <li>
                        <a href="contact.php"
                            class="<?= ($currentPage == 'contact.php') ? 'active' : '' ?>">
                            Contact
                        </a>
                    </li>
                    <li>
                        <a href="compliance.php"
                            class="<?= ($currentPage == 'compliance.php') ? 'active' : '' ?>">
                            Compliance
                        </a>
                    </li>
                    <li>
                        <a href="blacklisted-members.php"
                            class="<?= ($currentPage == 'blacklisted-members.php') ? 'active' : '' ?>">
                            Blacklisted
                        </a>
                    </li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
            <a class="cta-btn" href="#">Join Now</a>
        </div>
    </div>
</header>
<div class="blacklist-news">

    <span class="title">

        <i class="fas fa-exclamation-triangle"></i>

        BLACKLISTED STAKEHOLDERS

    </span>

    <marquee
        behavior="scroll"
        direction="left"
        scrollamount="7">

        John Doe (Lagos) |
        XYZ Charcoal Export Ltd |
        ABC Agro Company |
        Jane Smith |
        Click here to view the complete
        Blacklisted Stakeholders Directory.

    </marquee>

</div>