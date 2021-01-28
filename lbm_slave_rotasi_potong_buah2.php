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
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
//

$unitId=$_SESSION['lang']['all'];
$nmPrshn="Holding";
$purchaser=$_SESSION['lang']['all'];
if($periode=='')
{
    exit("Error: ".$_SESSION['lang']['periode']." tidak boleh kosong");
}
if($kdUnit!='')
{
    $unitId=$optNmOrg[$kdUnit];
}
else
{
    exit("Error:".$_SESSION['lang']['unit']." Tidak boleh kosong");
}
$thn=explode("-",$periode);
$bln=intval($thn[1]);
$thnLalu=$thn[0];
if(strlen($bln)<2)
{
    if($thn[1]=='1')
    {
        $blnLalu=12;
        $thnLalu=$thn[0]-1;
      
    }
    else
    {
        $bln=$bln-1;
        $blnLalu="0".$bln;
        
        //exit("Error".$ksng.$blnLalu);
    }
}
else
{
    $blnLalu=$bln-1;
  
}
$sLuas="select kodeorg,tahuntanam from ".$dbname.".kebun_interval_panen_vw 
       where substr(tanggal,1,7)='".$periode."' and kodeorg like '".$kdUnit."%' group by kodeorg order by kodeorg asc";
//echo $sLuas."<br />";
$qLuas=mysql_query($sLuas) or die(mysql_error());
while($rLuas=mysql_fetch_assoc($qLuas))
{
        $dtBlok[]=$rLuas['kodeorg'];  
        $dtThnTnm[$rLuas['kodeorg']]=$rLuas['tahuntanam'];  
}
$x=mktime(0,0,0,$blnLalu,15,$thnLalu);
$y=mktime(0,0,0,$thn[1],15,$thn[0]);
$jlhharilalu=date('t',$x);
$jlhhari=date('t',$y);
$harisbi=date('z',mktime(11,59,58,$thn[1],$jlhhari,$thn[0]));

$sRotasiBudget="select distinct rotasi,kodeorg from ".$dbname.".bgt_budget 
                where tahunbudget='".$thn[0]."' and kodeorg like '".$kdUnit."%' and kegiatan=611010101";
$qRotasiBudget=mysql_query($sRotasiBudget) or die(mysql_error());
while($rRotasiBudget=mysql_fetch_assoc($qRotasiBudget))
{
   @$dtBudgetBi[$rRotasiBudget['kodeorg']]+=$rRotasiBudget['rotasi']/12;
   $dtBudgetSbi[$rRotasiBudget['kodeorg']]+=$dtBudgetBi[$rRotasiBudget['kodeorg']]*(intval($thn[1]));
}
    $sRotasi="select count(kodeorg) as rotasi,kodeorg,substr(tanggal,7,2) as tgl from ".$dbname.".kebun_interval_panen_vw 
              where  substr(tanggal,1,7)='".$thnLalu."-".$blnLalu."' and kodeorg like '".$kdUnit."%' group by kodeorg";
    $qRotasi=mysql_query($sRotasi) or die(mysql_error());
    while($rRotasi=mysql_fetch_assoc($qRotasi))
    {
        $dataBlnLalu[$rRotasi['kodeorg']]+=$rRotasi['rotasi'];
        $dataTglLalu[$rRotasi['kodeorg']][$rRotasi['tgl']]+=$rRotasi['rotasi'];
    }

    $sRotasi="select count(kodeorg) as rotasi,kodeorg from ".$dbname.".kebun_interval_panen_vw 
              where  substr(tanggal,1,7)='".$thn[0]."-".$thn[1]."' and kodeorg like '".$kdUnit."%' group by kodeorg";
    //echo $sRotasi;
    $qRotasi=mysql_query($sRotasi) or die(mysql_error());
    while($rRotasi=mysql_fetch_assoc($qRotasi))
    {
        $dataBln[$rRotasi['kodeorg']]+=$rRotasi['rotasi'];
        $dataTgl[$rRotasi['kodeorg']][$rRotasi['tgl']]+=$rRotasi['rotasi'];
    }
    $sRotasi="select count(kodeorg) as rotasi,kodeorg from ".$dbname.".kebun_interval_panen_vw 
              where  substr(tanggal,1,7) between '".$thn[0]."-01' and '".$periode."' and kodeorg like '".$kdUnit."%' group by kodeorg";
    //echo $sRotasi;
    $qRotasi=mysql_query($sRotasi) or die(mysql_error());
    while($rRotasi=mysql_fetch_assoc($qRotasi))
    {
        $dataBlnSbi[$rRotasi['kodeorg']]+=$rRotasi['rotasi'];
        $dataTglSbi[$rRotasi['kodeorg']][$rRotasi['tgl']]+=$rRotasi['rotasi'];
    }
//}
 //echo $sRotasi."<br />".$thnLalu."__".$blnLalu."__".$harisbi;
//$jmlHari= cal_days_in_month(CAL_GREGORIAN, $thn[1], $thn[0]);
$varCek=count($dtBlok);
if($varCek<1)
{
    exit("Error:Data Kosong");
}
$brdr=0;
$bgcoloraja='';
$cols=count($dataAfd)*3;
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=5 align=left><b>07. ROTASI POTONG BUAH</b></td><td colspan=7 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['unit']." : ".$optNmOrg[$kdUnit]." </td></tr>
    <tr><td colspan=5 align=left>&nbsp;</td></tr>
    </table>";
}
        
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." colspan=8>ROTASI PANEN (KALI)</td></tr>";
        $tab.="<tr>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['blok']."</td>
        <td ".$bgcoloraja." rowspan=2>Tahun Tanam</td><td ".$bgcoloraja." rowspan=2>BULAN LALU</td>";
        $tab.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['budget']."</td><td ".$bgcoloraja." colspan=2>REALISASI</td>";
        $tab.="<td  ".$bgcoloraja." rowspan=2>ROTASI RATA-RATA PER BULAN</td></tr>";
       
        $tab.="<tr><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td></tr>";
        $tab.="</thead>
	<tbody>";
        foreach($dtBlok as $lsBlok)
        {
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$lsBlok."</td>";
            $tab.="<td align=right>".$dtThnTnm[$lsBlok]."</td>";
            $tab.="<td align=right>".number_format($dataBlnLalu[$lsBlok],2)."</td>";
            $tab.="<td align=right>".number_format($dtBudgetBi[$lsBlok],2)."</td>";
            $tab.="<td align=right>".number_format($dtBudgetSbi[$lsBlok],2)."</td>";
            $tab.="<td align=right>".number_format($dataBln[$lsBlok],2)."</td>";
            $tab.="<td align=right>".number_format($dataBlnSbi[$lsBlok],2)."</td>";
            @$rtBln[$lsBlok]=$dataBlnSbi[$lsBlok]/(intval($thn[1]));
            $tab.="<td align=right>".number_format($rtBln[$lsBlok],2)."</td>";
            $totBlnLalu+=$dataBlnLalu[$lsBlok];
            $totBudgetBi+=$dtBudgetBi[$lsBlok];
            $totBudgetSbi+=$dtBudgetSbi[$lsBlok];
            $totRealBi+=$dataBln[$lsBlok];
            $totRealSbi+=$dataBlnSbi[$lsBlok];
            $totRata+=$rtBln[$lsBlok];
        }
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
        $tab.="<td  align=right>".number_format($totBlnLalu,2)."</td>";
        $tab.="<td  align=right>".number_format($totBudgetBi,2)."</td>";
        $tab.="<td align=right>".number_format($totBudgetSbi,2)."</td>";
        $tab.="<td align=right>".number_format($totRealBi,2)."</td>";
        $tab.="<td align=right>".number_format($totRealSbi,2)."</td>";
        $tab.="<td align=right>".number_format($totRata,2)."</td>";
        $tab.="</tbody></table>";
       
switch($proses)
{
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="rotasipotongbuah_".$dte;
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
            global $thn;

                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("07. ROTASI POTONG BUAH"),0,1,'L');
                $this->Cell(790,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periode),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNmOrg[$kdUnit],0,1,'L');
                $this->Cell(790,$height,' ',0,1,'R');
                
                $height = 15;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',7);
                
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell(360,$height,"ROTASI PANEN (KALI)",TBLR,1,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['blok'],TLR,0,'C',1);
                $this->Cell(50,$height,"TAHUN",TLR,0,'C',1);
                $this->Cell(50,$height,"BULAN",TLR,0,'C',1);
                $this->Cell(70,$height,"ANGGARAN",TBLR,0,'C',1);
                $this->Cell(70,$height,"REALISASI",TBLR,0,'C',1);
                $this->Cell(70,$height,"ROTASI RATA",TLR,1,'C',1);
                
                $this->Cell(50,$height," ",BLR,0,'C',1);
                $this->Cell(50,$height,"TANAM",BLR,0,'C',1);
                $this->Cell(50,$height,"LALU",BLR,0,'C',1);
                $this->Cell(35,$height,"BI",TBLR,0,'C',1);
                $this->Cell(35,$height,"s/d BI",TBLR,0,'C',1);
                $this->Cell(35,$height,"BI",TBLR,0,'C',1);
                $this->Cell(35,$height,"s/d BI",TBLR,0,'C',1);
                $this->Cell(70,$height,"-RATA PER BULAN",BLR,1,'C',1);
                
          }
              function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('L','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 10;
            $tnggi=$jmlHari*$height;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','B',6);
         
            foreach($dtBlok as $lsBlok)
            {
                $pdf->Cell(50,$height,$lsBlok,TBLR,0,'L',1);
                $pdf->Cell(50,$height,$dtThnTnm[$lsBlok],TBLR,0,'C',1);
                $pdf->Cell(50,$height,number_format($dataBlnLalu[$lsBlok],2),TBLR,0,'R',1);
                $pdf->Cell(35,$height,number_format($dtBudgetBi[$lsBlok],2),TBLR,0,'R',1);
                $pdf->Cell(35,$height,number_format($dtBudgetSbi[$lsBlok],2),TBLR,0,'R',1);
                $pdf->Cell(35,$height,number_format($dataBln[$lsBlok],2),TBLR,0,'R',1);
                $pdf->Cell(35,$height,number_format($dataBlnSbi[$lsBlok],2),TBLR,0,'R',1);
                $pdf->Cell(70,$height,number_format($rtBln[$lsBlok],2),TBLR,1,'R',1);
            }
            $pdf->Cell(100,$height,$_SESSION['lang']['total'],TBLR,0,'L',1);
            $pdf->Cell(50,$height,number_format($totBlnLalu,2),TBLR,0,'R',1);
            $pdf->Cell(35,$height,number_format($totBudgetBi,2),TBLR,0,'R',1);
            $pdf->Cell(35,$height,number_format($totBudgetSbi,2),TBLR,0,'R',1);
            $pdf->Cell(35,$height,number_format($totRealBi,2),TBLR,0,'R',1);
            $pdf->Cell(35,$height,number_format($totRealSbi,2),TBLR,0,'R',1);
            $pdf->Cell(70,$height,number_format($totRata,2),TBLR,1,'R',1);
            $pdf->Output();
            break;
	
            
	
	default:
	break;
}
      
?>