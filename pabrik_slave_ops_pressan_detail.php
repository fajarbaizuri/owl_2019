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
    case 'showDetail':
		#== Prep Tab
		$headFrame = array(
			$_SESSION['lang']['log'],
			$_SESSION['lang']['mesin']
		);
		$contentFrame = array();
		
		$trans = explode('/',$param['notransaksi']);
		
		# Options
		$optMesin = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"kodeorganisasi like '".$trans[0]."%' and tipe='STENGINE'");
		
		#================ Log Tab =============================
		# Get Data
		$where = "notransaksi='".$param['notransaksi']."'";
		/*$cols = "time(waktu) as waktu,deg1hour,deg1amp,deg1c,deg2hour,deg2amp,deg2c,deg3hour,".
			"deg3amp,deg3c,press1hour,press1amp,press1c,press1tekanan,press2hour,".
			"press2amp,press2c,press2tekanan,press3hour,press3amp,press3c,".
			"press3tekanan,catatan";*/
			
		$cols = "time(waktu) as waktu,deg1hour,deg1amp,deg1c,deg2hour,deg2amp,deg2c,deg3hour,".
			"deg3amp,deg3c,press1hour,press1amp,press1c,press1tekanan,press2hour,".
			"press2amp,press2c,press2tekanan,press3hour,press3amp,press3c,".
			"press3tekanan,deg4hour,deg4amp,deg4c,deg5hour,deg5amp,deg5c,deg6hour,deg6amp,deg6c,".
			"press4hour,press4amp,press4c,press4tekanan,press5hour,press5amp,press5c,press5tekanan,".
			"press6hour,press6amp,press6c,press6tekanan,catatan";
				
		$colArr = array_merge(array('waktu'),explode(',',$cols));
		unset($colArr[1]);
		$query = selectQuery($dbname,'pabrik_ops_pressandtb',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			//$dataShow[$key]['mesin'] = $optMesin[$row['mesin']];
		}
		
		# Form
		$theForm2 = new uForm('logForm','Form Log',3);
		$theForm2->addEls('waktu',$_SESSION['lang']['waktu'],'','jammenit','L',25);
		foreach($colArr as $c) {
			if($c!='waktu' and $c!='catatan') {
				$theForm2->addEls($c,$_SESSION['lang'][$c],'0','textnum','L',15);
			}
		}
		//$theForm2->addEls('catatan',$_SESSION['lang']['catatan'],'','text','L',25);
		
		
		$theForm2->addEls('catatan',$_SESSION['lang']['catatan'],'','text','L',25);
		
		# Table
		$theTable2 = new uTable('logTable','Tabel Log',$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('ftLog',$theForm2,$theTable2,null,
			array('notransaksi','tanggal'));
		$formTab2->_target = "pabrik_slave_ops_pressan_log";
		
		$contentFrame[0] = $formTab2->prep();
		
		#================ Mesin Tab =============================
		# Get Data
		$where = "notransaksi='".$param['notransaksi']."'";
		$cols = "mesin,hariini,sdhi,sejakbaru,sejakrebuild";
		$query = selectQuery($dbname,'pabrik_ops_presandta',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			$dataShow[$key]['mesin'] = $optMesin[$row['mesin']];
		}
		
		# Form
		$theForm1 = new uForm('mesinForm','Form Mesin',2);
		$theForm1->addEls('mesin',$_SESSION['lang']['mesin'],'','select','R',20,$optMesin);
		$theForm1->addEls('hariini',$_SESSION['lang']['hariini'],'0','textnum','R',10);
		$theForm1->addEls('sdhi',$_SESSION['lang']['sdhi'],'0','textnum','R',10);
		$theForm1->addEls('sejakbaru',$_SESSION['lang']['sejakbaru'],'0','textnum','R',10);
		$theForm1->addEls('sejakrebuild',$_SESSION['lang']['sejakrebuild'],'0','textnum','R',10);
		
		# Table
		$theTable1 = new uTable('mesinTable','Tabel Mesin',$cols,$data,$dataShow);
		
		# FormTable
		$formTab1 = new uFormTable('ftMesin',$theForm1,$theTable1,null,array('notransaksi'));
		$formTab1->_target = "pabrik_slave_ops_pressan_mesin";
		
		$contentFrame[1] = $formTab1->prep();
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		drawTab('FRM',$headFrame,$contentFrame,150,'100%');
		echo "</fieldset>";
		break;
	default:
	break;
}
?>