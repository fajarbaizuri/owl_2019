<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/nangkoelib.php');

//$pt="PMO";
	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$tanggalpivot=$_GET['tanggalpivot'];
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='Seluruhnya';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
	
if($gudang!='')
{
		$str="select * from ".$dbname.".aging_sch_vw
		where kodeorg = '".$gudang."' and (nilaiinvoice > dibayar or dibayar is NULL)
		";
}else
if($pt!='')
{
		$str="select * from ".$dbname.".aging_sch_vw
		where kodeorg = '".$pt."' and (nilaiinvoice > dibayar or dibayar is NULL)
		";
}else
{
		$str="select * from ".$dbname.".aging_sch_vw
		where nilaiinvoice > dibayar or dibayar is NULL
		";
}

//=================================================
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $tanggalpivot;
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,3,$namapt." per ".$tanggalpivot,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(280,3,strtoupper($_SESSION['lang']['usiahutang']),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(225,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(225,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$this->PageNo(),'',1,'L');
		$this->Cell(225,3,' ','',0,'R');
		$this->Cell(15,3,'User','',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',8);
		$this->Cell(16,5,$_SESSION['lang']['nourut'],LTR,0,'C');	
		$this->Cell(55,5,$_SESSION['lang']['noinvoice'],1,0,'L');	
		$this->Cell(25,5,'Nilai PO/Kontrak',LTR,0,'C');	
		$this->Cell(25,5,'Nilai Invoice',LTR,0,'C');	
		$this->Cell(25,5,'Belum',LTR,0,'C');	
		$this->Cell(100,5,'Sudah Jatuh Tempo',1,0,'C');
		$this->Cell(25,5,$_SESSION['lang']['dibayar'],LTR,0,'C');
//		$this->Cell(25,5,'Jumlah Hari',LTR,0,'C');
        $this->Ln();						
		$this->Cell(16,5,$_SESSION['lang']['tanggal'],LBR,0,'C');	
		$this->Cell(55,5,$_SESSION['lang']['namasupplier'],1,0,'L');	
		$this->Cell(25,5,'No PO/Kontrak',LBR,0,'C');	
		$this->Cell(25,5,'Tgl Jatuh Tempo',LBR,0,'C');	
		$this->Cell(25,5,'Jatuh Tempo',LBR,0,'C');	
		$this->Cell(25,5,'1-15 Hari',1,0,'C');
		$this->Cell(25,5,'16-30 Hari',1,0,'C');
		$this->Cell(25,5,'31-45 Hari',1,0,'C');
		$this->Cell(25,5,'over 46 Hari',1,0,'C');
		$this->Cell(25,5,'Outstanding',LBR,0,'C');
//		$this->Cell(25,5,'Outstanding',LBR,0,'C');
        $this->Ln();						
    }
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
}
function tanggalbiasa($_q)
{
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,4,4)."-".substr($_q,2,2)."-".substr($_q,0,2);
 return($_retval);
}
function tanggalbiasa2($_q)
{
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,6,2)."-".substr($_q,4,2)."-".substr($_q,0,4);
 return($_retval);
}


//================================
	$res=mysql_query($str);
	$no=0;
	if(@mysql_num_rows($res)<1)
	{
		echo$_SESSION['lang']['tidakditemukan'];
	}
	else
	{
	$pdf=new PDF('L','mm','A4');
	$pdf->AddPage();
            $total0=$total15=$total30=$total45=$total100=$totaldibayar=0;
            $totalinvoice=0;
		while($bar=mysql_fetch_object($res))
		{
			$namasupplier	=$bar->namasupplier;
			$noinvoice	=$bar->noinvoice; 
			$tanggal	=$bar->tanggal; 
			$jatuhtempo 	=$bar->jatuhtempo;
                        $nopokontrak    =$bar->nopo;
                        $nilaipo        =$bar->kurs*$bar->nilaipo;
                        $nilaikontrak   =$bar->kurs*$bar->nilaikontrak;
			$nilaiinvoice 	=$bar->kurs*$bar->nilaiinvoice;
                        $totalinvoice+=$nilaiinvoice;
			$dibayar 	=$bar->kurs*$bar->dibayar;
                        $sisainvoice    =$nilaiinvoice-$dibayar;
                        $nilaipokontrak =$nilaipo;
                        if($nilaikontrak>0)$nilaipokontrak=$nilaikontrak;
//			$date1=date('Y-m-d');
			$date1=tanggalbiasa($tanggalpivot);
			$diff =(strtotime($jatuhtempo)-strtotime($date1));
			$outstd =floor(($diff)/(60*60*24));
//			if($outstd<1)$outstd=0;
			$flag0=$flag15=$flag30=$flag45=$flag100=0;
			if($outstd!=0)$outstd*=-1;
			if($outstd<=0)$flag0=1; 
			if(($outstd>=1)and($outstd<=15))$flag15=1;
			if(($outstd>=16)and($outstd<=30))$flag30=1;
			if(($outstd>=31)and($outstd<=45))$flag45=1;
			if($outstd>45)$flag100=1;
                        if($flag0==1)$total0+=$sisainvoice;
                        if($flag15==1)$total15+=$sisainvoice;
                        if($flag30==1)$total30+=$sisainvoice;
                        if($flag45==1)$total45+=$sisainvoice;
                        if($flag100==1)$total100+=$sisainvoice;
                        $totaldibayar+=$dibayar;
			if($jatuhtempo=='0000-00-00'){ $outstd=''; $jatuhtempo=''; }else{ $jatuhtempo=tanggalnormal($jatuhtempo); }
//			if($dibayar>=$nilaiinvoice)continue;
			$no+=1;
		
		$pdf->Cell(16,5,$no,LTR,0,'L');	
//		$pdf->Cell(40,5,$namasupplier,0,0,'L');	
		$pdf->Cell(55,5,$noinvoice,LTR,0,'L');	
		$pdf->Cell(25,5,number_format($nilaipokontrak,2),LTR,0,'R');
		$pdf->Cell(25,5,number_format($nilaiinvoice,2),LTR,0,'R');
		if($flag0==1)$dummy=number_format($sisainvoice,2);	
		$pdf->Cell(25,5,$dummy,LTR,0,'R'); $dummy='';
		if($flag15==1)$dummy=number_format($sisainvoice,2);	
		$pdf->Cell(25,5,$dummy,LTR,0,'R'); $dummy='';
		if($flag30==1)$dummy=number_format($sisainvoice,2);	
		$pdf->Cell(25,5,$dummy,LTR,0,'R'); $dummy='';
		if($flag45==1)$dummy=number_format($sisainvoice,2);	
		$pdf->Cell(25,5,$dummy,LTR,0,'R'); $dummy='';
		if($flag100==1)$dummy=number_format($sisainvoice,2);	
		$pdf->Cell(25,5,$dummy,LTR,0,'R'); $dummy='';
		$pdf->Cell(25,5,number_format($dibayar,2,'.',','),LTR,1,'R');	
//		$pdf->Cell(25,5,$outstd,LTR,1,'R');	

		$pdf->Cell(16,5,tanggalbiasa2($tanggal),LBR,0,'L');	
		$pdf->Cell(55,5,$namasupplier,LBR,0,'L');	
//		$pdf->Cell(55,5,$noinvoice,LBR,0,'L');	
		$pdf->Cell(25,5,$nopokontrak,LBR,0,'L');
		$pdf->Cell(25,5,$jatuhtempo,LBR,0,'R');
		$pdf->Cell(25,5,'',LBR,0,'R');
		$pdf->Cell(25,5,'',LBR,0,'R');	
		$pdf->Cell(25,5,'',LBR,0,'R');	
		$pdf->Cell(25,5,'',LBR,0,'R');	
		$pdf->Cell(25,5,'',LBR,0,'R');	
		$pdf->Cell(25,5,$outstd." Hari",LBR,1,'R');	
//		$pdf->Cell(25,5,'',LBR,1,'R');	
		}	
//		$pdf->Cell(14,5,"",LTR,0,'L');	
//		$pdf->Cell(40,5,$namasupplier,0,0,'L');	
		$pdf->Cell(96,5,"TOTAL",1,0,'C');	
//		$pdf->Cell(25,5,"",'',0,'L');
		$dummy=number_format($totalinvoice,2);	
		$pdf->Cell(25,5,$dummy,1,0,'R');
		$dummy=number_format($total0,2);	
		$pdf->Cell(25,5,$dummy,1,0,'R');
		$dummy=number_format($total15,2);	
		$pdf->Cell(25,5,$dummy,1,0,'R');
		$dummy=number_format($total30,2);	
		$pdf->Cell(25,5,$dummy,1,0,'R');
		$dummy=number_format($total45,2);	
		$pdf->Cell(25,5,$dummy,1,0,'R');
		$dummy=number_format($total100,2);	
		$pdf->Cell(25,5,$dummy,1,0,'R');
		$pdf->Cell(25,5,number_format($totaldibayar,2,'.',','),1,0,'R');	
//		$pdf->Cell(25,5,"", TRB,1,'R');	

                $pdf->Output();	
	}

		
?>