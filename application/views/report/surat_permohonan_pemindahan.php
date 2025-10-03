<?
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("libraries/phpqrcode/qrlib.php");

$id = $this->input->get("id");

$this->load->model("Pemindahan");
$this->load->model("Perusahaan");
$pemindahan = new Pemindahan();
$pemindahan_approval = new Pemindahan();
$perusahaan = new Perusahaan();

$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar"=>$id));
$pemindahan->firstRow();
$PERUSAHAAN_ID      = $pemindahan->getField("PERUSAHAAN_ID");
$NOMOR              = $pemindahan->getField("NOMOR");
$PERIHAL            = $pemindahan->getField("PERIHAL");
$TANGGAL            = $pemindahan->getField("TANGGAL");

$pemindahan_approval->selectByParamsApproval(array("A.PEMINDAHAN_ID::varchar"=>$id),-1,-1,""," ORDER BY URUT DESC");
$pemindahan_approval->firstRow();

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
    <img src="uploads/perusahaan/<?=$LOGO?>" width="100px" height="*">
  </div>
</div>
<!-- End Kop Surat -->

<!-- Start Jenis Naskah -->
<div class="jenis-naskah">
    <div class="nama-jenis-naskah"><b>N&nbsp;O&nbsp;T&nbsp;A&nbsp;&nbsp;D&nbsp;I&nbsp;N&nbsp;A&nbsp;S</b></div>
</div>
<!-- End Jenis Naskah -->

<!-- Start Kepada Naskah -->
<div class="kepada-naskah">
    <table width="100%">
        <tr>
            <td width="15%">Nomor</td>
            <td width="1%">:</td>
            <td width="84%"><?=$NOMOR?></td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td><?=$PERIHAL?></td>
        </tr>
        <tr>
            <td>Kepada Yth.</td>
            <td>:</td>
            <td>
                <?
                $pemindahan_tujuan = new Pemindahan();
                $jumlahTujuan = $pemindahan_tujuan->getCountByParamsTujuan(array("A.PEMINDAHAN_ID::varchar"=>$id,"A.JENIS"=>"TUJUAN"));
                $pemindahan_tujuan->selectByParamsTujuan(array("A.PEMINDAHAN_ID::varchar"=>$id,"A.JENIS"=>"TUJUAN"));
                
                if($jumlahTujuan == 1){
                    $pemindahan_tujuan->firstRow();

                    echo strtoupper($pemindahan_tujuan->getField("JABATAN"));
                }
                else{
                ?>
                <ol>
                    <?
                    while ($pemindahan_tujuan->nextRow()) {
                    ?>
                        <li><?=strtoupper($pemindahan_tujuan->getField("JABATAN"))?></li>
                    <?
                    }
                    ?>
                </ol>
                <?
                }
                ?>
            </td>
        </tr>
    </table>
</div>
<!-- End Jenis Naskah -->

<!-- Start Pembatas -->
<div class="pembatas"></div>
<!-- End Pembatas -->

<!-- Start Isi Naskah -->
<div class="isi-naskah">
    <p>Bersama dengan ini kami sampaikan Permohonan Pemindahan dan Penyimpanan Arsip Inaktif dari Unit Pengolahan <i><?=$pemindahan->getField("NAMA_SATUAN_KERJA")?></i> ke Unit Kearsipan, adapun Daftar Pertelaan Dokumen/Arsip Pindah adalah sebagai berikut :
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
                $pemindahan_berkas = new Pemindahan();
                $pemindahan_berkas->selectByParamsBerkas(array("A.PEMINDAHAN_ID::VARCHAR"=>$id));
                while($pemindahan_berkas->nextRow())
                {
                ?>
                <tr>
                    <td align="center"><?=$no?></td>
                    <td><?=$pemindahan_berkas->getField("KLASIFIKASI_KODE")." - ".$pemindahan_berkas->getField("KLASIFIKASI_NAMA")?></td>
                    <td><?=$pemindahan_berkas->getField("KETERANGAN")?></td>
                    <td align="center"><?=$pemindahan_berkas->getField("KURUN_WAKTU")?></td>
                    <td align="center"><?=$pemindahan_berkas->getField("TINGKAT_PERKEMBANGAN_NAMA")?></td>
                </tr>
                <?
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </p>
    <p>Demikian yang dapat kami sampaikan, atas perhatian dan kerjasamanya disampaikan terima kasih.</p>
</div>
<!-- End Isi Naskah -->

<!-- Start Tanda Tangan -->
<div class="tanda-tangan-kanan">
    <?=$pemindahan->getField("KOTA")?>, <?=getFormattedDateView($pemindahan->getField("TANGGAL"))?>
    <br><b><?=strtoupper($pemindahan_approval->getField("JABATAN"))?></b>
    <?
    if($pemindahan_approval->getField("STATUS") == "APPROVE"){
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
    <span style="font-size:10px;float: left;"><i>Ditandatangani secara elektronik</i></span>
    <br><b><u><?=strtoupper($pemindahan_approval->getField("NAMA"))?></u></b>
</div>
<!-- End Tanda Tangan -->

<!-- Start Tembusan -->
<?
$pemindahan_tembusan = new Pemindahan();
$jumlahTembusan = $pemindahan_tembusan->getCountByParamsTujuan(array("A.PEMINDAHAN_ID::varchar"=>$id,"A.JENIS"=>"TEMBUSAN"));
if($jumlahTembusan >= 1){
?>
<div class="tembusan">
    <b><u>Tembusan Yth. :</u></b>
    <?
    $pemindahan_tembusan->selectByParamsTujuan(array("A.PEMINDAHAN_ID::varchar"=>$id,"A.JENIS"=>"TEMBUSAN"));
    if($jumlahTujuan == 1){
        $pemindahan_tembusan->firstRow();

        echo "<br>".strtoupper($pemindahan_tembusan->getField("JABATAN"));
    }
    else
    {
        $no = 1;
        while ($pemindahan_tembusan->nextRow()) {
            echo "<br>".$no.". ".strtoupper($pemindahan_tembusan->getField("JABATAN"));
            $no++;
        }
    }
    ?>
</div>
<?
}
?>
<!-- End Tembusan -->