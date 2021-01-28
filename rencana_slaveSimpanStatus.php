<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
	

$lokasi =$_POST['lokasi'];
$pic	=$_POST['pic'];
$status	=$_POST['status'];
$keterangan	=$_POST['keterangan'];
$method =$_POST['method'];
$tanggal=$_POST['tanggal'];

switch($method)
{

case 'update':	
	$str="update ".$dbname.".rencana_sejarah set
	      pic='".$pic."',
		  status='".$status."',keterangan='".$keterangan."'
	       where nama='".$lokasi."' and tanggal=".tanggalsystem($tanggal);   
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".rencana_sejarah (nama,tanggal,pic,status,
	      keterangan)
	      values('".$lokasi."',".tanggalsystem($tanggal).",'".$pic."',
		  '".$status."','".$keterangan."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".rencana_sejarah
	where nama='".$lokasi."' and tanggal='".$tanggal."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}

$str1="select * from ".$dbname.".rencana_sejarah where nama='".$lokasi."'";
if($res1=mysql_query($str1))
{
	$no=0;
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		   <td>".$no."</td>
		    <td>".$bar1->nama."</td>
			<td align=right>".tanggalnormal($bar1->tanggal)."</td>
			<td>".str_replace("GAGAL","G A G A L",$bar1->status)."</td>
			<td>".$bar1->pic."</td>
			<td id=rem".$no.">".$bar1->keterangan."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldStatus('".$bar1->nama."','".tanggalnormal($bar1->tanggal)."','".$bar1->status."','".$bar1->pic."','rem".$no."');\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delRencanaStatus('".$bar1->nama."','".$bar1->tanggal."');\"></td></tr>";
	}	 
}
?>
