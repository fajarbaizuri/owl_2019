<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$method=$_POST['method'];

$kdorg=$_POST['kdorg'];
$hr=$_POST['hr'];
//$tgl=$_POST['tgl'];
$oldbjr=$_POST['oldbjr'];
$bjr=$_POST['bjr'];	
$kgX=$_POST['kg'];	
$oldkdorg=$_POST['oldkdorg'];
$tgl=tanggalsystem($_POST['tgl']);
$supplier=$_POST['supplier'];
$oldtgl=tanggalsystem($_POST['oldtgl']);
$periodesort=$_POST['periodesort'];
$suppsort=$_POST['suppsort'];
$kdorgsort=$_POST['kdorgsort'];
//exit("Error:$sInsert");	
$namasupp=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
$namaorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
?>

<?php

switch($method)
{
	case 'insert':
	
		$ha="insert into ".$dbname.".pabrik_5hargatbs (`supplierid`,`tanggal`,`kodeorg`,`bjr`,`harga`,`updateby`,`kg`)
		values ('".$supplier."','".$tgl."','".$kdorg."','".$bjr."','".$hr."','".$_SESSION['standard']['userid']."','".$kgX."')";
		if(mysql_query($ha))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}	
		/*//$oldtgl==''?$oldtgl=tanggalsystem($_POST['tgl']):$oldtgl=tanggalsystem($_POST['oldtgl']);
		$oldtgl==''?$oldtgl=$_POST['tgl']:$oldtgl=$_POST['oldtgl'];
		$oldkdorg==''?$oldkdorg=$_POST['kdorg']:$oldkdorg=$_POST['oldkdorg'];
		$oldbjr==''?$oldbjr=$_POST['bjr']:$oldbjr=$_POST['oldbjr'];

		$sRicek="select * from ".$dbname.".pabrik_5hargatbs where tanggal='".$oldtgl."' and kodeorg='".$oldkdorg."' and bjr='".$oldbjr."' ";
				//exit("Error:$sRicek");
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		if($rRicek>0)
		{
		$sDel="delete from ".$dbname.".pabrik_5hargatbs
				where tanggal='".$oldtgl."' and kodeorg='".$oldkdorg."' and bjr='".$oldbjr."' ";	    
			//exit("Error:$sDel");
			if(mysql_query($sDel))
			{
			$sDel2="insert into ".$dbname.".pabrik_5hargatbs (`tanggal`,`kodeorg`,`bjr`,`harga`,`updateby`)
		values ('".$tgl."','".$kdorg."','".$bjr."','".$hr."','".$_SESSION['standard']['userid']."')";
		
		

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
		$sDel2="insert into ".$dbname.".pabrik_5hargatbs (`tanggal`,`kodeorg`,`bjr`,`harga`,`updateby`)
		values ('".$tgl."','".$kdorg."','".$bjr."','".$hr."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		}*/
		
		
		
	break;

case'loadData':
		
		
		$tmbh='';
                if($periodesort!='')
                {
                    $tmbh="and tanggal like '%".$periodesort."%' ";
                }
		$tmbh2='';
		
                if($suppsort!='')
                {
                    $tmbh2=" and supplierid='".$suppsort."' ";
                }
		$tmbh3='';
                if($kdorgsort!='')
                {
                    $tmbh3=" where kodeorg like '".$kdorgsort."' ";
                }				
		
		/*$periodesort=$_POST['periodesort'];
			$suppsort=$_POST['suppsort'];
			$kdorgsort=$_POST['kdorgsort'];*/		

		$no=0;
		$str="select * from ".$dbname.".pabrik_5hargatbs ".$tmbh3."  ".$tmbh2." ".$tmbh." order by tanggal desc, supplierid asc,bjr asc";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".($bar1['supplierid']=='INTERNAL'?'Internal/Affiliasi':$namasupp[$bar1['supplierid']])."</td>";
			$tab.="<td align=left>".tanggalnormal($bar1['tanggal'])."</td>";
			//<td>".tanggalnormal($rlvhc['tanggal'])."</td>
			//$tab.="<td align=left>".$optNm[$bar1['karyawanid']]."</td>";
			$tab.="<td align=left>".$namaorg[$bar1['kodeorg']]."</td>";
			//$tab.="<td>".($bar1['bjr']==5?'Lebih dari 5':'Kurang dr 5')."</td>";
			if($bar1['bjr']=='1')
			{
				$tab.="<td align=right>3 - 5</td>";	
			}
			else if($bar1['bjr']=='2')
			{
				$tab.="<td align=right>5 - 7</td>";	
			}
			else if($bar1['bjr']=='3')
			{
				$tab.="<td align=right>7</td>";	
			}
			else if($bar1['bjr']=='4')
			{
				$tab.="<td align=right>< 5</td>";	
			}
			else if($bar1['bjr']=='5')
			{
				$tab.="<td align=right>5 - 8</td>";	
			}
			else if($bar1['bjr']=='6')
			{
				$tab.="<td align=right>> 8</td>";	
			}
			
			if($bar1['kg']=='1')
			{
				$tab.="<td align=center>> 0</td>";	
			}
			else if($bar1['kg']=='2')
			{
				$tab.="<td align=center>< 5000 KG</td>";	
			}
			else if($bar1['kg']=='3')
			{
				$tab.="<td align=center>>= 5000 KG</td>";	
			}
			
			$tab.="<td align=right>".number_format($bar1['harga'])."</td>";
			//$tab.="<td align=right>".$bar1['bjr']."</td>";
			
			//onclick=\"fillField('".$rlvhc['keperluan']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['jenisijin']."','".$rlvhc['persetujuan1']."','".$rlvhc['stpersetujuan1']."','".$rlvhc['darijam']."','".$rlvhc['sampaijam']."');\">
				$tab.="<td align=center>

				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['supplierid']."','".tanggalnormal($bar1['tanggal'])."','".$bar1['kodeorg']."','".$bar1['bjr']."','".$bar1['kg']."');\"></td>";
		echo $tab;//<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".tanggalnormal($bar1['tanggal'])."','".$bar1['kodeorg']."','".$bar1['bjr']."','".$bar1['harga']."');\">
		}

	case 'delete':
		
		$ha="update ".$dbname.".pabrik_5hargatbs SET updateby='".$_SESSION['standard']['userid']."' where supplierid='".$supplier."' and tanggal='".$tgl."' and kodeorg='".$kdorg."' and bjr='".$bjr."' and kg='".$kgX."' ";
		if(mysql_query($ha))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		//exit();
	
		$tab="delete from ".$dbname.".pabrik_5hargatbs where supplierid='".$supplier."' and tanggal='".$tgl."' and kodeorg='".$kdorg."' and bjr='".$bjr."' and kg='".$kgX."' ";
		//exit("Error:$tab");
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
	case 'getperiodesort':
	//exit("Error:MASUK");
		$optpersort="<option value=''>".$_SESSION['lang']['all']."</option>";
		$aper = "SELECT distinct substr(tanggal,1,7) as tanggal FROM ".$dbname.".pabrik_5hargatbs where substr(tanggal,1,7) order by tanggal desc, supplierid asc,bjr asc";
		//exit ("Error:$asup");
		$bper=mysql_query($aper) or die(mysql_error($conn));
		while($cper=mysql_fetch_assoc($bper))
		{
			$optpersort.="<option value='".$cper['tanggal']."'>".$cper['tanggal']."</option>";
		}
		echo $optpersort;
	break;
	
	case 'getsuppsort':
			//exit("Error:xx");
		$optsupsort="<option value=''>".$_SESSION['lang']['all']."</option>";
		$asup = "SELECT distinct supplierid FROM ".$dbname.".pabrik_5hargatbs order by tanggal desc, supplierid asc,bjr asc";
		//exit ("Error:$asup");
		$bsup=mysql_query($asup) or die(mysql_error($conn));
		while($csup=mysql_fetch_assoc($bsup))
		{
			$optsupsort.="<option value='".$csup['supplierid']."'>".$namasupp[$csup['supplierid']]."</option>";
		}
		echo $optsupsort;//exit();
		//exit ("Error:$optsupsort");
	break;
	
	case 'getorgsort':
			//exit("Error:xx");
		$optorgsort="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$aorg = "SELECT distinct kodeorg FROM ".$dbname.".pabrik_5hargatbs order by tanggal desc, supplierid asc,bjr asc";
		//exit ("Error:$aorg");
		$borg=mysql_query($aorg) or die(mysql_error($conn));
		while($corg=mysql_fetch_assoc($borg))
		{
			$optorgsort.="<option value='".$corg['kodeorg']."'>".$namaorg[$corg['kodeorg']]."</option>";
		}
		echo $optorgsort;//exit();
		//exit ("Error:$optsupsort");
	break;
	
	
default:
}
?>