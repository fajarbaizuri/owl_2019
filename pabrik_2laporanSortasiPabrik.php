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
$sPbk="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qPbk=mysql_query($sPbk) or die(mysql_error());
while($rPbk=mysql_fetch_assoc($qPbk))
{
	$optPabrik.="<option value=".$rPbk['kodeorganisasi'].">".$rPbk['namaorganisasi']."</option>";
}
$arrOptIntex=array("External","Internal","Afliasi");
foreach($arrOptIntex as $isi =>$tks)
{
	$optBuah.="<option value=".$isi." >".$tks."</option>";
}

$arr="##tglAwal##tglAkhir##statBuah##kdPbrk##suppId##kdOrg";
?>
<script>
optInt="<option value=''><? echo $_SESSION['lang']['all']?></option>";
optExt="<option value=''><? echo $_SESSION['lang']['all']?></option>";
</script>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>

<script language=javascript src='js/pabrik_2laporanSortasiPabrik.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanSortasi']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kdpabrik']?></label></td><td><select id="kdPbrk" name="kdPbrk" style="width:150px;"  ><option value=""><?php echo $_SESSION['lang']['all']?></option><?php echo $optPabrik?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['statusBuah']?></label></td><td><select id="statBuah" name="statBuah" style="width:150px;" onchange="getKbn()" ><option value="5"><?php echo $_SESSION['lang']['all']?></option><?php echo $optBuah?></select></td></tr>
<tr> 	 
			<td style='valign:top'><?php echo $_SESSION['lang']['kebun']?> </td><td>
			<select id="kdOrg" name="kdOrg"  style="width:150px;"><option value=''><?php echo $_SESSION['lang']['all'];?></option></select></td>
			</tr>
			<tr> 	 
			<td style='valign:top'><?php echo $_SESSION['lang']['namasupplier'] ?> </td><td>
			<select id="suppId" name="suppId"  style="width:150px;"><option value=''><?php echo $_SESSION['lang']['all'];?></option></select></td>
			</tr>
<tr><td><label><?php echo $_SESSION['lang']['startdate']?></label></td><td><input type="text" class="myinputtext" id="tglAwal" name="tglAwal" onmousemove="setCalendar(this.id)" onkeypress="return false; " size="10" maxlength="4" style="width:150px;" />
s.d.</td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type="text" class="myinputtext" id="tglAkhir" name="tglAkhir" onmousemove="setCalendar(this.id)" onkeypress="return false; " size="10" maxlength="4" style="width:150px;" /></td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2laporanSortasiPabrik','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'pabrik_slave_2laporanSortasiPabrik.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
<?php
CLOSE_BOX();/*<button onclick="zPdf('pabrik_slave_2laporanSortasiPabrik','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>*/
echo close_body();
?>