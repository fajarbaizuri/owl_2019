<?php
	require_once('master_validation.php');
	require_once('config/connection.php');

	$kode=$_POST['kode'];
	$kelompok=$_POST['kelompok'];
	$method=$_POST['method'];
	
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".sdm_5kl_prasarana where kode='".$kode."'";
			
		break;
		case 'update':
		$strx="update ".$dbname.".sdm_5kl_prasarana set nama='".$kelompok."' where kode='".$kode."'";
		break;	
		case 'insert':

		$strx="insert into ".$dbname.".sdm_5kl_prasarana(
					   kode,nama)
				values('".$kode."','".$kelompok."')";
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
	
$str="select * from ".$dbname.".sdm_5kl_prasarana order by kode desc";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		      <td>".$no."</td>
		      <td>".$bar->kode."</td>
			  <td>".$bar->nama."</td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->nama."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlmpkpra('".$bar->kode."','".$bar->nama."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
?>