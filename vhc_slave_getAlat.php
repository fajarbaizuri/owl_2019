<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$kodeorg=$_GET['kodeorg'];
$tgl=$_GET['tgl'];
$karyawan=$_GET['karyawan'];
$kondisi=$_GET['kondisi'];





	  





        $str="select * from ".$dbname.".vhc_kendaraan_detail_vw where pemilik ='".$kodeorg."' and tanggal ='".$tgl."' and idkaryawan ='".$karyawan."' and kondisi ='".$kondisi."'  order by tgl,kodekegiatan,rit asc";   
		$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$NAMAKAR=$bar->namakaryawan;
	$JABKAR=$bar->namajabatan;
}

echo "<fieldset><legend>Print Excel</legend><img onclick=\"parent.detailKeExcel(event,'vhc_slave_getAlat.php?type=excel&kodeorg=".$kodeorg."&tgl=".$tgl."&karyawan=".$karyawan."&kondisi=".$kondisi."')\" src=images/excel.jpg class=resicon title=\"MS.Excel\"></fieldset>";

$nama=array('KD'=>'Kendaraan','AB'=>'Alat Berat');
if($_GET['type']=='excel'){
	$border=1;
	
$stream="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>
			  <td align=center 	colspan=18>Laporan Monitoring Detail Perhari Prestasi Alat Berat/Kendaraan Per Kegiatan</td>	
			</tr>
			<tr>
			  <td align=center colspan=18></td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Nama</td>	
			  <td align=left colspan=2>: ". $NAMAKAR."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Jabatan</td>	
			  <td align=left colspan=2>: ".$JABKAR."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Tanggal</td>	
			  <td align=left colspan=2>: ".$tgl."</td>		
			</tr>
			<tr>
			  <td align=center colspan=18></td>		
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
			  <td align=center rowspan=2>No</td>
			  <td align=center rowspan=2>No Transaksi</td>	
			  <td align=center rowspan=2>Kondisi Alat</td>	
			  <td align=center rowspan=2>Plat No</td>
			  <td align=center rowspan=2>Kegiatan</td>
			  <td align=center rowspan=2>Lokasi</td>
			  <td align=center rowspan=2>Trip Ke-</td>
			  <td align=center colspan=2>Prestasi Alat/kendaraan</td>
			  <td align=center colspan=3>Waktu Operasional Alat/kendaraan</td>
			  <td align=center rowspan=2>Pemakaian BBM(ltr)</td>
			  <td align=center colspan=5>Proporsi Biaya Tenaga</td>
			</tr>  
			 <tr>
			  <td align=center >Volume</td>
			  <td align=center >Satuan</td>
			  
			  <td align=center >Total</td>
			  <td align=center >Konversi</td>
			  <td align=center >Satuan</td>
			  
			  <td align=center >Upah </td>
			  <td align=center >Premi</td>
			  <td align=center >Uang Makan</td>
			  <td align=center >Insentiv</td>
			  <td align=center >Total</td>
			</tr>  
		 </thead>
      <tbody>";
    $res=mysql_query($str);
    $no=0;
	$A1=0;
	$A2=0;
	$A3="";
	$A4="";
	$A5=0;
	$A6=0;
	$A7=0;
	$A8=0;
	$A9=0;
	
    
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
		$A9=$bar->upahpro+$bar->premipro+$bar->umknpro+$bar->insentivpro;
	$stream.="<tr class=rowcontent>
           <td>".$no."</td>
		   <td>".$bar->notransaksi."</td>
		   <td>".$bar->kondisinm."</td>
		   <td>".$bar->nopol."</td>
		   <td>".$bar->namakegiatan."</td>
           <td>".$bar->lokasi."</td>      
		   <td align=center>".$bar->rit."</td>    
<td align=right>".$bar->volume."</td>    
<td align=center>".$bar->satuan."</td>    
<td align=right>".$bar->total."</td> 
<td align=right>".$bar->convwaktu."</td>      		   
<td align=center>".$bar->satuk."</td>  
<td align=right>".number_format($bar->bbmpro,2)."</td>  

<td align=right>".number_format($bar->upahpro)."</td>  
<td align=right>".number_format($bar->premipro)."</td>  
<td align=right>".number_format($bar->umknpro)."</td>   
<td align=right>".number_format($bar->insentivpro)."</td>   
<td align=right>".number_format($A9)."</td>      		   
         </tr>";
		 
	
	$A1+=$bar->total;
	$A2+=$bar->convwaktu;
	$A3=$bar->satuk;
	$A4+=$bar->bbmpro;
	$A5+=$bar->upahpro;
	$A6+=$bar->premipro;
    $A7+=$bar->umknpro;
	$A8+=$bar->insentivpro;
	$A10+=$A9;
	
		    
    } 

	  $stream.="<tr class=rowcontent>
           <td colspan=9 align=center>SUBTOTAL</td>
		   <td align=right>".number_format($A1,2)."</td>
		   <td align=right>".number_format($A2,2)."</td>
		   <td align=center>".$A3."</td>
		   <td align=right>".number_format($A4,2)."</td>
		   <td align=right>".number_format($A5)."</td>
		   <td align=right>".number_format($A6)."</td>
		   <td align=right>".number_format($A7)."</td>
		   <td align=right>".number_format($A8)."</td>
		   <td align=right>".number_format($A10)."</td>
         </tr>";  
   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Monitoring_Detail_Perhari_Prestasi_Operator_Sopir_PerKegiatan".$tglSkrg;
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