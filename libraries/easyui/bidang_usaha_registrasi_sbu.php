<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?=base_url();?>" />

<link rel="stylesheet" href="css/gaya.css" type="text/css">
<link rel="stylesheet" href="css/gaya-bootstrap.css" type="text/css">

<!-- STYLE MASING2 PERUSAHAAN -->
<?
$this->load->model("KategoriBidang");
$reqKategoriBidangId = $this->input->get("reqKategoriBidangId");
$reqIjinUsahaId = $this->input->get("reqIjinUsahaId");
$kategori_bidang = new KategoriBidang();
$reqKategoriBidang = $kategori_bidang->getKategoriBidang($reqKategoriBidangId);

$sesPerusahaan = $this->session->userdata('perusahaanTerpilih');
if($sesPerusahaan == ""){} else {
?>
<link rel="stylesheet" href="css/gaya<?=$sesPerusahaan?>.css" type="text/css">
<?
}
?>

<!-- BOOTSTRAP -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="libraries/bootstrap/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="libraries/font-awesome/4.5.0/css/font-awesome.css">

<script src="js/jquery-1.11.1.js" type="text/javascript" charset="utf-8"></script> 

    <style>
	.col-md-12{
		padding-left:0px;
		padding-right:0px;
	}
	</style>

<!-- EASYUI -->
<link rel="stylesheet" type="text/css" href="libraries/easyui/themes/default/easyui.css">
<script type="text/javascript" src="libraries/easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="libraries/easyui/kalender-easyui.js"></script>
<script type="text/javascript" src="libraries/easyui/globalfunction.js"></script>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">

<style>
html, body{
	height:100%;
}
table.datagrid-btable .datagrid-cell{
	width:auto !important;
}

</style>
    
</head>

<body class="body-popup">
	
    <div class="container-fluid container-treegrid">
    	
        <div class="row row-treegrid">
        	<div class="col-md-12 col-treegrid">
            	<div class="area-konten-atas">
                	<div class="judul-halaman">
                    	<?
                        if($reqKategoriBidang == "")
							echo "Bidang Usaha";
						else
							echo "Kategori Bidang ".$reqKategoriBidang;
						?>
                    	<div class="info">
							<i class="fa fa-warning" aria-hidden="true"></i> Double-click untuk memilih bidang usaha.  
                        </div>
                    </div>
                </div>
             	<div id="idpencarian" class="area-pencarian">
                        <label>Pencarian : <input class="" placeholder="" name="reqPencarian" aria-controls="example" type="search"></label>
                </div>
				<div id="tableContainer" class="tableContainer tableContainer-treegrid">
                	<table id="treeSatker" class="easyui-treegrid" style="width:700px;height:300px"
                            data-options="
                                url: 'bidang_usaha_json/json/?reqKategoriBidangId=<?=$reqKategoriBidangId?>',
                                method: 'get',
                                idField: 'id',
                                treeField: 'text'
                            ">
                        <thead>
                            <tr>
                                <th data-options="field:'text'" width="100%">Nama</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
            </div>
        </div>        
    </div>
    
	<script>
    $(document).ready( function () {
		$('input[name=reqPencarian]').keyup(function() {
			var value = this.value;
			$("html, body").animate({ scrollTop: 0 });
	
			var urlApp = 'bidang_usaha_json/json/?reqSearch='+ value;
			$('#treeSatker').treegrid(
			{
				url: urlApp
			});	
		});
		
        $('#treeSatker').treegrid({
              onDblClickRow: function(param){
                // var tbody = <?=$reqIjinUsahaId?>;
              	if(param.parent_id == '0'){
              	 	$.messager.alert('Info','Bidang Usaha tidak dapat dipilih','info');
              	 	return false;
              	}
              	else{
				 	top.tambahBidangUsahaSBU(param.id, param.nama);
		  		 	$('#treeSatker').treegrid('deleteRow', param.id);
              	}

            }
        });
    });
	    
		$("#dnd-example tr").click(function(){
		   $(this).addClass('selected').siblings().removeClass('selected');
		   var id = $(this).find('td:first').attr('id');
		   var title = $(this).find('td:first').attr('title');

			
		});
    
	</script>
    
    <script>
		// Mendapatkan tinggi .area-konten-atas
		var divTinggi = $(".area-konten-atas").height();
		//alert(divTinggi);
		
		// Menentukan tinggi tableContainer
		$('#tableContainer').css({ 'height': 'calc(100% - ' + divTinggi+ 'px)' });
	</script>

</body>
</html>
