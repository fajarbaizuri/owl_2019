<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

//exit("Error:ASD");

	$kodeorg	  =$_POST['kodeorg'];
    $tanggal	  =tanggalsystem($_POST['tanggal']);
	$sisatbskemarin=$_POST['sisatbskemarin'];
	$tbsmasuk     =$_POST['tbsmasuk'];
	$tbsdiolah    =$_POST['tbsdiolah'];
	$sisahariini  =$_POST['sisa'];
	//exit($sisahariini);
	
	$oer     	  =$_POST['oercpo'];
	$kadarair     =$_POST['kadaraircpo'];
	$ffa     	  =$_POST['ffacpo'];
	$dirt     	  =$_POST['dirtcpo'];

	$oerpk     	  =$_POST['oerpk'];
	$kadarairpk   =$_POST['kadarairpk'];
	$ffapk     	  =$_POST['ffapk'];
	$dirtpk       =$_POST['dirtpk'];
	$method=$_POST['method'];
	
	switch($method)
	{
		case 'update':
		//exit("Error:MASUK");
		$indra="update ".$dbname.".pabrik_produksi set
                   sisatbskemarin=".$sisatbskemarin.",tbsmasuk=".$tbsmasuk.",tbsdiolah=".$tbsdiolah.",sisahariini=".$sisahariini.",
				    oer=".$oer.",ffa=".$ffa.",kadarair=".$kadarair.",kadarkotoran=".$dirt.",oerpk=".$oerpk.",ffapk=".$ffapk.",
					kadarairpk=".$kadarairpk.",kadarkotoranpk=".$dirtpk.",karyawanid=".$_SESSION['standard']['userid']." 
					where kodeorg='".$kodeorg."' and tanggal='".tanggalsystem($_POST['tanggal'])."'";
					//exit("Error:$indra");
		if(mysql_query($indra))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		
		  if(mysql_query($indra))
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
				    <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar->kodeorg."','".$bar->tanggal."','".$bar->sisatbskemarin."'
		   ,'".$bar->tbsmasuk."','".$bar->tbsdiolah."','".$bar->sisahariini."','".$bar->oer."','".$bar->ffa."','".$bar->kadarkotoran."','".$bar->kadarair."','".$bar->oerpk."',
		   '".$bar->ffapk."','".$bar->kadarkotoranpk."','".$bar->kadarairpk."');\">
				   </td>
				  </tr>";	
			}
		}	
  	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
		
		
	break;
	
	
	
	case'cektanggal':

		//exit("Error:$tanggal");
		
		$tanggalkemarin=$tanggal-1;
		//exit("Error:$tanggalkemarin");
		$atgl="select tanggal from ".$dbname.".pabrik_produksi where tanggal='".$tanggalkemarin."'";
		$btgl=mysql_query($atgl) or die (mysql_error($conn));
		$ctgl=mysql_fetch_assoc($btgl);
			$tanggalcek=$ctgl['tanggal'];
			
		$tanggalformat=tanggalnormal($tanggalkemarin);	
		if($ctgl['tanggal']=='')
		exit("Error : Tanggal untuk $tanggalformat belum di input, silahkan tekan 'batal' untuk men-load frame");

		
		//echo $haha;

	break;
	
	}
	
	
	
?>	


