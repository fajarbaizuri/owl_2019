<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
include('lib/zForm.php');
echo open_body();
?>

<?php
include('master_mainMenu.php');
?>


<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/zTools.js"></script>
<script language=javascript1.2 src='js/bgt_produksi_pks.js'></script>


<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}

$arr=array("External","Internal","Afliasi");
$opttbs="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arr as $isi =>$eia)
{
	$opttbs.="<option value=".$isi." >".$eia."</option>";
}
$optsup="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";



$optthnttp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
/*$sql = "SELECT distinct tahunbudget FROM ".$dbname.".bgt_produksi_pks where millcode like '%".$_SESSION['empl']['lokasitugas']."%' and tutup=0 order by tahunbudget desc";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optthnttp.="<option value=".$data['tahunbudget'].">".$data['tahunbudget']."</option>";
			}
*/


$optorgclose="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
/*$sql = "SELECT distinct millcode FROM ".$dbname.".bgt_produksi_pks where millcode like '%".$_SESSION['empl']['lokasitugas']."%' ";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optorgclose.="<option value=".$data['millcode'].">".$data['millcode']."</option>";
			}*/

//untuk lokasi tugas
//$lokasitugas=$_SESSION['empl']['lokasitugas'];


//untuk header sort
$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>



<?php
OPEN_BOX('',"<b>".$_SESSION['lang']['produksipks']."</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				 <tr><td width=95>".$_SESSION['lang']['budgetyear']."</td><td td width=7>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress=\"return angka_doang(event);\" style=\"width:175px;\" maxlength=4 /></td></tr>
				 <tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id=kdpks name=kdpks style=\"width:175px;\">".$optOrg."</select></td></tr>
				 <tr><td>".$_SESSION['lang']['statusBuah']."</td><td>:</td><td><select id=ktbs name=ktbs style=\"width:175px;\">".$opttbs."</select></td></tr>
				
				<tr><td></td><td></td><td><br /><div id=tmblSave>
					 <button onclick=savehead(0) class=mybutton name=saveDt id=saveDt>".$_SESSION['lang']['save']."</button> 
				   	 <button class=mybutton onclick=batal() name=btl id=btl>".$_SESSION['lang']['cancel']."</button></div></td></tr>
			</table></fieldset><input type=hidden id=method value=saveData />";


echo"<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>
    <div id=closetab><table>
		<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td><select id=thnttp style='widht:150px'>".$optthnttp."</select></td></tr>
		<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id=lkstgs style='widht:150px'>".$optorgclose."</select></td></tr>";
		
echo"<tr><td></td><td></td><td><br /><button class=\"mybutton\"  id=\"saveData\" onclick='closepks()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
echo"</div></fieldset>";
?>


<?php
echo"<div id='printContainer' style=display:none;>
      <fieldset style='clear:both;float: left;'><legend>Sebaran Bulanan</legend>";

// Sebaran
$arrBln1=array();
$arrBln1[] = substr($_SESSION['lang']['jan'],0,3);
$arrBln1[] = substr($_SESSION['lang']['peb'],0,3);
$arrBln1[] = substr($_SESSION['lang']['mar'],0,3);
$arrBln1[] = substr($_SESSION['lang']['apr'],0,3);
$arrBln1[] = substr($_SESSION['lang']['mei'],0,3);
$arrBln1[] = substr($_SESSION['lang']['jun'],0,3);
$arrBln1[] = substr($_SESSION['lang']['jul'],0,3);
$arrBln1[] = substr($_SESSION['lang']['agt'],0,3);
$arrBln1[] = substr($_SESSION['lang']['sep'],0,3);
$arrBln1[] = substr($_SESSION['lang']['okt'],0,3);
$arrBln1[] = substr($_SESSION['lang']['nov'],0,3);
$arrBln1[] = substr($_SESSION['lang']['dec'],0,3);

$sebar="<table class=sortable border=0 cellspacing=1 cellpadding=1>";
$sebar.="<thead>";
$sebar.="<tr class=rowheader><td colspan=12 align=center>Sebaran</td><td rowspan=2>Aksi</td></tr>";
$sebar.="<tr class=rowheader>";
foreach($arrBln1 as $brs=>$dtBln)
{
	$sebar.="<td>".$dtBln."</td>";
}
$sebar.="</tr></thead>";
$sebar.="<tbody><tr class=rowcontent>";
foreach($arrBln1 as $brs=>$dtBln)
{
	$sebar.="<td>".makeElement('sebar'.$brs,'textnum','1',array('style'=>'width:20px'))."</td>";
}
$sebar.="<td>".makeElement('aksiSebar','btn','Sebarkan',array('onclick'=>'sebarkan()'))."</td>";
$sebar.="</tr></tbody>";
$sebar.="</table><br>";
echo $sebar;

$arrBln=array(
"1"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['jan'],0,3),
"2"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['peb'],0,3),
"3"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['mar'],0,3),
"4"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['apr'],0,3),
"5"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['mei'],0,3),
"6"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['jun'],0,3),
"7"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['jul'],0,3),
"8"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['agt'],0,3),
"9"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['sep'],0,3),
"10"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['okt'],0,3),
"11"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['nov'],0,3),
"12"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['dec'],0,3)
);

$tot=count($arrBln);
echo"<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader>";
echo"
	<td>".$_SESSION['lang']['kodesupplier']."</td>
	<td>".$_SESSION['lang']['kgtbs']."</td>
	<td>".$_SESSION['lang']['oer']."(CPO)</td>
	<td>".$_SESSION['lang']['oer']."(Ker)</td>";
	
foreach($arrBln as $brs=>$dtBln)
{
	echo"<td>".$dtBln."</td>";
}
echo"<td>".$_SESSION['lang']['action']."</td></tr></thead>";
echo"<tbody><tr class=rowcontent>";
echo"
	<td><select id=kdsup name=kdsup style=\"width:150px;\">".$optsup."</select></td>
	<td><input type=text class=myinputtextnumber id=kgtbs name=kgtbs onkeyup=bagi() onkeypress=\"return angka_doang(event);\" style=\"width:50px;\"  /></td>
	<td><input type=text class=myinputtextnumber id=oerc name=oerc onkeypress=\"return angka_doang(event);\" style=\"width:50px;\"  /></td>
	<td><input type=text class=myinputtextnumber id=oerk name=oerk onkeypress=\"return angka_doang(event);\" style=\"width:50px;\"  /></td>";
	
foreach($arrBln as $brs2=>$dtBln2)
{
	echo"<td><input type='text' class='myinputtextnumber' id=brt_x".$brs2." value=0 style='width:50px' onkeypress=\"return angka_doang(event);\" /></td>";
}
echo"<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveBrt(".$tot.")\" src='images/save.png'/></td></tr></tbody></table>";
echo "</fieldset></div>";
CLOSE_BOX();
?>


<?php
OPEN_BOX();

echo"<div>".$_SESSION['lang']['budgetyear'].": <select id='thnbudgetHeader' style='width:150px;' onchange='ubah_list()'>".$optTahunBudgetHeader."</select></div>";
echo"<fieldset><legend><b>".$_SESSION['lang']['list']."</b></legend>";
echo"<div id=contain><script>loadData()</script></div>";
echo"</fieldset>";
CLOSE_BOX();
echo close_body();
?>