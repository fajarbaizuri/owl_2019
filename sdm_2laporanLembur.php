<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
//if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
//{
//	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi order by namaorganisasi asc ";	
//	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
//}
//else
//{
//	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
//}




//$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."'";
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}
$sBagian="select distinct * from ".$dbname.".sdm_5departemen order by nama asc";
$qBagian=mysql_query($sBagian) or die(mysql_error());
while($rBagian=mysql_fetch_assoc($qBagian))
{
	$optBagian.="<option value=".$rBagian['kode'].">".$rBagian['nama']."</option>";
}

//$sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".$lksiTugas."' and sistemgaji like '%Bulanan%'  order by namakaryawan asc";
//$qKry=mysql_query($sKry) or die(mysql_error());
//while($rKry=mysql_fetch_assoc($qKry))
//{
//	$optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
//}
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL') order by namaorganisasi asc ";	
}
else
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kdOrg##periode##tgl1##tgl2##pilihan##pilihan2##pilihan3";
$arrKry="##kdeOrg##period##idKry##tgl_1##tgl_2";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/sdm_2laporanLembur.js'></script>
<script>

</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanLembur']?></b></legend>
<table cellspacing="1" border="0" >
        <?php
        if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
        {           
        ?>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdOrg" name="kdOrg" style="width:150px" onchange="getPeriode()"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()"><?php echo $optPeriode?></select></td></tr>    
<?php
        }
        else
        {
?>
    <tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdOrg" name="kdOrg" style="width:150px"><?php echo $optOrg?></select></td></tr>
    <tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()"><?php echo $optPeriode?></select></td></tr>    
<?php }?>

<tr><td><label><?php echo $_SESSION['lang']['sistemgaji']?></label></td><td><select id="pilihan2" name="pilihan2" style="width:150px" onchange="getTgl()"><option value="semua">Seluruhnya</option><option value="bulanan">Bulanan</option><option value="harian">Harian</option></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td><td><input disabled type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input disabled type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['options']?></label></td><td><select id="pilihan" name="pilihan" style="width:150px"><option value="rupiah">dalam Rupiah</option><option value="jam_aktual">dalam Jam Aktual</option><option value="jam_lembur">dalam Jam Lembur</option></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['bagian']?></label></td><td><select id="pilihan3" name="pilihan3" style="width:150px"><option value="semua">Seluruhnya</option><?php echo $optBagian?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2laporanLembur','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2laporanLembur','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2laporanLembur.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

</table>
</fieldset>
</div>

<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
























<?php

CLOSE_BOX();
echo close_body();
?>