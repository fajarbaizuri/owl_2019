<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$namalokasi =$_POST['namalokasi'];
$desa		=$_POST['desa'];
$kecamatan	=$_POST['kecamatan'];
$kabupaten	=$_POST['kabupaten'];
$provinsi 	=$_POST['provinsi'];
$negara		=$_POST['negara'];
$kontak		=$_POST['kontak'];
$method		=$_POST['method'];
$peruntukan	=$_POST['peruntukan'];
$luas=$_POST['luas'];
$tanggal	=tanggalsystem($_POST['tanggal']);
switch($method)
{
case 'update':	
	$str="update ".$dbname.".rencana_lahan set desa='".$desa."',
	      kecamatan='".$kecamatan."',kabupaten='".$kabupaten."',
		  provinsi='".$provinsi."',negara='".$negara."',
		  kontak='".$kontak."',peruntukanlahan='".$peruntukan."',
		  tanggalmulai=".$tanggal.",luas='".$luas."'
	       where nama='".$namalokasi."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".rencana_lahan (nama,desa,kecamatan,kabupaten,
	      provinsi,negara,kontak,peruntukanlahan,tanggalmulai,luas)
	      values('".$namalokasi."','".$desa."','".$kecamatan."',
		  '".$kabupaten."','".$provinsi."','".$negara."','".$kontak."',
		  '".$peruntukan."',".$tanggal.",'".$luas."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".rencana_lahan
	where nama='".$namalokasi."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".rencana_lahan order by tanggalmulai desc";
if($res1=mysql_query($str1))
{
	$no=0;
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		   <td>".$no."</td>
		    <td>".$bar1->nama."</td>
			<td>".tanggalnormal($bar1->tanggalmulai)."</td>
			<td>".$bar1->peruntukanlahan."</td>
			<td>".$bar1->desa."</td>
			<td>".$bar1->kecamatan."</td>
			<td>".$bar1->kabupaten."</td>
			<td>".$bar1->provinsi."</td>
			<td>".$bar1->negara."</td>
			<td>".$bar1->kontak."</td>
			<td align=right>".$bar1->luas."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->peruntukanlahan."','".$bar1->desa."','".$bar1->kecamatan."','".$bar1->kabupaten."','".$bar1->provinsi."','".$bar1->negara."','".tanggalnormal($bar1->tanggalmulai)."','".$bar1->nama."','".$bar1->kontak."','".$bar1->luas."');\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delRencana('".$bar1->nama."');\"></td></tr>";
	}	 
}
?>
