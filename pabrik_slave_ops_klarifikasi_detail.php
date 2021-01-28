<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
		# Get Data
		$where = "notransaksi='".$param['notransaksi']."'";
		$cols = "time(waktu) as waktu,vaccumtemp,vaccumtekanan,purifier1temp,".
			"purifier1kap,purifier2temp,purifier2kap,decanteramp,decantertemp,".
			"decanterumpan,`separator`,cst1temp,cst1volume,cst2temp,cst2volume,".
			"slodgetemp,slodgetvolume,oiltemp,oilvolume,catatan";
		$colArr = array_merge(array('waktu'),explode(',',$cols));
		unset($colArr[1]);
		$query = selectQuery($dbname,'pabrik_ops_klarifikasidt',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		
		# Form
		$theForm2 = new uForm('transForm','Form Klarifikasi',3);
		$theForm2->addEls('waktu',$_SESSION['lang']['waktu'],'','jammenit','L',10);
		foreach($colArr as $c) {
			if($c != 'waktu' and $c != 'catatan') {
				$c = str_replace('`','',$c);
				$theForm2->addEls($c,$_SESSION['lang'][$c],'0','textnum','L',10);
			}
		}
		$theForm2->addEls('catatan',$_SESSION['lang']['catatan'],'','text','L',45);
		
		# Table
		$theTable2 = new uTable('transTable','Tabel Klarifikasi',$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('transFT',$theForm2,$theTable2,null,
			array('notransaksi','tanggal'));
		$formTab2->_target = "pabrik_slave_ops_klarifikasi_detail";
		//$formTab2->_numberFormat = '##nilai';
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		$formTab2->render();
		echo "</fieldset>";
		break;
	case 'add':
		$col = "waktu,vaccumtemp,vaccumtekanan,purifier1temp,".
			"purifier1kap,purifier2temp,purifier2kap,decanteramp,decantertemp,".
			"decanterumpan,separator,cst1temp,cst1volume,cst2temp,cst2volume,".
			"slodgetemp,slodgetvolume,oiltemp,oilvolume,catatan,notransaksi";
		$cols = explode(',',$col);
		$data = $param;
		unset($data['numRow']);
		unset($data['tanggal']);
		$data['waktu'] = tanggalsystem($param['tanggal']).
			str_replace(':','',$param['waktu']);
		
		$query = insertQuery($dbname,'pabrik_ops_klarifikasidt',$data,$cols);
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
		$query = updateQuery($dbname,'pabrik_ops_klarifikasidt',$data,$where);
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
		$query = "delete from `".$dbname."`.`pabrik_ops_klarifikasidt` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
	break;
}
?>