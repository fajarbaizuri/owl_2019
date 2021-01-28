<?//@Copy nangkoelframework
//--ind--
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
include_once('lib/zLib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/kebun_restan_v1.js'></script>

<?php
include('master_mainMenu.php');

$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

			
$optKdorg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg3="select kodeorg from ".$dbname.".setup_blok where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
 //echo $sOrg2;
$qOrg3=mysql_query($sOrg3) or die(mysql_error());
while($rOrg3=mysql_fetch_assoc($qOrg3))
{
	$optKdorg2.="<option value=".$rOrg3['kodeorg'].">".$optNmOrg[$rOrg3['kodeorg']]."</option>";
}			
				
?>


<?php
OPEN_BOX();

echo"<fieldset style='float:left;'>
		<legend><b>".$_SESSION['lang']['entryForm']." Restan</b></legend> 
			<table border=0 cellpadding=1 cellspacing=1>	
				<tr>
					<td>Periode</td>
					<td>:</td>
					<td><input type=text id=per size=10 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=7 style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>Blok</td>
					<td>:</td>
					<td><select id=kodeorg style=\"width:150px;\" >".$optKdorg2."</select></td>
				</tr>
				
				<tr>
					<td>Jumlah Janjang Restan</td>
					<td>:</td>
					<td><input type=text id=jjg onkeypress=\"return angka_doang(event);\" class=myinputtext style=\"width:150px;\"></td>
				</tr>
				
				<tr><td>Catatan<td>:</td></td><td><input type=text id=cat size=10 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=\"width:150px;\"> </td></tr>
				
				<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=cancel()>Hapus</button></td></tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldkodeorg value='insert'>";



CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
//echo "<div id=container>";
echo"<table><tr>";
echo"
	<td>Periode</td>
	<td>: <select id='thnbudgetHeader' style='width:150px;' onchange='ubah_list()'>".$optTahunBudgetHeader."</select></td>

	<td>Blok</td>
	<td>: <select id='blokheader' style='width:150px;' onchange='ubah_list()'>".$optblokheader."</select></td>


</tr>";
echo"</tr></table>";


echo" <div id=container style='width:750px;height:400px;overflow:scroll'>";		
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center>No</td>
			 <td align=center>Periode</td>
			 <td align=center>Blok</td>
			 <td align=center>Jumlah Janjang Restan</td>
			 <td align=center>Catatan</td>
			 <td align=center>*</td></tr>
		 </thead>
		 <tbody id='containerData'><script>loadData()</script>";

	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
echo "</fieldset>";
CLOSE_BOX();
echo close_body();					
?>