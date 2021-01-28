<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<?
$arr="##kdUnit##tgl_ganti";
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optUnit;
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
        where substr(induk,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error());
while($rUnit=mysql_fetch_assoc($qUnit))
{
   $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
}
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}

echo"
<fieldset style=\"float: left;\">
<legend><b>Kondisi Lapangan TB </b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\">".$optUnit."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl_ganti\" name=\"tgl_ganti\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:150px\" /></td></tr>



<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\">
<button onclick=\"zPreview('kebun_slave_qc_laporanKondisiTB','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
<button onclick=\"zPdf('kebun_slave_qc_laporanKondisiTB','".$arr."','printContainer')\" class=\"mybutton\">PDF</button>    
<button onclick=\"zExcel(event,'kebun_slave_qc_laporanKondisiTB.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>

</table>
</fieldset>";
echo"
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>";

?>
<?php
CLOSE_BOX();
echo close_body();
?>