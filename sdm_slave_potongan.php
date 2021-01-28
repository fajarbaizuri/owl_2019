<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    # Daftar Header
    case 'showHeadList':
	if(isset($param['where'])) {
	    $arrWhere = json_decode($param['where'],true);
	    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	    if(!empty($arrWhere)) {
		foreach($arrWhere as $key=>$r1) {
		    if($key==0) {
			$where .= $r1[0]." like '%".$r1[1]."%'";
		    } else {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
		    }
		}
	    } else {
		$where = null;
	    }
	} else {
	    $where = null;
	}
	
	# Header
	$header = array(
	    $_SESSION['lang']['kodeorg'],
            $_SESSION['lang']['periodegaji'],
            $_SESSION['lang']['keterangan'],
	);
	
	# Content
	if(!is_null($where)) {
	    $where .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	} else {
	    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	}
	$cols = "kodeorg,periodegaji,keterangan";
	$query = selectQuery($dbname,'sdm_potonganht',$cols,$where,"",false,$param['shows'],$param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'sdm_potonganht',$where);
	foreach($data as $key=>$row) {
	    #$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	}
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data);
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	#$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	#$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
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
        $where = "periodegaji='".$param['periodegaji']."' and kodeorg='".$param['kodeorg']."'";
	$query = selectQuery($dbname,'sdm_potonganht',"*",$where);
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	$data = $_POST;
	
	// Error Trap
	$warning = "";
	if($data['periodegaji']=='') {$warning .= "Periode Gaji harus diisi\n";}
	if($warning!=''){echo "Warning :\n".$warning;exit;}
	
	$cols = array('kodeorg','periodegaji','keterangan');
	$query = insertQuery($dbname,'sdm_potonganht',$data,$cols);
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "periodegaji='".$data['periodegaji']."' and kodeorg='".$param['kodeorg']."'";
	unset($data['periodegaji']);
        unset($data['kodeorg']);
	$query = updateQuery($dbname,'sdm_potonganht',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "periodegaji='".$param['periodegaji']."' and kodeorg='".$param['kodeorg']."'";
	$query = "delete from `".$dbname."`.`sdm_potonganht` where ".$where;
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
	$data['kodeorg'] = '';
	$data['periodegaji'] = '0';
	$data['keterangan'] = '';
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $whereOrg = "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
    $optPeriod = makeOption($dbname,'sdm_5periodegaji','periode,periode',
        "kodeorg='".$_SESSION['empl']['lokasitugas']."'");
    
    $els = array();
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optOrg)
    );
    $els[] = array(
	makeElement('periodegaji','label',$_SESSION['lang']['periodegaji']),
	makeElement('periodegaji','select',$data['periodegaji'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optPeriod)
    );
    $els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
	makeElement('keterangan','text',$data['keterangan'],
            array('style'=>'width:150px','maxlength'=>'50'))
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
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,1);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,1);
    }
}
?>