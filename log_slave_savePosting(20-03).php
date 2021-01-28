<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
	$tipetransaksi=$_POST['tipetransaksi'];
	$tanggal	=$_POST['tanggal'];
	$kodebarang	=$_POST['kodebarang'];
	$satuan		=$_POST['satuan'];
	$jumlah		=$_POST['jumlah'];
	$kodept		=$_POST['kodept'];
	$gudangx	=$_POST['gudangx'];
	$untukpt	=$_POST['untukpt'];
	$gudang		=$_POST['gudang'];
	$blok		=$_POST['kodeblok'];
	$notransaksi=$_POST['notransaksi'];
	$user		=$_SESSION['standard']['userid'];
	//periksa pada table harga barang
	$jlhbrs=0;
	$str="select count(*) as jlh from ".$dbname.".log_5hargabarang where
	      kodebarang='".$kodebarang."' and kodeorg='".$kodept."'";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$jlhbrs=$bar->jlh;
	}
	
	//jika belum terdaftar pada harga barang maka di insert dengan harga 0
	if($jlhbrs==0)
	{
		$str="insert into ".$dbname.".log_5hargabarang(kodeorg,kodebarang,hargasatuan)
		      values('".$kodept."','".$kodebarang."',0)";
		if(!mysql_query($str))
		{
		  	echo " Error: registring material price ,".addslashes(mysql_error($conn));
			exit(0);	   	
		}	
	}
//periksa saldo per gudang apakah sudah terdaftar
	$jlhbrs=0;
	$str="select count(*) as jlh from ".$dbname.".log_5masterbarangdt where
	      kodebarang='".$kodebarang."' and kodeorg='".$kodept."'
		  and kodegudang='".$gudang."'";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$jlhbrs=$bar->jlh;
	}	
	//jika belum terdaftar pada saldo per gudang maka di insert dengan qty 0
	if($jlhbrs==0)
	{
		$str="insert into ".$dbname.".log_5masterbarangdt
		      (kodeorg,kodebarang,saldoqty,
			   hargalastin,hargalastout,
			   stockbataspesan,stockminimum,
			   lastuser,kodegudang
			  )
		      values('".$kodept."','".$kodebarang."',0,
			  0,0,0,0,".$user.",'".$gudang."'
			  )";
		if(!mysql_query($str))
		{
		  	echo " Error: registring material on masterbadangdt ,".addslashes(mysql_error($conn));
			exit(0);	   	
		}	
	}	
   //periksa apakah sudah pernah mempengaruhi saldo
   $statussaldo=0;
   $str= "select  statussaldo from ".$dbname.".log_transaksidt 
          where notransaksi='".$notransaksi."'
		  and kodebarang='".$kodebarang."'
		  and kodeblok='".$blok."'";
   $res=mysql_query($str);
   while($bar=mysql_fetch_object($res))
   {
   	  $statussaldo=$res->statussaldo;
   }
   if($statussaldo>0)
   {
   	//exit without error
	 exit(0); 
   }				
   else
   {	  	
//========================================================================+++++++++++++++++++++++++++++++++
//========================================================================+++++++++++++++++++++++++++++++++
	if($tipetransaksi<5)
	{		
		//pemasukan
		if($jumlah<0)
		{
		  	echo " Error: volume less than 0 ";
			exit(0);			
		}
		else{
			//masukkan ke table saldo
                       if($tipetransaksi==1){//hanya penerimaan barang dari supplier yg mempengaruhi saldo
			$str="update ".$dbname.".log_5masterbarangdt 
			      set saldoqty=saldoqty+".$jumlah."
				  where kodeorg='".$kodept."'and
                                  kodegudang='".$gudang."' and 
				  kodebarang='".$kodebarang."'";  
                       }
                       else{
                           $str="select 1=1";//ignored
                       }
			if(mysql_query($str))
			{
				$str="update ".$dbname.".log_transaksidt
				      set statussaldo=1 where notransaksi='".$notransaksi."'
					  and kodebarang='".$kodebarang."'
					  and kodeblok='".$blok."'";
				if(!mysql_query($str))
				{
					echo "Error: update log_transaksidt status, please contact administrator ,".addslashes(mysql_error($conn));
				    exit(0);
				}	 
			}
			else
			{
		  	echo " Error: update balance (in) ,".addslashes(mysql_error($conn));
			exit(0);	   					
			}	    
		}		
	}
	else
	{
		//pengeluaran
		//ambil saldo existing
		$saldo=0;
		$str="select saldoqty from ".$dbname.".log_5masterbarangdt
		      where kodeorg='".$kodept."'and
	              kodegudang='".$gudang."' and 
		      kodebarang='".$kodebarang."'";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			$saldo=$bar->saldoqty;
		}	  

		if($saldo<$jumlah)
		{
		  	echo " Error: Volume too large, Balance less than current volume";
			exit(0);			
		}
		else if($jumlah<0)
		{
		  	echo " Error: volume less than 0 ";
			exit(0);			
		}
		else{
			//masukkan ke table saldo
			$str="update ".$dbname.".log_5masterbarangdt 
			      set saldoqty=saldoqty-".$jumlah."
				  where kodeorg='".$kodept."'and
	                          kodegudang='".$gudang."' and 
				  kodebarang='".$kodebarang."'";
			if(mysql_query($str))
			{
				$str="update ".$dbname.".log_transaksidt
				      set statussaldo=1 where notransaksi='".$notransaksi."'
					  and kodebarang='".$kodebarang."'
					  and kodeblok='".$blok."'";
				if(!mysql_query($str))
				{
				    echo "Error: update log_transaksidt status, please contact administrator ,".addslashes(mysql_error($conn));
				    exit(0);
				}                            
                        }
			else
			{
		  	echo " Error: update balance (out) ,".addslashes(mysql_error($conn));
			exit(0);	   					
			}	    
		}		
	}
   }
}
else
{
	echo " Error: Transaction Period missing";
}
?>