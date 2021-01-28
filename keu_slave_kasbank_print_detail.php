<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/iPdfMaster.php');
include_once('lib/terbilang.php');

$proses = $_GET['proses'];
$param = $_GET;

$noakun=$_GET['noakun'];
$tipetransaksi=$_GET['tipetransaksi'];
$notransaksi=$_GET['notransaksi'];
$kodeorg=$_GET['kodeorg'];
$optnamaakunpdf=makeOption($dbname,'keu_5akun','noakun,namaakun');
$optorgpdf=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

/** Report Prep **/
$cols = array();

#=============================== Header =======================================
$whereH = "notransaksi='".$param['notransaksi'].
    "' and kodeorg='".$param['kodeorg'].
    "' and noakun='".$param['noakun'].
    "' and tipetransaksi='".$param['tipetransaksi']."'";
$queryH = selectQuery($dbname,'keu_kasbankht','*',$whereH);
$resH = fetchData($queryH);

//echo "<pre>";
//print_r($resH);
//echo "</pre>";

# Get Nama Pembuat
$userId = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
    "karyawanid='".$resH[0]['userid']."'");
# Get Nama Akun Hutang
$namaakunhutang = makeOption($dbname,'keu_5akun','noakun,namaakun',
    "noakun='".$resH[0]['noakunhutang']."'");

#=============================== Detail =======================================
# Data
$col1 = 'noakun,jumlah,noaruskas,matauang,kode';
$cols = array('nomor','noakun','namaakun','matauang','debet','kredit');
$where = "notransaksi='".$param['notransaksi'].
    "' and kodeorg='".$param['kodeorg'].
    "' and noakun2a='".$param['noakun'].
    "' and tipetransaksi='".$param['tipetransaksi']."'";
$query = selectQuery($dbname,'keu_kasbankdt',$col1,$where);
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


	
case 'pdfdetail':
	
        $pdf=new iPdfMaster('P','pt','ind');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        
		
		$ha="select induk from ".$dbname.".organisasi where kodeorganisasi='".$param['kodeorg']."'";
		$hi=mysql_query($ha);
		$hu=mysql_fetch_assoc($hi);
			$indukorg=$hu['induk'];
			
		//	echo $indukorg;
		
		
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(100,$height,$optorgpdf[$indukorg],0,1,'L',1);
		$pdf->Ln();
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(100,$height,$_SESSION['lang']['kodeorg'],0,0,'L',1);
		$pdf->Cell(100,$height,': '.$param['kodeorg'],0,0,'L',1);
		
		$pdf->setX(420);
		$pdf->Cell(145,$height,'Tanggal       : '.tanggalnormal($resH[0]['tanggal']),0,1,'R',1);
		
		
		$pdf->Cell(100,$height,$_SESSION['lang']['notransaksi'],0,0,'L',1);
		$pdf->Cell(100,$height,': '.$res[0]['kode']."/".$param['notransaksi'],0,1,'L',1);
		
		$pdf->Cell(100,$height,$_SESSION['lang']['cgttu'],0,0,'L',1);
		$pdf->Cell(100,$height,': '.$resH[0]['cgttu'],0,1,'L',1);
		
		$pdf->Cell(100,$height,Terbilang,0,0,'L',1);
		$pdf->Cell(100,$height,': '.terbilang($resH[0]['jumlah'],2).' rupiah',0,1,'L',1);
		//$pdf->MultiCell($width,$height,'Terbilang : '.terbilang($resH[0]['jumlah'],2).' rupiah',0);
		
        //$pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".$res[0]['kode']."/".$param['notransaksi'],0,1,'L',1);
        //$pdf->Cell($width,$height,$_SESSION['lang']['cgttu']." : ". $resH[0]['cgttu'],0,1,'L',1);
		//$pdf->MultiCell($width,$height,'Terbilang : '.terbilang($resH[0]['jumlah'],2).' rupiah',0);
        $pdf->Ln();
	
	# Header
	$pdf->SetFont('Arial','',9);
	#$pdf->Cell($width,$height,$titleDetail,0,1,'L',1);
	
	$pdf->SetFillColor(220,220,220);
	$i=0;
	foreach($cols as $column) {
		if($column=='namaakun')
		{
			$pdf->Cell($length[$i]/75*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
		}
		else if ($column=='debet' || $column=='kredit')
		{
	    	$pdf->Cell($length[$i]/150*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
		}
		else 
		{
	    	$pdf->Cell($length[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
		}
		$i++;
	}
	$pdf->Ln();
	
	# Content
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        // nyusun ulang nomor setelah disort by debet. dz
        $nyomor=0;
	foreach($data as $key=>$row) {    
            $nyomor+=1;
	    $i=0;
	    foreach($row as $key=>$cont) 
		{
        	if($key=='nomor')
			{
		    	$pdf->Cell($length[$i]/100*$width,$height,$nyomor,1,0,'C',1);                    
            }
		
			else if($key=='noakun')
			{
		    	$pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);      
            }
			else
			{
				if($key=='debet' or $key=='kredit') 
				{
		    		$pdf->Cell($length[$i]/150*$width,$height,number_format($cont,0),1,0,$align[$i],1);
				} 
				else  if ($key=='matauang')
				{
		    		$pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
				}
				else 
				{
		    		$pdf->Cell($length[$i]/75*$width,$height,$cont,1,0,$align[$i],1);
				}                    
			}
		$i++;//matauang
	    }
	    $pdf->Ln();
	}
	# Total
	$pdf->SetFont('Arial','',9);
	$pdf->SetFillColor(220,220,220);
	$lenTotal = $length[0]+$length[1]+$length[2]+$length[3];
	$pdf->Cell(407.5,$height,'Total',1,0,'C',1);
	$pdf->Cell($length[4]/150*$width,$height,number_format($totalDebet,0),1,0,'R',1);
	$pdf->Cell($length[5]/150*$width,$height,number_format($totalKredit,0),1,0,'R',1);
	$pdf->Ln();
	
	# Keterangan
	//$pdf->MultiCell($width,$height,'Keterangan : '.$resH[0]['keterangan']);
	# Hutang Unit
        if($resH[0]['hutangunit']==1){
            $pdf->MultiCell($width,$height,'Pembayaran Hutang Unit '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']]);
        }
        $pdf->Ln();
	
	# TTD
/*	$pdf->SetFillColor(220,220,220);
        $pdf->Cell(21/100*$width,$height,'Penerima',1,0,'C',1);
	$pdf->Cell(21/100*$width,$height,'Dibuat',1,0,'C',1);
	$pdf->Cell(33/100*$width,$height,'Diperiksa',1,0,'C',1);
	$pdf->Cell(25/100*$width,$height,'Disetujui',1,0,'C',1);
	$pdf->Ln();
	$pdf->SetFillColor(255,255,255);
	for($i=0;$i<3;$i++) {
		$pdf->Cell(21/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Cell(21/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Cell(33/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Cell(25/100*$width,$height,'','LR',0,'C',1);
	    $pdf->Ln();
	}
	$pdf->Cell(21/100*$width,$height,'','BLR',0,'C',1);
	if(isset($userId[$resH[0]['userid']])) {
	    $pdf->Cell(21/100*$width,$height,$userId[$resH[0]['userid']],'BLR',0,'C',1);
	} else {
	    $pdf->Cell(21/100*$width,$height,'','BLR',0,'C',1);
	}
	$pdf->Cell(33/100*$width,$height,'','BLR',0,'C',1);
	$pdf->Cell(25/100*$width,$height,'','BLR',0,'C',1);*/
	
        $pdf->Output();
        break;
	
	//indra
	
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
							$a="select kodeorg,noakun from ".$dbname.".keu_kasbankht where notransaksi='".$notransaksi."'";
							//echo $a;
							$b=mysql_query($a) or die (mysql_error());
							$c=mysql_fetch_assoc($b);
								$kdorghead=$c['kodeorg'];
								$noakunhead=$c['noakun'];
								
							##penamaan no akun
							$hehe="select namaakun from ".$dbname.".keu_5akun where noakun='".$noakunhead."'";
							$haha=mysql_query($hehe) or die (mysql_error($hehe));
							$huhu=mysql_fetch_assoc($haha);
								$namaakunhead=$huhu['namaakun'];	
								
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
							$this->SetX(175); 
							$this->SetFont('Arial','B',14);			
							
							$w="select noakun,tipetransaksi,notransaksi,tanggal,cgttu from ".$dbname.".keu_kasbankht where notransaksi='".$notransaksi."' ";
							//echo $w;
							$i=mysql_query($w);
							$b=mysql_fetch_assoc($i);
							$notra=$b['notransaksi'];
							$tglh=$b['tanggal'];
							$byr=$b['cgttu'];
							$tipetran=$b['tipetransaksi'];
							$noakuna=$b['noakun'];
							if(substr($noakuna,0,5)=='11102')
							{
								$jns='BANK';
							}
							else
							{
								$jns='KAS';
								}
							
							if($tipetran=='M'){
								$this->Cell($width,12,'BUKTI PENERIMAAN '.$jns,0,0,'L');
							}
							else
							{
							
							$this->Cell($width,12,'BUKTI PENGELUARAN '.$jns,0,0,'L');	
							}
							//exit("Error:$byr");
							
							$this->SetX(450); 
							$this->SetFont('Arial','',9);
							$this->Cell($width,12,'No.',0,0,'L');
							$this->SetX(475);
							$this->Cell($width,12,': '.$notra,0,1,'L');	
							$this->SetX(450);  
							$this->Cell($width,12,'Tgl.',0,0,'L');	
							$this->SetX(475);
							$this->Cell($width,12,': '.tanggalnormal($tglh),0,1,'L');	
							
						$ind="select distinct kodesupplier from ".$dbname.".keu_kasbankdt where notransaksi='".$notra."' ";
						//echo $ind;
						$dr=mysql_query($ind) or die(mysql_error());
						$ra=mysql_fetch_assoc($dr);
						
							$suphead=$ra['kodesupplier'];
							$optnamasuphead=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
							//echo $optnamasuphead[$suphead];
							
						
							
							$this->SetX(25);
							$this->Ln(10); 
							$this->SetFont('Arial','',8);
							
							if($suphead==''){
								$this->Cell($width,12,'Dibayarkan / ditransfer kepada : ...............................................',0,1,'L');
							}else
							{
								$this->Cell($width,12,'Dibayarkan / ditransfer kepada : '.$optnamasuphead[$suphead],0,1,'L');
							}
							$this->Cell($width,12,'Dengan :',0,0,'L');
							
							//tunai
							$this->SetX(75);
							if($byr=='Tunai')
							{
								$this->Cell(10,12,'X',1,0,'L');
							}
							else
							{
								$this->Cell(10,12,'',1,0,'L');
							}
							$this->SetX(85);
							$this->Cell(10,12,'Tunai',0,0,'L');
							
							//cek
							$this->SetX(115);
							if($byr=='Cek')
							{
								$this->Cell(10,12,'X',1,0,'L');
							}
							else
							{
								$this->Cell(10,12,'',1,0,'L');
							}
							$this->SetX(125);
							$this->Cell(10,12,'Cek',0,0,'L');
							
							//giro
							$this->SetX(155);
							if($byr=='Giro')
							{
								$this->Cell(10,12,'X',1,0,'L');
							}
							else
							{
								$this->Cell(10,12,'',1,0,'L');
							}
							$this->SetX(165);
							$this->Cell(10,12,'Giro',0,0,'L');
							
							$this->SetX(195);
							if($byr=='Transfer')
							{
								$this->Cell(10,12,'X',1,0,'L');
							}
							else
							{
								$this->Cell(10,12,'',1,0,'L');
							}
							$this->SetX(205);
							$this->Cell(10,12,'Transfer',0,0,'L');
							
							//dll
							/*$this->SetX(255);
							$this->Cell(10,12,'',1,0,'L');
							$this->SetX(265);
							$this->Cell(10,12,'...................',0,0,'L');*/
							
							//bank
							$this->SetX(280);
							$this->Cell(10,12,'Bank : '.$namaakunhead,0,0,'L');
							
							$this->SetX(495);
							$this->Cell(10,12,'No. Akun : '.$noakunhead,0,0,'L');
							
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',9);
							$height = 12;
                            $this->Ln(13);
                            $this->SetFont('Arial','B',8);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(5/100*$width,13,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(65/100*$width,13,'Keterangan',1,0,'C',1);
											//$this->Cell(15/100*$width,15,'No. Akun',1,0,'C',1);
											$this->Cell(10/100*$width,13,'No. Akun',1,0,'C',1);
											$this->Cell(20/100*$width,13,'Jumlah',1,1,'C',1);

											//$this->Ln();
                       }

                        function Footer()
                        {
                           // $this->SetY(-15);
                           // $this->SetFont('Arial','I',8);
                           // $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('P','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 12;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',8);
							
		$str="select * from ".$dbname.".keu_kasbankdt where notransaksi='".$notransaksi."' order by keterangan2 asc ";
		//echo $str;
		$res=mysql_query($str);//tinggal tarik $res karna sudah di declarasi di atas
		$no=$total=0;
		

		

		
		while($bar=mysql_fetch_assoc($res)) {	
			$noakn=$bar['noakun'];
			
			$in="select noakun,namaakun from ".$dbname.".keu_5akun where noakun=".$noakn." ";
			//echo $in;
			$dr=mysql_query($in);
			$ra=mysql_fetch_assoc($dr);
			$namakun=$ra['namaakun'];
			//echo $namakun;
			$no+=1;	
			$pdf->Cell(5/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(65/100*$width,$height,$bar['keterangan2'],1,0,'L',1);		
			//$pdf->Cell(15/100*$width,$height,$bar['noakun'],1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,$bar['noakun'],1,0,'R',1);		
			$pdf->Cell(20/100*$width,$height,number_format($bar['jumlah']),1,1,'R',1);
			$total+=$bar['jumlah'];
		}
		for($no+=1;$no<=12;$no++)
		{	
			$pdf->Cell(5/100*$width,$height,'',1,0,'C',1);	
			$pdf->Cell(65/100*$width,$height,'',1,0,'L',1);		
			//$pdf->Cell(15/100*$width,$height,$bar['noakun'],1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,'',1,0,'R',1);		
			$pdf->Cell(20/100*$width,$height,'',1,1,'R',1);	
		}
		$pdf->Cell(70/100*$width,$height,'',1,0,'L',1);	
		$pdf->Cell(10/100*$width,$height,'Jumlah',1,0,'L',1);		
		$pdf->SetFont('arial','B',8);
		$pdf->Cell(20/100*$width,$height,number_format($total),1,1,'R',1);
		$pdf->SetFont('arial','',8);
		$pdf->Cell(10/100*$width,$height,'Terbilang :',1,0,'L',1);	
		$pdf->SetFont('arial','B',8);
		$pdf->Cell(90/100*$width,$height,terbilang($total,0),1,1,'R',1);	
		
		//cek
		$pdf->Ln(3);
		$pdf->SetX(400);
		$pdf->SetFont('arial','',8);
		$pdf->Cell(10,12,'Jakarta, ...........................................................',0,1,'L');
		
		//$pdf->Ln(0);
		
		$haha="select kodeorg from ".$dbname.".keu_kasbankht where notransaksi='".$notransaksi."'";
		$hehe=mysql_query($haha) or die (mysql_error());
		$huhu=mysql_fetch_assoc($hehe);
			$kodepemisah=$huhu['kodeorg'];
			//echo $kodepemisah;		
		
			
			
		if($kodepemisah=='FBHO' || $kodepemisah=='FPHO' || $kodepemisah=='BFSL' || $kodepemisah=='SPSL' || $kodepemisah=='USHO')
		{	
			$pdf->Cell(45,28,'Diajukan',1,0,'C',1);		
			$pdf->Cell(95,14,'Diperiksa',1,0,'C',1);
			$pdf->Cell(115,14,'Disetujui Pejabat Berwenang',1,0,'C',1);	
			//$pdf->Cell(115,15,'Disetujui Pejabat Berwenang',1,0,'C',1);
			$pdf->Cell(45,28,'Posting',1,0,'C',1);
			$pdf->Ln(14);
			$pdf->SetX(73.5);	
			$pdf->Cell(45,14,'Spv. Keu',1,0,'C',1);		
			$pdf->Cell(49.75,14,'Mgr. Keu',1,0,'C',1);
			$pdf->Cell(57.5,14,'Direktur',1,0,'C',1);
			$pdf->Cell(57.5,14,'Direktur Utama',1,0,'C',1);
			$pdf->SetX(485);
			$pdf->Cell(1,14,'(..........................................................................)',0,1,'C',1);	
		}	
		else
		{
			$pdf->Cell(45,28,'Diajukan',1,0,'C',1);		
			$pdf->Cell(95,14,'Diperiksa',1,0,'C',1);
			$pdf->Cell(115,28,'Disetujui Pejabat Berwenang',1,0,'C',1);	
			//$pdf->Cell(115,15,'Disetujui Pejabat Berwenang',1,0,'C',1);
			$pdf->Cell(45,28,'Posting',1,0,'C',1);
			$pdf->Ln(14);
			$pdf->SetX(73.5);	
			$pdf->Cell(45,14,'Spv. Keu',1,0,'C',1);		
			$pdf->Cell(49.75,14,'Mgr. Keu',1,0,'C',1);
			$pdf->SetX(485);
			$pdf->Cell(1,14,'(..........................................................................)',0,1,'C',1);
		}
		
			$pdf->Cell(45,84,'',1,0,'C',1);		
			$pdf->Cell(45,84,'',1,0,'C',1);	
			$pdf->Cell(49.75,84,'',1,0,'C',1);
		if ($kodepemisah=='FBHO' || $kodepemisah=='FPHO' || $kodepemisah=='BFSL' || $kodepemisah=='SPSL' || $kodepemisah=='USHO')
		{		
			$pdf->Cell(57.5,84,'',1,0,'C',1);	
			$pdf->Cell(57.5,84,'',1,0,'C',1);	
		}
		else
		{
			$pdf->Cell(115.5,84,'',1,0,'C',1);	
		}
			$pdf->Cell(44.75,84,'',1,0,'C',1);	
			
			
			
			
			
			$pdf->SetX(485);
			$pdf->Cell(1,14,'Penerima / Penyetor',0,1,'C',1);
			$pdf->SetX(328);		
			$pdf->Cell(170,14,'Budget',1,0,'C',1);
			$pdf->Cell(70,14,'Paraf',1,0,'C',1);
			$pdf->Ln(13);
			$pdf->SetX(328);
			$pdf->Cell(170,57,'',1,0,'C',1);
			$pdf->Cell(70,57,'',1,0,'C',1);
			
			$pdf->Ln(1);
			$pdf->SetX(330);	
			$pdf->Cell(1,14,'Pos Anggaran No.',0,1,'L',1);
			$pdf->SetX(330);
			$pdf->Cell(1,14,'Sisa Awal Anggaran',0,0,'L',1);
			$pdf->SetX(425);
			$pdf->Cell(1,14,': Rp.',0,1,'L',1);
			$pdf->SetX(330);
			$pdf->Cell(1,14,'Diajukan',0,0,'L',1);
			$pdf->SetX(425);
			$pdf->Cell(1,14,': Rp.',0,1,'L',1);
			$pdf->SetX(330);
			$pdf->Cell(1,14,'Sisa Dapat Dipakai',0,0,'L',1);
			$pdf->SetX(425);
			$pdf->Cell(1,14,': Rp.',0,1,'L',1);
			
	
			
		$pdf->Output();
            
	break;

	
	

	
	
	
	
	default:
	break;

}
?>