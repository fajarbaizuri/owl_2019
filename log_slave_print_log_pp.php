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


#====================== Prepare Data
//$query=
//$query = selectQuery($dbname,$table);
#print_r ($_GET);
#exit;
$ql="select a.kodeorg,a.nopp,a.tanggal,a.dibuat from ".$dbname.".`log_prapoht` a where a.nopp='".$column."'"; //echo $ql;
$pq=mysql_query($ql) or die(mysql_error());
$hsl=mysql_fetch_assoc($pq);
$kdr=$hsl['kodeorg'];

$sNmKry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$hsl['dibuat']."'";
$qNmKry=mysql_query($sNmKry) or die(mysql_error());
$rNmKry=mysql_fetch_assoc($qNmKry);
$dibuat=$rNmKry['namakaryawan'];


$sNmkntr="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdr."'";
$qNmkntr=mysql_query($sNmkntr) or die(mysql_error());
$rNmkntr=mysql_fetch_assoc($qNmkntr);
$nmKntr=$rNmkntr['namaorganisasi'];
$tgl=tanggalnormal($hsl['tanggal']);
		
$query="select a.*,b.*,c.namabarang,c.satuan,d.spesifikasi from ".$dbname.".".$table." a inner join ".$dbname.".`log_prapodt` b on a.nopp=b.nopp inner join ".$dbname.".`log_5masterbarang` c on b.kodebarang=c.kodebarang  left join ".$dbname.".`log_5photobarang` d on c.kodebarang=d.kodebarang where a.nopp='".$column."'"; //echo $query; exit();
//$query = mysql_query($sql) or die(mysql_error());
$result = fetchData($query);
/*$header = array();
foreach($result[0] as $key=>$row) {
    $header[] = $key;
}*/

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
		
		global $kdr;
		//echo $kdr;
        # Panjang, Lebar
		
		
		
		
        $width = $this->w - $this->lMargin - $this->rMargin;
		$height = 12;
		//echo $kdr;exit();
		
		if($kdr=='FBB')
		{
			$a=$this->Image('images/logo2.jpg',30,10,85,65,'jpg','');
		}
		else if ($kdr=='USJ')
		{
			$a=$this->Image('images/logo3.jpg',30,10,85,65,'jpg','');
		}
		else if ($kdr=='FPS')
		{
			$a=$this->Image('images/logo4.jpg',30,10,85,65,'jpg','');
		}
		else
		{
			$a=$this->Image('images/logo.jpg',30,10,85,65,'jpg','');
		}
		
		
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
		$this->Cell(12/100*$width,$height,'PROCUREMENT DEPARTEMENT','',0,'L');
		$this->Cell(2/100*$width,$height,'','',0,'L');
		$this->Cell(1/100*$width,$height,'','',1,'L');

		//$this->Cell(40/100*$width,$height,strtoupper($_SESSION['org']['namaorganisasi']),'',0,'L');
		$this->Cell(120,$height,' ','',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(12/100*$width,$height,'PB NO','',0,'L');
		$this->Cell(2/100*$width,$height,':','',0,'L');
		$this->Cell(1/100*$width,$height,$column,'',0,'L');		
		$this->Cell(25/100*$width,$height,' ','',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(14/100*$width,$height,'TANGGAL PB','',0,'L');
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
$pdf = new masterpdf('L','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 12;

$pdf->SetFont('Arial','B',8);
$pdf->AddPage();
	$pdf->Cell(20,1.5*$height,'No.','TBLR',0,'C');
	$pdf->Cell(60,1.5*$height,$_SESSION['lang']['kodebarang'],'TBLR',0,'L');
	$pdf->Cell(375,1.5*$height,$_SESSION['lang']['namabarang'],'TBLR',0,'C');
	//$pdf->Cell(80,1.5*$height,$_SESSION['lang']['spesifikasi'],'TBLR',0,'C');
	$pdf->Cell(65,1.5*$height,$_SESSION['lang']['jmlhDiminta'],'TBLR',0,'L');
	$pdf->Cell(45,1.5*$height,$_SESSION['lang']['satuan'],'TBLR',0,'C');
	//$this->Cell(80,1.5*$height,$_SESSION['lang']['keterangan'],'TBLR',0,'C');
	$pdf->Cell(210,1.5*$height,$_SESSION['lang']['keterangan'],'TBLR',0,'C');
	$pdf->Ln();
	$no=0;
	
	foreach($result as $data) {
            $pdf->SetFont('Arial','',8);
		$no++;
		//$tr=substr($data['namabarang'],0,20);
		$pdf->Cell(20,$height,$no,'TBLR',0,'L');
		$pdf->Cell(60,$height,$data['kodebarang'],'TBLR',0,'L');
		$pdf->Cell(375,$height,$data['namabarang'],'TBLR',0,'L');
		//$pdf->Cell(80,$height,$data['spesifikasi'],'TBLR',0,'L');
		$pdf->Cell(65,$height,number_format($data['jumlah'],2),'TBLR',0,'C');
		$pdf->Cell(45,$height,$data['satuan'],'TBLR',0,'C');
		//$pdf->Cell(80,$height,$data['matauang'],'',0,'L');
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(210, $height, $data['keterangan'],'TBLR',1,'L');
		
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
		$pdf->Cell(180,1.5*$height,$_SESSION['lang']['kodejabatan'],'TBLR',0,'C');
		$pdf->Cell(60,1.5*$height,$_SESSION['lang']['lokasitugas'],'TBLR',0,'C');
		$pdf->Cell(75,1.5*$height,$_SESSION['lang']['keputusan'],'TBLR',0,'C');
		$pdf->Cell(150,1.5*$height,$_SESSION['lang']['note'],'TBLR',0,'C');
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
					
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(20,1.5*$height,$i,'TBLR',0,'C');
					$pdf->Cell(120,1.5*$height,$res3->namakaryawan."(".$tanggal.") ",'TBLR',0,'L');
					$pdf->Cell(180,1.5*$height,$res2->namajabatan,'TBLR',0,'L');
					$pdf->Cell(60,1.5*$height,$res3->lokasitugas,'TBLR',0,'L');
					$pdf->Cell(75,1.5*$height,$b['status'],'TBLR',0,'L');
					$pdf->Cell(150,1.5*$height,$keterangan,'TBLR',0,'L');
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