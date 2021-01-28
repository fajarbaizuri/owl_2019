<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'add':
		if($param['tanggal']=='') {
			exit("Warning: Tanggal harus diisi");
		}
		
		## No Transaksi
		$tmpTgl = explode('-',$param['tanggal']);
		$noTrans = $param['kodeorg'].'/'.$tmpTgl[2].'/'.$tmpTgl[1].'/'.$tmpTgl[0];
		
		## Get Periode
		$tanggal1 = $_SESSION['org']['period']['start'];
		$tanggal2 = $_SESSION['org']['period']['end'];
		$tgl = tanggalsystem($param['tanggal']);
		if($tgl<$tanggal1 or $tgl>$tanggal2) {
			exit("Warning: Tanggal harus dalam range periode ".
				 tanggalnormal($tanggal1)." s/d ".tanggalnormal($tanggal2));
		}
		
	
		$col = "notransaksi,kodeorg,tanggal,diperiksa,sp1press,sp1bjpecah,sp1inpecah,".
	"sp2press,sp2bjpecah,sp2inpecah,sp3press,sp3bjpecah,sp3inpecah,sp4press,sp4bjpecah,sp4inpecah,".
	"sp5press,sp5bjpecah,sp5inpecah,sp6press,sp6bjpecah,sp6inpecah,soliddecanter1,soliddecanter2,".
	"hpdecanter1,hpdecanter2,hpseparator,underflow,condrebusan,fatpit,tandankosong,bdijangkos,".
	"cpoproduksialb,cpoproduksiair,cpoproduksikot,cpostokalb,cpostokair,".
	"rm1,rm1bjpecah,rm1inpecah,rm2,rm2bjpecah,rm2inpecah,rm3,rm3bjpecah,rm3inpecah,rm4,rm4bjpecah,rm4inpecah,rm5,rm5bjpecah,rm5inpecah,rm6,rm6bjpecah,rm6inpecah,".
	"cangkanghydre,claybatch,ltds1,ltds2,fibrecycl,kernelproduksikot,kernelproduksiair,kernelproduksialb,dibuat";	
		$cols = explode(',',$col);
		$data = $param;
		$data['dibuat'] = $_SESSION['standard']['userid'];
		$data['tanggal'] = $tgl;
		$data['notransaksi'] = $noTrans;
		unset($data['numRow']);
		
		$query = insertQuery($dbname,'pabrik_ops_qcdt',$data,$cols);
		
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['dibuat']);
		$data['tanggal'] = $param['tanggal'];
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
		$data['tanggal'] = tanggalsystem($param['tanggal']);
		
		$where = "notransaksi='".$param['cond_notransaksi']."'";
		$query = updateQuery($dbname,'pabrik_ops_qcdt',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		echo json_encode($param);
		break;
	case 'delete':
		$where = "notransaksi='".$param['notransaksi']."'";
		$query = "delete from `".$dbname."`.`pabrik_ops_qcdt` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
	break;
}
?>