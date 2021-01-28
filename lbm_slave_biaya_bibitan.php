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
        
        $blnLalu="0".$bln;
       
    }
}
else
{
    $blnLalu=$bln-1;
    if(strlen($blnLalu)<2)
    {
        $blnLalu="0".$blnLalu;
    }
    else
    {
        $blnLalu=$blnLalu;
    }
  
}
$addstr.="(";
for($w=1;$w<=$bln;$w++)
{
    if($w<10)
    {  $jack="rp0".$w;}
    else
    { $jack="rp".$w;}
    if($w<$bln)
        $addstr.=$jack."+";
    else
        $addstr.=$jack;
    
}
$addstr.=")";
//A1
//exit("Error:".$addstr);
$sData="select distinct volume from ".$dbname.".bgt_budget_bibit_vw where kebun='".$kdUnit."' and tahunbudget='".$thn[0]."'";
$qData=mysql_query($sData) or die(mysql_error($sData));
$rData=mysql_fetch_assoc($qData);
$dtA1=$rData['volume'];

//A2
$sDataA2="select distinct  rp".$blnLalu."  from ".$dbname.".bgt_lbm_porsi_kebun_vw where 
         kegiatan ='127010601' and tahunbudget='".$thn[0]."' and kebun='".$kdUnit."'";
// exit("Error".$sDataA2);
$qDataA2=mysql_query($sDataA2) or die(mysql_error($sDataA2));
$rDataA2=mysql_fetch_assoc($qDataA2);
$dtA2=$dtA1*$rDataA2['rp'.$blnLalu];


//A3
$sDataA3="select $addstr as jumlah  from ".$dbname.".bgt_lbm_porsi_kebun_vw 
         where kegiatan ='127010601' and tahunbudget='".$thn[0]."' and kebun='".$kdUnit."'";
  //exit("Error".$sDataA3);
$qDataA3=mysql_query($sDataA3) or die(mysql_error($conn));
$rDataA3=mysql_fetch_assoc($qDataA3);
$dtA3=$dtA1*$rDataA3['jumlah'];

//A4
$sDataA4="select sum(jumlah) as jlhM1 from ".$dbname.".bibitan_mutasi  where tanggal < LAST_DAY('".$periode."-15')    and kodeorg like '".$kdUnit."%'";
$qDataA4=mysql_query($sDataA4) or die(mysql_error($conn));
$rDataA4=mysql_fetch_assoc($qDataA4);
$dtA4=$rDataA4['jlhM1'];
//echo $sDataA4;

//A5
$sDataA5="select sum(jumlah) as jlhM1 from ".$dbname.".bibitan_mutasi  
          where tanggal < LAST_DAY('".$periode."-15')  and kodetransaksi in('TMB','AFB','DBT')   and kodeorg like '".$kdUnit."%'";
$qDataA5=mysql_query($sDataA5) or die(mysql_error($conn));
$rDataA5=mysql_fetch_assoc($qDataA5);
$dtA5=$rDataA5['jlhM1'];

//B1
$sDataB1="select (rp01+rp02+rp03+rp04+rp05+rp06+rp07+rp08+rp09+rp10+rp11+rp12) as setahun from ".$dbname.".bgt_lbm_porsi_kebun_vw 
          where kebun='".$kdUnit."' and tahunbudget='".$thn[0]."' and kegiatan='122040801'";
$qDataB1=mysql_query($sDataB1) or die(mysql_error($conn));
$rDataB1=mysql_fetch_assoc($qDataB1);
$dtB1=$rDataB1['setahun'];

//B2
$sDataB2="select rp".$blnLalu." as BI from ".$dbname.".bgt_lbm_porsi_kebun_vw 
          where kebun='".$kdUnit."' and tahunbudget='".$thn[0]."' and kegiatan='122040801'";
$qDataB2=mysql_query($sDataB2) or die(mysql_error($conn));
$rDataB2=mysql_fetch_assoc($qDataB2);
$dtB2=$rDataB2['rp'.$blnLalu]*$dtA1;

//B3
$sDataB3="select $addstr as SBI from ".$dbname.".bgt_lbm_porsi_kebun_vw 
          where kebun='".$kdUnit."' and tahunbudget='".$thn[0]."' and kegiatan='122040801'";

$qDataB3=mysql_query($sDataB3) or die(mysql_error($conn));
$rDataB3=mysql_fetch_assoc($qDataB3);
$dtB3=$rDataB3['SBI']*$dtA1;

//B4
$sDataB4="select sum(jumlah) as jlhM1 from ".$dbname.".bibitan_mutasi  
          where tanggal like '".$periode."%'  and kodetransaksi in('PNB')   and kodeorg like '".$kdUnit."%'";
$qDataB4=mysql_query($sDataB4) or die(mysql_error($conn));
$rDataB4=mysql_fetch_assoc($qDataB4);
$dtB4=$rDataB4['jlhM1'];

//B5
$sDataB5="select sum(jumlah)*-1 as jlhM1 from ".$dbname.".bibitan_mutasi 
          where tanggal <LAST_DAY('".$periode."-15')  and kodetransaksi in('PNB')   and kodeorg like '".$kdUnit."%'";
$qDataB5=mysql_query($sDataB5) or die(mysql_error($conn));
$rDataB5=mysql_fetch_assoc($qDataB5);
$dtB5=$rDataB5['jlhM1'];

//noakun
$sDnoakun="select noakun,namaakun from ".$dbname.".keu_5akun 
           where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun asc";
//exit("Error".$sDnoakun);
$qDnoakun=mysql_query($sDnoakun) or die(mysql_error($conn));
while($rDnoakun=mysql_fetch_assoc($qDnoakun))
{
    $lstNoakun[]=$rDnoakun['noakun'];
    $nmAkun[$rDnoakun['noakun']]=$rDnoakun['namaakun'];
}

//xx
$sDataxx="select  noakun, count(noakun) as rotasi from ".$dbname.".kebun_perawatan_vw where tanggal like '".$periode."%' and unit='".$kdUnit."' and noakun 
         in(select noakun from ".$dbname.".keu_5akun  where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun)  group by noakun";
// exit("Error".$sDataxx);
$qDataxx=mysql_query($sDataxx) or die(mysql_error($conn));
while($rDataxx=mysql_fetch_assoc($qDataxx))
{
    $dtXxRot[$rDataxx['noakun']]+=$rDataxx['rotasi'];
}
//z1
$dr=str_replace("-","",$periode);
$sDataZ1="select noakun,awal".$blnLalu." from ".$dbname.".keu_saldobulanan
          where periode='".$dr."' and kodeorg='".$kdUnit."' 
          and noakun in(select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun)
          order by noakun";
$qDataZ1=mysql_query($sDataZ1) or die(mysql_error($conn));
while($rDataZ1=mysql_fetch_assoc($qDataZ1))
{
    $dtZ1[$rDataZ1['noakun']]+=$rDataZ1['awal'.$blnLalu];
}

//z2
$sDataZ2="select noakun,setahun from ".$dbname.".bgt_summary_biaya_vw where 
         tahunbudget='".$thn[0]."' and unit='".$kdUnit."' and 
         noakun in(select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun) order by noakun";
$qDataZ2=mysql_query($sDataZ2) or die(mysql_error($conn));
while($rDataZ2=mysql_fetch_assoc($qDataZ2))
{
    $dtZ2[$rDataZ2['noakun']]+=$rDataZ2['setahun'];
}

//z3
$sDataZ3="select rp".$blnLalu." as BI,noakun from ".$dbname.".bgt_summary_biaya_vw 
          where tahunbudget='".$thn[0]."' and unit='".$kdUnit."' and 
          noakun in(select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun) order by noakun";
$qDataZ3=mysql_query($sDataZ3) or die(mysql_error($conn));
while($rDataZ3=  mysql_fetch_assoc($qDataZ3))
{
    $dtZ3[$rDataZ3['noakun']]+=$rDataZ3['BI'];
}
//Z4
$sDataZ4="select $addstr as SBI,noakun from ".$dbname.".bgt_summary_biaya_vw 
          where tahunbudget='".$thn[0]."' and unit='".$kdUnit."' and 
          noakun in(select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun) group by noakun order by noakun";
$qDataZ4=mysql_query($sDataZ4) or die(mysql_error());
while($rDataZ4=mysql_fetch_assoc($qDataZ4))
{
    $dtZ4[$rDataZ4['noakun']]+=$rDataZ4['SBI'];
}

//Z5
$sDataZ5="select sum(jumlah) as Bi,noakun from ".$dbname.".keu_jurnaldt 
          where kodeorg='".$kdUnit."' and tanggal like '".$periode."%' and noakun 
          in(select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun) group by noakun order by noakun";
$qDataZ5=mysql_query($sDataZ5) or die(mysql_error($conn));
while($rDataZ5=mysql_fetch_assoc($qDataZ5))
{
    $dtZ5[$rDataZ5['noakun']]+=$rDataZ5['Bi'];
}
//echo $sDataZ5;

//Z6
$sDataZ6="select sum(jumlah) as SBI,noakun from ".$dbname.".keu_jurnaldt where 
          kodeorg='".$kdUnit."' and (tanggal between '".$thn[0]."-01-01' and LAST_DAY('".$periode."-15')) and noakun in 
          (select noakun from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,5) between '12701' and '12704' order by noakun) group by noakun order by noakun";
$qDataZ6=mysql_query($sDataZ6) or die(mysql_error($conn));
while($rDataZ6=mysql_fetch_assoc($qDataZ6))
{
    $dtZ6[$rDataZ6['noakun']]+=$rDataZ6['SBI'];
}
//$varCek=count($dtKend);
//if($varCek<1)
//{
//    exit("Error:Data Kosong");
//}


$brdr=0;
$bgcoloraja='';

if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=5 align=left><b>22 BIAYA BIBITAN</b></td><td colspan=7 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=5 align=left>".$_SESSION['lang']['unit']." : ".$optNmOrg[$kdUnit]." </td></tr>
    <tr><td colspan=5 align=left>&nbsp;</td></tr>
    </table>";
    $tab.="<table border=0 ><tr><td>&nbsp;</td><td colspan=10>&nbsp;</td><td align=right>";
$tab.="<table class=sortable cellspacing=1 cellpadding=1 border=".$brdr.">";
$tab.="<thead><tr><td rowspan=2 style='border-left:none; border-top:none;'>&nbsp;</td>";
$tab.="<td colspan=3 ".$bgcoloraja.">Anggaran</td><td colspan=2 ".$bgcoloraja.">Realisasi</td></tr>";
$tab.="<tr><td ".$bgcoloraja.">Setahun</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td></tr></thead><tbody>";
$tab.="<tr class=rowcontent><td ".$bgcoloraja.">Stok Bibit </td>
       <td align=right>".number_format($dtA1,0)."</td><td align=right>".number_format($dtA2,0)."</td>
       <td align=right>".number_format($dtA3,0)."</td><td align=right>".number_format($dtA4,0)."</td><td align=right>".number_format($dtA5,0)."</td></tr>";
$tab.="<tr class=rowcontent><td ".$bgcoloraja.">Pengiriman Bibit</td>
       <td align=right>".number_format($dtB1,0)."</td><td align=right>".number_format($dtB2,0)."</td><td align=right>".number_format($dtB3,0)."</td><td align=right>".number_format($dtB4,0)."</td>
       <td align=right>".number_format($dtB5,0)."</td></tr>";
$tab.="</tbody></table></td></tr></table>";
}
elseif($proses=='preview')
{

$tab.="<table class=sortable cellspacing=1 cellpadding=1 border=".$brdr." style=float:right;>";
$tab.="<thead><tr><td rowspan=2>&nbsp;</td>";
$tab.="<td colspan=3 ".$bgcoloraja.">Anggaran</td><td colspan=2 ".$bgcoloraja.">Realisasi</td></tr>";
$tab.="<tr><td ".$bgcoloraja.">Setahun</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td><td>BI</td><td ".$bgcoloraja.">S/D BI</td></tr></thead><tbody>";
$tab.="<tr class=rowcontent><td ".$bgcoloraja.">Stok Bibit </td>
       <td align=right>".number_format($dtA1,0)."</td><td align=right>".number_format($dtA2,0)."</td>
       <td align=right>".number_format($dtA3,0)."</td><td align=right>".number_format($dtA4,0)."</td><td align=right>".number_format($dtA5,0)."</td></tr>";
$tab.="<tr class=rowcontent><td ".$bgcoloraja.">Pengiriman Bibit</td>
       <td align=right>".number_format($dtB1,0)."</td><td align=right>".number_format($dtB2,0)."</td><td align=right>".number_format($dtB3,0)."</td><td align=right>".number_format($dtB4,0)."</td>
       <td align=right>".number_format($dtB5,0)."</td></tr>";
    $tab.="</tbody></table>";
}

$tab.="<div style=clear:both;>&nbsp;</div>";
$tab.="<table border=0 ><tr><td>";
	$tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr><td rowspan=3 ".$bgcoloraja.">No.Akun</td>";
        $tab.="<td rowspan=3 ".$bgcoloraja.">URAIAN</td>";
        $tab.="<td rowspan=3 ".$bgcoloraja.">ROTASI</td>";
        $tab.="<td rowspan=3 ".$bgcoloraja.">SALDO AWAL</td>";
        $tab.="<td colspan=7 ".$bgcoloraja.">BIAYA (Rp)</td><td colspan=5 ".$bgcoloraja.">COST/BIBIT</td></tr>";
        $tab.="<tr><td colspan=3 ".$bgcoloraja.">ANGGARAN</td><td colspan=2 ".$bgcoloraja.">REALISASI</td><td colspan=2 ".$bgcoloraja.">% PENCAPAIAN</td>
            <td colspan=3 ".$bgcoloraja.">ANGGARAN</td><td colspan=2 ".$bgcoloraja.">REALISASI</td></tr>";
        $tab.="<tr><td ".$bgcoloraja.">Setahun</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td>";
        $tab.="<td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td>
            <td ".$bgcoloraja.">Setahun</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td></tr>";

        $tab.="</thead><tbody>";
        foreach($lstNoakun as $dtNoakun)
        {
            $tab.="<tr class=rowcontent><td>".$dtNoakun."</td>";
            $tab.="<td>".$nmAkun[$dtNoakun]."</td>";
            $tab.="<td align=right>".number_format($dtXxRot[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ1[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ2[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ3[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ4[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ5[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ6[$dtNoakun],0)."</td>";
            //Z6/Z4*100
            @$dtZ7[$dtNoakun]=$dtZ6[$dtNoakun]/$dtZ2[$dtNoakun]*100;
            @$dtZ8[$dtNoakun]=$dtZ6[$dtNoakun]/$dtZ4[$dtNoakun]*100;
            $tab.="<td align=right>".number_format($dtZ7[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ8[$dtNoakun],0)."</td>";
//            $tab.="</tr>";
            
            @$dtZ9[$dtNoakun]=$dtZ2[$dtNoakun]/$dtA1;
            @$dtZ10[$dtNoakun]=$dtZ3[$dtNoakun]/$dtA2;
            @$dtZ11[$dtNoakun]=$dtZ4[$dtNoakun]/$dtA3;
            @$dtZ12[$dtNoakun]=$dtZ5[$dtNoakun]/$dtA4;
            @$dtZ13[$dtNoakun]=$dtZ8[$dtNoakun]/$dtA5;
//             $tab.="<tr class=rowcontent>";
            $tab.="<td align=right>".number_format($dtZ9[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ10[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ11[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ12[$dtNoakun],0)."</td>";
            $tab.="<td align=right>".number_format($dtZ13[$dtNoakun],0)."</td>";
            $tab.="</tr>";
        }
        $tab.="</tbody></table></td><td>&nbsp;</td><td valign=top>";
     
//        $tab.="<table  cellspacing=1 cellpadding=1 border=".$brdr." class=sortable ><thead>";
//        $tab.="<tr><td colspan=5 ".$bgcoloraja.">COST/BIBIT</td></tr>";
//        $tab.="<tr><td colspan=3 ".$bgcoloraja.">ANGGARAN</td><td colspan=2 ".$bgcoloraja.">REALISASI</td></tr>";
//        $tab.="<tr><td ".$bgcoloraja.">Setahun</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td><td ".$bgcoloraja.">BI</td><td ".$bgcoloraja.">S/D BI</td></tr>";
//        $tab.="</thead><tbody>";
//        foreach($lstNoakun as $dtNoakun)
//        {
//            @$dtZ9[$dtNoakun]=$dtZ1[$dtNoakun]/$dtA1;
//            @$dtZ10[$dtNoakun]=$dtZ3[$dtNoakun]/$dtA2;
//            @$dtZ11[$dtNoakun]=$dtZ4[$dtNoakun]/$dtA3;
//            @$dtZ12[$dtNoakun]=$dtZ7[$dtNoakun]/$dtA4;
//            @$dtZ13[$dtNoakun]=$dtZ8[$dtNoakun]/$dtA5;
//             $tab.="<tr class=rowcontent>";
//            $tab.="<td align=right>".number_format($dtZ9[$dtNoakun],0)."</td>";
//            $tab.="<td align=right>".number_format($dtZ10[$dtNoakun],0)."</td>";
//            $tab.="<td align=right>".number_format($dtZ11[$dtNoakun],0)."</td>";
//            $tab.="<td align=right>".number_format($dtZ12[$dtNoakun],0)."</td>";
//            $tab.="<td align=right>".number_format($dtZ13[$dtNoakun],0)."</td>";
//            $tab.="</tr>";
//        }
//        $tab.="</tbody></table></td></tr></table>";
        $tab.="</table>";
       
switch($proses)
{
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="biayabibitan_".$dte;
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
            global $kdUnit;
            global $optNmOrg;  
            global $dbname;
            global $thn;
            global $tot;

   
                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("22 BIAYA BIBITAN"),0,1,'L');
                $this->Cell($width,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periode),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNmOrg[$kdUnit],0,1,'L');
                $this->Cell(790,$height,' ',0,1,'R');

//                $height = 10;
//                $this->SetFillColor(220,220,220);
//                $this->SetFont('Arial','B',5);
//                
//                $tinggiAkr=$this->GetY();
//                $ksamping=$this->GetX();
//                $this->SetY($tinggiAkr+20);
//                $this->SetX($ksamping);
      
                
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
//            $pdf->SetFillColor(255,255,255);
//            $pdf->SetFont('Arial','B',5);
            $tinggiAkr=$pdf->GetY();
            $ksamping=$pdf->GetX();
            
            $pdf->SetY($tinggiAkr+20);
            $pdf->SetX($ksamping+500);
            $pdf->SetFont('Arial','B',6);
            
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(65,$height," ",R,0,'C',1);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(135,$height,"ANGGARAN",TLR,0,'C',1);
            $pdf->Cell(90,$height,"Realisasi",TLR,1,'C',1);
            $tinggiAkr=$pdf->GetY();
            $ksamping=$pdf->GetX();
            
            $pdf->SetY($tinggiAkr);
            $pdf->SetX($ksamping+500);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(65,$height," ",R,0,'C',1);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(45,$height,"Setahun",TBLR,0,'C',1);
            $pdf->Cell(45,$height,"BI",TBLR,0,'C',1);
            $pdf->Cell(45,$height,"S/D BI",TBLR,0,'C',1);
            $pdf->Cell(45,$height,"BI",TBLR,0,'C',1);
            $pdf->Cell(45,$height,"S/D BI",TBLR,1,'C',1);

            $pdf->SetY($tinggiAkr);
            $pdf->SetX($ksamping+500);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(65,$height,"Stok Bibit",TBLR,0,'C',1);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(45,$height,number_format($dtA1,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtA2,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtA3,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtA4,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtA5,0),TBLR,1,'R',1);
            $pdf->SetY($tinggiAkr+10);
            $pdf->SetX($ksamping+500);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(65,$height,"Pengiriman Bibit",TBLR,0,'C',1);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(45,$height,number_format($dtB1,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtB2,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtB3,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtB4,0),TBLR,0,'R',1);
            $pdf->Cell(45,$height,number_format($dtB5,0),TBLR,1,'R',1);
            $pdf->SetY($tinggiAkr+35);
            $pdf->SetX($ksamping);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(55,$height,"No Akun",TLR,0,'C',1);
            $pdf->Cell(125,$height,"URAIAN",TLR,0,'C',1);
            $pdf->Cell(50,$height,"Rotasi",TLR,0,'C',1);
            $pdf->Cell(50,$height,"SALDO AWAL",TLR,0,'C',1);
            $pdf->Cell(290,$height,"BIAYA (Rp)",TLR,0,'C',1);
            $pdf->SetY($tinggiAkr+35);
            $pdf->SetX($ksamping+580);
            $pdf->Cell(210,$height,"COST/BIBIT",TLR,1,'C',1);
            
            $pdf->SetY($tinggiAkr+(35+$height));
            $pdf->SetX($ksamping);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(55,$height," ",LR,0,'C',1);
            $pdf->Cell(125,$height," ",LR,0,'C',1);
            $pdf->Cell(50,$height," ",LR,0,'C',1);
            $pdf->Cell(50,$height," ",LR,0,'C',1);
            $pdf->Cell(130,$height,"ANGGARAN",TBLR,0,'C',1);
            $pdf->Cell(80,$height,"REALISASI",TBLR,0,'C',1);
            $pdf->Cell(80,$height,"% PENCAPAIAN",TBLR,0,'C',1);
            $pdf->SetY($tinggiAkr+(35+$height));
            $pdf->SetX($ksamping+580);
            $pdf->Cell(130,$height,"ANGGARAN",TBLR,0,'C',1);
            $pdf->Cell(80,$height,"REALISASI",TBLR,1,'C',1);
            
            $pdf->SetY($tinggiAkr+(45+$height));
            $pdf->SetX($ksamping);
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(55,$height," ",BLR,0,'C',1);
            $pdf->Cell(125,$height," ",BLR,0,'C',1);
            $pdf->Cell(50,$height," ",BLR,0,'C',1);
            $pdf->Cell(50,$height," ",BLR,0,'C',1);
            $pdf->Cell(50,$height,"SETAHUN",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"S/D BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"S/D BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"SETAHUN",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"S/D BI",TBLR,0,'C',1);
           
            $pdf->SetY($tinggiAkr+(45+$height));
            $pdf->SetX($ksamping+580);
            $pdf->Cell(50,$height,"SETAHUN",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"S/D BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"BI",TBLR,0,'C',1);
            $pdf->Cell(40,$height,"S/D BI",TBLR,1,'C',1);
            $dar=0;
            $darb=0;
             $pdf->SetFillColor(255,255,255);
            foreach($lstNoakun as $dtNoakun)
            {
                $dar+=10;
                $pdf->SetY($tinggiAkr+(55+$dar));
                $pdf->SetX($ksamping);
                $pdf->Cell(55,$height,$dtNoakun,TBLR,0,'C',1);
                $pdf->Cell(125,$height,$nmAkun[$dtNoakun],TBLR,0,'L',1);
                $pdf->Cell(50,$height,number_format($dtXxRot[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($dtZ1[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(50,$height,number_format($dtZ2[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ3[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ4[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ5[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ6[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ7[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ8[$dtNoakun],0),TBLR,0,'R',1);
            }
            foreach($lstNoakun as $dtNoakun)
            {
                $darb+=10;
                $pdf->SetY($tinggiAkr+(55+$darb));
                $pdf->SetX($ksamping+580);
                $pdf->Cell(50,$height,number_format($dtZ9[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ10[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ11[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ12[$dtNoakun],0),TBLR,0,'R',1);
                $pdf->Cell(40,$height,number_format($dtZ13[$dtNoakun],0),TBLR,1,'R',1);
            }
            
            $pdf->Output();
            break;
	
            
	
	default:
	break;
}
      
?>