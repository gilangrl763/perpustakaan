<? 
/* *******************************************************************************************************
MODUL NAME 			: MTSN LAWANG
FILE NAME 			: 
AUTHOR				: 
VERSION				: 1.0
MODIFICATION DOC	:
DESCRIPTION			: 
***************************************************************************************************** */

  /***
  * Entity-base class untuk mengimplementasikan tabel kategori.
  * 
  ***/

  class Entity extends CI_Model{ 

	var $currentRow;
	var $errorMsg;
    var $rowCount;
    var $rowResult=array();
	var $currentRowIndex;
    /**
    * Class constructor.
    **/
    function Entity()
	{
      $this->load->database(); 
    }
	
	public function __construct()
	{
		parent::__construct();
	}

	public function setField($fieldName,$fieldValue){
		$fieldName = strtolower($fieldName);
						
		$this->currentRow[$fieldName]=$fieldValue;
	}

	public function getField($fieldName){
		$fieldName = strtolower($fieldName);
		
		return $this->currentRow[$fieldName];
	}
	
	public function getToken($primaryId, $filter=""){
		$str = "SELECT MD5('".$primaryId."'||'G!l4n6R0mY'||TO_CHAR(CURRENT_TIMESTAMP, 'HH24:MI')) AS maxvalue  ";
			
		if($filter != ""){
			$str = $str . " WHERE $filter";
		}

		$query = $this->db->query($str);
		$row = $query->first_row();
		
		$temp = $row->maxvalue;
			
		return $temp;
	}
	
	
	public function getNextId($idName,$tableName,$filter=""){
		$str = "SELECT MAX($idName::int) nilai FROM $tableName ";
		if($filter != "")
			$str = $str . " WHERE $filter";		
			$query = $this->db->query($str);
			$row = $query->first_row();
			
			if($row->nilai > 0){
				$temp = $row->nilai + 1; //next key*/
			}else{ //belum ada recordnya
        		$temp = 1;
      		}
		return $temp;
	}

	function getNextKode(){
		$str = " SELECT MAX(SUBSTR(KODE, 5, 6)) + 1 MAXVALUE FROM REKANAN ";
			
		$query = $this->db->query($str);
		$row = $query->first_row();
		
		if($row->MAXVALUE > 0){
			$temp = $row->MAXVALUE + 1; //next key*/
		}else{ //belum ada recordnya
        	$temp = 1;
      	}
		
		return $temp;
	}	
	
	function selectLimit($sql,$numrow=-1,$offset=-1){

		$_rowResult = array();
		$this->rowResult = array();
		$this->rowCount = 0;
		
		$sql = $this->db->_limit($sql, $numrow, $offset);
				
		$rs = $this->db->query($sql);
		
		if(!$rs){
			$this->errorMsg = "PESAN ERROR : ";
			
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row) 
			{
				foreach($row as $key=>$val){
					$arr[strtolower($key)] = $val;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}	
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;
			
			$this->currentRowIndex = -1;
			$this->currentRow = array();
			
		}
		return $rs;
    }

	function selectClobLimit($sql,$numrow=-1,$offset=-1){

		$_rowResult = array();
		$_clobField = array();
		$this->rowResult = array();
		$this->rowCount = 0;
		
	
		$sql = $this->db->_limit($sql, $numrow, $offset);
		$rs = $this->db->query($sql);
		$fields = $rs->field_data(); 
		foreach ($fields as $field)
		{
			if($field->type == "CLOB")
			   $_clobField[] = $field->name;		  
		} 
		if(!$rs){
			$this->errorMsg = "PESAN ERROR : ";
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row) 
			{
				foreach($row as $key=>$val){		
					if(in_array($key, $_clobField))
					{
						if($val)
							$value = $val->read($val->size());		
						else
							$value = "";
					}
					else
						$value = $val;
					$arr[strtolower($key)] = $value;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}	
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;
			
			$this->currentRowIndex = -1;
			$this->currentRow = array();
			
		}
		return $rs;
    }
	
	function selectClob($sql){

		$_rowResult = array();
		$this->rowResult = array();
		$this->rowCount = 0;
		
		$rs = $this->db->query($sql);
		if(!$rs){
			$this->errorMsg = "PESAN ERROR : ";
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row) 
			{
				foreach($row as $key=>$val){		
					if($val)
						$value = $val->read($val->size());		
					else
						$value = "";
					$arr[strtolower($key)] = $value;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}	
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;
			
			$this->currentRowIndex = -1;
			$this->currentRow = array();
			
		}
		return $rs;
    }
	
	function select($sql){

		$_rowResult = array();
		$this->rowResult = array();
		$this->rowCount = 0;
		
		$rs = $this->db->query($sql);
		if(!$rs){
			//$this->errorMsg = "PESAN ERROR : ";
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row) 
			{
				foreach($row as $key=>$val){
					$arr[strtolower($key)] = $val;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}	
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;
			
			$this->currentRowIndex = -1;
			$this->currentRow = array();
			
		}
		return $rs;
    }
		
	 function nextRow(){
		if($this->currentRowIndex < $this->rowCount-1){
			$this->currentRowIndex++;
			$this->setRowValue();
			return true;
		} else {
			return false;
    	}
	}
	
	function firstRow(){
      	if($this->rowCount>0){
			$this->currentRowIndex=0;
			$this->setRowValue();
			return true;
      	} else {
        	return false;
    	}
	}
	
	function setRowValue(){
		$this->currentRow = $this->rowResult[$this->currentRowIndex];
	}	
	
	function execQuery($sql){
		
		return $this->db->query($sql);
    }

	function updateClob($table, $primary_key, $primary_value, $field, $field_value){   

	   $jumlah_char = strlen($field_value);
	   $jumlah_char = round($jumlah_char / 4000);
	   $this->db->query("UPDATE $table SET $field='' WHERE $primary_key = $primary_value");

	   if(trim($field_value) == "")
	   {}
	   else
	   {
		   for($i=0;$i<=$jumlah_char;$i++)
		   { 	   
			   $a = $i * 4000;
			   $b = 4000;
			   $value = substr($field_value, $a, $b);
			   $this->db->query("UPDATE $table SET $field = $field || ? WHERE $primary_key = $primary_value",array($value));
		   }
	   }
	   
    }
	  

	function selectLimitDB($koneksi, $sql,$numrow=-1,$offset=-1){

		$sql = $koneksi->_limit($sql, $numrow, $offset);
		$sql = $koneksi->query($sql);
		if(!$rs){
			$this->errorMsg = "PESAN ERROR : ";
		}
		return $rs;
    }	

  } 
?>