<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/nangkoelib.php');
    require_once('lib/zLib.php');

    $pt=$_GET['pt'];
    $gudang=$_GET['gudang'];
    $tgl1=$_GET['tgl1'];
    $tgl2=$_GET['tgl2'];
    $optPanen = makeOption($dbname,'setup_kegiatan','kodekegiatan,kodekegiatan',
		"kelompok='PNN'");
	$kegPanen = getFirstKey($optPanen);
    
    if($gudang=='')
    {
        $str="select a.notransaksi,a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
            sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a
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

    $res=mysql_query($str);
    $no=0;
    if(mysql_num_rows($res)<1)
    {
        echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }
    else
    {
        $totberat = $totUpah = $totJjg = $totPremi = $totHk = $totPenalty = 0;
        $stream.="<table border=0 cellpading=1 ><tr><td colspan=7 align=center>".$_SESSION['lang']['laporanpanen']."</td></tr>
        <tr><td colspan=3>".$_SESSION['lang']['periode']."</td><td colspan=4 align=left>".$tgl1." S/d ".$tgl1."</td></tr>    
        <tr><td colspan=3>".$_SESSION['lang']['unit']."</td><td colspan=4 align=left>".($gudang!=''?$gudang:$_SESSION['lang']['all'])."</td></tr>
        <tr><td colspan=3>".$_SESSION['lang']['pt']."</td><td colspan=4 align=left>".$pt."</td></tr>        
        </table>
        <br />
        <table border=1>
        <tr>
            <td bgcolor=#DEDEDE align=center>No.</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['afdeling']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['lokasi']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tahuntanam']."</td>    
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['janjang']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['beratBersih']." (Kg)</td>
            <td bgcolor=#DEDEDE align=center>BJR Aktual (Kg)</td>    
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahkerja']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['upahpremi']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlahhk']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['penalti']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
        </tr>";
        while($bar=mysql_fetch_object($res))
        {
            $no+=1;
            $periode=date('Y-m-d H:i:s');
            $notransaksi=$bar->notransaksi;
            $tanggal=$bar->tanggal; 
            $kodeorg 	=$bar->kodeorg;

            $stream.="<tr>
                <td align=center width=20>".$no."</td>

                <td align=center>".tanggalnormal($tanggal)."</td>
                <td align=center>".substr($kodeorg,0,6)."</td>
                <td align=center>".$kodeorg."</td>
                <td align=center>".$bar->tahuntanam."</td>
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
			$stream.="<tr class=rowcontent>
					<td align=center width=20>".$no."</td>
					<td align=center>".tanggalnormal($row['tanggal'])."</td>
					<td align=center>".$row['divisi']."</td>
					<td align=center>".$row['kodeblok']."</td>
					<td align=center>".$row['tahuntanam']."</td>    
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
        $stream.="<tr>
            <td align=center width=20 colspan=5>TOTAL</td>		 
            <td align=right>".number_format($totJjg,0)."</td>
            <td align=right>".number_format($totberat,0)."</td>
            <td align=right>".number_format($totberat/$totJjg,2)."</td>
            <td align=right>".number_format($totUpah,0)."</td>
            <td align=right>".number_format($totPremi,0)."</td>
            <td align=right>".number_format($totHk,0)."</td>
            <td align=right>".number_format($totPenalty,0)."</td>
            <td align=left></td>
        </tr>";
        $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
    }	
    $tglSkrg=date("Ymd");
    $nop_="LaporanPanen".$pt."_".$gudang."_".$tgl1;
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