<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->library("kauth");
$userLogin = new kauth();

$this->load->model("UserLogin");
$this->load->model("UserType");
$user_login = new UserLogin();
$user_type = new UserType();

$reqId = $this->input->get("reqId");

if($reqId == ""){
    $reqMode = "insert";
}
else{
    $reqMode = "update";

    $user_login->selectByParams(array("USER_LOGIN_ID"=>$reqId));
    $user_login->firstRow();
    $reqUserTypeId = $user_login->getField("USER_TYPE_ID");
    $reqNama = $user_login->getField("NAMA");
    $reqEmail = $user_login->getField("EMAIL");
    $reqStatus = $user_login->getField("STATUS");
}

?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="app/index/user_login">User Login</a></li>
                    <li class="breadcrumb-item active">Kelola User Login</li>
                </ol>
            </div>
			<h4 class="page-title">Kelola User Login</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="ff" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Nama</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="reqNama" value="<?=$reqNama?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Email</label>
                        <div class="col-7">
                            <input type="email" class="form-control" name="reqEmail" value="<?=$reqEmail?>" required/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-2 col-form-label">Hak Akses</label>
                        <div class="col-7">
                            <?
                            $i=0;
                            $user_type->selectByParams();
                            while($user_type->nextRow()){
                            ?>
                            <input class="form-check-input" type="checkbox" name="reqUserTypeId[]" id="customckeck9<?=$i?>" 
                            value="<?=$user_type->getField("USER_TYPE_ID")?>"
                            <?if(searchWordDelimeter($reqUserTypeId, $user_type->getField("USER_TYPE_ID"))){?> checked <?}?>> 
                            <?=$user_type->getField("NAMA")?><br>
                            <?
                                $i++;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-8 offset-2">
                            <input type="hidden" name="reqId" value="<?=$reqId?>">
                            <input type="hidden" name="reqMode" value="<?=$reqMode?>">
                            <input type="hidden" name="reqStatus" value="<?=$reqStatus?>">
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
            url: "user_login_json/add",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/user_login';
                }
            }   
        });
        
        return false;    
    });   
});
</script>