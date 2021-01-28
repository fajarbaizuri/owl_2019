<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/zSearch.js'></script>
<script language=javascript1.2 src="js/log_permintaan_dana.js"></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type="text/css" href='style/zTable.css'>
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
    makeElement('sNoTrans','label',$_SESSION['lang']['notransaksi']).
    makeElement('sNoTrans','text','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array(
    $_SESSION['lang']['kodeorg'],
    $_SESSION['lang']['notransaksi'],
    $_SESSION['lang']['tanggal'],
	'Tipe',
   'Pemohon', 
   'Total Permintaan',
    $_SESSION['lang']['jumlahrealisasi'],
    $_SESSION['lang']['status']
);
 
# Content
$cols = "kodeorg,notransaksi,tanggal,kelompok,namakaryawan,totaldana,posting";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or
   $_SESSION['empl']['tipelokasitugas']=='KANWIL') {
    $where = "kodeorg LIKE '%%'";
} else {
    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
}
$query = selectQuery($dbname,'log_dana_vw',$cols,$where ." order by tanggal desc","",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'log_dana_vw');
foreach($data as $key=>$row) {
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']); 
    //=================ambil realisasi
    $data[$key]['realisasi'] =0;
    $strx="select sum(jumlahrealisasi) from ".$dbname.".log_badana 
		where notransaksi='".$data[$key]['notransaksi']."'";
    $resx=mysql_query($strx);
    while($barx=mysql_fetch_array($resx))
    {
      $data[$key]['realisasi']= number_format($barx[0]); 
    }
    
    // Cek Alur Persetujuan
    $qCek = "select * from $dbname.log_persetujuandana ".
        "where kodeorg='".$row['kodeorg']."' and notransaksi='".$row['notransaksi'].
        "' order by level desc limit 0,1";
    $resCek = fetchData($qCek);
    
    // Set Status
    if(!empty($resCek)) {
        $data[$key]['switched']=1;
        if($resCek[0]['level']==5 and $resCek[0]['tanggal']!='0000-00-00') {
            $data[$key]['status'] = "Disetujui";
        } else {
            $data[$key]['status'] = "Persetujuan Tahap ".$resCek[0]['level'];
        }
    } else {
        $data[$key]['status'] = "Usulan";
    }
    if($row['posting']==1) {
        $data[$key]['switched']=1;
        $data[$key]['status'] = "Disetujui";
    }
    unset($data[$key]['posting']);
}
 
# Options


# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
  $dataShow[$key]['totaldana'] = number_format($row['totaldana'],0);
}
$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='dana'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];
# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");
$tHeader->addAction('pengajuan','Pengajuan','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
$tHeader->_actions[2]->addAttr('event');
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->_switchException = array('detailPDF');
$tHeader->pageSetting(1,$totalRow,10);
#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>Permintaan Dana Operasional</h3></div>";
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