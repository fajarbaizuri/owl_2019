<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>	

<?php

$method=$_POST['method'];
$kdvhc=isset($_POST['kdvhc'])?$_POST['kdvhc']:'';
$material=isset($_POST['material'])?$_POST['material']:'';
$kapasitas=isset($_POST['kapasitas'])?$_POST['kapasitas']:'';

switch($method)
{
	case 'insert':
	
		$str="insert into ".$dbname.".bgt_vhc_kapasitas (`kodevhc`,`material`,`kapasitas`)
		values ('".$kdvhc."','".$material."','".$kapasitas."')";
		//exit("Error.$sDel2");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case'loadData':
		$no=0;
		$str="select * from ".$dbname.".bgt_vhc_kapasitas";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{	
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td>".$bar1['kodevhc']."</td>";
			$tab.="<td>".$bar1['material']."</td>";
			$tab.="<td align=right>".$bar1['kapasitas']."</td>";
			$tab.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodevhc']."','".$bar1['material']."');\"></td></tr>";
		echo $tab;
		}
	
	
	case 'delete':
		$tab="delete from ".$dbname.".bgt_vhc_kapasitas where kodevhc='".$kdvhc."' and material='".$material."'";	
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