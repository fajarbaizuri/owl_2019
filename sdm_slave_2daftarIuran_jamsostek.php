<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
//$periode=$_POST['periode'];
//$period=$_POST['period'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['periode']==''?$periodeGaji=$_GET['periode']:$periodeGaji=$_POST['periode'];

$_POST['tipeKary']==''?$tipeKary=$_GET['tipeKary']:$tipeKary=$_POST['tipeKary'];
$_GET['sistemGaji']==''?$sistemGaji=$_POST['sistemGaji']: $sistemGaji=$_GET['sistemGaji'];


//ambil query untuk data karyawan
	
        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
        {
            if($kdOrg!='')
            {
                //$where=" lokasitugas='".$kdOrg."'";
                $where=" lokasitugas!=''";
            }
            else
            {
                exit("Error:Unit Tidak Boleh Kosong");
            }
        }	
        else
        {
            $kdOrg=$_SESSION['empl']['lokasitugas'];
            $where=" lokasitugas!=''";
           // $where=" lokasitugas='".$kdOrg."'";
        }
        if($tipeKary!='')
        {
            $where.="  and (tipekaryawan='".$tipeKary."' and tipekaryawan NOT IN ('2','5'))";
        }
        else
        {
             $where.=" and tipekaryawan NOT IN ('2','5')";
        }
        if($sistemGaji!='')
        {
            $where.=" and sistemgaji='".$sistemGaji."'";
            $addTmbh=" and sistemgaji='".$sistemGaji."'";
        }
//	if($sistemGaji=='All')$wherez="";        
//	if($sistemGaji=='Bulanan')$wherez=" and sistemgaji = 'Bulanan'";        
//	if($sistemGaji=='Harian')$wherez=" and sistemgaji = 'Harian'";        

$sGetKary="select a.karyawanid,b.tipe,a.namakaryawan,tanggallahir,nik,jms from ".$dbname.".datakaryawan a 
           left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id where 
           ".$where."   order by namakaryawan asc";    
//echo $sGetKary; //exit;
$rGetkary=fetchData($sGetKary);
foreach($rGetkary as $row => $kar)
{
    $resData[$kar['karyawanid']]=$kar['karyawanid'];
    $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    $nmTipe[$kar['karyawanid']]=$kar['tipe'];
    $tglLahir[$kar['karyawanid']]=$kar['tanggallahir'];
    $dNik[$kar['karyawanid']]=$kar['nik'];
    $dJamsos[$kar['karyawanid']]=$kar['jms'];
}  
$sGapok="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_5gajipokok  WHERE  idkomponen in (1,2,30,31) group by karyawanid order by karyawanid asc";
$qGapok=mysql_query($sGapok) or die(mysql_error($sGapok));
while($rGapok=mysql_fetch_assoc($qGapok))
{
    $dtGapok[$rGapok['karyawanid']]=$rGapok['gapok'];
}
switch($proses)
{
	case'preview':

            if($periodeGaji=='')
            {
                exit("Error:Periode Tidak Boleh Kosong");
            }
            //$sList="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_5gajipokok where karyawanid in (select distinct karyawanid from ".$dbname.".sdm_gaji_vw where periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."'  ".$addTmbh." ) and idkomponen in (1,2,30,31)";
            $sList="SELECT distinct karyawanid FROM  ".$dbname.".sdm_gaji_vw   WHERE  idkomponen in (1,2,30,31) and periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' ".$addTmbh." order by karyawanid asc";
//            $sList="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_5gajipokok 
//                where periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' and idkomponen in (1,2,30,31) ".$addTmbh." group by karyawanid";
            //exit("Error".$sList);
            $data=fetchData($sList);
	$tab.="<table cellspacing='1' border='0' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td>No</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['tanggallahir']."</td>
        <td>".$_SESSION['lang']['tipekaryawan']."</td>
        <td>".$_SESSION['lang']['nik']."</td>
        <td>".$_SESSION['lang']['nokpj']."</td>
        <td>".$_SESSION['lang']['gaji']."</td>
        <td>".$_SESSION['lang']['potongan']." ".$_SESSION['lang']['karyawan']."</td>
        <td>".$_SESSION['lang']['perusahaan']."</td></tr></thead>
	<tbody>";
	foreach($data as $brsData => $rData)
        {
            $no+=1;
            if($resData[$rData['karyawanid']]!=''||$rData['karyawanid']=='')
            {
            $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$namakar[$rData['karyawanid']]."</td>
            <td>".tanggalnormal($tglLahir[$rData['karyawanid']])."</td>  
            <td>".$nmTipe[$rData['karyawanid']]."</td>
            <td>".$dNik[$rData['karyawanid']]."</td>
            <td>".$dJamsos[$rData['karyawanid']]."</td>
            <td align=right>".number_format($dtGapok[$rData['karyawanid']],2)."</td>
            <td align=right>".number_format($dtGapok[$rData['karyawanid']]*0.02,2)."</td>
            <td align=right>".number_format(($dtGapok[$rData['karyawanid']]*6.54)/100,2)."</td>
            </tr>";
            }
        }
        
	
	$tab.="</tbody></table>";
        echo $tab;
	break;
	case'pdf':
//        
//	$kdeOrg=$_GET['kdeOrg'];
//	$kdOrg=$_GET['kdOrg'];
//	$periodeGaji=$_GET['periode'];
//        $tipeKary=$_GET['tipeKary'];
       
	if($periodeGaji=='')
        {
            exit("Error:Periode Tidak Boleh Kosong");
        }
        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
        {
            if($kdOrg!='')
            {
               // $where=" lokasitugas='".$kdOrg."'";
                $where=" lokasitugas!=''";
            }
            else
            {
                exit("Error:Unit Tidak Boleh Kosong");
            }
        }	
        else
        {
            $kdOrg=$_SESSION['empl']['lokasitugas'];
           // $where=" lokasitugas='".$kdOrg."'";
            $where=" lokasitugas!=''";
        }
        if($tipeKary!='')
        {
            $where.=" and tipekaryawan='".$tipeKary."'";
        }
        if($sistemGaji!='')
        {
            $where.=" and sistemgaji='".$sistemGaji."'";
            $addTmbh=" and sistemgaji='".$sistemGaji."'";
        }
	
	
	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $periodeGaji;
				global $kdOrg;
				global $tglLahir;
				global $jmlHari;
				global $namakar;
                                global $tipeKary;
				global $sistemGaji;
                                global $nmTipe;
                                global $dNik;
                                global $dJamsos;
                                global $addTmbh;
                                global $resData;
			
				$cols=247.5;
			    # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                $path='images/logo.jpg';
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                
                $this->SetFont('Arial','B',10);
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['dafJams']." ".$sistemGaji,'',0,'L');
				$this->Ln();
				$this->Ln();
				
				$this->Cell($width,$height,strtoupper($_SESSION['lang']['dafJams']),'',0,'C');
				$this->Ln();
				$this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :".$periodeGaji,'',0,'C');
				$this->Ln();
				$this->Ln();
              	$this->SetFont('Arial','B',7);
                $this->SetFillColor(220,220,220);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
			        $this->Cell(20/100*$width,$height,$_SESSION['lang']['namakaryawan'],1,0,'C',1);	
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggallahir'],1,0,'C',1);	
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['tipekaryawan'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['nik'],1,0,'C',1);	
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['nokpj'],1,0,'C',1);	
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['gaji'],1,0,'C',1);	
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['potongan']." ".$_SESSION['lang']['karyawan'],1,0,'C',1);	
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['perusahaan'],1,1,'C',1);
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
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
		$subtot=array();
		//ambil query untuk data karyawan
                $sList="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_gaji_vw 
                where periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' and idkomponen in (1,2,30,31) ".$addTmbh." group by karyawanid";
                //exit("Error".$sList);
                $data=fetchData($sList);
                foreach($data as $brsData => $rData)
                {
                    $no+=1;
                    if($resData[$rData['karyawanid']]!=''||$rData['karyawanid']=='')
                    {
                        $pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
                        $pdf->Cell(20/100*$width,$height,$namakar[$rData['karyawanid']],1,0,'L',1);	
                        $pdf->Cell(10/100*$width,$height,tanggalnormal($tglLahir[$rData['karyawanid']]),1,0,'C',1);	
                        $pdf->Cell(10/100*$width,$height,$nmTipe[$rData['karyawanid']],1,0,'L',1);	
                        $pdf->Cell(8/100*$width,$height,$dNik[$rData['karyawanid']],1,0,'L',1);	
                        $pdf->Cell(15/100*$width,$height,$dJamsos[$rData['karyawanid']],1,0,'L',1);	
                        $pdf->Cell(10/100*$width,$height,number_format($dtGapok[$rData['karyawanid']],2),1,0,'R',1);	
                        $pdf->Cell(15/100*$width,$height,number_format($dtGapok[$rData['karyawanid']]*0.02,2),1,0,'R',1);	
                        $pdf->Cell(10/100*$width,$height,number_format(($dtGapok[$rData['karyawanid']]*6.54)/100,2),1,1,'R',1);
                    }
                }

		
        $pdf->Output();

	break;
	case'excel':
        $periodeGaji=$_GET['periode'];
	
        $tipeKary=$_GET['tipeKary'];
        $sistemGaji=$_GET['sistemGaji'];
        if($periodeGaji=='')
        {
            exit("Error:Periode Tidak Boleh Kosong");
        }
            $sList="select sum(jumlah) as gapok,karyawanid from ".$dbname.".sdm_gaji_vw 
                where periodegaji='".$periodeGaji."' and kodeorg='".$kdOrg."' and idkomponen in (1,2,30,31) ".$addTmbh." group by karyawanid";
            //exit("Error".$sList);
            $data=fetchData($sList);
	$tab.="<table cellspacing='1' border='1' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td bgcolor=#DEDEDE align=center>No</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggallahir']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipekaryawan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nik']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nokpj']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['gaji']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['potongan']." ".$_SESSION['lang']['karyawan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['perusahaan']."</td></tr></thead>
	<tbody>";
	foreach($data as $brsData => $rData)
        {
            $no+=1;
            if($resData[$rData['karyawanid']]!=''||$rData['karyawanid']=='')
            {
            $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$namakar[$rData['karyawanid']]."</td>
            <td>".tanggalnormal($tglLahir[$rData['karyawanid']])."</td>  
            <td>".$nmTipe[$rData['karyawanid']]."</td>
            <td>".$dNik[$rData['karyawanid']]."</td>
            <td>".$dJamsos[$rData['karyawanid']]."</td>
            <td align=right>".number_format($dtGapok[$rData['karyawanid']],2)."</td>
            <td align=right>".number_format($dtGapok[$rData['karyawanid']]*0.02,2)."</td>
            <td align=right>".number_format(($dtGapok[$rData['gapok']]*6.54)/100,2)."</td>
            </tr>";
            }
        }
        
	
	$tab.="</tbody></table>";
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="daftar_jamsostek_".$kdOrg;
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
	case'getTgl':
	if($periode!='')
	{
		$tgl=$periode;
		$tanggal=$tgl[0]."-".$tgl[1];
	}
	elseif($period!='')
	{
		$tgl=$period;
		$tanggal=$tgl[0]."-".$tgl[1];
	}
        if($kdUnit=='')
        {
            $kdUnit=$_SESSION['empl']['lokasitugas'];
        }
	$sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit,0,4)."' and periode='".$tanggal."' ";
	//echo"warning".$sTgl;
	$qTgl=mysql_query($sTgl) or die(mysql_error());
	$rTgl=mysql_fetch_assoc($qTgl);
	echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
	break;
	case'getKry':
	$optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	if(strlen($kdeOrg)>4)
	{
		$where=" subbagian='".$kdeOrg."'";
	}
	else
	{
		$where=" lokasitugas='".$kdeOrg."' and (subbagian='0' or subbagian is null)";
	}
	$sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
	$qKry=mysql_query($sKry) or die(mysql_error());
	while($rKry=mysql_fetch_assoc($qKry))
	{
		$optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
	}
	$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$kdeOrg."'";
	$qPeriode=mysql_query($sPeriode) or die(mysql_error());
	while($rPeriode=mysql_fetch_assoc($qPeriode))
	{
		$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
	}
	//echo $optPeriode;
	echo $optKry."###".$optPeriode;
	break;
	case'getPeriode':
	$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$kdUnit."'";
	$qPeriode=mysql_query($sPeriode) or die(mysql_error());
	while($rPeriode=mysql_fetch_assoc($qPeriode))
	{
		$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
	}
	echo $optPeriode;
	break;
	default:
	break;
}
?>