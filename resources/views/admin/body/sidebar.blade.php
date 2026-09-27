            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="{{ route('admin.admin_dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>

                            <div class="sb-sidenav-menu-heading">Applicants</div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseApplicants" aria-expanded="false"
                                aria-controls="collapseApplicants">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Create
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseApplicants" aria-labelledby="headingApplicants"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('admin.member.create') }}">New Applicant</a>
                                    <a class="nav-link" href="{{ route('admin.member.index') }}">List of Applicants</a>
                                </nav>
                            </div>


                            <div class="sb-sidenav-menu-heading">Members</div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseMembers" aria-expanded="false" aria-controls="collapseMembers">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Members
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseMembers" aria-labelledby="headingMembers"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('admin.member.approved') }}">Approved Members</a>
                                    <a class="nav-link" href="{{ route('admin.blacklist.index') }}">Blacklisted
                                        Members</a>
                                    <a class="nav-link" href="{{ route('admin.members.index') }}">Applicants/Members</a>
                                </nav>
                            </div>

                            {{-- =========================================================
                                 PENALTIES — SUPER ADMIN ONLY
                            ========================================================== --}}
                            @if (auth()->check() && auth()->user()->role === 'super_admin')
                                <div class="sb-sidenav-menu-heading">Super Admin</div>

                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                    data-bs-target="#collapsePenalties" aria-expanded="false"
                                    aria-controls="collapsePenalties">
                                    <div class="sb-nav-link-icon"><i class="fas fa-gavel"></i></div>
                                    Penalties
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapsePenalties" aria-labelledby="headingPenalties"
                                    data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="{{ route('admin.penalties.index') }}">
                                            Penalty Catalog
                                        </a>
                                        <a class="nav-link" href="{{ route('admin.penalties.assignments') }}">
                                            Assigned Penalties
                                        </a>
                                        <a class="nav-link" href="{{ route('admin.penalties.assign.form') }}">
                                            Assign Penalty
                                        </a>
                                    </nav>
                                </div>
                            @endif

                            {{-- Task Force & Appointments --}}
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseAppointments" aria-expanded="false"
                                aria-controls="collapseAppointments">
                                <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                                Appointments
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseAppointments" aria-labelledby="headingAppointments"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('admin.taskforce.index') }}">All Appointments</a>
                                    <a class="nav-link"
                                        href="{{ route('admin.taskforce.create', ['type' => 'task_force']) }}">New Task
                                        Force</a>
                                    <a class="nav-link"
                                        href="{{ route('admin.taskforce.create', ['type' => 'national_executive']) }}">New
                                        National Exec</a>
                                </nav>
                            </div>

                            <a class="nav-link" href="{{ route('admin.logout') }}">
                                <div class="sb-nav-link-icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                Logout
                            </a>

                            {{--  <a class="nav-link" href="{{ route('admin.members.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Members
                            </a>
                            <a class="nav-link" href="{{ route('admin.member.approved') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Approved Members
                            </a>
                            <a class="nav-link" href="{{ route('admin.blacklist.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Blacklisted Members
                            </a>

                            <div class="sb-sidenav-menu-heading">Payment</div>
                            <a class="nav-link" href="{{ route('admin.payment-items.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                                Payment Items
                            </a>  --}}
                            {{--  <a class="nav-link" href="{{ route('admin.payment-items.create') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-plus-circle"></i></div>
                                Create Payment Item
                            </a>

                            <div class="sb-sidenav-menu-heading">Documents</div>
                            <a class="nav-link" href="{{ route('admin.documents.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                Documents
                            </a>
                            <a class="nav-link" href="{{ route('admin.documents.create') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-plus-circle"></i></div>
                                Create Document
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>  --}}
                            {{--  <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Pages
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo"
                                data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                        data-bs-target="#pagesCollapseAuth" aria-expanded="false"
                                        aria-controls="pagesCollapseAuth">
                                        Authentication
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne"
                                        data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="login.html">Login</a>
                                            <a class="nav-link" href="register.html">Register</a>
                                            <a class="nav-link" href="password.html">Forgot Password</a>
                                        </nav>
                                    </div>
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                        data-bs-target="#pagesCollapseError" aria-expanded="false"
                                        aria-controls="pagesCollapseError">
                                        Error
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne"
                                        data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="401.html">401 Page</a>
                                            <a class="nav-link" href="404.html">404 Page</a>
                                            <a class="nav-link" href="500.html">500 Page</a>
                                        </nav>
                                    </div>
                                </nav>
                            </div>  --}}
                            {{--  <div class="sb-sidenav-menu-heading">Addons</div>
                            <a class="nav-link" href="charts.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Charts
                            </a>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a>  --}}
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        NACPDEAN
                    </div>
                </nav>
            </div>
