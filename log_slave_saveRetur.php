<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=2;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
    $nodok=$_POST['nodok'];
    $nomorlama=$_POST['nomorlama'];
	$idsupplier=$_POST['untukpt'];
	$tanggal=tanggalsystem($_POST['tanggal']);
	$nopo=$_POST['nomorlama'];
	$nofaktur='';
	$nosj='';
	$qty=$_POST['jlhretur'];
	$kodebarang=$_POST['kodebarang'];
	$kodegudang=$_POST['gudang'];
	$kodept=$_POST['kodept'];
	$untukunit=$_POST['untukunit'];
	$hargasatuan=$_POST['hargasatuan'];
    $kodeblok=$_POST['kodeblok'];
	$post=1;
	$keterangan=$_POST['keterangan'];
	$user=$_SESSION['standard']['userid'];
	$satuan=$_POST['satuan'];//satuan pada master barang
	//1 cek apakah sudah terekan di header
	//status=0 belum ada apa2
	//status=1 ada header
	//status=2 ada detail dan header
	//status=3 sudah di posting
	//status=7 sudah ada yang diposting pada tanggal yang lebih besar dengan barang yang sama dan pt yang sama
	
	 $status=0;
	 $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
	 $res=mysql_query($str);
	 if(mysql_num_rows($res)==1)
	 {
	 	$status=1;
	 }
	 
	 $str="select * from ".$dbname.".log_transaksidt where notransaksi='".$nodok."'
	       and kodebarang='".$kodebarang."'";
	 if(mysql_num_rows(mysql_query($str))>0)
	 {
	 	$status=2;
	 }	 
	 
	 $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'
	       and post=1";
	 if(mysql_num_rows(mysql_query($str))>0)
	 {
	 	$status=3;
	 }	
	 	 
if($hargasatuan=='')
   $hargasatuan=0;
	   
//==================ambil jumlah lalu====================
     $jumlahlalu=0;   
//===============================================================		 		  
  //periksa apakah sudah ada status 
  if($status==0 or $status==1 or $status==2)
  {
  	$stro="select * from ".$dbname.".setup_periodeakuntansi 
               where kodeorg='".$kodegudang."' and periode='".substr($tanggal,0,7)."'
               and tutupbuku=1"; 
	$reso=mysql_query($stro);
	if(mysql_num_rows($reso)>0)
	{
		$status=7;
		echo " Error :".$_SESSION['lang']['tanggaltutup'];
		exit(0);
	}	   
  }

//periksa apakah sudah posting trx lamanya
  $str="select statussaldo from ".$dbname.".log_transaksidt where notransaksi='".$nomorlama."' 
        and kodebarang='".$kodebarang."' and kodeblok='".$kodeblok."' and statussaldo=1";
  $res=mysql_query($str);
  if(mysql_num_rows($res)>0)
  {
      //update saldo bulanan
        $str="update ".$dbname.".log_5masterbarangdt 
              set saldoqty=saldoqty+".$qty."
                  where kodeorg='".$kodept."'and
                  kodegudang='".$kodegudang."' and 
                  kodebarang='".$kodebarang."'"; 
        mysql_query($str);
  }
  //update transaksi
  /*
    $str="update ".$dbname.".log_transaksidt set jumlah=jumlah-".$qty." 
    where notransaksi='".$nomorlama."' and kodebarang='".$kodebarang."' and kodeblok='".$kodeblok."'";
    mysql_query($str);
	*/
  //gudang tujuan
   $strf="select kodegudang,kodekegiatan from ".$dbname.".log_transaksi_vw where notransaksi='".$nomorlama."' and kodebarang='".$kodebarang."' ";
   $resf=mysql_query($strf);
   $gudangx=NULL;
   $biayax=NULL;
	while($barf=mysql_fetch_object($resf))
	{
		$gudangx=$barf->kodegudang;
		$biayax=$barf->kodekegiatan;
	}	   
//=============================start input/update	
//status=0
	if($status==0)
	{
		$str="insert into ".$dbname.".log_transaksiht (
  			`tipetransaksi`,`notransaksi`,`tanggal`,
  			`kodept`,`nopo`,`nosj`,`kodegudang`,`user`,`postedby`,
  			`idsupplier`,`nofaktur`,`post`,`untukunit`,
			`keterangan`,`notransaksireferensi`,`gudangx`)
		values(".$tipetransaksi.",'".$nodok."',".$tanggal.",
		     '".$kodept."','".$nopo."','".$nosj."','".$kodegudang."',".$user.",".$user." ,
			 '".$idsupplier."','".$nofaktur."',".$post.",'".$untukunit."',
			 '".$keterangan."','".$nopo."','".$gudangx."'
		)";	
		if(mysql_query($str))//insert hedaer
		{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `hargasatuan`,`updateby`,`kodeblok`,
			  `hargarata`,kodekegiatan,statussaldo )
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
			   ".$hargasatuan.",".$user.",
                           '".$kodeblok."',".$hargasatuan.",'".$biayax."','1')";
			if(mysql_query($str))//insert detail
			{	
			}   
			else
			{
		     echo " Gagal, (insert detail on status 0)".addslashes(mysql_error($conn));
			}	
		}
  		else
			{
		     echo " Gagal,  (insert header on status 0)".addslashes(mysql_error($conn));
			}		
	}     
//============================
//status=1
	else if($status==1)
	{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `hargasatuan`,`updateby`,`kodeblok`,
			  `hargarata`,kodekegiatan,statussaldo)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
			   ".$hargasatuan.",".$user.",'".$kodeblok."',".$hargasatuan.",'".$biayax."','1')";
			if(mysql_query($str))//insert detail
			{	
			}   
			else
			{
		     echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
			}	
	}	
//============================return message
//status=3
	if($status==3)
	{	
	   echo " Gagal: Data has been posted";
	}	
}
else
{
	echo " Error: Transaction Period missing";
}
?>