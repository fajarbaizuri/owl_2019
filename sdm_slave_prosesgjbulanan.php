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
              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=1 and jenisgaji='B'";
$qCekPeriode=mysql_query($sCekPeriode) or die(mysql_error($conn));
if(mysql_num_rows($qCekPeriode)>0)
    $aktif2=false;
       else
     $aktif2=true;
  if(!$aktif2)
  {
      exit("Periode gaji sudah ditutup");
  }
#periksa apakah sudah tutup buku

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
    $_SESSION['empl']['lokasitugas']."' and jenisgaji='B'");
$resPeriod = fetchData($qPeriod);
$tanggal1 = $resPeriod[0]['tanggalmulai'];
$tanggal2 = $resPeriod[0]['tanggalsampai'];

#2. Get Karyawan bulanan yang penggajian=bulanan dan alokasi=0
 if($_SESSION['empl']['lokasitugas']=="CBGM" ){
$query1 = selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian',"tipekaryawan in(1,2) and ".
    "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
    "(tanggalkeluar>='".$tanggal2."' or tanggalkeluar='0000-00-00') and alokasi=0 and sistemgaji='Bulanan'".
     " order by subbagian,namakaryawan ");
	 }else{
$query1 = selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian',"tipekaryawan in(1,2) and ".
    "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
    "(tanggalkeluar>='".$tanggal2."' or tanggalkeluar='0000-00-00') and alokasi=0 and sistemgaji='Bulanan' and not idfinger is null".
     " order by subbagian,namakaryawan ");
}	 
$absRes = fetchData($query1);
# Error empty karyawan
if(empty($absRes)) {
    echo "Error : Belum ada daftar kehadiran karyawan Bulanan pada periode tersebut";
    exit();
}
else
{
    $id=Array();
    foreach($absRes as $row => $kar)
    {
      $id[$kar['karyawanid']][]=$kar['karyawanid'];
      $namakar[$kar['karyawanid']]=$kar['subbagian']."-".$kar['namakaryawan'];
	  
    }  
}
#1ambil semua komponen dari gajipokok=====================
    $str1 = "select a.*,b.namakaryawan,b.tipekaryawan,b.jht,b.jk,b.jp from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'";
    $res1 = fetchData($str1);
#2 Get Jamsostek porsi==========================
    $query6 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karyawan'");
	$query61 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karyawanbpjs'");
	$query611 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karpensiun'");
    $jmsRes = fetchData($query6);
	$jmsRes1 = fetchData($query61);
	$jmsRes2 = fetchData($query611);
    $persenJms = $jmsRes[0]['value']/100;
	$persenBPJS= $jmsRes1[0]['value']/100;
	$persenPENSIUN= $jmsRes2[0]['value']/100;
        $tjms=Array();   
        $tipekaryawan=Array();
        foreach($res1 as $idx => $val)
        {
          if($id[$val['karyawanid']][0]==$val['karyawanid'])
          {
              if($val['tipekaryawan']=='2')
                 $tipekaryawan[$val['karyawanid']]='Kontrak';
               else
                 $tipekaryawan[$val['karyawanid']]='KBL';
               
             #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$val['karyawanid'],
                'idkomponen'=>$val['idkomponen'],
                'jumlah'=>$val['jumlah'],
                'pengali'=>1);
             //if($val['idkomponen']==1 or $val['idkomponen']==2 or $val['idkomponen']==30 or $val['idkomponen']==31 or $val['idkomponen']==14)
			if($val['idkomponen']==1 )
             { #ambil,
               #tunjangan jabatan
               #tunjangan masakerja
               #tunjangan Provesi
               #gaji pokok
				if($val['jht']==1){
					$tjms[$val['karyawanid']]+=$val['jumlah']; 
				}else{
					$tjms[$val['karyawanid']]+=0; 
				}
				if($val['jk']==1){
					$tbpjs[$val['karyawanid']]+=$val['jumlah']; 
				}else{
					$tbpjs[$val['karyawanid']]+=0; 
				}
				if($val['jp']==1){
					$tpensiun[$val['karyawanid']]+=$val['jumlah']; 
				}else{
					$tpensiun[$val['karyawanid']]+=0; 
				}
					
                
				
				
             }
          }
        }
        
        foreach($tjms as $key=>$nilai){
                 #add jamsostek to ready data====================================
            if($tipekaryawan[$key]=='KBL'){
                 $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$key,
                'idkomponen'=>3,   
                'jumlah'=>($nilai* $persenJms),
                'pengali'=>1);  
            }
        }
		
		 foreach($tbpjs as $key=>$nilai){
                 #add bpjs to ready data====================================
            if($tipekaryawan[$key]=='KBL'){
                 $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$key,
                'idkomponen'=>39,   
                'jumlah'=>($nilai* $persenBPJS),
                'pengali'=>1);  
            }
        }
		
		foreach($tpensiun as $key=>$nilai){
                 #add pensiun to ready data====================================
            if($tipekaryawan[$key]=='KBL'){
                 $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$key,
                'idkomponen'=>41,   
                'jumlah'=>($nilai* $persenPENSIUN),
                'pengali'=>1);  
            }
        }
		
        
#3. Get Lembur Data
    $where2 = " a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and (tanggal>='".
        $tanggal1."' and tanggal<='".$tanggal2."')";
      $query2="select a.karyawanid,sum(a.uangkelebihanjam) as lembur from ".$dbname.".sdm_lemburdt a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'                  
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
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'                   
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
	
#5. Get Premi Pengawasan============================================================
    
    $queryA="select a.*,b.namakaryawan,b.tipekaryawan from ".$dbname.".sdm_gaji a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.idkomponen in ('32','16') and  a.periodegaji='".substr($tanggal1,0,7)."' and b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'";

    $potResA = fetchData($queryA);
    foreach($potResA as $idx=>$row) {  
          if(isset ($id[$row['karyawanid']]))
          {
                $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>16,   
                'jumlah'=>$row['jumlah'],
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }   	
	
	
 /*
#51. Get Gol=BHL & tipe=khl & penggajian bulanan==========================================================
	
    $str11 = "select a.karyawanid,SUM(CASE a.absensi WHEN  'H' THEN 1  ELSE 0  END) AS totalhk,b.namakaryawan,b.tipekaryawan 
	from ".$dbname.".sdm_absensidt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid  where (a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' )  and b.tipekaryawan ='1' and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'  and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0 and b.sistemgaji='Bulanan' and b.kodegolongan='BHL'  Group by a.karyawanid" ;
			   			   
    $res11 = fetchData($str11);    
	$query611 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='hk'");
	$hkRes = fetchData($query611);
    $HKrupiah = $hkRes[0]['value'];
        $thk=Array();   
        foreach($res11 as $idx => $val)
        {
          if($id[$val['karyawanid']][0]==$val['karyawanid'])
          {
                $thk[$val['karyawanid']]+=$val['totalhk']; 
          }
        }
		
 

   foreach($thk as $key=>$nilai){
            
                 $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$key,
                'idkomponen'=>1,   
                'jumlah'=>($nilai* $HKrupiah),
                'pengali'=>1);  
            
        }
	
#5. Get Angsuran Data==========================================================
    $where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
    //$query4 = selectQuery($dbname,'sdm_angsuran','karyawanid,bulanan,jenis',$where4)." group by karyawanid";
    $query4="select a.karyawanid,a.bulanan,a.jenis from ".$dbname.".sdm_angsuran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.active=1                      
               and sistemgaji='Bulanan'                    
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
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'    
               and sistemgaji='Bulanan' order by tanggal";
    $resu1 = mysql_query($stru1); 
    #posting panen
    $stru2="select distinct(tanggal) from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan' order by tanggal";
   $resu2 = mysql_query($stru2); 
 
   #posting traksi
   $stru3="select distinct(tanggal)
           from ".$dbname.".vhc_runhk_vw a left join 
          ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
           where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
           and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
           and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
           and posting=0 and sistemgaji='Bulanan' order by tanggal";
   $resu3 = mysql_query($stru3);
   if(mysql_num_rows($resu1)>0 or mysql_num_rows($resu2)>0 or mysql_num_rows($resu3)>0)
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


    #6.3.1 Get Premi Kegiatan Perawatan
        $premi=Array();
        $penalty=Array();
        $query5="select a.karyawanid,sum(a.insentif) as premi from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'                    
               group by a.karyawanid";
        $premRes = fetchData($query5);  
        foreach($premRes as $idx => $val)
        {
          if($val['premi']>0)
            $premi[$val['karyawanid']]=$val['premi'];
        }  
		  
    #6.3.2 Get Premi Kegiatan Panen    
         $query6="select a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty 
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'                    
               group by a.karyawanid";
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
        }         
		
     #6.3.3 Get Premi Transport
        $query7="select a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty 
               from ".$dbname.".vhc_runhk_vw a left join 
              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
               where b.tipekaryawan in(1,2) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'                    
               group by a.idkaryawan";
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
			  */ 
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
            $list0.= "<td>".$_SESSION['lang']['jumlah']."</td></tr></thead><tbody>";
            
//periksa gaji minus
    $negatif=false; 
    $list1='';
     $listx = "Masih ada gaji dibawah 0:";    
    $list2='';
    $list3='';
    $no=0;
	/*
    //ambil premi pengawas di sdm_gaji
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
              continue;
           }
		   /*
           $sisa[$val[0]]+=$premPengawas[$val[0]]; //ditambahkan pada tampilan hanya sekali
           */
           if($sisa[$val[0]]<0)
           {
                $list1 .="<tr class=rowcontent>";
                $list1 .= "<td>-</td>";
                $list1 .= "<td>".$param['periodegaji']."</td>";
                $list1 .= "<td>".$namakar[$val[0]]."</td>";
                $list1 .= "<td>".number_format($sisa[$val[0]],0,',','.')."</td></tr>";                
                $negatif=true;
           
                
           } 
           else
           {
               $no+=1; 
                $list2 .="<tr class=rowcontent>";
                $list2 .= "<td>".$no."</td>";
                $list2 .= "<td>".$param['periodegaji']."</td>";
                $list2 .= "<td>".$namakar[$val[0]]."</td>";
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