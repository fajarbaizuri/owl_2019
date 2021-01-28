<?php
// file creator: Angelwhite
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$gudang=$_POST['gudang'];
$tanggal1=$_POST['tanggal1'];
$tanggal2=$_POST['tanggal2'];
$akundari=$_POST['akundari'];
$akunsampai=$_POST['akunsampai'];

//check, one-two
if($tanggal1==''){
    echo "WARNING: silakan mengisi tanggal."; exit;
}
if($tanggal2==''){
    echo "WARNING: silakan mengisi tanggal."; exit;
}
if($akundari==''){
    echo "WARNING: silakan memilih akun."; exit;
}
if($akunsampai==''){
    echo "WARNING: silakan memilih akun."; exit;
}

        

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
//$saldoawal=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $qwe="awal".$bulan;
    $saldoawal[$bar->noakun]+=$bar->$qwe;
}
       // echo "<pre>";
       // print_r($saldoawal);
       // echo "</pre>";

    $str="select noakun,namaakun from ".$dbname.".keu_5akun
                    where level = '5'
                    order by noakun
                    ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $namaakun[$bar->noakun]=$bar->namaakun;
    }

	$lawan=array();
	$lawan1=array();
	
	$cariLawan="select * from ".$dbname.".keu_jurnaldt_vw where tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."'  ".$wheregudang." order by noakun, tanggal";
	
	$res11=mysql_query($cariLawan);
	
	while($bar11=mysql_fetch_object($res11))
	{
	
	$lawan[$bar11->nojurnal]['qty']=$bar11->nourut;
	$lawan1[$bar11->nojurnal][$bar11->nourut]['akun']=$bar11->noakun;
	
	}
	
	
// ambil data
    $isidata=array();
$str="select nojurnal,noakun,nourut,tanggal,noreferensi,keterangan,debet,kredit,kodeblok from ".$dbname.".keu_jurnaldt_vw where tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun, tanggal";
           
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
	$isidata[$qwe]['tangg']=$bar->tanggal;
    $isidata[$qwe]['nojur']=$bar->nojurnal;
	$isidata[$qwe]['noref']=$bar->noreferensi;
	
	
    $isidata[$qwe]['noaku']=$bar->noakun;
    $isidata[$qwe]['keter']=$bar->keterangan;
    $isidata[$qwe]['debet']=$bar->debet;
    $isidata[$qwe]['kredi']=$bar->kredit;
    $isidata[$qwe]['kodeb']=$bar->kodeblok;
}
//        echo "<pre>";
//        print_r($isidata);
//        echo "</pre>";

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


//function aasort (&$arra, $key) {
//    $sorter=array();
//    $ret=array();
//    reset($arra);
//    foreach ($arra as $ii => $va) {
//        $sorter[$ii]=$va[$key];
//    }
//    asort($sorter);
//    foreach ($sorter as $ii => $va) {
//        $ret[$ii]=$arra[$ii];
//    }
//    $arra=$ret;
//}
//
//aasort($isidata,"noaku");

//tampil data
$no=0;
//        echo"<tr class=rowtitle>";
//        echo"<td align=center colspan=9>".$str."</td>";
//         echo"</tr>";
foreach($isidata as $baris)
{
	
    if($akunaktif<>$baris['noaku']){ // akun sekarang beda sama akun sebelumnya?
        $salwal=$saldoawal[$baris['noaku']]; // ambil saldo awal
        $grandsalwal+=$saldoawal[$baris['noaku']];
        if(($totaldebet!=0)or($totalkredit!=0)){ // ada debet/kredit dari transaksi sebelumnya? kalo ada tampilkan totalnya:
			echo"<tr class=rowtitle>";
			echo"<td align=right colspan=6>SubTotal</td>";
			//echo"<td align=right>".number_format($subsalwal)."</td>";
			echo"<td align=right>".number_format($totaldebet)."</td>";
			echo"<td align=right>".number_format($totalkredit)."</td>";
			echo"<td align=right>".number_format($subsalak)."</td>";
			echo"</tr>";
			$totaldebet=0;
			$totalkredit=0;
        }
        $subsalwal=$saldoawal[$baris['noaku']];
    echo"<tr class=rowtitle rowcontent>";
        echo"<td align=right colspan=3></td>";
        echo"<td>No. Akun:	".$baris['noaku']."</td>";
        echo"<td colspan=5>".$namaakun[$baris['noaku']]."</td>";
     echo"</tr>";
	 echo"<tr class=rowtitle rowcontent>";
        echo"<td align=right colspan=4></td>";
        echo"<td>".$_SESSION['lang']['saldoawal']."</td>";
		echo"<td align=right colspan=4>".number_format($saldoawal[$baris['noaku']],0,',','.')."</td>";
     echo"</tr>";
    }
    $no+=1;
    echo"<tr class=rowcontent>";
        echo"<td>".$no."</td>";
		echo"<td>".tanggalnormal($baris['tangg'])."</td>";
        echo"<td>".$baris['nojur']."</td>";
		echo"<td>".$baris['noref']."</td>";
        echo"<td>".$baris['keter']."</td>";
        //echo"<td>".$baris[noaku]."</td>";
		$jur=$baris[nojur];
		$kun=$baris[noaku];
		
		
	    $qty=$lawan[$jur]['qty'];
		
		$tes=LawanCaco($jur,$kun,$lawan1,$qty);
		echo"<td>".$tes."</td>";
        
        
        //echo"<td align=right>".number_format($salwal)."</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($baris['debet'],0,',','.')."</td>";
        $totaldebet+=$baris['debet'];
        $grandtotaldebet+=$baris['debet'];
        echo"<td align=right>&nbsp;&nbsp;".number_format($baris['kredi'],0,',','.')."</td>";
        $totalkredit+=$baris['kredi'];
        $grandtotalkredit+=$baris['kredi'];
        $salwal=$salwal+($baris['debet'])-($baris['kredi']);
        echo"<td align=right>&nbsp;&nbsp;".number_format($salwal,0,',','.')."</td>";
        echo"<td>".$baris['kodeb']."</td>";
     echo"</tr>";
     $akunaktif=$baris['noaku'];
     $subsalak=$salwal;
}    
if($akunaktif!=''){
    echo"<tr class=rowtitle>";
        echo"<td align=right colspan=6>SubTotal</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($totaldebet,0,',','.')."</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($totalkredit,0,',','.')."</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($subsalak,0,',','.')."</td>";
     echo"</tr>";
//     $grandtotaldebet+=$totaldebet;
//     $grandtotalkredit+=$totalkredit;
}
    $grandsalak=$grandsalwal+$grandtotaldebet-$grandtotalkredit;
	echo"<tr class=rowtitle>";
        echo"<td align=right colspan=6>GrandTotal</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($grandtotaldebet,0,',','.')."</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($grandtotalkredit,0,',','.')."</td>";
        echo"<td align=right>&nbsp;&nbsp;".number_format($grandsalak,0,',','.')."</td>";
     echo"</tr>";

//ambil data jurnal OPSI PERTAMA, PAKE QUERY OVER ARRAY... HASILNYA CUKUP LEMOT
//$no=0;
//foreach($saldoawal as $baris => $data)
//{
//$str="select * from ".$dbname.".keu_jurnaldt_vw where tanggal >= '".$tanggal1."' and tanggal <= '".$tanggal2."' and noakun = '".$baris."' ".$wheregudang;
//    $salwal=$saldoawal[$baris];
//    $res=mysql_query($str);
//    while($bar= mysql_fetch_object($res))
//    {
//        $no+=1;
//        $salak=$salwal+($bar->debet)-($bar->kredit);
//        echo"<tr class=rowcontent>
//               <td>".$no."</td>
//               <td>".$bar->nojurnal."</td>    
//               <td>".$bar->tanggal."</td>
//               <td>".$bar->noakun."</td>
//               <td>".$bar->keterangan."</td>
//               <td align=right>".number_format($salwal)."</td>
//               <td align=right>".number_format($bar->debet)."</td>
//               <td align=right>".number_format($bar->kredit)."</td>   
//               <td align=right>".number_format($salak)."</td>    
//             </tr>";
//        $salwal=$salak;
//     } 
// }

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