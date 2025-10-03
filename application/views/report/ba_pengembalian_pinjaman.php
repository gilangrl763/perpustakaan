<?
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("libraries/phpqrcode/qrlib.php");

$id = $this->input->get("id");

$this->load->model("Peminjaman");
$this->load->model("Perusahaan");
$peminjaman = new Peminjaman();
$peminjaman_tujuan = new Peminjaman();
$peminjaman_approval = new Peminjaman();
$perusahaan = new Perusahaan();

$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$id));
$peminjaman->firstRow();
$PERUSAHAAN_ID      = $peminjaman->getField("PERUSAHAAN_ID");
$NOMOR              = "BA.".$peminjaman->getField("NOMOR");
$PERIHAL            = $peminjaman->getField("PERIHAL");
$TANGGAL            = $peminjaman->getField("TANGGAL");

$time = strtotime($TANGGAL);
$hariInfoDokumen = date('w', $time);
$tanggalInfoDokumen = (int)date('d', $time);
$bulanInfoDokumen = (int)date('m', $time);
$tahunInfoDokumen = (int)date('Y', $time);

$peminjaman_approval->selectByParamsApproval(array("A.PEMINJAMAN_ID::varchar"=>$id),-1,-1,""," ORDER BY URUT DESC");
$peminjaman_approval->firstRow();

$peminjaman_tujuan->selectByParamsTujuan(array("A.PEMINJAMAN_ID::varchar"=>$id,"A.JENIS"=>"TUJUAN"));
$peminjaman_tujuan->firstRow();

$perusahaan->selectByParamsMonitoring(array("A.PERUSAHAAN_ID"=>$PERUSAHAAN_ID));
$perusahaan->firstRow();
$NAMA       = $perusahaan->getField("NAMA");
$ALAMAT     = $perusahaan->getField("ALAMAT");
$TELEPON    = $perusahaan->getField("TELEPON");
$WEBSITE    = $perusahaan->getField("WEBSITE");
$LOGO       = $perusahaan->getField("LOGO");
?>

<!-- Start Kop Surat -->
<div class="kop-surat">
  <div class="logo-kop">
    <img src="uploads/perusahaan/<?=$LOGO?>" width="130px" height="*">
  </div>
  <div class="alamat-kop">
    <span class="nama-perusahaan"><?=$NAMA?></span>
    <br><?=$ALAMAT?>
    <br>Telp : <?=$TELEPON?>
    <br>Web : <?=$WEBSITE?>
  </div>
</div>
<!-- End Kop Surat -->

<!-- Start Jenis Naskah -->
<div class="jenis-naskah">
  <div class="nama-jenis-naskah"><b><u>BERITA ACARA PEMINDAHAN ARSIP INAKTIF</u></b></div>
  <div class="nomor-naskah">Nomor : <?=$NOMOR?></div>
</div>
<!-- End Jenis Naskah -->

<!-- Start Isi Naskah -->
<div class="isi-naskah">
    <p>Pada hari ini <?=ucwords(getHari($hariInfoDokumen))?> tanggal <?=ucwords(getTerbilang($tanggalInfoDokumen))?> bulan <?=ucwords(getNameMonth($bulanInfoDokumen))?> tahun <?=ucwords(getTerbilang($tahunInfoDokumen))?> dilaksanakan Pemindahan Arsip Inaktif dari Unit Pengolahan <i><?=$peminjaman->getField("NAMA_SATUAN_KERJA")?></i> ke Unit Kearsipan, yang dilaksanakan oleh :</p>

    <table class="datatabel">
        <tbody>
            <tr>
                <td width="3%"></td>
                <td width="15%">Nama</td>
                <td width="2%">:</td>
                <td width="80%"><?=strtoupper($peminjaman_approval->getField("NAMA"))?></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td><?=strtoupper($peminjaman_approval->getField("JABATAN"))?></td>
            </tr>
        </tbody>
    </table>
    <p>Dalam hal ini bertindak atas nama Pimpinan Unit Pengolahan <i><?=$peminjaman->getField("NAMA_SATUAN_KERJA")?></i> yang selanjutnya disebut PIHAK PERTAMA.</p>
    <table class="datatabel">
        <tbody>
            <tr>
                <td width="3%"></td>
                <td width="15%">Nama</td>
                <td width="2%">:</td>
                <td width="80%"><?=strtoupper($peminjaman_tujuan->getField("NAMA"))?></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td><?=strtoupper($peminjaman_tujuan->getField("JABATAN"))?></td>
            </tr>
        </tbody>
    </table>
    <p>Dalam hal ini bertindak atas nama Pimpinan Unit Kearsipan yang selanjutnya disebut PIHAK KEDUA.</p>
    <p>Menyatakan telah mengadakan serah terima arsip inaktif yang dipindahkan sebagaimana tercantum dalam Daftar Pertelaan Arsip terlampir.</p>
</div>
<!-- End Isi Naskah -->

<table class="datatabel" style="margin-top: 50px;">
    <tbody>
        <tr>
            <td width="45%">PIHAK PERTAMA</td>
            <td width="10%"></td>
            <td width="45%">PIHAK KEDUA</td>
        </tr>
        <tr>
            <td>
                <b><?=strtoupper($peminjaman_approval->getField("JABATAN"))?></b>
                <?
                if($peminjaman_approval->getField("STATUS") == "APPROVE"){
                    $md5 = $id."_7mj4R5iP";
                    $qrText = base_url()."qr/index/pemindahan/".md5($md5);
                    $txt = QRcode::text($qrText);
                    $image = QRimage::image($txt,2,2);
                    ob_start();
                    imagepng($image);
                    $contents = ob_get_clean();
                    imagedestroy($image);
                    echo "<div class='barcode'><img src='data:image/png;base64,".base64_encode($contents)."' height='70px' width='*' /></div>";
                }
                else{
                    echo "<br><br><br><br>";
                }
                ?>
                <span style="font-size:10px;"><i>Ditandatangani secara elektronik</i></span>
            </td>
            <td>&nbsp;</td>
            <td>
                <b><?=strtoupper($peminjaman_tujuan->getField("JABATAN"))?></b>
                <?
                if($peminjaman_tujuan->getField("TERDISPOSISI") == "YA"){
                    $md5 = $id."_7mj4R5iP";
                    $qrText = base_url()."qr/index/pemindahan/".md5($md5);
                    $txt = QRcode::text($qrText);
                    $image = QRimage::image($txt,2,2);
                    ob_start();
                    imagepng($image);
                    $contents = ob_get_clean();
                    imagedestroy($image);
                    echo "<div class='barcode'><img src='data:image/png;base64,".base64_encode($contents)."' height='70px' width='*' /></div>";
                }
                else{
                    echo "<br><br><br><br>";
                }
                ?>
                <span style="font-size:10px;"><i>Ditandatangani secara elektronik</i></span>
            </td>
        </tr>
        <tr>
            <td>
                <b><u><?=strtoupper($peminjaman_approval->getField("NAMA"))?></u></b>
            </td>
            <td>&nbsp;</td>
            <td>
                <b><u><?=strtoupper($peminjaman_tujuan->getField("NAMA"))?></u></b>
            </td>
        </tr>
    </tbody>
</table>

<div style="page-break-before:always;"></div>
<div class="judul-lampiran">
    <table width="100%">
        <tr>
            <td colspan="3"><b><u>LAMPIRAN</u></b></td>
        </tr>
        <tr>
            <td width="20%">Nomor</td>
            <td width="3%">:</td>
            <td width="77%"><?=$NOMOR?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td><?=getFormattedDateView($peminjaman->getField("TANGGAL"))?></td>
        </tr>
    </table>
</div>
<!-- Start Isi Naskah -->
<div class="tujuan-lampiran">
    <table class="datatabel-border">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="27%">Klasifikasi</th>
                <th width="40%">Uraian</th>
                <th width="10%">Kurun Waktu</th>
                <th width="18%">Tingkat Perkembangan</th>
            </tr>
        </thead>
        <tbody>
            <?
            $no = 1;
            $peminjaman_berkas = new Peminjaman();
            $peminjaman_berkas->selectByParamsBerkas(array("A.PEMINJAMAN_ID::VARCHAR"=>$id));
            while($peminjaman_berkas->nextRow())
            {
            ?>
            <tr>
                <td align="center"><?=$no?></td>
                <td><?=$peminjaman_berkas->getField("KLASIFIKASI_KODE")." - ".$peminjaman_berkas->getField("KLASIFIKASI_NAMA")?></td>
                <td><?=$peminjaman_berkas->getField("KETERANGAN")?></td>
                <td align="center"><?=$peminjaman_berkas->getField("KURUN_WAKTU")?></td>
                <td align="center"><?=$peminjaman_berkas->getField("TINGKAT_PERKEMBANGAN_NAMA")?></td>
            </tr>
            <?
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>
<!-- End Isi Naskah -->