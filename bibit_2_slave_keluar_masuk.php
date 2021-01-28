<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['kdBatch']==''?$kdBatch=$_GET['kdBatch']:$kdBatch=$_POST['kdBatch'];
$optnmSup=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if($kdUnit=='')
{
    exit("Error:Kode Unit Tidak Boleh Kosong");
}
switch($proses)
{
	case'preview':
          $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td>".$_SESSION['lang']['batch']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['saldo']."</td>
            <td>".$_SESSION['lang']['supplier']."</td>
             <td>".$_SESSION['lang']['tgltanam']."</td>   
            <td>".$_SESSION['lang']['umur']." ".substr($_SESSION['lang']['afkirbibit'],5)."</td>
            </tr>
            </thead><tbody id=containDataStock>";
            if($kdUnit!='')
            {
                $where="  kodeorg like '%".$kdUnit."%'";
            }
            if($kdBatch!='')
            {
                $where.=" and batch='".$kdBatch."'";
            }
	        $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where ".$where." group by batch,kodeorg order by tanggal desc ";
               // exit("error".$sData);
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=time();//tanggal pembuatan dokumen
                    /*
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    */
                    
                    $jmlHari=($endtime-$starttime)/(60*60*24*30);
                    
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
                    $tab.="<td>".$optnmSup[$rDataBatch['supplerid']]."</td>";
                    $tab.="<td>".tanggalnormal($rDataBatch['tanggaltanam'])."</td>";
                    $tab.="<td align=right>".number_format($jmlHari,2)."</td>";
                    $tab.="</tr>";
                }
                $tab.="</tbody></table>";
                echo $tab;
	break;
	case'pdf':
	
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                global $kdUnit;
                global $kdBatch;
                global $rData;
                global $optNm;
				
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
                
                $this->SetFont('Arial','B',12);
			//	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanKendAb'],'',0,'L');
			//	$this->Ln();
				$this->SetFont('Arial','',8);
					
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$optNm[$kdUnit],'',0,'L');
					$this->Ln();
                                        if($kdBatch=='')
                                        {
                                            $kdBatchdt=$_SESSION['lang']['all'];
                                        }
                                        else
                                        {
                                            $kdBatchdt=$kdBatch;
                                        }
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['batch'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$kdBatchdt,'',0,'L');
					$this->Ln();					
			
				
			
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height, $_SESSION['lang']['laporanStockBIbit'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);
                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['batch'],1,0,'C',1);	
                $this->Cell(17/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);		
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['saldo'],1,0,'C',1);		
                $this->Cell(11/100*$width,$height,$_SESSION['lang']['supplier'],1,0,'C',1);		
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tgltanam']." ".substr($_SESSION['lang']['afkirbibit'],5),1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['umur'],1,1,'C',1);	
                				
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
                if($kdBatch!='')
                {
                    $where=" and batch='".$kdBatch."'";
                }
	        $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where." group by batch,kodeorg order by tanggal desc ";
              // exit("error".$sData);
                
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=strtotime($tglSkrng);//tanggal pembuatan dokumen
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    $no+=1;
               
                        
                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);		
			$pdf->Cell(8/100*$width,$height,$rData['batch'],1,0,'C',1);		
			$pdf->Cell(17/100*$width,$height,$optNm[$rData['kodeorg']],1,0,'C',1);		
			$pdf->Cell(8/100*$width,$height,number_format($rData['jumlah'],0),1,0,'R',1);
			$pdf->Cell(11/100*$width,$height,$optnmSup[$rDataBatch['supplerid']],1,0,'C',1);	
			$pdf->Cell(8/100*$width,$height,tanggalnormal($rDataBatch['tanggaltanam']),1,0,'C',1);	
			$pdf->Cell(8/100*$width,$height,$jmlHari,1,1,'C',1);	
			
                }

        $pdf->Output();
	break;
	case'excel':
	   $tab.="
            <table>
            <tr><td colspan=7 align=center>".$_SESSION['lang']['laporanStockBIbit']."</td></tr>
            ".$tbl."
            <tr><td colspan=7></td><td></td></tr>
            </table>
            <table cellpadding=1 cellspacing=1 border=1 class=sortable>
            <thead>
            <tr class=rowheader>
            <td bgcolor=#DEDEDE align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['batch']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldo']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tgltanam']."</td>   
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['umur']." ".substr($_SESSION['lang']['afkirbibit'],5)."</td>
            </tr>
            </thead><tbody id=containDataStock>";
            if($kdBatch!='')
            {
                $where=" and batch='".$kdBatch."'";
            }
	        $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where." group by batch,kodeorg order by tanggal desc ";
               // exit("error".$sData);
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=strtotime($tglSkrng);//tanggal pembuatan dokumen
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
                    $tab.="<td>".$optnmSup[$rDataBatch['supplerid']]."</td>";
                    $tab.="<td>".tanggalnormal($rDataBatch['tanggaltanam'])."</td>";
                    $tab.="<td align=right>".$jmlHari."</td>";
                    $tab.="</tr>";
                }
                $tab.="</tbody></table>";
		
	
			
			//echo "warning:".$strx;
			//=================================================
		$tab.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="laporanStock_".$kdUnit;
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
	
	default:
	break;
}

?>