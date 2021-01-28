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
a.posting,
a.kodeorg,ifnull(a.norma,'-')as norma,ifnull(a.satuannorma,'-') as satuannorma,
a.kodevhc,a.namajenisvhc,CONCAT (a.`kodevhc`,' [',a.`nopol`,']') AS kendaraan,
a.`pemilikalat`,a.kodekegiatan,a.namakegiatan,
COUNT(DISTINCT b.notransaksi,b.rit) AS rotasi,
sum(DISTINCT ifnull(a.`volume`,0)) AS `volume`,a.satuan,
sum(DISTINCT ifnull(a.`total`,0)) AS `total`,sum(DISTINCT ifnull(a.`convwaktu`,0)) AS `convwaktu`,
ifnull(a.`satuk`,'-') AS `satuk`,sum(DISTINCT a.`bbmpro`) AS `bbm`
FROM ".$dbname.".`vhc_kendaraan_detail_vw` a left JOIN ".$dbname.".vhc_kendaraan_kegiatan b ON a.notransaksi=b.notransaksi AND a.kodekegiatan=b.kodekegiatan AND a.rit=b.rit 
where date_format(a.tanggal,'%Y-%m')='".$periode."' AND a.kodeorg like '".$kodeorg."' and a.kodevhc like '".$kendaraan."%' and a.posting=1 
group BY a.kodeorg,date_format(a.tanggal,'%Y-%m'),a.`kodevhc`,a.kodekegiatan
order by a.kodeorg,a.tanggal ASC;";


  

echo "<fieldset><legend>Print Excel</legend><img onclick=\"parent.detailKeExcel(event,'vhc_slave_gePrestasiAlat.php?type=excel&kodeorg=".$kodeorg."&kendaraan=".$kendaraan."&kelompok=".$kelompok."&periode=".$periode."')\" src=images/excel.jpg class=resicon title=\"MS.Excel\"></fieldset>";

$nama=array('KD'=>'Kendaraan','AB'=>'Alat Berat');
if($_GET['type']=='excel'){
	$border=1;
	
$stream="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>";
			 if($_GET['kelompok']=='AB'){
				$stream.="<td align=center 	colspan=11>Laporan Monitoring Kondisi Alat Berat</td>";
			 }else{
				$stream.="<td align=center 	colspan=11>Laporan Monitoring Kondisi Kendaraan</td>";
			 }
			$stream.="</tr>
			<tr>
			  <td align=center colspan=11></td>	
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
			  <td align=center colspan=11></td>		
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
			  
			  <td align=center rowspan=2>Kegiatan</td>
			  <td align=center colspan=2>Norma Kegiatan</td>
			  <td align=center rowspan=2>Rotasi</td>
			  <td align=center colspan=2>Prestasi</td>
			  ";
			  if($_GET['kelompok']=='AB'){
					$stream.="<td align=center colspan=3>Waktu Operasional Alat Berat</td>";
			  }else{
					$stream.="<td align=center colspan=3>Waktu Operasional kendaraan</td>";
			  }
			  $stream.="<td align=center rowspan=2>Pemakaian BBM (Ltr)</td>";
			  
			  
			$stream.="</tr>
			 <tr>
			 <td align=center >Norma</td>
			  <td align=center >satuan</td>
			  <td align=center >Volume</td>
			  <td align=center >satuan</td>
			  <td align=center >Total Aktual</td>
			  <td align=center >Total Konversi</td>
			  <td align=center >Satuan</td>";
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
			<td >".$bar->kodekegiatan." :".$bar->namakegiatan."</td>
			<td align=center>".$bar->norma."</td>  
			<td align=center>".$bar->satuannorma."</td>  
			<td align=center>".$bar->rotasi."</td>      
			<td align=center>".$bar->volume."</td>  
			<td align=center>".$bar->satuan."</td>  
						
			<td align=right>".number_format($bar->total,2)."</td>    
			<td align=right>".number_format($bar->convwaktu,2)."</td>    
			<td align=center>".$bar->satuk."</td>    
			<td align=right>".number_format($bar->bbm,2)."</td>      		   
         </tr>";
		 
	$A1+=$bar->total;	 
	$A2+=$bar->convwaktu;
	$A3=$bar->satuk;
	$A4+=$bar->bbm;	    
    } 

	  $stream.="<tr class=rowcontent>
           <td colspan=7 align=center>SUBTOTAL</td>
		   <td align=right>".number_format($A1,2)."</td>
		   <td align=right>".number_format($A2,2)."</td>
		   <td align=center>".$A3."</td>
		   <td align=right>".number_format($A4,2)."</td>
		   
         </tr>";  
   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
		$tglSkrg=date("Ymd");
		if($_GET['kelompok']=='AB'){
			$nop_="Laporan_Monitoring_Prestasi_Alat_Berat_".$tglSkrg;
		}else{
			$nop_="Laporan_Monitoring_Prestasi_Kendaraan_".$tglSkrg;	
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