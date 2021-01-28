<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/kebun_5bjrharian.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<p align="left"><u><b><font face="Arial" size="3" color="#000080">Setup > BJR Harian</font></b></u></p>
<?php
#======Select Prep======
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"induk='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING'");
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('tahuntanam','label',$_SESSION['lang']['tahuntanam']),
  makeElement('tahuntanam','textnum','',array('style'=>'width:70px','maxlength'=>4))
);
$els[] = array(
  makeElement('afdeling','label',$_SESSION['lang']['afdeling']),
  makeElement('afdeling','select','',array('style'=>'width:170px'),$optOrg)
);
$els[] = array(
  makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
  makeElement('tanggal','date','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('bjr','label',$_SESSION['lang']['bjr']),
  makeElement('bjr','textnum','',array('style'=>'width:100px'))
);

# Fields
$fieldStr = "##tahuntanam##afdeling##tanggal##bjr";
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'kebun_5bjrharian',"##tahuntanam##afdeling##tanggal")
);

# Generate Field
echo genElementMultiDim('Form',$els,1);
echo "</div>";
#=======End Form============

#=======Table===============
$htmlAbove = "<div style='padding:7px 0 7px 0;'>".makeElement('filterDate','date',date('d-m-Y')).
	makeElement('filterDateBtn','btn','Filter',array('onclick'=>'filterData()'))."</div>";

# Display Table
echo "<div style='height:250px;overflow:auto;float:left;clear:left'>";
/*
echo masterTable($dbname,'kebun_5bjrharian',"*",array(),array(),null,array(),
	null,'tahuntanam##afdeling##tanggal',true,null,array(),null,null,
	array('afdeling'=>$optOrg));*/	
echo masterTable($dbname,'kebun_5bjrharian',"*",array(),array(),
	"left(afdeling,4) = '".$_SESSION['empl']['lokasitugas']."'",array(),null,null,true,
	null,array(),null,null,array(),null,$htmlAbove);

echo "</div>";
#=======End Table============
CLOSE_BOX();
echo close_body();
?>