<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];

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
	 $sawalQTY	='';
	 $sawalharga	='';
	 $sawalTotal	='';
		 $masukQTY	='';
		 $masukharga	='';
		 $masukTotal	='';
	 $keluarQTY	='';
	 $keluarharga	='';
	 $keluarTotal	='';
		 $salakHarga	='';
		 $salakTotal	='';
	 
		$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=17>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('Y-m-d H:i:s');
			$kodebarang=$bar->kodebarang;
			$namabarang=$bar->namabarang; 
			$kuantitas =$bar->kuan;
			echo"<tr  class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarangHarga(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
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
				   
				   <td align=right class=firsttd>".number_format($kuantitas,2,'.',',')."</td>
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
		echo"
			<tr class=rowcontent>
				<td colspan=5 align=center><b>".$_SESSION['lang']['total']."</td>
				<td align=right><b>".$totak."</td>
				<td align=right><b>".$totah."</td>
				<td align=right><b>".$totatot."</td>
				
				<td align=right><b>".$totmk."</td>
				<td align=right><b>".$totmh."</td>
				<td align=right><b>".$totmtot."</td>
				
				<td align=right><b>".$totkk."</td>
				<td align=right><b>".$totkh."</td>
				<td align=right><b>".$totktot."</td>
				
				<td  align=right class=firsttd><b>".$totsk."</td>
				<td align=right><b>".$totsh."</td>
				<td align=right><b>".$totstot."</td>
				
			

		</b></tr>";
	}
}
else
{
    $salakqty	=0;
    $harat	=0;
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
	if(mysql_num_rows($res)<1) {
		echo"<tr class=rowcontent><td colspan=17>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	} else {
		$tsawalQTY = $tsawalharat = $tsawalrp = 0;
		$tmasukqty = $tharat = $tmasukrp = 0;
		$tkeluarqty = $tkeluarrp = $tsalakqty = $tsalakrp = 0;
		while($bar=mysql_fetch_object($res)) {
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
			
			echo"<tr class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarangHarga(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
				  <td>".$no."</td>
				  <td>".$periode."</td>
				  <td>".$kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td>".$bar->satuan."</td>
				   <td align=right class=firsttd>".number_format($sawalQTY,2,'.',',')."</td>
				   <td align=right>".number_format($sawalharat,2,'.',',')."</td>
				   <td align=right>".number_format($sawalrp,2,'.',',')."</td>
				   
				   <td align=right class=firsttd>".number_format($masukqty,2,'.',',')."</td>
				   <td align=right>".number_format($harat,2,'.',',')."</td>
				   <td align=right>".number_format($masukrp,2,'.',',')."</td>
				   
				   <td align=right class=firsttd>".number_format($keluarqty,2,'.',',')."</td>
				   <td align=right>".number_format($harat,2,'.',',')."</td>
				   <td align=right>".number_format($keluarrp,2,'.',',')."</td>
				   
				   <td align=right class=firsttd>".number_format($salakqty,2,'.',',')."</td>
				   <td align=right>".number_format($harat,2,'.',',')."</td>
				   <td align=right>".number_format($salakrp,2,'.',',')."</td>			   
				</tr>"; 		
		}
		echo "<tr style='font-weight:bold' class=rowcontent>
		
		<td colspan=5 align=center>".$_SESSION['lang']['total']."</td>";
		//echo "<td>".$tsawalQTY."</td>";
		echo "<td></td>";
		echo "<td align=right>".number_format($tsawalharat,2,'.',',')."</td>
		<td align=right>".number_format($tsawalrp,2,'.',',')."</td>";
		
		//echo "<td>".$tmasukqty."</td>";
		echo "<td></td>";
		echo "<td align=right>".number_format($tharat,2,'.',',')."</td>
		<td align=right>".number_format($tmasukrp,2,'.',',')."</td>";
		
		//echo "<td>".$tkeluarqty."</td>";
		echo "<td></td>";
		echo "<td align=right>".number_format($tharat,2,'.',',')."</td>
		<td align=right>".number_format($tkeluarrp,2,'.',',')."</td>";
		
		//echo "<td>".$tsalakqty."</td>";
		echo "<td></td>";
		echo "<td align=right>".number_format($tharat,2,'.',',')."</td>
		<td align=right>".number_format($tsalakrp,2,'.',',')."</td></tr>";
				
	}
}	

?>