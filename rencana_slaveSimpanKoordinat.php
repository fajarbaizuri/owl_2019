<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$lokasi =$_POST['lokasi'];
$dpl	=$_POST['dpl'];
$jls	=$_POST['jls'];
$mls	=$_POST['mls'];
$dls 	=$_POST['dls'];
$jbt	=$_POST['jbt'];
$mbt	=$_POST['mbt'];
$dbt	=$_POST['dbt'];
$method	=$_POST['method'];	

switch($method)
{

case 'update':	
	$str="update ".$dbname.".rencana_koordinat set
	      ldrajat=".$jls.",lmenit=".$mls.",
		  ldetik=".$dls.",bdrajat=".$jbt.",
		  bmenit=".$mbt.",bdetik=".$dbt.",
		  dpl=".$dpl."
	       where nama='".$lokasi."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".rencana_koordinat (nama,ldrajat,lmenit,ldetik,
	      bdrajat,bmenit,bdetik,dpl)
	      values('".$lokasi."',".$jls.",".$mls.",
		  ".$dls.",".$jbt.",".$mbt.",".$dbt.",
		  ".$dpl.")";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".rencana_koordinat
	where nama='".$lokasi."' and ldrajat=".$jls." and lmenit=".$mls." and
	ldetik=".$dls." and bdrajat=".$jbt." and bmenit=".$mbt." and bdetik=".$dbt;
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".rencana_koordinat where nama='".$lokasi."'";
if($res1=mysql_query($str1))
{
	$no=0;
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		   <td>".$no."</td>
		    <td>".$bar1->nama."</td>
			<td align=right>".$bar1->ldrajat."</td>
			<td align=right>".$bar1->lmenit."</td>
			<td align=right>".$bar1->ldetik."</td>
			<td align=right>".$bar1->bdrajat."</td>
			<td align=right>".$bar1->bmenit."</td>
			<td align=right>".$bar1->bdetik."</td>
			<td align=right>".$bar1->dpl."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldKoordinat('".$bar1->nama."','".$bar1->ldrajat."','".$bar1->lmenit."','".$bar1->ldetik."','".$bar1->bdrajat."','".$bar1->bmenit."','".$bar1->bdetik."','".$bar1->dpl."');\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delRencanaKoordinat('".$bar1->nama."','".$bar1->ldrajat."','".$bar1->lmenit."','".$bar1->ldetik."','".$bar1->bdrajat."','".$bar1->bmenit."','".$bar1->bdetik."');\"></td></tr>";
	}	 
}
?>
