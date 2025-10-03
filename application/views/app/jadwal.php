<?
$this->load->model("Jadwal");
$jadwal = new Jadwal();
?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
			<h4 class="page-title">Jadwal</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <a class="btn btn-success btn-sm" href="app/index/jadwal_add"><i class="fa fa-plus"></i> Tambah</a>
                <table id="demo-custom-toolbar" data-toggle="table" data-toolbar="#demo-delete-row" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-sort-name="id" data-page-list="[5, 10, 20]"
                    data-page-size="5" data-pagination="true" data-show-pagination-switch="true" class="table table-hover table-borderless">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Kereta</th>
                            <th>Stasiun Keberangkatan</th>
                            <th>Tanggal Keberangkatan</th>
                            <th>Jam Keberangkatan</th>
                            <th>Stasiun Kedatangan</th>
                            <th>Tanggal Kedatangan</th>
                            <th>Jam Kedatangan</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $jadwal->selectByParamsMonitoring(array());
                        // echo $jadwal->query;
                        while($jadwal->nextRow()){
                        ?>
                        <tr>
                            <td><?=$jadwal->getField("KODE")?></td>
                            <td><?=$jadwal->getField("KERETA_ID")?></td>
                            <td><?=$jadwal->getField("STASIUN_ID_KEBERANGKATAN")?></td>
                            <td><?=$jadwal->getField("TANGGAL_KEBERANGKATAN")?></td>
                            <td><?=$jadwal->getField("JAM_KEBERANGKATAN")?></td>
                            <td><?=$jadwal->getField("STASIUN_ID_KEDATANGAN")?></td>
                            <td><?=$jadwal->getField("TANGGAL_KEDATANGAN")?></td>
                            <td><?=$jadwal->getField("JAM_KEDATANGAN")?></td>
                            <td><?=$jadwal->getField("KETERANGAN")?></td>
                            <td>
                            <div class="button-list">
                                <a type="button" class="btn btn-info btn-xs waves-effect waves-light" href="app/index/jadwal_add/?reqId=<?=$jadwal->getField("JADWAL_ID")?>"><i class="fas fa-pencil-alt"></i></a>
                                <button type="button" class="btn btn-danger btn-xs waves-effect waves-light" onclick="hapusData('<?=$jadwal->getField("JADWAL_ID")?>')"><i class="fas fa-trash"></i></button>
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
        $.post("jadwal_json/delete", { reqId: reqId})
            .done(function(data) {
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/jadwal';
                }
        });
    }
}
</script>