<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

$proses = $_GET['proses'];
$param = $_GET;

//$noakun=$_GET['noakun'];
//$tipetransaksi=$_GET['tipetransaksi'];
//$notransaksi=$_GET['notransaksi'];
//$kodeorg=$_GET['kodeorg'];

/** Report Prep **/
$cols = array();

#=============================== Header =======================================
$col1 = 'noinvoice,tipeinvoice,tanggal,nopo,kodesupplier,nilaiinvoice,jatuhtempo,keterangan';
//$cols = array('nomor','noakun','namaakun','matauang','debet','kredit');
$cols1 = explode(',',$col1);
$whereH = "noinvoice='".$param['noinvoice']."'";
$queryH = selectQuery($dbname,'keu_tagihanht',$col1,$whereH);
$resH = fetchData($queryH);

//# Get Nama Pembuat
//$userId = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
//    "karyawanid='".$resH[0]['userid']."'");
//# Get Nama Akun Hutang
//$namaakunhutang = makeOption($dbname,'keu_5akun','noakun,namaakun',
//    "noakun='".$resH[0]['noakunhutang']."'");

#=============================== Detail =======================================
# Data
$col2 = 'nosj,nilai';
//$cols = array('nomor','noakun','namaakun','matauang','debet','kredit');
$cols2 = explode(',',$col2);
$where = "noinvoice='".$param['noinvoice']."'";
$query = selectQuery($dbname,'keu_tagihandt',$col2,$where);
$res = fetchData($query);

# Data Empty
if(empty($res)) {
    echo 'Data Kosong';
    exit;
}

# Options
//$whereAkun = "noakun in (";
//$whereAkun .= "'".$resH[0]['noakun']."'";
//foreach($res as $key=>$row) {
//    $whereAkun .= ",'".$row['noakun']."'";
//}
//$whereAkun .= ")";
//$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereAkun);

# Data Show
$data = array();

#================================ Prep Data ===================================
switch($proses) {	
	case 'pdf':
		$length1=array('15','10','10','15','10','15','10','15');
		$align1=array('L','L','L','L','L','R','L','L');
		$length2=array('20','30');
		$align2=array('L','R');
		
		$pdf=new zPdfMaster('L','pt','A4');
        $pdf->_noThead=true;
        $pdf->_title='Pengakuan Hutang';
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        
		### Header Tagihan
		$pdf->Cell($width,$height,"Header",0,1,'L',1);
		
		# Header
		$pdf->SetFont('Arial','B',9);
		$i=0;
		foreach($cols1 as $column) {
			$pdf->Cell($length1[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
			$i++;
		}
		$pdf->Ln();
		
		# Content
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		foreach($resH as $key=>$row) {
			$i=0;
			foreach($row as $key=>$cont) {
				
				$pdf->Cell($length1[$i]/100*$width,$height,$cont,1,0,$align1[$i],1);
				$i++;
				
			}
			$pdf->Ln();
		}
		$pdf->Ln();
		
		### Detail Tagihan
		# Header
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell($width,$height,"Detail",0,1,'L',1);
		$i=0;
		foreach($cols2 as $column) {
			$pdf->Cell($length2[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
			$i++;
		}
		$pdf->Ln();
		
		# Content
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		foreach($res as $key=>$row) {
			$i=0;
			foreach($row as $key=>$cont) {
				$pdf->Cell($length2[$i]/100*$width,$height,$cont,1,0,$align2[$i],1);
				$i++;
			}
			$pdf->Ln();
		}
		# Total
		//$pdf->SetFont('Arial','B',9);
		//$lenTotal = $length[0]+$length[1]+$length[2]+$length[3];
		//$pdf->Cell($lenTotal/100*$width,$height,'Total',1,0,'C',1);
		//$pdf->Cell($length[4]/100*$width,$height,number_format($totalDebet,0),1,0,'R',1);
		//$pdf->Cell($length[5]/100*$width,$height,number_format($totalKredit,0),1,0,'R',1);
		//$pdf->Ln();
        $pdf->Output();
        break;
	default:
	break;
}
?>