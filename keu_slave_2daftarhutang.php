<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['kdOrg']==''?$kodeOrg=$_GET['kdOrg']:$kodeOrg=$_POST['kdOrg'];
$_POST['tgl1']==''?$tgl1=tanggalsystem($_GET['tgl1']):$tgl1=tanggalsystem($_POST['tgl1']);
$_POST['tgl2']==''?$tgl2=tanggalsystem($_GET['tgl2']):$tgl2=tanggalsystem($_POST['tgl2']);
$_POST['statTagihan']==''?$statTagihan=$_GET['statTagihan']:$statTagihan=$_POST['statTagihan'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['periode2']==''?$periode2=$_GET['periode2']:$periode2=$_POST['periode2'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmSupp=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optMatauang=makeOption($dbname, 'log_poht', 'nopo,matauang');

//if($tgl1==''||$tgl2=='')
if($periode==''||$statTagihan==''||$periode2=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

if($_GET['proses']=='excel')
{
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
     $tab.="<table>
             <tr><td colspan=5 align=left><font size=3>".strtoupper($_SESSION['lang']['daftarHutang'])."</font></td></tr> 
             <tr><td colspan=5 align=left>".$optNm[$kodeOrg]."</td></tr>   
             <tr><td colspan=5>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['tagihan']." ".$_SESSION['lang']['dari']." ".tanggalnormal($tgl1)." ".$_SESSION['lang']['sampai']." ".tanggalnormal($tgl2)."</td></tr>   
             </table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
//kondisi
 if($kodeOrg!='')
            {
                $where5=" and kodeorg = '".$kodeOrg."'";
                $where2=" and kodeorg='".$kodeOrg."'";
            }
if($statTagihan==1)
            {
                $where=" noinvoice
IN (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar!=0) ".$where2.")";
            }
            else if($statTagihan==0)
            {
                 $where=" noinvoice 
IN (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar is null or dibayar=0) ".$where2.")";
            }
            //if($tgl1!=''||$tgl2!='')
            if($periode!=''||$periode2!='')
            {
                if(strlen($periode)<7)
                {
                    $where.=" and substring(a.tanggal,1,4) between  '".$periode."' and '".$periode2."' ";
                }
                else
                {
                    $where.=" and substring(a.tanggal,1,7)  between '".$periode."' and '".$periode2."'";
                }
            }
           

//get data klo bukan pdf
if($_GET['proses']!='pdf')
{
            $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td align=center ".$bg.">No.</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['pt']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['noinvoice']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['tagihan']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['dibayar']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['notransaksi']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nopo']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['namasupplier']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['matauang']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nilaiinvoice']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nilaippn']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['uangmuka']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jatuhtempo']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nofp']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['keterangan']."</td>";
            $tab.="<td align=center ".$bg.">Dokumen</td>";
           
            $tab.="</tr></thead><tbody>";
            
            $sKodeOrg="SELECT a.`noinvoice`,a.`nopo`,a.`posting`,a.`tanggal`,a.kodesupplier,a.`nilaiinvoice`,a.`nilaippn`,a.`jatuhtempo`,a.`nofp`,a.`keterangan`,a.`matauang`,a.`uangmuka`,a.`kodeorg`
FROM ".$dbname.".`keu_tagihanht` a WHERE ".$where." ".$where5." order by a.tanggal asc";
           //echo $sKodeOrg;
            $qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
            $brsCek=mysql_num_rows($qKodeOrg);
            if($brsCek!=0)
            {
            while($rKode=mysql_fetch_assoc($qKodeOrg))
            {
                    $no+=1;
                    $sTglByr="SELECT DISTINCT a.tanggal AS tglByr, a.notransaksi FROM ".$dbname.".`keu_kasbankht` a 
                        LEFT JOIN ".$dbname.".keu_kasbankdt b ON a.`notransaksi` = b.`notransaksi`WHERE `keterangan1` = '".$rKode['noinvoice']."'";
                    $qTglByr=mysql_query($sTglByr) or die(mysql_error($conn));
                    $rTglByr=mysql_fetch_assoc($qTglByr);
                    if($rTglByr['tglByr']=='')
                    {
                        $rTglByr['tglByr']="0000-00-00";
                    }
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td align=center>".$no."</td>";
                    $tab.="<td align=left>".$optNm[$rKode['kodeorg']]."</td>";
                    $tab.="<td align=left>".$rKode['noinvoice']."</td>";
                    $tab.="<td align=center>".tanggalnormal($rKode['tanggal'])."</td>";
                    $tab.="<td align=center>".tanggalnormal($rTglByr['tglByr'])."</td>";
                    $tab.="<td align=left>".$rTglByr['notransaksi']."</td>";
                    $tab.="<td align=left>".$rKode['nopo']."</td>";
                    $tab.="<td align=left>".$optNmSupp[$rKode['kodesupplier']]."</td>";
                    $tab.="<td align=left>".$optMatauang[$rKode['nopo']]."</td>";
                    $tab.="<td align=right>".number_format($rKode['nilaiinvoice'],2)."</td>";
                    $tab.="<td align=right>".number_format($rKode['nilaippn'],2)."</td>";
                    $tab.="<td align=right>".number_format($rKode['uangmuka'],2)."</td>";
                    $tab.="<td align=center>".tanggalnormal($rKode['jatuhtempo'])."</td>";
                    $tab.="<td align=left>".$rKode['nofp']."</td>";
                    $tab.="<td align=left>".$rKode['keterangan']."</td>";
					if($rKode['posting']=='0')
					{
						 $tab.="<td align=left>Belum Lengkap</td>";
					}
					else
					{
						$tab.="<td align=left>Lengkap</td>";
					}
                    //if($rKode['posting']==0)$dok='Belum Lengkap';
                    //if($rKode['posting']==1)$dok='Lengkap';
                    //$tab.="<td align=left>".$dok."</td>";
                    $tab.="</tr>";
                    $totInvoice+=$rKode['nilaiinvoice'];
                   
            }
            
            $tab.="</tbody><thead><tr class=rowheader>";
            $tab.="<td  align=right colspan=8>".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right >".number_format($totInvoice,2)."</td>";
            $tab.="<td align=left colspan=7>".kekata($totInvoice)."</td>";
            $tab.="</tr>";
            $tab.="</thead>";
                }
                else
                {
                    $tab.="<tr class=rowcontent align=center><td colspan=15>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
                }
           $tab.="</table>";
}
	switch($proses)
        {
            case'preview':
                
            echo $tab;
            break;
            
            case'excel':
               
                if($periode==''||$statTagihan=='')
                {
                    exit("Error:Field Tidak Boleh Kosong");
                }
           
           
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="daftarHutang".$dte;
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
            class PDF extends FPDF {
            function Header() {
            global $tgl1;
            global $where;
            global $tgl2;
            global $kodeOrg;
            global $statTagihan;
            global $dbname;
            global $optNm;
            global $optNmSupp;
            
            	    # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 15;
				
				
				if($kodeOrg=='FBB')
				{
					$path='images/logo2.jpg';
				}
				else if ($kodeOrg=='USJ')
				{
					$path='images/logo3.jpg';	
				}
				else
				{
					$path='images/logo.jpg';
				}
				
				
                //$path='images/logo.jpg';
               // $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->Image($path,25,15,70);
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
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['daftarHutang'],'',0,'L');
                $this->Ln();
                $this->Ln();
                if(strlen($_GET['periode'])>4)
                {
                    $_GET['periode']=substr(tanggalnormal($_GET['periode']),1,7);
                }
                else {
                   $_GET['periode']=$_GET['periode'];
                }
                $this->Cell($width,$height,$_SESSION['lang']['periode']." : ".$_GET['periode'],'',0,'C');
                $this->Ln();
                $this->Ln();
              	$this->SetFont('Arial','B',6);
                $this->SetFillColor(220,220,220);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['pt'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['noinvoice'],1,0,'C',1);		
				$this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['tagihan'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['dibayar'],1,0,'C',1);
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['nopo'],1,0,'C',1);
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['namasupplier'],1,0,'C',1);
                                $this->Cell(8/100*$width,$height,$_SESSION['lang']['nilaiinvoice'],1,0,'C',1);
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['nilaippn'],1,0,'C',1);
                               // $this->Cell(8/100*$width,$height,$_SESSION['lang']['uangmuka'],1,0,'C',1);
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['jatuhtempo'],1,0,'C',1);
                                $this->Cell(9/100*$width,$height,$_SESSION['lang']['nofp'],1,1,'C',1);
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
        $height = 10;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',6);
                
                 $sKodeOrg="SELECT a.`noinvoice`,a.`nopo`,a.`tanggal`,a.kodesupplier,a.`nilaiinvoice`,a.`nilaippn`,a.`jatuhtempo`,a.`nofp`,a.`keterangan`,a.`matauang`,a.`uangmuka`,a.`kodeorg`
FROM ".$dbname.".`keu_tagihanht` a WHERE ".$where." order by a.tanggal asc";
           // echo $sKodeOrg;
            $qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
            $brsCek=mysql_num_rows($qKodeOrg);
            if($brsCek!=0)
            {
            while($rKode=mysql_fetch_assoc($qKodeOrg))
            {
                    $no+=1;
                    $sTglByr="SELECT DISTINCT a.tanggal AS tglByr, a.notransaksi FROM ".$dbname.".`keu_kasbankht` a 
                        LEFT JOIN ".$dbname.".keu_kasbankdt b ON a.`notransaksi` = b.`notransaksi`WHERE `keterangan1` = '".$rKode['noinvoice']."'";
                    $qTglByr=mysql_query($sTglByr) or die(mysql_error($conn));
                    $rTglByr=mysql_fetch_assoc($qTglByr);
                    if($rTglByr['tglByr']=='')
                    {
                        $rTglByr['tglByr']="0000-00-00";
                    }
                    $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                    $pdf->Cell(15/100*$width,$height,$optNm[$rKode['kodeorg']],1,0,'L',1);		
                    $pdf->Cell(10/100*$width,$height,$rKode['noinvoice'],1,0,'L',1);
                    $pdf->Cell(7/100*$width,$height,tanggalnormal($rKode['tanggal']),1,0,'C',1);
                    $pdf->Cell(7/100*$width,$height,tanggalnormal($rTglByr['tglByr']),1,0,'C',1);
                    $pdf->Cell(8/100*$width,$height,$rTglByr['notransaksi'],1,0,'L',1);
                    $pdf->Cell(10/100*$width,$height,$rKode['nopo'],1,0,'L',1);
                    $pdf->Cell(12/100*$width,$height,$optNmSupp[$rKode['kodesupplier']],1,0,'L',1);
                    $pdf->Cell(8/100*$width,$height,number_format($rKode['nilaiinvoice'],2),1,0,'R',1);
                    $pdf->Cell(5/100*$width,$height,$rKode['nilaippn'],1,0,'R',1);
                    //$pdf->Cell(8/100*$width,$height,number_format($rKode['uangmuka'],2),1,0,'R',1);
                    $pdf->Cell(6/100*$width,$height,tanggalnormal($rKode['jatuhtempo']),1,0,'C',1);
                    $pdf->Cell(9/100*$width,$height,$rKode['nofp'],1,1,'L',1);    
                     $totInvoice+=$rKode['nilaiinvoice'];
                    }
	
                    $pdf->Cell(72/100*$width,$height,$_SESSION['lang']['total'].": ".kekata($totInvoice),1,0,'L',1);
                    $pdf->Cell(28/100*$width,$height,number_format($totInvoice,2),1,1,'L',1);
            }
            else
            {
                exit("Error: Data Kosong");
            }
			
        $pdf->Output();

            break;
                
            default:
            break;
        }
	
?>
