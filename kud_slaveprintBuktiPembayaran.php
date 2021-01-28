<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$idPemilik=$_POST['idPemilik'];
$period=$_POST['period'];



	switch($proses)
	{
		case'loadData':
	OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['printBuktiPembayaran']).'</b>'); //1 O

			 echo"<fieldset>
	<legend>".$_SESSION['lang']['list']."</legend>";
				echo"<table cellspacing=1 border=0 class=sortable>
			<thead>
	<tr class=rowheader>
	<td>No</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['periode']."</td>
	<td>".$_SESSION['lang']['grnd_total']."</td>
	<td>Action</td>
	</tr>
	</thead>
	<tbody>
	";
			$limit=10;
			$page=0;
			if(isset($_POST['page']))
			{
			$page=$_POST['page'];
			if($page<0)
			$page=0;
			}
			$offset=$page*$limit;
			
			$sql2="select count(*) as jmlhrow from ".$dbname.".kud_pembayaran  group by idpemilik,periode order by`periode` desc ";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$slvhc="select sum(jumlah) as jmlh,idpemilik,periode,nosertifikat from ".$dbname.".kud_pembayaran where statuspembayaran='1' group by idpemilik,periode order by`periode` desc limit ".$offset.",".$limit."";
			$qlvhc=mysql_query($slvhc) or die(mysql_error());
			while($res=mysql_fetch_assoc($qlvhc))
			{
				$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$res['idpemilik']."'";
				$qPem=mysql_query($sPem) or die(mysql_error());
				$rPem=mysql_fetch_assoc($qPem);
				$no+=1;
			echo"
						<tr class=rowcontent>
						<td>".$no."</td>
						<td>". $rPem['nama']."</td>
						<td>". $res['periode']."</td>
						<td>". number_format($res['jmlh'])."</td>
						";
						echo"
						<td>
<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kud_pembayaran','".$res['idpemilik'].",".$res['periode'].",".$res['nosertifikat']."','','kud_slave_printBuktiPembayaranPdf',event);\">
						</td>
						</tr>";
						}
						echo"
						<tr><td colspan=10 align=center>
						".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
						<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
						<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
						</td>
						</tr>";
						echo"</table></fieldset>";
						CLOSE_BOX();
		break;
		case'getTahap':
		$sTahap="select tahap from ".$dbname.".kud_sertifikat where kodeorg='".$cmpId."'"; // "warning:".$sTahap;exit();
		$qTahap=mysql_query($sTahap) or die(mysql_error());
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