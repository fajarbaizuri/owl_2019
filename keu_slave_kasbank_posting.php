<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<?
$param = $_POST;

$kegiatan="SELECT * FROM ".$dbname.". setup_parameterappl WHERE kodeaplikasi = 'TX'";
$query=mysql_query($kegiatan) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $excludeacc[$res['nilai']]=$res['nilai'];
}

#=== Get Data ===
# Header
$queryH = selectQuery($dbname,'keu_kasbankht',"*","notransaksi='".
    $param['notransaksi']."' and kodeorg='".$param['kodeorg'].
    "' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."' limit 1");
$dataH = fetchData($queryH);
//echo '<pre>';print_r($dataH);
# Detail
$queryD = selectQuery($dbname,'keu_kasbankdt',"*","notransaksi='".
    $param['notransaksi']."' and kodeorg='".$param['kodeorg'].
    "' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'");
$dataD = fetchData($queryD);
#=== Cek Jumlah Detail dan Header harus sama ===
$tmpJml = 0;
foreach($dataD as $row) {
    $tmpJml += $row['jumlah'];
}
if($tmpJml!=$dataH[0]['jumlah']) {
	echo "SWarning : Jumlah pada Header(".$dataH[0]['jumlah'].") tidak sama dengan Detail(". $tmpJml.")\n";
    echo "Data belum dapat diposting";
    exit;
}


#=== Cek if posted ===
$error0 = "";
if($dataH[0]['posting']==1) {
    $error0 .= $_SESSION['lang']['errisposted'];
}
if($error0!='') {
    echo "Data Error :\n".$error0;
    exit;
}
#====cek periode
$tgl = str_replace("-","",$dataH[0]['tanggal']);
if($_SESSION['org']['period']['start']>$tgl)
    exit('Error:Tanggal diluar periode aktif');

#=== Cek if data not exist ===
$error1 = "";
if(count($dataH)==0) {
    $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
}
if(count($dataD)==0) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}
if($error1!='') {
    echo "Data Error :\n".$error1;
    exit;
}

#=== Transform Data ===
$dataRes['header'] = array();
$dataRes['detail'] = array();
$dataResoto['header'] = array();
$dataResoto['detail'] = array();

#1. Data Header
# Get Journal Counter
$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter']+1,3);

# Prep No Jurnal
$nojurnal = str_replace('-','',$dataH[0]['tanggal'])."/".$dataH[0]['kodeorg']."/".
    $dataD[0]['kode']."/".$konter;

# Keluar / Masuk
$kodeKM = substr($dataH[0]['tipetransaksi'],0,1);

# Prep Header
$dataRes['header'] = array(
    'nojurnal'=>$nojurnal,
    'kodejurnal'=>$dataD[0]['kode'],
    'tanggal'=>$dataH[0]['tanggal'],
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>$dataH[0]['notransaksi'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1'
  
);

#2. Data Detail
# Detail (Many)
$noUrut = 0;
$totalJumlah = 0;
foreach($dataD as $row) {    
    if(substr($row['kode'],1,1)=='M') {
        $jumlah = $row['jumlah']*(-1);
    } else {
        $jumlah = $row['jumlah'];
    }
    $dKurs=1;
    $dMtUang='IDR';
    if($row['matauang']!='IDR')
    {
        //$dMtUang=$row['matauang'];
        $dKurs=$row['kurs'];
        $jumlah=$jumlah*$dKurs;
    }
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$row['noakun'],
        'keterangan'=>$row['keterangan2'],
        'jumlah'=>$jumlah,
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$row['kodeorg'],
        'kodekegiatan'=>$row['kodekegiatan'],
        'kodeasset'=>$row['kodeasset'],
        'kodebarang'=>$row['kodebarang'],
        'nik'=>$row['nik'],
        'kodecustomer'=>$row['kodecustomer'],
        'kodesupplier'=>$row['kodesupplier'],
        'noreferensi'=>$dataH[0]['notransaksi'],
        'noaruskas'=>$row['noaruskas'],
        'kodevhc'=>$row['kodevhc'],
        'nodok'=>$row['nodok'],
        'kodeblok'=>$row['orgalokasi'],
        'kodebatch'=>''
    );
    $totalJumlah += $jumlah;
    $noUrut++;
}


# Detail (One)
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$dataH[0]['tanggal'],
    'nourut'=>$noUrut,
    'noakun'=>$dataH[0]['noakun'],
    'keterangan'=>$dataH[0]['keterangan'],
    'jumlah'=>$totalJumlah*(-1),
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>$dataH[0]['kodeorg'],
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>$dataH[0]['notransaksi'],
    'noaruskas'=>'',
    'kodevhc'=>'',
    'nodok'=>'',
    'kodeblok'=>'',
    'kodebatch'=>''
);

    #2. Data Detail Otomatis =======================================================
    # Detail (Many)
    $noUrut = 1;
    $totalJumlahOto = 0;
	
	
    foreach($dataD as $row) {
        if($row['hutangunit1']=='1') {
			
            $pembayarhutang=$param['kodeorg'];
            
            $pemilikhutang=$row['pemilikhutang'];
			  
            $noakunhutang=$row['noakunhutang'];
            $kodejurnal='M';
            $tanggal=$dataH[0]['tanggal'];
            $tanggal=tanggalnormal($tanggal);
            $tanggal=tanggalsystem($tanggal);
        
            #=============== Get Induk Pemilik Hutang
            $whereNomilhut = "kodeorganisasi='".
                $pemilikhutang."'";
            $query = selectQuery($dbname,'organisasi','induk',
                $whereNomilhut);
            $noKon = fetchData($query);
            $indukpemilikhutang = $noKon[0]['induk'];
            
            #=============== Get Induk Pembayar Hutang
            $whereNoyarhut = " kodeorganisasi='".$param['kodeorg']."'";
            $query = selectQuery($dbname,'organisasi','induk',$whereNoyarhut);
            $noKon = fetchData($query);
            $indukpembayarhutang = $noKon[0]['induk'];
            
            if($indukpemilikhutang==$indukpembayarhutang)$jenisinduk='intra'; else $jenisinduk='inter';
        
            #=============== Get Nomor Jurnal Otomatis (pemilikhutang)
        //    $whereNo = "kodekelompok='".$kodejurnal."' and kodeorg='".
        //        $pemilikhutang."'";
            $whereNoindukph = "kodekelompok='".$kodejurnal."' and kodeorg='".$indukpemilikhutang."'";
            $query = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',$whereNoindukph);
            $noKon = fetchData($query);
            $tmpC = $noKon[0]['nokounter'];
			//$tmpC++;
        
			$cek[$pemilikhutang]=$tmpC;
			$cek[$pemilikhutang]++;
			
			
			
			
            $konteroto = addZero($cek[$pemilikhutang],3);
            $nojuroto = $tanggal."/".
                $pemilikhutang."/".$kodejurnal."/".
                $konteroto;
               
				
			
            #=============== Get Nomor Akun Caco
            // ini ga dipake soale dipilih secara manual sama usernya pas nginput kasbank
            $whereNocaco = "jenis='".$jenisinduk."' and kodeorg='".
                $pemilikhutang."'";
            $query = selectQuery($dbname,'keu_5caco','akunpiutang',
                $whereNocaco);
            $noKon = fetchData($query);
            if(empty($noKon)) {
                exit("Warning: No Akun Intraco/Interco ".$pemilikhutang." belum ada");
            }
            $noakuncaco = $noKon[0]['akunpiutang'];
        
            #=============== Get Nomor Akun Caco Lawannya
            // ini yang dipake
            $whereNocacol = "jenis='".$jenisinduk."' and kodeorg='".
                $pembayarhutang."'";
            $query = selectQuery($dbname,'keu_5caco','akunpiutang',
                $whereNocacol);
            $noKon = fetchData($query);
            if(empty($noKon)) {
                exit("Warning: No Akun Intraco/Interco ".$pembayarhutang." belum ada");
            }
            $noakuncacol = $noKon[0]['akunpiutang'];
            
            if(substr($row['kode'],1,1)=='M') {
                $jumlah = $row['jumlah']*(-1);
            } else {
                $jumlah = $row['jumlah'];
            }
            $dKurs=1;
            $dMtUang='IDR';
            if($row['matauang']!='IDR')
            {
                //$dMtUang=$row['matauang'];
                $dKurs=$row['kurs'];
                $jumlah=$jumlah*$dKurs;
            }
			//cari total semua hutang dalam unit yg sama
			
			
			@$totDetOne[$nojuroto][$noakuncacol]['jumlah']+=$jumlah;
			$totDetOne[$nojuroto][$noakuncacol]['pemilikhutang']=$pemilikhutang;
		$totDetOne[$nojuroto][$noakuncacol]['akun']=$noakuncacol;
			 @$totDetOne[$nojuroto][$noakuncacol]['anak']++;
			
			$cariJur[$nojuroto]['jurnal']=$nojuroto;
			$cariJur[$nojuroto]['kode']=$kodejurnal;
            @$cariJur[$nojuroto]['jumlah']+=$jumlah;
			
            $dataResoto['detail'][] = array(
                'nojurnal'=>$nojuroto,
                'tanggal'=>$dataH[0]['tanggal'],
                'nourut'=>$totDetOne[$nojuroto][$noakuncacol]['anak'],
                'noakun'=>$noakunhutang,
                'keterangan'=>$row['keterangan2'],
                'jumlah'=>$jumlah,
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$pemilikhutang,
                'kodekegiatan'=>$row['kodekegiatan'],
                'kodeasset'=>$row['kodeasset'],
                'kodebarang'=>$row['kodebarang'],
                'nik'=>$row['nik'],
                'kodecustomer'=>$row['kodecustomer'],
                'kodesupplier'=>$row['kodesupplier'],
                'noreferensi'=>$pembayarhutang.$dataH[0]['notransaksi'],
                'noaruskas'=>$row['noaruskas'],
                'kodevhc'=>$row['kodevhc'],
                'nodok'=>$row['nodok'],
                'kodeblok'=>$row['orgalokasi'],
                'kodebatch'=>''
            );
            $totalJumlahOto += $jumlah;
                                
        }
    }
	
	if ($totalJumlahOto!=0){
	foreach($cariJur as $row=>$key) {
		foreach($totDetOne[$key['jurnal']] as $tot=>$key1) {
		//exit("Warning: No Akun Intraco/Interco ".$key1['anak']." belum ada");
		$dataResoto['detail'][] = array(
        'nojurnal'=>$key['jurnal'],
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$key1['anak']+1,
        'noakun'=>$key1['akun'],
        'keterangan'=>$dataH[0]['keterangan'],
        'jumlah'=>$key1['jumlah']*(-1),
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$key1['pemilikhutang'],
        'kodekegiatan'=>'',
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>'',
        'noreferensi'=>$pembayarhutang.$dataH[0]['notransaksi'],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>'',
        'kodebatch'=>''
    );
		}
	
	# Prep Header Otomatis =========================================================
                $dataResoto['header'][] = array(
                    'nojurnal'=>$key['jurnal'],
                    'kodejurnal'=>$key['kode'],
                    'tanggal'=>$dataH[0]['tanggal'],
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>'0',
                    'totaldebet'=>$key['jumlah'],
                    'totalkredit'=>$key['jumlah']*(-1),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$pembayarhutang.$dataH[0]['notransaksi'],
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1'

                );     
	}
	
	}
      
$konteroto++;
$dataRes['header']['totaldebet']=$totalJumlah; 
$dataRes['header']['totalkredit']=$totalJumlah*(-1);



#=== Insert Data ===
$errorDB = ""; 
//print_r($dataResoto['detail']);
//exit;
//exit("error". $dataResoto['header'].":");




        $queryH = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
        if(!mysql_query($queryH)) {
            $errorDB .= "Header : ".$key." :".mysql_error()."\n";
        }


# Header Otomatis ==============================================================
if($errorDB=='') {
	foreach($dataResoto['header'] as $key=>$dataHet) {
        $queryH = insertQuery($dbname,'keu_jurnalht',$dataHet);

        if(!mysql_query($queryH)) {
            $errorDB .= "Header oto : ".$key." :".mysql_error()."\n";
        }
    }

}
# Detail
if($errorDB=='') {
    foreach($dataRes['detail'] as $key=>$dataDet) {
        $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);

        if(!mysql_query($queryD)) {
            $errorDB .= "Detail ".$key." :".mysql_error()."\n";
			
        }
    }

    #=== Switch Jurnal to 1 ===
    # Cek if already posted
    $queryJ = selectQuery($dbname,'keu_kasbankht',"posting","notransaksi='".
        $param['notransaksi']."' and kodeorg='".$param['kodeorg']."'");
    $isJ = fetchData($queryJ);
    if($isJ[0]['posting']==1) {
        $errorDB .= "Data sudah di posting oleh user lain";
    } else {
        $queryToJ = updateQuery($dbname,'keu_kasbankht',array('posting'=>1),
            "notransaksi='".$dataH[0]['notransaksi']."' and kodeorg='".$dataH[0]['kodeorg']."' and tanggal='".$dataH[0]['tanggal']."'");
        if(!mysql_query($queryToJ)) {
            $errorDB .= "Posting Mark Error :".mysql_error()."\n";
        }
    }
}

# Detail Otomatis ==============================================================
 
    if($errorDB=='') {
        foreach($dataResoto['detail'] as $key=>$dataDet) {
            $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
            if(!mysql_query($queryD)) {
                $errorDB .= "Detail Otomatis ".$key." :".mysql_error().$queryD."\n";
            }
        }
    }    


if($errorDB!="") {
    // Rollback
    $where = "noreferensi LIKE '%".$dataH[0]['notransaksi']."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'keu_kasbankht',array('posting'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."' and kodeorg='".$dataH[0]['kodeorg']."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n";
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n";
    }
    
    
    echo "DB Error :\n".$errorDB;
    exit;
} else {
    // Posting Success
    #=== Add Counter Jurnal ===
    $queryJ = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']+1),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
    $errCounter = "";
    if(!mysql_query($queryJ)) {
        $errCounter.= "Update Counter Parameter Jurnal Error :".mysql_error()."\n";
    }
    if($errCounter!="") {
        $queryJRB = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
        $errCounter = "";
        if(!mysql_query($queryJRB)) {
            $errorJRB .= "Rollback Parameter Jurnal Error :".mysql_error()."\n";
        }
        echo "DB Error :\n".$errorJRB;
        exit;
    }
    #=== Add Counter Jurnal Otomatis === =======================================
if ($totalJumlahOto!=0){
        $queryJ = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konteroto),
            "kodeorg='".$indukpemilikhutang."' and kodekelompok='".$kodejurnal."'");
        $errCounter = "";
        if(!mysql_query($queryJ)) {
            $errCounter.= "Update Counter Parameter Jurnal Error :".mysql_error()."\n";
        }
        
        if($errCounter!="") {
            $queryJRB = updateQuery($dbname,'keu_5kelompokjurnal',array($noKon[0]['nokounter']),
                "kodeorg='".$indukpemilikhutang."' and kodekelompok='".$kodejurnal."'");
            $errCounter = "";
            if(!mysql_query($queryJRB)) {
                $errorJRB .= "Rollback Parameter Jurnal Error :".mysql_error()."\n";
            }
            echo "DB Error :\n".$errorJRB;
            exit;
        }
    }    
}
?>