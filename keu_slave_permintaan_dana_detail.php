<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php'); 
?>

<?

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
		$whereAKB = "kodeaplikasi='GL' and aktif=1";
		$queryAKB = selectQuery($dbname,'keu_5parameterjurnal',
			'jurnalid,noakundebet,sampaidebet,noakunkredit,sampaikredit',$whereAKB);
		$optAKB = fetchData($queryAKB);
		$tipe = "";
		foreach($optAKB as $row) {
			if($param['tipetransaksi']=='K') {
			if($param['noakun']>=$row['noakunkredit'] and $param['noakun']<=$row['sampaikredit']) {
				$tipe = $row['jurnalid'];
			}
			} else {
			if($param['noakun']>=$row['noakundebet'] and $param['noakun']<=$row['sampaidebet']) {
				$tipe = $row['jurnalid'];
			}
			}
		}
		
		
		# Cek Kelompok Jurnal
		$whereKel = "kodeorg='".$_SESSION['org']['kodeorganisasi'].
			"' and kodekelompok='".$tipe."'";
		$optKel = makeOption($dbname,'keu_5kelompokjurnal','kodekelompok,keterangan',$whereKel);
		if(empty($optKel)) {
			echo "Warning : Belum ada Kelompok Jurnal ".$tipe." untuk PT anda\n";
			echo "Mohon hubungi pihak IT";
			exit;
		}
		
		# Options
		//di sini yg dimaksud pak afuan
		$whereJam=" detail=1 and noakun <> '".$param['noakun']."' and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
		
		//$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
		$whereKary = "kodegolongan='A' ";
		$whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		$optAsset = makeOption($dbname,'sdm_daftarasset','kodeasset,namasset',$whereAsset,'2',true);
		$optMataUang = makeOption($dbname,'setup_matauang','kode,matauang');
		$optSupplier = makeOption($dbname,'log_5supplier','supplierid,namasupplier',null,'0',true);
		$optCustomer = makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer',null,'0',true);
		$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',null,'6',true);
		$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary,'0',true);
		$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereJam,'2');
		
		
		$optVhc = makeOption($dbname,'vhc_5master','kodevhc,kodeorg','','2',true);
		$optOrgAl = getOrgBelow($dbname,$param['kodeorg'],false,'all',true);
		
		$optCashFlow = makeOption($dbname,'aruskas_vw','noarus,uraian', "tipe='".$param['tipetransaksi']."' and metode ='DIRECT' and noakun='".$param['noakun']."' and kodeorg='".$param['kodeorg']."'",'2');
		
		/*
		if (($param['kodeorg']=='CBGM') && ($param['noakun']=='1110241')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_071%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_072%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='CBGM') && ($param['noakun']=='1110245')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_111%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_112%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='CBGM') && ($param['noakun']=='1110104')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_121%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_122%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110201')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_011%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_012%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110205')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_021%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_022%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110206')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_031%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_032%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110207')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_051%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_052%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110101')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_061%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_062%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='TDAE') && ($param['noakun']=='1110231')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_081%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_082%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='TDAE') && ($param['noakun']=='1110102')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_131%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_132%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='TDAE') && ($param['noakun']=='1110110')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_171%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_172%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='TDBE') && ($param['noakun']=='1110232')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_091%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_092%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='TDBE') && ($param['noakun']=='1110103')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_141%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_142%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='TKFB') && ($param['noakun']=='1110105')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_161%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_162%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='USHO') && ($param['noakun']=='1110251')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_041%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_042%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='USJE') && ($param['noakun']=='1110261')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_101%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_102%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}else if (($param['kodeorg']=='USJE') && ($param['noakun']=='1110106')) {
			if ($param['tipetransaksi']=='M') {
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_151%' and namalaporan='ARUS KAS LANGSUNG'",'2');
			}else{
				$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay', "tipe='Detail' and nourut LIKE '_152%' and namalaporan='ARUS KAS LANGSUNG'",'2');	
			}
		}
		
		
		
		*/
		
		
			
		 
		 
		$wheredz= "kodeorganisasi !='".$_SESSION['empl']['lokasitugas']."' and length(kodeorganisasi)=4";
		//$wheredzx="(noakun like '%211%' or noakun like '%212%' or noakun like '%221%') and length(noakun)=7 ";
		$wheredzx="(noakun like '12601%' or noakun like '12602%' or noakun like '12603%' or noakun like '12604%' or noakun like '12605%' or noakun like '12606%' or noakun like '21101%' or noakun like '21102%' or noakun like '21103%' or noakun like '21101%' or noakun like '21102%' or  noakun like '21103%' or  noakun like '82101%' or  noakun in ('8220809' ,'2130102' ,'2130109','711 0402','7111105','1120101','1120102','1120103','1120104','1120105','1120106','1120201','1120301','1120302','1120303','1130101','7111103','6110201','6110202','7110701','1270601','8110203','8110202','2140101','7110401','7110402','1140201','1140202','7110201','2130101','2130106','1140101','1140102','1140103','1140104','1270508','6310204','6310205','6310206','1270403','1270502','1270504','1270507','1270510','8220301','8220204','8220303','8220401','8220402','8220403','8220404','8220405','8220406','8220407','8220408','8220409','2120101','2120102','2120103','2120104','2120105','1120101','1120102','1130101','1140101','1140102','1140103','1140104','1140201','1140202','1140301','1140501','1140502','1260101','1260201','1260301','1260401','1260402','1260403','1260404','1260405','1260501','1260601','1270501','1270502','1270503','1270504','1270505','1270506','1270507','1270508','1270509','1270510','1270601','1270602','2110101','2110102','2110201','2110202','2120101','2120102','2120103','2120104','2120105','2120105','2120105','2120108','2130101','2130102','2130106','2130108','2130109','2210101','2210102','6310202','6310203','7110101','7110201','7110202','7110301','7110402','7110404','7110501','7110504','7110601','7110602','7110603','7110604','7110801','7110903','7110904','7111101','7111301','8210102','8220302','8220603','8220604','8220605','8220803','8220806','8220807','8220809','8220810','2120106','8110101','8110102','8110103','8110104','8110105','8110106','8110107','8110108','8110109','8110201','1150101','7111001','7111002','7111003','6410104','6410104', 
'7110202','8220808','8220807','2120107','2120108','7110601','7110402','1140501','1140502','7110404')or  noakun like '71101%' )  and length(noakun)=7 ";  
		
		$optPemilikHutang=makeOption($dbname,'organisasi','kodeorganisasi,kodeorganisasi',$wheredz,'0',true);
		
		$optNoakunHutang=makeOption($dbname,'keu_5akun','noakun,namaakun',$wheredzx,'2',true);
		
		//tambahan		
		$optHutangUnit=array('0'=>'Tidak','1'=>'Ya');		
			
		if($param['tipetransaksi']=='K') {
			$invTab = 'keu_tagihanht';
		} else {
			$invTab = 'keu_penagihanht';
		}
		$optInvoice = makeOption($dbname,$invTab,'noinvoice,noinvoice',
			"kodeorg='".$_SESSION['org']['kodeorganisasi']."'",'0',true);
		
		# Field Aktif
		$optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
			"noakun='".end(array_reverse(array_keys($optAkun)))."'");
		$fieldAktif = $optField[end(array_reverse(array_keys($optAkun)))];
		
		# Get Data
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and noakun2a='".$param['noakun']."'";
		$cols = "kode,keterangan1,noakun,noaruskas,matauang,kurs,keterangan2,jumlah,".
			"kodekegiatan,kodeasset,kodebarang,nik,kodecustomer,kodesupplier,kodevhc,orgalokasi,nodok,hutangunit1,pemilikhutang,noakunhutang";
			
			
			
			
		$query = selectQuery($dbname,'keu_kasbankdt',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
			$dataShow[$key]['kode'] = $optKel[$row['kode']];
			$dataShow[$key]['nik'] = $optKary[$row['nik']];
			$dataShow[$key]['noaruskas'] = $optCashFlow[$row['noaruskas']];
			$dataShow[$key]['kodekegiatan'] = $optKegiatan[$row['kodekegiatan']];
			$dataShow[$key]['kodecustomer'] = $optCustomer[$row['kodecustomer']];
			$dataShow[$key]['kodesupplier'] = $optSupplier[$row['kodesupplier']];
			$dataShow[$key]['kodevhc'] = $optVhc[$row['kodevhc']];
			$dataShow[$key]['matauang'] = $optMataUang[$row['matauang']];
			$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
			$dataShow[$key]['orgalokasi'] = $optOrgAl[$row['orgalokasi']];
			$dataShow[$key]['hutangunit1'] = $optHutangUnit[$row['hutangunit1']];
		}
		
		# Form
		$theForm2 = new uForm('kasbankForm','Form Kas Bank',2);
		$theForm2->addEls('kode',$_SESSION['lang']['kode'],'','select','L',25,$optKel);
		$theForm2->addEls('keterangan1',$_SESSION['lang']['noinvoice'],'','text','L',25);
		$theForm2->_elements[1]->_attr['onclick'] = "searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']."','<div id=formPencariandata></div>',event)";
		$theForm2->addEls('noakun',$_SESSION['lang']['noakun'],'','select','L',25,$optAkun);
		$theForm2->_elements[2]->_attr['onchange'] = 'updFieldAktif()';
		$theForm2->addEls('noaruskas',$_SESSION['lang']['noaruskas'],'','select','L',25,$optCashFlow);
		$theForm2->addEls('matauang',$_SESSION['lang']['matauang'],'IDR','select','L',25,$optMataUang);
		$theForm2->addEls('kurs',$_SESSION['lang']['kurs'],'1','textnum','L',10);
		$theForm2->addEls('keterangan2',$_SESSION['lang']['keterangan2'],'','text','L',40);
		$theForm2->addEls('jumlah',$_SESSION['lang']['jumlah'],'0','textnum','R',10);
		$theForm2->_elements[7]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
		$theForm2->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','select','L',35,$optKegiatan);
/*		if($fieldAktif[0]=='0') {
			$theForm2->_elements[8]->_attr['disabled'] = 'disabled';
		}*/
			
		$theForm2->addEls('kodeasset',$_SESSION['lang']['kodeasset'],'','select','L',35,$optAsset);
/*		if($fieldAktif[1]=='0') {
			$theForm2->_elements[9]->_attr['disabled'] = 'disabled';
		}*/
		$theForm2->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',10);
/*		if($fieldAktif[2]=='0') {
			$theForm2->_elements[10]->_attr['disabled'] = 'disabled';
		}*/
		$theForm2->addEls('nik',$_SESSION['lang']['nik'],'','select','L',35,$optKary);
/*		if($fieldAktif[3]=='0') {
			$theForm2->_elements[11]->_attr['disabled'] = 'disabled';
		}*/
		$theForm2->addEls('kodecustomer',$_SESSION['lang']['kodecustomer'],'','select','L',35,$optCustomer);
/*		if($fieldAktif[4]=='0') {
			$theForm2->_elements[12]->_attr['disabled'] = 'disabled';
		}*/
		$theForm2->addEls('kodesupplier',$_SESSION['lang']['kodesupplier'],'','select','L',35,$optSupplier);
/*		if($fieldAktif[5]=='0') {
			$theForm2->_elements[13]->_attr['disabled'] = 'disabled';
		}*/
		$theForm2->addEls('kodevhc',$_SESSION['lang']['kodevhc'],'','select','L',35,$optVhc);
/*		if($fieldAktif[6]=='0') {
			$theForm2->_elements[14]->_attr['disabled'] = 'disabled';
		}*/
		$theForm2->addEls('orgalokasi',$_SESSION['lang']['kodeorg'],'','select','L',35,$optOrgAl);
		$theForm2->addEls('nodok',$_SESSION['lang']['nodok'],'','text','L',35);
		$theForm2->addEls('hutangunit1',$_SESSION['lang']['hutangunit'],'','select','L',25,$optHutangUnit);
		$theForm2->addEls('pemilikhutang',$_SESSION['lang']['pemilikhutang'],'','select','L',25,$optPemilikHutang);
		
		$theForm2->addEls('noakunhutang',$_SESSION['lang']['noakunhutang'],'','select','L',25,$optNoakunHutang);
		
		# Table
		$theTable2 = new uTable('kasbankTable','Tabel Kas Bank',$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,
			array('notransaksi','kodeorg','noakun2a','tipetransaksi'));
		
		$formTab2->_target = "keu_slave_kasbank_detail";
		$formTab2->_defValue = '##matauang=IDR';
		$formTab2->_numberFormat = '##jumlah';
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		$formTab2->render();
		echo "</fieldset>";
		break;
    case 'add':
		$cols = array(
			'kode','keterangan1','noakun','noaruskas','matauang','kurs','keterangan2',
			'jumlah','kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
			'kodesupplier','kodevhc','orgalokasi','nodok','hutangunit1','pemilikhutang',
			'noakunhutang','notransaksi','kodeorg','noakun2a','tipetransaksi'
		);
		$data = $param;
		unset($data['numRow']);
		
		# Additional Default Data
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
		
		$query = insertQuery($dbname,'keu_kasbankdt',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['notransaksi']);unset($data['kodeorg']);
		unset($data['noakun2a']);unset($data['tipetransaksi']);
		
		$res = "";
		foreach($data as $cont) {
			$res .= "##".$cont;
		}
		
		$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
		echo $result;
		break;
    case 'edit':
		$data = $param;
		unset($data['notransaksi']);
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
		
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun2a='".$param['noakun2a'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and noakun='".$param['cond_noakun'].
			"' and keterangan2='".$param['cond_keterangan2']."'";
		$query = updateQuery($dbname,'keu_kasbankdt',$data,$where);
		//echo "DB Error : ".$query;
			//exit;
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun='".$param['noakun'].
			"' and noakun2a='".$param['noakun2a'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and keterangan1='".$param['keterangan1']."'
				 and keterangan2='".$param['keterangan2']."'";
		$query = "delete from `".$dbname."`.`keu_kasbankdt` where ".$where;
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    case 'updField':
		$optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
			"noakun='".$param['noakun']."'");
		echo $optField[$param['noakun']];
		break;
	 case 'updArusKas':
		$SQL = "select noarus	,uraian from ".$dbname.".aruskas_vw where tipe='".$param['tipetransaksi']."' and metode ='DIRECT' and noakun='".$param['noakun']."' and kodeorg='".$param['kodeorg']."' order by noarus asc";
		/*
		if (($param['kodeorg']=='CBGM') && ($param['noakun']=='1110241')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_071%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_072%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='CBGM') && ($param['noakun']=='1110245')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_111%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_112%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='CBGM') && ($param['noakun']=='1110104')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_121%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_122%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110201')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_011%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_012%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110205')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_021%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_022%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110206')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_031%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_032%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110207')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_051%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_052%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='FBHO') && ($param['noakun']=='1110101')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_061%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_062%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='TDAE') && ($param['noakun']=='1110231')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_081%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_082%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='TDAE') && ($param['noakun']=='1110102')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_131%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_132%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='TDAE') && ($param['noakun']=='1110110')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_171%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_172%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='TDBE') && ($param['noakun']=='1110232')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_091%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_092%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='TDBE') && ($param['noakun']=='1110103')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_141%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_142%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='TKFB') && ($param['noakun']=='1110105')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_161%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_162%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='USHO') && ($param['noakun']=='1110251')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_041%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_042%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='USJE') && ($param['noakun']=='1110261')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_101%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_102%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}else if (($param['kodeorg']=='USJE') && ($param['noakun']=='1110106')) {
			if ($param['tipetransaksi']=='M') {
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_151%' and namalaporan='ARUS KAS LANGSUNG'";
			}else{
				$SQL = "select nourut,keterangandisplay from ".$dbname.".keu_5mesinlaporandt where tipe='Detail' and nourut LIKE '_152%' and namalaporan='ARUS KAS LANGSUNG'";
			}
		}
		*/
		
			
		
		



$jab=mysql_query($SQL);
$data="";
while ($row = mysql_fetch_object($jab)){
	$data.="<option value=\"".$row->noarus."\" >".$row->noarus." - ".$row->uraian."</option>\n";
}
echo $data;

		break;
    case'getForminvoice':
        $optSupplierCr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sSuplier="select distinct supplierid,namasupplier,substr(kodekelompok,1,1) as status from ".$dbname.".log_5supplier order by namasupplier asc";
        $qSupplier=mysql_query($sSuplier) or die(mysql_error($sSupplier));
        while($rSupplier=mysql_fetch_assoc($qSupplier))
        {
            $optSupplierCr.="<option value='".$rSupplier['supplierid']."'>".$rSupplier['namasupplier']." [".$rSupplier['status']."]</option>";
        }
        $form="<fieldset style=float: left;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']."</legend>".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg>&nbsp;".$_SESSION['lang']['namasupplier']."<select id=supplierIdcr style=width:150px>".$optSupplierCr."</select><button class=mybutton onclick=findNoinvoice()>Find</button></fieldset><div id=container2><fieldset><legend>".$_SESSION['lang']['result']."</legend></fieldset></div>";
        echo $form;
		break;
    case'getInvoice':
        $optNmsupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
        $arrTipe=array("p"=>"Pembelian","k"=>"Kontrak");
        $dat.="<fieldset><legend>".$_SESSION['lang']['result']."</legend>";
        $dat.="<div style=overflow:auto;width:100%;height:500px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $dat.="<td>".$_SESSION['lang']['noinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nopo']."</td>";
        $dat.="<td>".$_SESSION['lang']['namasupplier']."</td>";
        $dat.="<td>".$_SESSION['lang']['tipeinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nilaiinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nilaippn']."</td>";
        $dat.="<td>".$_SESSION['lang']['noakun']."</td>";
        $dat.="</tr></thead><tbody>";
        if($param['txtfind']!='')
        {
            $whereCr=" and noinvoice like '%".$param['txtfind']."%'";
        }
        else
        {
//            $whereCr=" and noinvoice in (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar is null or dibayar=0) and kodeorg='".$_SESSION['org']['kodeorganisasi']."')";
            $whereCr=" and noinvoice in (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar is null or dibayar=0) and kodeorg like '%%')";
        } 
        if($param['idSupplier']!='')
        {
            $whereCr.=" and kodesupplier='".$param['idSupplier']."'";
        }
//        $sPo="select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,noakun,keterangan from ".$dbname.".keu_tagihanht where kodeorg='".$_SESSION['org']['kodeorganisasi']."' ".$whereCr." order by tanggal asc";
        $sPo="select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,noakun,keterangan from ".$dbname.".keu_tagihanht where kodeorg like '%%' ".$whereCr." order by tanggal asc";
        //echo $sPo;
        $qPo=mysql_query($sPo) or die(mysql_error($conn));
        while($rPo=mysql_fetch_assoc($qPo))
        {
            $no+=1;
            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rPo['nilaiinvoice']."','".$rPo['noakun']."','".$rPo['keterangan']."','".$rPo['kodesupplier']."','".$rPo['nopo']."')\" style='pointer:cursor;'><td>".$no."</td>";
            $dat.="<td>".$rPo['noinvoice']."</td>";
            $dat.="<td>".$rPo['nopo']."</td>";
            $dat.="<td>".$optNmsupp[$rPo['kodesupplier']]."</td>";
            $dat.="<td>".$arrTipe[$rPo['tipeinvoice']]."</td>";
            $dat.="<td>".number_format($rPo['nilaiinvoice'],2)."</td>";
            $dat.="<td>".$rPo['nilaippn']."</td>";
            $dat.="<td>".$rPo['noakun']."</td></tr>";
        }
        $dat.="</tbody></table></div>#Status S atau K mewakili S=Supplier,K=Kontraktor</fieldset>";
        echo $dat;
		break;
    default:
	break;
}
?>