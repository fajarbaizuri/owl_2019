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
		document.getElementById('produk').value='0';	
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
			
$optbulan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct(substr(tanggal,1,7)) as tanggal FROM ".$dbname.".pabrik_ops_qcdt group by tanggal";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optbulan.="<option value=".$data['tanggal'].">".$data['tanggal']."</option>";
			}			
			

$optproduk="<option value='0'>Pilih Produk</option>";	
$optproduk.="<option value='CPO'>Crude Palm Oil (CPO)</option>";			
$optproduk.="<option value='PKO'>Palm Kernel Oil (PKO)</option>";			
?>






<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##bulan##produk";	

echo "<fieldset style='float:left;'><legend><b>Laporan QC</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:155px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>Periode</td>
		<td>:</td>
		<td><select id=bulan style='width:155px;'>".$optbulan."</select></td>
	</tr>	
	<tr>
		<td>Produk</td>
		<td>:</td>
		<td><select id=produk style='width:155px;'>".$optproduk."</select></td>
	</tr>
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('pabrik_slave_2qc','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'pabrik_slave_2qc.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2qc','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>