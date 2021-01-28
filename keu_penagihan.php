<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript1.2 src=js/keu_penagihan.js></script>
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
    makeElement('sNoTrans','label',$_SESSION['lang']['noinvoice']).
    makeElement('sNoTrans','text','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array(
    'No. Invoice','Unit','Tanggal','No Order','Keterangan'
);

# Content
$cols = "noinvoice,kodeorg,tanggal,noorder,keterangan";
$query = selectQuery($dbname,'keu_penagihanht',$cols,"","",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'keu_penagihanht');
foreach($data as $key=>$row) {
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	if($data[$key]['keterangan']=='0')
	{
		$data[$key]['keterangan'] = '';
	}
	else
	$data[$key]['keterangan'] = 'Tidak Dipungut PPN/PPnBM eks PP No. 32 Tahun 2009';
}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");

$tHeader->addAction('detailPDF','Invoice','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');//tambahan indra
$tHeader->addAction('detailPDF1','Invoice Dengan PPn DP','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[4]->addAttr('event');//tambahan indra
$tHeader->addAction('detailPDF2','Kuitansi','images/'.$_SESSION['theme']."/pdf.jpg");//tambahan indra
$tHeader->_actions[5]->addAttr('event');//tambahan indra

$tHeader->_switchException = array('detailPDF');
$tHeader->_switchException = array('detailPDF2');

$tHeader->pageSetting(1,$totalRow,10);



#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['penagihan']."</h3></div>";
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