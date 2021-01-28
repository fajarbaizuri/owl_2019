<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
$karyawanid=$_POST['karyawanid'];
if($karyawanid=='')
{
 echo"<option value=''></option";
}
else
{
	$str="select nomor,nama,ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as umur,jeniskelamin
		  from ".$dbname.".sdm_karyawankeluarga where 
		  karyawanid=".$karyawanid." and tanggungan=1";
		
	$res=mysql_query($str);
	$no=0;
	$optKel="<option value=0>Ybs/PIC</option>";
	while($bar=mysql_fetch_object($res))
	{
		$optKel.="<option value='".$bar->nomor."'>".$bar->nama."(".$bar->umur."Th)-".$bar->jeniskelamin."</option>";
	}	
	echo $optKel;
}
?>