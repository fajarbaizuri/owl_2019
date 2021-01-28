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
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Basis Panen</font></b></u></p>
<?php
#======Select Prep======
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"induk='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING'");
$optTipe = array('JJG'=>'Janjang','KG'=>'KG');
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
  makeElement('tahuntanam','label',$_SESSION['lang']['tahuntanam']),
  makeElement('tahuntanam','textnum','',array('style'=>'width:70px'))
);
$els[] = array(
  makeElement('tipe','label',$_SESSION['lang']['tipe']),
  makeElement('tipe','select','',array('style'=>'width:100px'),$optTipe)
);
$els[] = array(
  makeElement('tahunproduksi','label',$_SESSION['lang']['tahunproduksi']),
  makeElement('tahunproduksi','textnum','',array('style'=>'width:70px'))
);
$els[] = array(
  makeElement('basisborong','label',$_SESSION['lang']['basisborong']),
  makeElement('basisborong','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('basisjumat','label',$_SESSION['lang']['basisjumat']),
  makeElement('basisjumat','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('rpsiapborong','label',$_SESSION['lang']['siapborong']),
  makeElement('rpsiapborong','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('rplebihborong1','label',$_SESSION['lang']['rplebihborong1']),
  makeElement('rplebihborong1','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('lebihborong2','label',$_SESSION['lang']['lebihborong2']),
  makeElement('lebihborong2','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('rplebihborong2','label',$_SESSION['lang']['rplebihborong2']),
  makeElement('rplebihborong2','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('lebihborong3','label',$_SESSION['lang']['lebihborong3']),
  makeElement('lebihborong3','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('rplebihborong3','label',$_SESSION['lang']['rplebihborong3']),
  makeElement('rplebihborong3','textnum','',array('style'=>'width:100px'))
);
$els[] = array(
  makeElement('brondolankg','label',$_SESSION['lang']['brondolankg']),
  makeElement('brondolankg','textnum','',array('style'=>'width:100px'))
);

# Fields
$fieldStr = "##kodeorg##tahuntanam##tipe##tahunproduksi##basisborong##".
	"basisjumat##rpsiapborong##rplebihborong1##lebihborong2##rplebihborong2".
	"##lebihborong3##rplebihborong3##brondolankg";
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'kebun_5basispanen',"##kodeorg##tahuntanam##tipe")
);

# Generate Field
echo genElementMultiDim('Form',$els,2);
echo "</div>";
#=======End Form============

#=======Table===============
# Condition
$where= "kodeorg in (";
$i=0;
foreach($optOrg as $key=>$row) {
	if($i==0) {
		$where.= "'".$key."'";
	} else {
		$where.= ",'".$key."'";
	}
	$i++;
}
$where.= ")";

# Display Table
echo "<div style='height:250px;overflow:auto;float:left;clear:left'>";
echo masterTable($dbname,'kebun_5basispanen',"*",array(),array(),$where,array(),
	null,'kodeorg##tahuntanam##tipe',true,null,array(),null,null,
	array('kodeorg'=>$optOrg,'tipe'=>$optTipe));
echo "</div>";
#=======End Table============
CLOSE_BOX();
echo close_body();
?>