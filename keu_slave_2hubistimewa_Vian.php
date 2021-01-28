<?php
// file creator: Angelwhite
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$pt2=$_POST['pt2'];
$unit1=$_POST['unit1'];
$unit2=$_POST['unit2'];
$tanggal1=$_POST['tanggal1'];
$tanggal2=$_POST['tanggal2'];

//check, one-two
if($tanggal1==''){
    echo "WARNING: silakan mengisi tanggal."; exit;
}
if($tanggal2==''){
    echo "WARNING: silakan mengisi tanggal."; exit;
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


// cari Akun R/K
$str="select * from ".$dbname.".keu_5caco  ";



$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$akunRK[$bar->kodeorg][$bar->jenis]=$bar->akunpiutang;
	$akunRKDB[$bar->akunpiutang][$bar->jenis]=$bar->kodeorg;
}


//tentukan scope unit berdasarkan pilihan
// buat cari pemilik biaya 
if($unit1==''){
// untuk pilihan semua unit1 & unit2
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' ";
}else if($unit1!=''){
// untuk pilihan semua unit2 & unit1 pilihan tertentu;
$str="select kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$unit1."' ";
}

$res=mysql_query($str);
$unitOrg="";

while($bar1=mysql_fetch_object($res))
{
	$unitOrg.="'".$bar1->kodeorganisasi."' , ";	
}

// buat cari Akun Istimewa
if($unit2==''){
// untuk pilihan semua unit1 & unit2
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt2."' ";
}else if($unit2!=''){
// untuk pilihan semua unit2 & unit1 pilihan tertentu;
$str="select kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi ='".$unit2."' ";
}


$res=mysql_query($str);
$unitRK="";


while($bar=mysql_fetch_object($res))
{
	$unitRK.="'".$akunRK[$bar->kodeorganisasi]["inter"]."' , ";	
}





$whereRK="AND noakun IN (".substr(str_replace(", ''","",$unitRK),0,-2).") ";
$whereUNIT="AND kodeorg IN (".substr(str_replace(", ''","",$unitOrg),0,-2).") ";
$cariUnit="WHERE tanggal >='".$tanggal1."' AND tanggal <='".$tanggal2."' ".$whereRK." ".$whereUNIT."";
$str="select * from ".$dbname.".keu_jurnaldt ".$cariUnit." order by kodeorg asc";



$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{	
	$hubakun=$akunRKDB[$bar->noakun]["inter"];
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

$dataArray=Array();
$datadebet=Array();
$crno=0;
if ($no!=0){
	foreach($totUNIT as $key) {
			
			
		foreach($rkakun[$key] as $key1) {
			$nilai1=$akunRK[$key1]["inter"];
			$dataArray=$hubrk[$key][$key1];
			$view.="<tr class= rowcontent>";
			$view.="<td align=center colspan=7></td>";
			$view.="</tr>";
			$view.="<tr class= rowcontent>";
			$view.="<td align=center colspan=7><b><i>Hubungan ".$key." - R/I ".$key1." ".$akunRK[$key1]["inter"]."</i></b></td>";
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
				$tes1="Jurnal:";
				$tes="Tip('".$tes1.$dataPrev['nojur']."')";
				$view.="<tr class= rowcontent onmouseover=".$tes." onmouseout=UnTip()>";
				$view.="<td align=center>".$crno."</td>";
				$view.="<td align=center>".$dataPrev['Ref']."</td>";
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
exit;

//-----------------------------------------------------------------------------

