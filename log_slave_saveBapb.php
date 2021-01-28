<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


//exit("Error:MASUK");
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=1;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
	
	
        $nodok=$_POST['nodok'];
		$idsupplier=$_POST['idsupplier'];
		$tanggal=tanggalsystem($_POST['tanggal']);
		$nopo=$_POST['nopo'];
		$nofaktur=$_POST['nofaktur'];
		$nosj=$_POST['nosj'];
		$qty=$_POST['qty'];
		$kodebarang=$_POST['kodebarang'];
		$kodegudang=$_POST['kodegudang'];
		$post=0;
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
	//get other data 
//kode pt dan kurs===================================
    $kurs=1;// default untuk kurs sebagai pengali
	$kodept='';
	$str="select kodeorg,kurs,matauang from ".$dbname.".log_poht where nopo='".$nopo."'";
	$res=mysql_query($str);
	$matauang='';        
	while($bar=mysql_fetch_object($res))
	{
		$kodept=$bar->kodeorg;
		$kurs=$bar->kurs;
		$matauang=str_replace(" ","",$bar->matauang);                
	}
//harga satuan base on conversion==============================
	$str="select hargasatuan,jumlahpesan,satuan,matauang,kodebarang from ".$dbname.".log_podt where 
	      nopo='".$nopo."' and kodebarang='".$kodebarang."'";
		  
		// exit("Error:$str");
	$res=mysql_query($str);
	$jumlahpesan='';
	$hargasatuan=0;

	while($bar=mysql_fetch_object($res))
	{

		$jumlahpesan=$bar->jumlahpesan;
		$hargasatuan=$bar->hargasatuan;
		//konversi satuan jika satuan default kodebarang tidak sama dengan satuan po
		if($satuan!=$bar->satuan)
		 {
			$jlhkonversi=0;//tidak nol untuk menhindari devide by zero
			$str1="select jumlah from ".$dbname.".log_5stkonversi 
			       where darisatuan='".$satuan."' and satuankonversi='".$bar->satuan."'
                               and kodebarang='".$bar->kodebarang."'";
			
			$res3=mysql_query($str1);
			if(mysql_num_rows($res3)>0)
			{
				//exit("Error:$str1");
				while($bar2=mysql_fetch_object($res3))
				{
					//$jlhkonversi=$bar2->jumlah;
					$jlhkonversi=$jumlahpesan/$bar2->jumlah;
					//exit("Error:$jlhkonversi.__.$jumlahpesan.__.$bar2->jumlah");
				}	
			}
			if($jlhkonversi!=0)
			{
			 $hargasatuan=$bar->hargasatuan*$jumlahpesan/$jlhkonversi;
			 
			 
			//exit("Error:$hargasatuan");
			// echo $jlhkonversi;
				//$hargasatuan=($bar->hargasatuan*$bar->jumlah)*$jlhkonversi;
			}
		 }
	}
       
	if($kurs==0 or $matauang=='IDR' or $matauang=='')
        {$kurs=1;}
        
	   $hargasatuan=$hargasatuan*$kurs;
	   
//==================ambil jumlah lalu====================
     $jumlahlalu=0;
	 $str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi 
	    from ".$dbname.".log_transaksidt a,
	         ".$dbname.".log_transaksiht b
		   where a.notransaksi=b.notransaksi and  
		   b.nopo='".$nopo."' 
	       and a.kodebarang='".$kodebarang."'
		   and a.notransaksi<'".$nodok."'
		   order by notransaksi desc limit 1";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
		   	$jumlahlalu=$bar->jumlah;
		}	   
//===============================================================		 		  
  //periksa apakah sudah ada status 7
  if($status==0 or $status==1 or $status==2)
  {
  	$stro="select a.post from ".$dbname.".log_transaksiht a
	       left join ".$dbname.".log_transaksidt b
		   on a.notransaksi=b.notransaksi
	       where a.tanggal>".$tanggal." and a.kodept='".$kodept."'
		   and b.kodebarang='".$kodebarang."' and kodegudang='".$kodegudang."'
		   and a.post=1";
//	echo $stro;
	$reso=mysql_query($stro);
	if(mysql_num_rows($reso)>0)
	{
		$status=7;
		
		//echo " rror :".$_SESSION['lang']['tanggaltutup'];
		echo " Error : Tidak Boleh Ada Transaksi Barang tersebut pada tanggal selanjutnya, harap unsposting tgl selanjutnya terlebih dahulu. ";
		//echo " Error : candra"; 
		exit(0);
	}	   
  }
//periksa apakah harga barang sudah diisi dalam PO
if($hargasatuan==0)
{
    exit('Error: belum ada harga pada PO:'.$nopo);
}
//=============================start input/update	
//status=0
	if($status==0)
	{
		$str="insert into ".$dbname.".log_transaksiht (
  			`tipetransaksi`,`notransaksi`,`tanggal`,
  			`kodept`,`nopo`,`nosj`,`kodegudang`,`user`,
  			`idsupplier`,`nofaktur`,`post`)
		values(".$tipetransaksi.",'".$nodok."',".$tanggal.",
		     '".$kodept."','".$nopo."','".$nosj."','".$kodegudang."',".$user.",
			 '".$idsupplier."','".$nofaktur."',".$post."
		)";	
		if(mysql_query($str))//insert hedaer
		{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `hargasatuan`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
			   ".$hargasatuan.")";
			if(mysql_query($str))//insert detail
			{	
			  //update PO jumlah masuk pada posting
			   //update statuspo pada table po
			   $str="update ".$dbname.".log_poht set statuspo=3 where nopo='".$nopo."'";
			   mysql_query($str); 
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
			  `hargasatuan`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
			   ".$hargasatuan.")";
			if(mysql_query($str))//insert detail
			{	
			   //update table po statuspo
			   $str="update ".$dbname.".log_poht set statuspo=3 where nopo='".$nopo."'";
			   mysql_query($str); 
			}   
			else
			{
		     echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
			}	
	}	
//============================update detail
//status=2
	else if($status==2)
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
				
				if($notrxnext!='')
				{
					$str="update ".$dbname.".log_transaksidt set
				      `jumlahlalu`=".$qty.",
					  `updateby`=".$user."
					  where `notransaksi`='".$notrxnext."'
					  and `kodebarang`='".$kodebarang."'";
					mysql_query($str);
					if(mysql_affected_rows($conn)<1)
					{	
			                     //echo " Gagal, (failed update next `jumlahlalu` on status 2)".addslashes(mysql_error($conn));
					}
				}
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