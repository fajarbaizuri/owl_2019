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
		document.getElementById('tahun').value='';	
		document.getElementById('printContainer').innerHTML='';	
	}
</script>

<?
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)=4 ORDER BY kodeorganisasi asc";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
			
$opttahun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct tahun FROM ".$dbname.".sdm_5plafon group by tahun";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$opttahun.="<option value=".$data['tahun'].">".$data['tahun']."</option>";
			}			
			
?>


<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##tahun";	

echo "<fieldset style='float:left;'><legend><b>Laporan Monitoring Plafon Pengobatan</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:155px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>Tahun</td>
		<td>:</td>
		<td><select id=tahun style='width:155px;'>".$opttahun."</select></td>
	</tr>	
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('sdm_slave_2plafon','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'sdm_slave_2plafon.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2qc','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>