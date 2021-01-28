<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$tipe=$_GET['tipe'];
$param = $_POST;


/** Report Prep **/
$cols = 'kodeorg,kodeblok,tanggal,jumlah,jumlahha,jumlahpremi,jumlahpokok';
$colArr = explode(',',$cols);
$query = selectQuery($dbname,'kebun_rencanapanen',$cols,
    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'");
$data = fetchData($query);

$title = $_SESSION['lang']['rencanapanen'];
$align = explode(",","L,L,L,R,R,R,R");
$length = explode(",","20,20,10,13,12,13,12");

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        foreach($data as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>