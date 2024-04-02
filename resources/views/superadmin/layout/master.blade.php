
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>EFU TeleSales Insurance Portal</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <!-- Include DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Include DataTables Buttons JavaScript -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- Include DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <!-- <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    /> -->

    <!-- // -->

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{asset('/assets/vendor/fonts/boxicons.css')}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css')}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/theme-default.css')}}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/demo.css')}}" />
    <link rel="stylesheet" href="{{ asset('/assets/css/counter.css') }}">
    <!-- Add these links to your HTML file or layout file -->

    <!-- Data Tables -->






    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>

    <!-- Helpers -->
    <script src="{{ asset('/assets/vendor/js/helpers.js')}}"></script>


    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('/assets/js/config.js')}}"></script>

  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo" style= "height:90px">
            <a href="#" class="app-brand-link">
            <img src="{{ asset('/assets/img/logo.png')}}" alt="Your Logo">
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <li class="menu-header small text-uppercase"><span class="menu-header-text">Side MenuBar</span></li>
          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item {{ Request::is('super-admin/dashboard') ? 'active' : '' }}">
              <a href="{{ route('superadmin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Companies Management</span></li>
            <!-- Cards -->
            <li class="menu-item {{ Request::is('super-admin/company/create') ? 'active' : '' }}">
              <a href="{{ route('company.create') }}" id = "salespagebutton" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Create New Company</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/company') ? 'active' : '' }}">
              <a href="{{ route('company.index') }}" id = "salespagebutton" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Check Company Status</div>
              </a>
            </li>


            <!-- Layouts -->


                       <!-- Components -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Sales Agent Management</span></li>
            <!-- Cards -->
            <li class="menu-item {{ Request::is('super-admin/telesales-agents/create') ? 'active' : '' }}">
              <a href="{{ route('telesales-agents.create') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Create New Agent</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/telesales-agents') ? 'active' : '' }}">
              <a href="{{ route('telesales-agents.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Basic">Check Agent Status</div>
              </a>
            </li>



                       <!-- Components  Manager -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Company Manager </span></li>
            <!-- Cards -->
            <li class="menu-item {{ Request::is('super-admin/company_manager/create') ? 'active' : '' }}">
              <a href="{{ route('superadmin.company_manager_create') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Create New Company Manager</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/company_manager/index') ? 'active' : '' }}">
              <a href="{{ route('superadmin.company_manager_index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Basic">Check Company Managers</div>
              </a>
            </li>


                       <!-- Super Agent -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Super Agent Management</span></li>
            <!-- Cards -->
            <li class="menu-item {{ Request::is('super-admin/super_agent/create') ? 'active' : '' }}">
              <a href="{{ route('superadmin.super_agent_create') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Create New Super Agent</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/super_agent/index') ? 'active' : '' }}">
              <a href="{{ route('superadmin.super_agent_index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Basic">Check Super Agents</div>
              </a>
            </li>



            <!-- User interface -->

            <li class="menu-header small text-uppercase"><span class="menu-header-text">IPS System Reports</span></li>
            <!-- Forms -->
            <li class="menu-item {{ Request::is('super-admin/datatable') ? 'active' : '' }}">
              <a href="{{ route('superadmin.datatable') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Net Enrollment Report</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/datatable-failed') ? 'active' : '' }}">
              <a href="{{ route('superadmin.datatable-failed') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Failed Sales Request</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/complete-active-subscriptions') ? 'active' : '' }}">
              <a href="{{ route('superadmin.complete-active-subscriptions') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Total Subscription</div>
              </a>
            </li>

            <li class="menu-item {{ Request::is('super-admin/companies-cancelled-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.companies-cancelled-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Cancelled Subscriptions</div>
              </a>
            </li>

            <li class="menu-item  {{ Request::is('super-admin/refunds-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.refunds-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Refunded Transactions</div>
              </a>
            </li>

             <li class="menu-item  {{ Request::is('super-admin/recusive/chargingdataindex') ? 'active' : '' }}">
                <a href="{{ route('superadmin.recusive-charging-data-index') }}" class="menu-link">
                  <i class="menu-icon tf-icons bx bx-collection"></i>
                  <div data-i18n="Basic">Recursive Charging Report</div>
                </a>
              </li>
            <!-- Misc -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Refund Manager</span></li>
            <li class="menu-item {{ Request::is('super-admin/manage-refunds') ? 'active' : '' }}">
              <a href="{{ route('superadmin.manage-refunds') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Manage Refunds</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/refunds-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.refunds-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Refunded Report</div>
              </a>
            </li>

            <li class="menu-item">
              <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Bulk Refunds Manager</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Companies Reports</span></li>
            <li class="menu-item {{ Request::is('super-admin/companies-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.companies-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Net Enrollment Report</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/companies-cancelled-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.companies-cancelled-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Unsubscription Report</div>
              </a>
            </li>

            <li class="menu-item {{ Request::is('super-admin/companies-failed-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.companies-failed-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Failed Sale Report</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Agents Reports</span></li>
            <li class="menu-item {{ Request::is('super-admin/agents-reports') ? 'active' : '' }}">
              <a href="{{ route('superadmin.agents-reports') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Net Enrollment Report</div>
              </a>
            </li>
            <li class="menu-item {{ Request::is('super-admin/agents-sales-request') ? 'active' : '' }}">
              <a href="{{ route('superadmin.agents-sales-request') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Total Sales Request Report</div>
              </a>
            </li>
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none"
                    placeholder="Search..."
                    aria-label="Search..."
                  />
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-3">
                  <a
                    class="github-button"
                    href="#"
                    data-icon="octicon-star"
                    data-size="large"
                    data-show-count="true"
                    aria-label=""
                    >Super Admin Name: {{ session('Superadmin')->firstname }}</a
                  >
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="{{ asset('/assets/img/avatars/1.png')}}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="{{asset('/assets/img/avatars/1.png')}}" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block">{{ session('Superadmin')->firstname }}</span>
                            <small class="text-muted">Marketing Captain</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>

                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>

                  </ul>
                </li>

<form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-fluid flex-grow-1 container-p-y">
              <!-- Layout Demo -->
              <div class="">
                <div class="">

                <!-- Here we Have to Put our Things Jahangir khan  -->

                <!-- Start -->

                @yield('content')

                <!-- End -->

              <!-- Buttons -->

            </div>



          </div>
        </div>
              <!--/ Layout Demo -->
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-fluid d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  , Developed By
                  <a href="https://www.efulife.com" target="_blank" class="footer-link fw-bolder">EFU LIFE Tech Team</a>
                </div>
                <div>
                  <a href="#" class="footer-link me-4" target="_blank">License</a>


                  <a
                    href="#"
                    target="_blank"
                    class="footer-link me-4"
                    >Support</a
                  >

                  <a
                    href="#"
                    target="_blank"
                    class="footer-link me-4"
                    >Support</a
                  >
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <div class="buy-now">
      <a
        href="#"
        target="_blank"
        class="btn btn-danger btn-buy-now"
        >Contact IT Support </a
      >
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <!-- <script src="{{asset ('/assets/vendor/libs/jquery/jquery.js')}}"></script> -->
    <script src="{{asset ('/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset ('/assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{asset ('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{asset ('/assets/vendor/js/menu.js')}}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{asset ('/assets/js/main.js')}}"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->




  <script>
  let table = new DataTable('#subscription');
  </script>


  </body>
</html>
