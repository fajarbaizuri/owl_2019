<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>

<?

#==== Status Blok Validation
if(!isset($_SESSION['tmp']['actStat'])) {
    echo "Error : Atribut Status tidak ada";
    exit;
} else {
    $blokStatus = $_SESSION['tmp']['actStat'];
}

#==== Parameter yang berbeda untuk tiap status
switch($blokStatus) {
	case "vhc":
	$title = "Kegiatan Kendaraan dan Alat Berat";
	$tipe = 'notransaksi';
	$_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'vhc';
	$whereContArr = array();
	$whereContArr[] = array();
	break;
    default:
	echo "Error : Atribut Status tidak terdefinisi";
	exit;
	break;
}
//if (substr($_SESSION['empl']['lokasitugas'],0,4) != 'TKFB'){
$whereCont = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
//}else{
//$whereCont = "kodeorg like '%%'";
//}

$whereContArr[] = array('kodeorg',$_SESSION['empl']['lokasitugas']);
?>
<script language=javascript1.2>
    function goToPages(page,shows,where) {
	if(typeof where != 'undefined') {
	    var newWhere = where.replace(/'/g,'"');
	}
	var workField = document.getElementById('workField');
	var param = "page="+page;
	param += "&shows="+shows+"&tipe=<?php echo $tipeVal?>";
	if(typeof where != 'undefined') {
	    param+="&where="+newWhere;
	}
	
	function respon() {
	    if (con.readyState == 4) {
		if (con.status == 200) {
		    busy_off();
		    if (!isSaveResponse(con.responseText)) {
			alert('ERROR TRANSACTION,\n' + con.responseText);
		    } else {
			//=== Success Response
			workField.innerHTML = con.responseText;
		    }
		} else {
		    busy_off();
		    error_catch(con.status);
		}
	    }
	}
	
	post_response_text('vhc_slave_kendaraan.php?proses=showHeadList', param, respon);
    }
</script>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/zSearch.js'></script>
<script language=javascript1.2 src='js/vhc_kendaraan.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#=== Prep Control & Search
$ctl = array();

# Control
$tmpWhere = json_encode($whereContArr);
$jsWhere = str_replace('"',"'",$tmpWhere);
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
	'Nomor','Tanggal','Kelompok','Plat No.','Kondisi','Mandor Transport','Penggunaan BBM (Ltr)'
);

# Content
$cols = "notransaksi,tanggal,kelompok,nopol,kondisinm,mandor,bbm,posting";
$query = selectQuery($dbname,'vhc_kendaraan_vw',$cols,$whereCont,
    "tanggal desc, notransaksi desc",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'vhc_kendaraan_vw',$whereCont);
if(!empty($data)) {
    $whereKarRow = "karyawanid in (";
    $notFirst = false;
    foreach($data as $key=>$row) {
	if($row['posting']==1) {
	    $data[$key]['switched']=true;
	}
	$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	unset($data[$key]['posting']);
	
	if($notFirst==false) {
	    if($row['mandor']!='' && $row['mandor']!=NULL) {
		$whereKarRow .= $row['mandor'];
		$notFirst=true;
	    }


	} else {
	    if($row['mandor']!='' && $row['mandor']!=NULL) {
		if($notFirst==false) {
		    $whereKarRow .= $row['mandor'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['mandor'];
		}
	    }
	}
    }
    $whereKarRow .= ")";
} else {
    $whereKarRow = "";
}
$optKarRow = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKarRow);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
    isset($optKarRow[$row['mandor']]) ? $dataShow[$key]['mandor'] = $optKarRow[$row['mandor']]:null;
}

# Posting --> Jabatan
$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='traksi'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->_printAttr = array($tipeVal);
$tHeader->_print = false;

#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->_actions[0]->addAttr($tipeVal);
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
    $tHeader->_actions[2]->_name='';
}
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->_actions[3]->addAttr($tipeVal);
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->setWhere($whereContArr);
$tHeader->_switchException = array('detailPDF');
#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<input type='hidden' id='tipeTransHid' value='".$tipeVal."' />";
echo "<div align='center'><h3>".$title."</h3></div>";
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
?>