<?
include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

$this->load->model("Pemesanan");
$pemesanan = new Pemesanan();
?>

<div class="row">
	<div class="col-2">
    </div>
    <div class="col-lg-8">
		<div class="page-title-box p-2">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item text-white"><a href="main/index/home" class="text-white">Beranda</a></li>
                    <li class="breadcrumb-item active text-white">Pesanan Saya</li>
                </ol>
            </div>
			<h4 class="page-title text-white">Pesanan Saya</h4>
		</div>
	</div>
    <div class="col-2">
    </div>
</div>

<div class="row">
    <div class="col-2">
    </div>
    <div class="col-lg-8">
        <div class="card p-2">
            <div class="card-body">
                <form id="ff" method="POST" enctype="multipart/form-data">
                    <table id="datatable-buttons" class="table table-sm table-hover table-striped dt-responsive nowrap w-100">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Kode</th>
                                <th>Jumlah Penumpang</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $pemesanan->selectByParams(array("A.USER_LOGIN_ID"=>$this->USER_LOGIN_ID));
                            while($pemesanan->nextRow()){
                            ?>
                            <tr>
                                <td><?=$pemesanan->getField('KODE')?></td>
                                <td><?=$pemesanan->getField('JUMLAH_PENUMPANG')?></td>
                                <td><?=$pemesanan->getField('HARGA')?></td>
                                <td><a href="main/index/pemesanan_tiket/?reqId=<?=$pemesanan->getField('PEMESANAN_ID')?>" class="btn btn-sm btn-primary">Lihat</a></td>
                            </tr>
                            <?
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="col-2">
    </div>
</div>