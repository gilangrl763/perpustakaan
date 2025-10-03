<?
$this->load->model("Kereta");
$kereta = new Kereta();
?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
			<h4 class="page-title">Kereta</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <a class="btn btn-success btn-sm" href="app/index/kereta_add"><i class="fa fa-plus"></i> Tambah</a>
                <table id="demo-custom-toolbar" data-toggle="table" data-toolbar="#demo-delete-row" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-sort-name="id" data-page-list="[5, 10, 20]"
                    data-page-size="5" data-pagination="true" data-show-pagination-switch="true" class="table table-hover table-borderless">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $kereta->selectByParamsMonitoring(array());
                        // echo $kereta->query;
                        while($kereta->nextRow()){
                        ?>
                        <tr>
                            <td><?=$kereta->getField("KODE")?></td>
                            <td><?=$kereta->getField("NAMA")?></td>
                            <td>
                            <div class="button-list">
                                <a type="button" class="btn btn-info btn-xs waves-effect waves-light" href="app/index/kereta_add/?reqId=<?=$kereta->getField("KERETA_ID")?>"><i class="fas fa-pencil-alt"></i></a>
                                <button type="button" class="btn btn-danger btn-xs waves-effect waves-light" onclick="hapusData('<?=$kereta->getField("KERETA_ID")?>')"><i class="fas fa-trash"></i></button>
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
        $.post("kereta_json/delete", { reqId: reqId})
            .done(function(data) {
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/kereta';
                }
        });
    }
}
</script>