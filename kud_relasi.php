<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
jdlExcel='<?php  echo $_SESSION['lang']['relasi']?>';
</script>
<script type="application/javascript" src="js/kud_relasi.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="headher">
<?php
OPEN_BOX('',"<b>".$_SESSION['lang']['relasi']."</b>");
$sCer="select nosertifikat from ".$dbname.".kud_sertifikat order by `updatetime` desc";
$qCer=mysql_query($sCer) or die(mysql_error());
while($rCer=mysql_fetch_assoc($qCer))
{
	$optCer.="<option value=".$rCer['nosertifikat'].">".$rCer['nosertifikat']."</option>";
}
$sPem="select idpemilik,nama  from ".$dbname.".kud_pemilik order by `idpemilik` desc";
$qPem=mysql_query($sPem) or die(mysql_error());
while($rPem=mysql_fetch_assoc($qPem))
{
	$optIdpem.="<option value=".$rPem['idpemilik'].">".$rPem['nama']."</option>";
}
$arrStat=array($_SESSION['lang']['tidakaktif'],$_SESSION['lang']['aktif']);
foreach($arrStat as $dt => $is)
{
	$optStat.="<option value=".$dt." ".($dt=='1'?'selected':'').">".$is."</option>";
}
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['nosertifikat']?></td>
<td>:</td>
<td><select id="idCer" name="idCer" style="width:150px;"><option value=""></option><?php echo $optCer?></select>
</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['nama']?></td>
<td>:</td>
<td><select id="idPem" name="idPem" style="width:150px;"><option value=""></option><?php echo $optIdpem?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['status']?></td>
<td>:</td>
<td><select id="stat" name="stat" style="width:150px;"><?php echo $optStat?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['keterangan']?></td>
<td>:</td>
<td><input type="text" id="ket" name="ket" class="myinputtext" maxlength="45" style="width:150px;"  /></td>
</tr>


<tr>
<td colspan="3" id="tmblHeader">
<button class=mybutton id='dtl_pem' onclick='saveData()'><?php echo $_SESSION['lang']['save']?></button><button class=mybutton id='cancel_gti' onclick='cancelSave()'><?php echo $_SESSION['lang']['cancel']?></button>
</td>
</tr>
</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="list_ganti">
<script>loadData();</script>
</div>
<?php 
echo close_body();
?>