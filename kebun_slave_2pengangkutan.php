<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$periode=$_POST['periode'];
$idKebun=$_POST['idKebun'];


switch($proses)
{
case'preview':


        if($periode=='')
        {
                echo "warning : Periode masih kosong";
                exit();	
        }



echo" <table class=sortable cellspacing=1 border=0>
<thead>
<tr class=rowheader>
<td>".substr($_SESSION['lang']['nomor'],0,2).".</td>
<td>".$_SESSION['lang']['nospb']."</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['status']."</td>
<td>BJR AKTUAL</td>
<td>".$_SESSION['lang']['janjang']."</td>
<td>".$_SESSION['lang']['brondolan']."</td>
<td>".$_SESSION['lang']['mentah']."</td>
<td>".$_SESSION['lang']['busuk']."</td>
<td>".$_SESSION['lang']['matang']."</td>
<td>".$_SESSION['lang']['lewatmatang']."</td>
<td>".$_SESSION['lang']['kgbjr']."</td>
<td>Kg Potongan</td>
<td>Netto</td>
<td>Normal</td>
<td>".$_SESSION['lang']['totalkg']."</td>
</tr>
</thead><tbody>";


/*$sql="select a.nospb,a.tanggal,a.posting,b.* from ".$dbname.".kebun_spbht a inner join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."'  order by a.tanggal asc";*/
$sql="select a.nospb,a.tanggal,a.posting from ".$dbname.".kebun_spbht a where tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc";
//echo $sql;
$query=mysql_query($sql) or die(mysql_error());
$row=mysql_num_rows($query);
if($row>0)
{
        while($res=mysql_fetch_assoc($query))
        {
                $no+=1;
                $sSpbDet=
                        "select avg(a.bjr) as Bjr,sum(a.jjg) as Janjang,sum(a.brondolan) as Brondolan,sum(a.mentah) as Mentah,sum(a.busuk) as Busuk,sum(a.matang) as Matang,sum(a.lewatmatang) as Lewatmatang,sum(a.kgbjr) as kgBjr,sum(a.kgwb) as kGwb,sum(a.totalkg) as totaLkg,b.kgpotsortasi
                        from ".$dbname.".kebun_spbdt a
                        left join ".$dbname.".pabrik_timbangan b
                        on a.nospb=b.nospb
                        where a.nospb='".$res['nospb']."'";

                        //echo $sSpbDet;exit();

        //	echo $sSpbDet;
                $qSpbDet=mysql_query($sSpbDet) or die(mysql_error());
                $rSpbDet=mysql_fetch_assoc($qSpbDet);
                $srow="select blok from ".$dbname.".kebun_spbdt where nospb='".$res['nospb']."'";
                $qrow=mysql_query($srow) or die(mysql_error($conn));
                $rRow=mysql_num_rows($qrow);
                @$bjrR=$rSpbDet['Bjr']/$rRow;
                $arrPost=array($_SESSION['lang']['belumposting'],$_SESSION['lang']['posting']);
                $arr="nospb"."##".$res['nospb'];

                $normal=$rSpbDet['kGwb']-$rSpbDet['kgpotsortasi'];

                echo"<tr class=rowcontent onclick=\"zDetail(event,'kebun_slave_2pengangkutan.php','".$arr."')\" style='cursor:pointer;'>
                <td>".$no."</td>
                <td>".$res['nospb']."</td>
                <td>".tanggalnormal($res['tanggal'])."</td>
                <td>".$arrPost[$res['posting']]."</td>		
                <td align=\"right\">".number_format($rSpbDet['kGwb']/$rSpbDet['Janjang'],2)."</td>
				
                <td align=\"right\">".number_format($rSpbDet['Janjang'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Brondolan'],2)."</td>
                <td align=\"right\">".number_format($rSpbDet['Mentah'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Busuk'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Matang'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Lewatmatang'])."</td>
                <td align=\"right\">".number_format($rSpbDet['kgBjr'],2)."</td>
                <td align=\"right\">".number_format($rSpbDet['kgpotsortasi'],2)."</td>
                <td align=\"right\">".number_format($rSpbDet['kGwb'],2)."</td>
                <td align=\"right\">".number_format($normal,2)."</td>
                <td align=\"right\">".number_format($rSpbDet['totaLkg'],2)."</td>
                </tr>
                ";


                $granjjg+=$rSpbDet['Janjang'];
                $granbrondolan+=$rSpbDet['Brondolan'];
                $granmentah+=$rSpbDet['Mentah'];
                $granbusuk+=$rSpbDet['Busuk'];
                $granmatang+=$rSpbDet['Matang'];

                $granlewatmatang+=$rSpbDet['Lewatmatang'];
                $grankgbjr+=$rSpbDet['kgBjr'];
                $grankgpot+=$rSpbDet['kgpotsortasi'];
                $grankgwb+=$rSpbDet['kGwb'];
                $grannormal+=$normal;

                $grantotalkg+=$rSpbDet['totaLkg'];

        }
        echo "
                <thead><tr>
                        <td colspan=5 align=center>".$_SESSION['lang']['total']."</td>
                        <td align=right>".number_format($granjjg,2)."</td>
                        <td align=right>".number_format($granbrondolan,2)."</td>
                        <td align=right>".number_format($granmentah,2)."</td>
                        <td align=right>".number_format($granbusuk,2)."</td>
                        <td align=right>".number_format($granmatang,2)."</td>

                        <td align=right>".number_format($granlewatmatang,2)."</td>
                        <td align=right>".number_format($grankgbjr,2)."</td>
                        <td align=right>".number_format($grankgpot,2)."</td>
                        <td align=right>".number_format($grankgwb,2)."</td>
                        <td align=right>".number_format($grannormal,2)."</td>

                        <td align=right>".number_format($grantotalkg,2)."</td>
                </tr></thead>

        ";

}


else
{
        echo"<tr class=rowcontent align=center><td colspan=14>Not Found</td></tr>";
}
echo"</tbody></table>";
break;
case'pdf':




$periode=$_GET['periode'];
$idKebun=$_GET['idKebun'];
if($periode=='')
{
echo "warning : Periode masih kosong";
exit();	
}
 class PDF extends FPDF
{
    function Header() {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
                        global $periode;
                        global $kdBrg;
                        global $idKebun;


                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$idKebun."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);

        # Alamat & No Telp
        $query = selectQuery($dbname,'organisasi','alamat,telepon',
            "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);

        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 10;
        $path='images/logo.jpg';
        //$this->Image($path,$this->lMargin,$this->tMargin,70);	
        $this->Image($path,15,5,55);
                        $this->SetFont('Arial','B',9);
        $this->SetFillColor(255,255,255);	
        $this->SetX(80);   
        $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
        $this->SetX(80); 		
        $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
        $this->SetX(80); 			
        $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
        $this->Line($this->lMargin,$this->tMargin+($height*4),
            $this->lMargin+$width,$this->tMargin+($height*4));
        $this->Ln();

/*                $this->SetFont('Arial','B',12);
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanPengangkutan'],'',0,'L');
                        $this->Ln();
*/				$this->SetFont('Arial','',7);
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,$periode,'',0,'L');
                        $this->Ln();
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,$idKebun."-".$rOrg['namaorganisasi'],'',0,'L');



        $this->Ln();
                        $this->Ln();
        $this->SetFont('Arial','U',9);
        $this->Cell($width,$height, $_SESSION['lang']['laporanPengangkutan'],0,1,'C');	
        $this->Ln();	

        $this->SetFont('Arial','B',6);	
        $this->SetFillColor(220,220,220);

                        $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                        $this->Cell(14/100*$width,$height,$_SESSION['lang']['nospb'],1,0,'C',1);		
                        $this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
                        $this->Cell(7/100*$width,$height,$_SESSION['lang']['status'],1,0,'C',1);			
                        $this->Cell(4/100*$width,$height,$_SESSION['lang']['bjr'],1,0,'C',1);		
                        $this->Cell(5/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);
                        $this->Cell(7/100*$width,$height,$_SESSION['lang']['brondolan'],1,0,'C',1);		
                        $this->Cell(5/100*$width,$height,$_SESSION['lang']['mentah'],1,0,'C',1);		
                        $this->Cell(4/100*$width,$height,$_SESSION['lang']['busuk'],1,0,'C',1);		
                        $this->Cell(5/100*$width,$height,$_SESSION['lang']['matang'],1,0,'C',1);		
                        $this->Cell(7/100*$width,$height,$_SESSION['lang']['lewatmatang'],1,0,'C',1);					
                        $this->Cell(6/100*$width,$height,$_SESSION['lang']['kgbjr'],1,0,'C',1);	
                        $this->Cell(7/100*$width,$height,'Kg Potongan',1,0,'C',1);				
                        $this->Cell(6/100*$width,$height,Netto,1,0,'C',1);		
                        $this->Cell(6/100*$width,$height,Normal,1,0,'C',1);			
                        $this->Cell(6/100*$width,$height,$_SESSION['lang']['totalkg'],1,1,'C',1);					

    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
}
$pdf=new PDF('P','pt','A4');
        $pdf->lMargin=10;
        $pdf->rMargin=10;
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 10;
        $pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',6);
        /*$sDet="select a.nospb,a.tanggal,a.posting,b.* from ".$dbname.".kebun_spbht a inner join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc ";*/
        $sDet="select a.nospb,a.tanggal,a.posting from ".$dbname.".kebun_spbht a where tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc ";
        $qDet=mysql_query($sDet) or die(mysql_error());
        $row=mysql_num_rows($qDet);

        if($row>0)
        {
                while($rDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        $arrPost=array($_SESSION['lang']['belumposting'],$_SESSION['lang']['posting']);

                        $sSpbDet=
                        "select avg(a.bjr) as Bjr,sum(a.jjg) as Janjang,sum(a.brondolan) as Brondolan,sum(a.mentah) as Mentah,sum(a.busuk) as Busuk,sum(a.matang) as Matang,sum(a.lewatmatang) as Lewatmatang,sum(a.kgbjr) as kgBjr,sum(a.kgwb) as kGwb,sum(a.totalkg) as totaLkg,b.kgpotsortasi
                        from ".$dbname.".kebun_spbdt a
                        left join ".$dbname.".pabrik_timbangan b
                        on a.nospb=b.nospb
                        where a.nospb='".$rDet['nospb']."'";




                        $qSpbDet=mysql_query($sSpbDet) or die(mysql_error());
                        $rSpbDet=mysql_fetch_assoc($qSpbDet);

                        $normal=$rSpbDet['kGwb']-$rSpbDet['kgpotsortasi'];

                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(14/100*$width,$height,$rDet['nospb'],1,0,'L',1);		
                        $pdf->Cell(7/100*$width,$height,tanggalnormal($rDet['tanggal']),1,0,'C',1);		
                        $pdf->Cell(7/100*$width,$height,$arrPost[$rDet['posting']],1,0,'L',1);			
                        $pdf->Cell(4/100*$width,$height,number_format($rSpbDet['Bjr'],2),1,0,'R',1);		
                        $pdf->Cell(5/100*$width,$height,number_format($rSpbDet['Janjang']),1,0,'R',1);
                        $pdf->Cell(7/100*$width,$height,number_format($rSpbDet['Brondolan'],2),1,0,'R',1);		
                        $pdf->Cell(5/100*$width,$height,number_format($rSpbDet['Mentah']),1,0,'R',1);		
                        $pdf->Cell(4/100*$width,$height,number_format($rSpbDet['Busuk']),1,0,'R',1);		
                        $pdf->Cell(5/100*$width,$height,number_format($rSpbDet['Matang']),1,0,'R',1);		
                        $pdf->Cell(7/100*$width,$height,number_format($rSpbDet['Lewatmatang']),1,0,'R',1);	
                        $pdf->Cell(6/100*$width,$height,number_format($rSpbDet['kgBjr'],2),1,0,'R',1);
                        //kg potongan		
                        $pdf->Cell(7/100*$width,$height,number_format($rSpbDet['kgpotsortasi'],2),1,0,'R',1);	
                        //netto		
                        $pdf->Cell(6/100*$width,$height,number_format($rSpbDet['kGwb'],2),1,0,'R',1);					
                        //normal
                        $pdf->Cell(6/100*$width,$height,number_format($normal),1,0,'R',1);	
                        $pdf->Cell(6/100*$width,$height,number_format($rSpbDet['totaLkg'],2),1,1,'R',1);


                $granjjg+=$rSpbDet['Janjang'];
                $granbrondolan+=$rSpbDet['Brondolan'];
                $granmentah+=$rSpbDet['Mentah'];
                $granbusuk+=$rSpbDet['Busuk'];
                $granmatang+=$rSpbDet['Matang'];

                $granlewatmatang+=$rSpbDet['Lewatmatang'];
                $grankgbjr+=$rSpbDet['kgBjr'];
                $grankgpot+=$rSpbDet['kgpotsortasi'];
                $grankgwb+=$rSpbDet['kGwb'];
                $grannormal+=$normal;

                $grantotalkg+=$rSpbDet['totaLkg'];


                }
                $pdf->SetFillColor(220,220,220);
                $pdf->cell(35/100*$width,$height,'TOTAL',1,0,'C',1);
                $pdf->Cell(5/100*$width,$height,number_format($granjjg,2),1,0,'R',1);
                $pdf->Cell(7/100*$width,$height,number_format($granbrondolan,2),1,0,'R',1);		
                $pdf->Cell(5/100*$width,$height,number_format($granmentah,2),1,0,'R',1);		
                $pdf->Cell(4/100*$width,$height,number_format($granbusuk,2),1,0,'R',1);		
                $pdf->Cell(5/100*$width,$height,number_format($granmatang,2),1,0,'R',1);	

                $pdf->Cell(7/100*$width,$height,number_format($granlewatmatang,2),1,0,'R',1);	
                $pdf->Cell(6/100*$width,$height,number_format($grankgbjr,2),1,0,'R',1);
                $pdf->Cell(7/100*$width,$height,number_format($grankgpot,2),1,0,'R',1);		
                $pdf->Cell(6/100*$width,$height,number_format($grankgwb,2),1,0,'R',1);					
                $pdf->Cell(6/100*$width,$height,number_format($grannormal,2),1,0,'R',1);

                $pdf->Cell(6/100*$width,$height,number_format($grantotalkg,2),1,1,'R',1);


        }
        else
        {
                $pdf->Cell(96/100*$width,$height,"Not Found",1,1,'C',1);
        }

$pdf->Output();
break;
case'excel':
$periode=$_GET['periode'];
$idKebun=$_GET['idKebun'];

if($periode=='')
        {
                echo "warning : Periode masih kosong";
                exit();	
        }


        if($periode=='')
        {
                echo "warning : Periode masih kosong";
                exit();	
        }



$stream.="Laporan pengangkutan panen ". $idKebun." Periode ". $periode."
    <table class=sortable  border=1>
<thead>
<tr class=rowheader>
<td>".substr($_SESSION['lang']['nomor'],0,2).".</td>
<td>".$_SESSION['lang']['nospb']."</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['status']."</td>
<td>BJR AKTUAL</td>
<td>".$_SESSION['lang']['janjang']."</td>
<td>".$_SESSION['lang']['brondolan']."</td>
<td>".$_SESSION['lang']['mentah']."</td>
<td>".$_SESSION['lang']['busuk']."</td>
<td>".$_SESSION['lang']['matang']."</td>
<td>".$_SESSION['lang']['lewatmatang']."</td>
<td>".$_SESSION['lang']['kgbjr']."</td>
<td>Kg Potongan</td>
<td>Netto</td>
<td>Normal</td>
<td>".$_SESSION['lang']['totalkg']."</td>
</tr>
</thead><tbody>";


/*$sql="select a.nospb,a.tanggal,a.posting,b.* from ".$dbname.".kebun_spbht a inner join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."'  order by a.tanggal asc";*/
$sql="select a.nospb,a.tanggal,a.posting from ".$dbname.".kebun_spbht a where tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc";
//echo $sql;
$query=mysql_query($sql) or die(mysql_error());
$row=mysql_num_rows($query);
if($row>0)
{
        while($res=mysql_fetch_assoc($query))
        {
                $no+=1;
                $sSpbDet=
                        "select avg(a.bjr) as Bjr,sum(a.jjg) as Janjang,sum(a.brondolan) as Brondolan,sum(a.mentah) as Mentah,sum(a.busuk) as Busuk,sum(a.matang) as Matang,sum(a.lewatmatang) as Lewatmatang,sum(a.kgbjr) as kgBjr,sum(a.kgwb) as kGwb,sum(a.totalkg) as totaLkg,b.kgpotsortasi
                        from ".$dbname.".kebun_spbdt a
                        left join ".$dbname.".pabrik_timbangan b
                        on a.nospb=b.nospb
                        where a.nospb='".$res['nospb']."'";

                        //echo $sSpbDet;exit();

        //	echo $sSpbDet;
                $qSpbDet=mysql_query($sSpbDet) or die(mysql_error());
                $rSpbDet=mysql_fetch_assoc($qSpbDet);
                $srow="select blok from ".$dbname.".kebun_spbdt where nospb='".$res['nospb']."'";
                $qrow=mysql_query($srow) or die(mysql_error($conn));
                $rRow=mysql_num_rows($qrow);
                @$bjrR=$rSpbDet['Bjr']/$rRow;
                $arrPost=array($_SESSION['lang']['belumposting'],$_SESSION['lang']['posting']);
                $arr="nospb"."##".$res['nospb'];

                $normal=$rSpbDet['kGwb']-$rSpbDet['kgpotsortasi'];

           $stream.="<tr class=rowcontent onclick=\"zDetail(event,'kebun_slave_2pengangkutan.php','".$arr."')\" style='cursor:pointer;'>
                <td>".$no."</td>
                <td>".$res['nospb']."</td>
                <td>".tanggalnormal($res['tanggal'])."</td>
                <td>".$arrPost[$res['posting']]."</td>		
                <td align=\"right\">".number_format($rSpbDet['kGwb']/$rSpbDet['Janjang'],2)."</td>
				
                <td align=\"right\">".number_format($rSpbDet['Janjang'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Brondolan'],2)."</td>
                <td align=\"right\">".number_format($rSpbDet['Mentah'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Busuk'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Matang'])."</td>
                <td align=\"right\">".number_format($rSpbDet['Lewatmatang'])."</td>
                <td align=\"right\">".number_format($rSpbDet['kgBjr'],2)."</td>
                <td align=\"right\">".number_format($rSpbDet['kgpotsortasi'],2)."</td>
                <td align=\"right\">".number_format($rSpbDet['kGwb'],2)."</td>
                <td align=\"right\">".number_format($normal,2)."</td>
                <td align=\"right\">".number_format($rSpbDet['totaLkg'],2)."</td>
                </tr>
                ";


                $granjjg+=$rSpbDet['Janjang'];
                $granbrondolan+=$rSpbDet['Brondolan'];
                $granmentah+=$rSpbDet['Mentah'];
                $granbusuk+=$rSpbDet['Busuk'];
                $granmatang+=$rSpbDet['Matang'];

                $granlewatmatang+=$rSpbDet['Lewatmatang'];
                $grankgbjr+=$rSpbDet['kgBjr'];
                $grankgpot+=$rSpbDet['kgpotsortasi'];
                $grankgwb+=$rSpbDet['kGwb'];
                $grannormal+=$normal;

                $grantotalkg+=$rSpbDet['totaLkg'];

        }
       $stream.="
                <thead><tr>
                        <td colspan=5 align=center>".$_SESSION['lang']['total']."</td>
                        <td align=right>".number_format($granjjg,2)."</td>
                        <td align=right>".number_format($granbrondolan,2)."</td>
                        <td align=right>".number_format($granmentah,2)."</td>
                        <td align=right>".number_format($granbusuk,2)."</td>
                        <td align=right>".number_format($granmatang,2)."</td>

                        <td align=right>".number_format($granlewatmatang,2)."</td>
                        <td align=right>".number_format($grankgbjr,2)."</td>
                        <td align=right>".number_format($grankgpot,2)."</td>
                        <td align=right>".number_format($grankgwb,2)."</td>
                        <td align=right>".number_format($grannormal,2)."</td>

                        <td align=right>".number_format($grantotalkg,2)."</td>
                </tr></thead>

        ";

    }


    else
    {
            $stream.="<tr class=rowcontent align=center><td colspan=14>Not Found</td></tr>";
    }
    $stream.="</tbody>";
    $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

                $nop_="laporanPengangkutanPanen";
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
case'getDetail':



$nospb=$_GET['nospb'];


 class PDF extends FPDF
{
    function Header() {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
                        global $nospb;


                        $sHed="select  a.nospb,a.kodeorg,a.tanggal,a.posting from ".$dbname.".kebun_spbht a where a.nospb='".$nospb."'";
                        $qHead=mysql_query($sHed) or die(mysql_error());
                        $rHead=mysql_fetch_assoc($qHead);

                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rHead['kodeorg']."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);

        # Alamat & No Telp
        $query = selectQuery($dbname,'organisasi','alamat,telepon',
            "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);

        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
                        $this->SetFont('Arial','',8);
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['nospb'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,$rHead['nospb'],'',0,'L');
                        $this->Ln();
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,tanggalnormal($rHead['tanggal']),'',0,'L');
                        $this->Ln();
                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                        $this->Cell(5,$height,':','',0,'L');
                        $this->Cell(45/100*$width,$height,$rOrg['namaorganisasi'],'',0,'L');


        $this->Line($this->lMargin,$this->tMargin+($height*4),
            $this->lMargin+$width,$this->tMargin+($height*4));
        $this->Ln();

        $this->Ln();
                        $this->Ln();
        $this->SetFont('Arial','U',9);
        $this->Cell($width,$height, $_SESSION['lang']['laporanPengangkutan'],0,1,'C');	
        $this->Ln();	

        $this->SetFont('Arial','B',8);	
        $this->SetFillColor(220,220,220);

                        $this->Cell(3/100*$width,$height,'No',1,0,'C',1);			
                        $this->Cell(10/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);	
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['bjr'],1,0,'C',1);		
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);
                        $this->Cell(9/100*$width,$height,$_SESSION['lang']['brondolan'],1,0,'C',1);		
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['mentah'],1,0,'C',1);		
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['busuk'],1,0,'C',1);		
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['matang'],1,0,'C',1);		
                        $this->Cell(10/100*$width,$height,$_SESSION['lang']['lewatmatang'],1,0,'C',1);					
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['kgbjr'],1,0,'C',1);					
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['kgwb'],1,0,'C',1);	
                        $this->Cell(8/100*$width,$height,'SPB WB',1,0,'C',1);				
                        $this->Cell(8/100*$width,$height,$_SESSION['lang']['totalkg'],1,1,'C',1);					

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
$height = 10;
        $pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',6);
        /*$sDet="select a.nospb,a.tanggal,a.posting,b.* from ".$dbname.".kebun_spbht a inner join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb where a.tanggal like '%".$periode."%' and a.kodeorg='".$idKebun."' order by a.tanggal asc ";*/
        $sDet="select * from ".$dbname.".kebun_spbdt where nospb='".$nospb."'";
        //echo $sDet;exit();
        $qDet=mysql_query($sDet) or die(mysql_error());
        $row=mysql_num_rows($qDet);

        if($row>0)
        {
                while($rSpbDet=mysql_fetch_assoc($qDet))
                {
                        $no+=1;
                        $arrPost=array($_SESSION['lang']['belumposting'],$_SESSION['lang']['posting']);

                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);			
                        $pdf->Cell(10/100*$width,$height,$rSpbDet['blok'],1,0,'L',1);	
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['bjr'],2),1,0,'R',1);		
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['jjg'],2),1,0,'R',1);
                        $pdf->Cell(9/100*$width,$height,number_format($rSpbDet['brondolan'],2),1,0,'R',1);		
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['mentah'],2),1,0,'R',1);		
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['busuk'],2),1,0,'R',1);		
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['matang'],2),1,0,'R',1);		
                        $pdf->Cell(10/100*$width,$height,number_format($rSpbDet['lewatmatang'],2),1,0,'R',1);	
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['kgbjr'],2),1,0,'C',1);					
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['kgwb'],2),1,0,'C',1);	
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['kgwb']/$rSpbDet['jjg'],2),1,0,'C',1);					
                        $pdf->Cell(8/100*$width,$height,number_format($rSpbDet['totalkg'],2),1,1,'C',1);
                }
        }
        else
        {
                $pdf->Cell(68/100*$width,$height,"Not Found",1,1,'C',1);
        }
$pdf->Output();

break;
default:
break;
}

?>