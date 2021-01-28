<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

			function tpbiaya($value,$org){
				if ($org=="CBGM"){
					/*
					if($value=="CBGM01" ||
						$value=="CBGM02" ||
						$value=="CBGM03" ||
						$value=="CBGM04" ||
						$value=="CBGM05" ||
						$value=="CBGM06" ||
						$value=="CBGM07" ||
						$value=="CBGM08" ||
						$value=="CBGM09" ||
						$value=="CBGM10" ||
						$value=="CBGM11" ||
						$value=="CBGM13" ){
					*/
					if($value=="PRKT. KANTOR" ||
						$value=="PRKT. SATPAM" ||
						$value=="MANDOR 1"  ){
						return "B/TL";
					}else{
						return "B/L";
					}
				}else{
					if($value=="PEMANEN" ||
						$value=="HELPER" ||
						$value=="KERNET" ||
						$value=="PEMELIHARAAN" ){
						return "B/L";
					}else{
						return "B/TL";
					}
				}
			 }
			 
	function tpkarcbgm($value){
				 switch($value){
					 //KANTOR
					 
					/*
					 85	Timbangan
					 94	Pemb. Gudang
					 96	Kepala Gudang
					 142	Kerani Gudang
					 150	Pjs. Ka. Gudang
					 162	Pemb. Krani Gudang
					 225	Admin U/P
					 57	Admin Area
					 58	Admin Humas
					 75	Admin IT
					 84	Administrasi
					 93	Admin Produksi
					 108	Admin Tanaman
					 167	Admin Keuangan
					 225	Admin U/P
					 261	Staff Administrasi
					 267	Admin Umum
					 270	Admin OWL USJ
					 135	Pengurus Masjid
					 */
					
					
					 case 85:
					 case 94:
					 case 96:
					 case 142:
					 case 150:
					 case 162:
					 case 225:
					 case 57:
					 case 58:
					 case 75:
					 case 84:
					 case 108:
					 case 167:
					 case 225:
					 case 261:
					 case 267:
					 case 270:
					 case 135:
					 case 53:
					 case 138:
					 case 120:

					 return "PRKT. KANTOR";
					 break;
					 /*
					 129	Danru Satpam
					 43	Satpam
					 130	Wadan Ru
					 82	Pam Portal
					 */
					 //SATPAM
					 case 129:
					 case 43:
					 case 130:
					 case 82:
					 CASE 228:
					 return "PRKT. SATPAM";
					 break;
					 //MANDOR 1
					/*
					 37	Mandor 1
					 109	Pjs. Mandor I
					 */
					 case 37:
					 case 109:
					 return "MANDOR 1";
					 break;

case 55:
case 175:
case 206:
 
return "PRGKT. ALAT BERAT";
break;
CASE 1:
CASE 287:

return "PRGKT. KENDARAAN";
break;
					 default:
					 return "PEKERJA";
					 
					 break;
					 
				 }
			 }
			 
			 function tpkar($value){
			    switch($value){
				CASE 53:
				CASE 142:
				CASE 167:
				CASE 225:
				CASE 226:
				CASE 238:
				CASE 264:
				CASE 48:
				CASE 93:	 
				CASE 96:
				CASE 99:
				CASE 105:
				CASE 108:
				CASE 138:
				CASE 156:
				CASE 171:
				CASE 174:
				CASE 178:
				CASE 229:
				CASE 292:
				
				CASE 147:
				CASE 162:
				CASE 49:
				CASE 174:
				CASE 57:
				CASE 58:
				CASE 59:
				CASE 188:
				
				CASE 267:
				CASE 84:
				CASE 88:
				CASE 203:
				CASE 180:
				CASE 179:
				CASE 178:
				CASE 65:
				CASE 237:
				CASE 310:
				case 202:
				case 135:				
				return "PRGKT. KANTOR";
				break;
				CASE 70:
				CASE 37:
				CASE 161:
				CASE 109:
				CASE 296:
				CASE 295:
				CASE 230:
				CASE 180:
				CASE 300:
				CASE 246:
				CASE 301:
				CASE 170:
				return "PRGKT. AFD";
				break;
				CASE 71:
				CASE 73:
				CASE 81:
				CASE 102:
				CASE 165:
				CASE 164:
				CASE 123:
				CASE 158:
				CASE 122:
				CASE 103:
				CASE 298:
				CASE 293:
				CASE 277:
				CASE 166:
				CASE 126:
				CASE 107:
				CASE 121:
				CASE 62:
				CASE 252:
				CASE 182:
				CASE 309:
				return "PRGKT. PENGAWASAN";
				break;
				case 129:
				CASE 82:
				CASE 43:
				CASE 228:
				CASE 227:
				CASE 233:
				
				return "PRGKT. SATPAM";
				break;
				CASE 297:
				CASE 232:
				CASE 243:
				CASE 299:
				CASE 240:
				return "PRGKT. CENTENG";
				break;
CASE 175:
CASE 206:
CASE 55:
CASE 95:
CASE 212:
CASE 208:
CASE 244:
CASE 205:
CASE 207:
CASE 210:
CASE 211:
CASE 212:
CASE 213:
CASE 220:
CASE 61:
case 204:
case 185:
return "PRGKT. ALAT BERAT";
break;
CASE 1:
CASE 287:
CASE 193:
CASE 209:
return "PRGKT. KENDARAAN";
break;
				CASE 159:
				return "KONTROL API";
				break;
				CASE 64:
				return "PEMANEN";
				break;
				CASE 250:
				return "PEMELIHARAAN";
				break;
				CASE 215:
				CASE 216:
				CASE 217:
				CASE 218:
				CASE 219:
				CASE 221:
				CASE 231:	
				return "HELPER";
				break;
				CASE 72:
				CASE 181:
				return "KERNET";
				break;
				 }
			 }
			    
$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
#ambil periode akuntansi
$str2="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
    where kodeorg='".$_SESSION['empl']['lokasitugas']."'
    and periode='".$param['periode']."'";
$tgmulai='';
$tgsampai='';
$res2=mysql_query($str2);
while($bar2=mysql_fetch_object($res2))
{
    $tgsampai   = $bar2->tanggalsampai;
    $tgmulai    = $bar2->tanggalmulai;
}
if($tgmulai=='' || $tgsampai=='')
    exit("Error: Periode akuntasi tidak terdaftar");








#---------------------------------------------------------------
#Ambil Komponen Gaji yg dimiliki karyawan
#---------------------------------------------------------------
 $str1="select a.jumlah,a.idkomponen,a.karyawanid,b.subbagian,b.kodejabatan from ".$dbname.".sdm_gajidetail_vw a  left join   ".$dbname.".`datakaryawan` b  ON a.karyawanid=b.karyawanid
       where a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and a.periodegaji='".$param['periode']."' order by b.subbagian ,b.kodejabatan asc" ;
 $res1=  mysql_query($str1);
 $gaji=Array();
 while($bar1=mysql_fetch_object($res1))
 {        
        $gaji[$bar1->karyawanid][$bar1->idkomponen]=$bar1->jumlah;
 }
 
 
 #2 Ambil subunit setiap karyawan
 $str="select a.subbagian,a.karyawanid,a.namakaryawan,b.namaorganisasi,a. kodejabatan,c.namajabatan from ((".$dbname.".datakaryawan a left join ".$dbname.".organisasi b ON a.subbagian=b.kodeorganisasi ) LEFT JOIN ".$dbname.".sdm_5jabatan c ON a.kodejabatan=c.kodejabatan)
       where a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
	   //exit('Error: '. $str);
 $res=mysql_query($str);
 $subunit=Array();
 $nmkaryawan=Array();
 $karjab=Array();
 $nmunit=Array();
 while($bar=mysql_fetch_object($res))
 {
     $subunit[$bar->karyawanid]=$bar->subbagian;
     $nmkaryawan[$bar->karyawanid]=$bar->namakaryawan;
	 $karjab[$bar->karyawanid]=$bar->namajabatan;
	 $nmunit[$bar->karyawanid]=$bar->namaorganisasi;
	 $kdjab[$bar->karyawanid]=$bar->kodejabatan;
	 
     
 }
 

  #buang karyawan yang tanggalmasuknya > dari tanggal akhir periode
    $str1="select karyawanid from ".$dbname.".datakaryawan where 
           lokasitugas='".$_SESSION['empl']['lokasitugas']."'
           and tanggalmasuk>'".$tgsampai."'";
    $res1=mysql_query($str1);
    while($bar1=mysql_fetch_object($res1))
    {
        unset($gaji[$bar1->karyawanid]);
    }     

 #==========================================================================================  
  
   #buang karyawan yang gajinya sudah teralokasi
 
    $str="select karyawanid from ".$dbname.".kebun_kehadiran_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
      unset($gaji[$bar->karyawanid]);
    }
   
  #b. ambil prestasi kebun 
    $str="select karyawanid from ".$dbname.".kebun_prestasi_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
       unset($gaji[$bar->karyawanid]);
    }  
       
 #==========================================================================================
 
 /*
 
 
 
 
 
 
 
 
 
 
 $str="select jumlah,idkomponen,karyawanid from ".$dbname.".sdm_gajidetail_vw 
       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and idkomponen=37 and periodegaji='".$param['periode']."'";
 $resx=  mysql_query($str);
 $potx=Array();
 while($barx=mysql_fetch_object($resx))
 {
     $potx[$barx->karyawanid]=$barx->jumlah;
 }
 
#---------------------------------------------------------------
#ambil denda
#---------------------------------------------------------------
 $str="select jumlah,idkomponen,karyawanid from ".$dbname.".sdm_gajidetail_vw 
       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and idkomponen=34 and periodegaji='".$param['periode']."'";
 $resx=  mysql_query($str);
 $potx=Array();
 while($barx=mysql_fetch_object($resx))
 {
     $potd[$barx->karyawanid]=$barx->jumlah;
 }
 
#---------------------------------------------------------------
#ambil komponen gaji/upah
#---------------------------------------------------------------
 $str="select jumlah,idkomponen,karyawanid from ".$dbname.".sdm_gajidetail_vw 
       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and idkomponen in (1,2,14,30,31,40) and periodegaji='".$param['periode']."'";
 $resx=  mysql_query($str);
 $potx=Array();
 
 while($barx=mysql_fetch_object($resx))
 {
     $upah[$barx->karyawanid]=$barx->jumlah;
 }
 
 #2 Ambil subunit setiap karyawan
 $str="select subbagian,karyawanid,namakaryawan from ".$dbname.".datakaryawan 
       where lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
 $res=mysql_query($str);
 $subunit=Array();
 while($bar=mysql_fetch_object($res))
 {
     $subunit[$bar->karyawanid]=$bar->subbagian;
     $namakaryawan[$bar->karyawanid]=$bar->namakaryawan;
     
 }
 
#------------------------------------------------------
# Gaji/Upah
#------------------------------------------------------
#+ Gaji Pokok 1
#+ Tunjangan Jabatan 2
#+ Rapel 14
#+ Tunjangan Khusus 30
#+ Tunjangan Lain-Lain 31
#+ Tunjangan Natura 40

#- Potongan HK
#- Denda

# Lembur
#+ Lembur


# Premi
#+ Premi Pengawasan
#+ Premi
#+ Premi Pabrik

#- Potongan Premi Sudah Bayar


# Thr
#+ Thr

# Insentif
#+ Bonus

# JHT
#+ Jamsostek

# BPJS
#+ BPJS

# Pinjaman
#- Angsuran Fasilitas Kerja
#- Angsuran Pinjaman Karyawan
#- Potongan Koperasi






#---------------------------------------------------------------
#ambil semua gaji per karyawan
#---------------------------------------------------------------
#1. Ambil gaji total per karyawan pada unit bersangkutan
 $str="select jumlah,idkomponen,karyawanid from ".$dbname.".sdm_gajidetail_vw 
       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and plus=1 and periodegaji='".$param['periode']."'";
 $res=  mysql_query($str);
 $gaji=Array();
 while($bar=mysql_fetch_object($res))
 {
     if($bar->idkomponen==1) 
        $gaji[$bar->karyawanid][$bar->idkomponen]=$bar->jumlah-$potx[$bar->karyawanid];//dikurangkan dengan potongan HK
     else
        $gaji[$bar->karyawanid][$bar->idkomponen]=$bar->jumlah;
 }
 
 #3 ambil semua organisasi yang traksi atau workshop
 $str="select distinct kodeorganisasi,tipe from ".$dbname.".organisasi 
       where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
 $res=mysql_query($str);
 $tipe=Array();
 while($bar=mysql_fetch_object($res))
 {
     $tipe[$bar->kodeorganisasi]=$bar->tipe;
     
 } 

  #==========================================================================================  
   $GJ=$gaji;
   #buang karyawan yang gajinya sudah teralokasi
 
    $str="select karyawanid from ".$dbname.".kebun_kehadiran_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
      unset($gaji[$bar->karyawanid]);
    }
    #buang karyawan yang tanggalmasuknya > dari tanggal akhir periode
    $str1="select karyawanid from ".$dbname.".datakaryawan where 
           lokasitugas='".$_SESSION['empl']['lokasitugas']."'
           and tanggalmasuk>'".$tgsampai."'";
    $res1=mysql_query($str1);
    while($bar1=mysql_fetch_object($res1))
    {
        unset($gaji[$bar1->karyawanid]);
    }    
  #b. ambil prestasi kebun
    $str="select karyawanid from ".$dbname.".kebun_prestasi_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
       unset($gaji[$bar->karyawanid]);
    }

 #==========================================================================================
    #ambil kendaraan atau mesin yang menempel pada orang
    $str="select vhc,karyawanid from ".$dbname.".vhc_5operator";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $ken[$bar->karyawanid]=$bar->vhc;
    }
 #ambil komponen gaji
     $str="select id,name from ".$dbname.".sdm_ho_component";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $komponen[$bar->id]=$bar->id;
        $namakomponen[$bar->id]=$bar->name;
    }   
    

 #ambil gaji yang sudah teralokasi per karyawan
 #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  #a.kehadiran kebun
    $str="select sum(umr) as umr, sum(insentif) as insentif,karyawanid from ".$dbname.".kebun_kehadiran_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1 group by karyawanid";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $potongan[$bar->karyawanid][1]+=$bar->umr;//potongan gaji pokok
        $potongan[$bar->karyawanid][32]+=$bar->insentif; //potongan premi    
    }
  #b. ambil prestasi kebun
    $str="select sum(upahkerja) as umr, sum(upahpremi) as insentif,sum(rupiahpenalty) as penalty,
          karyawanid from ".$dbname.".kebun_prestasi_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1 group by karyawanid";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $potongan[$bar->karyawanid][1]+=$bar->umr-$bar->penalty;//potongan gaji pokok
        $potongan[$bar->karyawanid][32]+=$bar->insentif; //potongan premi 
    }    
 #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

 #kurangkan gaji yang ada dengan yang sudah dialokasi
    $gajiblmalokasi=$GJ;
    foreach($GJ as $key=>$row)
    {
       $gajiblmalokasi[$key][1]-= $potongan[$key][1];
       $gajiblmalokasi[$key][32]-= $potongan[$key][32];
    }
 #ambil selisih kekurangan 
    $kekurangan=0;
    foreach($gajiblmalokasi as $key)
    { 
      foreach($key as $row=>$cell)
      {
        if($cell<0)
            $kekurangan+=$cell;
      }
    }

  #=======================================================================================================  
    */
   #echo $kekurangan; Buang escape ini untuk mengetahui selisih gaji yang belum teralokasi 
 if(empty($gaji))
     exit('Error: Tidak ada data gaji pada periode tsb');
 else {

     
       
             $data="<button class=mybutton onclick=prosesNewGaji(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>Periode</td>
                    <td>Nama.Karyawan</td>
                    <td>Karyawanid</td>
					<td>Jabatan</td>
					<td>Unit</td>
					<td>Tipe</td>
					<td>Jenis</td>
					<td>Biaya Gaji</td>
					<td>Biaya Lembur</td>
					<td>Pengawasan</td> 
					<td>Biaya Premi</td>
					<td>Biaya Thr</td>
					<td>Biaya Insentif</td>
					<td>Tot. Pinjaman</td>
					<td>Hut. JHT</td>
					<td>Hut. JK</td>
					<td>Hut. JP</td>
					<td>Hut. Gaji</td>
					
                    </tr>
                  </thead>
                  <tbody>";
				  /*
					<td>Debet(D)</td>
					<td>Kredit(C)</td>
					<td>Balance(B)</td>
					*/
				  /*
				    $data="<button class=mybutton onclick=prosesGaji(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>Periode</td>
                    <td>Nama.Karyawan</td>
                    <td>Karyawanid</td>
					<td>Unit</td>
					<td>Tipe</td>

					<td>Gaji Pokok</td>
					<td>Pot. HK</td>
					<td>Pot. Denda</td>

					<td>Tot. Gaji</td>
					
					<td>T. Jabatan</td>
					<td>T. Khusus</td>
					<td>T. Lain-Lain</td>
					<td>T. Natura</td>
					<td>Premi</td>
					<td>Pot. Premi</td>
					<td>Tot. Premi</td>
					<td>Premi Pengawasan</td>
					<td>Premi Pabrik</td>
					<td>Rapel</td>
					<td>Biaya Gaji</td>
					<td>Biaya Lembur</td>
					<td>Biaya Premi</td>
					<td>Biaya Thr</td>
					<td>Biaya Insentif</td>
					<td>Pot. Fasilitas</td>
					<td>Pot. Pinjaman</td>
					<td>Pot. Koperasi</td>
					<td>Tot. Pinjaman</td>
					<td>Hut. JHT</td>
					<td>Hut. JK</td>
					<td>Tot. Potongan</td>
					<td>Hut. Gaji</td>
                    </tr>
                  </thead>
                  <tbody>";*/

             $no=0;
			 $SORTAFD=ARRAY();
            foreach($gaji as $key =>$baris)
             {
				 $dwf=tpkar($kdjab[$key]);
				 $dwf2=tpkarcbgm($kdjab[$key]);
				 
               $tb1=tpbiaya($dwf,$_SESSION['empl']['lokasitugas']);
			   //$tb2=tpbiaya($subunit[$key],$_SESSION['empl']['lokasitugas']);
			   $tb2=tpbiaya($dwf2,$_SESSION['empl']['lokasitugas']);
			  
                $no+=1;
				/*
					$data.="<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
                    <td id='periode".$no."'>".$_POST['periode']."</td>
                    <td id='namakaryawan".$no."'>".$nmkaryawan[$key]."</td>
                    <td id='karyawanid".$no."'>".$key."</td>    
					<td id='subbagian".$no."'>".$subunit[$key]."</td>";
					*/
					if (substr($_SESSION['empl']['lokasitugas'], -1) == "E" || $_SESSION['empl']['lokasitugas'] == "FBAO" ){
						//tipe berdasarkan jabatan
						/*
						$data.="<td id='tipe".$no."'>".$dwf."</td>"; 
						$data.="<td id='biaya".$no."'>".$tb1."</td>";
						*/
						$TPA=$dwf;
						$BYA=$tb1;
					}else{
						//tipe berdasarkan unit
						/*
						$data.="<td id='tipe".$no."'>".$nmunit[$key]."</td>";
						$data.="<td id='biaya".$no."'>".$tb2."</td>";
						*/
						//$TPA=$nmunit[$key];
						$TPA=$dwf2;
						$BYA=$tb2;
					}
					$afd=$subunit[$key];
				
			
					//$totgp=$baris[1]-($baris[37]+$baris[34]);
					$totgp=$baris[1];
					//$totpre=$baris[32]-$baris[36];
					$totpre=$baris[32];
					//$bigaji=($totgp+$baris[2]+$baris[30]+$baris[31]+$baris[40]+$baris[14]);
					$bigaji=($totgp+$baris[2]+$baris[30]+$baris[40])-($baris[37]+$baris[34]+$baris[36]);
					$blembur=$baris[33];
					$bpremi=$totpre+$baris[38]+$baris[31];
					$bpenga=$baris[16];
					$bthr=$baris[28];
					$binse=$baris[26]+$baris[14]+$baris[42];
					$tpinj=$baris[5]+$baris[9]+$baris[18];
					$hjht=$baris[3];
					$hjk=$baris[39];
					$hjp=$baris[41];
					$tpot=$tpinj+$baris[3]+$baris[39]+$baris[41];
					$hgaji=($bigaji+$blembur+$bpremi+$bpenga+$bthr+$binse)-$tpot;
					$dbet=$bigaji+$blembur+$bpremi+$bpenga+$bthr+$binse;
					$dkre=$hgaji+$hjht+$hjk+$hjp+$tpinj;
					$dbal=$dbet-$dkre;
					/*
					$data.="
					 <td align=right id='AP".$no."'>".round($bigaji)."</td>
					 <td align=right id='AQ".$no."'>".round($blembur)."</td>
					 <td align=right id='AR".$no."'>".round($bpremi)."</td>
					 <td align=right id='AS".$no."'>".round($bthr)."</td>
					 <td align=right id='AT".$no."'>".round($binse)."</td>
					
					 <td align=right id='AX".$no."'>".round($tpinj)."</td>
					 <td align=right id='AY".$no."'>".round($hjht)."</td>
					 <td align=right id='AZ".$no."'>".round($hjk)."</td>
					 <td align=right id='BB".$no."'>".round($hgaji)."</td>
					 <td align=right id='BB".$no."'>".round($dbet)."</td>
					 <td align=right id='BB".$no."'>".round($dkre)."</td>
					 <td align=right id='BB".$no."'>".round($dbal)."</td>
                    </tr>";
					*/
					/*
					$data.=" <td align=right id='AB".$no."'>".round($baris[1])."</td>
					 <td align=right id='AC".$no."'>".round($baris[37])."</td>
					 <td align=right id='AD".$no."'>".round($baris[34])."</td>
					 <td align=right id='AE".$no."'>".round($totgp)."</td>
					 <td align=right id='AF".$no."'>".round($baris[2])."</td>
					 <td align=right id='AG".$no."'>".round($baris[30])."</td>
					 <td align=right id='AH".$no."'>".round($baris[31])."</td>
					 <td align=right id='AI".$no."'>".round($baris[40])."</td>
					 <td align=right id='AJ".$no."'>".round($baris[32])."</td>
					 <td align=right id='AK".$no."'>".round($baris[36])."</td>
					 <td align=right id='AL".$no."'>".round($totpre)."</td>
					 <td align=right id='AM".$no."'>".round($baris[16])."</td>
					 <td align=right id='AN".$no."'>".round($baris[38])."</td>
					 <td align=right id='AO".$no."'>".round($baris[14])."</td>
					 <td align=right id='AP".$no."'>".round($bgaji)."</td>
					 <td align=right id='AQ".$no."'>".round($blembur)."</td>
					 <td align=right id='AR".$no."'>".round($bpremi)."</td>
					 <td align=right id='AS".$no."'>".round($bthr)."</td>
					 <td align=right id='AT".$no."'>".round($binse)."</td>
					 <td align=right id='AU".$no."'>".round($baris[5])."</td>
					 <td align=right id='AV".$no."'>".round($baris[9])."</td>
					 <td align=right id='AW".$no."'>".round($baris[18])."</td>
					 <td align=right id='AX".$no."'>".round($tpinj)."</td>
					 <td align=right id='AY".$no."'>".round($hjht)."</td>
					 <td align=right id='AZ".$no."'>".round($hjk)."</td>
					 <td align=right id='BA".$no."'>".round($tpot)."</td>
					 <td align=right id='BB".$no."'>".round($hgaji)."</td>
                    </tr>";
					
					*/
					/*
                $tgp+=round($baris);
				$tlem+=round($blembur[$key]);
				$tpre+=round($bpremi[$key]);
				$tthr+=round($bthr[$key]);
				$tbon+=round($bins[$key]);
				$tjht+=round($bjht[$key]);
				$tbpjs+=round($bbpjs[$key]);
				$tnet+=round($gnet);
				$tpin+=round($bpinjaman[$key]);
				$tnet2+=round($gnet2);
                */
$SORTAFD[$BYA][$afd][$TPA][$key]['A'] =$_POST['periode'];
$SORTAFD[$BYA][$afd][$TPA][$key]['B'] =$nmkaryawan[$key];
$SORTAFD[$BYA][$afd][$TPA][$key]['C'] =$key;
$SORTAFD[$BYA][$afd][$TPA][$key]['U']=$karjab[$key];
$SORTAFD[$BYA][$afd][$TPA][$key]['D'] =$afd;
$SORTAFD[$BYA][$afd][$TPA][$key]['E'] =$TPA;
$SORTAFD[$BYA][$afd][$TPA][$key]['F'] =$BYA;
$SORTAFD[$BYA][$afd][$TPA][$key]['G'] =$bigaji;
$SORTAFD[$BYA][$afd][$TPA][$key]['H'] =$blembur;
$SORTAFD[$BYA][$afd][$TPA][$key]['T'] =$bpenga;
$SORTAFD[$BYA][$afd][$TPA][$key]['I'] =$bpremi;

$SORTAFD[$BYA][$afd][$TPA][$key]['J'] =$bthr;
$SORTAFD[$BYA][$afd][$TPA][$key]['K'] =$binse;
$SORTAFD[$BYA][$afd][$TPA][$key]['L'] =$tpinj;
$SORTAFD[$BYA][$afd][$TPA][$key]['M'] =$hjht;
$SORTAFD[$BYA][$afd][$TPA][$key]['N'] =$hjk;
$SORTAFD[$BYA][$afd][$TPA][$key]['V'] =$hjp;
$SORTAFD[$BYA][$afd][$TPA][$key]['O'] =$hgaji;
$SORTAFD[$BYA][$afd][$TPA][$key]['P'] =$dbet;
$SORTAFD[$BYA][$afd][$TPA][$key]['Q'] =$dkre;
$SORTAFD[$BYA][$afd][$TPA][$key]['R'] =$dbal;
$SORTAFD[$BYA][$afd][$TPA][$key]['S'] =$nmunit[$key];


             }
			  $no1=1;
			$no2=1;
					$TtA1=0;
					$TtB1=0;
					$TtC1=0;
					$TtD1=0;
					$TtE1=0;
					$TtF1=0;
					$TtG1=0;
					$TtH1=0;
					$TtV1=0;
					$TtI1=0;
					$TtJ1=0;
					$TtK1=0;
					$TtL1=0;
					$TtM1=0;
			 foreach($SORTAFD as $key =>$baris)
             {
				 $tA1=0;
				$tB1=0;
				$tC1=0;
				$tD1=0;
				$tE1=0;
				$tF1=0;
				$tG1=0;
				$tH1=0;
				$tV1=0;
				$tI1=0;
				$tJ1=0;
				/*
				$tJ1=0;
				$tK1=0;
				$tL1=0;
				*/
				 
				 foreach($baris as $key2 =>$baris2)
				{
					
					foreach($baris2 as $key3 =>$baris3)
					{
						$tA=0;
				$tB=0;
				$tC=0;
				$tD=0;
				$tE=0;
				$tF=0;
				$tG=0;
				$tH=0;
				$tV=0;
				$tI=0;
				$tJ=0;

				/*
				$tJ=0;
				$tK=0;
				$tL=0;
				*/
						foreach($baris3 as $key4 =>$baris4)
						{	
						$data.="<tr class=rowcontent >";
				 $data.="<td>".$no1."</td>";
					
					  $data.="<td >".$baris4['A']."</td>";
                    $data.="<td >".$baris4['B']."</td>";
                    $data.="<td >".$baris4['C']."</td>";    
					$data.="<td >".$baris4['U']."</td>";
					$data.="<td >".$baris4['D']."</td>";
					$data.="<td >".$baris4['E']."</td>"; 
					$data.="<td >".$baris4['F']."</td>";
					$data.="<td align=right >".number_format($baris4['G'])."</td>";
					
					 $data.="<td align=right >".number_format($baris4['H'])."</td>";
					 $data.="<td align=right >".number_format($baris4['T'])."</td>";
					 $data.="<td align=right >".number_format($baris4['I'])."</td>";
					 $data.="<td align=right >".number_format($baris4['J'])."</td>";
					 $data.="<td align=right >".number_format($baris4['K'])."</td>";
					
					 $data.="<td align=right >".number_format($baris4['L'])."</td>";
					 $data.="<td align=right >".number_format($baris4['M'])."</td>";
					 $data.="<td align=right >".number_format($baris4['N'])."</td>";
					 $data.="<td align=right >".number_format($baris4['V'])."</td>";
					 $data.="<td align=right >".number_format($baris4['O'])."</td>";
					 /*
					 $data.="<td align=right id='BC".$no."'>".number_format($baris4['P'])."</td>";
					 $data.="<td align=right id='BD".$no."'>".number_format($baris4['Q'])."</td>";
					 $data.="<td align=right id='BE".$no."'>".number_format($baris4['R'])."</td>";
					 */
					 $data.="</tr>";
					 
				$tA+=$baris4['G'];
				$tB+=$baris4['H'];
				$tC+=$baris4['I'];
				$tD+=$baris4['J'];
				$tE+=$baris4['K'];
				$tF+=$baris4['L'];
				$tG+=$baris4['M'];
				$tH+=$baris4['N'];
				$tV+=$baris4['V'];
				$tI+=$baris4['O'];
				$tJ+=$baris4['T'];
				/*
				$tJ+=$baris4['P'];
				$tK+=$baris4['Q'];
				$tL+=$baris4['R'];
				*/
				$no1+=1;
				
						}
					
					$data.="<tr style=\"background-color:yellow\" id='row".$no2."'>";
					$data.="<td  id='unit".$no2."'>".$baris4['D']."  </td>";
					if ($_SESSION['empl']['lokasitugas'] == "CBGM"){
							$data.="<td colspan=5 id='ketA".$no2."'>".$baris4['A'].":".$baris4['E']." ".$baris4['S']."  </td>";
							$data.="<td  id='kat".$no2."'>".$baris4['E']."  </td>";
					}else{
							$data.="<td colspan=5 id='ketA".$no2."'>".$baris4['A'].":".$baris4['E']." ".$baris4['S']." </td>";
							$data.="<td  id='kat".$no2."'>".$baris4['E']."  </td>";
					}
                    
					$data.="<td  id='biaya".$no2."'>".$baris4['F']."  </td>";
                    $data.="<td align=right id='bgaji".$no2."'>".number_format($tA)."</td>
					<td align=right id='blembur".$no2."'>".number_format($tB)."</td>
					<td align=right id='bawas".$no2."'>".number_format($tJ)."</td>
					<td align=right id='bpremi".$no2."'>".number_format($tC)."</td>
					<td align=right id='bthr".$no2."'>".number_format($tD)."</td>
					<td align=right id='bbonus".$no2."'>".number_format($tE)."</td>
					<td align=right id='ppiutang".$no2."'>".number_format($tF)."</td>
					<td align=right id='pjht".$no2."'>".number_format($tG)."</td>
					<td align=right id='pjk".$no2."'>".number_format($tH)."</td>
					<td align=right id='jp".$no2."'>".number_format($tV)."</td>
					<td align=right id='hgaji".$no2."'>".number_format($tI)."</td>
					
                    </tr>"; 
					/*<td align=right id='hdebet".$no."'>".number_format($tJ)."</td>
					<td align=right id='hkredit".$no."'>".number_format($tK)."</td>
					<td align=right id='hbalan".$no."'>".number_format($tL)."</td>
					*/
				$tA1+=$tA;
				$tB1+=$tB;
				$tC1+=$tC;
				$tD1+=$tD;
				$tE1+=$tE;
				$tF1+=$tF;
				$tG1+=$tG;
				$tH1+=$tH;
				$tV1+=$tV;
				$tI1+=$tI;
				$tJ1+=$tJ;
				/*
				$tJ1+=$tJ;
				$tK1+=$tK;
				$tL1+=$tL;
				*/
                    /*
                    $data.="<td id='periode".$no."'>".$baris3['A']."</td>";
                    $data.="<td id='namakaryawan".$no."'>".$baris3['B']."</td>";
                    $data.="<td id='karyawanid".$no."'>".$baris3['C']."</td>";    
					$data.="<td id='subbagian".$no."'>".$baris3['D']."</td>";
					$data.="<td id='tipe".$no."'>".$baris3['E']."</td>"; 
					$data.="<td id='biaya".$no."'>".$baris3['F']."</td>";
					$data.="<td align=right id='AP".$no."'>".round($baris3['G'])."</td>";
					
					 $data.="<td align=right id='AQ".$no."'>".round($baris3['H'])."</td>";
					 $data.="<td align=right id='AR".$no."'>".round($baris3['I'])."</td>";
					 $data.="<td align=right id='AS".$no."'>".round($baris3['J'])."</td>";
					 $data.="<td align=right id='AT".$no."'>".round($baris3['K'])."</td>";
					
					 $data.="<td align=right id='AX".$no."'>".round($baris3['L'])."</td>";
					 $data.="<td align=right id='AY".$no."'>".round($baris3['M'])."</td>";
					 $data.="<td align=right id='AZ".$no."'>".round($baris3['N'])."</td>";
					 $data.="<td align=right id='BB".$no."'>".round($baris3['O'])."</td>";
					 $data.="<td align=right id='BB".$no."'>".round($baris3['P'])."</td>";
					 $data.="<td align=right id='BB".$no."'>".round($baris3['Q'])."</td>";
					 $data.="<td align=right id='BB".$no."'>".round($baris3['R'])."</td>";
					 */
                     	$no2+=1;
					}
					
				}
				$no1++;
					$data.="<tr style=\"background-color:#e5e5e5\" >
                    <td colspan=8>Total ".$baris4['F']."</td>
                    <td align=right>".number_format($tA1)."</td>
					<td align=right>".number_format($tB1)."</td>
					<td align=right>".number_format($tJ1)."</td>
					<td align=right>".number_format($tC1)."</td>
					<td align=right>".number_format($tD1)."</td>
					<td align=right>".number_format($tE1)."</td>
					<td align=right>".number_format($tF1)."</td>
					<td align=right>".number_format($tG1)."</td>
					<td align=right>".number_format($tH1)."</td>
					<td align=right>".number_format($tV1)."</td>
					<td align=right>".number_format($tI1)."</td>
					
                    </tr>"; 
					/*
					<td align=right>".number_format($tJ1)."</td>
					<td align=right>".number_format($tK1)."</td>
					<td align=right>".number_format($tL1)."</td>
					*/
					
					$TtA1+=$tA1;
					$TtB1+=$tB1;
					$TtC1+=$tC1;
					$TtD1+=$tD1;
					$TtE1+=$tE1;
					$TtF1+=$tF1;
					$TtG1+=$tG1;
					$TtH1+=$tH1;
					$TtV1+=$tV1;
					$TtI1+=$tI1;
					$TtJ1+=$tJ1;
					/*
					$TtJ1+=$tJ1;
					$TtK1+=$tK1;
					$TtL1+=$tL1;
					*/
					
			 }
			 
			 $data.="<tr>
                    <td colspan=8>Total Gaji</td>
                    <td align=right>".number_format($TtA1)."</td>
					<td align=right>".number_format($TtB1)."</td>
					<td align=right>".number_format($TtJ1)."</td>
					<td align=right>".number_format($TtC1)."</td>
					<td align=right>".number_format($TtD1)."</td>
					<td align=right>".number_format($TtE1)."</td>
					<td align=right>".number_format($TtF1)."</td>
					<td align=right>".number_format($TtG1)."</td>
					<td align=right>".number_format($TtH1)."</td>
					<td align=right>".number_format($TtV1)."</td>
					<td align=right>".number_format($TtI1)."</td>
					
                    </tr>"; 
					/*
					<td align=right>".number_format($TtJ1)."</td>
					<td align=right>".number_format($TtK1)."</td>
					<td align=right>".number_format($TtL1)."</td>
					*/
			 /*
            $data.="<tr class=rowcontent id='row".$no."'>
                    <td colspan=7>Total</td>
                    <td align=right>".number_format($tgp)."</td>
					<td align=right>".number_format($tlem)."</td>
					<td align=right>".number_format($tpre)."</td>
					<td align=right>".number_format($tthr)."</td>
					<td align=right>".number_format($tbon)."</td>
					<td align=right>".number_format($tjht)."</td>
					<td align=right>".number_format($tbpjs)."</td>
					<td align=right>".number_format($tnet)."</td>
					<td align=right>".number_format($tpin)."</td>
					<td align=right>".number_format($tnet2)."</td>
                    </tr>"; 
			*/
             $data.="</tbody><tfoot></tfoot></table>";
			 
			 echo $data;

			 
}


#----------------------------------------------------------------
?>
