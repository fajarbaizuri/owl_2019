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

#====================== Rencana =========================
$col1 = "kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp";
$cols = explode(',',$col1);
$cols[0] = 'subunit';
$where = "notransaksi='".$param['notransaksi']."' and left(kodeblok,4)='".$param['kodeorg']."'";
$query = selectQuery($dbname,'log_spkdt',$col1,$where);
$data = fetchData($query);
$align = explode(",","L,L,R,R,L,R");
$length = explode(",","30,20,10,10,10,20");
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
foreach($dataShow as $key=>$row) {
    $dataShow[$key]['kodeblok'] = $optOrg[$row['kodeblok']];
    $dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
    $dataShow[$key]['jumlahrp'] = number_format($row['jumlahrp'],0);
}

#====================== Realisasi =========================
$col2 = "tanggal,kodeblok,kodekegiatan,hkrealisasi,hasilkerjarealisasi,jumlahrealisasi";
$cols2 = explode(',',$col2);
$cols2[1] = 'subunit';
$where2 = "notransaksi='".$param['notransaksi']."' and kodekegiatan in (";
foreach($data as $key=>$row) {
    if($key==0) {
        $where2 .= "'".$row['kodekegiatan']."'";
    } else {
        $where2 .= ",'".$row['kodekegiatan']."'";
    }
}
$where2 .= ")";
$query2 = selectQuery($dbname,'log_baspk',$col2,$where2);
$data2 = fetchData($query2);
$align2 = explode(",","L,L,L,R,R,R");
$length2 = explode(",","10,20,20,10,10,30");

if(empty($data2)) {
    echo "Data Realisasi belum ada";
    exit;
}

# Options
$whereOrg2 = "kodeorganisasi in (";
foreach($data2 as $key=>$row) {
    if($key==0) {
        $whereOrg2 .= "'".$row['kodeblok']."'";
    } else {
        $whereOrg2 .= ",'".$row['kodeblok']."'";
    }
}
$whereOrg2 .= ",'".$param['kodeorg']."')";
$optOrg2 = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    $whereOrg2,'0',true);

# Data Show
$dataShow2 = $data2;
foreach($dataShow2 as $key=>$row) {
    $dataShow2[$key]['kodeblok'] = $optOrg2[$row['kodeblok']];
    $dataShow2[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
    $dataShow2[$key]['jumlahrealisasi'] = number_format($row['jumlahrealisasi'],0);
}

$title = $_SESSION['lang']['realisasispk'];
$titleDetail = array('');

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
        
        #====================== Rencana =========================
        # Header
        $pdf->Cell($width,$height,'Rencana',0,0,'L',1);
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
		$total=0;
        foreach($dataShow as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
				if ($i==5){
					$total+=str_replace(",","",$cont);
					
				}
                $i++;
				
            }
			
            $pdf->Ln();
        }
        //$pdf->Ln();
		 $pdf->SetFont('Arial','B',9);
        $pdf->Cell(80/100*$width,$height,"Subtotal",1,0,'R',1);
		$pdf->Cell(20/100*$width,$height,number_format($total),1,0,'R',1);
		$pdf->Ln();
		$pdf->Ln();
        #====================== Realisasi =========================
        # Header
        $pdf->Cell($width,$height,'Realisasi',0,0,'L');
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$titleDetail[0],0,1,'L',1);
        $pdf->SetFillColor(220,220,220);
        $i=0;
        foreach($cols2 as $column) {
            $pdf->Cell($length2[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
			
            $i++;
        }
        $pdf->Ln();
        
        # Content
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',9);
		$total=0;
        foreach($dataShow2 as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length2[$i]/100*$width,$height,$cont,1,0,$align2[$i],1);
				if ($i==5){
					$total+=str_replace(",","",$cont);
					
				}
                $i++;
            }
            $pdf->Ln();
        }
       // $pdf->Ln();
        //$pdf->Ln();
		 $pdf->SetFont('Arial','B',9);
        $pdf->Cell(80/100*$width,$height,"Subtotal",1,0,'R',1);
		$pdf->Cell(20/100*$width,$height,number_format($total),1,0,'R',1);
		$pdf->Ln();
		$pdf->Ln();
		
		
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>