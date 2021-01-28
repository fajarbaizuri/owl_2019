<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$nm=$_POST['nm'];
$ktp=$_POST['ktp'];
$tglLahir=tanggalsystem($_POST['tglLahir']);
$almat=$_POST['almat'];
$tlp=$_POST['tlp'];
$idPemilik=$_POST['idPemilik'];
$desa=$_POST['desa'];
$nmBank=$_POST['nmBank'];
$atsNm=$_POST['atsNm'];
$noRek=$_POST['noRek'];
$jnsKlmn=$_POST['jnsKlmn'];

$usrId=$_SESSION['standard']['userid'];
	switch($proses)
	{
		
	case'loadData':
	
	OPEN_BOX();
			 echo"<fieldset>
	<legend>".$_SESSION['lang']['list']."</legend>";
				echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kud_pemilik','','','kud_slave_daftarPemilikPdf',event);\">&nbsp;<img onclick=dataKeExcel(event,'kud_slave_daftarPemilikExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
				<table cellspacing=1 border=0 class=sortable>
			<thead>
	<tr class=rowheader>
	<td>No</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['jeniskelamin']."</td>
	<td>".$_SESSION['lang']['tanggallahir']."</td>
	<td>".$_SESSION['lang']['noktp']."</td>
	<td>".$_SESSION['lang']['telp']."</td>
	<td>".$_SESSION['lang']['desa']."</td>
	<td>".$_SESSION['lang']['namabank']."</td>
	<td>".$_SESSION['lang']['atasnama']."</td>
	<td>".$_SESSION['lang']['norekeningbank']."</td>
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
			$arrJnsKlmn=array("L"=>$_SESSION['lang']['pria'],"P"=>$_SESSION['lang']['wanita']);
			$sql2="select count(*) as jmlhrow from ".$dbname.".kud_pemilik  order by `idpemilik` desc";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
			$slvhc="select * from ".$dbname.".kud_pemilik  order by `idpemilik` desc limit ".$offset.",".$limit."";
			$qlvhc=mysql_query($slvhc) or die(mysql_error());
			while($res=mysql_fetch_assoc($qlvhc))
			{
				
				$no+=1;
			echo"
						<tr class=rowcontent>
						<td>".$no."</td>
						<td>". $res['nama']."</td>
						<td>". $arrJnsKlmn[$res['jeniskelamin']]."</td>
						<td>". tanggalnormal($res['tanggallahir'])."</td>
						<td>". $res['noktp']."</td>
						<td>". $res['telp']."</td>
						<td>". $res['desa']."</td>
						<td>". $res['namabank']."</td>
						<td>". $res['atasnama']."</td><td>". $res['norek']."</td>";
						echo"
						<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['idpemilik']."');\">
						<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['idpemilik']."');\" >
						</td>
						</tr>";
						}
						echo"
						<tr><td colspan=12 align=center>
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
		if(($nm=='')||($tglLahir=='')||($almat=='')||($atsNm=='')||($nmBank=='')||($noRek=='')||($ktp=='')||($desa==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
				
			$sIns="insert into ".$dbname.".kud_pemilik (nama, jeniskelamin, tanggallahir, noktp, idpemilik, alamat, telp, desa, namabank, atasnama, norek) values 
			('".$nm."','".$jnsKlmn."','".$tglLahir."','".$ktp."','','".$almat."','".$tlp."','".$desa."','".$nmBank."','".$atsNm."','".$noRek."')";
			//echo"warning:".$sIns;exit();
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
			break;
		//getData
		case'getData':
		$sGet="select * from ".$dbname.".kud_pemilik  where idpemilik='".$idPemilik."'";
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		echo $rGet['nama']."###".$rGet['jeniskelamin']."###".tanggalnormal($rGet['tanggallahir'])."###".$rGet['noktp']."###".$rGet['idpemilik']."###".$rGet['alamat']."###".$rGet['telp']."###".$rGet['desa']."###".$rGet['namabank']."###".$rGet['atasnama']."###".$rGet['norek'];
		break;
		
		case'update':
		if(($nm=='')||($tglLahir=='')||($almat=='')||($atsNm=='')||($nmBank=='')||($noRek=='')||($ktp=='')||($desa==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		$sUp="update ".$dbname.".kud_pemilik set nama='".$nm."', jeniskelamin='".$jnsKlmn."', tanggallahir='".$tglLahir."', noktp='".$ktp."', alamat='".$almat."', telp='".$tlp."', desa='".$desa."', namabank='".$nmBank."', atasnama='".$atsNm."' , norek='".$noRek."' where idpemilik='".$idPemilik."'";
			//echo"warning:".$sUp;exit();
			if(mysql_query($sUp))
			echo"alert('Edit Succed');";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		case'delData':
		$sDel="delete from ".$dbname.".kud_pemilik where idpemilik='".$idPemilik."'";
		if(mysql_query($sDel))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		break;
		//cari transaksi
			case 'cariPemilik':
		 OPEN_BOX();
		 echo"<fieldset>
<legend>".$_SESSION['lang']['result']."</legend>";
			echo"<div style=\"width:600px; height:450px; overflow:auto;\">
			<table cellspacing=1 border=0 class='sortable'>
		<thead>
<tr class=rowheader>
<td>No</td>
<td>".$_SESSION['lang']['nama']."</td>
<td>".$_SESSION['lang']['jeniskelamin']."</td>
<td>".$_SESSION['lang']['tanggallahir']."</td>
<td>".$_SESSION['lang']['noktp']."</td>
<td>".$_SESSION['lang']['telp']."</td>
<td>".$_SESSION['lang']['desa']."</td>
<td>".$_SESSION['lang']['namabank']."</td>
<td>".$_SESSION['lang']['atasnama']."</td>
<td>".$_SESSION['lang']['norekeningbank']."</td>
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
				$where=" nama LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.=" tanggallahir LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.=" nama LIKE '%".$txt_search."%' and tanggallahir LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		
				$strx="select * from ".$dbname.".kud_pemilik where ".$where." order by idpemilik  desc";
				
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
			
					echo"
					<tr class=rowcontent>
					<td>".$no."</td>
					<td>". $res['nama']."</td>
					<td>". $res['jeniskelamin']."</td>
					<td>". tanggalnormal($res['tanggallahir'])."</td>
					<td>". $res['noktp']."</td>
					<td>". $res['telp']."</td>
					<td>". $res['desa']."</td>
					<td>". $res['namabank']."</td>
					<td>". $res['atasnama']."</td><td>". $res['norek']."</td>";
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['idpemilik']."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['idpemilik']."');\" >
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
		case'CekId':
		$sCek="select idpemilik from ".$dbname.".kud_pemilik  order by idpemilik desc limit 0,1";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		$idPem=intval($rCek['idpemilik'])+1;
		echo $idPem;
		break;
		default:
		break;
	}
		?>