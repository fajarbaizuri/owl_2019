<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

?>

<?

$proses = $_GET['proses'];
$param = $_POST;

// Total Summary / Allocation Control
$total = "<fieldset style='height:114px'><legend><b>Total</b></legend>";
$total .= "<table>";
$total .= "<tr>";
$total .= "<td colspan='2'><b>".$_SESSION['lang']['prestasi']."</b></td>";
$total .= "<td colspan='2'><b>".$_SESSION['lang']['absensi']."</b></td>";
$total .= "</tr>";
$total .= "<tr>";
$total .= "<td>".$_SESSION['lang']['jumlahhk']."</td>";
$total .= "<td>".makeElement('totalPresHk','textnum',0,
    array('style'=>'width:70px','disabled'=>'disabled','realValue'=>0))."</td>";
$total .= "<td>".$_SESSION['lang']['jumlahhk']."</td>";
$total .= "<td>".makeElement('totalAbsHk','textnum',0,
    array('style'=>'width:70px','disabled'=>'disabled','realValue'=>0))."</td>";
$total .= "</tr>";
$total .= "<tr>";
$total .= "<td>".$_SESSION['lang']['umr']."</td>";
$total .= "<td>".makeElement('totalPresUmr','textnum',0,
    array('style'=>'width:70px','disabled'=>'disabled','realValue'=>0))."</td>";
$total .= "<td>".$_SESSION['lang']['umr']."</td>";
$total .= "<td>".makeElement('totalAbsUmr','textnum',0,
    array('style'=>'width:70px','disabled'=>'disabled','realValue'=>0))."</td>";
$total .= "</tr>";
$total .= "<tr>";
$total .= "<td>".$_SESSION['lang']['insentif']."</td>";
$total .= "<td>".makeElement('totalPresIns','textnum',0,
    array('style'=>'width:70px','disabled'=>'disabled','realValue'=>0))."</td>";
$total .= "<td>".$_SESSION['lang']['insentif']."</td>";
$total .= "<td>".makeElement('totalAbsIns','textnum',0,
    array('style'=>'width:70px','disabled'=>'disabled','realValue'=>0))."</td>";
$total .= "</tr></table>";
$total .= makeElement('tmpValHk','hidden',0);
$total .= makeElement('tmpValUmr','hidden',0);
$total .= makeElement('tmpValIns','hidden',0);
$total .= "</fieldset>";

switch($proses) {
    # Daftar Header
    case 'showHeadList':
	if(isset($param['where'])) {
	    $tmpW = str_replace('\\','',$param['where']);
	    $arrWhere = json_decode($tmpW,true);
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
        if($param['tipe']=='PNN'){
            $header = array(
                'Nomor','Organisasi','Tanggal','Mandor','Mandor1','Kerani Produksi','Kerani Muat'
            );
        }
        else
        {
            $header = array(
                'Nomor','Organisasi','Tanggal','Mandor','Mandor1','Asisten','Kerani AFD'
            );            
        }    
	
	# Content
	if(is_null($where)) {
	    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	} else {
	    $where .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	}
	if(strlen($param['tipe'])==2) {
	    $where .= " and substr(notransaksi,15,2)='".$param['tipe']."'";
	} elseif(strlen($param['tipe'])==3) {
	    $where .= " and substr(notransaksi,15,3)='".$param['tipe']."'";
	}
	/// 
	// $where .= " and tanggal >='2014-01-01' and tanggal <='2014-01-31' and jurnal=0";
	$cols = "notransaksi,kodeorg,tanggal,nikmandor,nikmandor1,nikasisten,keranimuat,jurnal";
	$query = selectQuery($dbname,'kebun_aktifitas',$cols,$where,
	"tanggal desc, notransaksi desc",false,$param['shows'],$param['page']);
	//"jurnal asc",false,$param['shows'],$param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'kebun_aktifitas',$where);
	if(!empty($data)) {
	    $whereKarRow = "karyawanid in (";
	    $notFirst = false;
	    foreach($data as $key=>$row) {
		if($row['jurnal']==1) {
		    $data[$key]['switched']=true;
		}
		$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		unset($data[$key]['jurnal']);
		
		if($notFirst==false) {
		    if($row['nikmandor']!='') {
			$whereKarRow .= $row['nikmandor'];
			$notFirst=true;
		    }
		    if($row['nikmandor1']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['nikmandor1'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['nikmandor1'];
			}
		    }
		    if($row['nikasisten']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['nikasisten'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['nikasisten'];
			}
		    }
		    if($row['keranimuat']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['keranimuat'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['keranimuat'];
			}
		    }
		} else {
		    if($row['nikmandor']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['nikmandor'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['nikmandor'];
			}
		    }
		    if($row['nikmandor1']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['nikmandor1'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['nikmandor1'];
			}
		    }
		    if($row['nikasisten']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['nikasisten'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['nikasisten'];
			}
		    }
		    if($row['keranimuat']!='') {
			if($notFirst==false) {
			    $whereKarRow .= $row['keranimuat'];
			    $notFirst=true;
			} else {
			    $whereKarRow .= ",".$row['keranimuat'];
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
	    isset($optKarRow[$row['nikmandor']]) ? $dataShow[$key]['nikmandor'] = $optKarRow[$row['nikmandor']]:null;
	    isset($optKarRow[$row['nikmandor1']]) ? $dataShow[$key]['nikmandor1'] = $optKarRow[$row['nikmandor1']]:null;
	    isset($optKarRow[$row['nikasisten']]) ? $dataShow[$key]['nikasisten'] = $optKarRow[$row['nikasisten']]:null;
	    isset($optKarRow[$row['keranimuat']]) ? $dataShow[$key]['keranimuat'] = $optKarRow[$row['keranimuat']]:null;
	}
	
	# Posting --> Jabatan
	if($param['tipe']=='PNN') {
	    $app = 'panen';
	} else {
	    $app = 'rawatkebun';
	}
	$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='".$app."'");
	$tmpPost = fetchData($qPosting);
	$postJabatan = $tmpPost[0]['jabatan'];
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
	#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->_actions[0]->addAttr($param['tipe']);
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");
	$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
	if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
	    $tHeader->_actions[2]->_name='';
	}
	//if($param['tipe']!='PNN') {
	    $tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
	    $tHeader->_actions[3]->addAttr('event');
	    $tHeader->_actions[3]->addAttr($param['tipe']);
	    $tHeader->_switchException = array('detailPDF');
	//}
	//$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	$tHeader->setWhere($arrWhere);
	
	
	# View
	$tHeader->renderTable();
	break;
    # Form Add Header
    case 'showAdd':
	// View
	echo formHeader('add',$_POST['tipe'],array());
	if($param['tipe']!='PNN') {
	    echo $total;
	}
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Form Edit Header
    case 'showEdit':
	$query = selectQuery($dbname,'kebun_aktifitas',"*","notransaksi='".$param['notransaksi']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	echo formHeader('edit',$_SESSION['tmp']['kebun']['tipeTrans'],$data);
	if($param['tipe']!='PNN') {
	    echo $total;
	}
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	# Blank field validation
	$data = $_POST;
	if($data['tanggal']=='') {
	    echo "Validation Error : Date must not empty";
	    break;
	}
	
	# Data Capture & Reform
	$data['tipetransaksi'] = $_GET['tipe'];
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	
	#=== Generate No Transaksi
	# Get Existing Data
	$fWhere = "tanggal='".$data['tanggal']."' and kodeorg='".$data['kodeorg'].
	    "' and tipetransaksi='".$data['tipetransaksi']."'";
	$fQuery = selectQuery($dbname,'kebun_aktifitas','notransaksi',$fWhere);
	$tmpNo = fetchData($fQuery);
	
	# Generate No Transaksi
	if(count($tmpNo)==0) {
	    $data['notransaksi'] = $data['tanggal']."/".$data['kodeorg']."/".
		$data['tipetransaksi']."/001";
	} else {
	    # Get Max No Urut
	    $maxNo = 1;
	    foreach($tmpNo as $row) {
		$tmpRow = explode('/',$row['notransaksi']);
		$noUrut = (int)$tmpRow[3];
		if($noUrut>$maxNo)
		    $maxNo = $noUrut;
	    }
	    $currNo = addZero($maxNo+1,3);
	    $data['notransaksi'] = $data['tanggal']."/".$data['kodeorg']."/".
		$data['tipetransaksi']."/".$currNo;
	}
	
	$cols = array('notransaksi','kodeorg','tanggal','nikmandor',
	    'nikmandor1','nikasisten','keranimuat','tipetransaksi');
	$query = insertQuery($dbname,'kebun_aktifitas',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    echo $data['notransaksi'];
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "notransaksi='".$data['notransaksi']."'";
	unset($data['notransaksi']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$query = updateQuery($dbname,'kebun_aktifitas',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."'";
	$query = "delete from `".$dbname."`.`kebun_aktifitas` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}

function formHeader($mode,$tipe,$data) {
    global $dbname;
    global $param;
    
    # Default Value
    if(empty($data)) {
	$data['notransaksi'] = '';
	$data['kodeorg'] = '';
	$data['tanggal'] = '';
	$data['nikmandor'] = '';
	$data['nikmandor1'] = '';
	$data['nikasisten'] = '';
	$data['keranimuat'] = '';
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
	
    
    # Options
	
	
	/*$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('0','1','2','3')";
	$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00')";
	
	
    if($mode=='edit') {
	$whereOrg = "kodeorganisasi='".$data['kodeorg']."' and tipe<>'BLOK'";
    } else {
	$whereOrg = "left(kodeorganisasi,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and tipe='KEBUN'";
    }
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
    #$optOrg = getOrgBelow($dbname,$_SESSION['org']['kodeorganisasi'],false,'afdeling');
    $optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary);
    $optKary1 = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary,'0',true);
*/	

####INDRA UPDATE 
 //  $whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan<>1";
 	$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";
	$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan";
	
	 $whereKary2 = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('0','2')";
	$whereKary2 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan";
	
    if($mode=='edit') {
	$whereOrg = "kodeorganisasi='".$data['kodeorg']."' and tipe<>'BLOK'";
    } else {
	$whereOrg = "left(kodeorganisasi,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and tipe='KEBUN'";
    }
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
	
	$queryKary = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary;
			
			//echo $queryKary;
	$resKary = fetchData($queryKary);
	$optKary = array();
	foreach($resKary as $row) {
		$optKary[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
			$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	$optKary1 = array(''=>'');
	foreach($optKary as $key=>$row) {
		$optKary1[$key] = $row;
	}
    
	
	//Assisten  Afdeling
	$whereKary3 = "kodejabatan in ('27','100','101','125','127','153','168','222','236','242','274','307') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('0','2')";
	$whereKary3 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan asc";
	
	$queryKary3 = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary3;
			
	$resKary3 = fetchData($queryKary3);
	$optKary3 = array();
	foreach($resKary3 as $row) {
		$optKary3[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
			$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	$optKary3a = array(''=>'');
	foreach($optKary3 as $key=>$row) {
		$optKary3a[$key] = $row;
	}
	
	//Kerani  Afdeling
	$whereKary4 = "kodejabatan in ('70') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";
	$whereKary4 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan asc";
	
	$queryKary4 = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary4;
			
	$resKary4 = fetchData($queryKary4);
	$optKary4 = array();
	foreach($resKary4 as $row) {
		$optKary4[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
			$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	$optKary4a = array(''=>'');
	foreach($optKary4 as $key=>$row) {
		$optKary4a[$key] = $row;
	}
	
	//mandor1 
	$whereKary5 = "kodejabatan in ('37','109') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";
	$whereKary5 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan asc";
	
	$queryKary5 = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary5;
			
	$resKary5 = fetchData($queryKary5);
	$optKary5 = array();
	foreach($resKary5 as $row) {
		$optKary5[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
			$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	$optKary5a = array(''=>'');
	foreach($optKary5 as $key=>$row) {
		$optKary5a[$key] = $row;
	}
	
	
	//mandor
	$whereKary6 = "kodejabatan in ('62','71','73','81','104','105','106','121','122','123','126','157','158','164','165','166','172','174','178','179','180','183','234','239','241','277','285','293','294','298','303','306') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";
	$whereKary6 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan asc";
	
	$queryKary6 = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary6;
			
	$resKary6 = fetchData($queryKary6);
	$optKary6 = array();
	foreach($resKary6 as $row) {
		$optKary6[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
			$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	$optKary6a = array(''=>'');
	foreach($optKary6 as $key=>$row) {
		$optKary6a[$key] = $row;
	}
	
	
	//kerani panen
	$whereKary7 = "kodejabatan in ('102','103') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";
	$whereKary7 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan asc";
	
	$queryKary7 = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary7;
			
	$resKary7 = fetchData($queryKary7);
	$optKary7 = array();
	foreach($resKary7 as $row) {
		$optKary7[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
			$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	$optKary7a = array(''=>'');
	foreach($optKary7 as $key=>$row) {
		$optKary7a[$key] = $row;
	}
	$queryKary2 = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary2;
			
			//echo $queryKary;
	$resKary2 = fetchData($queryKary2);
		$optKary2 = array();
		foreach($resKary2 as $row) {
			$optKary2[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
				$row['tipe'].'/'.$row['subbagian'].")";
		}
	####################################################################################################################
	
	
    $els = array();
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','disabled'=>'disabled'))
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optOrg)
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)',$disabled=>$disabled))
    );
    $els[] = array(
	makeElement('nikmandor','label',$_SESSION['lang']['nikmandor']),
	makeElement('nikmandor','select',$data['nikmandor'],array('style'=>'width:150px'),$optKary6a)
    );
    $els[] = array(
	makeElement('nikmandor1','label',$_SESSION['lang']['nikmandor1']),
	makeElement('nikmandor1','select',$data['nikmandor1'],array('style'=>'width:150px'),$optKary5a)
    );

    if($param['tipe']=='PNN') {
        $els[] = array(
            makeElement('nikasisten','label','Kerani Produksi'),
            makeElement('nikasisten','select',$data['nikasisten'],array('style'=>'width:150px'),$optKary7a)
            );        
		$els[] = array(
			makeElement('keranimuat','label',$_SESSION['lang']['keranimuat']),
			makeElement('keranimuat','select',$data['keranimuat'],array('style'=>'width:150px'),$optKary1)
		);
    } else {
        $els[] = array(
            makeElement('nikasisten','label',$_SESSION['lang']['nikasisten']),
            makeElement('nikasisten','select',$data['nikasisten'],array('style'=>'width:150px'),$optKary3a)
            );        
		$els[] = array(
			makeElement('keranimuat','label',$_SESSION['lang']['keraniafdeling']),
			makeElement('keranimuat','select',$data['keranimuat'],array('style'=>'width:150px'),$optKary4a)
            );
    }
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable('".$tipe."')"))
	);
    } elseif($mode=='edit') {
	$els['btn'] = array(
	    makeElement('editHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"editDataTable('".$tipe."')"))
	);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>