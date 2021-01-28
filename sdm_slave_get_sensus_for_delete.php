<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//exit("Error:A");

$ki=$_POST['ki'];
$method=$_POST['method'];

//exit("Error:$ki");

switch($method)
{
	case 'delete':
		$tab="delete from ".$dbname.".datasensus where karyawanid='".$ki."' ";
		//echo $tab;
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
}
?>