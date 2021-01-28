<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
//$noakun=$_POST['noakun'];
//exit("$kdsup");
//echo $kdorg;exit();

//$bk1=$_POST['bk1'];
//$bk2=$_POST['bk2'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
//echo $bk1;exit();
//echo $tgl1_."  ".$tgl2_;exit();

##pisah
$replan=$_POST['replan'];//echo $replan;exit();
$pmks=$_POST['pmks'];
$giro=$_POST['giro'];
$sisasaldo=$_POST['sisasaldo'];


if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//$noakun=$_GET['noakun'];
	//$bk1=$_GET['bk1'];
	//$bk2=$_GET['bk2'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	#pisah
	$replan=$_GET['replan'];//echo $replan;exit();
	$pmks=$_GET['pmks'];
	$giro=$_GET['giro'];
	$sisasaldo=$_GET['sisasaldo'];
	//echo $replan."-".$pmks."-".$giro;exit();
}

$optnmor=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjnvhc=makeOption($dbname, 'vhc_5jenisvhc','jenisvhc,namajenisvhc');
$optnmbar=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optnamacostumer=makeOption($dbname,'log_5supplier','supplierid,namasupplier');

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

##ambil data SQL untuk global view 
			$totalpmksreplan=$pmks+$replan;
			$totalsaldorekeninggiro=$sisasaldo+$giro;
			if($proses=='excel'){
			$stream="
					<table>
						<tr>
							<td colspan=6 align=left>Jumlah Pencairan Pinjaman BRI KI Replanting & New Planting</td> 
							<td align=right>".number_format($replan)."</td>
						</tr>		
						<tr>
							<td colspan=6 align=left>Jumlah Pencairan Pinjaman BRI KI PMKS</td> 
							<td align=right><u>".number_format($pmks)."</u></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan=5 align=left>Total Pencairan Pinjaman BRI KI Replanting & New Planting & PMKS</td> 
							<td align=right>".number_format($totalpmksreplan)."</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td colspan=6 align=left>Sisa Saldo Pinjaman Kredit Investasi</td> 
							<td align=right>".number_format($sisasaldo)."</td>
						</tr>
						
						<tr>
							<td colspan=6 align=left>Saldo Rekening Giro BRI</td> 
							<td align=right><u>".number_format($giro)."</u></td>
						</tr>
						
						<tr>
							<td>&nbsp;</td>
							<td colspan=5>Total Saldo Rekening Giro</td>
							<td>".number_format($totalsaldorekeninggiro)."</td>
						</tr>
			
					</table><br />
";
			}
			
			//$stream="Total replanting ".$replan." <br /> ";
//			$stream.="Total pmks ".$pmks." <br /> ";
//			
//			$stream.="Total Pencairan ".$totalpmksgiro." <br />";	
//			$stream.="Total giro ".$giro."  ";

              if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                 <tr>
				 	<td align=center>No</td>
					<td align=center>No. BK</td>
					
					<td align=center>Tanggal</td>
					<td align=center>Keterangan</td>
					<td align=center>No. Giro/Cek</td>
					<td align=center>Jumlah (Rp)</td>					
  				</tr></thead>
                <tbody>";
/*if($kdorg=='')
{
			$str=" 	SELECT kgpotsortasi,kodecustomer,beratbersih as netto,substr(tanggal,1,10) as tanggal,(beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan 
			WHERE tanggal between '".$tgl1."' and '".$tgl2."' and kodecustomer like '%S%' ";	
	//		echo $str;
}
else
{
			$str=" 	SELECT kgpotsortasi,kodecustomer,beratbersih as netto,substr(tanggal,1,10) as tanggal,(beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan 
			WHERE tanggal between '".$tgl1."' and '".$tgl2."' and kodecustomer='".$kdsup."'  ";
			
			//echo $str;
}*/

if($kdorg=='')//kodorg semua
{
	/*if($noakun=='')//kdorg semua,noakun semua
	{
		$str="select a.*,b.keterangan from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b
				on a.notransaksi=b.notransaksi where a.notransaksi between '".$bk1."' and '".$bk2."' ";
	}
	else//kodeorg semua,noakun isi
	{
		$str="select a.*,b.keterangan from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b
				on a.notransaksi=b.notransaksi 
				where a.noakun='".$noakun."' and a.notransaksi between '".$bk1."' and '".$bk2."' ";	
	}*/
	
	/*$str="select a.*,b.keterangan from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b
				on a.notransaksi=b.notransaksi where a.notransaksi between '".$bk1."' and '".$bk2."' ";*/
	
	$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' and posting=0 order by notransaksi ";
	//echo $str;
	//echo $str;
	
	/*select * from fbg.keu_kasbankht where notransaksi between 'BK-002/07BRIHO' and 'BK-156/07/BRIHO' and tanggal between '2012-01-01' and '2012-12-12'*/
				
				
}
else
{	
	$str="select * from ".$dbname.".keu_kasbankht where kodeorg='".$kdorg."' and tanggal between '".$tgl1."' and '".$tgl2."' and posting=0 order by notransaksi ";
	//echo $str;
		
	/*if($noakun=='')//kdorg isi,noakun semua
	{
		$str="select a.*,b.keterangan from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b
				on a.notransaksi=b.notransaksi 
				where a.kodeorg='".$kdorg."' and a.notransaksi between '".$bk1."' and '".$bk2."' ";	
	}
	else//kodeorg isi,noakun isi
	{
		$str="select a.*,b.keterangan from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b
				on a.notransaksi=b.notransaksi 
				where a.kodeorg='".$kdorg."' and a.noakun='".$noakun."' and a.notransaksi between '".$bk1."' and '".$bk2."' ";	
	}*/
	/*$str="select a.*,b.keterangan from ".$dbname.".keu_kasbankdt a left join ".$dbname.".keu_kasbankht b
				on a.notransaksi=b.notransaksi 
				where a.kodeorg='".$kdorg."' and a.notransaksi between '".$bk1."' and '".$bk2."' ";*/
}
//echo $str;




$res=mysql_query($str);	
				
$no=0;
$ttl=0;





while($bar=mysql_fetch_assoc($res))
{
	
	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
		$no+=1;
		
			$stream.="
				<td align=center>".$no."</td>
				<td align=left>".$bar['notransaksi']."</td>
				<td align=left>".tanggalnormal($bar['tanggal'])."</td>
				<td align=left>".$bar['keterangan']."</td>
				<td align=right>".$bar['nogiro']."</td>
				<td align=right>".number_format($bar['jumlah'])."</td>
		</tr>";		
		
		#untuk total
		$tototal+=$bar['jumlah'];

					
	}
	
	
	$stream.="
				<thead><tr>
					<td align=center colspan=5>Total Pengeluaran</td>
					<td align=right>".number_format($tototal)."</td>
				</tr>
	</tbody></table><br />
";
	
	if($proses=='excel')
	{
	$hasil=$totalsaldorekeninggiro-$tototal;
	$stream.="<table>
			<tr>
				<td colspan=6>Saldo Akhir Rekening Giro BRI</td>
				<td>".number_format($hasil)."</td>
			</tr>
			</table><br /><br /><br />


				";
				
	$stream.="
	
	<table>
		<tr>
			<td colspan=2 align=center>Dibuat Oleh</td>
			<td align=center colspan=2>Diperiksa Oleh</td>
			<td align=center colspan=2>Diketahui Oleh</td>
			<td align=center>Disetujui Oleh</td>

		</tr>
		<tr></tr>
		<tr></tr>
		<tr></tr>
		<tr>
			<td colspan=2 align=center><u>Yossi Arita</u></td>
			<td align=center colspan=2><u>Ratnawathy</u></td>
			<td align=center colspan=2><u>Tri Budi Hardono</u></td>
			<td align=center><u>Asdar Hasan</u></td>
		</tr>
		<tr>
			<td colspan=2 align=center>Finance Staff</td>
			<td align=center colspan=2>Finance Supervisor</td>
			<td align=center colspan=2>Finance Manager</td>
			<td align=center>Finance Director</td>
		</tr>
		
		
	
	
	</table>
	
	
	
	";
				
	}
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
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="LAPORAN_RENCANA_PEMBAYARAN".$tglSkrg;
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
							
							global $replan;
							global $pmks;
							global $giro;
							global $sisasaldo;
							global $kdorg;//echo $kdorg; exit();
							
							
							
							//$arr="##kdorg##bk1##bk2##tgl1##tgl2##replan##pmks##sisasaldo##giro";
							

                            //$cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);


			
                            $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 20;
                           // $path='images/logo.jpg';
							
							$ha="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdorg."'";
							$hi=mysql_query($ha) or die(mysql_error());
							$hu=mysql_fetch_assoc($hi);
								$orglogo=$hu['induk'];
							
							if($orglogo=='FBB')
							{
								$path='images/logo2.jpg';
							}
							else if ($orglogo=='USJ')
							{
								$path='images/logo3.jpg';
							}
							else
								$path='images/logo.jpg';
								
							$ah="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$hu['induk']."'";
							$ih=mysql_query($ah) or die(mysql_error());
							$uh=mysql_fetch_assoc($ih);
								$namahead=$uh['namaorganisasi'];
								$alamathead=$uh['alamat'];
								$teleponhead=$uh['telepon'];
							
							
							
                            //$this->Image($path,$this->lMargin,$this->tMargin,50);	
							$this->Image($path,30,15,55);
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(90); 
							  
                            //$this->Cell($width-80,12,$_SESSION['org']['namaorganisasi'],0,1,'L');
							$this->Cell($width-80,12,$namahead,0,1,'L');	 
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',9);
							$height = 12;
                            //$this->Cell($width-80,$height,$orgData[0]['alamat'],0,1,'L');	
							$this->Cell($width-80,$height,$alamathead,0,1,'L');
                            $this->SetX(90); 			
                            //$this->Cell($width-80,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                            $this->Cell($width-80,$height,"Tel: ".$teleponhead,0,1,'L');
							$this->Ln();
                            $this->Line($this->lMargin,$this->tMargin+($height*4),
                            $this->lMargin+$width,$this->tMargin+($height*4));

                            $this->SetFont('Arial','B',12);
                                            $this->Ln();
                            $height = 15;
                                            $this->Cell($width,$height,"Laporan Rencana Pembayaran ".$kdorg,'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
										
											if($tgl1==$tgl2)
											{
												$this->Cell($width,$height,strtoupper($_SESSION['lang']['tanggal'])." : ".tanggalnormal($tgl1),'',0,'C');
											}
											else
											{
							
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['tanggal'])." : ". tanggalnormal($tgl1)." S/D ". tanggalnormal($tgl2),'',0,'C');
											}
											//$this->Ln();
                                            $this->Ln(40);
										
										
										$totalpmksreplan=$pmks+$replan;
										$totalsaldorekeninggiro=$sisasaldo+$giro;
										
										if($this->PageNo()=='1')
										{
											## Draw Box
											$this->SetDrawColor(0,0,0);
											## Box Supplier
											$this->Rect(20,135,550,115);
											
											
											$this->SetFont('Arial','',8);
											//$this->SetX(90); 
											$this->Cell(1/100*$width,15,'Jumlah Pencairan Pinjaman BRI KI Replanting & New Planting',0,0,'L',1);
											$this->SetX(560); 
											$this->Cell(1/100*$width,15,number_format($replan),0,1,'R',1);
											
											$this->Cell(1/100*$width,15,'Jumlah Pencairan Pinjaman BRI KI PMKS',0,0,'L',1);
											$this->SetX(560); 
											$this->SetFont('Arial','',8);
											$this->Cell(1/100*$width,15,number_format($pmks),0,1,'R',1);
											
											// $this->Line($this->lMargin,$this->tMargin+($height*4);
											//$this->line(520,173,565,173);
											$this->SetFont('Arial','',8);
											$this->SetX(50); 
											$this->Cell(1/100*$width,15,'Total Pencairan Pinjaman BRI KI Replanting & New Planting & PMKS',0,0,'L',1);
											//$this->SetX(560);
											$this->SetX(485);
											$this->SetFont('Arial','B',8);
											$this->Cell(15/100*$width,15,number_format($totalpmksreplan),1,1,'R',1);	
											
											####
											$this->Ln(10);
											$this->SetFont('Arial','',8);
											$this->Cell(1/100*$width,15,'Saldo Pinjaman Kredit Investasi',0,0,'L',1);
											$this->SetX(560); 
											$this->Cell(1/100*$width,15,number_format($sisasaldo),0,1,'R',1);
											
											$this->Cell(1/100*$width,15,'Saldo Rekening Giro BRI',0,0,'L',1);
											$this->SetX(560); 
											$this->SetFont('Arial','',8);
											$this->Cell(1/100*$width,15,number_format($giro),0,1,'R',1);
											
											$this->SetFont('Arial','',8);
											$this->SetX(50); 
											$this->Cell(1/100*$width,15,'Total Saldo Rekening Giro',0,0,'L',1);
											$this->SetX(485);
											$this->SetFont('Arial','B',8);
											$this->Cell(15/100*$width,15,number_format($totalsaldorekeninggiro),1,1,'R',1);					
										
										}//tutup keterangan atas beserta boxnya
														
											$this->Ln(30);
                           					$this->SetFont('Arial','B',8);
                            				$this->SetFillColor(220,220,220);
											
                                            $this->Cell(3/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
											$this->Cell(13/100*$width,15,'No. BK',1,0,'C',1);
											$this->Cell(9/100*$width,15,'Tanggal',1,0,'C',1);
											$this->Cell(55/100*$width,15,'Keterangan',1,0,'C',1);
											$this->Cell(10/100*$width,15,'No. Giro/Cek',1,0,'C',1);
											$this->Cell(11/100*$width,15,'Total',1,1,'C',1);	
											//$this->Ln();
                       }
					   
					   
					
					

                        function Footer()
                        {
                            $this->SetY(-15);
                            $this->SetFont('Arial','I',6);
                            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('P','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 15;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',8);
		
		
		
		$res=mysql_query($str);//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		$ttl=0;
		while($bar=mysql_fetch_assoc($res))
		{			
			$no+=1;	
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(13/100*$width,$height,$bar['notransaksi'],1,0,'L',1);		
			$pdf->Cell(9/100*$width,$height,tanggalnormal($bar['tanggal']),1,0,'L',1);		
			$pdf->Cell(55/100*$width,$height,$bar['keterangan'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,$bar['nogiro'],1,0,'R',1);
			//$pdf->Cell(8/100*$width,$height,9999999,1,0,'R',1);
			$pdf->Cell(11/100*$width,$height,number_format($bar['jumlah']),1,1,'R',1);	
		
			$tototal+=$total;
			
											
			if($no==25) 
			{
				//$i=0;
				/*$akhirY=$akhirY-20;
				$akhirY=$pdf->GetY()-$akhirY;
				$akhirY=$akhirY+25;*/
				$pdf->AddPage();
			}
		
		}
			$pdf->SetFillColor(220,220,220);
			$pdf->SetFont('arial','B',8);
			$pdf->Cell(90/100*$width,$height,strtoupper('Total Pengeluaran'),1,0,'C',1);
			$pdf->Cell(11/100*$width,$height,number_format($tototal),1,1,'R',1);
			
			
			$hasil=$totalsaldorekeninggiro-$tototal;
			//$pdf->SetFillColor(225,225,225);
			//$pdf->SetFillColor(255,255,255);
			$pdf->SetFillColor(220,220,220);	
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(86/100*$width,15,'Saldo Akhir Rekening Giro BRI',1,0,'L',1);
			//$pdf->SetX(560); 
			$pdf->Cell(14/100*$width,15,number_format($hasil),1,1,'R',1);
			
			## Draw Box
			$pdf->SetDrawColor(0,0,0);
			## Box Supplier
			$posisiY=$pdf->GetY()+10;
			$pdf->Rect(20,$posisiY,550,115);
			//$pdf->Rect(20,350,550,115);
			$pdf->Ln(20);
			$pdf->SetX(65); 
			$pdf->SetFillColor(255,255,255);
			$pdf->Cell(1/100*$width,15,'Dibuat Oleh',0,0,'C',1);
			
			$pdf->SetX(215); 
			$pdf->Cell(1/100*$width,15,'Diperiksa Oleh',0,0,'C',1);
			
			$pdf->SetX(365); 
			$pdf->Cell(1/100*$width,15,'Diketahui Oleh',0,0,'C',1);
			
			$pdf->SetX(515); 
			$pdf->Cell(1/100*$width,15,'Disetujui Oleh',0,0,'C',1);
			
			$pdf->Ln(50);
			$pdf->SetX(65); 
			$pdf->SetFont('Arial','U',8);
			$pdf->Cell(1/100*$width,15,'Yossi Arita',0,0,'C',1);
			
			$pdf->SetX(215);
			$pdf->Cell(1/100*$width,15,'Ratnawathy',0,0,'C',1);
			
			$pdf->SetX(365);
			$pdf->Cell(1/100*$width,15,'Tri Budi Hardono',0,0,'C',1);
			
			$pdf->SetX(515);
			$pdf->Cell(1/100*$width,15,'Asdar Hasan',0,0,'C',1);	
			
			$pdf->Ln(15);
			$pdf->SetX(65); 
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(1/100*$width,15,'Finance Staff',0,0,'C',1);
			
			$pdf->SetX(215);
			$pdf->Cell(1/100*$width,15,'Finance Supervisor',0,0,'C',1);
			
			$pdf->SetX(365);
			$pdf->Cell(1/100*$width,15,'Finance Manager',0,0,'C',1);
			
			$pdf->SetX(515);; 
			$pdf->Cell(1/100*$width,15,'Finance Director',0,0,'C',1);		
			
			

		
		$pdf->Output();
            
	break;

	
	
	default:
	break;
}

?>