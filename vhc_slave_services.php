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
	case 'getPlat':
		$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` a".
			"  where kelompokvhc='".$param['alat']."' and `kodeorg`='".$param['kdorg']."' ";
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$optKdvhc="<option value='' ></option>";
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['kodevhc']."' >".$res['nopol']." [".$res['kodevhc']."]</option>";
		}
		echo $optKdvhc;
	break;
	case 'getDriv':

		$whereKary = "kodejabatan in ('178',
'179',
'180',
'174',
'234',
'306') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";

 	
	$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') order by namakaryawan";
	/*
	 $whereKary2 = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan='0'";
	$whereKary2 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') order by namakaryawan";
	*/
   
	$queryKary = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary;
			//echo $queryKary;
	$resKary = fetchData($queryKary);
	$optKary = array(''=>'');
	foreach($resKary as $row){
		$optKary[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
		$row['tipe'].'/'.$row['subbagian'].")";
	}
	
		$queryKaryA ="select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary;
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$optKdvhc="<option value='' ></option>";
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['karyawanid']."' >".$res['namakaryawan']." [".$res['nik']."]</option>";
		}
		echo $optKdvhc;
	break;
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
      $header = array(
           'Nomor','Tanggal','Kelompok','Plat No.','Jenis','Mekanik','Waktu Pengerjaan (Jam)'
       );            

	# Content
	if(is_null($where)) {
		if (substr($_SESSION['empl']['lokasitugas'],0,4) != 'TKFB'){
			 $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		}else{
			$where= "kodeorg like '%%'";
		}
	   
	} else {
		if (substr($_SESSION['empl']['lokasitugas'],0,4) != 'TKFB'){
			 $where .= "and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		}else{
			$where.= "and kodeorg like '%%'";
		}
	}
	
	/// 
	// $where .= " and tanggal >='2014-01-01' and tanggal <='2014-01-31' and jurnal=0";
	$cols = "notransaksi,tanggal,kelompok,nopol,jenisnm,mekanik,waktu,posting";
	$query = selectQuery($dbname,'vhc_service_vw',$cols,$where,
	"tanggal desc, notransaksi desc",false,$param['shows'],$param['page']);
	//"jurnal asc",false,$param['shows'],$param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'vhc_service_vw',$where);
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
	    if($row['mekanik']!='' && $row['mekanik']!=NULL) {
		$whereKarRow .= $row['mekanik'];
		$notFirst=true;
	    }

	} else {
	    if($row['mekanik']!='' && $row['mekanik']!=NULL) {
		if($notFirst==false) {
		    $whereKarRow .= $row['mekanik'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['mekanik'];
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
	    isset($optKarRow[$row['mekanik']]) ? $dataShow[$key]['mekanik'] = $optKarRow[$row['mekanik']]:null;
	}
	
	# Posting --> Jabatan
	$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='traksi'");
	$tmpPost = fetchData($qPosting);
	$postJabatan = $tmpPost[0]['jabatan'];
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
	#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	//$tHeader->_actions[0]->addAttr($param['tipe']);
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
	    //$tHeader->_actions[3]->addAttr($param['tipe']);
	    $tHeader->_switchException = array('detailPDF');
	//}
	//$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	$tHeader->setWhere($arrWhere);
	$tHeader->_print = false;
	# View
	$tHeader->renderTable();
	break;
	case'getPo':
		$optNmsupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
        $dat="<fieldset><legend></legend>";
        $dat.="<div style=overflow:auto;width:100%;height:310px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $dat.="<td>No. Ref</td>";
		$dat.="<td>Kelompok</td>";
        $dat.="<td>Plat. No</td></tr></thead><tbody>";
       
            if($param['txtfind']!='') {
                $where=" and (notransaksi like '%".$param['txtfind']."%' or kelompok like '%".$param['txtfind']."%' or nopol like '%".$param['txtfind']."%')  ";
            } else {
				$where="  ";
			}
		
			
		
			
			$sPo="SELECT DISTINCT notransaksi,kelompok,nopol FROM ".
				$dbname.".vhc_kendaraan_vw  ".
				"WHERE posting=1 AND kondisi='S'  ".$where." AND kodeorg='".
				$_SESSION['empl']['lokasitugas']."' and  notransaksi not in (select noref from ".$dbname.".vhc_serviceht where kodeorg ='".$_SESSION['empl']['lokasitugas']."')  ORDER BY tanggal desc";

		
		$qPo = fetchData($sPo);$no=0;
        foreach ($qPo as $rPo) {
            $no+=1;
            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['notransaksi']."','".$rPo['kelompok']."','".$rPo['nopol']."')\" style='pointer:cursor;'><td>".$no."</td>";
            $dat.="<td>".$rPo['notransaksi']."</td>";
            $dat.="<td>".$rPo['kelompok']."</td>";
			$dat.="<td align='right'>".$rPo['nopol']."</td></tr>";
        }
        $dat.="</tbody></table></div></fieldset>";
        echo $dat;
        break;
	case'getBrg':
		$key = $param['numRow'];
		$optNmsupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
        $dat="<fieldset><legend></legend>";
        $dat.="<div style=overflow:auto;width:100%;height:310px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $dat.="<td>Kode Barang</td>";
		$dat.="<td>Nama Barang</td>";
        $dat.="<td>Satuan</td></tr></thead><tbody>";
       
            if($param['txtfind']!='') {
                $where=" and (kodebarang like '%".$param['txtfind']."%' or namabarang like '%".$param['txtfind']."%' or satuan like '%".$param['txtfind']."%')  ";
            } else {
				$where="  ";
			}
		
			
		
			
			$sPo="SELECT DISTINCT kodebarang,namabarang,satuan FROM ".
				$dbname.".log_5masterbarang  ".
				"WHERE inactive=0 and kelompokbarang in ('319','800','316')  ".$where."   ORDER BY kodebarang desc";

		$qPo = fetchData($sPo);$no=0;
        foreach ($qPo as $rPo) {
            $no+=1;
            $dat.="<tr class='rowcontent' onclick=\"setBrg('".$rPo['kodebarang']."','".$rPo['namabarang']."','".$rPo['satuan']."',".$key.")\" style='pointer:cursor;'><td>".$no."</td>";
            $dat.="<td>".$rPo['kodebarang']."</td>";
            $dat.="<td>".$rPo['namabarang']."</td>";
			$dat.="<td align='right'>".$rPo['satuan']."</td></tr>";
        }
        $dat.="</tbody></table></div></fieldset>";
        echo $dat;
        break;
    # Form Add Header
    case 'showAdd':
	// View
	echo formHeader('add',$_POST['tipe'],array());
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Form Edit Header
    case 'showEdit':
	
	$query = selectQuery($dbname,'vhc_service_vw',"*","notransaksi='".$param['notransaksi']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
		
	$dataA=array(
	'notransaksi'=>$data['notransaksi'],
	'kodeorg'=>$data['kodeorg'],
	'kelompok'=>$data['kelompokvhc'],
	'tanggal'=>tanggalnormal($data['tanggal']),
	'plat'=>$data['kodevhc'],
	'jenis'=>$data['jenis'],
	'keterangan'=>$data['catatan'] ,
	'nikmekanik'=>$data['mekanik'] ,
	'waktu'=>$data['waktu'],
	'noref'=>$data['noref']
	);
	
	echo formHeader('edit',$data['kodeorg'],$dataA);
	
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	# Blank field validation
	$data = $_POST;
	if($data['tanggal']=='') {
	    echo "Validation Error : Tanggal Tidak Boleh Kosong.";
	    break;
	}
	if($data['kelompok']=='') {
	    echo "Validation Error : Kelompok Tidak Boleh Kosong.";
	    break;
	}
	if($data['kodevhc']=='') {
	    echo "Validation Error : Plat No. Tidak Boleh Kosong.";
	    break;
	}
	if($data['mekanik']=='') {
	    echo "Validation Error : Mekanik Tidak Boleh Kosong.";
	    break;
	}

	if($data['noref']=='') {
	    echo "Validation Error : No. Ref Tidak Boleh Kosong.";
	    break;
	}
	
	$data['posting']='0';
	
	# Data Capture & Reform
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	
	#=== Generate No Transaksi
	# Get Existing Data
	
	$fWhere = "DATE_FORMAT(tanggal,'%Y%m')='".substr($data['tanggal'], 0, 6)."' and kodeorg='".$data['kodeorg'].
	    "' ";
	$fQuery = selectQuery($dbname,'vhc_serviceht','notransaksi',$fWhere);
	$tmpNo = fetchData($fQuery);
	
	
	//getNamabarang
	$sDtBarang="select kodevhc,nopol from ".$dbname.".vhc_5master order by kodeorg asc";
	$rData=fetchData($sDtBarang);
	foreach($rData as $brBarang =>$rNamabarang)
	{
		$RkodeVHC[$rNamabarang['nopol']]=$rNamabarang['kodevhc'];
	}
	
	# Generate No Transaksi
	if(count($tmpNo)==0) {
	    $data['notransaksi'] = $data['tanggal']."/".$param['kodeorg']."/SER/001";
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
	    $data['notransaksi'] = $data['tanggal']."/".$param['kodeorg']."/SER/".$currNo;
	}
	
	 

	$dataA=array(
	'notransaksi'=>$data['notransaksi'] ,
	'kodeorg'=>$data['kodeorg'] ,
	'tanggal'=>$data['tanggal'] ,
	'kodevhc'=>$RkodeVHC[$data['kodevhc']] ,
	'jenis'=>$data['jenis'] ,
	'catatan'=>$data['catatan'] ,
	'mekanik'=>$data['mekanik'] ,
	'waktu'=>$data['waktu'] ,
	'noref'=>$data['noref'] ,
	'posting'=>'0' 
	);
	
	$cols = array('notransaksi','kodeorg','tanggal','kodevhc',
	    'jenis','catatan','mekanik','waktu','noref','posting');
	$query = insertQuery($dbname,'vhc_serviceht',$dataA,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    echo $data['notransaksi'];
	}
	

	break;
    # Proses Edit Header
    case 'edit':
	
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."'";
	$query = "delete from `".$dbname."`.`vhc_serviceht` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
	 case 'posting':
	$where = "notransaksi='".$param['notransaksi']."'";
	$query = "update `".$dbname."`.`vhc_serviceht` set posting=1,postingby='".$_SESSION['standard']['username']."' where ".$where;
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
	$data['tanggal'] = '';
	$data['kodeorg'] = '';
	$data['nikmekanik'] = '';
	$data['waktu'] = '0';
	$data['kelompok'] = '';
	$data['plat'] = '';
	$data['jenis'] = '';
	$data['keterangan'] = '';
	$data['noref'] = '';
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
	
    
    

####INDRA UPDATE 
 //  $whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan<>1";
	if($mode=='edit') {
		$whereKary = "kodejabatan in ('65',
'88',
'186')  and tipekaryawan in ('1','2','3','4','0')";
	}else{
		$whereKary = "kodejabatan in ('65',
'88',
'186') and lokasitugas in ('FBAO','TKFB') and tipekaryawan in ('1','2','3','4','0')";
}
 	
	$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') order by namakaryawan";
	/*
	 $whereKary2 = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan='0'";
	$whereKary2 .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00') order by namakaryawan";
	*/
   
	$queryKary = "select karyawanid,namakaryawan,nik,b.tipe,a.subbagian from `".$dbname."`.`datakaryawan` a".
			" left join `".$dbname."`.`sdm_5tipekaryawan` b on a.tipekaryawan=b.id where ".$whereKary;
	$resKary = fetchData($queryKary);
	$optKary = array(''=>'');
	foreach($resKary as $row){
		$optKary[$row['karyawanid']] = $row['namakaryawan'].' ('.$row['nik'].'/'.
		$row['tipe'].'/'.$row['subbagian'].")";
	}
	
	
	$optKOn = array('S01'=>'StandBy Rusak Alat/Kendaraan','S02'=>'StandBy Service Alat/Kendaraan');
	
	$optZero = array(''=>'');
	
	$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` ".
			"  where kodevhc='".$data['plat']."' ";
			$query = fetchData($queryKaryA);
			$optKdvhc = array(''=>'');
	foreach($query as $row){
		$optKdvhc[$row['kodevhc']] = $row['nopol'].' ['.$row['kodevhc'].']';
	}
	
	

	
	
	####################################################################################################################
	
	
    $els = array();
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','disabled'=>'disabled'))
    );
	$els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)',$disabled=>$disabled))
    );
	

	
	
	$els[] = array(
	makeElement('jenis','label','Jenis'),
	makeElement('jenis','select',$data['jenis'],array('style'=>'width:150px',$disabled=>$disabled),$optKOn)
    );

	
	$els[] = array(
	makeElement('keterangan','label','Keterangan'),
	makeElement('keterangan','text',$data['keterangan'],
	    array('style'=>'width:150px','maxlength'=>'100',$disabled=>$disabled))
    );
	
	$els[] = array(
	makeElement('noref','label','No. Ref Kegiatan'),
	makeElement('noref','text',$data['noref'],array('style'=>'width:150px',$disabled=>$disabled,'onclick'=>"searchNopo('".$_SESSION['lang']['find']." No. Ref','<fieldset><legend></legend><input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findNopo()>Find</button></fieldset><div id=container2></div>',event)"))
    );
	
	
	$els[] = array(
	makeElement('kelompok','label','Kelompok'),
	makeElement('kelompok','text',$data['kelompok'],
	    array('style'=>'width:150px','maxlength'=>'100','disabled'=>'disabled'))
    );
	
	
	$els[] = array(
	makeElement('plat','label','Plat No'),
	makeElement('plat','text',$data['plat'],
	    array('style'=>'width:150px','maxlength'=>'100','disabled'=>'disabled'))
    );
	
	

	if($mode=='add') {
    $els[] = array(
	makeElement('nikmekanik','label','Mekanik'),
	makeElement('nikmekanik','select',$data['nikmekanik'],array('style'=>'width:150px',$disabled=>$disabled),$optKary)
    );
	}else{
	 $els[] = array(
	makeElement('nikmekanik','label','Mekanik'),
	makeElement('nikmekanik','select',$data['nikmekanik'],array('style'=>'width:150px',$disabled=>$disabled),$optKary)
    );	
	}
	$els[] = array(
	makeElement('waktu','label','Waktu Pengerjaan(Jam)'),
	makeElement('waktu','textnumeric',$data['waktu'],
	    array('style'=>'width:150px',$disabled=>$disabled))
    );
	
	
	
	
	
    
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable('".$_SESSION['empl']['lokasitugas']."')"))
	);
    } elseif($mode=='edit') {
	$els['btn'] = array(
	    makeElement('editHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"editDataTable('".$_SESSION['empl']['lokasitugas']."')"))
	);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>