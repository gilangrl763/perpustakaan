<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("kauth");
$user_login = new kauth();

$this->load->model("Kereta");
$kereta = new Kereta();


$reqId = $this->input->get("reqId");

if($reqId == ""){
    $reqMode = "insert";
}
else{
    $reqMode = "update";

    $kereta->selectByParams(array("KERETA_ID"=>$reqId));
    $kereta->firstRow();
    $reqKode = $kereta->getField("KODE");
    $reqNama = $kereta->getField("NAMA");
}

?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="app/index/kereta">Kereta</a></li>
                    <li class="breadcrumb-item active">Kelola Kereta</li>
                </ol>
            </div>
			<h4 class="page-title">Kelola Kereta</h4>
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
                        <label for="inputEmail3" class="col-2 col-form-label">Nama</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqNama" value="<?=$reqNama?>" required/>
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
            url: "kereta_json/add",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/kereta';
                }
            }   
        });
        
        return false;    
    });   
});
</script>