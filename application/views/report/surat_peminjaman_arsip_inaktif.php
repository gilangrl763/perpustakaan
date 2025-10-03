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

$pemindahan->selectByParamsMonitoring(array("A.PEMINDAHAN_ID::varchar" => $id));
$pemindahan->firstRow();
$PERUSAHAAN_ID      = $pemindahan->getField("PERUSAHAAN_ID");
$NOMOR              = $pemindahan->getField("NOMOR");
$PERIHAL            = $pemindahan->getField("PERIHAL");
$TANGGAL            = $pemindahan->getField("TANGGAL");

$pemindahan_approval->selectByParamsApproval(array("A.PEMINDAHAN_ID::varchar" => $id), -1, -1, "", " ORDER BY URUT DESC");
$pemindahan_approval->firstRow();

$perusahaan->selectByParamsMonitoring(array("A.PERUSAHAAN_ID" => $PERUSAHAAN_ID));
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
        <img src="uploads/perusahaan/<?= $LOGO ?>" width="130px" height="*">
    </div>
    <div class="alamat-kop">
        <span class="nama-perusahaan"><?= $NAMA ?></span>
        <br><?= $ALAMAT ?>
        <br>Telp : <?= $TELEPON ?>
        <br>Web : <?= $WEBSITE ?>
    </div>
</div>
<!-- End Kop Surat -->

<!-- Start Jenis Naskah -->
<div class="jenis-naskah">
    <div class="nama-jenis-naskah"><b><u>FORMULIR PEMINJAMAN ARSIP INAKTIF</u></b></div>
    <div class="nomor-naskah">Nomor : </div>
</div>
<!-- End Jenis Naskah -->

<!-- Start Kepada Naskah -->
<div class="kepada-naskah">
    <p><u>Identitas Peminjaman</u></p>
    <table width="100%">
        <tr>
            <td width="15%">Nama</td>
            <td width="1%">:</td>
            <td width="84%"></td>
        </tr>
        <tr>
            <td width="15%">Jabatan</td>
            <td width="1%">:</td>
            <td width="84%"></td>
        </tr>
        <tr>
            <td width="15%">Unit/Instansi</td>
            <td width="1%">:</td>
            <td width="84%"></td>
        </tr>
        <tr>
            <td width="20%">Nomer Telepon/HP</td>
            <td width="1%">:</td>
            <td width="84%"></td>
        </tr>
    </table>
    <br>
</div>
<!-- End Jenis Naskah -->

<!-- Start Isi Naskah -->
<div class="isi-naskah">
    <table class="datatabel-border">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="27%">Masalah/Subjek</th>
                <th width="40%">Kode</th>
                <th width="10%">Jumlah</th>
                <th width="18%">Keperluan</th>
                <th width="18%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center" rowspan="3">1</td>
                <td rowspan="3">Permintaan dokumen</td>
                <td rowspan="3"></td>
                <td align="center" rowspan="3">3</td>
                <td align="center" rowspan="3">Audit</td>
                <td align="center" rowspan="3"></td>
            </tr>
        </tbody>
    </table>
    <table class="datatabel-border">
        <thead>
            <tr>
                <th colspan="2">Tanggal Pinjam :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;05 Agustus 2024</th>
                <th colspan="2">Tanggal Pengembalian :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;05 Agustus 2024</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center" rowspan="3">Petugas Unit Kearsipan
                    <br><br><b><?= strtoupper($pemindahan_approval->getField("JABATAN")) ?></b>
                    <?
                    if ($pemindahan_approval->getField("STATUS") == "APPROVE") {
                        $md5 = $id . "_7mj4R5iP";
                        $qrText = base_url() . "qr/index/pemindahan/" . md5($md5);
                        $txt = QRcode::text($qrText);
                        $image = QRimage::image($txt, 2, 2);
                        ob_start();
                        imagepng($image);
                        $contents = ob_get_clean();
                        imagedestroy($image);
                        echo "<div class='barcode'><img src='data:image/png;base64," . base64_encode($contents) . "' height='70px' width='*' /></div>";
                    } else {
                        echo "<br><br><br><br>";
                    }
                    ?>
                    <span style="font-size:10px;float: left;"><i>Ditandatangani secara elektronik</i></span>
                    <br><b><u><?= strtoupper($pemindahan_approval->getField("NAMA")) ?></u></b>
                </td>
                <td align="center" rowspan="3">Peminjam
                    <br><br><b><?= strtoupper($pemindahan_approval->getField("JABATAN")) ?></b>
                    <?
                    if ($pemindahan_approval->getField("STATUS") == "APPROVE") {
                        $md5 = $id . "_7mj4R5iP";
                        $qrText = base_url() . "qr/index/pemindahan/" . md5($md5);
                        $txt = QRcode::text($qrText);
                        $image = QRimage::image($txt, 2, 2);
                        ob_start();
                        imagepng($image);
                        $contents = ob_get_clean();
                        imagedestroy($image);
                        echo "<div class='barcode'><img src='data:image/png;base64," . base64_encode($contents) . "' height='70px' width='*' /></div>";
                    } else {
                        echo "<br><br><br><br>";
                    }
                    ?>
                    <span style="font-size:10px;float: left;"><i>Ditandatangani secara elektronik</i></span>
                    <br><b><u><?= strtoupper($pemindahan_approval->getField("NAMA")) ?></u></b>
                </td>
                <td align="center" rowspan="3">Petugas Unit Kearsipan
                    <br><br><b><?= strtoupper($pemindahan_approval->getField("JABATAN")) ?></b>
                    <?
                    if ($pemindahan_approval->getField("STATUS") == "APPROVE") {
                        $md5 = $id . "_7mj4R5iP";
                        $qrText = base_url() . "qr/index/pemindahan/" . md5($md5);
                        $txt = QRcode::text($qrText);
                        $image = QRimage::image($txt, 2, 2);
                        ob_start();
                        imagepng($image);
                        $contents = ob_get_clean();
                        imagedestroy($image);
                        echo "<div class='barcode'><img src='data:image/png;base64," . base64_encode($contents) . "' height='70px' width='*' /></div>";
                    } else {
                        echo "<br><br><br><br>";
                    }
                    ?>
                    <span style="font-size:10px;float: left;"><i>Ditandatangani secara elektronik</i></span>
                    <br><b><u><?= strtoupper($pemindahan_approval->getField("NAMA")) ?></u></b>
                </td>
                <td align="center" rowspan="3">Peminjam
                    <br><br><b><?= strtoupper($pemindahan_approval->getField("JABATAN")) ?></b>
                    <?
                    if ($pemindahan_approval->getField("STATUS") == "APPROVE") {
                        $md5 = $id . "_7mj4R5iP";
                        $qrText = base_url() . "qr/index/pemindahan/" . md5($md5);
                        $txt = QRcode::text($qrText);
                        $image = QRimage::image($txt, 2, 2);
                        ob_start();
                        imagepng($image);
                        $contents = ob_get_clean();
                        imagedestroy($image);
                        echo "<div class='barcode'><img src='data:image/png;base64," . base64_encode($contents) . "' height='70px' width='*' /></div>";
                    } else {
                        echo "<br><br><br><br>";
                    }
                    ?>
                    <span style="font-size:10px;float: left;"><i>Ditandatangani secara elektronik</i></span>
                    <br><b><u><?= strtoupper($pemindahan_approval->getField("NAMA")) ?></u></b>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- End Isi Naskah -->

<!-- Start Catatan  -->
<div class="catatan">
    <p>Catatan :</p>
    <ol>
        <li>Waktu Peminjaman Arsip inaktif paling lama 5 (lima) hari</li>
        <li>Arsip inaktif yang bersifat rahasia hanya dapat dilihat dan dibaca di ruang pelayanan arsip dengan izin khusus dari pimpinan Unit Kearsipan.</li>
        <li>Arsip inaktif yang dapat dipinjamkan hanya dalam bentuk copy kecuali diperlukan aslinya untuk keperluan penegakan hukum jika ada ketentuan untuk menyajikan keaslian arsip</li>
        <li>Apabila terjadi kehilangan arsip karena kelalaian peminjam,maka peminjam dapat diberi sanksi sesuai dengan ketentuan peraturan perundang undangan</li>
    </ol>
</div>
<!-- End Catatan -->