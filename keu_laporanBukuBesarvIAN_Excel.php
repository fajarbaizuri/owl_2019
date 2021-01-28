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


	$lawan=array();
	$lawan1=array();
	
	$cariLawan="select * from ".$dbname.".keu_jurnaldt_vw where tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."'  ".$wheregudang." order by noakun, tanggal";
	
	$res11=mysql_query($cariLawan);
	
	while($bar11=mysql_fetch_object($res11))
	{
	
	$lawan[$bar11->nojurnal][qty]=$bar11->nourut;
	$lawan1[$bar11->nojurnal][$bar11->nourut][akun]=$bar11->noakun;
	
	}
	


// ambil data
    $isidata=array();
$str="select * from ".$dbname.".keu_jurnaldt_vw where tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun, tanggal";
//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe][tangg]=$bar->tanggal;
    $isidata[$qwe][nojur]=$bar->nojurnal;
	$isidata[$qwe][noref]=$bar->noreferensi;
    $isidata[$qwe][noaku]=$bar->noakun;
    $isidata[$qwe][keter]=$bar->keterangan;
    $isidata[$qwe][debet]=$bar->debet;
    $isidata[$qwe][kredi]=$bar->kredit;
	$isidata[$qwe][blok]=$bar->kodeblok;
    //$isidata[$qwe][kodeb]=$bar->kodeblok;
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
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td align=center>No. Ref</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  
			  
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['saldoakhir']."</td>
			  <td align=center>".$_SESSION['lang']['kodeblok']."</td>
			  
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
    $stream.="<tr >";
        $stream.="<td align=right colspan=6><b>SubTotal</b></td>";
       
        $stream.="<td align=right><b>".number_format($totaldebet)."</b></td>";
        $stream.="<td align=right><b>".number_format($totalkredit)."</b></td>";
        $stream.="<td align=right><b>".number_format($subsalak)."</b></td>";
     $stream.="</tr>";
        $totaldebet=0;
        $totalkredit=0;
        }
        $subsalwal=$saldoawal[$baris[noaku]];
		
    $stream.="<tr >";
        $stream.="<td align=right colspan=3></td>";
		$stream.="<td><b>No. Akun:	".$baris[noaku]."</b></td>";
		$stream.="<td ><b>".$namaakun[$baris[noaku]]."</b></td>";
		$stream.="<td colspan=4></td>";
     $stream.="</tr>";
	 $stream.="<tr >";
        $stream.="<td align=right colspan=3></td>";
		$stream.="<td><b>".$_SESSION['lang']['saldoawal']."</b></td>";
		$stream.="<td align=right colspan=4><b>".number_format($saldoawal[$baris[noaku]] )."</b></td>";
		
     $stream.="</tr>";
	 
	 
    }
    $no+=1;
    $stream.="<tr class=rowcontent>";
        $stream.="<td>".$no."</td>";
        
        $stream.="<td>".tanggalnormal($baris[tangg])."</td>";
        $stream.="<td>".$baris[nojur]."</td>";
		$stream.="<td>".$baris[noref]."</td>";
        $stream.="<td>".$baris[keter]."</td>";
        //$stream.="<td>".$baris[noaku]."</td>";
        
		
				$jur=$baris[nojur];
		$kun=$baris[noaku];
		
		
	    $qty=$lawan[$jur]['qty'];
		
		$tes=LawanCaco($jur,$kun,$lawan1,$qty);
		$stream.="<td>".$tes."</td>";
		//echo"<td>".$tes."</td>";
        $stream.="<td align=right>".number_format($baris[debet])."</td>";
        $totaldebet+=$baris[debet];
        $grandtotaldebet+=$baris[debet];
        $stream.="<td align=right>".number_format($baris[kredi])."</td>";
        $totalkredit+=$baris[kredi];
        $grandtotalkredit+=$baris[kredi];
        $salwal=$salwal+($baris[debet])-($baris[kredi]);
        $stream.="<td align=right>".number_format($salwal)."</td>";
		$stream.="<td align=center>".$baris[blok]."</td>";
		
        
     $stream.="</tr>";
     $akunaktif=$baris[noaku];
     $subsalak=$salwal;
}    
if($akunaktif!=''){
    $stream.="<tr >";
        $stream.="<td align=right colspan=6><b>SubTotal</b></td>";
        $stream.="<td align=right><b>".number_format($totaldebet)."</b></td>";
        $stream.="<td align=right><b>".number_format($totalkredit)."</b></td>";
        $stream.="<td align=right><b>".number_format($subsalak)."</b></td>";
     $stream.="</tr>";
}
    $grandsalak=$grandsalwal+$grandtotaldebet-$grandtotalkredit;
    $stream.="<tr >";
        $stream.="<td align=right colspan=6><b>GrandTotal</b></td>";
        $stream.="<td align=right><b>".number_format($grandtotaldebet)."</b></td>";
        $stream.="<td align=right><b>".number_format($grandtotalkredit)."</b></td>";
        $stream.="<td align=right><b>".number_format($grandsalak)."</b></td>";
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

function LawanCaco($nojur,$akun,$akuncari=array(),$qty){
//cari lawan akunnya jika 1 ke 1 tampilkan jika banyak kosongkan
	
	
	if ($qty ==0) {
	 $nilai="none";
	}elseif ($qty >1) {
	 $nilai="";
	}else{
		for ($i = 0; $i <= $qty; $i++) {
		    if($akuncari[$nojur][$i]['akun']!=$akun){
			$hasil=$akuncari[$nojur][$i]['akun'];
			}else{
				$hasil="";
			}
			
		}
	}
return $hasil;	
}
?>