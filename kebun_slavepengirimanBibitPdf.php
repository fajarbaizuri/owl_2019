<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

/*echo "<pre>";
print_r($_GET);
echo "</pre>";
*/
$table = $_GET['table'];
$column = $_GET['column'];
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
				global $periode;
				
                
				$periode=$_GET['column'];

                
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
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,
                   $periode,0,0,'L');
                $this->Ln();
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,date('d-m-Y H:i:s'),'',0,'L');
              	
              
				
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$_SESSION['lang']['pengirimanBibit'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			   // $this->Cell(10/100*$width,$height,'No',1,0,'C',1);
                /*foreach($colArr as $key=>$head) {
                    $this->Cell($length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                }*/
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(13/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);	
				$this->Cell(13/100*$width,$height,$_SESSION['lang']['namaorganisasi'],1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['nmcust'],1,0,'C',1);
				$this->Cell(13/100*$width,$height,$_SESSION['lang']['OrgTujuan'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);						
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['jenisbibit'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);		
				$this->Cell(18/100*$width,$height,$_SESSION['lang']['namakegiatan'],1,1,'C',1);

            
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
		
		$str="select * from ".$dbname.".".$_GET['table']."   where tanggal like '%".$periode."%'"; //echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		while($res=mysql_fetch_assoc($re))
		{
			$sKdOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$res['kodeorg']."'";
			$qKdOrg=mysql_query($sKdOrg) or die(mysql_error($conn));
			$rKdOrg=mysql_fetch_assoc($qKdOrg);
			
			$sKeg="select namakegiatan from ".$dbname.".setup_kegiatan where kodekegiatan='".$res['kodekegiatan']."'";
			$qKeg=mysql_query($sKeg) or die(mysql_error($conn));
			$rKeg=mysql_fetch_assoc($qKeg);
			
			$sCust="select namacustomer from ".$dbname.".pmn_4customer where kodecustomer='".$res['pembeliluar']."'";
			$qCust=mysql_query($sCust) or die(mysql_error($conn));
			$rCust=mysql_fetch_assoc($qCust);
			
			$sKdOrg2="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$res['orgtujuan']."'";
			$qKdOrg2=mysql_query($sKdOrg2) or die(mysql_error($conn));
			$rKdOrg2=mysql_fetch_assoc($qKdOrg2);
			
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
			$pdf->Cell(13/100*$width,$height,$res['notransaksi'],1,0,'L',1);	
			$pdf->Cell(13/100*$width,$height,$rKdOrg['namaorganisasi'],1,0,'L',1);
			$pdf->Cell(15/100*$width,$height,$rCust['namacustomer'],1,0,'L',1);
			$pdf->Cell(13/100*$width,$height,$rKdOrg2['namaorganisasi'],1,0,'L',1);
			$pdf->Cell(8/100*$width,$height,tanggalnormal($res['tanggal']),1,0,'C',1);						
			$pdf->Cell(10/100*$width,$height,$res['jenisbibit'],1,0,'L',1);
			$pdf->Cell(8/100*$width,$height,number_format($res['jumlah'],2),1,0,'R',1);		
			$pdf->Cell(18/100*$width,$height,$rKeg['namakegiatan'],1,1,'L',1);
		}
	
        $pdf->Output();
?>
