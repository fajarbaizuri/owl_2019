<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

$arr="##kdUnit##periode##judul##KMonitor";
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
 
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optUnit;
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
    where CHAR_LENGTH(kodeorganisasi)='4' and tipe='KEBUN' order by namaorganisasi asc";
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
$optMonitor.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optMonitor.="<option value='1'>Kapasitas Angkutan</option>";
$optMonitor.="<option value='2'>Trip Angkutan</option>";
$optMonitor.="<option value='3'>Rata - Rata Trip Angkutan </option>";
$optMonitor.="<option value='5'>Kapasitas Angkutan perKendaraan</option>";
$optMonitor.="<option value='6'>Trip Angkutan perKendaraan</option>";
$optMonitor.="<option value='7'>Rata - Rata Trip Angkutan perKendaraan</option>";
$optMonitor.="<option value='4'>Data Angkutan Panen </option>";

echo"
<fieldset style=\"float: left;\">
<legend><b>".$_POST['judul']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >

<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:300px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:300px\"  >".$optUnit."</select></td></tr>
<tr><td><label>Monitoring</label></td><td><select id=\"KMonitor\" name=\"KMonitor\" style=\"width:300px\"  >".$optMonitor."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$judul."'></td></tr>
<tr><td colspan=\"2\">
<button onclick=\"zPreview('lmk_slave_monitoring_angkutan','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
<button onclick=\"zExcel(event,'lmk_slave_monitoring_angkutan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>

</table>
</fieldset>";

?>
