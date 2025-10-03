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
$PERUSAHAAN_ID          = $peminjaman->getField("PERUSAHAAN_ID");
$NOMOR                  = $peminjaman->getField("NOMOR");
$PERIHAL                = $peminjaman->getField("PERIHAL");
$TANGGAL                = $peminjaman->getField("TANGGAL");
$NAMA_PEMINJAM          = $peminjaman->getField("NAMA_PEMINJAM");
$JABATAN_PEMINJAM       = $peminjaman->getField("JABATAN_PEMINJAM");
$NAMA_SATUAN_KERJA      = $peminjaman->getField("NAMA_SATUAN_KERJA");
$NAMA_CABANG            = $peminjaman->getField("NAMA_CABANG");
$KEPERLUAN              = $peminjaman->getField("KEPERLUAN");
$NAMA_ARSIPARIS         = $peminjaman->getField("NAMA_ARSIPARIS");
$JABATAN_ARSIPARIS      = $peminjaman->getField("JABATAN_ARSIPARIS");

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

<table class="datatabel-border">
    <tbody>
        <tr>
            <td width="15%" rowspan="4" align="center">
                <img src="uploads/perusahaan/<?=$LOGO?>" width="100px" height="*">
            </td>
            <td width="45%" rowspan="3" align="center"><h3>Formulir Peminjaman Arsip Inaktif</h3></td>
            <td width="15%">Nama Peminjam</td>
            <td width="25%">: <?=$NAMA_PEMINJAM?></td>
        </tr>
        <tr>
            <td>Jabatan Peminjam</td>
            <td>: <?=$JABATAN_PEMINJAM?></td>
        </tr>
        <tr>
            <td>Unit Peminjam</td>
            <td>: <?=$NAMA_SATUAN_KERJA?></td>
        </tr>
        <tr>
            <td align="center">Nomor : <?=$NOMOR?></td>
            <td>Cabang Peminjam</td>
            <td>: <?=$NAMA_CABANG?></td>
        </tr>
    </tbody>
</table>

<table class="datatabel-border">
    <thead>
        <tr>
            <th width="15%">Klasifikasi</th>
            <th width="10%">No. Dokumen</th>
            <th width="10%">No. Alternatif</th>
            <th width="30%">Uraian</th>
            <th width="6%">Kurun Waktu</th>
            <th width="6%">Tahun Pindah</th>
            <th width="15%">Unit Pengolah</th>
            <th width="12%">Cabang</th>
            <th width="6%">Pinjam Fisik?</th>
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

<table class="datatabel-border">
    <tbody>
        <tr>
            <td>
                <b>Keperluan :</b>
                <br><?=$KEPERLUAN?>
            </td>
        </tr>
    </tbody>
</table>

<table class="datatabel-border">
    <tbody>
        <tr>
            <td colspan="2" width="50%">Tanggal Pinjam : <?=getFormattedDateView($peminjaman->getField("TANGGAL_PINJAM"))?></td>
            <td colspan="2" width="50%">Tanggal Kembali : <?=getFormattedDateView($peminjaman->getField("TANGGAL_KEMBALI"))?></td>
        </tr>
        <tr>
            <td width="25%" align="center">
                Peminjam,
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
                <br><b><u><?=$NAMA_PEMINJAM?></u></b>
                <br><?=$JABATAN_PEMINJAM?>
            </td>
            <td width="25%" align="center">
                Petugas Unit Kearsipan,
                <?
                if($peminjaman->getField("STATUS") == "DIPINJAM" || $peminjaman->getField("STATUS") == "DIKEMBALIKAN" || $peminjaman->getField("STATUS") == "DIPINJAM_ULANG" || $peminjaman->getField("STATUS") == "SIMPAN"){
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
                <br><b><u><?=coalesce($NAMA_ARSIPARIS,".....................")?></u></b>
                <br><?=coalesce($JABATAN_ARSIPARIS,".....................")?>
            </td>
            <td width="25%" align="center">
                Peminjam,
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
                <br><b><u><?=$NAMA_PEMINJAM?></u></b>
                <br><?=$JABATAN_PEMINJAM?>
            </td>
            <td width="25%" align="center">
                Petugas Unit Kearsipan,
                <?
                if($peminjaman->getField("STATUS") == "DIPINJAM" || $peminjaman->getField("STATUS") == "DIKEMBALIKAN" || $peminjaman->getField("STATUS") == "DIPINJAM_ULANG" || $peminjaman->getField("STATUS") == "SIMPAN"){
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
                <br><b><u><?=coalesce($NAMA_ARSIPARIS,".....................")?></u></b>
                <br><?=coalesce($JABATAN_ARSIPARIS,".....................")?>
            </td>
        </tr>
    </tbody>
</table>

<div class="catatan-kaki">
    <b><u>CATATAN :</u></b>
    <ol>
        <li>Waktu peminjaman arsip inaktif paling lama 5 (lima) hari kalender, terhitung sejak diserah terimakan arsip inaktif pada tanggal <?=getFormattedDateView($peminjaman->getField("TANGGAL_PINJAM"))?> sampai dengan tanggal <?=getFormattedDateView($peminjaman->getField("BATAS_TANGGAL_KEMBALI"))?>;</li>
        <li>Arsip inaktif yang bersifat rahasia hanya dapat dilihat dan dibaca di ruang pelayanan arsip dengan izin khusus dari pimpinan Unit Kearsipan;</li>
        <li>Arsip inaktif yang dapat dipinjamkan hanya dalam bentuk copy kecuali diperlukan aslinya untuk keperluan penegakan hukum jika ada ketentuan untuk menyajikan keaslian arsip</li>
        <li>Apabila terjadi kehilangan arsip karena kelalaian peminjam, maka peminjam dapat diberi sangsi sesuai dengan ketentuan peraturan perusahaan</li>
    </ol>
</div>