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
<script language=javascript1.2 src='js/keu_tagihan.js'></script>
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
    'No. Invoice','PT','Tanggal','Jatuh Tempo','Last Update','No PO','Keterangan','Sub Total','Unit'
);

//cari nama orang
$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $nama[$bar->karyawanid]=$bar->namakaryawan;
}    


# Content
$cols = "noinvoice,kodeorg,tanggal,jatuhtempo,updateby,nopo,keterangan,nilaiinvoice,posting,unit";
$order="tanggal desc";
$query = selectQuery($dbname,'keu_tagihanht',$cols,"unit='".$_SESSION['empl']['lokasitugas']."'",$order,false,10,1);
//$query = selectQuery($dbname,'keu_tagihanht',$cols,"kodeorg='".$_SESSION['org']['kodeorganisasi']."'",$order,false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'keu_tagihanht');
	foreach($data as $key=>$row) {
        if($row['posting']==1) {
			$data[$key]['switched']=true;
	    }
        unset($data[$key]['posting']);            
	    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		$data[$key]['jatuhtempo'] = tanggalnormal($row['jatuhtempo']);
	    $data[$key]['nilaiinvoice'] = number_format($row['nilaiinvoice'],2);
	    $data[$key]['updateby'] = $nama[$row['updateby']];
        //$data[$key]['postingby'] = $nama[$row['postingby']];
	}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->_switchException = array('detailPDF');
#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['tagihan']."</h3></div>";
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