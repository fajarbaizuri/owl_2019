<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
 //default penerimaan barang 
 //tipe 1=masuk normal, 2 retur pengeluaran, 5=pengeluaran normal,6=retur pemsaukan(ke supplier)
 //============================================   
  $gudang	=$_POST['gudang'];
  $num=1;//default value 
  $str="select max(notransaksi) notransaksi from ".$dbname.".log_transaksiht where tipetransaksi>4 and tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']."
        and kodegudang='".$gudang."' order by notransaksi desc limit 1";	
  if($res=mysql_query($str))
  {
  	while($bar=mysql_fetch_object($res))
	{
		$num=$bar->notransaksi;
		if($num!='')
		{
			$num=intval(substr($num,6,5))+1;
		}	
		else
		{
			$num=1;
		}
	}
	if($num<10)
	   $num='0000'.$num;
	else if($num<100)
	   $num='000'.$num;
	else if($num<1000)
	   $num='00'.$num;
	else if($num<10000)
	   $num='0'.$num;
        else
	   $num=$num;
   $num=$_SESSION['gudang'][$gudang]['tahun'].$_SESSION['gudang'][$gudang]['bulan'].$num."-GI-".$gudang;	            	
  echo $num;
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
}
else
{
	echo " Error: Transaction Period missing";
}
?>