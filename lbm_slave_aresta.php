<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];

$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($unit==''||$periode=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];

// building array: dzArr (main data) =========================================================================
// as seen on sdm_slave_2prasarana.php
$dzArr=array();

//// cari kegiatan
//$kegiatan="SELECT kodekegiatan, namakegiatan,satuan FROM ".$dbname.".setup_kegiatan WHERE `kelompok` = 'TBM' order by kodekegiatan";
//$query=mysql_query($kegiatan) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kodekegiatan']][kode]=$res['kodekegiatan'];
//    $listkegiatan[$res['kodekegiatan']]=$res['kodekegiatan'];
//    $kamuskegiatan[$res['kodekegiatan']]=$res['namakegiatan'];
//    $kamussatuan[$res['kodekegiatan']]=$res['satuan'];
//}   
//
//// ambil data fisik anggaran setahun
//$str="SELECT * FROM ".$dbname.".bgt_lbm_volume_kebun_vw 
//    WHERE tahunbudget = '".$tahun."' and kebun = '".$unit."'";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][fisangset]=$res['volume'];
//}
//
//$str="SELECT * FROM ".$dbname.".bgt_lbm_porsi_kebun_vw 
//    WHERE tahunbudget = '".$tahun."' and kebun = '".$unit."'";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][fisangbin]=$dzArr[$res['kegiatan']][fisangset]*$res['rp'.$bulan];
//}
//
//// bikin penjumlahan sd bulan ini
//$bulanz=$bulan+0;
//$porsi='(';
//for ($i=1; $i<=$bulanz; $i++)
//{
//    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
//    $porsi.='rp'.$ii.'+';   
//}
//$porsi=substr($porsi,0,-1);
//$porsi.=') as porsi';
//
//// ambil data fisik anggaran sampai dengan bulan ini
//$str="SELECT kegiatan, ".$porsi." FROM ".$dbname.".bgt_lbm_porsi_kebun_vw 
//    WHERE tahunbudget = '".$tahun."' and kebun = '".$unit."'";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][fisangsdb]=$dzArr[$res['kegiatan']][fisangset]*$res['porsi'];
//}
//
//// ambil data fisik realisasi bulan ini
//$str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE tanggal like '".$periode."%' and unit = '".$unit."'
//    GROUP BY kodekegiatan";
//if($afdId!='')
//{
//   $str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE tanggal like '".$periode."%' and kodeorg like '".$afdId."%'
//    GROUP BY kodekegiatan"; 
//}
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kodekegiatan']][fisreabin]=$res['volume'];
//}
//
//// ambil data fisik realisasi sampai dengan bulan ini
//$str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and unit = '".$unit."'
//    GROUP BY kodekegiatan";
//if($afdId!='')
//{
//    $str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%'
//    GROUP BY kodekegiatan";
//}
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kodekegiatan']][fisreasdb]=$res['volume'];
//}
//
//// ambil data hkhm anggaran setahun
//$str="SELECT kegiatan, (hm01+hm02+hm03+hm04+hm05+hm06+hm07+hm08+hm09+hm10+hm11+hm12) as hkhm FROM ".$dbname.".bgt_hkhm_per_kegiatan_kebun_vw 
//    WHERE tahunbudget='".$tahun."' and kebun = '".$unit."'
//    ";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][hkmangset]=$res['hkhm'];
//}
//
//// ambil data hkhm anggaran bulan ini
//$str="SELECT kegiatan, hm".$bulan." as hkhm FROM ".$dbname.".bgt_hkhm_per_kegiatan_kebun_vw 
//    WHERE tahunbudget='".$tahun."' and kebun = '".$unit."'
//    ";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][hkmangbin]=$res['hkhm'];
//}
//
//// bikin penjumlahan sd bulan ini
//$bulanz=$bulan+0;
//$porsi='(';
//for ($i=1; $i<=$bulanz; $i++)
//{
//    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
//    $porsi.='hm'.$ii.'+';   
//}
//$porsi=substr($porsi,0,-1);
//$porsi.=') as hkhm';
//
//// ambil data hkhm anggaran sampai dengan bulan ini
//$str="SELECT kegiatan, ".$porsi." FROM ".$dbname.".bgt_hkhm_per_kegiatan_kebun_vw 
//    WHERE tahunbudget='".$tahun."' and kebun = '".$unit."'
//    ";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][hkmangsdb]=$res['hkhm'];
//}
//
//// ambil data hkhm realisasi bulan ini
//$str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE tanggal like '".$periode."%' and unit = '".$unit."'
//    GROUP BY kodekegiatan";
//if($afdId!='')
//{
//    $str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE tanggal like '".$periode."%' and kodeorg like '".$afdId."%'
//    GROUP BY kodekegiatan";
//}
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kodekegiatan']][hkmreabin]=$res['jhk'];
//}
////$str="SELECT kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt_vw 
////    WHERE tanggal like '".$periode."%' and alokasibiaya like '".$unit."%'
////    GROUP BY kegiatan";
//$str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
//    LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
//    LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
//    LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
//    WHERE tanggal like '".$periode."%' and alokasibiaya like '".$unit."%'
//    GROUP BY d.kodekegiatan";
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][hkmreabin]+=$res['jumlah'];
//}
//
//// ambil data hkhm realisasi sampai dengan bulan ini
//$str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and unit = '".$unit."'
//    GROUP BY kodekegiatan";
//if($afdId!='')
//{
//    $str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
//    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%'
//    GROUP BY kodekegiatan";
//}
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kodekegiatan']][hkmreasdb]=$res['jhk'];
//}
////$str="SELECT kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt_vw 
////    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and alokasibiaya like '".$unit."%'
////    GROUP BY kegiatan";
//$str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
//      LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
//      LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
//      LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
//     WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and alokasibiaya like '".$unit."%'
//     GROUP BY d.kodekegiatan";
//if($afdId!='')
//{
//    $str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
//      LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
//      LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
//      LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
//     WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and alokasibiaya like '".$afdId."%'
//     GROUP BY d.kodekegiatan";
//}
//$query=mysql_query($str) or die(mysql_error($conn));
//while($res=mysql_fetch_assoc($query))
//{
//    $dzArr[$res['kegiatan']][hkmreasdb]+=$res['jumlah'];
//}

//echo "<pre>";
//print_r($dzArr);
//echo "</pre>";

//if(!empty($listkegiatan))foreach($listkegiatan as $keg){
//    @$dzArr[$keg][fispenset]=$dzArr[$keg][fisreasdb]/$dzArr[$keg][fisangset]*100;
//    @$dzArr[$keg][fispensdb]=$dzArr[$keg][fisreasdb]/$dzArr[$keg][fisangsdb]*100;
//    @$dzArr[$keg][hkmpenset]=$dzArr[$keg][hkmreasdb]/$dzArr[$keg][hkmangset]*100;
//    @$dzArr[$keg][hkmpensdb]=$dzArr[$keg][hkmreasdb]/$dzArr[$keg][hkmangsdb]*100;
//    @$dzArr[$keg][humangset]=$dzArr[$keg][hkmangset]/$dzArr[$keg][fisangset];
//    @$dzArr[$keg][humangbin]=$dzArr[$keg][hkmangbin]/$dzArr[$keg][fisangbin];
//    @$dzArr[$keg][humangsdb]=$dzArr[$keg][hkmangsdb]/$dzArr[$keg][fisangsdb];
//    @$dzArr[$keg][humreabin]=$dzArr[$keg][hkmreabin]/$dzArr[$keg][fisreabin];
//    @$dzArr[$keg][humreasdb]=$dzArr[$keg][hkmreasdb]/$dzArr[$keg][fisreasdb];
//}

function numberformat($qwe,$asd)
{
    if($qwe==0)$zxc='0'; 
    else{
        $zxc=number_format($qwe,$asd);
    }
    return $zxc;
}        

// list afdeling + jumlah afdeling
$jumlah_af=0;
$qwery="SELECT kodeorganisasi FROM ".$dbname.".organisasi 
    WHERE induk = '".$unit."' and tipe in ('AFDELING', 'BIBITAN') and kodeorganisasi like '".$afdId."%'
    ORDER by kodeorganisasi";
$query=mysql_query($qwery) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $af[$res['kodeorganisasi']]=$res['kodeorganisasi'];
    $jumlah_af+=1;
}  

// 1 & 2 & 4
$qwery="SELECT luasareaproduktif,jumlahpokok,statusblok,substr(kodeorg,1,6) as kodeorg,tahuntanam,
        cadangan, okupasi, rendahan, sungai, rumah, kantor, pabrik, jalan, kolam, umum
    FROM ".$dbname.".setup_blok 
    WHERE kodeorg like '".$unit."%'  and kodeorg like '".$afdId."%'";
$query=mysql_query($qwery) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if($res['statusblok']=='TM'){
        $tttm[$res['tahuntanam']]=$res['tahuntanam'];
        $luastm[$res['kodeorg']][$res['tahuntanam']]+=$res['luasareaproduktif'];
        $pokoktm[$res['kodeorg']][$res['tahuntanam']]+=$res['jumlahpokok'];
    }    
    if($res['statusblok']=='TBM'){
        $tttbm[$res['tahuntanam']]=$res['tahuntanam'];
        $luastbm[$res['kodeorg']][$res['tahuntanam']]+=$res['luasareaproduktif'];
        $pokoktbm[$res['kodeorg']][$res['tahuntanam']]+=$res['jumlahpokok'];
    }    
//    if($res['statusblok']=='TB'){
//        $luaslc[$res['kodeorg']]+=$res['luasareaproduktif'];
//        $pokoklc[$res['kodeorg']]+=$res['jumlahpokok'];
//    }    
    if($res['statusblok']=='BBT'){
        $luasbt[$res['kodeorg']]+=$res['luasareaproduktif'];
        $pokokbt[$res['kodeorg']]+=$res['jumlahpokok'];
    }    
    $luascada[$res['kodeorg']]+=$res['cadangan'];
    $luasokup[$res['kodeorg']]+=$res['okupasi'];
    $luasrend[$res['kodeorg']]+=$res['rendahan'];
    $luassung[$res['kodeorg']]+=$res['sungai'];
    $luasruma[$res['kodeorg']]+=$res['rumah'];
    $luaskant[$res['kodeorg']]+=$res['kantor'];
    $luaspabr[$res['kodeorg']]+=$res['pabrik'];
    $luasjala[$res['kodeorg']]+=$res['jalan'];
    $luaskola[$res['kodeorg']]+=$res['kolam'];
    $luasumum[$res['kodeorg']]+=$res['umum'];
}  

// 3 ####indra
$qwery="SELECT hasilkerja,kodekegiatan,substr(kodeorg,1,6) as kodeorg FROM ".$dbname.". kebun_perawatan_vw 
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%' and kodekegiatan='122040802'";
	
$query=mysql_query($qwery) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $pokoktb[$res['kodeorg']]+=$res['hasilkerja'];        
//    if($res['kodekegiatan']==('126050504'||'621070101')){
//        $pokoksp[$res['kodeorg']]+=$res['hasilkerja'];        
//    }
}  


if(!empty($tttm))sort($tttm);
if(!empty($tttbm))sort($tttbm);
$jumlah_tttm=count($tttm);
$jumlah_tttbm=count($tttbm);

//echo $qwery."<pre>";
//print_r($luasumum);
//echo "</pre>";

if($proses!='pdf'){
    

if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=20 align=left><font size=3>01. AREAL STATEMENT</font> ".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=20 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>";
if($afdId!='')
{
    $tab.="<tr><td colspan=2 align=left>".$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")</td></tr>";
}
$tab.="</table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['nomor']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['uraian']."</td>
    <td align=center colspan=".($jumlah_af*2)." ".$bg.">".$_SESSION['lang']['lokasi']."</td>
    <td align=center colspan=2".$bg.">".$_SESSION['lang']['total']."</td>
    </tr>
    <tr>";
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=center colspan=2 ".$bg.">".$optNm[$afdeling]."</td>";        
    }
    $tab.="<td align=center rowspan=2".$bg.">".$_SESSION['lang']['luas']."<br>(Ha)</td>
    <td align=center rowspan=2".$bg.">".$_SESSION['lang']['pokok']."<br>(pkk)</td>
    </tr>
    <tr>";
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=center ".$bg.">Ha</td>";
        $tab.="<td align=center ".$bg.">pkk</td>";        
    }
    $tab.="
    </tr>
    </thead>
    <tbody>
";

// 1. TANAMAN MENGHASILKAN    
$tab.="<tr class=rowcontent>";
    $tab.="<td rowspan=".($jumlah_tttm+2)." valign=top align=center>1.</td>";
    $tab.="<td colspan=".(($jumlah_af*2)+3).">Tanaman Menghasilkan</td>";
$tab.="</tr>";
if(!empty($tttm))foreach($tttm as $tahuntanam){
    $tab.="<tr class=rowcontent>";
        $tab.="<td align=center>".$tahuntanam."</td>";        
        if(!empty($af))foreach($af as $afdeling){
            $tab.="<td align=right>".number_format($luastm[$afdeling][$tahuntanam],2)."</td>";
            $tab.="<td align=right>".number_format($pokoktm[$afdeling][$tahuntanam])."</td>";
            $totalha_tm_tt[$tahuntanam]+=$luastm[$afdeling][$tahuntanam]; // luas tm per tt
            $totalha_tm_af[$afdeling]+=$luastm[$afdeling][$tahuntanam]; // luas tm per afd
            $totalha_af[$afdeling]+=$luastm[$afdeling][$tahuntanam]; // luas per afd
            $totalpk_tm_tt[$tahuntanam]+=$pokoktm[$afdeling][$tahuntanam];
            $totalpk_tm_af[$afdeling]+=$pokoktm[$afdeling][$tahuntanam];
            $totalpk_af[$afdeling]+=$pokoktm[$afdeling][$tahuntanam];
        }
        $tab.="<td align=right>".number_format($totalha_tm_tt[$tahuntanam],2)."</td>";
        $tab.="<td align=right>".number_format($totalpk_tm_tt[$tahuntanam])."</td>";
    $tab.="</tr>";
}
$tab.="<tr class=rowcontent>";
$tab.="<td align=center>subtotal</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_tm_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_tm_af[$afdeling])."</td>";
        $totalha_tm+=$totalha_tm_af[$afdeling]; // subtotal tm per afd
        $totalpk_tm+=$totalpk_tm_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha_tm,2)."</td>";
$tab.="<td align=right>".number_format($totalpk_tm)."</td>";
$tab.="</tr>";

// 2. TANAMAN BELUM MENGHASILKAN    
$tab.="<tr class=rowcontent>";
    $tab.="<td rowspan=".($jumlah_tttbm+2)." valign=top align=center>2.</td>";
    $tab.="<td colspan=".(($jumlah_af*2)+3).">Tanaman Belum Menghasilkan</td>";
$tab.="</tr>";
if(!empty($tttbm))foreach($tttbm as $tahuntanam){
    $tab.="<tr class=rowcontent>";
        $tab.="<td align=center>".$tahuntanam."</td>";        
        if(!empty($af))foreach($af as $afdeling){
            $tab.="<td align=right>".number_format($luastbm[$afdeling][$tahuntanam],2)."</td>";
            $tab.="<td align=right>".number_format($pokoktbm[$afdeling][$tahuntanam])."</td>";
            $totalha_tbm_tt[$tahuntanam]+=$luastbm[$afdeling][$tahuntanam]; // luas tbm per tt
            $totalha_tbm_af[$afdeling]+=$luastbm[$afdeling][$tahuntanam]; // luas tbm per afd
            $totalha_af[$afdeling]+=$luastbm[$afdeling][$tahuntanam]; // luas per afd
            $totalpk_tbm_tt[$tahuntanam]+=$pokoktbm[$afdeling][$tahuntanam];
            $totalpk_tbm_af[$afdeling]+=$pokoktbm[$afdeling][$tahuntanam];
            $totalpk_af[$afdeling]+=$pokoktbm[$afdeling][$tahuntanam];
        }
        $tab.="<td align=right>".number_format($totalha_tbm_tt[$tahuntanam],2)."</td>";
        $tab.="<td align=right>".number_format($totalpk_tbm_tt[$tahuntanam])."</td>";
    $tab.="</tr>";
}
$tab.="<tr class=rowcontent>";
$tab.="<td align=center>subtotal</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_tbm_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_tbm_af[$afdeling])."</td>";
        $totalha_tbm+=$totalha_tbm_af[$afdeling]; // subtotal tbm per afd
        $totalpk_tbm+=$totalpk_tbm_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha_tbm,2)."</td>";
$tab.="<td align=right>".number_format($totalpk_tbm)."</td>";
$tab.="</tr>";
    
// TOTAL TANAMAN
$totalha=0;
$totalpk=0;
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=2 align=center>Total Tanaman</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_af[$afdeling])."</td>";
        $totalha+=$totalha_af[$afdeling]; // luas
        $totalpk+=$totalpk_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha,2)."</td>";
$tab.="<td align=right>".number_format($totalpk)."</td>";
$tab.="</tr>";
    
// 3. LC DAN TANAM    
#standar pokok/ha
$ssph=143;
$tab.="<tr class=rowcontent>";
    $tab.="<td rowspan=2 valign=top align=center>3.</td>";
    $tab.="<td colspan=".(($jumlah_af*2)+3).">LC dan Tanam</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Tanam Baru</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $luassph=$pokoktb[$afdeling]/$ssph; // luas tanam
        $tab.="<td align=right>".number_format($luassph,2)."</td>";
        $tab.="<td align=right>".number_format($pokoktb[$afdeling])."</td>";
        $totalha_tb+=$luassph; // subtotal tanam
        $totalpk_tb+=$pokoktb[$afdeling];
        $totalha_af[$afdeling]+=$luassph; // luas per afd
        $totalpk_af[$afdeling]+=$pokoktb[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_tb,2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_tb)."</td>";
$tab.="</tr>";
    
// TOTAL
$totalha=0;
$totalpk=0;
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=2 align=center>Total</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_af[$afdeling])."</td>";
    $totalha+=$totalha_af[$afdeling]; // luas
    $totalpk+=$totalpk_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha,2)."</td>";
$tab.="<td align=right>".number_format($totalpk)."</td>";
$tab.="</tr>";
    
// 4. BIBITAN    
$tab.="<tr class=rowcontent>";
    $tab.="<td rowspan=2 valign=top align=center>4.</td>";
    $tab.="<td colspan=".(($jumlah_af*2)+3).">Bibitan</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Areal Pembibitan</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luasbt[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format($pokokbt[$afdeling])."</td>";
        $totalha_bt+=$luasbt[$afdeling]; // subtotal bibitan
        $totalpk_bt+=$pokokbt[$afdeling];
        $totalha_af[$afdeling]+=$luasbt[$afdeling]; // luas per afd
        $totalpk_af[$afdeling]+=$pokokbt[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_bt,2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_bt)."</td>";
$tab.="</tr>";

// TOTAL + BIBITAN
$totalha=0;
$totalpk=0;
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=2 align=center>Areal Dapat Ditanam</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_af[$afdeling])."</td>";
    $totalha+=$totalha_af[$afdeling]; // luas
    $totalpk+=$totalpk_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha,2)."</td>";
$tab.="<td align=right>".number_format($totalpk)."</td>";
$tab.="</tr>";

// 5. AREAL PRASARANA    
$tab.="<tr class=rowcontent>";
    $tab.="<td rowspan=11 valign=top align=center>5.</td>";
    $tab.="<td colspan=".(($jumlah_af*2)+3).">Areal Prasarana</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Rumah</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luasruma[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_ruma+=$luasruma[$afdeling];
        $totalha_pr[$afdeling]+=$luasruma[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_ruma,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Kantor</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luaskant[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_kant+=$luaskant[$afdeling];
        $totalha_pr[$afdeling]+=$luaskant[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_kant,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Pabrik</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luaspabr[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_pabr+=$luaspabr[$afdeling];
        $totalha_pr[$afdeling]+=$luaspabr[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_pabr,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Jalan</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luasjala[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_jala+=$luasjala[$afdeling];
        $totalha_pr[$afdeling]+=$luasjala[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_jala,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Kolam</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luaskola[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_kola+=$luaskola[$afdeling];
        $totalha_pr[$afdeling]+=$luaskola[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_kola,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Umum</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luasumum[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_umum+=$luasumum[$afdeling];
        $totalha_pr[$afdeling]+=$luasumum[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_umum,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Sungai</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luassung[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_sung+=$luassung[$afdeling];
        $totalha_pr[$afdeling]+=$luassung[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_sung,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Cadangan</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luascada[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_cada+=$luascada[$afdeling];
        $totalha_pr[$afdeling]+=$luascada[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_cada,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Rendahan</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luasrend[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_rend+=$luasrend[$afdeling];
        $totalha_pr[$afdeling]+=$luasrend[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_rend,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";

// subtotal
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>subtotal</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($totalha_pr[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_pras+=$totalha_pr[$afdeling];
        $totalha_af[$afdeling]+=$totalha_pr[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_pras,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";

// TOTAL + BIBITAN + PPRASARANA
$totalha=0;
$totalpk=0;
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=2 align=center>Total Areal Diusahakan</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_af[$afdeling])."</td>";
    $totalha+=$totalha_af[$afdeling];
    $totalpk+=$totalpk_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha,2)."</td>";
$tab.="<td align=right>".number_format($totalpk)."</td>";
$tab.="</tr>";

// 6. AREAL OKUPASI    
$tab.="<tr class=rowcontent>";
    $tab.="<td rowspan=3 valign=top align=center>6.</td>";
    $tab.="<td colspan=".(($jumlah_af*2)+3).">Areal Okupasi</td>";
$tab.="</tr>";
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>Okupasi</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($luasokup[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_okup+=$luasokup[$afdeling];
        $totalha_ok[$afdeling]+=$luasokup[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_okup,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";

// subtotal
$tab.="<tr class=rowcontent>";
    $tab.="<td align=left>subtotal</td>";        
    if(!empty($af))foreach($af as $afdeling){
        $tab.="<td align=right>".number_format($totalha_ok[$afdeling],2)."</td>";
        $tab.="<td align=right>".number_format(0)."</td>";
        $totalha_okup+=$totalha_ok[$afdeling];
        $totalha_af[$afdeling]+=$totalha_ok[$afdeling];
    }
    $tab.="<td align=right>".number_format($totalha_okup,2)."</td>";
    $tab.="<td align=right>".number_format(0)."</td>";
$tab.="</tr>";

// TOTAL + BIBITAN + PPRASARANA + OKUPASI
$totalha=0;
$totalpk=0;
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=2 align=center>Grand Total</td>";        
if(!empty($af))foreach($af as $afdeling){
    $tab.="<td align=right>".number_format($totalha_af[$afdeling],2)."</td>";
    $tab.="<td align=right>".number_format($totalpk_af[$afdeling])."</td>";
    $totalha+=$totalha_af[$afdeling];
    $totalpk+=$totalpk_af[$afdeling];
}
$tab.="<td align=right>".number_format($totalha,2)."</td>";
$tab.="<td align=right>".number_format($totalpk)."</td>";
$tab.="</tr>";
    
    
    $dummy='';
// excel array content =========================================================================
    if(empty($totalha_af)){
        $tab.="<tr class=rowcontent><td colspan=".(($jumlah_af*2)+4).">Data Empty.</td></tr>";
    }
    $tab.="</tbody></table>";
} // end of if proses!=pdf
			
switch($proses)
{
    case'preview':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    echo $tab;
    break;

    case'excel':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="lbm_aresta_".$unit.$periode;
    if(strlen($tab)>0)
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
        if(!fwrite($handle,$tab))
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

    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

            $cols=247.5;
            $wkiri=10;
            $wlain=5;

    class PDF extends FPDF {
    function Header() {
        global $periode;
        global $unit;
        global $optNm;
        global $optBulan;
        global $tahun;
        global $bulan;
        global $dbname;
        global $luas;
        global $wkiri, $wlain,$afdId,$af,$jumlah_af;
            $width = $this->w - $this->lMargin - $this->rMargin;
  
        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);

        $this->Cell($width/2,$height,'01. AREAL STATEMENT ',NULL,0,'L',1);
        $this->Cell($width/2,$height,$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun,NULL,0,'R',1);
        $this->Ln();
        $this->Cell($width,$height,$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")",NULL,0,'L',1);
        if($afdId!='')
        {
                $this->Ln();
                $this->Cell($width,$height,$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")",NULL,0,'L',1);
        }
        $this->Ln();
        $this->Ln();
 
        $height = 15;
        $this->SetFont('Arial','B',7);

        $this->Cell($wlain/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wkiri/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wlain*$jumlah_af*2/100*$width,$height,$_SESSION['lang']['lokasi'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
        $this->Ln();

        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['nomor'],RL,0,'C',1);	
        $this->Cell($wkiri/100*$width,$height,$_SESSION['lang']['uraian'],RL,0,'C',1);
        if(!empty($af))foreach($af as $afdeling){
            $this->Cell($wlain*2/100*$width,$height,$optNm[$afdeling],1,0,'C',1);
        }        
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['luas'],TRL,0,'C',1);
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['pokok'],TRL,0,'C',1);        	
        $this->Ln();
        
        $this->Cell($wlain/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wkiri/100*$width,$height,'',BRL,0,'C',1);	
        if(!empty($af))foreach($af as $afdeling){
            $this->Cell($wlain/100*$width,$height,'ha',1,0,'C',1);
            $this->Cell($wlain/100*$width,$height,'pkk',1,0,'C',1);
        }        
        $this->Cell($wlain/100*$width,$height,'ha',BRL,0,'C',1);
        $this->Cell($wlain/100*$width,$height,'pkk',BRL,0,'C',1);        	
        $this->Ln();
        
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
}
    //================================

$pdf=new PDF('L','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',7);
    
// pdf array content =========================================================================

// 1. TANAMAN MENGHASILKAN        
$pdf->Cell($wlain/100*$width,$height,'1.',TRL,0,'R',1);	
$pdf->Cell(($wkiri+($wlain*$jumlah_af*2)+($wlain*2))/100*$width,$height,'Tanaman Menghasilkan',1,0,'L',1);	
$pdf->Ln();
if(!empty($tttm))foreach($tttm as $tahuntanam){
    $pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,$tahuntanam,1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
        $pdf->Cell($wlain/100*$width,$height,number_format($luastm[$afdeling][$tahuntanam],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($pokoktm[$afdeling][$tahuntanam]),1,0,'R',1);	
        $totalha_tm_tt[$tahuntanam]+=$luastm[$afdeling][$tahuntanam]; // luas tm per tt
        $totalha_tm_af[$afdeling]+=$luastm[$afdeling][$tahuntanam]; // luas tm per afd
        $totalha_af[$afdeling]+=$luastm[$afdeling][$tahuntanam]; // luas per afd
        $totalpk_tm_tt[$tahuntanam]+=$pokoktm[$afdeling][$tahuntanam];
        $totalpk_tm_af[$afdeling]+=$pokoktm[$afdeling][$tahuntanam];
        $totalpk_af[$afdeling]+=$pokoktm[$afdeling][$tahuntanam];
    }
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_tm_tt[$tahuntanam],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tm_tt[$tahuntanam]),1,0,'R',1);	
    $pdf->Ln();
}
$pdf->Cell($wlain/100*$width,$height,'',BRL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'subtotal',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_tm_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tm_af[$afdeling]),1,0,'R',1);	
    $totalha_tm+=$totalha_tm_af[$afdeling]; // subtotal tm per afd
    $totalpk_tm+=$totalpk_tm_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_tm,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tm),1,0,'R',1);	
$pdf->Ln();

// 2. TANAMAN BELUM MENGHASILKAN    
$pdf->Cell($wlain/100*$width,$height,'2.',TRL,0,'R',1);	
$pdf->Cell(($wkiri+($wlain*$jumlah_af*2)+($wlain*2))/100*$width,$height,'Tanaman Belum Menghasilkan',1,0,'L',1);	
$pdf->Ln();
if(!empty($tttbm))foreach($tttbm as $tahuntanam){
    $pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,$tahuntanam,1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
        $pdf->Cell($wlain/100*$width,$height,number_format($luastbm[$afdeling][$tahuntanam],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($pokoktbm[$afdeling][$tahuntanam]),1,0,'R',1);	
        $totalha_tbm_tt[$tahuntanam]+=$luastbm[$afdeling][$tahuntanam]; // luas tbm per tt
        $totalha_tbm_af[$afdeling]+=$luastbm[$afdeling][$tahuntanam]; // luas tbm per afd
        $totalha_af[$afdeling]+=$luastbm[$afdeling][$tahuntanam]; // luas per afd
        $totalpk_tbm_tt[$tahuntanam]+=$pokoktbm[$afdeling][$tahuntanam];
        $totalpk_tbm_af[$afdeling]+=$pokoktbm[$afdeling][$tahuntanam];
        $totalpk_af[$afdeling]+=$pokoktbm[$afdeling][$tahuntanam];
    }
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_tbm_tt[$tahuntanam],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tbm_tt[$tahuntanam]),1,0,'R',1);	
    $pdf->Ln();
}
$pdf->Cell($wlain/100*$width,$height,'',BRL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'subtotal',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_tbm_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tbm_af[$afdeling]),1,0,'R',1);	
        $totalha_tbm+=$totalha_tbm_af[$afdeling]; // subtotal tbm per afd
        $totalpk_tbm+=$totalpk_tbm_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_tbm,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tbm),1,0,'R',1);	
$pdf->Ln();
    
// TOTAL TANAMAN
$totalha=0;
$totalpk=0;
$pdf->Cell(($wlain+$wkiri)/100*$width,$height,'Total Tanaman',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_af[$afdeling]),1,0,'R',1);	
    $totalha+=$totalha_af[$afdeling]; // luas
    $totalpk+=$totalpk_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk),1,0,'R',1);	
$pdf->Ln();

// 3. LC DAN TANAM    
#standar pokok/ha
$ssph=143;
$pdf->Cell($wlain/100*$width,$height,'3.',TRL,0,'R',1);	
$pdf->Cell(($wkiri+($wlain*$jumlah_af*2)+($wlain*2))/100*$width,$height,'LC dan Tanam',1,0,'L',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Tanam Baru',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
        $luassph=$pokoktb[$afdeling]/$ssph; // luas tanam
        $pdf->Cell($wlain/100*$width,$height,number_format($luassph,2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,number_format($pokoktb[$afdeling]),1,0,'R',1);	
        $totalha_tb+=$luassph; // subtotal tanam
        $totalpk_tb+=$pokoktb[$afdeling];
        $totalha_af[$afdeling]+=$luassph; // luas per afd
        $totalpk_af[$afdeling]+=$pokoktb[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_tb,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk_tb),1,0,'R',1);	
$pdf->Ln();
    
// TOTAL
$totalha=0;
$totalpk=0;
$pdf->Cell(($wlain+$wkiri)/100*$width,$height,'Total',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_af[$afdeling]),1,0,'R',1);	
    $totalha+=$totalha_af[$afdeling]; // luas
    $totalpk+=$totalpk_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk),1,0,'R',1);	
$pdf->Ln();
    
// 4. BIBITAN    
$pdf->Cell($wlain/100*$width,$height,'4.',TRL,0,'R',1);	
$pdf->Cell(($wkiri+($wlain*$jumlah_af*2)+($wlain*2))/100*$width,$height,'Bibitan',1,0,'L',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Areal Pembibitan',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luasbt[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($pokokbt[$afdeling]),1,0,'R',1);	
        $totalha_bt+=$luasbt[$afdeling]; // subtotal bibitan
        $totalpk_bt+=$pokokbt[$afdeling];
        $totalha_af[$afdeling]+=$luasbt[$afdeling]; // luas per afd
        $totalpk_af[$afdeling]+=$pokokbt[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_bt,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk_bt),1,0,'R',1);	
$pdf->Ln();

// TOTAL + BIBITAN
$totalha=0;
$totalpk=0;
$pdf->Cell(($wlain+$wkiri)/100*$width,$height,'Areal Dapat Ditanam',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_af[$afdeling]),1,0,'R',1);	
    $totalha+=$totalha_af[$afdeling]; // luas
    $totalpk+=$totalpk_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk),1,0,'R',1);	
$pdf->Ln();

// 5. AREAL PRASARANA    
$pdf->Cell($wlain/100*$width,$height,'5.',TRL,0,'R',1);	
$pdf->Cell(($wkiri+($wlain*$jumlah_af*2)+($wlain*2))/100*$width,$height,'Areal Prasarana',1,0,'L',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Rumah',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luasruma[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_ruma+=$luasruma[$afdeling];
        $totalha_pr[$afdeling]+=$luasruma[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_ruma,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Kantor',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luaskant[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_kant+=$luaskant[$afdeling];
        $totalha_pr[$afdeling]+=$luaskant[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_kant,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Pabrik',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luaspabr[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_pabr+=$luaspabr[$afdeling];
        $totalha_pr[$afdeling]+=$luaspabr[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_pabr,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Jalan',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luasjala[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_jala+=$luasjala[$afdeling];
        $totalha_pr[$afdeling]+=$luasjala[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_jala,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Kolam',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luaskola[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_kola+=$luaskola[$afdeling];
        $totalha_pr[$afdeling]+=$luaskola[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_kola,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Umum',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luasumum[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_umum+=$luasumum[$afdeling];
        $totalha_pr[$afdeling]+=$luasumum[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_umum,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Sungai',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luassung[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_sung+=$luassung[$afdeling];
        $totalha_pr[$afdeling]+=$luassung[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_sung,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Cadangan',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luascada[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_cada+=$luascada[$afdeling];
        $totalha_pr[$afdeling]+=$luascada[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_cada,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Rendahan',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luasrend[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_rend+=$luasrend[$afdeling];
        $totalha_pr[$afdeling]+=$luasrend[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_rend,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();

// subtotal
$pdf->Cell($wlain/100*$width,$height,'',BRL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'subtotal',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_pr[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_pras+=$totalha_pr[$afdeling];
        $totalha_af[$afdeling]+=$totalha_pr[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_pras,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();

// TOTAL + BIBITAN + PPRASARANA
$totalha=0;
$totalpk=0;
$pdf->Cell(($wlain+$wkiri)/100*$width,$height,'Total Areal Diusahakan',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_af[$afdeling]),1,0,'R',1);	
    $totalha+=$totalha_af[$afdeling];
    $totalpk+=$totalpk_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk),1,0,'R',1);	
$pdf->Ln();

// 6. AREAL OKUPASI    
$pdf->Cell($wlain/100*$width,$height,'6.',TRL,0,'R',1);	
$pdf->Cell(($wkiri+($wlain*$jumlah_af*2)+($wlain*2))/100*$width,$height,'Areal Okupasi',1,0,'L',1);	
$pdf->Ln();
$pdf->Cell($wlain/100*$width,$height,'',RL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'Okupasi',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($luasokup[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_okup+=$luasokup[$afdeling];
        $totalha_ok[$afdeling]+=$luasokup[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_okup,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();

// subtotal
$pdf->Cell($wlain/100*$width,$height,'',BRL,0,'R',1);	
$pdf->Cell($wkiri/100*$width,$height,'subtotal',1,0,'C',1);	
    if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_ok[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
        $totalha_okup+=$totalha_ok[$afdeling];
        $totalha_af[$afdeling]+=$totalha_ok[$afdeling];
    }
$pdf->Cell($wlain/100*$width,$height,number_format($totalha_okup,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format(0),1,0,'R',1);	
$pdf->Ln();

// TOTAL + BIBITAN + PPRASARANA + OKUPASI
$totalha=0;
$totalpk=0;
$pdf->Cell(($wlain+$wkiri)/100*$width,$height,'Grand Total',1,0,'C',1);	
if(!empty($af))foreach($af as $afdeling){
    $pdf->Cell($wlain/100*$width,$height,number_format($totalha_af[$afdeling],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($totalpk_af[$afdeling]),1,0,'R',1);	
    $totalha+=$totalha_af[$afdeling];
    $totalpk+=$totalpk_af[$afdeling];
}
$pdf->Cell($wlain/100*$width,$height,number_format($totalha,2),1,0,'R',1);	
$pdf->Cell($wlain/100*$width,$height,number_format($totalpk),1,0,'R',1);	
$pdf->Ln();

//    if(!empty($listkegiatan))foreach($listkegiatan as $keg){
//        $pdf->Cell($wkiri/100*$width,$height,$kamuskegiatan[$keg],1,0,'L',1);	
//        $pdf->Cell($wlain/100*$width,$height,$kamussatuan[$keg],1,0,'L',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisangset],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisangbin],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisangsdb],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisreabin],0),1,0,'R',1);
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisreasdb],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fispenset],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fispensdb],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmangset],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmangbin],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmangsdb],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmreabin],0),1,0,'R',1);
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmreasdb],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmpenset],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmpensdb],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humangset],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humangbin],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humangsdb],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humreabin],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humreasdb],2),1,0,'R',1);	
//        $pdf->Ln();
//    }
    
    $pdf->Output();	 
    break;

    default:
    break;
}
	
?>
