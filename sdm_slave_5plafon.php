
<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('master_validation.php');
require_once('config/connection.php');


$method=$_POST['method'];
$tahun=$_POST['tahun'];
$nama=$_POST['nama'];
$jumlah=$_POST['jumlah'];
$optNm=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');


$tglskarang=date("Y-m-d");
		$tgls=substr($tglskarang,8,2);
		$blns=substr($tglskarang,5,2);		
		$thns=substr($tglskarang,0,4);
		
		//echo $thns;
		
		

switch($method)
{
	
	##########case insert
	case 'insert':
		
		$sql="insert into ".$dbname.".sdm_5plafon (`tahun`,`karyawanid`,`jumlah`)
		values ('".$tahun."','".$nama."','".$jumlah."')";
		if(mysql_query($sql))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	##########case delete
	case 'delete':
		$sql="delete from ".$dbname.".sdm_5plafon where tahun='".$tahun."' and karyawanid='".$nama."' ";
		if(mysql_query($sql))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
	#####LOAD DETAIL DATA	
	case 'loadData';	
		$no=0;
		$str="select * from ".$dbname.".sdm_5plafon where tahun='".$thns."' ";
		//echo $str;
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=right>".$bar1['tahun']."</td>";
			$tab.="<td align=left>".$optNm[$bar1['karyawanid']]."</td>";
			$tab.="<td align=right>".number_format($bar1['jumlah'])."</td>";
			$tab.="<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['tahun']."','".$bar1['karyawanid']."');\" ></td>";
		echo $tab;
		}
	break;


}
?>