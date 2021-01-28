<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
	$stream='';
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
if($periode=='')
{
	 $sawalQTY		='';
	 $sawalharga	='';
	 $sawalTotal	='';
		 $masukQTY		='';
		 $masukharga	='';
		 $masukTotal	='';
	 $keluarQTY		='';
	 $keluarharga	='';
	 $keluarTotal	='';
		 $salakHarga	='';
		 $salakTotal	='';
	 
		$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo $_SESSION['lang']['tidakditemukan'];
	}
	else
	{
		$stream.=$_SESSION['lang']['laporanstok'].": ".$pt."-".$gudang."<br>    
		<table border=1>
			<tr>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['periode']."</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kodebarang']."</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['namabarang']."</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['satuan']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['saldoawal']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['masuk']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['keluar']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['saldo']."</td>
			</tr>
			<tr>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			</tr>";   
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('d-m-Y H:i:s');
			$kodebarang=$bar->kodebarang;
			$namabarang=$bar->namabarang; 
			$kuantitas =$bar->kuan;
			$stream.="<tr>
				  <td>".$no."</td>
				  <td>".$periode."</td>
				  <td>".$kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td>".$bar->satuan."</td>
				   <td align=right>".$sawalQTY."</td>
				   <td align=right>".$sawalharga."</td>
				   <td align=right>".$sawalTotal."</td>
				   <td align=right>".$masukQTY."</td>
				   <td align=right>".$masukharga."</td>
				   <td align=right>".$masukTotal."</td>
				   <td align=right>".$keluarQTY."</td>
				   <td align=right>".$keluarharga."</td>
				   <td align=right>".$keluarTotal."</td>
				   <td align=right class=firsttd>".number_format($kuantitas,2,'.','')."</td>
				   <td align=right>".$salakHarga."</td>
				   <td align=right>".$salakTotal."</td>			   
				</tr>";
				
				
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
		$stream.="<tr bgcolor=#DEDEDE>
				  <td colspan=5 align=center>".$_SESSION['lang']['total']."</td>
				  <td>".$totak."</td>
				  <td>".$totah."</td>
				  <td>".$totatot."</td>
				  
				  <td>".$totmk."</td>
				  <td>".$totmh."</td>
				  <td>".$totmtot."</td>
				  
				  <td>".$totkk."</td>
				  <td>".$totkh."</td>
				  <td>".$totktot."</td>
				  
				  <td>".$totsk."</td>
				  <td>".$totsh."</td>
				  <td>".$totstot."</td>
				  </tr>";
		
		
		$stream.="</table>";
	}
}
else
	{
		$salakqty	=0;
		$harat		=0;
		$salakrp	=0;
		$masukqty	=0;
		$keluarqty	=0;
		$masukrp	=0;
		$keluarrp	=0;
		$sawalQTY	=0;
		$sawalharat	=0;
		$sawalrp	=0;
		$namabarang	=0;
	 

	//
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo $_SESSION['lang']['tidakditemukan'];
	}
	else
		{
		$stream.=$_SESSION['lang']['laporanstok'].": ".$pt."-".$gudang."<br>    
		<table border=1>
			<tr>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['periode']."</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kodebarang']."</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['namabarang']."</td>
			  <td rowspan=2 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['satuan']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['saldoawal']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['masuk']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['keluar']."</td>
			  <td colspan=3 align=center bgcolor=#DEDEDE >".$_SESSION['lang']['saldo']."</td>
			</tr>
			<tr>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['kuantitas']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['hargasatuan']."</td>
			   <td align=center bgcolor=#DEDEDE >".$_SESSION['lang']['totalharga']."</td>	   
			</tr>"; 
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
				  
			$stream.="<tr>
				  <td>".$no."</td>
				  <td>".$periode."</td>
				  <td>".$kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td>".$bar->satuan."</td>
				   <td align=right class=firsttd>".number_format($sawalQTY,2,'.','')."</td>
				   <td align=right>".number_format($sawalharat,2,'.','')."</td>
				   <td align=right>".number_format($sawalrp,2,'.','')."</td>
				   <td align=right class=firsttd>".number_format($masukqty,2,'.','')."</td>
				   <td align=right>".number_format($harat,2,'.','')."</td>
				   <td align=right>".number_format($masukrp,2,'.','')."</td>
				   <td align=right class=firsttd>".number_format($keluarqty,2,'.','')."</td>
				   <td align=right>".number_format($harat,2,'.','')."</td>
				   <td align=right>".number_format($keluarrp,2,'.','')."</td>
				   <td align=right class=firsttd>".number_format($salakqty,2,'.','')."</td>
				   <td align=right>".number_format($harat,2,'.','')."</td>
				   <td align=right>".number_format($salakrp,2,'.','')."</td>			   
				</tr>"; 	
				
				
				
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
		
		$stream.="<tr bgcolor=#DEDEDE>
		
		<td colspan=5 align=5>".$_SESSION['lang']['total']."</td>
		
		<td>".$tsawalQTY."</td>
		<td>".$tsawalharat."</td>
		<td>".$tsawalrp."</td>
		
		<td>".$tmasukqty."</td>
		<td>".$tharat."</td>
		<td>".$tmasukrp."</td>
		
		<td>".$tkeluarqty."</td>
		<td>".$tharat."</td>
		<td>".$tkeluarrp."</td>
		
		<td>".$tsalakqty."</td>
		<td>".$tharat."</td>
		<td>".$tsalakrp."</td>	
		
		
		
		
		
		";
		
		$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];			
   }
}	
$nop_="MaterialBalanceWPrice";
if(strlen($stream)>0)
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
 if(!fwrite($handle,$stream))
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
?>