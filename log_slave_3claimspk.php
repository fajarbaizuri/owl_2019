<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$proses = $_GET['proses'];

switch($proses) {
    case 'listBAPP':
        ## Get Data
        # List BAPP
        $qBAPP = "select a.notransaksi,a.blokspkdt,a.kodeblok,a.kodekegiatan,".
			"a.tanggal,a.hasilkerjarealisasi,a.jumlahrealisasi,b.tanggalretur,".
			"b.kuantitasretur,b.nilairetur from ".$dbname.
			".log_baspk a left join ".$dbname.".log_claimspk b on ".
			"a.notransaksi=b.nospk and ".
			"a.blokspkdt=b.blokspk and ".
			"a.kodeblok=b.blokbapp and ".
			"a.kodekegiatan=b.kodekegiatan and ".
			"a.tanggal=b.tanggalspk ".
			"where a.notransaksi='".$param['nospk']."' and b.posting=0";
        $resBAPP = fetchData($qBAPP);
        
        # Options
        $optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
        $optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"tipe='BLOK' or tipe='AFDELING'");
        
        ## Rearrange Data
        $data = $resBAPP;
        
        ## Header
        $tab = "<table class='data' border=1 cellspacing=0>";
        $tab .= "<thead><tr class='rowheader'>";
        $tab .= "<td rowspan=2>No SPK</td>";
        $tab .= "<td rowspan=2>Sub Unit</td>";
        $tab .= "<td rowspan=2>Kegiatan</td>";
		$tab .= "<td colspan=3>Realisasi</td>";
		$tab .= "<td colspan=3>Retur</td>";
		$tab .= "<td rowspan=2>Aksi</td></tr>";
		$tab .= "<tr class='rowheader'><td>Tanggal</td>";
		$tab .= "<td>Kuantitas</td>";
        $tab .= "<td>Nilai</td>";
        $tab .= "<td>Tanggal</td>";
        $tab .= "<td>Kuantitas</td>";
        $tab .= "<td>Nilai</td>";
        $tab .= "</tr></thead>";
        
		## Content
        $tab .= "<tbody>";
        foreach($data as $key=>$row) {
			$tab .= "<tr class=rowcontent>";
			$tab .= "<td id='notransaksi_".$key."' value='".$row['notransaksi']."'>".$row['notransaksi']."</td>";
			$tab .= "<td id='kodeblok_".$key."' value='".$row['kodeblok']."'>".$optBlok[$row['kodeblok']]."</td>";
			$tab .= "<td id='kodekegiatan_".$key."' value='".$row['kodekegiatan']."'>".$optKeg[$row['kodekegiatan']]."</td>";
			$tab .= "<td id='tanggal1_".$key."' value='".tanggalnormal($row['tanggal'])."'>".tanggalnormal($row['tanggal'])."</td>";
			$tab .= "<td id='kuantitas1_".$key."' value='".$row['hasilkerjarealisasi']."' align=right>".
				number_format($row['hasilkerjarealisasi'],0)."</td>";
			$tab .= "<td id='nilai1_".$key."' value='".$row['jumlahrealisasi']."' align=right>".
				number_format($row['jumlahrealisasi'],0)."</td>";
			$tab .= "<td id='tanggal2_".$key."' value='".tanggalnormal($row['tanggalretur'])."'>".tanggalnormal($row['tanggalretur'])."</td>";
			$tab .= "<td id='kuantitas2_".$key."' value='".$row['kuantitasretur']."' align=right>".
				number_format($row['kuantitasretur'],0)."</td>";
			$tab .= "<td id='nilai2_".$key."' value='".$row['nilairetur']."' align=right>".
				number_format($row['nilairetur'],0)."</td>";
			$tab .= "<td>".makeElement('post_'.$key,'btn','Posting',array('onclick'=>'post('.$key.')'))."</td>";
			$tab .= "</tr>";
		}
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        echo $tab;
        break;
    case 'post':
        // Check if data exist
		$where = "nospk='".$param['nospk']."' and blokspk='".$param['blok'].
			"' and blokbapp='".$param['blok']."' and kodekegiatan='".
			$param['kodekegiatan']."' and tanggalspk='".tanggalsystem($param['tanggalspk']).
			"' and posting=0";
		$qSel = selectQuery($dbname,'log_claimspk',"*",$where);
		$resSel = fetchData($qSel);
		$data = $resSel;
		
		// Get SPK
		$qSPK = selectQuery($dbname,'log_spkht',"*","notransaksi='".$param['nospk']."'");
		$resSPK = fetchData($qSPK);
		
		############################################################ [Jurnal] ##
		# Get Akun
		$kodeJurnal = 'SPK1';
		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,noakun',
			"kodekegiatan='".$param['kodekegiatan']."'");
		$optSupp = makeOption($dbname,'log_5klsupplier','kode,noakun',
			"kode='".substr($resSPK[0]['koderekanan'],0,4)."'");
		
		# Get Journal Counter
		$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
			"kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
		$tmpKonter = fetchData($queryJ);
		$konter = addZero($tmpKonter[0]['nokounter']+1,3);
		
		# Transform No Jurnal dari No Transaksi
		$tgl = tanggalsystem($param['tanggalretur']);
		$nojurnal = $tgl."/".substr($data[0]['blokbapp'],0,4)."/".$kodeJurnal."/".$konter;
		
		# No Referensi
		$noRef = $param['nospk'].'/'.$tgl;
		
		# Prep Header
		$dataRes['header'] = array(
			'nojurnal'=>$nojurnal,
			'kodejurnal'=>$kodeJurnal,
			'tanggal'=>$tgl,
			'tanggalentry'=>date('Ymd'),
			'posting'=>0,
			'totaldebet'=>$data[0]['nilairetur'],
			'totalkredit'=>(-1)*$data[0]['nilairetur'],
			'amountkoreksi'=>'0',
			'noreferensi'=>$noRef,
			'autojurnal'=>'1',
			'matauang'=>'IDR',
			'kurs'=>'1'
		);
		
		# Data Detail
		$noUrut = 1;
		
		# Debet
		$dataRes['detail'][] = array(
			'nojurnal'=>$nojurnal,
			'tanggal'=>$tgl,
			'nourut'=>$noUrut,
			'noakun'=>$optKeg[$param['kodekegiatan']],
			'keterangan'=>'Retur SPK '.substr($data[0]['blokbapp'],0,4).'/'.$param['nospk'],
			'jumlah'=>$data[0]['nilairetur'],
			'matauang'=>'IDR',
			'kurs'=>'1',
			'kodeorg'=>substr($data[0]['blokbapp'],0,4),
			'kodekegiatan'=>$param['kodekegiatan'],
			'kodeasset'=>'',
			'kodebarang'=>'',
			'nik'=>'',
			'kodecustomer'=>'',
			'kodesupplier'=>'',
			'noreferensi'=>$noRef,
			'noaruskas'=>'',
			'kodevhc'=>'',
			'nodok'=>'',
			'kodeblok'=>$data[0]['blokbapp'],
			'kodebatch'=>''
		);
		$noUrut++;
		
		# Kredit
		$dataRes['detail'][] = array(
			'nojurnal'=>$nojurnal,
			'tanggal'=>$tgl,
			'nourut'=>$noUrut,
			'noakun'=>$optSupp[substr($resSPK[0]['koderekanan'],0,4)],
			'keterangan'=>'Retur SPK '.substr($data[0]['blokbapp'],0,4).'/'.$param['nospk'],
			'jumlah'=>-1*$data[0]['nilairetur'],
			'matauang'=>'IDR',
			'kurs'=>'1',
			'kodeorg'=>substr($data[0]['blokbapp'],0,4),
			'kodekegiatan'=>$param['kodekegiatan'],
			'kodeasset'=>'',
			'kodebarang'=>'',
			'nik'=>'',
			'kodecustomer'=>'',
			'kodesupplier'=>$resSPK[0]['koderekanan'],
			'noreferensi'=>$noRef,
			'noaruskas'=>'',
			'kodevhc'=>'',
			'nodok'=>'',
			'kodeblok'=>$data[0]['blokbapp'],
			'kodebatch'=>''
		);
		$noUrut++;
		
		#>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Header
		$headErr = '';
		
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
					#>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update BAPP & Posting Status
					// Get Data BAPP
					$whereBAPP = "notransaksi='".$param['nospk']."' and blokspkdt='".$param['blok'].
						"' and kodeblok='".$param['blok']."' and kodekegiatan='".
						$param['kodekegiatan']."' and tanggal='".tanggalsystem($param['tanggalspk'])."'";
					$qBAPP = selectQuery($dbname,'log_baspk','*',$whereBAPP);
					$resBAPP = fetchData($qBAPP);
					
					// Update BAPP
					$dUpd = array(
						'hasilkerjarealisasi'=>$resBAPP[0]['hasilkerjarealisasi']-$param['kuantitasretur'],
						'jumlahrealisasi'=>$resBAPP[0]['jumlahrealisasi']-$param['nilairetur']
					);
					$qUpd = updateQuery($dbname,'log_baspk',$dUpd,$whereBAPP);
					if(!mysql_query($qUpd)) {
						exit("DB Error: ".mysql_error());
					}
					
					// Update Status Posting
					$dUpd = array('posting'=>1);
					$qUpd = updateQuery($dbname,'log_claimspk',$dUpd,$where);
					if(!mysql_query($qUpd)) {
						echo "Update Status Jurnal Error : ".mysql_error()."\n";
						# Rollback if Update Failed
						$RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
						if(!mysql_query($RBDet)) {
							echo "Rollback Delete Header Error : ".mysql_error()."\n";
							exit;
						}
						$RBJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter-1),
							"kodeorg='".$_SESSION['org']['kodeorganisasi'].
							"' and kodekelompok='".$kodeJurnal."'");
						if(!mysql_query($RBJurnal)) {
							echo "Rollback Update Jurnal Error : ".mysql_error()."\n";
							exit;
						}
						$dataRB = array(
							'hasilkerjarealisasi'=>$resBAPP[0]['hasilkerjarealisasi'],
							'jumlahrealisasi'=>$resBAPP[0]['jumlahrealisasi']
						);
						$RBBAPP = updateQuery($dbname,'log_baspk',$dataRB,$whereBAPP);
						if(!mysql_query($RBBAPP)) {
							echo "Rollback Update BAPP Error : ".mysql_error()."\n";
							exit;
						}
						exit;
					}
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
		########################################################### [/Jurnal] ##
		
		
		break;
    default:
}
?>