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
		
			$strx="select * from ".$dbname.".kud_relasi  order by `updatetime` desc";
		//echo"warning:".$strx;exit();
			$stream.="
			<table>
			<tr><td colspan=7 align=center>".$_SESSION['lang']['relasi']."</td></tr>
			<tr><td colspan=3>&nbsp;</td></tr>
			</table>
			<table border=1>
						<tr>
						  <td bgcolor=#DEDEDE align=center>No.</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nosertifikat']."</td>
						  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
						</tr>";
		
		$resx=mysql_query($strx);
		$row=mysql_fetch_row($resx);
		if($row<1)
		{
			$stream.="	<tr class=rowcontent>
			<td colspan=3 align=center>Not Avaliable</td></tr>
			";
		}
		else
		{
			$no=0;
			$resx=mysql_query($strx);
			while($barx=mysql_fetch_assoc($resx))
			{
				$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$barx['idpemilik']."'";
				$qPem=mysql_query($sPem) or die(mysql_error());
				$rPem=mysql_fetch_assoc($qPem);
				$no+=1;
				$stream.="	<tr class=rowcontent>
					<td>".$no."</td>
					<td>".$barx['nosertifikat']."</td>
					<td>".$rPem['nama']."</td>
					</tr>";
			}
		}
	
	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="Relasi";
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