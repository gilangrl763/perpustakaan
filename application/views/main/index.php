<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8" />
        <title>TRAINESIA | Pemesan Tiket by Gilang Romy Lesmana</title>
        <base href="<?=base_url()?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="images/favicon.ico">

        <!-- plugin css -->
        <link href="libraries/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <link href="libraries/mohithg-switchery/switchery.min.css" rel="stylesheet" type="text/css" />
        <link href="libraries/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
        <link href="libraries/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="libraries/selectize/css/selectize.bootstrap3.css" rel="stylesheet" type="text/css" />
        <link href="libraries/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

        <!-- Animation css -->
        <link href="libraries/animate.css/animate.min.css" rel="stylesheet" type="text/css" />
        <link href="libraries/animate.css/animate.compat.css" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="css/config/default/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
        <link href="css/config/default/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

        <link href="css/config/default/bootstrap-dark.min.css" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" />
        <link href="css/config/default/app-dark.min.css" rel="stylesheet" type="text/css" id="app-dark-stylesheet" />

        <!-- icons -->
        <link href="css/icons.min.css" rel="stylesheet" type="text/css" />

    </head>

    <body class="loading" data-layout-mode="horizontal" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "topbar": {"color": "dark"}, "showRightSidebarOnPageLoad": true}'>

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Topbar Start -->
            <div class="navbar-custom">
                <div class="container-fluid">
                    <ul class="list-unstyled topnav-menu float-end">                
                        <li class="dropdown notification-list topbar-dropdown">
                            <?
                            //JIKA BELUM LOGIN
                            if($this->USER_TYPE_ID == ""){
                            ?>
                            <a href="login" class="btn btn-sm btn-info nav-link nav-user me-0 waves-effect waves-light">LOGIN</a>
                            <?
                            }

                            //JIKA LOGIN SEBAGAI CUSTOMER
                            if($this->USER_TYPE_ID == "2"){
                            ?>
                            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="images/users/user-1.jpg" alt="user-image" class="rounded-circle">
                                <span class="pro-user-name ms-1">
                                    <?=$this->NAMA?> <i class="mdi mdi-chevron-down"></i> 
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Selamat Datang !</h6>
                                </div>
                                <a href="main/index/ubah_password" class="dropdown-item notify-item">
                                    <i class="fe-settings"></i>
                                    <span>Ubah Password</span>
                                </a>
                                <a href="login/logout" class="dropdown-item notify-item">
                                    <i class="fe-log-out"></i>
                                    <span>Logout</span>
                                </a>
    
                            </div>
                            <?
                            }
                            ?>
                        </li>
                    </ul>
                    
    
                    <!-- LOGO -->
                    <div class="logo-box">
                        <a href="main/index" class="logo logo-dark text-center">
                            <span class="logo-sm">
                                <img src="images/logo.png" alt="" height="50">
                                <!-- <span class="logo-lg-text-light">UBold</span> -->
                            </span>
                            <span class="logo-lg">
                                <img src="images/logo.png" alt="" height="50">
                                <!-- <span class="logo-lg-text-light">U</span> -->
                            </span>
                        </a>
    
                        <a href="main/index" class="logo logo-light text-center">
                            <span class="logo-sm">
                                <img src="images/logo.png" alt="" height="50">
                            </span>
                            <span class="logo-lg">
                                <img src="images/logo.png" alt="" height="50">
                            </span>
                        </a>
                    </div>
    
                    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                        <li>
                            <button class="button-menu-mobile waves-effect waves-light">
                                <i class="fe-menu"></i>
                            </button>
                        </li>

                        <li>
                            <!-- Mobile menu toggle (Horizontal Layout)-->
                            <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </li> 

                        <li class="dropdown d-none d-xl-block">
                            <a class="nav-link dropdown-toggle waves-effect waves-light" href="main/index">Beranda</a>
                        </li>  
            
                        <li class="dropdown d-none d-xl-block">
                            <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                Tentang Kami
                                <i class="mdi mdi-chevron-down"></i> 
                            </a>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="fe-briefcase me-1"></i>
                                    <span>Visi Misi</span>
                                </a>
                            </div>
                        </li>

                        <?
                        //JIKA LOGIN SEBAGAI CUSTOMER
                        if($this->USER_TYPE_ID != ""){
                        ?>
                        <li class="dropdown d-none d-xl-block">
                            <a class="nav-link dropdown-toggle waves-effect waves-light" href="main/index/pesanan_saya">Pesanan Saya</a>
                        </li>
                        <?
                        }
                        ?>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- end Topbar -->

           
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page bg-primary">
                <div class="content mb-5">
                    <div class="container-fluid">
                        <?=($content ? $content : '')?>
                    </div>
                </div>

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <script>document.write(new Date().getFullYear())</script> &copy; Tugas Kominfo by Gilang Romy Lesmana
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-end footer-links d-none d-sm-block">
                                    <a href="javascript:void(0);">About Us</a>
                                    <a href="javascript:void(0);">Help</a>
                                    <a href="javascript:void(0);">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->
            </div>
            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->
        </div>
        <!-- END wrapper -->

        <div class="modal fade" id="iframeModal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-scrollable" role="document">
              <div class="modal-content">
                <div class="modal-header bg-gradient-4">
                  <h5 class="modal-title text-white" id="scrollableModalTitle">TRAINESIA | Pemesan Tiket by Gilang Romy Lesmana</h5>
                  <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-pattern">
                  <div class="spinner-border avatar-sm text-center m-2" id="spinnerIframe" role="status" style="display:none"></div>
                  <iframe id="customIframeModal" src="" frameborder="0" style="width:100%;height:80vh;display:block;"></iframe>
                </div>
              </div>
            </div>
          </div>

        <!-- Vendor js -->
        <script src="js/vendor.min.js"></script>

        <!-- third party js -->
        <script src="libraries/bootstrap-table/bootstrap-table.min.js"></script>
        <!-- third party js ends -->

        <!-- Datatables init -->
        <script src="js/pages/bootstrap-tables.init.js"></script>

        <!-- App js -->
        <script src="js/app.min.js"></script>

        <!-- EASYUI -->
        <link rel="stylesheet" type="text/css" href="libraries/easyui/themes/default/easyui.css">
        <script type="text/javascript" src="libraries/easyui/jquery.easyui.min.js"></script>
        <script type="text/javascript" src="libraries/easyui/kalender-easyui.js"></script>
        <script type="text/javascript" src="libraries/easyui/globalfunction.js"></script>

        <script>
        function ubahUrlIframe(iframeName, url) {
            var $iframe = $('#'+iframeName);
            if ($iframe.length) {
                $iframe.attr('src',url);
                    return false;
            }

            return true;
        }

        function closeSpinner(){
        $("#spinnerIframe").fadeOut(); 
        }

        function openModal(pageUrl){
            ubahUrlIframe('customIframeModal',pageUrl);
            // $("#spinnerIframe").show();

            $('#iframeModal').modal('show');
        }
        </script>
    </body>
</html>