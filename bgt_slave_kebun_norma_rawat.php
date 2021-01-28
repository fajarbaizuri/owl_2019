<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
		if($param['tipeanggaran1']!='VHC') {
			$param['penggunaan']='';
		}
		$data = array(
			'kodeorg'=>$param['afdeling'],
			'tahuntanam'=>$param['tahuntanam'],
			'kodekegiatan'=>$param['kodekegiatan1'],
			'kelompok'=>$param['kelompok1'],
			'tipeanggaran'=>$param['tipeanggaran1'],
			'kodebarang'=>$param['kodebarang1'],
			'topografi'=>$param['topografi1'],
			'jumlah'=>$param['jumlah1'],
			'satuan'=>$param['satuan']
		);
		
		## Validate
		if(substr($param['tipeanggaran1'],0,1)=='M' and $param['kodebarang1']=="0") {
			exit("Warning: Untuk Budget Material, Barang harus dipilih");
		}
		
		# Get Cols
		$cols = array();
		foreach($data as $key=>$row) {
			$cols[] = $key;
		}
		
		# Insert
		$query = insertQuery($dbname,'setup_kegiatannorma',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		$res = "";
		foreach($data as $cont) {
			$res .= "##".$cont;
		}
		
		$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
		echo $result;
		break;
    case 'edit':
		if($param['tipeanggaran1']!='VHC') {
			$param['penggunaan']='';
		}
		$data = $param;
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}
		
		## Validate
		if(substr($param['tipeanggaran1'],0,1)=='M' and $param['kodebarang1']=="0") {
			exit("Warning: Untuk Budget Material, Barang harus dipilih");
		}
		
		$data = array(
			'kodeorg'=>$param['afdeling'],
			'tahuntanam'=>$param['tahuntanam'],
			'kodekegiatan'=>$param['kodekegiatan1'],
			'kelompok'=>$param['kelompok1'],
			'tipeanggaran'=>$param['tipeanggaran1'],
			'kodebarang'=>$param['kodebarang1'],
			'topografi'=>$param['topografi1'],
			'jumlah'=>$param['jumlah1'],
			'satuan'=>$param['satuan']
		);
		
		$where = "kodeorg='".$param['cond_afdeling']."' and kelompok='".
			$param['cond_kelompok1']."' and kodekegiatan='".$param['cond_kodekegiatan1'].
			"' and tipeanggaran='".$param['cond_tipeanggaran1']."' and kodebarang='".
			$param['cond_kodebarang1']."' and topografi='".$param['cond_topografi1'].
			"' and tahuntanam='".$param['cond_tahuntanam']."'";
		$query = updateQuery($dbname,'setup_kegiatannorma',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "kodeorg='".$param['afdeling']."' and kelompok='".
			$param['kelompok1']."' and kodekegiatan='".$param['kodekegiatan1'].
			"' and tipeanggaran='".$param['tipeanggaran1']."' and kodebarang='".
			$param['kodebarang1']."' and topografi='".$param['topografi1'].
			"' and tahuntanam='".$param['tahuntanam']."'";
		$query = "delete from `".$dbname."`.`setup_kegiatannorma` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
	case 'changeKlp':
                                      if($param['kelompok']=='TM')
                                      {
		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
			"kelompok in('".$param['kelompok']."','PNN')",'1');
                                      }
                                      else
                                      {
 		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
			"kelompok ='".$param['kelompok']."'",'1');                                         
                                      }
		
		$res = $optKeg;
		echo json_encode($res);
		break;
	case 'changeSatuan':
		$satuan='';
		if($param['tipe']=='VHC') {
			$satuan='HM-KM';
		} elseif(substr($param['tipe'],0,4)=='SDM-') {
			$satuan='HK';
		}
		
		$qSatuan = selectQuery($dbname,'setup_kegiatan','kodekegiatan,satuan',
			"kodekegiatan='".$param['kodekegiatan']."'");
		$resSatuan = fetchData($qSatuan);
		
		if($satuan!='') {
			$satuan.='/';
		}
		
		if(empty($resSatuan)) {
			echo $satuan;
		} else {
			echo $satuan.$resSatuan[0]['satuan'];
		}
		break;
	case 'changeTipe':
		if($param['tipe']=='VHC') {
			$optBarang = makeOption($dbname,'vhc_5master','kodevhc,kodevhc');
		} elseif(substr($param['tipe'],0,2)=='M-') {
			$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
				"kelompokbarang='".substr($param['tipe'],2,3)."'",'1');
		}
		
		$res = $optBarang;
		echo json_encode($res);
		break;
    default:
    break;
}
?>