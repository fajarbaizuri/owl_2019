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
		/*
		if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or
			$_SESSION['empl']['tipelokasitugas']=='KANWIL') {
			$where = "kodeorg LIKE '%%'";
		} else {
			$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		}
		*/
		$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' "; 
		if(isset($param['where'])) {
			$arrWhere = json_decode(str_replace('\\','',$param['where']),true);
			if(!empty($arrWhere)) {
			foreach($arrWhere as $key=>$r1) {
				$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
			}
			}
		}
		
		# Header & Align
		$header = array(
			'Organisasi','No. Transaksi','Tanggal','Tipe','Pemohon','Total Permintaan','Total Realisasi','Status'
		);
			$align = explode(',','C,C,C,C,C,C,C');
		
		# Content
		$cols = "kodeorg,notransaksi,tanggal,kelompok,namakaryawan,totaldana,'realisasi','status',posting";
		$query = selectQuery($dbname,'log_dana_vw',$cols,$where,
			"tanggal desc, notransaksi desc",false,$param['shows'],$param['page']);
		$data = fetchData($query);
		$totalRow = getTotalRow($dbname,'log_dana_vw',$where);
		//$whereAkun="";$whereOrg="";$i=0;
		foreach($data as $key=>$row) {
			if($row['posting']==1) {
			$data[$key]['switched']=true;
			}
			$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
			unset($data[$key]['posting']);
			
	// Cek Alur Persetujuan
	/*
    $qCek = "select * from $dbname.log_persetujuandana ".
        "where kodeorg='".$row['kodeorg']."' and notransaksi='".$row['notransaksi'].
        "' order by level desc limit 0,1";
    $resCek = fetchData($qCek);
	*/
	
	$qCek = "select * from $dbname.log_danadt ".
        "where notransaksi='".$row['notransaksi'].
        "' order by pengajuan desc limit 0,1";
    $resCek = fetchData($qCek);
	
	$qCek1 = "select * from $dbname.log_danadt ".
        "where `status`='2' and notransaksi='".$row['notransaksi'].
        "' order by `status` desc limit 0,1";
	$resCek1 = fetchData($qCek1);
	
     $qCek2 = "select * from $dbname.log_persetujuandana ".
        "where kodeorg='".$row['kodeorg']."' and notransaksi='".$row['notransaksi'].
        "' order by level desc limit 0,1";
    $resCek2 = fetchData($qCek2);
	
    // Set Status
	if(!empty($resCek)) {
		//
		
		if(!empty($resCek1)) {
			$data[$key]['switched']=1; 
			$data[$key]['status'] = "Over Budget";
		}else{
			if(!empty($resCek2)) {
					$data[$key]['switched']=1;
					if($resCek2[0]['status']=='1') {
						$data[$key]['status'] = "Disetujui";
					}else if($resCek2[0]['status']=='2') {
						$data[$key]['status'] = "Ditolak";
					}else {
						$data[$key]['status'] = "Persetujuan Tahap ".$resCek2[0]['level'];  
					}
			}else{
				$data[$key]['status'] = "Pengajuan";
			}
		}
	
    }else {
        $data[$key]['status'] = "Detail Kosong";
    }
	
	/*
    if($row['posting']==1) {
		$data[$key]['switched']=1;
        $data[$key]['status'] = "Disetujui";
    }
	
	
    if(!empty($resCek)) {
		$data[$key]['switched']=1;
        if($resCek[0]['level']==5 and $resCek[0]['tanggal']!='0000-00-00') {
            $data[$key]['status'] = "Disetujui";
        }else {
            $data[$key]['status'] = "Persetujuan Tahap ".$resCek[0]['level'];  
        }
    }else {
        $data[$key]['status'] = "Pengajuan";
    }
	
    if($row['posting']==1) {
		$data[$key]['switched']=1;
        $data[$key]['status'] = "Disetujui";
    }
	*/
	
			# Build Condition
			/*
			if($i==0) {
			  $whereAkun.="noakun='".$row['noakun']."'";
			  $whereOrg.="kodeorganisasi='".$row['kodeorg']."'";
			} else {
			  $whereAkun.=" or noakun='".$row['noakun']."'";
			  $whereOrg.=" or kodeorganisasi='".$row['kodeorg']."'";
			}
			$i++;
			*/
		}
		
			
		# Posting --> Jabatan
		$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='keuangan'");
		$tmpPost = fetchData($qPosting);
		$postJabatan = $tmpPost[0]['jabatan'];
		
		# Options
		//$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereAkun);
		//$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
		
		# Mask Data Show
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {			
			$dataShow[$key]['totaldana'] = number_format($row['totaldana'],0);
    //$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
    //$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
    #=====================tambahan ginting sebagai pembalance
    $str="select sum(jumlah)*-1 as jumlah from ".$dbname.".keu_jurnaldt 
          where noreferensi='".$data[$key]['notransaksi']."' and jumlah < 0 ";  
    $res=mysql_query($str);
    $bar=mysql_fetch_object($res);
    $balan=0;
    $balan=$bar->jumlah; 
    $balan=$balan-$row['jumlah'];
    #==================================
    $dataShow[$key]['realisasi'] = number_format($balan,0);   
		}
		
		# Make Table 
		$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
if($postJabatan!=$_SESSION['empl']['kodejabatan'] and $_SESSION['empl']['tipelokasitugas']!='HOLDING') {
  $tHeader->_actions[2]->_name='';
}
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->pageSetting(1,$totalRow,10);
			
			
		if(isset($param['where'])) {
			$tHeader->setWhere($arrWhere);
		}
		$tHeader->setAlign($align);
		
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
		$query = selectQuery($dbname,'keu_kasbankht',"*","notransaksi='".
			$param['notransaksi']."' and kodeorg='".$param['kodeorg'].
			"' and noakun='".$param['noakun']."' and tipetransaksi='".
			$param['tipetransaksi']."'");
			$tmpData = fetchData($query);
		$data = $tmpData[0];
		$data['tanggal'] = tanggalnormal($data['tanggal']);
        echo formHeader('edit',$data);
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Proses Add Header
    case 'add':
		$data = $_POST;
		$data['hutangunit']=0;
		$data['pemilikhutang']='';
		$data['noakunhutang']='';
		
		// Error Trap
		$warning = "";
		if($data['notransaksi']=='') {$warning .= "No Transaksi harus diisi\n";}
		if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
        //cek notransaksi pada kasbankht
        $str="select * from ".$dbname.".keu_kasbankht where notransaksi='".$data['notransaksi']."'";
        $res=mysql_query($str);
        if(mysql_num_rows($res)>0) {
            exit("Error: Dokumen dengan nomor yang sama sudah ada\nSilahkan buat no.baru");
        }
        
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
		$data['userid'] = $_SESSION['standard']['userid'];
		$cols = array('notransaksi','noakun','tanggal','matauang','kurs',
			'tipetransaksi','jumlah','cgttu','keterangan','nogiro','kelompokpembayaran','yn','kodeorg','hutangunit','pemilikhutang','noakunhutang','userid');
		$query = insertQuery($dbname,'keu_kasbankht',$data,$cols);
		//exit("Error".$query);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	
	$where = "notransaksi='".$data['notransaksi']."' and kodeorg='".
	    $data['kodeorg']."' and noakun='".$data['oldNoakun']."' and tipetransaksi='".
	    $data['tipetransaksi']."'";
        $wheredt = "notransaksi='".$data['notransaksi']."' and kodeorg='".
	    $data['kodeorg']."'";
        $datadt['noakun2a'] = $param['noakun'];
	unset($data['notransaksi']);
	unset($data['kodeorg']);
	unset($data['oldNoakun']);
	unset($data['tipetransaksi']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['jumlah'] = str_replace(',','',$data['jumlah']);
	$query = updateQuery($dbname,'keu_kasbankht',$data,$where);
	$querydt = updateQuery($dbname,'keu_kasbankdt',$datadt,$wheredt);
//        exit("Error".$querydt);
	if(!mysql_query($query)) {
	    echo "DB Error ht : ".mysql_error();
	}else{
            if(!mysql_query($querydt)) {
                echo "DB Error dt : ".mysql_error();
            }else{
                echo 'Done.';
            }
        }
        // tadinya ga pake else echo Done, tapi kalo ga pake update-annya ga kesimpen. koq bisa ya?
        // tambahan querydt untuk ngupdate noakun2a kasbankdt
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."' and kodeorg='".
	    $param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".
	    $param['tipetransaksi']."'";
	$query = "delete from `".$dbname."`.`keu_kasbankht` where ".$where;
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
	$data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
	if($_SESSION['empl']['lokasitugas']=='FBHO')
	{
		$data['noakun'] = '1110201';
	}
	else
	{
		$data['noakun'] = '';
	}
	$data['tanggal'] = '';
	$data['tipetransaksi'] = '';
	$data['jumlah'] = '0';
	$data['matauang'] = 'IDR';
	$data['kurs'] = '1';
	$data['cgttu'] = '';
	$data['keterangan'] = '';
	$data['nogiro'] = '';
	$data['kelompokpembayaran'] = '';
	$data['yn'] = '0';
        $data['oldNoakun'] = '';
		
	$data['hutangunit']='0';
	$data['pemilikhutang']='';
	$data['noakunhutang']='';	
    } else {
	$data['jumlah'] = number_format($data['jumlah'],0);
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $whereJam=" kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
    $optMataUang = makeOption($dbname,'setup_matauang','kode,matauang');
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereJam);//"kasbank=1 and detail=1");
   // $optTipe = array('M'=>'Masuk','K'=>'Keluar');
   	$optkelompok=array('0'=>'','1'=>'Pembayaran TBS','2'=>'Pembayaran Operasional','3'=>'Pembayaran Investasi & Kontraktor');
    $optTipe = array('K'=>'Keluar','M'=>'Masuk');
	$optCgt = getEnum($dbname,'keu_kasbankht','cgttu');
    $optYn = array(0=>'Belum Posting',1=>'Sudah Posting');
	
	$wheredz= "kodeorganisasi !='".$_SESSION['empl']['lokasitugas']."' and length(kodeorganisasi)=4";
	$wheredzx="noakun like '%211%' and length(noakun)=7 ";
	
	$optPemilikHutang=makeOption($dbname,'organisasi','kodeorganisasi,kodeorganisasi',$wheredz);
	$optNoakunHutang=makeOption($dbname,'keu_5akun','noakun,namaakun',$wheredzx);
	
	$optPemilikHutang['']='';ksort($optPemilikHutang);
	$optNoakunHutang['']='';ksort($optNoakunHutang);
	
	$optHutangUnit=array('0'=>'Ya','1'=>'Tidak');
	    
    $els = array();
    $els[] = array(
	makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
	makeElement('notransaksi','text',$data['notransaksi'],
	    array('style'=>'width:150px','maxlength'=>'25',$disabled=>$disabled))
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optOrg)
    );
      $els[] = array(
	makeElement('noakun2a','label',$_SESSION['lang']['noakun']),
	makeElement('noakun2a','select',$data['noakun'],
	    array('style'=>'width:150px','onchange'=>"toaruskas()"),$optAkun) 
    );
    
//    $els[] = array(
//	makeElement('noakun2a','label',$_SESSION['lang']['noakun']),
//	makeElement('noakun2a','select',$data['noakun'],
//	    array('style'=>'width:150px',$disabled=>$disabled),$optAkun)
//    );
//    
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('matauang','label',$_SESSION['lang']['matauang']),
	makeElement('matauang','select',$data['matauang'],
	    array('style'=>'width:150px'),$optMataUang)
    );
    $els[] = array(
	makeElement('kurs','label',$_SESSION['lang']['kurs']),
	makeElement('kurs','textnum',$data['kurs'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('tipetransaksi','label',$_SESSION['lang']['tipetransaksi']),
	makeElement('tipetransaksi','select',$data['tipetransaksi'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optTipe)
    );
    $els[] = array(
	makeElement('jumlah','label',$_SESSION['lang']['jumlah']),
	makeElement('jumlah','textnum',$data['jumlah'],
	    array('style'=>'width:150px','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );
    $els[] = array(
	makeElement('cgttu','label',$_SESSION['lang']['cgttu']),
	makeElement('cgttu','select',$data['cgttu'],array('style'=>'width:150px'),$optCgt)
    );
    $els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
	makeElement('keterangan','text',$data['keterangan'],array('style'=>'width:150px'))
    );
	$els[] = array(
	makeElement('nogiro','label',$_SESSION['lang']['nogiro']),
	makeElement('nogiro','text',$data['nogiro'],array('style'=>'width:150px'))
    );
	
	$els[] = array(
	makeElement('kelompokpembayaran','label',$_SESSION['lang']['kelompokpembayaran']),
	makeElement('kelompokpembayaran','select',$data['kelompokpembayaran'],
	    array('style'=>'width:150px'),$optkelompok)
    );
	
	
    $els[] = array(
	makeElement('yn','label',$_SESSION['lang']['yn']),
	makeElement('yn','select',$data['yn'],
	    array('style'=>'width:150px','disabled'=>'disabled'),$optYn)
    );
	
	//tambahan
//	if($data['hutangunit']==0){
//		$dis='disabled';
//	} else {
//		$dis='';
//	}
//	
//	$els[] = array(
//	makeElement('hutangunit','label',$_SESSION['lang']['hutangunit']),
//	makeElement('hutangunit','checkbox',$data['hutangunit'],
//	    array('onclick'=>"pilihhutang()"))
//    );
//	$els[] = array(
//	makeElement('pemilikhutang','label',$_SESSION['lang']['pemilikhutang']),
//	makeElement('pemilikhutang','select',$data['pemilikhutang'],
//	   array('style'=>'width:150px',$dis=>$dis),$optPemilikHutang)
//    );
//	$els[] = array(
//	makeElement('noakunhutang','label',$_SESSION['lang']['noakunhutang']),
//	makeElement('noakunhutang','select',$data['noakunhutang'],
//	   array('style'=>'width:150px',$dis=>$dis),$optNoakunHutang)
//    );
	
	$els[] = array(
	makeElement('oldNoakun','hid',$data['noakun'] ));
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