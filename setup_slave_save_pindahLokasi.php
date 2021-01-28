<?
require_once('master_validation.php');
require_once('config/connection.php');

$pt=$_POST['tjbaru'];
$lokasibaru=$_POST['lokasibaru'];

	$str="update ".$dbname.".datakaryawan set kodeorganisasi='".$pt."',
	      lokasitugas='".$lokasibaru."'
	       where karyawanid=".$_SESSION['standard']['userid'];
	if(mysql_query($str))
	{
		echo "Updated";
	}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
?>
