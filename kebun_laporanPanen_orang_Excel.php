<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/nangkoelib.php');
	include_once('lib/zLib.php');

    // ambil yang dilempar javascript
    $pt=$_GET['pt'];
    $unit=$_GET['unit'];
    $tgl1=$_GET['tgl1'];
    $tgl2=$_GET['tgl2'];
	
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

    // isi array
    $jumlahhari=count($tanggal);
    $res=mysql_query($str);
    $dzArr=array();
    if(mysql_num_rows($res)<1){
        $jukol=($jumlahhari*2)+5;
        echo $_SESSION['lang']['tidakditemukan'];
        exit;
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
    
    $stream=$_SESSION['lang']['laporanpanen']." ".$pt." ".$unit." per ".$_SESSION['lang']['orang']." ".$tgl1." - ".$tgl2;
    $stream.='<table border=1 cellpading=1>';
    // header
    $stream.="<thead>
        <tr>
            <td bgcolor=#DEDEDE rowspan=2 align=center>No.</td>
            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['namakaryawan']."</td>
			<td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['nik']."</td>
            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['subbagian']."</td>
            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['tipekaryawan']."</td>";    
    foreach($tanggal as $tang){
        $ting=explode('-',$tang);
        $qwe=date('D', strtotime($tang));
        $stream.="<td bgcolor=#DEDEDE colspan=2 align=center>";
        if($qwe=='Sun')$stream.="<font color=red>".$ting[2]."</font>"; else $stream.= $ting[2]; 
        $stream.="</td>";
    }
    $stream.="<td bgcolor=#DEDEDE colspan=3 align=center>Total</td></tr><tr>";  
    foreach($tanggal as $tang){
              $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jjg']."</td>
                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kg']."</td>
                 ";
    }
    $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jjg']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kg']."</td>
        
		<td bgcolor=#DEDEDE align=center>AVG</td>
        </thead>
	<tbody>";

    // content
    $no=0;
    foreach($dzArr as $arey){ // list isi data on kodeorg
        $no+=1;
		$z=0;
        $stream.="<tr class='rowcontent'>
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
                $stream.="<td align=right><font color=red>".number_format($arey[$tang.'j'])."</font></td>";    
                $stream.="<td align=right><font color=red>".number_format($arey[$tang.'k'])."</font></td>";    
               // $stream.="<td align=right><font color=red>".number_format($arey[$tang.'h'])."</font></td>";    
            }else{
                $stream.="<td align=right>".number_format($arey[$tang.'j'])."</td>";    
                $stream.="<td align=right>".number_format($arey[$tang.'k'])."</td>";    
              //  $stream.="<td align=right>".number_format($arey[$tang.'h'])."</td>";    
            }
            $stream.="</td>";
            $total[$tang.'j']+=$arey[$tang.'j']; // tambahin total bawah
            $total[$tang.'k']+=$arey[$tang.'k']; // tambahin total bawah
           // $total[$tang.'h']+=$arey[$tang.'h']; // tambahin total bawah
            
            $totalj+=$arey[$tang.'j']; // tambahin total kanan
            $totalk+=$arey[$tang.'k']; // tambahin total kanan
           // $totalh+=$arey[$tang.'h']; // tambahin total kanan
		   if($arey[$tang.'j']>0)
			{
				$z+=1;
				}
		   
			
        }
        $stream.="<td align=right>".number_format($totalj)."</td>
            <td align=right>".number_format($totalk)."</td>
           
			<td align=right>".number_format($rincikg)."</td></tr>";
    }
    
    // tampilin total
    $stream.="<tr class='rowcontent' bgcolor=#DEDEDE>
        <td colspan=4 align=center>Total</td>";
    $totalj=0;
    $totalk=0;
    $totalh=0;
    foreach($tanggal as $tang){ // list tanggal
        $stream.="<td align=right>".number_format($total[$tang.'j'])."</td>";   
        $stream.="<td align=right>".number_format($total[$tang.'k'])."</td>";    
        //$stream.="<td align=right>".number_format($total[$tang.'h'])."</td>";    
        $totalj+=$total[$tang.'j']; // tambahin total kanan
        $totalk+=$total[$tang.'k']; // tambahin total kanan
        //$totalh+=$total[$tang.'h']; // tambahin total kanan
    }
    $stream.="<td align=right>".number_format($totalj)."</td>
        <td align=right>".number_format($totalk)."</td>
        <td align=right>".@number_format($totalk/$z)."</td></tr>";
    $stream.="</tbody>
        <tfoot>
        </tfoot>";		 
                
    $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
	
$tglSkrg=date("Ymd");
$nop_="LaporanPanenOrang".$pt."_".$unit."_".$tgl1;
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