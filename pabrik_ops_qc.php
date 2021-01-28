<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

echo open_body();
include('master_mainMenu.php');
?>
<script>var USER_THEME='<?php echo $_SESSION['theme']?>';</script>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
OPEN_BOX('font-weight:bold','Log Mesin > QC');
CLOSE_BOX();

OPEN_BOX();
# Options
$optStart = getEnum($dbname,'pabrik_ops_boilerdt','start');
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
	"lokasitugas='".$_SESSION['empl']['lokasitugas']."'");

## Get Periode
$tanggal1 = $_SESSION['org']['period']['start'];
$tanggal2 = $_SESSION['org']['period']['end'];

# Get Data
$primary = array('notransaksi','tanggal','kodeorg');
$where = "tanggal between '".$tanggal1."' and '".$tanggal2."' and kodeorg='".
	$_SESSION['empl']['lokasitugas']."'";
$cols = "notransaksi,kodeorg,tanggal,diperiksa,sp1press,sp1bjpecah,sp1inpecah,".
	"sp2press,sp2bjpecah,sp2inpecah,sp3press,sp3bjpecah,sp3inpecah,sp4press,sp4bjpecah,sp4inpecah,".
	"sp5press,sp5bjpecah,sp5inpecah,sp6press,sp6bjpecah,sp6inpecah,soliddecanter1,soliddecanter2,".
	"hpdecanter1,hpdecanter2,hpseparator,underflow,condrebusan,fatpit,tandankosong,bdijangkos,".
	"cpoproduksialb,cpoproduksiair,cpoproduksikot,cpostokalb,cpostokair,".
	"rm1,rm1bjpecah,rm1inpecah,rm2,rm2bjpecah,rm2inpecah,rm3,rm3bjpecah,rm3inpecah,rm4,rm4bjpecah,rm4inpecah,rm5,rm5bjpecah,rm5inpecah,rm6,rm6bjpecah,rm6inpecah,".
	"cangkanghydre,claybatch,ltds1,ltds2,fibrecycl,kernelproduksikot,kernelproduksiair,kernelproduksialb";
$colArr = explode(',',$cols);
$query = selectQuery($dbname,'pabrik_ops_qcdt',$cols,$where);
$data = fetchData($query);

# Data Show
$primaryPlus = array_merge($primary,array('diperiksa'));
$dataShow = $data;
foreach($dataShow as $key=>$row) {
	$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	$dataShow[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	$dataShow[$key]['diperiksa'] = $optKary[$row['diperiksa']];
	foreach($row as $head=>$val) {
		if(!in_array($head,$primaryPlus)) {
			$dataShow[$key][$head] = number_format($val,2);
		}
	}
}

# Form
$theForm2 = new uForm('transForm','Form QC',3);
$theForm2->addEls('notransaksi',$_SESSION['lang']['notransaksi'],'','text','L',20);
$theForm2->_elements[0]->_attr['disabled'] = 'disabled';
$theForm2->addEls('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',20,$optOrg);
$theForm2->addEls('tanggal',$_SESSION['lang']['tanggal'],'','date','L',15);
$theForm2->addEls('diperiksa',$_SESSION['lang']['diperiksa'],'','select','L',20,$optKary);
foreach($colArr as $c) {
	if(!in_array($c,$primaryPlus)) {
		$theForm2->addEls($c,$_SESSION['lang'][$c],'0','textnum','R',15);
	}
}

# Table
$theTable2 = new uTable('transTable','Tabel QC',$cols,$data,$dataShow);

# FormTable
$formTab2 = new uFormTable('transFT',$theForm2,$theTable2);
$formTab2->setFreezeEls($primary);
$formTab2->_noEnable = '##notransaksi##kodeorg##tanggal';
$formTab2->_noClearField = '##notransaksi##kodeorg##tanggal';
$formTab2->_target = "pabrik_slave_ops_qc";
$formTab2->render();
CLOSE_BOX();
echo close_body();
?>