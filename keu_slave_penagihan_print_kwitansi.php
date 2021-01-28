<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
include_once('lib/terbilang.php');

$proses = $_GET['proses'];
$param = $_POST;
$noinvoice=$_GET['noinvoice'];



switch($proses) {
  case'pdf':

            class PDF extends FPDF
                    {
                        function Header() {
							global $noinvoice;
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;

							//echo $notransaksi;exit();
							$a="select * from ".$dbname.".keu_penagihanht where noinvoice='".$noinvoice."'";
							$b=mysql_query($a);
							$c=mysql_fetch_assoc($b);
								$kodeorg=$c['kodeorg'];
								$tanggal=$c['tanggal'];
									$abulan=numToMonth(substr($tanggal,5,2),$lang='I',$format='long');//echo $abulan;
									$atanggal=substr($tanggal,8,2);
									$atahun=substr($tanggal,0,4);
								$kodecustomer=$c['kodecustomer'];
								
								//echo $kodecustomer;
									
                            $width = $this->w - $this->lMargin - $this->rMargin;
							$height = 15;
							
							
								
							$ha="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
							//echo $ha;
							$hi=mysql_query($ha) or die(mysql_error());
							$hu=mysql_fetch_assoc($hi);
								$kodept=$hu['induk'];
								
							$d="select * from ".$dbname.".organisasi where kodeorganisasi='".$kodept."'";
							$e=mysql_query($d) or die(mysql_error());
							$f=mysql_fetch_assoc($e);
								$namapt=$f['namaorganisasi'];
								$alamatpt=$f['alamat'];
								$telppt=$f['telepon'];
								$telppt2=$f['telepon2'];
								$faxpt=$f['fax'];
								$wilkotapt=$f['wilayahkota'];
								$kodepospt=$f['kodepos'];
								
								//echo $orglogo;
							
							if($kodept=='FBB')
							{
								$path='images/logo2.jpg';
							}
							else if ($kodept=='USJ')
							{
								$path='images/logo3.jpg';
							}
							else
								$path='images/logo.jpg';
								

							//$path='images/logo.jpg';
                            //$this->Image($path,$this->lMargin,$this->tMargin,50);	
							$this->Image($path,30,20,55);
                            $this->SetFont('Arial','B',8);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(90); 
							  
                            $this->Cell($width-80,12,$namapt,0,1,'L');	 
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',8);
							$height = 12;
                            $this->Cell($width-80,$height,$alamatpt,0,1,'L');	
							$this->SetX(90); 
							$this->Cell($width-80,$height,$wilkotapt.' '.$kodepospt,0,1,'L');
                            $this->SetX(90); 			
                            $this->Cell($width-80,$height,"Tel: ".$telppt.' Fax : '.$faxpt,0,1,'L');	
                            $this->Ln();
							$this->SetLineWidth(1);
							$this->SetDrawColor(127, 127, 255);
							$this->Line(30,85,390,85);
							$this->Line(515,85,570,85);
							$this->SetLineWidth();
							$this->Line(30,87,390,87);
							$this->Line(515,87,570,87);
							$this->SetXY(410,80);
							$this->SetFont('Arial','bi',14);
							$this->SetTextColor(0, 0, 255);
							$this->Cell(25,$height,'K W I T A N S I',0,1,'L');	
							$this->SetTextColor(0, 0, 0, 100);
							$this->SetDrawColor(0, 0, 0, 50);	
							
							$this->Ln(20);
							$this->SetFont('Arial','',10);
							$this->SetX(330);
							$this->Cell(20,$height,'No. Kwitansi',0,0,'L');
							$this->SetX(410);
							$this->Cell(20,$height,'     :     KW - '.$noinvoice,0,1,'L');
							$this->SetX(330);
							$this->Cell(20,$height,'Tanggal Kwitansi',0,0,'L');//
                            $this->SetX(410);
							//$this->Cell(20,$height,'     :     '.hari(substr($tanggal,0,10)).', '.tanggalnormal(substr($tanggal,0,10)),0,1,'L');
							$this->Cell(20,$height,'     :     '.$atanggal.' '.$abulan.' '.$atahun,0,1,'L');	
                       }

                        function Footer()
                        {

                        }
                    }
                    $pdf=new PDF('P','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 13;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',8);
		
		#ambil data detail untuk isian					
		
		
		
		
		#panggil data untuk pengisian data dibawah
		$a="select * from ".$dbname.".keu_penagihanht where noinvoice='".$noinvoice."'";
		$b=mysql_query($a) or die (mysql_error());
		$c=mysql_fetch_assoc($b);
			$kodeorg=$c['kodeorg'];
			$tanggal=$c['tanggal'];
			$abulan=numToMonth(substr($tanggal,5,2),$lang='I',$format='long');//echo $abulan;
			$atanggal=substr($tanggal,8,2);
			$atahun=substr($tanggal,0,4);
			$kodecustomer=$c['kodecustomer'];
			$potongan=$c['potongan'];//echo $potongan;
			$ppn=$c['nilaippn'];
			$pungutan=$c['pungutan'];
			$uangmuka=$c['uangmuka'];
			
		##kotak yang isinya  pembeli
		$acus="select * from ".$dbname.".pmn_4customer where kodecustomer='".$kodecustomer."'";
		//echo $acus;
		$bcus=mysql_query($acus) or die (mysql_error());
		$ccus=mysql_fetch_assoc($bcus);
			$custnmpt=$ccus['namacustomer'];
;
		
		$Atdet="select sum(nilaitransaksi) as nilaitransaksi,uraian from ".$dbname.".keu_penagihandt where noinvoice='".$noinvoice."' ";
		
		$Btdet=mysql_query($Atdet) or die (mysql_error());
		$Ctdet=mysql_fetch_assoc($Btdet);
			$Tnilaitrans=$Ctdet['nilaitransaksi'];
			//echo $Tnilaitrans;
			
		#perhitungan untuk jumlah => jika tidak tw jumlah yg di maksud lihat di pdf invoice
		$jumlah=$Tnilaitrans-$potongan-$uangmuka;
		
		#jika ppn di pungut biayanya 
		$totaldgnppn=$jumlah+($ppn/100*$jumlah);
		//echo $totaldgnppn;
			
		#untuk kalkulasi dari subtotal sampai total
		$pdf->Ln(50);
		$pdf->SetFont('arial','I',12);
		$pdf->Cell(50,$height,'Sudah terima dari',0,0,'L');	
		$pdf->Ln(5);
		$pdf->Cell(50,$height,'_________________',0,0,'L');
		$pdf->setX(175);
		$pdf->Cell(50,$height,':',0,0,'L');
		$pdf->setX(195);
		$pdf->SetFont('arial','',12);
		$pdf->Cell(50,$height,$custnmpt,0,1,'L');
		$pdf->Ln(5);
		$pdf->SetFont('arial','I',12);
		$pdf->Cell(50,$height,'Received From',0,1,'L');	

		$pdf->Ln(20);


		$pdf->Cell(50,$height,'Banyaknya Uang',0,0,'L');	
		$pdf->Ln(5);
		$pdf->Cell(50,$height,'_________________',0,0,'L');
		$pdf->setX(175);
		$pdf->Cell(50,$height,':',0,1,'L');

		$pdf->Ln(5);
		$pdf->Cell(50,$height,'The Sum of',0,1,'L');	
		$akhiry=$pdf->GetY();//echo $akhiry;
		
		//242 278
		$pdf->SetXY(195,$akhiry-36);
		$pdf->SetFillColor(220,220,220);
		$pdf->SetFont('arial','I',12);
		if ($pungutan==0)
		{
			//$pdf->MultiCell(375,20,terbilang($totaldgnppn)." rupiah",1,'L',220);
			$pdf->MultiCell(375,20,terbilang(round($totaldgnppn))." rupiah",1,'L',220);
		}
		else
		{	
			//$pdf->MultiCell(375,20,terbilang($jumlah)." rupiah",1,'L',220);
			$pdf->MultiCell(375,20,terbilang(round($jumlah))." rupiah",1,'L',220);
		}
		
		
		
		
		
		$pdf->Ln(20);
		$pdf->SetFont('arial','I',12);
		$pdf->Cell(50,$height,'Untuk Pembayaran',0,0,'L');	
		$pdf->Ln(5);
		$pdf->Cell(50,$height,'_________________',0,0,'L');
		$pdf->setX(175);
		$pdf->Cell(50,$height,':',0,1,'L');
		$pdf->Ln(5);
		$pdf->Cell(50,$height,'Being Payment of',0,1,'L');	
		
		$akhiry=$pdf->GetY();
		$pdf->SetXY(195,$akhiry-36);
		$pdf->SetFillColor(220,220,220);
		
		$str="select * from ".$dbname.".keu_penagihandt where noinvoice='".$noinvoice."' ";
		$res=mysql_query($str);
		while($bar=mysql_fetch_assoc($res)){
			$pdf->SetFont('arial','',11);
			$pdf->MultiCell(400,15,$bar['uraian'],0,'L');	
			$pdf->SetX(195);
			//$pdf->MultiCell(
		}
		
		//$akhirY3=$pdf->GetY();
		//$pdf->SetY($akhirY3-36);
		$pdf->Ln(40);
		$pdf->SetFont('arial','I',12);
		$pdf->Cell(50,$height,'Jumlah',0,0,'L');	
		$pdf->Ln(5);
		$pdf->Cell(50,$height,'_________________',0,0,'L');
		$pdf->setX(175);
		$pdf->Cell(50,$height,':',0,1,'L');
		$pdf->Ln(5);
		$pdf->Cell(50,$height,'Amount',0,1,'L');	
		$akhiry=$pdf->GetY();//echo $akhiry;
		
		//242 278
		$pdf->SetXY(195,$akhiry-36);
		$pdf->SetFillColor(220,220,220);
		$pdf->SetFont('arial','',12);
		if ($pungutan==0) 
		{
			//$pdf->MultiCell(150,40,'Rp.          '.number_format($totaldgnppn),1,'C',220);//dipungut 0=dipungut
			$pdf->MultiCell(150,40,'Rp.          '.number_format($totaldgnppn),1,'C',220);//dipungut 0=dipungut
		}
		else
		{	
			
			//$pdf->MultiCell(150,40,'Rp.          '.number_format($jumlah),1,'C',220);
			$pdf->MultiCell(150,40,'Rp.          '.number_format($jumlah),1,'C',220);
		}
		
		/*$pdf->SetFillColor(220,220,220);
		$pdf->MultiCell(200,50,'asdaasdsdfsdasdas asdsad ad asd ad asd as da q e wer we rw erwerwerwer dfsdfsdfwer',0,'L',220);*/
		
		//$pdf->setf
		
		$pdf->Ln(75);
		$pdf->SetFont('arial','',12);	
		$pdf->SetX(450);
		$pdf->Cell(50,$height,'Jakarta, '.$atanggal.' '.$abulan.' '.$atahun,0,1,'C');
		$pdf->SetX(450);
		$pdf->Cell(50,$height,'PT FAJAR BAIZURI & BROTHERS',0,1,'C');	
		$pdf->Ln(100);
		
		$pdf->SetX(450);$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,$height,'Machruzal Shaviq Ibrahim',0,0,'C');	
		//$pdf->Cell(50,$height,'H. Ibrahim Pidie',0,0,'C');	
		$pdf->SetX(450);
		$pdf->Ln(5);
		$pdf->SetX(450);
		$pdf->Cell(50,$height,'________________',0,1,'C');
		$pdf->SetX(450);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(50,$height,'Wakil Direktur Utama',0,0,'C');	
		//$pdf->Cell(50,$height,'Direktur Utama',0,0,'C');	
		$pdf->SetX(450);
			 
			
			
			
			
			
			
		$pdf->Output();
            
	break;

    default:
    break;
}
?>