<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/formTable.js></script>
<script language=javascript src=js/pabrik_5shift.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$where = "`tipe`='PABRIK'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');
$whereKary = "";$i=0;
foreach($optOrg as $key=>$row) {
  if($i==0) {
    $whereKary .= "lokasitugas='".$key."'";
  } else {
    $whereKary .= " or lokasitugas='".$key."'";
  }
  $i++;
}
$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary,'0');
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
  makeElement('shift','label',$_SESSION['lang']['shift']),
  makeElement('shift','textnum','1',array('style'=>'width:200px','maxlength'=>'1',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('mandor','label',$_SESSION['lang']['mandor']),
  makeElement('mandor','select','',array('style'=>'width:200px'),$optKary)
);
$els[] = array(
  makeElement('asisten','label',$_SESSION['lang']['asisten']),
  makeElement('asisten','select','',array('style'=>'width:200px'),$optKary)
);
/*$els[] = array(
  makeElement('berlakusdtgl','label',$_SESSION['lang']['berlakusdtgl']),
  makeElement('berlakusdtgl','text','',array('style'=>'width:200px',
    'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
);
*/
# Fields
$fieldStr = '##kodeorg##shift##mandor##asisten';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'pabrik_5shift',"##kodeorg##shift",null,'kodeorg##shift')
);

# Generate Field
echo genElTitle('Shift',$els);
echo "</div>";
#=======End Form============
#=======Prepare Table=======

$table = 'pabrik_5shift';

# Extract Data
$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$query = "select * from ".$dbname.".".$table." where ".$where;
$res=mysql_query($query);
$j = mysql_num_fields($res);
$i = 0;
$field = array();
$fieldStr = "";
$primary = array();
$primaryStr = "";

# Get Names
while ($i < $j) {
  $meta = mysql_fetch_field($res, $i);
  # Get Field Name
  $field[] = strtolower($meta->name);
  $fieldStr .= "##".strtolower($meta->name);
  
  # Get Primary Key and Value
  if($meta->primary_key=='1') {
    $primary[] = strtolower($meta->name);
    $primaryStr .= "##".strtolower($meta->name);
  }
  
  $i++;
}

$fForm = $field;

# Rearrange Result and Extract Values
$result = array();
while($bar=mysql_fetch_assoc($res)) {
  $result[] = $bar;
}
#======Create Table======
# Create Print
$tables = "<fieldset><legend><b>".$_SESSION['lang']['list']." : ".$table."</b></legend>";
$tables .= "<img src='images/pdf.jpg' title='PDF Format'
  style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','*',null,'slave_master_pdf',event)\">&nbsp;";
$tables .= "<img src='images/printer.png' title='Print Page'
  style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";

# Start Table
$tables .= "<div style='overflow:auto'>";
$tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";

# Create Header
$tables .= "<thead><tr class='rowheader'>";
foreach($field as $hName) {
  $tables .= "<td>".$_SESSION['lang'][$hName]."</td>";
}

$tables .= "<td colspan='3'></td>";
$tables .= "</tr></thead>";

# Iterate Content
$tables .= "<tbody id='mTabBody'>";
$i=0;
foreach($result as $row) {
  $tables .= "<tr id='tr_".$i."' class='rowcontent'>";
  $tmpVal = "";
  $tmpKey = "";
  $j=0;
  foreach($row as $b=>$c) {
    # For Tipe Tanggal
    $tmpC = explode("-",$c);
    if(count($tmpC)==3) {
      $c = $tmpC[2]."-".$tmpC[1]."-".$tmpC[0];
    }
    
    $tables .= "<td id='".$fForm[$j]."_".$i."' value='".$c."'>".$c."</td>";
    $tmpVal .= "##".$c;
    if(in_array($fForm[$j],$primary)) {
      $tmpKey .= "##".$c;
    }
    $j++;
  }
  # Edit, Delete Row
  $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','kodeorg##shift')\"
    class='zImgBtn' src='images/001_45.png' /></td>";
  $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow(".$i.",'".$primaryStr."','".$tmpKey."',null,'".$table."')\"
    class='zImgBtn' src='images/delete_32.png' /></td>";
  $tables .= "<td><img id='detail".$i."' title='Edit Detail' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','kodeorg##shift');".
    "showDetail(".$i.",'".$primaryStr."##kodeorg##shift',event)\"
    class='zImgBtn' src='images/application/application_view_xp.png' /></td>";
  $tables .= "</tr>";
  $i++;
}
$tables .= "</tbody>";

# Create Footer
$tables .= "<tfoot></tfoot>";

# End Table
$tables .= "</table></div></fieldset>";

#=======End Prepare Table=======
#=======Table===============
# Display Table
echo "<div style='clear:both;float:left'>";
echo $tables;
#echo masterTable($dbname,'pabrik_5shift',"*",array(),array(),array(),array(),null,'kodeorg##shift');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>