<?//@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();


?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/keu_2rbkt.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>


<?
$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE tipe='PT'";
}
else
{
	$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
	//echo $sql;
}
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
$arr="##tgl1##tgl2##replan##pmks##sisasaldo##giro##kdorg##kdunit";

echo "<fieldset style='float:left;'><legend><b>Laporan Rencana Pembayaran</b></legend>
<table>
	<tr>
		<td>Organisasi</td>
		<td>:</td>
		<td><select id=kdorg style='width:155px;' onchange=getunit()>".$optorg."</select></td>
	</tr>
	<tr>
		<td>Unit</td>
		<td>:</td>
		<td><select id=kdunit style='width:155px;'>".$optunit."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl1' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >
		s/d
		<input type='text' class='myinputtext' id='tgl2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' ></td>
	</tr>
	
	<tr>
		<td>Jumlah Pencairan Pinjaman BRI KI Replanting & New Planting</td>
		<td width=10>:</td>
		<td><input type=text id=replan size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext  style=\"width:155px;\"></td>		
	</tr>
	
	<tr>
		<td>Jumlah Pencairan Pinjaman BRI KI PMKS</td>
		<td width=10>:</td>
		<td><input type=text id=pmks size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext  style=\"width:155px;\"></td>		
	</tr>
	<tr>
		<td>Sisa Saldo Pinjaman Kredit Investasi</td>
		<td width=10>:</td>
		<td><input type=text id=sisasaldo size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext  style=\"width:155px;\"></td>		
	</tr>
	
	<tr>
		<td>Saldo Rekening Giro BRI</td>
		<td width=10>:</td>
		<td><input type=text id=giro size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext  style=\"width:155px;\"></td>		
	</tr>
	
	<tr><td colspan=2></td>
		<td colspan=4>
		<button onclick=zPreview('keu_slave_2rencanabayar','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'keu_slave_2rencanabayar.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		
		<button onclick=batalrencanabayar() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
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