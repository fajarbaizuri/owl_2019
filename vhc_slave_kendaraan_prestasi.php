<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
	
		if ($param['satuk']==""){
			echo "Error:Silahkan Pilih Waktu Operasional";
			exit;
		} 
		if ($param['kodekegiatan']==""){
			echo "Error:Silahkan Pilih Kode Kegiatan";
			exit;
		} 
		if ($param['total'] <= 0){
			echo "Error:Silahkan Entri Waktu Awal dan Akhir dengan Benar..";
			exit;
		} 
		$whereOrgB = "notransaksi ='".$param['notransaksi']."' ";
		$optVHC = makeOption($dbname,'vhc_kendaraanht','notransaksi,kodevhc',$whereOrgB);	
		$has=0;
		if ($param['satuk']=="JAM"){
			$has=CheckNilaiCon($optVHC[$param['notransaksi']],substr($param['kodekegiatan'],0,4),$param['kodekegiatan'],$dbname);
			if ($has==NULL){
			echo "Error:Silahkan Masukan Konversi Alat Terlebih Dahulu..";
			exit;
			}
		}
		$whereOrgA = "kelompok in ('AB','KD') ";
		$optKeg = makeOption($dbname,'vhc_kegiatan','kodekegiatan,satuan',$whereOrgA);	
		$cols = array(
		'satuk','lokasi','kodekegiatan','volume','awal','akhir','total','notransaksi','konversi','satkon','satuan','nilaikon','rit'
		);
		$data = $param;
		unset($data['numRow']);	
		unset($data['rit']);
		# Additional Default Data
		if ($param['satuk']=="JAM"){
			$data['konversi'] = '1';
		}else{
			$data['konversi'] = '0';
		}	
		
		if (substr($param['kodekegiatan'],0,2)=="DK"){
			$data['satkon'] = 'KM/H';
		}else{
			$data['satkon'] = 'HM';
		}
		$data['satuan'] = $optKeg[$param['kodekegiatan']];
		$data['nilaikon'] = $has;
		
		$data['rit']= CheckCountRit($param['notransaksi'],$param['kodekegiatan'],$dbname);
		
		
		$query = insertQuery($dbname,'vhc_kendaraan_kegiatan',$data,$cols);
		if(!mysql_query($query)) {
			print_r($data).print_r($cols);
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['notransaksi']);
		unset($data['nilaikon']);
		
		unset($data['konversi']);unset($data['satkon']);unset($data['satuan']);
		
		
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
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['cond_kodekegiatan']."' and rit='".$param['cond_rit']."'";
		$query = updateQuery($dbname,'vhc_kendaraan_kegiatan',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['kodekegiatan']."' and rit='".$param['rit']."'";
		$query = "delete from `".$dbname."`.`vhc_kendaraan_kegiatan` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
    break;
}
function CheckNilaiCon($a,$b,$c,$dbname){
	$queryKaryA = "select nilai from `".$dbname."`.`vhc_konversi` where kodevhc='".$a."' and kodeorg='".$b."' and kodekegiatan='".$c."'";
	$query = fetchData($queryKaryA);
	return $query['nilai'];
}
function CheckCountRit($a,$b,$dbname){
	$queryKaryA = "select notransaksi from `".$dbname."`.`vhc_kendaraan_kegiatan` where notransaksi='".$a."' and kodekegiatan='".$b."' ";
		$queryA = mysql_query($queryKaryA);
		return mysql_num_rows($queryA)+1;
}
?>