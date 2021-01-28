<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Jam Efektif</font></b></u></p>
<?php
#======Select Prep======
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"length(kodeorganisasi)=4");
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
  makeElement('tanggal','date','',array('style'=>'width:70px'))
);
$els[] = array(
  makeElement('jumlahjam','label',$_SESSION['lang']['jumlahjam']),
  makeElement('jumlahjam','textnum','',array('style'=>'width:70px'))
);
$els[] = array(
  makeElement('shift','label',$_SESSION['lang']['shift']),
  makeElement('shift','textnum','',array('style'=>'width:100px'))
);

# Fields
$fieldStr = "##kodeorg##tanggal##jumlahjam##shift";
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'sdm_5jamefektif',"##kodeorg##tanggal##shift")
);

# Generate Field
echo genElementMultiDim('Form',$els,1);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:250px;overflow:auto;float:left;clear:left'>";
echo masterTable($dbname,'sdm_5jamefektif',"*",array(),array(),null,array(),
	null,'kodeorg##tanggal##shift',true,null,array(),null,null,
	array('kodeorg'=>$optOrg));
echo "</div>";
#=======End Table============
CLOSE_BOX();
echo close_body();
?>