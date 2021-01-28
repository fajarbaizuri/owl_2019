<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/kebun_5nourutmandor.js'></script>


<?php
require_once('master_mainMenu.php');			

$optaktif="";
$optaktif.="<option value=0>Tidak Aktif</option>";
$optaktif.="<option value=1 selected>Aktif</option>";
$optorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');


$optnik="";
$sql = "SELECT namakaryawan,karyawanid,nik,subbagian FROM ".$dbname.".datakaryawan where ".
	"kodejabatan='37'  and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";//and subbagian<>''
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optnik.="<option value=".$data['karyawanid'].">".$data['namakaryawan']." - ".$data['nik']." - ".$data['subbagian']."</option>";
			//$optnik.="<option value=".(int)$data['karyawanid'].">".$data['namakaryawan']."</option>";
			}	


$optkar="";
$sql1 = "SELECT namakaryawan,karyawanid,nik,subbagian FROM ".$dbname.".datakaryawan where ".
	"tipekaryawan in('1','3','4') and kodejabatan<>'37' and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";//and subbagian<>''
$qry1 = mysql_query($sql1) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry1))
			{
			$optkar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']." - ".$data['nik']." - ".$data['subbagian']."</option>";
			}	
?>


<?php
OPEN_BOX('',"<b>Karyawan Kemandoran ".$_SESSION['empl']['lokasitugas']."</b>");

//<tr><td width=100>Nik Mandor<td width=10>:</td></td><td><input type=text id=nm size=10 class=myinputtext maxlength=50 style=\"width:200px;\"></td></tr>
//<tr><td width=100>Karyawan ID<td width=10>:</td></td><td><input type=text id=ki size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:200px;\"></td></tr>

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				
				<tr><td>Nama Mandor</td><td>:</td></td><td><select id=nm style=\"width:225px;\" >".$optnik."</select> Nama - Nik - lokasi tugas</td></tr>
				
				<tr><td width=100>No. Urut</td><td width=10>:</td></td><td><input type=text id=nu size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:25px;\"></td></tr>
				
				<tr><td>Nama Karyawan</td><td>:</td></td><td><select id=ki style=\"width:225px;\" >".$optkar."</select>Nama - Nik - lokasi tugas</td></tr>
				
				<tr><td>Status</td><td>:</td></td><td><select id=st style=\"width:125px;\" >".$optaktif."</select></td></tr>
				
				<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=hapus()>Hapus</button></td></tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldnm value='insert'>
					<input type=hidden id=oldnu value='insert'>
					<input type=hidden id=oldki value='insert'>";
CLOSE_BOX();
?>



<?php
OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
echo "<div id=container>";
	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center style='width:5px;'>No</td>
			 <td align=center style='width:250px;'>Nama Mandor</td>
			 <td align=center>No. Urut Karyawan</td>
			 <td align=center style='width:250px;'>Nama Karyawan</td>
			 <td align=center>Divisi</td>
			 <td align=center>Status Aktif</td>
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