<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//========================
  $user  =$_SESSION['standard']['userid'];
  $period=$_POST['periode'];
  $pt	 =$_POST['pt'];
  $gudang=$pt;
  $kodebarang=$_POST['kodebarang'];  
  $awal  =$_POST['awal'];
  $akhir =$_POST['akhir'];
//=============================


function HargaAwal($kriteria,$kodebarang, $gudang,$period,$dbname){
	$strA="SELECT  DATE_FORMAT('".$period."-01 00:00:00' - INTERVAL 1 DAY, '%Y-%m') AS newperiod;";
	$resA=mysql_query($strA);
	$barA=mysql_fetch_object($resA);
	$periodKemaren=$barA->newperiod;
		$str="select ifnull(sum(saldoakhirqty),0) as sakhir,ifnull(sum(nilaisaldoakhir),0) as sakhirrp 
		      from ".$dbname.".log_5saldobulanan
		      where kodegudang='".$gudang."'
			  and kodebarang='".$kodebarang."' and periode='".$periodKemaren."'";
		$res=mysql_query($str);
		$sakhir=0;
		$nilaisaldoakhir=0;
		if (mysql_num_rows($res)==0){
			$sakhir=0;
			$nilaisaldoakhir=0;
		}else{
			while($bar=mysql_fetch_object($res))
			{
				$sakhir=$bar->sakhir;
				$nilaisaldoakhir=$bar->sakhirrp;
			}
		}	
			if ($kriteria=="fisik"){
				return $sakhir;
			}else{
				return $nilaisaldoakhir;
			}
}

function HargaMasuk($kriteria,$kodebarang, $gudang,$awal,$akhir,$dbname){
	$str="select ifnull(sum(a.jumlah),0) as jumlah,ifnull(sum(if(b.tipetransaksi=1,a.hargasatuan,a.hargarata)*a.jumlah) ,0) as hartot from ".$dbname.".log_transaksidt a
       left join ".$dbname.".log_transaksiht b on
	   a.notransaksi=b.notransaksi
      where b.kodegudang='".$gudang."'
	  and a.kodebarang='".$kodebarang."'
	  and b.tanggal>='".$awal."' and b.tanggal<='".$akhir."' 
	  and b.tipetransaksi in ('1','3','6') and b.post=1";	  
	$res=mysql_query($str);
	$masuk=0;
	$hartotmasuk=0;
	if (mysql_num_rows($res)==0){
			$masuk=0;
			$hartotmasuk=0;
		}else{
			while($bar=mysql_fetch_object($res))
			{
				$masuk=$bar->jumlah;
				$hartotmasuk=$bar->hartot;
			}
		}	
			if ($kriteria=="fisik"){
				return $masuk;
			}else{
				return $hartotmasuk;
			}
			
			
}

function updateHargaMutasi($kodebarang, $notreferensi,$transaksi,$dbname){
	
	$str="select ifnull(hargarata,0) as harga 
		      from ".$dbname.".log_transaksidt
		      where kodebarang='".$kodebarang."' and notransaksi='".$notreferensi."'";
		$res=mysql_query($str);
		$haratbaru=0;
		if (mysql_num_rows($res)==0){
			$haratbaru=0;
		}else{
			while($bar=mysql_fetch_object($res))
			{
				$haratbaru=$bar->harga;
			}
		}
		
	
		
		 $strA="update ".$dbname.".log_transaksidt set hargarata=".$haratbaru." 
        where kodebarang='".$kodebarang."'
         and notransaksi in ('".$transaksi."')"; 
		 if(mysql_query($strA))
 {
	 /*
 	$str="update ".$dbname.".log_5saldobulanan 
	      set hargarata=".$haratbaru.",hargaratasaldoawal=".$haratsakhir.",
		  nilaisaldoawal=saldoawalqty*".$haratsakhir.",
		  nilaisaldoakhir=saldoakhirqty*".$haratbaru.",
		  qtymasukxharga=qtymasuk*".$haratmasuk.",
		  qtykeluarxharga=qtykeluar*".$haratbaru."
	      where kodebarang='".$kodebarang."' and kodegudang='".$gudang."' 
		  and periode='".$period."'";
	if(mysql_query($str))
	{
	}
	  */
	
 }
 
		
}

 /*
$strB="select kodebarang,gudangx,concat(LEFT(notransaksireferensi,4),'-',MID(notransaksireferensi, 5, 2)) as periodenew,
concat(LEFT(notransaksireferensi,6),'01') as awalq,concat(LEFT(notransaksireferensi,6),DAY(LAST_DAY( concat(LEFT(notransaksireferensi,4),'-',MID(notransaksireferensi, 5, 2),'-01') ) )) as akhirq 
 from ".$dbname.".log_transaksi_vw  where kodebarang='".$kodebarang."' and kodegudang='".$gudang."'  and tanggal>='".$awal."' and tanggal<='".$akhir."' and post=1 and tipetransaksi ='3' group by kodebarang,gudangx,LEFT(notransaksireferensi,6)";
 */
$strB="select kodebarang,notransaksireferensi,notransaksi 
 from ".$dbname.".log_transaksi_vw  where kodebarang='".$kodebarang."' and kodegudang='".$gudang."'  and tanggal>='".$awal."' and tanggal<='".$akhir."' and post=1 and tipetransaksi ='3' group by kodebarang,gudangx,notransaksireferensi";
 
$resB=mysql_query($strB);
while($barB=mysql_fetch_object($resB))
{
	
	
		updateHargaMutasi($barB->kodebarang,$barB->notransaksireferensi,$barB->notransaksi,$dbname);
	

}

/*
-------------

//===============Cari Harga Rata2 Periode Akhir Bulan Kemaren
$strA="SELECT  DATE_FORMAT('".$period."-01 00:00:00' - INTERVAL 1 DAY, '%Y-%m') AS newperiod;";
$resA=mysql_query($strA);
$barA=mysql_fetch_object($resA);
$periodKemaren=$barA->newperiod;
		$str="select ifnull(sum(saldoakhirqty),0) as sakhir,ifnull(sum(nilaisaldoakhir),0) as sakhirrp 
		      from ".$dbname.".log_5saldobulanan
		      where kodegudang='".$gudang."'
			  and kodebarang='".$kodebarang."' and periode='".$periodKemaren."'";
		$res=mysql_query($str);
		$sakhir=0;
		$nilaisaldoakhir=0;
		while($bar=mysql_fetch_object($res))
		{
			$sakhir=$bar->sakhir;
			$nilaisaldoakhir=$bar->sakhirrp;
		}
		if($sakhir=='')
		   $sakhir=0;
		if($nilaisaldoakhir=='')
		   $nilaisaldoakhir=0;   
        if($sakhir==0 or $nilaisaldoakhir==0)
		{
			$haratsakhir=0;
		}
		else
		{
			$haratsakhir=$nilaisaldoakhir/$sakhir;
		}

//===============Cari Harga Rata2 Barang Masuk Periode saat ini
 $str="select ifnull(sum(a.jumlah),0) as jumlah,ifnull(sum(if(b.tipetransaksi='1',a.hargasatuan*a.jumlah,a.hargarata*a.jumlah)  ),0) as hartot from ".$dbname.".log_transaksidt a
       left join ".$dbname.".log_transaksiht b on
	   a.notransaksi=b.notransaksi
      where b.kodegudang='".$gudang."'
	  and a.kodebarang='".$kodebarang."'
	  and b.tanggal>='".$awal."' and b.tanggal<='".$akhir."' 
	  and b.tipetransaksi in in ('1') and b.post=1";	  
 $res=mysql_query($str);
 $masuk=0;
 $hartotmasuk=0;
 while($bar=mysql_fetch_object($res))
 {
 	$masuk=$bar->jumlah;
	$hartotmasuk=$bar->hartot;
 }
		if($masuk=='')
		   $masuk=0;
		if($hartotmasuk=='')
		   $hartotmasuk=0;   
        if($masuk==0 or $hartotmasuk==0)
		{
			$haratmasuk=0;
		}
		else
		{
			$haratmasuk=$hartotmasuk/$masuk;
		}

--------------------------
*/
//===============Cari Harga Rata2 saat ini
		$nilaisaldoakhir=HargaAwal("rupiah",$kodebarang, $gudang,$period,$dbname);
		$hartotmasuk=HargaMasuk("rupiah",$kodebarang, $gudang,$awal,$akhir,$dbname);
		$sakhir=HargaAwal("fisik",$kodebarang, $gudang,$period,$dbname);
		$masuk=HargaMasuk("fisik",$kodebarang, $gudang,$awal,$akhir,$dbname);
		 if($sakhir==0 or $nilaisaldoakhir==0)
		{
			$haratsakhir=0;
		}
		else
		{
			$haratsakhir=$nilaisaldoakhir/$sakhir;
		}
		 if($masuk==0 or $hartotmasuk==0)
		{
			$haratmasuk=0;
		}
		else
		{
			$haratmasuk=$hartotmasuk/$masuk;
		}
	$haratbaru=0;
	$nilaisaldobaru=$nilaisaldoakhir+$hartotmasuk;
	$sbaru=$sakhir+$masuk;
	 if($sbaru==0 or $nilaisaldobaru==0)
		{
			$haratbaru=0;
		}
		else
		{
			$haratbaru=$nilaisaldobaru/$sbaru;
		}
	

/*

	
		
//ambil saldo saldoawal bulan ini
		$str="select sum(saldoawalqty) as sawal,sum(nilaisaldoawal) as sawalrp 
		      from ".$dbname.".log_5saldobulanan
		      where kodegudang='".$gudang."'
			  and kodebarang='".$kodebarang."' and periode='".$period."'";
		$res=mysql_query($str);
		$sawal=0;
		$nilaisaldoawal=0;
		while($bar=mysql_fetch_object($res))
		{
			$sawal=$bar->sawal;
			$nilaisaldoawal=$bar->sawalrp;
		}
		if($sawal=='')
		   $sawal=0;
		if($nilaisaldoawal=='')
		   $nilaisaldoawal=0;   
        if($sawal==0 or $nilaisaldoawal==0)
		{
			$haratsawal=0;
		}
		else
		{
			$haratsawal=$nilaisaldoawal/$sawal;
		}
   
//===============ambil semua penerimaan yan sudah posting dan harga
 $str="select sum(a.jumlah) as jumlah,sum(a.hargasatuan*a.jumlah) as hartot from ".$dbname.".log_transaksidt a
       left join ".$dbname.".log_transaksiht b on
	   a.notransaksi=b.notransaksi
      where b.kodegudang='".$gudang."'
	  and a.kodebarang='".$kodebarang."'
	  and b.tanggal>=".$awal." and b.tanggal<=".$akhir." 
	  and b.tipetransaksi<5 and b.post=1";	  
 $masuk=0;
 $hartotmasuk=0;
 $res=mysql_query($str);
 while($bar=mysql_fetch_object($res))
 {
 	$masuk=$bar->jumlah;
	$hartotmasuk=$bar->hartot;
 }
 
 //===============ambil semua retur ke supplier
 $str="select sum(a.jumlah) as jumlah,sum(a.hargasatuan*a.jumlah) as hartot from ".$dbname.".log_transaksidt a
       left join ".$dbname.".log_transaksiht b on
	   a.notransaksi=b.notransaksi
      where b.kodegudang='".$gudang."'
	  and a.kodebarang='".$kodebarang."'
	  and b.tanggal>=".$awal." and b.tanggal<=".$akhir." 
	  and b.tipetransaksi=6 and b.post=1";	  
 $res=mysql_query($str);
 while($bar=mysql_fetch_object($res))
 {
 	$masuk-=$bar->jumlah;
	$hartotmasuk-=$bar->hartot;
 }
 //=============== hitung harga rata-rata
 if($masuk=='')
    $masuk=0;
 if($hartotmasuk=='')
    $hartotmasuk=0;
   
   if($masuk<=0)
     $haratmasuk=0;
   else 	 
     $haratmasuk=$hartotmasuk/$masuk;	
   	
	
//=======================================================
    if(($sawal+$masuk)<=0)
	{
		$haratbaru=0;
	}
	else
	{
	  $haratbaru=	($hartotmasuk+$nilaisaldoawal)/($sawal+$masuk);
	}

#jika harga rata-rata baru adalah 0
if($haratbaru==0)
    $haratbaru=$haratmasuk;
if($haratbaru==0)
    $haratbaru=$haratsawal;
if($haratbaru==0)
{
    $str="select hargarata from ".$dbname.".log_5saldobulanan where kodebarang='".$kodebarang."' and hargarata>0
          order by lastupdate desc limit 1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $haratbaru=$bar->hargarata;
    }
}
if($haratbaru=='')
    $haratbaru=1;
*/
//==========================================
 //update harga pada 
 //untuk mutasi sudah tidak perlu dilakukan pemberian harga lagi
  $strA="update ".$dbname.".log_transaksidt set hargarata=".$haratbaru." 
        where kodebarang='".$kodebarang."'
         and notransaksi in(select notransaksi from ".$dbname.".log_transaksiht b where b.kodegudang='".$gudang."' and b.tanggal>='".$awal."' and b.tanggal<='".$akhir."' and b.post=1 and not b.tipetransaksi in ('1','3','6'))"; 
		
 if(mysql_query($strA))
 {
 	$str="update ".$dbname.".log_5saldobulanan 
	      set hargarata=".$haratbaru.",hargaratasaldoawal=".$haratsakhir.",
		  nilaisaldoawal=saldoawalqty*".$haratsakhir.",
		  nilaisaldoakhir=saldoakhirqty*".$haratbaru.",
		  qtymasukxharga=qtymasuk*".$haratmasuk.",
		  qtykeluarxharga=qtykeluar*".$haratbaru."
	      where kodebarang='".$kodebarang."' and kodegudang='".$gudang."' 
		  and periode='".$period."'";
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	  
	
 }
 else
 {
 	echo " Gagal,".addslashes(mysql_error($conn));
 }			
?>