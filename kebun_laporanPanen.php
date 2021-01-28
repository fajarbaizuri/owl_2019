<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$tgl1=$_POST['tgl1'];
        $tgl2=$_POST['tgl2'];
    $optPanen = makeOption($dbname,'setup_kegiatan','kodekegiatan,kodekegiatan',
		"kelompok='PNN'");
	$kegPanen = getFirstKey($optPanen);
	if($gudang=='')
	{
	
		$str="select a.notransaksi,a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
                    sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk from ".$dbname.".kebun_prestasi_vw a
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal,a.kodeorg";
		
		$qKontan="SELECT a.*,b.*,d.tahuntanam FROM ".$dbname.".log_baspk a LEFT JOIN ".$dbname.".
			log_spkht b ON a.notransaksi=b.notransaksi left join ".$dbname.".organisasi c
			on b.kodeorg=c.kodeorganisasi left join ".$dbname.".setup_blok d on a.kodeblok=d.kodeorg ".
			"WHERE a.kodekegiatan='".$kegPanen.
			"' and c.induk = '".$pt."' and b.tanggal between ".tanggalsystem($tgl1).
			" and ".tanggalsystem($tgl2)."";
	}
	else
	{
		$str="select a.notransaksi,a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
                    sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a
		where unit = '".$gudang."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal, a.kodeorg";
		
		$qKontan="SELECT a.*,b.*,c.tahuntanam FROM ".$dbname.".log_baspk a LEFT JOIN ".$dbname.".
			log_spkht b ON a.notransaksi=b.notransaksi left join ".$dbname.".setup_blok c on ".
			"a.kodeblok=c.kodeorg WHERE a.kodekegiatan='".$kegPanen.
			"' and b.kodeorg= '".$gudang."' and b.tanggal between ".tanggalsystem($tgl1).
			" and ".tanggalsystem($tgl2)."";
	}
	$resKontan = fetchData($qKontan);
//=================================================

	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1) {
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	} else {
		$totberat = $totUpah = $totJjg = $totPremi = $totHk = $totPenalty = 0;
		while($bar=mysql_fetch_object($res)) {
			$no+=1;
				$periode=date('Y-m-d H:i:s');
				$notransaksi=$bar->notransaksi;
				$tanggal=$bar->tanggal; 
				$kodeorg 	=$bar->kodeorg;
			$arr="tanggal##".$tanggal."##kodeorg##".$kodeorg;	  
			echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"zDetail(event,'kebun_slave_2panen.php','".$arr."');\">
					<td align=center width=20>".$no."</td>
					<td align=center>".tanggalnormal($tanggal)."</td>
					<td align=center>".substr($kodeorg,0,6)."</td>
					<td align=center>".$kodeorg."</td>
					<td align=right>".$bar->tahuntanam."</td>    
					<td align=right>".number_format($bar->jjg,0)."</td>
					<td align=right>".number_format($bar->berat,0)."</td>
					<td align=right>".number_format($bar->berat/$bar->jjg,2)."</td>
					<td align=right>".number_format($bar->upah,0)."</td>
					<td align=right>".number_format($bar->premi,0)."</td>
					<td align=right>".number_format($bar->jumlahhk,0)."</td>
					<td align=right>".number_format($bar->penalty,0)."</td>
					<td align=left></td>
				</tr>"; 
					$totberat+=$bar->berat;
					$totUpah+=$bar->upah;
					$totJjg+=$bar->jjg;
					$totPremi+=$bar->premi;
					$totHk+=$bar->jumlahhk;
					$totPenalty+=$bar->penalty;
		}
		
		# Kontanan
		foreach($resKontan as $row) {
			$no++;
			echo"<tr class=rowcontent>
					<td align=center width=20>".$no."</td>
					<td align=center>".tanggalnormal($row['tanggal'])."</td>
					<td align=center>".$row['divisi']."</td>
					<td align=center>".$row['kodeblok']."</td>
					<td align=right>".$row['tahuntanam']."</td>    
					<td align=right>".$row['hasilkerjarealisasi']."</td>
					<td align=right>".number_format(0,0)."</td>
					<td align=right>".number_format(0,0)."</td>
					<td align=right>".number_format($row['jumlahrealisasi'],0)."</td>
					<td align=right>".number_format(0,0)."</td>
					<td align=right>".number_format($row['hkrealisasi'],0)."</td>
					<td align=right>".number_format(0,0)."</td>
					<td align=left>Kontanan</td>
				</tr>";
			$totUpah+=$row['jumlahrealisasi'];
			$totJjg+=$row['hasilkerjarealisasi'];
			$totHk+=$row['hkrealisasi'];
		}
		
        echo"<tr class=rowcontent>
				  <td align=center colspan=5>Total</td>		 
				  <td align=right>".number_format($totJjg,0)."</td>
				<td align=right>".number_format($totberat,0)."</td>
				<td align=right>".number_format($totberat/$totJjg,2)."</td>    
				<td align=right>".number_format($totUpah,0)."</td>
				<td align=right>".number_format($totPremi,0)."</td>
				<td align=right>".number_format($totHk,0)."</td>
				<td align=right>".number_format($totPenalty,0)."</td>
				<td align=left></td>
            </tr>";
  }
?>