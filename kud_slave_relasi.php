<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$noSertifikat=$_POST['noSertifikat'];
$idPem=$_POST['idPem'];
$usrId=$_SESSION['standard']['userid'];
$status=$_POST['stat'];
$ket=$_POST['ket'];
	switch($proses)
	{
		
	case'loadData':
	$arrStat=array($_SESSION['lang']['tidakaktif'],$_SESSION['lang']['aktif']);
	OPEN_BOX();
			 echo"<fieldset>
	<legend>".$_SESSION['lang']['list']."</legend>";
				echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kud_relasi','','','kud_slave_relasiPdf',event);\">&nbsp;<img onclick=dataKeExcel(event,'kud_slave_relasiExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
				<table cellspacing=1 border=0 class=sortable>
			<thead>
	<tr class=rowheader>
	<td>No</td>
	<td>".$_SESSION['lang']['nosertifikat']."</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['keterangan']."</td>
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
			
			$sql2="select count(*) as jmlhrow from ".$dbname.".kud_relasi where status='1' order by `updatetime` desc";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$slvhc="select * from ".$dbname.".kud_relasi where status='1' order by `updatetime` desc limit ".$offset.",".$limit."";
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
						<td>". $res['nosertifikat']."</td>
						<td>". $rPem['nama']."</td>
				
						<td>". $res['keterangan']."</td>
						";
						echo"
						<td><!--<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['nosertifikat']."','".$res['idpemilik']."');\">-->
						<img src=images/application/application_delete.png class=resicon  title='Non Active' onclick=\"delData('". $res['nosertifikat']."','". $res['idpemilik']."');\" >
						</td>
						</tr>";
						}
						echo"
						<tr><td colspan=7 align=center>
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
		if(($noSertifikat=='')||($idPem==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		/*$sCek="select nosertifikat,idpemilik from ".$dbname.".kud_relasi where nosertifikat='".$noSertifikat."' or idpemilik='".$idPem."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek<1)
		{*/
			
			$sIns="insert into ".$dbname.".kud_relasi (nosertifikat,idpemilik, updateby,status,keterangan) values 
			('".$noSertifikat."','".$idPem."','".$usrId."','".$status."','".$ket."')";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
	/*	}
		else
		{
			echo"warning:This data already input";
			exit();
		}*/
		break;
		//getData
		case'getData':
		$sGet="select * from ".$dbname.".kud_sertifikat where nosertifikat='".$noSertifikat."'";
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		echo $rGet['nosertifikat']."###".$rGet['kodeorg']."###".tanggalnormal($rGet['tanggalsertifikat'])."###".$rGet['luas']."###".$rGet['tahap'];
		break;
		
		case'update':
		if(($noSertifikat=='')||($kdOrg=='')||($tglSertifikat=='')||($Luas=='')||($taHap==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sUp="update ".$dbname.".kud_sertifikat set kodeorg='".$kdOrg."', tanggalsertifikat='".$tglSertifikat."', luas='".$Luas."', tahap='".$taHap."', updateby='".$usrId."'  where nosertifikat='".$noSertifikat."'";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sUp))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		case'delData':
	//	$sDel="delete from ".$dbname.".kud_relasi where nosertifikat='".$noSertifikat."' and idpemilik='".$idPem."'";
		$sDel="update  ".$dbname.".kud_relasi set status='0' where nosertifikat='".$noSertifikat."' and idpemilik='".$idPem."'";
		//echo "warning:".$sDel;exit();
		if(mysql_query($sDel))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		//cari transaksi
			case 'cari_transaksi':
		 OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['result']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0 class='sortable'>
		<thead>
<tr class=rowheader>
<td>No</td>
<td>".$_SESSION['lang']['nosertifikat']."</td>
<td>".$_SESSION['lang']['kodeorg']."</td>
<td>".$_SESSION['lang']['tglSertifikat']."</td>
<td>".$_SESSION['lang']['luas']."</td>
<td>".$_SESSION['lang']['tahap']."</td>
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
				$where=" nosertifikat LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggalsertifikat LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" nosertifikat LIKE '%".$txt_search."%' and tanggalsertifikat LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		
				$strx="select * from ".$dbname.".kud_sertifikat where ".$where." order by updatetime desc";
				
		//echo "warning:".$strx; exit();
		
		
			if($qry=mysql_query($strx))
			{
				$numrows=mysql_num_rows($qry);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=7 align=center>Not Found</td></tr>";
					echo"</tbody></table></div></fieldset>";
				}
				else
				{
					while($res=mysql_fetch_assoc($qry))
					{
						$no+=1;
			
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>". $res['nosertifikat']."</td>
					<td>". $res['kodeorg']."</td>
					<td>". tanggalnormal($res['tanggalsertifikat'])."</td>
					<td>". $res['luas']."</td>
					<td>". $res['tahap']."</td>";
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['nosertifikat']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['nosertifikat']."');\" >
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
		default:
		break;
	}
		?>