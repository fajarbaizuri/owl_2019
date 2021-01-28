<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

# Get Attr
$proses = $_GET['proses'];
$data = $_POST;

switch($proses) {
    case 'add':
	#=============== Get Nomor Jurnal
	$whereNo = "kodekelompok='".$data['kodejurnal']."' and kodeorg='".
	    $_SESSION['org']['kodeorganisasi']."'";
	$query = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
	    $whereNo);
	$noKon = fetchData($query);
	$tmpC = $noKon[0]['nokounter'];
	$tmpC++;
	$counter = addZero($tmpC,3);
	$data['nojurnal'] = tanggalsystem($data['tanggal'])."/".
	    $_SESSION['empl']['lokasitugas']."/".$data['kodejurnal']."/".
	    $counter;
	$nojur = $data['nojurnal'];
	
	#=============== Insert Process
	# Column
	$column = array('kodejurnal','tanggal','noreferensi','matauang',
	    'nojurnal','tanggalentry','posting','totaldebet','totalkredit',
	    'amountkoreksi','autojurnal','kurs');
	
	# Add Default Data
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['tanggalentry'] = date('Ymd');
	$data['posting'] = 0;
	$data['totaldebet'] = 0;
	$data['totalkredit'] = 0;
	$data['amountkoreksi'] = 0;
	$data['autojurnal'] = 0;
	$data['kurs'] = 0;
	
	# Query
	$query = insertQuery($dbname,'keu_jurnalht',$data,$column);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    $updData = array('nokounter'=>$tmpC);
	    $query2 = updateQuery($dbname,'keu_5kelompokjurnal',$updData,$whereNo);
	    if(!mysql_query($query2)) {
		echo "DB Error : ".mysql_error();
	    } else {
		echo $nojur;
	    }
	}
	break;
    case 'edit':
	$data = $_POST;
	unset($data['nojurnal']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	
	$query = updateQuery($dbname,'keu_jurnalht',$data,"nojurnal='".$_POST['nojurnal']."'");
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    $data['tanggal'] = tanggalnormal($data['tanggal']);
	    echo json_encode($data);
	}
	break;
    case 'delete':
	$query = selectQuery($dbname,'keu_jurnaldt','nojurnal',"nojurnal='".$data['nojurnal']."'");
	$res = fetchData($query);
	if(empty($res)) {
	    $qDel = "delete from `".$dbname."`.`keu_jurnalht` where nojurnal='".$data['nojurnal']."'";
	    echo $qDel;
	    if(!mysql_query($qDel)) {
		echo "DB Error : ".mysql_error();
		exit;
	    }
	} else {
	    echo "Warning : Detail Jurnal tidak kosong. Header tidak dapat dihapus";
	    exit;
	}
    default:
	break;
}
?>