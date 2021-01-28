<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/zGrid.php');
require_once('lib/formTable.php');

# Get Attr
$proses = $_GET['proses'];
$data = $_POST;
$tmpNoJ = explode('/',$data['nojurnal']);
$org = $tmpNoJ[1];

switch($proses) {
    case 'show':
	$ids = $_POST;
	
	# Options
	$whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
	$whereKary = "kodegolongan='A' ";
	//$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
        $whereJam=" detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
	$optCashFlow = makeOption($dbname,'aruskas_vw','noarus,uraian', "metode ='DIRECT' ",'2');
	
	
	$optMatauang = makeOption($dbname,'setup_matauang','kode,matauang');
	$optAsset = makeOption($dbname,'sdm_daftarasset','kodeasset,namasset',$whereAsset,'2',true);
	$optSupplier = makeOption($dbname,'log_5supplier','supplierid,namasupplier',null,'0',true);
	$optCustomer = makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer',null,'0',true);
	$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary,'0',true);
	//$optAkun = makeOptionAkun($dbname,'keu_5akun','noakun,namaakun',$whereJam,'2',true);
        $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereJam,'2',true);
	//$optVhc = makeOption($dbname,'vhc_5master','kodevhc,kodevhc',"kodeorg='".$org."'",'0',true);
        $optVhc = makeOption($dbname,'vhc_5master','kodevhc,nopol','','2',true);
	$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"length(kodeorganisasi)>6 and induk like '".$_SESSION['empl']['lokasitugas']."%'",'0',true);
	
	# Kegiatan
        
	$optKlpKeg = makeOption($dbname,'setup_klpkegiatan','kodeklp,namakelompok',null,'0',true);
	$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,kodekegiatan',"(pilih in ('2','5','8','9','11','12','14','15'))",'6',true);
	//$qKegiatan = selectQuery($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,kelompok').' order by namakegiatan';
       // exit("Error".$qKegiatan);
	   /*
	$tmpKeg = fetchData($qKegiatan);
	$optKegiatan = array(''=>'');
	foreach($tmpKeg as $row) {
	    $optKegiatan[$row['kodekegiatan']] = $row['namakegiatan']." (".$optKlpKeg[$row['kelompok']].")";
	}
	*/
	#$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',null,'0',true);
	
	$tmpKlp = makeOption($dbname,'setup_klpkegiatan','noakun,namakelompok');
	/*foreach($optAkun as $key=>$row) {
	    if(isset($tmpKlp[$key])) {
		$optAkun[$key] = $row." (".$tmpKlp[$key].")";
	    } else {
		if(isset($tmpKlp[substr($key,0,5)])) {
		    $optAkun[$key] = $row." (".$tmpKlp[substr($key,0,5)].")";
		} else {
		    if(isset($tmpKlp[substr($key,0,3)])) {
			$optAkun[$key] = $row." (".$tmpKlp[substr($key,0,3)].")";
		    } else {
			if(isset($tmpKlp[substr($key,0,1)])) {
			    $optAkun[$key] = $row." (".$tmpKlp[substr($key,0,1)].")";
			}
		    }
		}
	    }
	}*/
	
	# Get Data
	$cols = array('nourut','noakun','keterangan','jumlah','matauang','kurs','noaruskas',
	    'kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
	    'kodesupplier','kodevhc','nodok','kodeblok');
	$where = "nojurnal='".$ids['nojurnal']."'";
	$query = selectQuery($dbname,'keu_jurnaldt',$cols,$where);
	$data = fetchData($query);
	
	# Masking Nama Barang
	if($data!=array()) {
	    $whereBarang = "";
	    $i=0;
	    foreach($data as $row) {
		if($i==0) {
		    $whereBarang .= "kodebarang='".$row['kodebarang']."'";
		} else {
		    $whereBarang .= " or kodebarang='".$row['kodebarang']."'";
		}
		$i++;
	    }
	    $optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whereBarang);
	} else {
	    $optBarang = array();
	}
	
	# Replace Code with Name
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['nik'] = $optKary[$row['nik']];
	    $dataShow[$key]['noaruskas'] = $optCashFlow[$row['noaruskas']];
	    $dataShow[$key]['kodekegiatan'] = $optKegiatan[$row['kodekegiatan']];
	    $dataShow[$key]['kodecustomer'] = $optCustomer[$row['kodecustomer']];
	    $dataShow[$key]['kodesupplier'] = $optSupplier[$row['kodesupplier']];
	    $dataShow[$key]['kodevhc'] = $optVhc[$row['kodevhc']];
	    $dataShow[$key]['matauang'] = $optMatauang[$row['matauang']];
	    $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
	    if($row['kodebarang']!='' and $row['kodebarang']!='0') {
		$dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
	    }
	    $dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
	}
	
	# Form
	$theForm = new uForm('jurnalForm','Form Jurnal Detail',2);
	$theForm->addEls('nourut',$_SESSION['lang']['nourut'],'0','textnum','R',3);
	$theForm->_elements[0]->_attr['disabled'] = 'disabled';
	$theForm->addEls('noakun',$_SESSION['lang']['noakun'],'','select','L',25,$optAkun);
	$theForm->addEls('keterangan',$_SESSION['lang']['keterangan'],'','text','L',25);
	$theForm->addEls('jumlah',$_SESSION['lang']['jumlah'],'0','dk','R',15);
	$theForm->_elements[3]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
	$theForm->addEls('matauang',$_SESSION['lang']['matauang'],'IDR','select','L',25,$optMatauang);
	$theForm->addEls('kurs',$_SESSION['lang']['kurs'],'1','textnum','R',10);
	$theForm->addEls('noaruskas',$_SESSION['lang']['noaruskas'],'','select','L',25,$optCashFlow);
	$theForm->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','select','L',25,$optKegiatan);
	$theForm->addEls('kodeasset',$_SESSION['lang']['kodeasset'],'','select','L',35,$optAsset);
	$theForm->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',10);
	$theForm->addEls('nik',$_SESSION['lang']['nik'],'','select','L',35,$optKary);
	$theForm->addEls('kodecustomer',$_SESSION['lang']['kodecustomer'],'','select','L',35,$optCustomer);
	$theForm->addEls('kodesupplier',$_SESSION['lang']['kodesupplier'],'','select','L',35,$optSupplier);
	$theForm->addEls('kodevhc',$_SESSION['lang']['kodevhc'],'','select','L',35,$optVhc);
	$theForm->addEls('nodok',$_SESSION['lang']['nodok'],'','text','L',30);
	$theForm->addEls('kodeblok',$_SESSION['lang']['kodeblok'],'','select','L',30,$optBlok);
	
	
	# Table
	$theTable = new uTable('jurnalTable','Tabel Jurnal Detail',"",$data,$dataShow);
	
	# FormTable
	$formTab = new uFormTable('ftJurnalDt',$theForm,$theTable,null,
	    array('nojurnal','kodejurnal','tanggal','matauang'));
	$formTab->_target = "keu_slave_jurnal_manage_detail";
	$formTab->_defValue = '##matauang=IDR##kurs=1';
	$formTab->_numberFormat = '##jumlah';
	$formTab->render();
	break;
    default:
	break;
}
?>