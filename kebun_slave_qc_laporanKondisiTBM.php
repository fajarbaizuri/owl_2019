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
    $where=" afdeling='".$kdUnit."'";
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
$sHead="select distinct notransaksi,afdeling from ".$dbname.".kebun_qc_kondisitbht where ".$where." order by tanggal asc";
//exit("Error".$sHead);
$qHead=mysql_query($sHead) or die(mysql_error());
while($rHead=mysql_fetch_assoc($qHead))
{
    $lstNoTrans[]=$rHead['notransaksi'];
}

$kbn=substr($rHead['afdeling'],0,4);
$Afd=substr($rHead['afdeling'],0,6);
//$dtCek=count($lstNoTrans);
//if($dtCek<1)
//{
//    exit("Error:Sudah Terinput");
//}


$brdr=0;
$bgcoloraja='';

if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=12 align=center><b>Kondisi Lapangan TB</b></td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['kebun']." : ".$optNmOrg[$kbn]."</td><td colspan=2>&nbsp;</td><td colspan=5 align=right>".$_SESSION['lang']['tanggal']." : ".tanggalnormal($periode)."</td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['afdeling']." : ".$optNmOrg[$Afd]."</td><td colspan=2>&nbsp;</td><td colspan=5 align=right>&nbsp;</td></tr>
    
    </table>";
}
        
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr class=\"rowheader\">
                <td align=center rowspan=2  ".$bgcoloraja.">No.</td>
                <td align=center rowspan=2  ".$bgcoloraja.">".$_SESSION['lang']['kodeorg']."</td>
                <td align=center  ".$bgcoloraja." colspan=2>Persiapan lahan</td>
                <td align=center rowspan=2 ".$bgcoloraja.">Teras</td>
                <td align=center rowspan=2 ".$bgcoloraja.">Kacangan</td>
                </tr>";
        $tab.="<tr class=rowheader><td>Chipping</td><td>Stacking</td></tr>";
	
        $tab.="</thead>
	<tbody>";
        foreach($lstNoTrans as $dtTrans)
        {    
            $sData="select distinct * from ".$dbname.".kebun_qc_kondisitbdt where notransaksi='".$dtTrans."'";
            //echo $sData;
            $qData=mysql_query($sData) or die(mysql_error());
            $rwData=mysql_num_rows($qData);
            while($rData=mysql_fetch_assoc($qData))
            {                
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td align=left>". $no."</td>";
                $tab.="<td align=left>". $rData['kodeorg']."</td>";
                $tab.="<td align=right>".$rData['chipping']."</td>";
                $tab.="<td align=right>".$rData['stcking']."</td>";
                $tab.="<td align=right>".$rData['teras']."</td>";
                $tab.="<td align=right>".$rData['kacangan']."</td>";
                $tab.="</tr>";
                $totPir+=$rData['chipping'];
                $totGaw+=$rData['stcking'];
                $totPemumpukan+=$rData['teras'];
                $totHpt+=$rData['kacangan'];
                $totRow+=$rwData;
            }
        }
        $tab.="<tr class=rowcontent>";
        $tab.="<td align=left colspan=2>".$_SESSION['lang']['total']."</td>";
        $tab.="<td align=right>".$totPir."</td>";
        $tab.="<td align=right>".$totGaw."</td>";
        $tab.="<td align=right>".$totPemumpukan."</td>";
        $tab.="<td align=right>".$totHpt."</td>";
        $tab.="</tr>";
        @$rtPir=$totPir/$totRow;
        @$rtGaw=$totGaw/$totRow;
        @$rtPemumpukan=$totPemumpukan/$totRow;
        @$rtHpt=$totHpt/$totRow;
        $tab.="<tr class=rowcontent>";
        $tab.="<td align=left colspan=2>Rata-rata</td>";
        $tab.="<td align=right>".$rtPir."</td>";
        $tab.="<td align=right>".$rtGaw."</td>";
        $tab.="<td align=right>".$rtPemumpukan."</td>";
        $tab.="<td align=right>".$rtHpt."</td>";
        $tab.="</tr>";
        $tab.="</tbody></table>";
        
       
switch($proses)
{
	case'preview':
            //exit("Error:masuk");
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
           
            $sHead="select distinct notransaksi,afdeling from ".$dbname.".kebun_qc_kondisitbmht where ".$where." order by tanggal asc";
            //exit("Error".$sHead);
            $qHead=mysql_query($sHead) or die(mysql_error());
            $rHead=mysql_fetch_assoc($qHead);
            
            $kbn=substr($kdUnit,0,4);
            $Afd=substr($kdUnit,0,6);
                $height=15;
                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("Kondisi Lapangan TB"),0,1,'C');

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
                $this->Cell(60,$height,"No.",TLR,0,'C',1);
                $this->Cell(75,$height,$_SESSION['lang']['kodeorg'],TBLR,0,'C',1);
                $this->Cell(100,$height,"Persiapan lahan",TBLR,0,'C',1);
                $this->Cell(50,$height,"Teras",TLR,0,'C',1);
                $this->Cell(50,$height,"Kacangan",TBLR,1,'C',1);
                
                $this->Cell(60,$height," ",BLR,0,'C',1);
                $this->Cell(75,$height,"",BLR,0,'C',1);
                $this->Cell(50,$height,"Chipping",TBLR,0,'C',1);
                $this->Cell(50,$height,"Stacking",TBLR,0,'C',1);
                $this->Cell(50,$height," ",BLR,0,'C',1);
                $this->Cell(50,$height," ",BLR,1,'C',1);
                           
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
            $nor=0;
            foreach($lstNoTrans as $dtTrans)
            {    
                $sData="select distinct * from ".$dbname.".kebun_qc_kondisitbdt where notransaksi='".$dtTrans."'";
                //echo $sData;
                $qData=mysql_query($sData) or die(mysql_error());
                $rwData=mysql_num_rows($qData);
                while($rData=mysql_fetch_assoc($qData))
                {                
                    $nor+=1;

                    $totPir2+=$rData['chipping'];
                    $totGaw2+=$rData['stcking'];
                    $totPemumpukan2+=$rData['teras'];
                    $totHpt2+=$rData['kacangan'];
                    $totRow2+=$rwData;
                    $pdf->Cell(60,$height,$nor,TBLR,0,'C',1);
                    $pdf->Cell(75,$height,$rData['kodeorg'],TBLR,0,'L',1);
                    $pdf->Cell(50,$height,$rData['chipping'],TBLR,0,'R',1);
                    $pdf->Cell(50,$height,$rData['stcking'],TBLR,0,'R',1);
                    $pdf->Cell(50,$height,$rData['teras'],TBLR,0,'R',1);
                    $pdf->Cell(50,$height,$rData['kacangan'],TBLR,1,'R',1);
                }
            }
            $pdf->Cell(135,$height,$_SESSION['lang']['total'],TBLR,0,'C',1);
            
            $pdf->Cell(50,$height,$totPir2,TBLR,0,'R',1);
            $pdf->Cell(50,$height,$totGaw2,TBLR,0,'R',1);
            $pdf->Cell(50,$height,$totPemumpukan2,TBLR,0,'R',1);
            $pdf->Cell(50,$height,$totHpt2,TBLR,1,'R',1);
            @$rtPir2=$totPir2/$totRow2;
            @$rtGaw2=$totGaw2/$totRow2;
            @$rtPemumpukan2=$totPemumpukan2/$totRow2;
            @$rtHpt2=$totHpt2/$totRow2;
            $pdf->Cell(135,$height,"Rata-rata",TBLR,0,'C',1);
            
            $pdf->Cell(50,$height,$rtPir2,TBLR,0,'R',1);
            $pdf->Cell(50,$height,$rtGaw2,TBLR,0,'R',1);
            $pdf->Cell(50,$height,$rtPemumpukan2,TBLR,0,'R',1);
            $pdf->Cell(50,$height,$rtHpt2,TBLR,1,'R',1);
            $pdf->Output();
            break;
	
            
	
	default:
	break;
}
      
?>