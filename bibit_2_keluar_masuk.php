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
$optBatch="<option value=''>".$_SESSION['lang']['all']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
    $sBatch="select distinct batch from ".$dbname.".bibitan_mutasi order by batch desc";
    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' order by namaorganisasi asc";
}
else
{
    $sBatch="select distinct batch from ".$dbname.".bibitan_mutasi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
}
$qBatch=mysql_query($sBatch) or die(mysql_error());
while($rBatch=mysql_fetch_assoc($qBatch))
{
    $optBatch.="<option value='".$rBatch['batch']."'>".$rBatch['batch']."</option>";
}

$optKodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$qKodeOrg=mysql_query($sKodeorg) or die(mysql_error());
while($rKodeorg=mysql_fetch_assoc($qKodeOrg))
{
    $optKodeorg.="<option value='".$rKodeorg['kodeorganisasi']."'>".$rKodeorg['namaorganisasi']."</option>";
}
$arr="##kdUnit##kdBatch";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanStockBIbit']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">
<?php echo $optKodeorg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['batch']?></label></td><td><select id="kdBatch" name="kdBatch" style="width:150px">
<?php echo $optBatch?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview('bibit_2_slave_keluar_masuk','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('bibit_2_slave_keluar_masuk','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'bibit_2_slave_keluar_masuk.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
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