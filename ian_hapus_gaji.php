<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

$karyawanid	=$_POST['karyawanid'];
$tahun		=$_POST['tahun'];

$str="delete from ".$dbname.".test_gaji where karyawanid=".$karyawanid." and tahun=".$tahun;
if(mysql_query($str))
{
  $str="select b.namakaryawan,a.tahun,a.gaji from ".$dbname.".test_gaji a left join ".$dbname.".datakaryawan b
        on a.karyawanid=b.karyawanid order by namakaryawan,tahun";
  $res=mysql_query($str);
  $result='';
  while($bar=mysql_fetch_object($res))
  {
       $result.="<tr class=rowcontent><td>".$bar->namakaryawan."</td>
<td>".$bar->tahun."</td>
<td>".number_format($bar->gaji,2,",",".")."</td>	
<td><img src=images/delete1.jpg onclick=hapusGaji('".$bar->karyawanid."','".$bar->tahun."') title='Hapus' style='cursor:pointer'></td>
										</tr>";

  }
  echo $result;
}
else
{
  echo " Error ".mysql_error($conn);
}

?>
