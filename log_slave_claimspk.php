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
			"where a.notransaksi='".$param['nospk']."'";
        $resBAPP = fetchData($qBAPP);
        
		# List Claim SPK
		$qClaim = selectQuery($dbname,'log_claimspk','*',"nospk='".$param['nospk']."' and posting=0");
		$resClaim = fetchData($qClaim);
		$dataClaim = array();
		foreach($resClaim as $row) {
			$dataClaim[$row['nospk']][$row['blokbapp']][$row['kodekegiatan']][$row['tanggalspk']] = $row;
		}
		
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
			if(isset($dataClaim[$row['notransaksi']][$row['kodeblok']][$row['kodekegiatan']][$row['tanggal']])) {
				$row['tanggalretur'] = $dataClaim[$row['notransaksi']][$row['kodeblok']][$row['kodekegiatan']][$row['tanggal']]['tanggalretur'];
				$row['kuantitasretur'] = $dataClaim[$row['notransaksi']][$row['kodeblok']][$row['kodekegiatan']][$row['tanggal']]['kuantitasretur'];
				$row['nilairetur'] = $dataClaim[$row['notransaksi']][$row['kodeblok']][$row['kodekegiatan']][$row['tanggal']]['nilairetur'];
			} else {
				$row['tanggalretur'] = '';
				$row['kuantitasretur'] = '';
				$row['nilairetur'] = '';
			}
			$tab .= "<tr class=rowcontent>";
			$tab .= "<td id='notransaksi_".$key."' value='".$row['notransaksi']."'>".$row['notransaksi']."</td>";
			$tab .= "<td id='kodeblok_".$key."' value='".$row['kodeblok']."'>".$optBlok[$row['kodeblok']]."</td>";
			$tab .= "<td id='kodekegiatan_".$key."' value='".$row['kodekegiatan']."'>".$optKeg[$row['kodekegiatan']]."</td>";
			$tab .= "<td id='tanggal1_".$key."' value='".tanggalnormal($row['tanggal'])."'>".tanggalnormal($row['tanggal'])."</td>";
			$tab .= "<td id='kuantitas1_".$key."' value='".$row['hasilkerjarealisasi']."' align=right>".
				number_format($row['hasilkerjarealisasi'],0)."</td>";
			$tab .= "<td id='nilai1_".$key."' value='".$row['jumlahrealisasi']."' align=right>".
				number_format($row['jumlahrealisasi'],0)."</td>";
			if($row['tanggalretur']=='') {
				$tab .= "<td id='tanggal2_".$key."'>".makeElement('tanggal2El_'.$key,'date','')."</td>";
			} else {
				$tab .= "<td id='tanggal2_".$key."'>".makeElement('tanggal2El_'.$key,'date',tanggalnormal($row['tanggalretur']))."</td>";
			}
			$tab .= "<td id='kuantitas2_".$key."'>".makeElement('kuantitas2El_'.$key,'textnum',$row['kuantitasretur'])."</td>";
			$tab .= "<td id='nilai2_".$key."'>".makeElement('nilai2El_'.$key,'textnum',$row['nilairetur'])."</td>";
			$tab .= "<td><img id='save_".$key."' src='images/".$_SESSION['theme']."/save.png'".
				" onclick='save(".$key.")' style='cursor:pointer'></td>";
			$tab .= "</tr>";
		}
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        echo $tab;
        break;
    case 'save':
        // Check if data exist
		$where = "nospk='".$param['nospk']."' and blokspk='".$param['blok'].
			"' and blokbapp='".$param['blok']."' and kodekegiatan='".
			$param['kodekegiatan']."' and tanggalspk='".tanggalsystem($param['tanggalspk']).
			"' and posting=0";
		$qSel = selectQuery($dbname,'log_claimspk',"*",$where);
		$resSel = fetchData($qSel);
		
		if(empty($resSel)) {
			// Insert
			$dIns = array(
				'nospk'=>$param['nospk'],
				'blokspk'=>$param['blok'],
				'blokbapp'=>$param['blok'],
				'kodekegiatan'=>$param['kodekegiatan'],
				'tanggalspk'=>tanggalsystem($param['tanggalspk']),
				'tanggalretur'=>tanggalsystem($param['tanggalretur']),
				'kuantitasretur'=>$param['kuantitasretur'],
				'nilairetur'=>$param['nilairetur'],
				'posting'=>0
			);
			$qIns = insertQuery($dbname,'log_claimspk',$dIns);
			if(!mysql_query($qIns)) {
				exit("DB Error: ".mysql_error());
			}
		} else {
			// Update
			$dUpd = array(
				'tanggalretur'=>tanggalsystem($param['tanggalretur']),
				'kuantitasretur'=>$param['kuantitasretur'],
				'nilairetur'=>$param['nilairetur']
			);
			$qUpd = updateQuery($dbname,'log_claimspk',$dUpd,$where);
			if(!mysql_query($qUpd)) {
				exit("DB Error: ".mysql_error());
			}
		}
		break;
    default:
}
?>