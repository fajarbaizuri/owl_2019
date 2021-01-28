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

$table = $_GET['table'];
$ar=explode(",",$column = $_GET['column']);
$periode=$ar[1];
$idPemilik=$ar[0];
$noCer=$ar[2];
$where = $_GET['cond'];
//=============

 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $idPemilik;
				global $periode;
				global $noCer;
                
				$sCer="select kodeorg  from ".$dbname.".kud_sertifikat where nosertifikat='".$noCer."'";
				//echo "warning:".$sCer;exit();
				$qCer=mysql_query($sCer) or die(mysql_error());
				$rCer=mysql_fetch_assoc($qCer);
				
				$sPem="select nama  from ".$dbname.".kud_pemilik where idpemilik='".$idPemilik."'";
				$qPem=mysql_query($sPem) or die(mysql_error());
				$rPem=mysql_fetch_assoc($qPem);
				$query = selectQuery($dbname,'organisasi','induk',
				"kodeorganisasi='".$rCer['kodeorg']."'");
				$orgData2 = fetchData($query);
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                "kodeorganisasi='".$orgData2[0]['induk']."'");
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
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['namapemilik'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$rPem['nama'],'',0,'L');
                $this->Ln();
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$periode,'',0,'L');
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['nama'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',0,'L');
				$this->Ln();
				$query = selectQuery($dbname,'organisasi','namaorganisasi',
				"kodeorganisasi='".$rCer['kodeorg']."'");
				$orgData25 = fetchData($query);
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kebun'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$rCer['kodeorg']." [".$orgData25[0]['namaorganisasi']."]",'',0,'L');
               
              	
            	$this->Ln();
				$this->Ln();
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$_SESSION['lang']['BuktiPembayaran'],0,1,'C');	
                $this->Ln();	
				$this->Ln();
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			  	$this->setX(85);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['nosertifikat'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['tanggalbayar'],1,0,'C',1);	
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['namapenerima'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['dibayaroleh'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['mengetahui'],1,0,'C',1);	
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);						
			
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		
		$str="select jumlah,nosertifikat,tanggalbayar,namapenerima,dibayaroleh,mengetahui from ".$dbname.".".$_GET['table']."  where idpemilik='".$idPemilik."' and statuspembayaran='1' and nosertifikat='".$noCer."'"; 
		//echo $str;exit();
		$re=mysql_query($str) or die(mysql_error());
		$no=0;

		while($res=mysql_fetch_assoc($re))
		{
					
			$no+=1;
			$pdf->setX(85);
			$pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
			$pdf->Cell(15/100*$width,$height,$res['nosertifikat'],1,0,'L',1);
			$pdf->Cell(12/100*$width,$height,tanggalnormal($res['tanggalbayar']),1,0,'L',1);
			$pdf->Cell(15/100*$width,$height,$res['namapenerima'],1,0,'L',1);
			$pdf->Cell(12/100*$width,$height,$res['dibayaroleh'],1,0,'L',1);
			$pdf->Cell(12/100*$width,$height,$res['mengetahui'],1,0,'L',1);	
			$pdf->Cell(15/100*$width,$height,"Rp. ".number_format($res['jumlah'],2),1,1,'R',1);	
			$jmlh=intval($res['jumlah']);
			$tot=$tot+$jmlh;				
			$_SESSION['temp']['penerima']=$res['namapenerima'];
			$_SESSION['temp']['bayaroleh']=$res['dibayaroleh'];
			$_SESSION['temp']['tahui']=$res['mengetahui'];
		}
			$pdf->setX(85);
			$pdf->Cell(69/100*$width,$height,$_SESSION['lang']['grnd_total'],1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,"Rp. ".number_format($tot,2),1,1,'R',1);
			$pdf->Ln();

			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial','B',8);
			$pdf->setX(85);
			$pdf->Cell((38/100*$width)-5,$height,$_SESSION['lang']['namapenerima'],'',0,'L');
			$pdf->Cell((38/100*$width)-5,$height,$_SESSION['lang']['dibayaroleh'],'',0,'L');
			$pdf->Cell((38/100*$width)-5,$height,$_SESSION['lang']['mengetahui'],'',0,'L');
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			$pdf->setX(85);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell((38/100*$width)-5,$height,ucfirst(strtolower($_SESSION['temp']['penerima'])),'',0,'L');
			$pdf->Cell((38/100*$width)-5,$height,ucfirst(strtolower($_SESSION['temp']['bayaroleh'])),'',0,'L');
			$pdf->Cell((38/100*$width)-5,$height,ucfirst(strtolower($_SESSION['temp']['tahui'])),'',0,'L');
			$pdf->Ln();
			
			/*$pdf->Cell(5,$height,':','',0,'L');
			$pdf->Cell(45/100*$width,$height,$rPem['nama'],'',0,'L');
			$pdf->Ln();
			$pdf->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
			$pdf->Cell(5,$height,':','',0,'L');
			$pdf->Cell(45/100*$width,$height,$periode,'',0,'L');
			$pdf->Ln();
			$pdf->Cell((20/100*$width)-5,$height,$_SESSION['lang']['nama'],'',0,'L');
			$pdf->Cell(5,$height,':','',0,'L');
			$pdf->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',0,'L');	*/
        $pdf->Output();
?>
