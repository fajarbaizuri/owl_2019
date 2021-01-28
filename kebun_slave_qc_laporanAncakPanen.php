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
    $where=" kodeorg='".$kdUnit."'";
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
$sHead="select distinct notransaksi,kodeorg,notph from ".$dbname.".kebun_qc_ancakht where ".$where." order by tanggal asc";
//exit("Error".$sHead);
$qHead=mysql_query($sHead) or die(mysql_error());
$rHead=mysql_fetch_assoc($qHead);

$kbn=substr($rHead['kodeorg'],0,4);
$Afd=substr($rHead['kodeorg'],0,6);



$brdr=0;
$bgcoloraja='';
$cols=count($dataAfd)*3;
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=12 align=center><b>FORMULIR PEMERIKSAAN ANCAK</b></td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['kebun']." : ".$optNmOrg[$kbn]."</td><td colspan=2>&nbsp;</td><td colspan=5 align=right>".$_SESSION['lang']['tanggal']." : ".tanggalnormal($periode)."</td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['afdeling']." : ".$optNmOrg[$Afd]."</td><td colspan=2>&nbsp;</td><td colspan=5 align=right>No TPH : ".$rHead['notph']."</td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['blok']." : ".$optNmOrg[$kdUnit]."</td></tr>
    </table>";
}
        
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr class=\"rowheader\">
                <td rowspan=3 align=center  ".$bgcoloraja.">Urut Pkk</td>
                <td colspan=5 align=center  ".$bgcoloraja.">Brondolan Tinggal</td>
                <td colspan=4 align=center  ".$bgcoloraja.">Buah Tinggal</td>
                <td colspan=2  align=center rowspan=2  ".$bgcoloraja.">Tunasan lalai</td>
                </tr>
                <tr>
                <td rowspan=2 align=center  ".$bgcoloraja.">Pasar Pikul</td>
                <td colspan=2 align=center  ".$bgcoloraja.">Piringan</td>
                <td colspan=2 align=center  ".$bgcoloraja.">Luar Piringan</td>
                <td colspan=2 align=center  ".$bgcoloraja.">Pokok</td>
                <td colspan=2 align=center  ".$bgcoloraja.">Gaw/Piringan</td>
                </tr><tr>";
	for($ard=1;$ard<=5;$ard++)
        {
            $tab.="<td align=center ".$bgcoloraja.">Kiri</td><td align=center ".$bgcoloraja.">Kanan</td>";
        }
        $tab.="</tr></thead>
	<tbody>";
        $sData="select distinct * from ".$dbname.".kebun_qc_ancakdt  where notransaksi='".$rHead['notransaksi']."'";
        $qData=mysql_query($sData) or die(mysql_error());
        $rRow=mysql_num_rows($qData);
        if($rRow>0)
        {
        while($rData=mysql_fetch_assoc($qData))
        {
            $tab.="<tr class=rowcontent>";
            $tab.="<td align=left>".$rData['urutpkk']."</td>";
            $tab.="<td align=right>".$rData['brdpikul']."</td>";
            $tab.="<td align=right>".$rData['brdpirki']."</td>";
            $tab.="<td align=right>".$rData['brdpirka']."</td>";
            $tab.="<td align=right>".$rData['brdlpki']."</td>";
            $tab.="<td align=right>".$rData['brdlpka']."</td>";
            $tab.="<td align=right>".$rData['buahpkkki']."</td>";
            $tab.="<td align=right>".$rData['buahpkkka']."</td>";
            $tab.="<td align=right>".$rData['bhgwki']."</td>";
            $tab.="<td align=right>".$rData['bhgwka']."</td>";
            $tab.="<td align=right>".$rData['brdlama']."</td>";
            $tab.="<td align=right>".$rData['tunasl']."</td>";
            $tab.="</tr>";
            $totbrdpikul+=$rData['brdpikul'];
            $totbrdpirki+=$rData['brdpirki'];
            $totbrdpirka+=$rData['brdpirka'];
            $totbrdlpki+=$rData['brdlpki'];
            $totbrdlpka+=$rData['brdlpka'];
            $totbuahpkkki+=$rData['buahpkkki'];
            $totbuahpkkka+=$rData['buahpkkka'];
            $totbhgwki+=$rData['bhgwki'];
            $totbrdlama+=$rData['brdlama'];
            $totbhgwka+=$rData['bhgwka'];
            $tottunasl+=$rData['tunasl'];
            
        }
        $tab.="<tr class=rowcontent>";
        $tab.="<td align=left>".$_SESSION['lang']['total']."</td>";
        $tab.="<td align=right>".$totbrdpikul."</td>";
        $tab.="<td align=right>".$totbrdpirki."</td>";
        $tab.="<td align=right>".$totbrdpirka."</td>";
        $tab.="<td align=right>".$totbrdlpki."</td>";
        $tab.="<td align=right>".$totbrdlpka."</td>";
        $tab.="<td align=right>".$totbuahpkkki."</td>";
        $tab.="<td align=right>".$totbuahpkkka."</td>";
        $tab.="<td align=right>".$totbhgwki."</td>";
        $tab.="<td align=right>".$totbhgwka."</td>";
        $tab.="<td align=right>".$totbrdlama."</td>";
        $tab.="<td align=right>".$tottunasl."</td>";
        $tab.="</tr>";
       
        
        }
        else
        {
            $tab.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
        $tab.="</tbody></table>";
        $tab.="<table>";
        $bhTinggal=$totbhgwka+$totbhgwki+$totbuahpkkka+$totbuahpkkki;
        $brndlnTinggal=$totbrdpirki+$totbrdpirka+$totbrdlpki+$totbrdlpka;
        $tab.="<tr><td colspan=7>Kualitas Transportasi</td><td colspan=5>&nbsp;</td></tr>";
        $tab.="<tr><td colspan=3>Buah Tinggal</td><td>:</td><td colspan=3>".$bhTinggal." JJG</td><td colspan=5>&nbsp;</td></tr>";
        $tab.="<tr><td colspan=3>Brondolan Tinggal</td><td>:</td><td colspan=3>".$brndlnTinggal." BTR</td><td colspan=5>&nbsp;</td></tr>";
        $tab.="<tr><td colspan=12>&nbsp;</td></tr>";
        $tab.="</table>";
switch($proses)
{
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="lapranAncakPanen".$dte;
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
                $this->Cell($width,$height,strtoupper("FORMULIR PEMERIKSAAN ANCAK"),0,1,'C');

                $this->Cell(50,$height,$_SESSION['lang']['kebun'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(335,$height,$optNmOrg[$kbn],0,0,'L');
                
                $this->Cell(50,$height,$_SESSION['lang']['tanggal'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(20,$height,tanggalnormal($periode),0,1,'L');
                
                $this->Cell(50,$height,$_SESSION['lang']['afdeling'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(335,$height,$optNmOrg[$Afd],0,0,'L');
                
                 $this->Cell(50,$height,"No TPH",0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(20,$height,$rHead['notph'],0,1,'L');
                
                $this->Cell(50,$height,$_SESSION['lang']['blok'],0,0,'L');
                $this->Cell(20,$height,':',0,0,'L');
                $this->Cell(20,$height,$optNmOrg[$kdUnit],0,1,'L');
                
                
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',7);
                
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell(60,$height,"Urut ",TLR,0,'C',1);
                $this->Cell(200,$height,"Brondolan Tinggal",TBLR,0,'C',1);
                $this->Cell(160,$height,"Buah Tinggal",TBLR,0,'C',1);
                $this->Cell(80,$height,"Tunasan ",TBLR,1,'C',1);

                $this->Cell(60,$height,"PKK",LR,0,'C',1);
                $this->Cell(40,$height,"Pasar",TLR,0,'C',1);
                $this->Cell(80,$height,"Piringan",TLBR,0,'C',1);
                $this->Cell(80,$height,"Luar Piringan",TLBR,0,'C',1);
                $this->Cell(80,$height,"Pokok",TLBR,0,'C',1);
                $this->Cell(80,$height,"Gaw/Piringan",TLBR,0,'C',1);
                $this->Cell(80,$height,"lalai ", BLR,1,'C',1);
                
                $this->Cell(60,$height,"",LBR,0,'C',1);
                $this->Cell(40,$height,"Pikul",LBR,0,'C',1);
                for($dtAwal=1;$dtAwal<6;$dtAwal++)
                {
                    if($dtAwal==5)
                    {
                        $this->Cell(40,$height,"Kiri",TLBR,0,'C',1);
                        $this->Cell(40,$height,"Kanan",TLBR,1,'C',1);
                    }
                    else
                    {
                        $this->Cell(40,$height,"Kiri",TLBR,0,'C',1);
                        $this->Cell(40,$height,"Kanan",TLBR,0,'C',1);
                    }
                }
               
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
 $sData="select distinct * from ".$dbname.".kebun_qc_ancakdt  where notransaksi='".$rHead['notransaksi']."'";
        $qData=mysql_query($sData) or die(mysql_error());
        $rRow=mysql_num_rows($qData);
        if($rRow>0)
        {
        while($rData=mysql_fetch_assoc($qData))
        {
            $pdf->Cell(60,$height,$rData['urutpkk'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['brdpikul'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['brdpirki'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['brdpirka'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['brdlpki'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['brdlpka'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['buahpkkki'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['buahpkkka'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['bhgwki'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['bhgwka'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['brdlama'],TLBR,0,'C',1);
            $pdf->Cell(40,$height,$rData['tunasl'],TLBR,1,'C',1);
            $totbrdpikul+=$rData['brdpikul'];
            $totbrdpirki+=$rData['brdpirki'];
            $totbrdpirka+=$rData['brdpirka'];
            $totbrdlpki+=$rData['brdlpki'];
            $totbrdlpka+=$rData['brdlpka'];
            $totbuahpkkki+=$rData['buahpkkki'];
            $totbuahpkkka+=$rData['buahpkkka'];
            $totbhgwki+=$rData['bhgwki'];
            $totbrdlama+=$rData['brdlama'];
            $totbhgwka+=$rData['bhgwka'];
            $tottunasl+=$rData['tunasl'];
            
        }
        $pdf->Cell(60,$height,$_SESSION['lang']['total'],TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbrdpikul,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbrdpirki,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbrdpirka,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbrdlpki,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbrdlpka,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbuahpkkki,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbuahpkkka,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbhgwki,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbhgwka,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$totbrdlama,TLBR,0,'C',1);
        $pdf->Cell(40,$height,$tottunasl,TLBR,1,'C',1);
    
        }
        else
        {
            $pdf->Cell(500,$height,$_SESSION['lang']['dataempty'],TLBR,0,'C',1);
        }

        $tinggiAkr=$pdf->GetY();
        $ksamping=$pdf->GetX();
        $bhTinggal=$totbhgwka+$totbhgwki+$totbuahpkkka+$totbuahpkkki;
        $brndlnTinggal=$totbrdpirki+$totbrdpirka+$totbrdlpki+$totbrdlpka;
        $pdf->SetY($tinggiAkr+20);
        $pdf->SetX($ksamping);
        $pdf->Cell(50,$height,"Kualitas Transportasi",0,1,'L',1);
        $pdf->Cell(70,$height,"Buah Tinggal",0,0,'L',1);
        $pdf->Cell(10,$height,":",0,0,'L',1);
        $pdf->Cell(50,$height,$bhTinggal." JJG",0,1,'L',1);
        $pdf->Cell(70,$height,"Brondolan Tinggal",0,0,'L',1);
        $pdf->Cell(10,$height,":",0,0,'L',1);
        $pdf->Cell(50,$height,$brndlnTinggal." BTR",0,0,'L',1);
            $pdf->Output();
            break;
	
            
	
	default:
	break;
}
      
?>