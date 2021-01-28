<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
$method=$_POST['method'];

$noTrans=$_POST['noTrans'];
$noKontrak=$_POST['noKontrak'];
$idCust=$_POST['idCust'];
$noDo=$_POST['noDo'];
$nosipb=$_POST['nosipb'];
$nopol=$_POST['nopol'];
$brtKsng=$_POST['brtKsng'];
$brtBrsih=$_POST['brtBrsih'];
$brtKlr=$_POST['brtKlr'];
$jamMasuk=$_POST['jamMasuk'];
$jamKeluar=$_POST['jamKeluar'];
$nmSpir=$_POST['nmSpir'];
$kdBrg=$_POST['kdBrg'];
$kdpabrik=$_POST['kdpabrik'];
$noSpb=$_POST['noSpb'];
$kdOrg=$_POST['kdOrg'];
$statSortasi=$_POST['statSortasi'];
$tgsSortasi=$_POST['tgsSortasi'];
$thnTnm1=$_POST['thnTnm1'];
$thnTnm2=$_POST['thnTnm2'];
$thnTnm3=$_POST['thnTnm3'];
$jmlhTndn1=$_POST['jmlhTndn1'];
$jmlhTndn2=$_POST['jmlhTndn2'];
$jmlhTndn3=$_POST['jmlhTndn3'];
$statBuah=$_POST['statBuah'];//echo $statBuah;

$tglTrans=tanggalsystem($_POST['tglTrans']);
$usrName=$_SESSION['standard']['username'];
$statTmbngn=$_POST['statTimbangan'];
if($thnTnm1=='')$thnTnm1=0;
if($thnTnm2=='')$thnTnm2=0;
if($thnTnm3=='')$thnTnm3=0;

	switch($method)
	{
		case'insertCpk':
		//echo"warning:masuk".$noTrans;exit();
		if(($noTrans=='')||($noKontrak=='')||($nosipb=='')||($nopol=='')||($nmSpir=='')||($brtKsng=='')||($brtBrsih=='')||($brtKlr=='')||($tglTrans==''))
		{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
		}
		else
		{		
			$sCek="select notransaksi from ".$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
			$qCek=mysql_query($sCek) or die(mysql_error());
			$rCek=mysql_num_rows($qCek);
			if($rCek<1)
			{
				$sIns="insert into ".$dbname.".pabrik_timbangan (notransaksi,tanggal,kodecustomer,kodebarang,jammasuk,beratmasuk,jamkeluar,beratkeluar,	nokendaraan,supir,nosipb, nokontrak,nodo,millcode,beratbersih,username,nospb,timbangonoff ) 
				values ('".$noTrans."','".$tglTrans."','".$idCust."','".$kdBrg."','".$jamMasuk."','".$brtKsng."','".$jamKeluar."','".$brtKlr."','".$nopol."','".$nmSpir."','".$nosipb."','".$noKontrak."','".$noDo."','".$kdpabrik."','".$brtBrsih."','".$usrName."','".$noTrans."','".$statTmbngn."')"; //echo "warning:".$sIns;
				if(mysql_query($sIns))
					echo"";
					else
					echo "DB Error : ".mysql_error($conn);
				
			}
		}
		break;
		case'insertJk':
		if(($noTrans=='')||($nopol=='')||($nmSpir=='')||($idCust==''))
		{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
		}
		if(($brtKsng=='')||($brtBrsih=='')||($brtKlr=='')||($jamMasuk=='')||($jamKeluar=='')||($kdpabrik=='')||($tglTrans==''))
		{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
		}
			
		$sCek="select notransaksi from ".$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".pabrik_timbangan (notransaksi,tanggal,kodecustomer,kodebarang,jammasuk,beratmasuk,jamkeluar,beratkeluar,	nokendaraan,supir,millcode,beratbersih,username,timbangonoff ) 
			values ('".$noTrans."','".$tglTrans."','".$idCust."','".$kdBrg."','".$jamMasuk."','".$brtKsng."','".$jamKeluar."','".$brtKlr."','".$nopol."','".$nmSpir."','".$kdpabrik."','".$brtBrsih."','".$usrName."','".$statTmbngn."')"; //echo "warning:".$sIns;
			if(mysql_query($sIns))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
			
		}
		break;
		case'insertTbs':
		if(($noTrans=='')||($nopol=='')||($nmSpir=='')||($noSpb=='')||($statSortasi==''))
		{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
		}
		if(($brtKsng=='')||($brtBrsih=='')||($brtKlr=='')||($jamMasuk=='')||($jamKeluar=='')||($kdpabrik=='')||($tglTrans==''))
		{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
		}
			
		$sCek="select notransaksi from ".$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{
			$sIns="insert into ".$dbname.".pabrik_timbangan (notransaksi,tanggal,kodebarang,jammasuk,beratmasuk,jamkeluar,beratkeluar,	nokendaraan,supir,millcode,nospb,beratbersih,username,thntm1,thntm2,thntm3,jumlahtandan1,jumlahtandan2,jumlahtandan3,statussortasi,petugassortasi,timbangonoff,kodeorg,intex,kodecustomer ) 
			values ('".$noTrans."','".$tglTrans."','".$kdBrg."','".$jamMasuk."','".$brtKsng."','".$jamKeluar."','".$brtKlr."','".$nopol."','".$nmSpir."','".$kdpabrik."','".$noSpb."','".$brtBrsih."','".$usrName."','".$thnTnm1."','".$thnTnm2."','".$thnTnm3."','".$jmlhTndn1."','".$jmlhTndn2."','".$jmlhTndn3."','".$statSortasi."','".$tgsSortasi."','".$statTmbngn."','".$kdOrg."','".$statBuah."','".$idCust."')"; //echo "warning:".$sIns;
			if(mysql_query($sIns))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
			
		}
		break;
		case'update':
		//exit("Error:$statBuah");
		if(($kdBrg=='40000001')||($kdBrg=='40000005')||($kdBrg=='40000002'))
		{
			if(($noTrans=='')||($noKontrak=='')||($nosipb=='')||($nopol=='')||($nmSpir==''))
			{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
			}
			if(($brtKsng=='')||($brtBrsih=='')||($brtKlr=='')||($jamMasuk=='')||($jamKeluar=='')||($kdpabrik=='')||($tglTrans==''))
			{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
			}
			$sUpd="update ".$dbname.".pabrik_timbangan set tanggal='".$tglTrans."',kodecustomer='".$idCust."',kodebarang='".$kdBrg."',jammasuk='".$jamMasuk."',beratmasuk='".$brtKsng."',jamkeluar='".$jamKeluar."',beratkeluar='".$brtKlr."',nokendaraan='".$nopol."',supir='".$nmSpir."',nosipb='".$nosipb."', nokontrak='".$noKontrak."',nodo='".$noDo."',millcode='".$kdpabrik."',beratbersih='".$brtBrsih."',username='".$usrName."',timbangonoff='".$statTmbngn."' where notransaksi='".$noTrans."'";
                        //exit("error".$sUpd);
			if(mysql_query($sUpd))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
		}
		elseif($kdBrg=='40000004')
		{
			if(($noTrans=='')||($nopol=='')||($nmSpir=='')||($idCust==''))
			{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
			}
			if(($brtKsng=='')||($brtBrsih=='')||($brtKlr=='')||($jamMasuk=='')||($jamKeluar=='')||($kdpabrik=='')||($tglTrans==''))
			{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
			}
			$sUpd="update ".$dbname.".pabrik_timbangan  set tanggal='".$tglTrans."',kodecustomer='".$idCust."',kodebarang='".$kdBrg."',jammasuk='".$jamMasuk."',beratmasuk='".$brtKsng."',jamkeluar='".$jamKeluar."',beratkeluar='".$brtKlr."', nokendaraan='".$nopol."',supir='".$nmSpir."',millcode='".$kdpabrik."',beratbersih='".$brtBrsih."',username='".$usrName."',timbangonoff='".$statTmbngn."' where  notransaksi='".$noTrans."'"; //echo "warning:".$sUpd;
			if(mysql_query($sUpd))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
		}
		elseif($kdBrg=='40000003')
		{
			//exit("Error:$kdOrg");
			//if(($noTrans=='')||($nopol=='')||($nmSpir=='')||($noSpb=='')||($kdOrg=='')||($statSortasi=='')) default
			#indra
			//statBuah
			if($statBuah==0)
			{
				if(($noTrans=='')||($nopol=='')||($nmSpir=='')||($noSpb=='')||($idCust=='')||($statSortasi==''))
				{
					echo"warning:Nomor transaksi / nomor polisi / nama supir / no spb / supplier / status sortasi masih kosong";
					exit();
				}
			}
			else
			{
				if(($noTrans=='')||($nopol=='')||($nmSpir=='')||($noSpb=='')||($statSortasi=='') ||($kdOrg==''))
				{
					echo"warning:Nomor transaksi / nomor polisi / nama supir / no spb / Kebun / status sortasi masih kosong";
					exit();
				}
			}
			if(($brtKsng=='')||($brtBrsih=='')||($brtKlr=='')||($jamMasuk=='')||($jamKeluar=='')||($kdpabrik=='')||($tglTrans==''))
			{
			echo"warning:Tolong dilengkapi pengisian";
			exit();
			}
			$sUpd="update ".$dbname.".pabrik_timbangan set tanggal='".$tglTrans."',kodebarang='".$kdBrg."',jammasuk='".$jamMasuk."',beratmasuk='".$brtKsng."',jamkeluar='".$jamKeluar."',beratkeluar='".$brtKlr."',	nokendaraan='".$nopol."',supir='".$nmSpir."',millcode='".$kdpabrik."',nospb='".$noSpb."',beratbersih='".$brtBrsih."',username='".$usrName."',thntm1='".$thnTnm1."',thntm2='".$thnTnm2."',thntm3='".$thnTnm3."',jumlahtandan1='".$jmlhTndn1."',jumlahtandan2='".$jmlhTndn2."',jumlahtandan3='".$jmlhTndn3."',statussortasi='".$statSortasi."',petugassortasi='".$tgsSortasi."',timbangonoff='".$statTmbngn."',kodeorg='".$kdOrg."',intex='".$statBuah."',kodecustomer='".$idCust."'  where notransaksi='".$noTrans."'"; //echo "warning:".$sUpd;
			if(mysql_query($sUpd))
				echo"";
				else
				echo "DB Error : ".mysql_error($conn);
		}
		break;
		case'delData':
		$sDel="delete from ".$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
		if(mysql_query($sDel))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		
		default:
		break;
	}



?>
