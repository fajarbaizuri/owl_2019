<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$nokon=$_POST['nokon'];
$eks=$_POST['eks'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];

if(($proses=='excel')or($proses=='pdf'))
{
	$nokon=$_GET['nokon'];
	$eks=$_GET['eks'];	
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
}

$optnm=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{
	/*if($nokon=='')
	{
		echo"Error: Nomor kontrak tidak boleh kosong"; 
		exit;
    }*/
	
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
//$str=" SELECT * FROM ".$dbname.".vhc_penggantianht where kodevhc='".$kdorgs."'";
//$str="select a.kodecustomer,a.beratbersih as netto,a.substr(tanggal,1,10) as tanggal from ".$dbname.".pabrik_timbangan join where tanggal between '".$tgl1."' and '".$tgl2."'";


              if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                 <tr>
				 	 <td align=center rowspan=2>No</td>
					 <td align=center rowspan=2>No. Kontrak</td>
					 <td align=center rowspan=2>Ekspeditor</td>
					 <td align=center rowspan=2>Tanggal</td>
					 <td align=center rowspan=2>VHC</td>
					 <td align=center colspan=4>Internal</td>
					 <td align=center rowspan=2>Netto VHC</td>
					 <td align=center colspan=4>Eksternal</td>
				 </tr>
				 <tr>	 
					<td align=center>Netto</td>
					<td align=center>Air</td>
					<td align=center>Kotoran</td>
					<td align=center>ALB</td>
					<td align=center>Netto</td>
					<td align=center>Air</td>
					<td align=center>Kotoran</td>
					<td align=center>ALB</td>
					 
  				</tr></thead>
                <tbody>";
if($eks=='' && $nokon=='')
{
			$str="select * from ".$dbname.".pmn_pengakuan where tanggal between '".$tgl1."' and '".$tgl2."' ";	
			//echo $str;
}
else if($eks=='')
{
	$str="select * from ".$dbname.".pmn_pengakuan where nokontrak='".$nokon."' and tanggal between '".$tgl1."' and '".$tgl2."' ";	
}
else if($nokon=='')
{
	$str="select * from ".$dbname.".pmn_pengakuan where ekspeditor='".$eks."' and tanggal between '".$tgl1."' and '".$tgl2."' ";
}
else{
			$str="select * from ".$dbname.".pmn_pengakuan where nokontrak='".$nokon."' and ekspeditor='".$eks."' and tanggal between '".$tgl1."' and '".$tgl2."'  ";	
			
			//echo $str;
}

$res=mysql_query($str);	
				
$no=0;


while($bar=mysql_fetch_assoc($res))
{
	


	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
		$no+=1;
		
		
			$stream.="<td>".$no."</td>";
			$stream.="<td align=left>".$bar['nokontrak']."</td>";
			$stream.="<td align=left>".$optnm[$bar['ekspeditor']]."</td>";
			$stream.="<td align=right>".tanggalnormal($bar['tanggal'])."</td>";
			$stream.="<td align=right>".$bar['vhc']."</td>";
			$stream.="<td align=right>".$bar['nettoint']."</td>";
			$stream.="<td align=right>".$bar['intair']."</td>";
			$stream.="<td align=right>".$bar['intkotoran']."</td>";
			$stream.="<td align=right>".$bar['intalb']."</td>";
			$stream.="<td align=right>".$bar['nettovhc']."</td>";
			$stream.="<td align=right>".$bar['nettoext']."</td>";
			$stream.="<td align=right>".$bar['extair']."</td>";
			$stream.="<td align=right>".$bar['extkotoran']."</td>";
			$stream.="<td align=right>".$bar['extalb']."</td>";
					
	}
	$stream.="</tr></tbody></table>";
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
                                            $this->Cell($width,$height,"Laporan Harga TBS ".$kdorg,'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ". tanggalnormal($tgl1)." S/D ". tanggalnormal($tgl2),'',0,'C');
											//$this->Ln();
                                            $this->Ln(30);
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(25,30,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(100,30,'No. Kontrak',1,0,'C',1);
											$this->Cell(125,30,'Ekspeditor',1,0,'C',1);
											$this->Cell(50,30,'Tanggal',1,0,'C',1);
											$this->Cell(75,30,'VHC',1,0,'C',1);
											$this->Cell(180,15,'Internal',1,0,'C',1);
											
											/*$this->Cell(8,15,'Netto Internal',1,0,'C',1);
											$this->Cell(7,15,'Internal Air',1,0,'C',1);
											$this->Cell(8,15,'Internal Kotoran',1,0,'C',1);
											$this->Cell(8,15,'Internal ALB',1,0,'C',1);*/
											$this->Cell(50,30,'Netto VHC',1,0,'C',1);
											$this->Cell(180,15,'Eksternal',1,1,'C',1);
											/*$this->Cell(8,15,'Netto Eksternal',1,0,'C',1);
											$this->Cell(8,15,'Eksternal Air',1,0,'C',1);
											$this->Cell(8,15,'Eksternal Kotoran',1,0,'C',1);
											$this->Cell(8,15,'Eksternal ALB',1,1,'C',1);*/
											//$this->SetFillColor(225,225,225);
											//$this->Cell(500,15,'',0,0,'C',1);
											$this->SetX(403.5);
											$this->Cell(45,15,'Netto',1,0,'C',1);
											$this->Cell(45,15,'Air',1,0,'C',1);
											$this->Cell(50,15,'Kotoran',1,0,'C',1);
											$this->Cell(40,15,'ALB',1,0,'C',1);
											$this->SetX(633.5);
											$this->Cell(45,15,'Netto',1,0,'C',1);
											$this->Cell(45,15,'Air',1,0,'C',1);
											$this->Cell(50,15,'Kotoran',1,0,'C',1);
											$this->Cell(40,15,'ALB',1,1,'C',1);
											
											
											
											
											/*<td align=center>No</td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>
					 <td align=center></td>*/	
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
		//$pdf->Ln(40);
		while($bar=mysql_fetch_assoc($res))
		{	
			
			
		
			$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$bar['ekspeditor']."'";
	//echo $sNm;
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namasupplier'];
				
			$total=$bar['netto']*$c['harga'];
				//echo $sNm;			
			$no+=1;	
			$pdf->Cell(25,$height,$no,1,0,'C',1);	
			$pdf->Cell(100,$height,$bar['nokontrak'],1,0,'L',1);		
			$pdf->Cell(125,$height,$nm,1,0,'L',1);	
			$pdf->Cell(50,$height,tanggalnormal($bar['tanggal']),1,0,'L',1);		
				
			$pdf->Cell(75,$height,$bar['vhc'],1,0,'L',1);
			
			$pdf->Cell(45,$height,number_format($bar['nettoint']),1,0,'R',1);
			$pdf->Cell(45,$height,number_format($bar['intair']),1,0,'R',1);
			$pdf->Cell(50,$height,number_format($bar['intkotoran']),1,0,'R',1);
			$pdf->Cell(40,$height,number_format($bar['intalb']),1,0,'R',1);
			
			$pdf->Cell(50,$height,number_format($bar['nettovhc']),1,0,'R',1);
			
			$pdf->Cell(45,$height,number_format($bar['nettoext']),1,0,'R',1);
			$pdf->Cell(45,$height,number_format($bar['extair']),1,0,'R',1);
			$pdf->Cell(50,$height,number_format($bar['extkotoran']),1,0,'R',1);
			$pdf->Cell(40,$height,number_format($bar['extalb']),1,1,'R',1);
			
		
		
		
		/*
		$stream.="<td>".$no."</td>";
			$stream.="<td align=left>".$bar['nokontrak']."</td>";
			$stream.="<td align=left>".$optnm[$bar['ekspeditor']]."</td>";
			$stream.="<td align=right>".tanggalnormal($bar['tanggal'])."</td>";
			
			$stream.="<td align=right>".$bar['vhc']."</td>";
			
			$stream.="<td align=right>".$bar['nettoint']."</td>";
			$stream.="<td align=right>".$bar['intair']."</td>";
			$stream.="<td align=right>".$bar['intkotoran']."</td>";
			$stream.="<td align=right>".$bar['intalb']."</td>";
			
			$stream.="<td align=right>".$bar['nettovhc']."</td>";
			
			$stream.="<td align=right>".$bar['nettoext']."</td>";
			$stream.="<td align=right>".$bar['extair']."</td>";
			$stream.="<td align=right>".$bar['extkotoran']."</td>";
			$stream.="<td align=right>".$bar['extalb']."</td>";*/
		
		
		
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