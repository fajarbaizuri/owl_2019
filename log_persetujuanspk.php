<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/log_persetujuanspk.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "length(kodeorganisasi)=4");

# Fields
$els = array();
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listPosting()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
echo genElTitle("Persetujuan SPK",$els);
CLOSE_BOX();

# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listPosting',null,true);
CLOSE_BOX();

#=== End ===
close_body();
?>