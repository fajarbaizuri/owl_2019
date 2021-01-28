






<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$kodeorg=$_POST['kodeorg'];
$thntanam=$_POST['thntanam'];
$bjr=$_POST['bjr'];
$thnproduksi=$_POST['thnproduksi'];
$jenisbbt=$_POST['jenisbbt'];




$oldkodeorg=$_POST['oldkodeorg'];
//$oldbjr=$_POST['oldbjr'];
$oldthnproduksi=$_POST['oldthnproduksi'];
$oldthntnm=$_POST['oldthntnm'];

$method=$_POST['method'];
$thntnm=$_POST['thntnm'];
$kdorg=$_POST['kdorg'];

$thnprod=$_POST['thnprod'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

?>

<?php
switch($method)
{
	case 'insert':

		$oldkodeorg==''?$oldkodeorg=$_POST['kodeorg']:$oldkodeorg=$_POST['oldkodeorg'];
		//$oldbjr==''?$oldbjr=$_POST['bjr']:$oldbjr=$_POST['oldbjr'];
		$oldthntnm==''?$oldthntnm=$_POST['thntnm']:$oldthntnm=$_POST['oldthntnm'];
		$oldthnproduksi==''?$oldthnproduksi=$_POST['thnproduksi']:$oldthnproduksi=$_POST['oldthnproduksi'];
		//exit("Error:$oldkodeorg");
			
		$sRicek="select * from ".$dbname.".kebun_5bjr where  kodeorg='".$oldkodeorg."' and thntanam='".$oldthntnm."' and tahunproduksi='".$oldthnproduksi."' ";
				//exit("Error:$sRicek");
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		
		if($rRicek>0)
		{
		$sDel="delete from ".$dbname.".kebun_5bjr where
				kodeorg='".$oldkodeorg."' and thntanam='".$oldthntnm."' and tahunproduksi='".$oldthnproduksi."' ";	 
				//exit("Error:$sDel");   
			if(mysql_query($sDel))
			{
			$sDel2="insert into ".$dbname.".kebun_5bjr (`kodeorg`,`bjr`,`tahunproduksi`,`thntanam`,`jenisbibit`)
		values ('".$kodeorg."','".$bjr."','".$thnproduksi."','".$thntanam."','".$jenisbbt."')";
		
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
		$sDel2="insert into ".$dbname.".kebun_5bjr (`kodeorg`,`bjr`,`tahunproduksi`,`thntanam`,`jenisbibit`)
		values ('".$kodeorg."','".$bjr."','".$thnproduksi."','".$thntanam."','".$jenisbbt."')";
		//exit("Error.$sDel2");
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
		
case'loadData':
		
		$tmbh='';
                if($thnprod!='')
                {
                    $tmbh=" and tahunproduksi='".$thnprod."' ";
                }	
				
		$tmbh2='';
                if($thntnm!='')
                {
                    $tmbh=" and thntanam='".$thntnm."' ";
                }
				
		$tmbh3='';
                if($kdorg!='')
                {
                    $tmbh=" and kodeorg='".$kdorg."' ";
                }					
		
		$no=0;
		$str="select * from ".$dbname.".kebun_5bjr where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'  ".$tmbh." ".$tmbh2." ".$tmbh3." ";
		$str2=mysql_query($str) or die(mysql_error());
		//$res1=mysql_query($str2);
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$optNm[$bar1['kodeorg']]."</td>";
			$tab.="<td align=right>".$bar1['tahunproduksi']."</td>";
			$tab.="<td align=right>".$bar1['thntanam']."</td>";
			$tab.="<td align=right>".$bar1['bjr']."</td>";
			$tab.="<td align=right>".$bar1['jenisbibit']."</td>";
			
			$tab.="<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['kodeorg']."','".$bar1['tahunproduksi']."','".$bar1['thntanam']."','".$bar1['bjr']."','".$bar1['jenisbibit']."');\"></td>";
		
		echo $tab;
		}
    break;

	
	case 'thnprod':
		//$bjr="select bjr from ".$dbname.".kebun_5bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optgetthnprod="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct tahunproduksi FROM ".$dbname.".kebun_5bjr where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunproduksi desc";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optgetthnprod.="<option value='".$rThn['tahunproduksi']."'>".$rThn['tahunproduksi']."</option>";
		}
		echo $optgetthnprod;
	break;
	
	case 'thntnm':
		//$bjr="select bjr from ".$dbname.".kebun_5bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optthntnm="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct thntanam FROM ".$dbname.".kebun_5bjr where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by thntanam desc";

		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optthntnm.="<option value='".$rThn['thntanam']."'>".$rThn['thntanam']."</option>";
		}
		echo $optthntnm;
	break;
	
	
	case 'kdorg':
		//$bjr="select bjr from ".$dbname.".kebun_5bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optkdorg="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct kodeorg FROM ".$dbname.".kebun_5bjr where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
		
			
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optkdorg.="<option value='".$rThn['kodeorg']."'>".$optNm[$rThn['kodeorg']]."</option>";
		}
		echo $optkdorg;
	break;
	
	
	
	
	
	
	
default:
}
?>
