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
FROM ".$dbname.".`datakaryawan` a left join ".$dbname.".sdm_5jabatan b ON a.kodejabatan=b.kodejabatan where a.karyawanid='".$idkary."';";
	  
$res1=mysql_query($str);
while($bar1=mysql_fetch_object($res1))
{
	$nama=$bar1->namakaryawan;
	$jabatan=$bar1->namajabatan;
}



$stra="SELECT 
a.posting,
a.pemilikalat,ifnull(a.norma,'-')as norma,ifnull(a.satuannorma,'-') as satuannorma,
a.kodevhc,a.namajenisvhc,CONCAT (a.`kodevhc`,' [',a.`nopol`,']') AS kendaraan,
a.kodekegiatan,a.namakegiatan,
COUNT(DISTINCT b.notransaksi,b.rit) AS rotasi,
sum(DISTINCT ifnull(a.`volume`,0)) AS `volume`,a.satuan,
sum(DISTINCT ifnull(a.`total`,0)) AS `total`,sum(DISTINCT ifnull(a.`convwaktu`,0)) AS `convwaktu`,
ifnull(a.`satuk`,'-') AS `satuk`,
sum(DISTINCT upahpro) AS `upah`, sum(DISTINCT premipro) AS `premi`, 
sum(DISTINCT umknpro) AS `umkn`, sum(DISTINCT insentivpro) AS `insentiv`, 
sum(DISTINCT upahpro+premipro+umknpro+insentivpro) AS `totalgaji` 
FROM ".$dbname.".`vhc_kendaraan_detail_vw` a left JOIN ".$dbname.".vhc_kendaraan_kegiatan b ON a.notransaksi=b.notransaksi AND a.kodekegiatan=b.kodekegiatan AND a.rit=b.rit 
where date_format(a.tanggal,'%Y-%m')='".$periode."' AND a.kodeorg like '".$kodeorg."' and a.idkaryawan like '".$idkary."' and a.posting=1 
group BY a.kodeorg,date_format(a.tanggal,'%Y-%m'),a.`kodevhc`,a.kodekegiatan
order by a.kodeorg,a.tanggal ASC;";
echo "<fieldset><legend>Print Excel</legend><img onclick=\"parent.detailKeExcel(event,'vhc_slave_getTenagaPerkegiatan.php?type=excel&kodeorg=".$kodeorg."&idkary=".$idkary."&periode=".$periode."')\" src=images/excel.jpg class=resicon title=\"MS.Excel\"></fieldset>";


if($_GET['type']=='excel'){
	$border=1;
	
$stream="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>";
			 
				$stream.="<td align=center 	colspan=15>Laporan Monitoring Penghasilan Tenaga Peralat/Kendaraan Perkegiatan</td>";
			 
			$stream.="</tr>
			<tr>
			  <td align=center colspan=15></td>	
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
			  <td align=center colspan=15></td>		
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
			  <td align=center colspan=2>Prestasi</td>";
		$stream.="<td align=center colspan=3>Waktu Operasional Alat Berat/Kendaraan</td>";
			  $stream.="<td align=center colspan=5>Penghasilan Karyawan</td>";
			 
			  
			$stream.="</tr>
			 <tr>
			 <td align=center >Norma</td>
			  <td align=center >satuan</td>
			  <td align=center >Volume</td>
			  <td align=center >satuan</td>
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
			<td >".$bar->kodekegiatan." :".$bar->namakegiatan."</td>
			<td align=center>".$bar->norma."</td>  
			<td align=center>".$bar->satuannorma."</td>  
			<td align=center>".$bar->rotasi."</td>      
			<td align=center>".$bar->volume."</td>  
			<td align=center>".$bar->satuan."</td>
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
           <td colspan=7 align=center>SUBTOTAL</td>
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
		
			$nop_="Penghasilan_Tenaga_Perkegiatan_".$tglSkrg;
		

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