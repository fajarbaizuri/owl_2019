<?//@Copy nangkoelframework
//ind
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
		document.getElementById('nokon').value='';
		document.getElementById('eks').value='';
		document.getElementById('tgl1').value='';
		document.getElementById('tgl2').value='';
		document.getElementById('printContainer').innerHTML='';	
	}


</script>

<?
$optsup="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql = "SELECT nokontrak FROM ".$dbname.".pmn_kontrakjual";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optsup.="<option value=".$data['nokontrak'].">".$data['nokontrak']."</option>";
			}	
			
$opteks="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql = "SELECT supplierid,namasupplier FROM ".$dbname.".log_5supplier where supplierid like '%K%' ";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$opteks.="<option value=".$data['supplierid'].">".$data['namasupplier']."</option>";
			}	
			
?>


<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##nokon##eks##tgl1##tgl2";	

echo "<fieldset style='float:left;'><legend><b>Laporan Pengakuan Penjualan</b></legend>
<table>
	<tr>
		<td>No. Kontrak</td>
		<td>:</td>
		<td><select id=nokon style=\"width:125px;\" >".$optsup."</select></td>
	</tr>
	<tr>
		<td>Ekspeditor</td>
		<td>:</td>
		<td><select id=eks style=\"width:125px;\" >".$opteks."</select></td>
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
		<button onclick=zPreview('pmn_slave_2pengakuanpenjualan','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'pmn_slave_2pengakuanpenjualan.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		<button onclick=zPdf('pmn_slave_2pengakuanpenjualan','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('vhc_slave_2riwayat','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>