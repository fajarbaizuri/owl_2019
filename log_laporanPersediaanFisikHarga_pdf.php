<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}	
//=======================================	

if($periode=='' and $gudang=='')
{
	$str="select a.kodebarang,sum(a.saldoqty) as kuan, 
	      b.namabarang,b.satuan,a.kodeorg from ".$dbname.".log_5masterbarangdt a
		  left join ".$dbname.".log_5masterbarang b
		  on a.kodebarang=b.kodebarang
		  where kodeorg='".$pt."' group by a.kodeorg,a.kodebarang order by kodebarang";
}
else if($periode=='' and $gudang!='')
{
	$str="select a.kodebarang,sum(a.saldoqty) as kuan, 
	      b.namabarang,b.satuan from ".$dbname.".log_5masterbarangdt a
		  left join ".$dbname.".log_5masterbarang b
		  on a.kodebarang=b.kodebarang
		  where kodeorg='".$pt."' 
		  and kodegudang='".$gudang."'
		  group by a.kodeorg,a.kodebarang  order by kodebarang";	
}
else{
	if($gudang=='')
	{
		$str="select 
			  a.kodeorg,
			  a.kodebarang,
			  sum(a.saldoakhirqty) as salakqty,
			  avg(a.hargarata) as harat,
			  sum(a.nilaisaldoakhir) as salakrp,
			  sum(a.qtymasuk) as masukqty,
			  sum(a.qtykeluar) as keluarqty,
			  avg(a.qtymasukxharga) as masukrp,
			  avg(a.qtykeluarxharga) as keluarrp,
			  sum(a.saldoawalqty) as sawalqty,
			  avg(a.hargaratasaldoawal) as sawalharat,
			  sum(a.nilaisaldoawal) as sawalrp,
		      b.namabarang,b.satuan    
		      from ".$dbname.".log_5saldobulanan a
		      left join ".$dbname.".log_5masterbarang b
			  on a.kodebarang=b.kodebarang
			  where kodeorg='".$pt."' 
			  and periode='".$periode."'
			  group by a.kodebarang order by a.kodebarang";
	}
	else
	{
		$str="select
			  a.kodeorg,
			  a.kodebarang,
			  sum(a.saldoakhirqty) as salakqty,
			  avg(a.hargarata) as harat,
			  sum(a.nilaisaldoakhir) as salakrp,
			  sum(a.qtymasuk) as masukqty,
			  sum(a.qtykeluar) as keluarqty,
			  avg(a.qtymasukxharga) as masukrp,
			  avg(a.qtykeluarxharga) as keluarrp,
			  sum(a.saldoawalqty) as sawalqty,
			  avg(a.hargaratasaldoawal) as sawalharat,
			  sum(a.nilaisaldoawal) as sawalrp,
		      b.namabarang,b.satuan 		 		      
			  from ".$dbname.".log_5saldobulanan a
		      left join ".$dbname.".log_5masterbarang b
			  on a.kodebarang=b.kodebarang
			  where kodeorg='".$pt."' 
			  and periode='".$periode."'
			  and kodegudang='".$gudang."'
			  group by a.kodebarang order by a.kodebarang";		
	}	
}
//=================================================
class PDF extends FPDF {
    function Header() {
       global $namapt;
	   global $pt;
	   
	   
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(190,5,strtoupper($_SESSION['lang']['laporanstok']),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(140,5,' ','',0,'R');
		$this->Cell(15,5,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(140,5,' ','',0,'R');
		$this->Cell(15,5,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$this->PageNo(),'',1,'L');
		$this->Cell(140,5,'UNIT:'.$pt.'-'.$gudang,'',0,'L');
		$this->Cell(15,5,'User','',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$_SESSION['standard']['username'],'',1,'L');
        $this->SetFont('Arial','',4);	
		$this->SetFillColor(220,220,220);
		
		$this->Cell(5,8,'No.',1,0,'C');
		//$this->SetFillColor(220,220,220);
		$this->Cell(15,8,$_SESSION['lang']['periode'],1,0,'C');				
		$this->Cell(15,8,$_SESSION['lang']['kodebarang'],1,0,'C');	
		$this->Cell(40,8,substr($_SESSION['lang']['namabarang'],0,30),1,0,'C');
		$this->Cell(5,8,$_SESSION['lang']['satuan'],1,0,'C');		
		$this->Cell(27,4,$_SESSION['lang']['saldoawal'],1,0,'C');		
		$this->Cell(27,4,$_SESSION['lang']['masuk'],1,0,'C');
		$this->Cell(27,4,$_SESSION['lang']['keluar'],1,0,'C');
		$this->Cell(27,4,$_SESSION['lang']['saldo'],1,1,'C');
//=====================================
		$this->SetX(90);
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,0,'C');		
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['kuantitas'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(9,4,$_SESSION['lang']['totalharga'],1,1,'C');									

    }
}
//================================
if($periode=='')
{
	 $sawalQTY		='';
		 $masukQTY		='';
	 $keluarQTY		='';
	 $kuantitas=0;
		$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo $_SESSION['lang']['tidakditemukan'];
	}
	else
	{
	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('d-m-Y H:i:s');
			$kodebarang=$bar->kodebarang;
			$namabarang=$bar->namabarang; 
			$kuantitas =$bar->kuan;
			
			$pdf->Cell(5,4,$no,1,0,'C');
			$pdf->Cell(15,4,$periode,1,0,'C');	
			$pdf->Cell(15,4,$kodebarang,1,0,'L');
			$pdf->Cell(40,4,$namabarang,1,0,'L');
		    $pdf->Cell(5,4,$bar->satuan,1,0,'L');
		    $pdf->Cell(9,4,$sawalQTY,1,0,'R');
			$pdf->Cell(9,4,$sawalharga,1,0,'R');						
			$pdf->Cell(9,4,$sawalTotal,1,0,'R');	
			$pdf->Cell(9,4,$masukQTY,1,0,'R');	
			$pdf->Cell(9,4,$masukharga,1,0,'R');
			$pdf->Cell(9,4,$masukTotal,1,0,'R');
			$pdf->Cell(9,4,$keluarQTY,1,0,'R');	
			$pdf->Cell(9,4,$keluarharga,1,0,'R');	
			$pdf->Cell(9,4,$keluarTotal,1,0,'R');
			$pdf->Cell(9,4,number_format($kuantitas,2,'.',','),1,0,'R');																							
            $pdf->Cell(9,4,$salakHarga,1,0,'R');
			$pdf->Cell(9,4,$salakTotal,1,1,'R');
			
			$totak+=$sawalQTY;
			$totah+=$sawalharga;
			$totatot+=$sawalTotal;
			
			$totmk+=$masukQTY;
			$totmh+=$masukharga;
			$totmtot+=$masukTotal;
			//$a+=$kuantitas;
			
			$totkk+=$keluarQTY;
			$totkh+=$keluarharga;
			$totktot+=$keluarTotal;
			
			$totsk+=$kuantitas;
			$totsh+=$salakHarga;
			$totstot+=$salakTotal;
							
		}	
		
		$pdf->Cell(80,4,'TOTAL',1,0,'C');
		
		$pdf->Cell(9,4,$totak,1,0,'R');
		$pdf->Cell(9,4,$totah,1,0,'R');
		$pdf->Cell(9,4,$totatot,1,0,'R');
		
		$pdf->Cell(9,4,$totmk,1,0,'R');
		$pdf->Cell(9,4,$totmh,1,0,'R');
		$pdf->Cell(9,4,$totmtot,1,0,'R');
		
		$pdf->Cell(9,4,$totkk,1,0,'R');
		$pdf->Cell(9,4,$totkh,1,0,'R');
		$pdf->Cell(9,4,$totktot,1,0,'R');
		
		$pdf->Cell(9,4,$totsk,1,0,'R');
		$pdf->Cell(9,4,$totsh,1,0,'R');
		$pdf->Cell(9,4,$totstot,1,1,'R');
		
		
		
		
		$pdf->Output();	
	}
}
else
	{
		$salakqty	=0;
		$masukqty	=0;
		$keluarqty	=0;
		$sawalQTY	=0;
	 

	//
	$res=mysql_query($str);
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
			$no+=1;
			$kodebarang=$bar->kodebarang;
			$namabarang=$bar->namabarang; 
	
	
			$salakqty	=$bar->salakqty;
			$harat		=$bar->harat;
			$salakrp	=$bar->salakrp;
			$masukqty	=$bar->masukqty;
			$keluarqty	=$bar->keluarqty;
			$masukrp	=$bar->masukrp;
			$keluarrp	=$bar->keluarrp;
			$sawalQTY	=$bar->sawalqty;
			$sawalharat	=$bar->sawalharat;
			$sawalrp	=$bar->sawalrp;
 		
			$pdf->Cell(5,4,$no,1,0,'C');
			$pdf->Cell(15,4,$periode,1,0,'C');	
			$pdf->Cell(15,4,$kodebarang,1,0,'L');
			$pdf->Cell(40,4,$namabarang,1,0,'L');
		    $pdf->Cell(5,4,$bar->satuan,1,0,'L');
		    $pdf->Cell(9,4,number_format($sawalQTY,2,'.',','),1,0,'R');
			$pdf->Cell(9,4,number_format($sawalharat,2,'.',','),1,0,'R');						
			$pdf->Cell(9,4,number_format($sawalrp,2,'.',','),1,0,'R');	
			$pdf->Cell(9,4,number_format($masukqty,2,'.',','),1,0,'R');	
			$pdf->Cell(9,4,number_format($harat,2,'.',','),1,0,'R');
			$pdf->Cell(9,4,number_format($masukrp,2,'.',','),1,0,'R');
			$pdf->Cell(9,4,number_format($keluarqty,2,'.',','),1,0,'R');	
			$pdf->Cell(9,4,number_format($harat,2,'.',','),1,0,'R');	
			$pdf->Cell(9,4,number_format($keluarrp,2,'.',','),1,0,'R');
			$pdf->Cell(9,4,number_format($salakqty,2,'.',','),1,0,'R');																							
            $pdf->Cell(9,4,number_format($harat,2,'.',','),1,0,'R');
			$pdf->Cell(9,4,number_format($salakrp,2,'.',','),1,1,'R');
			
			
			$tsawalQTY+=$sawalQTY;
			$tsawalharat+=$sawalharat;
			$tsawalrp+=$sawalrp;
			
			$tmasukqty+=$masukqty;
			$tharat+=$harat;
			$tmasukrp+=$masukrp;
			
			$tkeluarqty+=$keluarqty;
			$tkeluarrp+=$keluarrp;
			
			$tsalakqty+=$salakqty;
			$tsalakrp+=$salakrp;
		}
		
	$pdf->Cell(80,4,'TOTAL',1,0,'C');	
	$pdf->Cell(9,4,number_format($tsawalQTY,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tsawalharat,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tsawalrp,2,'.',','),1,0,'R');	
	
	$pdf->Cell(9,4,number_format($tmasukqty,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tharat,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tmasukrp,2,'.',','),1,0,'R');	
	
	$pdf->Cell(9,4,number_format($tkeluarqty,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tharat,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tkeluarrp,2,'.',','),1,0,'R');	
	
	$pdf->Cell(9,4,number_format($tsalakqty,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tharat,2,'.',','),1,0,'R');	
	$pdf->Cell(9,4,number_format($tsalakrp,2,'.',','),1,1,'R');	
	
	
	
	$pdf->Output();	
 }
}	
?>