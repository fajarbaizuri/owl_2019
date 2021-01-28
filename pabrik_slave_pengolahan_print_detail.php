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

# Header
$qHead = selectQuery($dbname,'pabrik_pengolahan',"*","nopengolahan='".
	$param['nopengolahan']."'");
$resHead = fetchData($qHead);

# Option
$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
	"karyawanid in (".$resHead[0]['mandor'].",".$resHead[0]['asisten'].")");

# Detail
$col1 = "kodeorg as station,tahuntanam,jammulai,jamselesai,jamstagnasi,".
    "keterangan";
$cols = array(
	'station','mesin','jammulai','jamselesai','Jam Operasional','jamstagnasi',
	'Jam Efektif','keterangan'
);
$query = selectQuery($dbname,'pabrik_pengolahanmesin',$col1,
    "nopengolahan='".$param['nopengolahan']."'");
$data = fetchData($query);
$align = explode(",","L,L,C,C,C,C,C,L");
$length = explode(",","20,15,10,10,10,10,10,15");
if(empty($data)) {
    echo "Data Kosong";
    exit;
}

# Material
$queryMat = selectQuery($dbname,'pabrik_pengolahan_barang','kodeorg,tahuntanam,kodebarang,jumlah',
    "nopengolahan='".$param['nopengolahan']."'");
$resMat = fetchData($queryMat);

# Options
$whereOrg = "kodeorganisasi in (";
foreach($data as $key=>$row) {
    if($key==0) {
        $whereOrg .= "'".$row['station']."','".$row['tahuntanam']."'";
    } else {
        $whereOrg .= ",'".$row['station']."','".$row['tahuntanam']."'";
    }
}
$whereOrg .= ")";
if(!empty($resMat)) {
    $whereBarang = "kodebarang in (";
    foreach($resMat as $key=>$row) {
	if($key==0) {
	    $whereBarang .= "'".$row['kodebarang']."'";
	} else {
	    $whereBarang .= ",'".$row['kodebarang']."'";
	}
    }
    $whereBarang .= ")";
} else {
    $whereBarang = null;
}

$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    $whereOrg,'0',true);
$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
    $whereBarang,'0',true);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
    $dataShow[$key]['station'] = $optOrg[$row['station']];
    $dataShow[$key]['tahuntanam'] = $optOrg[$row['tahuntanam']];
}

$dataMat = array();
foreach($resMat as $key=>$row) {
    $dataMat[$row['kodeorg']][$row['tahuntanam']][] = array(
	'nama'=>$optBarang[$row['kodebarang']],
	'jumlah'=>$row['jumlah']
    );
}
$title = $_SESSION['lang']['operasipabrik'];
$titleDetail = 'Detail';

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
        $pdf->Cell(100,$height,$_SESSION['lang']['nopengolahan'],0,0,'L',1);
		$pdf->Cell(1,$height,":  ".$param['nopengolahan'],0,1,'L',1);
		$pdf->Cell(100,$height,$_SESSION['lang']['tanggal'],0,0,'L',1);
		$pdf->Cell(1,$height,":  ".tanggalnormal($resHead[0]['tanggal']),0,1,'L',1);
		$pdf->Cell(100,$height,$_SESSION['lang']['shift'],0,0,'L',1);
		$pdf->Cell(1,$height,":  ".$resHead[0]['shift'],0,1,'L',1);
		$pdf->Cell(100,$height,$_SESSION['lang']['mandor'],0,0,'L',1);
		$pdf->Cell(1,$height,": ".$optKary[$resHead[0]['mandor']],0,1,'L',1);
		$pdf->Cell(100,$height,$_SESSION['lang']['asisten'],0,0,'L',1);
		$pdf->Cell(1,$height,": ".$optKary[$resHead[0]['asisten']],0,1,'L',1);
		$pdf->Ln();
        
        # Header
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$titleDetail,0,1,'L',1);
        $pdf->SetFillColor(220,220,220);
        $i=0;
        foreach($cols as $column) {
            if(isset($_SESSION['lang'][$column])) {
				$pdf->Cell($length[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
			} else {
				$pdf->Cell($length[$i]/100*$width,$height,$column,1,0,'C',1);
			}
            $i++;
        }
        $pdf->Ln();
        
        # Content
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',9);
        foreach($dataShow as $key=>$row) {
			$jam1 = explode(':',$row['jammulai']);
			$jam2 = explode(':',$row['jamselesai']);
			
			// Detik Operasional
			$sOp = $jam2[2]-$jam1[2];
			if($sOp<0) {
				$sOp+=60;
				$jam2[1]-=1;
			}
			
			// Menit Operasional
			$mOp = $jam2[1]-$jam1[1];
			if($mOp<0) {
				$mOp+=60;
				$jam2[0]-=1;
			}
			
			// Jam Operasional
			$jOp = $jam2[0]-$jam1[0];
			if($jOp<0) {
				$jOp=0;
			}
			
			// Add Zero
			$jEff = $jOp-$row['jamstagnasi'];
			if($jEff>0) {
				$jEff = addZero($jEff,2);
			}
			$jOp = addZero($jOp,2);
			$mOp = addZero($mOp,2);
			$sOp = addZero($sOp,2);
			$op = $jOp.":".$mOp.":".$sOp;
			if($jEff>=0) {
				$ef = $jEff.":".$mOp.":".$sOp;
			} else {
				$ef = "00:00:00";
			}
			
            $pdf->Cell($length[0]/100*$width,$height,$row['station'],1,0,$align[0],1);
			$pdf->Cell($length[1]/100*$width,$height,$row['tahuntanam'],1,0,$align[1],1);
			$pdf->Cell($length[2]/100*$width,$height,$row['jammulai'],1,0,$align[2],1);
			$pdf->Cell($length[3]/100*$width,$height,$row['jamselesai'],1,0,$align[3],1);
			$pdf->Cell($length[4]/100*$width,$height,$op,1,0,$align[4],1);
			$pdf->Cell($length[5]/100*$width,$height,$row['jamstagnasi'],1,0,$align[5],1);
			$pdf->Cell($length[6]/100*$width,$height,$ef,1,0,$align[6],1);
			$pdf->Cell($length[7]/100*$width,$height,$row['keterangan'],1,0,$align[7],1);
            $pdf->Ln();
	    
	    # Show Material
	    if(isset($dataMat[$data[$key]['station']][$data[$key]['tahuntanam']])) {
		$j=1;
		foreach($dataMat[$data[$key]['station']][$data[$key]['tahuntanam']] as $rowMat) {
		    $pdf->Cell(20/100*$width,$height,$j,1,0,'R',1);
		    $pdf->Cell(30/100*$width,$height,$rowMat['nama'],1,0,'L',1);
		    $pdf->Cell(50/100*$width,$height,$rowMat['jumlah'],1,0,'R',1);
		    $pdf->Ln();
		    $j++;
		}
	    }
        }
        $pdf->Ln();
        
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>