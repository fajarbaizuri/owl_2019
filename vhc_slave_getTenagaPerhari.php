<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$kodeorg=$_GET['kodeorg'];
$idkary=$_GET['idkary'];
$periode=$_GET['periode'];



	$nama="";
	$jabatan="";
	
$str="SELECT a.namakaryawan,b.namajabatan
FROM ".$dbname.".`datakaryawan` a left join ".$dbname.".sdm_5jabatan b ON a.kodejabatan=b.kodejabatan where a.idkaryawan='".trim($idkary)."';";
	  
$res1=mysql_query($str);
while($bar1=mysql_fetch_object($res1))
{
	$nama=$bar1->namakaryawan;
	$jabatan=$bar1->namajabatan;
	
	
}


$stra="select 
posting,
pemilikalat,
idkaryawan,namakaryawan,namajabatan,if(kodegolongan='BHL','KHL','KHT') AS kodegolongan,
notransaksi,`tgl`,`tanggal`,
kodevhc,namajenisvhc,CONCAT (`kodevhc`,' [',`nopol`,']') AS kendaraan,
sum(DISTINCT ifnull(`total`,0)) AS `total`,sum(DISTINCT ifnull(`convwaktu`,0)) AS `convwaktu`,
ifnull(`satuk`,'-') AS `satuk`,
sum(DISTINCT upahpro) AS `upah`,
sum(DISTINCT premipro) AS `premi`,
sum(DISTINCT umknpro) AS `umkn`,
sum(DISTINCT insentivpro) AS `insentiv`,
sum(DISTINCT upahpro+premipro+umknpro+insentivpro) AS `totalgaji`
 FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$periode."' and pemilikalat like '".$kodeorg."' AND idkaryawan='".$idkary."' and posting=1 group BY pemilikalat,tanggal,idkaryawan order by tanggal,idkaryawan asc";
echo "<fieldset><legend>Print Excel</legend><img onclick=\"parent.detailKeExcel(event,'vhc_slave_getTenagaPerhari.php?type=excel&kodeorg=".$kodeorg."&idkary=".$idkary."&periode=".$periode."')\" src=images/excel.jpg class=resicon title=\"MS.Excel\"></fieldset>";


if($_GET['type']=='excel'){
	$border=1;
	
$stream="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>";
			 
				$stream.="<td align=center 	colspan=13>Laporan Monitoring Penghasilan Perhari Tenaga Peralat/Kendaraan</td>";
			 
			$stream.="</tr>
			<tr>
			  <td align=center colspan=13></td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Periode</td>	
			  <td align=left colspan=5>: ".$periode."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Nama Karyawan</td>	
			  <td align=left colspan=5>: ".$nama."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Jabatan</td>	
			  <td align=left colspan=5>: ".$jabatan."</td>		
			</tr>			
			<tr>
			  <td align=center colspan=13></td>		
			</tr>";
		$stream .="</thead><tbody></tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";	
}else{
	$stream="";
	$border=0;
}
	 

$stream.="<table class=sortable border=".$border." cellspacing=1>
	  <thead>
		    <tr>
			  <td align=center rowspan=2>Unit</td>
			  <td align=center rowspan=2>No. Transaksi</td>
			  <td align=center rowspan=2>Tanggal</td>
			  <td align=center rowspan=2>Kendaraan</td>
			  <td align=center rowspan=2>Type</td>";
			  

			  $stream.="<td align=center colspan=3>Waktu Operasional Alat Berat/Kendaraan</td>";
			  $stream.="<td align=center colspan=5>Penghasilan Karyawan</td>";
			 
			  
			$stream.="</tr>
			 <tr>
			  <td align=center >Total Aktual</td>
			  <td align=center >Total Konversi</td>
			  <td align=center >Satuan</td>
			  <td align=center >Upah</td>
			  <td align=center >Premi</td>
			  <td align=center >Uang Makan</td>
			  <td align=center >Insentive</td>
			  <td align=center >Total</td>";
			
			  $stream.="
			</tr>  
		 </thead>
      <tbody>";
    $res=mysql_query($stra);
    
	$A1=0;
	$A2=0;
	$A3="";
	$A4=0;
	$A5=0;
	$A6=0;
	$A7=0;
	$A8=0;
    
    while($bar= mysql_fetch_object($res))
    {
       
$stream.="<tr class=rowcontent>
			<td align=center>".$bar->pemilikalat."</td>
			<td align=center>".$bar->notransaksi."</td>  
			
			<td align=center>".$bar->tgl."</td>
			<td align=center>".$bar->kendaraan."</td>   
			
			 
			<td align=center>".$bar->namajenisvhc."</td>      
			<td align=right>".number_format($bar->total,2)."</td>    
			<td align=right>".number_format($bar->convwaktu,2)."</td>    
			<td align=center>".$bar->satuk."</td>    
			<td align=right>".number_format($bar->upah,2)."</td>   
			<td align=right>".number_format($bar->premi,2)."</td>   
			<td align=right>".number_format($bar->umkn,2)."</td>   
			<td align=right>".number_format($bar->insentiv,2)."</td>   
			<td align=right>".number_format($bar->totalgaji,2)."</td>   
         </tr>";
		 
	$A1+=$bar->total;	 
	$A2+=$bar->convwaktu;
	$A3=$bar->satuk;
	$A4+=$bar->upah;	 
	$A5+=$bar->premi;	 	
	$A6+=$bar->umkn;	 
	$A7+=$bar->insentiv;	 
	$A8+=$bar->totalgaji;	 
    } 

	  $stream.="<tr class=rowcontent>
           <td colspan=5 align=center>SUBTOTAL</td>
		   <td align=right>".number_format($A1,2)."</td>
		   <td align=right>".number_format($A2,2)."</td>
		   <td align=center>".$A3."</td>
		   <td align=right>".number_format($A4,2)."</td>
		   <td align=right>".number_format($A5,2)."</td>
		   <td align=right>".number_format($A6,2)."</td>
		   <td align=right>".number_format($A7,2)."</td>
		   <td align=right>".number_format($A8,2)."</td>
         </tr>";  
   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
		$tglSkrg=date("Ymd");
		
			$nop_="Penghasilan_Tenaga_Perhari_".$tglSkrg;
		

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
      
   }
   else
   {
       echo $stream;
   }    
       
?>