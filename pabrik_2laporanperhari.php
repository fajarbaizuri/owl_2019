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


<?
//print_r($_SESSION['empl']);
$optnmorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
//$sortorga=$_SESSION['empl']['tipelokasitugas'];echo $sortorga;
		//
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE tipe='PABRIK'";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}						
?>


<?
include('master_mainMenu.php');
OPEN_BOX();
//$arr="##kdorg##noakun##bk1##bk2";
//$arr="##bk1##bk2##tgl1##tgl2##replan##pmks##sisasaldo##giro##kdorg";
$arr="##kdorg##tgl";



echo "<fieldset style='float:left;'><legend><b>Laporan Penerimaan dan Pengeluaran</b></legend>
<table>
	<tr>
		<td>Organisasi</td>
		<td>:</td>	
		<td><select id=kdorg style='width:155px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' ></td>
	</tr>

	<tr><td colspan=2></td>
		<td colspan=4>
		<button onclick=zPreview('pabrik_slave_2laporanperhari','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'pabrik_slave_2laporanperhari.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		
		<button onclick=batalkeluarterima() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('keu_slave_2rencanabayar','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>


echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>