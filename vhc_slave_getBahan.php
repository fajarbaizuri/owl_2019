<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$notransaksi=$_GET['notransaksi'];

	



        $str="select * from ".$dbname.".vhc_servicedt_vw where notransaksi ='".$notransaksi."'";   
  

echo "<fieldset><legend>Print Excel</legend><img onclick=\"parent.detailKeExcel(event,'vhc_slave_getBahan.php?type=excel&notransaksi=".$notransaksi."')\" src=images/excel.jpg class=resicon title=\"MS.Excel\"></fieldset>";
if($_GET['type']=='excel')$border=1; else $border=0;

$stream="<table class=sortable border=".$border." cellspacing=1>
      <thead>
        <tr class=rowcontent>
          <td>No</td>
		  <td>No. Transaksi</td>
		  <td>Kode barang</td>
          <td>Barang</td>
          <td>Jumlah</td>
          <td>Satuan</td>
          <td>Keterangan</td>
        </tr>
      </thead>
      <tbody>";
    $res=mysql_query($str);
    $no=0;

    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
	$stream.="<tr class=rowcontent>
           <td>".$no."</td>
		   <td>".$bar->notransaksi."</td>
		   <td>".$bar->kodebarang."</td>
           <td>".$bar->namabarang."</td>               
           <td align=right>".$bar->jumlah."</td>
           <td align=right>".$bar->satuan."</td>  
           <td align=right>".$bar->keterangan."</td>  
         </tr>";

		    
    } 


   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
		$tglSkrg=date("Ymd");
		$nop_="Detail_Biaya_Sparepart_AlatBerat_Kendaraan".$tglSkrg;
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