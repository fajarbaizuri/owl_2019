<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
$nosp=$_GET['nosp'];

//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
		global $conn;
		global $dbname;
		 $this->SetFillColor(255,255,255); 
/* 
			  $str1="select namaorganisasi,alamat,wilayahkota,telepon from ".$dbname.".organisasi where kodeorganisasi='PMO'";
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   } 
*/	   
	 //  $this->SetMargins(15,10,0);
	    $this->SetMargins(15,10,0);
		$path='images/logo.jpg';
	    $this->Image($path,15,5,40);	
		$this->SetFont('Arial','B',20);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
		$this->Cell(60,15,'MINANGA GROUP',0,1,'L');	 
		//$this->SetX(55); 		
		//$this->Cell(60,5,$alamatpt,0,1,'L');
		//$this->SetX(55); 		
		//$this->Cell(60,5,'Telp:'.$telp,0,1,'L');	
			
		$this->Line(15,35,205,35);	
		$this->SetFont('Arial','',6); 	
		//$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');
				 
	}


	
	function Footer()
	{
 		global $conn;
		global $dbname;
            $str1="select namaorganisasi,alamat,wilayahkota,telepon from ".$dbname.".organisasi where kodeorganisasi='PMO'";
               $res1=mysql_query($str1);
               while($bar1=mysql_fetch_object($res1))
               {
                     $namapt=$bar1->namaorganisasi;
                     $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
                     $telp=$bar1->telepon;				 
               }            
	    $this->SetY(-15);
            $this->Line(15,275,205,275);    
	    $this->SetFont('Arial','I',8);
            $this->Cell(160,5,$alamatpt.", Tel:".$telp,0,1,'L');
	    //$this->Cell(10,5,'Page '.$this->PageNo(),0,0,'C');
	}

}
  $str="select * from ".$dbname.".sdm_suratperingatan 
        where nomor='".$nosp."'";	
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {

	//===================smbil nama karyawan
	  $namakaryawan='';
	  $strx="select a.namakaryawan,b.namajabatan from ".$dbname.".datakaryawan a 
	  left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
	  where karyawanid=".$bar->karyawanid;

	  $resx=mysql_query($strx);
	  //echo mysql_error($conn);
	  while($barx=mysql_fetch_object($resx))
	  {
	  	$namakaryawan=$barx->namakaryawan;
		$jabatanybs=$barx->namajabatan;
	  }
	  
	  $tanggal=tanggalnormal($bar->tanggal);
	  $sampai=tanggalnormal($bar->sampai);
	  $tipesp=$bar->jenissp;
	  //====================ambil tipe untuk hal
	  $ketHal='';
	  $str="select keterangan from ".$dbname.".sdm_5jenissp where kode='".$tipesp."'";
	  $rekx=mysql_query($str);
	  while($barkx=mysql_fetch_object($rekx))
	  {
	  	$ketHal=trim($barkx->keterangan);
	  }
	  //===============================

	  $paragraf1=$bar->paragraf1;
	  $pelanggaran=$bar->pelanggaran;
	  $paragraf3=$bar->paragraf3;
	  $paragraf4=$bar->paragraf4;
	  $karyawanid=$bar->karyawanid;
	  
	  $penandatangan=$bar->penandatangan;
	  $jabatan=$bar->jabatan;
	  $tembusan1=$bar->tembusan1;
	  $tembusan2=$bar->tembusan2;
	  $tembusan3=$bar->tembusan3;
	  $tembusan4=$bar->tembusan4;
  }
	$pdf=new PDF('P','mm','A4');
	$pdf->SetFont('Arial','B',14);
	$pdf->AddPage();
	$pdf->SetY(40);
	$pdf->SetFillColor(255,255,255); 
	$pdf->SetX(20);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,5,'No ',0,0,'L');
	$pdf->Cell(5,5,':',0,0,'L');
	$pdf->Cell(100,5,$nosp,0,0,'L');
	$pdf->Cell(40,5,$_SESSION['lang']['tanggal']." : ".$tanggal,0,1,'R');
	$pdf->SetX(20);
	$pdf->Cell(20,5,$_SESSION['lang']['hal1'],0,0,'L');
	$pdf->Cell(5,5,':',0,0,'L');
	$pdf->SetFont('Arial','U',10);
	$pdf->Cell(115,5,$ketHal,0,1,'L');
	$pdf->SetFont('Arial','',10);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(20);
	$pdf->Cell(20,5,$_SESSION['lang']['kepada'],0,0,'L');
	$pdf->Cell(5,5,':',0,0,'L');	
	$pdf->Cell(100,5,$namakaryawan,0,1,'L');
	$pdf->SetX(20);
	$pdf->Cell(20,5,$_SESSION['lang']['jabatan'],0,0,'L');
	$pdf->Cell(5,5,':',0,0,'L');	
	$pdf->Cell(100,5,$jabatanybs,0,1,'L');
	$pdf->SetX(20);	
	$pdf->SetFont('Arial','U',10);
	$pdf->Cell(115,5,$_SESSION['lang']['ditempat'],0,1,'L');
	$pdf->SetFont('Arial','',10);			
	$pdf->Ln();
	$pdf->Ln();		
	$pdf->SetX(20);			
    $pdf->MultiCell(170,5,$paragraf1,0,'J');	
	$pdf->Ln();
	$pdf->SetX(20);	
	$pdf->SetFont('Arial','I',10);		
    $pdf->MultiCell(170,5,$pelanggaran,0,'J');
	$pdf->Ln();	
	$pdf->SetFont('Arial','',10);				
	$pdf->SetX(20);			
    $pdf->MultiCell(170,5,$paragraf3,0,'J');
	$pdf->Ln();	
	$pdf->SetX(20);			
    $pdf->MultiCell(170,5,$paragraf4,0,'J');			
	
//=========penandatangan
	$pdf->Ln();	
	$pdf->Ln();
	$pdf->Ln();			
	$pdf->SetX(20);
	$pdf->Cell(40,5,'PT.PERKEBUNAN MINANGA OGAN',0,1,'L');
	$pdf->Ln();
	$pdf->Ln();			
	$pdf->Ln();	
	$pdf->SetX(20);
	$pdf->Cell(40,5,"".$penandatangan." ",'B',1,'L');
	$pdf->SetX(20);
	$pdf->SetFont('Arial','',10);	
	$pdf->Cell(40,5,"".$jabatan." ",0,1,'L');	
//=====================tembusan	
	$pdf->Ln();
	$pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(i)   : ".$tembusan1,0,1,'L');
	$pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(ii)  : ".$tembusan2,0,1,'L');			
	$pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(iii) : ".$tembusan3,0,1,'L');			
	$pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(iv) : ".$tembusan4,0,1,'L');			
				
//footer================================
    $pdf->Ln();		
	$pdf->Output();
	
?>
