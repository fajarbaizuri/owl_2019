<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>
<?

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    # Daftar Header
    case 'showHeadList':
	$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	if(isset($param['where'])) {
	    $arrWhere = json_decode($param['where'],true);
	    if(!empty($arrWhere)) {
		foreach($arrWhere as $key=>$r1) {
		    if($key==0) {
			$where .= $r1[0]." like '%".$r1[1]."%'";
		    } else {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
		    }
		}
	    } else {
		$where .= null;
	    }
	} else {
	    $where .= null;
	}
	
	# Header
/*	$header = array(
	    'Nomor','Tanggal','Pabrik','Kode Tangki','Kuantitas','Suhu'
	);
	
	# Content
	$cols = "notransaksi,tanggal,kodeorg,kodetangki,kuantitas,suhu";*/
	$header = array(
		'Nomor','Tanggal','Pabrik','Kode Tangki','Jumlah CPO','Jumlah Kernel'
	);
	
	# Content
	$cols = "notransaksi,tanggal,kodeorg,kodetangki,kuantitas,kernelquantity";
	$query = selectQuery($dbname,'pabrik_masukkeluartangki',$cols,$where,"",false,$param['shows'],$param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'pabrik_masukkeluartangki',$where);
	foreach($data as $key=>$row) {
	    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	}
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data);
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	//$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	$tHeader->_actions[1]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
	$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	if(isset($param['where'])) {
	    $tHeader->setWhere($arrWhere);
	}
	
	# View
	$tHeader->renderTable();
	break;
    # Form Add Header
    case 'showAdd':
	// View
	echo formHeader('add',array());
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Form Edit Header
    case 'showEdit':
	$query = selectQuery($dbname,'pabrik_masukkeluartangki',"*","notransaksi='".$param['notransaksi']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	$data = $_POST;
	
	// Error Trap
	$warning = "";
	if($data['notransaksi']=='') {$warning .= "No Transaksi harus diisi\n";}
	if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
	if($warning!=''){echo "Warning :\n".$warning;exit;}
	
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	unset($data['notransaksi']);
	$cols = array('tanggal','kodeorg','kodetangki','kuantitas','suhu',
	    'cporendemen','cpoffa','cpokdair','cpokdkot',
	    'kernelquantity','kernelrendemen','kernelkdair','kernelkdkot','kernelffa');
	$query = insertQuery($dbname,'pabrik_masukkeluartangki',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "notransaksi='".$data['notransaksi']."'";
	unset($data['notransaksi']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$query = updateQuery($dbname,'pabrik_masukkeluartangki',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."'";
	$query = "delete from `".$dbname."`.`pabrik_masukkeluartangki` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$data['notransaksi'] = '0';
	$data['kodeorg'] = '';
	$data['tanggal'] = '';
	$data['kodetangki'] = '';
	$data['kuantitas'] = '0';
	$data['suhu'] = '0';
	$data['cporendemen'] = '0';$data['cpoffa'] = '0';$data['cpokdair'] = '0';
	$data['cpokdkot'] = '0';$data['kernelquantity'] = '0';$data['kernelrendemen'] = '0';
	$data['kernelkdair'] = '0';$data['kernelkdkot'] = '0';$data['kernelffa'] = '0';
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $optTangki = makeOption($dbname,'pabrik_5tangki','kodetangki,kodetangki');
    
    $els = array();
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','maxlength'=>'12','disabled'=>'disabled'))
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px'),$optOrg)
    );
    $els[] = array(
	makeElement('kodetangki','label',$_SESSION['lang']['kodetangki']),
	makeElement('kodetangki','select',$data['kodetangki'],
	    array('style'=>'width:150px'),$optTangki)
    );
    $els[] = array(
	makeElement('suhu','label',$_SESSION['lang']['suhu']),
	makeElement('suhu','textnum',$data['suhu'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('kuantitas','label',$_SESSION['lang']['cpokuantitas']),
	makeElement('kuantitas','textnum',$data['kuantitas'],array('style'=>'width:100px'))."kg"
    );
   // $els[] = array(
//	makeElement('cporendemen','label',$_SESSION['lang']['cporendemen']),
//	makeElement('cporendemen','textnum',$data['cporendemen'],array('style'=>'width:100px'))."%"
//    );
    $els[] = array(
	makeElement('cpoffa','label',$_SESSION['lang']['cpoffa']),
	makeElement('cpoffa','textnum',$data['cpoffa'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('cpokdair','label',$_SESSION['lang']['cpokdair']),
	makeElement('cpokdair','textnum',$data['cpokdair'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('cpokdkot','label',$_SESSION['lang']['cpokdkot']),
	makeElement('cpokdkot','textnum',$data['cpokdkot'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('kernelquantity','label',$_SESSION['lang']['kernelquantity']),
	makeElement('kernelquantity','textnum',$data['kernelquantity'],array('style'=>'width:100px'))."kg"
    );
//    $els[] = array(
//	makeElement('kernelrendemen','label',$_SESSION['lang']['kernelrendemen']),
//	makeElement('kernelrendemen','textnum',$data['kernelrendemen'],array('style'=>'width:100px'))."%"
//    );
    $els[] = array(
	makeElement('kernelkdair','label',$_SESSION['lang']['kernelkdair']),
	makeElement('kernelkdair','textnum',$data['kernelkdair'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('kernelkdkot','label',$_SESSION['lang']['kernelkdkot']),
	makeElement('kernelkdkot','textnum',$data['kernelkdkot'],array('style'=>'width:100px'))."%"
    );
    $els[] = array(
	makeElement('kernelffa','label',$_SESSION['lang']['kernelffa']),
	makeElement('kernelffa','textnum',$data['kernelffa'],array('style'=>'width:100px'))."%"
    );
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable()"))
	);
    } elseif($mode=='edit') {
	$els['btn'] = array(
	    makeElement('editHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"editDataTable()"))
	);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,3);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,3);
    }
}
?>