<?
require_once('master_validation.php');
require_once('config/connection.php');

$kodekegiatan=$_POST['kodekegiatan'];
$kodekategori=$_POST['kodekategori'];
$method=$_POST['method'];


switch($method)
{
case 'update':	
	if($kodekategori==''){
	echo " Gagal,Silakan Pilih kategori terlebih dahulu.";
	exit;
	}
	$str="update ".$dbname.".vhc_kegiatan set kelompok='".$kodekategori."'
	       where kodekegiatan='".$kodekegiatan."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	if($kodekategori==''){
	echo " Gagal,Silakan Pilih kategori terlebih dahulu.";
	exit;
	}
	
	$str="delete from ".$dbname.".vhc_kegiatan 
	where kodekegiatan='".$kodekegiatan."'";
	if(mysql_query($str))
	{
		$str="insert into ".$dbname.".vhc_kegiatan (kodekegiatan,kelompok)
	      values('".$kodekegiatan ."','".$kodekategori."')";
		
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
		
		
	}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	
	break;
case 'delete':
	$str="delete from ".$dbname.".vhc_kegiatan 
	where kodekegiatan='".$kodekegiatan."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".vhc_kegiatan_vw order by kodekegiatan";
if($res1=mysql_query($str1))
{
	echo"<table class=sortable cellspacing=1 border=0 style='width:1024px;'>
	     <thead>
		 <tr class=rowheader>
			<td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>
			<td>kategori</td>
            <td style='width:250px;'>".$_SESSION['lang']['namakegiatan']."</td>
			<td>Satuan</td>
			<td>Akun Alokasi</td>
			<td style='width:30px;'>Aksi</td></tr>
		 </thead>
		 <tbody>";
	while($bar2=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
					<td align=center>".$bar2->kodekegiatan."</td>
                    <td>".$bar2->kelompoknm."</td>
					<td style='width:250px;'>".$bar2->namakegiatan."</td>
                    <td>".$bar2->satuan."</td>    
					<td>".$bar2->noakun."</td>    
                    <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar2->kodekegiatan."','".$bar2->kelompok."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
	
}
?>
