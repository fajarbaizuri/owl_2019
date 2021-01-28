<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;
$namakar=Array();
#cek tutup atau belum periode gaji
$sCekPeriode="select distinct * from ".$dbname.".sdm_5periodegaji where periode='".$param['periodegaji']."' 
              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=1 and jenisgaji='H'";
$qCekPeriode=mysql_query($sCekPeriode) or die(mysql_error($conn));
if(mysql_num_rows($qCekPeriode)>0)
    $aktif2=false;
       else
     $aktif2=true;
  if(!$aktif2)
  {
      exit("Periode gaji sudah ditutup");
  }
  #periksa tutupbuku
       $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and 
             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
       $res=mysql_query($str);
       if(mysql_num_rows($res)>0)
           $aktif=false;
       else
           $aktif=true;
  if(!$aktif)
  {
      exit("Periode akuntansi sudah tutup buku");
  }
  
# Get Period Range
$qPeriod = selectQuery($dbname,'sdm_5periodegaji','tanggalmulai,tanggalsampai',
    "periode='".$param['periodegaji']."' and kodeorg='".
    $_SESSION['empl']['lokasitugas']."' and jenisgaji='H'");
$resPeriod = fetchData($qPeriod);
$tanggal1 = $resPeriod[0]['tanggalmulai'];
$tanggal2 = $resPeriod[0]['tanggalsampai'];

$query1 = selectQuery($dbname,'datakaryawan','karyawanid,tipekaryawan,namakaryawan,subbagian',"tipekaryawan in(2,3,4) and ".
    "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
    "(tanggalkeluar>='".$tanggal2."' or tanggalkeluar='0000-00-00') and alokasi=0 and sistemgaji='Harian' and not idfinger is null".
     " order by subbagian,namakaryawan ");
	 
$absRes = fetchData($query1);


		
# Error empty karyawan
if(empty($absRes)) {
    echo "Error : Belum ada daftar kehadiran karyawan Harian pada periode tersebut";
    exit();
}
else
{
    $id=Array();
    foreach($absRes as $row => $kar)
    {
      $id[$kar['karyawanid']][]=$kar['karyawanid'];
      //$namakar[$kar['karyawanid']]=$kar['namakaryawan'];
	  $namakar[$kar['karyawanid']]=$kar['subbagian']."-".$kar['namakaryawan'];
      $gajiperhari[$kar['karyawanid']]=0;   #default gaji KHT=0
      if($kar['tipekaryawan']==3){
         $tipekaryawan[$kar['karyawanid']]='KHT';
      }else if($kar['tipekaryawan']==4){
         $tipekaryawan[$kar['karyawanid']]='BHL'; 
      }else{
         $tipekaryawan[$kar['karyawanid']]='Kontrak'; 
	  }
    }  
}

# ambil gaji pokok per hari untuk KHT


    $strgjh = "select a.karyawanid,sum(jumlah)/25 as gjperhari from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan in(2,3) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.idkomponen in(1,2,31) and sistemgaji='Harian'
               group by a.karyawanid";
			   
	//exit("Error:$strgjh");		   

    $resgjh = fetchData($strgjh);
    foreach($resgjh as $idx => $val)
        {
          $gajiperhari[$val['karyawanid']]=$val['gjperhari'];
        }

 #ambil jumlah hk tidak dibayar untuk KHT dan total tidak dibayar
     $strgjh = "select  count(*) as jlh,b.karyawanid from ".$dbname.".sdm_hktdkdibayar_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian'
               group by a.karyawanid";
	//exit("Error:$strgjh");			   
    $tdkdibayar=Array();
    $resgjh = fetchData($strgjh);
    foreach($resgjh as $idx => $val)
        {
          $tdkdibayar[$val['karyawanid']]=$gajiperhari[$val['karyawanid']]*$val['jlh'];#jumlah tidak dibayar
        //koreksi untuk memindahkan potongan hk dari gaji pokok ke komponen potongan hk
        //seperti yang diterangkan pada escape dibawah  
        $readyData[] = array(
        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
        'periodegaji'=>$param['periodegaji'],
        'karyawanid'=>$val['karyawanid'],
        'idkomponen'=>37,//potongan hk
        'jumlah'=>$tdkdibayar[$val['karyawanid']],
        'pengali'=>1);      
        }
       // echo "<pre>";
        //print_r($gajiperhari);
       // echo"</pre>";
       // exit();
     //Jika periode gaji lebih dari sebulan, maka kelebihannya ditambah sesuai dengan gaji  harian            
     //==========hitung selisih hari
        $t1=$tanggal1." 00:00:00";//awal
        $t2=$tanggal2." 23:59:59";//sampai
        $endd = strtotime($t2);
        $startd = strtotime($t1);
        $jumlahh= round(abs($endd-$startd)/60/60/24);
                //ambil jumlah hari periode gaji ( jumlah hari satu bulan)
                $pengurang=date('t',$startd);        
     //=======================================================================   
        
#2 Get Jamsostek porsi==========================
#ambil semua komponen dari gajipokok khusus KHT dan Kontrak Harian=====================
#6.3.6 Get kontanan
$kontanan=Array();
$hasil=Array();
		 $querykontanan="select sum(a.upahkerja) as upahkerja,a.karyawanid,sum(a.rpkgkontanan) as kontanan
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' and a.rpkgkontanan  > 0
               and sistemgaji='Harian' group by a.karyawanid";
        $premReskontanan = fetchData($querykontanan); 
         foreach($premReskontanan as $idx => $val)
        {
            
			if($val['kontanan']>0)
             { 
                  if(isset ($kontanan[$val['karyawanid']])){
					$kontanan[$val['karyawanid']]+=$val['upahkerja'];
				  }else{
				   $kontanan[$val['karyawanid']]=$val['upahkerja'];
				   }      
				   
                
             }
        }       
		
    $str1 = "select a.*,b.namakaryawan from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan in(2,3) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0    
               and sistemgaji='Harian'";
    $res1 = fetchData($str1);
    $query6 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karyawan'");
    $jmsRes = fetchData($query6);
    $persenJms = $jmsRes[0]['value']/100;
    $tjms=Array(); 
        foreach($res1 as $idx => $val)
        {
          #KHT dan Kontrak Harian
          if($id[$val['karyawanid']][0]==$val['karyawanid'] and ($tipekaryawan[$val['karyawanid']]=='KHT' or $tipekaryawan[$val['karyawanid']]=='Kontrak')){
            
            // #kurangkan pemotongan HK tidak dibayar
            // if (array_key_exists($val['karyawanid'], $tdkdibayar)){ 
            //     if($val['idkomponen']=='1')#kurangkan hanya pada gaji pokok
            //     $val['jumlah']=$val['jumlah']-$tdkdibayar[$val['karyawanid']];#pengurangan HK tidak dibayar
            // }
            //Khusus KHT dan Kontrak harian tidak lagi dipotong pada gaji pokok, tetapi ditambah pada potongan HK diatas 
             //==============================================================================================================================
                //filter jumlah hari     
				/*
                if($val['idkomponen']==1 and $jumlahh>$pengurang)
                   { 
                    $selisih=$jumlahh-$pengurang;
                    $pengurangkelebihanminggu=floor($selisih/7);
                    $bersih=$selisih-$pengurangkelebihanminggu;
                     $val['jumlah']+=($gajiperhari[$val['karyawanid']]*$bersih);//nilai gajipokok diubah ditambah kelebihan hari
                   } 
                if($val['idkomponen']==1 and $jumlahh<$pengurang)
                   { 
                    $selisih=$pengurang-$jumlahh;
                    $pengurangkelebihanminggu=floor($selisih/7);
                    $bersih=$selisih-$pengurangkelebihanminggu;
                     $val['jumlah']-=($gajiperhari[$val['karyawanid']]*$bersih);//nilai gajipokok diubah dikurang kekurangan hari                   
                   }
				*/
				if($val['idkomponen']=="1"){
					//$val['jumlah']+=$kontanan[$val['karyawanid']];
					$hasil=$val['jumlah']+$kontanan[$val['karyawanid']];
				}
                //============================== 
           //exit("Error:$bersih");
             #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$val['karyawanid'],
                'idkomponen'=>$val['idkomponen'],
                'jumlah'=>$hasil,
                'pengali'=>1);
             if($val['idkomponen']==1 or $val['idkomponen']==2 or $val['idkomponen']==30 or $val['idkomponen']==31)
             { #ambil,
               #tunjangan jabatan
               #tunjangan masakerja
               #tunjangan Provesi
               #gaji pokok
                $tjms[$val['karyawanid']]+=$val['jumlah']; 
             }
          }
         else {
         #BHL
            //diabaikan yang dari gaji pokok 
           }  
      }
      
    foreach($tjms as $key=>$nilai){
             #add jamsostek to ready data====================================
           if($tipekaryawan[$key]=='KHT'){ 
             $readyData[] = array(
            'kodeorg'=>$_SESSION['empl']['lokasitugas'],
            'periodegaji'=>$param['periodegaji'],
            'karyawanid'=>$key,
            'idkomponen'=>3,   
            'jumlah'=>($nilai* $persenJms),//'jumlah'=>(($nilai+$tdkdibayar[$key])* $persenJms),
            'pengali'=>1);  
           }
    }      
#3. Get Lembur Data
    $where2 = " a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and (tanggal>='".
        $tanggal1."' and tanggal<='".$tanggal2."')";
      $query2="select a.karyawanid,sum(a.uangkelebihanjam) as lembur from ".$dbname.".sdm_lemburdt a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Harian'                  
               and ".$where2." group by a.karyawanid";
    $lbrRes = fetchData($query2); 
    foreach($lbrRes as $idx=>$row) {  
          if(isset ($id[$row['karyawanid']]))
          {
                $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>33,   
                'jumlah'=>$row['lembur'],
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }

#4. Get Potongan Data============================================================
    $where3 = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".
        $param['periodegaji']."'";
    //$query3 = selectQuery($dbname,'sdm_potongandt','nik,sum(jumlahpotongan) as potongan',$where3)." group by nik";
    $query3="select a.nik as karyawanid,sum(jumlahpotongan) as potongan from ".$dbname.".sdm_potongandt a left join 
              ".$dbname.".datakaryawan b on a.nik=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Harian'                   
               and ".$where3." group by a.nik";
    $potRes = fetchData($query3);
    foreach($potRes as $idx=>$row) {  
          if(isset ($id[$row['karyawanid']]))
          {
                $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>18,   
                'jumlah'=>$row['potongan'],
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }   

#5. Get Angsuran Data==========================================================
    $where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
    //$query4 = selectQuery($dbname,'sdm_angsuran','karyawanid,bulanan,jenis',$where4)." group by karyawanid";
    $query4="select a.karyawanid,a.bulanan,a.jenis from ".$dbname.".sdm_angsuran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.active=1    
               and sistemgaji='Harian'                    
               and ".$where4;
    $angRes = fetchData($query4);
    foreach($angRes as $idx=>$row) { 
          if($id[$row['karyawanid']][0]==$row['karyawanid'])
          {
           #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>$row['jenis'],
                'jumlah'=>$row['bulanan'],
                'pengali'=>1);
          }
    }
#6 Premi dan penalty =======================================================================
    #6.0 periksa posting transaksi
    #posting perawatan
    $stru1="select distinct(tanggal) from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian' order by tanggal";
			   
    $resu1 = mysql_query($stru1); 
    #posting panen
    $stru2="select distinct(tanggal) from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian' order by tanggal";
   $resu2 = mysql_query($stru2); 
   #posting traksi
   $stru3="select distinct(tanggal)
           from ".$dbname.".vhc_runhk_vw a left join 
          ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
           where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
           and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
           and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
           and posting=0 and sistemgaji='Harian' order by tanggal";
   $resu3 = mysql_query($stru3);
   //if(mysql_num_rows($resu1)>0 or mysql_num_rows($resu2)>0 or mysql_num_rows($resu3)>0)
	   if(mysql_num_rows($resu1)<0 or mysql_num_rows($resu2)<0 or mysql_num_rows($resu3)<0)
   {
       echo"Masih ada data yang belum di posting:";
       echo"<table class=sortable border=0 cellspacing=1>
            <thead><tr class=rowheader>
            <td>".$_SESSION['lang']['jenis']."</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            </tr></thead><tbody>";
       while($bar=mysql_fetch_object($resu1))
       {
           echo"<tr class=rowcontent><td>Perawatan Kebun</td><td>".tanggalnormal($bar->tanggal)."</td></tr>";
       }
       while($bar=mysql_fetch_object($resu2))
       {
           echo"<tr class=rowcontent><td>Panen</td><td>".tanggalnormal($bar->tanggal)."</td></tr>";
       }
       while($bar=mysql_fetch_object($resu3))
       {
           echo"<tr class=rowcontent><td>Traksi Pekerjaan</td><td>".tanggalnormal($bar->tanggal)."</td></tr>";
       }
       echo "</tbody><tfoot></tfoot></table>";
      exit();//keluar dari proses
   }
   

    #6.3.1 Get Premi Kegiatan Perawatan dan gaji pokok BHL
        $premi=Array();
        $penalty=Array();
        $gapokbhl=Array();
		
        $query5="select distinct (tanggal),a.umr as gaji,a.karyawanid,a.insentif as premi from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian' ";
			  /*$query5="select distinct (tanggal),a.umr as gaji,a.karyawanid,a.insentif as premi from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian' a.tanggal,a.karyawanid";*/ //default
		
	  
		//exit("Error:$query5");
        
        $premRes = fetchData($query5);
		
		//echo "<pre>".print_r($premRes)."</pre>";
		 
        foreach($premRes as $idx => $val)
        {
            if($val['premi']>0)
			$premi[$val['karyawanid']]+=$val['premi'];//default
           // $premi[$val['karyawanid']]=$val['premi'];//default
          #gapok KHL
          if($tipekaryawan[$val['karyawanid']]=='BHL')
          {   
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['gaji'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['gaji'];  
          }
        }  
    #6.3.2 Get Premi Kegiatan Panen    dan gaji pokok BHL
         $query6="select sum(a.upahkerja) as upahkerja,a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty+a.dendabasis) as penalty ,sum(a.rpkgkontanan) as kontanan
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian' group by a.karyawanid";
        $premRes1 = fetchData($query6); 
         foreach($premRes1 as $idx => $val)
        {
             if($val['premi']>0)
             { 
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi']; 
             }
             if($val['penalty']>0)    
                 $penalty[$val['karyawanid']]=$val['penalty'];
           #gapok KHL
          if($tipekaryawan[$val['karyawanid']]=='BHL')
          {   
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['upahkerja'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['upahkerja'];  
          }  
			
        }         
		 


		 
		
     #6.3.3 Get Premi Transport dan gaji pokok BHL
        $query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty 
               from ".$dbname.".vhc_runhk_vw a left join 
              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
               where b.tipekaryawan in(2,3,4) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Harian' group by a.idkaryawan";
        $premRes2 = fetchData($query7); 
         foreach($premRes2 as $idx => $val)
        {
             if($val['premi']>0)
             {   
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi'];
             }
              if($val['penalty']>0)
             {              
                 if(isset ($penalty[$val['karyawanid']]))
                     $penalty[$val['karyawanid']]+=$val['penalty'];
                 else
                     $penalty[$val['karyawanid']]=$val['penalty'];   
             }
           #gapok KHL
          /*   BHL sudah dapat gaji pokok dari absensi, karena vhc_runhk sudah otomatis masuk ke sdm_absensi
          if($tipekaryawan[$val['karyawanid']]=='BHL')
          {   
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['upah'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['upah'];  
          } 
           * 
           */
        }  
		
		
        #gapok BHL dari absensi===================================
        $strup="select a.karyawanid,(b.jumlah/25) as upahabsen FROM ".$dbname.".sdm_absensidt_vw a 
                left join ".$dbname.".sdm_5gajipokok b on a.karyawanid=b.karyawanid and nilaihk=1
                and b.idkomponen=1 
               where b.tahun=".substr($tanggal1,0,4)." and substr(a.kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' 
               and a.tipekaryawan=4";
        $resup = fetchData($strup);    
        foreach($resup as $idx => $val)
        {        
             // if(empty ($gapokbhl[$val['karyawanid']]))
                  //$gapokbhl[$val['karyawanid']]=$val['upahabsen'];
              //else
                  $gapokbhl[$val['karyawanid']]+=$val['upahabsen']; 
        }
       #add gapok BHL to ready data
       foreach($gapokbhl as $key=>$val){
             if($val>0) {
                 $readyData[] = array(
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$key,
                    'idkomponen'=>1,#kode komponen gapok
                    'jumlah'=>$val,
                    'pengali'=>1);
               }           
       }
      
        foreach($premi as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {
                 $readyData[] = array(
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$idx,
                    'idkomponen'=>32,
                    'jumlah'=>$row,
                    'pengali'=>1);
                 }
             }    
			 
         foreach($penalty as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {             
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$idx,
                'idkomponen'=>34,
                'jumlah'=>$row,
                'pengali'=>1);
             }
             } 
			  
		 foreach($kontanan as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {             
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$idx,
                'idkomponen'=>43,
                'jumlah'=>$row,
                'pengali'=>1);
             }
             } 
 
 //calculate to component
       $strx="select id as komponen, case plus when 0 then -1 else plus end as pengali,name as nakomp 
              FROM ".$dbname.".sdm_ho_component";
       $comRes = fetchData($strx); 
       $comp=Array();
       $nakomp=Array();
       foreach($comRes as $idx=>$row){
          $comp[$row['komponen']]=$row['pengali'];
          $nakomp[$row['komponen']]=$row['nakomp'];
       }
       
   //=tampilan  ============================
           $listbutton="<button class=mybuttton name=postBtn id=postBtn onclick=post()>Proses</button>"; 
           $list0 ="<table class=sortable border=0 cellspacing=1>
                     <thead>
                     <tr class=rowheader>";
            $list0 .= "<td>".$_SESSION['lang']['nomor']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['periodegaji']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['karyawanid']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['tipe']."</td>";
            $list0.= "<td>".$_SESSION['lang']['jumlah']."</td></tr></thead><tbody>";
            
//periksa gaji minus
    $negatif=false; 
    $list1='';
    $listx = "Masih ada gaji dibawah 0:";
    $list2='';
    $list3='';
    $no=0;
		/*
    //ambil premi pengawas di sdm_gaji hanya untuk pemeriksaan minus
    $strsl="select karyawanid,jumlah from ".$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'
         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=16"; 
    $slRes = fetchData($strsl); 
   foreach($slRes as $key=>$val)
   {
       $premPengawas[$val['karyawanid']]=$val['jumlah'];
   }     
     */
        foreach($id as $key=>$val){
           $sisa[$val[0]]=0;
           foreach($readyData as $dat=>$bar){
              if($val[0]==$bar['karyawanid'])
              {
                  $sisa[$val[0]]+=$bar['jumlah']*$comp[$bar['idkomponen']]; 
              } 
              else
               continue;
           }
		      /*
           $sisa[$val[0]]+=$premPengawas[$val[0]];
		   */
           if($sisa[$val[0]]<0)
           {
                $list1 .="<tr class=rowcontent>";
                $list1 .= "<td>-</td>";
                $list1 .= "<td>".$param['periodegaji']."</td>";
                $list1 .= "<td>".$namakar[$val[0]]."</td>";
                $list1 .= "<td>".$tipekaryawan[$val[0]]."</td>";
                $list1 .= "<td>".number_format($sisa[$val[0]],0,',','.')."</td></tr>";                
              $negatif=false;
           } 
           else
           {
               $no+=1; 
                $list2 .="<tr class=rowcontent>";
                $list2 .= "<td>".$no."</td>";
                $list2 .= "<td>".$param['periodegaji']."</td>";
                $list2 .= "<td>".$namakar[$val[0]]."</td>";
                $list2 .= "<td>".$tipekaryawan[$val[0]]."</td>";
                $list2 .= "<td align=right>".number_format($sisa[$val[0]],0,',','.')."</td></tr>";  
           }    
       }
     $list3="</tbody><table>";     
switch($proses) {
    case 'list':
         if($negatif)
             echo $listx.$list0.$list1.$list3;
         else
             echo $listbutton.$list0.$list2.$list3;
         break;
    case 'post':
        #delete first
        # Insert All ready data
        $insError = "";
		
		  
		
        foreach($readyData as $row) {
            if($row['jumlah']==0 or $row['jumlah']=='')
            {
                continue;
            }
            else{
            $queryIns = insertQuery($dbname,'sdm_gaji',$row);
            if(!mysql_query($queryIns)) {
                $queryUpd = updateQuery($dbname,'sdm_gaji',$row,
                    "kodeorg='".$row['kodeorg'].
                    "' and periodegaji='".$row['periodegaji'].
                    "' and karyawanid='".$row['karyawanid'].
                    "' and idkomponen=".$row['idkomponen']);
                $tmpErr = mysql_error();
                if(!mysql_query($queryUpd)) {
                    echo "DB Insert Error :".$tmpErr."\n";
                    print_r($row);
                    echo "DB Update Error :".mysql_error()."\n";
                }
            }
          } 
        }
		
        break;
    default:
        break;
}      
       
/*
echo"<pre>";
print_r($readyData);
echo"</pre>";
*/