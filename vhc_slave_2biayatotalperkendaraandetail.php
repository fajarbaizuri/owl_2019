<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/vhc_2biayatotalperkendaraan.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$kodevhc=$_GET['kodevhc'];
$tanggalmulai=$_GET['tanggalmulai'];
$tanggalsampai=$_GET['tanggalsampai'];
$unit=$_GET['unit'];
$periode=$_GET['periode'];

/*
if($_GET['type']!='excel')
$stream="Detail Pekerjaan :".$param['kodevhc']." [".$bar->namajenisvhc."] <br />
		 ".$_SESSION['lang']['tanggal'].":".$param['tglAwal']." - ".$param['tglAkhir']."
      <table class=sortable cellspacing=1 border=0>";
else
$stream="Detail Pekerjaan :".$param['kodevhc']." [".$bar->namajenisvhc."] <br />
 		".$_SESSION['lang']['tanggal'].":".$param['tglAwal']." - ".$param['tglAkhir']."
      <table class=sortable cellspacing=1 border=1>";
*/

//=================================================
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"detailExcel(event,'vhc_slave_2biayatotalperkendaraandetail.php?type=excel&kodevhc=".$kodevhc."&tanggalmulai=".$tanggalmulai."&tanggalsampai=".$tanggalsampai."&unit=".$unit."&periode=".$periode."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>"; 
	 
//rubah di sene boi	 
	$in="select * from ".$dbname.".vhc_5master b
		 join ".$dbname.".vhc_5jenisvhc c 
		 on b.jenisvhc=c.jenisvhc
		 where b.kodevhc='".$kodevhc."'";
	//echo $in;
	$dra=mysql_query($in);
	$wibi=mysql_fetch_assoc($dra);
	 
$stream="Detail Biaya Perkendaraan : ".$kodevhc." [".$wibi['namajenisvhc']."]<br />
 		".$_SESSION['lang']['periode'].": ".$periode." ";
	 
if($_GET['type']!='excel')$stream.="<table class=sortable border=0 cellspacing=1>"; else



$stream.="<table class=sortable border=1 cellspacing=1>";
$stream.="
      <thead>
        <tr class=rowcontent>
			  <td bgcolor=#DEDEDE align=center>No.</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>";
        $stream.="</tr>
      </thead>
      <tbody>";
	$str="select tanggal, noakun, keterangan, debet as jumlah, kodevhc from ".$dbname.".keu_jurnaldt_vw
	
		 where
		  kodevhc = '".$kodevhc."'
		  and tanggal>='".$tanggalmulai."' and tanggal<='".$tanggalsampai."' and nojurnal like '%".$unit."%'";
    $res=mysql_query($str);
    $no=0;
    $total=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
    
    $stream.="<tr class=rowcontent>
				  <td align=right>".$no."</td>
				  <td>".$bar->tanggal."</td>
				  <td align=right>".$bar->noakun."</td>
				  <td nowrap>".$bar->keterangan."</td>
				  <td align=right>".number_format($bar->jumlah)."</td>";
         $stream.="</tr>";
		 $total+=$bar->jumlah;
    } 
    $stream.="<thead><tr class=rowtitle>
				  <td colspan= 4 align=center>TOTAL</td>
				  <td align=right>".number_format($total)."</td>";
         $stream.="</tr>";

   $stream.="</tbody></table>";
   if($_GET['type']=='excel')
   {
$nop_="Detail_BiayaPerKendaraan_".$kodevhc."_".$periode;
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