<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "tipe='BLOK' and left(kodeorganisasi,4)='".$_SESSION['empl']['lokasitugas']."'",0,true);
$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',null,0,true);
$optBlok['']='Seluruhnya';
$optKeg['']='Seluruhnya';

$fReport = new formReport('progressspk','log_slave_2progressSpk',"Progress SPK");
$fReport->addPrime('periode',$_SESSION['lang']['periode'],date('d-m-Y'),'period','L',15);
$fReport->addPrime('kodeblok',$_SESSION['lang']['blok'],'','select','L',30,$optBlok);
$fReport->addPrime('kegiatan',$_SESSION['lang']['kegiatan'],'','select','L',30,$optKeg);
$fReport->_detailHeight = 60;

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