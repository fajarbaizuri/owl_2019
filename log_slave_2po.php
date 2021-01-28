<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
//echo $bulan.$kdorg;
if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//echo $kdorg;
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	
}
$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);

$optnamasup=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
$optbarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
	{
		echo"Error: Organisasi tidak boleh kosong"; 
		exit;
    }

/*    else if(($tgl==''))
	{
        echo"Error: Bulan tidak boleh kosong"; 
		exit;
    }*/
	
}	
			$stream="Laporan PO<br />Tanggal : ".tanggalnormal($tgl1)." s/d ".tanggalnormal($tgl2)." ";
			
              if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
					  <tr>
						<td align=center>No</td>
						<td align=center>Tanggal</td>
						<td align=center>No. PO</td>
						<td align=center>No. PB</td>
						<td align=center>Nama Supplier</td>
						<td align=center>Nama Barang</td>
						<td align=center>Qty</td>
						<td align=center>Satuan</td>
						<td align=center>Harga Satuan <br />Sebelum Diskon</td>
						
						<td align=center>Sub Total</td>
						<td align=center>Diskon %</td>
						<td align=center>Nilai Diskon</td>
						<td align=center>Harga Satuan <br />Setelah Diskon</td>
						<td align=center>Sub Total <br /> setelah diskon</td>
						<td align=center>PPN</td>
						<td align=center>Total</td>
						
					  </tr>
				</thead>
               <tbody>";
			   
$str="select * FROM ".$dbname.".log_poht where kodeorg='".$kdorg."' and tanggal between '".$tgl1."' and '".$tgl2."' and stat_release='1' order by tanggal,nopo asc";
//echo $str;		
$res=mysql_query($str);	
while($bar=mysql_fetch_assoc($res))
{

		
	
	$astr="select * FROM ".$dbname.".log_podt where nopo='".$bar['nopo']."'";		
	$ares=mysql_query($astr);	
	while($abar=mysql_fetch_assoc($ares))
	{
		$no+=1;
		if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
						else $stream.="<tr class=rowcontent>";
	
			$subjumlah=$abar['jumlahpesan']*$abar['hargasbldiskon'];
			$nilaidiskon=$subjumlah*($bar['diskonpersen']/100);
			
			if($bar['ppn']!=0)
			{
				$nilaippn=($subjumlah-$nilaidiskon)/10;
			}
			else
			{	
				$nilaippn=$bar['ppn'];
			}
			$subtotalsetelahdiskon=$abar['hargasatuan']*$abar['jumlahpesan'];
			//echo $subjumlah.___.$nilaidiskon.___.$nilaippn;exit();
			$totalbaris=$subjumlah-$nilaidiskon+$nilaippn;	
		
			$stream.="
				<td align=center>".$no."</td>
				<td align=center>".tanggalnormal($bar['tanggal'])."</td>
				<td align=left>".$abar['nopo']."</td>
				<td align=left>".$abar['nopp']."</td>
				<td align=left>".$optnamasup[$bar['kodesupplier']]."</td>
				<td align=left>".$optbarang[$abar['kodebarang']]."</td>
				<td align=right>".number_format($abar['jumlahpesan'])."</td>
				<td align=left>".$abar['satuan']."</td>
				<td align=right>".number_format($abar['hargasbldiskon'])."</td>
				
				<td align=right>".number_format($subjumlah)."</td>
				<td align=right>".$bar['diskonpersen']." %</td>
				<td align=right>".number_format($nilaidiskon)."</td>
				<td align=right>".number_format($abar['hargasatuan'])."</td>
				<td align=right>".number_format($subtotalsetelahdiskon)."</td>";
	     	$stream.="
				<td align=right>".number_format($nilaippn)."</td>
				<td align=right>".number_format($totalbaris)."</td>";
				
				//$a=$no;
			
				
				
				
						
		$stream.="</tr>";		
		$grandtotalsub+=$subjumlah;
		$grandtotalnilaidiskon+=$nilaidiskon;
		$grandtotalnilaippn+=$nilaippn;
		$grandtotalsemua+=$totalbaris;
		$grantotalsetelahdiskon+=$subtotalsetelahdiskon;
			
	}
		
		if($bar['ppn']=='0')
		{
			$subtotalsetelahdiskon=$bar['nilaipo'];
		}
		else
		{
			$subtotalsetelahdiskon=$bar['ppn']*10;
		}
		$stream.="<tr class=rowcontent><td align=center colspan=9>Sub Total</td>";
		$stream.="<td align=right>".number_format($bar['subtotal'])."</td>
					<td></td>
					<td align=right>".number_format($bar['nilaidiskon'])."</td>
					<td></td>
					<td align=right>".number_format($subtotalsetelahdiskon)."</td>
					<td align=right>".number_format($bar['ppn'])."</td>
					<td align=right>".number_format($bar['nilaipo'])."</td></tr>";

			
			
	}
	$stream.="
			<thead><tr>
				<td align=center colspan=9><B>Grand Total<b></td>
				<td align=right><B>".number_format($grandtotalsub)."</td>
				<td></td>
				<td align=right><B>".number_format($grandtotalnilaidiskon)."</td>
				<td></td>
				
				<td align=right><B>".number_format($grantotalsetelahdiskon)."</td>
				<td align=right><B>".number_format($grandtotalnilaippn)."</td>
				<td align=right><B>".number_format($grandtotalsemua)."</td>
			</tr></thead>";

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
		$nop_="Laporan_PO".$tglSkrg;
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