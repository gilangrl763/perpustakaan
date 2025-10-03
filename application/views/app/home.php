<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");

$this->load->model("PetunjukPenggunaan");
$this->load->model("Faq");
$petunjuk_penggunaan = new PetunjukPenggunaan();
$faq = new Faq();
?>
<!-- SLICK -->
<link rel="stylesheet" type="text/css" href="lib/slick-1.8.1/slick/slick.css">
<link rel="stylesheet" type="text/css" href="lib/slick-1.8.1/slick/slick-theme.css">
<style type="text/css">
    .slider {
        width: 100%;
        margin: 0px auto;
    }

    .slick-slide {
        /*margin: 0px 20px;*/
    }

    .slick-slide img {
        width: 100%;
    }

    .slick-prev:before,
    .slick-next:before {
        color: black;
    }


    .slick-slide {
        transition: all ease-in-out .3s;
        opacity: 1;
    }

    .slick-active {
        opacity: 1;
    }

    .slick-current {
        opacity: 1;
    }

    .thead-dark {
        background-color: #31bde7;
        color: #fff;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="judul-halaman">Dashboard</div>
        </div>
        <div class="konten">
            <div class="col-md-4"> <!-- kiri -->
                <div class="row"> <!-- selamat datang -->
                    <div class="col-md-12">
                        <i>Selamat Datang, </i>
                        <span class="selamat-datang-nama"><?= $this->NAMA_PEGAWAI ?></span>
                        <br>Di Aplikasi Manajemen Kearsipan

                    </div>
                </div>
                <div class="row"> <!-- visi misi -->
                    <div class="col-md-12">
                        <div class="area-visi-misi">
                            <div class="item visi">
                                <div class="judul">Visi</div>
                                <div class="inner">
                                    Menjadi Pengembang Airport City serta Penyedia Jasa Konstruksi yang Unggul dan Terpercaya di Indonesia
                                </div>
                            </div>
                            <div class="item misi">
                                <div class="judul">Misi</div>
                                <div class="inner">
                                    <ul>
                                        <li>Mendukung perkembangan sektor aviasi dan pariwisata Indonesia.</li>
                                        <li>Menerapkan pembangunan yang berkelanjutan dalam rangka meningkatkan nilai bagi stakeholder.</li>
                                        <li>Menyediakan produk dan jasa yang memuaskan dan bermutu tinggi dengan memanfaatkan teknologi terbaik, serta dengan memperhatikan aspek lingkungan.</li>
                                        <li>Membangun network untuk mengembangkan produk dan jasa yang unggul.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row"> <!-- petunjuk penggunaan -->
                    <div class="col-md-12">
                        <div class="area-petunjuk-penggunaan">
                            <div class="judul">
                                <div class="ikon"><img src="images/icon-instruction.png"></div>
                                <div class="title">
                                    Petunjuk Penggunaan
                                    <span>Daftar Petunjuk Penggunaan Sistem</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="inner">
                                <section class="vertical slider">
                                    <?php
                                    $petunjuk_penggunaan->selectByParamsMonitoring(array("STATUS" => "AKTIF"));
                                    // echo $petunjuk_penggunaan->query;exit;
                                    while ($petunjuk_penggunaan->nextRow()) {
                                        $PETUNJUK_PENGGUNAAN_ID = $petunjuk_penggunaan->getField("PETUNJUK_PENGGUNAAN_ID");
                                        $NAMA                   = $petunjuk_penggunaan->getField("NAMA");
                                        $USER_GROUP             = $petunjuk_penggunaan->getField("USER_GROUP");
                                        $KETERANGAN             = $petunjuk_penggunaan->getField("KETERANGAN");
                                        $DOKUMEN                = $petunjuk_penggunaan->getField("DOKUMEN");
                                        $UKURAN_DOKUMEN         = $petunjuk_penggunaan->getField("UKURAN_DOKUMEN");

                                        if (getExtension($DOKUMEN) == "pdf") {
                                            $icon = "fa-file-pdf-o";
                                        } else {
                                            $icon = "fa-file-video-o";
                                        }
                                    ?>
                                        <div class="item">
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <div class="ikon-file"><i class="fa <?= $icon ?>"></i></div>
                                                </div>
                                                <div class="col-md-8 padding-none">
                                                    <div class="nama"><?= $NAMA ?></div>
                                                    <div class="ukuran"><?= round(($UKURAN_DOKUMEN / 1024 / 1024), 2) ?> MB</div>
                                                </div>
                                                <div class="col-md-2 padding-none">
                                                    <?php
                                                    if (!empty($DOKUMEN) && file_exists('uploads/petunjuk_penggunaan/' . $DOKUMEN)) {
                                                    ?>
                                                        <a href="javascript:void(0)" class="btn btn-link" onclick="openPopup('uploads/petunjuk_penggunaan/<?= $DOKUMEN ?>')">
                                                            <img src="images/icon-download.png">
                                                        </a>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SLICK -->
<!-- <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script> -->
<script src="lib/slick-1.8.1/slick/slick.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    $(document).on('ready', function() {
        $(".vertical").slick({
            dots: false,
            vertical: true,
            slidesToShow: 8,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
            // centerMode: false,
        });

        $(".vertical-eform").slick({
            dots: false,
            vertical: true,
            slidesToShow: 4,
            slidesToScroll: 1,

            // dots: false,
            // vertical: true,
            // slidesToShow: 10,
            // slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            arrows: false,
            // centerMode: false,
        });

        $(".regular").slick({
            dots: false,
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1
        });

        $(".regular-aep").slick({
            dots: false,
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1
        });

        $(".regular-asp").slick({
            dots: false,
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1
        });

        $(".regular-kendaraan").slick({
            dots: false,
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1
        });
    });
</script>

<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
        $('#bandaraId').on('change', function() {
            var bandaraId = $("#bandaraId").val();
            document.location.href = "app/loadUrl/app/home/?reqBandaraId=" + bandaraId;
        });
    });
</script>

<script src="libraries/Highcharts-11.1.0/code/highcharts.js"></script>
<script src="libraries/Highcharts-11.1.0/code/modules/exporting.js"></script>
<script src="libraries/Highcharts-11.1.0/code/modules/export-data.js"></script>
<script src="libraries/Highcharts-11.1.0/code/modules/accessibility.js"></script>
<script type="text/javascript">
    // Data retrieved from https://netmarketshare.com/
    // Radialize the colors
    Highcharts.setOptions({
        colors: Highcharts.map(Highcharts.getOptions().colors, function(color) {
            return {
                radialGradient: {
                    cx: 0.5,
                    cy: 0.3,
                    r: 0.7
                },
                stops: [
                    [0, color],
                    [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                ]
            };
        })
    });

    // Build the chart
    Highcharts.chart('laporan-kejadian', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            backgroundColor: null
        },
        title: {
            text: 'Laporan Kejadian',
        },
        subtitle: {
            text: 'Tahun 2023'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    shadow: false,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    connectorColor: 'silver'
                }
            }
        },
        series: [{
            name: '',
            data: [{
                    name: 'Security Contigency',
                    y: 40
                },
                {
                    name: 'Airport Disaster',
                    y: 25
                },
                {
                    name: 'Aircraft Accident',
                    y: 10
                },
                {
                    name: 'Domestic Fire',
                    y: 25
                }
            ]
        }]
    });


    Highcharts.chart('unjuk-kerja-kendaraan', {
        chart: {
            type: 'column',
            backgroundColor: null
        },
        title: {
            text: 'Unjuk Kerja Kendaraan'
        },
        subtitle: {
            text: 'Tahun 2023'
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Hasil'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Kapasitas Air',
            data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4,
                194.1, 95.6, 54.4
            ]

        }, {
            name: 'Kapasitas Foam',
            data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5,
                106.6, 92.3
            ]

        }, {
            name: 'Kapasitas DCP',
            data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3,
                51.2
            ]

        }, {
            name: 'Akselerasi',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8,
                51.1
            ]

        }, {
            name: 'Top Speed',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8,
                51.1
            ]

        }, {
            name: 'Discharge Rate',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8,
                51.1
            ]

        }, {
            name: 'Discharge Range',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8,
                51.1
            ]

        }, {
            name: 'Stop Distance',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8,
                51.1
            ]

        }]
    });


    Highcharts.chart('evaluasi-personil-arff', {
        chart: {
            type: 'column',
            backgroundColor: null
        },
        title: {
            text: 'Evaluasi Personil ARFF'
        },
        subtitle: {
            text: 'Tahun 2023'
        },
        xAxis: {
            categories: [
                'DPS',
                'YIA',
                'UPG',
                'BPN',
                'LOP',
                'SRG',
                'SUB',
                'BDJ',
                'MDC',
                'SOC',
                'KOE',
                'AMQ',
                'DJJ',
                'JOG',
                'BIK'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Hasil'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'EVALUASI TEORI',
            data: [4.46, 3.03, 4.05, 4.17, 4.72, 4.08, 3.64, 4.35, 4.28, 3.67, 4.35, 3.85, 4.19, 4.44, 3.23, 3.67]

        }, {
            name: 'EVALUASI PRAKTEK',
            data: [4.06, 4.06, 4.09, 4.55, 4.66, 3.98, 3.91, 4.44, 4.08, 4.44, 4.41, 4.23, 3.93, 3.16, 3.62, 3.74]

        }, {
            name: 'TES KEBUGARAN',
            data: [4.50, 4.20, 4.44, 4.29, 4.37, 3.64, 3.85, 4.04, 4.25, 4.08, 4.44, 4.60, 2.84, 3.79, 3.55, 4.15]

        }, {
            name: 'KEDISIPLINAN',
            data: [4.76, 4.71, 4.71, 4.63, 4.89, 3.91, 4.65, 5.00, 4.82, 4.98, 5.00, 4.65, 4.71, 2.34, 4.70]

        }, {
            name: 'BMI',
            data: [3.48, 3.91, 3.68, 3.71, 3.67, 3.85, 4.95, 3.53, 3.50, 4.35, 4.00, 3.89, 3.97, 3.86, 3.74]

        }]
    });
</script>