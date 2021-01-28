<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
//$arr="##kdOrg##bagId##periode##karyawanId";
$_POST['kdOrg']!=''?$kdPt=$_POST['kdOrg']:$kdPt=$_GET['kdOrg'];
$_POST['periode']!=''?$periode=$_POST['periode']:$periode=$_GET['periode'];
$_POST['bagId']!=''?$bagian=$_POST['bagId']:$bagian=$_GET['bagId'];
$_POST['karyawanId']!=''?$karyawanId=$_POST['karyawanId']:$karyawanId=$_GET['karyawanId'];
$_POST['stat']!=''?$stat=$_POST['stat']:$stat=$_GET['stat'];


//ambil query untuk data karyawan
	if($kdPt!='')
	{
            
		$where="  kodeorganisasi='".$kdPt."' and tipekaryawan=0";
	}
        else
        {
           exit("Error:Perusahaan Tidak Boleh Kosong");
        }
        if($karyawanId!='')
        {
            $where.=" and karyawanid='".$karyawanId."'";
        }
        if($bagian!='')
        {
            $where.=" and bagian='".$bagian."'";
        }
            $sGetKary="select a.karyawanid,b.namajabatan,a.namakaryawan,a.lokasitugas from ".$dbname.".datakaryawan a 
           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where
           ".$where." order by namakaryawan asc";    
//exit("Error".$sGetKary."__".$where);
$rGetkary=fetchData($sGetKary);
foreach($rGetkary as $row => $kar)
{
    $resData[$kar['karyawanid']][]=$kar['karyawanid'];
    $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    $nmJabatan[$kar['karyawanid']]=$kar['namajabatan'];
    $lokTugas[$kar['karyawanid']]=$kar['lokasitugas'];
}  
$optOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if($periode!='')
{
    $add=" and substring(tglpertanggungjawaban,1,7)='".$periode."'";
}
if($stat!='')
{
    $add.=" and lunas='".$stat."'";
}
switch($proses)
{
	case'getKaryawan':
            if($kdPt=='')
            {
                exit("Error:Perusahaan Tidak Boleh Kosong");
            }
            $optKary="<option value=''>".$_SESSION['lang']['all']."</option>";
            $sKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where."";
            $qKary=mysql_query($sKary) or die(mysql_error($conn));
            while($rKary=  mysql_fetch_assoc($qKary))
            {
                $optKary.="<option value=".$rKary['karyawanid'].">".$rKary['namakaryawan']."</option>";
            }
            echo $optKary;
        break;
        case'preview':
 	
        
        if(empty($resData))
        {
        exit("Error: Data Kosong");
        }
        else
        {
	$tab.="<table cellspacing='1' border='0' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggaldinas']."</td>
        <td>".$_SESSION['lang']['tanggalkembali']."</td>
        <td>Tgl Pertanggung Jawaban</td>
       
        <td>".$_SESSION['lang']['tujuan']." 1</td>
        <td>".$_SESSION['lang']['tujuan']." 2</td>
        <td>".$_SESSION['lang']['tujuan']." 3</td>
        <td>".$_SESSION['lang']['tujuan']." Lainnya</td>
        <td>".$_SESSION['lang']['jumlah']."</td>";
	$tab.="</tr></thead>
	<tbody>";
	foreach($resData as $brsDt =>$rData)
        {
            $sPjd="select a.*,sum(b.jumlah) as jmlhPjd,sum(b.jumlahhrd) as jmlhSetuju from ".$dbname.".sdm_pjdinasht a left join ".$dbname.".sdm_pjdinasdt b on a.notransaksi=b.notransaksi
                where  kodeorg='".$lokTugas[$rData[0]]."' and statushrd=1   ".$add." and a.karyawanid='".$rData[0]."' group by notransaksi";
            //exit("Error".$sPjd);
            $qPjd=mysql_query($sPjd) or die(mysql_error($conn));
            while($rPjd=mysql_fetch_assoc($qPjd))
            {
//                $sTgl="select distinct tanggal from ".$dbname.".sdm_pjdinasdt where notransaksi='".$rPjd['notransaksi']."' and jenisbiaya=1 order by tanggal desc";
//                $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
//                $rTgl=mysql_fetch_assoc($qTgl);
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$namakar[$rData[0]]."</td>";
            $tab.="<td>".$nmJabatan[$rData[0]]."</td>";
            $tab.="<td>".$rPjd['notransaksi']."</td>";
            $tab.="<td>".tanggalnormal($rPjd['tanggalperjalanan'])."</td>";
            $tab.="<td>".tanggalnormal($rPjd['tanggalkembali'])."</td>";
            $tab.="<td>".tanggalnormal($rPjd['tglpertanggungjawaban'])."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuan1']]."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuan2']]."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuan3']]."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuanlain']]."</td>";
            $tab.="<td align=right>".number_format($rPjd['jmlhSetuju'],2)."</td>";
            $tab.="</tr>";
            $jmlTot[$rPjd['karyawanid']]+=$rPjd['jmlhSetuju'];
            
            }
            if($jmlTot[$rData[0]]!=''||$jmlTot[$rData[0]]!=0)
            {
            $tab.="<tr class=rowcontent><td colspan=10 align=right>Total ".$namakar[$rData[0]]."</td><td  align=right>".number_format($jmlTot[$rData[0]],2)."</td></tr>";
            }
             $grandTot+=$jmlTot[$rData[0]];
        }
        $tab.="<tr class=rowcontent><td colspan=10 align=right>Grand Total </td><td  align=right>".number_format($grandTot,2)."</td></tr>";
	$tab.="</tbody></table>";
        
        echo $tab;
        }
        break;
	case'pdf':
        
	
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
				global $period;
				global $periode;
				global $kdOrg;
				global $kdeOrg;
				global $tgl1;
				global $tgl2;
				global $where;
				global $jmlHari;
				global $test;
				global $klmpkAbsn;
                                global $tipeKary;
				global $resData;
				
				
				$jmlHari=$jmlHari*1.5;
				$cols=247.5;
			    # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 15;
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
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['lapPjd'],'',0,'L');
				$this->Ln();
				$this->Ln();
				
				//$this->Cell($width,$height,strtoupper("Rekapitulasi Absensi Karyawan"),'',0,'C');
				//$this->Ln();
				//$this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
				//$this->Ln();
				//$this->Ln();
              	$this->SetFont('Arial','B',6);
                $this->SetFillColor(220,220,220);
                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                $this->Cell(11/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);		
                //$this->Cell(10/100*$width,$height,$_SESSION['lang']['jabatan'],1,0,'C',1);	
                $this->Cell(10/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggaldinas'],1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggalkembali'],1,0,'C',1);
                $this->Cell(11/100*$width,$height,"Tgl Pertanggung Jawaban",1,0,'C',1);
                $this->Cell(7/100*$width,$height,$_SESSION['lang']['tujuan']."1",1,0,'C',1);
                $this->Cell(7/100*$width,$height,$_SESSION['lang']['tujuan']."2",1,0,'C',1);
                $this->Cell(7/100*$width,$height,$_SESSION['lang']['tujuan']."3",1,0,'C',1);
                $this->Cell(21/100*$width,$height,$_SESSION['lang']['tujuan']." Lainnya",1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
	foreach($resData as $brsDt =>$rData)
        {
            $sPjd="select a.*,sum(b.jumlah) as jmlhPjd,sum(b.jumlahhrd) as jmlhSetuju from ".$dbname.".sdm_pjdinasht a left join ".$dbname.".sdm_pjdinasdt b on a.notransaksi=b.notransaksi
                where  kodeorg='".$lokTugas[$rData[0]]."'  ".$add." and a.karyawanid='".$rData[0]."'group by notransaksi";
            //exit("Error".$sPjd);
            $qPjd=mysql_query($sPjd) or die(mysql_error($conn));
            while($rPjd=mysql_fetch_assoc($qPjd))
            {
                $no+=1;
                $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                $pdf->Cell(11/100*$width,$height,$namakar[$rData[0]],1,0,'L',1);		
                //$this->Cell(10/100*$width,$height,$_SESSION['lang']['jabatan'],1,0,'C',1);	
                $pdf->Cell(10/100*$width,$height,$rPjd['notransaksi'],1,0,'C',1);
                $pdf->Cell(8/100*$width,$height,tanggalnormal($rPjd['tanggalperjalanan']),1,0,'C',1);
                $pdf->Cell(8/100*$width,$height,tanggalnormal($rPjd['tanggalkembali']),1,0,'C',1);
                $pdf->Cell(11/100*$width,$height,tanggalnormal($rPjd['tglpertanggungjawaban']),1,0,'C',1);
                $pdf->Cell(7/100*$width,$height,$rPjd['tujuan1'],1,0,'C',1);
                $pdf->Cell(7/100*$width,$height,$rPjd['tujuan2'],1,0,'C',1);
                $pdf->Cell(7/100*$width,$height,$rPjd['tujuan3'],1,0,'C',1);
                $pdf->Cell(21/100*$width,$height,$rPjd['tujuanlain'],1,0,'L',1);
                $pdf->Cell(8/100*$width,$height,number_format($rPjd['jmlhSetuju'],2),1,1,'R',1);
                $jmlTot[$rPjd['karyawanid']]+=$rPjd['jmlhSetuju'];
            }
            if($jmlTot[$rData[0]]!=''||$jmlTot[$rData[0]]!=0)
            {
                $pdf->Cell(93/100*$width,$height,"Total ".$namakar[$rData[0]],1,0,'R',1);
                $pdf->Cell(8/100*$width,$height,number_format($jmlTot[$rData[0]],2),1,1,'R',1);
                $no=0;
            //$tab.="<tr class=rowcontent><td colspan=10 align=right>Total ".$namakar[$rData[0]]."</td><td  align=right>".number_format($jmlTot[$rData[0]],2)."</td></tr>";
            }
            $grandTot+=$jmlTot[$rData[0]];
        }
        $pdf->Cell(93/100*$width,$height,"Grand Total ",1,0,'R',1);
        $pdf->Cell(8/100*$width,$height,number_format($grandTot,2),1,1,'R',1);
	$pdf->Output();

	break;
	case'excel':
	if($periode!='')
        {
            $add=" and substring(tglpertanggungjawaban,1,7)='".$periode."'";
        }
         if(empty($resData))
        {
        exit("Error: Data Kosong");
        }
        else
        {
	$tab.="<table cellspacing='1' border='1' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggaldinas']."</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggalkembali']."</td>
        <td bgcolor=#DEDEDE align=center>Tgl Pertanggung Jawaban</td>
       
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." 1</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." 2</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." 3</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']." Lainnya</td>
        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>";
	$tab.="</tr></thead>
	<tbody>";
	foreach($resData as $brsDt =>$rData)
        {
            $sPjd="select a.*,sum(b.jumlah) as jmlhPjd,sum(b.jumlahhrd) as jmlhSetuju from ".$dbname.".sdm_pjdinasht a left join ".$dbname.".sdm_pjdinasdt b on a.notransaksi=b.notransaksi
                where  kodeorg='".$lokTugas[$rData[0]]."'  ".$add." and a.karyawanid='".$rData[0]."'group by notransaksi";
            //exit("Error".$sPjd);
            $qPjd=mysql_query($sPjd) or die(mysql_error($conn));
            while($rPjd=mysql_fetch_assoc($qPjd))
            {
//                $sTgl="select distinct tanggal from ".$dbname.".sdm_pjdinasdt where notransaksi='".$rPjd['notransaksi']."' and jenisbiaya=1 order by tanggal desc";
//                $qTgl=mysql_query($sTgl) or die(mysql_error($conn));
//                $rTgl=mysql_fetch_assoc($qTgl);
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$namakar[$rData[0]]."</td>";
            $tab.="<td>".$nmJabatan[$rData[0]]."</td>";
            $tab.="<td>".$rPjd['notransaksi']."</td>";
            $tab.="<td>".tanggalnormal($rPjd['tanggalperjalanan'])."</td>";
            $tab.="<td>".tanggalnormal($rPjd['tanggalkembali'])."</td>";
            $tab.="<td>".tanggalnormal($rPjd['tglpertanggungjawaban'])."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuan1']]."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuan2']]."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuan3']]."</td>";
            $tab.="<td>".$optOrg[$rPjd['tujuanlain']]."</td>";
            $tab.="<td align=right>".number_format($rPjd['jmlhSetuju'],2)."</td>";
            $tab.="</tr>";
            $jmlTot[$rPjd['karyawanid']]+=$rPjd['jmlhSetuju'];
            
            }
            if($jmlTot[$rData[0]]!=''||$jmlTot[$rData[0]]!=0)
            {
            $tab.="<tr class=rowcontent><td colspan=10 align=right>Total ".$namakar[$rData[0]]."</td><td  align=right>".number_format($jmlTot[$rData[0]],2)."</td></tr>";
            }
             $grandTot+=$jmlTot[$rData[0]];
        }
        $tab.="<tr class=rowcontent><td colspan=10 align=right>Grand Total</td><td  align=right>".number_format($grandTot,2)."</td></tr>";
	$tab.="</tbody></table>";
        }
	
	
	
	
	
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="rekapPerjalananDinas__".$kdPt;
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