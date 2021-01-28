<!--ind-->
<?//@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

echo open_body();
?>

<script language=javascript1.2 src='js/bgt_vhc_kapasitas.js'></script>

<?php

include('master_mainMenu.php');	
		
$optjenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optjenis.="<option value=BUAH>BUAH</option>";
$optjenis.="<option value=PUPUK>PUPUK</option>";
$optjenis.="<option value=BIBIT>BIBIT</option>";

$optvhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodevhc FROM ".$dbname.".vhc_5master ORDER BY kodevhc";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optvhc.="<option value=".$data['kodevhc'].">".$data['kodevhc']."</option>";
			}

?>


<?php
OPEN_BOX('',"<b>Kapasitas VHC</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['kodevhc']."</td>
					<td>:</td>
					<td><select id=kdvhc style=\"width:150px;\" >".$optvhc."</select></td>
				</tr>
				<tr>
					<td>Material</td>
					<td>:</td>
					<td><select id=material style=\"width:150px;\" >".$optjenis."</select></td>
				</tr>
				<tr>
					<td>Kapasitas</td>
					<td>:</td>
					<td><input type=text id=kapasitas size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:150px;\"> </td>
				</tr>
				
				<tr><td></td><td></td>
					<td><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=hapus()>Hapus</button></td></tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>";
CLOSE_BOX();
?>



<?php
OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
//echo "<div id=container>";
echo" <div id=container style='width:500px;height:400px;overflow:scroll'>";	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center>No</td>
			 <td align=center>".$_SESSION['lang']['kodevhc']."</td>
			 <td align=center>Material</td>
			 <td align=center>Kapasitas</td>
			 <td align=center>Aksi</td></tr>
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