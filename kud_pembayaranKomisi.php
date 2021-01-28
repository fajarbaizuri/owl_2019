<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['pembayaranKomisi']."</b>");
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script>
jdlExcel='<?php  echo $_SESSION['lang']['pembayaranKomisi']?>';
</script>
<script type="application/javascript" src="js/kud_pembayaranKomisi.js"></script>
<input type="hidden" id="proses" name="proses" value="insert"  />
<div id="action_list">
<?php
$sKbn="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qKbn=mysql_query($sKbn) or die(mysql_error());
while($rKbn=mysql_fetch_assoc($qKbn))
{
	$optKbn.="<option value=".$rKbn['kodeorganisasi'].">".$rKbn['namaorganisasi']."</option>";
}
/*for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}*/
for($x=-1;$x<=5;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['notransaksi'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tanggalbayar'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />&nbsp;";
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

$sPem="select idpemilik,nama  from ".$dbname.".kud_pemilik order by `idpemilik` desc";
$qPem=mysql_query($sPem) or die(mysql_error());
while($rPem=mysql_fetch_assoc($qPem))
{
	$optIdpem.="<option value=".$rPem['idpemilik'].">".$rPem['nama']."</option>";
}
/*for($x=0;$x<=12;$x++)
{
	$dte=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPrd.="<option value=".date("Y-m",$dte).">".date("Y-m",$dte)."</option>";
}*/
$str="select distinct periode from ".$dbname.".log_5saldobulanan order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optPrd.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	

?>
<fieldset>
<legend><?php echo $_SESSION['lang']['entryForm']?></legend>
<table cellspacing="1" border="0">
<tr>
<td><?php echo $_SESSION['lang']['notransaksi']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" onkeypress="return tanpa_kutip(event)" id="noTrans" name="noTrans" style="width:150px;" />
</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['namapemilik']?></td>
<td>:</td>
<td><select id="idPemilik" name="idPemilik" style="width:150px;" onchange="getCer('0','0')"><option value=""></option><?php echo $optIdpem?></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['nosertifikat']?></td>
<td>:</td>
<td><select id="idCer" name="idCer" style="width:150px;"></select></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['periode']?></td>
<td>:</td>
<td>
<select id="period" name="period" style="width:150px;" ><?php echo $optPeriode;?></select>
</td>
</tr>

<tr>
<td><?php echo $_SESSION['lang']['tanggalbayar']?></td>
<td>:</td>
<td><input type="text" class="myinputtext" id="tglPembyrn" name="tglPembyrn" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" style="width:150px;" /></td>
</tr>
<tr>
<tr>
<td><?php echo $_SESSION['lang']['namapenerima']?></td>
<td>:</td>
<td>
<input type="text" id="nmPenerima" name="nmPenerima" class="myinputtext" style="width:150px;" onkeypress="return tanpa_kutip(event)" maxlength="30" />&nbsp;</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['dibayaroleh']?></td>
<td>:</td>
<td>
<input type="text" id="dByrOlh" name="dByrOlh" class="myinputtext" style="width:150px;" onkeypress="return tanpa_kutip(event)" maxlength="30" />&nbsp;</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['mengetahui']?></td>
<td>:</td>
<td>
<input type="text" id="mngthi" name="mngthi" class="myinputtext" style="width:150px;" onkeypress="return tanpa_kutip(event)" maxlength="30" />&nbsp;</td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['jumlah']?></td>
<td>:</td>
<td>
<input type="text" id="jMlh" name="jMlh" class="myinputtextnumber" style="width:150px;" onkeypress="return angka_doang(event)" value="0" /></td>
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