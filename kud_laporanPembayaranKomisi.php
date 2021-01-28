<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanPembayaranKomisi']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/kud_laporanPembayaranKomisi.js" /></script>
<div id="action_list">
<?php
//$lokasi=$_SESSION['empl']['lokasitugas'];
	$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$optOrg.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>"; 
	}
/*$str="select distinct periode from ".$dbname.".log_5saldobulanan order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	*/
for($x=-1;$x<=5;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optper.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}

echo"<table>
     <tr valign=moiddle>
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
			echo $_SESSION['lang']['kebun'].":<select id=company_id name=company_id style=width:150px onChange='getThp()'><option value='0'>All</option>".$optOrg."</select>&nbsp;"; 
			echo $_SESSION['lang']['tahap'].":<select id=thp name=thp style=width:150px><option value='0'>All</option></select>&nbsp;";
			echo $_SESSION['lang']['periode'].":<select id=period name=period>".$optper."</select>&nbsp;";			
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
     <img onclick=dataKeExcel(event,'kud_slavelaporanPembayaranKomisiExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>

    <table cellspacing="1" border="0">
        <tbody id="contain">
        </tbody>
    </table>
    </fieldset>
    </div>
</div>
<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>