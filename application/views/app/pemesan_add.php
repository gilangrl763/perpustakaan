<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");



$this->load->model("Pemesan");
$pemesan = new Pemesan();


$reqId = $this->input->get("reqId");

if($reqId == ""){
    $reqMode = "insert";
}
else{
    $reqMode = "update";

    $pemesan->selectByParams(array("PEMESANAN_ID"=>$reqId));
    $pemesan->firstRow();
    $reqKode                    = $pemesan->getField("KODE");
    $reqUserlogin_Id            = $pemesan->getField("USERLOGIN_ID_PEMESAN");
    $reqNomer_identitas         = $pemesan->getField("NOMER_IDENTITAS_PEMESAN");
    $reqNomer_telepon           = $pemesan->getField("NOMER_TELEPON");
    $reqAlamat                  = $pemesan->getField("ALAMAT");
    $reqNama_penumpang          = $pemesan->getField("NAMA_PENUMPANG");
    $reqTitle_penumpang         = $pemesan->getField("TITLE_PENUMPANG");
    $reqJadwal                  = $pemesan->getField("JADWAL_ID");
    $reqPembayaran              = $pemesan->getField("PEMBAYARAN_ID");
    $reqHarga                   = $pemesan->getField("HARGA_ID");
    $reqStatus                  = $pemesan->getField("STATUS_ID");
}

?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="app/index/pemesan">pemesan</a></li>
                    <li class="breadcrumb-item active">Kelola pemesan</li>
                </ol>
            </div>
			<h4 class="page-title">Kelola pemesan</h4>
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
                        <label for="inputEmail3" class="col-2 col-form-label">User Login Pemesan</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqUserlogin_Id" value="<?=$reqUserlogin_Id?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Nomer Identitas Pemesan</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqNomer_identitas" value="<?=$reqNomer_identitas?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Nomer Telepon</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqNomer_telepon" value="<?=$reqNomer_telepon?>" required/>
                        </div>
                    </div>  
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Alamat</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqAlamat" value="<?=$reqAlamat?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Nama Penumpang</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqNama_penumpang" value="<?=$reqNama_penumpang?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Title Penumpang</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqTitle_penumpang" value="<?=$reqTitle_penumpang?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Jadwal</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqJadwal" value="<?=$reqJadwal?>" required/>
                        </div>
                    </div> 
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Pembayaran</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqPembayaran" value="<?=$reqPembayaran?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Harga</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqHarga" value="<?=$reqHarga?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Status</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqStatus" value="<?=$reqStatus?>" required/>
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
            url: "pemesan_json/add",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/pemesan';
                }
            }   
        });
        
        return false;    
    });   
});
</script>