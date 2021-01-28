<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/log_3claimspk.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$whereSPK = "posting=0";
$optSPK = makeOption($dbname,'log_claimspk','nospk,nospk',$whereSPK);

# Fields
$els = array();
$els[] = array(
  makeElement('nospk','label',"No SPK"),
  makeElement('nospk','select','',array('style'=>'width:200px'),$optSPK)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listBAPP()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
echo genElTitle("Posting Klaim SPK",$els);
CLOSE_BOX();

# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listBAPP',null,true);
CLOSE_BOX();

#=== End ===
close_body();
?>