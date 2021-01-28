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
		if(isset($param['where'])) {
			$arrWhere = json_decode($param['where'],true);
			$where = "";
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
			'No. Invoice','Unit','Tanggal','No Order','Keterangan'
		);
		
		# Content
		$cols = "noinvoice,kodeorg,tanggal,noorder,keterangan";
		$query = selectQuery($dbname,'keu_penagihanht',$cols,$where,"",false,$param['shows'],$param['page']);
		$data = fetchData($query);
		$totalRow = getTotalRow($dbname,'keu_penagihanht',$where);
		foreach($data as $key=>$row) {
			$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
			//$optketerangan = array(0=>'-',1=>'Tidak Dipungut PPN/PPnBM eks PP No. 32 Tahun 2009');
			if($data[$key]['keterangan']=='0')
			{
				$data[$key]['keterangan'] = '';
			}
			else
			$data[$key]['keterangan'] = 'PPN Tidak Dipungut Sesuai PP Tempat Penimbunan Berikat';
			
		}
		
		# Make Table
		$tHeader = new rTable('headTable','headTableBody',$header,$data);
		$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
		$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
		$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
		$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
		
		$tHeader->addAction('detailPDF','Invoice','images/'.$_SESSION['theme']."/pdf.jpg");//tambahan indra
		$tHeader->_actions[3]->addAttr('event');//tambahan indra
		$tHeader->addAction('detailPDF1','Invoice Dengan PPn DP','images/'.$_SESSION['theme']."/pdf.jpg");//tambahan indra
		$tHeader->_actions[4]->addAttr('event');//tambahan indra
		$tHeader->addAction('detailPDF2','Kuitansi','images/'.$_SESSION['theme']."/pdf.jpg");//tambahan indra
		$tHeader->_actions[5]->addAttr('event');//tambahan indra
		$tHeader->_switchException = array('detailPDF');//tambahan indra
		$tHeader->_switchException = array('detailPDF2');//tambahan indra
		
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
	$query = selectQuery($dbname,'keu_penagihanht',"*","noinvoice='".$param['noinvoice']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	$data['jatuhtempo'] = tanggalnormal($data['jatuhtempo']);
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
		$data = $_POST;
		
		// Error Trap
		$warning = "";
		if($data['noinvoice']=='') {$warning .= "No Tagihan harus diisi\n";}
		if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['nilaiinvoice'] = str_replace(',','',$data['nilaiinvoice']);
		if($data['jatuhtempo']!='') {
			$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
		} else {
			$data['jatuhtempo'] = '0000-00-00';
		}
		#$data['tipeinvoice'] = substr($data['tipeinvoice'],0,1);
		$cols = array();
		foreach($data as $key=>$row) {
			$cols[] = $key;
		}
		$query = insertQuery($dbname,'keu_penagihanht',$data,$cols);
		
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
		$data = $_POST;
		$where = "noinvoice='".$data['noinvoice']."'";
		unset($data['noinvoice']);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
		#$data['tipeinvoice'] = substr($data['tipeinvoice'],0,1);
		$data['nilaiinvoice'] = str_replace(',','',$data['nilaiinvoice']);
		$query = updateQuery($dbname,'keu_penagihanht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
	$where = "noinvoice='".$param['noinvoice']."'";
	$query = "delete from `".$dbname."`.`keu_penagihanht` where ".$where;
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
	//$data['noinvoice'] = date('Ymdhis');
	$data['noinvoice'] = '';
	$data['kodeorg'] = '';
	$data['tipeinvoice'] = 'po';
	$data['bayarke'] = '';
	$data['tanggal'] = '';
	$data['noorder'] = '';
	$data['jatuhtempo'] = '';
	$data['nofakturpajak'] = '';
	$data['keterangan'] = '';
	$data['uangmuka'] = '0';
	$data['nilaippn'] = '0';
	$data['nilaiinvoice'] = '0';
	$data['potongan'] = '0';
    } else {
	$data['nilaiinvoice'] = number_format($data['nilaiinvoice'],0);
	$tmpNopo = explode('/',$data['noorder']);
	if(count($tmpNopo)>5 and $tmpNopo[3]=='PO') {
	    $data['tipeinvoice']='po';
	} else {
	    $data['tipeinvoice']='kontrak';
	}
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $whereJam=" kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
	$optCust = makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer');
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereJam);//"kasbank=1 and detail=1");
    if($data['tipeinvoice']=='po') {
	$optPO = makeOption($dbname,'log_poht','nopo,nopo',"stat_release=1",'0',true);
    } else {
	$optPO = makeOption($dbname,'log_spkht','notransaksi,notransaksi',null,'0',true);
    }
    $optCgt = getEnum($dbname,'keu_kasbankht','cgttu');
    $optYn = array(0=>'Belum Posting',1=>'Sudah Posting');
	$optpungutan=array('0'=>'Dipungut','1'=>'Tidak Dipungut');
	
	$optketerangan = array(0=>'-',1=>'PPN Tidak Dipungut Sesuai PP Tempat Penimbunan Berikat');
	
	
    
    $els = array();
    $els[] = array(
	makeElement('noinvoice','label',$_SESSION['lang']['noinvoice']),
	makeElement('noinvoice','text',$data['noinvoice'],
	    array('style'=>'width:150px','maxlength'=>'25',$disabled=>$disabled))
    );//,$disabled=>$disabled
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px'),$optOrg)
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    /*$els[] = array(
	makeElement('tipeinvoice','label',$_SESSION['lang']['jenis']),
	makeElement('tipeinvoice','select',$data['tipeinvoice'],
	    array('style'=>'width:150px',$disabled=>$disabled,'onchange'=>'updPO()'),
	    array('po'=>'PO','kontrak'=>'Kontrak'))
    );*/
	$els[] = array(
	makeElement('kodecustomer','label',$_SESSION['lang']['kodecustomer']),
	makeElement('kodecustomer','select',$data['kodecustomer'],
	    array('style'=>'width:150px'),$optCust)
    );
    $els[] = array(
	makeElement('noorder','label',$_SESSION['lang']['noorder']),
	makeElement('noorder','text',$data['noorder'],
	    array('style'=>'width:150px'))
    );
    
	
	/*$els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
	makeElement('keterangan','text',$data['keterangan'],array('style'=>'width:150px'))
    );
	
	$els[] = array(
	makeElement('kelompokpembayaran','label',$_SESSION['lang']['kelompokpembayaran']),
	makeElement('kelompokpembayaran','select',$data['kelompokpembayaran'],
	    array('style'=>'width:150px'),$optkelompok)
    );*/
	
	$els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
	makeElement('keterangan','select',$data['keterangan'],
		array('style'=>'width:150px'),$optketerangan)
    );
	
	
	
    $els[] = array(
	makeElement('jatuhtempo','label',$_SESSION['lang']['jatuhtempo']),
	makeElement('jatuhtempo','text',$data['jatuhtempo'],
	    array('style'=>'width:150px','readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('nofakturpajak','label',$_SESSION['lang']['nofp']),
	makeElement('nofakturpajak','text',$data['nofakturpajak'],
	    array('style'=>'width:150px','maxlength'=>'20'))
    );
    $els[] = array(
	makeElement('bayarke','label',$_SESSION['lang']['bayarke']),
	makeElement('bayarke','select',$data['bayarke'],
	    array('style'=>'width:150px'),$optAkun)
    );
    $els[] = array(
	makeElement('uangmuka','label',$_SESSION['lang']['uangmuka']),
	makeElement('uangmuka','textnum',$data['uangmuka'],
	    array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('nilaippn','label',$_SESSION['lang']['nilaippn'].' (%)'),
	makeElement('nilaippn','textnum',$data['nilaippn'],
	    array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('nilaiinvoice','label',$_SESSION['lang']['nilaiinvoice']),
	makeElement('nilaiinvoice','textnum',$data['nilaiinvoice'],
	    array('style'=>'width:150px','disabled'=>'disabled','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );//$disabled=>$disabled
    
	#tambahan lagi indra
	$els[] = array(
	makeElement('pungutan','label','Pungutan Pembayaran'),
	makeElement('pungutan','select',$data['pungutan'],
	    array('style'=>'width:150px'),$optpungutan)
    );
	 $els[] = array(
	makeElement('potongan','label',$_SESSION['lang']['potongan']),
	makeElement('potongan','textnum',$data['potongan'],
	    array('style'=>'width:150px'))
    );
	## tutup tambahan indra
	
	
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
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>