<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$tanggal=$param['periode']."-28";


#parameter
#periode
#kodevhc
#jumlah
#jenis (ALK_BY_WS atau ALK_KERJA_AB)


if($param['jenis']=='BYWS')
   prosesByBengkel();
else 
  prosesAlokasi();  
    
function prosesByBengkel(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
    
    #output pada jurnal kolom noreferensi ALK_BY_WS  
    $group='WS2';
    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk WS2");
    else
    {
        $akundebet='';
        $akunkredit='';
        $bar=mysql_fetch_object($res);
        $akundebet=$bar->noakundebet;
        $akunkredit=$bar->noakunkredit;
    }

       #proses data
        $kodeJurnal = $group;
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter']+1,3);

        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
        #======================== /Nomor Jurnal ============================
        # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tanggal,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$param['jumlah'],
                    'totalkredit'=>-1*$param['jumlah'],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_BY_WS',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1'
                );
                # Data Detail
                $noUrut = 1;

                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akundebet,
                    'keterangan'=>'Biaya Bengkel/Reprasi '.$param['kodevhc'],
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_BY_WS',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>''
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunkredit,
                    'keterangan'=> 'Alokasi biaya bengkel ke '.$param['kodevhc'],
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_BY_WS',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>''
                );
                $noUrut++; 
 /*                
            #periksa apakah sudah pernah diproses dengan karyawan yang sama
            $str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
                  like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
                  and noakun='".$akundebet."' and kodevhc='".$param['kodevhc']."'";
            if(mysql_num_rows(mysql_query($str))>0)
                exit("Error: Data sudah pernah di proses");
 */       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header WS Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail WS Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                            "' and kodekelompok='".$kodeJurnal."'");
                        if(!mysql_query($updJurnal)) {
                            echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                            # Rollback if Update Failed
                            $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header Error : ".mysql_error()."\n";
                                exit;
                            }
                            exit;
                        } else {
                        }
                    } else {
                        echo $detailErr;
                        # Rollback, Delete Header
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".mysql_error();
                            exit;
                        }
                    }
                } else {
                    echo $headErr;
                    exit;
                }                          
}
function prosesAlokasi(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname; 

    #1 ambil periode akuntansi
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          kodeorg ='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0";
    $tgmulai='';
    $tgsampai='';
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
    {
        exit("Error: Tidak ada periode akuntansi untuk induk ".$_SESSION['empl']['lokasitugas']);
    }
    while($bar=mysql_fetch_object($res))
    {
        $tgsampai   = $bar->tanggalsampai;
        $tgmulai    = $bar->tanggalmulai;
    }
    if($tgmulai=='' || $tgsampai=='')
    exit("Error: Periode akuntasi tidak terdaftar");
    
    #2 output pada jurnal kolom noreferensi ALK_KERJA_AB  
      
      $group='VHC1';
      #ambil akun alokasi
          $str="select noakundebet from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
      $res=mysql_query($str);
      if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk VHC1");
      else
      {
          $bar=mysql_fetch_object($res);
          $akunalok=$bar->noakundebet;
      }  
      #3 ambil semua lokasi kegiatan
      $str="select sum(a.jumlah) as jlh,a.alokasibiaya,b.noakun from ".$dbname.".vhc_rundt_vw a
            left join ".$dbname.".vhc_kegiatan b on a.jenispekerjaan=b.kodekegiatan
            where kodevhc='".$param['kodevhc']."'
            and tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' and alokasibiaya!='' 
            and jenispekerjaan!=''    
            group by jenispekerjaan,noakun";
      $res=mysql_query($str);
      $lokasi=Array();
      $biaya=Array();
      $jam  =Array();
      $akun =Array();
      $ttl=0;
      while($bar=mysql_fetch_object($res))
      {
          $lokasi[]=$bar->alokasibiaya;
          $akun[]  =$bar->noakun;
          $jam[] =$bar->jlh;
          $kegiatan[]=$bar->noakun."01";
      } 
      foreach ($jam as $key=>$val)
      {
        $biaya[$key] =$val*$param['jumlah']; 
      } 
     #ambil akun intraco dan interco milik sendiri
     $str="select akunhutang,jenis from ".$dbname.".keu_5caco where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
     $res=mysql_query($str);
     $intraco='';
     $interco='';
     while($bar=mysql_fetch_object($res)){
         if($bar->jenis=='intra')
            $intraco=$bar->akunhutang;
         else
            $interco=$bar->akunhutang; 
     }
     
     foreach($biaya as $key=>$nilai){    
       #periksa unit 
         $dataRes['header']=Array();
         $dataRes['detail']=Array();
         $intern=true;
         
         $pengguna=substr($lokasi[$key],0,4);
         #++++++++++++++++++++++++++++++++++++++
         $akunpekerjaan=$akun[$key];
         #++++++++++++++++++++++++++++++++++++++++
         $ptpengguna='';
         $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
         $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
         {
             $ptpengguna=$bar->induk;
         }

         $ptGudang='';
         $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
         $res=mysql_query($str);
         while($bar=mysql_fetch_object($res))
         {
             $ptGudang=$bar->induk;
         }
         #jika pt tidak sama maka pakai akun interco
         $akunpengguna='';
         if($ptGudang !=$ptpengguna)
         {
             #ambil akun interco
             $intern=false;
             $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$pengguna."' and jenis='inter'";
                $res=mysql_query($str);
                $akunpengguna='';
                while($bar=mysql_fetch_object($res))
                {
                    $akunpengguna=$bar->akunpiutang;
                }
            $akunsendiri=$interco;    
            if($akunpengguna=='')
               exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna); 
         }
         else if($pengguna!=$_SESSION['empl']['lokasitugas']){ #jika satu pt beda kebun
              #ambil akun intraco
             $intern=false;
             $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$pengguna."' and jenis='intra'";
                $res=mysql_query($str);
                $akunpengguna='';
                while($bar=mysql_fetch_object($res))
                {
                    $akunpengguna=$bar->akunpiutang;
                } 
             $akunsendiri=$intraco;    
             if($akunpengguna=='')
                exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna);    
         }
         else
         {
             $intern=true;
         }   
          
        if($intern)
         {  
           #proses data
            $kodeJurnal = $group;
            #======================== Nomor Jurnal =============================
            # Get Journal Counter
            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter']+1,3);

            # Transform No Jurnal dari No Transaksi
            $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
            #======================== /Nomor Jurnal ============================
            # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tanggal,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$biaya[$key],
                    'totalkredit'=>-1*$biaya[$key],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1'
                    );
            # Data Detail
                $noUrut = 1;
                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunpekerjaan,
                    'keterangan'=> $param['periode'].':Biaya Kendaraan '.$param['kodevhc'],
                    'jumlah'=>$biaya[$key],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>$kegiatan[$key],
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>0,
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>$lokasi[$key]
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunalok,
                    'keterangan'=>$param['periode'].':Alokasi biaya kend'.$param['kodevhc'],
                    'jumlah'=>-1*$biaya[$key],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>$kegiatan[$key],
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'0',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>$lokasi[$key]
                );
                $noUrut++; 
                 $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header Intern Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Intern Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                            "' and kodekelompok='".$kodeJurnal."'");
                        if(!mysql_query($updJurnal)) {
                            echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                            # Rollback if Update Failed
                            $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header Error : ".mysql_error()."\n";
                                exit;
                            }
                            exit;
                        } else {
                        }
                    } else {
                        echo $detailErr;
                        # Rollback, Delete Header
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".mysql_error();
                            exit;
                        }
                    }
                } else {
                    echo $headErr;
                    exit;
                }                 
         }
         else {
           # Data Detail
            $noUrut = 1;
            #proses data
            $kodeJurnal = $group;
            #======================== Nomor Jurnal =============================
            # Get Journal Counter
            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter']+1,3);

            # Transform No Jurnal dari No Transaksi
            $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
      
            #======================== /Nomor Jurnal ============================
            # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tanggal,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$biaya[$key],
                    'totalkredit'=>-1*$biaya[$key],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1'
                    );
                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunpengguna,
                    'keterangan'=>$param['periode'].':Biaya Kendaraan '.$param['kodevhc'],
                    'jumlah'=>$biaya[$key],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>$kegiatan[$key],
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'0',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>''
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunalok,
                    'keterangan'=>$param['periode'].':Alokasi biaya kend'.$param['kodevhc'],
                    'jumlah'=>-1*$biaya[$key],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>$kegiatan[$key],
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'0',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>''
                );
  
               $noUrut++;
               $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header Ex.Self Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Ex.Self Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                            "' and kodekelompok='".$kodeJurnal."'");
                        if(!mysql_query($updJurnal)) {
                            echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                            # Rollback if Update Failed
                            $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header Error : ".mysql_error()."\n";
                                exit;
                            }
                            exit;
                        } else {
                        }
                    } else {
                        echo $detailErr;
                        # Rollback, Delete Header
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".mysql_error();
                            exit;
                        }
                    }
                } else {
                    echo $headErr;
                    exit;
                }              
            #+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    
            #sisi Pengguna
            $kodeJurnal = $group;
            #ambil periodeaktif pengguna
            $strd="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
                  kodeorg='".$pengguna."' and tutupbuku=0";

            $resd=mysql_query($strd);
            $tgmulaid='';
            while($bard=mysql_fetch_object($resd))
            {
                $tgmulaid    = $bard->tanggalmulai;
            }            
             if($tgmulaid=='' or substr($tgmulaid,0,7)==substr($tanggal,0,7))#jika periode sama maka biarkan
                   $tgmulaid=$tanggal;  
             
            #======================== Nomor Jurnal =============================
            # Get Journal Counter
            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter']+1,3);

            # Transform No Jurnal dari No Transaksi
            $nojurnal = str_replace("-","",$tgmulaid)."/".$pengguna."/".$kodeJurnal."/".$konter;
            #======================== /Nomor Jurnal ============================
            # Prep Header
              unset($dataRes['header']);//ganti header   
              $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tgmulaid,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$biaya[$key],
                    'totalkredit'=>-1*$biaya[$key],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1'
                    ); 
                //print_r($dataRes['header']);
                //exit("Error");     
               # Debet 1
              $noUrut=1;
              unset($dataRes['detail']);//ganti header 
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tgmulaid,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunpekerjaan,
                    'keterangan'=>$param['periode'].':Biaya Kendaraan '.$param['kodevhc'],
                    'jumlah'=>$biaya[$key],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$pengguna,
                    'kodekegiatan'=>$kegiatan[$key],
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'0',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>$lokasi[$key]
                );
                $noUrut++;

                # Kredit 1
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tgmulaid,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunsendiri,
                    'keterangan'=>$param['periode'].':Alokasi biaya kend'.$param['kodevhc'],
                    'jumlah'=>-1*$biaya[$key],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$pengguna,
                    'kodekegiatan'=>$kegiatan[$key],
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'0',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_KERJA_AB',
                    'noaruskas'=>'',
                    'kodevhc'=>$param['kodevhc'],
                    'nodok'=>'',
                    'kodeblok'=>$lokasi[$key]
                );
                $noUrut++; 
                
              $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header OSIDE Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail OSIDE Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$ptpengguna.
                            "' and kodekelompok='".$kodeJurnal."'");
                        if(!mysql_query($updJurnal)) {
                            echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                            # Rollback if Update Failed
                            $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header Error : ".mysql_error()."\n";
                                exit;
                            }
                            exit;
                        } else {
                        }
                    } else {
                        echo $detailErr;
                        # Rollback, Delete Header
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".mysql_error();
                            exit;
                        }
                    }
                } else {
                    echo $headErr;
                    exit;
                }  
           }
      }
}  
?>