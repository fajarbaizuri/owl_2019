<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
$fReport = new formReport('harianPengolahan','pabrik_slave_2harianPengolahan',"Laporan Pengolahan Harian");
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "tipe='PABRIK'");
$fReport->addPrime('pabrik',$_SESSION['lang']['pabrik'],'','select','L',30,$optOrg);
$fReport->addPrime('tanggal',$_SESSION['lang']['tanggal'],date('d-m-Y'),'date','L',15);
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