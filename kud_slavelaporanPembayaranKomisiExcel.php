<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


	$cmpId=$_GET['cmpId'];
	//$periode=substr($_GET['periode'],0,7);
	$periode=$_GET['period'];
	$tahap=$_GET['tahap'];
	//echo"warning:"."-".$periode[1]."-".$periode[0]."-".$_GET['period'];
/*	print"<pre>";
	print_r($_GET);
	print"<pre>";
*/	
//======================================

  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$cmpId."'";
//echo $str; exit();
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
			
			if($cmpId=='0')
			{
				$stream.="
				<table>
				<tr><td colspan=10 align=center>".$_SESSION['lang']['laporanPembayaranKomisi']."</td></tr>
				<tr><td colspan=5>".$_SESSION['lang']['periode'].":".$periode."</td></tr>
				<tr><td colspan=5>".$_SESSION['lang']['date'].":".$_SESSION['empl']['lokasitugas']."</td></tr>
				<tr><td colspan=5>".$_SESSION['lang']['periode'].":".$periode."</td></tr><tr><td colspan=3>&nbsp;</td></tr>				</table>";
			}
			else
			{
				
				$stream.="
				<table>
				<tr><td colspan=10 align=center>".$_SESSION['lang']['laporanPembayaranKomisi']."</td></tr>
				<tr><td colspan=5>".$_SESSION['lang']['kebun'].":".$namapt."</td></tr>
				<tr><td colspan=5>".$_SESSION['lang']['kodeorg'].":".$cmpId."</td></tr>
				<tr><td colspan=5>".$_SESSION['lang']['periode'].":".$periode."</td></tr><tr><td colspan=10>&nbsp;</td></tr>
				</table>";
			}
			$stream.="
			<table border=1>
						<tr>
							<td bgcolor=#DEDEDE align=center>No.</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namapemilik']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nosertifikat']."</td>
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggalbayar']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namapenerima']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dibayaroleh']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['mengetahui']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>	
							<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tahap']."</td>	
						</tr>";
		
		if(($cmpId=='0')&&($tahap=='0'))
		{
				//echo"warning:a";
			$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where a.periode='".$periode."' order by a.tanggalbayar desc";	
		}
			elseif(($cmpId!='0')&&($tahap=='0'))
			{
				//echo"warning:b";
				$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where b.kodeorg='".$cmpId."' and a.periode='".$periode."' order by a.tanggalbayar desc";
			}
			elseif(($cmpId=='0')&&($tahap!='0'))
			{
			//	echo"warning:c";
				$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where tahap='".$tahap."' and a.periode='".$periode."' order by a.tanggalbayar desc";
			}
			elseif(($cmpId!='0')&&($tahap!='0'))
		{
			//echo"warning:d";
			$sCrh="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where b.kodeorg='".$cmpId."' and a.periode='".$periode."' and tahap='".$tahap."' order by a.tanggalbayar desc";
		}
	
	//	echo "warning:__".$sCrh;exit();
		//$resx=mysql_query($sCrh);
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
				$stream.="<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$rCrh['notransaksi']."</td> 
				<td>".$rPem['nama']."</td>
				<td>".$rCrh['nosertifikat']."</td>	 
				<td>".tanggalnormal($rCrh['tanggalbayar'])."</td>
				<td>".$rCrh['namapenerima']."</td>
				<td>".$rCrh['dibayaroleh']."</td>
				<td>".$rCrh['mengetahui']."</td>
				<td>".number_format($rCrh['jumlah'],2)."</td>
				<td>".$rCrh['tahap']."</td>
				</tr>
				";
			}
		}
		else
		{
			$stream.="<tr class='rowcontent'><td colspan=10>Not Found</td></tr>";
		}

	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];	

$nop_="LaporanPembayaranKomisi";
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