<?php
##ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$tgl=$_POST['tgl'];
$kdorg=$_POST['kdorg'];

if(($proses=='excel')or($proses=='pdf')){
	$tgl=$_GET['tgl'];
	$kdorg=$_GET['kdorg'];
}

$namaAfd=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');



$tgl=tanggalsystem($tgl); $tgl=substr($tgl,0,4).'-'.substr($tgl,4,2).'-'.substr($tgl,6,2);
$tahun=substr($tgl,0,4);
$bulan=substr($tgl,5,2);
$hari=substr($tgl,8,2);

#tanggal awal bulan
$tglmulai=$tahun.'-'.$bulan.'-01';//echo $hari;
$tglawaltahun=$tahun.'-01-01';
//echo $tglawaltahun;exit();

#tgl kemarin
list($year, $month, $day) = explode('-',  $tgl);
$tglkmrn=date("Y-m-d", mktime(0, 0, 0, $month,$day-1,$year));
$tglbesok=date("Y-m-d", mktime(0, 0, 0, $month, $day+1, $year));





if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{
		if($tgl=='--')
		{
			echo"Error: Tanggal tidak boleh kosong"; 
			exit;
		}
}
$namabulan=numToMonth($bulan,$lang='I',$format='long');
$stream="LAPORAN BUDGET VS PRODUKSI<br>TANGGAl : 1 Januari ".$tahun.' sampai dengan '.$hari.' '.$namabulan.' '.$tahun;
if(($proses=='excel'))
{
	$stream.="<table cellspacing='1' border='1' class='sortable'><thead class=rowheader>";
}
else
{
	$stream.="<table cellspacing='1' border='0' class='sortable'><thead class=rowheader>";
}
 $stream.=" <tr>
    <td rowspan=3 align=center bgcolor=#CCCCCC>Kebun Afdeling</td>
    <td colspan=2 rowspan=2 align=center bgcolor=#CCCCCC>Budget</td>
    <td colspan=7 align=center bgcolor=#CCCCCC>Realaisasi</td>
  </tr>
  <tr>
    <td colspan=4 align=center bgcolor=#CCCCCC>Bulan Ini</td>
    <td colspan=3 align=center bgcolor=#CCCCCC>Sampai dengan Bulan Ini</td>
  </tr>
  <tr>
    <td align=center bgcolor=#CCCCCC>Setahun</td>
    <td align=center bgcolor=#CCCCCC>Bulan Ini</td>
    <td align=center bgcolor=#CCCCCC>HI</td>
    <td align=center bgcolor=#CCCCCC>S/D HI</td>
    <td align=center bgcolor=#CCCCCC>+/-</td>
    <td align=center bgcolor=#CCCCCC>Pencapaian (%)</td>
    <td align=center bgcolor=#CCCCCC>S/D HI BI</td>
    <td align=center bgcolor=#CCCCCC>+/-</td>
    <td align=center bgcolor=#CCCCCC>Pencapaian(%)</td>
  </tr></thead>";


$kgbulan='kg'.$bulan;
//echo $kgbulan;
if($kdorg=='')
{
	$str="select sum(jlhkg) as jlhkg,tahunbudget,kodeunit,afdeling from ".$dbname.".bgt_produksi_afdeling where  tahunbudget ='".$tahun."' group by afdeling  ";
}
else
{	
	$str="select sum(jlhkg) as jlhkg,tahunbudget,kodeunit,afdeling from ".$dbname.".bgt_produksi_afdeling where kodeunit='".$kdorg."' and tahunbudget ='".$tahun."' group by afdeling  ";
}
//echo $str;
$res=mysql_query($str) or die (mysql_error($conn));
while($bar=mysql_fetch_assoc($res))
{
	
	#untuk budget bulan ini
	$aBgtBlnIni="select sum($kgbulan) as kgbulan from ".$dbname.".bgt_produksi_kbn_kg_vw where kodeblok like '%".$bar['afdeling']."%' ";
	//echo $aBgtBlnIni;
	$bBgtBlnIni=mysql_query($aBgtBlnIni) or die (mysql_error($conn));
	$cBgtBlnIni=mysql_fetch_assoc($bBgtBlnIni);
	
	#4~untuk realisasi Hari ini
	$aProdBulHi="select sum(hasilkerjakg) as hasilkerjakg from ".$dbname.".kebun_prestasi_vw where tanggal='".$tgl."' and afdeling='".$bar['afdeling']."' group by afdeling  ";
	//echo $aProdBulHi;
	$bProdBulHi=mysql_query($aProdBulHi) or die (mysql_error($conn));
	$cProdBulHi=mysql_fetch_assoc($bProdBulHi);
	
	#5~untuk realisasi sampai dengan Hari ini
	$aProdBulSdHi="select sum(hasilkerjakg) as hasilkerjakg from ".$dbname.".kebun_prestasi_vw where tanggal between '".$tglmulai."' and '".$tgl."' and afdeling='".$bar['afdeling']."' group by afdeling  ";
	//echo $aProdBulSdHi;
	$bProdBulSdHi=mysql_query($aProdBulSdHi) or die (mysql_error($conn));
	$cProdBulSdHi=mysql_fetch_assoc($bProdBulSdHi);

	#6~untuk +/-	
	$plusMinusBlnIni=$cProdBulSdHi['hasilkerjakg']-$cBgtBlnIni['kgbulan'];
	
	#7 %
	$pencapaianBlnIni=$cProdBulSdHi['hasilkerjakg']/$cBgtBlnIni['kgbulan']*100;
	
	#8~untuk realisasi sampai dengan Hari ini (bulan ini) dari tgl 1 januari
	$aProdBulSdBiHi="select sum(hasilkerjakg) as hasilkerjakg from ".$dbname.".kebun_prestasi_vw where tanggal between '".$tglawaltahun."' and '".$tgl."' and afdeling='".$bar['afdeling']."' group by afdeling  ";
	$bProdBulSdBiHi=mysql_query($aProdBulSdBiHi) or die (mysql_error($conn));
	$cProdBulSdBiHi=mysql_fetch_assoc($bProdBulSdBiHi);
	
	#9~untuk +/-	
	$plusMinusBlnIniHariIni=$cProdBulSdBiHi['hasilkerjakg']-$bar['jlhkg'];
	#10~%
	$pencapaianBlnIniHariIni=$cProdBulSdBiHi['hasilkerjakg']/$bar['jlhkg']*100;

	$stream.="
			<tr class=rowcontent>
				<td>".$namaAfd[$bar['afdeling']]."</td>
				<td align=right>".number_format($bar['jlhkg'],2)."</td>
				<td align=right>".number_format($cBgtBlnIni['kgbulan'],2)."</td>
				
				<td align=right>".number_format($cProdBulHi['hasilkerjakg'],2)."</td>
				<td align=right>".number_format($cProdBulSdHi['hasilkerjakg'],2)."</td>
				<td align=right>".number_format($plusMinusBlnIni,2)."</td>
				<td align=right>".number_format($pencapaianBlnIni,2)."</td>
				
				
				<td align=right>".number_format($cProdBulSdBiHi['hasilkerjakg'],2)."</td>
				<td align=right>".number_format($plusMinusBlnIniHariIni,2)."</td>
				<td align=right>".number_format($pencapaianBlnIniHariIni,2)."</td>
			</tr>";		
			
			$total1+=$bar['jlhkg'];
			$total2+=$cBgtBlnIni['kgbulan'];
			$total3+=$cProdBulHi['hasilkerjakg'];
			$total4+=$cProdBulSdHi['hasilkerjakg'];
			$total5+=$plusMinusBlnIni;
			$total6+=$pencapaianBlnIni;
			$total7+=$cProdBulSdBiHi['hasilkerjakg'];
			$total8+=$plusMinusBlnIniHariIni;
			$total9+=$pencapaianBlnIniHariIni;
}

	$persentotal6=$plusMinusBlnIni/$total1;
	$persentotal9=$plusMinusBlnIniHariIni/$total1;

$stream.="<thead>
			<tr>
				<td align=center>Total</td>
				<td align=right>".number_format($total1,2)."</td>
				<td align=center>-</td>
				<td align=center>-</td>
				<td align=center>-</td>
				<td align=right>".number_format($total5,2)."</td>
				<td align=right>".number_format($persentotal6,2)."</td>
				<td align=center>-</td>
				<td align=right>".number_format($total8,2)."</td>
				<td align=right>".number_format($persentotal9,2)."</td>							
			
			</tr></table>";

  

	
#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Harian_Pengolahan_Kelapa_Sawit".$tglSkrg;
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
		break;
	
	
	default:
	break;
}

?>