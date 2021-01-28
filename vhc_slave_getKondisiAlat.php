<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$kodeorg=$_GET['kodeorg'];
$kendaraan=$_GET['kendaraan'];
$kelompok=$_GET['kelompok'];
$periode=$_GET['periode'];



	$pemilik="";
	$plat="";
	$tipe="";
$str="SELECT a.kodevhc,a.kodeorg,a.jenisvhc,a.nopol,b.namajenisvhc
FROM ".$dbname.".`vhc_5master` a left join ".$dbname.".vhc_5jenisvhc b ON a.jenisvhc=b.jenisvhc where a.kodevhc='".trim($kendaraan)."';";
	  
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$pemilik=$bar->kodeorg;
	$plat=$bar->nopol;
	$tipe=$bar->namajenisvhc;
	
}



$str="SELECT 
posting,
kodeorg,
`tgl`,`tanggal`,petugas,
kodevhc,namajenisvhc,CONCAT (`kodevhc`,' [',`nopol`,']') AS kendaraan,
`pemilikalat`,`kondisi`, `kondisinm`,
sum(DISTINCT ifnull(`total`,0)) AS `total`,sum(DISTINCT ifnull(`convwaktu`,0)) AS `convwaktu`,
ifnull(`satuk`,'-') AS `satuk`,sum(DISTINCT `bbmpro`) AS `bbm`,
MAX(CASE WHEN petugas = 1 THEN namakaryawan END) AS operator,
MAX(CASE WHEN petugas = 2 THEN namakaryawan END)  AS helper
FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$periode."' and kodeorg like '".$kodeorg."' and kodevhc like '".$kendaraan."%' and posting=1 
group BY kodeorg,tanggal,`kodevhc` order by kodeorg,tanggal asc";
 //$str="select * from ".$dbname.".vhc_kendaraan_detail_vw where kodeorg ='".$kodeorg."' and tanggal ='".$tgl."' and kodevhc ='".$kendaraan."'   order by tgl,kodekegiatan,rit asc";   
  

echo "<fieldset><legend>Print Excel</legend><img onclick=\"parent.detailKeExcel(event,'vhc_slave_getKondisiAlat.php?type=excel&kodeorg=".$kodeorg."&kendaraan=".$kendaraan."&kelompok=".$kelompok."&periode=".$periode."')\" src=images/excel.jpg class=resicon title=\"MS.Excel\"></fieldset>";

$nama=array('KD'=>'Kendaraan','AB'=>'Alat Berat');
if($_GET['type']=='excel'){
	$border=1;
	
$stream="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>";
			 if($_GET['kelompok']=='AB'){
				$stream.="<td align=center 	colspan=9>Laporan Monitoring Kondisi Alat Berat</td>";
			 }else{
				$stream.="<td align=center 	colspan=9>Laporan Monitoring Kondisi Kendaraan</td>";
			 }
			$stream.="</tr>
			<tr>
			  <td align=center colspan=9></td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Kelompok</td>	
			  <td align=left colspan=5>: ". $nama[$kelompok]."</td>		
			  <td align=left >Pemilik</td>	
			  <td align=left >: ".$pemilik."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Plat No.</td>	
			  <td align=left colspan=5>: ".$kendaraan ."[".$plat."]</td>		
			  <td align=left >Periode</td>	
			  <td align=left >: ".$periode."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Type</td>	
			  <td align=left colspan=5>: ".$tipe ." </td>		
			</tr>
			<tr>
			  <td align=center colspan=9></td>		
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
			  <td align=center rowspan=2>Tanggal</td>";
			  if($_GET['kelompok']=='AB'){
			  $stream.="<td align=center rowspan=2>Kondisi Alat Berat</td>
			  <td align=center colspan=3>Waktu Operasional Alat Berat</td>
			  ";
			  }else{
			  $stream.="<td align=center rowspan=2>Kondisi Kendaraan</td>
			  <td align=center colspan=3>Waktu Operasional kendaraan</td>
			  ";
			  }
			  $stream.="<td align=center rowspan=2>Pemakaian BBM (Ltr)</td>";
			  if($_GET['kelompok']=='AB'){
			  $stream.="<td align=center colspan=2>Petugas Alat Berat</td>";
			  }else{
			  $stream.="<td align=center colspan=2>Petugas Kendaraan</td>";
			  }
			  
			$stream.="</tr>
			 <tr>
			  <td align=center >Total Aktual</td>
			  <td align=center >Total Konversi</td>
			  <td align=center >Satuan</td>";
			  if($_GET['kelompok']=='AB'){
			  $stream.="
			  <td align=center >Operator </td>
			  <td align=center >Helper</td>";
			  }else{
			  $stream.="
			  <td align=center >Sopir</td>
			  <td align=center >Kernet</td>"; 	  
			  }
			  $stream.="
			</tr>  
		 </thead>
      <tbody>";
    $res=mysql_query($str);
    
	$A1=0;
	$A2=0;
	$A3="";
	$A4=0;
    
    while($bar= mysql_fetch_object($res))
    {
       
$stream.="<tr class=rowcontent>
			<td align=center>".$bar->kodeorg."</td>
			<td align=center>".$bar->tgl."</td>
			<td align=center>".$bar->kondisinm."</td>      
			<td align=right>".number_format($bar->total,2)."</td>    
			<td align=right>".number_format($bar->convwaktu,2)."</td>    
			<td align=center>".$bar->satuk."</td>    
			<td align=right>".$bar->bbm."</td>      		   
			<td align=center>".$bar->operator."</td>  
			<td align=center>".$bar->helper."</td>  
         </tr>";
		 
	$A1+=$bar->total;	 
	$A2+=$bar->convwaktu;
	$A3=$bar->satuk;
	$A4+=$bar->bbm;	    
    } 

	  $stream.="<tr class=rowcontent>
           <td colspan=3 align=center>SUBTOTAL</td>
		   <td align=right>".number_format($A1,2)."</td>
		   <td align=right>".number_format($A2,2)."</td>
		   <td align=center>".$A3."</td>
		   <td align=right>".number_format($A4,2)."</td>
		   <td align=right colspan=2></td>
         </tr>";  
   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
		$tglSkrg=date("Ymd");
		if($_GET['kelompok']=='AB'){
			$nop_="Laporan_Monitoring_Kondisi_Alat_Berat_".$tglSkrg;
		}else{
			$nop_="Laporan_Monitoring_Kondisi_Kendaraan_".$tglSkrg;	
		}

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