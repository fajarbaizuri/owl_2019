<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	
	$txtfind_klp		=$_POST['txtfind_klp'];
	$stp="select * from ".$dbname.".pmn_4klcustomer where kelompok like '%".$txtfind_klp."%' limit 12";
if($txtfind_klp!='')
{
	if($rep=mysql_query($stp))
	{
	echo"<table class=data cellspacing=1 cellpadding=2  border=0>
		 <thead>
		 <tr class=rowheader>
		 <td class=firsttd>
		 No.
		 </td>
		 <td>Kode Kelompok</td>
		 <td>Nama Kelompok</td>
		 <td>No. Akun</td>
		 <td>Nama Akun</td>
		 </tr>
		 </thead>
		 <tbody>";
	$no=0;	 
	while($bas=mysql_fetch_object($rep))
	{
		$op="select * from ".$dbname.".keu_5akun where `noakun`='".$bas->noakun."'";
		$po=mysql_query($op) or die(mysql_error($conn));
		$pos=mysql_fetch_object($po);
		$no+=1;
		echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setGroup('".$bas->kode."','".$bas->kelompok."')\" title='Click' >
			  <td class=firsttd>".$no."</td>
			  <td>".$bas->kode."</td>
			  <td>".$bas->kelompok."</td>
			  <td>".$pos->noakun."</td>
			  <td>".$pos->namaakun."</td>
			 </tr>";
	}	 
	echo "</tbody>
		  <tfoot>
		  </tfoot>
		  </table>";	   	
	}	
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
}
else
{
	$txtfind		=$_POST['txtfind'];
	$str=" select * from ".$dbname.".keu_5akun where namaakun like '%".$txtfind."%' or noakun like '%".$txtfind."%' or tipeakun like '%".$txtfind."%' limit 12";

 if($res=mysql_query($str))
  {
  	echo"<table class=data cellspacing=1 cellpadding=2  border=0>
	     <thead>
		 <tr class=rowheader>
		 <td class=firsttd>
		 No.
		 </td>
		 <td>No.Akun</td>
		 <td>Nama Akun</td>
		 <td>Tipe</td>
		 <td>Mata Uang</td>
		 <td>Kode Organisasi</td>
		 </tr>
		 </thead>
		 <tbody>";
	$no=0;	 
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setNoakun('".$bar->noakun."','".$bar->namaakun."','".$bar->tipeakun."','".$bar->matauang."','".$bar->kodeorg."')\" title='Click' >
		      <td class=firsttd>".$no."</td>
		      <td>".$bar->noakun."</td>
			  <td>".$bar->namaakun."</td><td>".$bar->tipeakun."</td>
			  <td>".$bar->matauang."</td><td>".$bar->kodeorg."</td>
			 </tr>";
	}	 
	echo "</tbody>
	      <tfoot>
		  </tfoot>
		  </table>";	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
}
?>