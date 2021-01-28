<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

   $periode=$_POST['periode'];
   
 $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id
	  where a.periode='".$periode."'
	  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  order by a.updatetime desc, a.tanggal desc";
	  
$res=mysql_query($str);
	  $no=0;
  while($bar=mysql_fetch_object($res))
  {
		   $no+=1;
		   
		   $pasien='';
		   //get hubungan keluarga
		   $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
		          where nomor=".$bar->ygsakit;
			$resu=mysql_query($stru);
			while($baru=mysql_fetch_object($resu))
			{
				$pasien=$baru->hubungankeluarga;
			}
		if($pasien=='')
		   $pasien='AsIs';	
				  
		   echo"<tr class=rowcontent>
		      <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)>
		       </td><td>".$no."</td>
			  <td>".$bar->notransaksi."</td>
			  <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
			  <td>".tanggalnormal($bar->tanggal)."</td>
			  <td>".$bar->namakaryawan."</td>
			  <td>".$pasien."</td>
			  <td>".$bar->namars."[".$bar->kota."]"."</td>
			  <td>".$bar->kodebiaya."</td>
			  <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>
			  <td align=right>".number_format($bar->jlhbayar,2,'.',',')."</td>
			  <td>".$bar->ketdiag."</td>
			  <td>".$bar->keterangan."</td>
			</tr>";	  	
	 }
?>
