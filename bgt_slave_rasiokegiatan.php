<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>	

<?php		
$method=$_POST['method'];
$kdkeg=$_POST['kdkeg'];
$rasio=$_POST['rasio'];

$namakeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$namakelompok=makeOption($dbname,'setup_kegiatan','kodekegiatan,kelompok');



switch($method)
{
	case 'insert':
		$str="insert into ".$dbname.".bgt_rasiokegiatan (`kodekegiatan`,`rasio`,`updateby`)
		values ('".$kdkeg."','".$rasio."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		
	break;

case'loadData':
		$no=0;
		$str="select * from ".$dbname.".bgt_rasiokegiatan";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			
			/*$a="select kodekegiatan,namakegiatan from ".$dbname.".setup_kegiatan where kodekegiatan='".$bar1['kodekegiatan']."'";
			$b=mysql_query($a);
			$c=mysql_fetch_assoc($b);
				$namakeg=$c['namakegiatan'];*/
			
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=right>".$bar1['kodekegiatan']."</td>";
			$tab.="<td align=left>".$namakeg[$bar1['kodekegiatan']]."</td>";
			$tab.="<td align=left>".$namakelompok[$bar1['kodekegiatan']]."</td>";
			$tab.="<td align=center>".$bar1['rasio']."</td>";
				$tab.="<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodekegiatan']."');\"></td>";
		echo $tab;
		}

	case 'delete':
		$tab="delete from ".$dbname.".bgt_rasiokegiatan where kodekegiatan='".$kdkeg."'";
		if(mysql_query($tab))
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