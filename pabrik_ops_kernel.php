<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
?>
<script>var USER_THEME='<?php echo $_SESSION['theme']?>';</script>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript1.2 src='js/pabrik_ops_kernel.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#=== Prep Control & Search
$ctl = array();

# Control
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".
    $_SESSION['lang']['new']."' onclick=\"showAdd()\"><br><span align='center'>".$_SESSION['lang']['new']."</span></div>";
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".
    $_SESSION['lang']['list']."' onclick=\"defaultList()\"><br><span align='center'>".$_SESSION['lang']['list']."</span></div>";

# Search
$ctl[] = "<fieldset><legend><b>".$_SESSION['lang']['find']."</b></legend>".
    makeElement('sNoTrans','label',$_SESSION['lang']['tanggal']).
    makeElement('sNoTrans','date','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array('No Transaksi','Unit','Tanggal','Pemeriksa');

## Get Periode
$tanggal1 = $_SESSION['org']['period']['start'];
$tanggal2 = $_SESSION['org']['period']['end'];

# Content
$cols = "notransaksi,kodeorg,tanggal,diperiksa";
$order="notransaksi desc";
$query = selectQuery($dbname,'pabrik_ops_kernelht',$cols,"tanggal between '".
	$tanggal1."' and '".$tanggal2."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'",$order,false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'pabrik_ops_kernelht');
$listKary = '';
foreach($data as $key=>$row) {
	$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	if($listKary!='') {$listKary.=',';}
	$listKary.=$row['diperiksa'];
}

$dataShow = $data;
//cari nama orang
if(!empty($data)) {
	$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
		"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
	$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
		"karyawanid in (".$listKary.")");
	foreach($data as $key=>$row) {
		$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
		$dataShow[$key]['diperiksa'] = $optKary[$row['diperiksa']];
	}
}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[2]->addAttr('event');
$tHeader->_switchException = array('detailPDF');
$tHeader->pageSetting(1,$totalRow,10);

#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>Log Mesin > Kernel</h3></div>";
echo "<div><table align='center'><tr>";
foreach($ctl as $el) {
    echo "<td v-align='middle' style='min-width:100px'>".$el."</td>";
}
echo "</tr></table></div>";
CLOSE_BOX();

# List
OPEN_BOX();
echo "<div id='workField'>";
$tHeader->renderTable();
echo "</div>";
CLOSE_BOX();
echo close_body();
?>