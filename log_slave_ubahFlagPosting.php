<?php
	require_once('master_validation.php');
	require_once('config/connection.php');

	$notransaksi=$_POST['notransaksi'];
	$status=$_POST['status'];
	$gudang=$_POST['gudang'];
	$user		=$_SESSION['standard']['userid'];
     $str="update ".$dbname.".log_transaksiht set post=".$status.",
	       postedby=".$user."
	       where notransaksi='".$notransaksi."'
		   and kodegudang='".$gudang."'";   
	 if(mysql_query($str))
	 {
       if(mysql_affected_rows($conn)<1)
	   {
	   	 echo "Error : post status update nothing";
	   }	
	 }
	 else
		 {
			echo " Gagal,".(mysql_error($conn));
		 }
?>
