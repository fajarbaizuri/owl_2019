<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
//tentukan table yang akan dikontrol
$TABLENAME='sdm_5absensi';
?>

<script language=javascript1.2 src=js/tablebrowser.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX("","<b>Table:".$TABLENAME."</b>");
printTableController($TABLENAME);
CLOSE_BOX();
OPEN_BOX("","<b>Table:".$TABLENAME."</b>");
echo"<div id=container style='width:100%;height:400px;overflow:scroll;'></div>";
CLOSE_BOX();
echo close_body();
?>