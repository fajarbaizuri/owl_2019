<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
//========================
 
  $notransaksi=$_POST['notransaksi'];
  $gudang=$_POST['gudang'];
  $jlhbaris=0;
  $str="select a.tipetransaksi,a.notransaksi,a.tanggal,a.kodept,a.kodegudang,
         b.kodebarang,b.satuan,b.jumlah   
         from ".$dbname.".log_transaksiht a 
         left join ".$dbname.".log_transaksidt b on
		 a.notransaksi=b.notransaksi
		 where a.notransaksi='".$notransaksi."'
        and a.tipetransaksi =7";
  echo "<table class=sortable cellspacing=1 border=0>
        <thead>
		   <tr>
		      <td>No.</td>
			  <td>No.Transaksi</td>
			  <td>Tipe</td>
			  <td>Kodebarang</td>
			  <td>Namabarang</td>
			  <td>Satuan</td>
			  <td>Jumlah</td>
			  <td>Kodept</td>
			  <td>Pengirim</td>
			  <td>Penerima</td>
		   </tr>
		 </thead>
		 <tbody>  	  
			  ";	
$no=0;
  $res=mysql_query($str);
  $jlhbaris=mysql_num_rows($res);
  while($bar=mysql_fetch_object($res))
  {
  	$no+=1;	  
    //ambil namabarang
	$stru="select namabarang from ".$dbname.".log_5masterbarang 
	      where kodebarang='".$bar->kodebarang."'";
	$resu=mysql_query($stru);
	$namabarang='';
	while($baru=mysql_fetch_object($resu))
	{
		$namabarang=$baru->namabarang;
	}
		  
	echo"<tr class=rowcontent id=row".$no.">
	  <td>".$no."</td>
	  <td id=notransaksi".$no.">".$bar->notransaksi."</td>
	  <td>".$bar->tipetransaksi."</td>
	  <td id=kodebarang".$no.">".$bar->kodebarang."</td>	  
	  <td>".$namabarang."</td>
	  <td id=satuan".$no.">".$bar->satuan."</td>
	  <td id=jumlah".$no.">".$bar->jumlah."</td>
	  <td id=kodept".$no.">".$bar->kodept."</td>			  
	  <td id=asalgudang".$no.">".$bar->kodegudang."</td>
	  <td id=gudang".$no.">".$gudang."</td>
	  </tr>";
  }
  echo"</tbody><tfoot></tfoot></table>
  	   <button onclick=mulaiSimpan(".$jlhbaris.") class=mybutton>".$_SESSION['lang']['save']."</button>
  ";
}
else
{
	echo " Error: Transaction Period missing";
}
?>