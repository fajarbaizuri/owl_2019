<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$jenissp		=$_POST['jenissp'];
	$karyawanid		=$_POST['karyawanid'];
	$masaberlaku	=$_POST['masaberlaku'];
	$tanggalsp		=tanggalsystem($_POST['tanggalsp']);
	$paragraf1		=$_POST['paragraf1'];
	$paragraf3		=$_POST['paragraf3'];
	$paragraf4		=$_POST['paragraf4'];
	$pelanggaran	=$_POST['pelanggaran'];
	$penandatangan	=$_POST['penandatangan'];
	$jabatan		=$_POST['jabatan'];
	$tembusan1		=$_POST['tembusan1'];
	$tembusan2		=$_POST['tembusan2'];
	$tembusan3		=$_POST['tembusan3'];
	$tembusan4		=$_POST['tembusan4'];
	$method			=$_POST['method'];
	$kodeorg		=substr($_SESSION['empl']['lokasitugas'],0,4);

$t=mktime(0,0,0,substr($tanggalsp,4,2)+$masaberlaku,substr($tanggalsp,6,2),substr($tanggalsp,0,4));
$sampai=date('Ymd',$t);


if($method=='insert')
{
//get number
$potSK=substr($_SESSION['empl']['lokasitugas'],0,4).strtoupper($jenissp).substr($tanggalsp,0,4);
$str="select nomor from ".$dbname.".sdm_suratperingatan
      where  nomor like '".$potSK."%'
	  order by nomor desc limit 1";	  
$notrx=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$notrx=substr($bar->nomor,10,5);
}
$notrx=intval($notrx);
$notrx=$notrx+1;
$notrx=str_pad($notrx, 4, "0", STR_PAD_LEFT);
$notrx=$potSK.$notrx;

	$str="insert into ".$dbname.".sdm_suratperingatan (
		      `nomor`,`jenissp`,`karyawanid`,
			  `pelanggaran`,`tanggal`,`masaberlaku`,
			  `sampai`,`tembusan1`,`tembusan2`,
			  `tembusan4`,`tembusan3`,
			  `kodeorg`, `penandatangan`,`jabatan`,
			  `updateby`,`paragraf1`,`paragraf3`,
			  `paragraf4`
		  ) values(
		   '".$notrx."','".$jenissp."',".$karyawanid.",
		   '".$pelanggaran."',".$tanggalsp.",".$masaberlaku.",
		   ".$sampai.",'".$tembusan1."','".$tembusan2."',
		   '".$tembusan4."','".$tembusan3."','".$kodeorg."',
		   '".$penandatangan."','".$jabatan."',".$_SESSION['standard']['userid'].",
		   '".$paragraf1."','".$paragraf3."','".$paragraf4."'
		  )";  
}
else if($method=='delete')
{
  $nosp=$_POST['nosp'];
	$str="delete from ".$dbname.".sdm_suratperingatan
	      where karyawanid=".$karyawanid." and nomor='".$nosp."'"; 
}
else if($method=='update')
{
  $nosp=$_POST['nosp'];
	$str="update ".$dbname.".sdm_suratperingatan set
			  `jenissp`='".$jenissp."',
			  `pelanggaran`='".$pelanggaran."',
			  `tanggal`=".$tanggalsp.",
			  `masaberlaku`=".$masaberlaku.",
			  `sampai`=".$sampai.",
			  `tembusan1`='".$tembusan1."',
			  `tembusan2`='".$tembusan2."',
			  `tembusan4`='".$tembusan4."',
			  `tembusan3`='".$tembusan3."',
			  `kodeorg`='".$kodeorg."', 
			  `penandatangan`='".$penandatangan."',
			  `jabatan`='".$jabatan."',
			  `updateby`=".$_SESSION['standard']['userid'].",
			  `paragraf1`='".$paragraf1."',
			  `paragraf3`='".$paragraf3."',
			  `paragraf4`='".$paragraf4."'
		where karyawanid=".$karyawanid." and nomor='".$nosp."'"; 	  	
}

if(mysql_query($str))
{}
else
   echo " Gagal:".addslashes(mysql_error($conn));


?>