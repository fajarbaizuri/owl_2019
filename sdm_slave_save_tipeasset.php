<?
require_once('master_validation.php');
require_once('config/connection.php');

$kodetipe=$_POST['kodetipe'];
$namatipe=$_POST['namatipe'];
$noakun=$_POST['noakun'];
$noakunak=$_POST['noakunak'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5tipeasset set namatipe='".$namatipe."'
	       ,noakun='".$noakun."',akunak='".$noakunak."'
	       where kodetipe='".$kodetipe."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5tipeasset (kodetipe,namatipe,noakun,akunak)
	      values('".$kodetipe."','".$namatipe."','".$noakun."','".$noakunak."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5tipeasset 
	where kodetipe='".$kodetipe."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$stru="select noakun,namaakun from ".$dbname.".keu_5akun";
$res=mysql_query($stru);
while($bar=mysql_fetch_object($res))
{
    $namaakun[$bar->noakun]=$bar->namaakun;
}    
$str1="select * from ".$dbname.".sdm_5tipeasset
		   order by namatipe";

if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent>
		     <td align=center>".$bar1->kodetipe."</td>
			 <td>".$bar1->namatipe."</td>
			 <td>".$namaakun[$bar1->noakun]."</td>
                                                             <td>".$namaakun[$bar1->akunak]."</td>
			 <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodetipe."','".$bar1->namatipe."','".$bar1->noakun."','".$bar1->akunak."');\"></td></tr>";
}	 
}
?>
