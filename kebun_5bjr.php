<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_5bjr.js'></script>


<?php


$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' ORDER BY kodeorganisasi";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}	
			
$opttt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "select distinct tahuntanam from ".$dbname.".setup_blok where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by tahuntanam desc ";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$opttt.="<option value=".$data['tahuntanam'].">".$data['tahuntanam']."</option>";
			}
			
/*$optJns="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";	
$optJns.="<option value=''*/

$optJns="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "select distinct jenisbibit from ".$dbname.".setup_blok where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' ";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optJns.="<option value=".$data['jenisbibit'].">".$data['jenisbibit']."</option>";
			}	
									
?>


<?php
OPEN_BOX('',"<b>".$_SESSION['lang']['bjr']."</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['kodeorganisasi']."</td> 
					<td>:</td>
					<td><select id=kodeorg style=\"width:200px;\" >".$optOrg."</select></td>
				</tr>
				<tr><td width=100>Tahun Produksi</td><td>:</td></td><td><input type=text id=thnproduksi size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:200px;\"></td></tr>
				
				<tr><td>".$_SESSION['lang']['thntnm']."</td><td>:</td></td><td><select id=thntanam style=\"width:200px;\" >".$opttt."</select></td></tr>
				
				<tr><td>".$_SESSION['lang']['bjr']."<td>:</td></td><td><input type=text id=bjr size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:200px;\"> </td></tr>
				
				<tr>
					<td>Jenis Bibit</td> 
					<td>:</td>
					<td><select id=jenisbbt style=\"width:200px;\" >".$optJns."</select></td>
				</tr>
				<tr><td></td></tr>
				<tr><td></td></tr>
				<tr><td></td></tr>
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpanbjr()>Simpan</button>
						<button class=mybutton onclick=cancelbjr()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldkodeorg value='insert'>
					<input type=hidden id=oldthntnm value='insert'>
					<input type=hidden id=oldthnproduksi value='insert'>";


echo"
<fieldset style='float:left;'><legend>Filter data tersimpan</legend>
<table>
		<tr>
			<td>Kode Organisasi</td><td>: <select id='kdorg' style='width:150px;' onchange='ubah_list()'>".$optkdorg."</select></td>
		</tr>
		<tr>
			<td>Tahun Produksi</td><td>: <select id='thnprod' style='width:150px;' onchange='ubah_list()'>".$optgetthnprod."</select></td>
		</tr>
		<tr>
			<td>Tahun Tanam</td><td>: <select id='thntnm' style='width:150px;' onchange='ubah_list()'>".$optthntnm."</select></td>
		</tr>
	</table>
</fieldset>
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
echo"


	<div id=container style='width:750px;height:400px;overflow:scroll'>
		<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center style='width:25px;'>No</td>
			 <td align=center style='width:250px;'>".$_SESSION['lang']['kodeorganisasi']." </td>
			 <td align=center style='width:100px;'>Tahun Produksi</td>
			 <td align=center>".$_SESSION['lang']['thntnm']."</td>
			 <td align=center style='width:50px;'>".$_SESSION['lang']['bjr']."</td>
			 <td align=center style='width:80px;'>Jenis Bibit</td>
			 <td align=center style='width:20px;'>Edit</td></tr>
		 </thead>
		 <tbody id='containerData'><script>loadData()</script>
		 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>
	</div>";
echo close_theme();
echo "</fieldset>";
CLOSE_BOX();
echo close_body();					
?>