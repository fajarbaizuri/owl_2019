<?php
##ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$tgl=$_POST['tgl'];

if(($proses=='excel')or($proses=='pdf')){
	$kdorg=$_GET['kdorg'];
	$tgl=$_GET['tgl'];

}

$optnmor=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjnvhc=makeOption($dbname, 'vhc_5jenisvhc','jenisvhc,namajenisvhc');
$optnmbar=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optnamacostumer=makeOption($dbname,'log_5supplier','supplierid,namasupplier');

$optnamatangki=makeOption($dbname,'pabrik_5tangki','kodetangki,keterangan');


$tgl=tanggalsystem($tgl); $tgl=substr($tgl,0,4).'-'.substr($tgl,4,2).'-'.substr($tgl,6,2);
$tahun=substr($tgl,0,4);
$bulan=substr($tgl,5,2);
$hari=substr($tgl,8,2);

#tanggal awal bulan
$tglmulai=$tahun.'-'.$bulan.'-01';//echo $hari;

#tgl kemarin
list($year, $month, $day) = explode('-',  $tgl);
$tglkmrn=date("Y-m-d", mktime(0, 0, 0, $month,$day-1,$year));
$tglbesok=date("Y-m-d", mktime(0, 0, 0, $month, $day+1, $year));
//echo $tglbesok;exit();


//exit("Error:$kdunit");

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

		if($kdorg=='')
		{
			echo"Error: Organisasi tidak boleh kosong"; 
			exit;
		}
}

$stream="LAPORAN HARIAN PENGOLAHAN KELAPA SAWIT";
$stream.=" A. TBS DITERIMA";

  //$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
	//else 
	$stream.="<table cellspacing='1' border='1' class='sortable'>";
	
	$stream.="<thead class=rowheader>
	 <tr>
		<td align=center style=width:40px>No</td>
		<td align=center style=width:400px>Supplier</td>
		<td align=center style=width:200px>Netto</td>
		<td align=center style=width:200px>Normal</td>			
	</tr></thead>
	<tbody>";


###PT FBB
	$str="SELECT a.kodeorg,SUM(a.beratbersih) AS netto,SUM(a.beratbersih-a.kgpotsortasi) AS normal,b.namaorganisasi,b.induk
			FROM ".$dbname.".pabrik_timbangan a LEFT JOIN ".$dbname.".organisasi b ON a.kodeorg=b.kodeorganisasi
			WHERE date(a.tanggal)='".$tgl."' and a.millcode='".$kdorg."' and kodebarang='40000003' and induk='FBB' GROUP BY a.kodeorg";
			//echo $str;
	$res=mysql_query($str);					
	$no=0;
	$ttl=0;
$stream.="<tr class=rowcontent><td align=center>01</td><td colspan=3><i><u><b>Kebun Sendiri</b></u></i></td></tr>";	
$stream.="<tr class=rowcontent><td align=center>a</td><td colspan=3><b>PT FBB</b></td></tr>";	
	while($bar=mysql_fetch_assoc($res))
	{
		$no+=1;
		$stream.="
		<tr class=rowcontent>
			<td align=center align=10></td>
			
			<td align=left>".$bar['namaorganisasi']."</td>
			<td align=right>".number_format($bar['netto'])." Kg</td>
			<td align=right>".number_format($bar['normal'])." Kg</td>	
		</tr>";		
		$totalnettofbb+=$bar['netto'];
		$totalnormalfbb+=$bar['normal'];
	};
	$stream.="<tr class=rowcontent>
				<td></td>
				<td align=center>Jumlah FBB</td>
				<td align=right>".number_format($totalnettofbb)." Kg</td>
				<td align=right>".number_format($totalnormalfbb)." Kg</td>
			</tr>";
	
##pt USJ
$str="SELECT a.kodeorg,SUM(a.beratbersih) AS netto,SUM(a.beratbersih-a.kgpotsortasi) AS normal,b.namaorganisasi,b.induk
			FROM ".$dbname.".pabrik_timbangan a LEFT JOIN ".$dbname.".organisasi b ON a.kodeorg=b.kodeorganisasi
			WHERE date(a.tanggal)='".$tgl."' and a.millcode='".$kdorg."' and kodebarang='40000003' and induk='USJ' GROUP BY a.kodeorg";
			//echo $str;
	$res=mysql_query($str);					
	$no=0;
	$ttl=0;

$stream.="<tr class=rowcontent><td align=center>b</td><td colspan=3><b>PT USJ</b></td></tr>";	
	while($bar=mysql_fetch_assoc($res))
	{
		$no+=1;
		$stream.="
		<tr class=rowcontent>
			<td align=center align=10></td>
			<td align=left>".$bar['namaorganisasi']."</td>
			<td align=right>".number_format($bar['netto'])." Kg</td>
			<td align=right>".number_format($bar['normal'])." Kg</td>	
		</tr>";	
		$totalnettousj+=$bar['netto'];
		$totalnormalusj+=$bar['normal'];
	};
	$stream.="<tr class=rowcontent>
				<td></td>
				<td align=center>Jumalh USJ</td>
				<td align=right>".number_format($totalnettousj)." Kg</td>
				<td align=right>".number_format($totalnormalusj)." Kg</td>
			</tr>";
			
$totalnettofbg=$totalnettofbb+$totalnettousj;
$totalnormalfbg=$totalnormalfbb+$totalnormalusj;
			
$stream.="<tr class=rowcontent>
			<td></td>
			<td align=center><b>JUMLAH FBG (KEBUN SENDIRI)</b></td>
			<td align=right><b>".number_format($totalnettofbg)." Kg </b></td>
			<td align=right><b>".number_format($totalnormalfbg)." Kg</b></td>
		</tr>";			
		
		
##pihak ke 3

$stream.="<tr class=rowcontent><td align=center>02</td><td colspan=3><i><u><b>Pihak Ketiga</b></u></i></td></tr>";	
	
$str="SELECT a.kodecustomer,c.namasupplier,SUM(a.beratbersih) AS netto,SUM(a.beratbersih-a.kgpotsortasi) AS normal,c.namasupplier 
	 FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5supplier c on a.kodecustomer=c.supplierid or a.kodecustomer=c.kodetimbangan
	 WHERE date(a.tanggal)='".$tgl."' and a.millcode='".$kdorg."' and a.kodebarang='40000003' and a.kodeorg='' GROUP BY a.kodeorg,a.kodecustomer";
			//echo $str;
	$res=mysql_query($str);					
	$no=0;
	$ttl=0;
	while($bar=mysql_fetch_assoc($res))
	{
		$no+=1;
		$stream.="
		<tr class=rowcontent>
			<td align=center align=10></td>
			<td align=left>".$bar['namasupplier']."</td>
			<td align=right>".number_format($bar['netto'])." Kg</td>
			<td align=right>".number_format($bar['normal'])." Kg</td>	
		</tr>";	
		$totalnettopihak3+=$bar['netto'];
		$totalnormalpihak3+=$bar['normal'];
	};
	$stream.="<tr class=rowcontent>
				<td></td>
				<td align=center><b>JUMLAH DARI PIHAK KETIGA<b></td>
				<td align=right><b>".number_format($totalnettopihak3)." Kg</b></td>
				<td align=right><b>".number_format($totalnormalpihak3)." Kg</b></td>
			</tr>";
			
##tutup pihak ke 3
	
##total keseluruhan
$grandtotalnetto=$totalnettofbb+$totalnettousj+$totalnettopihak3;
$grandtotalnormal=$totalnormalfbb+$totalnormalusj+$totalnormalpihak3;

$stream.="<tr class=rowcontent><td colspan=2 align=center><b>JUMLAH TOTAL</b></td><td align=right><b>".number_format($grandtotalnetto)." Kg</b></td>
<td align=right><b>".number_format($grandtotalnormal)." Kg<b></td></tr></table>";	
##tutup total keseluruhan	


##############################################################################################################################################
###PENGOLAHAN
##############################################################################################################################################

$stream.="<br />B. PENGOLAHAN";
 //if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
	//else 
	$stream.="<table cellspacing='1' border='1' class='sortable'>";
	$stream.="<thead class=rowheader>
	 <tr>
		<td align=center style=width:40px>no</td>
		<td align=center style=width:400px></td>
		<td align=center style=width:200px>ini</td>
		<td align=center style=width:200px>s/d ini</td>			
	</tr></thead>
	<tbody>";
	




##1~ Restan TBS kemarin
$arestanTbsKemarin="select sisahariini from ".$dbname.".pabrik_produksi where tanggal='".$tglkmrn."'";
$brestanTbsKemarin=mysql_query($arestanTbsKemarin) or die (mysql_query($conn));
$crestanTbsKemarin=mysql_fetch_assoc($brestanTbsKemarin);
	$restanTbsKemarin=$crestanTbsKemarin['sisahariini'];
$stream.="<tr class=rowcontent>
			<td align=center>1</td>
			<td>Restan TBS Diterima Tanggal ".tanggalnormal($tglkmrn)."</td>
			<td align=right>".number_format($restanTbsKemarin)." Kg</td>
			<td align=right></td></tr>	";		

##2~ TBS yang di terima olah hi ini,
##3~ TBS yang di olah	
$aTbsHr="select * from ".$dbname.".pabrik_produksi where tanggal='".$tgl."' and kodeorg='".$kdorg."' ";
$bTbsHr=mysql_query($aTbsHr) or die (mysql_error($conn));
$cTbsHr=mysql_fetch_assoc($bTbsHr);
	$tbsTerimaHr=$cTbsHr['tbsmasuk'];
	$tbsOlahHr=$cTbsHr['tbsdiolah'];
	$oerHr=$cTbsHr['oer'];
	$kernelHr=$cTbsHr['oerpk'];
	#rendeman
	$albCpo=$cTbsHr['ffa'];
	$kadarairCpo=$cTbsHr['kadarair'];
	$kadarkotoranCpo=$cTbsHr['kadarkotoran'];//echo $kadarkotoranCpo;exit();
	#rendeman kerenel
	$albKernel=$cTbsHr['ffapk'];
	$kadarairKernel=$cTbsHr['kadarairpk'];
	$kadarkotoranKernel=$cTbsHr['kadarkotoranpk'];//echo $kadarkotoranKernel;exit();
$aTbsSd="select sum(tbsmasuk) as tbsmasuk, sum(tbsdiolah) as tbsdiolah, sum(oer) as oer,sum(oerpk) as oerpk from ".$dbname.".pabrik_produksi where tanggal between '".$tglmulai."' and '".$tgl."' and kodeorg='".$kdorg."' ";
$bTbsSd=mysql_query($aTbsSd) or die (mysql_error($conn));
$cTbsSd=mysql_fetch_assoc($bTbsSd);
	$tbsTerimaSd=$cTbsSd['tbsmasuk'];
	$tbsOlahSd=$cTbsSd['tbsdiolah'];
	$oerSd=$cTbsSd['oer'];
	$kernelSd=$cTbsSd['oerpk'];	
$stream.="<tr class=rowcontent>
			<td align=center>2</td>
			<td>TBS Diterima Tanggal ".tanggalnormal($tgl)."</td>
			<td align=right>".number_format($tbsTerimaHr)." Kg</td>
			<td align=right>".number_format($tbsTerimaSd)." Kg</td></tr>	";	
$stream.="<tr class=rowcontent>
			<td align=center>3</td>
			<td>TBS yang diolah</td>
			<td align=right>".number_format($tbsOlahHr)." Kg</td>
			<td align=right>".number_format($tbsOlahSd)." Kg</td></tr>	";				
	
##4~ restan TBS hari ini 
$restanTbsHrIni=$restanTbsKemarin-$tbsOlahHr;
$restanTbsSd=$restanTbsKemarin+$tbsTerimaHr-$tbsOlahHr;
$stream.="<tr class=rowcontent>
			<td align=center>4</td>
			<td>Restan TBS hari ini ".tanggalnormal($tgl)."</td>
			<td align=right>".number_format(abs($restanTbsHrIni))." Kg</td>
			<td align=right>".number_format($restanTbsSd)." Kg</td></tr>	";	//abs
		
			
##5~jam olah
$aJamOlahHr="SELECT sum(jamdinasbruto) - sum(jamstagnasi) as jamolah FROM ".$dbname.".`pabrik_pengolahan` where tanggal='".$tgl."' and kodeorg='".$kdorg."'";
$bJamOlahHr=mysql_query($aJamOlahHr) or die (mysql_error($conn));
$cJamOlahHr=mysql_fetch_assoc($bJamOlahHr);
	$JamOlahHr=$cJamOlahHr['jamolah'];//echo $JamOlahHr;exit();
$aJamOlahSd="SELECT sum(jamdinasbruto) - sum(jamstagnasi) as jamolah FROM ".$dbname.".`pabrik_pengolahan` where tanggal between '".$tglmulai."' and '".$tgl."'
			 and kodeorg='".$kdorg."'";
$bJamOlahSd=mysql_query($aJamOlahSd) or die (mysql_error($conn));
$cJamOlahSd=mysql_fetch_assoc($bJamOlahSd);
	$JamOlahSd=$cJamOlahSd['jamolah'];//echo $JamOlahSd;exit();
$stream.="<tr class=rowcontent>
			<td align=center>5</td>
			<td>Jam Olah</td>
			<td align=right>".number_format($JamOlahHr,2)." Jam</td>
			<td align=right>".number_format($JamOlahSd,2)."	Jam</td></tr>";	
#mulai selesai			
$stream.="<tr class=rowcontent><td></td><td>Mulai - Selesai</td><td>08.00 WIB</td><td>08.00 WIB</td></tr>";		//indra masih harus tanya mba vira


#6~kapasitas olah
$kapasitasOlahHr=$tbsOlahHr/$JamOlahHr;//kapasitas olah = tbs olah / jam olah
$kapasitasOlahSd=$tbsOlahSd/$JamOlahSd;
$stream.="<tr class=rowcontent>
			<td align=center>6</td>
			<td>Kapasitas Olah</td>
			<td align=right>".number_format($kapasitasOlahHr)." Kg/Jam</td>
			<td align=right>".number_format($kapasitasOlahSd)." Kg/Jam</td>	
		  </tr>";
		  				
#7~CPO dihasilkan		  
$stream.="<tr class=rowcontent><td align=center>7</td><td>CPO dihasilkan (menurut flow meter)</td><td align=right>Kg</td><td align=right>Kg</td></tr>";	
$stream.="<tr class=rowcontent>
			<td align=center></td>
			<td>CPO dihasilkan (sesuai sounding)</td>
			<td align=right>".number_format($oerHr)." Kg</td>
			<td align=right>".number_format($oerSd)." Kg</td>
		  </tr>";	

#8~rendemen	
$rendemanHr=$oerHr/$tbsOlahHr*100;
$rendemanSd=$oerSd/$tbsOlahSd*100;
$stream.="<tr class=rowcontent>
			<td align=center>8</td>
			<td>Rendemen</td>
			<td align=right>".number_format($rendemanHr,2)." %</td>
			<td align=right>".number_format($rendemanSd,2)." %</td>
		  </tr>";//rumus cpo dihasilkan / tbs diolah =>rendeman produksi	  
$stream.="<tr class=rowcontent><td></td><td>ALB</td><td align=right>".$albCpo." %</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Air</td><td align=right>".$kadarairCpo." %</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Kotoran</td><td align=right>".$kadarkotoranCpo." %</td><td align=right>%</td></tr>";


#9~pengiriman CPO
$aCpoKirimHr="select sum(beratbersih) as beratbersih from ".$dbname.".pabrik_timbangan where millcode = '".$kdorg."' and tanggal='".$tgl."%' and kodebarang = '40000001'";
$bCpoKirimHr=mysql_query($aCpoKirimHr) or die (mysql_error($conn));
$cCpoKirimHr=mysql_fetch_assoc($bCpoKirimHr);
	$CpoKirimHr=$cCpoKirimHr['beratbersih'];
$aCpoKirimSd="select sum(beratbersih) as beratbersih from ".$dbname.".pabrik_timbangan where millcode = '".$kdorg."' and tanggal between '".$tglmulai."' and '".$tgl."' and kodebarang = '40000001'";
$bCpoKirimSd=mysql_query($aCpoKirimSd) or die (mysql_error($conn));
$cCpoKirimSd=mysql_fetch_assoc($bCpoKirimSd);
	$CpoKirimSd=$cCpoKirimSd['beratbersih'];
$stream.="<tr class=rowcontent>
			<td align=center>9</td>
			<td>Pengiriman CPO</td>
			<td align=right>".number_format($CpoKirimHr)." Kg</td>
			<td align=right>".number_format($CpoKirimSd)." Kg</td>
		  </tr>";	

#10~stok CPO
$aStokCpo="select sum(kuantitas) as cpo from ".$dbname.".pabrik_masukkeluartangki where kodeorg = '".$kdorg."' and tanggal = '".$tgl."' ";
$bStokCpo=mysql_query($aStokCpo) or die (mysql_error($conn));
$cStokCpo=mysql_fetch_assoc($bStokCpo);
	$StokCpoHr=$cStokCpo['cpo'];
	$StokCpoSd=$StokCpoHr+$oerHr-$CpoKirimHr;
	//$KernelKirimHr+$oerHr;
	
$stream.="<tr class=rowcontent>
			<td align=center>10</td>
			<td>Stok CPO</td>
			<td bgcolor=#99FF00 align=right>".number_format($StokCpoHr)." Kg</td>
			<td align=right>".number_format($StokCpoSd)." Kg</td>
		  </tr>";	  
$stream.="<tr class=rowcontent><td></td><td>ALB</td><td align=right>%</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Air</td><td align=right>%</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Kotoran</td><td align=right>%</td><td align=right>%</td></tr>";		  



#11~kernel dihasilkan
$stream.="<tr class=rowcontent>
			<td align=center>11</td>
			<td>Kernel dihasilkan</td>
			<td align=right>".number_format($kernelHr)." Kg</td>
			<td align=right>".number_format($kernelSd)." KG</td>
		  </tr>";

#12~rendeman kernel
$rendemanKernelHr=$kernelHr/$tbsOlahHr*100;
$rendemanKernelSd=$kernelSd/$tbsOlahSd*100;
$stream.="<tr class=rowcontent>
			<td align=center>12</td>
			<td>Rendeman Kernel</td>
			<td align=right>".number_format($rendemanKernelHr,2)." %</td>
			<td align=right>".number_format($rendemanKernelSd,2)." %</td>
		  </tr>";	
$stream.="<tr class=rowcontent><td></td><td>ALB</td><td align=right>".$albKernel." %</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Air</td><td align=right>".$kadarairKernel." %</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Kotoran</td><td align=right>".$kadarkotoranKernel." %</td><td align=right>%</td></tr>";		  


#13~pengiriman kernel
$aKernelKirimHr="select sum(beratbersih) as beratbersih from ".$dbname.".pabrik_timbangan where millcode = '".$kdorg."' and tanggal='".$tgl."%' and kodebarang = '40000002'";
$bKernelKirimHr=mysql_query($aKernelKirimHr) or die (mysql_error($conn));
$cKernelKirimHr=mysql_fetch_assoc($bKernelKirimHr);
	$KernelKirimHr=$cKernelKirimHr['beratbersih'];
$aKernelKirimSd="select sum(beratbersih) as beratbersih from ".$dbname.".pabrik_timbangan where millcode = '".$kdorg."' and tanggal between '".$tglmulai."' and '".$tgl."' and kodebarang = '40000002'";
//echo $aKernelKirimSd;
$bKernelKirimSd=mysql_query($aKernelKirimSd) or die (mysql_error($conn));
$cKernelKirimSd=mysql_fetch_assoc($bKernelKirimSd);
	$KernelKirimSd=$cKernelKirimSd['beratbersih'];	 
$stream.="<tr class=rowcontent>
			<td align=center>13</td>
			<td>Pengiriman Kernel</td>
			<td align=right>".number_format($KernelKirimHr)." Kg</td>
			<td align=right>".number_format($KernelKirimSd)." Kg</td>
		  </tr>";	


#14~stok kernel
$aStokKernel="select sum(kernelquantity) as kernel from ".$dbname.".pabrik_masukkeluartangki where kodeorg = '".$kdorg."' and tanggal = '".$tgl."' ";
$bStokKernel=mysql_query($aStokKernel) or die(mysql_error($conn));
$cStokKernel=mysql_fetch_assoc($bStokKernel);
	$stokKernelHr=$cStokKernel['kernel'];
	$stokKernelSd=$stokKernelHr+$kernelHr-$KernelKirimHr;
	
$stream.="<tr class=rowcontent>
			<td align=center>14</td>
			<td>Stok Kernel</td>
			<td align=right bgcolor=#99FF00>".number_format($stokKernelHr)." Kg</td>
			<td align=right>".number_format($stokKernelSd)." Kg</td>
		  </tr>";	  
$stream.="<tr class=rowcontent><td></td><td>ALB</td><td align=right>%</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Air</td><td align=right>%</td><td align=right>%</td></tr>";
$stream.="<tr class=rowcontent><td></td><td>Kadar Kotoran</td><td align=right>%</td><td align=right>%</td></tr>";

##15~abu janjang dihasilkan
/*
##16~Pengiriman Abu Janjang

$stream.="<tr class=rowcontent>
			<td align=center></td>
			<td></td>
			<td align=right>".number_format($a)."</td>
			<td align=right>".number_format($a)."</td>
		  </tr>";
		  
$stream.="<tr class=rowcontent>
			<td align=center></td>
			<td></td>
			<td align=right>".number_format($a)."</td>
			<td align=right>".number_format($a)."</td>
		  </tr>";	*/
$stream.="</table>";


$stream.="<br />POSISI MINYAK DI TANGKI TIMBUN<br />";
$stream.="<table>";
##posisi minyak di tangki timbun (perhitungan +1 harinya karna sounding di lakukan besoknya)
$aStokTangki="select * from ".$dbname.".pabrik_masukkeluartangki where kodeorg = '".$kdorg."' and tanggal = '".$tglbesok."' ";
$bStokTangki=mysql_query($aStokTangki) or die (mysql_error($conn));
while($cStokTangki=mysql_fetch_assoc($bStokTangki))
{
	$no+=1;
	$stream.="
			
			<tr>
				<td align=center style=width:40px >".$no."</td>
				<td style=width:400px>".$optnamatangki[$cStokTangki['kodetangki']]." </td>
				<td style=width:200px align=right>".number_format($cStokTangki['kuantitas'])." Kg</td>	
				<td style=width:200px align=center>ALB = ".number_format($cStokTangki['cpoffa'],2)." %</td>			
			</tr>";
			$totalcpotangki+=$cStokTangki['kuantitas'];

}

$stream.="<tr><td colspan=3><hr></hr></td></tr>";
$stream.="<tr><td colspan=2 align=center><b>Jumlah</td><td align=right><b>".number_format($totalcpotangki)." Kg</td></tr>";
$stream.="</table>";


	
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