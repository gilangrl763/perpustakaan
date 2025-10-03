<?
$this->load->model("Pemesanan");
$this->load->model("Jadwal");
$pemesanan = new Pemesanan();
$jadwal = new Jadwal();

$reqId = $this->input->get("reqId");
$reqJadwalId = $this->input->get("reqJadwalId");
$reqJumlahPenumpang = $this->input->get("reqJumlahPenumpang");

if($reqId == ""){
    $reqMode = "insert";
    $jadwal->selectByParamsMonitoring(array("A.JADWAL_ID"=>$reqJadwalId));
    $jadwal->firstRow();
    $reqHarga = $reqJumlahPenumpang * $jadwal->getField("HARGA");
}
else{
    $reqMode = "update";

    $pemesanan->selectByParams(array("PEMESANAN_ID"=>$reqId,"A.USER_LOGIN_ID"=>$this->USER_LOGIN_ID));
    $pemesanan->firstRow();
    $reqKode = $pemesanan->getField("KODE");
    $reqJadwalId = $pemesanan->getField("JADWAL_ID");
    $reqJumlahPenumpang = $pemesanan->getField("JUMLAH_PENUMPANG");
    $reqHarga = $pemesanan->getField("HARGA");
    $reqMetodePembayaran = $pemesanan->getField("METODE_PEMBAYARAN");
    $reqStatus = $pemesanan->getField("STATUS");
    $reqKeterangan = $pemesanan->getField("KETERANGAN");

    $jadwal->selectByParamsMonitoring(array("A.JADWAL_ID"=>$reqJadwalId));
    $jadwal->firstRow();
}


?>

<style>
.pesan{
    color: #fff !important;
}
</style>

<div class="row mt-2">
	<div class="col-2">
	</div>
    <div class="col-8">
        <div class="row">
            <!--- KIRI -->
            <div class="col-8">
                <h3 class="text-light">Formulir Pemesanan Tiket</h3 class="text-light">
                <form id="ff" class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <h4 class="header-title">Data Pemesan</h4>
                                <div class="row">
                                    <!--kiri-->
                                    <div class="col-5">
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Nama Pemesan</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                                <input type="text" name="reqNama" class="form-control" value="<?=$this->NAMA?>" readonly>
                                                <input type="hidden" name="reqUserLoginId" class="form-control" value="<?=$this->USER_LOGIN_ID?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!--Kanan-->
                                    <div class="col-7">
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-envelope"></i></span>
                                               <input type="email" name="reqEmail" class="form-control" value="<?=$this->EMAIL?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?
                for($i=0; $i <$reqJumlahPenumpang; $i++) {
                ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <h4 class="header-title">Data Penumpang <?=$i+1?></h4>
                                <div class="row">
                                    <!--Kiri-->
                                    <div class="col-5">
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Title</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                                <input type="text" name="reqTitleGender[]" id="reqTitleGender" class="form-control easyui-combobox"
                                                data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/title_gender'" 
                                                value="<?=$reqTitleGender?>" style="width:265px"/>
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Tipe Identitas</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-id-card"></i></span>
                                                <input type="text" name="reqTipeIdentitas[]" id="reqTipeIdentitas" class="form-control easyui-combobox"
                                                data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/tipe_identitas'" 
                                                value="<?=$reqTipeIdentitas?>" style="width:260px"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Kanan-->
                                    <div class="col-7">
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Nama Penumpang</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" name="reqNamaPenumpang[]" value="<?=$reqNamaPenumpang?>" 
                                                placeholder="Nama sesuai NIK/Paspor" required>
                                            </div>
                                        </div>
                                        <div class="form-input mt-2">
                                            <label for="simpleinput" class="form-label">Nomer Identitas</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="far fa-id-card"></i></span>
                                                <input type="text" class="form-control" name="reqNomerIdentitas[]" value="<?=$reqNomerIdentitas?>" 
                                                placeholder="No Identitas sesuai Tipe Identitas" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?
                }
                ?>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <h4 class="header-title">Pembayaran</h4>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><i class="far fa-id-card"></i></span>
                                    <input type="text" name="reqMetodePembayaran" id="reqMetodePembayaran" class="easyui-combobox"
                                    data-options="valueField:'id',textField:'text',editable:false,url:'combo_json/metode_pembayaran'" 
                                    value="<?=$reqMetodePembayaran?>" style="width:500px"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <h4 class="header-title">Ketentuan Reservasi Tiket Kereta Api</h4>
                                <ol>
                                    <li>Reservasi dapat dilakukan 3 jam sebelum kereta berangkat.</li>
                                    <li>Harga dan ketersediaan tempat duduk sewaktu-waktu dapat berubah.</li>
                                    <li>Pastikan anda telah menerima email konfirmasi pembayaran dari petugas untuk ditukarkan dengan boarding pass di stasiun online.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="button-list">
                    <input type="hidden" name="reqId" class="form-control" value="<?=$reqId?>">
                    <input type="hidden" name="reqMode" class="form-control" value="<?=$reqMode?>">
                    <input type="hidden" name="reqKode" class="form-control" value="<?=$reqKode?>">
                    <input type="hidden" name="reqJadwalId" class="form-control" value="<?=$reqJadwalId?>">
                    <input type="hidden" name="reqJumlahPenumpang" class="form-control" value="<?=$reqJumlahPenumpang?>">
                    <input type="hidden" name="reqHarga" class="form-control" value="<?=$reqHarga?>">
                    <input type="hidden" name="reqStatus" class="form-control" value="<?=$reqStatus?>">
                    <?
                    if($reqMode == "insert"){
                    ?>
                    <button type="submit" class="btn btn-warning btn-sm waves-effect fw-bold btn-cari"><i class="fa fa-save"></i> Pesan & Bayar</button>
                    <?
                    }
                    ?>
                </div>
                </form>
            </div>

            <!--- KANAN -->
            <div class="col-4">
                <h3 class="text-light">Rincian Pembayaran</h3 class="text-light">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h1 class="text-center">Total Rp <?=numberToIna($reqHarga)?>,-</h1>
                            </div>
                            <div class="card-body">
                                <h3 class="fw-bold"><?=$jadwal->getField('KERETA')?> (<?=$jadwal->getField('KODE_KERETA')?>)</h3>
                                <h5><?=ucWords(strtolower($jadwal->getField('KELAS')))?></h5>
                            </div>
                           
                            <div class="row p-2">
                                <div class="col-5">
                                    <span class="fs-5"><?=$jadwal->getField('STASIUN_KEBERANGKATAN')?></span><br>
                                    <span class="fs-5 fw-bold"><?=$jadwal->getField('JAM_KEBERANGKATAN')?></span><br>
                                    <span class="fs-5"><?=getFormattedDate3($jadwal->getField('TANGGAL_KEBERANGKATAN'))?></span>
                                </div>
                                <div class="col-2 text-center">
                                    <i class=" fas fa-angle-double-right  text-warning fs-2"></i>  
                                </div>
                                <div class="col-5">
                                    <span class="fs-5"><?=$jadwal->getField('STASIUN_KEDATANGAN')?></span><br>
                                    <span class="fs-5 fw-bold"><?=$jadwal->getField('JAM_KEDATANGAN')?></span><br>
                                    <span class="fs-5"><?=getFormattedDate3($jadwal->getField('TANGGAL_KEDATANGAN'))?></span>
                                </div>   
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-2">
</div>
</div>

<script src="js/jquery-1.12.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#ff").submit(function(){
        $.ajax({   
            type: "POST",
            data : $(this).serialize(),
            url: "pemesanan_json/add",   
            success: function(data){
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'main/index/pesanan_saya';
                }
            }   
        });
        
        return false;    
    });   
});
</script>


 
    