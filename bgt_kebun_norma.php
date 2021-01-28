<?
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/zSearch.js"></script>
<script language="javascript" src="js/zTools.js"></script>
<script language="javascript" src="js/formTable.js"></script>
<script type="text/javascript" src="js/bgt_kebun_norma.js"></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php

## Tab Attribute
$headFrame = array(
	'Pemeliharaan'
);
$contentFrame = array();

# Get Data
/*$cols = 'a.kodeorg,b.kelompok,a.kodekegiatan,a.tipeanggaran,a.kodebarang,'.
	'a.topografi,a.jumlah,b.namakegiatan';
$query = "select ".$cols." from `".$dbname."`.`setup_kegiatannorma` a ".
	"left join `".$dbname."`.`setup_kegiatan` b on a.kodekegiatan=b.kodekegiatan ".
	" where b.kelompok in ('TB','LC','BBT') and left(a.kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'";
$data= fetchData($query);*/

# Get Data Perawatan
$cols1 = 'a.kodeorg,a.tahuntanam,a.kelompok,a.kodekegiatan,a.tipeanggaran,a.kodebarang,'.
	'a.topografi,a.jumlah,b.namakegiatan,a.satuan';
$query1 = "select ".$cols1." from `".$dbname."`.`setup_kegiatannorma` a ".
	"left join `".$dbname."`.`setup_kegiatan` b on a.kodekegiatan=b.kodekegiatan ".
	" where b.kelompok in ('TBM','TM','PNN') and left(a.kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'";
$tmpData = fetchData($query1);
$data1 = array();
foreach($tmpData as $row) {
	$data1[] = array(
		'afdeling'=>$row['kodeorg'],
		'tahuntanam'=>$row['tahuntanam'],
		'kelompok1'=>$row['kelompok'],
		'kodekegiatan1'=>$row['kodekegiatan'],
		'tipeanggaran1'=>$row['tipeanggaran'],
		'kodebarang1'=>$row['kodebarang'],
		'topografi1'=>$row['topografi'],
		'jumlah1'=>$row['jumlah'],
		'namakegiatan'=>$row['namakegiatan'],
		'satuan'=>$row['satuan']
	);
}

###################################################################### Option ##
$whereBrg='';
foreach($data1 as $row) {
	if($whereBrg!='') {
		$whereBrg.=',';
	}
	$whereBrg.="'".$row['kodebarang1']."'";
}
if($whereBrg=='') {
	$optBrg=array();
} else {
	$optBrg = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
		"kodebarang in (".$whereBrg.")");
}
$optBrg[0]='';

### Org untuk LC
$qOrg = "select a.kodeorg,b.namaorganisasi,a.statusblok from `".$dbname."`.`setup_blok` a ".
	"left join `".$dbname."`.`organisasi` b on a.kodeorg=b.kodeorganisasi".
	" where a.statusblok in ('TB','LC','BBT') and left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'";
$tmpOrg = fetchData($qOrg);
$optOrg = array();
foreach($tmpOrg as $row) {
	$optOrg[$row['kodeorg']] = $row['namaorganisasi'];
}
$statusBlok = $tmpOrg[0]['statusblok'];

### Org untuk Perawatan
$optOrg1 = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"induk='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING'");

$optKlp2 = makeOption($dbname,'kebun_5sttanam','kode,keterangan',
	"kode in ('TBM','TM','PNN')");
$firstKlp = getFirstKey($optKlp2);

$optKeg1 = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
	"kelompok in ('TBM','TM','PNN')",'1',true);
$optKeg1[''] = 'Pilih Data';

$optTipe = makeOption($dbname,'bgt_kode','kodebudget,nama',
	"kodebudget in ('KONTRAK') or left(kodebudget,3)='SDM' or ".
	"left(kodebudget,1)='M'",'2',true);
$optTipe[''] = 'Pilih Data';
$optTopografi = makeOption($dbname,'setup_topografi','topografi,keterangan');
##################################################################### /Option ##

# Data Show LC
//$dataShow = $data;
//foreach($dataShow as $key=>$row) {
//	$dataShow[$key]['kodekegiatan'] = $row['namakegiatan'];
//	$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
//	$dataShow[$key]['tipeanggaran'] = $optTipe[$row['tipeanggaran']];
//	$dataShow[$key]['topografi'] = $optTopografi[$row['topografi']];
//	$dataShow[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
//	$dataShow[$key]['kelompok'] = $optKlp1[$row['kelompok']];
//	unset($data[$key]['namakegiatan']);
//}

# Data Show Perawatan
$dataShow1 = $data1;
foreach($dataShow1 as $key=>$row) {
	$dataShow1[$key]['kodekegiatan1'] = $row['namakegiatan'];
	$dataShow1[$key]['afdeling'] = $optOrg1[$row['afdeling']];
	$dataShow1[$key]['tipeanggaran1'] = $optTipe[$row['tipeanggaran1']];
	$dataShow1[$key]['topografi1'] = $optTopografi[$row['topografi1']];
	$dataShow1[$key]['kodebarang1'] = $optBrg[$row['kodebarang1']];
	$dataShow1[$key]['kelompok1'] = $optKlp2[$row['kelompok1']];
	unset($data1[$key]['namakegiatan']);
}

############################################################### Form Table LC ##
# Form
/*$theForm1 = new uForm('lcForm','Form Norma',2);
$theForm1->addEls('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',30,$optOrg);
$theForm1->_elements[0]->_attr['onchange'] = "changeBlok()";
$theForm1->addEls('kelompok',$_SESSION['lang']['kelompok'],'','select','L',20,$optKlp);
$theForm1->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','select','L',35,$optKeg);
$theForm1->addEls('tipeanggaran',$_SESSION['lang']['tipeanggaran'],'','select','L',25,$optTipe);
$theForm1->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'0','searchBarang','L',20);
$theForm1->addEls('topografi',$_SESSION['lang']['topografi'],'H','select','L',25,$optTopografi);
$theForm1->addEls('jumlah',$_SESSION['lang']['jumlah'],'0','textnum','R',10);

# Table
$theTable1 = new uTable('lcTable','Table Norma',$cols,$data,$dataShow);

# FormTable
$formTab1 = new uFormTable('ftLc',$theForm1,$theTable1,null,array());
$formTab1->_target = "bgt_slave_kebun_norma_lc";
$formTab1->_noEnable = '##kodebarang';
$formTab1->_defValue = "##kodebarang=0";

$contentFrame[0] = $formTab1->prep();*/
############################################################## /Form Table LC ##

##################################################### Form Table Pemeliharaan ##
# Form
$theForm2 = new uForm('rawatForm','Form Norma',2);
$theForm2->addEls('afdeling',$_SESSION['lang']['afdeling'],'','select','L',30,$optOrg1);
$theForm2->addEls('tahuntanam',$_SESSION['lang']['tahuntanam'],date('Y'),'textnum','R',7);
$theForm2->_elements[1]->_attr['maxlength'] = 4;
$theForm2->addEls('kelompok1',$_SESSION['lang']['kelompok'],'','select','L',35,$optKlp2);
$theForm2->_elements[2]->_attr['onchange'] = "changeKlp()";
$theForm2->addEls('kodekegiatan1',$_SESSION['lang']['kodekegiatan'],'','select','L',35,$optKeg1);
$theForm2->_elements[3]->_attr['onchange'] = "changeSatuan()";
$theForm2->addEls('tipeanggaran1',$_SESSION['lang']['tipeanggaran'],'','select','L',35,$optTipe);
$theForm2->_elements[4]->_attr['onchange'] = "changeSatuan()";
$theForm2->addEls('kodebarang1',$_SESSION['lang']['kodebarang'],'0','searchBarang','L',20,null,null,'hiddenSatuan');
$theForm2->addEls('topografi1',$_SESSION['lang']['topografi'],'H','select','L',25,$optTopografi);
$theForm2->addEls('jumlah1','Norma','0','textnum','R',10);
$theForm2->_elements[7]->_attr['onfocus'] = "makeSatuan()";
$theForm2->addEls('satuan','Satuan','0','text','R',10);
$theForm2->_elements[8]->_attr['disabled'] = 'disabled';

# Table
$theTable2 = new uTable('rawatTable','Table Norma',$cols1,$data1,$dataShow1);

# FormTable
$formTab2 = new uFormTable('ftRawat',$theForm2,$theTable2,null,array());
$formTab2->_target = "bgt_slave_kebun_norma_rawat";
$formTab2->_noEnable = '##kodebarang1';
$formTab2->_defValue = "##tahuntanam=".date('Y')."##kodebarang1=0";

$contentFrame[0] = $formTab2->prep();
#################################################### /Form Table Pemeliharaan ##

### View ###
OPEN_BOX('',"<b>Norma Anggaran</b>");
CLOSE_BOX();
OPEN_BOX();
echo '<input type=hidden id=hiddenKegSatuan>';
echo '<input type=hidden id=hiddenSatuan>';
drawTab('FRM',$headFrame,$contentFrame,150,'100%');
CLOSE_BOX();
echo close_body();
?>