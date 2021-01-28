<!--ind-->

<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pabrik_5hargatbs.js'></script>



<?php
include('master_mainMenu.php');			
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PABRIK' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}	

$optsup="<option value='INTERNAL'>Internal dan Affiliasi</option>";
$ha="SELECT namasupplier,`supplierid`,`kodetimbangan` FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' order by namasupplier asc";
$hi=mysql_query($ha) or die (mysql_error());
while ($hu=mysql_fetch_assoc($hi))
{
	$optsup.="<option value=".$hu['supplierid'].">".$hu['namasupplier']."</option>";
}

$optbjr="<option value=1>3 - 5</option>";
$optbjr.="<option value=2>5 - 7</option>";		
$optbjr.="<option value=3>> 7</option>";		
$optbjr.="<option value=4>< 5</option>";		
$optbjr.="<option value=5>5-8</option>";		
$optbjr.="<option value=6>> 8</option>";		

$optkg="<option value=1>> 0</option>";
$optkg.="<option value=2>< 5000 KG</option>";		
$optkg.="<option value=3>>= 5000 KG</option>";		

?>


<?php
OPEN_BOX('',"<b>Harga TBS</b>");//optsup

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['supplier']."</td>
					<td>:</td>
					<td><select id=supplier style=\"width:125px;\">".$optsup."</select></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['tanggal']."</td>
					<td>:</td>
					<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=width:125px; /></td>
				<tr>
				
				<tr>
					<td>Pabrik</td>
					<td>:</td>
					<td><select id=kdorg style=\"width:125px;\" >".$optOrg."</select></td>
				</tr>
				
				<tr>
					<td>Klarisifikasi BJR</td>
					<td>:</td>
					<td><select id=bjr style=\"width:125px;\" >".$optbjr."</select></td>
				</tr>
				
				<tr>
					<td>Klarisifikasi KG</td>
					<td>:</td>
					<td><select id=kg style=\"width:125px;\" >".$optkg."</select></td>
				</tr>
				
				<tr>
					<td width=100>Harga</td>
					<td>:</td>
					<td><input type=text id=hr size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
				</tr>
				
				
				<tr>
					<td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=hapus()>Batal</button></td>
				</tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldtgl value='insert'>
					<input type=hidden id=oldbjr value='insert'>
					<input type=hidden id=oldkdorg value='insert'>";
					
	echo"<fieldset style='float:left;'>
			<legend>Sort</legend> 
				<table border=0 cellpadding=1 cellspacing=1>
					<tr>
						<td>Pabrik</td>
						<td>: <select id='kdorgsort' style='width:150px;' onchange='ubah_list()'>".$optorgsort."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['periode']."</td>
						<td>: <select id='periodesort' style='width:150px;' onchange='ubah_list()'>".$optpersort."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['supplier']."</td>
						<td>: <select id='suppsort' style='width:150px;' onchange='ubah_list()'>".$optsupsort."</select></td>
					</tr>
					
				</table></fieldset>";
					
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
			 <td align=center>Supplier</td>
			 <td align=center>Tanggal</td>
			 <td align=center>Kode Organisasi</td>
			 <td align=center>BJR</td>
			 <td align=center style='width:120px'>KG</td>
			 <td align=center>Harga Satuan</td>
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