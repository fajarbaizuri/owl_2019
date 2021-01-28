<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$kodeorg	=$_POST['kodeorgJ'];
$karyawanid	=$_POST['karyawanidJ'];
$periode	=$_POST['periodeJ'];
$dari		=tanggalsystem($_POST['dariJ']);
$sampai		=tanggalsystem($_POST['sampaiJ']);
$diambil	=$_POST['diambilJ'];
$keterangan	=$_POST['keteranganJ'];
$method     =$_POST['method'];

//periksa apakah ada yang tidak benar
//==============================================
if($method=='insert')
{
$strc="select * from ".$dbname.".sdm_cutidt
       where (daritanggal>=".$dari." and daritanggal<=".$sampai.")
	   or (sampaitanggal>=".$dari." and sampaitanggal<=".$sampai.")
	   or (daritanggal<=".$dari." and sampaitanggal>=".$sampai.")";
	if(mysql_num_rows(mysql_query($strc))>0)
	{
		echo " Error ".$_SESSION['lang']['irisan'];
		exit(0);
	}	
	else if($sampai<$dari)
	{
		echo " Error < >";
		exit(0);
	} 
}
  
//===============================================

	if($diambil==''){
		$diambil=0;
	}
	
	switch($method)
	{
	case 'delete':	
		$str="delete from ".$dbname.".sdm_cutidt
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'
			   and daritanggal='".$_POST['dariJ']."'";
		break;	   
	case 'insert':
		$str="insert into ".$dbname.".sdm_cutidt 
		      (kodeorg,karyawanid,periodecuti,daritanggal,
			  sampaitanggal,jumlahcuti,keterangan
			  )
		      values('".$kodeorg."',".$karyawanid.",
			  '".$periode."','".$dari."','".$sampai."',
			  ".$diambil.",'".$keterangan."'
			  )";
		break;
	default:
	   break;					
	}
	if(mysql_query($str))
		{
		//ambil sum jumlah diambil dan update table header
		$strx="select sum(jumlahcuti) as diambil from ".$dbname.".sdm_cutidt
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'";
			   
		$diambil=0;
		$resx=mysql_query($strx);
		while($barx=mysql_fetch_object($resx))
		{
			$diambil=$barx->diambil;
		}
                if($diambil=='')
                    $diambil=0;
		$strup="update ".$dbname.".sdm_cutiht set diambil=".$diambil.",sisa=(hakcuti-".$diambil.")	
		       where kodeorg='".$kodeorg."'
			   and karyawanid=".$karyawanid."
			   and periodecuti='".$periode."'";

		mysql_query($strup);	   
		}
	else
		{echo " Gagal,".addslashes(mysql_error($conn));}

?>
