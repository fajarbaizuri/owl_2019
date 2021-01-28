<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script languange=javascript1.2 src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script languange=javascript1.2 src='js/formReport.js'></script>
<script languange=javascript1.2 src='js/zGrid.js'></script>
<script languange=javascript1.2 src='js/keu_jurnal.js'></script>
<script languange=javascript1.2>
    //zGrid.column.push(1);
    theGrid[1].addColumn('nourut','<?php echo $_SESSION['lang']['nourut']?>','textnum',0,'R',10);
    theGrid[1].addColumn('noakun','<?php echo $_SESSION['lang']['noakun']?>','text','-','L',14);
    theGrid[1].addColumn('keterangan','<?php echo $_SESSION['lang']['keterangan']?>','text','-','L',50);
    theGrid[1].addPrimColumn('nojurnal','nojurnal');
    theGrid[1].target = "keu_slave_jurnal_manage_detail";
</script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?
#== Prep Periode Akuntansi
$org = $_SESSION['org'];
$period = $_SESSION['org']['period'];
if($period=='') {
    echo "Error : User tidak aktif dalam periode akuntansi tertentu";
    CLOSE_BOX();
    echo close_body();
    exit;
}

#== Get Journal Header
$where = "tanggal<=".$period['end']." AND tanggal>=".$period['start'].
    " and substr(nojurnal,10,4)='".$_SESSION['empl']['lokasitugas'].
    "' and kodejurnal='M'";
$query = selectQuery($dbname,'keu_jurnalht',"kodejurnal,nojurnal,tanggal,noreferensi,matauang",$where);
$resTab = fetchData($query);

#== Prep List Header
$header = array('Kode','Nomor','Tanggal','No Referensi','Mata Uang');

$table = "<table id='listHeader' class='sortable'>";
$table .= "<thead><tr class='rowheader'>";
$table .= "<td colspan='2'>".$_SESSION['lang']['action']."</td>";
foreach($header as $head) {
    $table .= "<td>".$head."</td>";
}
$table .= "</tr></thead>";
$table .= "<tbody id='bodyListHeader'>";
foreach($resTab as $key=>$row) {
    $table .= "<tr id='tr_".$key."' class='rowcontent' style='cursor:pointer'>";
    $table .= "<td id='pdf_".$key."'><img src='images/".$_SESSION['theme']."/pdf.jpg' ";
    $table .= "class='zImgBtn' onclick='detailPDF(".$key.",event)'></td>";
    $table .= "<td id='delHead_".$key."'>";
    $table .= "<img src='images/".$_SESSION['theme']."/delete.png' ";
    $table .= "class='zImgBtn' onclick='delHead(".$key.")'></td>";
    foreach($row as $col=>$dat) {
        if($col=='tanggal') {
            $dat = tanggalnormal($dat);
        }
        $table .= "<td id='".$col."_".$key."' onclick='passEditHeader(".$key.")'>".$dat."</td>";
    }
    $table .= "</tr>";
}
$table .= "</tbody>";
$table .= "<tfoot></tfoot></table>";

#== Prep Form Header
# Options
$optCurr = makeOption($dbname,'setup_matauang','kode,matauang');
$optJCode = makeOption($dbname,'keu_5kelompokjurnal','kodekelompok,keterangan',
    "kodeorg='".$org['kodeorganisasi']."' and kodekelompok='M'");

# Elements
$els = array();
$els[] = array(
    makeElement('nojurnal','label',$_SESSION['lang']['nojurnal']),
    makeElement('nojurnal','text','',array('style'=>'width:120px',
        'readonly'=>'readonly','disabled'=>'disabled'))." *) Kode Jurnal Otomatis di-<i>generate</i>"
);
$els[] = array(
    makeElement('kodejurnal','label',$_SESSION['lang']['kodejurnal']),
    makeElement('kodejurnal','select','',array('style'=>'width:150px',
        'disabled'=>'disabled'),$optJCode)
);
$els[] = array(
    makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
    makeElement('tanggal','text','',array('style'=>'width:80px','readonly'=>'readonly',
        'onkeypress'=>'return tanpa_kutip(event)','disabled'=>'disabled'))
);
$els[] = array(
    makeElement('noreferensi','label',$_SESSION['lang']['nodok']),
    makeElement('noreferensi','text','',array('style'=>'width:80px','maxlength'=>'20',
        'onkeypress'=>'return tanpa_kutip(event)','disabled'=>'disabled'))
);
$els[] = array(
    makeElement('matauang','label',$_SESSION['lang']['matauang']),
    makeElement('matauang','select','',array('style'=>'width:70px',
        'disabled'=>'disabled'),$optCurr)
);

$els['btn'] = array(
    makeElement('saveButton','button',$_SESSION['lang']['save'],
        array('disabled'=>'disabled'))
);
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
#===== Show =======
# Table
echo "<fieldset id='fieldListTable' style='float:left;clear:left;min-height:200px;height:85%;overflow:auto'>";
echo "<legend><b>Daftar Header</b></legend>";
//echo "<img id='addHeadBtn' src='images/".$_SESSION['theme']."/plus.png' style='cursor:pointer' onclick=\"addModeForm()\" />".
//    "<a style='cursor:pointer' onclick=\"addModeForm('".$_SESSION['theme']."')\">Tambah Header</a>";
echo "<img id='addHeadBtn' src='images/".$_SESSION['theme']."/plus.png' style='cursor:pointer' onclick=\"addModeForm('".$_SESSION['theme']."')\" />".
    "<a style='cursor:pointer' onclick=\"addModeForm('".$_SESSION['theme']."')\">Tambah Header</a>";
echo $table;
echo "</fieldset>";

# Active Form
echo makeElement('startPeriod','hidden',$_SESSION['org']['period']['start']);
echo makeElement('endPeriod','hidden',$_SESSION['org']['period']['end']);
echo "<fieldset id='fieldFormHeader' style='clear:right;min-height:200px;'>";
echo "<legend><b>Form Header</b></legend>";
echo genElement($els);
echo "</fieldset>";

# Detail List
echo "<fieldset id='fieldListDetail' style='clear:both'>";
echo "<legend><b>".$_SESSION['lang']['list']." ".$_SESSION['lang']['detail']."</b></legend>";
echo "<div id='divDetail'></div>";
echo "</fieldset>";

CLOSE_BOX();
echo close_body();
?>