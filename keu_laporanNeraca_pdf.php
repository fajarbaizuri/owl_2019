<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/nangkoelib.php');

/*
	$pt=$_GET['pt'];
	$unit=$_GET['gudang'];
	$periode=$_GET['periode'];
	*/
	$pt=$_GET['pt'];
$unit=$_GET['gudang'];//kebun
$periode=$_GET['periode'];
$periode1=$_GET['periode1'];
$gudang=$_GET['gudang'];
$revisi=$_GET['revisi'];
$qwe=explode('-',$periode);
$tahun=$qwe[0];
$tahunlalu=$tahun-1;
$bulan=$qwe[1];
//ambil logo
switch ($pt) {
    case "FBB":
        if ($unit=="CBGM")
		{$logo="images/logo2.jpg";}
		elseif ($unit=="TDAE")
		{$logo="images/logo2.jpg";}
		elseif ($unit=="TDBE")
		{$logo="images/logo2.jpg";}
		elseif ($unit=="TKFB")
		{$logo="images/logo2.jpg";}
		else
		{$logo="images/logo.jpg";}
        break;
    case "FPS":
        if ($unit=="FPHO")
		{$logo="images/logo4.jpg";}
		elseif ($unit=="BFSL")
		{$logo="images/logo4.jpg";}
		elseif ($unit=="SPSL")
		{$logo="images/logo4.jpg";}
		else
		{$logo="images/logo4.jpg";}
        break;
    case "GME":
        $logo="images/logo.jpg";
        break;
	case "USJ":
        if ($unit=="USHO")
		{$logo="images/logo3.jpg";}
		elseif ($unit=="USJE")
		{$logo="images/logo3.jpg";}
		elseif ($unit=="CLGE")
		{$logo="images/logo3.jpg";}
		else
		{$logo="images/logo3.jpg";}
        break;	
}
#================
#===========================================
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}

$a="select distinct tanggalsampai from ".$dbname.".setup_periodeakuntansi where periode='".$periode."'";
		//echo $a;
		$b=mysql_query($a);
		$c=mysql_fetch_assoc($b);
			$tglsampai=$c['tanggalsampai'];


#++++++++++++++++++++++++++++++++++++++++++
$kodelaporan='BALANCE SHEET';

$periodesaldo=str_replace("-", "", $periode);
#lalu
//$periodPRF=substr($periodesaldo,0,4)."01";
//$kolomPRF="awal01";#"awal".substr($periodesaldo,4,2);
if($periode1=='akhir')$periodPRF=substr($periodesaldo,0,4)."01"; else $periodPRF=$tahunlalu.$bulan;
if($periode1=='akhir')$periodPRF2=substr($periodesaldo,0,4)."-01"; else $periodPRF2=$tahunlalu."-".$bulan;
if($periode1=='akhir')$kolomPRF="awal01"; else $kolomPRF="awal".date('m',$t); #"awal".substr($periodesaldo,4,2);
#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);
$periodCUR2=substr($periodesaldo,0,4).'-'.substr($periodesaldo,4,2);
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);
$NAMABLN=date('F',$t);
$lastmonth=date('M-Y', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));

$periodeCUR=date('Ym',$t);
$periodeLAST=date('Ym', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));

$bulanCUR=date('m',$t);
$bulanLAST=date('m', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));





#ambil format mesinlaporan==========
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);
#print_r($res);

#exit("error:$res");
#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";

#==========================create page
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
       global $unit;
	   global $periodeCUR;
	   global $periodeLAST;
	   global $bulanCUR;
	   global $bulanLAST;
       global $captionCUR;
       global $lastmonth;
	   global $tglsampai;
	   global $logo;
	   global $NAMABLN;
	   
	   	$bln=substr($tglsampai,6,1);
			$tgla=substr($tglsampai,8,2);
			$thna=substr($tglsampai,0,4);
			//echo $bln;
		$path=$logo;
		$this->Image($path,10,7,17);
        $this->SetFont('Arial','B',8); 
		$this->SetX(30);
		$this->Cell(20,3,$namapt,'',0,'L');
		
		$this->SetFont('Arial','',8); 
		$this->SetX(160);
		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
		
		
		$this->SetFont('Arial','B',8); 
		$this->SetX(30);
		$this->Cell(20,3,'NERACA','',0,'L');
		$this->SetFont('Arial','',8);
		$this->SetX(160);
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$this->PageNo(),'',1,'L');
		
		$this->SetFont('Arial','B',8);
		$this->SetX(30);
		//$this->Cell(20,3,'PERIODE '.$tgla.' '.strtoupper(numToMonth($bln,$lang='I',$format='long')).' '.$thna,'',0,'L');
		$this->Cell(20,3,'PERIODE '.strtoupper($NAMABLN).' '.$thna,'',0,'L');
		$this->SetFont('Arial','',8);
		$this->SetX(160);
		$this->Cell(15,3,'User','',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
    	
	
	
	
	
	   	if($unit=='')//$int,$lang='E',$format='short'
		{
		}
		else
		{	
			$this->SetFont('Arial','B',8);
			$this->SetX(30);
			$this->Cell(20,3,"UNIT : ".$unit,'',1,'L');
		}
        //$this->Line(10,27,200,27);
		$this->Ln(20);
        $this->Cell(110,5,'','',0,'L');
        $this->Cell(30,5,$lastmonth,'B',0,'R');
        $this->Cell(30,5,$captionCUR,'B',0,'R');
		$this->Cell(30,5,'(Naik/Turun)','B',0,'R');
        $this->Ln();
    }
 	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}   
}

$pdf=new PDF('P','mm','A4');
$pdf->AddPage();
while($bar=mysql_fetch_object($res))
{
    if($bar->tipe=='Header')
      {
	  if($bar->nourut==1410) {
		$pdf->AddPage();
		}
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10,5,'','',0,'C');
        $pdf->Cell(100,5,$bar->keterangandisplay,'',0,'L');
        $pdf->Cell(30,5,'','',0,'C');
        $pdf->Cell(30,5,'','',0,'C'); 
		$pdf->Cell(30,5,'','',1,'C'); 
      }
    else
    {
            #mutasi bulan sebelumnya
		$st12="select sum(awal".$bulanLAST.")+sum(debet".$bulanLAST.") - sum(kredit".$bulanLAST.") as akumilasi
                from ".$dbname.".keu_saldobulanan where noakun >= '".$bar->noakundari."' 
                and noakun <= '".$bar->noakunsampai."' and  periode='".$periodeLAST."' and ".$where; 
        $res12=mysql_query($st12);        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
		
        #mutasi bulan berjalan
        $st13="select sum(awal".$bulanCUR.")+sum(debet".$bulanCUR.") - sum(kredit".$bulanCUR.") as sekarang
               from ".$dbname.".keu_saldobulanan where noakun  >= '".$bar->noakundari."' 
               and noakun <='".$bar->noakunsampai."' and  periode='".$periodeCUR."' and ".$where;
        $res13=mysql_query($st13);
        $jlhsekarang=0;
        while($ba13=mysql_fetch_object($res13))
        {
            $jlhsekarang=$ba13->sekarang;
        }
		
		
		
		
        $tnow2+=$jlhsekarang;
        $ttill2+=$akumulasi;
        $tnow3+=$jlhsekarang;
        $ttill3+=$akumulasi;
		
            
        if($bar->tipe=='Total'){    
			if($bar->noakundari=='' or $bar->noakunsampai=='')
                {
                    if($bar->variableoutput=='1')
                    {
                        $jlhsekarang=$tnow2;
                        $akumulasi=$ttill2; 
                        $tnow2=0;
                        $ttill2=0;
                    }
                    if($bar->variableoutput=='2')
                    {
                        $jlhsekarang=$tnow3;
                        $akumulasi=$ttill3; 
                        $tnow3=0;
                        $ttill3=0;
                    }                 
					
            }   
				 $TT1=number_format(round($jlhsekarang));  
				  $TT2=number_format(round($akumulasi));
				  
				  //$TT3=number_format(bs(ceil($jlhsekarang))-ceil($akumulasi));
				  if($bar->noakundari >=2110101 and $bar->noakundari <=3110104) {
						$TT2=number_format(round($akumulasi * -1));
						$TT1=number_format(round($jlhsekarang * -1));  
				  }
				  
				  
				  
				  $TT3=$jlhsekarang-$akumulasi;
				  $TT3=number_format(round($TT3));  
				  
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(10,5,'','',0,'C');
            $pdf->Cell(5,5,'','',0,'L');
            $pdf->Cell(95,5,$bar->keterangandisplay,'',0,'L');
			
            $pdf->Cell(30,5,$TT2,'TB',0,'R');
            $pdf->Cell(30,5,$TT1,'TB',0,'R');
            $pdf->Cell(30,5,$TT3,'TB',1,'R'); 
			
            $pdf->Ln();
			
		if($bar->keterangandisplay=='JUMLAH KEWAJIBAN DAN EKUITAS'){
			$PASIVATT1=$akumulasi;
			$PASIVATT2=$jlhsekarang;
			$PASIVATT3=$PASIVATT2-$PASIVATT1;
			}
			
			if($bar->keterangandisplay=='JUMLAH AKTIVA'){
			$AKTIVATT1=$akumulasi;
			$AKTIVATT2=$jlhsekarang;
			$AKTIVATT3=$AKTIVATT2-$AKTIVATT1;
			}
			
			$total1=ceil($AKTIVATT1)+ceil($PASIVATT1);
			$total2=ceil($AKTIVATT2)+ceil($PASIVATT2);
			$total3=ceil($AKTIVATT3)+ceil($PASIVATT3);
			
        }
        else
        {
			$TT1=number_format(round($jlhsekarang));  
				  $TT2=number_format(round($akumulasi));
				  
				  //$TT3=number_format(bs(ceil($jlhsekarang))-ceil($akumulasi));
				  if($bar->noakundari >=2110101 and $bar->noakundari <=3110104) {
						$TT2=number_format(round($akumulasi * -1));
						$TT1=number_format(round($jlhsekarang * -1));  
						
						
						
				  
				  }
				  $TT3=$jlhsekarang-$akumulasi;
				  $TT3=number_format(round($TT3));  
				  
            #escape yang nilainya nol
#            if($jlhsekarang==0 and $jlhlalu==0)
#                continue;
#            else{
                $pdf->Cell(10,4,'','',0,'C');
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(10,4,'','',0,'L');
                $pdf->Cell(90,4,$bar->keterangandisplay,'',0,'L');
				 $pdf->Cell(30,4,$TT2,'',0,'R');
				$pdf->Cell(30,4,$TT1,'',0,'R');
				$pdf->Cell(30,4,$TT3,'',1,'R'); 
				
                
#            }
        }   
    }   
}

$pdf->Output();		
?>