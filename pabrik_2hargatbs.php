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
		document.getElementById('kdsup').value='';
		document.getElementById('tgl2').value='';	
		document.getElementById('tgl1').value='';
		document.getElementById('printContainer').innerHTML='';	
	}


</script>

<?

$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
  $optsup.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}


$sql="SELECT namasupplier,`supplierid` FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' order by namasupplier asc";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optsup.="<option value=".$data['supplierid'].">".$data['namasupplier']."</option>";
			}
			
?>


<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdsup##tgl1##tgl2##kdkg";	

echo "<fieldset style='float:left;'><legend><b>Laporan Harga TBS</b></legend>
<table>
	<tr>
		<td>Suplier</td>
		<td>:</td>
		<td><select id=kdsup style='width:155px;'>".$optsup."</select></td>
	</tr>
	<tr>
		<td>Kg</td>
		<td>:</td>
		<td>
		<select id=kdkg style='width:155px;'>
		<option value=\"NO\" >Dalam Normal</option>
		<option value=\"NT\" >Dalam Netto</option>
		</select>
		</td>
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
		<button onclick=zPreview('pabrik_slave_2hargatbs','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'pabrik_slave_2hargatbs.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2hargatbs','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>