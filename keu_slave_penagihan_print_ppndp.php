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
							$this->SetDrawColor(127, 127, 255);
							$this->SetLineWidth(1);
							$this->Line(30,85,390,85);
							$this->Line(515,85,570,85);
							$this->SetLineWidth();
							
							$this->Line(30,87,390,87);
							$this->Line(515,87,570,87);
							$this->SetXY(410,80);
							$this->SetFont('Arial','ib',14);
							//$this->SetFillColor(225,225,225);
							$this->SetTextColor(0, 0, 255);
							$this->Cell(25,$height,'I N V O I C E',0,1,'L');		
							$this->SetTextColor(0, 0, 0, 100);
							$this->SetDrawColor(0, 0, 0, 50);
							$this->Ln(20);
							$this->SetFont('Arial','',10);
							$this->SetX(350);
							$this->Cell(20,$height,'No. Invoice',0,0,'L');
							$this->SetX(430);
							$this->Cell(20,$height,'     :     '.$noinvoice,0,1,'L');
							$this->SetX(350);
							$this->Cell(20,$height,'Tanggal Invoice',0,0,'L');//
                            $this->SetX(430);
							//$this->Cell(20,$height,'     :     '.hari(substr($tanggal,0,10)).', '.tanggalnormal(substr($tanggal,0,10)),0,1,'L');
							$this->Cell(20,$height,'     :     '.$atanggal.' '.$abulan.' '.$atahun,0,1,'L');
							
							
							##kotak yang isinya keterangan pembeli
							$acus="select * from ".$dbname.".pmn_4customer where kodecustomer='".$kodecustomer."'";
							//echo $acus;
							$bcus=mysql_query($acus) or die (mysql_error());
							$ccus=mysql_fetch_assoc($bcus);
								$custnmpt=$ccus['namacustomer'];
								$custcp=$ccus['kontakperson'];
								$custalamat=$ccus['alamat'];
								$custtel=$ccus['telepon'];
								$custfax=$ccus['fax'];
								$custkota=$ccus['kota'];
							
							$this->SetDrawColor(0,0,0);
							$this->Rect(50,125,290,110);
							$this->SetXY(150,130);
							$this->SetFont('Arial','BI',10);
							$this->Cell(20,$height,'Kepada :',0,0,'L');
							$this->Ln(15);
							$this->SetFont('Arial','',10);
							$this->SetX(60);
							$this->cell(70,$height,Perusahaan,0,0,'L');
							$this->setx(130);
							$this->cell(20,$height,':',0,0,'L');
							$this->setx(140);
							$this->cell(20,$height,$custnmpt,0,1,'L');
							$this->SetX(60);
							$this->cell(70,$height,'Nama Kontak',0,0,'L');
							$this->setx(130);
							$this->cell(20,$height,':',0,0,'L');
							$this->setx(140);
							$this->cell(20,$height,$custcp,0,1,'L');
							$this->SetX(60);
							$this->cell(70,$height,'Alamat',0,0,'L');
							$this->setx(130);
							$this->cell(20,$height,':',0,0,'L');
							$this->setx(140);
							$this->cell(150,$height,$custalamat,0,1,'L');
							$this->setx(140);
							$this->cell(20,$height,$custkota,0,1,'L');
							$this->SetX(60);
							$this->cell(70,$height,'Telp.',0,0,'L');
							$this->setx(130);
							$this->cell(20,$height,':',0,0,'L');
							$this->setx(140);
							$this->cell(20,$height,$custtel,0,1,'L');
							$this->SetX(60);
							$this->cell(70,$height,'Fax.',0,0,'L');
							$this->setx(130);
							$this->cell(20,$height,':',0,0,'L');
							$this->setx(140);
							$this->cell(20,$height,$custfax,0,1,'L');
							
							//$this->SetX(90); 		
			            
							$height = 12;
                            $this->Ln(35);
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(220,220,220);
							$akhiryatas=$this->GetY();
							$this->SetLineWidth(0);
							$this->Line(28.5,$akhiryatas-1,589,$akhiryatas-1);
							$this->SetLineWidth(1);
							$this->Line(28.5,$akhiryatas-3,589,$akhiryatas-3);
							
							//$this->Line(28.5,$akhiryatas+15,583,$akhiryatas+15);

							$this->SetLineWidth(0);
                                            $this->Cell(3/100*$width,26,'',0,0,'C',1);		
                                            $this->Cell(65/100*$width,26,'',0,0,'C',1);
											$this->Cell(12/100*$width,13,'Kuantitas',0,0,'C',1);
											$this->Cell(11/100*$width,13,'Harga Satuan',0,0,'C',1);
											$this->Cell(13/100*$width,12,'Jumlah',0,1,'C',1);
											
											$this->Cell(3/100*$width,'',No,0,0,'C',1);	
											$this->Cell(65/100*$width,'',Uraian,0,0,'C',1);
													
  
											$this->Cell(12/100*$width,13,'(Kg)',0,0,'C',1);
											$this->Cell(11/100*$width,13,'(Rp)',0,0,'C',1);
											$this->Cell(13/100*$width,13,'(Rp)',0,1,'C',1);
										
                       }

                        function Footer()
                        {
							#script footer untuk pengisian bawah
                            $this->SetY(-40);
							$this->SetX(10);
                            $this->SetFont('Arial','I',10);
                            //$this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
							$this->Cell(10,15,'Mohon ditransfer ke rek. PT Fajar Baizuri & Brothers. Rek : 0122.01.000730.30.2 BRI Cabang Jatinegara Jakarta Timur',0,0,'L');
							$akhirY=$this->GetY();//echo $akhirY;
							$akhirY2=$akhirY-2;
							$this->SetLineWidth(0);
							$this->Line(10,$akhirY,570,$akhirY);
							$this->SetLineWidth(1);
							$this->Line(10,$akhirY2,570,$akhirY2);
							
                        }
                    }
                    $pdf=new PDF('P','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 14;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',10);
		
		#ambil data detail untuk isian					
		$str="select * from ".$dbname.".keu_penagihandt where noinvoice='".$noinvoice."' ";
		$res=mysql_query($str);
		$no=$total=0;
		#perulangan isian
		while($bar=mysql_fetch_assoc($res)) {	
			$no+=1;	
			$pdf->SetFont('arial','',8);
			$pdf->Cell(3/100*$width,$height,$no,0,0,'C',1);	
			$pdf->SetFont('arial','',8);
			$pdf->Cell(65/100*$width,$height,$bar['uraian'],0,0,'L',1);	
			$pdf->SetFont('arial','',9);
			$pdf->Cell(12/100*$width,$height,number_format($bar['kuantitas']).' ',0,0,'R',1);		
			$pdf->Cell(11/100*$width,$height,number_format($bar['hargasatuan'],2),0,0,'R',1);
			$pdf->Cell(13/100*$width,$height,number_format($bar['nilaitransaksi']),0,1,'R',1);
			//$pdf->SetY($akhircoly);
			$subtotal+=$bar['nilaitransaksi'];	
		}
		#untuk panjang pengisian dengan max data 13 data
		for($no+=1;$no<=13;$no++)
		{	
			$pdf->Cell(5/100*$width,$height,'',0,0,'C',1);	
			$pdf->Cell(50/100*$width,$height,'',0,0,'L',1);			
			$pdf->Cell(10/100*$width,$height,'',0,0,'R',1);		
			$pdf->Cell(15/100*$width,$height,'',0,0,'R',1);	
			$pdf->Cell(15/100*$width,$height,'',0,1,'R',1);	
		}
			
		#pembuatan garis untuk isi data dipatok tidak lebih dari 13 data				
		$pdf->Line(28.5,250,28.5,419);
		$pdf->Line(45,250,45,419);
		$pdf->Line(403,250,403,419);
		$pdf->Line(456,250,456,419);
		$pdf->Line(520,250,520,419);
		$pdf->Line(589,250,589,419);
		#garis bawahnya
		$pdf->Line(28.5,420,589,420);
		
		#ini garis untuk kolom judul yg bawah (cara manual di patok)
		$pdf->Line(28.5,277,589,277);

		#panggil data untuk pengisian data dibawah
		$a="select * from ".$dbname.".keu_penagihanht where noinvoice='".$noinvoice."'";
		$b=mysql_query($a) or die (mysql_error());
		$c=mysql_fetch_assoc($b);
			$uangmuka=$c['uangmuka'];
			$ppn=$c['nilaippn'];
			$potongan=$c['potongan'];
			$tanggal=$c['tanggal'];
				$abulan=numToMonth(substr($tanggal,5,2),$lang='I',$format='long');//echo $abulan;
				$atanggal=substr($tanggal,8,2);
				$atahun=substr($tanggal,0,4);
			$keterangan=$c['keterangan'];
			$pungut=$c['pungutan'];
	
		
		#untuk ppn dp
		$uangppndp=10/100*$uangmuka;
		
		#untuk jumlah
		//$jumlah=$subtotal-$potongan-$uangmuka;
		//$jumlah=$subtotal-$uangmuka+$uangppndp;
		
		$jumlah=$subtotal-$uangmuka;
		/*Ratna
		$jumlah=$subtotal-$uangmuka-$potongan;
		*/
		#untuk ppn
		$nilaippn=$ppn/100*($jumlah-$potongan);
		
		
		if ($pungut=="1"){
			#untuk total
			$total=$jumlah-$potongan;
			/*ratna
			$total=$jumlah;
			*/
		}else{
			#untuk total
			
			$total=$nilaippn+$jumlah-$potongan;
				/*ratna
			$total=$nilaippn+$jumlah;
			*/
		}
		
		
		
		
		
		#untuk kalkulasi dari subtotal sampai total
		//$akhirybawahcol=$pdf->GetY();
		//$pdf->SetY($akhirybawahcol-68);
		$pdf->Ln(-38);
		$pdf->SetFont('arial','B',9);
		#sub total
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'Sub Total',0,0,'L',1);
		
		$pdf->Cell(13/100*$width,$height,number_format($subtotal),1,1,'R',1);
		#uang muka
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'Uang Muka',0,0,'L',1);	
		$pdf->Cell(13/100*$width,$height,number_format($uangmuka),1,1,'R',1);
		/* Ratna
		#potongan
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'Potongan / Klaim',0,0,'L',1);		
		$pdf->Cell(13/100*$width,$height,number_format($potongan),1,1,'R',1);
		*/
		#ppn dp
		
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'PPN DP',0,0,'L',1);	
		$pdf->Cell(13/100*$width,$height,number_format($uangppndp),1,1,'R',1);
		
		
		##jumlah
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'Jumlah',0,0,'L',1);	
		$pdf->Cell(13/100*$width,$height,number_format($jumlah),1,1,'R',1);
		
		
		
		#potongan
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'Potongan / Klaim',0,0,'L',1);		
		$pdf->Cell(13/100*$width,$height,number_format($potongan),1,1,'R',1);
		
		##ppn
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'PPN',0,0,'L',1);		

		$pdf->Cell(13/100*$width,$height,number_format($nilaippn),1,1,'R',1);
		
		##total
		$pdf->SetFont('arial','B',9);
		$pdf->Cell(71/100*$width,$height,'',0,0,'L',1);	
		$pdf->Cell(20/100*$width,$height,'TOTAL',0,0,'L',1);
			
		$pdf->Cell(13/100*$width,$height,number_format($total),1,1,'R',1);
		
		#untuk kotak terbilang		
		$akhirYt=$pdf->GetY();//echo $akhirYt;
		$pdf->Line(105,$akhirYt-18,360,$akhirYt-18);
		$pdf->Line(360,$akhirYt-18,360,$akhirYt+20);
		$pdf->Line(360,$akhirYt+20,28.5,$akhirYt+20);		
		$pdf->Line(28.5,$akhirYt+20,28.5,$akhirYt-18);	
		$pdf->Line(28.5,$akhirYt-18,50,$akhirYt-18);
		
		#isi dari kotak terbilang
		$akhirY=$pdf->GetY();
		$pdf->SetXY(50,$akhirY-25);
		$pdf->SetFont('Arial','BI',10);
		$pdf->Cell(60,$height,'Terbilang :',0,1,'L');
		$pdf->SetX(50);
		$pdf->SetFont('Arial','I',10);
		//$pdf->Cell(60,$height,terbilang($total),0,1,'L');
		$pdf->MultiCell(300,$height,terbilang(round($total)).' rupiah',0,'L');
		
		
		##untuk isian diatas ttd pak ibrahim
		$pdf->Ln(30);
		$pdf->SetFont('arial','',10);	
		$pdf->SetX(450);
		$pdf->Cell(50,$height,'Jakarta, '.$atanggal.' '.$abulan.' '.$atahun,0,1,'C');
		$pdf->SetX(450);
		$pdf->Cell(50,$height,'PT FAJAR BAIZURI & BROTHERS',0,1,'C');	
		$pdf->Ln(100);
		
		$akhirY=$pdf->GetY();//echo $akhirY;
		#kotak untuk keterangan
		$pdf->Line(120,$akhirY,340,$akhirY);
		$pdf->Line(340,$akhirY,340,$akhirY+50);
		$pdf->Line(340,$akhirY+50,28.5,$akhirY+50);		
		$pdf->Line(28.5,$akhirY+50,28.5,$akhirY);	
		$pdf->Line(28.5,$akhirY,50,$akhirY);
		
		#isi dalam kotak keterangan
		$akhirY=$pdf->GetY();
		$pdf->SetXY(50,$akhirY-6);
		$pdf->SetFont('Arial','BI',10);
		$pdf->Cell(50,$height,'Keterangan :',0,1,'L');	
		$pdf->SetX(50);
		$pdf->SetFont('Arial','',10);
		
		if ($keterangan==0)
		{
			$isiket='';
		}
		else
		{
			$isiket='PPN Tidak Dipungut Sesuai PP Tempat Penimbunan Berikat';
		}
		$pdf->MultiCell(275,$height,$isiket,0,'L');
		
		#ttd pak ibrahim di bawah
		$akhirY=$pdf->GetY();
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY(450,$akhirY-25);
		$pdf->Cell(50,$height,'Machruzal Shaviq Ibrahim',0,0,'C');	
		//$pdf->Cell(50,$height,'H. Ibrahim Pidie',0,0,'C');	
		//$pdf->Cell(50,$height,'Asdar Hasan',0,0,'C');	
		$pdf->SetX(450);
		$pdf->Ln(5);
		$pdf->SetX(450);
		$pdf->Cell(50,$height,'________________',0,1,'C');
		$pdf->SetX(450);
		
		$pdf->SetFont('Arial','',10);
		//$pdf->Cell(50,$height,'Direktur Utama',0,0,'C');	
		$pdf->Cell(50,$height,'Wakil Direktur Utama',0,0,'C');	
		//$pdf->Cell(50,$height,'Direktur Akuntansi & Keuangan',0,0,'C');	
		$pdf->SetX(450);
		 
			
		$pdf->Output();
            
	break;

    default:
    break;
}
?>