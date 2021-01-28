<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];

$thn = substr($periode,0,4);
$bln = substr($periode,5,2);
$start=$thn."-01-01";
$wktu=mktime(0,0,0,$bln,15,$thn);
$end=$thn."-".$bln."-".date('t',$wktu);

$str = "select a.notransaksi,a.tanggal,a.nilaikontrak,b.namasupplier
        from ".$dbname.".log_spkht a left join ".$dbname.".log_5supplier b on a.koderekanan = b.supplierid
        where a.tanggal  between '".$start."' and '".$end."'";

$query = mysql_query($str) or die(mysql_error());
while($res=mysql_fetch_assoc($query))
{
    $nospk[]=$res['notransaksi'];
    $tgl[$res['notransaksi']]=$res['tanggal'];
    $kontraktor[$res['notransaksi']]=$res['namasupplier'];
    $rpkontrak[$res['notransaksi']]=$res['nilaikontrak'];
}

$sDataBi ="select notransaksi,sum(jumlahrealisasi) as jml
           from ".$dbname.".log_baspk 
           where tanggal like '".$periode."%' group by notransaksi";

$qDataBi = mysql_query($sDataBi) or die(mysql_error());
while($rDataBi=mysql_fetch_assoc($qDataBi))
{
    $DtBi[$rDataBi['notransaksi']]=$rDataBi['jml'];
}

$sDataSBi ="select notransaksi,sum(jumlahrealisasi) as jml
           from ".$dbname.".log_baspk 
           where tanggal between '".$start."' and '".$end."' group by notransaksi";

$qDataSBi = mysql_query($sDataSBi) or die(mysql_error());
while($rDataSBi=mysql_fetch_assoc($qDataSBi))
{
    $DtSBi[$rDataSBi['notransaksi']]=$rDataSBi['jml'];
}

if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;

}
else
{ 
    $bg="";
    $brdr=0;
}

if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE ";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=5 align=center><b>Summary Progress SPK</b></td><td colspan=7 align=right><b>".$_SESSION['lang']['periode']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=5 align=left>&nbsp;</td></tr>
    </table>";
}
        
$tab.="<table class=sortable cellspacing=1 border=0 width=100%>
        <thead>
            <tr>
                <td align=center rowspan=2>No.</td>
                <td align=center rowspan=2>".$_SESSION['lang']['nospk']."</td>
                <td align=center rowspan=2>".$_SESSION['lang']['tanggal']."</td>
                <td align=center rowspan=2>".$_SESSION['lang']['kontraktor']."</td>
                <td align=center rowspan=2>".$_SESSION['lang']['rpkontrak']."</td>
                <td align=center colspan=2>".$_SESSION['lang']['rprealisasi']."</td>
                <td align=center rowspan=2>".$_SESSION['lang']['%']."</td>
            </tr>  
            <tr>
                <td align=center>".$_SESSION['lang']['bi']."</td>
                <td align=center>".$_SESSION['lang']['sbi']."</td>                        
            </tr>
        </thead>
        <tbody id=container>";
          $i=0;
          foreach($nospk as $spk=>$lsspk)
          {
           $i++;
           $tab.="<tr class=rowcontent>";
           $tab.="<td align=center>".$i."</td>"; 
           $tab.="<td align=left>".$lsspk."</td>"; 
           $tab.="<td align=center>".tanggalnormal($tgl[$lsspk])."</td>"; 
           $tab.="<td align=left>".$kontraktor[$lsspk]."</td>";
           $tab.="<td align=right>".number_format($rpkontrak[$lsspk],0)."</td>";
           $tab.="<td align=right>".number_format($DtBi[$lsspk],0)."</td>";
           $tab.="<td align=right>".number_format($DtSBi[$lsspk],0)."</td>";
           @$persen=number_format((($DtSBi[$lsspk]/$rpkontrak[$lsspk])*100),2);
           $tab.="<td align=right>".$persen."</td>";
           $tab.="</tr>";
          }

$tab.=" </tbody></table>";

switch($proses)
{
    case'preview':
   
    echo $tab;
    break;

    case'excel':
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="Summary_Progress_SPK_".$periode;
    if(strlen($tab)>0)
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
        if(!fwrite($handle,$tab))
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

    case'pdf':
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

            $cols=247.5;
            $wkiri=50;
            $wlain=11;

    class PDF extends FPDF {
        function Header() {
            global $periode;
            global $dbname;
            global $wkiri, $wlain;
                $width = $this->w - $this->lMargin - $this->rMargin;

                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("SUMMARY PROGRESS SPK"),0,1,'L');
                $this->Cell($width,$height,$_SESSION['lang']['periode'].' : '.substr(tanggalnormal($periode),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell(790,$height,' ',0,1,'R');
                
                $height = 15;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',8);
                
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
               
                $this->Cell(15,$height,"No.",TLR,0,'C',1);
                $this->Cell(150,$height,"No SPK",TLR,0,'C',1);
                $this->Cell(50,$height,"Tanggal",TLR,0,'C',1);
                $this->Cell(100,$height,"Kontraktor",TLR,0,'C',1);
                $this->Cell(80,$height,"Rp. Kontrak",TLR,0,'C',1);
                $this->Cell(160,$height,"Rp. Realisasi",TLR,0,'C',1);
                $this->Cell(80,$height,"%",TLR,1,'C',1);
                
              
                $this->Cell(15,$height," ",BLR,0,'C',1);
                $this->Cell(150,$height," ",BLR,0,'C',1);
                $this->Cell(50,$height," ",BLR,0,'C',1);
                $this->Cell(100,$height," ",BLR,0,'C',1);
                $this->Cell(80,$height," ",BLR,0,'C',1);
                $this->Cell(80,$height,"BI",TBLR,0,'C',1);
                $this->Cell(80,$height,"S/d BI",TBLR,0,'C',1);
                $this->Cell(80,$height," ",BLR,1,'C',1);
        }
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',11);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
    //================================

    $pdf=new PDF('L','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 10;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',7);
            $i=0;
    foreach($nospk as $spk=>$lsspk)
        {
            $i++;
               
                $pdf->Cell(15,$height,$i,TBLR,0,'L',1);
                $pdf->Cell(150,$height,$lsspk,TBLR,0,'L',1);
                $pdf->Cell(50,$height,tanggalnormal($tgl[$lsspk]),TBLR,0,'C',1);
                $pdf->Cell(100,$height,$kontraktor[$lsspk],TBLR,0,'L',1);
                $pdf->Cell(80,$height,number_format($rpkontrak[$lsspk],0),TBLR,0,'R',1);
          
                $pdf->Cell(80,$height,number_format($DtBi[$lsspk],0),TBLR,0,'R',1);
                $pdf->Cell(80,$height,number_format($DtSBi[$lsspk],0),TBLR,0,'R',1);
                @$persen=number_format((($DtSBi[$lsspk]/$rpkontrak[$lsspk])*100),2);
                $pdf->Cell(80,$height,$persen,TBLR,1,'R',1);                           
        }
            $pdf->Output();
            break;

    default:
    break;
}
	
?>
