<?
$this->load->model("Pemesan");
$pemesan = new Pemesan();
?>

<div class="row">
	<div class="col-lg-12">
		<div class="page-title-box">
			<h4 class="page-title">Pemesan</h4>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <a class="btn btn-success btn-sm" href="app/index/pemesan_add"><i class="fa fa-plus"></i> Tambah</a>
                <table id="demo-custom-toolbar" data-toggle="table" data-toolbar="#demo-delete-row" data-search="true"
                    data-show-refresh="true" data-show-columns="true" data-sort-name="id" data-page-list="[5, 10, 20]"
                    data-page-size="5" data-pagination="true" data-show-pagination-switch="true" class="table table-hover table-borderless">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>User Login Pemesan</th>
                            <th>Nomer Identitas Pemesan</th>
                            <th>Nomer Telepon</th>
                            <th>Alamat</th>
                            <th>Nama Penumpang</th>
                            <th>Title Penumpang</th>
                            <th>Jadwal</th>
                            <th>Pembayaran</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $pemesan->selectByParamsMonitoring(array());
                        // echo $pemesan->query;
                        while($pemesan->nextRow()){
                        ?>
                        <tr>
                            <td><?=$pemesan->getField("KODE")?></td>
                            <td><?=$pemesan->getField("USERLOGIN_ID_PEMESAN")?></td>
                            <td><?=$pemesan->getField("NOMER_IDENTITAS_PEMESAN")?></td>
                            <td><?=$pemesan->getField("NOMER_TELEPON")?></td>
                            <td><?=$pemesan->getField("ALAMAT")?></td>
                            <td><?=$pemesan->getField("NAMA_PENUMPANG")?></td>
                            <td><?=$pemesan->getField("TITLE_PENUMPANG")?></td>
                            <td><?=$pemesan->getField("JADWAL_ID")?></td>
                            <td><?=$pemesan->getField("PEMBAYARAN_ID")?></td>
                            <td><?=$pemesan->getField("HARGA_ID")?></td>
                            <td><?=$pemesan->getField("STATUS_ID")?></td>
                            <td>
                            <div class="button-list">
                                <a type="button" class="btn btn-info btn-xs waves-effect waves-light" href="app/index/pemesan_add/?reqId=<?=$pemesan->getField("PEMESANAN_ID")?>"><i class="fas fa-pencil-alt"></i></a>
                                <button type="button" class="btn btn-danger btn-xs waves-effect waves-light" onclick="hapusData('<?=$pemesan->getField("PEMESANAN_ID")?>')"><i class="fas fa-trash"></i></button>
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
        $.post("pemesan_json/delete", { reqId: reqId})
            .done(function(data) {
                arrData = data.split("|");
                if(arrData[0] == "GAGAL"){
                    alert(arrData[1]);  
                }
                else{
                    alert(arrData[1]);
                    document.location.href = 'app/index/pemesan';
                }
        });
    }
}
</script>