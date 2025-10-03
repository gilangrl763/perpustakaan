<?
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");


$this->load->model("Perusahaan");
$this->load->model("Area");
$perusahaan = new Perusahaan();
$area_objek = new Area();

$PERUSAHAAN_ID = $this->input->get("PERUSAHAAN_ID");

$perusahaan->selectByParams(array("A.PERUSAHAAN_ID" => $PERUSAHAAN_ID));
$perusahaan->firstRow();

$NAMA = $perusahaan->getField("NAMA");
$NAMA_FILE = "template_impor_mcp_".str_replace(" ","_",strtolower($NAMA));
header("Content-type: application/vnd-ms-excel; name=$NAMA_FILE");
header("Content-Disposition: attachment; filename=$NAMA_FILE.xls");
?>

<!DOCTYPE html>
<html lang="en" moznomarginboxes mozdisallowselectionprint>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <base href="<?=base_url()?>" />
  <link href="css/gaya-pdf.css" rel="stylesheet">
</head>
<body>
<table class="datatable-border">
    <thead>
        <tr>
            <th rowspan="3" width="3%">No</th>
            <th rowspan="3" width="3%">ID</th>
            <th rowspan="3" width="3%">Cabang</th>
            <th rowspan="3" width="3%">Gedung</th>
            <th rowspan="3" width="3%">Area</th>
            <th rowspan="3" width="3%">Item/Object</th>
            <th rowspan="3" width="3%">Tingkat Pengotoran</th>
            <th rowspan="3" width="3%">Standard Quality</th>
            <th rowspan="3" width="3%">Nama Chemical</th>
            <th rowspan="3" width="3%">Dosis</th>
            <th colspan="2" width="3%">Aktifitas Pembersihan</th>
            <th colspan="3" width="3%">Aktifitas Verifikasi</th>
        </tr>
        <tr>
            <th rowspan="2" width="3%">Metode</th>
            <th width="3%">Interval</th>
            <th width="3%">Interval</th>
            <th rowspan="2" width="3%">Pelaksana</th>
            <th rowspan="2" width="3%">Form</th>
        </tr>
        <tr>
            <th width="3%">Shift</th>
            <th width="3%">Verifikasi</th>
        </tr>
    </thead>
    <tbody id="tbodyAreaObjek">
    <?
    $nomor = 1;
    $area_objek->selectByParamsAreaObjekPerusahaan(array("E.PERUSAHAAN_ID" => $PERUSAHAAN_ID));
    // echo $area_objek->query;              
    while ($area_objek->nextRow()) {
        $random = rand() . $nomor;
        $AREA_OBJEK_ID          = $area_objek->getField("AREA_OBJEK_ID");
        $AREA_ID                = $area_objek->getField("AREA_ID");
        $GEDUNG_ID              = $area_objek->getField("GEDUNG_ID");
        $CABANG_ID              = $area_objek->getField("CABANG_ID");
        $PERUSAHAAN_ID_MCP      = $area_objek->getField("PERUSAHAAN_ID");
        $KETERANGAN_OBJEK       = $area_objek->getField("KETERANGAN");
        $STATUS_OBJEK           = $area_objek->getField("STATUS");
        $NAMA_CABANG            = $area_objek->getField("NAMA_CABANG");
        $NAMA_GEDUNG            = $area_objek->getField("NAMA_GEDUNG");
        $NAMA_AREA              = $area_objek->getField("NAMA_AREA");
        $NAMA_OBJEK             = $area_objek->getField("NAMA_OBJEK");
    ?>
        <tr class="trAreaObjek">
            <td class="cell-restricted"><?= $nomor ?></td>
            <td class="cell-restricted"><?= $AREA_OBJEK_ID ?></td>
            <td class="cell-restricted"><?= $NAMA_CABANG ?></td>
            <td class="cell-restricted"><?= $NAMA_GEDUNG ?></td>
            <td class="cell-restricted"><?= $NAMA_AREA ?></td>
            <td class="cell-restricted"><?= $NAMA_OBJEK ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?
        $nomor++;
    }
    ?>
    </tbody>
</table>
</body>
</html>