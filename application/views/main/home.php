<?
$this->load->model("Stasiun");
$stasiun = new Stasiun();
?>



<style>
.btn-cari:hover {
	color: #fff !important;
}
.border{
	border-radius:5px !important;
	color: #000 !important;
}
</style>


<div id="carouselExampleCaption" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <div class="carousel-item active">
            <img src="uploads/slider/slide-2.png" alt="..." class="d-block img-fluid" width="100%" height="100px">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="uploads/slider/slide-1.png" alt="Second slide">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleCaption" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleCaption" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </a>
</div>

<div class="row mt-3">
	<div class="col-2">
	</div>
    <div class="col-8">
        <div class="card bg-secondary border">
            <div class="card-body text-light fw-bold">
            	<h4 class="header-title text-light ">PEMESANAN TIKET KERETA API</h4>
            	<p class="sub-header text-grey">Silahkan pesan tiket!</p>
            	<form id="ff" method="POST" enctype="multipart/form-data">
            		<div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Asal</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-train"></i></span>
                                    <input type="text" name="reqAsal" id="reqAsal" class="easyui-combobox"
                                    data-options="valueField:'id',textField:'text',editable:false,url:'stasiun_json/combo'" style="width:550px"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Tujuan</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-train"></i></span>
                                    <input type="text" name="reqTujuan" id="reqTujuan" class="easyui-combobox"
                                    data-options="valueField:'id',textField:'text',editable:false,url:'stasiun_json/combo'" style="width:550px"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Tanggal Keberangkatan</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" class="form-control" name="reqTanggal" id="reqTanggal" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Jumlah Penumpang</label>
                                <input type="text" class="form-control" name="reqJumlahPenumpang" id="reqJumlahPenumpang" value="<?=$reqJumlahPenumpang?>" 
                                onkeypress="return onlyNumberKey(event)" required/>       
                            </div>
                        </div>
	                    <div class="col-md-6">
	                    	<div class="button-list">
	                    		<button type="button" class="btn btn-warning btn-sm waves-effect fw-bold btn-cari" onclick="pencarianTiket()"><i class="fa fa-search"></i> Cari & Pesan Tiket</button>
	                    	</div>
	                    </div>
                    </div>
            	</form>
            </div>
        </div>
    </div>
    <div class="col-2">
	</div>
</div>

<script type="text/javascript">
function pencarianTiket(){
    var reqAsal = $('#reqAsal').combobox('getValue');
    var reqTujuan = $('#reqTujuan').combobox('getValue');
    var reqTanggal = $('#reqTanggal').val();
    var reqJumlahPenumpang = $('#reqJumlahPenumpang').val();

    //VALIDASI
    if(reqAsal == ''){
        alert('Stasiun asal belum ditentukan');
        return false;
    }
    else if(reqTujuan == ''){
        alert('Stasiun tujuan belum ditentukan');
        return false;
    }
    else if(reqTanggal == ''){
        alert('Tanggal keberangkatan belum ditentukan');
        return false;
    }
    else if(reqJumlahPenumpang == ''){
        alert('Jumlah penumpang belum diisi');
        return false;
    }

    document.location.href='main/index/pencarian_tiket/?reqAsal='+reqAsal+'&reqTujuan='+reqTujuan+'&reqTanggal='+reqTanggal+'&reqJumlahPenumpang='+reqJumlahPenumpang;
}

function onlyNumberKey(evt){
    // Only ASCII charactar in that range allowed 
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode 
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
        return false; 
    }

    return true; 
} 
</script>