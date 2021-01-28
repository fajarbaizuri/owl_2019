<?php
// file creator: Angelwhite
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_GET['pt'];
$unit1=$_GET['unit1'];
$unit2=$_GET['unit2'];
$tanggal1=$_GET['tanggal1'];
$tanggal2=$_GET['tanggal2'];
        

//$periode buat filter keu_saldobulanan, $bulan buat nentuin field-nya
$qwe=explode("-",$tanggal1);
$periode=$qwe[2].$qwe[1];
$bulan=$qwe[1];

//balik tanggal
$qwe=explode("-",$tanggal1);
$tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggal2);
$tanggal2=$qwe[2]."-".$qwe[1]."-".$qwe[0];


// cari Akun R/K
$str="select * from ".$dbname.".keu_5caco  ";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$akunRK[$bar->kodeorg][$bar->jenis]=$bar->akunpiutang;
	$akunRKDB[$bar->akunpiutang][$bar->jenis]=$bar->kodeorg;
}

// cari nama pt
$str="select * from ".$dbname.".organisasi";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$crPT[$bar->kodeorganisasi]=$bar->namaorganisasi;
}

//tentukan scope unit berdasarkan pilihan

if(($unit1==$pt)&&($unit2==$pt)){
// untuk pilihan semua unit1 & unit2
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'";
}else if(($unit1==$pt)&&($unit2!=$pt)){
// untuk pilihan semua unit1 & unit2 pilihan tertentu;
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' AND kodeorganisasi !='".$unit2."' ";
}else if(($unit1!=$pt)&&($unit2==$pt)){
// untuk pilihan semua unit2 & unit1 pilihan tertentu;
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' AND kodeorganisasi !='".$unit1."' ";
}else if($unit1==$unit2){
// untuk pilihan unit1 == unit2 
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' AND kodeorganisasi ='".$unit1."' ";
}else {
// untuk pilihan unit1 != unit2 
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' AND kodeorganisasi ='".$unit1."' ";
}

$res=mysql_query($str);
$unitPT="";
$unitRK="";
$unitOrg="";

while($bar=mysql_fetch_object($res))
{
	
	$unitPT.="'".$bar->kodeorganisasi."' , ";	
	$unitRK.="'".$akunRK[$bar->kodeorganisasi]["intra"]."' , ";	
	$unitOrg.="'".$bar->kodeorganisasi."' , ";	
}



if(($unit1==$pt)&&($unit2==$pt)){
// untuk pilihan semua unit1 & unit2
	if (substr(str_replace(", ''","",$unitRK),0,-2)==""){
		$whereRK="";
	}else{
		$whereRK="AND noakun IN (".substr(str_replace(", ''","",$unitRK),0,-2).")";
	}
	$whereRKA=$whereRK;
	$whereUNIT="AND kodeorg IN (".substr(str_replace(", ''","",$unitPT),0,-2).")";
}else if(($unit1==$pt)&&($unit2!=$pt)){
// untuk pilihan semua unit1 & unit2 pilihan tertentu;
	if ($akunRK[$unit2]["intra"]==""){
		$whereRK="";
	}else{
		$whereRK="AND noakun = '".$akunRK[$unit2]["intra"]."'";
	}
	
	if ($akunRK[$unit1]["intra"]==""){
		$whereRKA="";
	}else{
		$whereRKA="AND noakun = '".$akunRK[$unit1]["intra"]."'";
	}
	
	$whereUNIT="AND kodeorg IN (".substr(str_replace(", ''","",$unitPT),0,-2).")";
}else if(($unit1!=$pt)&&($unit2==$pt)){
// untuk pilihan semua unit2 & unit1 pilihan tertentu;
	if (substr(str_replace(", ''","",$unitRK),0,-2)==""){
		$whereRK="";
	}else{
		$whereRK="AND noakun IN (".substr(str_replace(", ''","",$unitRK),0,-2).")";
	}
	$whereRKA=$whereRK;
	
	$whereUNIT="AND kodeorg = '".$unit1."'";
}else if($unit1==$unit2){
// untuk pilihan unit1 == unit2 
	if ($akunRK[$unit2]["intra"]==""){
		$whereRK="";
	}else{
		$whereRK="AND noakun = '".$akunRK[$unit2]["intra"]."'";
	}
	if ($akunRK[$unit1]["intra"]==""){
		$whereRKA="";
	}else{
		$whereRKA="AND noakun = '".$akunRK[$unit1]["intra"]."'";
	}
	
	$whereUNIT="AND kodeorg = '".$unit1."'";
}else {
// untuk pilihan unit1 != unit2 
	if ($akunRK[$unit2]["intra"]==""){
		$whereRK="";
	}else{
		$whereRK="AND noakun = '".$akunRK[$unit2]["intra"]."'";
	}
	if ($akunRK[$unit1]["intra"]==""){
		$whereRKA="";
	}else{
		$whereRKA="AND noakun = '".$akunRK[$unit1]["intra"]."'";
	}
	
	$whereUNIT="AND kodeorg = '".$unit1."'";
}


$cariUnit="WHERE tanggal >='".$tanggal1."' AND tanggal <='".$tanggal2."' ".$whereRK." ".$whereUNIT."";
$str="select * from ".$dbname.".keu_jurnaldt ".$cariUnit." order by kodeorg asc";

$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{	
	$hubakun=$akunRKDB[$bar->noakun]["intra"];
	//cari banyaknya hub. RK
	$hubrk[$bar->kodeorg][$hubakun][]=array (
	'nojur'=>$bar->nojurnal,
	'nourut'=>$bar->nourut,
	'Ref'=>$bar->noreferensi,
		'tgl'=>$bar->tanggal,
		'akun'=>$bar->noakun,
		'ket'=>$bar->keterangan,
		'jumlah'=>$bar->jumlah
	);
	
	$totUNIT[$bar->kodeorg]=$bar->kodeorg;
	$rkakun[$bar->kodeorg][$hubakun]=$hubakun;
	
	$jumlah=$bar->jumlah;

	$no++;
}
//ambil saldo
$str="select * from ".$dbname.".keu_saldobulanan where periode = '".$periode."' order by noakun";
//$saldoawal=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $qwe="awal".$bulan;
    $saldoawal[$bar->noakun][$bar->kodeorg]+=$bar->$qwe;
}

$view="<table border=1>
		 <thead>
		 <tr>
		 <td align=center colspan=7 ><b>Hubungan Rekening Koran (R/K)</b></td>
		 </tr>";
		 if(($unit1==$pt)&&($unit2==$pt)){
		// untuk pilihan semua unit1 & unit2
		$view.="<tr><td align=center colspan=7 >".$crPT[$pt]."</td></tr>";
		}else if(($unit1==$pt)&&($unit2!=$pt)){
		// untuk pilihan semua unit1 & unit2 pilihan tertentu;
		$view.="<tr><td align=center colspan=7 >".$crPT[$pt]."</td></tr>";
		}else if(($unit1!=$pt)&&($unit2==$pt)){
		// untuk pilihan semua unit2 & unit1 pilihan tertentu;
		$view.="<tr><td align=center colspan=7 >".$crPT[$pt]."</td></tr>";
		}else if($unit1==$unit2){
		// untuk pilihan unit1 == unit2 
		$view.="<tr><td align=center colspan=7 >".$crPT[$unit1]."(".$unit1.")</td></tr>";
		}else {
		// untuk pilihan unit1 != unit2 
		$view.="<tr><td align=center colspan=7 >".$crPT[$unit1]."(".$unit1.") - ".$crPT[$unit2]."(".$unit2.")</td></tr>";
		}
		$view.="
		 <tr >
		 <td align=center colspan=7 >Periode (".$tanggal1." s/d ".$tanggal2.") </td>
		 </tr>
		 </thead>
	     <thead>
		    <tr bgcolor='#dedede'>
				<td align=center >No.</td>
				<td align=center >No.Ref</td>
				<td align=center >Tanggal</td>
				<td align=center >Keterangan</td>
				<td align=center >Debet</td>
				<td align=center >Kredit</td>
				<td align=center >Saldo</td>
			</tr>  
		 </thead>
		 <tbody id=container>";
		 
$dataArray=Array();
$datadebet=Array();
$crno=0;
if ($no!=0){
	foreach($totUNIT as $key) {
		foreach($rkakun[$key] as $key1) {
			$nilai1=$akunRK[$key1]["intra"];
			$dataArray=$hubrk[$key][$key1];
			$view.="<tr class= rowcontent>";
			$view.="<td align=center colspan=7></td>";
			$view.="</tr>";
			$view.="<tr class= rowcontent>";
			$view.="<td align=center colspan=7><b><i>Hubungan ".$key." - R/K ".$key1." ".$akunRK[$key1]["intra"]."</i></b></td>";
			$view.="</tr>";
			$view.="<tr class= rowcontent>";
			$view.="<td align=right colspan=4><b>Saldo Awal</b></td>";
			$view.="<td align=center ></td>";
			$view.="<td align=center ></td>";
			
			$view.="<td align=right ><b>".number_format($saldoawal[$nilai1][$key],2)."<b/></td>";
			$view.="</tr>";
			$dataArray=$hubrk[$key][$key1];
			$saldo=$saldoawal[$nilai1][$key];
			$TSaldo=0;
			$TKredit=0;
			$TDebet=0;
			foreach($dataArray as $dataPrev) {
				$crno++;
				$view.="<tr class= rowcontent >";
				$view.="<td align=center >".$crno."</td>";
				$view.="<td align=center >".$dataPrev['Ref']."</td>";
				$view.="<td align=center>".$dataPrev['tgl']."</td>";
				$view.="<td align=left>".$dataPrev['ket']."</td>";
				if($dataPrev['jumlah'] > 0){
					$view.="<td align=right>".number_format($dataPrev['jumlah'],2)."</td>";
					$view.="<td align=right>0.00</td>";
					$TDebet+=$dataPrev['jumlah'];
				}else{
					$view.="<td align=right>0.00</td>";
					$view.="<td align=right>".number_format($dataPrev['jumlah']*(-1),2)."</td>";
					$TKredit+=$dataPrev['jumlah'];
				}
				$saldo+=$dataPrev['jumlah'];
				$view.="<td align=right><b>".number_format($saldo,2)."</b></td>";
				$view.="</tr>";
				
				
				
			}
		
			$view.="<tr class= rowcontent>";
			$view.="<td align=center colspan=4><b><i>Jumlah</i></b></td>";
			$view.="<td align=right><b>".number_format($TDebet,2)."</b></td>";
			$view.="<td align=right><b>".number_format($TKredit*(-1),2)."</b></td>";
			$TSaldo=$saldoawal[$nilai1][$key]+$TDebet+$TKredit;
			$view.="<td align=right><b>".number_format($TSaldo,2)."</b></td>";
			$view.="</tr>";
		}
	}
}else{
		$view.="<tr class= rowcontent>";
		$view.="<td align=center colspan=4><b><i>Jumlah</i></b></td>";
		$view.="<td align=right><b>0</b></td>";
		$view.="<td align=right><b>0</b></td>";
		$view.="<td align=right><b>0</b></td>";
		$view.="</tr>";
}
echo $view;
$view.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
$view.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="Laporan_hubrk_".$pt.$uni1." ".$qwe;
if(strlen($view)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $view);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    

//-----------------------------------------------------------------------------

