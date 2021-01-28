<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/sdm_5plafon.js'></script>


<?php
include('master_mainMenu.php');

$optkar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT karyawanid,namakaryawan FROM ".$dbname.".datakaryawan where alokasi='1' and lokasitugas='".$_SESSION['empl']['lokasitugas']."' ORDER BY namakaryawan asc";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optkar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']."</option>";
			}	
		
				
?>


<?php
OPEN_BOX('',"<b>Plafon</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
			
				<tr><td width=100>Tahun<td width=10>:</td></td><td><input type=text id=tahun size=10 onkeypress=\"return angka_doang(event);\"  class=myinputtext maxlength=4 style=\"width:150px;\"></td></tr>
				
				<tr><td>Nama Karyawan</td><td>:</td><td><select id=nama style=\"width:150px;\" >".$optkar."</select></td></tr>

				<tr><td>Jumlah</td><td>:</td><td><input type=text id=jumlah size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext  style=\"width:150px;\"> </td></tr>
				
				<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=hapus()>Hapus</button></td></tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>";



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
			 <td align=center>No</td>
			 <td align=center>Tahun</td>
			 <td align=center>Nama</td>
			 <td align=center>Jumlah</td>
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