<?
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$tipekaryawan=$_POST['tipekaryawan'];
$kodebarang=$_POST['kodebarang'];
$jumlah=$_POST['jumlah'];
$hargasatuan=$_POST['hargasatuan'];
$keterangan=$_POST['keterangan'];
$satuan=$_POST['satuan'];
$stpajak=$_POST['statuspajak'];
$method=$_POST['method'];
		

if($jumlah=='')
   $jumlah=0;
if($hargasatusn=='')
   $hargasatusn=0;

$total=$hargasatuan*$jumlah;
switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5natura set 
	       jumlah=".$jumlah.",
	       rupiahkonversi=".$hargasatuan.",
		   keterangan='".$keterangan."',
		   totalrupiah=".$total."
	       where kodeorg='".$kodeorg."' and tipekaryawan=".$tipekaryawan."
		   and kodebarang='".$kodebarang."' and statuspajak='".$stpajak."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5natura 
	      (kodeorg,tipekaryawan,kodebarang,jumlah,satuan,rupiahkonversi,keterangan,totalrupiah,statuspajak)
	      values('".$kodeorg."',".$tipekaryawan.",'".$kodebarang."',".$jumlah.",'".$satuan."',".$hargasatuan.",'".$keterangan."',".$total.",'".$stpajak."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5natura
	 where kodeorg='".$kodeorg."' and tipekaryawan=".$tipekaryawan."
	 and kodebarang='".$kodebarang."' and statuspajak='".$stpajak."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}


	$str1="select a.*,b.tipe
	     from ".$dbname.".sdm_5natura a left join
		 ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id
		   where LEFT(kodeorg,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  order by kodebarang"; 
	$res1=mysql_query($str1);
	while($bar1=mysql_fetch_object($res1))
	{
		$strx="select namabarang from ".$dbname.".log_5masterbarang where
		       kodebarang='".$bar1->kodebarang."'";
		$resx=mysql_query($strx);
		$namabarang='';
		while($barx=mysql_fetch_object($resx))
		{
			$namabarang=$barx->namabarang;
		}	   
		$strx="select nama from ".$dbname.".sdm_5statuspajak where
		       kode='".$bar1->statuspajak."'";
		$resx=mysql_query($strx);
		$stp='';
		while($barx=mysql_fetch_object($resx))
		{
			$stp=$barx->nama;
		}
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeorg."</td>
				   <td>".$bar1->tipe."</td>
				   <td>".$stp."</td>
				   <td>".$namabarang."</td>
				   <td align=center>".$bar1->jumlah."</td>
				   <td align=center>".$bar1->satuan."</td>
				   <td align=center>".number_format($bar1->totalrupiah,2,'.',',')."</td>
				   <td align=center>".$bar1->keterangan."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tipekaryawan."','".$bar1->kodebarang."','".$bar1->jumlah."','".$bar1->satuan."','".$bar1->rupiahkonversi."','".$bar1->keterangan."','".$bar1->statuspajak."');\"></td></tr>";
	}				 

?>
