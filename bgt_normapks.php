<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/bgt_normapks.js'></script>


<?php
include('master_mainMenu.php');

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='STATION' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' ORDER BY kodeorganisasi";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}						
?>


<?php
OPEN_BOX('',"<b>Norma PKS</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				<tr><td>Station<td>:</td></td><td><select id=st style=\"width:150px;\" >".$optOrg."</select></td></tr>
				<tr><td>Jam Setahun
</td><td>:</td></td><td><input type=text id=js size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=10 style=\"width:150px;\"></td></tr>
				<tr><td>Jumlah HK SKU
</td><td>:</td></td><td><input type=text id=jhs size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=10 style=\"width:150px;\"></td></tr>
				<tr><td>Jumlah HK BHL
</td><td>:</td></td><td><input type=text id=jhb size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=10 style=\"width:150px;\"></td></tr>
				
			
			
			<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=batal()>Hapus</button></td></tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldst value='insert'>
					";

CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
//echo "<div id=container>";
echo" <div id=container style='width:750px;height:400px;overflow:scroll'>";		
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center style='width:25px;'>No</td>
			 <td align=center style='width:200px;'>Station</td>
			 <td>Jam Setahun</td>
			 <td>Jumlah HK SKU</td>
			 <td>Jumlah HK BHL</td>
			 <td align=center style='width:20px;'>Edit</td></tr>
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