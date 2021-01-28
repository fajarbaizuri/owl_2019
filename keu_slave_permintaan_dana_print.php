<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php'); 

$proses = $_GET['proses'];
$param = $_POST;


/** Report Prep **/
$str="select periode, tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku = '0'";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $periodeaktif=$res['periode'];
    $periodemulai=$res['tanggalmulai'];
    $periodesampai=$res['tanggalsampai'];
}

$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal >= '".$periodemulai."' and tanggal <= '".$periodesampai."'";
//$where = "";
//$cols = 'notransaksi,tanggal,noakun,tipetransaksi,jumlah,posting';
$cols = "kodeorg,notransaksi,tanggal,kelompok,namakaryawan,totaldana";
$colArr = Array('Organisasi','No. Transaksi','Tanggal','Tipe','Pemohon','Total Permintaan');
//$colArr = explode(',',$cols);
$query = selectQuery($dbname,'log_dana_vw',$cols,$where,'tanggal desc, notransaksi desc');
$data = fetchData($query);

//$title = "Kas Bank";
$title = "Permintaan Dana Operasional";
$align = explode(",","C,C,C,C,C,R");
$length = explode(",","10,15,10,30,20,15");

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('L','pt','A4');
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