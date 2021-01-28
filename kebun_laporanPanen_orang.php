<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/nangkoelib.php');
	include_once('lib/zLib.php');

    // ambil yang dilempar javascript
    $pt=$_POST['pt'];
    $unit=$_POST['unit'];
    $tgl1=$_POST['tgl1'];
    $tgl2=$_POST['tgl2'];
	
	//
	$optorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
    
    // olah tanggal
    $tanggal1=explode('-',$tgl1);
    $tanggal2=explode('-',$tgl2);
    $date1=$tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
    $tanggalterakhir=date(t, strtotime($date1));
    
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
    
    // kamus karyawan --- ga dibatesin, batesin untuk optimize (kalo dah yakin)
    $sdakar="select karyawanid, namakaryawan, tipekaryawan,nik, subbagian from ".$dbname.".datakaryawan";
    $qdakar=mysql_query($sdakar) or die(mysql_error($conn));
    while($rdakar=  mysql_fetch_assoc($qdakar))
    {
        $dakar[$rdakar['karyawanid']]['karyawanid']=$rdakar['karyawanid'];
        $dakar[$rdakar['karyawanid']]['namakaryawan']=$rdakar['namakaryawan'];
		$dakar[$rdakar['karyawanid']]['nik']=$rdakar['nik'];
        $dakar[$rdakar['karyawanid']]['tipekaryawan']=$rdakar['tipekaryawan'];
        $dakar[$rdakar['karyawanid']]['subbagian']=$rdakar['subbagian'];
    }

    $stikar="select id, tipe from ".$dbname.".sdm_5tipekaryawan";
    $qtikar=mysql_query($stikar) or die(mysql_error($conn));
    while($rtikar=  mysql_fetch_assoc($qtikar))
    {
        $tikar[$rtikar['id']]=$rtikar['tipe'];
    }
    
    if($unit=='') // script copy-an dari kebun_laporanPanen.php
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,sum(a.luaspanen) as luaspanen, a.karyawanid  from ".$dbname.".kebun_prestasi_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasi
        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal,a.karyawanid";
    }
	
    else
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,sum(a.luaspanen) as luaspanen, a.karyawanid  from ".$dbname.".kebun_prestasi_vw a
        where unit = '".$unit."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal, a.karyawanid";
    }	
  //  echo $str;

    // isi array
    $jumlahhari=count($tanggal);
    $res=mysql_query($str);
    $dzArr=array();
    if(mysql_num_rows($res)<1){
        $jukol=($jumlahhari*3)+5;
        echo"<tr class=rowcontent><td colspan=".$jukol.">".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }else{
        while($bar=mysql_fetch_object($res)){
            $dzArr[$bar->karyawanid][$bar->tanggal]=$bar->tanggal;
            $dzArr[$bar->karyawanid]['karyawanid']=$bar->karyawanid;
//            $dzArr[$bar->karyawanid]['tahuntanam']=$bar->tahuntanam;
            $dzArr[$bar->karyawanid][$bar->tanggal.'j']=$bar->jjg;
            $dzArr[$bar->karyawanid][$bar->tanggal.'k']=$bar->berat;
            $dzArr[$bar->karyawanid][$bar->tanggal.'h']=$bar->luaspanen;
        }	
    } 
    if(!empty($dzArr)) { // list isi data on kodeorg
        foreach($dzArr as $c=>$key) { // list tanggal
            $sort_kodeorg[] = $key['karyawanid'];
//            $sort_tahuntanam[] = $key['tahuntanam'];
        }
        array_multisort($sort_kodeorg, SORT_ASC, $dzArr); // urut kodeorg, terus tahun tanam
    }
        
    // header
    echo"<thead>
        <tr>
            <td rowspan=2 align=center>No.</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['namakaryawan']."</td>
			<td rowspan=2 align=center>".$_SESSION['lang']['nik']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['subbagian']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['tipekaryawan']."</td>";    
    foreach($tanggal as $tang){
        $ting=explode('-',$tang);
        $qwe=date('D', strtotime($tang));
        echo"<td colspan=2 align=center>";
        if($qwe=='Sun')echo"<font color=red>".$ting[2]."</font>"; else echo $ting[2]; 
        echo"</td>";
    }
    echo"<td colspan=3 align=center>Total</td></tr><tr>";  
    foreach($tanggal as $tang){
        echo"<td align=center>".$_SESSION['lang']['jjg']."</td>
            <td align=center>".$_SESSION['lang']['kg']."</td>
            ";
    }
    echo"<td align=center>".$_SESSION['lang']['jjg']."</td>
        <td align=center>".$_SESSION['lang']['kg']."</td>
        
        <td align=center>AVG</td>
		
		</thead>
	<tbody>";

    // content
    $no=0;
    foreach($dzArr as $arey){ // list isi data on kodeorg
        $no+=1;
		$z=0;
        echo"<tr class='rowcontent'>
            <td align=center>".$no."</td>
            <td align=left>".$dakar[$arey['karyawanid']]['namakaryawan']."</td>
			<td align=left>".$dakar[$arey['karyawanid']]['nik']."</td>
            <td align=left>".$optorg[$dakar[$arey['karyawanid']]['subbagian']]."</td>
            <td align=center>".$tikar[$dakar[$arey['karyawanid']]['tipekaryawan']]."</td>";
        $totalj=0;
        $totalk=0;
        $totalh=0;
        foreach($tanggal as $tang){ // list tanggal
            $qwe=date('D', strtotime($tang));
            if($qwe=='Sun'){
                echo"<td align=right><font color=red>".number_format($arey[$tang.'j'])."</font></td>";    
                echo"<td align=right><font color=red>".number_format($arey[$tang.'k'])."</font></td>";    
               // echo"<td align=right><font color=red>".number_format($arey[$tang.'h'])."</font></td>";    
            }else{
                echo"<td align=right>".number_format($arey[$tang.'j'])."</td>";    
                echo"<td align=right>".number_format($arey[$tang.'k'])."</td>";    
                //echo"<td align=right>".number_format($arey[$tang.'h'])."</td>";    
            }
            echo"</td>";
            $total[$tang.'j']+=$arey[$tang.'j']; // tambahin total bawah
            $total[$tang.'k']+=$arey[$tang.'k']; // tambahin total bawah
            //$total[$tang.'h']+=$arey[$tang.'h']; // tambahin total bawah
            
            $totalj+=$arey[$tang.'j']; // tambahin total kanan
            $totalk+=$arey[$tang.'k']; // tambahin total kanan
            //$totalh+=$arey[$tang.'h']; // tambahin total kanan
			//$rincikg=$totalk/$totalh;
			if($arey[$tang.'j']>0)
			{
				$z+=1;
				}
			
        }
        echo"<td align=right>".number_format($totalj)."</td>
            <td align=right>".number_format($totalk)."</td>
            
			<td align=right>".@number_format($totalk/$z)."</td>";
    }
    
    // tampilin total
    echo"<thead><tr class='rowcontent'>
        <td colspan=4 align=center>Total</td>";
    $totalj=0;
    $totalk=0;
    $totalh=0;
    foreach($tanggal as $tang){ // list tanggal
        echo"<td align=right>".number_format($total[$tang.'j'])."</td>";   
        echo"<td align=right>".number_format($total[$tang.'k'])."</td>";    
           
        $totalj+=$total[$tang.'j']; // tambahin total kanan
        $totalk+=$total[$tang.'k']; // tambahin total kanan
       
    }
    echo"<td align=right>".number_format($totalj)."</td>
        <td align=right>".number_format($totalk)."</td>
		
        </tr>";
    echo"</tbody>
        <tfoot>
        </tfoot>";		 

?>