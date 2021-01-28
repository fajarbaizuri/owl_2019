<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$noTrans=$_POST['noTrans'];
$idPemilik=$_POST['idPemilik'];
$tglPembyrn=tanggalsystem($_POST['tglPembyrn']);
$idCer=$_POST['idCer'];
$nmPenerima=$_POST['nmPenerima'];
$dByrOlh=$_POST['dByrOlh'];
$mngthi=$_POST['mngthi'];
$period=$_POST['period'];
$usrId=$_SESSION['standard']['userid'];
$jMlh=$_POST['jMlh'];
$noCer=$_POST['noCer'];
	switch($proses)
	{
		
	case'loadData':
	
	OPEN_BOX();
			 echo"<fieldset>
	<legend>".$_SESSION['lang']['list']."</legend>";
				echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kud_pembayaran','','','kud_slave_pembayaranKomisiPdf',event);\">&nbsp;<img onclick=dataKeExcel(event,'kud_slave_pembayaranKomisiExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
				<table cellspacing=1 border=0 class=sortable>
			<thead>
	<tr class=rowheader>
	<td>No</td>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['nosertifikat']."</td>
	<td>".$_SESSION['lang']['tanggalbayar']."</td>
	<td>".$_SESSION['lang']['namapenerima']."</td>
	<td>".$_SESSION['lang']['dibayaroleh']."</td>
	<td>".$_SESSION['lang']['mengetahui']."</td>
	<td>".$_SESSION['lang']['jumlah']."</td>
	<td>".$_SESSION['lang']['status']."</td>
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
			
			$sql2="select count(*) as jmlhrow from ".$dbname.".kud_pembayaran  order by `tanggalbayar` desc";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$slvhc="select * from ".$dbname.".kud_pembayaran  order by `tanggalbayar` desc limit ".$offset.",".$limit."";
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
						<td>". $res['notransaksi']."</td>
						<td>". $rPem['nama']."</td>
						<td>". $res['nosertifikat']."</td>
						<td>". tanggalnormal($res['tanggalbayar'])."</td>
						<td>". $res['namapenerima']."</td>
						<td>". $res['dibayaroleh']."</td>
						<td>". $res['mengetahui']."</td>
						<td>". number_format($res['jumlah'])."</td>
						";
						if($res['statuspembayaran']==0)
						{
							echo"<td align=center><input type='checkbox' id=chkStat name=chkStat onclick=UbhStat('". $res['notransaksi']."')></td>";
						}
						else if($res['statuspembayaran']==1)
						{
							echo"<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kud_pembayaran','".$res['idpemilik'].",".$res['periode'].",".$res['nosertifikat']."','','kud_slave_printBuktiPembayaranPdf2',event);\"></td>";
						}
						echo"
						<td ><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">
						<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."');\" >
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
		case'insert':
		//echo"warning:masuk";
		if(($noTrans=='')||($nmPenerima=='')||($tglPembyrn=='')||($mngthi=='')||($idCer=='')||($idPemilik=='')||($dByrOlh==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
				
			$sIns="insert into ".$dbname.".kud_pembayaran (notransaksi, nosertifikat, idpemilik, periode, tanggalbayar, namapenerima, dibayaroleh, mengetahui, updateoleh, jumlah) values 
			('".$noTrans."','".$idCer."','".$idPemilik."','".$period."','".$tglPembyrn."','".$nmPenerima."','".$dByrOlh."','".$mngthi."','".$usrId."','".$jMlh."')";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
			break;
		//getData
		case'getData':
		$sGet="select * from ".$dbname.".kud_pembayaran  where notransaksi='".$noTrans."'";
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		echo $rGet['notransaksi']."###".$rGet['nosertifikat']."###".tanggalnormal($rGet['tanggalbayar'])."###".$rGet['idpemilik']."###".$rGet['periode']."###".$rGet['namapenerima']."###".$rGet['dibayaroleh']."###".$rGet['mengetahui']."###".$rGet['jumlah'];
		break;
		
		case'update':
		if(($noTrans=='')||($nmPenerima=='')||($tglPembyrn=='')||($mngthi=='')||($idCer=='')||($idPemilik=='')||($dByrOlh==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sUp="update ".$dbname.".kud_pembayaran set tanggalbayar='".$tglPembyrn."', namapenerima='".$nmPenerima."', dibayaroleh='".$dByrOlh."', mengetahui='".$mngthi."', updateoleh='".$usrId."', jumlah='".$jMlh."' where notransaksi='".$noTrans."'";
			//echo"warning:".$sUp;exit();
			if(mysql_query($sUp))
			echo"alert('Edit Succed');";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		case'delData':
		$sDel="delete from ".$dbname.".kud_pembayaran where notransaksi='".$noTrans."'";
		if(mysql_query($sDel))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		//cari transaksi
			case 'cariTransaksi':
		 OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['result']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0 class='sortable'>
		<thead>
<tr class=rowheader>
<td>No</td>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['nosertifikat']."</td>
	<td>".$_SESSION['lang']['tanggalbayar']."</td>
	<td>".$_SESSION['lang']['namapenerima']."</td>
	<td>".$_SESSION['lang']['dibayaroleh']."</td>
	<td>".$_SESSION['lang']['mengetahui']."</td>
	<td>".$_SESSION['lang']['jumlah']."</td>
<td>Action</td>
</tr>
</thead>
<tbody>
";
		if(isset($_POST['txtSearch']))
		{
			$txt_search=$_POST['txtSearch'];
			$txt_tgl=tanggalsystem($_POST['txtTgl']);
			$txt_tgl_a=substr($txt_tgl,0,4);
			$txt_tgl_b=substr($txt_tgl,4,2);
			$txt_tgl_c=substr($txt_tgl,6,2);
			$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
		}
		else
		{
			$txt_search='';
			$txt_tgl='';			
		}
			if($txt_search!='')
			{
				$where=" 	notransaksi LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggalbayar LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" notransaksi LIKE '%".$txt_search."%' and tanggalbayar LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		
				$strx="select * from ".$dbname.".kud_pembayaran where ".$where." order by tanggalbayar  desc";
				
		//echo "warning:".$strx; exit();
		
		
			if($qry=mysql_query($strx))
			{
				$numrows=mysql_num_rows($qry);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=11 align=center>Not Found</td></tr>";
					echo"</tbody></table></div></fieldset>";
				}
				else
				{
					while($res=mysql_fetch_assoc($qry))
					{
						$no+=1;
						$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$res['idpemilik']."'";
						$qPem=mysql_query($sPem) or die(mysql_error());
						$rPem=mysql_fetch_assoc($qPem);
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
						<td>". $res['notransaksi']."</td>
						<td>". $rPem['nama']."</td>
						<td>". $res['nosertifikat']."</td>
						<td>". tanggalnormal($res['tanggalbayar'])."</td>
						<td>". $res['namapenerima']."</td>
						<td>". $res['dibayaroleh']."</td>
						<td>". $res['mengetahui']."</td>
						<td>". number_format($res['jumlah'])."</td>";
				
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."');\" >
				</td>
					</tr>";
					}

					echo"</tbody></table></div></fieldset>";
					
				}
			 }	
			else
			{
				echo "Gagal,".(mysql_error($conn));
			}	
			CLOSE_BOX();
		break;
		
		
		case'getCertificate':
		$sCer="select * from ".$dbname.".kud_relasi where idpemilik='".$idPemilik."' and status='1'";
		$qCer=mysql_query($sCer) or die(mysql_error());
		while($rCer=mysql_fetch_assoc($qCer))
		{
			$optCer.="<option value=".$rCer['nosertifikat']." ".($rCer['nosertifikat']==$noCer?'selected':'').">".$rCer['nosertifikat']."</option>";
		}
		echo $optCer;
		break;
		
		case'getStat':
		$sUpd="update ".$dbname.".kud_pembayaran set statuspembayaran='1' where notransaksi='".$noTrans."'";
		$qUpd=mysql_query($sUpd) or die(mysql_error($conn));
		break;
		
		default:
		break;
	}
		?>