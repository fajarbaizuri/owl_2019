<!--ind-->
<?//@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

echo open_body();
?>

<script language=javascript1.2 src='js/bgt_rasiokegiatan.js'></script>

<?php

include('master_mainMenu.php');	
		

$optkeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodekegiatan,namakegiatan,kelompok,satuan FROM ".$dbname.".setup_kegiatan where kelompok in('TM','TBM','BBT','TB')  ORDER BY namakegiatan";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optkeg.="<option value=".$data['kodekegiatan'].">".$data['namakegiatan']." [ ".$data['kodekegiatan']." - ".$data['kelompok']." - ".$data['satuan']." ]</option>";
			}	

?>


<?php
OPEN_BOX('',"<b>Rasio Kegiatan</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				
				<tr><td>".$_SESSION['lang']['namakegiatan']."</td><td>:</td><td><select id=kdkeg style=\"width:125px;\" >".$optkeg."</select></td></tr>
				<tr><td width=100>Rasio</td><td>:</td><td><input type=text id=rasio size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\">Satuan/Ha</td></tr>
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
			 <td align=center>Kode Kegiatan</td>
			 <td align=center>Nama Kegiatan</td>
			 <td align=center>Kelompok Kegiatan</td>
			 <td align=center>Rasio</td>
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