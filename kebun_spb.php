<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['suratPengantarBuah']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript">

var nmTmblDone='<?php echo $_SESSION['lang']['done']?>';
var nmTmblCancel='<?php echo $_SESSION['lang']['cancel']?>';
var nmTmblSave='<?php echo $_SESSION['lang']['save']?>';
 optIsi='<?php 
 			 $kodeOrg=isset($_SESSION['temp'])?substr($_SESSION['temp']['nSpb'],8,6):'';	 
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
<script language="javascript" src="js/kebun_spb.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />

<?php // print"<pre>"; print_r($_SESSION); print"</pre>"; ?>
<div id="action_list">
<?php
$optPeriode="<option value=''></option>";
$sql = "SELECT distinct periode FROM ".$dbname.".setup_periodeakuntansi where  kodeorg	='".$_SESSION['empl']['lokasitugas']."' ORDER BY periode desc limit 6";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optPeriode.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}	
		/*	
$optPeriode='';
for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
	
	*/
	$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' ";
	$query=mysql_query($sql) or die(mysql_error());
	$optOrg='';
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
echo"<table cellspacing=1 border=0>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['nospb'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariSpb()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
	 <td><fieldset><legend>".$_SESSION['lang']['exportData']."</legend>"; 
			echo $_SESSION['lang']['periode'].":<select id=periode nama=periode>".$optPeriode."</select>&nbsp;";
			echo $_SESSION['lang']['kodeorg'].":<select id=unitOrg name=unitOrg>".$optOrg."</select>";
			echo"&nbsp;<img onclick=dataKeExcel(event,'kebun_sbp_excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>";
echo"</fieldset></td>
	 </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="listSpb">
<?php OPEN_BOX()?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list']?></legend>
<table cellspacing="1" border="0" class="sortable">
<thead>
<tr class="rowheader">
<td>No.</td>
<td><?php echo $_SESSION['lang']['nospb']?></td>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>Action</td>
</tr>
</thead>
<tbody id="contain">
<script>loadData()</script>
</tbody>
</table>
</fieldset>
<?php CLOSE_BOX()?>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();

$sORg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$qOrg=mysql_query($sORg) or die(mysql_error());
$optOrg2='';
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";	
}
$optPrd="<option value=''></option>";
$sql = "SELECT distinct periode FROM ".$dbname.".setup_periodeakuntansi where  kodeorg	='".$_SESSION['empl']['lokasitugas']."' ORDER BY periode desc limit 6";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optPrd.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}	
			/*
$optPrd='';
for($x=0;$x<=12;$x++)
{
	$dte=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("Y-m",$dte)."</option>";
}
*/
?>
<fieldset>
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['kodeorg']?></td>
<td>:</td>
<td>
<select id="kodeOrg" name="kodeOrg" style="width:120px;" onchange="getDiv(0)"><option value=""></option><?php echo $optOrg2;?></select>
<!--<input type="text"  id="noSpb" name="noSpb" class="myinputtext" style="width:120px;" disabled="disabled" />--></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['afdeling']?></td>
<td>:</td>
<td>
<select id="kodeDiv" name="kodeDiv" style="width:120px;" ></select>
</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['periode']?></td>
<td>:</td>
<td>
<select id="period" name="period" style="width:120px;" ><?php echo $optPrd;?></select>
<!--<input type="text"  id="noSpb" name="noSpb" class="myinputtext" style="width:120px;" disabled="disabled" />--></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['nourut']?></td>
<td>:</td>
<td>
<input type="text" id="nourut" name="nourut" class="myinputtextnumber" style="width:120px;" maxlength="7" onkeypress="return angka_doang(event)"  onblur="fillZero()"/>
<input type="hidden"  id="noSpb" name="noSpb" class="myinputtext" style="width:120px;" disabled="disabled" /></td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['tanggal']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="tgl_ganti" name="tgl_ganti" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" style="width:120px;" /></td>
</tr>

<?php

//$optnamaken


$optop="<option value='0'>".$_SESSION['lang']['pilihdata']."</option>";
//$ind="select * from ".$dbname.".vhc_5operator where aktif=1";
$ind="select a.*,b.nopol,ifnull(c.namakaryawan,a.nama) as namakaryawan from ".$dbname.".vhc_5operator a left join ".$dbname.".vhc_5master b on a.vhc=b.kodevhc 
left join ".$dbname.".datakaryawan c ON a.karyawanid=c.karyawanid where c.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and a.aktif=1 or a.karyawanid like '%".$_SESSION['empl']['lokasitugas']."%' ";
$dr = mysql_query($ind) or die ("SQL ERR : ".mysql_error());
while ($ra=mysql_fetch_assoc($dr))
			{
			$optop.="<option value=".$ra['karyawanid'].">".$ra['namakaryawan']."</option>";
			}	


	echo"
		<tr>
			<td>No. Polisi Kendaraan</td>
			<td>:</td>
			<td><input type=text id=nopol name=nopol maxlength=10 class=myinputtext style=width:120px;   onkeypress=\"return tanpa_kutip(event);\" ></td>
		</tr>
	
	
	
		<tr>
			<td>Sopir</td>
			<td>:</td>
			<td><select onchange=\"getPlat(this)\" id=op style=\"width:120px;\">".$optop."</select></td></td>
		</tr>
	
	";


?>


<tr>
<td colspan="3" id="tmbLheader">
</td>
</tr>



</table>
</fieldset>

<?php
CLOSE_BOX();
?>
</div>
<div id="detailSpb" style="display:none">
<?php 
OPEN_BOX();
?>

<fieldset>
<legend><?php echo $_SESSION['lang']['detail']?></legend>
<b><? echo $_SESSION['lang']['nospb']?></b> : <input type="text" id="detail_kode" name="detail_kode" disabled="disabled" class="myinputtext" style="width:150px;" /><!--".makeElement("detail_kode",'text',$noSpb,array('disabled'=>'disabled','style'=>'width:150px'));-->
  
<table cellspacing="1" border="0">
<tbody id="detailIsi">
<tr><td><table id='ppDetailTable'>
</table>
</tbody>
<tr><td id="tombol">

</td></tr>
</table>
</fieldset><br />
<br />
<div style="overflow:auto; height:300px;">
<fieldset>
<legend><?php echo $_SESSION['lang']['datatersimpan']?></legend>
<table cellspacing="1" border="0">
<thead>
 <tr class="rowheader">
 	<td>No.</td>
    <td><?php echo $_SESSION['lang']['blok'] ?></td>
 	<td><?php echo $_SESSION['lang']['bjr'] ?></td>
  	<td><?php echo $_SESSION['lang']['janjang'] ?></td>
    <td><?php echo $_SESSION['lang']['brondolan'] ?></td>
  	<td><?php echo $_SESSION['lang']['mentah'] ?></td>
	<td><?php echo $_SESSION['lang']['busuk'] ?></td>
	<td><?php echo $_SESSION['lang']['matang'] ?></td>
	<td><?php echo $_SESSION['lang']['lewatmatang'] ?></td>
    <td colspan=3>Action</td>
    </tr>
</thead>
<tbody id="contentDetail">
</tbody>
</table>

</fieldset></div>

<?php
CLOSE_BOX();
?>
</div>
<?php 
echo close_body();
?>