<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>Kegiatan Kendaraan dan Alat Berat</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript">

nmTmblPilih='<?php echo $_SESSION['lang']['pilihdata']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
nmTmblSave='<?php echo $_SESSION['lang']['save']?>';
nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
optIsi='<?php 
 			 $kodeOrg=substr($_SESSION['temp']['nSpb'],8,6);	 
 			 $sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where `induk`='".$kodeOrg."' and tipe='BLOK' ORDER BY `namaorganisasi` ASC";
			 //echo $sql;
 			 $query=mysql_query($sql) or die(mysql_error());
			 $optBlok="<option value=></option>";
			 while($res=mysql_fetch_assoc($query))
			 {
			 	$optBlok.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
			 }
			 echo $optBlok;?>';
</script>
<script language="javascript" src="js/vhc_prestasi_ian.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />

<?php // print"<pre>"; print_r($_SESSION); print"</pre>"; ?>
<div id="action_list">
<?php
$optOrg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optOrg2;
if ($_SESSION['empl']['lokasitugas']=='TKFB'){
	$sORg="select distinct  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where  tipe in ('kebun','pabrik')";
}else{
	$sORg="select distinct  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where  tipe in ('kebun','pabrik') and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
$qOrg=mysql_query($sORg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";	
}

if ($_SESSION['empl']['lokasitugas']=='TKFB'){
	$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_kendaraanht";
}else{
	$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_kendaraanht where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
}


$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}
echo"<table cellspacing=1 border=0 align=center>
     <tr valign=moiddle>
		<td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
			<img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."
		</td>
		<td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
			<img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."
		</td>
		<td>
			<fieldset><legend>".$_SESSION['lang']['find']."</legend>
			".$_SESSION['lang']['kodeorg'].":&nbsp;
				<select id=kodeOrgCari style=width:150px; onchange=cariData()>".$optOrg2."</select>&nbsp;&nbsp;
			".$_SESSION['lang']['periode'].":
				<select id=tgl_cari onchange=cariData()>".$optPeriode."</select>
			</fieldset>
		</td>
	 </tr>
	 </table> "; 

$optStaff="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTph=$optMandor=$optStaff;
$sStaff="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
         where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan=0";
$qStaff=mysql_query($sStaff) or die(mysql_error());
while($rStaff=mysql_fetch_assoc($qStaff))
{
    $optStaff.="<option value='".$rStaff['karyawanid']."'>".$rStaff['namakaryawan']."</option>";
}

		if ($_SESSION['empl']['lokasitugas'] != "TKFB"){
			$whereKary = "and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
		}else{
			$whereKary = "";
		}
		
		$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00')";
		$whereKary .= " and subbagian<>''";
		$sStaff2 = "select karyawanid,namakaryawan,subbagian,lokasitugas from ".$dbname.".datakaryawan  where kodejabatan in ('157','174')  ".$whereKary;
		

$qStaff2=mysql_query($sStaff2) or die(mysql_error());
while($rStaff2=mysql_fetch_assoc($qStaff2))
{
    $optMandor.="<option value=".$rStaff2['karyawanid'].">".$rStaff2['namakaryawan']." - ".$rStaff2['subbagian']." (".$rStaff2['lokasitugas'].")</option>";
}

?>
</div>
<?php
CLOSE_BOX();
?>

<div id="listSpb">
<?php OPEN_BOX();
//------------------------LIST HEADER
echo"
<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>
<table cellspacing=\"1\" border=\"0\"  width=100%>
<thead>
	<tr class=\"rowheader\">
		<td align=center>Nomor</td>
		<td align=center>Tanggal</td>
		<td align=center>Kelompok</td>
		<td align=center>Plat No.</td>
		<td align=center>Kondisi</td>
		<td align=center>Mandor Transport</td>
		<td align=center>Sopir/Operator</td>
		<td align=center colspan=4>Aksi</td>
	</tr>
</thead>";
echo"
<tbody id=\"contain\">
<script>loadData()</script>
</tbody>
</table>
</fieldset>";
//------------------------LIST HEADER
 CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
//------------------------INPUT HEADER
OPEN_BOX();

echo "<fieldset style=\"float:left\"><legend id=\"title_Form\"><b>Tambah Header</b></legend><div id=\"Tambah Header\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"1\"><tbody>
<tr>
	<td style=\"padding-right:20px;font-size:12px\">No Transaksi</td>
	<td style=\"padding-right:20px;font-size:12px\"><input id=\"notransaksi\" name=\"notransaksi\" class=\"myinputtext\" type=\"text\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:150px\" disabled=\"disabled\"></td>
	<td style=\"padding-right:20px;font-size:12px\">Mandor Transport</td>
	<td style=\"padding-right:20px;font-size:12px\"><select id=\"nikmandor\" name=\"nikmandor\" style=\"width:150px\">".$optMandor."</select></td>
</tr>
<tr>
	<td style=\"padding-right:20px;font-size:12px\">Tanggal</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" type=\"text\" onkeypress=\"return tanpa_kutip(event)\" value=\"\" style=\"width:150px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" =\"\"=\"\"></td><td style=\"padding-right:20px;font-size:12px\">Sopir/Operator
	</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<select id=\"sopir\" name=\"sopir\" style=\"width:150px\"><option value=\"\" selected>Pilih Data</option></select>
	</td>
</tr>
<tr>
	<td style=\"padding-right:20px;font-size:12px\">Kelompok</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<select id=\"kelompok\" name=\"kelompok\" style=\"width:150px\" onchange=\"getPlat(this);\" >
			<option value=\"\" selected>Pilih Data</option>
			<option value=\"AB\">Alat Berat</option>
			<option value=\"KD\">Kendaraan</option>
		</select>
	</td>
	<td style=\"padding-right:20px;font-size:12px\">Penggunaan BBM</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<input id=\"bbm\" name=\"bbm\" class=\"myinputtext\" type=\"text\" onkeypress=\"return angka_doang(event)\" value=\"0\" style=\"width:150px\" maxlength=\"21\">
	</td>
</tr>
<tr>
	<td style=\"padding-right:20px;font-size:12px\">Plat No</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<select id=\"plat\" name=\"plat\" style=\"width:150px\" onchange=\"getDriv(this)\" >
			<option value=\"\" selected>Pilih Data</option>
		</select>
	</td>
	<td style=\"padding-right:20px;font-size:12px\">No. Service</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<input id=\"noservice\" name=\"noservice\" class=\"myinputtext\" type=\"text\" onkeypress=\"return tanpa_kutip(event)\" value=\"\" style=\"width:150px\" maxlength=\"21\" disabled=\"disabled\">
	</td>
	
</tr>
<tr>
	<td style=\"padding-right:20px;font-size:12px\">Kondisi</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<select id=\"kondisi\" name=\"kondisi\" style=\"width:150px\" onchange=\"getValda(this);\">
			<option value=\"B\">Beroperasi</option>
			<option value=\"S\">Standby</option>
		</select>
	</td>
	<td style=\"padding-right:20px;font-size:12px\">Keterangan</td>
	<td style=\"padding-right:20px;font-size:12px\">
		<input id=\"keterangan\" name=\"keterangan\" class=\"myinputtext\" type=\"text\" onkeypress=\"return tanpa_kutip(event)\" value=\"\" style=\"width:150px\" maxlength=\"100\" disabled=\"disabled\">
	</td>
</tr>
<tr>
	<td colspan=\"4\">
		<button id=\"save_kepala\" name=\"save_kepala\" class=\"mybutton\" onclick=\"save_header()\">Simpan</button>
	</td>
</tr>
</tbody></table></div></fieldset>";
/*
echo"
<fieldset style=width:550px;>
	<legend>".$_SESSION['lang']['addheader']."</legend>
	<table cellspacing=\"1\" border=\"0\" cellpadding=1>
		<tr>
			<td>".$_SESSION['lang']['afdeling']."</td>
			<td>:</td>
			<td><select id=\"kodeOrg\" name=\"kodeOrg\" style=\"width:120px;\">".$optOrg2."</select></td>
			<td>&nbsp;</td>
			<td>Petugas</td>
			<td>:</td>
			<td><select id=\"idPetugas\" name=\"idPetugas\" style=\"width:120px;\">".$optMandor."</select></td>
		</tr>
		<tr>
			<td>".$_SESSION['lang']['tanggal']."</td>
			<td>:</td>
			<td><input type=\"text\" class=\"myinputtext\" id=\"tgl_ganti\" name=\"tgl_ganti\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:120px;\" /></td>
			<td>&nbsp;</td>
			<td>".$_SESSION['lang']['asisten']."</td>
			<td>:</td>
			<td><select id=\"idAsisten\" name=\"idAsisten\" style=\"width:120px;\">".$optStaff."</select></td>
		</tr>
		<tr>
			<td colspan=\"3\" id=\"tmbLheader\">
				<button class=mybutton id=save_kepala name=save_kepala onclick=save_header()>".$_SESSION['lang']['save']."</button>
			</td>
		</tr>
	</table>
</fieldset>";
*/
//------------------------INPUT HEADER
CLOSE_BOX();
?>
</div>
<div id="detailSpb" style="display:none">
   
<?php 
//------------------------INPUT DETAIL
OPEN_BOX();
echo"
<fieldset>
<legend>".$_SESSION['lang']['detailPembelian']."</legend>
	<table cellspacing=\"1\" border=\"0\" class=\"sortable\" width=100%>
		<thead>
			<tr class=\"rowheader\">
				<td align=center>Waktu Operasional</td>
				<td align=center>Satuan</td>
				<td align=center>Awal</td>
				<td align=center>Akhir</td>
				<td align=center>Total</td>
				<td align=center>Lokasi</td>
				<td align=center>Kegiatan</td>
				<td align=center>Rit Ke-</td>
				<td align=center>Volume</td>
				<td align=center>Satuan</td>
				<td align=center>Action</td>
			</tr>
		</thead>";


$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
echo"<tbody>";
echo"<tr class=rowcontent>";
	echo"<td><select id=satuk style=\"width:135px;\" onchange=\"getSatWaktu(this.value)\"></select></td>";
	echo"<td><input type=text id=satuanwaktu name=satuanwaktu onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:45px;\" disabled=\"disabled\"/></td>";
	echo"<td><input type=text id=awal name=awal onkeypress='return angka_doang(event)' onchange='hitTot()' onkeyup='hitTot()' class=myinputtextnumber  style=\"width:45px;\" disabled=\"disabled\" /></td>";
	echo"<td><input type=text id=akhir name=akhir onkeypress='return angka_doang(event)' onchange='hitTot()' onkeyup='hitTot()' class=myinputtextnumber  style=\"width:45px;\" disabled=\"disabled\" /></td>";
	echo"<td><input type=text id=total name=total  class=myinputtextnumber  style=\"width:45px;\" disabled=\"disabled\" /></td>";
	
	echo"<td><select id=lokasi style=\"width:170px;\" ></select></td>";
	echo"<td><select id=kodekegiatan style=\"width:290px;\" onchange=\"getSatuan(this);\"></select></td>";
	echo"<td><input type=text id=rit name=rit onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:30px;\" disabled=\"disabled\"/></td>";
	echo"<td><input type=text id=volume name=volume onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:45px;\" /></td>";
	echo"<td><input type=text id=satuanvalome name=satuanvalome onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:45px;\" disabled=\"disabled\"/></td>";
	echo"<td align=center><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/></td>";
echo"</tr>";
echo"
</tbody>
</table>
</fieldset><input type=hidden id=proses_detail value='insert_detail' /><br /><br /><input type=hidden id=notrans />";

echo"<fieldset><legend>".$_SESSION['lang']['list']." ".$_SESSION['lang']['detailPembelian']."</legend>";
echo"<table cellspacing=\"1\" border=\"0\" class=\"sortable\" width=100%>
<thead>
<tr class=\"rowheader\">
				<td align=center>Waktu Operasional</td>
				<td align=center>Satuan</td>
				<td align=center>Awal</td>
				<td align=center>Akhir</td>
				<td align=center>Total</td>
				<td align=center>Lokasi</td>
				<td align=center>Kegiatan</td>
				<td align=center>Rit Ke-</td>
				<td align=center>Volume</td>
				<td align=center>Satuan</td>
				<td align=center>Action</td>
</tr>
</thead><tbody id=detailContent>";

echo"</tbody></table>";
echo"</fieldset>";
CLOSE_BOX();
//------------------------INPUT DETAIL
?>
</div>
<?php 
echo close_body();
?>