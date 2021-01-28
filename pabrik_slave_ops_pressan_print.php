<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_GET;
$title = 'Log Mesin > Pressan';

switch($proses) {
    case 'all':
		$alignStr = 'C,C,C,C';
		$align = explode(',',$alignStr);
		$length = array(25,25,25,25);
		$cols = 'notransaksi,kodeorg,tanggal,diperiksa';
		$colArr = explode(',',$cols);
		
		## Get Periode
		$tanggal1 = $_SESSION['org']['period']['start'];
		$tanggal2 = $_SESSION['org']['period']['end'];
		
		## Options
		$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
		
		## Get Data
		$q = selectQuery($dbname,'pabrik_ops_pressanht','*',"tanggal between '".
			$tanggal1."' and '".$tanggal2."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'");
		$data = fetchData($q);
		
		## Start PDF
		$pdf = new zPdfMaster('P','pt','A4');
		$pdf->setAttr1($title,$align,$length,$colArr);
		//$pdf->_noThead=true;
		$pdf->AddPage();
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->SetFont('Arial','',8);
		foreach($data as $row) {
			$pdf->Cell(25/100*$width,$height,$row['notransaksi'],0,0,'C');
			$pdf->Cell(25/100*$width,$height,$optOrg[$row['kodeorg']],0,0,'C');
			$pdf->Cell(25/100*$width,$height,tanggalnormal($row['tanggal']),0,0,'C');
			$pdf->Cell(25/100*$width,$height,$row['diperiksa'],0,1,'C');
		}
		$pdf->Output();
		break;
    case 'detail':
		$align = array('C','C','C','C');
		$length = array(25,25,25,25);
		$cols = 'waktu,start,sdhi,tekananbar1';
		$colArr = explode(',',$cols);
		$col1 = 'time(waktu) as waktu,(deg1hour+deg2hour+deg3hour) as hour,'.
			'(deg1amp+deg2amp+deg3amp) as amp,(deg1c+deg2c+deg3c) as c';
		
		## Get Periode
		$tanggal1 = $_SESSION['org']['period']['start'];
		$tanggal2 = $_SESSION['org']['period']['end'];
		
		## Data Log
		$q1 = selectQuery($dbname,'pabrik_ops_pressandtb',$col1,"notransaksi='".
			$param['notransaksi']."'");
		$data1 = fetchData($q1);
		
		## Data Mesin
		$q2 = selectQuery($dbname,'pabrik_ops_presandta','*',"notransaksi='".
			$param['notransaksi']."'");
		$data2 = fetchData($q2);
		
		## Start PDF
		$pdf = new zPdfMaster('L','pt','A4');
		$pdf->setAttr1($title,$align,$length,$colArr);
		$pdf->_subTitle = "No Transaksi: ".$param['notransaksi'];
		$pdf->_noThead=true;
		$pdf->AddPage();
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->SetFont('Arial','',8);
		
		## Data Log
		if(empty($data1)) {
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell($width,$height,'Data Log: Data Kosong',0,1,'L');
			$pdf->SetFont('Arial','',8);
		} else {
			$pdf->SetFont('Arial','BU',8);
			$pdf->Cell($width,$height,'Data Log',0,1,'L');
			$pdf->SetFont('Arial','',8);
			$len = 100/count($data1[0]);
			foreach($data1[0] as $head=>$val) {
				$pdf->SetFillColor(220,220,220);
				if(isset($_SESSION['lang'][$head])) {
					$pdf->Cell($len/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
				} else {
					$pdf->Cell($len/100*$width,$height,$head,1,0,'C',1);
				}
			}
			$pdf->Ln();	
			foreach($data1 as $row) {
				foreach($row as $val) {
					$pdf->Cell($len/100*$width,$height,$val,1,0,'C');
				}
				$pdf->Ln();
			}
		}
		$pdf->Ln();
		
		## Data Mesin
		if(empty($data2)) {
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell($width,$height,'Data Mesin: Data Kosong',0,1,'L');
			$pdf->SetFont('Arial','',8);
		} else {
			$pdf->SetFont('Arial','BU',8);
			$pdf->Cell($width,$height,'Data Mesin',0,1,'L');
			$pdf->SetFont('Arial','',8);
			$len = 100/count($data2[0]);
			foreach($data2[0] as $head=>$val) {
				$pdf->SetFillColor(220,220,220);
				if(isset($_SESSION['lang'][$head])) {
					$pdf->Cell($len/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
				} else {
					$pdf->Cell($len/100*$width,$height,$head,1,0,'C',1);
				}
			}
			$pdf->Ln();	
			foreach($data2 as $row) {
				foreach($row as $val) {
					$pdf->Cell($len/100*$width,$height,$val,1,0,'C');
				}
				$pdf->Ln();
			}
		}
		$pdf->Ln();
		$pdf->Output();
		break;
    default:
	break;
}
?>