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

	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN'  order by kodeorganisasi";

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kodeorg##kegiatan##tgl1##tgl2";

$kegiatan="<option value=''>Seluruhnya</option>";
$str="select noakun,namaakun from ".$dbname.".keu_5akun where noakun like '126%' or noakun like '62%'

      order by noakun,namaakun";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if(strlen($bar->noakun)==3)
            $head=$bar->namaakun;
    if(strlen($bar->noakun)==7)
        $kegiatan.="<option value='".$bar->noakun."'>".$bar->namaakun."-".$head."</option>";
}

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>


<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo "Laporan Biaya Per Blok"; ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kebun']?></label></td><td><select id="kodeorg" name="kdOrg" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kegiatan']?></label></td><td><select id="kegiatan" name="kdAfd" style="width:150px"><?php echo $kegiatan?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td>
<input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" /> s.d.
<input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" /></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <button onclick="zPreview('kebun_slave_2kegiatanPerBlok','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
    <button onclick="zPdf('kebun_slave_2kegiatanPerBlok','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
    <button onclick="zExcel(event,'kebun_slave_2kegiatanPerBlok.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
 
</table>
</fieldset>
</div>
<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto; height:350px; max-width:1220px;'>

</div></fieldset>
<?php

CLOSE_BOX();
echo close_body();
?>