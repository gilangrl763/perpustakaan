<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("kauth");
$user_login = new kauth();

$this->load->model("Stasiun");
$stasiun = new Stasiun();


$reqId = $this->input->get("reqId");

if($reqId == ""){
    $reqMode = "insert";
}
else{
    $reqMode = "update";

    $stasiun->selectByParams(array("STASIUN_ID"=>$reqId));
    $stasiun->firstRow();
    $reqKode            = $stasiun->getField("KODE");
    $reqNama            = $stasiun->getField("NAMA");
    $reqAlamat          = $stasiun->getField("ALAMAT");
    $reqKelurahanId     = $stasiun->getField("KELURAHAN_ID");
    $reqKecamatanId     = $stasiun->getField("KECAMATAN_ID");
    $reqKotaId          = $stasiun->getField("KOTA_ID");
    $reqProvinsiId      = $stasiun->getField("PROVINSI_ID");
    $reqTelepon         = $stasiun->getField("TELEPON");
    $reqFax             = $stasiun->getField("FAX");
}

?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="app/index/stasiun">Stasiun</a></li>
                    <li class="breadcrumb-item active">Kelola Stasiun</li>
                </ol>
            </div>
			<h4 class="page-title">Kelola stasiun</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="ff" class="easyui-form form-horizontal" method="post" novalidate enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kode</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqKode" value="<?=$reqKode?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Nama</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqNama" value="<?=$reqNama?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Alamat</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqAlamat" value="<?=$reqAlamat?>" required/>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Provinsi</label>
                        <div class="col-7">
                            <input type="text" name="reqProvinsiId" id="reqProvinsiId" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/provinsi',
                            onSelect: function(rec){
                                $('#reqKotaId').combobox('setValue','');
                                $('#reqKotaId').combobox('reload', 'combo_json/kota/?reqProvinsiId='+rec.id);
                            }" value="<?=$reqProvinsiId?>"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kota</label>
                        <div class="col-7">
                            <input type="text" name="reqKotaId" id="reqKotaId" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/kota',
                            onSelect: function(rec){
                                $('#reqKecamatanId').combobox('setValue','');
                                $('#reqKecamatanId').combobox('reload', 'combo_json/kecamatan/?reqKotaId='+rec.id);
                            }" value="<?=$reqKotaId?>"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kecamatan</label>
                        <div class="col-7">
                            <input type="text" name="reqKecamatanId" id="reqKecamatanId" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/kecamatan',
                            onSelect: function(rec){
                                $('#reqKelurahanId').combobox('setValue','');
                                $('#reqKelurahanId').combobox('reload', 'combo_json/kelurahan/?reqKecamatanId='+rec.id);
                            }" value="<?=$reqKecamatanId?>"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kelurahan</label>
                        <div class="col-7">
                            <input type="text" name="reqKelurahanId" id="reqKelurahanId" class="easyui-combobox"
                            data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/kelurahan',
                            onSelect: function(rec){
                                $('#reqKodePos').val(rec.id);
                            }" value="<?=$reqKelurahanId?>"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Kode Pos</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqKodePos" id="reqKodePos" 
                            value="<?=$reqKodePos?>" required/>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Telepon</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqTelepon" value="<?=$reqTelepon?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Fax</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqFax" value="<?=$reqFax?>" required/>
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
            url: "stasiun_json/add",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/stasiun';
                }
            }   
        });
        
        return false;    
    });   
});
</script>