<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_GET['pt'];
$gudang=$_GET['gudang'];
$tanggal1=$_GET['tanggal1'];
$tanggal2=$_GET['tanggal2'];
$akundari=$_GET['akundari'];
$akunsampai=$_GET['akunsampai'];
        
//          echo $pt." ".$gudang." ".$tanggal1." ".$tanggal2." ".$akundari." ".$akunsampai."<br>"; exit;

//$periode buat filter keu_saldobulanan, $bulan buat nentuin field-nya
$qwe=explode("-",$tanggal1);
$periode=$qwe[2].$qwe[1];
$bulan=$qwe[1];

//balik tanggal
$qwe=explode("-",$tanggal1);
$tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggal2);
$tanggal2=$qwe[2]."-".$qwe[1]."-".$qwe[0];

//ambil saldo awal
if($gudang==''){
    $str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'";
    $wheregudang='';
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
	$wheregudang.="'".strtoupper($bar->kodeorganisasi)."',";
    }
    $wheregudang="and kodeorg in (".substr($wheregudang,0,-1).") ";
}else{
    $wheregudang="and kodeorg = '".$gudang."' ";
}
$str="select * from ".$dbname.".keu_saldobulanan where periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun";
//          echo $str."<br>";
//          echo $wheregudang."<br>";
//$saldoawal=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $qwe="awal".$bulan;
    $saldoawal[$bar->noakun]+=$bar->$qwe;
}
//        echo "<pre>";
//        print_r($saldoawal);
//        echo "</pre>";

// ambil data
    $isidata=array();
$str="select * from ".$dbname.".keu_jurnaldt_vw where tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun, tanggal";
//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe][nojur]=$bar->nojurnal;
    $isidata[$qwe][tangg]=$bar->tanggal;
    $isidata[$qwe][noaku]=$bar->noakun;
    $isidata[$qwe][keter]=$bar->keterangan;
    $isidata[$qwe][debet]=$bar->debet;
    $isidata[$qwe][kredi]=$bar->kredit;
    $isidata[$qwe][kodeb]=$bar->kodeblok;
    $isidata[$qwe][noref]=$bar->noreferensi;
}
//        echo "<pre>";
//        print_r($isidata);
//        echo "</pre>";

    $str="select noakun,namaakun from ".$dbname.".keu_5akun
                    where level = '5'
                    order by noakun
                    ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $namaakun[$bar->noakun]=$bar->namaakun;
    }

    if(!empty($isidata)) foreach($isidata as $c=>$key) {
        $sort_noaku[] = $key['noaku'];
        $sort_tangg[] = $key['tangg'];
        $sort_debet[] = $key['debet'];
        $sort_nojur[] = $key['nojur'];
    }else{
        echo "Data tidak ditemukan.";
        exit;
    }

    array_multisort($sort_noaku, SORT_ASC, $sort_tangg, SORT_ASC, $sort_debet, SORT_DESC, $sort_nojur, SORT_ASC, $isidata);


//function aasort (&$array, $key) {
//    $sorter=array();
//    $ret=array();
//    reset($array);
//    foreach ($array as $ii => $va) {
//        $sorter[$ii]=$va[$key];
//    }
//    asort($sorter);
//    foreach ($sorter as $ii => $va) {
//        $ret[$ii]=$array[$ii];
//    }
//    $array=$ret;
//}
//
//aasort($isidata,"noaku");

$stream="<table border=1>
	     <thead>
		    <tr bgcolor='#dedede'>
			  <td align=center>".$_SESSION['lang']['nomor']."</td>
			  <td align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['saldoakhir']."</td>
			  <td align=center>".$_SESSION['lang']['kodeblok']."</td>
			  <td align=center>".$_SESSION['lang']['noreferensi']."</td>
			</tr>  
		 </thead>
		 <tbody id=container>";
 //tampil data
$no=0;
foreach($isidata as $baris)
{
    if($akunaktif<>$baris[noaku]){ // akun sekarang beda sama akun sebelumnya?
        $salwal=$saldoawal[$baris[noaku]]; // ambil saldo awal
        $grandsalwal+=$saldoawal[$baris[noaku]];
        if(($totaldebet!=0)or($totalkredit!=0)){ // ada debet/kredit dari transaksi sebelumnya? kalo ada tampilkan totalnya:
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=5>SubTotal</td>";
        $stream.="<td align=right>".number_format($subsalwal)."</td>";
        $stream.="<td align=right>".number_format($totaldebet)."</td>";
        $stream.="<td align=right>".number_format($totalkredit)."</td>";
        $stream.="<td align=right>".number_format($subsalak)."</td>";
        $stream.="<td align=right colspan=2></td>";
     $stream.="</tr>";
        $totaldebet=0;
        $totalkredit=0;
        }
        $subsalwal=$saldoawal[$baris[noaku]];
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=4>".$baris[noaku]."</td>";
        $stream.="<td colspan=7>".$namaakun[$baris[noaku]]."</td>";
     $stream.="</tr>";
    }
    $no+=1;
    $stream.="<tr class=rowcontent>";
        $stream.="<td>".$no."</td>";
        $stream.="<td>".$baris[nojur]."</td>";
        $stream.="<td>".$baris[tangg]."</td>";
        $stream.="<td>".$baris[noaku]."</td>";
        $stream.="<td>".$baris[keter]."</td>";
        
        $stream.="<td align=right>".number_format($salwal)."</td>";
        $stream.="<td align=right>".number_format($baris[debet])."</td>";
        $totaldebet+=$baris[debet];
        $grandtotaldebet+=$baris[debet];
        $stream.="<td align=right>".number_format($baris[kredi])."</td>";
        $totalkredit+=$baris[kredi];
        $grandtotalkredit+=$baris[kredi];
        $salwal=$salwal+($baris[debet])-($baris[kredi]);
        $stream.="<td align=right>".number_format($salwal)."</td>";
        $stream.="<td>".$baris[kodeb]."</td>";
        $stream.="<td>".$baris[noref]."</td>";
     $stream.="</tr>";
     $akunaktif=$baris[noaku];
     $subsalak=$salwal;
}    
if($akunaktif!=''){
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=5>SubTotal</td>";
        $stream.="<td align=right>".number_format($subsalwal)."</td>";
        $stream.="<td align=right>".number_format($totaldebet)."</td>";
        $stream.="<td align=right>".number_format($totalkredit)."</td>";
        $stream.="<td align=right>".number_format($subsalak)."</td>";
        $stream.="<td align=right colspan=2></td>";
     $stream.="</tr>";
}
    $grandsalak=$grandsalwal+$grandtotaldebet-$grandtotalkredit;
    $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=5>GrandTotal</td>";
        $stream.="<td align=right>".number_format($grandsalwal)."</td>";
        $stream.="<td align=right>".number_format($grandtotaldebet)."</td>";
        $stream.="<td align=right>".number_format($grandtotalkredit)."</td>";
        $stream.="<td align=right>".number_format($grandsalak)."</td>";
        $stream.="<td align=right colspan=2></td>";
     $stream.="</tr>";

$stream.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="Laporan_BukuBesar_".$pt.$gudang." ".$qwe;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>