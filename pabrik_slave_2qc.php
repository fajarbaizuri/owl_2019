<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$bulan=$_POST['bulan'];
$produk=$_POST['produk'];
//echo $bulan.$kdorg;
if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//echo $kdorg;
	$bulan=$_GET['bulan'];
	$produk=$_GET['produk'];
	
}




if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
	{
		echo"Error: Pabrik tidak boleh kosong"; 
		exit;
    }

    else if(($bulan==''))
	{
        echo"Error: Periode tidak boleh kosong"; 
		exit;
    }
	else if(($produk=='0'))
	{
        echo"Error: Produk Belum Dipilih"; 
		exit;
    }
	
}





$str=" 	SELECT * FROM ".$dbname.".pabrik_ops_qcdt
			WHERE substr(tanggal,1,7)='".$bulan."' ";
//echo $str;
$res=mysql_query($str);	
			
			$stream="Laporan Quality Control<br />Periode : ".$bulan." ";
			
              if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable' style=\"table-layout: fixed; width: 100%;\"   >";
                $stream.="<thead class=rowheader>
					  <tr>";
						$stream.="<td style=\"width:30px;\" align=center rowspan=4>Tgl</td>";
					if ($produk=="CPO"){  
						$stream.="
						<td align=center colspan=28 style=\"width:1500px;\" >OIL LOSSES(%)</td>
						<td align=center colspan=5 style=\"width:270px;\">MUTU CPO(%)</td>";
					}
					if ($produk=="PKO"){	
						$stream.="<td align=center colspan=23 style=\"width:1270px;\">KERNEL LOSSES(%)</td>
						<td align=center colspan=3 style=\"width:160px;\">MUTU KERNEL(%)</td>";
					}	
				$stream.="</tr>";
					  
				$stream.="<tr>";
						if ($produk=="CPO"){  
						$stream.="<td align=center colspan=3>Screw Press I</td>
						<td align=center colspan=3>Screw Press II</td>
						<td align=center colspan=3>Screw Press III</td>
						<td align=center colspan=3>Screw Press IV</td>
						<td align=center colspan=3>Screw Press V</td>
						<td align=center colspan=3>Screw Press VI</td>
						<td align=center colspan=2>Solid Decanter</td>
						<td align=center colspan=2>Heavy Phase Decanter</td>
						<td align=center rowspan=2>Heavy Phase Separator</td>
						<td align=center rowspan=2>Under Flow</td>
						<td align=center rowspan=2>Cond. Rebusan</td>
						<td align=center rowspan=2>Fat-Fit</td>
						<td align=center rowspan=2>Tandan Kosong</td>
						<td align=center rowspan=2>Brondol Ikut Janjang Kosong</td>
						<td align=center colspan=3>CPO Produksi</td>
						<td align=center colspan=2>CPO Stock</td>";
						}
						if ($produk=="PKO"){  
						$stream.="<td align=center colspan=3>Ripple Mill I</td>
						<td align=center colspan=3>Ripple Mill II</td>
						<td align=center colspan=3>Ripple Mill III</td>
						<td align=center colspan=3>Ripple Mill IV</td>
						<td align=center colspan=3>Ripple Mill V</td>
						<td align=center colspan=3>Ripple Mill VI</td>
						<td align=center rowspan=2>Cangkang Hidrey Clone</td>
						<td align=center rowspan=2>Clay Batch</td>
						<td align=center rowspan=2>LTDS I</td>
						<td align=center rowspan=2>LTDS II</td>
						<td align=center rowspan=2>Fibre Cyclone</td>
						<td align=center rowspan=2>Kdr. Kotoran</td>
						<td align=center rowspan=2>Kdr. Air</td>						
						<td align=center rowspan=2>Kdr. ALB</td>";
						}
				$stream.="</tr>";
					  
				$stream.="<tr>";
						if ($produk=="CPO"){ 
						$stream.="<td align=center>Press Cake (OLDB)</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Press Cake (OLDB)</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Press Cake (OLDB)</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Press Cake (OLDB)</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Press Cake (OLDB)</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Press Cake (OLDB)</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>I</td>
						<td align=center>II</td>
						<td align=center>I</td>
						<td align=center>II</td>
						<td align=center>ALB</td>
						<td align=center>Ka. Air</td>
						<td align=center>Ka. Kotoran</td>
						<td align=center>ALB</td>
						<td align=center>Ka. Air</td>";
						}
						if ($produk=="PKO"){ 
						$stream.="<td align=center>Effisiensi</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Effisiensi</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Effisiensi</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Effisiensi</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Effisiensi</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>
						<td align=center>Effisiensi</td>
						<td align=center>Bj. Pecah</td>
						<td align=center>In. Pecah</td>";
						}
						
						
				$stream.="</tr>";
				$stream.="<tr>";
						if ($produk=="CPO"){ 
						$stream.="<td align=center >≤ 8.00</td>
						<td align=center >≤ 5.00</td>
						<td align=center >≤ 15.00</td>
						<td align=center >≤ 8.00</td>
						<td align=center >≤ 5.00</td>
						<td align=center >≤ 15.00</td>
						<td align=center >≤ 8.00</td>
						<td align=center >≤ 5.00</td>
						<td align=center >≤ 15.00</td>
						<td align=center >≤ 8.00</td>
						<td align=center >≤ 5.00</td>
						<td align=center >≤ 15.00</td>
						<td align=center >≤ 8.00</td>
						<td align=center >≤ 5.00</td>
						<td align=center >≤ 15.00</td>
						<td align=center >≤ 8.00</td>
						<td align=center >≤ 5.00</td>
						<td align=center >≤ 15.00</td>
						<td align=center >3.00</td>
						<td align=center >3.00</td>
						<td align=center >1.00</td>
						<td align=center >1.00</td>
						<td align=center >1.00</td>
						<td align=center >≤ 8.00</td>	
						<td align=center >≤ 2.00</td>	
						<td align=center >≤ 2.00</td>	
						<td align=center >≤ 1.00</td>
						<td align=center >2.00</td>	
						<td align=center >≤ 3.50</td>
						<td align=center >0.15</td>	
						<td align=center >0.02</td>							
						<td align=center >< 5.00</td>									
						<td align=center >0.15</td>";
						}
						if ($produk=="PKO"){ 
						$stream.="<td align=center>> 98.00</td>
						<td align=center>< 2.00</td>
						<td align=center>10.00</td>
						<td align=center>> 98.00</td>
						<td align=center>< 2.00</td>
						<td align=center>10.00</td>
						<td align=center>> 98.00</td>
						<td align=center>< 2.00</td>
						<td align=center>10.00</td>
						<td align=center>> 98.00</td>
						<td align=center>< 2.00</td>
						<td align=center>10.00</td>
						<td align=center>> 98.00</td>
						<td align=center>< 2.00</td>
						<td align=center>10.00</td>
						<td align=center>> 98.00</td>
						<td align=center>< 2.00</td>
						<td align=center>10.00</td>
						<td align=center>03.00</td>
						<td align=center>< 1.00</td>
						<td align=center>< 1.00</td>
						<td align=center>< 1.50</td>
						<td align=center>< 1.00</td>
						<td align=center>< 7.00</td>
						<td align=center>< 7.00</td>
						<td align=center>< 5.00</td>";
						}
				$stream.="</tr>
				</thead>
               <tbody>";


while($bar=mysql_fetch_assoc($res))
{

	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
			$stream.="
				<td align=center>".substr($bar['tanggal'],8,9)."</td>";
				if ($produk=="CPO"){ 
				$stream.="<td align=right>".$bar['sp1press']."</td>
				<td align=right>".$bar['sp1bjpecah']."</td>
				<td align=right>".$bar['sp1inpecah']."</td>
				
				<td align=right>".$bar['sp2press']."</td>
				<td align=right>".$bar['sp2bjpecah']."</td>
				<td align=right>".$bar['sp2inpecah']."</td>
				
				<td align=right>".$bar['sp3press']."</td>
				<td align=right>".$bar['sp3bjpecah']."</td>
				<td align=right>".$bar['sp3inpecah']."</td>
				
				<td align=right>".$bar['sp4press']."</td>
				<td align=right>".$bar['sp4bjpecah']."</td>
				<td align=right>".$bar['sp4inpecah']."</td>
				
				<td align=right>".$bar['sp5press']."</td>
				<td align=right>".$bar['sp5bjpecah']."</td>
				<td align=right>".$bar['sp5inpecah']."</td>
				
				<td align=right>".$bar['sp6press']."</td>
				<td align=right>".$bar['sp6bjpecah']."</td>
				<td align=right>".$bar['sp6inpecah']."</td>
				
				
				<td align=right>".$bar['soliddecanter1']."</td>
				<td align=right>".$bar['soliddecanter2']."</td>
				
				
				<td align=right>".$bar['hpdecanter1']."</td>
				<td align=right>".$bar['hpdecanter2']."</td>
				<td align=right>".$bar['hpseparator']."</td>
				<td align=right>".$bar['underflow']."</td>
				<td align=right>".$bar['condrebusan']."</td>
				<td align=right>".$bar['fatpit']."</td>
				
				<td align=right>".$bar['tandankosong']."</td>
				<td align=right>".$bar['bdijangkos']."</td>
				<td align=right>".$bar['cpoproduksialb']."</td>
				<td align=right>".$bar['cpoproduksiair']."</td>
				<td align=right>".$bar['cpoproduksikot']."</td>
				<td align=right>".$bar['cpostokalb']."</td>
				<td align=right>".$bar['cpostokair']."</td>";
				}
				if ($produk=="PKO"){ 
				
				$stream.="<td align=right>".$bar['rm1']."</td>
				<td align=right>".$bar['rm1bjpecah']."</td>
				<td align=right>".$bar['rm1inpecah']."</td>
				
				<td align=right>".$bar['rm2']."</td>
				<td align=right>".$bar['rm2bjpecah']."</td>
				<td align=right>".$bar['rm2inpecah']."</td>
				
				<td align=right>".$bar['rm3']."</td>
				<td align=right>".$bar['rm3bjpecah']."</td>
				<td align=right>".$bar['rm3inpecah']."</td>
				
				<td align=right>".$bar['rm4']."</td>
				<td align=right>".$bar['rm4bjpecah']."</td>
				<td align=right>".$bar['rm4inpecah']."</td>
				
				<td align=right>".$bar['rm5']."</td>
				<td align=right>".$bar['rm5bjpecah']."</td>
				<td align=right>".$bar['rm5inpecah']."</td>
				
				<td align=right>".$bar['rm6']."</td>
				<td align=right>".$bar['rm6bjpecah']."</td>
				<td align=right>".$bar['rm6inpecah']."</td>
				
				
				
				<td align=right>".$bar['cangkanghydre']."</td>
				<td align=right>".$bar['claybatch']."</td>
				<td align=right>".$bar['ltds1']."</td>
				<td align=right>".$bar['ltds2']."</td>
				
				<td align=right>".$bar['fibrecycl']."</td>
				
				
				
				<td align=right>".$bar['kernelproduksikot']."</td>
				<td align=right>".$bar['kernelproduksiair']."</td>
				<td align=right>".$bar['kernelproduksialb']."</td>
				";
				}
		$stream.="</tr>";					
	}
	$stream.="</tbody></table><br />";
	
	if(($proses=='excel')or($proses=='pdf'))
	{
	$stream.="
		<table>
			<tr>
				<td colspan=12 align=left>Mengetahui</td>
				<td colspan=11 align=left>Diperiksa</td>
				<td colspan=3 align=left>Diperiksa</td>
			</tr><br />
			<tr>
				<td colspan=12 align=left>A S K E P</td>
				<td colspan=11 align=left>AST. MUTU</td>
				<td colspan=3 align=left>A N A L I S</td>
			</tr>
			</table>";
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
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Quality_Control_".$produk."-".$tglSkrg;
	
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