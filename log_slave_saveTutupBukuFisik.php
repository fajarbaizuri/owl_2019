<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//========================
  $gudang=$_POST['gudang'];
  $user  =$_SESSION['standard']['userid'];
  $period=$_POST['periode'];
  $pt	 =$_POST['pt'];
  $kodebarang=$_POST['kodebarang'];
  $awal  =$_POST['awal'];
  $akhir =$_POST['akhir'];
  
//==============
  $x=str_replace("-","",$period);
  $x=str_replace("/","",$x);
  $x=mktime(0,0,0,intval(substr($x,4,2))-1,15,substr($x,0,4));
  $prefper=date('Y-m',$x);  
//=============================
//ambil saldo akhir bulan lalu termasuk rupiah
        $str="select saldoakhirqty,nilaisaldoakhir,hargarata 
              from ".$dbname.".log_5saldobulanan
              where kodeorg='".$pt."' and kodegudang='".$gudang."'
                  and kodebarang='".$kodebarang."' and periode='".$prefper."'";
        
        $res=mysql_query($str);
        $sawal=0;
        $nilaisaldoawal=0;
        $hargaratasaldoawal=0;
        while($bar=mysql_fetch_object($res))
        {
           $sawal=$bar->saldoakhirqty;
           $nilaisaldoawal=$bar->nilaisaldoakhir;//yg diambil adalah periode lalu
           $hargaratasaldoawal=$bar->hargarata;
        }
        if($sawal=='')
           $sawal=0;
        if($hargaratasaldoawal=='')
           $hargaratasaldoawal=0; 
        if($nilaisaldoawal=='')
           $nilaisaldoawal=0;   
	     
   
//===============ambil semua penerimaan yan sudah posting
 $str="select sum(a.jumlah) as jumlah from ".$dbname.".log_transaksidt a
       left join ".$dbname.".log_transaksiht b on
	   a.notransaksi=b.notransaksi
      where b.kodept='".$pt."' and b.kodegudang='".$gudang."'
	  and a.kodebarang='".$kodebarang."'
	  and b.tanggal>=".$awal." and b.tanggal<=".$akhir." 
	  and b.tipetransaksi<5";	  
 $masuk=0;
 $res=mysql_query($str);
 while($bar=mysql_fetch_object($res))
 {
 	$masuk=$bar->jumlah;
 }
 if($masuk=='')
    $masuk=0;
	
//==============ambil semua jumlah keluar
		 $str="select sum(a.jumlah) as jumlah from ".$dbname.".log_transaksidt a
		       left join ".$dbname.".log_transaksiht b on
			   a.notransaksi=b.notransaksi
		      where b.kodept='".$pt."' and b.kodegudang='".$gudang."'
			  and a.kodebarang='".$kodebarang."'
			  and b.tanggal>=".$awal." and b.tanggal<=".$akhir." 
			  and b.tipetransaksi>4";
		 $keluar=0;
		 $res=mysql_query($str);
		 while($bar=mysql_fetch_object($res))
		 {
		 	$keluar=$bar->jumlah;
		 }
		 if($keluar=='')
		    $keluar=0;	  
//==============formula saldo fisik
 $saldoakhirqty=($sawal+$masuk)-$keluar;
			
//==========================================
 //delete jika sudah terdaftar pada saldo bulanan
  $str="delete from ".$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'
			  and kodebarang='".$kodebarang."' and periode='".$period."'";
  mysql_query($str);
 //insert new line
  $str="INSERT INTO `".$dbname."`.`log_5saldobulanan`
			(`kodeorg`,
			`kodebarang`,
			`saldoakhirqty`,
			`hargarata`,
			`lastuser`,
			`periode`,
			`nilaisaldoakhir`,
			`kodegudang`,
			`qtymasuk`,
			`qtykeluar`,
			`qtymasukxharga`,
			`qtykeluarxharga`,
			`saldoawalqty`,
			`hargaratasaldoawal`,
			`nilaisaldoawal`)
			VALUES
			(
			 '".$pt."',
			 '".$kodebarang."',
			  ".$saldoakhirqty.",
			  0,
			  ".$user.",
			 '".$period."',
			  0,
			 '".$gudang."',
			  ".$masuk.",
			  ".$keluar.",
			  0,
			  0,
			  ".$sawal.",
			  ".$hargaratasaldoawal.",
			  ".$nilaisaldoawal."
			)";
  			
 if(mysql_query($str))
 {
 	$str="update ".$dbname.".log_5masterbarangdt set saldoqty=".$saldoakhirqty."
	      where kodebarang='".$kodebarang."' and kodeorg='".$pt."' 
		  and kodegudang='".$gudang."'";
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