<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$method=$_POST['method'];
$kdorg=$_POST['kdorg'];
$tipe=$_POST['tipe'];
$persen=$_POST['persen'];
//$tgl=$_POST['tgl'];;
//exit("Error:$sInsert");	
?>

<?php

$namaorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

switch($method)
{
		case 'insert':
		//exit("Error:masuk");
		$indra="insert into ".$dbname.".pabrik_5persenproduksi (`kodeorg`,`tipe`,`persen`)
				values ('".$kdorg."','".$tipe."','".$persen."')";
		if(mysql_query($indra))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

case'loadData':
		$no=0;
		$str="select * from ".$dbname.".pabrik_5persenproduksi";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td>".$namaorg[$bar1['kodeorg']]."</td>";
			$tab.="<td>".$bar1['tipe']."</td>";
			$tab.="<td align=right>".$bar1['persen']."</td>";
			
			$tab.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodeorg']."','".$bar1['tipe']."');\"></td>";
		echo $tab;
		}

	case 'delete':
		$indra="delete from ".$dbname.".pabrik_5persenproduksi where kodeorg='".$kdorg."' and tipe='".$tipe."'";
		if(mysql_query($indra))
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