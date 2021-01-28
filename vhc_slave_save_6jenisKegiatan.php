<?
require_once('master_validation.php');
require_once('config/connection.php');

$NOR=$_POST['NOR'];
$kelompokvhc=$_POST['kelompokvhc'];
$namajenisvhc=$_POST['namajenisvhc'];
if ($noakun1=='NULL'){
	$noakun1="NULL";
}else{
	$noakun1="'".$_POST['noakun1']."'";
}
if ($noakun2=='NULL'){
	$noakun2="NULL";
}else{
	$noakun2="'".$_POST['noakun1']."'";
}
if ($noakun3=='NULL'){
	$noakun3="NULL";
}else{
	$noakun3="'".$_POST['noakun1']."'";
}
if ($noakun4=='NULL'){
	$noakun4="NULL";
}else{
	$noakun4="'".$_POST['noakun1']."'";
}
if ($noakun5=='NULL'){
	$noakun5="NULL";
}else{
	$noakun5="'".$_POST['noakun1']."'";
}
if ($noakun6=='NULL'){
	$noakun6="NULL";
}else{
	$noakun6="'".$_POST['noakun1']."'";
}

$method=$_POST['method'];


switch($method)
{
case 'update':	
	if($NOR==''){
	echo " Gagal,Kode Tidak Ada.";
	exit;
	}
	
	$str="UPDATE `".$dbname."`.`vhc_5jenisvhc` SET `namajenisvhc`='".$namajenisvhc."', `noakun_upah`=".$noakun1.", `noakun_premi`=".$noakun2.",
	`noakun_umkn`=".$noakun3.", `noakun_bbm`=".$noakun4.", `noakun_spart`=".$noakun5.", `noakun_service`=".$noakun6.",
	`kelompokvhc`='".$kelompokvhc."' WHERE  `jenisvhc`='".$NOR."';";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	if($NOR==''){
	echo " Gagal,Kode Tidak Ada.";
	exit;
	}
	
	$str="delete from ".$dbname.".vhc_5jenisvhc 
	where jenisvhc='".$NOR."'";
	


	if(mysql_query($str))
	{
		$str="INSERT INTO ".$dbname.".`vhc_5jenisvhc` 
	(`jenisvhc`, `namajenisvhc`, `noakun_upah`, `noakun_premi`, `noakun_umkn`, `noakun_bbm`, `noakun_spart`, `noakun_service`, `kelompokvhc`) VALUES 
	('".$NOR."', '".$namajenisvhc."', ".$noakun1.", ".$noakun2.", ".$noakun3.", ".$noakun4.", ".$noakun5.", ".$noakun6.", '".$kelompokvhc."');";
		
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
		
		
	}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	
	break;
	default:
   break;					
}
$str1="select * from ".$dbname.".vhc_5jenisvhc order by jenisvhc";

if($res1=mysql_query($str1))
{
	echo"<table class=sortable cellspacing=1 border=0 style='width:1024px;'>
	     <thead>
		 <tr class=rowheader>
		   <td style='width:150px;'>".$_SESSION['lang']['tipe']."</td>
		   <td>".$_SESSION['lang']['kodekelompok']."</td>
		   <td>".$_SESSION['lang']['namajenisvhc']."</td>
		   <td>Akun Transit Upah</td>		   
		   <td>Akun Transit Premi</td>		   
		   <td>Akun Transit Uang Makan</td>		   
		   <td>Akun Transit BBM</td>		   
		   <td>Akun Transit Sparepart</td>		   
		   <td>Akun Transit Service</td>		
			<td style='width:30px;'>Aksi</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
			 <td align=center>".$bar1->jenisvhc."</td>
		     <td align=center>".$bar1->kelompokvhc."</td>
			 <td >".$bar1->namajenisvhc."</td>
			 <td align=center>".$bar1->noakun_upah."</td>
			 <td align=center>".$bar1->noakun_premi."</td>
			 <td align=center>".$bar1->noakun_umkn."</td>
			 <td align=center>".$bar1->noakun_bbm."</td>
			 <td align=center>".$bar1->noakun_spart."</td>
			 <td align=center>".$bar1->noakun_service."</td>
                    <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->jenisvhc."','".$bar1->kelompokvhc."','".$bar1->namajenisvhc."','".$bar1->noakun_upah."',
	'".$bar1->noakun_premi."','".$bar1->noakun_umkn."','".$bar1->noakun_bbm."','".$bar1->noakun_spart."',
	'".$bar1->noakun_service."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
	
}
?>
