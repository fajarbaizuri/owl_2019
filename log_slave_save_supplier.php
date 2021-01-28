<?
require_once('master_validation.php');
require_once('config/connection.php');

	$kelompok	=$_POST['kelompok'];
	$telp		=$_POST['telp'];
	$fax		=$_POST['fax'];
	$idsupplier		=$_POST['idsupplier'];
	$email			=$_POST['email'];
	$namasupplier	=$_POST['namasupplier'];
	$npwp			=$_POST['npwp'];
	$cperson		=$_POST['cperson'];
	$kota			=$_POST['kota'];		
	$plafon			=$_POST['plafon'];
	$method			=$_POST['method'];
	$alamat			=$_POST['alamat'];
	$bank=$_POST['bank'];
	$rek=$_POST['rek'];
	$an=$_POST['an'];
	$kdtim=$_POST['kdtim'];
	
    $strx="select 1=1";

	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".log_5supplier where supplierid='".$idsupplier."'"; 
		break;
		case 'update':
			$strx="update ".$dbname.".log_5supplier set
                   kodekelompok='".$kelompok."',
				   namasupplier='".$namasupplier."',
				   alamat='".$alamat."',
				   kota='".$kota."',
				   telepon='".$telp."',
				   kontakperson='".$cperson."',
				   plafon=".$plafon.",
			       npwp='".$npwp."',
				   fax='".$fax."',
				   email='".$email."',
				   bank='".$bank."',
				   rekening='".$rek."',
				   an='".$an."',
				   kodetimbangan=''
				   where supplierid='".$idsupplier."'
				  "; 			
		break;	
		
		
		case 'insert':
		
			### cek nama suplier agar tidak terjadi kesamaan dalam input##
			$iCek="select distinct namasupplier from ".$dbname.".log_5supplier where namasupplier='".$namasupplier."'";
			$ada=true;
			$nCek=mysql_query($iCek)or die(mysql_error());
			while($dCek=mysql_fetch_assoc($nCek))
			{
				if ($ada==true)
				{
					echo "warning : Nama supplier untuk ".$namasupplier." sudah ada";
					exit();	
				}
				else
				{
				}
			}
		
			$strx="insert into ".$dbname.".log_5supplier(
			kodekelompok,namasupplier,alamat,
			kota,telepon,kontakperson,plafon,
			npwp,supplierid,fax,email,bank,rekening,an,kodetimbangan)
			values('".$kelompok."','".$namasupplier."','"
			         .$alamat."','".$kota."','".$telp."','"
					 .$cperson."',".$plafon.",'".$npwp."','"
					 .$idsupplier."','".$fax."','".$email."','".$bank."','".$rek."','".$an."','".$kdtim."')";	   			 
		break;
		default:
        break;	
	}
  if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	

$str=" select * from ".$dbname.".log_5supplier where kodekelompok='".$kelompok."' order by supplierid";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		     <td>".$kelompok."</td>
			 <td>".$bar->supplierid."</td>
			 <td>".$bar->namasupplier."</td>
			 <td>".$bar->alamat."</td>
			 <td>".$bar->kontakperson."</td>
			 <td>".$bar->kota."</td>
			 <td>".$bar->telepon."</td>		 
			 <td>".$bar->fax."</td>	
			 
			 <td>".$bar->bank."</td>	
			 <td>".$bar->rekening."</td>	
			 <td>".$bar->an."</td>	
			 	 
			 <td>".$bar->email."</td>		 
			 <td>".$bar->npwp."</td>	 
			 <td align=right>".number_format($bar->plafon,0,',','.')."</td>
			 <td></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delSupplier('".$bar->supplierid."','".$bar->namasupplier."');\">
			  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editSupplier('".$bar->supplierid."','".$bar->namasupplier."','".$bar->alamat."','".$bar->kontakperson."','".$bar->kota."','".$bar->telepon."','".$bar->fax."','".$bar->email."','".$bar->npwp."','".$bar->plafon."','".$bar->bank."','".$bar->rekening."','".$bar->an."','');\"></td>
			 </tr>";
			 
	}	 	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
?>