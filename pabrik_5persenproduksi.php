<!--ind-->

<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pabrik_5persenproduksi.js'></script>



<?php
include('master_mainMenu.php');			

$opttipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$opttipe.="<option value=kernel>Kernel</option>";
$opttipe.="<option value=cpo>CPO</option>";

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PABRIK' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}	
					
/*$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}	*/
			
?>


<?php
OPEN_BOX('',"<b>Persen Produksi</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				
				<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td>:</td><td><select id=kdorg style=\"width:125px;\" >".$optOrg."</select></td></tr>
				<tr>
				<td>Tipe</td><td>:</td><td><select id=tipe style=\"width:125px;\" >".$opttipe."</select></td></tr>
				
				<tr><td>Persen</td><td>:</td><td><input type=text id=persen size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"> %</td></tr>
				
				
				
				<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
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
			 <td align=center>Kode Organisasi</td>
			 <td align=center>Tipe</td>
			 <td align=center>Persen</td>
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