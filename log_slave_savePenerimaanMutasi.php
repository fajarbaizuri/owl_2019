<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=3;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
        $nodok=$_POST['nodok'];
		$kodebarang=$_POST['kodebarang'];
		$tanggal=tanggalsystem($_POST['tanggal']);	
		$gudangx=$_POST['gudangx'];		
		$satuan=$_POST['satuan'];
		$jumlah=$_POST['jumlah'];		
		$kodegudang=$_POST['kodegudang'];
		$referensi=$_POST['referensi'];				
		$pemilikbarang=$_POST['pemilikbarang'];
		$post=0;
		$user=$_SESSION['standard']['userid'];
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
	 	 
	//get other data 

//harga satuan ==============================
/*
   $strx="select a.hargasatuan from ".$dbname.".log_transaksidt a
           left join ".$dbname.".log_transaksiht b 
		   on a.notransaksi=b.notransaksi
	       where a.kodebarang='".$kodebarang."'
		   and a.notransaksi='".$referensi."'
		   and  b.tipetransaksi=7
		   order by a.notransaksi desc limit 1";      
		          
   $hargasatuan=0;
   $resx=mysql_query($strx);
   while($barx=mysql_fetch_object($resx))
   {
   	  $hargasatuan=$barx->hargasatuan;
   }

   if($hargasatuan==0)
   {
   	  echo " Error: Price is 0";
	  exit(0);
   }
*/
	   
//==================ambil jumlah lalu====================
     $jumlahlalu=0;
	 $str="select a.jumlah as jumlah,a.notransaksi as notransaksi 
	    from ".$dbname.".log_transaksidt a,
	         ".$dbname.".log_transaksiht b
		   where a.notransaksi=b.notransaksi
	       and a.kodebarang='".$kodebarang."'
		   and a.notransaksi<='".$nodok."'
		   and b.kodegudang='".$kodegudang."'
		   order by notransaksi desc limit 1";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
		   	$jumlahlalu=$bar->jumlah;
		}	   
//===============================================================		 		  
  //periksa apakah sudah ada status 7
/*
  if($status==0 or $status==1 or $status==2)
  {
  	$stro="select a.post from ".$dbname.".log_transaksiht a
	       left join ".$dbname.".log_transaksidt b
		   on a.notransaksi=b.notransaksi
	       where a.tanggal>=".$tanggal." and a.kodept='".$kodept."'
		   and b.kodebarang='".$kodebarang."'
		   and a.post=1";
	$reso=mysql_query($stro);
	if(mysql_num_rows($reso)>0)
	{
		$status=7;
		echo " Error :".$_SESSION['lang']['tanggaltutup'];
		exit(0);
	}	   
  }
*/

//=============================start input/update	
//status=0
	if($status==0)
	{
            //get kode pt penerima barang
            $a="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($kodegudang,0,4)."'";
			//exit("Error:$sKdPt");
            $b=mysql_query($a) or die(mysql_error($a));
            $c=mysql_fetch_assoc($b);
            if($c['induk']=='')
            {
                exit("Error:Kode PT Penerima Kosong");
            }
			
			//GET KODE PT 
			$x="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($kodegudang,0,4)."'";
			//exit("Error:$sKdPt");
            $y=mysql_query($x) or die(mysql_error($a));
            $z=mysql_fetch_assoc($y);
            if($z['induk']=='')
            {
                exit("Error:Kode PT Penerima Kosong");
            }
			
			#indra untukpt
		$str="insert into ".$dbname.".log_transaksiht (
  			`tipetransaksi`,`notransaksi`,`tanggal`,
  			`untukpt`,`kodegudang`,`user`,
  			`gudangx`,`notransaksireferensi`,`post`,`kodept`)
		values(".$tipetransaksi.",'".$nodok."',".$tanggal.",
		     '".$c['induk']."','".$kodegudang."',".$user.",
			 '".$gudangx."','".$referensi."',".$post.",'".$z['induk']."'
		)";	
		if(mysql_query($str))//insert hedaer
		{
			//update sumber pada pengeluaran mutasi
			$str="update ".$dbname.".log_transaksiht 
			       set notransaksireferensi='".$nodok."'
				   where notransaksi='".$referensi."'
				   and kodegudang='".$gudangx."'";   
			if(mysql_query($str))
			{}
			else
			{
				 echo " Gagal, (update reference on status 0)".addslashes(mysql_error($conn));
			}	   
			
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$jumlah.",".$jumlahlalu.")";
			if(mysql_query($str))//insert detail
			{	
			  //update PO jumlah masuk pada posting
			   
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
	if($status==1)
	{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$jumlah.",".$jumlahlalu.")";
			if(mysql_query($str))//insert detail
			{	
			}   
			else
			{
		     echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
			}	
	}	
//============================update detail
//status=2// pada penerimaan mutasi tidak ada update
/*
	if($status==2)
	{
			$str="update ".$dbname.".log_transaksidt set
			      `jumlah`=".$qty.",
				  `updateby`=".$user."
				  where `notransaksi`='".$nodok."'
				  and `kodebarang`='".$kodebarang."'";
			mysql_query($str);//insert detail
			if(mysql_affected_rows($conn)<1)
			{	
		       echo " Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
			}
			else
			{
				//update jumlah lalu pada transaksi berikutnya jika ada
				//ambil no trx yg berikutnya
				$notrxnext='';
				$strc="select a.notransaksi as notrx from ".$dbname.".log_transaksidt a, ".$dbname.".log_transaksiht b
				      where a.notransaksi= b.notransaksi 
					  and b.nopo='".$nopo."'
					  and a.notransaksi>'".$nodok."'
					  and a.kodebarang='".$kodebarang."'
					  order by notrx asc limit 1";
				$resc=mysql_query($strc);
				while($barc=mysql_fetch_object($resc))	
				{
					$notrxnext=$barc->notrx;
				}  
				
				$str="update ".$dbname.".log_transaksidt set
			      `jumlahlalu`=".$qty.",
				  `updateby`=".$user."
				  where `notransaksi`='".$notrxnext."'
				  and `kodebarang`='".$kodebarang."'";
				 
				mysql_query($str);
				if(mysql_affected_rows($conn)<1)
				{	
		          echo " Gagal, (failed update next `jumlahlalu` on status 2)".addslashes(mysql_error($conn));
				}  
			}	
	}
*/
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