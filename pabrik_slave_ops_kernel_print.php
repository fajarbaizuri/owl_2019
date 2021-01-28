<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_GET;
$title = 'Log Mesin > Kernel';

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
		$q = selectQuery($dbname,'pabrik_ops_kernelht','*',"tanggal between '".
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
		$cols = "waktu,rm1start,rm1stop,rm1jlh";
		$colArr = explode(',',$cols);
		
		## Get Periode
		$tanggal1 = $_SESSION['org']['period']['start'];
		$tanggal2 = $_SESSION['org']['period']['end'];
		
		$q = selectQuery($dbname,'pabrik_ops_kerneldt',$cols,"notransaksi='".
			$param['notransaksi']."'");
		$data = fetchData($q);
		
		## Start PDF
		$pdf = new zPdfMaster('P','pt','A4');
		$pdf->setAttr1($title,$align,$length,$colArr);
		$pdf->_subTitle = "No Transaksi: ".$param['notransaksi'];
		//$pdf->_noThead=true;
		$pdf->AddPage();
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->SetFont('Arial','',8);
		foreach($data as $row) {
			$i=0;
			foreach($row as $head=>$val) {
				$pdf->Cell($length[$i]/100*$width,$height,$row[$head],0,0,$align[$i]);
				$i++;
			}
			$pdf->Ln();
		}
		$pdf->Output();
		break;
    default:
	break;
}
?>