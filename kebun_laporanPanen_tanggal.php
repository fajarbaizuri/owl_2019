<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/nangkoelib.php');
	require_once('lib/zLib.php');

    // ambil yang dilempar javascript
    $pt=$_POST['pt'];
    $unit=$_POST['unit'];
    $tgl1=$_POST['tgl1'];
    $tgl2=$_POST['tgl2'];
    $optPanen = makeOption($dbname,'setup_kegiatan','kodekegiatan,kodekegiatan',
		"kelompok='PNN'");
	$kegPanen = getFirstKey($optPanen);
	
    // olah tanggal
    $tanggal1=explode('-',$tgl1);
    $tanggal2=explode('-',$tgl2);
    $date1=$tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
    $tanggalterakhir=date('t', strtotime($date1));
    
    // urutin tanggal
    $tanggal=Array();
    if($tanggal2[1]>$tanggal1[1]){ // beda bulan
        for ($i = $tanggal1[0]; $i <= $tanggalterakhir; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii]=$tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
        }
        for ($i = 1; $i <= $tanggal2[0]; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $tanggal[$tanggal2[2].'-'.$tanggal2[1].'-'.$ii]=$tanggal2[2].'-'.$tanggal2[1].'-'.$ii;
        }
    }else{ // sama bulan
        for ($i = $tanggal1[0]; $i <= $tanggal2[0]; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii]=$tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
        }
    }
        
    if($unit=='') // script copy-an dari kebun_laporanPanen.php
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
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
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a
        where unit = '".$unit."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal, a.kodeorg";
		
		$qKontan="SELECT a.*,b.*,c.tahuntanam FROM ".$dbname.".log_baspk a LEFT JOIN ".$dbname.".
			log_spkht b ON a.notransaksi=b.notransaksi left join ".$dbname.".setup_blok c on ".
			"a.kodeblok=c.kodeorg WHERE a.kodekegiatan='".$kegPanen.
			"' and b.kodeorg= '".$unit."' and b.tanggal between ".tanggalsystem($tgl1).
			" and ".tanggalsystem($tgl2)."";
    }
	$resKontan = fetchData($qKontan);

    // isi array
    $jumlahhari=count($tanggal);
    $res=mysql_query($str);
    $dzArr=array();
    if(mysql_num_rows($res)<1){
        $jukol=($jumlahhari*3)+5;
        echo"<tr class=rowcontent><td colspan=".$jukol.">".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }else{
        while($bar=mysql_fetch_object($res)){
            $dzArr[$bar->kodeorg][$bar->tanggal]=$bar->tanggal;
            $dzArr[$bar->kodeorg]['kodeorg']=$bar->kodeorg;
            $dzArr[$bar->kodeorg]['tahuntanam']=$bar->tahuntanam;
            $dzArr[$bar->kodeorg][$bar->tanggal.'j']=$bar->jjg;
            $dzArr[$bar->kodeorg][$bar->tanggal.'k']=$bar->berat;
            $dzArr[$bar->kodeorg][$bar->tanggal.'h']=$bar->jumlahhk;
        }	
    }
	
	# Kontan
	foreach($resKontan as $row) {
		if(isset($dzArr[$row['kodeblok']][$row['tanggal']])) {
			$dzArr[$row['kodeblok']][$row['tanggal'].'j']+=$row['hasilkerjarealisasi'];
			$dzArr[$row['kodeblok']][$row['tanggal'].'h']+=$row['hkrealisasi'];
		} else {
			$dzArr[$row['kodeblok']][$row['tanggal']]=$row['tanggal'];
            $dzArr[$row['kodeblok']]['kodeorg']=$row['kodeblok'];
            $dzArr[$row['kodeblok']]['tahuntanam']=$row['tahuntanam'];
            $dzArr[$row['kodeblok']][$row['tanggal'].'j']=$row['hasilkerjarealisasi'];
            $dzArr[$row['kodeblok']][$row['tanggal'].'k']=0;
            $dzArr[$row['kodeblok']][$row['tanggal'].'h']=$row['hkrealisasi'];
		}
	}
	
    if(!empty($dzArr)) { // list isi data on kodeorg
        foreach($dzArr as $c=>$key) { // list tanggal
            $sort_kodeorg[] = $key['kodeorg'];
            $sort_tahuntanam[] = $key['tahuntanam'];
        }
        array_multisort($sort_kodeorg, SORT_ASC, $sort_tahuntanam, SORT_ASC, $dzArr); // urut kodeorg, terus tahun tanam
    }
        
    // header
    echo"<thead>
        <tr>
            <td rowspan=2 align=center>No.</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['afdeling']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['kodeblok']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['tahuntanam']."</td>";    
    foreach($tanggal as $tang){
        $ting=explode('-',$tang);
        $qwe=date('D', strtotime($tang));
        echo"<td colspan=3 align=center>";
        if($qwe=='Sun')echo"<font color=red>".$ting[2]."</font>"; else echo $ting[2]; 
        echo"</td>";
    }
    echo"<td colspan=3 align=center>Total</td></tr><tr>";  
    foreach($tanggal as $tang){
        echo"<td align=center>".$_SESSION['lang']['jjg']."</td>
            <td align=center>".$_SESSION['lang']['kg']."</td>
            <td align=center>Hk</td>";    
    }
    echo"<td align=center>".$_SESSION['lang']['jjg']."</td>
        <td align=center>".$_SESSION['lang']['kg']."</td>
        <td align=center>Hk</td></tr>  
        </thead>
	<tbody>";

    // content
    $no=0;
    foreach($dzArr as $arey){ // list isi data on kodeorg
        $no+=1;
        echo"<tr class='rowcontent'>
            <td align=center>".$no."</td>
            <td align=center>".substr($arey['kodeorg'],0,6)."</td>
            <td align=center>".$arey['kodeorg']."</td>
            <td align=center>".$arey['tahuntanam']."</td>";    
        $totalj=0;
        $totalk=0;
        $totalh=0;
        foreach($tanggal as $tang){ // list tanggal
            $qwe=date('D', strtotime($tang));
			if(!isset($arey[$tang.'j']))$arey[$tang.'j']=0;
			if(!isset($arey[$tang.'k']))$arey[$tang.'k']=0;
			if(!isset($arey[$tang.'h']))$arey[$tang.'h']=0;
            if($qwe=='Sun'){
                echo"<td align=right><font color=red>".number_format($arey[$tang.'j'])."</font></td>";    
                echo"<td align=right><font color=red>".number_format($arey[$tang.'k'])."</font></td>";    
                echo"<td align=right><font color=red>".number_format($arey[$tang.'h'])."</font></td>";    
            }else{
                echo"<td align=right>".number_format($arey[$tang.'j'])."</td>";    
                echo"<td align=right>".number_format($arey[$tang.'k'])."</td>";    
                echo"<td align=right>".number_format($arey[$tang.'h'])."</td>";    
            }
            echo"</td>";
			if(isset($total[$tang.'j'])) {
				$total[$tang.'j']+=$arey[$tang.'j']; // tambahin total bawah
			} else {
				$total[$tang.'j']=$arey[$tang.'j'];
			}
			if(isset($total[$tang.'k'])) {
				$total[$tang.'k']+=$arey[$tang.'k']; // tambahin total bawah
			} else {
				$total[$tang.'k']=$arey[$tang.'k'];
			}
			if(isset($total[$tang.'h'])) {
				$total[$tang.'h']+=$arey[$tang.'h']; // tambahin total bawah
			} else {
				$total[$tang.'h']=$arey[$tang.'h'];
			}
            
            $totalj+=$arey[$tang.'j']; // tambahin total kanan
            $totalk+=$arey[$tang.'k']; // tambahin total kanan
            $totalh+=$arey[$tang.'h']; // tambahin total kanan
        }
        echo"<td align=right>".number_format($totalj)."</td>
            <td align=right>".number_format($totalk)."</td>
            <td align=right>".number_format($totalh)."</td></tr>";
    }
    
    // tampilin total
    echo"<tr class='rowcontent'>
        <td colspan=4 align=center>Total</td>";
    $totalj=0;
    $totalk=0;
    $totalh=0;
    foreach($tanggal as $tang){ // list tanggal
        echo"<td align=right>".number_format($total[$tang.'j'])."</td>";   
        echo"<td align=right>".number_format($total[$tang.'k'])."</td>";    
        echo"<td align=right>".number_format($total[$tang.'h'])."</td>";    
        $totalj+=$total[$tang.'j']; // tambahin total kanan
        $totalk+=$total[$tang.'k']; // tambahin total kanan
        $totalh+=$total[$tang.'h']; // tambahin total kanan
    }
    echo"<td align=right>".number_format($totalj)."</td>
        <td align=right>".number_format($totalk)."</td>
        <td align=right>".number_format($totalh)."</td></tr>";
    echo"</tbody>
        <tfoot>
        </tfoot>";		 

?>