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
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)=4 ORDER BY kodeorganisasi";
else $sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
			
			
			
//print_r($_SESSION['empl']);		
			
/*$optbulan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sql="SELECT distinct(substr(tanggal,1,7)) as tanggal FROM ".$dbname.".pabrik_ops_qcdt group by tanggal";
$sql="select periode from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optbulan.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}*/		

$optbulan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sql="SELECT distinct(substr(tanggal,1,7)) as tanggal FROM ".$dbname.".pabrik_ops_qcdt group by tanggal";
$sql="select distinct substr(tanggal,1,7) as periode from ".$dbname.".keu_jurnaldt
      order by periode desc";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optbulan.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}	
			
?>



<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##bulan";	

echo "<fieldset style='float:left;'><legend><b>Pengeluaran Barang dan Transaksi</b></legend>
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
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('log_slave_2pengeluaranbarang','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zPdf('log_slave_2pengeluaranbarang','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>
		<button onclick=zExcel(event,'log_slave_2pengeluaranbarang.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>