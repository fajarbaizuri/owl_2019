<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>Pemeriksaan Ancak</b>");
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
<script language="javascript" src="js/kebun_qc_ancakPanen.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />

<?php // print"<pre>"; print_r($_SESSION); print"</pre>"; ?>
<div id="action_list">
<?php
//for($x=0;$x<=24;$x++)
//{
//	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
//	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
//}
$optOrg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optOrg2;
$sORg="select distinct  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where substr(induk,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipe='BLOK'";
$qOrg=mysql_query($sORg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";	
}
$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_ancakht where kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}
echo"<table cellspacing=1 border=0 align=center>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['kodeorg'].":<select id=kodeOrgCari style=width:150px; onchange=cariData()>".$optOrg2."</select>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<select id=tgl_cari  onchange=cariData()>".$optPeriode."</select>";
			//echo"<button class=mybutton onclick=cariData()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
	 
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

$sStaff2="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan 
         where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in (1,3)";
$qStaff2=mysql_query($sStaff2) or die(mysql_error());
while($rStaff2=mysql_fetch_assoc($qStaff2))
{
    $optMandor.="<option value='".$rStaff2['karyawanid']."'>".$rStaff2['namakaryawan']."</option>";
}

?>
</div>
<?php
CLOSE_BOX();
?>
<div id="listSpb">
<?php OPEN_BOX();

echo"
<fieldset>
<legend>".$_SESSION['lang']['list']."</legend>
<table cellspacing=\"1\" border=\"0\" class=\"sortable\" width=100%>
<thead>
<tr class=\"rowheader\">
<td rowspan=3 align=center>".$_SESSION['lang']['kodeorg']."</td>
<td rowspan=3 align=center>".$_SESSION['lang']['tanggal']."</td>
<td rowspan=3 align=center>No.Tph</td>
<td rowspan=3 align=center>Urut Pkk</td>
<td colspan=5 align=center>Brondolan Tinggal</td>
<td colspan=4 align=center>Buah Tinggal</td>
<td colspan=2  align=center rowspan=2>Tunasan lalai</td>
<td rowspan=3 align=center>Action</td>
</tr>
<tr>
<td rowspan=2 align=center>Pasar Pikul</td>
<td colspan=2 align=center>Piringan</td>
<td colspan=2 align=center>Luar Piringan</td>
<td colspan=2 align=center>Pokok</td>
<td colspan=2 align=center>Gaw/Piringan</td>
</tr><tr>";
for($ard=1;$ard<=5;$ard++)
{
    echo"<td align=center>Kiri</td><td align=center>Kanan</td>";
}
echo"</tr>
</thead>";
echo"
<tbody id=\"contain\">
<script>loadData()</script>
</tbody>
</table>
</fieldset>";
 CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();


echo"
<fieldset style=width:550px;>
<legend>".$_SESSION['lang']['addheader']."</legend>
<table cellspacing=\"1\" border=\"0\" cellpadding=1>
<tr>
<td>".$_SESSION['lang']['kodeorg']."</td>
<td>:</td>
<td>
<select id=\"kodeOrg\" name=\"kodeOrg\" style=\"width:120px;\" onchange=\"getTph(0,0,0,0)\">".$optOrg2."</select>
</td>
<td>&nbsp;</td>
<td>Petugas Sensus</td>
<td>:</td>
<td>
<select id=\"ptgSensus\" name=\"ptgSensus\" style=\"width:120px;\">".$optMandor."</select>
</td>
</tr>
<tr>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>:</td>
<td><input type=\"text\" class=\"myinputtext\" id=\"tgl_ganti\" name=\"tgl_ganti\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:120px;\" /></td>
<td>&nbsp;</td>
<td>".$_SESSION['lang']['notph']."</td>
<td>:</td>
<td>
<select id=\"noTph\" name=\"noTph\" style=\"width:120px;\">".$optTph."</select>
</td>
</tr>
<tr>
<td>".$_SESSION['lang']['asisten']."</td>
<td>:</td>
<td>
<select id=\"idAsisten\" name=\"idAsisten\" style=\"width:120px;\" >".$optStaff."</select>
</td>
<td>&nbsp;</td>
<td>Baris TPH</td>
<td>:</td>
<td>
<input type=text id=brsTph name=brsTph onkeypress='return tanpa_kutip(event)' class=myinputtext  style=\"width:120px;\" />
</td>
</tr>
<tr>
<td>".$_SESSION['lang']['nikmandor1']."</td>
<td>:</td>
<td>
<select id=\"idMandor\" name=\"idMandor\" style=\"width:120px;\" >".$optMandor."</select>
    </td>
    <td>&nbsp;</td>
<td>TTl.Buah TPH</td>
<td>:</td>
<td>
<input type=text id=buahTph name=buahTph onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:120px;\"  value=0 />JJG
</td>
</tr>

<tr>
<td>".$_SESSION['lang']['mandor']." ".$_SESSION['lang']['panen']." 1</td>
<td>:</td>
<td>
<select id=\"idMandor_1\" name=\"idMandor_1\" style=\"width:120px;\" >".$optMandor."</select></td>
     <td>&nbsp;</td>
<td>TTLl.Brondolan TPH</td>
<td>:</td>
<td>
<input type=text id=brndlanTph name=brndlanTph onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:120px;\"  value=0 />BTR
</td>
</tr>

<tr>
<td>".$_SESSION['lang']['mandor']." ".$_SESSION['lang']['panen']." 2</td>
<td>:</td>
<td>
<select id=\"idMandor_2\" name=\"idMandor_2\" style=\"width:120px;\" >".$optMandor."</select></td>
    <td>&nbsp;</td>
<td>Buah Tinggal</td>
<td>:</td>
<td>
<input type=text id=bhTinggal name=bhTinggal onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:120px;\"  value=0 />JJG
</td>
</tr>

<tr>
<td>".$_SESSION['lang']['mandor']." ".$_SESSION['lang']['panen']." 3</td>
<td>:</td>
<td>
<select id=\"idMandor_3\" name=\"idMandor_3\" style=\"width:120px;\" >".$optMandor."</select></td>
      <td>&nbsp;</td>
<td>Brondolan Tinggal</td>
<td>:</td>
<td>
<input type=text id=brndlTinggal name=brndlTinggal onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:120px;\" value=0 />BTR
</td>
</tr>

<tr>
<td colspan=\"3\" id=\"tmbLheader\">
<button class=mybutton id=save_kepala name=save_kepala onclick=save_header()>".$_SESSION['lang']['save']."</button>
</td>
</tr>
</table>
</fieldset>";

CLOSE_BOX();
?>
</div>
<div id="detailSpb" style="display:none">
   
<?php 
OPEN_BOX();
echo"
<fieldset>
<legend>".$_SESSION['lang']['detailPembelian']."</legend>
<table cellspacing=\"1\" border=\"0\" class=\"sortable\" width=100%>
<thead>
<tr class=\"rowheader\">
<td rowspan=3 align=center>Urut Pkk</td>
<td colspan=5 align=center>Brondolan Tinggal</td>
<td colspan=4 align=center>Buah Tinggal</td>
<td colspan=2  align=center rowspan=2>Tunasan lalai</td>
<td rowspan=3 align=center>Action</td>
</tr>
<tr>
<td rowspan=2 align=center>Pasar Pikul</td>
<td colspan=2 align=center>Piringan</td>
<td colspan=2 align=center>Luar Piringan</td>
<td colspan=2 align=center>Pokok</td>
<td colspan=2 align=center>Gaw/Piringan</td>
</tr><tr>";
for($ard=1;$ard<=5;$ard++)
{
    echo"<td align=center>Kiri</td><td align=center>Kanan</td>";
}
echo"</tr>
</thead>";
echo"
<tbody>";
echo"<tr class=rowcontent>";
echo"<td><input type=text id=urutPkk name=urutPkk onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:120px;\" /></td>";
echo"<td><input type=text id=psrPikul name=psrPikul onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:120px;\" /></td>";
for($ard=1;$ard<=5;$ard++)
{
    echo"<td align=center><input type=text id=kiri_".$ard." name=kiri_".$ard." onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:90px;\" /></td>";
    echo"<td align=center><input type=text id=kanan_".$ard." name=kanan_".$ard." onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:90px;\" /></td>";
}
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
<td rowspan=3 align=center>Urut Pkk</td>
<td colspan=5 align=center>Brondolan Tinggal</td>
<td colspan=4 align=center>Buah Tinggal</td>
<td colspan=2  align=center rowspan=2>Tunasan lalai</td>
<td rowspan=3 align=center>Action</td>
</tr>
<tr>
<td rowspan=2 align=center>Pasar Pikul</td>
<td colspan=2 align=center>Piringan</td>
<td colspan=2 align=center>Luar Piringan</td>
<td colspan=2 align=center>Pokok</td>
<td colspan=2 align=center>Gaw/Piringan</td>
</tr><tr>";
for($ard=1;$ard<=5;$ard++)
{
    echo"<td align=center>Kiri</td><td align=center>Kanan</td>";
}
echo"</tr>
</thead><tbody id=detailContent>";

echo"</tbody></table>";
echo"</fieldset>";
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>