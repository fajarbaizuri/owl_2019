<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

//$proses=$_GET['proses'];
if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}

/*$kdsup=$_POST['kdsup'];
//exit("$kdsup");
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	
	$kdsup=$_GET['kdsup'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	
}*/

!isset($_POST['kdUnit'])?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
!isset($_POST['tanggal'])?$periodeId=$_GET['tanggal']:$periodeId=$_POST['tanggal'];


$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');


if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdUnit==''))
	{
		echo"Error: Organisasi masih kosong"; 
		exit;
    }

    else if(($periodeId==''))
	{
		echo"Error: Periode masih kosong"; 
		exit;
    }
	
}

##ambil data SQL untuk global view 
$optPanen = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
	"kelompok='PNN'");
$kegPanen = getFirstKey($optPanen);

$tglArr = explode('-',$periodeId);

## Ambil dari SPK
//$q1 = selectQuery($dbname,'log_baspk','*',"tanggal like '".
//	$periodeId."%' and kodeblok='".$BlokId."' and kodekegiatan='".$kegPanen."'");
//$res1 = fetchData($q1);
//$dataSPK = array();
//foreach($res1 as $row) {
//	$dataSPK[$row['tanggal']][$row['kodeblok']] = $row;
//}

//$str="  SELECT sum(b.hasilkerja) as hasilkerja,b.tanggal,a.kodeorg,a.jumlahjjgrestan,a.catatan,b.notransaksi,a.periode,c.blok,c.jjg	
//		FROM ".$dbname.".kebun_restan_v1 a
//		JOIN ".$dbname.".kebun_prestasi_vw b
//		JOIN ".$dbname.".kebun_spbdt c
//		ON a.kodeorg=b.kodeorg and a.periode=substr(b.tanggal,1,7) and a.kodeorg=c.blok
//		WHERE  a.kodeorg='".$BlokId."' and a.periode='".$periodeId."' and b.notransaksi like '%PNN%' group by b.tanggal ";
$str = "SELECT sum(b.hasilkerja) AS hasilkerja,b.tanggal,a.kodeorg,sum(a.jumlahjjgrestan) as jumlahjjgrestan,a.catatan,b.notransaksi,a.periode,c.blok,sum(c.jjg) as jjg
	FROM ".$dbname.".kebun_restan_v1 a 
	LEFT JOIN ".$dbname.".kebun_prestasi_vw b ON a.kodeorg=b.kodeorg AND a.periode=SUBSTR(b.tanggal,1,7) 
	LEFT JOIN ".$dbname.".kebun_spbdt c ON a.kodeorg=c.blok 
	WHERE left(a.kodeorg,4)='".$kdUnit."' AND a.periode='".$tglArr[2]."-".$tglArr[1]."' and b.tanggal<='".tanggalsystem($periodeId)."' AND b.notransaksi LIKE '%PNN%' GROUP BY b.tanggal,a.kodeorg";

// Restan
$optRestan = makeOption($dbname,'kebun_restan_v1','kodeorg,jumlahjjgrestan',
	"periode='".$tglArr[2]."-".$tglArr[1]."'");

// Panen
$q1 = selectQuery($dbname,'kebun_prestasi_vw','sum(hasilkerja) as jjg,kodeorg,tanggal',"tanggal<='".
	tanggalsystem($periodeId)."' and left(kodeorg,4)='".$kdUnit."' and notransaksi LIKE '%PNN%'")." group by kodeorg,tanggal";
$resPanen = fetchData($q1);

$optPanen = array();
foreach($resPanen as $row) {
	$optPanen[$row['tanggal']][$row['kodeorg']] = $row['jjg'];
}

// SPB
$q2 = selectQuery($dbname,'kebun_spb_vw','sum(jjg) as jjg,blok,tanggal',"tanggal<='".
	tanggalsystem($periodeId)."' and left(blok,4) = '".$kdUnit."'")." group by blok,tanggal";
$resSPB = fetchData($q2);
$optSPB = array();

foreach($resSPB as $row) {
	$optSPB[$row['tanggal']][$row['blok']] = $row['jjg'];
}

// Data
$data=array();
foreach($optPanen as $tgl=>$row) {
	foreach($row as $blok=>$jjg) {
		if(isset($optRestan[$blok])) {
			$data[$blok][$tgl]['start']=$jjg;
		} else {
			$data[$blok][$tgl]['start']=0;
		}
		$data[$blok][$tgl]['panen']=$jjg;
	}
}

foreach($optSPB as $tgl=>$row) {
	foreach($row as $blok=>$jjg) {
		if(isset($optRestan[$blok])) {
			$data[$blok][$tgl]['start']=$jjg;
		} else {
			$data[$blok][$tgl]['start']=0;
		}
		$data[$blok][$tgl]['spb']=$jjg;
	}
}

//for($i=1;$i<32;$i++) {
//	if(isset($optPanen[$tglArr[2].'-'.$tglArr[1].'-'.addZero($i,2)])) {
//		$data[$tglArr[2].'-'.$tglArr[1].'-'.addZero($i,2)]['panen'] = $optPanen[$tglArr[2].'-'.$tglArr[1].'-'.addZero($i,2)];
//	}
//	if(isset($optSPB[$tglArr[2].'-'.$tglArr[1].'-'.addZero($i,2)])) {
//		$data[$tglArr[2].'-'.$tglArr[1].'-'.addZero($i,2)]['spb'] = $optSPB[$tglArr[2].'-'.$tglArr[1].'-'.addZero($i,2)];
//	}
//}

if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
else $stream="<table cellspacing='1' border='0' class='sortable'>";

$stream.="<thead class=rowheader>
	 <tr>
		<td align=center>No</td>
		<td align=center>Kode Blok</td>
		<td align=center>Tanggal</td>
		<td align=center>Restan Awal (JJG)</td>
		<td align=center>Panen (JJG)</td>
		<td align=center>Angkut (JJG)</td>
		<td align=center>Restan (JJG)</td>
	</tr></thead>
	<tbody>";

$no=1;
$total = array(
	'panen'=>0,
	'spb'=>0
);
foreach($data as $blok=>$row) {
	
	$i=0;
	$stream.="<tr class=rowcontent>";
	$stream.="<td>".$no."</td>";
	$stream.="<td>".$blok."</td>";
	if(isset($optRestan[$blok])) {
		$restanEnd = $optRestan[$blok];
	} else {
		$restanEnd = 0;
	}
	foreach($row as $tgl=>$row2) {
		if(!isset($row2['panen']))$row2['panen']=0;
		if(!isset($row2['spb']))$row2['spb']=0;
		$row2['start']=$restanEnd;
		$restanEnd += $row2['panen']-$row2['spb'];
		if($i>0) {
			$stream.="<tr class=rowcontent>";
			$stream.="<td>".$no."</td>";
			$stream.="<td></td>";
		}
		$stream.="<td>".tanggalnormal($tgl)."</td>";
		$stream.="<td align=right>".number_format($row2['start'],0)."</td>";
		$stream.="<td align=right>".number_format($row2['panen'],0)."</td>";
		$stream.="<td align=right>".number_format($row2['spb'],0)."</td>";
		$stream.="<td align=right>".number_format($restanEnd,0)."</td>";
		$stream.="</tr>";
		$no++;
		$total['panen']+=$row2['panen'];
		$total['spb']+=$row2['spb'];
		$i++;
	}
}
$stream.="<tr class=rowcontent style='font-weight:bold'><td align=center colspan=4>TOTAL</td>";
$stream.="<td align=right>".number_format($total['panen'],0)."</td>";
$stream.="<td align=right>".number_format($total['spb'],0)."</td>";
$stream.="<td></td></tr></tbody></table>";
/*}
else
{
	exit("Tidak ada data silahkan input");
}*/

#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
	
	case'getAfd':
        //exit("Error:MASUK");
		$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sAfd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdUnit."'";
		//exit("Error:$sAfd");
        $qAfd=mysql_query($sAfd) or die(mysql_error());
        while($rAfd=mysql_fetch_assoc($qAfd))
        {
            $optUnit.="<option value='".$rAfd['kodeorganisasi']."'>".$rAfd['namaorganisasi']."</option>";
        }
        echo $optUnit;
	break;	
		
	case'getBlok':
        $optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sAfd="select distinct kodeorg from ".$dbname.".setup_blok where kodeorg like '".$afdId."%'";
        $qAfd=mysql_query($sAfd) or die(mysql_error());
        while($rAfd=mysql_fetch_assoc($qAfd))
        {
            $optUnit.="<option value='".$rAfd['kodeorg']."'>".$optNm[$rAfd['kodeorg']]."</option>";
        }
        echo $optUnit;
	break;
	
######HTML
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Workshop".$tglSkrg;
		if(strlen($stream)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					@unlink('tempExcel/'.$file);
				}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream))
			{
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			}
			else
			{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}           
		break;
	
	
	
###############	
#panggil PDFnya
###############
	
	case'pdf':
		class PDF extends FPDF {
			function Header() {
				global $conn;
				global $dbname;
				global $align;
				global $length;
				global $colArr;
				global $title;
				global $kdorg;
				global $kdAfd;
				global $where;
				global $nmOrg;
				global $lok;
				global $notrans;
				global $kdUnit;
				global $BlokId;
				global $periodeId;
				

				//$cols=247.5;
				$query = selectQuery($dbname,'organisasi','alamat,telepon',
					"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
				$orgData = fetchData($query);

				$width = $this->w - $this->lMargin - $this->rMargin;
				$height = 20;
				$path='images/logo.jpg';
				//$this->Image($path,$this->lMargin,$this->tMargin,50);	
				$this->Image($path,30,15,55);
				$this->SetFont('Arial','B',9);
				$this->SetFillColor(255,255,255);	
				$this->SetX(90); 
				  
				$this->Cell($width-80,12,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
				$this->SetX(90); 		
				$this->SetFont('Arial','',9);
				$height = 12;
				$this->Cell($width-80,$height,$orgData[0]['alamat'],0,1,'L');	
				$this->SetX(90); 			
				$this->Cell($width-80,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
				$this->Ln();
				$this->Line($this->lMargin,$this->tMargin+($height*4),
				$this->lMargin+$width,$this->tMargin+($height*4));

				$this->SetFont('Arial','B',12);
								$this->Ln();
				$height = 15;
								$this->Cell($width,$height,"Laporan Restan ".$BlokId,'',0,'C');
								$this->Ln();
				$this->SetFont('Arial','',10);
								$this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ".$periodeId,'',0,'C');
								//$this->Ln();
								$this->Ln(30);
				$this->SetFont('Arial','B',7);
				$this->SetFillColor(220,220,220);
								$this->Cell(5/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
								$this->Cell(20/100*$width,15,'Kode Blok',1,0,'C',1);
								$this->Cell(15/100*$width,15,'Tanggal',1,0,'C',1);
								$this->Cell(15/100*$width,15,'Restan Awal (JJG)',1,0,'C',1);
								$this->Cell(15/100*$width,15,'Panen (JJG)',1,0,'C',1);
								$this->Cell(15/100*$width,15,'Angkut (JJG)',1,0,'C',1);
								$this->Cell(15/100*$width,15,'Restan (JJG)',1,0,'C',1);
								$this->Ln();
			}
			
			function Footer()
			{
				$this->SetY(-15);
				$this->SetFont('Arial','I',8);
				$this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
			}
        }
		$pdf=new PDF('P','pt','A4');
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
		$height = 15;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
		
		$no=1;
		foreach($data as $blok=>$row) {
			$i=0;
			$pdf->Cell(5/100*$width,15,$no,1,0,'R');
			$pdf->Cell(20/100*$width,15,$blok,1,0,'L');
			if(isset($optRestan[$blok])) {
				$restanEnd = $optRestan[$blok];
			} else {
				$restanEnd = 0;
			}
			foreach($row as $tgl=>$row2) {
				if(!isset($row2['panen']))$row2['panen']=0;
				if(!isset($row2['spb']))$row2['spb']=0;
				$row2['start']=$restanEnd;
				$restanEnd += $row2['panen']-$row2['spb'];
				if($i>0) {
					$pdf->Cell(5/100*$width,15,$no,1,0,'R');
					$pdf->Cell(20/100*$width,15,'',1,0,'C');
				}
				$pdf->Cell(15/100*$width,15,tanggalnormal($tgl),1,0,'L');
				$pdf->Cell(15/100*$width,15,number_format($row2['start'],0),1,0,'R');
				$pdf->Cell(15/100*$width,15,number_format($row2['panen'],0),1,0,'R');
				$pdf->Cell(15/100*$width,15,number_format($row2['spb'],0),1,0,'R');
				$pdf->Cell(15/100*$width,15,number_format($restanEnd,0),1,0,'R');
				$pdf->Ln();
				$i++;
				$no++;
			}
		}
		$pdf->SetFont('Arial','BI',7);
		$pdf->Cell(55/100*$width,15,'TOTAL',1,0,'C');
		$pdf->Cell(15/100*$width,15,number_format($total['panen'],0),1,0,'R');
		$pdf->Cell(15/100*$width,15,number_format($total['spb'],0),1,0,'R');
		$pdf->Cell(15/100*$width,15,number_format('',0),1,0,'R');
		$pdf->Output();
            
	break;
	
	

	
	
	default:
	break;
}

?>