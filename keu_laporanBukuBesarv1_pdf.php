<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

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

//=================================================
class PDF extends FPDF {
    function Header() {
       global $pt;
       global $gudang;
       global $tanggal1;
       global $tanggal2;
        $this->SetFont('Arial','B',9); 
		$this->Cell(20,3,$pt.' '.$gudang,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(190,3,strtoupper($_SESSION['lang']['rincianbb']),0,1,'C');
        $this->SetFont('Arial','',9);
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,date('d-m-Y H:i'),0,1,'L'); if($gudang=='')$gudang='All';
		$this->Cell(150,3,'UNIT : '.$gudang,'',0,'L');
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$this->PageNo(),'',1,'L');
		$this->Cell(150,3,"Tanggal : ".tanggalnormal($tanggal1).' sampai '.tanggalnormal($tanggal2),'',0,'L'); 
		$this->Cell(15,3,'User','',0,'L');
		$this->Cell(2,3,' : ','',0,'L');
		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',7);
                $this->Cell(10,5,$_SESSION['lang']['nomor'],1,0,'C');
		$this->Cell(35,5,$_SESSION['lang']['nojurnal'],1,0,'C');	
		$this->Cell(13,5,$_SESSION['lang']['tanggal'],1,0,'C');	
		$this->Cell(16,5,$_SESSION['lang']['noakun'],1,0,'C');	
		$this->Cell(38,5,$_SESSION['lang']['keterangan'],1,0,'C');	
		$this->Cell(20,5,$_SESSION['lang']['saldoawal'],1,0,'C');	
		$this->Cell(20,5,$_SESSION['lang']['debet'],1,0,'C');
		$this->Cell(20,5,$_SESSION['lang']['kredit'],1,0,'C');
		$this->Cell(20,5,$_SESSION['lang']['saldoakhir'],1,0,'C');
        $this->Ln();						
        $this->Ln();						

    }
}
//================================

    $pdf=new PDF('P','mm','A4');
    $pdf->AddPage();

foreach($isidata as $baris)
{
    if($akunaktif<>$baris[noaku]){ // akun sekarang beda sama akun sebelumnya?
        $salwal=$saldoawal[$baris[noaku]]; // ambil saldo awal
        $grandsalwal+=$saldoawal[$baris[noaku]];
        if(($totaldebet!=0)or($totalkredit!=0)){ // ada debet/kredit dari transaksi sebelumnya? kalo ada tampilkan totalnya:
        $pdf->Cell(112,5,'Sub Total',0,0,'R');				
        $pdf->Cell(20,5,number_format($subsalwal),0,0,'R');
        $pdf->Cell(20,5,number_format($totaldebet),0,0,'R');
        $pdf->Cell(20,5,number_format($totalkredit),0,0,'R');	
        $pdf->Cell(20,5,number_format($subsalak),0,1,'R');	
        $totaldebet=0;
        $totalkredit=0;
        }
        $subsalwal=$saldoawal[$baris[noaku]];
        $pdf->Cell(10,5,'',0,0,'C');
        $pdf->Cell(35,5,'',0,0,'L');
        $pdf->Cell(15,5,'',0,0,'L');
        $pdf->Cell(12,5,$baris[noaku],0,0,'L');
        $pdf->Cell(80,5,$namaakun[$baris[noaku]],0,1,'L');
    }
    $no+=1;
        $pdf->Cell(10,5,$no,0,0,'C');
        $pdf->Cell(35,5,$baris[nojur],0,0,'L');
        $pdf->Cell(15,5,tanggalnormal($baris[tangg]),0,0,'L');
        $pdf->Cell(12,5,$baris[noaku],0,0,'L');
        $pdf->Cell(40,5,substr($baris[keter],0,30),0,0,'L');				
        $pdf->Cell(20,5,number_format($salwal),0,0,'R');	
        $pdf->Cell(20,5,number_format($baris[debet]),0,0,'R');
        $totaldebet+=$baris[debet];
        $grandtotaldebet+=$baris[debet];
        $pdf->Cell(20,5,number_format($baris[kredi]),0,0,'R');
        $totalkredit+=$baris[kredi];
        $grandtotalkredit+=$baris[kredi];
        $salwal=$salwal+($baris[debet])-($baris[kredi]);
        $pdf->Cell(20,5,number_format($salwal),0,1,'R');	
     $akunaktif=$baris[noaku];
     $subsalak=$salwal;
}    
if($akunaktif!=''){
        $pdf->Cell(112,5,'Sub Total',0,0,'R');				
        $pdf->Cell(20,5,number_format($subsalwal),0,0,'R');
        $pdf->Cell(20,5,number_format($totaldebet),0,0,'R');
        $pdf->Cell(20,5,number_format($totalkredit),0,0,'R');	
        $pdf->Cell(20,5,number_format($subsalak),0,1,'R');	
}    
    $grandsalak=$grandsalwal+$grandtotaldebet-$grandtotalkredit;
        $pdf->Cell(112,5,'Grand Total',0,0,'R');				
        $pdf->Cell(20,5,number_format($grandsalwal),0,0,'R');
        $pdf->Cell(20,5,number_format($grandtotaldebet),0,0,'R');
        $pdf->Cell(20,5,number_format($grandtotalkredit),0,0,'R');	
        $pdf->Cell(20,5,number_format($grandsalak),0,1,'R');	
    
$pdf->Output();		
?>