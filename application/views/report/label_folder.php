<?
include_once("functions/date.func.php");
include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("libraries/phpqrcode/qrlib.php");

$this->load->model("Pemindahan");
$pemindahan_berkas = new Pemindahan();

$id = $this->input->get("id");
$arrId = $this->input->get("arrId");

if($arrId != "ALL"){
    //PROSES DECODE STATEMENT, CETAK HANYA BEBERAPA FOLDER
    $arrId = explode(",",$arrId);

    if(count($arrId) == 1){
        $statement = " AND A.PEMINDAHAN_BERKAS_ID::VARCHAR = '".$arrId[0]."' ";
    }
    else{
        for($i=0;$i<count($arrId);$i++)
        {
            if($i == 0){
                $arrayId .= "'".$arrId[$i]."'";
            }
            else{
                $arrayId .= ",'".$arrId[$i]."'";
            }
        }
        
        $statement = " AND A.PEMINDAHAN_BERKAS_ID::VARCHAR IN (".$arrayId.") ";
    }

}
?>

<div class="container">
    <?
    $no = 1;
    $pemindahan_berkas->selectByParamsBerkasCetak(array("A.PEMINDAHAN_ID::VARCHAR"=>$id),-1,-1,$statement);
    // echo $pemindahan_berkas->query;
    while ($pemindahan_berkas->nextRow()) {
        $arrKlasifikasiKode = explode(".", $pemindahan_berkas->getField("KLASIFIKASI_KODE"));
    ?>
        <div class="label-folder">
            <h4><?= $pemindahan_berkas->getField("SATUAN_KERJA_NAMA") ?> - <?= $pemindahan_berkas->getField("KLASIFIKASI_KODE") ?></h4>
            <h4>[Rak - <?= $pemindahan_berkas->getField("LOKASI_SIMPAN_LEMARI") ?>]
                [B.<?= $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_BOKS") ?>]
                [F - <?= $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_FOLDER") ?>]
            </h4>
        </div>
    <?
        $no++;
    }
    ?>

</div>