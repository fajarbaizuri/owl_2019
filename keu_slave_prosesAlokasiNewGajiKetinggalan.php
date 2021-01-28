<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$tanggal=$param['periode']."-28";



if($param['rowA']=="1"){
#periksa dan hapus transaksi untuk data yang sudah di proses pada periode yang sama    
$str="DELETE FROM  ".$dbname.".`keu_jurnalht` WHERE  `nojurnal` LIKE  '".str_replace("-","",$param['periode'])."__/".$_SESSION['empl']['lokasitugas']."/%' AND  `noreferensi` =  'ALK_GAJI_LBR'";
    
    mysql_query($str);
}

//id ktu
switch($_SESSION['empl']['lokasitugas']){
	case "TDAE":
		$KTUID="0000000388";
	break;
	case "TDBE":
		$KTUID="0000000487";
	break;
	case "USJE":
		$KTUID="0000009441";
	break;
	case "CBGM":
		$KTUID="0000000068";
	break;
	case "TKFB":
		$KTUID="0000000060";
	break;
	case "FBAO":
		$KTUID="0000000060";
	break;
}
#=================================================
#Periksa Proses Gaji Kebun 
switch($_SESSION['empl']['lokasitugas']){
	case "TDAE":
	case "TDBE":
	case "USJE":
	case "FBAO":
		ProsesALGJKebun();
		
	break;
	case "CBGM":
	case "TKFB":
		exit("Alokasi Penggajian Langsung Hanya untuk Kebun.");
	break;
}


function ProsesALGJKebun(){ 
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
	global $KTUID;
	
	$param['balok']=str_replace(",","",$param['balok']);
	$param['bnet']=str_replace(",","",$param['bnet']);
	$param['bhut']=str_replace(",","",$param['bhut']);
	$param['bbiaya']=str_replace(",","",$param['bbiaya']);
	$param['pjht']=str_replace(",","",$param['pjht']);
	$param['pjk']=str_replace(",","",$param['pjk']);
	$param['jp']=str_replace(",","",$param['jp']);
	$param['ppinjaman']=str_replace(",","",$param['ppinjaman']);
	$param['phk']=str_replace(",","",$param['phk']);
	$param['ppsd']=str_replace(",","",$param['ppsd']);
	$param['pnat']=str_replace(",","",$param['pnat']);
	$param['pmin']=str_replace(",","",$param['pmin']);
  
		$group="M0";
    if(trim($param['biaya'])=="B/L"){
		$AkunDBA="2130101";
		
		if(trim($param['kat'])=="PEMANEN"){
			
			//$AkunDBG="6110902";
			//$AkunDBG="6110101";
			//$AkunKRG="6110101";
			$AkunKR1="6110101";
			//jht
			$AkunKRA="2130102";
			//kesehatan
			$AkunKRB="2130109";
			//piutang
			$AkunKRC="1130101";
			//jp
			$AkunKRD="2130110";
			
		}else if(trim($param['kat'])=="PEMELIHARAAN"){
			
			#periksa di perawatan
			$AkunKRA="2130102";
			$AkunKRB="2130109";
			$AkunKRC="1130101";
			$AkunKRD="2130110";
		}else if(trim($param['kat'])=="HELPER"){
			
			#periksa di perawatan
			$AkunKRA="2130102";
			$AkunKRB="2130109";
			$AkunKRC="1130101";
			$AkunKRD="2130110";
		}else if(trim($param['kat'])=="KERNET"){
			
			#periksa di perawatan
			$AkunKRA="2130102";
			$AkunKRB="2130109";
			$AkunKRC="1130101";
			$AkunKRD="2130110";
		}
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
		if ($param['bhut'] !=0){
			$dataRes['header'] = array(
                'nojurnal'=>$nojurnal,
                'kodejurnal'=>$kodeJurnal,
                'tanggal'=>$tanggal,
                'tanggalentry'=>date('Y-m-d'),
                'posting'=>'1',
                'totaldebet'=>$param['bhut'],
                'totalkredit'=>-1*$param['bhut'], 
                'amountkoreksi'=>'0',
                'noreferensi'=>'ALK_GAJI_LBR',
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1'
            );
		}else{
			exit;
		}
           

            # Data Detail
            $noUrut = 1;
			# Debet
			
				if ($param['bhut'] !=0){
					$dataRes['detail'][] = array(
					'nojurnal'=>$nojurnal,
					'tanggal'=>$tanggal,
					'nourut'=>$noUrut,
					'noakun'=>$AkunDBA,
					'keterangan'=>'ADJ H.Gaji '.$param['ketA'],
					'jumlah'=>$param['bhut'],
					'matauang'=>'IDR',
					'kurs'=>'1',
					'kodeorg'=>$_SESSION['empl']['lokasitugas'],
					'kodekegiatan'=>'',
					'kodeasset'=>'',
					'kodebarang'=>'',
					'nik'=>'',
					'kodecustomer'=>'',
					'kodesupplier'=>'',
					'noreferensi'=>'ALK_GAJI_LBR',
					'noaruskas'=>'',
					'kodevhc'=>'',
					'nodok'=>'',
					'kodeblok'=>$param['unit'],
					'kodebatch'=>''
					);
					$noUrut++;
				}
				/*
				if ($param['pmin'] !=0 && trim($param['kat']) =="PEMANEN"){
					$dataRes['detail'][] = array(
					'nojurnal'=>$nojurnal,
					'tanggal'=>$tanggal,
					'nourut'=>$noUrut,
					'noakun'=>$AkunDBG,
					'keterangan'=>'A/L B.Premi Panen '.$param['ketA'],
					'jumlah'=>$param['pmin'],
					'matauang'=>'IDR',
					'kurs'=>'1',
					'kodeorg'=>$_SESSION['empl']['lokasitugas'],
					'kodekegiatan'=>'',
					'kodeasset'=>'',
					'kodebarang'=>'',
					'nik'=>'',
					'kodecustomer'=>'',
					'kodesupplier'=>'',
					'noreferensi'=>'ALK_GAJI_LBR',
					'noaruskas'=>'',
					'kodevhc'=>'',
					'nodok'=>'',
					'kodeblok'=>$param['unit'],
					'kodebatch'=>''
					);
					$noUrut++;
				}
			
			*/
			
			 # Kredit
			 if(trim($param['kat'])=="PEMANEN"){
				if ($param['bbiaya'] !=0){
					$dataRes['detail'][] = array(
					'nojurnal'=>$nojurnal,
					'tanggal'=>$tanggal,
					'nourut'=>$noUrut,
					'noakun'=>$AkunKR1,
					'keterangan'=>'ADJ B.Gaji '.$param['ketA'],
					'jumlah'=>$param['bbiaya']  * -1 ,
					'matauang'=>'IDR',
					'kurs'=>'1',
					'kodeorg'=>$_SESSION['empl']['lokasitugas'],
					'kodekegiatan'=>'611010105',
					'kodeasset'=>'',
					'kodebarang'=>'',
					'nik'=>'',
					'kodecustomer'=>'',
					'kodesupplier'=>'',
					'noreferensi'=>'ALK_GAJI_LBR',
					'noaruskas'=>'',
					'kodevhc'=>'',
					'nodok'=>'',
					'kodeblok'=>$param['unit'],
					'kodebatch'=>''
					);
					$noUrut++;
				}
				/*
				if ($param['pmin'] !=0){
					$dataRes['detail'][] = array(
					'nojurnal'=>$nojurnal,
					'tanggal'=>$tanggal,
					'nourut'=>$noUrut,
					'noakun'=>$AkunKRG,
					'keterangan'=>'A/L B.Premi Panen '.$param['ketA'],
					'jumlah'=>$param['pmin']  * -1 ,
					'matauang'=>'IDR',
					'kurs'=>'1',
					'kodeorg'=>$_SESSION['empl']['lokasitugas'],
					'kodekegiatan'=>'',
					'kodeasset'=>'',
					'kodebarang'=>'',
					'nik'=>'',
					'kodecustomer'=>'',
					'kodesupplier'=>'',
					'noreferensi'=>'ALK_GAJI_LBR',
					'noaruskas'=>'',
					'kodevhc'=>'',
					'nodok'=>'',
					'kodeblok'=>$param['unit'],
					'kodebatch'=>''
					);
					$noUrut++;
				}
				*/
			 }else{
				 if ($param['bbiaya'] !=0){
				 $ind=1;
				 $strA="select distinct b.kodekegiatan,b.kodeorg,c.noakun from ".$dbname.".kebun_kehadiran_vw a 
			left join ".$dbname.".kebun_perawatan_vw b on a.notransaksi=b.notransaksi 
			left join ".$dbname.".setup_kegiatan c on b.kodekegiatan=c.kodekegiatan    
			where a.tanggal between '".$param['dari']."' and '".$param['sampai']."'
			and a.kodeorg like '".trim($param['unit'])."%' and a.unit='".$_SESSION['empl']['lokasitugas']."' 
			and c.noakun!='' group by c.noakun";
			$resA=mysql_query($strA);
			
			$totBia=mysql_num_rows($resA);
			$porsiBIaya=round($param['bbiaya']/$totBia, PHP_ROUND_HALF_UP);
			$sisaPorsi=$param['bbiaya']-($porsiBIaya * $totBia);
				 while($bar=mysql_fetch_object($resA)){
					 
					
						if ($ind < $totBia){
							$dbBia=$porsiBIaya;
						}else{
							$dbBia=$porsiBIaya+$sisaPorsi;
						}
					$dataRes['detail'][] = array(
					'nojurnal'=>$nojurnal,
					'tanggal'=>$tanggal,
					'nourut'=>$noUrut,
					'noakun'=>$bar->noakun,
					'keterangan'=>'ADJ B.Gaji '.$param['ketA'],
					'jumlah'=>$dbBia  * -1,
					'matauang'=>'IDR',
					'kurs'=>'1',
					'kodeorg'=>$_SESSION['empl']['lokasitugas'],
					'kodekegiatan'=>$bar->kodekegiatan,
					'kodeasset'=>'',
					'kodebarang'=>'',
					'nik'=>'',
					'kodecustomer'=>'',
					'kodesupplier'=>'',
					'noreferensi'=>'ALK_GAJI_LBR',
					'noaruskas'=>'',
					'kodevhc'=>'',
					'nodok'=>'',
					'kodeblok'=>$bar->kodeorg,
					'kodebatch'=>''
					
					);
					$noUrut++;
					$ind++;	
					
				}
				 }
			 }
			
			if ($param['pjht'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKRA,
                'keterangan'=>'A/L H.JHT 2% '.$param['ketA'],
                'jumlah'=>$param['pjht'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI_LBR',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['pjk'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKRB,
                'keterangan'=>'A/L H.JK 1% '.$param['ketA'],
                'jumlah'=>$param['pjk'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI_LBR',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['jp'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKRD,
                'keterangan'=>'A/L H.JP 1% '.$param['ketA'],
                'jumlah'=>$param['jp'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI_LBR',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['ppinjaman'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKRC,
                'keterangan'=>'A/L P.Kary '.$param['ketA'],
                'jumlah'=>$param['ppinjaman'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$KTUID,
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI_LBR',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
            
     
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr = 'Insert Header BL Error : '.mysql_error()."\n";
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
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header BL Error : ".mysql_error()."\n";
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

     
?>