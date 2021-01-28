<?
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$arr="##unit##periode";  

$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' 
    and tipe in ('kebun', 'pabrik')
    order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optunit.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$sOrg="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optperiode.="<option value=".$rOrg['periode'].">".$rOrg['periode']."</option>";
}

echo"
<table cellspacing=\"1\" border=\"0\" >
    <tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id='unit' style=\"width:200px;\">".$optunit."</select></td></tr>
    <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:200px;\">".$optperiode."</select></td></tr>
    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
    <tr><td colspan=\"2\">
    <button onclick=\"zPdf('lbm_slave_peta_kebun','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>
<!--    <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>--></td></tr>
</table>
";
?>