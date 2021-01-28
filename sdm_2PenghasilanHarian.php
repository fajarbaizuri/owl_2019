<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
    where CHAR_LENGTH(kodeorganisasi)='4' and tipe='KEBUN' order by namaorganisasi asc";
	//$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') order by namaorganisasi asc ";	
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
}
else
{
	$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
    where CHAR_LENGTH(kodeorganisasi)='4' and tipe='KEBUN' and induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";
	
	//$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
}
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}

$optPendapatan="<option value='1'>Hk</option>";
$optPendapatan.="<option value='2'>Upah</option>";
$optPendapatan.="<option value='3'>Premi</option>";
$optPendapatan.="<option value='4'>Denda</option>";
$optPendapatan.="<option value='5'>Kontanan</option>";
//$sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".$lksiTugas."' and sistemgaji like '%Bulanan%'  order by namakaryawan asc";
//$qKry=mysql_query($sKry) or die(mysql_error());
//while($rKry=mysql_fetch_assoc($qKry))
//{
//	$optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
//}

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kdOrg##periode##pendapatan";

?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>

<div>
<fieldset style="float: left;">
<legend><b>Penghasilan Pekerja Harian</b></legend>
<table cellspacing="1" border="0" >
<tr>
	<td><label><?php echo $_SESSION['lang']['unit'];?></label></td>
	<td><select id="kdOrg" name="kdOrg" style="width:150px"><!--<option value="">
		<?php echo $_SESSION['lang']['all']?></option>--><?php echo $optOrg;?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['periode'];?></label></td>
	<td><select id="periode" name="periode" style="width:150px">
		<!--<option value=""></option>--><?php echo $optPeriode;?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo "Pendapatan"; ?></label></td>
	<td><select id="pendapatan" name="pendapatan" style="width:150px">
		<!--<option value=""></option>--><?php echo $optPendapatan;?>
	</select></td>
</tr>
<!--
<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai'];?></label></td><td><input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai'];?></label></td><td><input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
-->
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
	<button onclick="zPreview('sdm_slave_2PendapatanHarian','<?php echo $arr;?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
	
	<button onclick="zExcel(event,'sdm_slave_2PendapatanHarian.php','<?php echo $arr;?>')" class="mybutton" name="preview" id="preview">Excel</button>

	<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel'];?></button>
</td></tr>
</table>
</fieldset>
</div>


<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:330px;max-width:1920px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>