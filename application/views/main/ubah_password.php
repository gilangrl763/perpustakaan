<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("kauth");
$userLogin = new kauth();
?>

<div class="row">
	<div class="col-2">
    </div>
    <div class="col-lg-8">
		<div class="page-title-box p-2">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item text-white"><a href="main/index/home" class="text-white">Beranda</a></li>
                    <li class="breadcrumb-item active text-white">Ubah Password</li>
                </ol>
            </div>
			<h4 class="page-title text-white">Ubah Password</h4>
		</div>
	</div>
    <div class="col-2">
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card p-2">
            <div class="card-body">
                <form id="ff" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Password Baru</label>
                        <div class="col-7">
                            <input type="password" class="form-control" name="reqPassword" value="" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Konfirmasi Password Baru</label>
                        <div class="col-7">
                            <input type="password" class="form-control" name="reqKonfirmasiPassword" value="" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-8 offset-2">
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
            url: "user_login_json/ubah_password",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'main/index/home';
                }
            }   
        });
        
        return false;    
    });   
});
</script>