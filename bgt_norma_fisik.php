<!--ind-->
<?//@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

echo open_body();
?>
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/bgt_norma_fisik.js'></script>
<?php

include('master_mainMenu.php');	
	
$optjenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optjenis.="<option value=pupuk>Pupuk</option>";
$optjenis.="<option value=bibit>Bibit</option>";

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi,tipe FROM ".$dbname.".organisasi where (tipe='afdeling' or tipe='bibitan') and induk='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
$jenisOrg = array();
while ($data=mysql_fetch_assoc($qry))
{
	$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
	$jenisOrg[$data['kodeorganisasi']] = $data['tipe'];
}

$optbarang='';

OPEN_BOX('',"<b>Norma Fisik</b>");

echo "<param id='jenisOrg' value='".json_encode($jenisOrg)."'>";

echo"<br /><br /><fieldset style='float:left;'><legend>".$_SESSION['lang']['entryForm']."</legend>
		<table border=0 cellpadding=1 cellspacing=1>
			<tr>
				<td>".$_SESSION['lang']['budgetyear']."</td>
				<td>:</td>
				<td><input type=text id=thnbudget size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:150px;\"></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['kodeorganisasi']."</td>
				<td>:</td>
				<td><select id=kdorg style=\"width:150px;\" onchange='setPupukBibit()' >".$optOrg."</select></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jenis']."</td>
				<td>:</td>
				<td><select id=jenis disabled style=\"width:150px;\" >".$optjenis."</select></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['thntnm']."</td>
				<td>:</td>
				<td><input type=text id=thntnm size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=4 style=\"width:150px;\"></td>
			</tr>
			<tr>	
				<td>Barang / Supplier</td>
				<td>:</td>
				<td><select id=barang style=width:150px;>".$optbarang."</select></td>
			</tr>
			<tr>
				<td>Norma</td>
				<td>:</td>
				<td><input type=text id=norma size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:150px;\"> </td>
			</tr>
			<tr><td></td><td></td>
				<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
				<button class=mybutton onclick=hapus()>Hapus</button></td></tr>
		</table></fieldset>
		<input type=hidden id=method value='insert'>";

$optthnttp=$optorgclose='';
echo"<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>
    <div id=closetab><table>
		<tr>
			<td>".$_SESSION['lang']['budgetyear']."</td>
			<td>:</td>
			<td><select id=thnttp style='widht:150px'>".$optthnttp."</select></td>
		</tr>
		<tr>
			<td>".$_SESSION['lang']['kodeorganisasi']."</td>
			<td>:</td>
			<td><select id=lkstgs style='widht:150px'>".$optorgclose."</select></td>
		</tr>";
echo"<tr><td></td><td></td><td><br /><button class=\"mybutton\"  id=\"saveData\" onclick='tutup()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
echo"</fieldset>";
CLOSE_BOX();

OPEN_BOX();

$optTahunBudgetHeader=$optkdorgHeader='';

//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
echo"<table>
		<tr>
			<td>".$_SESSION['lang']['budgetyear']." : <select id='thnbudgetHeader' style='width:150px;' onchange='ubah_list()'>".$optTahunBudgetHeader."</select></td>
			<td>".$_SESSION['lang']['organisasi']." : <select id='kdorgHeader' style='width:150px;' onchange='ubah_list()'>".$optkdorgHeader."</select></td>
		</tr></table>";

echo" <div id=container style='width:500px;height:400px;overflow:scroll'>";	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center>No</td>
			 <td align=center>Tahun Budget</td>
			 <td align=center>Afdeling</td>
			 <td align=center>Jenis</td>
			 <td align=center>Tahun Tanam</td>
			 <td align=center>Barang / Supplier</td>
			 <td align=center>Norma</td>
			 <td align=center>".$_SESSION['lang']['action']."</td></tr>
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