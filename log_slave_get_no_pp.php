<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
	
if(isset($_POST['kdorg'])){
	$kodeorg=trim($_POST['kdorg']);
	if($_POST['kdorg']=='')
	{
		echo "warning:Kode Organisasi Inconsistent";
		exit();
	}
	else
	{
		$tgl=  date('Ymd');
		$bln = substr($tgl,4,2);
		$thn = substr($tgl,0,4);
		
		
		//	if($_SESSION['org']['tipeorganisasi']=='HOLDING')
//			{
//				//$kodept['induk']=substr($_SESSION['empl']['lokasitugas'],0,4);
//				$kodept['induk']=$kodeorg;
//			}
//			else
//			{
//				$kodept['induk']=substr($_SESSION['empl']['lokasitugas'],0,4);
//			}
			//$nopp="/".date('Y')."/PB/".$kodeorg;
			$nopp="/".$bln."/".$thn."/PB/".$kodeorg;
			
			//$ql="select `nopp` from ".$dbname.".`log_prapoht` where nopp like '%".$nopp."%' order by `nopp` desc limit 0,1";
			$ql="select COUNT(*) AS jum from ".$dbname.".`log_prapoht` where nopp like '%".$nopp."%'";
			$qr=mysql_query($ql) or die(mysql_error());
			$rp=mysql_fetch_object($qr);
			//$awal=substr($rp->nopp,0,3);
			$awal=intval($rp->jum);
			//$cekbln=substr($rp->nopp,4,2);
			//$cekthn=substr($rp->nopp,7,4);
			//if(($bln!=$cekbln)&&($thn!=$cekthn))
			
			//if($thn!=$cekthn)
			//{
			//echo $awal; exit();
				//$awal=1;
			//}
			//else
			//{
			//	$awal++;
			//}
			
			ANGELWHITE:
			$awal++;
			if (strlen($awal) >= 3){
				$counter=addZero($awal,strlen($awal));
			}else{
				$counter=addZero($awal,3);
			}
			$nopp=$counter."/".$bln."/".$thn."/PB/".$kodeorg;
			
			
			
			$cl="select COUNT(*) AS jum from ".$dbname.".`log_prapoht` where nopp like '%".$nopp."%'";
			$cr=mysql_query($cl) or die(mysql_error());
			$cp=mysql_fetch_object($cr);
			
			$tot=intval($cp->jum);
			
		
			if ($tot == 0){
				echo $nopp;
			}else
			{
			goto ANGELWHITE;
			}
			
			
			
			
		}
	
	}
		
?>