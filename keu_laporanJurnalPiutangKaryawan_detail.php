<?php
//IND
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   

$noakun=$_GET['noakun'];
$supplier=$_GET['supplier'];
$tgl=$_GET['tgl'];
$tgl1=$_GET['tgl1'];
$gudang=$_GET['gudang'];

if($noakun=='2110102')
{
	$str="select * from ".$dbname.".keu_jurnaldt_vw where nojurnal in(select distinct nojurnal from ".$dbname.".keu_jurnaldt_vw where noakun='".$noakun."' 
	and tanggal>='".$tgl."' and tanggal<='".$tgl1."' and kodeorg ='".$gudang."' 
	and kodesupplier='".$supplier."' or kodecustomer='".$supplier."' or nik='".$supplier."') and noakun in('1160101','1150208')";// untuk kredit barangnya =	
}
else
{
	$str="select * from ".$dbname.".keu_jurnaldt_vw where nojurnal in(select distinct nojurnal from ".$dbname.".keu_jurnaldt_vw where noakun='".$noakun."' 
	and tanggal>='".$tgl."' and tanggal<='".$tgl1."' and kodeorg ='".$gudang."' 
	and kodesupplier='".$supplier."' or kodecustomer='".$supplier."' or nik='".$supplier."')";
}
//echo $str;

//=================================================																
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"parent.detailKeExcel(event,'keu_laporanJurnalPiutangKaryawan_detail.php?type=excel&noakun=".$noakun."&supplier=".$supplier."&tgl=".$tgl."&tgl1=".$tgl1."&gudang=".$gudang."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
if($_GET['type']=='excel')$border=1; else $border=0;
$stream="<table class=sortable border=".$border." cellspacing=1>
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>No.Transaksi</td>
          <td>Tanggal</td>
          <td>No.Akun</td>
          <td>Keterangan</td>
          <td>Debet</td>
          <td>Kredit</td>
          <td>Karyawan</td>
          <td>Mesin</td>
          <td>Blok</td>
		  <td>Supplier</td>
		  <td>Customer</td>
		  <td>No. Jurnal</td>
		  <td>No. PO</td>
        </tr>
      </thead>
      <tbody>";
    $res=mysql_query($str);
    $no=0;
    $tdebet=0;
    $tkredit=0;
    while($bar= mysql_fetch_object($res))
    {
		
		
		$anopo="select nopo from ".$dbname.".log_transaksi_vw where notransaksi='".$bar->noreferensi."'";
		$bnopo=mysql_query($anopo) or die (mysql_error($conn));
		$cnopo=mysql_fetch_assoc($bnopo);
			$nopo=$cnopo['nopo'];
	
		$asup="select * from ".$dbname.".log_5supplier where supplierid='".$bar->kodesupplier."' ";
		$bsup=mysql_query($asup) or die (mysql_error($conn));
		$csup=mysql_fetch_assoc($bsup);
			$namasup=$csup['namasupplier'];
	
		$acus="select * from ".$dbname.".pmn_4customer where kodecustomer='".$bar->kodecustomer."' ";
		$bcus=mysql_query($acus) or die (mysql_error($conn));
		$ccus=mysql_fetch_assoc($bcus);
			$namacus=$ccus['namasupplier'];	
		
		
        $no+=1;
        $debet=0;
        $kredit=0;
		
        if($bar->jumlah>0)
             $debet= $bar->jumlah;
        else
             $kredit= $bar->jumlah*-1;
    
    $stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->noreferensi."</td>
		                  
           <td>".tanggalnormal($bar->tanggal)."</td>    
           <td>".$bar->noakun."</td>   
		   
           <td>".$bar->keterangan."</td>";
		  	
			if($noakun=='2110102')
			{
				 $stream.="<td align=right>".number_format($kredit)."</td>
           <td align=right>".number_format($debet)."</td>";  				
			}
			else
			{
				 $stream.="<td align=right>".number_format($debet)."</td>
           <td align=right>".number_format($kredit)."</td>";  
			}
          
          $stream.=" <td align=right>".$bar->karyawanid."</td>
           <td align=right>".$bar->kodevhc."</td>
           <td align=right>".$bar->kodeblok."</td>  
		   <td align=left>".$namasup."</td>
           <td align=left>".$namacus."</td>
		   <td align=left>".$bar->nojurnal."</td>
         	<td align=left>".$nopo."</td>
			
		 </tr>";
    $tdebet+=$debet;
    $tkredit+=$kredit;    
    } 
      $stream.="<tr class=rowcontent>
           <td colspan=5>TOTAL</td>";
		   
	if($noakun=='2110102')
	{   
     $stream.="<td align=right>".number_format($tkredit)."</td>
           <td align=right>".number_format($tdebet)."</td>";
	}
	else
	{
		 $stream.="<td align=right>".number_format($tdebet)."</td>
           <td align=right>".number_format($tkredit)."</td>";
	}
	 
	 $stream.="<td></td>
           <td></td>
		   <td></td>
           <td></td>
           <td></td>
		   <td></td>
		   <td></td>

         </tr>";  
   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$nop_="Detail_jurnal_".$_GET['gudang']."_".$_GET['periode'];
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