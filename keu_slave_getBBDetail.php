<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$noakun=$_GET['noakun'];
$periode=$_GET['periode'];
$periode1=$_GET['periode1'];
$lmperiode=$_GET['lmperiode'];
$pt=$_GET['pt'];
$gudang=$_GET['gudang'];
	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
//ambil namagudang
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=strtoupper($bar->namaorganisasi);
}

//ambil mutasi-----------------------

if($gudang=='' and $pt=='')
{
        $str="select jumlah,keterangan,tanggal,noreferensi,kodevhc,nik,kodeblok 
            from ".$dbname.".keu_jurnaldt_vw
              where periode>='".$periode."' and periode<='".$periode1."' and noakun='".$noakun."'";
}
else if($gudang=='' and $pt!='')
{
        $str="select jumlah,keterangan,tanggal,noreferensi,kodevhc,nik,kodeblok 
            from ".$dbname.".keu_jurnaldt_vw
              where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi 
              from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)
               and noakun='".$noakun."'";
}
else
{
        $str="select jumlah,keterangan,tanggal,noreferensi,kodevhc,nik,kodeblok 
            from ".$dbname.".keu_jurnaldt_vw
              where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'
              and noakun='".$noakun."'";   
}   
//=================================================
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"parent.detailKeExcel(event,'keu_slave_getBBDetail.php?type=excel&noakun=".$noakun."&periode=".$periode."&periode1=".$periode1."&lmperiode=".$lmperiode."&pt=".$pt."&gudang=".$gudang."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
if($_GET['type']=='excel')$border=1; else $border=0;
/*
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
        </tr>
      </thead>
      <tbody>";
*/
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
          <td>Saldo</td>
        </tr>
      </thead>
      <tbody>";
    $res=mysql_query($str);
    $no=0;
    $tdebet=0;
    $tkredit=0;
	$tsaldo=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
        $debet=0;
        $kredit=0;
        if($bar->jumlah>0)
             $debet= $bar->jumlah;
        else
             $kredit= $bar->jumlah*-1;
    /*
    $stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->noreferensi."</td>               
           <td>".tanggalnormal($bar->tanggal)."</td>    
           <td>".$noakun."</td>    
           <td>".$bar->keterangan."</td>
           <td align=right>".number_format($debet)."</td>
           <td align=right>".number_format($kredit)."</td>  
           <td align=right>".$bar->karyawanid."</td>
           <td align=right>".$bar->kodevhc."</td>
           <td align=right>".$bar->kodeblok."</td>  
         </tr>";
    */
	$tsaldo+=$debet-$kredit;    
	$stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->noreferensi."</td>               
           <td>".tanggalnormal($bar->tanggal)."</td>    
           <td>".$noakun."</td>    
           <td>".$bar->keterangan."</td>
           <td align=right>".number_format($debet)."</td>
           <td align=right>".number_format($kredit)."</td>  
           <td align=right>".number_format($tsaldo)."</td>  
         </tr>";
		 
    $tdebet+=$debet;
    $tkredit+=$kredit;    
    } 
	/*
      $stream.="<tr class=rowcontent>
           <td colspan=5>TOTAL</td>
           <td align=right>".number_format($tdebet)."</td>
           <td align=right>".number_format($tkredit)."</td>
           <td></td>
           <td></td>
           <td></td>
         </tr>";  
	*/
	  $stream.="<tr class=rowcontent>
           <td colspan=5>TOTAL</td>
           <td align=right>".number_format($tdebet)."</td>
           <td align=right>".number_format($tkredit)."</td>
            <td align=right>".number_format($tsaldo)."</td>
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