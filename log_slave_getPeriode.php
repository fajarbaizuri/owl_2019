<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$gudang=$_POST['gudang'];

//ambil namapt
$str="select kodeorg, periode from ".$dbname.".setup_periodeakuntansi 
      where kodeorg='".$gudang."'";
$res=mysql_query($str);
//	$hasil='<option value="">Seluruhnya</option>';
//$hasil="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$hasil.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
echo $hasil;
?>