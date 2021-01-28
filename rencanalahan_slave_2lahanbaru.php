<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
//exit("$kdsup");
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	
}

$optnmor=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjnvhc=makeOption($dbname, 'vhc_5jenisvhc','jenisvhc,namajenisvhc');
$optnmbar=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');


$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);



if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($tgl1_=='')or($tgl2_==''))
	{
		echo"Error: Tanggal tidak boleh kosong"; 
		exit;
    }

    else if($tgl1>$tgl2)
	{
        echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua"; 
		exit;
    }
	
}

              if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                 <tr>
					<td align=center>No</td>
					<td align=center>".$_SESSION['lang']['locationname']."</td>
					<td align=center>".$_SESSION['lang']['startdate']."</td>
					<td align=center>".$_SESSION['lang']['purposedfor']."</td>
					<td align=center>".$_SESSION['lang']['villagename']."</td>
					<td align=center>".$_SESSION['lang']['subdistrict']."</td>
					<td align=center>".$_SESSION['lang']['regency']."</td>
					<td align=center>".$_SESSION['lang']['province']."</td>
					<td align=center>".$_SESSION['lang']['country']."</td>	
					<td align=center>".$_SESSION['lang']['pic']."</td>  
					<td align=center>Luas Lahan</td>
  				</tr></thead>
                <tbody>";

$str=" 	SELECT * FROM ".$dbname.".rencana_lahan WHERE tanggalmulai between '".$tgl1."' and '".$tgl2."'";	
//echo $str;
$res=mysql_query($str);					
$no=0;
while($bar1=mysql_fetch_assoc($res))
{
	if($proses=='excel')
		$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
	else $stream.="<tr class=rowcontent>";
	
	$no+=1;	
	$stream.="
		<td>".$no."</td>
		    <td>".$bar1['nama']."</td>
			<td>".tanggalnormal($bar1['tanggalmulai'])."</td>
			<td>".$bar1['peruntukanlahan']."</td>
			<td>".$bar1['desa']."</td>
			<td>".$bar1['kecamatan']."</td>
			<td>".$bar1['kabupaten']."</td>
			<td>".$bar1['provinsi']."</td>
			<td>".$bar1['negara']."</td>
			<td>".$bar1['kontak']."</td>
			<td align=right>".$bar1['luas']."</td>
</tr>";					
	}
	$stream.="</tbody></table>";
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

            class PDF extends FPDF
                    {
                        function Header() {
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
							global $kdorg;
							global $kdAfd;
							global $tgl1;
							global $tgl2;
							global $where;
							global $nmOrg;
							global $lok;
							global $notrans;
							

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
                                            $this->Cell($width,$height,"Laporan Rencana Lahan",'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ". tanggalnormal($tgl1)." S/D ". tanggalnormal($tgl2),'',0,'C');
											//$this->Ln();
                                            $this->Ln(30);
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(3/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(10/100*$width,15,$_SESSION['lang']['locationname'],1,0,'C',1);
											$this->Cell(7/100*$width,15,$_SESSION['lang']['startdate'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['purposedfor'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['villagename'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['subdistrict'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['regency'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['province'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['country'],1,0,'C',1);
											$this->Cell(10/100*$width,15,$_SESSION['lang']['pic'],1,0,'C',1);
											$this->Cell(10/100*$width,15,'Luas Lahan',1,1,'C',1);	
											
											
											//$this->Ln();
                       }

                        function Footer()
                        {
                            $this->SetY(-15);
                            $this->SetFont('Arial','I',8);
                            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('L','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 15;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',7);
		
		$res=mysql_query($str);//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		$ttl=0;
		while($bar1=mysql_fetch_assoc($res))
		{	
			
		
			$no+=1;	
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(10/100*$width,$height,$bar1['nama'],1,0,'L',1);
			$pdf->Cell(7/100*$width,$height,tanggalnormal($bar1['tanggalmulai']),1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,$bar1['peruntukanlahan'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['desa'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['kecamatan'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['kabupaten'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['provinsi'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['negara'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['kontak'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar1['luas'],1,1,'R',1);
		}
		
		
											/*<td>".$bar1['nama']."</td>
			<td>".tanggalnormal($bar1['tanggalmulai'])."</td>
			<td>".$bar1['peruntukanlahan']."</td>
			<td>".$bar1['desa']."</td>
			<td>".$bar1['kecamatan']."</td>
			<td>".$bar1['kabupaten']."</td>
			<td>".$bar1['provinsi']."</td>
			<td>".$bar1['negara']."</td>
			<td>".$bar1['kontak']."</td>
			<td align=right>".$bar1['luas']."</td>*/
		
		$pdf->Output();
            
	break;

	
	
	default:
	break;
}

?>