<?
require_once('master_validation.php');
require_once('config/connection.php');

  $action=$_POST['action'];
  $val  =$_POST['id'];
   if($action=='insert'){
   $stra="insert into ".$dbname.".sdm_ho_thr_setup
			(component) values(".$val.")";
   }
   else
   {
   $stra="delete from ".$dbname.".sdm_ho_thr_setup
			where component=".$val;   	
   }
	if(mysql_query($stra))
	{
	}
	else
	{
		echo " Error: ".addslashes(mysql_error($conn1));
	} 	

?>
