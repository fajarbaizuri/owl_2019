<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

	$kodeorg	  =$_POST['kodeorg'];
    $tanggal	  =tanggalsystem($_POST['tanggal']);
	$sisatbskemarin=$_POST['sisatbskemarin'];
	$tbsmasuk     =$_POST['tbsmasuk'];
	$tbsdiolah    =$_POST['tbsdiolah'];
	$sisahariini  =$_POST['sisahariini'];
	
	$oer     	  =$_POST['oer'];
	$kadarair     =$_POST['kadarair'];
	$ffa     	  =$_POST['ffa'];
	$dirt     	  =$_POST['dirt'];

	$oerpk     	  =$_POST['oerpk'];
	$kadarairpk   =$_POST['kadarairpk'];
	$ffapk     	  =$_POST['ffapk'];
	$dirtpk       =$_POST['dirtpk'];
	
	if(isset($_POST['del']))
	  {
			$strx="delete from ".$dbname.".pabrik_produksi 
			       where kodeorg='".$kodeorg."' 
				   and tanggal='".$_POST['tanggal']."'";   
	  }
	  else
	  {

			$strx="insert into ".$dbname.".pabrik_produksi
                   (kodeorg,tanggal,sisatbskemarin,
				    tbsmasuk,tbsdiolah,sisahariini,
				    oer,ffa,kadarair,kadarkotoran,
					oerpk,ffapk,kadarairpk,kadarkotoranpk,
					karyawanid)
					values('".$kodeorg."',".$tanggal.",".$sisatbskemarin.",
					".$tbsmasuk.",".$tbsdiolah.",".$sisahariini.",
					".$oer.",".$ffa.",".$kadarair.",".$dirt.",
					".$oerpk.",".$ffapk.",".$kadarairpk.",".$dirtpk.",
					".$_SESSION['standard']['userid'].")";			   
	  }

  if(mysql_query($strx))
  {
	
			$str="select a.* from ".$dbname.".pabrik_produksi a 
			      order by a.tanggal desc limit 20";
			$res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
				 echo"<tr class=rowcontent>
					   <td>".$bar->kodeorg."</td>
					   <td>".tanggalnormal($bar->tanggal)."</td>
					   <td align=right>".number_format($bar->sisatbskemarin,0,'.',',')."</td>
					   <td align=right>".number_format($bar->tbsmasuk,0,'.',',')."</td>
					   <td align=right>".number_format($bar->tbsdiolah,0,'.',',.')."</td>
					   <td align=right>".number_format($bar->sisahariini,0,'.',',')."</td>
					   
					   <td align=right>".number_format($bar->oer,0,'.',',')."</td>
					   <td align=right>".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
					   <td align=right>".$bar->ffa."</td>
					   <td align=right>".$bar->kadarkotoran."</td>
					   <td align=right>".$bar->kadarair."</td>
					   
					   <td align=right>".number_format($bar->oerpk,0,'.',',')."</td>
					   <td align=right>".(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
					   <td align=right>".$bar->ffapk."</td>
					   <td align=right>".$bar->kadarkotoranpk."</td>
					   <td align=right>".$bar->kadarairpk."</td>
				   <td>
				     <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delProduksi('".$bar->kodeorg."','".$bar->tanggal."','".$bar->kodebarang."');\">
				   </td>
				  </tr>";	
			}
}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	
?>
