<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$tgl=$_POST['tgl'];
//echo $bulan.$kdorg;
if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//echo $kdorg;
	$tgl=$_GET['tgl'];
}


$tgl=tanggalsystem($tgl); $tgl=substr($tgl,0,4).'-'.substr($tgl,4,2).'-'.substr($tgl,6,2);
//$tgl=tanggalsystem($tgl);
$optnm=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjabatan=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
$optnama=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
	{
		echo"Error: Pabrik tidak boleh kosong"; 
		exit;
    }

    else if(($tgl==''))
	{
        echo"Error: Tanggal tidak boleh kosong"; 
		exit;
    }
	
}


$str=" 	SELECT * FROM ".$dbname.".pabrik_ops_pressandtb
			WHERE notransaksi like '%".$kdorg."%' and substr(waktu,1,10)='".$tgl."' ";
			
			//tanggalnormal
//echo $str;
$res=mysql_query($str);	
			$stream="<table>
				<tr>
					<td colspan=27 align=center style=font-size:18px><b><u>LAPORAN HARIAN JAM OPRASIONAL DIGESTER</b></u></td>
				</tr>
				<tr>	
					<td>Hari</td>
					<td>: ".hari($tgl)."</td>
				</tr>	
				<tr>
					<td>Tanggal</td>
					<td>: ".tanggalnormal($tgl)."</td>
				</tr></table>";
			//$stream.="Hari : ".hari($tgl)." <br />";
//			$stream.="Tanggal : ".tanggalnormal($tgl)." ";
			
              if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
				  <tr>
						<td align=center colspan=4>DIGESTER I</td>
						<td align=center colspan=4>DIGESTER II</td>
						<td align=center colspan=4>DIGESTER III</td>
						<td align=center colspan=4>DIGESTER IV</td>
						<td align=center colspan=4>DIGESTER V</td>
						<td align=center colspan=4>DIGESTER VI</td>
					  </tr>
					  <tr>
						<td align=center>Jam</td>
						<td align=center>Hour Meter (HM)</td>
						<td align=center>Amper(A)</td>
						<td align=center>ºC</td>
						<td align=center>Jam</td>
						<td align=center>Hour Meter (HM)</td>
						<td align=center>Amper(A)</td>
						<td align=center>ºC</td>
						<td align=center>Jam</td>
						<td align=center>Hour Meter (HM)</td>
						<td align=center>Amper(A)</td>
						<td align=center>ºC</td>
						<td align=center>Jam</td>
						<td align=center>Hour Meter (HM)</td>
						<td align=center>Amper(A)</td>
						<td align=center>ºC</td>
						<td align=center>Jam</td>
						<td align=center>Hour Meter (HM)</td>
						<td align=center>Amper(A)</td>
						<td align=center>ºC</td>
						<td align=center>Jam</td>
						<td align=center>Hour Meter (HM)</td>
						<td align=center>Amper(A)</td>
						<td align=center>ºC</td>
					  </tr>
				</thead>
               <tbody>";

$no=0;
while($bar=mysql_fetch_assoc($res))
{

	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
			$no+=1;		
			$stream.="
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=right>".$bar['deg1hour']."</td>
				<td align=right>".$bar['deg1amp']."</td>
				<td align=right>".$bar['deg1c']."</td>
				
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=right>".$bar['deg2hour']."</td>
				<td align=right>".$bar['deg2amp']."</td>
				<td align=right>".$bar['deg2c']."</td>
				
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=right>".$bar['deg3hour']."</td>
				<td align=right>".$bar['deg3amp']."</td>
				<td align=right>".$bar['deg3c']."</td>
				
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=right>".$bar['deg4hour']."</td>
				<td align=right>".$bar['deg4amp']."</td>
				<td align=right>".$bar['deg4c']."</td>
				
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=right>".$bar['deg5hour']."</td>
				<td align=right>".$bar['deg5amp']."</td>
				<td align=right>".$bar['deg5c']."</td>
				
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=right>".$bar['deg6hour']."</td>
				<td align=right>".$bar['deg6amp']."</td>
				<td align=right>".$bar['deg6c']."</td>				
		</tr>";
	
	$dh1+=$bar['deg1hour'];$rdh1=$dh1/$no;
	$da1+=$bar['deg1amp'];$rda1=$da1/$no;
	$dc1+=$bar['deg1c'];$rdc1=$dc1/$no;
	
	$dh2+=$bar['deg2hour'];$rdh2=$dh2/$no;
	$da2+=$bar['deg2amp'];$rda2=$da2/$no;
	$dc2+=$bar['deg2c'];$rdc2=$dc2/$no;
	
	$dh3+=$bar['deg3hour'];$rdh3=$dh3/$no;
	$da3+=$bar['deg3amp'];$rda3=$da3/$no;
	$dc3+=$bar['deg3c'];$rdc3=$dc3/$no;
	
	$dh4+=$bar['deg4hour'];$rdh4=$dh4/$no;
	$da4+=$bar['deg4amp'];$rda4=$da4/$no;
	$dc4+=$bar['deg4c'];$rdc4=$dc4/$no;
	
	$dh5+=$bar['deg5hour'];$rdh5=$dh5/$no;
	$da5+=$bar['deg5amp'];$rda5=$da5/$no;
	$dc5+=$bar['deg5c'];$rdc5=$dc5/$no;
	
	$dh6+=$bar['deg6hour'];$rdh6=$dh6/$no;
	$da6+=$bar['deg6amp'];$rda6=$da6/$no;
	$dc6+=$bar['deg6c'];$rdc6=$dc6/$no;
	//rata2 presan
	
	

	
					
	}
	$stream.="</tbody>";
		$stream.="
	<thead><tr>
		<td>Rata - Rata</td>
		<td align=right>".number_format($rdh1,2)."</td>
		<td align=right>".number_format($rda1,2)."</td>
		<td align=right>".number_format($rdc1,2)."</td>
		<td align=center>-------</td>
		<td align=right>".number_format($rdh2,2)."</td>
		<td align=right>".number_format($rda2,2)."</td>
		<td align=right>".number_format($rdc2,2)."</td>
		<td align=center>-------</td>
		<td align=right>".number_format($rdh3,2)."</td>
		<td align=right>".number_format($rda3,2)."</td>
		<td align=right>".number_format($rdc3,2)."</td>
		<td align=center>-------</td>
		
		<td align=right>".number_format($rdh4,2)."</td>
		<td align=right>".number_format($rda4,2)."</td>
		<td align=right>".number_format($rdc4,2)."</td>
		<td align=center>-------</td>
		<td align=right>".number_format($rdh5,2)."</td>
		<td align=right>".number_format($rda5,2)."</td>
		<td align=right>".number_format($rdc5,2)."</td>
		<td align=center>-------</td>
		<td align=right>".number_format($rdh6,2)."</td>
		<td align=right>".number_format($rda6,2)."</td>
		<td align=right>".number_format($rdc6,2)."</td>

		
		
	
	</tr>
	
	
	</table><br />";
	
	

	
	
	
	
#############untuk tabel bawahnya	
				  if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
					else $stream.="<table cellspacing='1' border='0' class='sortable'>";
					$stream.="<thead class=rowheader>
					  <tr>
						<td align=center>No</td>
						<td align=center colspan=3>Nama Mesin</td>
						<td align=center>Hari Ini</td>
						<td align=center>S/D Hari Ini</td>
						<td align=center>Sejak Baru</td>
						<td align=center>Sejak Rebuilt</td>
					  </tr>
					</thead>
				   <tbody>";
				   
	$str1=" SELECT distinct(mesin),hariini,sdhi,sejakbaru,sejakrebuild FROM ".$dbname.".pabrik_ops_presandta a
				JOIN ".$dbname.".pabrik_ops_pressandtb b
				ON a.notransaksi=b.notransaksi
				WHERE  substr(waktu,1,10)='".$tgl."' and b.notransaksi like '%".$kdorg."%' ";
			//echo $str1;
	$res1=mysql_query($str1);	
	$no=0;			   
	while($bar1=mysql_fetch_assoc($res1))
	{
	
							if($proses=='excel')
							$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
						else $stream.="<tr class=rowcontent>";
				$no+=1;		
				$stream.="
					<td>".$no."</td>
					<td align=left  colspan=3>".$optnm[$bar1['mesin']]."</td>
					<td align=right>".$bar1['hariini']."</td>
					<td align=right>".$bar1['sdhi']."</td>
					<td align=right>".$bar1['sejakbaru']."</td>
					<td align=right>".$bar1['sejakrebuild']."</td>
			</tr>";					
		}
		$stream.="</tbody></table><br />";		
		
		
		
	

#####untuk di penyetuju	
	
	$in=" SELECT * FROM ".$dbname.".pabrik_ops_pressanht
			WHERE notransaksi like '%".$kdorg."%' and kodeorg='".$kdorg."' and tanggal='".$tgl."' ";		
			//echo $in	;
	$dr=mysql_query($in);
	$ra=mysql_fetch_assoc($dr);
	
	$nmperiksa=$ra['diperiksa'];
	$nmbuat=$ra['dibuat'];
	
	$ap=" SELECT namakaryawan,kodejabatan FROM ".$dbname.".datakaryawan
			WHERE karyawanid='".$nmperiksa."' ";		
			//echo $a	;
	$bp=mysql_query($ap);
	$cp=mysql_fetch_assoc($bp);
	
	$ab=" SELECT namakaryawan,kodejabatan FROM ".$dbname.".datakaryawan
			WHERE karyawanid='".$nmbuat."' ";		
			//echo $a	;
	$bb=mysql_query($ab);
	$cb=mysql_fetch_assoc($bb);
	
	$kdjp=$cp['kodejabatan'];
	$kdjb=$cb['kodejabatan'];
	
	$nmp=$cp['namakaryawan'];
	$nmb=$cb['namakaryawan'];

	/*echo $nmp.$nmb;
	echo $kdjp.$kdjb;*/
	
	
	
	
	if(($proses=='excel')or($proses=='pdf'))
	{
	$stream.="
		<table>
			<tr>
				<td colspan=11 align=center>Diperiksa</td>
				<td colspan=11 align=center>Dibuat</td>
			</tr><br />
			<tr>
				<td colspan=11 align=center>".$optjabatan[$kdjp]."</td>
				<td colspan=11 align=center>".$optjabatan[$kdjb]."</td>
			</tr>
			<tr></tr>
			<tr></tr>
			<tr></tr>
			<tr>
				<td colspan=11 align=center>_________________________________</td>
				<td colspan=11 align=center>_________________________________</td>
			</tr>
			<tr>
				<td colspan=11 align=center>".$nmp."</td>
				<td colspan=11 align=center>".$nmb."</td>
			</tr>
			</table>";
	}


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
		$nop_="Laporan_Pressan".$tglSkrg;
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