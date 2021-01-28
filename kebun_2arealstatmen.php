<?
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
//$optTahunTanam = makeOption($dbname,'setup_blok','tahuntanam,tahuntanam',
//    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'",'0',true);
$optkebun = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "tipe='KEBUN'",'0',true);
$optkebun[''] = $_SESSION['lang']['all'];


//$optTahunTanam = makeOption($dbname,'setup_blok','tahuntanam,tahuntanam',
//    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'",'0',true);

$optTahunTanam[''] = $_SESSION['lang']['all'];
$optAfdeling = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas'],false,'afdeling');
$optAfdeling[''] = $_SESSION['lang']['all'];
#$add=array('onchange'=>"getAfdeling(this,'afdeling','kebun_slave_2arealstatement')");
#$add2=array('onchange'=>"getThnTnm('kebun_slave_2arealstatement')");
$fReport = new formReport('arealstatement','kebun_slave_2arealstatmen',$_SESSION['lang']['arealstatement']);
$fReport->addPrime('periode',$_SESSION['lang']['periode'],'','bulantahun','','L',25);
$fReport->addPrime('unit',$_SESSION['lang']['unit'],'','select','L',20,$optkebun);
$fReport->_primeEls[1]->_attr['onchange'] = "getAfdeling(this,'afdeling','kebun_slave_2arealstatement')";
$fReport->addPrime('afdeling',$_SESSION['lang']['afdeling'],'','select','L',20,$optAfdeling);
$fReport->_primeEls[2]->_attr['onchange'] = "getThnTnm('kebun_slave_2arealstatement')";
$fReport->addPrime('tahuntanam',$_SESSION['lang']['tahuntanam'],'','select','L',20,$optTahunTanam);

/** View **/
echo open_body();
?>
<script language=javascript src="js/kebun_2arealstatement.js"></script>
<script language="JavaScript1.2" src="js/formReport.js"></script>
<script language="JavaScript1.2" src="js/biReport.js"></script>


<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?
include('master_mainMenu.php');

OPEN_BOX();
$fReport->render();
CLOSE_BOX();

echo close_body();
?>