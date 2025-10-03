<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("kauth");
$user_login = new kauth();

$this->load->model("Jadwal");
$jadwal = new Jadwal();


$reqId = $this->input->get("reqId");

if($reqId == ""){
    $reqMode = "insert";
}
else{
    $reqMode = "update";

    $jadwal->selectByParams(array("JADWAL_ID"=>$reqId));
    $jadwal->firstRow();
    $reqKode                    = $jadwal->getField("KODE");
    $reqKeretaId                = $jadwal->getField("KERETA_ID");
    $reqStasiunIdKeberangkatan  = $jadwal->getField("STASIUN_ID_KEBERANGKATAN");
    $reqTanggalKeberangkatan    = $jadwal->getField("TANGGAL_KEBERANGKATAN");
    $reqJamKeberangkatan        = $jadwal->getField("JAM_KEBERANGKATAN");
    $reqStasiunIdKedatangan     = $jadwal->getField("STASIUN_ID_KEDATANGAN");
    $reqTanggalKedatangan       = $jadwal->getField("TANGGAL_KEDATANGAN");
    $reqJamKedatangan           = $jadwal->getField("JAM_KEDATANGAN");
    $reqKeterangan              = $jadwal->getField("KETERANGAN");
    $reqKuota                   = $jadwal->getField("KUOTA");
    $reqHarga                   = $jadwal->getField("HARGA");
    $reqKelas                   = $jadwal->getField("KELAS");
}

?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="app/index/jadwal">jadwal</a></li>
                    <li class="breadcrumb-item active">Kelola jadwal</li>
                </ol>
            </div>
			<h4 class="page-title">Kelola jadwal</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="ff" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kode</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqKode" value="<?=$reqKode?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kereta</label>
                        <div class="col-7">
                            <input type="text" name="reqKeretaId" id="reqKeretaId" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'kereta_json/combo'" value="<?=$reqKeretaId?>"
                            style="width:250px"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kelas</label>
                        <div class="col-7">
                            <input type="text" name="reqKelas" id="reqKelas" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/kelas_kereta'" 
                            value="<?=$reqKelas?>" style="width:250px"/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Stasiun Keberangkatan</label>
                        <div class="col-7">
                           <input type="text" name="reqStasiunIdKeberangkatan" id="reqStasiunIdKeberangkatan" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'stasiun_json/combo'" value="<?=$reqStasiunIdKeberangkatan?>"
                            style="width:250px"/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Tanggal Keberangkatan</label>
                        <div class="col-7">
                            <input type="date" class="form-control" name="reqTanggalKeberangkatan" value="<?=$reqTanggalKeberangkatan?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Jam Keberangkatan</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqJamKeberangkatan" value="<?=$reqJamKeberangkatan?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Stasiun Kedatangan</label>
                        <div class="col-7">
                            <input type="text" name="reqStasiunIdKedatangan" id="reqStasiunIdKedatangan" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'stasiun_json/combo'" value="<?=$reqStasiunIdKedatangan?>"
                            style="width:250px"/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Tanggal Kedatangan</label>
                        <div class="col-7">
                            <input type="date" class="form-control" name="reqTanggalKedatangan" value="<?=$reqTanggalKedatangan?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Jam Kedatangan</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqJamKedatangan" value="<?=$reqJamKedatangan?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kuota</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqKuota" value="<?=$reqKuota?>" 
                            onkeypress="return onlyNumberKey(event)" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Harga</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqHarga" value="<?=$reqHarga?>" 
                            onkeypress="return onlyNumberKey(event)" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Keterangan</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqKeterangan" value="<?=$reqKeterangan?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-8 offset-2">
                            <input type="hidden" name="reqId" value="<?=$reqId?>">
                            <input type="hidden" name="reqMode" value="<?=$reqMode?>">
                            <button type="submit" class="btn btn-success waves-effect waves-light">Simpan</button>
                            <button type="reset" class="btn btn-danger waves-effect">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-1.12.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#ff").submit(function(){
        $.ajax({   
            type: "POST",
            data : $(this).serialize(),
            url: "jadwal_json/add",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/jadwal';
                }
            }   
        });
        
        return false;    
    });   
});

function onlyNumberKey(evt){
    // Only ASCII charactar in that range allowed 
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode 
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
        return false; 
    }

    return true; 
} 
</script>