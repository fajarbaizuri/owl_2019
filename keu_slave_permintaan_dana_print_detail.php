<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/iPdfMaster.php');
include_once('lib/terbilang.php'); 

$proses = $_GET['proses'];
$param = $_GET;



$notransaksi=$_GET['notransaksi'];
/** Report Prep **/
$cols = array();

#=============================== Header =======================================
$whereH = "notransaksi='".$param['notransaksi']."'";
$queryH = selectQuery($dbname,'log_dana_vw','*',$whereH);
$resH = fetchData($queryH);

//echo "<pre>";
//print_r($resH);
//echo "</pre>";

#=============================== Detail =======================================
# Data
$col1 = 'noakun,jumlah,noaruskas,matauang,kode';
$cols = array('nomor','noakun','namaakun','matauang','debet','kredit');
$where = "notransaksi='".$param['notransaksi']."'";
$query = selectQuery($dbname,'log_danadt_vw','*',$where);
//echo $query;
$res = fetchData($query);

# Data Empty
if(empty($res)) {
	
    echo 'Data Kosong';
    exit;
}

# Options
$whereAkun = "noakun in (";
$whereAkun .= "'".$resH[0]['noakun']."'";
foreach($res as $key=>$row) {
    $whereAkun .= ",'".$row['noakun']."'";
}
$whereAkun .= ")";
$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereAkun);

# Data Show
$data = array();

#================================ Prep Data ===================================
# Total
$totalDebet = 0;$totalKredit = 0;

# Dari Header
$i=1;
$data[$i] = array(
    'nomor'=>$i,
    'noakun'=>$resH[0]['noakun'],
    'namaakun'=>$optAkun[$resH[0]['noakun']],
    'matauang'=>$resH[0]['matauang'],
    'debet'=>0,
    'kredit'=>0
);
if($param['tipetransaksi']=='M') {
    $data[$i]['debet'] = $resH[0]['jumlah'];
    $totalDebet += $resH[0]['jumlah'];
} else {
    $data[$i]['kredit'] = $resH[0]['jumlah'];
    $totalKredit += $resH[0]['jumlah'];
}
$i++;

# Dari Detail
foreach($res as $row) {
    $data[$i] = array(
	'nomor'=>$i,
	'noakun'=>$row['noakun'],
	'namaakun'=>$optAkun[$row['noakun']],
	'matauang'=>$row['matauang'],
	'debet'=>0,
	'kredit'=>0
    );
    if($param['tipetransaksi']=='M' and $row['jumlah']>0) {
	$data[$i]['kredit'] = $row['jumlah'];
	$totalKredit += $row['jumlah'];
    }
    else if($param['tipetransaksi']=='K' and $row['jumlah']<0){
	$data[$i]['kredit'] = $row['jumlah']*-1;
	$totalKredit += $row['jumlah']*-1;        
    }
    else if($param['tipetransaksi']=='M' and $row['jumlah']<0){
	$data[$i]['debet'] = $row['jumlah']*-1;
	$totalDebet += $row['jumlah']*-1;        
    }    
    else {
	$data[$i]['debet'] = $row['jumlah'];
	$totalDebet += $row['jumlah'];
    }
    $i++;
}

// nyusun berdasarkan debet dulu, abis itu baru kredit. by dz
##fbg gk pake sort.. ind
if(!empty($data)) foreach($data as $c=>$key) {
    $sort_debet[] = $key['debet'];
    $sort_kredit[] = $key['kredit'];
}

// sort
if(!empty($data))array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);



//$a=array_multisort(

$align = explode(",","R,R,L,L,R,R");
$length = explode(",","7,12,35,10,18,18");
$title = $_SESSION['lang']['kasbank'];
$titleDetail = 'Detail';

//exit("Error:$param");
//echo $noakun."_".$notransaksi."_".$tipetransaksi."_".$kodeorg;

switch($proses)
{
	
case'html':
        $tab.="<link rel=stylesheet type=text/css href=style/generic.css>";
        $tab.="<fieldset><legend>".$title."</legend>";
        $tab.="<table cellpadding=1 cellspacing=1 border=0 width=65% class=sortable><tbody class=rowcontent>";
        $tab.="<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td> :</td><td> ".$_SESSION['empl']['lokasitugas']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['notransaksi']."</td><td> :</td><td> ".$res[0]['kode']."/".$param['notransaksi']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['cgttu']."</td><td> :</td><td> ".$resH[0]['cgttu']."</td></tr>";
        $tab.="<tr><td>Terbilang</td><td> :</td><td> ".terbilang($resH[0]['jumlah'],2).
	    ' rupiah'."</td></tr>";
        $tab.="</tbody></table><br />";
       
            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>";
            foreach($cols as $column) {
                $tab.="<td>".$_SESSION['lang'][$column]."</td>";
            }
            $tab.="</tr></thead><tbody class=rowcontent>";
        // nyusun ulang nomor setelah disort by debet. dz
            $nyomor=0;
            foreach($data as $key=>$row) 
			{    
                $nyomor+=1;
                $tab.="<tr>";
                foreach($row as $key=>$cont) 
				{
                    if($key=='nomor')
					{
                        $tab.="<td>".$nyomor."</td>";
                    }
					else
					{
                        if($key=='debet' or $key=='kredit') 
						{
                            $tab.="<td>".number_format($cont,0)."</td>";
                        } 
						else 
						{
                            $tab.="<td>".$cont."</td>";
                        }                    
                    }
					
                }
                $tab.="</tr>";
				
            }
        $tab.="<tr><td colspan=4 align=center>Total</td><td align=right>".number_format($totalDebet,0)."</td><td align=right>".number_format($totalKredit,0)."</td></tr>";
             $tab.="</tbody></table> <br />";
       
        echo $tab;
        
    break;
    default:


	
case'pdf':

            class PDF extends FPDF
                    {
                        function Header() {
							global $notransaksi;
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
							global $kdorg;
							global $kdAfd;
							global $tgl1;
							global $tgl2;
							global $where;
							global $nmOrg;
							global $lok;
							global $notrans;
							
							//echo $notransaksi;exit();

                            //$cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);

                            $width = $this->w - $this->lMargin - $this->rMargin;
                            //$height = 20;
							$height = 15;							
							$a="select kodeorg from ".$dbname.".log_danaht where notransaksi='".$notransaksi."'";
							//echo $a;
							$b=mysql_query($a) or die (mysql_error());
							$c=mysql_fetch_assoc($b);
							$kdorghead=$c['kodeorg'];
							
							$ha="select induk,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdorghead."'";
							$hi=mysql_query($ha) or die(mysql_error());
							$hu=mysql_fetch_assoc($hi);
								$orglogo=$hu['induk'];
								$namahead=$hu['namaorganisasi'];
							
							if($orglogo=='FBB')
							{
								$path='images/logo2.jpg';
							}
							else if ($orglogo=='USJ')
							{
								$path='images/logo3.jpg';
							}
							else if ($orglogo=='FPS')
							{
								$path='images/logo4.jpg';
							}
							else
								$path='images/logo.jpg';
							
							
							
                           // $path='images/logo.jpg';
                            //$this->Image($path,$this->lMargin,$this->tMargin,50);	
							$this->Image($path,60,40,35);
                            $this->SetFont('Arial','I',7);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(25); 
                            $this->Cell($width,12,$namahead,0,1,'L');	
							//$this->Ln();
							$this->SetX(300); 
							$this->SetFont('Arial','B',14);			
							$this->Cell($width,12,'PERMINTAAN DANA OPERASIONAL',0,0,'L');
							
						$ind="select * from ".$dbname.".log_dana_vw where notransaksi='".$notransaksi."' ";
						//echo $ind;
							$dr=mysql_query($ind) or die(mysql_error());
							$ra=mysql_fetch_assoc($dr);							
							 
							$this->SetX(25);
							$this->Ln(40); 
							$this->SetFont('Arial','',8);
							$this->Cell($width,12,'No. Transaksi',0,1,'L');
							$this->SetXY(85,80);
							$this->Cell(10,12,': '.$ra["notransaksi"],0,1,'L');
							$this->Cell($width,12,'Organisasi',0,1,'L');
							$this->SetXY(85,92);
							$this->Cell(10,12,': '.$ra["kodeorg"],0,1,'L');
							$this->Cell($width,12,'Bulan',0,1,'L');
							$this->SetXY(85,104);
							$this->Cell(10,12,': '.$ra["bulanthn"],0,1,'L');
							$this->Ln(10); 
							$this->Cell($width,12,'Rincian Permintaan :',0,1,'L');
							 
							
							
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',9);
							$height = 12;
                            $this->Ln(1);
                            $this->SetFont('Arial','B',8);
                            $this->SetFillColor(220,220,220);
							$this->Cell(5/100*$width,13,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
							$this->Cell(25/100*$width,13,'Uraian',1,0,'C',1);
							$this->Cell(10/100*$width,13,'Pengajuan',1,0,'C',1);
							$this->Cell(11/100*$width,13,'Budget s/d BI',1,0,'C',1);
							$this->Cell(11/100*$width,13,'Permintaan s/d BI',1,0,'C',1);
							$this->Cell(12/100*$width,13,'Sisa Budget s/d BI',1,0,'C',1);
							$this->Cell(13/100*$width,13,'Realisasi Dana s/d BI',1,0,'C',1);
							$this->Cell(13/100*$width,13,'Sisa Realisasi Dana s/d BI',1,0,'C',1);
											

											$this->Ln(13);
                       }

                        function Footer()
                        {
                           // $this->SetY(-15);
                           // $this->SetFont('Arial','I',8);
                           // $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
					
                    $pdf=new PDF('L','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 12;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',8);
							
		$str="select * from ".$dbname.".log_danadt_vw where notransaksi='".$notransaksi."' order by keterangan asc ";
		//echo $str;
		$res=mysql_query($str);//tinggal tarik $res karna sudah di declarasi di atas
		$no=$total=0;
		

		

		
		while($bar=mysql_fetch_assoc($res)) {	
			$no+=1;	
			if ($no >1) $pdf->Ln(12);
			
			$pdf->Cell(5/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(25/100*$width,$height,$bar['keterangan'],1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,number_format($bar['pengajuan']),1,0,'R',1);
			$pdf->Cell(11/100*$width,$height,number_format($bar['budgetsbi']),1,0,'R',1);
			$pdf->Cell(11/100*$width,$height,number_format($bar['permintaansbi']),1,0,'R',1);
			$pdf->Cell(12/100*$width,$height,number_format($bar['sisabudgetsbi']),1,0,'R',1);
			$pdf->Cell(13/100*$width,$height,number_format($bar['realisasidanasbi']),1,0,'R',1);
			$pdf->Cell(13/100*$width,$height,number_format($bar['sisarealisasidanasbi']),1,0,'R',1);
			$total+=$bar['pengajuan'];
			
		}
		
		for($no+=1;$no<=18;$no++)
		{	
			$pdf->Ln(12);
			$pdf->Cell(5/100*$width,$height,'',1,0,'C',1);	
			$pdf->Cell(25/100*$width,$height,'',1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,'',1,0,'R',1);
			$pdf->Cell(11/100*$width,$height,'',1,0,'R',1);
			$pdf->Cell(11/100*$width,$height,'',1,0,'R',1);
			$pdf->Cell(12/100*$width,$height,'',1,0,'R',1);
			$pdf->Cell(13/100*$width,$height,'',1,0,'R',1);
			$pdf->Cell(13/100*$width,$height,'',1,0,'R',1);
		}
		
		$pdf->Ln(12);
		$pdf->Cell(30/100*$width,$height,'Total Pengajuan',1,0,'C',1);
		$pdf->Cell(10/100*$width,$height,number_format($total),1,0,'R',1);
		$pdf->Cell(60/100*$width,$height,terbilang($total,0)." Rupiah",1,0,'C',1);
		
		$a="select tglharian,namakaryawan from ".$dbname.".log_dana_vw where notransaksi='".$notransaksi."'";
		$b=mysql_query($a) or die (mysql_error());
		$ca=mysql_fetch_assoc($b);
		$pemohon=$ca["namakaryawan"];
		
		$pdf->Ln(40);
		$pdf->Cell(74/100*$width,$height,'',0,0,'C',1); 
		$pdf->Cell(26/100*$width,$height,"................................, Tanggal: ".$ca["tglharian"],0,0,'R',1);
		
		$pdf->Ln(10);
		$pdf->Cell(25/100*$width,$height,'Pemohon',0,0,'C',1);  
		$pdf->Cell(75/100*$width,$height,'Status Persetujuan:',0,0,'L',1);  
		$pdf->Ln(12);
		$pdf->Cell(25/100*$width,$height,'',0,0,'C',1);  
		$pdf->Cell(15/100*$width,$height,'Nama',1,0,'C',1);  
		$pdf->Cell(22/100*$width,$height,'Jabatan',1,0,'C',1);  
		$pdf->Cell(12/100*$width,$height,'Tanggal',1,0,'C',1);  
		$pdf->Cell(26/100*$width,$height,'Catatan',1,0,'C',1);  
		
		$str1="select * from ".$dbname.".log_persetujuandana_vw where notransaksi='".$notransaksi."' order by `level` asc ";
		$res1=mysql_query($str1);//tinggal tarik $res karna sudah di declarasi di atas
		$no1=0;
		while($bar1=mysql_fetch_assoc($res1)) {	
			
			$pdf->Ln(12);
			$pdf->Cell(25/100*$width,$height,'',0,0,'L',1);  
			$pdf->Cell(15/100*$width,$height,$bar1['namakaryawan'],1,0,'L',1);  
			$pdf->Cell(22/100*$width,$height,$bar1['namajabatan'],1,0,'L',1);  
			$pdf->Cell(12/100*$width,$height,$bar1['tanggal'],1,0,'C',1);  
			$pdf->Cell(26/100*$width,$height,$bar1['keputusan'],1,0,'L',1);  
			
		}
		
		
		
		for($no=1;$no<=7;$no++)
		{	
			
			if ($no >1) $pdf->Ln(12);
			
			//$pdf->Ln(12);
			
			if($no==7){
					$pdf->Cell(25/100*$width,$height,"(  ".$pemohon."  )",0,0,'C',1);  
			}else{
					$pdf->Cell(25/100*$width,$height,'',0,0,'C',1);  
			}
			
			$pdf->Cell(15/100*$width,$height,'',1,0,'C',1);  
			$pdf->Cell(22/100*$width,$height,'',1,0,'C',1);  
			$pdf->Cell(12/100*$width,$height,'',1,0,'C',1);  
			$pdf->Cell(26/100*$width,$height,'',1,0,'C',1);  
			
			
		}
		
		
		$pdf->Output();
            
	break;

	
	

	
	
	
	
	default:
	break;

}
?>