<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
		$data = array(
			'kebun'=>$param['kebun'],
			'afdeling'=>$param['afdeling'],
			'kodebarang'=>$param['barang'],
			'kuantitas'=>$param['kuantitas'],
			'kodegudang'=>$param['gudang']
		);
		$query = insertQuery($dbname,'log_gudangdivisi',$data);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'edit':
		$where = "kodegudang='".$param['oldGudang']."' and kebun='".$param['oldKebun']."'".
			" and afdeling='".$param['oldAfdeling']."' and kodebarang='".$param['oldBarang']."'";
		$data = array(
			'kebun'=>$param['kebun'],
			'afdeling'=>$param['afdeling'],
			'kodebarang'=>$param['barang'],
			'kuantitas'=>$param['kuantitas'],
			'kodegudang'=>$param['gudang']
		);
		
		$query = updateQuery($dbname,'log_gudangdivisi',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
		$where = "kodegudang='".$param['gudang']."' and kebun='".$param['kebun']."'".
			" and afdeling='".$param['afdeling']."' and kodebarang='".$param['barang']."'";
		$query = deleteQuery($dbname,'log_gudangdivisi',$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    default:
		break;
}
?>