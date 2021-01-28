<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>Kondisi lapangan TB</b>");
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
<script language="javascript" src="js/kebun_qc_kondTB.js"></script>
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
$sORg="select distinct  kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where substr(induk,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING'";
$qOrg=mysql_query($sORg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";	
}
$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_kondisitbht where afdeling like '".$_SESSION['empl']['lokasitugas']."%'";
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
			echo $_SESSION['lang']['tanggal'].":<select id=tgl_cari onchange=cariData()>".$optPeriode."</select>";
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
<td align=center>No.</td>
<td align=center>".$_SESSION['lang']['blok']."</td>
<td align=center>".$_SESSION['lang']['tanggal']."</td>
<td align=center>".$_SESSION['lang']['kodeorg']."</td>
<td align=center>Chipping</td>
<td align=center>Stacking</td>
<td align=center>Teras</td>
<td align=center>Kacangan</td>
<td align=center>Action</td>
</tr>
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
<td>".$_SESSION['lang']['afdeling']."</td>
<td>:</td>
<td>
<select id=\"kodeOrg\" name=\"kodeOrg\" style=\"width:120px;\">".$optOrg2."</select>
</td>
<td>&nbsp;</td>
<td>Petugas</td>
<td>:</td>
<td>
<select id=\"idPetugas\" name=\"idPetugas\" style=\"width:120px;\">".$optMandor."</select>
</td>
</tr>
<tr>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>:</td>
<td><input type=\"text\" class=\"myinputtext\" id=\"tgl_ganti\" name=\"tgl_ganti\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  size=\"10\" maxlength=\"10\" style=\"width:120px;\" /></td>
<td>&nbsp;</td>
<td>".$_SESSION['lang']['asisten']."</td>
<td>:</td>
<td>
<select id=\"idAsisten\" name=\"idAsisten\" style=\"width:120px;\">".$optStaff."</select>
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
<td align=center>".$_SESSION['lang']['kodeorg']."</td>
<td align=center>Chipping</td>
<td align=center>Stacking</td>
<td align=center>Teras</td>
<td align=center>Kacangan</td>
<td align=center>Action</td>
</tr>
</thead>";
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
echo"
<tbody>";
echo"<tr class=rowcontent>";
echo"<td><select id=kdBlok style=\"width:220px;\">".$optKary."</select></td>";
echo"<td><input type=text id=piringan name=piringan onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:250px;\" /></td>";
echo"<td><input type=text id=gwngan name=gwngan onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:250px;\" /></td>";
echo"<td><input type=text id=pmupukn name=pmupukn onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:250px;\" /></td>";
echo"<td><input type=text id=hpt name=hpt onkeypress='return angka_doang(event)' class=myinputtextnumber  style=\"width:200px;\" /></td>";

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
<td align=center>".$_SESSION['lang']['kodeorg']."</td>
<td align=center>Chipping</td>
<td align=center>Stacking</td>
<td align=center>Teras</td>
<td align=center>Kacangan</td>
<td align=center>Action</td>
</tr>
</thead><tbody id=detailContent>";

echo"</tbody></table>";
echo"</fieldset>";
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>