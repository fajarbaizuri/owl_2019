<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zMysql.php');

/*	print"<pre>";
	print_r($_GET);
	print"<pre>";*/
	
//======================================

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
	
			//echo"warning:masuk vvv";
		
			$strx="select * from ".$dbname.".kud_pemilik order by `idpemilik` desc";
		//echo"warning:".$strx;exit();
			$stream.="
			<table>
			<tr><td colspan=7 align=center>".$_SESSION['lang']['daftarPemilik']."</td></tr>
			<tr><td colspan=3>&nbsp;</td></tr>
			</table>
			<table border=1>
						<tr>
						  <td bgcolor=#DEDEDE align=center>No.</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jeniskelamin']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggallahir']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noktp']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['telp']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['desa']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabank']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['atasnama']."</td>	
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['norekeningbank']."</td>	
						</tr>";
		
		$resx=mysql_query($strx);
		$row=mysql_fetch_row($resx);
		if($row<1)
		{
			$stream.="	<tr class=rowcontent>
			<td colspan=10 align=center>Not Avaliable</td></tr>
			";
		}
		else
		{
			$no=0;
			$resx=mysql_query($strx) or die(mysql_error());
			$arrJnsKlmn=array("L"=>$_SESSION['lang']['pria'],"P"=>$_SESSION['lang']['wanita']);
			while($barx=mysql_fetch_assoc($resx))
			{
				$no+=1;
			
				
				$stream.="	<tr class=rowcontent>
					<td>".$no."</td>
					<td>". $barx['nama']."</td>
						<td>". $arrJnsKlmn[$barx['jeniskelamin']]."</td>
						<td>". tanggalnormal($barx['tanggallahir'])."</td>
						<td>". $barx['noktp']."</td>
						<td>". $barx['telp']."</td>
						<td>". $barx['desa']."</td>
						<td>". $barx['namabank']."</td>
						<td>". $barx['atasnama']."</td>
						<td>". $barx['norek']."</td>
					</tr>";
			}
		}
	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="DaftarPemilik";
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}
?>