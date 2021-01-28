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
		//$where = "kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
		$where = "";
		if(isset($param['where'])) {
			$tmpW = str_replace('\\','',$param['where']);
			$arrWhere = json_decode($tmpW,true);
			if(!empty($arrWhere)) {
				foreach($arrWhere as $key=>$r1) {
					$where .= " and ".$r1[0]." = '".$r1[1]."'";
				}
			} 
		}
		
		# Header
		$header = array('No Transaksi','Unit','Tanggal','Pemeriksa');
		
		## Get Periode
		$tanggal1 = $_SESSION['org']['period']['start'];
		$tanggal2 = $_SESSION['org']['period']['end'];
		
		# Content
		$cols = "notransaksi,kodeorg,tanggal,diperiksa";
		$order="notransaksi desc";
		$query = selectQuery($dbname,'pabrik_ops_boilerht',$cols,"tanggal between '".
			$tanggal1."' and '".$tanggal2."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'".$where,$order,false,10,1);
		$data = fetchData($query);
		$totalRow = getTotalRow($dbname,'pabrik_ops_boilerht');
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
		$query = selectQuery($dbname,'pabrik_ops_boilerht',"*",
			"notransaksi='".$param['notransaksi']."'");
		$tmpData = fetchData($query);
		$data = $tmpData[0];
		$data['tanggal'] = tanggalnormal($data['tanggal']);
		echo formHeader('edit',$data);
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Proses Add Header
    case 'add':
		$tgl = tanggalsystem($param['tanggal']);
		
		## Get Periode
		$tanggal1 = $_SESSION['org']['period']['start'];
		$tanggal2 = $_SESSION['org']['period']['end'];
		
		// Error Trap
		$warning = "";
		if($param['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
		if($tgl<$tanggal1 or $tgl>$tanggal2) {
			$warning .= "Tanggal harus dalam periode ";
			$warning .= tanggalnormal($tanggal1)." s/d ".tanggalnormal($tanggal2);
		}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$tmp = explode('-',$param['tanggal']);
		$noTrans = $param['kodeorg'].'/'.$tmp[2].'/'.$tmp[1].'/'.$tmp[0];
		$data = array(
			'notransaksi'=>$noTrans,
			'kodeorg'=>$param['kodeorg'],
			'tanggal'=>tanggalsystem($param['tanggal']),
			'diperiksa'=>$param['diperiksa'],
			'dibuat'=>$_SESSION['standard']['userid']
		);
		$cols = array();
		foreach($data as $key=>$row) {
			$cols[] = $key;
		}
		$query = insertQuery($dbname,'pabrik_ops_boilerht',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		} else {
			echo $noTrans;
		}
		break;
    # Proses Edit Header
    case 'edit':
		$data = $_POST;
		$where = "notransaksi='".$data['notransaksi']."'";
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$query = updateQuery($dbname,'pabrik_ops_boilerht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi']."'";
		$query = "delete from `".$dbname."`.`pabrik_ops_boilerht` where ".$where;
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
		$data['notransaksi'] = '';
		$data['kodeorg'] = '';
		$data['tanggal'] = '';
		$data['diperiksa'] = '0';
    }
    
	# Options
	$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
		"lokasitugas='".$_SESSION['empl']['lokasitugas']."'");
	$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
		"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
	
    # Disabled Primary
    $disabled = ($mode=='edit')?'disabled':'';
    
    $els = array();
    $els[] = array(
		makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
		makeElement('notransaksi','text',$data['notransaksi'],array('style'=>'width:150px',
			'readonly'=>'readonly',$disabled=>$disabled))
    );
	$els[] = array(
		makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
		makeElement('kodeorg','select',$data['kodeorg'],array('style'=>'width:150px',
			$disabled=>$disabled),$optOrg)
    );
	$els[] = array(
		makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
		makeElement('tanggal','date',$data['tanggal'],array('style'=>'width:150px',
			'readonly'=>'readonly',$disabled=>$disabled))
    );
	$els[] = array(
		makeElement('diperiksa','label',$_SESSION['lang']['diperiksa']),
		makeElement('diperiksa','select',$data['diperiksa'],array('style'=>'width:150px'),$optKary)
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
	return genElementMultiDim($_SESSION['lang']['header'],$els,1);
    }
}
?>