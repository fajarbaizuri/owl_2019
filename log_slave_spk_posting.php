<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
//$postingSpk=$param['postingSpk'];
$proses=$param['proses'];
$noTrans=$param['noTrans'];
switch($proses)
{
	case'postingSpk':
	$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='".$app."'");
	$tmpPost = fetchData($qPosting);
	$postJabatan = $tmpPost[0]['jabatan'];
	
	$sCek="select kodeorg,notransaksi,divisi,posting from ".$dbname.".log_spkht where notransaksi='".$noTrans."' and posting=0
               and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_num_rows($qCek);
	if($rCek>0)
	{
		$sUp="update  ".$dbname.".log_spkht set posting='1' where notransaksi='".$noTrans."'";
		//echo "warning".$sUp;exit();
		if(!mysql_query($sUp))
		{
			echo "DB Error : ".mysql_error($conn);
			exit();
		}
		else
		{
			//$sUpBaspk="update ".$dbname.".log_baspk set posting='1' where notransaksi='".$noTrans."'";
			//if(!mysql_query($sUpBaspk))
			//{
			//	$sUp="update  ".$dbname.".log_spkht set posting='0' where notransaksi='".$noTrans."'";
			//	mysql_query($sUp) or die(mysql_error());
			//	echo "DB Error : ".mysql_error($conn);
			//	exit();
			//}
		}
	}
	else
	{
		echo"warning:Sudah Terposting";
		exit();
	}
	break;
	default:
	break;
}
?>