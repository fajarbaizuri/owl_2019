<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where  tipe='KEBUN' order by kodeorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$arr="##kdUnit##afdId##BlokId##periodeId";
$optModel="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_aktifitas order by tanggal desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optModel.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/kebun_restan.js'></script>
<script>
function Clear1()
{
    document.getElementById('thnBudget').value='';
    document.getElementById('kdUnit').value='';
    document.getElementById('printContainer').innerHTML='';
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapRestan']?></b></legend>
<table cellspacing="1" border="0" >
<?php
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
}
$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
$optBlok=$optAfd;
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_restan where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($sPeriode));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
echo"<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\" onchange='getAfd()'>".$optUnit."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id=\"afdId\" name=\"afdId\" style=\"width:150px\"  onchange='getBlok()'>".$optAfd."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['blok']."</label></td><td><select id=\"BlokId\" name=\"BlokId\" style=\"width:150px\">".$optBlok."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periodeId\" name=\"periodeId\" style=\"width:150px\">".$optPeriode."</select></td></tr>";
?>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('kebun_slave_2laporan_restan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('kebun_slave_2laporan_restan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'kebun_slave_2laporan_restan.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
</td></tr>

</table>
</fieldset>
</div>

<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:650px;max-width:1220px'>

</div></fieldset>

<?php

CLOSE_BOX();
echo close_body();
?>