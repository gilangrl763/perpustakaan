<?
$reqId = $this->input->post("reqId");
$reqNama = $this->input->post("reqNama");
$reqPrefix = $this->input->post("reqPrefix");


if($reqElement == "")
    $reqElement = "reqBidangUsahaId";

$id = rand();
?>
<tr>
	<td><?=$reqNama?></td>
    <td>
        <input id="reqBidangKualifikasiPilih<?=$id?>" name="reqBidangKualifikasiPilih[]" class="easyui-combotree" style="width:220px;" data-options="
        url: 'bidang_usaha_json/combo_kualifikasi/?reqId=<?=$reqId?>',
        method: 'get',
        required: true,
        onChange: function(rec){
            $('#reqBidangKualifikasi<?=$id?>').val($('#reqBidangKualifikasiPilih<?=$id?>').combotree('getValues'));
        }
    " value="">
    <input type="hidden" id="reqBidangKualifikasi<?=$id?>" name="reqBidangKualifikasi[]" value="" />
	<script>
		$("#reqBidangKualifikasiPilih<?=$id?>").combotree();
	</script>
    </td>
    <td><input type="hidden" name="reqBidangUsahaId[]" value="<?=$reqId?>" /><a title="#"  onclick="$(this).parent().parent().remove();" class="btn-aksi"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
</tr>