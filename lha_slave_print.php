<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');

$proses=$_GET['proses'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
$kdKeg=$_POST['kdKeg'];
$kdAfd=$_POST['kdAfd'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	$kdOrg=$_GET['kdOrg'];
	$kdAfd=$_GET['kdAfd'];
	$kdKeg=$_GET['kdKeg'];
	$tgl1_=$_GET['tgl1'];  
	$tgl2_=$_GET['tgl2'];  
}
if($kdAfd=='')
    $kdAfd=$kdOrg;
if($kdKeg=='')
    $kdKeg='%%';
$lha=true; if($tgl2_!='')$lha=false;

// luas areal
$luas=0;
          $str="select luasareaproduktif from ".$dbname.".setup_blok 
                where kodeorg like '".$kdAfd."%'";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $luas+=$bar->luasareaproduktif;
          }
//          echo $luas;

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);
$tglqwe1=juliantojd(substr($tgl1_,4,2),substr($tgl1_,6,2),substr($tgl1_,0,4));
$tglqwe2=juliantojd(substr($tgl2_,4,2),substr($tgl2_,6,2),substr($tgl2_,0,4));
$jumlahhari=1+$tglqwe2-$tglqwe1;

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($kdOrg==''){
            echo"Error: Kebun dan Afdeling tidak boleh kosong."; exit;
    }

    if($tgl1_==''){
            echo"Error: Tanggal tidak boleh kosong."; exit;
    }
	
}
     

 /*   
//ambil material
    $str="select a.kodekegiatan,a.kodeorg,a.kodebarang, b.namabarang,b.satuan,sum(a.kwantitas) as jumlah from
          ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b
          on a.kodebarang=b.kodebarang    
          where  kodeorg like '".$kdAfd."%' and tanggal='".$tgl1_."'
          group by a.kodekegiatan,a.kodeorg,a.kodebarang";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $barang[$bar->kodekegiatan][$bar->kodeorg]['kodebarang'][]=$bar->kodebarang;
        $barang[$bar->kodekegiatan][$bar->kodeorg]['namabarang'][]=$bar->namabarang;
        $barang[$bar->kodekegiatan][$bar->kodeorg]['satuan'][]=$bar->satuan;
        $barang[$bar->kodekegiatan][$bar->kodeorg]['jumlah'][]=$bar->jumlah;
    }
*/
    if($proses=='getAfdAll'){
          $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                where kodeorganisasi like '".$kdAfd."%' and length(kodeorganisasi)=6 order by namaorganisasi";
          $op="<option value=''>".$_SESSION['lang']['all']."</option>";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $op.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
          }
          echo $op;
          exit();
    }else if($proses=='getKegAll'){
		
		  $str="SELECT kodekegiatan,concat(kodekegiatan,':',namakegiatan ) as nama FROM  ".$dbname.".`setup_kegiatan` where kelompok NOT in ('MIL','KNT')";
          $op="<option value=''>".$_SESSION['lang']['all']."</option>";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $op.="<option value='".$bar->kodekegiatan."'>".$bar->nama."</option>";
          }
          echo $op;
          exit();
    }
    else{
        if($lha)$tanggalsampai=''; else $tanggalsampai=tanggalnormal($tgl2_);
        $stream.="<table width=100%>
                 <tr>
                     <td colspan=5>FAJAR BAIZURY GROUP</td>
                 </tr>
                 <tr>
                     <td align=center colspan=5>
                     LAPORAN HARIAN AFDELING
                     </td>
                 </tr>
                 <tr>
                     <td style='width:75px;'>KEBUN</td><td style='width:75px;'>:".substr($kdAfd,0,4)."</td><td></td><td style='width:75px;'>Diperiksa</td><td style='width:75px;'>Dibuat</td>
                 </tr>
                 <tr>
                    <td>AFDELING</td><td>:".$kdAfd."</td><td>(".number_format($luas,2)." Ha)</td><td> </td><td> </td>
                 </tr>
                 <tr>
                    <td>TANGGAL</td><td>:".tanggalnormal($tgl1_)."</td><td>".$tanggalsampai."</td><td> </td><td> </td>
                 </tr>   
                 <tr>
                    <td></td><td></td><td></td><td>Askep</td><td>Asisten</td>
                 </tr>                   
                </table>";
	if($proses=='excel')
                $stream.="<table border='1'>";
        else {
              $stream.="<table cellspacing='1' border='0' class='sortable' width=100%>";
            }
	$stream.="<thead>
	<tr class=rowheader>
        <td rowspan=2 align=center  >".$_SESSION['lang']['kode']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>    
	<td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>
	<td colspan=2 align=center>".$_SESSION['lang']['kodeblok']."</td>            
	<td rowspan=2 align=center>".$_SESSION['lang']['thntnm']."</td>
	<td colspan=2 align=center>HK KHT/KHL</td>    
	<td colspan=2 align=center>HK KBL</td>          
	<td colspan=2 align=center>".$_SESSION['lang']['upahkerja']."</td>
	<td colspan=2 align=center>Hasil Kerja</td>
	<td colspan=4 align=center>".$_SESSION['lang']['pemakaianBarang']."</td>
        <td colspan=2 align=center>Biaya Bahan</td>
	<td rowspan=2 align=center>".$_SESSION['lang']['totalbiaya']."</td>
        <td rowspan=2 align=center>Rp/Satuan</td>    
        <td rowspan=2 align=center>HK/Satuan</td>    
        </tr>
        
 	<tr class=rowheader>
        <td align=center>".$_SESSION['lang']['blok']."</td>
        <td align=center>".$_SESSION['lang']['luas']."</td>";    
        if($lha)$stream.="<td align=center>HI</td>    
	<td align=center>SDHI</td>"; else $stream.="<td align=center colspan=2></td>";
        if($lha)$stream.="<td align=center>HI</td>    
	<td align=center>SDHI</td>"; else $stream.="<td align=center colspan=2></td>";
        $stream.="<td align=center>Rp/HK</td>            
	<td align=center>".$_SESSION['lang']['jumlah']."</td>";
	if($lha)$stream.="<td align=center>HI</td>
        <td align=center>SDHI</td>"; else $stream.="<td align=center colspan=2></td>";
	$stream.="<td align=center>".$_SESSION['lang']['namabarang']."</td>
	<td align=center>".$_SESSION['lang']['satuan']."</td>";
	if($lha)$stream.="<td align=center>HI</td>
        <td align=center>SDHI</td>"; else $stream.="<td align=center colspan=2></td>";
        $stream.="<td align=center>Rp/HK</td>            
	<td align=center>".$_SESSION['lang']['jumlah']."</td>   
        </tr>       
        </thead>
	<tbody>";
    if($lha)$str="select distinct kodekegiatan,kodeorg,namakegiatan,satuan from ".$dbname.".kebun_perawatan_dan_spk_vw where kodeorg like '".$kdAfd."%' and tanggal ='".$tgl1_."' and kodekegiatan LIKE '".$kdKeg."'";
    else $str="select distinct kodekegiatan,kodeorg,namakegiatan,satuan from ".$dbname.".kebun_perawatan_dan_spk_vw where kodeorg like '".$kdAfd."%' 
             and tanggal between '".$tgl1_."' and '".$tgl2_."' and kodekegiatan LIKE '".$kdKeg."'";
   // echo $str;
        $res=mysql_query($str);        
        $master=Array();  
//    if(mysql_num_rows($res)<1){
////        exit(" Error, tidak ada data");
//    }    
        while($bar=mysql_fetch_object($res))
        {
            $master['kegiatan'][]=$bar->kodekegiatan;
            $master['blok'][]=$bar->kodeorg;
            $master['namakegiatan'][]=$bar->namakegiatan;
            $master['satuankegiatan'][]=$bar->satuan;
        }
    $str="select kodeorg,tahuntanam,luasareaproduktif from ".$dbname.".setup_blok where kodeorg  like '".$kdAfd."%'";   
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $blok[$bar->kodeorg]['kode']=$bar->kodeorg;
            $blok[$bar->kodeorg]['thntnm']=$bar->tahuntanam;
            $blok[$bar->kodeorg]['luas']=$bar->luasareaproduktif;
        } 
        if(!empty($master['blok']))foreach($master['blok'] as $key=>$val)
        {          
            $master['luas'][$key]=1;
            $master['thntnm'][$key]=0;
            if($val==$blok[$val]['kode'])
            {
                $master['luas'][$key]=$blok[$val]['luas'];
                $master['thntnm'][$key]=$blok[$val]['thntnm'];
            }

        }
//upah KBL-==========================================        
    if($lha)$str="select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah 
          from ".$dbname.".kebun_kehadiran_vw
          where  kodeorg like '".$kdAfd."%' and tanggal='".$tgl1_."' and tipekaryawan='KBL' and umr != '0' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
    else $str="select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah 
          from ".$dbname.".kebun_kehadiran_vw
          where  kodeorg like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and tipekaryawan='KBL' and umr != '0' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
//    echo $str;
     $hkKBL=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hkKBL['kegiatan'][]=$bar->kodekegiatan;
            $hkKBL['blok'][]=$bar->kodeorg;
            $hkKBL['jhk'][]=$bar->jhk;
            $hkKBL['upah'][]=$bar->upah;            
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            $master['hkkbl'][$key]=0;
            $master['upahkbl'][$key]=0;
            if(count($hkKBL['kegiatan'])>0){
                if(!empty($hkKBL['kegiatan']))foreach($hkKBL['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hkKBL['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hkkbl'][$key]=$hkKBL['jhk'][$g];
                        $master['upahkbl'][$key]=$hkKBL['upah'][$g];
                    }
            }
            }
        }  
     //====================sdbi
    $str="select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah 
          from ".$dbname.".kebun_kehadiran_vw
          where  kodeorg like '".$kdAfd."%' and tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."' and tipekaryawan='KBL' and umr != '0' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
  
     $hkKBL=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hkKBL['kegiatan'][]=$bar->kodekegiatan;
            $hkKBL['blok'][]=$bar->kodeorg;
            $hkKBL['jhk'][]=$bar->jhk;
            $hkKBL['upah'][]=$bar->upah;            
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            $master['hkkblsbi'][$key]=0;
            $master['upahkblsbi'][$key]=0;
            if(count($hkKBL['kegiatan'])>0){
                if(!empty($hkKBL['kegiatan']))foreach($hkKBL['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hkKBL['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hkkblsbi'][$key]=$hkKBL['jhk'][$g];
                        $master['upahkblsbi'][$key]=$hkKBL['upah'][$g];
                    }
            }
            }
        }    
//upah KHT/KHL-==========================================
        if($lha)$str="select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah from ".$dbname.".kebun_kehadiran_vw
          where  kodeorg like '".$kdAfd."%' and tanggal='".$tgl1_."' and tipekaryawan in('KHL','KHT','Kontrak') and umr != '0' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
        else $str="select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah from ".$dbname.".kebun_kehadiran_vw
          where  kodeorg like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and tipekaryawan in('KHL','KHT','Kontrak') and umr != '0' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
        //echo $str;
     $hkKHL=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hkKHL['kegiatan'][]=$bar->kodekegiatan;
            $hkKHL['blok'][]=$bar->kodeorg;
            $hkKHL['jhk'][]=$bar->jhk;
            $hkKHL['upah'][]=$bar->upah;            
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            $master['hkkhl'][$key]=0;
            $master['upahkhl'][$key]=0;
            if(count($hkKHL['kegiatan'])>0){
                if(!empty($hkKHL['kegiatan']))foreach($hkKHL['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hkKHL['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hkkhl'][$key]=$hkKHL['jhk'][$g];
                        $master['upahkhl'][$key]=$hkKHL['upah'][$g];
                    }
            }
            }
        }       
//   tambahkan biaya dari kontrak ke bhl
        if($lha)$str="select kodeblok as kodeorg,kodekegiatan,sum(hkrealisasi) as jhk,sum(jumlahrealisasi) as upah from ".$dbname.".log_baspk
          where  kodeblok like '".$kdAfd."%' and tanggal='".$tgl1_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeblok,kodekegiatan;";  
        else $str="select kodeblok as kodeorg,kodekegiatan,sum(hkrealisasi) as jhk,sum(jumlahrealisasi) as upah from ".$dbname.".log_baspk
          where  kodeblok like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeblok,kodekegiatan;";  
     $hkKHL=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hkKHL['kegiatan'][]=$bar->kodekegiatan;
            $hkKHL['blok'][]=$bar->kodeorg;
            $hkKHL['jhk'][]=$bar->jhk;
            $hkKHL['upah'][]=$bar->upah;            
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            if(count($hkKHL['kegiatan'])>0){
                if(!empty($hkKHL['kegiatan']))foreach($hkKHL['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hkKHL['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hkkhl'][$key]+=$hkKHL['jhk'][$g];
                        $master['upahkhl'][$key]+=$hkKHL['upah'][$g];
                    }
            }
            }
        }           
  //=======sbi
        $str="select kodeorg,kodekegiatan,sum(jhk) as jhk,sum(umr+insentif) as upah from ".$dbname.".kebun_kehadiran_vw
          where  kodeorg like '".$kdAfd."%' and tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."' and tipekaryawan in('KHL','KHT','Kontrak') and umr != '0' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
     $hkKHL=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hkKHL['kegiatan'][]=$bar->kodekegiatan;
            $hkKHL['blok'][]=$bar->kodeorg;
            $hkKHL['jhk'][]=$bar->jhk;
            $hkKHL['upah'][]=$bar->upah;            
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            $master['hkkhlsbi'][$key]=0;
            $master['upahkhlsbi'][$key]=0;
            if(count($hkKHL['kegiatan'])>0){
                if(!empty($hkKHL['kegiatan']))foreach($hkKHL['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hkKHL['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hkkhlsbi'][$key]=$hkKHL['jhk'][$g];
                        $master['upahkhlsbi'][$key]=$hkKHL['upah'][$g];
                    }
            }
            }
        }       
//   tambahkan biaya dari kontrak ke bhl
        $str="select kodeblok as kodeorg,kodekegiatan,sum(hkrealisasi) as jhk,sum(jumlahrealisasi) as upah from ".$dbname.".log_baspk
          where  kodeblok like '".$kdAfd."%' and tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeblok,kodekegiatan;";  
     $hkKHL=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hkKHL['kegiatan'][]=$bar->kodekegiatan;
            $hkKHL['blok'][]=$bar->kodeorg;
            $hkKHL['jhk'][]=$bar->jhk;
            $hkKHL['upah'][]=$bar->upah;            
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            if(count($hkKHL['kegiatan'])>0){
                if(!empty($hkKHL['kegiatan']))foreach($hkKHL['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hkKHL['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hkkhlsbi'][$key]+=$hkKHL['jhk'][$g];
                        $master['upahkhlsbi'][$key]+=$hkKHL['upah'][$g];
                    }
            }
            }
        }    
//total biaya upah==========================================================
    if(!empty($master['upahkhl']))foreach($master['upahkhl'] as $kut=>$uk)
    {
        $master['totalupah'][$kut]=$master['upahkbl'][$kut]+$master['upahkhl'][$kut];
        @$master['rpperhk'][$kut]=$master['totalupah'][$kut]/($master['hkkhl'][$kut]+$master['hkkbl'][$kut]);
        $master['totbiaya'][$kut]=$master['totalupah'][$kut];
    }
//============================================================================        
//Hasil Kerja=========================================
        if($lha)$str="select kodeorg,kodekegiatan,sum(hasilkerja) as hasil from 
            ".$dbname.".kebun_perawatan_vw
          where  kodeorg like '".$kdAfd."%' and tanggal='".$tgl1_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
        else $str="select kodeorg,kodekegiatan,sum(hasilkerja) as hasil from 
            ".$dbname.".kebun_perawatan_vw
          where  kodeorg like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
     $hasil=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hasil['kegiatan'][]=$bar->kodekegiatan;
            $hasil['blok'][]=$bar->kodeorg;
            $hasil['hasil'][]=$bar->hasil;           
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            $master['hasilbi'][$key]=0;
            if(count($hasil['kegiatan'])>0){
                if(!empty($hasil['kegiatan']))foreach($hasil['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hasil['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hasilbi'][$key]=$hasil['hasil'][$g];
                    }
            }
            }
        }       
//   tambahkan hasil dari spk
        if($lha)$str="select kodeblok as kodeorg,kodekegiatan,sum(hasilkerjarealisasi) as hasil
            from ".$dbname.".log_baspk
          where  kodeblok like '".$kdAfd."%' and tanggal='".$tgl1_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeblok,kodekegiatan;";  
        else $str="select kodeblok as kodeorg,kodekegiatan,sum(hasilkerjarealisasi) as hasil
            from ".$dbname.".log_baspk
          where  kodeblok like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeblok,kodekegiatan;";  
     $hasil=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hasil['kegiatan'][]=$bar->kodekegiatan;
            $hasil['blok'][]=$bar->kodeorg;
            $hasil['hasil'][]=$bar->hasil;           
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            if(count($hasil['kegiatan'])>0){
                if(!empty($hasil['kegiatan']))foreach($hasil['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hasil['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hasilbi'][$key]+=$hasil['hasil'][$g];
                    }
            }
            }
        }
// ============sbi
        $str="select kodeorg,kodekegiatan,sum(hasilkerja) as hasil from 
            ".$dbname.".kebun_perawatan_vw
          where  kodeorg like '".$kdAfd."%' and tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."'  and kodekegiatan LIKE '".$kdKeg."'
          group by kodeorg,kodekegiatan;";  
     $hasil=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hasil['kegiatan'][]=$bar->kodekegiatan;
            $hasil['blok'][]=$bar->kodeorg;
            $hasil['hasil'][]=$bar->hasil;           
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            $master['hasilsbi'][$key]=0;
            if(count($hasil['kegiatan'])>0){
                if(!empty($hasil['kegiatan']))foreach($hasil['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hasil['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hasilsbi'][$key]=$hasil['hasil'][$g];
                    }
            }
            }
        }       
//   tambahkan hasil dari spk
        $str="select kodeblok as kodeorg,kodekegiatan,sum(hasilkerjarealisasi) as hasil
            from ".$dbname.".log_baspk
          where  kodeblok like '".$kdAfd."%' and tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."' and kodekegiatan LIKE '".$kdKeg."'
          group by kodeblok,kodekegiatan;";  
     $hasil=Array();
       $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
        {
            $hasil['kegiatan'][]=$bar->kodekegiatan;
            $hasil['blok'][]=$bar->kodeorg;
            $hasil['hasil'][]=$bar->hasil;           
        }
        if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
        {          
            if(count($hasil['kegiatan'])>0){
                if(!empty($hasil['kegiatan']))foreach($hasil['kegiatan'] as $g=>$h){ 
                    if($val==$h and  $hasil['blok'][$g]==$master['blok'][$key])
                    {
                        $master['hasilsbi'][$key]+=$hasil['hasil'][$g];
                    }
            }
            }
        }
        
//========================bahan sbi
if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
{           
     if($lha)$str="SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan 
           FROM ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b
           on a.kodebarang=b.kodebarang    
           where  tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."'       
           and a.kodeorg='".$master['blok'][$key]."' and a.kodekegiatan='".$val."'
           group by kodekegiatan,kodeorg,kodebarang";
     else $str="SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan 
           FROM ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b
           on a.kodebarang=b.kodebarang    
           where tanggal between '".$tgl1_."'  and '".$tgl2_."'        
           and a.kodeorg='".$master['blok'][$key]."' and a.kodekegiatan='".$val."'
           group by kodekegiatan,kodeorg,kodebarang";
         $barang=Array();
     $res=mysql_query($str);
     if(mysql_numrows($res)<1)
     {
                $master['barangsbi'][$key][]=0;
                $master['kodebarangsbi'][$key][]=0;
                $master['satuanbarangsbi'][$key][]=0; 
                $master['qtysbi'][$key][]=0;          
     }   
     else
     {    
         while($bar=mysql_fetch_object($res))
         {
                $master['barangsbi'][$key][]=$bar->namabarang;
                $master['kodebarangsbi'][$key][]=$bar->kodebarang;
                $master['satuanbarangsbi'][$key][]=$bar->satuan; 
                $master['qtysbi'][$key][]=$bar->qty; 
         }
     }
}

//echo "<pre>";
//print_r($master);
//echo "</pre>";

if(!empty($master['kegiatan']))foreach($master['kegiatan'] as $key=>$val)
{           
     if($lha)$str="SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan 
           FROM ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b
           on a.kodebarang=b.kodebarang    
           where  tanggal='".$tgl1_."'       
           and a.kodeorg='".$master['blok'][$key]."' and a.kodekegiatan='".$val."'
           group by kodekegiatan,kodeorg,kodebarang";
     else $str="SELECT a.kodekegiatan,a.kodeorg,a.kodebarang,sum(a.kwantitas) as qty,b.namabarang,b.satuan 
           FROM ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b
           on a.kodebarang=b.kodebarang    
           where  tanggal between '".$tgl1_."'  and '".$tgl2_."'      
           and a.kodeorg='".$master['blok'][$key]."' and a.kodekegiatan='".$val."'
           group by kodekegiatan,kodeorg,kodebarang";
//     echo $str."<br>";
         $barang=Array();
     $res=mysql_query($str);
     if(mysql_numrows($res)<1)
     {
           $master['qtybi'][$key][]=0;         
     }   
     else
     {   
         while($bar=mysql_fetch_object($res))
         {
                if(!empty($master['kodebarangsbi'][$key]))foreach($master['kodebarangsbi'][$key] as $kunci=>$isi)
                {
                    if($bar->kodebarang==$isi)
                         $master['qtybi'][$key][$kunci]=$bar->qty;
// kalo 2 baris di bawah ga disabled, barang dengan kunci 0 (urutan pertama) = 0                    
//                    else
//                         $master['qtybi'][$key][$kunci]=0;
                }
         }
     }
}
//echo "<pre>";
//print_r($master['qtybi']);
//echo "</pre>";

// ambil harga barang
$t=mktime(0,0,0,intval(substr($tgl1_,4,2))-1,15,intval(substr($tgl1_,0,4)));
$bl=date('Y-m',$t);
$str="SELECT distinct b.kodebarang,a.hargarata FROM ".$dbname.".kebun_pakai_material_vw b
      left join ".$dbname.".log_5saldobulanan a
      on b.kodebarang=a.kodebarang
      where b.kodeorg like '".$kdAfd."%' and b.tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."'
      and a.periode='".$bl."'
      and a.kodeorg in(select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($kdAfd,0,4)."')";               
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $harga[$bar->kodebarang]=$bar->hargarata;
}
 if(!empty($master['kodebarangsbi']))foreach($master['kodebarangsbi'] as $kuku=>$vx)
 {
     if(!empty($master['kodebarangsbi'][$kuku]))foreach($master['kodebarangsbi'][$kuku] as $jack=>$pot)
     {
         $master['hargabarangbi'][$kuku][$jack]=$harga[$pot];
         $master['bybarangbi'][$kuku][$jack]=$harga[$pot]*$master['qtybi'][$kuku][$jack];
         $master['totbiaya'][$kuku]+=$master['bybarangbi'][$kuku][$jack];
     }
 }
 
//============rupiah/ha
 if(!empty($master['totbiaya']))foreach($master['totbiaya'] as $kun=>$tak)
 {
     @$master['rppersatuan'][$kun]=$tak/$master['hasilbi'][$kun];
 }
//============hk/ha
 if(!empty($master['totbiaya']))foreach($master['totbiaya'] as $kun=>$tak)
 {
     @$master['hkpersatuan'][$kun]=($master['hkkhl'][$kun]+$master['hkkbl'][$kun])/$master['hasilbi'][$kun];
 }
 //Grand Total
 if(!empty($master['blok']))foreach($master['blok'] as $kun=>$tak)
 {
     $TOTAL['luas']+=$master['luas'][$kun];
     $TOTAL['hkkhlbi']+=$master['hkkhl'][$kun];
     $TOTAL['hkkhlsbi']+=$master['hkkhlsbi'][$kun];
     $TOTAL['hkkblbi']+=$master['hkkbl'][$kun];
     $TOTAL['hkkblsbi']+=$master['hkkblsbi'][$kun];     
     $TOTAL['totalupah']+=$master['totalupah'][$kun];
     $TOTAL['hasilbi']+=$master['hasilbi'][$kun];
     $TOTAL['hasilsbi']+=$master['hasilsbi'][$kun];
     $TOTAL['totalbiaya']+=$master['totbiaya'][$kun];
     $TOTAL['rppersatuan']+=$master['rppersatuan'][$kun];
     $TOTAL['hkpersatuan']+=$master['hkpersatuan'][$kun];
     if(!empty($master['bybarangbi'][$kun]))foreach($master['bybarangbi'][$kun] as $la=>$li)
     {
        $TOTAL['totalbiayabarang']+=$li; 
     }    
 } 
 @$TOTAL['rpperhk']=$TOTAL['totalupah']/($TOTAL['hkkhlbi']+$TOTAL['hkkblbi']);
 

 //PRINT OUT============================================
   if(!empty($master['blok']))foreach($master['blok'] as $kunc=>$va){
            $stream.="<tr class=rowcontent>
            <td>".$master['kegiatan'][$kunc]."</td>
            <td>".$master['namakegiatan'][$kunc]."</td>    
            <td>".$master['satuankegiatan'][$kunc]."</td>
            <td>".$master['blok'][$kunc]."</td>
            <td align=right>".number_format($master['luas'][$kunc],2)."</td>
            <td>".$master['thntnm'][$kunc]."</td>";
            if($lha)$stream.="<td align=right>".$master['hkkhl'][$kunc]."</td>         
            <td align=right>".$master['hkkhlsbi'][$kunc]."</td>"; else $stream.="<td align=right colspan=2>".$master['hkkhl'][$kunc]."</td>";
            if($lha)$stream.="<td align=right>".$master['hkkbl'][$kunc]."</td>
            <td align=right>".$master['hkkblsbi'][$kunc]."</td>"; else $stream.="<td align=right colspan=2>".$master['hkkbl'][$kunc]."</td>";
            $stream.="<td align=right>".number_format($master['rpperhk'][$kunc],0)."</td>    
            <td align=right>".number_format($master['totalupah'][$kunc],0)."</td>"; 
            if($lha)$stream.="<td align=right>".number_format($master['hasilbi'][$kunc],2)."</td>    
            <td align=right>".number_format($master['hasilsbi'][$kunc],2)."</td>"; else $stream.="<td align=right colspan=2>".number_format($master['hasilbi'][$kunc],2)."</td>";               
            $stream.="<td></td>
            <td></td>"; 
            if($lha)$stream.="<td></td>
            <td></td>"; else $stream.="<td colspan=2></td>";
            $stream.="<td></td>
            <td></td>
            <td align=right>".number_format($master['totbiaya'][$kunc],0)."</td>   
            <td align=right>".number_format($master['rppersatuan'][$kunc],0)."</td>                 
            <td align=right>".number_format($master['hkpersatuan'][$kunc],2)."</td>                 
            </tr>";
                  if(!empty($master['barangsbi'][$kunc]))foreach($master['barangsbi'][$kunc] as $dd=>$ee)
                  {
                    $stream.="<tr class=rowcontent>
                    <td></td>
                    <td></td>    
                    <td></td>
                    <td></td>
                    <td align=right></td>
                    <td></td>";
                    if($lha)$stream.="<td align=right></td>    
                    <td align=right></td>"; else $stream.="<td align=right colspan=2></td>";                
                    if($lha)$stream.="<td align=right></td>    
                    <td align=right></td>"; else $stream.="<td align=right colspan=2></td>";                
                    $stream.="<td align=right></td>    
                    <td align=right></td>"; 
                    if($lha)$stream.="<td align=right></td>    
                    <td align=right></td>"; else $stream.="<td align=right colspan=2></td>";                
                    $stream.="<td>".$master['barangsbi'][$kunc][$dd]."</td>
                    <td>".$master['satuanbarangsbi'][$kunc][$dd]."</td>"; 
                    if($lha)$stream.="<td align=right>".number_format($master['qtybi'][$kunc][$dd],2)."</td>
                    <td align=right>".number_format($master['qtysbi'][$kunc][$dd],2)."</td>"; else $stream.="<td align=right colspan=2>".number_format($master['qtybi'][$kunc][$dd],2)."</td>"; 
                    $stream.="<td align=right>".number_format($master['hargabarangbi'][$kunc][$dd],0)."</td>
                    <td align=right>".number_format($master['bybarangbi'][$kunc][$dd],0)."</td>
                    <td align=right></td>   
                    <td align=right></td>                 
                    <td align=right></td>                 
                    </tr>";            
                  }
   }     
                   
       $stream.="
	<tr class=header>
	<td colspan=4>Total</td>
	<td align=right>".number_format($TOTAL['luas'],2)."</td>
        <td></td>";    
	if($lha)$stream.="<td align=right>".number_format($TOTAL['hkkhlbi'])."</td>
	<td align=right>".number_format($TOTAL['hkkhlsbi'])."</td>"; else $stream.="<td align=right colspan=2>".number_format($TOTAL['hkkhlbi'])."</td>";
	if($lha)$stream.="<td align=right>".number_format($TOTAL['hkkblbi'])."</td>
	<td align=right>".number_format($TOTAL['hkkblsbi'])."</td>"; else $stream.="<td align=right colspan=2>".number_format($TOTAL['hkkblbi'])."</td>";
	$stream.="<td align=right></td>
	<td align=right>".number_format($TOTAL['totalupah'])."</td>";   
 	if($lha)$stream.="<td align=right></td>
	<td align=right></td>"; else $stream.="<td align=right colspan=2></td>";          
        $stream.="<td></td> 
        <td></td>
        <td></td>  
        <td></td>  
        <td></td>
        <td align=right>".number_format($TOTAL['totalbiayabarang'])."</td> 
        <td align=right>".number_format($TOTAL['totalbiaya'])."</td>
        <td align=right></td>    
        <td align=right></td>    
        </tbody></table>";
        //".number_format($TOTAL['rpperhk'])."
        //".number_format($TOTAL['hasilbi'],2)."
        //".number_format($TOTAL['hasilsbi'],2)."
        //".number_format($TOTAL['rppersatuan'])."
        //".number_format($TOTAL['hkpersatuan'],2)."

//================================================================================================================================== atas: lba perawatan        
//================================================================================================================================== bawah: lba panen (dz apr 28, 2012)        
//        $stream.='<br>';
	if($proses=='excel')
                $stream.="<br><table border='1'>";
        else {
              $stream.="<br><table cellspacing='1' border='0' class='sortable' width=100%>";
            }
	$stream.="<thead>
	<tr class=rowheader>
        <td rowspan=2 align=center  >".$_SESSION['lang']['kode']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>    
	<td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>
	<td colspan=2 align=center>".$_SESSION['lang']['kodeblok']."</td>            
	<td rowspan=2 align=center>".$_SESSION['lang']['thntnm']."</td>
	<td colspan=2 align=center>HK</td>    
	<td colspan=4 align=center>".$_SESSION['lang']['biaya']."</td>
	<td colspan=2 align=center>Hasil Kerja</td>
        <td rowspan=2 align=center>Rp/Kg</td>    
        <td rowspan=2 align=center>Kg/HK</td>    
        </tr>        
 	<tr class=rowheader>
        <td align=center>".$_SESSION['lang']['blok']."</td>
        <td align=center>".$_SESSION['lang']['luas']."</td>";    
        if($lha)$stream.="<td align=center>HI</td>    
	<td align=center>SDHI</td>"; else $stream.="<td align=center colspan=2></td>";
        $stream.="<td align=center>Upah</td>            
            <td align=center>Premi</td>            
            <td align=center>Penalty</td>            
	<td align=center>".$_SESSION['lang']['jumlah']."</td>";
	if($lha)$stream.="<td align=center>HI</td>
        <td align=center>SDHI</td>"; else $stream.="<td align=center colspan=2></td>";
	$stream.="</tr>       
        </thead>
	<tbody>";

// kegiatan panen
$str="SELECT kodekegiatan,namakegiatan FROM ".$dbname.".setup_kegiatan 
    where kelompok='PNN' order by kodekegiatan asc limit 1"; // buat jaga2 kalo ada PNN lebih dari 1, ambil yang paling atas aja (panen)
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kodepanen=$bar->kodekegiatan;
    $namapanen=$bar->namakegiatan;
} 

// kamus luas
$str="SELECT kodeorg, luasareaproduktif, tahuntanam FROM ".$dbname.".setup_blok 
    where kodeorg like '".$kdAfd."%'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $area[$bar->kodeorg]=$bar->luasareaproduktif;
} 

if($lha)$str="SELECT count(*) as hk,kodeorg FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
else $str="SELECT count(*) as hk,kodeorg FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal between '".$tgl1_."' and '".$tgl2_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $hksd[$bar->kodeorg]+=$bar->hk;
}
if($lha)$str="SELECT sum(hasilkerjakg)as hasil,kodeorg FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal between '".substr($tgl1_,0,6)."01' and '".$tgl1_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
else $str="SELECT sum(hasilkerjakg)as hasil,kodeorg FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal between '".$tgl1_."' and '".$tgl2_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kgsd[$bar->kodeorg]+=$bar->hasil;
}

$areatotal=0;
$hktotal=0;
$hksdtotal=0;
$upahtotal=0;
$premitotal=0;
$penaltytotal=0;
$jumlahupahtotal=0;
$kgtotal=0;
$kgsdtotal=0;

if($lha)$str="SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal = '".$tgl1_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
else $str="SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal between '".$tgl1_."' and '".$tgl2_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
//echo $str; 
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jumlahupah=$bar->upah+$bar->premi-$bar->penalty;
    @$rppersat=$jumlahupah/$bar->hasil;
    @$kgperhk=$bar->hasil/$bar->hk;
    $areatotal+=$area[$bar->kodeorg];
    $hktotal+=$bar->hk;
    $hksdtotal+=$hksd[$bar->kodeorg];
    $upahtotal+=$bar->upah;
    $premitotal+=$bar->premi;
    $penaltytotal+=$bar->penalty;
    $jumlahupahtotal+=$jumlahupah;
    $kgtotal+=$bar->hasil;
    $kgsdtotal+=$kgsd[$bar->kodeorg];
	$stream.="<tr class=rowcontent>
            <td align=left>".$kodepanen."</td>
            <td align=left>".$namapanen."</td>
            <td align=left>KG</td>
            <td align=left>".$bar->kodeorg."</td>
            <td align=right>".$area[$bar->kodeorg]."</td>
            <td align=center>".$bar->tahuntanam."</td>";
            if($lha)$stream.="<td align=right>".$bar->hk."</td>
            <td align=right>".$hksd[$bar->kodeorg]."</td>";
            else $stream.="<td align=right colspan=2>".$bar->hk."</td>";
            $stream.="<td align=right>".number_format($bar->upah,0)."</td>
            <td align=right>".number_format($bar->premi,0)."</td>
            <td align=right>".number_format($bar->penalty,0)."</td>
            <td align=right>".number_format($jumlahupah,0)."</td>";
            if($lha)$stream.="<td align=right>".number_format($bar->hasil,0)."</td>
            <td align=right>".number_format($kgsd[$bar->kodeorg],0)."</td>";
            else $stream.="<td align=right colspan=2>".number_format($bar->hasil,0)."</td>";
            $stream.="<td align=right>".number_format($rppersat,0)."</td>
            <td align=right>".number_format($kgperhk,2)."</td>
        </tr>    
        ";
}

	$stream.="<tr class=title>
            <td align=left colspan=4>Total</td>
            <td align=right>".$areatotal."</td>
            <td align=center></td>";
            if($lha)$stream.="<td align=right>".$hktotal."</td>
            <td align=right>".$hksdtotal."</td>";
            else $stream.="<td align=right colspan=2>".$hktotal."</td>";
            $stream.="<td align=right>".number_format($upahtotal,0)."</td>
            <td align=right>".number_format($premitotal,0)."</td>";
            $stream.="<td align=right>".number_format($penaltytotal,0)."</td>
            <td align=right>".number_format($jumlahupahtotal,0)."</td>";
            if($lha)$stream.="<td align=right>".number_format($kgtotal,0)."</td>
            <td align=right>".number_format($kgsdtotal,0)."</td>";
            else $stream.="<td align=right colspan=2>".number_format($kgtotal,0)."</td>";
            $stream.="<td align=right></td>
            <td align=right></td>
        </tr>    
        ";

$stream.="</tbody></table>";        
        
        
        
        
        
$stream.="<br>ADMINISTRASI DAN UMUM";
$stream.="<table cellspacing='1' border='0' class='sortable'>";        
$stream.="<thead>
	<tr class=rowheader>
        <td align=center>".$_SESSION['lang']['nomor']."</td>    
        <td align=center>".$_SESSION['lang']['jenis']."</td>    
        <td align=center>".$_SESSION['lang']['jumlahhk']."</td>    
        <td align=center>".$_SESSION['lang']['upahkerja']."</td>    
        </tr></thead><tbody>";


$str="SELECT id FROM ".$dbname.".sdm_ho_component where plus=1";
$komponen="(";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $komponen.="'".$bar->id."',";
}
$komponen=substr($komponen,0,-1);
$komponen.=')';

// cari hk pengawas
if($lha)$str="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal = '".$tgl1_."' and b.kodeorg like '".$kdAfd."%' and c.namakaryawan is not NULL
    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal = '".$tgl1_."' and b.kodeorg like '".$kdAfd."%' and c.namakaryawan is not NULL";
else $str="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1_."' and '".$tgl2_."' and b.kodeorg like '".$kdAfd."%' and c.namakaryawan is not NULL
    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1_."' and '".$tgl2_."' and b.kodeorg like '".$kdAfd."%' and c.namakaryawan is not NULL";
//echo $str;

$awaskar="(";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $awaskar.="'".$bar->nikmandor."',"; // list karyawan pengawas
    $qwe=$bar->nikmandor.$bar->tanggal;
    $awas[$qwe]=$qwe;
}
$awaskar=substr($awaskar,0,-1);
$awaskar.=')';
if(mysql_num_rows($res)==0)$awaskar='(\'\')';

$awashk = count($awas);

// upah pengawas : sebulan, jadi hariannya dibagi 30
$awasupah=0;
$str="SELECT * FROM ".$dbname.".sdm_5gajipokok where karyawanid in ".$awaskar." and idkomponen in ".$komponen." and tahun = '".substr($tgl1,0,4)."'";
//echo $str;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $awasupah+=$bar->jumlah;
}
if($lha)@$awasupah=$awasupah/30;
else @$awasupah=$jumlahhari*$awasupah/30;

// cari hk administrasi
if($lha)$str="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal = '".$tgl1_."' and b.kodeorg like '".$kdAfd."%' and nikmandor not in ".$awaskar." and c.namakaryawan is not NULL
    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal = '".$tgl1_."' and b.kodeorg like '".$kdAfd."%' and keranimuat not in ".$awaskar." and c.namakaryawan is not NULL";
else $str="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1_."' and '".$tgl2_."' and b.kodeorg like '".$kdAfd."%' and nikmandor not in ".$awaskar." and c.namakaryawan is not NULL
    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1_."' and '".$tgl2_."' and b.kodeorg like '".$kdAfd."%' and keranimuat not in ".$awaskar." and c.namakaryawan is not NULL";
//echo $str;

$admkar="(";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $admkar.="'".$bar->nikmandor."',"; // list karyawan administrasi
    $qwe=$bar->nikmandor.$bar->tanggal;
    $adm[$qwe]=$qwe;
}
$admkar=substr($admkar,0,-1);
$admkar.=')';
if(mysql_num_rows($res)==0)$admkar='(\'\')';

$admhk = count($adm);

// upah administrasi : sebulan, jadi hariannya dibagi 30
$admupah=0;
$str="SELECT * FROM ".$dbname.".sdm_5gajipokok where karyawanid in ".$admkar." and karyawanid not in ".$awaskar." and idkomponen in ".$komponen." and tahun = '".substr($tgl1,0,4)."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $admupah+=$bar->jumlah;
}
if($lha)@$admupah=$admupah/30;
else @$admupah=$jumlahhari*$admupah/30;

// cari hk umum
if($lha)$str="SELECT karyawanid FROM ".$dbname.".sdm_absensidt
        where kodeorg like '".$kdAfd."%' and tanggal = '".$tgl1_."' and absensi = 'H' and karyawanid not in ".$admkar." and karyawanid not in ".$awaskar.""; 
else $str="SELECT karyawanid FROM ".$dbname.".sdm_absensidt
        where kodeorg like '".$kdAfd."%' and tanggal between '".$tgl1_."' and '".$tgl2_."' and absensi = 'H' and karyawanid not in ".$admkar." and karyawanid not in ".$awaskar."";
$res=mysql_query($str);
$umumhk=0;

$umumkar="karyawanid in (";
while($bar=mysql_fetch_object($res))
{
    $umumkar.="'".$bar->karyawanid."',"; // list karyawan umum
    $umumhk+=1;
}
$umumkar=substr($umumkar,0,-1);
$umumkar.=')';
if(mysql_num_rows($res)==0)$umumkar='karyawanid = \'\'';
    
// upah umum : sebulan, jadi hariannya dibagi 30
$umumupah=0;
$str="SELECT * FROM ".$dbname.".sdm_5gajipokok where ".$umumkar." and idkomponen in ".$komponen." and tahun = '".substr($tgl1,0,4)."'";
$res=mysql_query($str);
//if(mysql_num_rows($res)==0)
while($bar=mysql_fetch_object($res))
{
    $umumupah+=$bar->jumlah;
}
if($lha)@$umumupah=$umumupah/30;
else @$umumupah=$jumlahhari*$umumupah/30;

$total=$awasupah+$admupah+$umumupah;
$grandtotal=$TOTAL['totalbiaya']+$jumlahupahtotal+$total;
//$cost=@$grandtotal/$TOTAL['hasilbi'];
$cost=@$grandtotal/$luas;

$stream.="<tr class=rowcontent>
        <td align=right>1</td>    
        <td align=left>Pengawasan (Mandor)</td>    
        <td align=right>".number_format($awashk,2)."</td>    
        <td align=right>".number_format($awasupah,0)."</td>    
        </tr>";
$stream.="<tr class=rowcontent>
        <td align=right>2</td>    
        <td align=left>Administrasi (Kerani)</td>    
        <td align=right>".number_format($admhk,2)."</td>    
        <td align=right>".number_format($admupah,0)."</td>    
        </tr>";
$stream.="<tr class=rowcontent>
        <td align=right>3</td>    
        <td align=left>Umum (Kantor)</td>    
        <td align=right>".number_format($umumhk,2)."</td>    
        <td align=right>".number_format($umumupah,0)."</td>    
        </tr>";
$stream.="<tr class=rowcontent>
        <td align=center colspan=3>Total</td>    
        <td align=right>".number_format($total,0)."</td>    
        </tr>";
$stream.="<tr class=rowcontent>
        <td align=center colspan=3>Grand Total Biaya (Rp.)</td>    
        <td align=right>".number_format($grandtotal,0)."</td>    
        </tr>";
$stream.="<tr class=title>
        <td align=center colspan=3>Total Cost (Rp./Ha)</td>    
        <td align=right>".number_format($cost,2)."</td>    
        </tr>";
$stream.="</tbody></table>";

       if($proses=='preview'){
            echo $stream;    
       }
        
       if($proses=='excel'){
            $stream.="</table><br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHms");
            $nop_="LHA_".$kdAfd."_".$tgl1_."_".$tgl2_."_".$dte;
             $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
             gzwrite($gztralala, $stream);
             gzclose($gztralala);
             echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls.gz';
                </script>";            
        }
       if($proses=='pdf')
       {
           //belum selesai
           
            $lkojur=4;
            $lpeker=12;
            $llain=4;
           
            class PDF extends FPDF
            {
                function Header() {
                    global $kdAfd;
                    global $tgl1_,$tgl2_;
                    global $dbname;
                    global $lkojur, $lpeker, $llain,$lha,$luas;

                    $width = $this->w - $this->lMargin - $this->rMargin;
                    $height = 12;
                    $path='images/logo.jpg';
                    $this->Image($path,$this->lMargin,$this->tMargin,20);	
                    $this->SetFont('Arial','B',9);
                    $this->SetFillColor(255,255,255);	
                    $this->SetX(50);   
                    $this->Cell($width-50,$height,'FAJAR BAIZURY GROUP',0,1,'L');	 
                    $this->Line($this->lMargin,$this->tMargin+($height*1),
                        $this->lMargin+$width,$this->tMargin+($height*1));
                    $this->Ln();
                    $this->SetFont('Arial','U',10);
                    $title='LAPORAN HARIAN AFDELING';		
                    $this->Cell($width,$height,$title,0,1,'C');	
                    $this->Ln();	
                    $this->SetFont('Arial','',8);
                    $this->Cell((7/100*$width)-5,$height,$_SESSION['lang']['kebun'],'',0,'L');
                    $this->Cell(5,$height,':','',0,'L');
                    $this->Cell(43/100*$width,$height,substr($kdAfd,0,4),'',0,'L');		
                    $this->Cell(20/100*$width,$height,'','',0,'L');		
                    $this->Cell(15/100*$width,$height,'Diperiksa','',0,'C');		
                    $this->Cell(15/100*$width,$height,'Dibuat','',0,'C');		
                    $this->Ln();	
                    $this->Cell((7/100*$width)-5,$height,$_SESSION['lang']['afdeling'],'',0,'L');
                    $this->Cell(5,$height,':','',0,'L');
                    $this->Cell(43/100*$width,$height,$kdAfd.' ('.number_format($luas,2).' Ha)','',0,'L');		
                    $this->Ln();
                    $this->Cell((7/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                    $this->Cell(5,$height,':','',0,'L');
                    $this->Cell(43/100*$width,$height,tanggalnormal($tgl1_).' '.tanggalnormal($tgl2_),'',0,'L');		
                    $this->Ln();	
                    $this->Cell(70/100*$width,$height,'','',0,'L');		
                    $this->Cell(15/100*$width,$height,'Askep','',0,'C');		
                    $this->Cell(15/100*$width,$height,'Asisten','',0,'C');		
                    $this->Ln();	
//                    $this->SetFont('Arial','',7);
//                    $this->SetFillColor(220,220,220);
//                    $this->Cell($lkojur/100*$width,$height,'Kode',TRL,0,'C',1);
//                    $this->Cell($lpeker/100*$width,$height,$_SESSION['lang']['pekerjaan'],TRL,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,$_SESSION['lang']['satuan'],TRL,0,'C',1);
//                    $this->Cell(($lkojur+$llain)/100*$width,$height,$_SESSION['lang']['kodeblok'],1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Thn',TRL,0,'C',1);
//                    $this->Cell(2*$llain/100*$width,$height,'HK KHT/KHL',1,0,'C',1);
//                    $this->Cell(2*$llain/100*$width,$height,'HK KBL',1,0,'C',1);
//                    $this->Cell(2*$llain/100*$width,$height,'Upah (Rp.)',1,0,'C',1);
//                    $this->Cell(2*$llain/100*$width,$height,'Hasil Kerja',1,0,'C',1);
//                    $this->Cell(((3*$llain)+$lkojur)/100*$width,$height,$_SESSION['lang']['pemakaianBarang'],1,0,'C',1);
//                    $this->Cell(2*$llain/100*$width,$height,'Biaya Bahan',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'T. Biaya',TRL,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Rp./Sat',TRL,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'HK/Sat',TRL,0,'C',1);
//                    $this->Ln();	
//                    $this->Cell($lkojur/100*$width,$height,'Jurnal',BRL,0,'C',1);
//                    $this->Cell($lpeker/100*$width,$height,'',BRL,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
//                    $this->Cell($lkojur/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,$_SESSION['lang']['luas'],1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Tnm',BRL,0,'C',1);
//                    if($lha){
//                    $this->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
//                    }else $this->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
//                    if($lha){
//                    $this->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
//                    }else $this->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Rp./Unit',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Jumlah',1,0,'C',1);
//                    if($lha){
//                    $this->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
//                    }else $this->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
//                    $this->Cell($lkojur/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
//                    if($lha){
//                    $this->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
//                    }else $this->Cell($llain*2/100*$width,$height,'',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Rp./Unit',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'Jumlah',1,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
//                    $this->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
//                    $this->Ln();	
                }

                function Footer()
                {
                    $this->SetY(-15);
                    $this->SetFont('Arial','I',8);
                    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C',1);
                    $this->SetX(-520);
                    $this->Cell(500,10,'Printed By'." : ".$_SESSION['empl']['name'].", ".date('d-m-Y H:i:s'),'',1,'R',1);		
                }
            }           
           
            $pdf=new PDF('L','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 12;
            $pdf->AddPage();
            
                    $pdf->SetFont('Arial','',7);
                    $pdf->SetFillColor(220,220,220);
                    $pdf->Cell($lkojur/100*$width,$height,'Kode',TRL,0,'C',1);
                    $pdf->Cell($lpeker/100*$width,$height,$_SESSION['lang']['pekerjaan'],TRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['satuan'],TRL,0,'C',1);
                    $pdf->Cell(($lkojur+$llain)/100*$width,$height,$_SESSION['lang']['kodeblok'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Thn',TRL,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'HK KHT/KHL',1,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'HK KBL',1,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'Upah (Rp.)',1,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'Hasil Kerja',1,0,'C',1);
                    $pdf->Cell(((3*$llain)+$lkojur)/100*$width,$height,$_SESSION['lang']['pemakaianBarang'],1,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'Biaya Bahan',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'T. Biaya',TRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Rp./Sat',TRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'HK/Sat',TRL,0,'C',1);
                    $pdf->Ln();	
                    $pdf->Cell($lkojur/100*$width,$height,'Jurnal',BRL,0,'C',1);
                    $pdf->Cell($lpeker/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($lkojur/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['luas'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Tnm',BRL,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Rp./Unit',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Jumlah',1,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
                    $pdf->Cell($lkojur/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,'',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Rp./Unit',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Jumlah',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Ln();	
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',5);
            
                
 //PRINT OUT============================================
            if(!empty($master['blok']))foreach($master['blok'] as $kunc=>$va){
               $qwe=1;
                $pdf->Cell($lkojur/100*$width,$height,$master['kegiatan'][$kunc],1,0,'L',1);
                $pdf->Cell($lpeker/100*$width,$height,$master['namakegiatan'][$kunc],1,0,'L',1);
                $pdf->Cell($llain/100*$width,$height,$master['satuankegiatan'][$kunc],1,0,'L',1);
                $pdf->Cell($lkojur/100*$width,$height,$master['blok'][$kunc],1,0,'L',1);
                $pdf->Cell($llain/100*$width,$height,number_format($master['luas'][$kunc],2),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,$master['thntnm'][$kunc],1,0,'C',1);
                if($lha){
                $pdf->Cell($llain/100*$width,$height,number_format($master['hkkhl'][$kunc],2),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($master['hkkhlsbi'][$kunc],2),1,0,'R',1);
                }else $pdf->Cell($llain*2/100*$width,$height,number_format($master['hkkhl'][$kunc],2),1,0,'R',1);
                if($lha){
                $pdf->Cell($llain/100*$width,$height,$master['hkkbl'][$kunc],1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,$master['hkkblsbi'][$kunc],1,0,'R',1);
                }else $pdf->Cell($llain*2/100*$width,$height,$master['hkkbl'][$kunc],1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($master['rpperhk'][$kunc],0),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($master['totalupah'][$kunc],0),1,0,'R',1);
                if($lha){
                $pdf->Cell($llain/100*$width,$height,number_format($master['hasilbi'][$kunc],2),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($master['hasilsbi'][$kunc],2),1,0,'R',1);
                }else $pdf->Cell($llain*2/100*$width,$height,number_format($master['hasilbi'][$kunc],2),1,0,'R',1);
                //                $pdf->Cell(((5*$llain)+$lkojur)/100*$width,$height,'',1,0,'C',1);
                if(!empty($master['barangsbi']))foreach($master['barangsbi'][$kunc] as $dd=>$ee)
                {
                    if($qwe==0){
                        $pdf->Cell((2*$lkojur+$lpeker+(11*$llain))/100*$width,$height,'',0,0,'L',0);
                    }
                    $pdf->Cell($lkojur/100*$width,$height,$master['barangsbi'][$kunc][$dd],1,0,'L',1);
                    $pdf->Cell($llain/100*$width,$height,$master['satuanbarangsbi'][$kunc][$dd],1,0,'L',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,number_format($master['qtybi'][$kunc][$dd],2),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($master['qtysbi'][$kunc][$dd],2),1,0,'R',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,number_format($master['qtybi'][$kunc][$dd],2),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($master['hargabarangbi'][$kunc][$dd],0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($master['bybarangbi'][$kunc][$dd],0),1,0,'R',1);         
                    if($qwe==1){
                        $qwe=0;
                        $pdf->Cell($llain/100*$width,$height,number_format($master['totbiaya'][$kunc],0),1,0,'R',1);
                        $pdf->Cell($llain/100*$width,$height,number_format($master['rppersatuan'][$kunc],0),1,0,'R',1);
                        $pdf->Cell($llain/100*$width,$height,number_format($master['hkpersatuan'][$kunc],2),1,0,'R',1);
                    }
                    $pdf->Ln();	       
                }
            }     
            $pdf->Cell(($lkojur+$lpeker+$llain)/100*$width,$height,'Total',1,0,'C',1);
            $pdf->Cell($lkojur/100*$width,$height,'',1,0,'L',1);
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['luas'],2),1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,'',1,0,'C',1);
            if($lha){
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['hkkhlbi']),1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['hkkhlsbi']),1,0,'R',1);
            }else $pdf->Cell($llain*2/100*$width,$height,number_format($TOTAL['hkkhlbi']),1,0,'R',1);
            if($lha){
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['hkkblbi']),1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['hkkblsbi']),1,0,'R',1);
            }else $pdf->Cell($llain*2/100*$width,$height,number_format($TOTAL['hkkblbi']),1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,'',1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['totalupah']),1,0,'R',1);
            if($lha){
            $pdf->Cell($llain/100*$width,$height,'',1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,'',1,0,'R',1);
            }else $pdf->Cell($llain*2/100*$width,$height,number_format($TOTAL['hasilbi'],2),1,0,'R',1);
            $pdf->Cell((4*$llain+($lkojur))/100*$width,$height,'',1,0,'L',1);
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['totalbiayabarang']),1,0,'R',1);         
            $pdf->Cell($llain/100*$width,$height,number_format($TOTAL['totalbiaya']),1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,'',1,0,'R',1);
            $pdf->Cell($llain/100*$width,$height,'',1,0,'R',1);
                    $pdf->Ln();	       
                    $pdf->Ln();	  

                    $ar=$pdf->GetY();

            if($ar>400){
                $pdf->AddPage();
            }else $pdf->Ln();	
            
                    $pdf->SetFont('Arial','',7);
                    $pdf->SetFillColor(220,220,220);
                    $pdf->Cell($lkojur/100*$width,$height,'Kode',TRL,0,'C',1);
                    $pdf->Cell($lpeker/100*$width,$height,$_SESSION['lang']['pekerjaan'],TRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['satuan'],TRL,0,'C',1);
                    $pdf->Cell(($lkojur+$llain)/100*$width,$height,$_SESSION['lang']['kodeblok'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Thn',TRL,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'HK',1,0,'C',1);
                    $pdf->Cell(4*$llain/100*$width,$height,'Biaya',1,0,'C',1);
                    $pdf->Cell(2*$llain/100*$width,$height,'Hasil Kerja',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Rp./Sat',TRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'HK/Sat',TRL,0,'C',1);
                    $pdf->Ln();	
                    $pdf->Cell($lkojur/100*$width,$height,'Jurnal',BRL,0,'C',1);
                    $pdf->Cell($lpeker/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($lkojur/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['luas'],1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Tnm',BRL,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Upah',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Premi',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Penalty',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'Jumlah',1,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,'HI',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'sd HI',1,0,'C',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,'',RLB,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',BRL,0,'C',1);
                    $pdf->Ln();	
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',5);                    

// copy dari atasnya administrasi dan umum
            
$areatotal=0;
$hktotal=0;
$hksdtotal=0;
$upahtotal=0;
$premitotal=0;
$penaltytotal=0;
$jumlahupahtotal=0;
$kgtotal=0;
$kgsdtotal=0;

if($lha)$str="SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal = '".$tgl1_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
else $str="SELECT count(*) as hk,kodeorg,tahuntanam,sum(hasilkerjakg)as hasil,sum(upahkerja)as upah,sum(upahpremi)as premi,sum(rupiahpenalty)penalty FROM ".$dbname.".kebun_prestasi_vw 
    where tanggal between '".$tgl1_."' and '".$tgl2_."' and kodeorg like '".$kdAfd."%' group by kodeorg";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jumlahupah=$bar->upah+$bar->premi-$bar->penalty;
    @$rppersat=$jumlahupah/$bar->hasil;
    @$kgperhk=$bar->hasil/$bar->hk;
    $areatotal+=$area[$bar->kodeorg];
    $hktotal+=$bar->hk;
    $hksdtotal+=$hksd[$bar->kodeorg];
    $upahtotal+=$bar->upah;
    $premitotal+=$bar->premi;
    $penaltytotal+=$bar->penalty;
    $jumlahupahtotal+=$jumlahupah;
    $kgtotal+=$bar->hasil;
    $kgsdtotal+=$kgsd[$bar->kodeorg];

                    $pdf->Cell($lkojur/100*$width,$height,$kodepanen,1,0,'L',1);
                    $pdf->Cell($lpeker/100*$width,$height,$namapanen,1,0,'L',1);
                    $pdf->Cell($llain/100*$width,$height,'KG',1,0,'L',1);
                    $pdf->Cell($lkojur/100*$width,$height,$bar->kodeorg,1,0,'L',1);
                    $pdf->Cell($llain/100*$width,$height,$area[$bar->kodeorg],1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,$bar->tahuntanam,1,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,$bar->hk,1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,$hksd[$bar->kodeorg],1,0,'R',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,$bar->hk,1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($bar->upah,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($bar->premi,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($bar->penalty,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($jumlahupah,0),1,0,'R',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,number_format($bar->hasil,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($kgsd[$bar->kodeorg],0),1,0,'R',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,number_format($bar->hasil,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($rppersat,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($kgperhk,2),1,0,'R',1);
                    $pdf->Ln();	    
    }        
                    $pdf->Cell(($lkojur+$lpeker+$llain)/100*$width,$height,'Total',1,0,'C',1);
                    $pdf->Cell($llain/100*$width,$height,'',1,0,'L',1);
                    $pdf->Cell($llain/100*$width,$height,$areatotal,1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,'',1,0,'C',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,$hktotal,1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,$hksdtotal,1,0,'R',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,$hktotal,1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($upahtotal,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($premitotal,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($penaltytotal,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($jumlahupahtotal,0),1,0,'R',1);
                    if($lha){
                    $pdf->Cell($llain/100*$width,$height,number_format($kgtotal,0),1,0,'R',1);
                    $pdf->Cell($llain/100*$width,$height,number_format($kgsdtotal,0),1,0,'R',1);
                    }else $pdf->Cell($llain*2/100*$width,$height,number_format($kgtotal,0),1,0,'R',1);
                    $pdf->Cell(($llain+$llain)/100*$width,$height,'',1,0,'R',1);
                    $pdf->Ln();	    
            
                    $ar=$pdf->GetY();

            if($ar>400){
                $pdf->AddPage();
            }else $pdf->Ln();	
                    
                $pdf->Cell(($lkojur+$lpeker+$llain+$llain)/100*$width,$height,'ADMINISTRASI DAN UMUM ',0,0,'L',1);
                    $pdf->SetFillColor(220,220,220);
                    $pdf->Ln();	       
                $pdf->Cell($lkojur/100*$width,$height,$_SESSION['lang']['nomor'],1,0,'L',1);
                $pdf->Cell($lpeker/100*$width,$height,$_SESSION['lang']['jenis'],1,0,'L',1);
                $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['jumlahhk'],1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,$_SESSION['lang']['upahkerja'],1,0,'R',1);
                    $pdf->Ln();	       
                    $pdf->SetFillColor(255,255,255);	
                $pdf->Cell($lkojur/100*$width,$height,'1',1,0,'R',1);
                $pdf->Cell($lpeker/100*$width,$height,'Pengawasan Mandor)',1,0,'L',1);
                $pdf->Cell($llain/100*$width,$height,number_format($awashk,2),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($awasupah,0),1,0,'R',1);
                    $pdf->Ln();	       
                $pdf->Cell($lkojur/100*$width,$height,'2',1,0,'R',1);
                $pdf->Cell($lpeker/100*$width,$height,'Administrasi (Kerani)',1,0,'L',1);
                $pdf->Cell($llain/100*$width,$height,number_format($admhk,2),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($admupah,0),1,0,'R',1);
                    $pdf->Ln();	       
                $pdf->Cell($lkojur/100*$width,$height,'3',1,0,'R',1);
                $pdf->Cell($lpeker/100*$width,$height,'Umum (Kantor)',1,0,'L',1);
                $pdf->Cell($llain/100*$width,$height,number_format($umumhk,2),1,0,'R',1);
                $pdf->Cell($llain/100*$width,$height,number_format($umumupah,0),1,0,'R',1);
                    $pdf->Ln();	       
                $pdf->Cell(($lkojur+$lpeker+$llain)/100*$width,$height,'Total',1,0,'C',1);
                $pdf->Cell($llain/100*$width,$height,number_format($total,0),1,0,'R',1);
                    $pdf->Ln();	       
                $pdf->Cell(($lkojur+$lpeker+$llain)/100*$width,$height,'Grand Total (Rp.)',1,0,'C',1);
                $pdf->Cell($llain/100*$width,$height,number_format($grandtotal,0),1,0,'R',1);
                    $pdf->Ln();	       
                    $pdf->SetFillColor(220,220,220);
                $pdf->Cell(($lkojur+$lpeker+$llain)/100*$width,$height,'Total Cost (Rp./Ha)',1,0,'C',1);
                $pdf->Cell($llain/100*$width,$height,number_format($cost,2),1,0,'R',1);
                    $pdf->Ln();	            
                     $pdf->SetFillColor(255,255,255);            
                
            $pdf->Output();
           
       }
    }
?>