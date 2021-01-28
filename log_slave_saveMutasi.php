<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//====================================
//default setting on database 
//1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,
//5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi 
$tipetransaksi=7;
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
		$nodok		=$_POST['nodok'];
		$tanggal	=tanggalsystem($_POST['tanggal']);
		$kodebarang	=$_POST['kodebarang'];
		$kegudang	=$_POST['kegudang'];
		$satuan		=$_POST['satuan'];
		$qty		=$_POST['qty'];
		$gudang		=$_POST['gudang'];
		$catatan	=$_POST['catatan'];
		$pemilikbarang=$_POST['pemilikbarang'];        
		$user		=$_SESSION['standard']['userid'];
		$post=0;
	    			

	//1 cek apakah sudah terekan di header
	//status=0 belum ada apa2
	//status=1 ada header
	//status=2 ada detail dan header
	//status=3 sudah di posting
	//status=4 kode pt penerima barang tidak ada
	//status=5 delete item
	//status=6 display only
	//status=7 sudah ada yang diposting pada tanggal yang lebih besar dengan barang yang sama dan pt yang sama
	  $status=0;
	 $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'";
	 $res=mysql_query($str);
	 if(mysql_num_rows($res)==1)
	 {
	 	$status=1;
	 }
	 
//	 $str="select * from ".$dbname.".log_transaksidt where notransaksi='".$nodok."'
//	       and kodebarang='".$kodebarang."'
//		   and kodeblok='".$blok."'";
//	 if(mysql_num_rows(mysql_query($str))>0)
//	 {
//==============================update is not available here
	 	if($method=='update')
		   $status=2;
//	 }	 
	 
	if(isset($_POST['delete']))
	{
		$status=5;
	}	
	
	 $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$nodok."'
	       and post=1";
	 if(mysql_num_rows(mysql_query($str))>0)
	 {
	 	$status=3;
	 }	
    if($pemilikbarang=='')
		{
			$status=4;
		}
	if(isset($_POST['displayonly']))
	{
		$status=6;
	}

//==================ambil jumlah lalu====================
     $jumlahlalu=0;
	 $str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi 
	    from ".$dbname.".log_transaksidt a,
	         ".$dbname.".log_transaksiht b
		   where a.notransaksi=b.notransaksi 
	       and a.kodebarang='".$kodebarang."'
		   and a.notransaksi<='".$nodok."'
		   and tipetransaksi>4
		   and b.kodegudang='".$gudang."'
		   order by notransaksi desc, waktutransaksi desc limit 1";   
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
		   	$jumlahlalu=$bar->jumlah;
		}	    		  

	//ambil pemasukan barang yang belum di posting
		$qtynotpostedin=0;
		$str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
               b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' 
			   and a.tipetransaksi<5
			   and a.kodegudang='".$gudang."'
			   and a.post=0			   
			   group by kodebarang";
//echo $str2; exit;
                
		$res2=mysql_query($str2);
		while($bar2=mysql_fetch_object($res2))
		{
			$qtynotpostedin=$bar2->jumlah;
		}
		if($qtynotpostedin=='')
		   $qtynotpostedin=0;
		   
//ambil trx yg blm di posting
	//ambil pengeluaran barnag yang belum di posting
	$qtynotposted=0;
	$str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
           b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$kodebarang."' 
		   and a.tipetransaksi>4
		   and a.kodegudang='".$gudang."'
		   and a.post=0		   
		   group by kodebarang";

	$res2=mysql_query($str2);
	while($bar2=mysql_fetch_object($res2))
	{
		$qtynotposted=$bar2->jumlah;
	}
//ambil saldo qty===============================================
   $saldoqty=0;
   $strs="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$kodebarang."'
          and kodeorg='".$pemilikbarang."'
		  and kodegudang='".$gudang."'";   
   $ress=mysql_query($strs);
   while($bars=mysql_fetch_object($ress))
   {
   	$saldoqty=$bars->saldoqty;
   }  		  

 //==================periksa kecukupan saldo
  if($status==0 or $status==1)
  {
  	if(($qty+$qtynotposted)>($saldoqty+$qtynotpostedin))
	  {
	  	echo " Error: ".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['tidakcukup']." ".$saldoqty."+".$qtynotpostedin."-".$qtynotposted."=".$qty;
		$status=6;//status ngeles
		exit(0);		
	  }
  } 
  else if($status==2)
  {
   //status 2 tidak akan perbah dieksekusi
  	//ambil jumlah lama dan bandingkan dengan qty kemudian bandingkan dengan saldo
	$jlhlama=0;
	$strt="select jumlah from ".$dbname.".log_transaksidt where notransaksi='".$nodok."'
	       and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";
	$rest=mysql_query($strt);
	while($bart=mysql_fetch_object($rest))
	{
		$jlhlama=$bart->jumlah;
	}	
	if(($saldoqty+$jlhlama+$qtynotpostedin)<($qty+$qtynotposted))
	{
	  	echo " Error: ".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['tidakcukup'];
		$status=6;//status ngeles
		exit(0);
	}   
  } 
  
  //periksa apakah sudah ada status 7
/*
  if($status==0 or $status==1 or $status==2)
  {
  	$stro="select a.post from ".$dbname.".log_transaksiht a
	       left join ".$dbname.".log_transaksidt b
		   on a.notransaksi=b.notransaksi
	       where a.tanggal>=".$tanggal." and a.kodept='".$pemilikbarang."'
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
            $sKdPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($kegudang,0,4)."'";
            $qKdPt=mysql_query($sKdPt) or die(mysql_error($sKdPt));
			$rKdpt=mysql_fetch_assoc($qKdPt);
            if($rKdpt['induk']=='')
            {
                exit("Kode PT Penerima Kosong");
            }
		$str="insert into ".$dbname.".log_transaksiht (
  			  `tipetransaksi`,`notransaksi`,
			  `tanggal`,`kodept`,`untukpt`,
			  `gudangx`,`keterangan`,
			  `kodegudang`,`user`,
			  `post`)
		values(".$tipetransaksi.",'".$nodok."',
		       ".$tanggal.",'".$pemilikbarang."','".$rKdpt['induk']."',
			  '".$kegudang."','".$catatan."',
			  '".$gudang."',".$user.",
			   ".$post."
		)";	
		if(mysql_query($str))//insert hedaer
		{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `updateby`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
			  '".$user."')";
			if(mysql_query($str))//insert detail
			{	
			   
			}   
			else
			{
		     echo " Gagal, (insert detail on status 0)".addslashes(mysql_error($conn));
			 exit(0);
			}	
		}
  		else
			{
		     echo " Gagal,  (insert header on status 0)".addslashes(mysql_error($conn));
			 exit(0);
			}		
	}
//============================
//status=1
	if($status==1)
	{
			$str="insert into ".$dbname.".log_transaksidt (
			  `notransaksi`,`kodebarang`,
			  `satuan`,`jumlah`,`jumlahlalu`,
			  `updateby`)
			  values('".$nodok."','".$kodebarang."',
			  '".$satuan."',".$qty.",".$jumlahlalu.",
			  '".$user."')";
			if(mysql_query($str))//insert detail
			{	
			}   
			else
			{
		     echo " Gagal, (insert detail on status 1)".addslashes(mysql_error($conn));
			 exit(0);
			}	
	}	
//============================update detail
//status=2
	if($status==2)
	{
		//status ini tidak akan tereksekusi
			$str="update ".$dbname.".log_transaksidt set
			      `jumlah`=".$qty.",
				  `updateby`=".$user.",
				  where `notransaksi`='".$nodok."'
				  and `kodebarang`='".$kodebarang."'";
			mysql_query($str);//insert detail
			if(mysql_affected_rows($conn)<1)
			{	
		       echo " Gagal, (update detail on status 2)".addslashes(mysql_error($conn));
			   exit(0);
			}	
	}
//============================return message
//status=3
	if($status==3)
	{	
	   echo " Gagal: Data has been posted";
	   exit(0);
	}
//============================return message
//status=4
	if($status==4)
	{	
	   echo " Gagal: Company code of the Recipient is not defined";
	   exit(0);
	}
//===========delete ==========================
//status=5
	if($status==5)
	{ //delete item not header		   	 
	   $str="delete from ".$dbname.".log_transaksidt where kodebarang='".$kodebarang."'
	         and notransaksi='".$nodok."'";	 
	   mysql_query($str);
	   if(mysql_affected_rows($conn)>0)
	   {		
	   }		 
	}
	
//ambil data untuk ditampilkan
 $strj="select a.* from ".$dbname.".log_transaksidt a 
        where a.notransaksi='".$nodok."'";	
 $resj=mysql_query($strj);
 $no=0;
 while($barj=mysql_fetch_object($resj))
   {
	$no+=1;
	//ambil namabarang
	$namabarangk='';
	$strk="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$barj->kodebarang."'";
	$resk=mysql_query($strk);
	while($bark=mysql_fetch_object($resk))
	{
		$namabarangk=$bark->namabarang;
	}

	echo"<tr class=rowcontent>
		    <td>".$no."</td>
			<td>".$barj->kodebarang."</td>
			<td>".$namabarangk."</td>
			<td>".$barj->satuan."</td>
			<td align=right>".number_format($barj->jumlah,2,'.',',')."</td>
			<td>
		        &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delMutasi('".$nodok."','".$barj->kodebarang."');\">
			</td>
 		   </tr>";
   }			
}
else
{
	echo " Error: Transaction Period missing";
}
?>