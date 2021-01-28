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
$noCer=$ar[1];
$idPemilik=$ar[0];
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
				//echo $sCer;exit();
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
                "kodeorganisasi='".$orgData2['induk']."'");
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
                $query = selectQuery($dbname,'organisasi','namaorganisasi',
                "kodeorganisasi='".$rCer['kodeorg']."'");
                $orgData5 = fetchData($query);
                $this->SetFont('Arial','',8);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$rCer['kodeorg'],'',0,'L');
                $this->Ln();
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kebun'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$orgData5[0]['namaorganisasi'],'',0,'L');
				 $this->Ln();
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['nama'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',0,'L');
               
              	
                $this->Ln();
                $this->Ln();
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$_SESSION['lang']['daftarKepemilikan'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			  	$this->setX(85);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['nosertifikat'],1,0,'C',1);	
				$this->Cell(18/100*$width,$height,$_SESSION['lang']['namapemilik'],1,0,'C',1);	
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['tglSertifikat'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['luas'],1,0,'C',1);	
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['tahap'],1,1,'C',1);	
				
			
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
		
		$str="select nosertifikat,idpemilik from ".$dbname.".".$_GET['table']."  where nosertifikat='".$noCer."' "; 
		//echo $str;exit();
		$re=mysql_query($str) or die(mysql_error());
		$no=0;

		while($res=mysql_fetch_assoc($re))
		{
			
			$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$res['idpemilik']."'";
			$qPem=mysql_query($sPem) or die(mysql_error());
			$rPem=mysql_fetch_assoc($qPem);
			
			$sCer="select * from ".$dbname.".kud_sertifikat where nosertifikat='".$res['nosertifikat']."'";
			$qCer=mysql_query($sCer) or die(mysql_error());
			$rCer=mysql_fetch_assoc($qCer);
					
			$no+=1;
			$pdf->setX(85);
			$pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
			if($prefSertifikat==$res['nosertifikat'])
			{
				$pdf->Cell(15/100*$width,$height,'',1,0,'L',1);
			}
			else
			{		
				$pdf->Cell(15/100*$width,$height,$res['nosertifikat'],1,0,'L',1);
			}
			$pdf->Cell(18/100*$width,$height,$rPem['nama'],1,0,'L',1);
			$pdf->Cell(15/100*$width,$height,tanggalnormal($rCer['tanggalsertifikat']),1,0,'L',1);
			$pdf->Cell(12/100*$width,$height,$rCer['luas']." Ha",1,0,'L',1);
			$pdf->Cell(15/100*$width,$height,$rCer['tahap'],1,1,'L',1);	
			$jmlh=intval($res['jumlah']);
			$tot=$tot+$jmlh;				
			 $prefSertifikat=$res['nosertifikat'];
		}
		
        $pdf->Output();
?>
