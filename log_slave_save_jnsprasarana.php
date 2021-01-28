<?php
	require_once('master_validation.php');
	require_once('config/connection.php');

	$kode=$_POST['kode'];
	$kelompok=$_POST['kelompok'];
	$satuan=$_POST['satuan'];
	$kel=$_POST['kel'];
	$method=$_POST['method'];
	
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".sdm_5jenis_prasarana where jenis='".$kode."'";
			
		break;
		case 'update':
		$strx="update ".$dbname.".sdm_5jenis_prasarana set nama='".$kelompok."',satuan='".$satuan."',kelompok='".$kel."' where jenis='".$kode."'";
		break;	
		case 'insert':

		$strx="insert into ".$dbname.".sdm_5jenis_prasarana(
					   jenis,nama,satuan,kelompok)
				values('".$kode."','".$kelompok."','".$satuan."','".$kel."')";
						//echo $strx; exit();
		//break;
		//default:
        break;	
	}
	if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	
$str="select * from ".$dbname.".sdm_5jenis_prasarana order by jenis desc";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		      <td>".$no."</td>
		      <td>".$bar->jenis."</td>
			  <td>".$bar->nama."</td>
			  <td>".$bar->satuan."</td>
			  <td>".$bar->kelompok."</td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->jenis."','".$bar->nama."','".$bar->satuan."','".$bar->kelompok."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlmpkje('".$bar->jenis."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
?>