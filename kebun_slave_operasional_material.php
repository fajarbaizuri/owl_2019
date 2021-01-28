<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
foreach($param as $key=>$row) {
	if(substr($key,0,23)=='ftPrestasi_kodekegiatan') {
		$param['kodekegiatan'] = $row;
		unset($param[$key]);
	}
	if(substr($key,0,18)=='ftPrestasi_kodeorg') {
		$param['kodeorg'] = $row;
		unset($param[$key]);
	}
}
switch($proses) {
    case 'add':
		# Kegiatan harus ada
		$qKeg = selectQuery($dbname,'kebun_prestasi','*',"notransaksi='".$param['notransaksi']."'");
		$resKeg = fetchData($qKeg);
		if(empty($resKeg)) {
			echo 'Warning : Kegiatan harus diisi lebih dahulu';
			exit;
		}
		
		# Set Kolom dan Extract Data
		$cols = array(
			'kodebarang','kwantitas','kwantitasha','hargasatuan','notransaksi','tanggal',
			'kodekegiatan','kodeorg'
		);
		$data = $param;
		unset($data['numRow']);
		//$data['hargasatuan'] = 0;
		//$data['hargasatuan'] = $param['harga'];
		$data['tanggal'] = tanggalsystem($param['tanggal']);
		//print_r($data); 
		//echo "DB Error : ".$data;
		//	exit;
		# Barang harus ada
		if($data['kodebarang']=='' or $data['kodebarang']=='0') {
			echo 'Warning : Barang harus diisi';
			exit;
		}
		
		# Cek Ha
		
		//	$theHa = makeOption($dbname,'setup_blok','kodeorg,luasareaproduktif',
		//	"kodeorg='".$param['kodeorg']."'");
		//if(strlen(trim($data['kodeorg']))==6)
		//	{
		//		
		//	}
		//	else if($data['kwantitasha']>$theHa[$data['kodeorg']]) {
		//	echo "Validation Error : Ha harus lebih kecil dari Luas produktif Blok:".$data['kodeorg'];
		//	exit;
		//}
		
		# Insert
		$query = insertQuery($dbname,'kebun_pakaimaterial',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['notransaksi']);
		//unset($data['hargasatuan']);
		unset($data['tanggal']);
		unset($data['kodekegiatan']);
		unset($data['kodeorg']);
		$res = "";
		foreach($data as $cont) {
			$res .= "##".$cont;
		}
		
		$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
		echo $result;
		break;
    case 'edit':
		$data = $param;
		unset($data['notransaksi']);
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}
		$data['tanggal'] = tanggalsystem($param['tanggal']);
		
		# Cek Ha
		$theHa = makeOption($dbname,'setup_blok','kodeorg,luasareaproduktif',
			"kodeorg='".$param['kodeorg']."'");
		if($data['kwantitasha']>$theHa[$data['kodeorg']]) {
			echo "Validation Error : Ha harus lebih kecil dari Luas produktif Blok";
			exit;
		}
		
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['kodekegiatan']."' and kodeorg='".$param['kodeorg'].
			"' and kodebarang='".$param['cond_kodebarang']."'";
		$query = updateQuery($dbname,'kebun_pakaimaterial',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['kodekegiatan']."' and kodeorg='".$param['kodeorg'].
			"' and kodebarang='".$param['kodebarang']."'";
		$query = "delete from `".$dbname."`.`kebun_pakaimaterial` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
    break;
}
?>