<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
//$arr="##klmpkBrg##kdUnit##periode##lokasi##statId##purId";
$sKlmpk="select kode,kelompok from ".$dbname.".log_5klbarang order by kode";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $rKelompok[$rKlmpk['kode']]=$rKlmpk['kelompok'];
}
$optNmOrang=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk=makeOption($dbname, 'organisasi','kodeorganisasi,induk');
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['tgl_ganti']==''?$periode=tanggalsystem($_GET['tgl_ganti']):$periode=tanggalsystem($_POST['tgl_ganti']);
//



if($kdUnit!='')
{
    $unitId=$optNmOrg[$kdUnit];
    $where=" substr(kodeorg,1,6)='".$kdUnit."'";
}
else
{
    exit("Error:".$_SESSION['lang']['unit']." Tidak boleh kosong");
}
if($periode=='')
{
    exit("Error: ".$_SESSION['lang']['tanggal']." tidak boleh kosong");
}
else
{
    $where.=" and tanggal='".$periode."'";
}
$thn=explode("-",$periode);
$bln=intval($thn[1]);
 $thnLalu=$thn[0];
 //header
$sHead="select distinct notransaksi,kodeorg,pemeriksa,asisten from ".$dbname.".kebun_qc_panenht where ".$where." order by tanggal asc";
//exit("Error".$sHead);
$qHead=mysql_query($sHead) or die(mysql_error());
while($rHead=mysql_fetch_assoc($qHead))
{
    $lstNoTrans[]=$rHead['notransaksi'];
}

$kbn=substr($rHead['kodeorg'],0,4);
$Afd=substr($rHead['kodeorg'],0,6);
$dtCek=count($lstNoTrans);
if($dtCek<1)
{
    exit("Error:Sudah Terinput");
}


$brdr=0;
$bgcoloraja='';

if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=12 align=center><b>FORMULIR PEMERIKSAAN BUAH</b></td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['kebun']." : ".$optNmOrg[$kbn]."</td><td colspan=2>&nbsp;</td><td colspan=5 align=right>".$_SESSION['lang']['tanggal']." : ".tanggalnormal($periode)."</td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['afdeling']." : ".$optNmOrg[$Afd]."</td><td colspan=2>&nbsp;</td><td colspan=5 align=right>&nbsp;</td></tr>
    
    </table>";
}
        
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr class=\"rowheader\">
                <td align=center  ".$bgcoloraja.">".$_SESSION['lang']['kodeorg']."</td>
                <td align=center  ".$bgcoloraja.">".$_SESSION['lang']['namakaryawan']."</td>
                <td align=center  ".$bgcoloraja.">No TPH</td>
                <td align=center  ".$bgcoloraja.">JJg Diperiksa</td>
                <td align=center  ".$bgcoloraja.">Mentah</td>
                <td align=center  ".$bgcoloraja.">Kurang Matang</td>
                <td align=center  ".$bgcoloraja.">Matang</td>
                <td align=center  ".$bgcoloraja.">Lwt Matang</td>
                <td align=center  ".$bgcoloraja.">Tangkai panjang</td>
                </tr>";
	
        $tab.="</thead>
	<tbody>";
     
       foreach($lstNoTrans as $dtTrans)
       {    
            $sData="select distinct * from ".$dbname.".kebun_qc_panendt where notransaksi='".$dtTrans."'";
            //echo $sData;
            $qData=mysql_query($sData) or die(mysql_error());
            $rwData=mysql_num_rows($qData);
            while($rData=mysql_fetch_assoc($qData))
            {                
                $tab.="<tr class=rowcontent>";
                $tab.="<td align=left>".substr($rData['notransaksi'],0,10)."</td>";
                $tab.="<td align=right>".$optNmOrang[$rData['karyawanid']]."</td>";
                $tab.="<td align=right>".$rData['notph']."</td>";
                $tab.="<td align=right>".$rData['diperiksa']."</td>";
                $tab.="<td align=right>".$rData['mentah']."</td>";
                $tab.="<td align=right>".$rData['kmatang']."</td>";
                $tab.="<td align=right>".$rData['matang']."</td>";
                $tab.="<td align=right>".$rData['lmatang']."</td>";
                $tab.="<td align=right>".$rData['tpanjang']."</td>";
                $tab.="</tr>";
                $rwData--;
                $totY[$dtTrans]+=$rData['diperiksa'];
                $totMentah[$dtTrans]+=$rData['mentah'];
                $totKmatang[$dtTrans]+=$rData['kmatang'];
                $totMatang[$dtTrans]+=$rData['matang'];
                $totLmatang[$dtTrans]+=$rData['lmatang'];
                $totTpanjang[$dtTrans]+=$rData['tpanjang'];
                
            }
            if($rwData==0)
            {
                @$perMentah=$totMentah[$dtTrans]/($totY[$dtTrans]/100);
                @$perKmatang=$totKmatang[$dtTrans]/($totY[$dtTrans]/100);
                @$perMatang=$totMatang[$dtTrans]/($totY[$dtTrans]/100);
                @$perLmatang=$totLmatang[$dtTrans]/($totY[$dtTrans]/100);
                @$perTpanjang=$totTpanjang[$dtTrans]/($totY[$dtTrans]/100);
                $tab.="<tr class=rowcontent>";
                $tab.="<td align=center colspan=3>".$_SESSION['lang']['total']."</td>";
                $tab.="<td align=right>".$totY[$dtTrans]."</td>";
                $tab.="<td align=right>".number_format($perMentah,2)."</td>";
                $tab.="<td align=right>".number_format($perKmatang,2)."</td>";
                $tab.="<td align=right>".number_format($perMatang,2)."</td>";
                $tab.="<td align=right>".number_format($perLmatang,2)."</td>";
                $tab.="<td align=right>".number_format($perTpanjang,2)."</td>";
                $tab.="</tr>"; 
//                $grPerMentah+=$perMentah;
//                $grPerKmatang+=$perKmatang;
//                $grPerMatang+=$perMatang;
//                $grPerLmatang+=$perLmatang;
//                $grPerTpanjang+=$perTpanjang;
//                $grY+=$totY[$dtTrans];
            }
        }
//        @$perMentah=$grPerMentah/($grY/100);
//        @$perKmatang=$grPerKmatang/($grY/100);
//        @$perMatang=$grPerMatang/($grY/100);
//        @$perLmatang=$totLmatang[$dtTrans]/($grY/100);
//        @$perTpanjang=$grPerTpanjang/($grY/100);
//        $tab.="<tr class=rowcontent>";
//        $tab.="<td align=center colspan=3>".$_SESSION['lang']['grnd_total']."</td>";
//        $tab.="<td align=right>".$grY."</td>";
//        $tab.="<td align=right>".number_format($perMentah,2)."</td>";
//        $tab.="<td align=right>".number_format($perKmatang,2)."</td>";
//        $tab.="<td align=right>".number_format($perMatang,2)."</td>";
//        $tab.="<td align=right>".number_format($perLmatang,2)."</td>";
//        $tab.="<td align=right>".number_format($perTpanjang,2)."</td>";
//        $tab.="</tr>"; 
        $tab.="</tbody></table>";
        
       
switch($proses)
{
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="lapranPemeriksaanBuah".$dte;
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $tab);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";	
	break;
        case'pdf':
      
           class PDF extends FPDF {
           function Header() {
            global $periode;
            global $dataAfd;
            global $kdUnit;
            global $optNmOrg;  
            global $dbname;
            global $periode;
            global $where;
           
            $sHead="select distinct notransaksi,kodeorg,notph from ".$dbname.".kebun_qc_ancakht where ".$where." order by tanggal asc";
            //exit("Error".$sHead);
            $qHead=mysql_query($sHead) or die(mysql_error());
            $rHead=mysql_fetch_assoc($qHead);
            
            $kbn=substr($kdUnit,0,4);
            $Afd=substr($kdUnit,0,6);
                $height=15;
                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("FORMULIR PEMERIKSAAN BUAH"),0,1,'C');

                $this->Cell(50,$height,$_SESSION['lang']['kebun'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(335,$height,$optNmOrg[$kbn],0,0,'L');
                
                $this->Cell(50,$height,$_SESSION['lang']['tanggal'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(20,$height,tanggalnormal($periode),0,1,'L');
                
                $this->Cell(50,$height,$_SESSION['lang']['afdeling'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(335,$height,$optNmOrg[$Afd],0,1,'L');
                
                
                
                
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',6);
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell(60,$height,$_SESSION['lang']['kodeorg'],TBLR,0,'C',1);
                $this->Cell(75,$height,$_SESSION['lang']['namakaryawan'],TBLR,0,'C',1);
                $this->Cell(50,$height,"No TPH",TBLR,0,'C',1);
                $this->Cell(50,$height,"JJg Diperiksa",TBLR,0,'C',1);
                $this->Cell(50,$height,"Mentah",TBLR,0,'C',1);
                $this->Cell(50,$height,"Kurang Matang",TBLR,0,'C',1);
                $this->Cell(50,$height,"Matang",TBLR,0,'C',1);
                $this->Cell(50,$height,"Lwt Matang",TBLR,0,'C',1);
                $this->Cell(60,$height,"Tangkai panjang ",TBLR,1,'C',1);               
          }
              function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('P','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 10;
            $tnggi=$jmlHari*$height;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','B',6);
 foreach($lstNoTrans as $dtTrans)
       {    
            $sData="select distinct * from ".$dbname.".kebun_qc_panendt where notransaksi='".$dtTrans."'";
            //echo $sData;
            $qData=mysql_query($sData) or die(mysql_error());
            $rwData=mysql_num_rows($qData);
            while($rData=mysql_fetch_assoc($qData))
            {                
                $pdf->Cell(60,$height,substr($rData['notransaksi'],0,10),TBLR,0,'L',1);
                $pdf->Cell(75,$height,$optNmOrang[$rData['karyawanid']],TBLR,0,'L',1);
                $pdf->Cell(50,$height,$rData['notph'],TBLR,0,'C',1);
                $pdf->Cell(50,$height,$rData['diperiksa'],TBLR,0,'R',1);
                $pdf->Cell(50,$height,$rData['mentah'],TBLR,0,'R',1);
                $pdf->Cell(50,$height,$rData['kmatang'],TBLR,0,'R',1);
                $pdf->Cell(50,$height,$rData['matang'],TBLR,0,'R',1);
                $pdf->Cell(50,$height,$rData['lmatang'],TBLR,0,'R',1);
                $pdf->Cell(60,$height,$rData['tpanjang'],TBLR,1,'R',1);  
                $rwData--;
                $totY[$dtTrans]+=$rData['diperiksa'];
                $totMentah[$dtTrans]+=$rData['mentah'];
                $totKmatang[$dtTrans]+=$rData['kmatang'];
                $totMatang[$dtTrans]+=$rData['matang'];
                $totLmatang[$dtTrans]+=$rData['lmatang'];
                $totTpanjang[$dtTrans]+=$rData['tpanjang'];
                
            }
            if($rwData==0)
            {
                @$perMentah=$totMentah[$dtTrans]/($totY[$dtTrans]/100);
                @$perKmatang=$totKmatang[$dtTrans]/($totY[$dtTrans]/100);
                @$perMatang=$totMatang[$dtTrans]/($totY[$dtTrans]/100);
                @$perLmatang=$totLmatang[$dtTrans]/($totY[$dtTrans]/100);
                @$perTpanjang=$totTpanjang[$dtTrans]/($totY[$dtTrans]/100);
               
                $pdf->Cell(135,$height,$_SESSION['lang']['total'],TBLR,0,'L',1);
                
                $pdf->Cell(50,$height,$rData['notph'],TBLR,0,'C',1);
                $pdf->Cell(50,$height,$totY[$dtTrans],TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perMentah,2),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perKmatang,2),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perMatang,2),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perLmatang,2),TBLR,0,'R',1);
                $pdf->Cell(60,$height,number_format($perTpanjang,2),TBLR,1,'R',1);  
                $grPerMentah+=$perMentah;
                $grPerKmatang+=$perKmatang;
                $grPerMatang+=$perMatang;
                $grPerLmatang+=$perLmatang;
                $grPerTpanjang+=$perTpanjang;
                $grY+=$totY[$dtTrans];
            }
        }
        @$perMentah=$grPerMentah/($grY/100);
        @$perKmatang=$grPerKmatang/($grY/100);
        @$perMatang=$grPerMatang/($grY/100);
        @$perLmatang=$totLmatang[$dtTrans]/($grY/100);
        @$perTpanjang=$grPerTpanjang/($grY/100);
        
         
                $pdf->Cell(135,$height,$_SESSION['lang']['grnd_total'],TBLR,0,'L',1);
                
                $pdf->Cell(50,$height,$rData['notph'],TBLR,0,'C',1);
                $pdf->Cell(50,$height,$totY[$dtTrans],TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perMentah,2),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perKmatang,2),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perMatang,2),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($perLmatang,2),TBLR,0,'R',1);
                $pdf->Cell(60,$height,number_format($perTpanjang,2),TBLR,1,'R',1);  
            $pdf->Output();
            break;
	
            
	
	default:
	break;
}
      
?>