<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

$proses = $_GET['proses'];
$param = $_GET;


/** Report Prep **/
$cols = array();

#=============================== Header =======================================
$whereH = "notransaksi='".$param['notransaksi'].
    "' and kodeorg='".$param['kodeorg'].
    "' and noakun='".$param['noakun'].
    "' and tipetransaksi='".$param['tipetransaksi']."'";
$queryH = selectQuery($dbname,'keu_kasbankht','*',$whereH);
$resH = fetchData($queryH);

# Get Nama Pembuat
$userId = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
    "karyawanid='".$resH[0]['userid']."'");

#=============================== Detail =======================================
# Data
$col1 = 'noakun,jumlah,noaruskas,matauang,kode';
$cols = array('nomor','noakun','namaakun','matauang','debet','kredit');
$where = "notransaksi='".$param['notransaksi'].
    "' and kodeorg='".$param['kodeorg'].
    "' and noakun2a='".$param['noakun'].
    "' and tipetransaksi='".$param['tipetransaksi']."'";
$query = selectQuery($dbname,'keu_kasbankdt',$col1,$where);
$res = fetchData($query);

# Data Empty
if(empty($res)) {
    echo 'Data Kosong';
    exit;
}

# Options
$whereAkun = "noakun in (";
$whereAkun .= "'".$resH[0]['noakun']."'";
foreach($res as $key=>$row) {
    $whereAkun .= ",'".$row['noakun']."'";
}
$whereAkun .= ")";
$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereAkun);

# Data Show
$data = array();

#================================ Prep Data ===================================
# Total
$totalDebet = 0;$totalKredit = 0;

# Dari Header
$i=1;
$data[$i] = array(
    'nomor'=>$i,
    'noakun'=>$resH[0]['noakun'],
    'namaakun'=>$optAkun[$resH[0]['noakun']],
    'matauang'=>$resH[0]['matauang'],
    'debet'=>0,
    'kredit'=>0
);
if($param['tipetransaksi']=='M') {
    $data[$i]['debet'] = $resH[0]['jumlah'];
    $totalDebet += $resH[0]['jumlah'];
} else {
    $data[$i]['kredit'] = $resH[0]['jumlah'];
    $totalKredit += $resH[0]['jumlah'];
}
$i++;

# Dari Detail
foreach($res as $row) {
    $data[$i] = array(
	'nomor'=>$i,
	'noakun'=>$row['noakun'],
	'namaakun'=>$optAkun[$row['noakun']],
	'matauang'=>$row['matauang'],
	'debet'=>0,
	'kredit'=>0
    );
    if($param['tipetransaksi']=='M' and $row['jumlah']>0) {
	$data[$i]['kredit'] = $row['jumlah'];
	$totalKredit += $row['jumlah'];
    }
    else if($param['tipetransaksi']=='K' and $row['jumlah']<0){
	$data[$i]['kredit'] = $row['jumlah']*-1;
	$totalKredit += $row['jumlah']*-1;        
    }
    else if($param['tipetransaksi']=='M' and $row['jumlah']<0){
	$data[$i]['debet'] = $row['jumlah']*-1;
	$totalDebet += $row['jumlah']*-1;        
    }    
    else {
	$data[$i]['debet'] = $row['jumlah'];
	$totalDebet += $row['jumlah'];
    }
    $i++;
}

$align = explode(",","R,R,L,L,R,R");
$length = explode(",","7,12,35,10,18,18");
$title = $_SESSION['lang']['kasbank'];
$titleDetail = 'Detail';

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".
            $res[0]['kode']."/".$param['notransaksi'],0,1,'L',1);
        $pdf->Cell($width,$height,$_SESSION['lang']['cgttu']." : ".
            $resH[0]['cgttu'],0,1,'L',1);
        $pdf->Ln();
	
	# Header
	$pdf->SetFont('Arial','B',9);
	#$pdf->Cell($width,$height,$titleDetail,0,1,'L',1);
	$pdf->MultiCell($width,$height,'Terbilang : '.terbilang($resH[0]['jumlah'],2).
	    ' rupiah',0);
	$pdf->SetFillColor(220,220,220);
	$i=0;
	foreach($cols as $column) {
	    $pdf->Cell($length[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
	    $i++;
	}
	$pdf->Ln();
	
	# Content
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
	foreach($data as $key=>$row) {    
	    $i=0;
	    foreach($row as $key=>$cont) {
		if($key=='debet' or $key=='kredit') {
		    $pdf->Cell($length[$i]/100*$width,$height,number_format($cont,0),1,0,$align[$i],1);
		} else {
		    $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
		}
		$i++;
	    }
	    $pdf->Ln();
	}
	# Total
	$pdf->SetFont('Arial','B',9);
	$lenTotal = $length[0]+$length[1]+$length[2]+$length[3];
	$pdf->Cell($lenTotal/100*$width,$height,'Total',1,0,'C',1);
	$pdf->Cell($length[4]/100*$width,$height,number_format($totalDebet,0),1,0,'R',1);
	$pdf->Cell($length[5]/100*$width,$height,number_format($totalKredit,0),1,0,'R',1);
	$pdf->Ln();
	
	# Keterangan
	$pdf->MultiCell($width,$height,'Keterangan : '.$resH[0]['keterangan']);
	$pdf->Ln();
	
	# TTD
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(21/100*$width,$height,'Penerima',1,0,'C',1);
	$pdf->Cell(21/100*$width,$height,'Dibuat',1,0,'C',1);
	$pdf->Cell(33/100*$width,$height,'Diperiksa',1,0,'C',1);
	$pdf->Cell(25/100*$width,$height,'Disetujui',1,0,'C',1);
	$pdf->Ln();
	$pdf->SetFillColor(255,255,255);
	for($i=0;$i<3;$i++) {
		$pdf->Cell(21/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Cell(21/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Cell(33/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Cell(25/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Ln();
	}
	$pdf->Cell(21/100*$width,$height,'','BLR',0,'C',1);
	if(isset($userId[$resH[0]['userid']])) {
	    $pdf->Cell(21/100*$width,$height,$userId[$resH[0]['userid']],'BLR',0,'C',1);
	} else {
	    $pdf->Cell(21/100*$width,$height,'','BLR',0,'C',1);
	}
	$pdf->Cell(33/100*$width,$height,'','BLR',0,'C',1);
	$pdf->Cell(25/100*$width,$height,'','BLR',0,'C',1);
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>