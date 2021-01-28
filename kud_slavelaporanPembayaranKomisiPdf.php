<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/fpdf.php');

$proses = $_GET['proses'];
$kdOrg=$_GET['cmpId'];
$tgl=explode('-',$_GET['period']);
$tahap=$_GET['tahap'];
$period=$_GET['period'];


$param = $_POST;
//$where=" kodeorg='".$kdOrg."' and tanggal like '%".$tngl."%'";

/** Report Prep **/
$cols = 'no,notransaksi,namapemilik,nosertifikat,tanggalbayar,namapenerima,dibayaroleh,mengetahui,jumlah,tahap';
$colArr = explode(',',$cols);

//$query = selectQuery($dbname,'kebun_curahhujan','kodeorg, tanggal, pagi, sore, catatan',$where);

//$data = fetchData($query);

$title = $_SESSION['lang']['laporanPembayaranKomisi'];
$align = explode(",","L,L,R,R,L");
$length = explode(",","3,12,11,12,8,12,11,10,11,10");

/** Output Format **/
switch($proses) {
    case 'pdf':
        class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $kdOrg;
				global $tgl;
				global $data;
				global $period;
                
                # Bulan
               // $optBulan = 
                
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
                
                if($kdOrg=='0')
				{
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$period,'',0,'L');$this->Ln();
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),0,0,'L');$this->Ln();
					 $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(15/100*$width,$height,
					$_SESSION['standard']['username'],0,0,'L');
				}
				else
				{
					$this->SetFont('Arial','',8);
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$kdOrg,'',0,'L');
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(15/100*$width,$height,
					   $_SESSION['standard']['username'],0,0,'L');
					$this->Ln();
					$query2 = selectQuery($dbname,'organisasi','namaorganisasi',
					"kodeorganisasi='".$kdOrg."'");
					$orgData2 = fetchData($query2);
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kebun'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$orgData2[0]['namaorganisasi'],'',0,'L');
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$tgl[1]."-".$tgl[0],'',0,'L');
				}
				
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			   // $this->Cell(10/100*$width,$height,'No',1,0,'C',1);
                foreach($colArr as $key=>$head) {
                    $this->Cell($length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                }
                $this->Ln();
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

if(($kdOrg=='0')&&($tahap=='0'))
		{
				//echo"warning:a";
			$query="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where a.periode='".$period."' order by a.tanggalbayar desc";	
		}
			elseif(($kdOrg!='0')&&($tahap=='0'))
			{
				//echo"warning:b";
				$query="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where b.kodeorg='".$kdOrg."' and a.periode='".$period."' order by a.tanggalbayar desc";
			}
			elseif(($kdOrg=='0')&&($tahap!='0'))
			{
			//	echo"warning:c";
				$query="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where tahap='".$tahap."' and a.periode='".$period."' order by a.tanggalbayar desc";
			}
			elseif(($kdOrg!='0')&&($tahap!='0'))
		{
			//echo"warning:d";
			$query="select * from ".$dbname.".kud_pembayaran a inner join ".$dbname.".kud_sertifikat b on a.nosertifikat=b.nosertifikat where b.kodeorg='".$kdOrg."' and a.periode='".$period."' and tahap='".$tahap."' order by a.tanggalbayar desc";
		}
		$qCrh=mysql_query($query) or die(mysql_error());
		$rCek=mysql_num_rows($qCrh);
		if($rCek>0)
		{
			while($rCrh=mysql_fetch_assoc($qCrh))
			{
				
				$no+=1;
				$sPem="select nama from ".$dbname.".kud_pemilik where idpemilik='".$rCrh['idpemilik']."'";
				$qPem=mysql_query($sPem) or die(mysql_error());
				$rPem=mysql_fetch_assoc($qPem);
				$pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
				$pdf->Cell(12/100*$width,$height,$rCrh['notransaksi'],1,0,'L',1);
				$pdf->Cell(11/100*$width,$height,$rPem['nama'],1,0,'L',1);
				$pdf->Cell(12/100*$width,$height,$rCrh['nosertifikat'],1,0,'L',1);
				$pdf->Cell(8/100*$width,$height,tanggalnormal($rCrh['tanggalbayar']),1,0,'C',1);
				$pdf->Cell(12/100*$width,$height,$rCrh['namapenerima'],1,0,'L',1);
				$pdf->Cell(11/100*$width,$height,$rCrh['dibayaroleh'],1,0,'L',1);
				$pdf->Cell(10/100*$width,$height,$rCrh['mengetahui'],1,0,'L',1);
				$pdf->Cell(11/100*$width,$height,"Rp. ".number_format($rCrh['jumlah'],2),1,0,'R',1);
				$pdf->Cell(10/100*$width,$height,$rCrh['tahap'],1,1,'L',1);							
			}
		}
		else
		{
			$pdf->Cell($width,$height,'Not Found',1,1,'C',1);
		}
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>