<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
?>

<?

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'add':
		/*$col = "waktu,deg1hour,deg1amp,deg1c,deg2hour,deg2amp,deg2c,deg3hour,".
			"deg3amp,deg3c,press1hour,press1amp,press1c,press1tekanan,press2hour,".
			"press2amp,press2c,press2tekanan,press3hour,press3amp,press3c,".
			"press3tekanan,catatan,notransaksi";*/
			
		$col = "waktu,deg1hour,deg1amp,deg1c,deg2hour,deg2amp,deg2c,deg3hour,".
			"deg3amp,deg3c,press1hour,press1amp,press1c,press1tekanan,press2hour,".
			"press2amp,press2c,press2tekanan,press3hour,press3amp,press3c,".
			"press3tekanan,deg4hour,deg4amp,deg4c,deg5hour,deg5amp,deg5c,deg6hour,deg6amp,deg6c,".
			"press4hour,press4amp,press4c,press4tekanan,press5hour,press5amp,press5c,press5tekanan,".
			"press6hour,press6amp,press6c,press6tekanan,catatan,notransaksi";
			
		$cols = explode(',',$col);
		$data = $param;
		unset($data['numRow']);
		unset($data['tanggal']);
		$data['waktu'] = tanggalsystem($param['tanggal']).
			str_replace(':','',$param['waktu']);
		
		$query = insertQuery($dbname,'pabrik_ops_pressandtb',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		$data['waktu'] = $param['waktu'];
		unset($data['notransaksi']);
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
		
		$data['waktu'] = tanggalsystem($param['tanggal']).
			str_replace(':','',$param['waktu']);
		$waktuCond = tanggalsystem($param['tanggal']).
			str_replace(':','',$param['cond_waktu']);
		unset($data['tanggal']);
		
		$where = "notransaksi='".$param['notransaksi']."' and waktu='".
			$waktuCond."'";
		$query = updateQuery($dbname,'pabrik_ops_pressandtb',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		echo json_encode($param);
		break;
	case 'delete':
		$waktuCond = tanggalsystem($param['tanggal']).
			str_replace(':','',$param['waktu']);
		$where = "notransaksi='".$param['notransaksi']."' and waktu='".
			$waktuCond."'";
		$query = "delete from `".$dbname."`.`pabrik_ops_pressandtb` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
	break;
}
?>