<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$kelompokvhc=$_POST['kelompokvhc'];
$jenisvhc=$_POST['jenisvhc'];
$kodeorg=$_POST['kodeorg'];
$method=$_POST['method'];
$kodevhc=$_POST['kodevhc'];
$tahunperolehan=$_POST['tahunperolehan'];
$noakun=$_POST['noakun'];
$beratkosong=$_POST['beratkosong'];
$nomorrangka=$_POST['nomorrangka'];
$nomormesin=$_POST['nomormesin'];
$detailvhc=$_POST['detailvhc'];
$kodebarang=$_POST['kodebarang'];
$kepemilikan=$_POST['kepemilikan'];
$kodetraksi=$_POST['kodetraksi'];

$nopol=$_POST['nopol'];
$tglkir=tanggalsystem($_POST['tglkir']);
$tglstnk=tanggalsystem($_POST['tglstnk']);
$tglpajak=tanggalsystem($_POST['tglpajak']);
$tglasuransi=tanggalsystem($_POST['tglasuransi']);


if($beratkosong=='')
  $beratkosong=0;

$strx="select 1=1";
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".vhc_5master where kodevhc='".$kodevhc."'";
		break;
		case 'update':
		 if ($kelompokvhc=='AB'){
			 $satuk='HM';
		 }else if ($kelompokvhc=='KD'){
			$satuk='KMH';
		 }else{
			$satuk='JAM';
		 }
		 
		   $strx="update ".$dbname.".vhc_5master set jenisvhc='".$jenisvhc."',
		          kelompokvhc='".$kelompokvhc."', noakun='".$noakun."' ,
		          kodeorg='".$kodeorg."', tahunperolehan='".$tahunperolehan."',
		          beratkosong='".$beratkosong."', nomorrangka='".$nomorrangka."' ,
				  nomormesin='".$nomormesin."',detailvhc='".$detailvhc."',
				  kodebarang='".$kodebarang."',kepemilikan=".$kepemilikan.",nopol='".$nopol."',
				  masaberlakukir='".$tglkir."',masaberlakustnk='".$tglstnk."',masaberlakupajak='".$tglpajak."',masaberlakuasuransi='".$tglasuransi."',
                                  kodetraksi='".$kodetraksi."', satuk='".$satuk."'
				  where kodevhc='".$kodevhc."'";		
		break;	
		case 'insert':
		 if ($kelompokvhc=='AB'){
			 $satuk='HM';
		 }else if ($kelompokvhc=='KD'){
			$satuk='KMH';
		 }else{
			$satuk='JAM';
		 }
			$strx="insert into ".$dbname.".vhc_5master(
			       kodevhc,kelompokvhc,noakun,kodeorg,jenisvhc,
				   tahunperolehan,beratkosong,nomorrangka,
				   nomormesin,detailvhc,kodebarang,kepemilikan,kodetraksi,nopol,masaberlakukir,masaberlakustnk,masaberlakupajak,masaberlakuasuransi,satuk)
			values('".$kodevhc."','".$kelompokvhc."','".$noakun."',
			       '".$kodeorg."','".$jenisvhc."',".$tahunperolehan.",
				   ".$beratkosong.",'".$nomorrangka."','".$nomormesin."',
				   '".$detailvhc."','".$kodebarang."',".$kepemilikan.",
                                   '".$kodetraksi."','".$nopol."','".$tglkir."','".$tglstnk."','".$tglpajak."','".$tglasuransi."','".$satuk."')";	   
		break;
		default:
        break;	

	}
  if(mysql_query($strx))
  {}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	

$where='1=1';
if($kodeorg!='')
   $where.=" and kodeorg='".$kodeorg."' ";
if($kelompokvhc!='')
   $where.=" and kelompokvhc='".$kelompokvhc."' ";   
if($jenisvhc!='')
   $where.=" and jenisvhc='".$jenisvhc."' ";
   
$str="select * from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSON['lokasitugas']."%' and ".$where." order by kodeorg,jenisvhc";

$res=mysql_query($str);
//echo $str.mysql_error($conn);
	$no=0;
	while($bar1=mysql_fetch_object($res))
	{
		$no+=1;
		$str="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar1->kodebarang."'";
		$res1=mysql_query($str);
		$namabarang='';
		while($bar=mysql_fetch_object($res1))
		{
			$namabarang=$bar->namabarang;
		}
		if($bar1->kepemilikan==1)
		{
	      $dptk=$_SESSION['lang']['miliksendiri'];	
		}
		else
		{
			$dptk=$_SESSION['lang']['sewa'];
		}
		echo"<tr class=rowcontent>
		     <td>".$no."</td>
		     <td>".$bar1->kodeorg."</td>
			 <td>".$bar1->kelompokvhc."</td>				 
			 <td>".$bar1->jenisvhc."</td>			 		
			 <td>".$bar1->kodevhc."</td>
			 <td>".$namabarang."</td>
			 <td>".$bar1->tahunperolehan."</td>
			 <td>".$bar1->noakun."</td>
			 <td>".$bar1->beratkosong."</td>		
			 <td>".$bar1->nomorrangka."</td>	
			 <td>".$bar1->nomormesin."</td> 
			 <td>".$bar1->detailvhc."</td> 	
			 <td>".$dptk."</td> 
			 <td>".$bar1->kodetraksi."</td>    
			 <td>".$bar1->nopol."</td> ";
						 
						 
						 
 			// kir
			 if($bar1->masaberlakukir==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo "<td>".tanggalnormal($bar1->masaberlakukir)."</td>  ";
			 }
			 
			 //stnk
			 if($bar1->masaberlakustnk==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo "<td>".tanggalnormal($bar1->masaberlakustnk)."</td>  ";
			 }
			//pajak
			 if($bar1->masaberlakupajak==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo " <td>".tanggalnormal($bar1->masaberlakupajak)."</td>  ";
			 }
			 
			 //asuransi
			 if($bar1->masaberlakuasuransi==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo " <td>".tanggalnormal($bar1->masaberlakuasuransi)."</td>  ";
			 }	
			 
			 
			 echo"
			 		  
			 <td>
			     <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillMasterField('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."','".$bar1->noakun."','".$bar1->beratkosong."','".$bar1->nomorrangka."','".$bar1->nomormesin."','".$bar1->tahunperolehan."','".$bar1->kodebarang."','".$bar1->kepemilikan."','".$bar->kodetraksi."','".$bar1->nopol."','".tanggalnormal($bar1->masaberlakukir)."','".tanggalnormal($bar1->masaberlakustnk)."','".tanggalnormal($bar1->masaberlakupajak)."','".tanggalnormal($bar1->masaberlakuasuransi)."');\">
			      <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"deleteMasterVhc('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."');\">
			 </td></tr>";
	}	   
?>
