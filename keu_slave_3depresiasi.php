<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
#1. Ambil semua aktiva yang aktif
 $str="select a.tipeasset,a.jlhblnpenyusutan,a.awalpenyusutan,a.bulanan,(a.tahunan/12) as tahunan,b.namatipe 
       from ".$dbname.".sdm_daftarasset a left join ".$dbname.".sdm_5tipeasset b
       on a.tipeasset=b.kodetipe    
       where a.kodeorg='".$_SESSION['empl']['lokasitugas']."' 
       and status=1";
 $res=  mysql_query($str);
 $ass=Array();
 $nama=Array();
 $pass=Array();
 while($bar=mysql_fetch_object($res))
 {
	 $date1=new DateTime($bar->awalpenyusutan."-01");
	 $jmlsusut="P".$bar->jlhblnpenyusutan."M";
	 $date=$date1->add(new DateInterval($jmlsusut));
     //$x=mktime(0,0,0,  intval(substr($bar->awalpenyusutan,5,2)+$bar->jlhblnpenyusutan),15,substr($bar->awalpenyusutan,0,4));
     //$maxperiod=date('Y-m',$x);
	 
	 $maxperiod=$date->format('Y-m');
     if(($param['periode']<$maxperiod)&&($param['periode']>=$bar->awalpenyusutan))
           //$ass[$bar->tipeasset]+=$bar->bulanan;
		   $ass[$bar->tipeasset]+=$bar->tahunan;
     
     $nama[$bar->tipeasset]=$bar->namatipe;
     $pass[$bar->tipeasset]='DEP'.substr($bar->tipeasset,0,2);
 }
 
             echo"<button class=mybutton onclick=prosesPenyusutan(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>TipeAsset</td>
                    <td>Kodejurnal</td>
                    <td>Periode</td>
                    <td>Keterangan</td>
                    <td>Jumlah</td>
                    </tr>
                  </thead>
                  <tbody>";

             $no=0;
             foreach($ass as $key=>$val)
             { 
                 $no+=1;
                 
                 echo"<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
                    <td id='tipeasset".$no."'>".$key."</td>
                    <td id='kodejurnal".$no."'>".$pass[$key]."</td>    
                    <td id='periode".$no."'>".$param['periode']."</td>
                    <td id='keterangan".$no."'>".$nama[$key]."</td>
                    <td align=right id='jumlah".$no."'>".number_format($ass[$key],0,"","")."</td>
                    </tr>";
                 
             }
             echo"</tbody><tfoot></tfoot></table>";


#----------------------------------------------------------------
?>