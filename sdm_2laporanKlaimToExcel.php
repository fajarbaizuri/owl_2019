<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

   $periode=$_GET['periode'];
   if($periode=='')
      $periode=date('Y-d');
   
 $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id
	  where a.periode='".$periode."'
	  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  order by a.updatetime desc, a.tanggal desc";
	
	$stream="
	  Laporan Klaim Pengobatan Periode ".$periode."
	  <table border=1>
	  <thead>
	    <tr>
		  <td bgcolor=#dedede>No</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['notransaksi']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['periode']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['tanggal']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['namakaryawan']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['pasien']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['rumahsakit']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['nilaiklaim']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['dibayar']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['diagnosa']."</td>
		  <td bgcolor=#dedede>".$_SESSION['lang']['keterangan']."</td>
		</tr>
	  </thead>
	  <tbody>";  
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
				  
		   $stream.="<tr>
		      <td>".$no."</td>
			  <td>".$bar->notransaksi."</td>
			  <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
			  <td>".tanggalnormal($bar->tanggal)."</td>
			  <td>".$bar->namakaryawan."</td>
			  <td>".$pasien."</td>
			  <td>".$bar->namars."[".$bar->kota."]"."</td>
			  <td>".$bar->kodebiaya."</td>
			  <td align=right>".$bar->totalklaim."</td>
			  <td align=right>".$bar->jlhbayar."</td>
			  <td>".$bar->ketdiag."</td>
			  <td>".$bar->keterangan."</td>
			</tr>";	  	
	 }
$stream.="</tbody>
	 <tfoot>
	 </tfoot>
	 </table>";	 
//write exel   
$nop_="LaporanKlaimPengobatan-".$periode;
	if(strlen($stream)>0)
	{
	if ($handle = opendir('tempExcel')) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != "..") {
	            @unlink('tempExcel/'.$file);
	        }
	    }	
	   closedir($handle);
	}
	 $handle=fopen("tempExcel/".$nop_.".xls",'w');
	 if(!fwrite($handle,$stream))
	 {
	  echo "<script language=javascript1.2>
	        parent.window.alert('Can't convert to excel format');
	        </script>";
	   exit;
	 }
	 else
	 {
	  echo "<script language=javascript1.2>
	        window.location='tempExcel/".$nop_.".xls';
	        </script>";
	 }
	closedir($handle);
	}		 
?>
