<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
#1ambil periode akuntansi
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      kodeorg ='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0";
$tgmulai='';
$tgsampai='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $tgsampai   = $bar->tanggalsampai;
    $tgmulai    = $bar->tanggalmulai;
}
if($tgmulai=='' || $tgsampai=='')
    exit("Error: Periode akuntasi tidak terdaftar");

#2. ambil biaya workshop
#kode parameter WS1, ambil semua noakun biaya bengkel
$str="select noakundebet,sampaidebet from ".$dbname.".keu_5parameterjurnal where jurnalid='WS1'";
$res=mysql_query($str);
$dariakun='';
$sampaiakun='';
while($bar=  mysql_fetch_object($res))
{
    $dariakun=$bar->noakundebet;
    $sampaiakun=$bar->sampaidebet;
}
if($dariakun=='' or $sampaiakun=='')
    exit('Eror: Tidak ada akun untuk jurnalid WS1');

 $str="select sum(debet) as jumlah from ".$dbname.".keu_jurnaldt_vw  
       where noakun >='".$dariakun."' and noakun<='".$sampaiakun."' 
       and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."'
       and (noreferensi NOT LIKE '%ALK_KERJA_AB%' or noreferensi is NULL)";
 
 $res=  mysql_query($str);
 $bybengkel=0;
 while($bar=mysql_fetch_object($res))
 {
     $bybengkel=$bar->jumlah;
 }

 #3 periksa apakah sudah posting semua
 $str="select * from ".$dbname.".msvhc_by_operator where posting=0 
       and kodevhc in(select kodevhc from ".$dbname.".vhc_5master 
       where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')
       and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' limit 1";
 $res=mysql_query($str);
 $str1="select * from ".$dbname.".vhc_runht where posting=0
        and kodevhc in(select kodevhc from ".$dbname.".vhc_5master 
        where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')
        and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' limit 1";
 $res1=mysql_query($str1);
 if(mysql_num_rows($res)>0 or mysql_num_rows($res1)>0){
     //print_r($str);
     //print_r($str1);
     exit("Error: masih ada data pekerjaan atau service yang belum dipoting ");
 }
 #4 ambil semua kendaraan yang diservice pada periode berjalan
 $str="select sum(downtime) as dt,kodevhc from ".$dbname.".msvhc_by_operator 
       where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' 
       and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' and posting=1
       group by kodevhc";
 $res=mysql_query($str);
 $kend=Array();
 $byrinci=Array();
 $totaljamservice=0;
 while($bar=mysql_fetch_object($res))
 {
    $totaljamservice+=$bar->dt;
    $kend[$bar->kodevhc]=$bar->dt; 
 }
 foreach($kend as $key=>$val)
 {
     $byrinci[$key]=($val/$totaljamservice)*$bybengkel;
 }
  $biayattlkend=$byrinci;
 
  #=======================================================================================================  
  #4.5 ambilnoakun biaya kendaraan
  $akunkdari='';
  $akunksampai='';
  $strh="select distinct noakundebet,sampaidebet  from ".$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
  $resh=mysql_query($strh);
  while($barh=mysql_fetch_object($resh))
  {
      $akunkdari=$barh->noakundebet;
      $akunksampai=$barh->sampaidebet;
  }
  if($akunkdari=='' or $akunksampai=='')
  {
      exit("Error: parameter jurnal untuk LPVHC(by kendaraan) belum dibuat");
  }
  #5 ambil biaya perkendaraan dari data yang sudah dijurnal jurnal 
  $str="select sum(debet) as jlh,kodevhc from ".$dbname.".keu_jurnaldt_vw where
        kodevhc in(select kodevhc from ".$dbname.".vhc_5master 
        where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%') 
        and tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' 
        and nojurnal like '%".$_SESSION['empl']['lokasitugas']."%'
        and (noakun between '".$akunkdari."' and '".$akunksampai."')   
        and (noreferensi NOT LIKE '%ALK_KERJA_AB%' or noreferensi is NULL)  
        group by kodevhc";
  
  
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
   {
    $biayattlkend[$bar->kodevhc]+=$bar->jlh; 
   }
 
  #6 ambil semua jamkerja kendaraan per unit
 /*  $str="select sum(jumlah) as jlhjam ,kodevhc from ".$dbname.".vhc_runht where 
       tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."'
       and kodevhc in(select kodevhc from ".$dbname.".vhc_5master 
       where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')
       group by kodevhc";
 */  
   $str="select sum(a.jumlah) as jlhjam,kodevhc from ".$dbname.".vhc_rundt_vw a
            left join ".$dbname.".vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan
            where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' and alokasibiaya!='' 
            and jenispekerjaan!=''  and   
            kodevhc in(select kodevhc from ".$dbname.".vhc_5master 
            where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%')
            group by kodevhc";
           
   $res=mysql_query($str);
  // echo mysql_error($conn);
   $biayaperjam=Array();
    while($bar=mysql_fetch_object($res))
    {
        if($bar->jlhjam==0) {
            $biayaperjam[$bar->kodevhc]=0;
        } else {
            $biayaperjam[$bar->kodevhc]=$biayattlkend[$bar->kodevhc]/$bar->jlhjam;
        }
    }  
             echo"<button  onclick=prosesAlokasi(1) id=btnproses>Process</button>
                 
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>Periode</td>
                    <td>KodeVhc</td>
                    <td>Jlh | Rp per Jam</td>
                    <td>Jenis</td>
                    </tr>
                  </thead>
                  <tbody>";

             $no=0;
            foreach($byrinci as $key =>$val)
             { 
                 $no+=1;
                 echo"<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
                    <td id='periode".$no."'>".$_POST['periode']."</td>
                    <td id='kodevhc".$no."'>".$key."</td>
                    <td id='jumlah".$no."' align=right>".number_format($val,2,'.','')."</td>    
                    <td id='jenis".$no."'>BYWS</td>
                    </tr>";
                }
             
                foreach ($biayaperjam as $key=>$jlh)
                { 
                 $no+=1;
                 echo"<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
                    <td id='periode".$no."'>".$_POST['periode']."</td>
                    <td id='kodevhc".$no."'>".$key."</td>
                    <td id='jumlah".$no."' align=right>".number_format($jlh,2,'.','')."</td>    
                    <td id='jenis".$no."'>ALKJAM</td>
                    </tr>";
                }
             echo"</tbody><tfoot></tfoot></table>";
?>