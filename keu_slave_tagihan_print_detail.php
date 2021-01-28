<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');


$proses = $_GET['proses'];
$param = $_GET;

$namasup=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
//echo $param['noinvoice'];;

class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
    global $userid;
	global $notran;
				
				$sInduk="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
				$qInduk=mysql_query($sInduk) or die(mysql_error());
				$rInduk=mysql_fetch_assoc($qInduk);
				
				$str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'"; 
				$res1=mysql_query($str1);
				while($bar1=mysql_fetch_object($res1))
			    {
					$nama=$bar1->namaorganisasi;
					$alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
					$telp=$bar1->telepon;				 
			    }    
			   
				/*$sIsi="select * from ".$dbname.".sdm_splht where notransaksi='".$notran."'";
				$qIsi=mysql_query($sIsi) or die(mysql_error());
				$rIsi=mysql_fetch_assoc($qIsi);
	
				$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rIsi['kodeorg']."'";
				$qOrg=mysql_query($sOrg) or die(mysql_error());
				$rOrg=mysql_fetch_assoc($qOrg);
						
						$bagian=$rIsi['kode'];
					
						$ind2=" SELECT * 
						FROM ".$dbname.".sdm_5departemen
						WHERE kode='".$bagian."' ";
						//exit("Error:$ind2");
						$indr2=mysql_query($ind2) or die(mysql_error());
						$indra2=mysql_fetch_assoc($indr2);
						
						$namabagian=$indra2['nama'];*/
				
		$path='images/logo.jpg';
	    $this->Image($path,10,3,25);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(40);   
	    $this->Cell(60,5,$nama,0,1,'L');	 
		$this->SetX(40); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(40); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','',8);
		$this->Line(10,30,280,30);	
		
		
			//	        $this->Ln();	
     $this->Ln();
	 
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}

	$pdf=new PDF('L','mm','A4');
	$pdf->AddPage();
			
	//$pdf->Ln();
	$pdf->SetFont('Arial','U',15);
	
	$pdf->Cell(300,5,'PENGAKUAN HUTANG',0,1,'C');

	$pdf->Ln(15);
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetFillColor(255,255,255);	
	$pdf->Cell(40,5,'Header',0,1,'L',1);
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(30,5,'No. Tagihan',1,0,'C',1);
	$pdf->Cell(25,5,'Tipe Invoice',1,0,'C',1);	
	$pdf->Cell(25,5,'Tanggal',1,0,'C',1);
	$pdf->Cell(40,5,'No. Po',1,0,'C',1);
	$pdf->Cell(60,5,'Nama Supplier',1,0,'C',1);
	$pdf->Cell(35,5,'Nilai Invoice Rupiah',1,0,'C',1);
	$pdf->Cell(25,5,'Jatuh Tempo',1,0,'C',1);
	$pdf->Cell(40,5,'Keterangan',1,1,'C',1);		
	
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
		$str="select * from ".$dbname.".keu_tagihanht where noinvoice='".$param['noinvoice']."'";// echo $str;exit();
		$re=mysql_query($str);

		$no=0;
		while($res=mysql_fetch_assoc($re))
		{		
			$pdf->Cell(30,5,$res['noinvoice'],1,0,'L',1);
			$pdf->Cell(25,5,$res['tipeinvoice'],1,0,'L',1);
			$pdf->Cell(25,5,tanggalnormal($res['tanggal']),1,0,'L',1);
			$pdf->Cell(40,5,$res['nopo'],1,0,'L',1);
			$pdf->Cell(60,5,$namasup[$res['kodesupplier']],1,0,'L',1);
			$pdf->Cell(35,5,number_format($res['nilaiinvoice']),1,0,'R',1);
			$pdf->Cell(25,5,tanggalnormal($res['jatuhtempo']),1,0,'L',1);
			$pdf->Cell(40,5,$res['keterangan'],1,1,'L',1);
		}

		#detail
	$pdf->Ln(10);
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetFillColor(255,255,255);	
	$pdf->Cell(40,5,'Detail',0,1,'L',1);
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(40,5,'No. Penerimaan',1,0,'C',1);
	$pdf->Cell(25,5,'Nilai Rupiah',1,1,'C',1);	
	
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
		$str2="select * from ".$dbname.".keu_tagihandt where noinvoice='".$param['noinvoice']."'";// echo $str;exit();
		$re2=mysql_query($str2);

		$no=0;
		while($res2=mysql_fetch_assoc($re2))
		{		
			$pdf->Cell(40,5,$res2['nosj'],1,0,'L',1);
			$pdf->Cell(25,5,number_format($res2['nilai']),1,1,'R',1);
		}
		
		
		

	
		
	
	$pdf->Output();
?>
