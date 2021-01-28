<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$cmpId=$_POST['cmpId'];
$period=$_POST['period'];
$tahap=$_POST['tahap'];


	switch($proses)
	{
		case'GetData':
		echo"
		<table cellspacing=1 border=0>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>".$_SESSION['lang']['notransaksi']."</td> 
		<td>".$_SESSION['lang']['namapemilik']."</td>
		<td>".$_SESSION['lang']['nosertifikat']."</td>	 
		<td>".$_SESSION['lang']['tanggalbayar']."</td>
		<td>".$_SESSION['lang']['namapenerima']."</td>
		<td>".$_SESSION['lang']['dibayaroleh']."</td>
		<td>".$_SESSION['lang']['mengetahui']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>
		<td>".$_SESSION['lang']['tahap']."</td>
		</tr>
		</thead>
		";
		if(($cmpId=='0')&&($tahap=='0'))
		{
				//echo"warning:a";
			$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where a.periode='".$period."' order by a.tanggalbayar desc";	
		}
			elseif(($cmpId!='0')&&($tahap=='0'))
			{
				//echo"warning:b";
				$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where b.kodeorg='".$cmpId."' and a.periode='".$period."' order by a.tanggalbayar desc";
			}
			elseif(($cmpId=='0')&&($tahap!='0'))
			{
			//	echo"warning:c";
				$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where tahap='".$tahap."' and a.periode='".$period."' order by a.tanggalbayar desc";
			}
			elseif(($cmpId!='0')&&($tahap!='0'))
		{
			//echo"warning:d";
			$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where b.kodeorg='".$cmpId."' and a.periode='".$period."' and tahap='".$tahap."' order by a.tanggalbayar desc";
		}
	//	echo "warning".$sCrh;exit();
		$qCrh=mysql_query($sCrh) or die(mysql_error());
		$rCek=mysql_num_rows($qCrh);
		if($rCek>0)
		{
			while($rCrh=mysql_fetch_assoc($qCrh))
			{
				$no+=1;
				$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$rCrh['idpemilik']."'";
				$qPem=mysql_query($sPem) or die(mysql_error());
				$rPem=mysql_fetch_assoc($qPem);
				echo"<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$rCrh['notransaksi']."</td> 
				<td>".$rPem['nama']."</td>
				<td>".$rCrh['nosertifikat']."</td>	 
				<td>".tanggalnormal($rCrh['tanggalbayar'])."</td>
				<td>".$rCrh['namapenerima']."</td>
				<td>".$rCrh['dibayaroleh']."</td>
				<td>".$rCrh['mengetahui']."</td>
				<td>".$rCrh['jumlah']."</td>
				<td>".$rCrh['tahap']."</td>
				</tr>
				";
			}
		}
		else
		{
			echo"<tr class='rowcontent'><td colspan=10>Not Found</td></tr>";
		}
		echo"</table>";
		break;
		case'getTahap':
		$sTahap="select tahap from ".$dbname.".kud_sertifikat where kodeorg='".$cmpId."'"; // "warning:".$sTahap;exit();
		$qTahap=mysql_query($sTahap) or die(mysql_error());
		$optThp="<option value='0'>All</option>";
		while($rTahap=mysql_fetch_assoc($qTahap))
		{
			$optThp.="<option value=".$rTahap['tahap'].">".$rTahap['tahap']."</option>";
		}
		echo $optThp;
		break;
		default:
		break;
	}

?>