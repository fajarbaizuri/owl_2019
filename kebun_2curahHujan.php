<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanCurahHujan']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/kebun_2urahHujan.js" /></script>
<div id="action_list">
<?php
//$lokasi=$_SESSION['empl']['lokasitugas'];
	$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}

//for($x=0;$x<=6;$x++)
//{
//	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
//	$optper.="<option value=".date("Y-m",$dt).">".date("m-Y",$dt)."</option>";
//}
$optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_curahhujan order by tanggal desc";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
     $thn=explode("-", $rTgl['periode']);
   if($thn[1]=='12')
   {
   $optper.="<option value='".substr($rTgl['periode'],0,4)."'>".substr($rTgl['periode'],0,4)."</option>";
   }
   $optper.="<option value='".$rTgl['periode']."'>".substr($rTgl['periode'],5,2)."-".substr($rTgl['periode'],0,4)."</option>";
}

echo"<table>
     <tr valign=moiddle>
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
			echo $_SESSION['lang']['kebun'].":<select id=company_id name=company_id style=width:200px>".$optOrg."</select>&nbsp;"; 
			echo $_SESSION['lang']['periode'].":<select id=period name=period>".$optper."</select>";
			echo"<button class=mybutton onclick=save_pil()>".$_SESSION['lang']['save']."</button>
			     <button class=mybutton onclick=ganti_pil()>".$_SESSION['lang']['ganti']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php 
CLOSE_BOX();
OPEN_BOX();

?>
<div id="cari_barang" name="cari_barang">
<div id="hasil_cari" name="hasil_cari">
    <fieldset>
    <legend><?php echo $_SESSION['lang']['result']?></legend>
     <img onclick=dataKeExcel(event,'kebun_2curahHujanExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>

   <div id="contain">
   </div>
    </fieldset>
    </div>
</div>
<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>