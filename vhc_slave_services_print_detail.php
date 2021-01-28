<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_GET;
$notransaksi = $_GET['notransaksi'];

	
	
	$query="select notransaksi,tanggal,kelompok,nopol,jenisnm,mekanik,nmmekanik,posting,catatan,waktu,noref from ".$dbname.".vhc_service_vw where notransaksi='".$param['notransaksi']."'";
	$Quer = mysql_query($query);
	$HasilQue=mysql_fetch_assoc($Quer);
	
	
	//Detail Services
	$query2="select * from ".$dbname.".vhc_servicedt_vw where notransaksi='".$param['notransaksi']."'";
	$Quer2 = mysql_query($query2);
	$i=0;
	$j=0;
	$b="";
	$Has=Array();
	while($HasilQue2=mysql_fetch_assoc($Quer2)){
			$Has[$i]['notransaksi']=$HasilQue2['notransaksi'];
			$Has[$i]['kodebarang']=$HasilQue2['kodebarang'];
			$Has[$i]['namabarang']=$HasilQue2['namabarang'];
			$Has[$i]['jumlah']=$HasilQue2['jumlah'];
			$Has[$i]['satuan']=$HasilQue2['satuan'];
			$Has[$i]['keterangan']=$HasilQue2['keterangan'];
			$i++;
	}

	
	
	$align[] = explode(",","C,L,C,R,L,R");
$length[] = explode(",","10,30,20,15,5,20");
	
$titleDetail = array('Penggunaan Bahan dan Material');

/** Output Format **/
switch($proses) {
    case 'pdf':
        
        $pdf=new zPdfMaster('L','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1('Laporan Perawatan Alat Berat/Kendaraan',$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',10);
		$pdf->SetXY(28,130);
		$pdf->Cell($width,$height,"No. Transaksi",0,1,'L',1);
		$pdf->SetXY(108,130);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,130);
        $pdf->Cell($width,$height,$HasilQue['notransaksi'],0,1,'L',1);
		
		$pdf->SetXY(520,130);
		$pdf->Cell($width,$height,"Kelompok",0,1,'L',1);
		$pdf->SetXY(620,130);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,130);
        $pdf->Cell($width,$height,$HasilQue['kelompok'],0,1,'L',1);
		
		
		$pdf->SetXY(28,144);
		$pdf->Cell($width,$height,"Tanggal",0,1,'L',1);
		$pdf->SetXY(108,144);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,144);
        $pdf->Cell($width,$height,tanggalnormal($HasilQue['tanggal']),0,1,'L',1);
		
		$pdf->SetXY(520,144);
		$pdf->Cell($width,$height,"Plat No",0,1,'L',1);
		$pdf->SetXY(620,144);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,144);
        $pdf->Cell($width,$height,$HasilQue['nopol'],0,1,'L',1);
		
		
		$pdf->SetXY(28,158);
		$pdf->Cell($width,$height,"Jenis",0,1,'L',1);
		$pdf->SetXY(108,158);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,158);
        $pdf->Cell($width,$height,$HasilQue['jenisnm'],0,1,'L',1);
		

		$pdf->SetXY(520,158);
		$pdf->Cell($width,$height,"Waktu Pengerjaan",0,1,'L',1);
		$pdf->SetXY(620,158);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,158);
        $pdf->Cell($width,$height,$HasilQue['waktu']." Jam",0,1,'L',1);
	
		$pdf->SetXY(28,172);
		$pdf->Cell($width,$height,"Keterangan",0,1,'L',1);
		$pdf->SetXY(108,172);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,172);
        $pdf->Cell($width,$height,$HasilQue['catatan'],0,1,'L',1);
	
		$pdf->SetXY(520,172);
		$pdf->Cell($width,$height,"Mekanik",0,1,'L',1);
		$pdf->SetXY(620,172);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,172);
        $pdf->Cell($width,$height,$HasilQue['nmmekanik'],0,1,'L',1);
		
		$pdf->SetXY(28,186);
		$pdf->Cell($width,$height,"No. Referensi",0,1,'L',1);
		$pdf->SetXY(108,186);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,186);
        $pdf->Cell($width,$height,$HasilQue['noref'],0,1,'L',1);
		
		$pdf->SetXY(28,206);
		$pdf->Ln(); 
		//----------------------------------------------------------
		$pdf->Cell($width,$height,$titleDetail[0],0,1,'L',1);
	
		
		$pdf->SetXY(28,232);
		$pdf->Cell(70,$height,"Kode",1,1,'C',1);
		$pdf->SetXY(98,232);
		$pdf->Cell(240,$height,"Nama",1,1,'C',1);
		$pdf->SetXY(338,232);
		$pdf->Cell(65,$height,"Jumlah",1,1,'C',1);
		$pdf->SetXY(403,232);
		$pdf->Cell(65,$height,"Satuan",1,1,'C',1);
		$pdf->SetXY(468,232);
		$pdf->Cell(340,$height,"Keterangan",1,1,'C',1);
		
		$H=244;
		//---------------isi
		foreach($Has as $key=>$row) {
			$pdf->Cell(70,$height,$row['kodebarang'],1,1,'C',1);
			$pdf->SetXY(98,$H);
			$pdf->Cell(240,$height,$row['namabarang'],1,1,'L',1);
			$pdf->SetXY(338,$H);
			$pdf->Cell(65,$height,number_format($row['jumlah'],0),1,1,'C',1);
			$pdf->SetXY(403,$H);
			$pdf->Cell(65,$height,$row['satuan'],1,1,'C',1);
			$pdf->SetXY(468,$H);
			$pdf->Cell(340,$height,$row['keterangan'],1,1,'L',1);
			$H=$H+12;
		}
		
		//---------------total
		
		
		
		
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>