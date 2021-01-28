<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	


<?php		
$method=$_POST['method'];
$thn=$_POST['thn'];
$tipe=$_POST['tipe'];
$hs=$_POST['hs'];

?>

<?php
switch($method)
{
	case 'insert':
		$in="insert into ".$dbname.".bgt_hargakontrakhm (`tahunbudget`,`tipe`,`hargasatuan`) values ('".$thn."','".$tipe."','".$hs."')";
		if(mysql_query($in))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		
	break;
	
	

		
case'loadData':
	
	//exit("Error:MASUK");
		$no=0;
		$wi="select * from ".$dbname.".bgt_hargakontrakhm";
		//echo $wi;
		$bi=mysql_query($wi) or die(mysql_error());
		while($wibi=mysql_fetch_assoc($bi))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$wibi['tahunbudget']."</td>";
			$tab.="<td align=right>".$wibi['tipe']."</td>"; 
			$tab.="<td align=right>".number_format($wibi['hargasatuan'],2)."</td>"; 
			$tab.="<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$wibi['tahunbudget']."','".$wibi['tipe']."');\"></td>";
		echo $tab;
		
		}
		
		
	case 'delete':
		$ind="delete from ".$dbname.".bgt_hargakontrakhm where tahunbudget='".$thn."' and tipe='".$tipe."' ";
		if(mysql_query($ind))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
default:
}
?>
