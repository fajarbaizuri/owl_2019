<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
?>

<?

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
	# Get Data
	$where = "nopengolahan='".$param['nopengolahan']."'";
	$cols = "kodeorg as station,tahuntanam,jammulai,jamselesai,jamstagnasi,".
	    "keterangan";
	$query = selectQuery($dbname,'pabrik_pengolahanmesin',$cols,$where);
	$data = fetchData($query);
	
	# Options
	/*if(!empty($whereBarang)) {
	    $whereBarang = "kodebarang in (";
	    foreach($data as $key=>$row) {
		if($key==0) {
		    $whereBarang .= "'".$row['kodebarang']."'";
		} else {
		    $whereBarang .= ",'".$row['kodebarang']."'";
		}
	    }
	    $whereBarang .= ")";
	    $optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
		$whereBarang);
	} else {
	    $optBarang = array();
	}*/
	$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "induk='".$param['kodeorg']."' and tipe='STATION'");
	$optMesin = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "tipe='STENGINE' and induk='".end(array_reverse(array_keys($optOrg)))."'");
	$optMesinAll = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "tipe='STENGINE'",'0',true);
	
	# Data Show
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['station'] = $optOrg[$row['station']];
	    $dataShow[$key]['tahuntanam'] = $optMesinAll[$row['tahuntanam']];
	}
	
	# Form
	$theForm1 = new uForm('mesinForm','Form Mesin',2);
	$theForm1->addEls('station',$_SESSION['lang']['station'],'','select','L',25,$optOrg);
	$theForm1->_elements[0]->_attr['onchange']='updMesin()';
	$theForm1->addEls('tahuntanam',$_SESSION['lang']['mesin'],'0','select','L',25,$optMesin);
	
	$theForm1->addEls('jammulai1',$_SESSION['lang']['jammulai'],'0','jammenit','R',6);
	$theForm1->_elements[2]->_attr['onchange']='tambah()';
	$theForm1->addEls('jamselesai1',$_SESSION['lang']['jamselesai'],'0','jammenit','R',6);
	$theForm1->_elements[3]->_attr['onchange']='tambah()';
	
	$theForm1->addEls('jamstagnasi1',$_SESSION['lang']['jamstagnasi'],'0','textnum','R',10);
	$theForm1->addEls('keterangan',$_SESSION['lang']['keterangan'],'','text','L',50);
	#$theForm1->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20,null,null,'jumlahbarang_satuan');
	#$theForm1->addEls('jumlahbarang',$_SESSION['lang']['jumlahbarang'],'0','textnumwsatuan','L',10);
	
	# Table
	$theTable1 = new uTable('mesinTable','Tabel Mesin',$cols,$data,$dataShow);
	
	# FormTable
	$formTab1 = new uFormTable('ftMesin',$theForm1,$theTable1,null,array('nopengolahan'));
	$formTab1->_target = "pabrik_slave_pengolahan_mesin";
	$formTab1->_addActions = array(
	    'material'=>array(
		'img'=>'detail1.png',
		'onclick'=>'showMaterial'
	    )
	);
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
	$formTab1->render();
	echo "</fieldset>";
	break;
    case 'updMesin':
	$opt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "tipe='STENGINE' and induk='".$param['station']."'");
	echo json_encode($opt);
	break;
    default:
	break;
}
?>