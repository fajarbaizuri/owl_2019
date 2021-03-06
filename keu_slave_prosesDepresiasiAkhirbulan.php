<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
//hilangkan koma
$param['hartot']=str_replace(",","",$param['hartot']);
#contoh format parameter
#Array
#(
#       kodejurnal    
//      periode
//      keterangan
//      jumlah 
#)
#
       #proses data
        $kodeJurnal = $param['kodejurnal'];
        
#ambil noakun pada table parameterjurnal
   $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal 
         where jurnalid='".$kodeJurnal."'";
   $res=mysql_query($str);
   //echo $str." Error ".mysql_error($conn);
   if(mysql_num_rows($res)<1)
   {
       exit("Error: Tidak ada kode jurnal untuk ".$kodeJurnal);
   }
   else
   {
      while($bar=mysql_fetch_object($res))
      {
           $debet=$bar->noakundebet;
           $kredit=$bar->noakunkredit;
      } 
      
      #periksa jika sudah pernah dilakukan
      $blm=str_replace("-","",$param['periode']);
       $str="select * from ".$dbname.".keu_jurnalht where nojurnal 
             like '%".$blm."28/".substr($_SESSION['empl']['lokasitugas'],0,4)."/".$kodeJurnal."%'";
       $res=mysql_query($str);
       if(mysql_num_rows($res)>0)
       {
          exit("Error: Proses penarikan data Penyusutan sudah pernah dilakukan"); 
       }   
   
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $konter ='001';
        $tanggal=$param['periode']."-28";
        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".substr($_SESSION['empl']['lokasitugas'],0,4)."/".$kodeJurnal."/".$konter;
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
                'noreferensi'=>$kodeJurnal.":".str_replace("-","",$tanggal),
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
        'noakun'=>$debet,
        'keterangan'=>$param['keterangan']." Periode:".$_POST['periode'],
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
        'noreferensi'=>$kodeJurnal.":".str_replace("-","",$tanggal),
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>'',
		'kodebatch'=>''
    );
    $noUrut++;

    # Kredit
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$tanggal,
        'nourut'=>$noUrut,
        'noakun'=>$kredit,
        'keterangan'=>$param['keterangan']." Periode:".$_POST['periode'],
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
        'noreferensi'=>$kodeJurnal.":".str_replace("-","",$tanggal),
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>'',
		'kodebatch'=>''
    );
    $noUrut++;
     #===========EXECUTE
    $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
    if(!mysql_query($insHead)) {
        $headErr .= 'Insert Header Error : '.mysql_error()."\n";
    }

    if($headErr=='') {
        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
        $detailErr = '';
        foreach($dataRes['detail'] as $row) {
            $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
            if(!mysql_query($insDet)) {
                $detailErr .= "Insert Detail Error : ".mysql_error()."\n";
                break;
            }
        }

        if($detailErr=='') {
            # Header and Detail inserted
            #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
            }
        else {
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
?>