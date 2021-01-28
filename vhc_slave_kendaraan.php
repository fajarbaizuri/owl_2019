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
	case 'getLokas':
		
		if ($param['keg']=='0'){
			echo "<option value='0' >Pilih Lokasi</option>";
		}else{
			
		    
		
		$queryKaryA = "SELECT DISTINCT kodeorg from `".$dbname."`.vhc_kendaraan_premi_vw WHERE kodeorg LIKE '".$_SESSION['empl']['lokasitugas']."%' AND kegiatan='".$param['keg']."' ;";
		
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$num_rows = mysql_num_rows($query);
		//$hasil='AND';
		if ($num_rows==0){
			$str = "SELECT kodeorganisasi,namaorganisasi FROM `".$dbname."`.organisasi  WHERE tipe in ('BLOK','AFDELING','KEBUN','PABRIK') limit 0;";
		}else if ($num_rows==1){
			$res=mysql_fetch_assoc($query);	
			$str = "SELECT kodeorganisasi,namaorganisasi FROM `".$dbname."`.organisasi  WHERE tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and kodeorganisasi like '".$res['kodeorg']."%';";
		}
		else{
			$hasil="";
			
			while($res=mysql_fetch_assoc($query))
			{
				$hasil.= "kodeorganisasi like '".$res['kodeorg']."%' or ";
			}			
			
			
			$str = "SELECT kodeorganisasi,namaorganisasi FROM `".$dbname."`.organisasi  WHERE tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and (".substr($hasil,0,-3).")";
		
		}

		$query=mysql_query($str) or die(mysql_error());
		$optKdvhc="<option value='0' >Pilih Lokasi</option>";
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['kodeorganisasi']."' >".$res['namaorganisasi']."</option>";
		}
		echo $optKdvhc;
		}
		
	break;
	case 'getPlat':
		$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` a".
			"  where kelompokvhc like '%".$param['alat']."%' and `kodeorg` like '%".$param['kdorg']."%' and kepemilikan=1";
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$optKdvhc="<option value='' >Pilih Plat No</option>";
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['kodevhc']."' >".$res['nopol']." [".$res['kodevhc']."]</option>";
		}
		echo $optKdvhc;
	break;
	case 'getPlatA':
		$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` a".
			"  where kelompokvhc like '%".$param['alat']."%' and `kodeorg` like '%".$param['kdorg']."%' and kepemilikan=1";
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$optKdvhc="<option value='' >Pilih Plat No</option>";
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['kodevhc']."' >".$res['nopol']." [".$res['kodevhc']."]</option>";
		}
		echo $optKdvhc;
	break;
	case 'getSop':
			$queryKaryA = "select karyawanid,nama from ".$dbname.".vhc_5operator_vw where lokasitugas='".$param['kdorg']."' and aktif=1";
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$optKdvhc="<option value='' >Operator/Sopir</option>";
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['karyawanid']."' >".$res['nama']."</option>";
		}
		echo $optKdvhc;
	break;
	case 'getDriv':

		$whereKary = "kodejabatan in ('178',
'179',
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
            'Nomor','Tanggal','Kelompok','Plat No.','Kondisi','Mandor Transport','Penggunaan BBM (Ltr)'
       );            
	
	# Content
	if(is_null($where)) {
		//if (substr($_SESSION['empl']['lokasitugas'],0,4) != 'TKFB'){
			 $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		//}else{
			//$where= "kodeorg like '%%'";
		//}
	   
	} else {
		//if (substr($_SESSION['empl']['lokasitugas'],0,4) != 'TKFB'){
			 $where .= "and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		//}else{
			//$where.= "and kodeorg like '%%'";
		//}
	}
	
	/// 
	// $where .= " and tanggal >='2014-01-01' and tanggal <='2014-01-31' and jurnal=0";
	$cols = "notransaksi,tanggal,kelompok,nopol,kondisinm,mandor,bbm,posting";
	$query = selectQuery($dbname,'vhc_kendaraan_vw',$cols,$where,
	"tanggal desc, notransaksi desc",false,$param['shows'],$param['page']);
	//"jurnal asc",false,$param['shows'],$param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'vhc_kendaraan_vw',$where);
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
    # Form Add Header
    case 'showAdd':
	// View
	echo formHeader('add',$_POST['tipe'],array());
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Form Edit Header
    case 'showEdit':
	
	$query = selectQuery($dbname,'vhc_kendaraan_vw',"*","notransaksi='".$param['notransaksi']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	
	$dataA=array(
	'notransaksi'=>$data['notransaksi'],
	'kodeorg'=>$data['kodeorg'],
	'kelompok'=>$data['kelompokvhc'],
	'stsalat'=>$data['stsalat'],
	'tanggal'=>tanggalnormal($data['tanggal']),
	'plat'=>$data['kodevhc'],
	'kondisi'=>$data['kondisi'],
	'keterangan'=>$data['catatan'] ,
	'nikmandor'=>$data['mandor'] ,
	'bbm'=>$data['bbm'] ,
	'libur'=>$data['harilibur']
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
	if($data['stsalat']=='') {
	    echo "Validation Error : Pemilik Alat Belum Dipilih.";
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
	if($data['mandor']=='') {
	    echo "Validation Error : Mandor Transport Tidak Boleh Kosong.";
	    break;
	}

	if($data['kondisi']!='B') {
		if($data['catatan']=='') {
	    echo "Validation Error : Keterangan Tidak Boleh Kosong.";
	    break;
		}
	}
	
	$data['posting']='0';
	
	# Data Capture & Reform
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	
	#=== Generate No Transaksi
	# Get Existing Data
	
	$fWhere = "DATE_FORMAT(tanggal,'%Y%m')='".substr($data['tanggal'], 0, 6)."' and kodeorg='".$data['kodeorg'].
	    "' ";
	$fQuery = selectQuery($dbname,'vhc_kendaraanht','notransaksi',$fWhere);
	$tmpNo = fetchData($fQuery);
	
	
	//getNamabarang
	$sDtBarang="select kodevhc,kodeorg from ".$dbname.".vhc_5master order by kodeorg asc";
	$rData=fetchData($sDtBarang);
	foreach($rData as $brBarang =>$rNamabarang)
	{
		$RkodeVHC[$rNamabarang['kodevhc']]=$rNamabarang['kodeorg'];
	}
	
	# Generate No Transaksi
	if(count($tmpNo)==0) {
	    $data['notransaksi'] = $data['tanggal']."/".$param['kodeorg']."/RUN/001";
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
	    $data['notransaksi'] = $data['tanggal']."/".$param['kodeorg']."/RUN/".$currNo;
	}
	
	 
	if ($data['libur']=="true"){
		$hasilL=1;
	}else{
		$hasilL=0;
	}
	$dataA=array(
	'notransaksi'=>$data['notransaksi'] ,
	'kodeorg'=>substr($data['notransaksi'],9,4),
	'tanggal'=>$data['tanggal'] ,
	'kodevhc'=>$data['kodevhc'] ,
	'stsalat'=>$data['stsalat'] ,
	'kondisi'=>$data['kondisi'] ,
	'catatan'=>$data['catatan'] ,
	'mandor'=>$data['mandor'] ,
	'bbm'=>$data['bbm'] ,
	'harilibur'=>$hasilL ,
	'posting'=>'0' 
	);
	
	$cols = array('notransaksi','kodeorg','tanggal','kodevhc','stsalat',
	    'kondisi','catatan','mandor','bbm','harilibur','posting');
	$query = insertQuery($dbname,'vhc_kendaraanht',$dataA,$cols);
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
	$query = "delete from `".$dbname."`.`vhc_kendaraanht` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
	 case 'posting':
	 $queryKaryA = "select upah  from `".$dbname."`.`vhc_kendaraan_tenaga` a".
			"  where notransaksi ='".$param['notransaksi']."'";
			
			
		$query=mysql_query($queryKaryA) or die(mysql_error());
		$num_rows = mysql_num_rows($query);
		if ($num_rows==0){
			 echo "Transaksi Error : Kehadiran Karyawan belum tersedia";
			 exit;
		}
		
	 $query = selectQuery($dbname,'vhc_kendaraan_tenaga',"*","notransaksi='".$param['notransaksi']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	
	$where = "notransaksi='".$param['notransaksi']."'";
	$query = "update `".$dbname."`.`vhc_kendaraanht` set posting=1,postingby='".$_SESSION['standard']['username']."' where ".$where;
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
	$data['libur'] = '';
	$data['kodeorg'] = '';
	$data['nikmandor'] = '';
	$data['bbm'] = '';
	$data['kelompok'] = '';
	$data['plat'] = '';
	$data['kondisi'] = '';
	$data['keterangan'] = '';
	$data['stsalat'] = '';
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
		$whereKary = "kodejabatan in ('178',
'179',
'180',
'174',
'234',
'306')  and tipekaryawan in ('1','2','3','4')";
	}else{
		$whereKary = "kodejabatan in ('178',
'179',
'180',
'174',
'234',
'306') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in ('1','2','3','4')";
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
	
	
	
	$optsts = array(''=>'Pilih Pemilik Alat','TDAE'=>'Kebun Tadu A','TDBE'=>'Kebun Tadu B','USJE'=>'Kebun USJ','CBGM'=>'Ceumbring Mill','FBAO'=>'Kantor Area');
    $optkelom = array(''=>'Pilih Kelompok','AB'=>'Alat Berat','KD'=>'Kendaraan');
	$optKOn = array('B'=>'Beroperasi',
	'S01'=>'StandBy Rusak Alat/Kendaraan',
	'S02'=>'StandBy Service Alat/Kendaraan',
	'S03'=>'StandBy Menunggu Pengadaan Sparepart',
	'S04'=>'StandBy Menunggu Pengadaan BBM',
	'S05'=>'StandBy Gangguan Alam & Sosial',
	'S06'=>'StandBy Lainnya');
	
	
	
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
	makeElement('kondisi','label','Kondisi'),
	makeElement('kondisi','select',$data['kondisi'],array('style'=>'width:150px',"onchange"=>"showStandby(this)",$disabled=>$disabled),$optKOn)
    );

	
	$els[] = array(
	makeElement('keterangan','label','Keterangan'),
	makeElement('keterangan','text',$data['keterangan'],
	    array('style'=>'width:150px','maxlength'=>'100','disabled'=>'disabled'))
    );
	
	if ($data['libur']==true ){
		$els[] = array(
			makeElement('libur','label','Hari Libur Bekerja'),
			makeElement('libur','check',$data['libur'],
			array($disabled=>$disabled,'checked'=>'checked'))
		);
		
		
	}else{
		$els[] = array(
		makeElement('libur','label','Hari Libur Bekerja'),
		makeElement('libur','check',$data['libur'],
	    array($disabled=>$disabled))
		);
		
	}
	
	$els[] = array(
	makeElement('stsalat','label','Pemilik Alat'),
	makeElement('stsalat','select',$data['stsalat'],array("style"=>"width:150px","onchange"=>"getPlat()",$disabled=>$disabled),$optsts)
    );
	$els[] = array(
	makeElement('kelompok','label','Kelompok'),
	makeElement('kelompok','select',$data['kelompok'],array("style"=>"width:150px","onchange"=>"getPlat()",$disabled=>$disabled),$optkelom)
    );
	if($mode=='add') {
		$els[] = array(
			makeElement('plat','label','Plat No'),
			makeElement('plat','select',$data['plat'],array('style'=>'width:150px',"onchange"=>"getDriv('".$_SESSION['empl']['lokasitugas']."')",$disabled=>$disabled),$optZero)
		);
	}else{
		$els[] = array(
			makeElement('plat','label','Plat No'),
			makeElement('plat','select',$data['plat'],array('style'=>'width:150px',"onchange"=>"getDriv('".$_SESSION['empl']['lokasitugas']."')",$disabled=>$disabled),$optKdvhc)
		);
	}
	if($mode=='add') {
    $els[] = array(
	makeElement('nikmandor','label','Mandor Transport'),
	makeElement('nikmandor','select',$data['nikmandor'],array('style'=>'width:150px',$disabled=>$disabled),$optZero)
    );
	}else{
	 $els[] = array(
	makeElement('nikmandor','label','Mandor Transport'),
	makeElement('nikmandor','select',$data['nikmandor'],array('style'=>'width:150px',$disabled=>$disabled),$optKary)
    );	
	}
	$els[] = array(
	makeElement('bbm','label','Penggunaan BBM (Ltr)'),
	makeElement('bbm','textnumeric',$data['bbm'],
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