<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_GET;


/** Report Prep **/
$cols = array();

# Detail
$col1 = "kodeblok,kodekegiatan,hk,hasilkerjajumlah,hasilkerjalalu,satuan,jumlahrp";
$cols = explode(',',$col1);
$cols[0] = 'subunit';
$where = "notransaksi='".$param['notransaksi']."' and left(kodeblok,4)='".$param['kodeorg']."'";
$query = selectQuery($dbname,'log_spkdt',$col1,$where);
$data = fetchData($query);
$align = explode(",","L,L,L,R,R,L,R");
$length = explode(",","15,20,10,10,20,10,15");
if(empty($data)) {
    echo "Data Kosong";
    exit;
}

# Options
$whereOrg = "kodeorganisasi in (";
$whereKeg = "kodekegiatan in (";
foreach($data as $key=>$row) {
    if($key==0) {
        $whereOrg .= "'".$row['kodeblok']."'";
        $whereKeg .= "'".$row['kodekegiatan']."'";
    } else {
        $whereOrg .= ",'".$row['kodeblok']."'";
        $whereKeg .= ",'".$row['kodekegiatan']."'";
    }
}
$whereOrg .= ",'".$param['kodeorg']."')";
$whereKeg .= ")";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    $whereOrg,'0',true);
$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
    $whereKeg,'0',true);
$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',
    "supplierid='".$param['koderekanan']."'");

# Data Show
$dataShow = $data;
$totalJumlah = 0;
foreach($dataShow as $key=>$row) {
    $dataShow[$key]['kodeblok'] = $optOrg[$row['kodeblok']];
    $dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
	$dataShow[$key]['jumlahrp'] = number_format($row['jumlahrp'],0);
	$totalJumlah += $row['jumlahrp'];
}
$totalJumlah = number_format($totalJumlah,0);

$title = $_SESSION['lang']['spk'];
$titleDetail = array('Detail');

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('L','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".
            $param['notransaksi'],0,1,'L',1);
        $pdf->Cell($width,$height,$_SESSION['lang']['kodeorg']." : ".
            $optOrg[$param['kodeorg']],0,1,'L',1);
		$pdf->Cell($width,$height,$_SESSION['lang']['koderekanan']." : ".
            $optSupp[$param['koderekanan']],0,1,'L',1);
        $pdf->Ln();
        
        # Header
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$titleDetail[0],0,1,'L',1);
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
        foreach($dataShow as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }
		
		# Total
		$lenTotal = $length[0]+$length[1]+$length[2]+$length[3]+$length[4]+$length[5];
		$pdf->Cell($lenTotal/100*$width,$height,'Total',1,0,'C',1);
		$pdf->Cell($length[6]/100*$width,$height,$totalJumlah,1,0,'R',1);
        
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>