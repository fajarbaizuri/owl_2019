<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
	$tahun1 = substr($periode,0,4);
	$bulan1 = substr($periode,5,2);
	$periode1 = $periode;
	list($thn, $bln) =split("-", $periode, 2);
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
		{$logo="images/logo.jpg";}
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
		{$logo="images/logo.jpg";}
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
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namaorganisasi=strtoupper($bar->namaorganisasi);
}

	if($gudang=='')
	{
		/*
		$str="select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_jurnalsum_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."' and a.periode='".$periode1."'
		order by a.noakun, a.periode 
		";
		*/
		$str="Select z.kodeorg,z.periode,z.noakun,z.awal".$bln." as awal, ifnull(a.debet,0) as debet,ifnull(a.kredit,0) as kredit, (z.awal".$bln." + ifnull(a.debet,0)-ifnull(a.kredit,0)) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.periode='".$thn.$bln."' order by z.noakun, z.periode";
		$str1="Select z.kodeorg,z.periode,z.noakun, sum(ifnull(a.debet,0)) as debet,sum(ifnull(a.kredit,0)) as kredit, ( sum(ifnull(a.debet,0))-sum(ifnull(a.kredit,0))) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.periode <='".$thn.$bln."' GROUP BY z.kodeorg, z.noakun order by z.noakun, z.periode";
		
	}
	else
	{

		$str="Select z.kodeorg,z.periode,z.noakun,z.awal".$bln." as awal, ifnull(a.debet,0) as debet,ifnull(a.kredit,0) as kredit, (z.awal".$bln." + ifnull(a.debet,0)-ifnull(a.kredit,0)) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.kodeorg = '".$gudang."' and z.periode='".$thn.$bln."' order by z.noakun, z.periode";
		$str1="Select z.kodeorg,z.periode,z.noakun, sum(ifnull(a.debet,0)) as debet,sum(ifnull(a.kredit,0)) as kredit, (sum(ifnull(a.debet,0))-sum(ifnull(a.kredit,0))) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.kodeorg = '".$gudang."' and z.periode <='".$thn.$bln."' GROUP BY z.kodeorg, z.noakun order by z.noakun, z.periode";
		/*
		$str="select a.*,substr(a.kodeorg,1,4) as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_jurnalsum_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		left join ".$dbname.".organisasi c
		on substr(a.kodeorg,1,4)=c.kodeorganisasi
		where c.induk = '".$pt."' and substr(a.kodeorg,1,4) = '".$gudang."' and a.periode='".$periode1."' 
		order by a.noakun, a.periode 
		";
		*/
	}	

//=================================================
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
	   global $namaorganisasi;	
	   global $logo;
	   

        $this->SetFont('Arial','B',8); 
		$this->Cell(20,3,$namapt,'',1,'L');
		$this->Cell(20,3,$namaorganisasi,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(170,3,strtoupper($_SESSION['lang']['laporanneracacoba']),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(190,3,$_SESSION['lang']['periode'].' : '.substr($periode,5,2).'-'.substr($periode,0,4),0,1,'C');				
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$this->PageNo(),'',1,'L');
		$this->Cell(150,3,' ','',0,'R');
		$this->Cell(15,3,'User','',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',6);
#		$this->Cell(5,5,'No.',1,0,'C');
#		$this->Cell(14,5,$_SESSION['lang']['pt'],1,0,'C');
#		$this->Cell(18,5,$_SESSION['lang']['nojurnal'],1,0,'C');
#		$this->Cell(16,5,$_SESSION['lang']['tanggal'],1,0,'C');	
		$this->Cell(14,5,$_SESSION['lang']['noakun'],1,0,'C');	
		$this->Cell(50,5,$_SESSION['lang']['namaakun'],1,0,'C');	
		$this->Cell(30,5,$_SESSION['lang']['saldoawal'],1,0,'C');
		$this->Cell(30,5,$_SESSION['lang']['debet'],1,0,'C');
		$this->Cell(30,5,$_SESSION['lang']['kredit'],1,0,'C');
		$this->Cell(30,5,$_SESSION['lang']['saldoakhir'],1,0,'C');
        $this->Ln();						
        $this->Ln();						

    }
}
//================================
	$res=mysql_query($str);
	$res4=mysql_query($str1);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo$_SESSION['lang']['tidakditemukan'];
	}
	else
	{
	
	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
		while($bar=mysql_fetch_object($res))
		{
			if($bar->noakun<4000000){
			
			
			
			$no+=1;
			$periode=date('Y-m-d H:i:s');
			$noakun 		=$bar->noakun;
			$namaakun 		=$bar->namaakun;
			$periode 		=$bar->periode;
			//$tahun		=substr($periode,0,4);
			//$bulan 		=substr($periode,5,2);
			$kodeorg		=$bar->kodeorg; 
			$bussunitcode	=$bar->bussunitcode; 
			$induk			=$bar->induk; 
			$debet 			=$bar->debet; 
			$kredit 		=$bar->kredit;
            //$sawal		=0;
			$sawal			=$bar->awal;
			$sakhir			=$bar->akhir;
#		$pdf->Cell(5,3,$no,0,0,'C');
#		$pdf->Cell(18,3,$nojurnal,0,0,'L');
#		$pdf->Cell(18,3,$tanggal,0,0,'C');				
		$pdf->Cell(14,3,$noakun,0,0,'L');	
		$pdf->Cell(50,3,$namaakun,0,0,'L');	
		$pdf->Cell(30,3,number_format($sawal,2,'.',','),0,0,'R');	
		$pdf->Cell(30,3,number_format($debet,2,'.',','),0,0,'R');	
		$pdf->Cell(30,3,number_format($kredit,2,'.',','),0,0,'R');	
		$pdf->Cell(30,3,number_format($sakhir,2,'.',','),0,1,'R');	
		$tawal += $sawal;
		$tdebet += $debet;
		$tkredit += $kredit;
		$takhir += $sakhir;
			}
		}	
		$pdf->Ln();						
		$pdf->Cell(14,4,' ',1,0,'L');	
		$pdf->Cell(50,4,' ',1,0,'L');	
		$pdf->Cell(30,4,number_format($tawal,2,'.',','),1,0,'R');	
		$pdf->Cell(30,4,number_format($tdebet,2,'.',','),1,0,'R');	
		$pdf->Cell(30,4,number_format($tkredit,2,'.',','),1,0,'R');	
		$pdf->Cell(30,4,number_format($takhir,2,'.',','),1,1,'R');
        $pdf->Ln();						
		$pdf->AddPage();
		while($bar=mysql_fetch_object($res4))
		{
			if($bar->noakun>3999999){
			
			
			
			$no+=1;
			$periode=date('Y-m-d H:i:s');
			$noakun 		=$bar->noakun;
			$namaakun 		=$bar->namaakun;
			$periode 		=$bar->periode;
			//$tahun		=substr($periode,0,4);
			//$bulan 		=substr($periode,5,2);
			$kodeorg		=$bar->kodeorg; 
			$bussunitcode	=$bar->bussunitcode; 
			$induk			=$bar->induk; 
			$debet 			=$bar->debet; 
			$kredit 		=$bar->kredit;
            //$sawal		=0;
			//$sawal			=$bar->awal;
			$sakhir			=$bar->akhir;
#		$pdf->Cell(5,3,$no,0,0,'C');
#		$pdf->Cell(18,3,$nojurnal,0,0,'L');
#		$pdf->Cell(18,3,$tanggal,0,0,'C');				
		$pdf->Cell(14,3,$noakun,0,0,'L');	
		$pdf->Cell(50,3,$namaakun,0,0,'L');	
		$pdf->Cell(30,3,number_format(0,2,'.',','),0,0,'R');	
		$pdf->Cell(30,3,number_format($debet,2,'.',','),0,0,'R');	
		$pdf->Cell(30,3,number_format($kredit,2,'.',','),0,0,'R');	
		$pdf->Cell(30,3,number_format($sakhir,2,'.',','),0,1,'R');	
		$tawal = 0;
		$tdebet += $debet;
		$tkredit += $kredit;
		$takhir += $sakhir;
			}
		}	
		
        $pdf->Ln();						
		$pdf->Cell(14,4,' ',1,0,'L');	
		$pdf->Cell(50,4,' ',1,0,'L');	
		$pdf->Cell(30,4,number_format($tawal,2,'.',','),1,0,'R');	
		$pdf->Cell(30,4,number_format($tdebet,2,'.',','),1,0,'R');	
		$pdf->Cell(30,4,number_format($tkredit,2,'.',','),1,0,'R');	
		$pdf->Cell(30,4,number_format($takhir,2,'.',','),1,1,'R');
        $pdf->Ln();						
	$pdf->Output();	
	

}	
?>