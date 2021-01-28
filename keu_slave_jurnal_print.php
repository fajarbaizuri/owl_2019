<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/iPdfMasterJurnalMemo.php');
include_once('lib/formTable.php');








$level = $_GET['level'];
if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}
if($mode=='pdf') {
    $param = $_GET;
    unset($param['mode']);
    unset($param['level']);
} else {
    $param = $_POST;
}

foreach($param as $key=>$row) {
    if(substr($key,0,8)=='nojurnal') {
        $nojurnal = $row;
    }
}

# Level
switch($level) {
    case '0':
        break;
    case '1':
        # Data
        $cols = 'nojurnal,noakun,keterangan,jumlah,nodok';
        $where = "nojurnal='".$nojurnal."'";
        $query = selectQuery($dbname,'keu_jurnaldt',$cols,$where);
        $data = fetchData($query);
	
	# Total
	$total = array(
	    'debet'=>0,
	    'kredit'=>0
	);
	foreach($data as $row) {
	    if($row['jumlah']<0) {
		$total['kredit'] += $row['jumlah']*(-1);
	    } else {
		$total['debet'] += $row['jumlah'];
	    }
	}
        break;
    default:
}

# Mode
switch($mode) {
    case 'pdf':
        /** Report Prep **/
        $pdf = new iPdfMasterJurnalMemo('P','pt','ind');
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
        
		$aHeader="select * from ".$dbname.".keu_jurnalht where nojurnal='".$nojurnal."'";
		//echo $aHeader;
		$bHeader=mysql_query($aHeader);
		$cHeader=mysql_fetch_assoc($bHeader);
			$kdorg=substr($cHeader['nojurnal'],9,4);//echo $kdorg;
			
		$aOrgInduk="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdorg."'";
		$bOrgInduk=mysql_query($aOrgInduk);
		$cOrgInduk=mysql_fetch_assoc($bOrgInduk);
			$indukorg=$cOrgInduk['induk'];
			
		
		$aKetOrg="select * from ".$dbname.".organisasi where kodeorganisasi='".$indukorg."'";
		//echo $aKetOrg;
		$bKetOrg=mysql_query($aKetOrg);
		$cKetOrg=mysql_fetch_assoc($bKetOrg);
			
			
			
		
		$pdf->Cell($width-100,$height,$cKetOrg['namaorganisasi'],0,0,'L');
		$pdf->SetX(575);
		$pdf->SetFont('Arial','',9);
      	$pdf->Cell(1,$height,'Tanggal : '.tanggalnormal($cHeader['tanggal']),0,1,'R');	
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell($width-100,$height,$cKetOrg['alamat'].', '.$cKetOrg['wilayahkota'],0,1,'L');	
        $pdf->Cell($width-100,$height,"Tel: ".$cKetOrg['telepon'].' Fax: '.$cKetOrg['fax'],0,1,'L');	
		
		
        $pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
	
		$pdf->Line($pdf->lMargin,$pdf->tMargin+($height*4),
		$pdf->lMargin+$width,$pdf->tMargin+($height*4));
		$height = 12;
        $pdf->Ln();$pdf->Ln();$pdf->Ln();
        $pdf->SetFont('Arial','B',12);
		if($kdorg=='SPSL')
		{
			$pdf->Cell($width,$height,'Jurnal Memorial MT Sabang Putra',0,0,'C');
		}
		if($kdorg=='BFSL')
		{
			$pdf->Cell($width,$height,'Jurnal Memorial MT Bintang Fajar',0,0,'C');
		}
        $pdf->Cell($width,$height,'Jurnal Memorial',0,0,'C');
		$pdf->Ln(25);
		
        
		$pdf->SetFillColor(220,220,220);
		$pdf->SetFont('Arial','B',7);	
		$pdf->Cell(82,$height,'No. Jurnal',1,0,'C',1);
		$pdf->Cell(40,$height,'No. Akun',1,0,'C',1);
		$pdf->Cell(275,$height,'Keterangan',1,0,'C',1);
		$pdf->Cell(50,$height,'Debet',1,0,'C',1);
		$pdf->Cell(50,$height,'Kredit',1,0,'C',1);
		$pdf->Cell(60,$height,'No. Document',1,1,'C',1);
	
	$pdf->SetFont('Arial','',7);
	// 'nojurnal,noakun,keterangan,jumlah,nodok';
	$str="select * from ".$dbname.".keu_jurnaldt where nojurnal='".$nojurnal."'";
	//echo $str;
	$res=mysql_query($str);
	while($bar=mysql_fetch_assoc($res))
	{
		
		$aXnama="select namaakun from ".$dbname.".keu_5akun where noakun='".$bar['noakun']."'";
		$bXnama=mysql_query($aXnama);
		$cXnama=mysql_fetch_assoc($bXnama);
			$namaakun=$cXnama['namaakun'];
		
		if($bar['jumlah']>0)
		{	
			$pdf->Cell(82,$height,$bar['nojurnal'],1,0,'L');	
			$pdf->Cell(40,$height,$bar['noakun'],1,0,'L');	
			$pdf->Cell(275,$height,$namaakun,1,0,'L');	
			//$pdf->Cell(275,$height,$bar['keterangan'],1,0,'L');	
			$pdf->Cell(50,$height,number_format($bar['jumlah']),1,0,'R');	
			$pdf->Cell(50,$height,'',1,0,'L');
			$pdf->Cell(60,$height,$bar['nodok'],1,1,'L');	
		}
		else
		{
			$pdf->Cell(82,$height,$bar['nojurnal'],1,0,'L');	
			$pdf->Cell(40,$height,$bar['noakun'],1,0,'L');	
			$pdf->Cell(275,$height,$namaakun,1,0,'L');	
			//$pdf->Cell(275,$height,$bar['keterangan'],1,0,'L');	
			$pdf->Cell(50,$height,'',1,0,'L');
			$pdf->Cell(50,$height,number_format(abs($bar['jumlah'])),1,0,'R');	
			$pdf->Cell(60,$height,$bar['nodok'],1,1,'L');
		}
		
	}
	
	# Total
	//$lenTotal = $length[0]+$length[1]+$length[2];
	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(397,$height,'TOTAL',1,0,'C');
	$pdf->Cell(50,$height,number_format($total['debet']),1,0,'R');
	$pdf->Cell(50,$height,number_format($total['kredit']),1,0,'R');
	$pdf->Cell(60,$height,'',1,0,'R');
	$pdf->Ln();
	$pdf->SetFont('Arial','',7);
	
	$pdf->Output();
        break;
    default:
}
?>