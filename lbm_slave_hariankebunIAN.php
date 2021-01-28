<?php
##ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
mysql_select_db($dbname,$conn) or die ("gagal");
$proses=$_GET['proses'];
$tgl=$_POST['tgl'];
$kdorg=$_POST['kdorg'];
$kgt=$_POST['kgt'];



if(($proses=='excel')or($proses=='pdf')){
	$tgl=$_GET['tgl'];
	$kdorg=$_GET['kdorg'];
	$kgt=$_GET['kgt'];
}

$namaAfd=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$namaKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$namaSat=makeOption($dbname,'setup_kegiatan','kodekegiatan,satuan');



$tgl=tanggalsystem($tgl); $tgl=substr($tgl,0,4).'-'.substr($tgl,4,2).'-'.substr($tgl,6,2);
$tahun=substr($tgl,0,4);
$bulan=substr($tgl,5,2);
$hari=substr($tgl,8,2);

#tanggal awal bulan
$tglmulai=$tahun.'-'.$bulan.'-01';//echo $hari;
$tglawaltahun=$tahun.'-01-01';

#tgl kemarin
list($year, $month, $day) = explode('-',  $tgl);
$tglkmrn=date("Y-m-d", mktime(0, 0, 0, $month,$day-1,$year));
$tglbesok=date("Y-m-d", mktime(0, 0, 0, $month, $day+1, $year));
$tglskr=date("Ymd", mktime(0, 0, 0, $month, $day, $year));
$tglawalthn=date("Ymd", mktime(0, 0, 0, 1, 1, $year));
$tglblnthn=date("Ymd", mktime(0, 0, 0, $month, 1, $year));
	

if (($kgt=='')&&($kdorg=='')){
$isiKat= "";
}else if (($kgt=='')&&($kdorg!='')){
$isiKat= " unit ='".$kdorg."' AND";	
}else if (($kgt!='')&&($kdorg=='')){
$isiKat= " `status` ='".$kgt."' AND";		
}else if (($kgt!='')&&($kdorg!='')){
$isiKat= "`status` ='".$kgt."'  AND unit ='".$kdorg."' AND";			
}

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{
		if($tgl=='--')
		{
			echo"Error: Tanggal tidak boleh kosong"; 
			exit;
		}
}


$strFix="SELECT * FROM ((SELECT `tahun`,  `status`,`kodekegiatan`,  `unit`,  `kodeorg` FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` WHERE tgl BETWEEN  '".$tglawalthn."' AND '".$tglskr."' )
UNION (SELECT  A.`tahunbudget` AS `tahun` ,B.`kelompok` AS `status`, A.`kegiatan` AS `kodekegiatan`, LEFT(A.`afdeling`,4) AS `unit`, A.`afdeling` AS `kodeorg`  FROM `bgt_hk_per_kegiatan_afdeling_vw` A  left join `setup_kegiatan` B ON  A.`kegiatan`=B.`kodekegiatan` )) AS tbl WHERE ".$isiKat." `tahun`='".$tahun."' ";

			
			// BUDGET VS REALISASI
			$resKeb=mysql_query($strFix) or die ("SQL ERR : ".mysql_error());
			$nmFiel="A.hk"+$bulan;
			$arrKeb=array();
			while($barKeb=mysql_fetch_assoc($resKeb))
			{
				$arrKeb[$barKeb['kodekegiatan']][$barKeb['unit']][$barKeb['kodeorg']]=$barKeb['status'];
			}
			
			//-------------------------------------> BUDGET SETAHUN
			// BUDGET SETAHUN PERKEGIATAN PERKEBUN PERAFDELING
			$aBgtThnIni="SELECT  `tahunbudget`, `afdeling`,  `kegiatan`, sum(`volume`) AS `volume` FROM `bgt_lbm_volume_afdeling_vw` WHERE `tahunbudget`='".$tahun."' GROUP BY `tahunbudget`,  `afdeling`,  `kegiatan`"; 
			$bBgtThnIni=mysql_query($aBgtThnIni) or die (mysql_error($conn));
				$arrBgtThn=array();
				while($cBgtThnIni=mysql_fetch_assoc($bBgtThnIni))
				{
					$arrBgtThn[$cBgtThnIni['kegiatan']][$cBgtThnIni['afdeling']]=$cBgtThnIni['volume'];
				}
			
			// BUDGET SETAHUN PERKEGIATAN PERKEBUN
			
			$aBgtThnIniKEB="SELECT  `tahunbudget`,  LEFT(`afdeling`,4) AS `kebun`,  `kegiatan`,  sum(`volume`) AS `volume` FROM `bgt_lbm_volume_afdeling_vw` WHERE `tahunbudget`='".$tahun."' GROUP BY `tahunbudget`,  LEFT(`afdeling`,4),  `kegiatan`"; 
			$bBgtThnIniKEB=mysql_query($aBgtThnIniKEB) or die (mysql_error($conn));
				$arrBgtThnKEB=array();
				while($cBgtThnIniKEB=mysql_fetch_assoc($bBgtThnIniKEB))
				{
					$arrBgtThnKEB[$cBgtThnIniKEB['kegiatan']][$cBgtThnIniKEB['kebun']]=$cBgtThnIniKEB['volume'];
				}
			
			// BUDGET SETAHUN PERKEGIATAN
			$aBgtThnIniKEG="SELECT  `tahunbudget`,  `kegiatan`,  sum(`volume`) AS `volume` FROM `bgt_lbm_volume_afdeling_vw` WHERE `tahunbudget`='".$tahun."'  GROUP BY `tahunbudget`,  `kegiatan`"; 
			$bBgtThnIniKEG=mysql_query($aBgtThnIniKEG) or die (mysql_error($conn));
				$arrBgtThnKEG=array();
				while($cBgtThnIniKEG=mysql_fetch_assoc($bBgtThnIniKEG))
				{
					$arrBgtThnKEG[$cBgtThnIniKEG['kegiatan']]=$cBgtThnIniKEG['volume'];
				}
				
				
			//-------------------------------------> BUDGET BLN INI
			// #BUDGET BULAN INI PERKEGIATAN PERKEBUN PERAFDELING
			$aBgtBlnIni="SELECT  A.`tahunbudget`,  A.`afdeling`,  A.`kegiatan`, sum(B.`volume`/A.`jumlahhk` * ".$nmFiel.") AS bgtbln FROM `bgt_hk_per_kegiatan_afdeling_vw` A LEFT JOIN `bgt_lbm_volume_afdeling_vw` B ON CONCAT(A.`tahunbudget`,  A.`afdeling`,  A.`kegiatan`)=CONCAT(B.`tahunbudget`,  B.`afdeling`,  B.`kegiatan`)  WHERE A.`tahunbudget`='".$tahun."' GROUP BY A.`tahunbudget`,  A.`afdeling`,  A.`kegiatan`";
			
			$bBgtBlnIni=mysql_query($aBgtBlnIni) or die (mysql_error($conn));
				$arrBgtThnIni=array();
				while($cBgtBlnIni=mysql_fetch_assoc($bBgtBlnIni))
				{
				$arrBgtThnIni[$cBgtBlnIni['kegiatan']][$cBgtBlnIni['afdeling']]=$cBgtBlnIni['bgtbln'];
				}
			
			// #BUDGET BULAN INI PERKEGIATAN PERKEBUN
			$aBgtBlnIniKEB="SELECT  A.`tahunbudget`,  LEFT(A.`afdeling`,4) AS `kebun`,  A.`kegiatan`, sum(B.`volume`/A.`jumlahhk` * ".$nmFiel.") AS bgtbln FROM `bgt_hk_per_kegiatan_afdeling_vw` A LEFT JOIN `bgt_lbm_volume_afdeling_vw` B ON CONCAT(A.`tahunbudget`,  A.`afdeling`,  A.`kegiatan`)=CONCAT(B.`tahunbudget`,  B.`afdeling`,  B.`kegiatan`)  WHERE A.`tahunbudget`='".$tahun."' GROUP BY A.`tahunbudget`,  LEFT(A.`afdeling`,4),  A.`kegiatan`";
			
			$bBgtBlnIniKEB=mysql_query($aBgtBlnIniKEB) or die (mysql_error($conn));
				$arrBgtThnIniKEB=array();
				while($cBgtBlnIniKEB=mysql_fetch_assoc($bBgtBlnIniKEB))
				{
				$arrBgtThnIniKEB[$cBgtBlnIniKEB['kegiatan']][$cBgtBlnIniKEB['kebun']]=$cBgtBlnIniKEB['bgtbln'];
				}
			
			// #BUDGET BULAN INI PERKEGIATAN
			$aBgtBlnIniKEG="SELECT  A.`tahunbudget`,   A.`kegiatan`, sum(B.`volume`/A.`jumlahhk` * ".$nmFiel.") AS bgtbln FROM `bgt_hk_per_kegiatan_afdeling_vw` A LEFT JOIN `bgt_lbm_volume_afdeling_vw` B ON CONCAT(A.`tahunbudget`,  A.`afdeling`,  A.`kegiatan`)=CONCAT(B.`tahunbudget`,  B.`afdeling`,  B.`kegiatan`)  WHERE A.`tahunbudget`='".$tahun."' GROUP BY A.`tahunbudget`,  A.`kegiatan`";
			
			$bBgtBlnIniKEG=mysql_query($aBgtBlnIniKEG) or die (mysql_error($conn));
				$arrBgtThnIniKEG=array();
				while($cBgtBlnIniKEG=mysql_fetch_assoc($bBgtBlnIniKEG))
				{
				$arrBgtThnIniKEG[$cBgtBlnIniKEG['kegiatan']]=$cBgtBlnIniKEG['bgtbln'];
				}
			

			//-------------------------------------> REALISASI HARI INI
			// #REALISASI HARI INI PERKEGIATAN PERKEBUN PERAFDELING
			$aProdBulHi="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as HI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl='".$tglskr."' AND tahun='".$tahun."' group by  tgl,kodekegiatan,kodeorg";
			
			
			$bProdBulHi=mysql_query($aProdBulHi) or die (mysql_error($conn));
			
				$arrRelBulIni=array();
				while($cProdBulHi=mysql_fetch_assoc($bProdBulHi))
				{
				$hasilB=$cProdBulHi['HI'];
				$arrRelBulIni[$cProdBulHi['kodekegiatan']][$cProdBulHi['kodeorg']]=$hasilB;
				}
				
			// #REALISASI HARI INI PERKEGIATAN PERKEBUN
			$aProdBulHiKEB="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as HI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl='".$tglskr."' AND tahun='".$tahun."' group by  tgl,kodekegiatan,unit";
			
			
			$bProdBulHiKEB=mysql_query($aProdBulHiKEB) or die (mysql_error($conn));
			
				$arrRelBulIniKEB=array();
				while($cProdBulHiKEB=mysql_fetch_assoc($bProdBulHiKEB))
				{
				$hasilBKEB=$cProdBulHiKEB['HI'];
				$arrRelBulIniKEB[$cProdBulHiKEB['kodekegiatan']][$cProdBulHiKEB['unit']]=$hasilBKEB;
				}
				
			// #REALISASI HARI INI PERKEGIATAN
			$aProdBulHiKEG="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as HI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl='".$tglskr."' AND tahun='".$tahun."'  group by  tgl,kodekegiatan";
			
			
			$bProdBulHiKEG=mysql_query($aProdBulHiKEG) or die (mysql_error($conn));
			
				$arrRelBulIniKEG=array();
				while($cProdBulHiKEG=mysql_fetch_assoc($bProdBulHiKEG))
				{
				$hasilBKEG=$cProdBulHiKEG['HI'];
				$arrRelBulIniKEG[$cProdBulHiKEG['kodekegiatan']]=$hasilBKEG;
				}

				
			//-------------------------------------> REALISASI S/D HARI INI
			// #REALISASI S/D HARI INI PERKEGIATAN PERKEBUN PERAFDELING	
			$aProdBulSdHi="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as SDHI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl BETWEEN  '".$tglblnthn."' AND '".$tglskr."' AND tahun='".$tahun."' group by kodekegiatan,kodeorg";
			
			$bProdBulSdHi=mysql_query($aProdBulSdHi) or die (mysql_error($conn));
				$arrRelBulsdIni=array();
				$arrplusMinusBlnIni=array();
				$arrpencapaianBlnIni=array();
				while($cProdBulSdHi=mysql_fetch_assoc($bProdBulSdHi))
				{
				
				$hasilC=$cProdBulSdHi['SDHI'];
				$arrRelBulsdIni[$cProdBulSdHi['kodekegiatan']][$cProdBulSdHi['kodeorg']]=$hasilC;
				
				#5~untuk +/-	
				$hasilC1=$arrBgtThnIni[$cProdBulSdHi['kodekegiatan']][$cProdBulSdHi['kodeorg']];
				
					// #REALISASI +/- S/D HARI INI PERKEGIATAN PERKEBUN PERAFDELING	
					@$arrplusMinusBlnIni[$cProdBulSdHi['kodekegiatan']][$cProdBulSdHi['kodeorg']]=$hasilC-$hasilC1;
					// #REALISASI PERCENT S/D HARI INI PERKEGIATAN PERKEBUN PERAFDELING	
					@$arrpencapaianBlnIni[$cProdBulSdHi['kodekegiatan']][$cProdBulSdHi['kodeorg']]=($hasilC/$hasilC1)*100;
			
				
				}
			
			// #REALISASI S/D HARI INI PERKEGIATAN PERKEBUN
			$aProdBulSdHiKEB="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as SDHI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl BETWEEN  '".$tglblnthn."' AND '".$tglskr."' AND tahun='".$tahun."'group by kodekegiatan,unit";
			
			$bProdBulSdHiKEB=mysql_query($aProdBulSdHiKEB) or die (mysql_error($conn));
				$arrRelBulsdIniKEB=array();
				$arrplusMinusBlnIniKEB=array();
				$arrpencapaianBlnIniKEB=array();
				while($cProdBulSdHiKEB=mysql_fetch_assoc($bProdBulSdHiKEB))
				{
				
				$hasilCKEB=$cProdBulSdHiKEB['SDHI'];
				$arrRelBulsdIniKEB[$cProdBulSdHiKEB['kodekegiatan']][$cProdBulSdHiKEB['unit']]=$hasilCKEB;
				
				#5~untuk +/-	
				$hasilC1KEB=$arrBgtThnIniKEB[$cProdBulSdHiKEB['kodekegiatan']][$cProdBulSdHiKEB['unit']];
				
					// #REALISASI +/- S/D HARI INI PERKEGIATAN PERKEBUN
					@$arrplusMinusBlnIniKEB[$cProdBulSdHiKEB['kodekegiatan']][$cProdBulSdHiKEB['unit']]=$hasilCKEB-$hasilC1KEB;
					// #REALISASI PERCENT S/D HARI INI PERKEGIATAN PERKEBUN
					@$arrpencapaianBlnIniKEB[$cProdBulSdHiKEB['kodekegiatan']][$cProdBulSdHiKEB['unit']]=($hasilCKEB/$hasilC1KEB)*100;
			
				
				}			
				
			// #REALISASI S/D HARI INI PERKEGIATAN
			$aProdBulSdHiKEG="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as SDHI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl BETWEEN  '".$tglblnthn."' AND '".$tglskr."' AND tahun='".$tahun."' group by kodekegiatan";
			
			$bProdBulSdHiKEG=mysql_query($aProdBulSdHiKEG) or die (mysql_error($conn));
				$arrRelBulsdIniKEG=array();
				$arrplusMinusBlnIniKEG=array();
				$arrpencapaianBlnIniKEG=array();
				while($cProdBulSdHiKEG=mysql_fetch_assoc($bProdBulSdHiKEG))
				{
				
				$hasilCKEG=$cProdBulSdHiKEG['SDHI'];
				$arrRelBulsdIniKEG[$cProdBulSdHiKEG['kodekegiatan']]=$hasilCKEG;
				
				#5~untuk +/-	
				$hasilC1KEG=$arrBgtThnIniKEG[$cProdBulSdHiKEG['kodekegiatan']];
				
					// #REALISASI +/- S/D HARI INI PERKEGIATAN PERKEBUN
					@$arrplusMinusBlnIniKEG[$cProdBulSdHiKEG['kodekegiatan']]=$hasilCKEG-$hasilC1KEG;
					// #REALISASI PERCENT S/D HARI INI PERKEGIATAN PERKEBUN
					@$arrpencapaianBlnIniKEG[$cProdBulSdHiKEG['kodekegiatan']]=($hasilCKEG/$hasilC1KEG)*100;
			
				
				}			
		
		
			//-------------------------------------> REALISASI S/D HARI INI BULAN INI
			// #REALISASI S/D HARI INI BULAN INI PERKEGIATAN PERKEBUN PERAFDELING	
			$aProdBulSdBiHi="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as SDHIBI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl BETWEEN  '".$tglawalthn."' AND '".$tglskr."' AND tahun='".$tahun."' group by kodekegiatan,kodeorg";
			
			$bProdBulSdBiHi=mysql_query($aProdBulSdBiHi) or die (mysql_error($conn));
			
			
				$arrRelBulsdbihi=array();
				$arrplusMinusBlnIniHariIni=array();
				$arrpencapaianBlnIniHariIni=array();
				while($cProdBulSdBiHi=mysql_fetch_assoc($bProdBulSdBiHi)){
				
					$hasilD=$cProdBulSdBiHi['SDHIBI'];
					$arrRelBulsdbihi[$cProdBulSdBiHi['kodekegiatan']][$cProdBulSdBiHi['kodeorg']]=$hasilD;
				
					#8~untuk +/-	
					$hasilVol=$arrBgtThn[$cProdBulSdBiHi['kodekegiatan']][$cProdBulSdBiHi['kodeorg']];
					// #REALISASI +/- S/D HARI INI BULAN INI PERKEGIATAN PERKEBUN PERAFDELING	
					@$arrplusMinusBlnIniHariIni[$cProdBulSdBiHi['kodekegiatan']][$cProdBulSdBiHi['kodeorg']]=$hasilD-$hasilVol;
					// #REALISASI PERCENT S/D HARI INI BULAN INI PERKEGIATAN PERKEBUN PERAFDELING	
					@$arrpencapaianBlnIniHariIni[$cProdBulSdBiHi['kodekegiatan']][$cProdBulSdBiHi['kodeorg']]=$hasilD/$hasilVol*100;
				
				}
				
				// #REALISASI S/D HARI INI BULAN INI PERKEGIATAN PERKEBUN	
			$aProdBulSdBiHiKEB="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as SDHIBI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl BETWEEN  '".$tglawalthn."' AND '".$tglskr."' AND tahun='".$tahun."' group by kodekegiatan,unit";
			
			$bProdBulSdBiHiKEB=mysql_query($aProdBulSdBiHiKEB) or die (mysql_error($conn));
			
			
				$arrRelBulsdbihiKEB=array();
				$arrplusMinusBlnIniHariIniKEB=array();
				$arrpencapaianBlnIniHariIniKEB=array();
				while($cProdBulSdBiHiKEB=mysql_fetch_assoc($bProdBulSdBiHiKEB)){
				
					$hasilDKEB=$cProdBulSdBiHiKEB['SDHIBI'];
					$arrRelBulsdbihiKEB[$cProdBulSdBiHiKEB['kodekegiatan']][$cProdBulSdBiHiKEB['unit']]=$hasilDKEB;
				
					#8~untuk +/-	
					$hasilVolKEB=$arrBgtThnKEB[$cProdBulSdBiHiKEB['kodekegiatan']][$cProdBulSdBiHiKEB['unit']];
					// #REALISASI +/- S/D HARI INI BULAN INI PERKEGIATAN PERKEBUN 	
					@$arrplusMinusBlnIniHariIniKEB[$cProdBulSdBiHiKEB['kodekegiatan']][$cProdBulSdBiHiKEB['unit']]=$hasilDKEB-$hasilVolKEB;
					// #REALISASI PERCENT S/D HARI INI BULAN INI PERKEGIATAN PERKEBUN 
					@$arrpencapaianBlnIniHariIniKEB[$cProdBulSdBiHiKEB['kodekegiatan']][$cProdBulSdBiHiKEB['unit']]=$hasilDKEB/$hasilVolKEB*100;
				
				}
				
				// #REALISASI S/D HARI INI BULAN INI PERKEGIATAN 
			$aProdBulSdBiHiKEG="SELECT  `tahun`,  `tgl`,`status`,  `kodekegiatan`,  `unit`,  `kodeorg`,  sum(`hasilkerja`) as SDHIBI FROM `kebun_prestasi_kegiatan_perafdeling_thn_vw` where ".$isiKat." tgl BETWEEN  '".$tglawalthn."' AND '".$tglskr."' AND tahun='".$tahun."' group by kodekegiatan";
			
			$bProdBulSdBiHiKEG=mysql_query($aProdBulSdBiHiKEG) or die (mysql_error($conn));
			
			
				$arrRelBulsdbihiKEG=array();
				$arrplusMinusBlnIniHariIniKEG=array();
				$arrpencapaianBlnIniHariIniKEG=array();
				while($cProdBulSdBiHiKEG=mysql_fetch_assoc($bProdBulSdBiHiKEG)){
				
					$hasilDKEG=$cProdBulSdBiHiKEG['SDHIBI'];
					$arrRelBulsdbihiKEG[$cProdBulSdBiHiKEG['kodekegiatan']]=$hasilDKEG;
				
					#8~untuk +/-	
					$hasilVolKEG=$arrBgtThnKEG[$cProdBulSdBiHiKEG['kodekegiatan']];
					// #REALISASI +/- S/D HARI INI BULAN INI PERKEGIATAN  	
					@$arrplusMinusBlnIniHariIniKEG[$cProdBulSdBiHiKEG['kodekegiatan']]=$hasilDKEG-$hasilVolKEG;
					// #REALISASI PERCENT S/D HARI INI BULAN INI PERKEGIATAN  
					@$arrpencapaianBlnIniHariIniKEG[$cProdBulSdBiHiKEG['kodekegiatan']]=$hasilDKEG/$hasilVolKEG*100;
				
				}


$No=1;
$kosong=mysql_num_rows($resKeb);
switch($kgt){
	case "TB":
		$nmSts="<tr><td  align=center colspan=10>( Tanaman Baru )</td></tr>";
		break;
	case "TBM":
		$nmSts="<tr><td  align=center colspan=10>( Tanaman Belum Menghasilkan )</td></tr>";
		break;
	case "TM":
		$nmSts="<tr><td  align=center colspan=10>( Tanaman Menghasilkan )</td></tr>";
		break;	
	case "BBT":
		$nmSts="<tr><td  align=center colspan=10>( Pembibitan )</td></tr>";
		break;	
}
if($kosong!=0){
$namabulan=numToMonth($bulan,$lang='I',$format='long');
$plusminus="+/-";
if(($proses=='excel'))
{
$plusminus="=CHAR(43)&CHAR(47)&CHAR(45)";
$stream="<table cellspacing='1' border='0' >
<tr><td  colspan=10 align=center><b>LAPORAN HARIAN KEGIATAN KEBUN<b></td></tr>
".$nmSts."
<tr><td  colspan=10 align=center>Tanggal : ".$hari." ".$namabulan." ".$tahun."</td></tr>
</table><br>";
}



foreach($arrKeb as $key=>$value){

$stream.="<br/>".$No." ".strtoupper($namaKeg[$key]);
$stream.="<table cellspacing='1' border='1' class='sortable'><thead class=rowheader>";
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
    <td colspan=2 rowspan=2 align=center bgcolor=#CCCCCC>Budget ( ".strtoupper($namaSat[$key])." )</td>
    <td colspan=7 align=center bgcolor=#CCCCCC>Realisasi</td>
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
    <td align=center bgcolor=#CCCCCC>".$plusminus."</td>
    <td align=center bgcolor=#CCCCCC>Pencapaian (%)</td>
    <td align=center bgcolor=#CCCCCC>S/D HI BI</td>
    <td align=center bgcolor=#CCCCCC>".$plusminus."</td>
    <td align=center bgcolor=#CCCCCC>Pencapaian(%)</td>
  </tr></thead>";
				
				
	foreach($value as $key1=>$value1){
		
		$strkbn=str_replace("ESTATE","",$namaAfd[$key1]);
		$stream.="
			<tr class=rowcontent  >
				<td colspan=10>".$strkbn."</td>
			</tr>
			";	
		
		foreach($value1 as $key2=>$value2){
				
				
				$stream.="<tr class=rowcontent>
				<td>".str_replace($strkbn,"",$namaAfd[$key2])."</td>
				
				<td align=right>".number_format($arrBgtThn[$key][$key2],2)."</td>
				<td align=right>".number_format($arrBgtThnIni[$key][$key2],2)."</td>
				<td align=right>".number_format($arbrRelBulIni[$key][$key2],2)."</td>
				<td align=right>".number_format($arrRelBulsdIni[$key][$key2],2)."</td>
				<td align=right>".number_format($arrplusMinusBlnIni[$key][$key2],2)."</td>
				<td align=right>".number_format($arrpencapaianBlnIni[$key][$key2],2)."</td>
				<td align=right>".number_format($arrRelBulsdbihi[$key][$key2],2)."</td>
				<td align=right>".number_format($arrplusMinusBlnIniHariIni[$key][$key2],2)."</td>
				<td align=right>".number_format($arrpencapaianBlnIniHariIni[$key][$key2],2)."</td>
				
				
			</tr>";	
				
		}		
								
				
			
				
				
								
			
								
			$stream.="<tr >
			
				<td>Sub Total ".$strkbn."</td>								
				<td align=right>".number_format($arrBgtThnKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrBgtThnIniKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arbrRelBulIniKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrRelBulsdIniKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrplusMinusBlnIniKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrpencapaianBlnIniKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrRelBulsdbihiKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrplusMinusBlnIniHariIniKEB[$key][$key1],2)."</td>
				<td align=right>".number_format($arrpencapaianBlnIniHariIniKEB[$key][$key1],2)."</td>
				
			</tr>";
				
				
			
	}


$stream.="<thead>
			<tr>
				<td align=center>Total</td>
				<td align=right>".number_format($arrBgtThnKEG[$key],2)."</td>
				<td align=right>".number_format($arrBgtThnIniKEG[$key],2)."</td>
				<td align=right>".number_format($arbrRelBulIniKEG[$key],2)."</td>
				<td align=right>".number_format($arrRelBulsdIniKEG[$key],2)."</td>
				<td align=right>".number_format($arrplusMinusBlnIniKEG[$key],2)."</td>
				<td align=right>".number_format($arrpencapaianBlnIniKEG[$key],2)."</td>
				<td align=right>".number_format($arrRelBulsdbihiKEG[$key],2)."</td>
				<td align=right>".number_format($arrplusMinusBlnIniHariIniKEG[$key],2)."</td>
				<td align=right>".number_format($arrpencapaianBlnIniHariIniKEG[$key],2)."</td>
			
			</tr></table>";

$No++;




} }




	
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
		$nop_="Laporan_Harian_Kegiatan_Kebun".$tglSkrg;
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