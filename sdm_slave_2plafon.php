<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$tahun=$_POST['tahun'];
//echo $bulan.$kdorg;
if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//echo $kdorg;
	$tahun=$_GET['tahun'];
	
}


$optOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$optNm=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');


if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
	{
		echo"Error: Kode Organisasi tidak boleh kosong"; 
		exit;
    }

    else if(($tahun==''))
	{
        echo"Error: Tahun tidak boleh kosong"; 
		exit;
    }
	
}





$str=" 	SELECT sum(a.totalklaim) as totalklaim,b.* FROM ".$dbname.".sdm_pengobatanht a left join ".$dbname.".sdm_5plafon b 
		on  a.tahunplafon=b.tahun and a.karyawanid=b.karyawanid
			WHERE a.kodeorg='".$kdorg."' and b.tahun='".$tahun."'  group by a.kodeorg,b.tahun,a.karyawanid ";

//echo $str;			
			
//echo $str;
$res=mysql_query($str);	
			//Tahun Plafon : ".$tahun."
			$stream="Tahun Plafon : ".$tahun." <br />Unit : ".$optOrg[$kdorg]."<br /><br />";
			
              if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
					  <tr>
						<td align=center>No</td>
						<td align=center>Nama Karyawan</td>
						<td align=center>Plafon</td>
						<td align=center>Pemakaian</td>
					  </tr>
				</thead>
               <tbody>";

while($bar=mysql_fetch_assoc($res))
{
	$no+=1;
	
	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
			$stream.="
				<td>".$no."</td>
				<td align=left>".$optNm[$bar['karyawanid']]."</td>
				<td align=right>".number_format($bar['jumlah'])."</td>
				<td align=right>".number_format($bar['totalklaim'])."</td>
		</tr>";		
		
	$totaljumlah+=$bar['jumlah'];
	$totalklaim+=$bar['totalklaim'];
					
}
$stream.="<thead><tr>
			<td align=center colspan=2>Total</td>
			<td align=right>".number_format($totaljumlah)."</td>
			<td align=right>".number_format($totalklaim)."</td>
			
		</tr>";

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
	
	//$stream="Tahun Plafon : ".$tahun." <br />Unit : ".$optOrg[$kdorg]."<br /><br />";
	
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Monitoring_Plafon".$tahun;
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
                                            $this->Cell($width,$height,"Laporan Harga TBS ".$kdorg,'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ". tanggalnormal($tgl1)." S/D ". tanggalnormal($tgl2),'',0,'C');
											//$this->Ln();
                                            $this->Ln(30);
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(3/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(15/100*$width,15,'Supplier',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Tanggal',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Netto',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Harga Satuan',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Total',1,1,'C',1);	
											//$this->Ln();
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
		
		$res=mysql_query($str);//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		$ttl=0;
		while($bar=mysql_fetch_assoc($res))
		{	
			$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$bar['kodecustomer']."'";
	//echo $sNm;
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namasupplier'];
				
			$total=$bar['netto']*number_format($bar['harga']);
				//echo $sNm;			
			$no+=1;	
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$nm,1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,tanggalnormal($bar['tanggal']),1,0,'L',1);		
				
			$pdf->Cell(10/100*$width,$height,$bar['netto'],1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,number_format($bar['harga']),1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,number_format($total),1,1,'R',1);	
		
		
		//$totnet+=$bar['netto'];
		
		}
		
			//$pdf->SetFillColor(220,220,220);
//			//$pdf->SetFont('arial','B',10);
//			$pdf->Cell(28/100*$width,$height,strtoupper('Total'),1,0,'C',1);
//					
//			$pdf->Cell(10/100*$width,$height,$totnet,1,0,'R',1);	
//			$pdf->Cell(10/100*$width,$height,number_format($totbiayapdf,2),1,0,'R',1);
//			$pdf->Cell(10/100*$width,$height,number_format($totbiayapdf,2),1,1,'R',1);
//		
		
		$pdf->Output();
            
	break;

	
	
	default:
	break;
}

?>