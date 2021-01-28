<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;

   
		$KodS="";

		
		if ($param['kode']=="TDAE"){
		//persediaan tbs
		$debet="1150101";
		$kredit1="5110201";
		
		 $kredit="1160103";
		 $debet1="1160102";
		}else if ($param['kode']=="TDBE"){
		//persediaan tbs
		$debet="1150101";
		$kredit1="5110201";
		
		 $kredit="1160104";
		 $debet1="1160102";
		}else if ($param['kode']=="USJE"){
		//persediaan tbs
		/*
		$debet="1150101";
		$kredit1="5110201";	
			
		 $kredit="1160207";
		 $debet1="1160203";
		 */
		 //persediaan tbs
		
		$debet="1150101";
		$kredit="2110101";
		$KodS="S001180554";
		
		$debet1="1120201";
		 $kredit1="5110201";
		 
		 
		
		 
		}
   
       
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
		$TglP=explode('#',$param['tglpro']);
		$TglDB=explode('-',$TglP[2]);
		 $str="select nokounter from ".$dbname.".keu_5kelompokjurnal 
         where kodekelompok='M' limit 1";
		$res=mysql_query($str);
		$bar=mysql_fetch_object($res);
	   $konter =$bar->nokounter;
	   $konterrU=$konter+2;
		mysql_query("update ".$dbname.".keu_5kelompokjurnal set nokounter='".$konterrU."'  where kodekelompok='M' "); 
	
        # Transform No Jurnal dari No Transaksi
        $nojurnal = $TglDB[2].$TglDB[1].$TglDB[0]."/CBGM/M/".$konter;
		$konter++;
		$nojurnal1 = $TglDB[2].$TglDB[1].$TglDB[0]."/".$param['kode']."/M/".$konter;
        #======================== /Nomor Jurnal ============================
        # Prep Header
            $dataRes['header'][] = array(
                'nojurnal'=>$nojurnal,
                'kodejurnal'=>'M',
                'tanggal'=>$TglDB[2].'-'.$TglDB[1].'-'.$TglDB[0],
                'tanggalentry'=>date('Ymd'),
                'posting'=>1,
                'totaldebet'=>$param['rppro'],
                'totalkredit'=>-1*$param['rppro'],
                'amountkoreksi'=>'0',
                'noreferensi'=>'TBS_INT_'.$TglDB[2].$TglDB[1],
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1'
            );
			
			 $dataRes['header'][]= array(
                'nojurnal'=>$nojurnal1,
                'kodejurnal'=>'M',
                'tanggal'=>$TglDB[2].'-'.$TglDB[1].'-'.$TglDB[0],
                'tanggalentry'=>date('Ymd'),
                'posting'=>1,
                'totaldebet'=>$param['rppro'],
                'totalkredit'=>-1*$param['rppro'],
                'amountkoreksi'=>'0',
                'noreferensi'=>'TBS_INT_'.$TglDB[2].$TglDB[1],
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1'
            );
    # Data Detail
    $noUrut = 1;

    # Debet
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$TglDB[2].'-'.$TglDB[1].'-'.$TglDB[0],
        'nourut'=>$noUrut,
        'noakun'=>$debet, 
        'keterangan'=>"Pend. Tbs a/n ".$param['kode']." = ".$param['kgpro']." Kg (".$param['tglpro'].")",
        'jumlah'=>$param['rppro'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>"CBGM",
        'kodekegiatan'=>'',
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>$KodS,
        'noreferensi'=>'TBS_INT_'.$TglDB[2].$TglDB[1],
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
        'tanggal'=>$TglDB[2].'-'.$TglDB[1].'-'.$TglDB[0],
        'nourut'=>$noUrut,
        'noakun'=>$kredit, 
        'keterangan'=>"Pend. Tbs a/n ".$param['kode']." = ".$param['kgpro']." Kg (".$param['tglpro'].")",
        'jumlah'=>-1*$param['rppro'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>"CBGM",
        'kodekegiatan'=>'',
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>$KodS,
        'noreferensi'=>'TBS_INT_'.$TglDB[2].$TglDB[1],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>'',
		'kodebatch'=>''
    );
    $noUrut++;
	
	
	# Debet1
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal1,
        'tanggal'=>$TglDB[2].'-'.$TglDB[1].'-'.$TglDB[0],
        'nourut'=>$noUrut,
        'noakun'=>$debet1, 
        'keterangan'=>"Pend. Tbs a/n ".$param['kode']." = ".$param['kgpro']." Kg (".$param['tglpro'].")",
        'jumlah'=>$param['rppro'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$param['kode'],
        'kodekegiatan'=>'',
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>'',
        'noreferensi'=>'TBS_INT_'.$TglDB[2].$TglDB[1],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>'',
		'kodebatch'=>''
    );
    $noUrut++;

    # Kredit1
    $dataRes['detail'][] = array(
       'nojurnal'=>$nojurnal1,
        'tanggal'=>$TglDB[2].'-'.$TglDB[1].'-'.$TglDB[0],
        'nourut'=>$noUrut,
        'noakun'=>$kredit1, 
        'keterangan'=>"Pend. Tbs a/n ".$param['kode']." = ".$param['kgpro']." Kg (".$param['tglpro'].")",
        'jumlah'=>-1*$param['rppro'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$param['kode'],
        'kodekegiatan'=>'',
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>'',
        'noreferensi'=>'TBS_INT_'.$TglDB[2].$TglDB[1],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>'',
		'kodebatch'=>''
    );
	
     #===========EXECUTE
    
	foreach($dataRes['header'] as $row) {
            $insHead = insertQuery($dbname,'keu_jurnalht',$row);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".mysql_error()."\n";
                break;
            }
     }
	 /*
	 $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
    if(!mysql_query($insHead)) {
        $headErr .= 'Insert Header Error : '.mysql_error()."\n";
    }
*/
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
			$RBDet1 = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal1."'");
            if(!mysql_query($RBDet1)) {
                echo "Rollback Delete Header Error : ".mysql_error();
                exit;
            }
        }
    } else {
        echo $headErr;
        exit;
    }               
    
?>