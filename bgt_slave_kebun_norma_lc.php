<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
		$data = $param;
		unset($data['numRow']);
		
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
		$data = $param;
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}
		
		$where = "kodeorg='".$param['cond_kodeorg']."' and kelompok='".
			$param['cond_kelompok']."' and kodekegiatan='".$param['cond_kodekegiatan'].
			"' and tipeanggaran='".$param['cond_tipeanggaran']."' and kodebarang='".
			$param['cond_kodebarang']."' and topografi='".$param['cond_topografi']."' and tahuntanam=0";
		$query = updateQuery($dbname,'setup_kegiatannorma',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "kodeorg='".$param['kodeorg']."' and kelompok='".
			$param['kelompok']."' and kodekegiatan='".$param['kodekegiatan'].
			"' and tipeanggaran='".$param['tipeanggaran']."' and kodebarang='".
			$param['kodebarang']."' and topografi='".$param['topografi']."' and tahuntanam=0";
		$query = "delete from `".$dbname."`.`setup_kegiatannorma` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
	case 'changeBlok':
		# Get Kelompok Status Tanam
		$query = selectQuery($dbname,'setup_blok','statusblok',"kodeorg='".$param['kodeorg']."'");
		$resData = fetchData($query);
		
		$optKlp = makeOption($dbname,'kebun_5sttanam','kode,keterangan',
			"kode = '".$resData[0]['statusblok']."'");
		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
			"kelompok ='".$resData[0]['statusblok']."'",'1');
		
		$res = array(
			'klp'=>$optKlp,
			'keg'=>$optKeg
		);
		echo json_encode($res);
		break;
    default:
    break;
}
?>