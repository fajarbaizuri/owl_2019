<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/

/*$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
*///=============

 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;

				
                


                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
                $path='images/logo.jpg';
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                
                $this->SetFont('Arial','',8);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',0,'L');
                $this->Ln();
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,date('d-m-Y H:i:s'),'',0,'L');
              	
              
				
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$_SESSION['lang']['pembayaranKomisi'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			   
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);	
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['nosertifikat'],1,0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggalbayar'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['namapenerima'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['dibayaroleh'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['mengetahui'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);			
						
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		
		$str="select * from ".$dbname.".".$_GET['table']." order by `tanggalbayar` desc"; //echo $str;exit();
		$re=mysql_query($str) or die(mysql_error());
		$no=0;
		while($res=mysql_fetch_assoc($re))
		{
			$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$res['idpemilik']."'";
			$qPem=mysql_query($sPem) or die(mysql_error());
			$rPem=mysql_fetch_assoc($qPem);
					
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
			$pdf->Cell(12/100*$width,$height,$res['notransaksi'],1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$rPem['nama'],1,0,'C',1);	
			$pdf->Cell(12/100*$width,$height,$res['nosertifikat'],1,0,'C',1);	
			$pdf->Cell(10/100*$width,$height,tanggalnormal($res['tanggalbayar']),1,0,'C',1);	
			$pdf->Cell(12/100*$width,$height,$res['namapenerima'],1,0,'C',1);	
			$pdf->Cell(12/100*$width,$height,$res['dibayaroleh'],1,0,'C',1);	
			$pdf->Cell(12/100*$width,$height,$res['mengetahui'],1,0,'C',1);
			$pdf->Cell(8/100*$width,$height,number_format($res['jumlah']),1,1,'C',1);						
		}
	
        $pdf->Output();
?>
