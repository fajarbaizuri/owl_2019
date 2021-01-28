<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zConfig.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/setup_blok.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$tmpKlsTanah = readLst("./config/kelastanah.lst");
$optKlsTanah = lst2opt($tmpKlsTanah,0,1);
$tmpJenisTanah = readLst("./config/jenistanah.lst");
$optJenisTanah = lst2opt($tmpJenisTanah,0,1);
$optTopografi = makeOption($dbname,'setup_topografi','topografi,keterangan');
$optOrg = array();
$optMonth = optionMonth('I','long');
$optBlokStat = getEnum($dbname,'setup_blok','statusblok');
$optIP = getEnum($dbname,'setup_blok','intiplasma');
$optIP['I'] = 'Inti';
$optIP['P'] = 'Plasma';
#======End Select Prep======

#=======Search==============
# Get Options
if($_SESSION['empl']['tipelokasitugas']=='HOLDING') {
  $tmpOpt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe='KEBUN'");
} elseif($_SESSION['empl']['tipelokasitugas']=='KEBUN') {
  $tmpOpt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
} else {
  $tmpOpt = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas'],false,'kebunonly');
}
$sKebun = array(''=>'');
foreach($tmpOpt as $key=>$row) {
  $sKebun[$key] = $row;
}
$optBibit = makeOption($dbname,'setup_jenisbibit','jenisbibit,jenisbibit');

# Create Elements
$searchEls = $_SESSION['lang']['kebun']." ";
$searchEls .= makeElement('sKebun','select','',
  array('onchange'=>"getAfdeling(this,'sAfdeling')",'style'=>'width:150px'),$sKebun)." ";
$searchEls .= $_SESSION['lang']['afdeling']." ";
$searchEls .= makeElement('sAfdeling','select','',array('style'=>'width:150px'),array())." ";
$searchEls .= makeElement('searchIt','button',$_SESSION['lang']['find'],array('onclick'=>'showData()'))." ";

# Render Search Element
echo "<fieldset id='search' style='margin-bottom:10px;float:left;clear:both'>";
echo "<legend><b>".$_SESSION['lang']['searchdata']."</b></legend>";
echo $searchEls;
echo "</fieldset>";
#=======End Search==========

#=======Form============
echo "<div id='formBlok' style='display:none;margin-bottom:10px;clear:both'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('tahuntanam','label',$_SESSION['lang']['tahuntanam']),
  makeElement('tahuntanam','text','',array('style'=>'width:70px','maxlength'=>'6')).
  makeElement('tahuntanamCurr','hidden','')
);
$els[] = array(
  makeElement('luasareaproduktif','label',$_SESSION['lang']['luasareaproduktif']),
  makeElement('luasareaproduktif','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('luasareanonproduktif','label',$_SESSION['lang']['luasareanonproduktif']),
  makeElement('luasareanonproduktif','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);

################################################################ Detail Areal ##
$els[] = array(
  makeElement('cadangan','label',$_SESSION['lang']['arealcadangan']),
  makeElement('cadangan','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('okupasi','label',$_SESSION['lang']['arealokupasi']),
  makeElement('okupasi','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('rendahan','label',$_SESSION['lang']['arealrendahan']),
  makeElement('rendahan','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('sungai','label',$_SESSION['lang']['arealsungai']),
  makeElement('sungai','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('rumah','label',$_SESSION['lang']['arealrumah']),
  makeElement('rumah','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('kantor','label',$_SESSION['lang']['arealkantor']),
  makeElement('kantor','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('pabrik','label',$_SESSION['lang']['arealpabrik']),
  makeElement('pabrik','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('jalan','label',$_SESSION['lang']['arealjalan']),
  makeElement('jalan','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('kolam','label',$_SESSION['lang']['arealkolam']),
  makeElement('kolam','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
$els[] = array(
  makeElement('umum','label',$_SESSION['lang']['arealumum']),
  makeElement('umum','textnum','',array('style'=>'width:80px','maxlength'=>'10',
    'onblur'=>'this.value=_formatted(this)'))." Ha"
);
############################################################### /Detail Areal ##

####################################################################### Pokok ##
$els[] = array(
  makeElement('jumlahpokok','label',$_SESSION['lang']['jumlahpokok']),
  makeElement('jumlahpokok','text','',array('style'=>'width:70px','maxlength'=>'10','onkeypress'=>'return angka_doang(event)'))
);
###################################################################### /Pokok ##

$els[] = array(
  makeElement('statusblok','label',$_SESSION['lang']['statusblok']),
  makeElement('statusblok','select','',array('style'=>'width:100px'),$optBlokStat)
);
$els[] = array(
  makeElement('tahunmulaipanen','label',$_SESSION['lang']['mulaipanen']),
  makeElement('bulanmulaipanen','select','',array('style'=>'width:150px'),$optMonth)." / ".
  makeElement('tahunmulaipanen','text','',array('style'=>'width:90px','maxlength'=>'4','onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('kodetanah','label',$_SESSION['lang']['kodetanah']),
  makeElement('kodetanah','select','',array('style'=>'width:150px'),$optJenisTanah)
);
$els[] = array(
  makeElement('klasifikasitanah','label',$_SESSION['lang']['klasifikasitanah']),
  makeElement('klasifikasitanah','select','',array('style'=>'width:150px'),$optKlsTanah)
);
$els[] = array(
  makeElement('topografi','label',$_SESSION['lang']['topografi']),
  makeElement('topografi','select','',array('style'=>'width:150px' ),$optTopografi)
);
$els[] = array(
  makeElement('intiplasma','label',$_SESSION['lang']['intiplasma']),
  makeElement('intiplasma','select','',array('style'=>'width:150px'),$optIP)//'style'=>'width:150px'
);

$els[] = array(
  makeElement('jenisbibit','label',$_SESSION['lang']['jenisbibit']),
  makeElement('jenisbibit','select','',array('style'=>'width:150px'),$optBibit)
);
$els[] = array(
  makeElement('tanggalpengakuan','label',$_SESSION['lang']['tanggal']),
  makeElement('tanggalpengakuan','text','',array('style'=>'width:150px',
  'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
);

# Fields
$fieldStr = '##kodeorg##tahuntanam##tahuntanamCurr##luasareaproduktif##luasareanonproduktif';
$fieldStr .= '##jumlahpokok##statusblok##bulanmulaipanen##tahunmulaipanen';
$fieldStr .= '##kodetanah##klasifikasitanah##topografi##intiplasma##jenisbibit##tanggalpengakuan';
$fieldStr .= '##cadangan##okupasi##rendahan##sungai##rumah##kantor##pabrik';
$fieldStr .= '##jalan##kolam##umum';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'setup_blok',"##kodeorg##tahuntanam",'setup_slave_blok_add',null,null,
    'setup_slave_blok_edit','##tahuntanamCurr')
);

# Generate Field
echo genElementMultiDim('Blok',$els,3);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div id='blokTable' style='float:left;clear:both;'>";
#echo masterTable($dbname,'setup_blok',"*",array(),array(),array(),array(),'setup_slave_blok_pdf');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>


<?php
/*
$els[] = array(
  makeElement('intiplasma','label',$_SESSION['lang']['intiplasma']),
  makeElement('intiplasma','select','',array('onchange'=>"getKoperasi(this,'sKoperasi')",'style'=>'width:150px'),$optIP)//'style'=>'width:150px'
);

//koperasi
$els[] = array(
  makeElement('koperasi','label',Koperasi),
  makeElement('koperasi','text','',array('style'=>'width:70px','maxlength'=>'10','onkeypress'=>'return angka_doang(event)','disabled'=>'disabled'))
);

$els[] = array(
  makeElement('pokokproduktif','label',$_SESSION['lang']['pokokproduktif']),
  makeElement('pokokproduktif','text','',array('style'=>'width:70px','maxlength'=>'10','onkeypress'=>'return angka_doang(event)'))
);

*/
?>