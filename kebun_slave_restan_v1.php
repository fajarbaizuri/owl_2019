<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$per=$_POST['per'];
$kodeorg=$_POST['kodeorg'];
$jjg=$_POST['jjg'];
$cat=$_POST['cat'];
$oldkodeorg=$_POST['oldkodeorg'];
$method=$_POST['method'];


$thnbudgetHeader=$_POST['thnbudgetHeader'];
$blokheader=$_POST['blokheader'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
?>

<?php
switch($method)
{
	case 'insert':
	
		
		$oldkodeorg==''?$oldkodeorg=$_POST['kodeorg']:$oldkodeorg=$_POST['oldkodeorg'];
			
		$sRicek="select * from ".$dbname.".kebun_restan_v1 where kodeorg='".$oldkodeorg."'  ";
		//		exit("Error:$sRicek");
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		
		if($rRicek>0)
		{
		$sDel="delete from ".$dbname.".kebun_restan_v1
				where kodeorg='".$oldkodeorg."'  ";	    
			if(mysql_query($sDel))
			{
			$sDel2="insert into ".$dbname.".kebun_restan_v1 (`periode`,`kodeorg`,`jumlahjjgrestan`,`catatan`,`updateby`)
		values ('".$per."','".$kodeorg."','".$jjg."','".$cat."','".$_SESSION['standard']['userid']."')";
		//echo $sDel2;
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
		$sDel2="insert into ".$dbname.".kebun_restan_v1 (`periode`,`kodeorg`,`jumlahjjgrestan`,`catatan`,`updateby`)
		values ('".$per."','".$kodeorg."','".$jjg."','".$cat."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	
case'loadData':
		
		$tmbh='';
                if($thnbudgetHeader!='')
                {
                    $tmbh=" and periode='".$thnbudgetHeader."' ";
                }
		
		$tmbh2='';
                if($blokheader!='')
                {
                    $tmbh2=" and kodeorg='".$blokheader."' ";
                }		
				
		
		$no=0;
		$str="select * from ".$dbname.".kebun_restan_v1 where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'  ".$tmbh." ".$tmbh2." order by periode desc";
		//echo $str;
		$str2=mysql_query($str) or die(mysql_error());
		//$res1=mysql_query($str2);
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";

			$tab.="<td align=right>".$bar1['periode']."</td>";
			//$tab.="<td align=left>".$bar1['kodeorg']."</td>";
			
			
			$tab.="<td align=left>".$optNm[$bar1['kodeorg']]."</td>";
			$tab.="<td align=right>".$bar1['jumlahjjgrestan']."</td>";
			$tab.="<td align=right>".$bar1['catatan']."</td>";
			$tab.="<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['periode']."','".$bar1['kodeorg']."','".$bar1['jumlahjjgrestan']."','".$bar1['catatan']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodeorg']."');\">
			</td>
			";
		echo $tab;
		}
		
	
	
	case 'getthnbudgetHeader':
		//$cat="select cat from ".$dbname.".kebun_restan_v1 WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optperHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct periode FROM ".$dbname.".kebun_restan_v1 where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by periode desc";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optperHeader.="<option value='".$rThn['periode']."'>".$rThn['periode']."</option>";
		}
		echo $optperHeader;
	break;
	
	
	case 'getblokheader':
		//$cat="select cat from ".$dbname.".kebun_restan_v1 WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optgetblokheader="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct kodeorg FROM ".$dbname.".kebun_restan_v1 where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by periode asc";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optgetblokheader.="<option value='".$rThn['kodeorg']."'>".$optNmOrg[$rThn['kodeorg']]."</option>";
		}
		echo $optgetblokheader;
	break;
	
	case 'delete':
		$tab="delete from ".$dbname.".kebun_restan_v1 where kodeorg='".$kodeorg."' ";
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;

	
	//$optKdorg2.="<option value=".$rOrg3['kodeorg'].">".$optNmOrg[$rOrg3['kodeorg']]."</option>";
	
	
default:
}
?>
