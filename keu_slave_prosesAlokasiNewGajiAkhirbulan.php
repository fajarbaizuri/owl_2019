<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$tanggal=$param['periode']."-28";

if($param['rowA']=="1"){ 
	
#periksa dan hapus transaksi untuk data yang sudah di proses pada periode yang sama    
    $str="DELETE FROM  ".$dbname.".`keu_jurnalht` WHERE  `nojurnal` LIKE  '".str_replace("-","",$param['periode'])."__/".$_SESSION['empl']['lokasitugas']."%' AND  `noreferensi` =  'ALK_GAJI'";
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
#==========================================konfigurasi database
# KBNB0	Gaji BTL Kebun/Pabrik
# KBNB1	Premi/Lebur BTL Kebun/Pabrik
# KBNB2	Tunjangan Lain
# KBNB3	THR BTL
# KBNB4	Bonus BTL
# KBNB5	Pengobatan BTL
# VHCG0	Gaji Kendaraan/A.Berat
# VHCG1	Biaya Lebur Kendaraan/A.Berat
# VHCG2	Biaya Tunjangan Lain Kend./A.Berat
# VHCG3	THR Kend./A.Berat
# VHCG4	Bonus Kend. A.Berat
# VHCG5	Pengobatan Kend./A.Berat
# WSG0	Biaya Gaji Bengkel
# WSG1	Biaya Premi/Lembur Bengkel
# WSG2	Tunjangan Lain Bengkel
# WSG3	THR Traksi
# WSG4	Bonus Traksi
# WSG5	Pengobatan Traksi
# KBNL0	Biaya pengawasan BBT
# KBNL1	Biaya pengawasan TBM
# KBNL2	Biaya pengawasan TM
# KBNL3	Biaya Pengawasan Panen
#============================================konfigurasi database

#==Komfigurasi komponen gaji
# 1	Gaji Pokok
# 2	Tunjangan Jabatan
# 14	Rapel
# 16	Premi Pengawasan
# 21	Klaim Pengobatan
# 26	Bonus
# 27	Tunjangan Fasilitas
# 28	THR
# 30	Tunjangan Profesi
# 31	Tunjangan Masa Kerja
# 32	Premi
# 33	Lembur
# 34	Penalti
#
#=======================================================
#parameter
/*
rowA
bgaji
blembur
bpremi
bthr
bbonus
ppiutang
pjht
pjk
jp
hgaji
*/

#=================================================
#Periksa Proses Gaji Kebun & Pabrik & Traksi
switch($_SESSION['empl']['lokasitugas']){
	case "TDAE":
	case "TDBE":
	case "USJE":
	case "TKFB":
	case "FBAO":
		ProsesGJKebun();
	break;
		
	case "CBGM":
		prosesGajiPabrik();
	break;
	
	
		
}

#----AKun Gaji

/* Kebun  , pabrik  , traksi TDL
Biaya Gaji 7110102
Biaya Lembur 7110103
Biaya Premi 7110102
Biaya Thr 7110106
Biaya Insentif 7110104

Hut. Gaji 2130101
Pot. Premi JHT  2130102
Pot. Premi JK 2130109
Pot. Premin PENSIUN 2130110
Pot. Pinjaman 1130101
*/

/* BIAYA GAJI PABRIK LANGSUNG
olah
Biaya Gaji(Gaji+Premi+THR+bonus) 6310101
Biaya Lembur 6310102

maintenance
Biaya Gaji(Gaji+Premi+THR+bonus) 6310201
Biaya Lembur 6310202


Hut. Gaji 2130101
Pot. Premi JHT  2130102
Pot. Premi JK 2130109
Pot. Premin PENSIUN 2130110
Pot. Pinjaman 1130101
*/

/* BIAYA GAJI KEBUN LANGSUNG
tidak boleh ada biaya langsung di proses ini 
karena sudah di alokasi oleh system kecuali 
operator / kernet dari traksi 
Biaya Gaji(Gaji+lembur+THR+bonus) 4110201
Biaya Premi 4110202


*/

function prosesGajiPabrik(){ 
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
 	global $KTUID;
	
$param['bgaji']=str_replace(",","",$param['bgaji']);
$param['blembur']=str_replace(",","",$param['blembur']);
$param['bawas']=str_replace(",","",$param['bawas']);
$param['bpremi']=str_replace(",","",$param['bpremi']);
$param['bthr']=str_replace(",","",$param['bthr']);
$param['bbonus']=str_replace(",","",$param['bbonus']);
$param['ppiutang']=str_replace(",","",$param['ppiutang']);
$param['pjht']=str_replace(",","",$param['pjht']);
$param['pjk']=str_replace(",","",$param['pjk']);
$param['jp']=str_replace(",","",$param['jp']);
$param['hgaji']=str_replace(",","",$param['hgaji']);

   
	
  #output pada jurnal kolom noreferensi ALK_GAJI  
    if(trim($param['biaya'])=="B/TL"){
		$group="KBNB0";
		if(trim($param['kat'])=="PRKT. KANTOR"){
			$KegDB1="711010201";
			$KegDB2="711010301";
			$KegDB3="711010301";
			$KegDB4="711030102";
			$KegDB5="711010301";
			$KegDB6="711010301";
			
			//gaji
			$AkunDB1="7110102"; //7110102
			//lembur
			$AkunDB2="7110103";
			//premi
			$AkunDB3="7110103";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110103";
			//pengawas
			$AkunDB6="7110103";
		
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
			
		}else if(trim($param['kat'])=="PRKT. SATPAM"){
			$KegDB1="711090101";
			$KegDB2="711090201";
			$KegDB3="711090201";
			$KegDB4="711030102";
			$KegDB5="711090201";
			$KegDB6="711090201";
			
			//gaji
			$AkunDB1="7110901"; //7110102
			//lembur
			$AkunDB2="7110902";
			//premi
			$AkunDB3="7110902";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110902";
			//pengawas
			$AkunDB6="7110902";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
			
		}else if(trim($param['kat'])=="MANDOR 1"){
			$KegDB1="711010201";
			$KegDB2="711010301";
			$KegDB3="711010301";
			$KegDB4="711030102";
			$KegDB5="711010301";
			$KegDB6="711010301";
			
			//gaji
			$AkunDB1="7110102"; //7110102
			//lembur
			$AkunDB2="7110103";
			//premi
			$AkunDB3="7110103";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110103";
			//pengawas
			$AkunDB6="7110103";
		
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}
		
		
	}else{
			$group="PKS01";
			$KegDB1="631010101";
			$KegDB2="631010201";
			$KegDB3="631010201";
			$KegDB4="711030103";
			$KegDB5="631010201";
			$KegDB6="631010201";
			
			//gaji
			$AkunDB1="6310101";
			//lembur
			$AkunDB2="6310102";
			//premi
			$AkunDB3="6310102";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="6310102";
			//pengawas
			$AkunDB6="6310102";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		/*
		if(trim($param['unit'])=="CBGM11"){
			$group="PKS04";
			$AkunDB1="6310201";
			$AkunDB2="6310202";
			$AkunDB3="6310201";
			$AkunDB4="6310201";
			$AkunDB5="6310201";
			$AkunDB6="6310201";
			
			$AkunKR1="2130101";
			$AkunKR2="2130102";
			$AkunKR3="2130109";
			$AkunKR4="1130101";
			$AkunKR5="2130110";
		}else{
			$group="PKS01";
			$AkunDB1="6310101";
			$AkunDB2="6310102";
			$AkunDB3="6310101";
			$AkunDB4="6310101";
			$AkunDB5="6310101";
			$AkunDB6="6310101";
			
			$AkunKR1="2130101";
			$AkunKR2="2130102";
			$AkunKR3="2130109";
			$AkunKR4="1130101";
			$AkunKR5="2130110";
		}
		*/
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
                'tanggalentry'=>date('Y-m-d'),
                'posting'=>'1',
                'totaldebet'=>$param['hgaji']+$param['pjht']+$param['pjk']+$param['jp']+$param['ppiutang'],
                'totalkredit'=>-1*($param['hgaji']+$param['pjht']+$param['pjk']+$param['jp']+$param['ppiutang']),
                'amountkoreksi'=>'0',
                'noreferensi'=>'ALK_GAJI',
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1'
            );

            # Data Detail
            $noUrut = 1;
			# Debet
			if ($param['bgaji'] !=0){
			 $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB1,
                'keterangan'=>'B.Gaji '.$param['ketA'],
                'jumlah'=>$param['bgaji'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB1,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			
		
			
			if ($param['blembur'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB2,
                'keterangan'=>'B.Lembur '.$param['ketA'],
                'jumlah'=>$param['blembur'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB2,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bpremi'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB3,
                'keterangan'=>'B.Premi '.$param['ketA'],
                'jumlah'=>$param['bpremi'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB3,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bthr'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB4,
                'keterangan'=>'B.THR '.$param['ketA'],
                'jumlah'=>$param['bthr'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB4,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bbonus'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB5,
                'keterangan'=>'B.Bonus '.$param['ketA'],
                'jumlah'=>$param['bbonus'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB5,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bawas'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB6,
                'keterangan'=>'B.Pengawasan '.$param['ketA'],
                'jumlah'=>$param['bawas'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB6,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			
			 # Kredit
			if ($param['hgaji'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR1,
                'keterangan'=>'H.Gaji '.$param['ketA'],
                'jumlah'=>$param['hgaji']  * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['pjht'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR2,
                'keterangan'=>'H.JHT 2% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
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
                'noakun'=>$AkunKR3,
                'keterangan'=>'H.JK 1% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
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
                'noakun'=>$AkunKR5,
                'keterangan'=>'H.JP 1% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['ppiutang'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR4,
                'keterangan'=>'P.Kary '.$param['ketA'],
                'jumlah'=>$param['ppiutang'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$KTUID,
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
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
                $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n";
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
                            echo "Rollback Delete Header BTL Error : ".mysql_error()."\n";
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
/*
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
                'tanggalentry'=>date('Y-m-d'),
                'posting'=>'1',
                'totaldebet'=>$param['hgaji']+$param['pjht']+$param['pjk']+$param['jp']+$param['ppiutang'],
                'totalkredit'=>-1*($param['hgaji']+$param['pjht']+$param['pjk']+$param['jp']+$param['ppiutang']),
                'amountkoreksi'=>'0',
                'noreferensi'=>'ALK_GAJI',
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1'
            );

            # Data Detail
            $noUrut = 1;
			# Debet
			if ($param['bgaji'] !=0){
			 $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB1,
                'keterangan'=>'B.Gaji '.$param['ketA'],
                'jumlah'=>$param['bgaji'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			
			if ($param['blembur'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB2,
                'keterangan'=>'B.Lembur '.$param['ketA'],
                'jumlah'=>$param['blembur'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bpremi'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB3,
                'keterangan'=>'B.Premi '.$param['ketA'],
                'jumlah'=>$param['bpremi'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bthr'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB4,
                'keterangan'=>'B.THR '.$param['ketA'],
                'jumlah'=>$param['bthr'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bbonus'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB5,
                'keterangan'=>'B.Bonus '.$param['ketA'],
                'jumlah'=>$param['bbonus'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bawas'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB6,
                'keterangan'=>'B.Pengawasan '.$param['ketA'],
                'jumlah'=>$param['bawas'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			
			 # Kredit
			if ($param['hgaji'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR1,
                'keterangan'=>'H.Gaji '.$param['ketA'],
                'jumlah'=>$param['hgaji'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['pjht'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR2,
                'keterangan'=>'H.JHT 2% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
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
                'noakun'=>$AkunKR3,
                'keterangan'=>'H.JK 2% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
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
                'noakun'=>$AkunKR5,
                'keterangan'=>'H.JP 1% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['ppiutang'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR4,
                'keterangan'=>'P.Kary '.$param['ketA'],
                'jumlah'=>$param['ppiutang'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$KTUID,
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
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
                $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n";
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
                            echo "Rollback Delete Header BTL Error : ".mysql_error()."\n";
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
*/			
}


function ProsesGJKebun(){ 
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
	global $KTUID;

$param['bgaji']=str_replace(",","",$param['bgaji']);
$param['blembur']=str_replace(",","",$param['blembur']);
$param['bawas']=str_replace(",","",$param['bawas']);
$param['bpremi']=str_replace(",","",$param['bpremi']);
$param['bthr']=str_replace(",","",$param['bthr']);
$param['bbonus']=str_replace(",","",$param['bbonus']);
$param['ppiutang']=str_replace(",","",$param['ppiutang']);
$param['pjht']=str_replace(",","",$param['pjht']);
$param['pjk']=str_replace(",","",$param['pjk']);
$param['jp']=str_replace(",","",$param['jp']);
$param['hgaji']=str_replace(",","",$param['hgaji']);
		
		#output pada jurnal kolom noreferensi ALK_GAJI  
		

		
  
  
    if(trim($param['biaya'])=="B/TL"){
		$group="KBNB0";
		
		if(trim($param['kat'])=="PRGKT. KANTOR"){
			$KegDB1="711010201";
			$KegDB2="711010301";
			$KegDB3="711010301";
			$KegDB4="711030102";
			$KegDB5="711010301";
			$KegDB6="711010301";
			
			//gaji
			$AkunDB1="7110102"; //7110102
			//lembur
			$AkunDB2="7110103";
			//premi
			$AkunDB3="7110103";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110103";
			//pengawas
			$AkunDB6="7110103";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}else if(trim($param['kat'])=="PRGKT. AFD"){
			$KegDB1="711010202";
			$KegDB2="711010301";
			$KegDB3="711010301";
			$KegDB4="711030102";
			$KegDB5="711010301";
			$KegDB6="611090101";
			
			//gaji
			$AkunDB1="7110102"; //7110102
			//lembur
			$AkunDB2="7110103";
			//premi
			$AkunDB3="7110103";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110103";
			//pengawas
			$AkunDB6="6110901";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}else if(trim($param['kat'])=="PRGKT. PENGAWASAN"){
			$KegDB1="711010401";
			$KegDB2="711010402";
			$KegDB3="711010402";
			$KegDB4="711030102";
			$KegDB5="711010402";
			$KegDB6="611090101";
			
			//gaji
			$AkunDB1="7110104"; //7110102
			//lembur
			$AkunDB2="7110104";
			//premi
			$AkunDB3="7110104";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110104";
			//pengawas
			$AkunDB6="6110901";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}else if(trim($param['kat'])=="PRGKT. SATPAM"){
			$KegDB1="711090101";
			$KegDB2="711090201";
			$KegDB3="711090201";
			$KegDB4="711030102";
			$KegDB5="711090201";
			$KegDB6="711090201";
			
			//gaji
			$AkunDB1="7110901"; //7110102
			//lembur
			$AkunDB2="7110902";
			//premi
			$AkunDB3="7110902";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110902";
			//pengawas
			$AkunDB6="7110902";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}else if(trim($param['kat'])=="PRGKT. CENTENG"){
			$KegDB1="711090901";
			$KegDB2="711090902";
			$KegDB3="711090902";
			$KegDB4="711030102";
			$KegDB5="711090902";
			$KegDB6="711090902";
			
			//gaji
			$AkunDB1="7110909"; //7110102
			//lembur
			$AkunDB2="7110909";
			//premi
			$AkunDB3="7110909";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7110909";
			//pengawas
			$AkunDB6="7110909";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
			
		}else if(trim($param['kat'])=="PRGKT. ALAT BERAT"){
			$KegDB1="411020101";
			$KegDB2="411020201";
			$KegDB3="411020201";
			$KegDB4="711030102";
			$KegDB5="411020701";
			$KegDB6="411020201";
			
			//gaji
			$AkunDB1="4110201"; //7110102
			//lembur
			$AkunDB2="4110202";
			//premi
			$AkunDB3="4110202";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="4110207";
			//pengawas
			$AkunDB6="4110202";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}else if(trim($param['kat'])=="PRGKT. KENDARAAN"){
			$KegDB1="411020801";
			$KegDB2="411020901";
			$KegDB3="411020901";
			$KegDB4="711030102";
			$KegDB5="411020901";
			$KegDB6="411020901";
			
			//gaji
			$AkunDB1="4110208"; //7110102
			//lembur
			$AkunDB2="4110209";
			//premi
			$AkunDB3="4110209";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="4110209";
			//pengawas
			$AkunDB6="4110209";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}else if(trim($param['kat'])=="KONTROL API"){
			$KegDB1="711140101";
			$KegDB2="711140101";
			$KegDB3="711140101";
			$KegDB4="711030102";
			$KegDB5="711140101";
			$KegDB6="711140101";
			
			//gaji
			$AkunDB1="7111401"; //7110102
			//lembur
			$AkunDB2="7111401";
			//premi
			$AkunDB3="7111401";
			//thr
			$AkunDB4="7110301";
			//bonus
			$AkunDB5="7111401";
			//pengawas
			$AkunDB6="7111401";
			
			//HUTANG GAJI
			$AkunKR1="2130101";
			//HUT. JHT
			$AkunKR2="2130102";
			//HUT.BPJS
			$AkunKR3="2130109";
			//PIUTANG KARYAWAN
			$AkunKR4="1130101";
			//HUT. PENSIUN
			$AkunKR5="2130110";
		}
			
			
			
	}else if(trim($param['biaya'])=="B/L"){
		exit;
	  $group="VHCG0";
	  /*
	  if(trim($param['kat'])=="OPR/KERNET"){
		//gaji
		$AkunDB1="4110201";
		//lembur
		$AkunDB2="4110201";
		//premi
		$AkunDB3="4110202";
		//thr
		$AkunDB4="7110301";
		//bonus
		$AkunDB5="4110201";
		//pengawas
		$AkunDB6="4110201";
		
		$AkunKR1="2130101";
		$AkunKR2="2130102";
		$AkunKR3="2130109";
		$AkunKR4="1130101";
		$AkunKR5="2130110";
		}else{
			exit;
		}
	  */
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
                'tanggalentry'=>date('Y-m-d'),
                'posting'=>'1',
                'totaldebet'=>$param['hgaji']+$param['pjht']+$param['pjk']+$param['jp']+$param['ppiutang'],
                'totalkredit'=>-1*($param['hgaji']+$param['pjht']+$param['pjk']+$param['jp']+$param['ppiutang']),
                'amountkoreksi'=>'0',
                'noreferensi'=>'ALK_GAJI',
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1'
            );

            # Data Detail
            $noUrut = 1;
			# Debet
			if ($param['bgaji'] !=0){
			 $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB1,
                'keterangan'=>'B.Gaji '.$param['ketA'],
                'jumlah'=>$param['bgaji'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB1,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			
		
			
			if ($param['blembur'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB2,
                'keterangan'=>'B.Lembur '.$param['ketA'],
                'jumlah'=>$param['blembur'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB2,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bpremi'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB3,
                'keterangan'=>'B.Premi '.$param['ketA'],
                'jumlah'=>$param['bpremi'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB3,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bthr'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB4,
                'keterangan'=>'B.THR '.$param['ketA'],
                'jumlah'=>$param['bthr'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB4,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bbonus'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB5,
                'keterangan'=>'B.Bonus '.$param['ketA'],
                'jumlah'=>$param['bbonus'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB5,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['bawas'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunDB6,
                'keterangan'=>'P.Pengawasan '.$param['ketA'],
                'jumlah'=>$param['bawas'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>$KegDB6,
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			
			 # Kredit
			if ($param['hgaji'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR1,
                'keterangan'=>'H.Gaji '.$param['ketA'],
                'jumlah'=>$param['hgaji']  * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>'',
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['pjht'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR2,
                'keterangan'=>'H.JHT 2% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
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
                'noakun'=>$AkunKR3,
                'keterangan'=>'H.JK 1% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
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
                'noakun'=>$AkunKR5,
                'keterangan'=>'H.JP 1% '.$param['ketA'],
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
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['unit'],
                'kodebatch'=>''
            );
            $noUrut++;
			}
			if ($param['ppiutang'] !=0){
			$dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$AkunKR4,
                'keterangan'=>'P.Kary '.$param['ketA'],
                'jumlah'=>$param['ppiutang'] * -1,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$KTUID,
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
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
                $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n";
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
                            echo "Rollback Delete Header BTL Error : ".mysql_error()."\n";
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

















//=====================================
function prosesGajiSipil(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
    #WSG0	Gaji Bengkel	
    #WSG1	Biaya Lebur Bengkel
    #WSG2	Biaya Tunjangan Lain Bengkel
    #WSG3	THR Bengkel	
    #WSG4	Bonus Bengkel
    #WSG5	Pengobatan Bengkel
    
    #output pada jurnal kolom noreferensi ALK_SIPL_GYMH  
      $group='SIPL1';  //defaultnya tunjangan

    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
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
                    'noreferensi'=>'ALK_SIPL_GYMH',
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
                    'keterangan'=>$param['namakomponen'].' '.$param['namakaryawan']." By. Perumahan",
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_SIPL_GYMH',
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
                    'noakun'=>$akunkredit,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'] ." By.Perumahan",
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_SIPL_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'kodebatch'=>''
                );
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header SIPIL Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail SIPIL Error : ".mysql_error()."\n";
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

function prosesGajiWs(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
    #WSG0	Gaji Bengkel	
    #WSG1	Biaya Lebur Bengkel
    #WSG2	Biaya Tunjangan Lain Bengkel
    #WSG3	THR Bengkel	
    #WSG4	Bonus Bengkel
    #WSG5	Pengobatan Bengkel
    
    #output pada jurnal kolom noreferensi ALK_WS_GYMH  
    if($param['komponen']==1 or $param['komponen']==14)
      $group='WSG0';
    elseif($param['komponen']==16 or $param['komponen']==32 or $param['komponen']==33)
      $group='WSG1';
    elseif($param['komponen']==28)
      $group='WSG3';  
    elseif($param['komponen']==26)
      $group='WSG4';  
    elseif($param['komponen']==21)
      $group='WSG5';
    else
      $group='WSG2';  //defaultnya tunjangan

    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
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
                    'noreferensi'=>'ALK_WS_GYMH',
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
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan']." By.Bengkel",
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_WS_GYMH',
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
                    'noakun'=>$akunkredit,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'] ." By.Bengkel",
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_WS_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'kodebatch'=>''
                );
                $noUrut++; 
 /*                
            #periksa apakah sudah pernah diproses dengan karyawan yang sama
            $str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
                  like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
                  and noakun='".$akundebet."' and nik='".$param['karyawanid']."'";
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
function prosesGajiTraksi(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname; 
    #VHCG0	Gaji Kendaraan/A.Berat		
    #VHCG1	Biaya Lebur Kendaraan/A.Berat	
    #VHCG2	Biaya Tunjangan Lain Kend./A.Berat	
    #VHCG3	THR Kend./A.Berat	
    #VHCG4	Bonus Kend. A.Berat	
    #VHCG5	Pengobatan Kend./A.Berat
    
    #output pada jurnal kolom noreferensi ALK_TRK_GYMH  
    if($param['komponen']==1 or $param['komponen']==14)
      $group='VHCG0';
    elseif($param['komponen']==16 or $param['komponen']==32 or $param['komponen']==33)
      $group='VHCG1';
    elseif($param['komponen']==28)
      $group='VHCG3';  
    elseif($param['komponen']==26)
      $group='VHCG4';  
    elseif($param['komponen']==21)
      $group='VHCG5';
    else
      $group='VHCG2';  //defaultnya tunjangan

    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
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
                    'noreferensi'=>'ALK_TRK_GYMH',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1'
                );
       #periksa apakan dia sebagai operator kendaraan
        $str="select * from ".$dbname.".vhc_5operator where karyawanid=".$param['karyawanid'];
        $res=mysql_query($str);
        
        #ambil kendaraan
        $kodekend=''; 
        while($bas=mysql_fetch_object($res))
         {
             $kodekend=$bas->vhc;
         }
        if($kodekend!='')
        {
                # Data Detail
                $noUrut = 1;

                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akundebet,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan']." By.Kendaraan",
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_TRK_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>$kodekend,
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
                    'noakun'=>$akunkredit,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'] ." By.Kendaraan",
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_TRK_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>$kodekend,
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'kodebatch'=>''
                );
                $noUrut++; 
       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header Traksi Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Traksi Error : ".mysql_error()."\n";
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
    else
    {
      #jika tidak maka jika workshop proses ke workshop, jika tidak maka miaya umum
        if($param['tipeorganisasi']=='WORKSHOP')
            prosesGajiWs();
        else
            prosesGajiKebun();
    }   
}
             
?>