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
        $statement = " and a.pemindahan_berkas_id::varchar = '".$arrId[0]."' ";
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
        
        $statement = " and a.pemindahan_berkas_id::varchar in (".$arrayId.") ";
    }

}
?>

<div class="container">
    <?
    $query = $this->db->query("select distinct a.lokasi_simpan_nomor_boks
        from pemindahan_berkas a
        where 1 = 1 and a.pemindahan_id::varchar='$id' $statement");
    foreach($query->result() as $row)
    {
        $pemindahan_berkas->selectByParamsBerkasCetak(array("A.PEMINDAHAN_ID::VARCHAR"=>$id,
            "A.LOKASI_SIMPAN_NOMOR_BOKS"=>$row->lokasi_simpan_nomor_boks));
        $pemindahan_berkas->firstRow();

        $minFolder = $this->db->query("select lokasi_simpan_nomor_folder
            from pemindahan_berkas a
            where a.pemindahan_id::varchar='$id' and lokasi_simpan_nomor_boks='$row->lokasi_simpan_nomor_boks' 
            order by lokasi_simpan_nomor_folder asc limit 1 ")->row()->lokasi_simpan_nomor_folder;
        $maxFolder = $this->db->query("select lokasi_simpan_nomor_folder
            from pemindahan_berkas a
            where a.pemindahan_id::varchar='$id' and lokasi_simpan_nomor_boks='$row->lokasi_simpan_nomor_boks' 
            order by lokasi_simpan_nomor_folder desc limit 1 ")->row()->lokasi_simpan_nomor_folder;
    ?>
    <div class="section-left">
        <div class="section">
            <div class="section-title">
                <h3><?= $pemindahan_berkas->getField("SATUAN_KERJA_NAMA") ?></h3>
            </div>
            <div class="section-content">
                <table class="datatabel-label-box">
                    <tbody>
                        <tr>
                            <td width="3%"></td>
                            <td width="15%">RUANG</td>
                            <td width="2%">:</td>
                            <td width="80%"><?= $pemindahan_berkas->getField("LOKASI_SIMPAN_RUANG") ?></td>
                        </tr>
                        <tr>
                            <td width="3%"></td>
                            <td width="15%">RAK</td>
                            <td width="2%">:</td>
                            <td width="80%"><?= $pemindahan_berkas->getField("LOKASI_SIMPAN_LEMARI") ?></td>
                        </tr>
                        <tr>
                            <td width="3%"></td>
                            <td width="15%">BOX</td>
                            <td width="2%">:</td>
                            <td width="80%"><?= $pemindahan_berkas->getField("LOKASI_SIMPAN_NOMOR_BOKS") ?></td>   
                        </tr>
                        <tr>
                            <td width="3%"></td>
                            <td width="15%">FOLDER</td>
                            <td width="2%">:</td>
                            <td width="80%"><?=$minFolder?> S/D <?=$maxFolder?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?
    }
    ?>
</div>