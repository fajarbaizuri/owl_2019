<?
require_once('master_validation.php'); 
require_once('config/connection.php');
	
	$Xgudang=$_POST['Xgudang'];
	$Xbarang=$_POST['Xbarang'];
	$Xsts=$_POST['Xsts'];
	$Xthn=$_POST['Xthn'];
	$Xlifetime=$_POST['Xlifetime'];
	$Xqtythn=$_POST['Xqtythn'];
	$Xqtymin=$_POST['Xqtymin'];
	$method=$_POST['method'];
	
	$Xgudanglm=$_POST['Xgudanglm'];
	$Xbaranglm=$_POST['Xbaranglm'];
	$Xthnlm=$_POST['Xthnlm'];


	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".log_5monitoring where `tahun`='".$Xthn."' and `kodegudang`='".$Xgudang."' and `kodebarang`='".$Xbarang."' ";
		break;
		case 'update':
		
			$strx="update ".$dbname.".log_5monitoring set 
			       tahun='".$Xthn."',
			       kodegudang='".$Xgudang."',kodebarang='".$Xbarang."',status='".$Xsts."',lifetime='".$Xlifetime."',qtythn='".$Xqtythn."',qtymin='".$Xqtymin."'
				   where tahun='".$Xthnlm."' and
			       kodegudang='".$Xgudanglm."' and kodebarang='".$Xbaranglm."' ";
		
		break;	
		case 'insert':
			$strx="insert into ".$dbname.".log_5monitoring(
			       tahun,kodegudang,kodebarang,status,lifetime,qtythn,qtymin)
			values('".$Xthn."','".$Xgudang."','"
			         .$Xbarang."','".$Xsts."','".$Xlifetime."','".$Xqtythn."','".$Xqtymin."')";	   
		break;
		default:
        break;	
	}
  if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
		exit; 
	}	
	
if ($_SESSION['empl']['lokasitugas'] == 'FBHO'){
		$str="select * from ".$dbname.".log_5monitoring_vw order by tahun desc,gudang asc, namabarang asc";
}else{
		$str="select * from ".$dbname.".log_5monitoring_vw where gudang like '".$_SESSION['empl']['lokasitugas']."%' order by tahun desc,gudang asc, namabarang asc";
}

$res=mysql_query($str);
$no=0;	  
while($bar=mysql_fetch_object($res))
{
 $no+=1;	
  echo"<tr class=rowcontent>
	   <td>".$no."</td>
	   <td>".$bar->tahun."</td>
	   <td>".$bar->gudang."</td>
	   <td>".$bar->kodebarang."</td>
	   <td>".$bar->namabarang."</td>
	   <td style=\"text-align:center;\">".$bar->satuan."</td>
	   <td style=\"text-align:center;\">".$bar->status."</td>
	   <td style=\"text-align:center;\">".$bar->lifetime."</td>
	   <td style=\"text-align:center;\">".$bar->qtythn."</td>
	   <td style=\"text-align:center;\">".$bar->qtymin."</td>
		  <td>
		      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->tahun."','".$bar->kodegudang."','".$bar->kodebarang."','".$bar->kdstatus."','".$bar->lifetime."','".$bar->qtythn."','".$bar->qtymin."'
			  );\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delMonitoring('".$bar->tahun."','".$bar->kodegudang."','".$bar->kodebarang."');\">
		  </td>
	   
	  </tr>";	
}      


?>
