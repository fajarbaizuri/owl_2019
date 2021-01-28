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
<script language=javascript1.2 src='js/kebun_2restan_v1.js'></script>


<?



$optsup="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql="SELECT namasupplier,`kodetimbangan` FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan!='NULL' order by namasupplier asc";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optsup.="<option value=".$data['kodetimbangan'].">".$data['namasupplier']."</option>";
			}
			
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sThn = "SELECT distinct periode FROM ".$dbname.".kebun_restan_v1 where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by periode desc";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optPeriode.="<option value='".$rThn['periode']."'>".$rThn['periode']."</option>";
		}
		echo $optPeriode;			
			
?>


<?
include('master_mainMenu.php');
OPEN_BOX();
//$arr="##kdsup##tgl1##tgl2";	
$arr="##kdUnit##tanggal";
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
$optBlok=$optAfd;

$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optUnit.="<option value='".$_SESSION['empl']['lokasitugas']."'>".$optNm[$_SESSION['empl']['lokasitugas']]."</option>";


echo "<fieldset style='float:left;'><legend><b>Laporan Restan</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['unit']."</td>
		<td>:</td>
		<td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\" onchange='getAfd()'>".$optUnit."</select></td>
	</tr>
		
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input id='tanggal' class='myinputtext' type='text'
			onmousemove='setCalendar(this.id)' readonly='readonly' style='cursor:pointer' /></td>
	</tr>
	
	
	
	
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('kebun_slave_2restan_v1','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'kebun_slave_2restan_v1.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		<button onclick=zPdf('kebun_slave_2restan_v1','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>
		
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