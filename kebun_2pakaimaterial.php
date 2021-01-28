<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
if($_SESSION['empl']['tipelokasitugas']=='HOLDING') {
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "tipe='KEBUN'");
} else {
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
}

$optTahunTanam = makeOption($dbname,'setup_blok','tahuntanam,tahuntanam',
    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'",'0',true);
$optTahunTanam[''] = $_SESSION['lang']['all'];

if(!isset($_SESSION['lang']['lapmaterial'])) {
    $_SESSION['lang']['lapmaterial'] = ucfirst('pakaimaterial');
}
$fReport = new formReport('pakaimaterial','kebun_slave_2pakaimaterial',$_SESSION['lang']['lapmaterial']);
$fReport->addPrime('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',20,$optOrg);
$fReport->addPrime('periode',$_SESSION['lang']['periode'],'','bulantahun','L',25);
#$fReport->addPrime('tahuntanam',$_SESSION['lang']['tahuntanam'],'','select','L',20,$optTahunTanam);

/** View **/
echo open_body();
?>
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