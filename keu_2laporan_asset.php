<?php
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
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PT'";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}

$optAst="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodetipe,namatipe FROM ".$dbname.".sdm_5tipeasset";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optAst.="<option value=".$data['kodetipe'].">".$data['namatipe']."</option>";
}
$optBatch="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
 $sBatch="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qBatch=mysql_query($sBatch) or die(mysql_error());
while($rBatch=mysql_fetch_assoc($qBatch))
{
    $optBatch.="<option value='".$rBatch['periode']."'>".$rBatch['periode']."</option>";
}
$optTipeAsset="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTipeAsset="select distinct kodetipe,namatipe from ".$dbname.".sdm_5tipeasset order by namatipe asc";
$qTipeAsset=mysql_query($sTipeAsset) or die(mysql_error());
while($rTipeAsset=mysql_fetch_assoc($qTipeAsset))
{
    $optTipeAsset.="<option value='".$rTipeAsset['kodetipe']."'>".$rTipeAsset['namatipe']."</option>";
}

$arr="##kdOrg##kdAst##tpAsset##kdunit";
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language=javascript src='js/keu_2rbkt.js'></script>

<script language=javascript>
	function batal()
	{
		document.getElementById('kdOrg').value='';	
		document.getElementById('kdAst').value='';
		document.getElementById('printContainer').innerHTML='';
		document.getElementById('kdunit').value='';
	}	
</script>

<div style="margin-bottom: 30px;">
<fieldset style="float: left;" >
<legend><b><?php echo $_SESSION['lang']['daftarasset'] ?></b></legend>

<table cellspacing="1" border="0" >
    <tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td width="10">:</td><td width="155"><select id="kdOrg" name="kdOrg" onchange="getunitaset()" style="width:150px;" ><?php echo $optOrg?></select></td></tr>
     <tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td width="10">:</td><td width="155"><select id="kdunit" name="kdunit" style="width:150px;" ><?php echo $optunit?></select></td></tr>
    <tr><td><label><?php echo $_SESSION['lang']['sdbulanini']." ".$_SESSION['lang']['periode']?></label></td><td>:</td><td><select id="kdAst" name="kdAst" style="width:150px;"><?php echo $optBatch?></select></td></tr>
    <tr><td><label><?php echo $_SESSION['lang']['tipeasset']?></label></td><td>:</td><td><select id="tpAsset" name="tpAsset" style="width:150px;"><?php echo $optTipeAsset?></select></td></tr>
	<tr></tr></table>
    
    <table width="400"><td width="115"></tr><td>&nbsp;</td>
    <td width="273" colspan="3">
        <button onclick="zPreview('keu_slave_2laporanAsset','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zPdf('keu_slave_2laporanAsset','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
        <button onclick="zExcel(event,'keu_slave_2laporanAsset.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
        <button onclick="batal()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel'];?></button></td></tr>
</table>
</fieldset>
</div>



<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>




<?php
CLOSE_BOX();
echo close_body();
?>