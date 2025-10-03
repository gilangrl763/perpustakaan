<?
include_once("functions/default.func.php");
include_once("functions/date.func.php");
include_once("functions/string.func.php");

$reqBandaraId = $this->input->get("reqBandaraId");

if($reqBandaraId == ""){
  $reqBandaraId = $this->BANDARA_ID;
}
else{
  $reqBandaraId = $reqBandaraId;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="<?=base_url()?>">
    <link href="libraries/bootstrap-3.3.7/docs/dist/css/bootstrap.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="libraries/bootstrap-3.3.7/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- DATATABLE -->
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/media/css/jquery.dataTables.css">

    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/examples/resources/syntax/shCore.css">
    <link rel="stylesheet" type="text/css" href="libraries/DataTables-1.10.7/examples/resources/demo.css">
    <style type="text/css" class="init">
      th, td { white-space: nowrap; }
      div.dataTables_wrapper {
      /*width: 100%;
      margin: 0 auto;
      border: 2px solid red;
      height: calc(100vh - 80px);*/
      }

    </style>

  	<script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/media/js/jquery.js"></script>
  	<script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/media/js/jquery.dataTables.js"></script>
  	<script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/examples/resources/syntax/shCore.js"></script>
  	<script type="text/javascript" language="javascript" src="libraries/DataTables-1.10.7/examples/resources/demo.js"></script>

    <link rel="stylesheet" href="css/gaya.css" type="text/css">
    <link rel="stylesheet" href="libraries/font-awesome-4.7.0/css/font-awesome.css" type="text/css">
    <link href="css/tabs.css" rel="stylesheet" type="text/css" />
    <link href="css/begron.css" rel="stylesheet" type="text/css">
  </head>

  <body>

    <?
    if($this->USER_GROUP_ID == "5" || $this->USER_GROUP_ID == "15"){
    ?>
    <div class="area-shortcut">
      <div class="area-icon">
        <a href="watchroom" target="_blank">
          <i class="fa fa-wpexplorer"></i>
        </a>
      </div>
      <div class="area-text">
        <a href="watchroom" target="_blank">
          <span>Watchroom</span>
        </a>
      </div>
    </div>
    <?
    }
    ?>

    <?  
    $data = array(
      'reqFolder' => $reqFolder,
      'reqFilename' => $reqFilename,
      'reqParse1' =>$reqParse1,
      'reqParse2' => $reqParse2,
      'reqParse3' =>$reqParse3,
      'reqParse4' => $reqParse4,
      'reqParse5' =>$reqParse5
    );
    
    $this->load->view($reqFolder.'/'.$reqFilename, $data);
    ?>


    
    <script src="libraries/bootstrap-3.3.7/docs/dist/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/vendor/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="libraries/bootstrap-3.3.7/docs/assets/js/ie10-viewport-bug-workaround.js"></script>
    
    <!-- EASYUI 1.4.5 -->
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="libraries/jquery-easyui-1.4.5/themes/icon.css">
    <script type="text/javascript" src="libraries/jquery-easyui-1.4.5/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="libraries/easyui/kalender-easyui.js"></script>
    <script type="text/javascript" src="libraries/easyui/globalfunction.js"></script>
    
    <!-- POP UP WINDOWS -->
    <link rel="stylesheet" href="libraries/DHTMLWindow/windowfiles/dhtmlwindow.css" type="text/css" />
    <script type="text/javascript" src="libraries/DHTMLWindow/windowfiles/dhtmlwindow.js"></script>
    
    <!-- TREE --->
    <link href="libraries/treeTable/doc/stylesheets/master.css" rel="stylesheet" type="text/css" />
    
    <link href="libraries/treeTable/src/stylesheets/jquery.treeTable.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="libraries/treeTable/src/javascripts/jquery.treeTable.js"></script>
    

  </body>
</html>
