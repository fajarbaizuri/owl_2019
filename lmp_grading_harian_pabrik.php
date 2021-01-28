<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

$arr="##kdUnit##periode##judul##kdtbs";
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optUnit;

$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}

$optTBS="<option value='0'>".$_SESSION['lang']['all']."</option>";
$optTBS.="<option value='1'>External</option>";
$optTBS.="<option value='2'>Internal</option>";
$optTBS.="<option value='3'>Afiliasi</option>";

$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
echo"
<fieldset style=\"float: left;\">
<legend><b>".$_POST['judul']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >

<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>

<tr><td><label>TBS</label></td><td><select id=\"kdtbs\" name=\"kdtbs\" style=\"width:150px\"  onchange=getTBS(this)>".$optTBS."</select></td></tr>

<tr><td><label>".$_SESSION['lang']['unit']."/Supplier</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\"  >".$optUnit."</select></td></tr>

<tr height=\"20\"><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$judul."'></td></tr>
<tr><td colspan=\"2\">
<button onclick=\"zPreview('lmp_slave_grading_harian_pabrik','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
<button onclick=\"zExcel(event,'lmp_slave_grading_harian_pabrik.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>

</table>
</fieldset>";

?>
