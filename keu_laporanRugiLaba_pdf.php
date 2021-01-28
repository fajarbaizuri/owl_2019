<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$unit=$_GET['gudang'];
	$periode=$_GET['periode'];

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
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}


$a="select distinct tanggalsampai from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' ";
		//echo $a;
		$b=mysql_query($a);
		$c=mysql_fetch_assoc($b);
			$tglsampai=$c['tanggalsampai'];


#++++++++++++++++++++++++++++++++++++++++++
$kodelaporan='INCOME STATEMENT';

$periodesaldo=str_replace("-", "", $periode);
$tahunini=substr($periodesaldo,0,4);

#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);
$NAMABLN=date('F',$t);
#ambil format mesinlaporan==========
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);

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
       global $captionCUR;
	   global $tglsampai;
	   global $logo;
	   global $NAMABLN;
	   //global $gudangl
	   
/*	  	$a="select distinct tanggalsampai from ".$dbname.".setup_periodeakuntansi where periode='".$periode."'";
		//echo $a;
		$b=mysql_query($a);
		$c=mysql_fetch_assoc($b);
			$tglsampai=$c['tanggalsampai'];*/
			/*
			$bln=substr($tglsampai,6,1);
			$tgla=substr($tglsampai,8,2);
			$thna=substr($tglsampai,0,4);
			*/
			$thna=substr($periode,0,4);
			$bln=substr($periode,6,2);
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
		$this->Cell(20,3,'LAPORAN LABA RUGI','',0,'L');
		$this->SetFont('Arial','',8);
		$this->SetX(160);
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$this->PageNo(),'',1,'L');
		
		$this->SetFont('Arial','B',8);
		$this->SetX(30);
		//$this->Cell(20,3,'PERIODE '.$tgla.' '.strtoupper(numToMonth($bln,$lang='I',$format='long')).' '.$thna,'',0,'L');
		//$this->Cell(20,3,'PERIODE '.strtoupper(numToMonth($bln,$lang='I',$format='long')).' '.$thna,'',0,'L');
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
      //  $this->Line(10,30,200,30);
		//$this->lin
        $this->Ln(20);
        $this->Cell(110,5,'','',0,'L');
        $this->Cell(30,5,$captionCUR,'B',0,'R');
        $this->Cell(30,5,'YTD','B',1,'R');
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

$tnow2=0;
$ttill2=0;
$tnow3=0;
$ttill3=0;

while($bar=mysql_fetch_object($res))
{
    if($bar->tipe=='Header')
      {
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10,5,'','',0,'C');
        $pdf->Cell(100,5,$bar->keterangandisplay,'',0,'L');
        $pdf->Cell(30,5,'','',0,'C');
        $pdf->Cell(30,5,'','',1,'C'); 
      }
    else
    {
        #ambil saldo akhir periode barjalan sebagai akumulasi
      /*  $st12="select sum(".$kolomCUR.") as akumilasi
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periodCUR."' and ".$where;
        //echo $st12;
        */
        $st12="select sum(awal".substr($periodesaldo,4,2).")+sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as akumilasi
        from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
        and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where; 
        $res12=mysql_query($st12);
        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
        #mutasi bulan berjalan
        $st13="select sum(debet) - sum(kredit) as sekarang
               from ".$dbname.".keu_jurnalsum_vw where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periode."' and ".$where;
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
                    if($bar->variableoutput=='2')
                    {
                        $jlhsekarang=$tnow2;
                        $akumulasi=$ttill2; 
                        $tnow2=0;
                        $ttill2=0;
                    }
                    if($bar->variableoutput=='3')
                    {
                        $jlhsekarang=$tnow3;
                        $akumulasi=$ttill3; 
                        $tnow3=0;
                        $ttill3=0;
                    }                                        
                }       
  #==============================================================
				  $TT1=number_format(abs($jlhsekarang));  
				  $TT2=number_format(abs($akumulasi));						  
				if($bar->noakundari=='5110101' and substr($bar->keterangandisplay,0,4)=='LABA' and $jlhsekarang>0)
				{
				   $TT1="(".number_format(abs($jlhsekarang)).")";
				}
				else
				{
					  $TT1=number_format(abs($jlhsekarang));		
				}
				if($bar->noakundari=='5110101' and substr($bar->keterangandisplay,0,4)=='LABA' and $akumulasi>0)
				{
				   $TT2="(".number_format(abs($akumulasi)).")";
				}
				else
				{
					  $TT2=number_format(abs($akumulasi));		
				}
  #======================================================================			
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(10,5,'','',0,'C');
            $pdf->Cell(5,5,'','',0,'L');
            $pdf->Cell(95,5,$bar->keterangandisplay,'',0,'L');
            $pdf->Cell(30,5,$TT1,'T',0,'R');
            $pdf->Cell(30,5,$TT2,'T',1,'R'); 
            $pdf->Ln();
        }
        else
        {
			  $TT1=number_format(abs($jlhsekarang));
			  $TT2=number_format(abs($akumulasi)); 
 #escape yang nilainya nol
            //if($jlhsekarang==0 and $akumulasi==0)
            //    continue;
            //else{
                $pdf->Cell(10,5,'','',0,'C');
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(10,5,'','',0,'L');
                $pdf->Cell(90,5,$bar->keterangandisplay,'',0,'L');
                $pdf->Cell(30,5,$TT1,'',0,'R');
                $pdf->Cell(30,5,$TT2,'',1,'R'); 
            //}
        }   
    }   
}

$pdf->Output();		
?>