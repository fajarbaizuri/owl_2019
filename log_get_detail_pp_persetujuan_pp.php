<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');

?>

<?php
$method=$_POST['method'];
$kolom=$_POST['kolom'];

switch($method){
	case'get_form_approval':
	$sql="select * from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."'";
	$query=mysql_query($sql) or die(mysql_error());
	$rest=mysql_fetch_assoc($query);
	
	//for($i=1;$i<6;$i++)
	//{
     // echo "warning".$rest['persetujuan'.$a];
		if($_SESSION['standard']['userid']==$rest['persetujuan'.$kolom])
		{
			if($rest['persetujuan5']!='')
			{
				echo"<br /><div id=approve>
				<fieldset>
				<legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
				<table cellspacing=1 border=0>
				<tr>
				<td colspan=3>
				Diajukan Ke Purchase Dept. Untuk Proses PO</td></tr>
				<tr>
				<td>".$_SESSION['lang']['note']."</td>
				<td>:</td>
				<td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event)\" /></td>
				</tr>
				<tr><td colspan=3 align=center>
				<button class=mybutton onclick=close_pp() >".$_SESSION['lang']['ok']."</button></td></tr></table>
				</fieldset>
				</div>";
			}
			else
				{	
					echo"<br />
					<div id=test style=display:block>
					<fieldset>
					<legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
					<table cellspacing=1 border=0>
					<tr>
					<td colspan=3>
					Diajukan Untuk Verifikasi Berikutnya :</td>
					</tr>
					<td>".$_SESSION['lang']['namakaryawan']."</td>
					<td>:</td>
					<td valign=top>";
					
					//print_r($_SESSION['standard']);
					
					$optPur='';
					
					
					//if($_SESSION['standard']['username']==hendar)
					//{
					//$klq="select karyawanid,namakaryawan,lokasitugas,bagian from ".$dbname.".`datakaryawan` where karyawanid='0000000242'";
					//}
					//else
					//{
					//	$klq="select karyawanid,namakaryawan,lokasitugas,bagian from ".$dbname.".`datakaryawan` where karyawanid!='".$_SESSION['standard']['userid']."' and tipekaryawan=0 and lokasitugas!='' order by namakaryawan asc";
					//}
				   /*
					switch($kolom){
						case 1:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('17','76','272','283','34','279','275','18','12','13') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('17','76','272','283','34','279','275','18','12','13') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('17','76','272','283','34','279','275','18','12','13') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('17','76','272','283','34','279','275','18','12','13') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('17','76','272','283','34','279','275','18','12','13') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('17','76','272','283','34','279','275','18','276','4','13') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}
							
							//$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.kodejabatan in ('12','279','276','282') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							
							
						break;
						case 2:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12','276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12','276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12','276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12','276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12','276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','5') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}
							//$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.kodejabatan in ('276','5','282','283') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							
						break;
						case 3:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('5') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}
							//$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.kodejabatan in ('4','282') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							
						break;
						case 4:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('5') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}
							
						break;
						case 5:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}
							
						break;
					}
					
					*/
					switch($kolom){
						case 1:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('12') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('13','17','76','279') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276','4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}
							//$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.kodejabatan in ('276','5','282','283') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							
						break;
						case 2:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('13','17','76','279') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('13','17','76','279') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('13','17','76','279') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('13','17','76','279') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('5') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}
							//$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.kodejabatan in ('4','282') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							
						break;
						case 3:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('276') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}
							
						break;
						case 4:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and a.kodejabatan in ('4') and (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}
							
						break;
						case 5:
							if (substr($_POST['nopp'],-4) == "USJE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "TDAE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "TDBE"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "CBGM"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "FBAO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}elseif (substr($_POST['nopp'],-4) == "FBHO"){
								$klq="select a.karyawanid,a.namakaryawan,a.lokasitugas,a.bagian from ".$dbname.".`datakaryawan` a where a.karyawanid!='".$_SESSION['standard']['userid']."' and  (tanggalkeluar>=DATE_FORMAT(NOW(), '%Y-%m-%d') or tanggalkeluar='0000-00-00') order by a.namakaryawan asc limit 0";
							}
							
						break;
					}
					
				/*	
				if($_SESSION['empl']['tipeinduk']=='HOLDING')
					{
					$klq="select * from ".$dbname.".`datakaryawan` where (`lokasitugas`='".substr($_SESSION['empl']['lokasitugas'],0,4)."' OR `kodeorganisasi`='".substr($_SESSION['empl']['lokasitugas'],0,4)."') and karyawanid!='".$_SESSION['standard']['userid']."'"; 
					}
					else
					{
					$klq="select * from ".$dbname.".`datakaryawan` where (`lokasitugas`='".$_SESSION['empl']['lokasitugas']."' OR `kodeorganisasi`='".$_SESSION['empl']['lokasitugas']."') and karyawanid!='".$_SESSION['standard']['userid']."'"; 
					}*/
					//echo $klq;
					$qry=mysql_query($klq) or die(mysql_error());
					while($rst=mysql_fetch_object($qry))
					{
						$sBag="select nama from ".$dbname.".sdm_5departemen where kode='".$rst->bagian."'";
						$qBag=mysql_query($sBag) or die(mysql_error());
						$rBag=mysql_fetch_assoc($qBag);
						$optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan." [".$rst->lokasitugas."]  [".$rBag['nama']."]</option>";
					}
					echo"
					
						<select id=user_id name=user_id  style=\"width:150px;\">
							".$optPur." 
						</select></td></tr>
						<tr>
						<tr>
						<td>".$_SESSION['lang']['note']."</td>
						<td>:</td>
						<td><input type=text id=comment_fr name=comment_fr class=myinputtext onClick='return tanpa_kutip(event)'  style=\"width:150px;\" /></td>
						</tr>
						<td colspan=3 align=center>
						<button class=mybutton onclick=forward_pp() title=\"Diajukan Kembali Untuk Verifikasi\" id=Ajukan >".$_SESSION['lang']['diajukan']."</button>
					
						<button class=mybutton onclick=cancel_pp() title=\"Menutup Form Ini\">".$_SESSION['lang']['cancel']."</button>
						</td></tr></table><br /> 
						<input type=hidden name=method id=method  /> 
						<input type=hidden name=user_id id=user_id value=".$_SESSION['standard']['userid']." />
						<input type=hidden name=nopp id=nopp value=".$_POST['nopp']."  /> 
						</fieldset></div><br />
<br />
						<div id=approve style=display:block>
						<fieldset>
						<legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
						<table cellspacing=1 border=0>
						<tr>
						<td colspan=3>
						Di Setujui dan No PB terdaftar di Verifikasi Purchasing</td></tr>
						<tr>
						<td>".$_SESSION['lang']['note']."</td>
						<td>:</td>
						<td><input type=text id=note name=note class=myinputtext onClick=\"return tanpa_kutip(event)\" style=\"width:150px;\" /></td>
						</tr>
						<tr><td colspan=3 align=center>
						<button class=mybutton onclick=close_pp() title=\"Di Setujui dan No PB terdaftar di Verifikasi Purchasing\"  >".$_SESSION['lang']['kePurchaser']."</button><button class=mybutton onclick=cancel_pp() title=\"Menutup Form Ini\">".$_SESSION['lang']['cancel']."</button></td></tr></table>
						</fieldset>
						</div>
						";
				}
		}
	//}
	
break;

	case 'get_form_rejected':
	echo"<div id=rejected_form>
	<fieldset>
	<legend><input type=text readonly=readonly name=rnopp id=rnopp value=".$_POST['nopp']."  /></legend>
	<table cellspacing=1 border=0>
	<tr>
	<td colspan=3>
	From Penolakan PB </td></tr>
	<tr>
	<td>".$_SESSION['lang']['note']."</td>
	<td>:</td>
	<td><input type=text id=cmnt_tolak name=cmnt_tolak class=myinputtext onClick=\"return tanpa_kutip(event)\" /></td>
	</tr>
	<tr><td colspan=3 align=center>
	<button class=mybutton onclick=\"rejected_pp_proses()\" >".$_SESSION['lang']['ditolak']."</button>
	</td></tr></table>
	</fieldset>
	</div>";
	break;
	case 'get_form_rejected_some':
	$nopp=$_POST['nopp'];
	$sql="select * from ".$dbname.".log_prapodt where `nopp`='".$nopp."'";
	$query=mysql_query($sql) or die(mysql_error());
	
	echo"
	<fieldset>
	<legend><input type=text id=rnopp name=rnopp value=".$nopp." readonly=readonly /></legend>
	<div style=overflow:auto;width=850px;height:350px;>
	<table cellspacing=1 border=0 class=sortable>
	<thead class=rowheader>
	<tr>
	<td>No.</td>
	<td>".$_SESSION['lang']['kodebarang']."</td>
	<td>".$_SESSION['lang']['namabarang']."</td>
	<td>".$_SESSION['lang']['satuan']."</td>
	<td>".$_SESSION['lang']['kodeanggaran']."</td>
	<td>".$_SESSION['lang']['jmlhDiminta']."</td>
	<td>".$_SESSION['lang']['tanggalSdt']."</td>
	<td>".$_SESSION['lang']['keterangan']."</td>
	<td>".$_SESSION['lang']['alasanDtolak']."</td>
	<td colspan=2>Action</td>
	</tr>
	</thead>

	<tbody id=reject_some class=rowcontent>
	
	";
	while($res=mysql_fetch_assoc($query)){
	$no+=1;
	$sql2="select * from ".$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
	$query2=mysql_query($sql2) or die(mysql_error());
	$res2=mysql_fetch_assoc($query2);
	if($res['status']==3)
	{
		$dis="disabled=disabled";
	}
	else
	{
		$dis="";
	}
	echo"<tr>
	<td>".$no."</td>
	<td id=kd_brg_".$no.">".$res['kodebarang']."</td>
	<td>".$res2['namabarang']."</td>
	<td>".$res2['satuan']."</td>
	<td id=kd_angrn_".$no.">".$res['kd_anggran']."</td>
	<td id=jmlh_".$no.">".$res['jumlah']."</td>
	<td id=tgl_".$no.">".$res['tgl_sdt']."</td>
	<td id=ket_".$no.">".$res['keterangan']."</td>
	<td><input type=text id=alsnDtolak_".$no." name=alsnDtolak_".$no." class=myinputtext style=width:100px /></td>
	<td><button class=mybutton onclick=\"rejected_some('".$nopp."','".$no."','".$kolom."')\" ".$dis." >".$_SESSION['lang']['ditolak']."</button></td>
	</tr>";
	}
	echo"</tbody><tfoot><tr><td colspan=10 align=center><button class=mybutton onclick=\"rejected_some_done('".$nopp."','".$kolom."')\" >".$_SESSION['lang']['done']."</button></td></tr></tfoot></table></div></fieldset><input type=hidden id=user_id name=user_id value='".$_SESSION['standard']['userid']."'>";
	break;

	case 'rejected_some_done':
	$user_id=$_POST['user_id'];
	for($i=1;$i<6;$i++)
	{
		$sql="select * from ".$dbname.".log_prapoht where nopp='".$_POST['nopp']."' and persetujuan".$i."='".$user_id."' ";
		if($query2=mysql_query($sql2))
		{
			while($res=mysql_fetch_assoc($query))
			{
				for($i=1;$i<6;$i++)
				{	
					if($res['hasilpersetujuan'.$i]=='')
					{
						$sql2="update ".$dbname.".log_prapoht set hasilpersetujuan".$i."='1'";
					}
				}
			}
	
			break;
		}
		else
		{
			echo $sql2;exit();
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	}
	break;
	case 'cari_pp':
	if((isset($_POST['txtSearch']))||(isset($_POST['tglCari'])))
	{
		$txt_search=$_POST['txtSearch'];
		$txt_tgl=tanggalsystem($_POST['tglCari']);
		$txt_tgl_a=substr($txt_tgl,0,4);
		$txt_tgl_b=substr($txt_tgl,4,2);
		$txt_tgl_c=substr($txt_tgl,6,2);
		$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
	}
		
	if($_POST['txtSearch']!='')
	{
		$where="nopp LIKE  '%".$txt_search."%' and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."')";
	}
	elseif($_POST['tglCari']!='')
	{
		$where="tanggal LIKE '%".$txt_tgl."%' and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."')";
	}
	elseif(($txt_tgl!='')&&($txt_search!=''))
	{
		$where="nopp LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%' and (persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."')";
	}//
	elseif(($_POST['txtSearch']=='')&&($_POST['tglCari']==''))
	{
		
		$where="(persetujuan1='".$_SESSION['standard']['userid']."' or persetujuan2='".$_SESSION['standard']['userid']."' or persetujuan3='".$_SESSION['standard']['userid']."' or persetujuan4='".$_SESSION['standard']['userid']."' or persetujuan5='".$_SESSION['standard']['userid']."') ORDER BY `tanggal` DESC";
						 
	}//
		$str="SELECT * FROM ".$dbname.".log_prapoht where ".$where." ORDER BY tanggal desc ";
		//$sql="SELECT count(*) as jmlhrow FROM ".$dbname.".log_prapoht where  ".$where." ORDER BY nopp DESC";
	//echo "warning :".$str;exit();
/*	$query=mysql_query($sql) or die(mysql_error());
			while($jsl=mysql_fetch_object($query)){
			$jlhbrs= $jsl->jmlhrow;
			}
*/	 
	  $res=mysql_query($str) or die(mysql_error($conn));
		$rCek=mysql_num_rows($res);
		if($rCek>0)
		{
		
			while($bar=mysql_fetch_assoc($res))
			{
			$koderorg=substr($bar['nopp'],15,4);
			$spr="select namaorganisasi from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
			$rep=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rep);
			$no+=1;
			echo"<tr class=rowcontent id='tr_".$no."'>
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['nopp']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td>".$bas->namaorganisasi."</td>
				  <td align=center>
                                  <img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_prapoht','".$bar['nopp']."','','log_slave_print_log_pp',event);\"> &nbsp
                                  <img src=images/zoom.png class=resicon height='30' title='Preview' onclick=\"previewDetail('".$bar['nopp']."',event);\">    
                                  </td>";      
					if($bar['close']==2)
					{
						$accept=0;
						for($i=1;$i<6;$i++)
						{
							if($bar['hasilpersetujuan'.$i]=='3')
							{
								$accept=3;
								break;
							}
							elseif($bar['hasilpersetujuan'.$i]=='1')
							{
								$accept=1;
								
							}
						}
						if($accept==3) {
							echo"<td colspan=3>".$_SESSION['lang']['ditolak']."</td>";
						} elseif($accept==1) {
							echo"<td colspan=3>".$_SESSION['lang']['disetujui']."</td>";
						}
					}
					elseif($bar['close']<2)
					{
						
						$PBDS="";
						for($a=1;$a<6;$a++)
						{
							if($bar['persetujuan'.$a]!='')
							{
								if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&$bar['hasilpersetujuan'.$a]==0){
									echo"
								<td><a href=# onclick=\"get_data_pp('".$bar['nopp']."','".$a."')\">".$_SESSION['lang']['approve']."</a></td>
								<td><a href=# onclick=rejected_pp('".$bar['nopp']."','".$a."') >".$_SESSION['lang']['ditolak']."</a></td>
								<td><a href=# onclick=\"rejected_some_proses('".$bar['nopp']."','".$a."')\" >
								".$_SESSION['lang']['ditolak_some']."</a></td>";
									$PBDS=$bar['persetujuan'.$a];
									goto A;
								}
								/*
								else{
									echo"<td colspan=3>&nbsp;</td>";
									
								}
								*/
								/*
								if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&(($bar['hasilpersetujuan'.$a]!='')
								and $bar['hasilpersetujuan'.$a]!=0))
								{
									echo"<td colspan=3>&nbsp;</td>";
								}
								elseif(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]=='' 
								or $bar['hasilpersetujuan'.$a]==0))
								{
								echo"
								<td><a href=# onclick=\"get_data_pp('".$bar['nopp']."','".$a."')\">".$_SESSION['lang']['approve']."</a></td>
								<td><a href=# onclick=rejected_pp('".$bar['nopp']."','".$a."') >".$_SESSION['lang']['ditolak']."</a></td>
								<td><a href=# onclick=\"rejected_some_proses('".$bar['nopp']."','".$a."')\" >
								".$_SESSION['lang']['ditolak_some']."</a></td>";
								}
								*/
							}
						}
						/*
						for($a=1;$a<6;$a++)
						{
							if($bar['persetujuan'.$a]!='')
							{
								if(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&(($bar['hasilpersetujuan'.$a]!='')
								and $bar['hasilpersetujuan'.$a]!=0))
								{
									echo"<td colspan=3>&nbsp;</td>";
								}
								elseif(($bar['persetujuan'.$a]==$_SESSION['standard']['userid'])&&($bar['hasilpersetujuan'.$a]=='' 
								or $bar['hasilpersetujuan'.$a]==0))
								{
								echo"
								<td><a href=# onclick=\"get_data_pp('".$bar['nopp']."','".$a."')\">".$_SESSION['lang']['approve']."</a></td>
								<td><a href=# onclick=rejected_pp('".$bar['nopp']."','".$a."') >".$_SESSION['lang']['ditolak']."</a></td>
								<td><a href=# onclick=\"rejected_some_proses('".$bar['nopp']."','".$a."')\" >
								".$_SESSION['lang']['ditolak_some']."</a></td>";
								}
							}
						}
						*/
					}
					/*
				 for($i=1;$i<6;$i++)
				 {
				 	//echo $bar['hasilpersetujuan'.$i];
					if(($bar['persetujuan'.$i]!='')&&($bar['persetujuan'.$i]!=0))
					{	
						$kr=$bar['persetujuan'.$i];
						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);
						echo"<td><a href=# onclick=\"cek_status_pp('".$bar['hasilpersetujuan'.$i]."')\">".$yrs['namakaryawan']."</a></td>";
					}
					else
					{
						echo"<td>&nbsp;</td>";
					}
				 }			
						*/
						A:
					if($PBDS!=$_SESSION['standard']['userid'] && $bar['close']!=2){
						echo"<td colspan=3>&nbsp;</td>";
					}
					
				 for($i=1;$i<6;$i++)
				 {
				 	//echo $bar['hasilpersetujuan'.$i];
					if(($bar['persetujuan'.$i]!='')&&($bar['persetujuan'.$i]!=0))
					{	
						$kr=$bar['persetujuan'.$i];
						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);
						echo"<td><a href=# onclick=\"cek_status_pp('".$bar['hasilpersetujuan'.$i]."')\">".$yrs['namakaryawan']."</a></td>";
					}
					else
					{
						echo"<td>&nbsp;</td>";
					}
				 }		
				 
				 echo"</tr>";
		}	 	 	
		  	
	  }
	  else
	  {
		echo"<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>";  
	  }
	break;
	
	default:
	break;
	}
?>	








