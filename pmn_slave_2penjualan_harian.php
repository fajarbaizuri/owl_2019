<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php'); 

$proses=$_GET['proses'];
$_POST['kodeorg1']==''?$kodeorg1=$_GET['kodeorg1']:$kodeorg1=$_POST['kodeorg1'];
$_POST['kodebarang1']==''?$kodebarang1=$_GET['kodebarang1']:$kodebarang1=$_POST['kodebarang1'];
$_POST['tgl1_1']==''?$tgl1_1=$_GET['tgl1_1']:$tgl1_1=$_POST['tgl1_1'];
$_POST['tgl2_1']==''?$tgl2_1=$_GET['tgl2_1']:$tgl2_1=$_POST['tgl2_1'];
$tgl=explode('-',$tgl1_1);
$tgl1_1=$tgl[2].'-'.$tgl[1].'-'.$tgl[0];
$tgl=explode('-',$tgl2_1);
$tgl2_1=$tgl[2].'-'.$tgl[1].'-'.$tgl[0];

if($kodeorg1==''){
    echo"error: Please choose Mill.";
    exit;
}
if($kodebarang1==''){
    echo"error: Please choose Commodity.";
    exit;
}
if($tgl1_1=='--'||$tgl2_1=='--'){
    echo"error: Please choose Dates.";
    exit;
}

$str="select nokontrak, koderekanan from ".$dbname.".pmn_kontrakjual";   
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $kontrak[$bar->nokontrak]=$bar->koderekanan;
}
$str="select kodecustomer,namacustomer from ".$dbname.".pmn_4customer";   
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $kustom[$bar->kodecustomer]=$bar->namacustomer;
}
$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang in ('40000001', '40000002')";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
	$barang[$rBrg['kodebarang']]=$rBrg['namabarang'];
}

$stream='';
$border=0;
if($proses=='excel'){
    $border=1;
    $stream.=$_SESSION['lang']['pabrik']." : ".$kodeorg1."<br>";
    $stream.=$_SESSION['lang']['komoditi']." : ".$barang[$kodebarang1]."<br>";
    $stream.=$_SESSION['lang']['tanggal']." : ".$tgl1_1." - ".$tgl2_1."<br>";
}
$stream.=" <table class=sortable cellspacing=1 border=".$border.">
    <thead>
        <tr class=rowheader>
            <td>No.</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['noTiket']."</td>
            <td>".$_SESSION['lang']['NoKontrak']."</td>
            <td>".$_SESSION['lang']['Pembeli']."</td>
            <td>".$_SESSION['lang']['jumlah']." (kg)</td>
            <td>".$_SESSION['lang']['nopol']."</td>
            <td>".$_SESSION['lang']['supir']."</td>
	</tr>
    </thead><tbody>";
$no=1;
$total=0;
$sql="select tanggal,notransaksi,nokontrak,beratbersih,nokendaraan,supir from ".$dbname.".pabrik_timbangan where millcode = '".$kodeorg1."' and kodebarang = '".$kodebarang1."' and tanggal between '".$tgl1_1." 00:00:00' and '".$tgl2_1." 23:59:59' order by tanggal asc";
$query=mysql_query($sql) or die(mysql_error());
$row=mysql_num_rows($query);
if($row>0){
    while($res=mysql_fetch_assoc($query)){
        $stream.="<tr class=rowcontent>";
            $stream.="<td align=right>".$no."</td>";
            if($proses=='preview')$stream.="<td>".tanggalnormal(substr($res['tanggal'],0,10))."</td>";
            if($proses=='excel')$stream.="<td>".substr($res['tanggal'],0,10)."</td>";
            $stream.="<td>".$res['notransaksi']."</td>";
            $stream.="<td>".$res['nokontrak']."</td>";
            $stream.="<td>".$kustom[$kontrak[$res['nokontrak']]]."</td>";
            $stream.="<td align=right>".number_format($res['beratbersih'],0)."</td>";
            $stream.="<td>".$res['nokendaraan']."</td>";
            $stream.="<td>".$res['supir']."</td>";
        $stream.="</tr>";
        $no+=1;
        $total+=$res['beratbersih'];
    }
    $stream.="<tr class=rowcontent>";
        $stream.="<td align=center colspan=5>Total</td>";
        $stream.="<td align=right>".number_format($total,0)."</td>";
        $stream.="<td align=center colspan=2></td>";
    $stream.="</tr>";
    $no+=1;
}
else
{
    $stream.="<tr class=rowcontent align=center><td colspan=8>Not Found</td></tr>";
}
$stream.="</tbody></table>";

switch($proses)
{
    case'preview':
        echo $stream;
    break;
//	case'pdf':
//	$periode=$_GET['periode'];
//	$kdBrg=$_GET['kdBrg'];
//	$idPabrik=$_GET['idPabrik'];
//	 class PDF extends FPDF
//        {
//            function Header() {
//                global $conn;
//                global $dbname;
//                global $align;
//                global $length;
//                global $colArr;
//                global $title;
//				global $periode;
//				global $kdBrg;
//				global $idPabrik;
//			
//				$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
//				$qBrg=mysql_query($sBrg) or die(mysql_error());
//				$rBrg=mysql_fetch_assoc($qBrg);
//				
//				$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idPabrik."'";
//				$qOrg=mysql_query($sOrg) or die(mysql_error());
//				$rOrg=mysql_fetch_assoc($qOrg);
//				 
//                # Alamat & No Telp
//                $query = selectQuery($dbname,'organisasi','alamat,telepon',
//                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
//                $orgData = fetchData($query);
//                
//                $width = $this->w - $this->lMargin - $this->rMargin;
//                $height = 15;
//                $path='images/logo.jpg';
//                $this->Image($path,$this->lMargin,$this->tMargin,70);	
//                $this->SetFont('Arial','B',9);
//                $this->SetFillColor(255,255,255);	
//                $this->SetX(100);   
//                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
//                $this->SetX(100); 		
//                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
//                $this->SetX(100); 			
//                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
//                $this->Line($this->lMargin,$this->tMargin+($height*4),
//                    $this->lMargin+$width,$this->tMargin+($height*4));
//                $this->Ln();
//                
//                $this->SetFont('Arial','B',12);
//				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanPenjualan'],'',0,'L');
//				$this->Ln();
//				$this->SetFont('Arial','',8);
//				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
//				$this->Cell(5,$height,':','',0,'L');
//				$this->Cell(45/100*$width,$height,$periode,'',0,'L');
//				$this->Ln();
//				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['nm_perusahaan'],'',0,'L');
//				$this->Cell(5,$height,':','',0,'L');
//				$this->Cell(45/100*$width,$height,$rOrg['namaorganisasi'],'',0,'L');
//				$this->Ln();
//				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['komoditi'],'',0,'L');
//				$this->Cell(5,$height,':','',0,'L');
//				$this->Cell(45/100*$width,$height,$rBrg['namabarang'],'',0,'L');
//			
//              
//				
//                $this->Ln();
//				$this->Ln();
//                $this->SetFont('Arial','U',12);
//                $this->Cell($width,$height, $_SESSION['lang']['laporanPenjualan'],0,1,'C');	
//                $this->Ln();	
//				
//                $this->SetFont('Arial','B',9);	
//                $this->SetFillColor(220,220,220);
//
//				
//				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
//				$this->Cell(18/100*$width,$height,$_SESSION['lang']['NoKontrak'],1,0,'C',1);		
//				$this->Cell(20/100*$width,$height,$_SESSION['lang']['nmcust'],1,0,'C',1);		
//				$this->Cell(13/100*$width,$height,$_SESSION['lang']['tglKontrak'],1,0,'C',1);			
//				$this->Cell(12/100*$width,$height,$_SESSION['lang']['tgl_kirim'],1,0,'C',1);	
//				$this->Cell(8/100*$width,$height,$_SESSION['lang']['jmlhBrg'],1,0,'C',1);		
//				$this->Cell(10/100*$width,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
//				$this->Cell(7/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);		
//				$this->Cell(9/100*$width,$height,$_SESSION['lang']['kurs'],1,1,'C',1);					
//            
//            }
//                
//            function Footer()
//            {
//                $this->SetY(-15);
//                $this->SetFont('Arial','I',8);
//                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
//            }
//        }
//        $pdf=new PDF('P','pt','A4');
//        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
//        $height = 12;
//		$pdf->AddPage();
//		$pdf->SetFillColor(255,255,255);
//		$pdf->SetFont('Arial','',9);
//		$sDet="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,kuantitaskontrak,kodept,hargasatuan,matauang,satuan  from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%' and kodebarang='".$kdBrg."' and kodept='".$idPabrik."' order by tanggalkontrak asc ";
//		$qDet=mysql_query($sDet) or die(mysql_error());
//		$row=mysql_num_rows($qDet);
//		
//		if($row>0)
//		{
//			while($rDet=mysql_fetch_assoc($qDet))
//			{
//				$no+=1;
//				$arrKurs=array("IDR","USD");
//				
//				$sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rDet['koderekanan']."'";
//				$qCust=mysql_query($sCust) or die(mysql_error());
//				$rCust=mysql_fetch_assoc($qCust);
//						
//				
//				$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
//				$pdf->Cell(18/100*$width,$height,$rDet['nokontrak'],1,0,'L',1);	
//				$pdf->Cell(20/100*$width,$height,$rCust['namacustomer'],1,0,'L',1);		
//				$pdf->Cell(13/100*$width,$height,tanggalnormal($rDet['tanggalkontrak']),1,0,'C',1);				
//				$pdf->Cell(12/100*$width,$height,tanggalnormal($rDet['tanggalkirim']),1,0,'C',1);	
//				$pdf->Cell(8/100*$width,$height,number_format($rDet['kuantitaskontrak'],2),1,0,'R',1);		
//				$pdf->Cell(10/100*$width,$height,number_format($rDet['hargasatuan'],2),1,0,'R',1);	
//				$pdf->Cell(7/100*$width,$height,$rDet['satuan'],1,0,'C',1);		
//				$pdf->Cell(9/100*$width,$height,$arrKurs[$rDet['matauang']],1,1,'C',1);		
//				
//			}
//		}
//		else
//		{
//			$pdf->Cell($width,$height,"Not Found",1,1,'C',1);
//		}
//				
//        $pdf->Output();
//	break;
    case'excel':
//	$periode=$_GET['periode'];
//	$kdBrg=$_GET['kdBrg'];
//	$idPabrik=$_GET['idPabrik'];
//	$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
//	$qBrg=mysql_query($sBrg) or die(mysql_error());
//	$rBrg=mysql_fetch_assoc($qBrg);
//	
//	$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idPabrik."'";
//	$qOrg=mysql_query($sOrg) or die(mysql_error());
//	$rOrg=mysql_fetch_assoc($qOrg);
//			$stream.="
//			<table>
//			<tr><td colspan=9 align=center><b>".$_SESSION['lang']['laporanPenjualan']."</b></td></tr>
//			<tr><td colspan=3>".$_SESSION['lang']['periode']."</td><td>".$periode."</td></tr>
//			<tr><td colspan=3>".$_SESSION['lang']['komoditi']."</td><td>".$rBrg['namabarang']."</td></tr>
//			<tr><td colspan=3>".$_SESSION['lang']['nm_perusahaan']."</td><td>".$rOrg['namaorganisasi']."</td></tr>
//			<tr><td colspan=3></td><td></td></tr>
//			</table>
//			<table border=1>
//			<tr>
//				<td rowspan=2 bgcolor=#DEDEDE align=center>No.</td>
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['NoKontrak']."</td>
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nmcust']."</td>
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tglKontrak']."</td>
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tgl_kirim']."</td>	
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmlhBrg']."</td>	
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>	
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
//                                <td bgcolor=#DEDEDE align=center colspan=2>".$_SESSION['lang']['pemenuhan']."</td>
//				<td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kurs']."</td>				
//			</tr>
//                        <tr><td bgcolor=#DEDEDE align=center>Loco</td><td bgcolor=#DEDEDE align=center>Franco</td></tr>";
//			$strx="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,kuantitaskontrak,kodept,hargasatuan,matauang,satuan  
//                               from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%' and kodebarang='".$kdBrg."' and kodept='".$idPabrik."' 
//                               order by tanggalkontrak asc";
//			$resx=mysql_query($strx) or die(mysql_error());
//			$row=mysql_fetch_row($resx);
//			if($row<1)
//			{
//			$stream.="	<tr class=rowcontent>
//			<td colspan=9 align=center>Not Found</td></tr>
//			";
//			}
//			else
//			{
//			$no=0;
//			$resx=mysql_query($strx);
//				while($barx=mysql_fetch_assoc($resx))
//				{
//				$no+=1;
//				$arrKurs=array("IDR","USD");							
//				$sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$barx['koderekanan']."'";
//				$qCust=mysql_query($sCust) or die(mysql_error());
//				$rCust=mysql_fetch_assoc($qCust);
//                                
//				$sTimb="select sum(beratbersih) as beratbersih,sum(kgpembeli) as kgpembeli  from ".$dbname.".pabrik_timbangan where nokontrak='".$barx['nokontrak']."'";
//                                $qTimb=mysql_query($sTimb) or die(mysql_error($conn));
//                                $rTimb=mysql_fetch_assoc($qTimb);
//                                
//				$stream.="<tr class=rowcontent>
//				<td>".$no."</td>
//				<td>".$barx['nokontrak']."</td>
//				<td>".$rCust['namacustomer']."</td>
//				<td>".tanggalnormal($barx['tanggalkontrak'])."</td>
//				<td>".tanggalnormal($barx['tanggalkirim'])."</td>
//				<td align=right>".number_format($barx['kuantitaskontrak'],2)."</td>
//				<td align=right>".number_format($barx['hargasatuan'],2)."</td>
//				<td>".$barx['satuan']."</td>
//                                <td align=right>".number_format($rTimb['beratbersih'],2)."</td>
//                                <td align=right>".number_format($rTimb['kgpembeli'],2)."</td>
//				<td>".$arrKurs[$barx['matauang']]."</td>
//				</tr>";
//				}
//			}
//			
//			//echo "warning:".$strx;
//			//=================================================
//			$stream.="</table>";
        $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
        $nop_="Penjualan Harian ".$kodeorg1." ".$kodebarang1." ".$tgl1_1."-".$tgl2_1;
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
//	case'getDetail':
//	echo"<link rel=stylesheet type=text/css href=style/generic.css>";
//	$nokontrak=$_GET['nokontrak'];
//	$sHed="select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ".$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
//	$qHead=mysql_query($sHed) or die(mysql_error());
//	$rHead=mysql_fetch_assoc($qHead);
//	$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
//	$qBrg=mysql_query($sBrg) or die(mysql_error());
//	$rBrg=mysql_fetch_assoc($qBrg);
//	
//	$sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
//	$qCust=mysql_query($sCust) or die(mysql_error());
//	$rCust=mysql_fetch_assoc($qCust);
//	echo"<fieldset><legend>".$_SESSION['lang']['detailPengiriman']."</legend>
//	<table cellspacing=1 border=0 class=myinputtext>
//	<tr>
//		<td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td>".$nokontrak."</td>
//	</tr>
//	<tr>
//		<td>".$_SESSION['lang']['tglKontrak']."</td><td>:</td><td>".tanggalnormal($rHead['tanggalkontrak'])."</td>
//	</tr>
//	<tr>
//		<td>".$_SESSION['lang']['komoditi']."</td><td>:</td><td>".$rBrg['namabarang']."</td>
//	</tr>
//	<tr>
//		<td>".$_SESSION['lang']['Pembeli']."</td><td>:</td><td>".$rCust['namacustomer']."</td>
//	</tr>
//	</table><br />
//	<table cellspacing=1 border=0 class=sortable><thead>
//	<tr class=data>
//	<td>".$_SESSION['lang']['notransaksi']."</td>
//	<td>".$_SESSION['lang']['tanggal']."</td>
//	<td>".$_SESSION['lang']['nodo']."</td>
//	<td>".$_SESSION['lang']['nosipb']."</td>
//	<td>".$_SESSION['lang']['beratBersih']."</td>
//	<td>".$_SESSION['lang']['kodenopol']."</td>
//	<td>".$_SESSION['lang']['sopir']."</td>
//	</tr></thead><tbody>
//	";
///*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
//*/	
//
//	$sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from ".$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
//	$qDet=mysql_query($sDet) or die(mysql_error());
//	$rCek=mysql_num_rows($qDet);
//	if($rCek>0)
//	{
//		while($rDet=mysql_fetch_assoc($qDet))
//		{
//			echo"<tr class=rowcontent>
//			<td>".$rDet['notransaksi']."</td>
//			<td>".tanggalnormal($rDet['tanggal'])."</td>
//			<td>".$rDet['nodo']."</td>
//			<td>".$rDet['nosipb']."</td>
//			<td align=right>".number_format($rDet['beratbersih'],2)."</td>
//			<td>".$rDet['nokendaraan']."</td>
//			<td>".ucfirst($rDet['supir'])."</td>
//			</tr>";
//		}
//	}
//	else
//	{
//		echo"<tr><td colspan=7>Not Found</td></tr>";
//	}
//	echo"</tbody></table></fieldset>";
//
//	break;
    default:
    break;
}

?>