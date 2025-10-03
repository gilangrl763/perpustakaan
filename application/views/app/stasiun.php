<?
$this->load->model("Stasiun");
$stasiun = new Stasiun();
?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
			<h4 class="page-title">Stasiun</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <a class="btn btn-success btn-sm" href="app/index/stasiun_add"><i class="fa fa-plus"></i> Tambah</a>
                <table id="demo-custom-toolbar" data-toggle="table" data-toolbar="#demo-delete-row" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-sort-name="id" data-page-list="[5, 10, 20]"
                    data-page-size="5" data-pagination="true" data-show-pagination-switch="true" class="table table-hover table-borderless">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Kelurahan id</th>
                            <th>Kecamatan id</th>
                            <th>Kota id</th>
                            <th>Provinsi id</th>
                            <th>Telepon</th>
                            <th>Fax</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $stasiun->selectByParamsMonitoring(array());
                        // echo $stasiun->query;
                        while($stasiun->nextRow()){
                        ?>
                        <tr>
                            <td><?=$stasiun->getField("KODE")?></td>
                            <td><?=$stasiun->getField("NAMA")?></td>
                            <td><?=$stasiun->getField("ALAMAT")?></td>
                            <td><?=$stasiun->getField("KELURAHAN_ID")?></td>
                            <td><?=$stasiun->getField("KECAMATAN_ID")?></td>
                            <td><?=$stasiun->getField("KOTA_ID")?></td>
                            <td><?=$stasiun->getField("PROVINSI_ID")?></td>
                            <td><?=$stasiun->getField("TELEPON")?></td>
                            <td><?=$stasiun->getField("FAX")?></td>
                            <td>
                            <div class="button-list">
                                <a type="button" class="btn btn-info btn-xs waves-effect waves-light" href="app/index/stasiun_add/?reqId=<?=$stasiun->getField("STASIUN_ID")?>"><i class="fas fa-pencil-alt"></i></a>
                                <button type="button" class="btn btn-danger btn-xs waves-effect waves-light" onclick="hapusData('<?=$stasiun->getField("STASIUN_ID")?>')"><i class="fas fa-trash"></i></button>
                            </div>
                            </td>
                        </tr>
                        <?
                        }
                        ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>

<script>
function hapusData(reqId){
    if(confirm('Apakah anda yakin ingin menghapus data?')){
        $.post("stasiun_json/delete", { reqId: reqId})
            .done(function(data) {
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/stasiun';
                }
        });
    }
}
</script>