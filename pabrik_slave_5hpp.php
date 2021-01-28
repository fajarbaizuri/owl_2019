<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$produk=$_POST['produk'];
$jumlah=$_POST['jumlah'];
$bulan=$_POST['bulan'];
$tahun=$_POST['tahun'];
$periode=$tahun.'-'.$bulan;
$periodedel=$_POST['periodedel'];
$thnsort=$_POST['thnsort'];
$method=$_POST['method'];
?>

<?php
switch($method)
{
	case 'insert':
		$str="insert into ".$dbname.".pabrik_porsihpp (`kodeproduk`,`periode`,`jumlah`)
		values ('".$produk."','".$periode."','".$jumlah."')";
		//exit("Error.$sDel2");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
		
case'loadData':
		$sort='';
                if($thnsort!='')
                {
                    $sort=" where periode like '%".$thnsort."%' ";
                }	
		$no=0;
		$str="select * from ".$dbname.".pabrik_porsihpp ".$sort." ";
		//echo $str;
		$str2=mysql_query($str) or die(mysql_error());
		//$res1=mysql_query($str2);
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$bar1['kodeproduk']."</td>";
			$tab.="<td align=right>".$bar1['periode']."</td>";
			$tab.="<td align=right>".$bar1['jumlah']." %</td>";
			$tab.="<td align=center><img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$bar1['kodeproduk']."','".$bar1['periode']."');\"></td>";
		echo $tab;
		}
    break;

	case 'delete':
	//exit("Error:hahaha");
		$str="delete from ".$dbname.".pabrik_porsihpp where kodeproduk='".$produk."' and periode='".$periodedel."'";
		//exit("Error.$str");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>
