<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo OPEN_BODY();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript1.2 src=js/log_gudangdivisi.js></script>
<?php
######################################################################## Init ##
$title = "Gudang Divisi";

##################################################################### Content ##
$cont = array();
$tForm = "Form - Mode <span id='modeForm'>Tambah</span> <span id='rowEdit'></span>";
$tTable = 'Table';

## Options
$optGudang = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"tipe='GUDANG'");
$optKebun = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"length(kodeorganisasi)=4 and right(kodeorganisasi,1)='E'",'0',true);
$tmpAfd = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"length(kodeorganisasi)=6 and substr(kodeorganisasi,4,1)='E'");
$optAfd = array();
foreach($tmpAfd as $key=>$row) {
	$optAfd[substr($key,0,4)][$key] = $row;
}

## Form
$cont[$tForm] = "<input id='afdList' type='hidden' value='".json_encode($optAfd)."' />";
$cont[$tForm] .= "<table class='data'>";
$cont[$tForm] .= "<tr><td>".$_SESSION['lang']['pilihgudang']."</td>";
$cont[$tForm] .= "<td>: ".makeElement('gudang','select','',array('onchange'=>'changeKebun()'),$optGudang)."</td></tr>";
$cont[$tForm] .= "<tr><td>".$_SESSION['lang']['kebun']."</td>";
$cont[$tForm] .= "<td>: ".makeElement('kebun','select','',array('onchange'=>'changeKebun()'),$optKebun)."</td></tr>";
$cont[$tForm] .= "<tr><td>".$_SESSION['lang']['afdeling']."</td>";
$cont[$tForm] .= "<td>: ".makeElement('afdeling','select','',array(),array())."</td></tr>";
$cont[$tForm] .= "<tr><td>".$_SESSION['lang']['kodebarang']."</td>";
$cont[$tForm] .= "<td>: ".makeElement('barang','searchBarang','',array())."</td></tr>";
$cont[$tForm] .= "<tr><td>".$_SESSION['lang']['jumlah']."</td>";
$cont[$tForm] .= "<td>: ".makeElement('kuantitas','textnum','1',array())."</td></tr>";
$cont[$tForm] .= "<tr><td colspan=3>".
	makeElement('addBtn','button',$_SESSION['lang']['save'],array('onclick'=>'addTrans()')).
	makeElement('saveBtn','button',$_SESSION['lang']['save'],array('onclick'=>'saveTrans()','style'=>'display:none')).
	makeElement('cancelBtn','button',$_SESSION['lang']['cancel'],array('onclick'=>'cancelTrans()'));
$cont[$tForm] .= "</td></tr>";
$cont[$tForm] .= "</table>";

## Get Data
$query = selectQuery($dbname,'log_gudangdivisi',
	'kodegudang,kebun,afdeling,kodebarang,kuantitas');
$data = fetchData($query);

##################################################################### Masking ##
## Condition for Masking
$whereOrg = $whereBarang = "";
$i=0;
foreach($data as $row) {
	if($i>0) {
		$whereBarang .= ",";
		$whereOrg .= ",";
	}
	$whereBarang .= $row['kodebarang'];
	$whereOrg .= "'".$row['kodegudang']."','".$row['kebun']."','".$row['afdeling']."'";
	$i++;
}

## Option for Masking
$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
	"kodebarang in (".$whereBarang.")");
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"kodeorganisasi in (".$whereOrg.")");

## Masking
$dataShow = $data;
foreach($dataShow as $key=>$row) {
	$dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
	$dataShow[$key]['kodegudang'] = $optOrg[$row['kodegudang']];
	$dataShow[$key]['kebun'] = $optOrg[$row['kebun']];
	$dataShow[$key]['afdeling'] = $optOrg[$row['afdeling']];
}
#################################################################### /Masking ##

## Table
$cont[$tTable] = "<table class='sortable' callpadding=1 border=0>";
$cont[$tTable] .= "<thead><tr class='rowheader'>";
$cont[$tTable] .= "<td colspan=2>".$_SESSION['lang']['action']."</td>";
$cont[$tTable] .= "<td>".$_SESSION['lang']['daftargudang']."</td>";
$cont[$tTable] .= "<td>".$_SESSION['lang']['kebun']."</td>";
$cont[$tTable] .= "<td>".$_SESSION['lang']['afdeling']."</td>";
$cont[$tTable] .= "<td>".$_SESSION['lang']['kodebarang']."</td>";
$cont[$tTable] .= "<td>".$_SESSION['lang']['jumlah']."</td>";
$cont[$tTable] .= "</tr></thead>";
$cont[$tTable] .= "<tbody id='tBody'>";
foreach($data as $key=>$row) {
	$cont[$tTable] .= "<tr id='row_".$key."' class='rowcontent'>";
	$cont[$tTable] .= "<td><img id='edit_".$key."' src='images/edit.png' ".
		"style='width:15px;cursor:pointer' onclick='editRow(".$key.")'></td>";
	$cont[$tTable] .= "<td><img id='del_".$key."' src='images/delete_32.png' ".
		"style='width:15px;cursor:pointer' onclick='delTrans(".$key.")'></td>";
	foreach($row as $head=>$val) {
		$cont[$tTable] .= "<td id='".$head."_".$key."' value='".$val."'>";
		$cont[$tTable] .= $dataShow[$key][$head]."</td>";
	}
	$cont[$tTable] .= "</tr>";
}
$cont[$tTable] .= "</tbody></table>";

################################################################ Display View ##
include('master_mainMenu.php');

# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$title."</h3></div>";
CLOSE_BOX();

# List
OPEN_BOX();
echo "<div id='workField'>";
foreach($cont as $key=>$row) {
	echo "<fieldset><legend><b>".$key."</b></legend>";
	echo $row;
	echo "</fieldset>";
}
echo "</div>";
CLOSE_BOX();

//echo CLOSE_BODY();
?>