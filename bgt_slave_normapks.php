<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$st=$_POST['st'];
$js=$_POST['js'];
$jhs=$_POST['jhs'];
$jhb=$_POST['jhb'];
$oldst=$_POST['oldst'];
$method=$_POST['method'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
?>

<?php
switch($method)
{
	case 'insert':
	
		$oldst==''?$oldst=$_POST['kodeorg']:$oldst=$_POST['oldst'];
		$sRicek="select * from ".$dbname.".bgt_norma_pks where kodeorg='".$oldst."'";
				//exit("Error:$sRicek");
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		
		if($rRicek>0)
		{
		$sDel="delete from ".$dbname.".bgt_norma_pks
				where kodeorg='".$oldst."' ";	    
			if(mysql_query($sDel))
			{
			$sDel2="insert into ".$dbname.".bgt_norma_pks (`kodeorg`,`jamsetahun`,`hksku`,`hkbhl`)
		values ('".$st."','".$js."','".$jhs."','".$jhb."')";
		
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
			}
			else	
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}	
		}
		else
		{
		$sDel2="insert into ".$dbname.".bgt_norma_pks (`kodeorg`,`jamsetahun`,`hksku`,`hkbhl`)
		values ('".$st."','".$js."','".$jhs."','".$jhb."')";
		//exit("Error.$sDel2");
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
		
case'loadData':
		
		$no=0;
		//$str="select * from ".$dbname.".bgt_norma_pks where substr('".$_SESSION['empl']['lokasitugas']."',0,4)='CBGM' ";
		$str="select * from ".$dbname.".bgt_norma_pks where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' ";
		//
		//echo $str;
		$str2=mysql_query($str) or die(mysql_error());
		//$res1=mysql_query($str2);
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$optNm[$bar1['kodeorg']]."</td>";
			$tab.="<td align=right>".$bar1['jamsetahun']."</td>";
			$tab.="<td align=right>".number_format($bar1['hksku'])."</td>";
			$tab.="<td align=right>".number_format($bar1['hkbhl'])."</td>";
			$tab.="<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['kodeorg']."','".$bar1['jamsetahun']."','".$bar1['hksku']."','".$bar1['hkbhl']."');\"></td>";
		echo $tab;
		}
	
default:
}
?>
