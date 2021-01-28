<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
$_POST['BlokId']==''?$BlokId=$_GET['BlokId']:$BlokId=$_POST['BlokId'];
$_POST['periodeId']==''?$periodeId=$_GET['periodeId']:$periodeId=$_POST['periodeId'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$where=" kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sep","10"=>"Okt","11"=>"Nov","12"=>"Des");
$optNmkeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$optSatkeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,satuan');
            if($kdUnit!='')
            {
                $where="kodeorg like '".$kdUnit."%'";
            }
            elseif($afdId!='')
            {
                $where="kodeorg like '".$kdUnit."%'";
            }
            
           if($kdUnit!='')
            {
                $where="kodeorg like '".$kdUnit."%'";
            }
            else
            {
                exit("Error:Unit Tidak Boleh Kosong");
            }
            
//            if($BlokId!='')
//            {
//                $where.=" and kodeorg='".$BlokId."'";
//            }
            if($periodeId!='')
            {
                $where.=" and tanggal like '".$periodeId."%'";
            }
            else
            {
                exit("Error:Periode Tidak Boleh Kosong");
            }

	switch($proses)
        {
            case'preview':
            
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead><tr>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td rowspan=2>".$_SESSION['lang']['blok']."</td>";
        $tab.="<td colspan=2 align=center>Panen</td>";
        $tab.="<td colspan=2 align=center>Kirim</td>";
        $tab.="<td colspan=2 align=center>Restan</td>";
        $tab.="</tr><tr><td>Janjang</td><td>Kg</td><td>Janjang</td><td>Kg</td><td>Janjang</td><td>Kg</td>";
        $tab.="</tr></thead><tbody>";
        $sData="select distinct * from ".$dbname.".kebun_restan where ".$where." order by tanggal asc";
        //exit("Error".$sData);
        $qData=mysql_query($sData) or die(mysql_error());
        $row=mysql_num_rows($qData);
        if($row!=0)
        {
        while($rdata=mysql_fetch_assoc($qData))
        {
             $sKg="select distinct bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                    $qKg=mysql_query($sKg) or die(mysql_error());
                    $rKg=mysql_fetch_assoc($qKg);
                    $panenKg=$rdata['jjgpanen']*$rKg['bjr'];
                    $KirimKg=$rdata['jjgkirim']*$rKg['bjr'];
                    $resJjg=$rdata['jjgpanen']-$rdata['jjgkirim'];
                    $resKg=$resJjg*$rKg['bjr'];
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".tanggalnormal($rdata['tanggal'])."</td>";
            $tab.="<td align=right>".$optNm[$rdata['kodeorg']]."</td>";
            $tab.="<td align=right>".$rdata['jjgpanen']."</td>";
            $tab.="<td align=right>".$panenKg."</td>";
            $tab.="<td align=right>".$rdata['jjgkirim']."</td>";
            $tab.="<td align=right>".$KirimKg."</td>";
            $tab.="<td align=right>".$resJjg."</td>";
            $tab.="<td align=right>".$resKg."</td>";
        }
        }
        else
        {
            $tab.="<tr class=rowcontent><td colspan=8>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
        $tab.="</tbody></table>";
        echo $tab;
        break;
          case'excel':
           
            $bgcoloraja="bgcolor=#DEDEDE align=center";
            $brdr=1;
            $tab.="
            <table>
            <tr><td colspan=5 align=left><b>".$_SESSION['lang']['lapRestan']."</b></td><td colspan=7 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
            <tr><td colspan=5 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$kdUnit]." </td></tr>
            <tr><td colspan=5 align=left>&nbsp;</td></tr>
            </table>";
        $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>";
        $tab.="<thead><tr>";
        $tab.="<td rowspan=2 ".$bgcoloraja.">".$_SESSION['lang']['tanggal']."</td>";
        $tab.="<td rowspan=2 ".$bgcoloraja.">".$_SESSION['lang']['blok']."</td>";
        $tab.="<td colspan=2 align=center  ".$bgcoloraja.">Panen</td>";
        $tab.="<td colspan=2 align=center  ".$bgcoloraja.">Kirim</td>";
        $tab.="<td colspan=2 align=center  ".$bgcoloraja.">Restan</td>";
        $tab.="</tr><tr><td  ".$bgcoloraja.">Janjang</td><td  ".$bgcoloraja.">Kg</td><td  ".$bgcoloraja.">Janjang</td><td  ".$bgcoloraja.">Kg</td><td  ".$bgcoloraja.">Janjang</td><td  ".$bgcoloraja.">Kg</td>";
        $tab.="</tr></thead><tbody>";
        $sData="select distinct * from ".$dbname.".kebun_restan where ".$where." order by tanggal asc";
        //exit("Error".$sData);
        $qData=mysql_query($sData) or die(mysql_error());
        $row=mysql_num_rows($qData);
        if($row!=0)
        {
        while($rdata=mysql_fetch_assoc($qData))
        {
             $sKg="select distinct bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                    $qKg=mysql_query($sKg) or die(mysql_error());
                    $rKg=mysql_fetch_assoc($qKg);
                    $panenKg=$rdata['jjgpanen']*$rKg['bjr'];
                    $KirimKg=$rdata['jjgkirim']*$rKg['bjr'];
                    $resJjg=$rdata['jjgpanen']-$rdata['jjgkirim'];
                    $resKg=$resJjg*$rKg['bjr'];
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".tanggalnormal($rdata['tanggal'])."</td>";
            $tab.="<td align=right>".$optNm[$rdata['kodeorg']]."</td>";
            $tab.="<td align=right>".$rdata['jjgpanen']."</td>";
            $tab.="<td align=right>".$panenKg."</td>";
            $tab.="<td align=right>".$rdata['jjgkirim']."</td>";
            $tab.="<td align=right>".$KirimKg."</td>";
            $tab.="<td align=right>".$resJjg."</td>";
            $tab.="<td align=right>".$resKg."</td>";
        }
        }
        else
        {
            $tab.="<tr class=rowcontent><td colspan=8>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
        $tab.="</tbody></table>";
          $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="laporanRestan_".$dte;
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
            global $periodeId;
            global $dataAfd;
            global $kdUnit;
            global $optNm;  
            global $dbname;
            global $where;
          
            global $optSatuan;
            global $optNmBrg;
            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 20;
            
                $this->SetFont('Arial','B',8);
                $this->Cell(250,$height,strtoupper("LAPORAN RESTAN"),0,0,'L');
                $this->Cell(270,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periodeId),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNm[$kdUnit],0,1,'L');
                
                $this->SetFillColor(220,220,220);
                $this->Cell(55,$height,"Tanggal",TLR,0,'C',1);
                $this->Cell(55,$height,$_SESSION['lang']['blok'],TLR,0,'C',1);
                $this->Cell(90,$height,"Panen",TLR,0,'C',1);
                $this->Cell(90,$height,"Kirim",TLR,0,'C',1);
                $this->Cell(90,$height,"Restan",TLR,1,'C',1);
                
                $this->Cell(55,$height," ",BLR,0,'C',1);
                $this->Cell(55,$height," ",BLR,0,'C',1);
                $this->Cell(45,$height,"Janjang",TBLR,0,'C',1);
                $this->Cell(45,$height,"Kg",TBLR,0,'C',1);
                $this->Cell(45,$height,"Janjang",TBLR,0,'C',1);
                $this->Cell(45,$height,"Kg",TBLR,0,'C',1);
                $this->Cell(45,$height,"Janjang",TBLR,0,'C',1);
                $this->Cell(45,$height,"Kg",TBLR,1,'C',1);

               
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
            $height = 15;
           
            $pdf->AddPage();
            
            $pdf->SetFillColor(255,255,255);
             $pdf->SetFont('Arial','',7);
            
             $sData="select distinct * from ".$dbname.".kebun_restan where ".$where." order by tanggal asc";
        //exit("Error".$sData);
        $qData=mysql_query($sData) or die(mysql_error());
        $row=mysql_num_rows($qData);
        if($row!=0)
        {
        while($rdata=mysql_fetch_assoc($qData))
        {
             $sKg="select distinct bjr from ".$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                    $qKg=mysql_query($sKg) or die(mysql_error());
                    $rKg=mysql_fetch_assoc($qKg);
                    $panenKg=$rdata['jjgpanen']*$rKg['bjr'];
                    $KirimKg=$rdata['jjgkirim']*$rKg['bjr'];
                    $resJjg=$rdata['jjgpanen']-$rdata['jjgkirim'];
                    $resKg=$resJjg*$rKg['bjr'];
                    $pdf->Cell(55,$height,tanggalnormal($rdata['tanggal']),TBLR,0,'C',1);
                    $pdf->Cell(55,$height,$rdata['kodeorg'],TBLR,0,'C',1);
                    $pdf->Cell(45,$height,$rdata['jjgpanen'],TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$panenKg,TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$rdata['jjgkirim'],TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$KirimKg,TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$resJjg,TBLR,0,'R',1);
                    $pdf->Cell(45,$height,$resKg,TBLR,1,'R',1);
           
        }
        }
        else
        {
           $pdf->Cell(380,$height,$_SESSION['lang']['dataempty'],TBLR,1,'C',1); 
        }

            $pdf->Output();
        break;
                
            default:
            break;
        }
	
?>
