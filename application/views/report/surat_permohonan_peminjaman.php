<?
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("libraries/phpqrcode/qrlib.php");

$id = $this->input->get("id");

$this->load->model("Peminjaman");
$this->load->model("Perusahaan");
$peminjaman = new Peminjaman();
$peminjaman_approval = new Peminjaman();
$perusahaan = new Perusahaan();

$peminjaman->selectByParamsMonitoring(array("A.PEMINJAMAN_ID::varchar"=>$id));
$peminjaman->firstRow();
$PERUSAHAAN_ID      = $peminjaman->getField("PERUSAHAAN_ID");
$NOMOR              = $peminjaman->getField("NOMOR");
$PERIHAL            = $peminjaman->getField("PERIHAL");
$TANGGAL            = $peminjaman->getField("TANGGAL");

$peminjaman_approval->selectByParamsApproval(array("A.PEMINJAMAN_ID::varchar"=>$id),-1,-1,""," ORDER BY URUT DESC");
$peminjaman_approval->firstRow();

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
                $peminjaman_tujuan = new Peminjaman();
                $jumlahTujuan = $peminjaman_tujuan->getCountByParamsTujuan(array("A.PEMINJAMAN_ID::varchar"=>$id,"A.JENIS"=>"TUJUAN"));
                $peminjaman_tujuan->selectByParamsTujuan(array("A.PEMINJAMAN_ID::varchar"=>$id,"A.JENIS"=>"TUJUAN"));
                
                if($jumlahTujuan == 1){
                    $peminjaman_tujuan->firstRow();

                    echo strtoupper($peminjaman_tujuan->getField("JABATAN"));
                }
                else{
                ?>
                <ol>
                    <?
                    while ($peminjaman_tujuan->nextRow()) {
                    ?>
                        <li><?=strtoupper($peminjaman_tujuan->getField("JABATAN"))?></li>
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
    <p>Bersama dengan ini kami sampaikan Permohonan Peminjaman Arsip Inaktif untuk keperluan <?=$peminjaman->getField("KEPERLUAN")?>, adapun Daftar Pertelaan Dokumen/Arsip Pindah adalah sebagai berikut :
        <table class="datatabel-border">
            <thead>
                <tr>
                    <th width="15%">Klasifikasi</th>
                    <th width="10%">No. Dokumen</th>
                    <th width="10%">No. Alternatif</th>
                    <th width="20%">Uraian</th>
                    <th width="7%">Kurun Waktu</th>
                    <th width="8%">Tahun Pindah</th>
                    <th width="15%">Unit Pengolah</th>
                    <th width="12%">Cabang</th>
                    <th width="10%">Pinjam Fisik?</th>
                </tr>
            </thead>
            <tbody>
                <?
                $no = 1;
                $peminjaman_berkas = new Peminjaman();
                $peminjaman_berkas->selectByParamsMonitoringBerkas(array("A.PEMINJAMAN_ID::VARCHAR"=>$id));
                while($peminjaman_berkas->nextRow())
                {
                    $PEMINJAMAN_BERKAS_ID = $peminjaman_berkas->getField("PEMINJAMAN_BERKAS_ID");
                    $PEMINDAHAN_BERKAS_ID = $peminjaman_berkas->getField("PEMINDAHAN_BERKAS_ID");
                    $PEMINDAHAN_ID = $peminjaman_berkas->getField("PEMINDAHAN_ID");
                    $KLASIFIKASI_KODE = $peminjaman_berkas->getField("KLASIFIKASI_KODE");
                    $KLASIFIKASI_NAMA = $peminjaman_berkas->getField("KLASIFIKASI_NAMA");
                    $NOMOR_DOKUMEN = $peminjaman_berkas->getField("NOMOR_DOKUMEN");
                    $NOMOR_ALTERNATIF = $peminjaman_berkas->getField("NOMOR_ALTERNATIF");
                    $KETERANGAN_BERKAS = $peminjaman_berkas->getField("KETERANGAN");
                    $KURUN_WAKTU = $peminjaman_berkas->getField("KURUN_WAKTU");
                    $TAHUN_PINDAH = $peminjaman_berkas->getField("TAHUN_PINDAH");
                    $NAMA_SATUAN_KERJA = $peminjaman_berkas->getField("NAMA_SATUAN_KERJA");
                    $NAMA_CABANG = $peminjaman_berkas->getField("NAMA_CABANG");
                    $ADA_HARDCOPY = $peminjaman_berkas->getField("ADA_HARDCOPY");
                    $ADA_HARDCOPY_PEMINDAHAN = $peminjaman_berkas->getField("ADA_HARDCOPY_PEMINDAHAN");
                ?>
                <tr>
                    <td><?=$KLASIFIKASI_KODE."<br>".$KLASIFIKASI_NAMA?></td>
                    <td><?=$NOMOR_DOKUMEN?></td>
                    <td><?=$NOMOR_ALTERNATIF?></td>
                    <td><?=$KETERANGAN_BERKAS?></td>
                    <td align="center"><?=$KURUN_WAKTU?></td>
                    <td align="center"><?=$TAHUN_PINDAH?></td>
                    <td><?=$NAMA_SATUAN_KERJA?></td>
                    <td><?=$NAMA_CABANG?></td>
                    <td align="center"><?=$ADA_HARDCOPY?></td>
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
    <?=$peminjaman->getField("KOTA")?>, <?=getFormattedDateView($peminjaman->getField("TANGGAL"))?>
    <br><b><?=strtoupper($peminjaman_approval->getField("JABATAN"))?></b>
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
    <span style="font-size:10px;float: left;"><i>Ditandatangani secara elektronik</i></span>
    <br><b><u><?=strtoupper($peminjaman_approval->getField("NAMA"))?></u></b>
</div>
<!-- End Tanda Tangan -->

<!-- Start Tembusan -->
<?
$peminjaman_tembusan = new Peminjaman();
$jumlahTembusan = $peminjaman_tembusan->getCountByParamsTujuan(array("A.PEMINJAMAN_ID::varchar"=>$id,"A.JENIS"=>"TEMBUSAN"));
if($jumlahTembusan >= 1){
?>
<div class="tembusan">
    <b><u>Tembusan Yth. :</u></b>
    <?
    $peminjaman_tembusan->selectByParamsTujuan(array("A.PEMINJAMAN_ID::varchar"=>$id,"A.JENIS"=>"TEMBUSAN"));
    if($jumlahTujuan == 1){
        $peminjaman_tembusan->firstRow();

        echo "<br>".strtoupper($peminjaman_tembusan->getField("JABATAN"));
    }
    else
    {
        $no = 1;
        while ($peminjaman_tembusan->nextRow()) {
            echo "<br>".$no.". ".strtoupper($peminjaman_tembusan->getField("JABATAN"));
            $no++;
        }
    }
    ?>
</div>
<?
}
?>
<!-- End Tembusan -->