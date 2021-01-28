<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	



<?php
//exit("Error:MASUK");
$nopp=$_POST['nopp'];
$method=$_POST['method'];

switch($method)
{
	//gabung
	case'cekno':
	
		##UNTUK VALIDASI DATA YANG UDAH DI TUTUP GK BISA INSERT LAGI
		$iCek="select distinct nopp from ".$dbname.".log_prapoht where nopp='".$nopp."'";
		//exit("Error:$nopp");
		$ada=true;
		$nCek=mysql_query($iCek)or die(mysql_error());
		while($dCek=mysql_fetch_assoc($nCek))
		{
			if ($ada==true)
			{
				echo "warning : No PP untuk ".$nopp." sudah ada, silahkan masukan data kembali";
				exit();	
			}
			else
			{
			}	
		}
	break;	
	default;
	}
	//default;

?>