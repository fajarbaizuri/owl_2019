<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];



	switch($proses)
	{
		case'loadData':
	OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['daftarKepemilikan']).'</b>'); //1 O

			 echo"<fieldset>
	<legend>".$_SESSION['lang']['list']."</legend>";
				echo"<table cellspacing=1 border=0 class=sortable>
			<thead>
	<tr class=rowheader>
	<td>No</td>
	<td>".$_SESSION['lang']['nosertifikat']."</td>
	<td>".$_SESSION['lang']['tglSertifikat']."</td>
	<td>".$_SESSION['lang']['namapemilik']."</td>
	<td>".$_SESSION['lang']['kodeorg']."</td>
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
			
			//$sql2="select count(*) as jmlhrow from ".$dbname.".kud_relasi a inner join ".$dbname.".kud_sertifikat b  on a.nosertifikat=b.nosertifikat  order by b.tanggalsertifikat desc";
			$sql2="select count(*) as jmlhrow from ".$dbname.".kud_relasi ";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
		//	$slvhc="select a.*,b.tanggalsertifikat,b.kodeorg  from ".$dbname.".kud_relasi a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat order by b.tanggalsertifikat desc limit ".$offset.",".$limit."";
		$slvhc="select * from ".$dbname.".kud_relasi limit ".$offset.",".$limit."";
			$qlvhc=mysql_query($slvhc) or die(mysql_error());
			while($res=mysql_fetch_assoc($qlvhc))
			{
				$no+=1;
				
				$sCer="select * from ".$dbname.".kud_sertifikat where nosertifikat='".$res['nosertifikat']."'";
				$qCer=mysql_query($sCer) or die(mysql_error());
				$rCer=mysql_fetch_assoc($qCer);
				$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$res['idpemilik']."'";
				$qPem=mysql_query($sPem) or die(mysql_error());
				$rPem=mysql_fetch_assoc($qPem);
			
					echo"
						<tr class=rowcontent>
						<td>".$no."</td>";
						if($prefSertifikat==$res['nosertifikat'])
						{
							echo"<td></td>";
						}
						else
						{
							echo"<td>".$res['nosertifikat']."</td>";
						}
						
						echo"<td>". tanggalnormal($rCer['tanggalsertifikat'])."</td>
						<td>". $rPem['nama']."</td>
						<td>". $rCer['kodeorg']."</td>
					";

						echo"
						<td>
<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kud_relasi','".$res['idpemilik'].",".$res['nosertifikat']."','','kud_slave_daftarKepemilikanPdf',event);\">
						</td>
						</tr>";
						$prefSertifikat=$res['nosertifikat'];
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