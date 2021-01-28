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
		document.getElementById('bulan').value='';	
		document.getElementById('printContainer').innerHTML='';	
	}
</script>

<?
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where induk='FHO' ORDER BY kodeorganisasi";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
			
$optbulan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct(substr(tanggal,1,7)) as tanggal FROM ".$dbname.".log_poht group by tanggal";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optbulan.="<option value=".$data['tanggal'].">".$data['tanggal']."</option>";
			}			
			
?>






<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##tgl1##tgl2";	

echo "<fieldset style='float:left;'><legend><b>Laporan PO</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:200px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl1' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >
		s/d
		<input type='text' class='myinputtext' id='tgl2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' ></td>
	</tr>	
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('log_slave_2po','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'log_slave_2po.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2qc','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer'  >
</div></fieldset>";//style='overflow:auto;height:350px;max-width:1500px';

CLOSE_BOX();
echo close_body();




?>