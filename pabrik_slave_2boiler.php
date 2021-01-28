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



			$stream="<table>
				<tr>
					<td colspan=27 align=center style=font-size:18px><b><u>LAPORAN HARIAN JAM OPRASIONAL BOILER</b></u></td>
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
								<td rowspan=3 align=center>Jam</td>
								<td rowspan=3 align=center>Start / Stop</td>
								<td rowspan=3 align=center>s/d Hari Ini</td>
								<td colspan=2 align=center>TEKANAN</td>
								<td align=center>FLOW METER</td>
								<td align=center>SUPER HEATER</td>
								<td colspan=2 align=center>TEKANAN</td>
								<td rowspan=3 align=center>Presure In Combation</td>
								<td rowspan=3 align=center>Tempat Flue Glass</td>
								<td rowspan=3 align=center>Preasure Flue Glass</td>
								<td rowspan=3 align=center>Preasure Flection On Les</td>
								<td colspan=5 align=center>TEKANAN</td>
								<td rowspan=3 align=center>VOLT</td>
								<td rowspan=3 align=center>Level Air</td>
								<td rowspan=3 align=center>Shoot Blowning</td>
								<td rowspan=3 align=center>Blow Down</td>
							  </tr>
							  <tr>
								<td rowspan=2 align=center>Bar 1</td>
								<td rowspan=2 align=center>ºC</td>
								<td rowspan=2 align=center>M3</td>
								<td rowspan=2 align=center>BAR</td>
								<td align=center>Drum Atas</td>
								<td align=center>Drum Atas</td>
								<td align=center>IDF</td>
								<td align=center>PAF</td>
								<td align=center>SAF</td>
								<td align=center>EDP</td>
								<td align=center>CAF</td>
							  </tr>
							  <tr>
								<td align=center>Bar</td>
								<td align=center>Bar</td>
								<td align=center>A</td>
								<td align=center>A</td>
								<td align=center>A</td>
								<td align=center>A</td>
								<td align=center>A</td>
							 </tr>
				</thead>
               <tbody>";
$str=" 	SELECT * FROM ".$dbname.".pabrik_ops_boilerdt
			WHERE notransaksi like '%".$kdorg."%' and substr(waktu,1,10)='".$tgl."' ";
			
			//tanggalnormal
//echo $str;
$res=mysql_query($str);	
$no=0;
while($bar=mysql_fetch_assoc($res))
{

	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
			$no+=1;		
			$stream.="
				<td align=right>".substr($bar['waktu'],10,6)."</td>
				<td align=left>".$bar['start']."</td>
				
				<td align=right>".$bar['sdhi']."</td>
				<td align=right>".$bar['tekananbar1']."</td>
				<td align=right>".$bar['tekananc']."</td>
				<td align=right>".$bar['flowmeterm3']."</td>
				<td align=right>".$bar['superheaterbar']."</td>
				<td align=right>".$bar['tekanandrum1']."</td>
				<td align=right>".$bar['tekanandrum2']."</td>
				
				<td align=right>".$bar['presssurein']."</td>
				<td align=right>".$bar['tempatflueglass']."</td>
				<td align=right>".$bar['pressflueglass']."</td>
				<td align=right>".$bar['pressureonless']."</td>
				
				<td align=right>".$bar['tekananidf']."</td>
				<td align=right>".$bar['tekananpaf']."</td>
				<td align=right>".$bar['tekanansaf']."</td>
				<td align=right>".$bar['tekananedf']."</td>
				<td align=right>".$bar['tekanancaf']."</td>
				
				<td align=right>".$bar['volt']."</td>
				<td align=right>".$bar['levelair']."</td>
				<td align=right>".$bar['shootblwing']."</td>
				<td align=right>".$bar['blowdown']."</td>
				
		</tr>";
		
		$t1+=$bar['sdhi'];$rt1=$t1/$no;
		$t2+=$bar['tekananbar1'];$rt2=$t2/$no;
		$t3+=$bar['tekananc'];$rt3=$t3/$no;
		$t4+=$bar['flowmeterm3'];$rt4=$t4/$no;
		$t5+=$bar['superheaterbar'];$rt5=$t5/$no;
		$t6+=$bar['tekanandrum1'];$rt6=$t6/$no;
		$t7+=$bar['tekanandrum2'];$rt7=$t7/$no;
		
		$t8+=$bar['presssurein'];$rt8=$t8/$no;
		$t9+=$bar['tempatflueglass'];$rt9=$t9/$no;
		$t10+=$bar['pressflueglass'];$rt10=$t10/$no;
		$t11+=$bar['pressureonless'];$rt11=$t11/$no;
		
		$t12+=$bar['tekananidf'];$rt12=$t12/$no;
		$t13+=$bar['tekananpaf'];$rt13=$t13/$no;
		$t14+=$bar['tekanansaf'];$rt14=$t14/$no;
		$t15+=$bar['tekananedf'];$rt15=$t15/$no;
		$t16+=$bar['tekanancaf'];$rt16=$t16/$no;
		
		$t17+=$bar['volt'];$rt17=$t17/$no;
		$t18+=$bar['levelair'];$rt18=$t18/$no;
		$t19+=$bar['shootblwing'];$rt19=$t19/$no;
		$t20+=$bar['blowdown'];$rt20=$t20/$no;
		
		
				
	}
	$stream.="</tbody>";
		$stream.="
	<thead><tr>
		<td colspan=2 align=center>Rata - Rata</td>
		<td align=right>".number_format($rt1,2)."</td>
		<td align=right>".number_format($rt2,2)."</td>
		<td align=right>".number_format($rt3,2)."</td>
		<td align=right>".number_format($rt4,2)."</td>
		
		<td align=right>".number_format($rt5,2)."</td>
		<td align=right>".number_format($rt6,2)."</td>
		<td align=right>".number_format($rt7,2)."</td>
		<td align=right>".number_format($rt8,2)."</td>
		<td align=right>".number_format($rt9,2)."</td>
		<td align=right>".number_format($rt10,2)."</td>
		<td align=right>".number_format($rt11,2)."</td>
		
		<td align=right>".number_format($rt12,2)."</td>
		<td align=right>".number_format($rt13,2)."</td>
		<td align=right>".number_format($rt14,2)."</td>
		<td align=right>".number_format($rt15,2)."</td>
		<td align=right>".number_format($rt16,2)."</td>
		<td align=right>".number_format($rt17,2)."</td>
		<td align=right>".number_format($rt18,2)."</td>
		
		<td align=right>".number_format($rt19,2)."</td>
		<td align=right>".number_format($rt20,2)."</td>
		


	
	</tr>
	
	
	</table><br />";



	$in=" SELECT * FROM ".$dbname.".pabrik_ops_boilerht WHERE notransaksi like '%".$kdorg."%' and kodeorg='".$kdorg."' and tanggal='".$tgl."' ";		
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
		$nop_="Laporan_Boiler".$tglSkrg;
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