<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'add':
		$col = "mesin,hariini,sdhi,sejakbaru,sejakrebuild,notransaksi";
		$cols = explode(',',$col);
		$data = $param;
		unset($data['numRow']);
		
		$query = insertQuery($dbname,'pabrik_ops_presandta',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
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
		
		$where = "notransaksi='".$param['notransaksi']."' and mesin='".
			$param['cond_mesin']."'";
		$query = updateQuery($dbname,'pabrik_ops_presandta',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		echo json_encode($param);
		break;
	case 'delete':
		$where = "notransaksi='".$param['notransaksi']."' and mesin='".
			$param['mesin']."'";
		$query = "delete from `".$dbname."`.`pabrik_ops_presandta` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
	break;
}
?>