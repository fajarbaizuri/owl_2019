<?
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;
foreach($_GET as $k=>$p) {
	$param[$k] = $p;
}

switch($proses) {
    # Daftar Header
    case 'showHeadList':
		if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
			$_SESSION['empl']['tipelokasitugas']=='HOLDING' or
			$_SESSION['empl']['tipelokasitugas']=='KANWIL') {
			$where = "length(kodeorg)=4";
		} else {
			$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		}
		if(isset($param['where'])) {
			$arrWhere = json_decode(str_replace('\\','',$param['where']),true);
			if(!empty($arrWhere)) {
			foreach($arrWhere as $key=>$r1) {
				$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
			}
			}
		}
		
		# Header
		$header = array(
			$_SESSION['lang']['kodeorg'],
			$_SESSION['lang']['notransaksi'],
			$_SESSION['lang']['tanggal'],
			$_SESSION['lang']['subunit'],
			$_SESSION['lang']['koderekanan'],
			$_SESSION['lang']['nilaikontrak'],
			$_SESSION['lang']['jumlahrealisasi'],
			$_SESSION['lang']['status']
		);
		
		# Content
		$cols = "kodeorg,notransaksi,tanggal,divisi,koderekanan,nilaikontrak,posting";
		$query = selectQuery($dbname,'log_spkht',$cols,$where." order by tanggal desc","",false,$param['shows'],$param['page']);
			$data = fetchData($query);
		$totalRow = getTotalRow($dbname,'log_spkht',$where);
		foreach($data as $key=>$row) {
			$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
			//=================ambil realisasi
			$data[$key]['realisasi'] =0;
			$strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
				  where notransaksi='".$data[$key]['notransaksi']."'";
			$resx=mysql_query($strx);
			while($barx=mysql_fetch_array($resx))
			{
			  $data[$key]['realisasi']= number_format($barx[0]); 
			}
			
			// Cek Alur Persetujuan
			$qCek = "select * from $dbname.log_persetujuanspk ".
				"where kodeorg='".$row['kodeorg']."' and notransaksi='".$row['notransaksi'].
				"' order by level desc limit 0,1";
			$resCek = fetchData($qCek);
			
			// Set Status
			if(!empty($resCek)) {
				$data[$key]['switched']=1;
				if($resCek[0]['level']==5 and $resCek[0]['tanggal']!='0000-00-00') {
					$data[$key]['status'] = "SPK";
				} else {
					$data[$key]['status'] = "Persetujuan Tahap ".$resCek[0]['level'];
				}
			} else {
				$data[$key]['status'] = "Usulan";
			}
			if($row['posting']==1) {
				$data[$key]['switched']=1;
				$data[$key]['status'] = "SPK";
			}
			unset($data[$key]['posting']);
		}
		
		# Options
		if(!empty($data)) {
			$whereSupp = "supplierid in (";
			foreach($data as $key=>$row) {
			  if($key==0) {
			$whereSupp .= "'".$row['koderekanan']."'";
			  } else {
			$whereSupp .= ",'".$row['koderekanan']."'";
			  }
			}
			$whereSupp .= ")";
		} else {
			$whereSupp = null;
		}
		$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',
			$whereSupp);
		
		# Data Show
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
		  $dataShow[$key]['koderekanan'] = $optSupp[$row['koderekanan']];
		  $dataShow[$key]['nilaikontrak'] = number_format($row['nilaikontrak'],0);
		}
		$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='panen'");
		$tmpPost = fetchData($qPosting);
		$postJabatan = $tmpPost[0]['jabatan'];
		# Make Table
		$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
		$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
		$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
		$tHeader->addAction('pengajuan','Pengajuan','images/'.$_SESSION['theme']."/posting.png");
		$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
		$tHeader->_actions[2]->addAttr('event');
		$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
		$tHeader->_actions[3]->addAttr('event');
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
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."'";
		$query = selectQuery($dbname,'log_spkht',"*",$where);
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
		if($data['notransaksi']=='') {$warning .= "No SPK harus diisi\n";}
		if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
		if($data['kodeorg']=='') {$warning = "Lokasi Tugas harus Kebun\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['nilaikontrak'] = str_replace(',','',$data['nilaikontrak']);
		
		$cols = array('kodeorg','notransaksi','tanggal','divisi',
			'koderekanan','nilaikontrak');
		$query = insertQuery($dbname,'log_spkht',$data,$cols);
		
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
		$data['nilaikontrak'] = str_replace(',','',$data['nilaikontrak']);
		
		$query = updateQuery($dbname,'log_spkht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
        //================periksa realisasi
		$m =0;
		$strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
			  where notransaksi='".$param['notransaksi']."'";
		$resx=mysql_query($strx);
		while($barx=mysql_fetch_array($resx))
		{
		  $m= $barx[0]; 
		}   
		//lihat postingan-=============================
		$n ='';
		$strx="select statusjurnal from ".$dbname.".log_baspk 
			  where notransaksi='".$param['notransaksi']."' and statusjurnal=0";
		$resx=mysql_query($strx);           
		if(mysql_num_rows($resx)>0)
		$n ='?';
		
		if($n=='' and $m==0)
		{     
        //=================================
			$where = "notransaksi='".$param['notransaksi']."'";
			$query = "delete from `".$dbname."`.`log_spkht` where ".$where;
			if(!mysql_query($query)) {
				echo "DB Error : ".mysql_error();
				exit;
			}
		}
		else
		{
			exit('Error:Realisasi sudah terisi');
		}
		break;
    case 'updSub':
		$whereDiv = "induk='".$param['kodeorg']."' or kodeorganisasi='".
			$param['kodeorg']."'";
		$optDiv = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereDiv);
		echo json_encode($optDiv);
		break;
	case 'pengajuan':
		$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan,lokasitugas',
			"tipekaryawan=0",4);
		$res = "<fieldset><legend style='font-weight:bold'>Pengajuan SPK ".$param['notransaksi']."</legend>";
		$res .= "<span>Ajukan ke :</span>";
		$res .= makeElement('noTransAjukan','hidden',$param['notransaksi']);
		$res .= makeElement('kodeOrgAjukan','hidden',$param['kodeorg']);
		$res .= makeElement('kodeRekananAjukan','hidden',$param['koderekanan']);
		$res .= makeElement('karyAjukan','select','',array('style'=>'width:200px'),$optKary);
		$res .= makeElement('karyAjukanBtn','button','Ajukan',array('onclick'=>'ajukan()'));
		$res .= "</fieldset>";
		echo $res;
		break;
	case 'prosesPengajuan':
		$data = array(
			'kodeorg'=>$param['kodeorg'],
			'notransaksi'=>$param['noTrans'],
			'level'=>1,
			'status'=>0,
			'tanggal'=>'0000-00-00',
			'penyutuju'=>$param['nik'],
			'catatan'=>''
		);
		$qIns = insertQuery($dbname,'log_persetujuanspk',$data);
		if(!mysql_query($qIns)) {
			exit("DB Error: ".mysql_error());
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
	$data['notransaksi'] = '0';
	$data['tanggal'] = '';
	$data['divisi'] = '';
	$data['koderekanan'] = '';
	$data['nilaikontrak'] = '0';
    } else {
	$data['nilaikontrak'] = number_format($data['nilaikontrak']);
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
	$_SESSION['empl']['tipelokasitugas']=='HOLDING' or
	$_SESSION['empl']['tipelokasitugas']=='KANWIL') {
	$whereOrg = "length(kodeorganisasi)=4";
    } else {
	$whereOrg = "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    }
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
    if($data['divisi']=='') {
	if($_SESSION['empl']['tipelokasitugas']=='TRAKSI') {
	    $whereDiv = "induk='".getFirstKey($optOrg)."'";
	} else {
	    $whereDiv = "induk='".$_SESSION['empl']['lokasitugas']."'";
	}
	$whereDiv .= " or kodeorganisasi='".getFirstKey($optOrg)."'";
    } else {
	$whereDiv = "kodeorganisasi='".$data['divisi']."'";
    }
    $optDiv = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereDiv);
    $optSup = makeOption($dbname,'log_5supplier','supplierid,namasupplier',"left(kodekelompok,1)='K'");
    
    $els = array();
    if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
	$_SESSION['empl']['tipelokasitugas']=='HOLDING' or
	$_SESSION['empl']['tipelokasitugas']=='KANWIL') {
	$els[] = array(
	    makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	    makeElement('kodeorg','select',$data['kodeorg'],
		array('style'=>'width:150px',$disabled=>$disabled,'onchange'=>'updSub()'),$optOrg)
	);
    } else {
	$els[] = array(
	    makeElement('kodeorg','label',$_SESSION['lang']['kebun']),
	    makeElement('kodeorg','select',$data['kodeorg'],
		array('style'=>'width:150px',$disabled=>$disabled),$optOrg)
	);
    }
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','maxlength'=>'25',$disabled=>$disabled))
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('divisi','label',$_SESSION['lang']['subunit']),
	makeElement('divisi','select',$data['divisi'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optDiv)
    );
    $els[] = array(
	makeElement('koderekanan','label',$_SESSION['lang']['koderekanan']),
	makeElement('koderekanan','select',$data['koderekanan'],
            array('style'=>'width:150px'),$optSup)
    );
    $els[] = array(
	makeElement('nilaikontrak','label',$_SESSION['lang']['nilaikontrak']),
	makeElement('nilaikontrak','textnum',$data['nilaikontrak'],
	    array('style'=>'width:150px','maxlength'=>'15',
		'this.value=remove_comma(this);onchange'=>'this.value = _formatted(this)'))
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
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>