<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['daftarSertifikat']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
jdlExcel='<?php  echo $_SESSION['lang']['daftarSertifikat']?>';
</script>
<script type="application/javascript" src="js/kud_daftarSertifikat.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
$sKbn="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
$qKbn=mysql_query($sKbn) or die(mysql_error());
while($rKbn=mysql_fetch_assoc($qKbn))
{
	$optKbn.="<option value=".$rKbn['kodeorganisasi'].">".$rKbn['namaorganisasi']."</option>";
}
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['nosertifikat'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tglSertifikat'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariTransaksi()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX();
?>
<div id="list_ganti">
<script>loadData();</script>
</div>



<div id="headher" style="display:none">
<?php
OPEN_BOX();

?>
<fieldset>
<legend><?php echo $_SESSION['lang']['header']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['nosertifikat']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" onkeypress="return tanpa_kutip(event)" id="nosertifi" name="nosertifi" style="width:150px;" />
</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['kebun']?></td>
<td>:</td>
<td><select id="idKbn" name="idKbn" style="width:150px;"><?php echo $optKbn ?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['tglSertifikat']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="tglSer" name="tglSer" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" style="width:150px;" /></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['luas']?></td>
<td>:</td>
<td><input type="text" id="las" name="las" class="myinputtextnumber" style="width:150px;" onkeypress="return angka_doang(event)" value="0" />&nbsp;Ha</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['tahap']?></td>
<td>:</td>
<td>
<input type="text" id="thp" name="thp" class="myinputtext" style="width:150px;" onkeypress="return tanpa_kutip(event)" />&nbsp;</td>
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

<?php 
echo close_body();
?>