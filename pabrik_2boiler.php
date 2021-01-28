<?//@Copy nangkoelframework
//-----------------ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();


?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<script language=javascript>
	function batal()
	{
		document.getElementById('kdorg').value='';
		document.getElementById('tgl').value='';	
		document.getElementById('printContainer').innerHTML='';	
	}
</script>

<?
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PABRIK' ORDER BY kodeorganisasi";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}	
?>






<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##tgl";	

echo "<fieldset style='float:left;'><legend><b>Laporan Harian Jam Oprasional Boilers</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:125px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;' style='width:125px;'  size='7' maxlength='10' >
	</td>
	</tr>	
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('pabrik_slave_2boiler','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'pabrik_slave_2boiler.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2boiler','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:400px;max-width:1250px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>