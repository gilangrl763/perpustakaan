<?
include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/string.func.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>SMKN 1 | Aplikasi Manajemen Perpustakaan</title>
    <base href="<?= base_url() ?>" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Manajemen Perpustakaan SMKN 1">
    <meta name="author" content="SMKN 1">

    <link rel="icon" type="image/png" href="images/favicon.ico" />

    <link href="libraries/bootstrap-3.3.7/docs/dist/css/bootstrap.css" rel="stylesheet">
    <link href="libraries/bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="libraries/bootstrap-3.3.7/docs/examples/navbar-fixed-top/navbar-fixed-top.css" rel="stylesheet">
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>

    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
</head>

<body>
    <nav class="navbar navbar-default navbar-fixed-top" id="header">
        <div class="container-fluid">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="app/loadUrl/app/home" target="mainFrame">
                    <img src="images/logo-smk.png" alt="logo">
                </a>
            </div>

            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">

                    <!-- <li><a href="app/loadUrl/app/helpdesk" target="mainFrame">Helpdesk</a></li> -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Perusahaan <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_perusahaan" target="mainFrame">Perusahaan</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_cabang" target="mainFrame">Cabang</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_satuan_kerja" target="mainFrame">Satuan Kerja</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_pegawai" target="mainFrame">Pegawai</a></li>
                        </ul>
                    </li>

                    <li><a href="app/loadUrl/app/master_user_login" target="mainFrame">User Login</a></li>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Referensi <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="app/loadUrl/app/master_data_tingkat_perkembangan" target="mainFrame">Tingkat Perkembangan</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/master_data_penyusutan_akhir" target="mainFrame">Penyusutan Akhir</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/master_data_media_simpan" target="mainFrame">Media Simpan</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/master_data_lokasi_simpan" target="mainFrame">Lokasi Simpan</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/master_data_kondisi_fisik" target="mainFrame">Kondisi Fisik</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/master_data_klasifikasi" target="mainFrame">Klasifikasi</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Pengaturan Umum <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_banner" target="mainFrame">Banner</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_faq" target="mainFrame">F.A.Q</a></li>
                            <li class="dropdown-item"><a href="app/loadUrl/app/konfigurasi_petunjuk_penggunaan" target="mainFrame">Petunjuk Penggunaan</a></li>
                        </ul>
                    </li>

                    <li><a href="app/loadUrl/app/pemindahan" target="mainFrame">Pemindahan</a></li>
                    <li><a href="app/loadUrl/app/peminjaman" target="mainFrame">Peminjaman</a></li>
                    <li><a href="app/loadUrl/app/usul_musnah" target="mainFrame">Usul Musnah</a></li>


                    <li class="active" title="Dashboard"><a href="app/loadUrl/app/home" target="mainFrame"><i class="fa fa-dashboard"></i></a></li>

                    <!-----NOTIFIKASI----->
                    <li class="dropdown active">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="icon-notifikasi"><i class="fa fa-bell"></i>
                                <span class="badge badge-danger badge-notifikasi">&nbsp;</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu notify-drop">
                            <div class="notify-drop-title">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-6">Notifikasi Sistem</div>
                                    <div class="col-md-6 col-sm-6 col-xs-6 text-right"><a href="app/loadUrl/app/notifikasi" class="rIcon allRead" data-tooltip="tooltip" data-placement="bottom" title="Selengkapnya"><i class="fa fa-dot-circle-o"></i></a></div>
                                </div>
                            </div>
                            <div class="drop-content">
                                <li>
                                    <div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                                        <i class="fa fa-exclamation-triangle"></i> Data tidak tersedia
                                    </div>
                                </li>

                                <li id="item-notifikasi-<?= $NOTIFIKASI_ID ?>" onclick="bacaNotifikasi('<?= $NOTIFIKASI_ID ?>','<?= $NOTIFIKASI_LINK ?>')">
                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                        <div class="notify-img">
                                            <i class="fa fa-check-square-o"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-10 col-sm-10 col-xs-10 pd-l0">
                                        <a href="<?= $NOTIFIKASI_LINK ?>" target="mainFrame">Permohonan Validasi</a>
                                        <br>Daily Checklist FC/DAILY/II/2024
                                        <hr>
                                        <p class="time">02 Januari 2024 18:45</p>
                                    </div>
                                </li>

                            </div>
                            <div class="notify-drop-footer text-center">
                                <a href="app/loadUrl/app/notifikasi" target="mainFrame">Selengkapnya <i class="fa fa-long-arrow-right"></i></a>
                            </div>
                        </ul>
                    </li>
                    <!-----END NOTIFIKASI----->

                    <!-----ID SESSION----->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="foto"></div>
                            <div class="info">
                                <div class="nama"></div>
                                <div class="jabatan"></div>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="app/loadUrl/app/profil" target="mainFrame">Profil</a></li>
                            <li><a href="app/loadUrl/app/ubah_password" target="mainFrame">Ubah Password</a></li>
                        </ul>
                    </li>
                    <!-----END ID SESSION----->

                    <!-----ROLE----->

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="foto"><i class="fa fa-tags"></i></div>
                            <div class="info">
                                <div class="nama">Role :</div>
                                <div class="jabatan"><?= $this->USER_GROUP ?></div>
                            </div>
                        </a>
                        <ul class="dropdown-menu">

                        </ul>
                    </li>

                    <!-----END ROLE----->

                    <li class="logout">
                        <a href="javascript:void(0)" onclick="konfirmasiLogout()">
                            <i class="fa fa-sign-out"></i>
                            <label>LOGOUT</label>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>

    <div class="container-fluid container-main-frame">
        <iframe name="mainFrame" id="mainFrame" src="app/loadUrl/app/home"></iframe>
    </div>

    <footer id="footer" class="footer">
        <div class="copyright">&copy; <?= date("Y") ?>. <b>SMKN 1</b>. All Rights Reserved</div>
        <div class="menu-lain">
            <a href="javascript:void(0)" onclick="openPopup('app/loadUrl/app/faq')">FAQ</a>
            <a href="javascript:void(0)" onclick="openPopup('app/loadUrl/app/petunjuk_penggunaan')">Petunjuk Penggunaan</a>
            <!-- <a href="app/loadUrl/app/hubungi_kami" target="mainFrame">Hubungi Kami</a> -->
        </div>
    </footer>

    <script src="js/jquery.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')
    </script>
    <script src="libraries/bootstrap-3.3.7/docs/dist/js/bootstrap.min.js"></script>
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie10-viewport-bug-workaround.js"></script>

    <script>
        $(window).load(function() {
            $('.loader').delay(5000).fadeOut(800);
        });
    </script>

    <script src="libraries/eModal-master/dist/eModal.js"></script>
    <script>
        function openPopup(page) {
            eModal.iframe(page, 'TMJ | Aplikasi Manajemen Kearsipan')
        }

        function closePopup() {
            eModal.close();
        }
    </script>

    <link rel="stylesheet" type="text/css" href="libraries/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="libraries/easyui/themes/icon.css">
    <script type="text/javascript" src="libraries/easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="libraries/easyui/globalfunction.js"></script>
    <script type="text/javascript">
        function bacaNotifikasi(reqId, reqLink) {
            $.messager.confirm('Konfirmasi', 'Apakah anda yakin ingin membaca notifikasi?', function(r) {
                if (r) {
                    var win = $.messager.progress({
                        title: 'TMJ | Aplikasi Manajemen Kearsipan',
                        msg: 'proses data...'
                    });

                    var jqxhr = $.get('app/bacaNotifikasi/?reqId=' + reqId, function(data) {})
                        .done(function() {
                            $.messager.progress('close');
                            window.open(reqLink, 'contentFrame');

                            var jumlahNotifikasi = $(".badge-notifikasi").text();
                            var jumlahNotifikasiAkhir = jumlahNotifikasi - 1;
                            $(".badge-notifikasi").html(jumlahNotifikasiAkhir);
                            $("#item-notifikasi-" + reqId).remove();
                        })
                        .fail(function() {
                            $.messager.progress('close');
                            $.messager.alert('Info', 'Gagal membaca notifikasi', 'info');
                        });
                }
            });
        }

        function konfirmasiLogout() {
            $.messager.confirm('Konfirmasi', 'Apakah anda yakin ingin logout dari sistem?', function(r) {
                if (r) {
                    var win = $.messager.progress({
                        title: 'SMKN 1 | Aplikasi Manajemen Perpustakaan',
                        msg: 'Proses data...'
                    });

                    var jqxhr = $.get('login/logout', function(data) {
                            $.messager.progress('close');
                            $.messager.alertLink('Info', data, 'info', 'login');
                        })
                        .done(function() {
                            $.messager.progress('close');
                            $.messager.alertLink('Info', data, 'info', 'login');
                        })
                        .fail(function() {
                            $.messager.progress('close');
                            $.messager.alert('Info', 'error', 'info');
                        });
                }
            });
        }

        function ubahRole(role, roleLabel) {
            $.messager.confirm('Konfirmasi', 'Apakah anda yakin ingin mengubah role ke ' + roleLabel + '?', function(r) {
                if (r) {
                    var win = $.messager.progress({
                        title: 'TMJ | Aplikasi Manajemen Kearsipan',
                        msg: 'Proses data...'
                    });

                    $.post("login/change", {
                            reqId: role
                        })
                        .done(function(data) {
                            $.messager.progress('close');
                            top.location.href = 'app';
                        })
                        .fail(function() {
                            $.messager.progress('close');
                            $.messager.alert('Info', 'error', 'info');
                        });
                }
            });
        }

        $('.dropdown-submenu a.dropdown-submenu-toggle').on("click", function(e) {
            $('.dropdown-submenu ul').removeAttr('style');
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });

        $('#bs-navbar-collapse-1').on('hidden.bs.dropdown', function() {
            $('.navbar-nav .dropdown-submenu ul.dropdown-menu').removeAttr('style');
        });
    </script>
</body>

</html>