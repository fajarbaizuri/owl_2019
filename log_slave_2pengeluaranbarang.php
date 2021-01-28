<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$bulan=$_POST['bulan'];
//echo $bulan.$kdorg;
if(($proses=='excel')or($proses=='pdf')){
	
	$kdorg=$_GET['kdorg'];
	//echo $kdorg;
	$bulan=$_GET['bulan'];
	
}

$optnama=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optsatuan=makeOption($dbname,'log_5masterbarang','kodebarang,satuan');


if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($kdorg==''))
	{
		echo"Error: Organisasi tidak boleh kosong"; 
		exit;
    }

    else if(($bulan==''))
	{
        echo"Error: Periode tidak boleh kosong"; 
		exit;
    }
	
}

			
			$stream="Pengeluaran Barang dan Transaksi<br />Periode : ".$bulan." ";
			
              if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
					  <tr>
						<td align=center rowspan=2>No</td>
						<td align=center rowspan=2>Kode barang</td>
						<td align=center rowspan=2>Nama barang</td>
						<td align=center rowspan=2>Satuan</td>
						<td align=center colspan=4>Pengeluaran</td>
					  </tr>
					  <tr>
					  	<td align=center>Gudang</td>
						<td align=center>Kebun</td>
						<td align=center>Traksi</td>
						<td align=center>Pabrik</td>
					  </tr>
						
				</thead>
               <tbody>";
			
/*#<tr>
						<td align=center rowspan=3>No</td>
						<td align=center rowspan=3>Kode barang</td>
						<td align=center rowspan=3>Nama barang</td>
						<td align=center colspan=5>Pengeluaran</td>
					  </tr>
					  <tr>
					  	<td align=center rowspan=2>Gudang</td>
						<td align=center rowspan=2>Kebun</td>
						<td align=center rowspan=2>Traksi</td>
						<td align=center colspan=2>Pabrik</td>
					  </tr>
						<tr>
						<td align=center>Pengolahan</td>
						<td align=center>Perawatan</td>
					  </tr>*/
			   
			   

			   
##ambil notransaksi yang tipe keluar dahulu dari gudang
$xgudang="select sum(jumlah) as jumlah,kodebarang from ".$dbname.". log_transaksi_vw 
			where tipetransaksi='5' and kodegudang like '%".$kdorg."%' and 			
			tanggal like '%".$bulan."%' group by kodebarang";
$ygudang=mysql_query($xgudang);
			
//echo $xgudang;			
while($zgudang=mysql_fetch_assoc($ygudang))
{
	$no+=1;
	
	#kebun
	$akebun="select sum(kwantitas) as kwantitas from ".$dbname.".kebun_pakaimaterial where kodebarang='".$zgudang['kodebarang']."' and kodeorg like '%".$kdorg."%' group by kodebarang,kodeorg ";
	//echo $akebun;
	$bkebun=mysql_query($akebun);
	$ckebun=mysql_fetch_assoc($bkebun);
	
	#traksi
	$xtraksi="select sum(b.jumlah) as jumlah from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi = b.notransaksi where a.tanggal like '%".$bulan."%' and b.kodebarang='".$zgudang['kodebarang']."' and a.kodeorg like '%".$kdorg."%' group by b.kodebarang,a.kodeorg";
	//echo $xtraksi;
	$ytraksi=mysql_query($xtraksi);
	$ztraksi=mysql_fetch_assoc($ytraksi);
	
	#pabrik
	$xpabrik="select sum(b.jumlah) as jumlah from ".$dbname.".pabrik_rawatmesinht a left join ".$dbname.".pabrik_rawatmesindt b on a.notransaksi = b.notransaksi where a.tanggal like '%".$bulan."%' and b.kodebarang='".$zgudang['kodebarang']."' and a.pabrik like '%".$kdorg."%' group by b.kodebarang,a.pabrik";
	//echo $xpabrik;
	$ypabrik=mysql_query($xpabrik);
	$zpabrik=mysql_fetch_assoc($ypabrik);
	
	
	
/*pabrik_pengolahan_barang
pabrik_rawatmesindt*/



	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
					
			$stream.="
				<td align=right>".$no."</td>
				<td align=right>".$zgudang['kodebarang']."</td>
				<td align=left>".$optnama[$zgudang['kodebarang']]."</td>
				<td align=left>".$optsatuan[$zgudang['kodebarang']]."</td>
				
				<td align=right>".$zgudang['jumlah']."</td>		
				<td align=right>".$ckebun['kwantitas']."</td>			
				<td align=right>".$ztraksi['jumlah']."</td>		
				<td align=right>".$zpabrik['jumlah']."</td>	
			</tr>";		
	}
	$stream.="</table>";
	if($proses=='excel')	
	$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];						
	
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
		
		$tglSkrg=date("Ymd");
		$nop_="Pengeluaran Barang dan Transaksi".$tglSkrg;
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
							global $bulan;
							

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
                                            $this->Cell($width,$height,"Pengeluaran Barang dan Transaksi ".$kdorg,'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ".$bulan,'',0,'C');
											//$this->Ln();
                                            $this->Ln(30);
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(3/100*$width,30,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(15/100*$width,30,'Kode Barang',1,0,'C',1);
											$this->Cell(30/100*$width,30,'Nama Barang',1,0,'C',1);
											$this->Cell(10/100*$width,30,'Satuan',1,0,'C',1);
											$this->Cell(40/100*$width,15,'Pengeluaran',1,1,'C',1);
											$this->Cell(58/100*$width,15,'',0,0,'C',0);
											//$this->Cell(10/100*$width,30,'Nama Barang',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Gudang',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Kebun',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Traksi',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Pabrik',1,1,'C',1);	
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
		
			
//echo $xgudang;			
		$ygudang=mysql_query($xgudang);;//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		$ttl=0;
		while($zgudang=mysql_fetch_assoc($ygudang))
		{	

			$no+=1;
			
			#kebun
			$akebun="select sum(kwantitas) as kwantitas from ".$dbname.".kebun_pakaimaterial where kodebarang='".$zgudang['kodebarang']."' and kodeorg like '%".$kdorg."%' group by kodebarang,kodeorg ";
			//echo $akebun;
			$bkebun=mysql_query($akebun);
			$ckebun=mysql_fetch_assoc($bkebun);
			
			#traksi
			$xtraksi="select sum(b.jumlah) as jumlah from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi = b.notransaksi where a.tanggal like '%".$bulan."%' and b.kodebarang='".$zgudang['kodebarang']."' and a.kodeorg like '%".$kdorg."%' group by b.kodebarang,a.kodeorg";
			//echo $xtraksi;
			$ytraksi=mysql_query($xtraksi);
			$ztraksi=mysql_fetch_assoc($ytraksi);
			
			#pabrik
			$xpabrik="select sum(b.jumlah) as jumlah from ".$dbname.".pabrik_rawatmesinht a left join ".$dbname.".pabrik_rawatmesindt b on a.notransaksi = b.notransaksi where a.tanggal like '%".$bulan."%' and b.kodebarang='".$zgudang['kodebarang']."' and a.pabrik like '%".$kdorg."%' group by b.kodebarang,a.pabrik";
			//echo $xpabrik;
			$ypabrik=mysql_query($xpabrik);
			$zpabrik=mysql_fetch_assoc($ypabrik);	
			
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$zgudang['kodebarang'],1,0,'R',1);		
			$pdf->Cell(30/100*$width,$height,$optnama[$zgudang['kodebarang']],1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,$optsatuan[$zgudang['kodebarang']],1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,$zgudang['jumlah'],1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,$ckebun['kwantitas'],1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,$ztraksi['jumlah'],1,0,'R',1);	
			$pdf->Cell(10/100*$width,$height,$zpabrik['jumlah'],1,1,'R',1);	
	/*					<td align=right>".$no."</td>
				<td align=right>".$zgudang['kodebarang']."</td>
				<td align=left>".$optnama[$zgudang['kodebarang']]."</td>
				<td align=right>".$zgudang['jumlah']."</td>		
				<td align=right>".$ckebun['kwantitas']."</td>			
				<td align=right>".$ztraksi['jumlah']."</td>		
				<td align=right>".$zpabrik['jumlah']."</td>	
		*/
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