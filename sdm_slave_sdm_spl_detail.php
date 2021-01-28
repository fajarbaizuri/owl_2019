<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');

#echo "<pre>";
#print_r($_SESSION);
#exit;
# Get Data
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
#print_r($column);

#====================== Prepare Data



#====================== Prepare Header PDF
class masterpdf extends FPDF {
    function Header() {
        global $table;
        global $header;
		global $column;
		global $dbname;
		global $tgl;
        global $nmKntr;
		global $dibuat;
        # Panjang, Lebar
        $width = $this->w - $this->lMargin - $this->rMargin;
		$height = 12;
		$a=$this->Image('images/logo.jpg',20,10,120,65,'jpg','');
		$this->Cell(120,$height,$a,' ',0,'L');
        $this->SetFont('Arial','B',10);
		$this->Cell(40/100*$width,$height,$nmKntr,'',0,'L');
		$this->Cell(40/100*$width,$height,'KEPADA YTH :','',1,'L');
		$this->Cell(120,$height,' ','',0,'L');
		//$this->Cell(22/100*$width,$height,' ','',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(12/100*$width,$height,'UNIT KERJA','',0,'L');
		$this->Cell(2/100*$width,$height,':','',0,'L');
		$this->Cell(1/100*$width,$height,substr($column,15,4),'',0,'L');		
		$this->Cell(25/100*$width,$height,' ','',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(12/100*$width,$height,'PURCHASING DEPARTEMENT','',0,'L');
		$this->Cell(2/100*$width,$height,'','',0,'L');
		$this->Cell(1/100*$width,$height,'','',1,'L');

		//$this->Cell(40/100*$width,$height,strtoupper($_SESSION['org']['namaorganisasi']),'',0,'L');
		$this->Cell(120,$height,' ','',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(12/100*$width,$height,'PP NO','',0,'L');
		$this->Cell(2/100*$width,$height,':','',0,'L');
		$this->Cell(1/100*$width,$height,$column,'',0,'L');		
		$this->Cell(25/100*$width,$height,' ','',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(14/100*$width,$height,'TANGGAL PP','',0,'L');
		$this->Cell(2/100*$width,$height,':','',0,'L');
		$this->Cell(1/100*$width,$height,$tgl,'',1,'L');

		
    /*    $this->SetFont('Arial','B',8);
		$this->Cell(420,$height,' ','',0,'R');
		$this->Cell(38,$height,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(5,$height,':','',0,'L');
		$this->Cell(40,$height,date('d-m-Y H:i'),'',1,'L');
		$this->Cell(420,$height,' ','',0,'R');
		$this->Cell(38,$height,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(8,$height,':','',0,'L');
		$this->Cell(15,$height,$this->PageNo(),'',1,'L');
#        $this->Ln();
		$this->Cell(420,$height,' ','',0,'R');
		$this->Cell(38,$height,'User','',0,'L');
		$this->Cell(8,$height,':','',0,'L');
		$this->Cell(20,$height,$_SESSION['standard']['username'],'',1,'L');*/
        $this->Ln();

    }
}

#====================== Prepare PDF Setting
$pdf = new masterpdf('P','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 12;

$pdf->SetFont('Arial','B',8);
$pdf->AddPage();
	$pdf->Cell(20,1.5*$height,'No.','TBLR',0,'C');
	$pdf->Cell(60,1.5*$height,$_SESSION['lang']['kodebarang'],'TBLR',0,'L');
	$pdf->Cell(165,1.5*$height,$_SESSION['lang']['namabarang'],'TBLR',0,'C');
	//$pdf->Cell(80,1.5*$height,$_SESSION['lang']['spesifikasi'],'TBLR',0,'C');
	$pdf->Cell(65,1.5*$height,$_SESSION['lang']['jmlhDiminta'],'TBLR',0,'L');
	$pdf->Cell(45,1.5*$height,$_SESSION['lang']['satuan'],'TBLR',0,'C');
	//$this->Cell(80,1.5*$height,$_SESSION['lang']['keterangan'],'TBLR',0,'C');
	$pdf->Cell(190,1.5*$height,$_SESSION['lang']['keterangan'],'TBLR',0,'C');
	$pdf->Ln();
	$no=0;
	
	foreach($result as $data) {
            $pdf->SetFont('Arial','',7);
		$no++;
		//$tr=substr($data['namabarang'],0,20);
		$pdf->Cell(20,$height,$no,'TBLR',0,'L');
		$pdf->Cell(60,$height,$data['kodebarang'],'TBLR',0,'L');
		$pdf->Cell(165,$height,$data['namabarang'],'TBLR',0,'L');
		//$pdf->Cell(80,$height,$data['spesifikasi'],'TBLR',0,'L');
		$pdf->Cell(65,$height,number_format($data['jumlah'],2),'TBLR',0,'C');
		$pdf->Cell(45,$height,$data['satuan'],'TBLR',0,'C');
		//$pdf->Cell(80,$height,$data['matauang'],'',0,'L');
                $pdf->SetFont('Arial','',6.5);
                $pdf->Cell(190, $height, $data['keterangan'],'TBLR',1,'L');
		
	}
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(120,$height,$_SESSION['lang']['dbuat_oleh'].':'.$dibuat,'',0,L);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Cell(120,$height,$_SESSION['lang']['approval_status'].':','',0,L);
	$pdf->Ln();
	$ko=0;
	
		$pdf->Cell(20,1.5*$height,'No.','TBLR',0,'C');
		$pdf->Cell(120,1.5*$height,$_SESSION['lang']['nama'],'TBLR',0,'C');
		$pdf->Cell(80,1.5*$height,$_SESSION['lang']['kodejabatan'],'TBLR',0,'C');
		$pdf->Cell(70,1.5*$height,$_SESSION['lang']['lokasitugas'],'TBLR',0,'C');
		$pdf->Cell(100,1.5*$height,$_SESSION['lang']['keputusan'],'TBLR',0,'C');
		$pdf->Cell(130,1.5*$height,$_SESSION['lang']['note'],'TBLR',0,'C');
		$pdf->Ln();	
		$sCek="select nopp from ".$dbname.".log_prapodt where nopp='".$column."'";
		//echo $sCek;exit();
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			$qp="select * from ".$dbname.".`log_prapoht` where `nopp`='".$column."'"; //echo $qp;
			$qyr=fetchData($qp);
			foreach($qyr as $hsl)
			{
			/*	$i=1;
				print"<pre>";
				print_r($hsl);
				print"</pre>";
				print $hsl['persetujuan'.$i];
				exit();*/
				
				for($i=1;$i<6;$i++)
				{
				//print $i;
					if($hsl['persetujuan'.$i]!='')
					{
						$sql="select * from ".$dbname.".`datakaryawan` where `karyawanid`='".$hsl['persetujuan'.$i]."'"; //echo $sql;//exit();
						$keterangan=$hsl['komentar'.$i];
						$tanggal=tanggalnormal($hsl['tglp'.$i]);
					}
					else
					{
						break;
					}
						if($hsl['hasilpersetujuan'.$i]==1)
						{
							$b['status']=$_SESSION['lang']['disetujui'];
						}
						elseif($hsl['hasilpersetujuan'.$i]==3)
						{
							$b['status']=$_SESSION['lang']['ditolak'];
						}
						elseif($hsl['hasilpersetujuan'.$i]==''||$hsl['hasilpersetujuan'.$i]==0)
						{
							$b['status']=$_SESSION['lang']['wait_approve'];
						}
					
					$query=mysql_query($sql) or die(mysql_error());
					$res3=mysql_fetch_object($query);
					
					$sql2="select * from ".$dbname.".`sdm_5jabatan` where kodejabatan='".$res3->kodejabatan."'";
					$query2=mysql_query($sql2) or die(mysql_error());
					$res2=mysql_fetch_object($query2);
					
					$pdf->SetFont('Arial','',7);
					$pdf->Cell(20,1.5*$height,$i,'TBLR',0,'C');
					$pdf->Cell(120,1.5*$height,$res3->namakaryawan."(".$tanggal.") ",'TBLR',0,'L');
					$pdf->Cell(80,1.5*$height,$res2->namajabatan,'TBLR',0,'L');
					$pdf->Cell(70,1.5*$height,$res3->lokasitugas,'TBLR',0,'L');
					$pdf->Cell(100,1.5*$height,$b['status'],'TBLR',0,'L');
					$pdf->Cell(130,1.5*$height,$keterangan,'TBLR',0,'L');
					$pdf->Ln();	
				}
			}
	}
	else
	{
			$pdf->SetFont('Arial','',7);
			$pdf->Cell(520,1.5*$height,"Not Found",'TBLR',0,'C');
	}
	$pdf->Cell(15,$height,'Page '.$pdf->PageNo(),'',1,'L');

# Print Out
$pdf->Output();

?>