<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

// get post =========================================================================
$proses=$_GET['proses'];
$periode=$_POST['periode'];

$kdOrg=$_POST['kdOrg'];
$pendapatan=$_POST['pendapatan'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
if($pendapatan=='')$pendapatan=$_GET['pendapatan'];
if($periode=='')$periode=$_GET['periode'];
if($kdOrg=='')$kdOrg=$_GET['kdOrg'];
if($kdOrg=='')$kdOrg=$_SESSION['empl']['lokasitugas'];

// get namaorganisasi =========================================================================
        $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";	
        $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $nmOrg=$rOrg['namaorganisasi'];
        }
        if(!$nmOrg)$nmOrg=$kdOrg;

// determine begin end =========================================================================
        $lok=substr($kdOrg,0,4); //$_SESSION['empl']['lokasitugas'];
        $sDatez = "select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where periode = '".$periode."' and kodeorg= '".$lok."'";
        $qDatez=mysql_query($sDatez) or die(mysql_error($conn));
        while($rDatez=mysql_fetch_assoc($qDatez))
        {
                $tanggalMulai=$rDatez['tanggalmulai'];
                $tanggalSampai=$rDatez['tanggalsampai'];
        }

 
        
function dates_inbetween($date1, $date2)
{
    $day = 60*60*24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between
    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
    for($x = 1; $x < $days_diff; $x++)
        {
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }
    $dates_array[] = date('Y-m-d',$date2);
    return $dates_array;
}
$tgltgl = dates_inbetween($tanggalMulai, $tanggalSampai);
#ambil data premi


if ($pendapatan=='1'){
	$info="HK";
	//Hk
	/*
	$str="select a.karyawanid,a.tanggal,sum(a.jumlahhk) as pend,b.noktp from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
     where substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal
     order by a.karyawanid"; 
	*/
	$str="select 0 as pend,NULL as noktp,NULL as kodejabatan,NULL as namajabatan";
	
	$sql="select a.karyawanid,a.tanggal,sum(a.jhk) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_kehadiran_new_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal 
     order by a. karyawanid";
	
}else if ($pendapatan=='2'){
	$info="UPAH";
	//Upah
	$str="select a.karyawanid,a.tanggal,sum(a.upahkerja) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where a.rpkgkontanan=0 and substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal
     order by a.karyawanid"; 
	
	$sql="select a.karyawanid,a.tanggal,sum(a.umr) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_kehadiran_new_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal 
     order by a. karyawanid";
	
}else if ($pendapatan=='3'){
	$info="PREMI";
	//Premi
	$str="select a.karyawanid,a.tanggal,sum(a.upahpremi) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal
     order by a.karyawanid"; 
	
	$sql="select a.karyawanid,a.tanggal,sum(a.insentif) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_kehadiran_new_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where substr(a.tanggal,1,7)='".$periode."'  and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal 
     order by a. karyawanid";
}else if ($pendapatan=='4'){
	$info="DENDA";
	//Denda
	$str="select a.karyawanid,a.tanggal,sum(a.rupiahpenalty+a.dendabasis) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where  substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal
     order by a.karyawanid"; 
	$sql="select 0 as pend,NULL as noktp,NULL as kodejabatan,NULL as namajabatan";
	

}else {
	$info="KONTANAN";
	//Kontanan
	$str="select a.karyawanid,a.tanggal,sum(a.upahkerja) as pend,b.noktp,b.kodejabatan,c.namajabatan from ".$dbname.".kebun_prestasi_vw a 
     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	 left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
     where a.rpkgkontanan>0 and substr(a.tanggal,1,7)='".$periode."' and b.lokasitugas='".substr($kdOrg,0,4)."' and (b.tanggalkeluar>'".$tanggalSampai."' or b.tanggalkeluar='0000-00-00')
     group by a.karyawanid,tanggal
     order by a.karyawanid"; 
	 $sql="select 0 as pend,NULL as noktp,NULL as kodejabatan,NULL as namajabatan";
	
	
}

// echo $sql;
$res=mysql_query($str);
$jab=Array();
$prem=Array();
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->karyawanid]=$bar->namajabatan;
    $prem[$bar->karyawanid][$bar->tanggal]=$bar->pend;    
}
$qData=mysql_query($sql);
//
while($rData=mysql_fetch_object($qData))
{
    $jab[$rData->karyawanid]=$rData->namajabatan;
    $prem[$rData->karyawanid][$rData->tanggal]+=$rData->pend;   
}






#ambil karyawan

  $str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".substr($kdOrg,0,4)."'
    and (tanggalkeluar>'".$tanggalSampai."' or tanggalkeluar='0000-00-00')";   



$res=mysql_query($str);
$karid=Array();
while($bar=mysql_fetch_object($res))
{
    if($jab[$bar->karyawanid]!='')#jika terdaftar pada premi maka sertakan
        $karid[$bar->karyawanid]=$bar->namakaryawan;
}
$brd=0;
$bgclr="align='center'";
if($proses=='excel')
{
    $brd=1;
    $bgclr="bgcolor='#DEDEDE' align='center'";
}
$stream="Laporan_".$info."_Pekerja_Harian_".$kdOrg."_".$periode; 
#preview: nampilin header ================================================================================
        $stream.="<table cellspacing='1' border='".$brd."' class='sortable'>
        <thead class=rowheader>
        <tr>
        <td ".$bgclr.">No</td>
        <td ".$bgclr.">".$_SESSION['lang']['nama']."</td>
		<td ".$bgclr.">".$_SESSION['lang']['jabatan']."</td>";
        foreach($tgltgl as $ar => $isi)
        {
                $qwe=date('D', strtotime($isi));
                $stream.="<td width=5px  ".$bgclr.">";
                if($qwe=='Sun')
                    $stream.="<font color=red>".substr($isi,8,2)."</font>"; 
                else 
                    $stream.=(substr($isi,8,2)); 
                $stream.="</td>";
        }
        $stream.="<td ".$bgclr.">Jumlah</td></tr></thead>
        <tbody>";
           # preview: nampilin data ================================================================================
        foreach($karid as $id=>$val)
        {
            $no+=1;
            $totperkar=0;
            $stream.="<tr class=rowcontent><td>".$no."</td>
            <td>".$val."</td>
			<td>".$jab[$id]."</td>";
            foreach($tgltgl as $key=>$tangval)
            {	             
					if ($prem[$id][$tangval]==0){
						$stream.="<td align=right></td>";
					}else{
						$stream.="<td align=right>".number_format($prem[$id][$tangval],2)."</td>";
					}
                    
                    $tottgl[$tangval]+=$prem[$id][$tangval];
                    $totperkar+=$prem[$id][$tangval];
            }
            $stream.="<td align=right>".number_format($totperkar,2)."</td></tr>";
        }  
           # preview: nampilin total ================================================================================
        $stream.="<thead class=rowheader>
        <tr>
        <td colspan=3>Total</td>";
        foreach($tgltgl as $ar => $isi)
        {
                $stream.="<td align=right>".number_format($tottgl[$isi],2)."</td>";
                $total+=$tottgl[$isi];
        }
        $stream.="<td align=right>".number_format($total,2)."</td>";
        $stream.="</tbody></table>";

switch($proses)
{
        case'preview':
          echo $stream;
        break;
        case 'excel':
            $nop_="Laporan_".$info."_Pekerja_Harian_".$kdOrg."_".$periode;
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
       
}    
?>