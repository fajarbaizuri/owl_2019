<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
//========================
  $gudang=$_POST['gudang'];
  $user  =$_SESSION['standard']['userid'];
  $awal  =$_POST['awal'];
  $akhir =$_POST['akhir'];
  $period=$_SESSION['gudang'][$gudang]['tahun']."-".$_SESSION['gudang'][$gudang]['bulan'];
//=============================
//next period is
  $tg=mktime(0,0,0,$_SESSION['gudang'][$gudang]['bulan']+1,15,$_SESSION['gudang'][$gudang]['tahun']);
  $nextPeriod=date('Y-m',$tg);
  $tg=mktime(0,0,0,substr($akhir,4,2),intval(substr($akhir,6,2)+1),$_SESSION['gudang'][$gudang]['bulan']);
  $nextAwal=date('Ymd',$tg);
  $tg=mktime(0,0,0,intval(substr($akhir,4,2)+1),substr($akhir,6,2),$_SESSION['gudang'][$gudang]['bulan']);
  $nextAkhir=date('Ymd',$tg); 
//================================================
//periksa periode
$str="select tutupbuku from ".$dbname.".setup_periodeakuntansi where periode='".$period."'
      and kodeorg='".$gudang."'";

$res=mysql_query($str);
$periode='benar';
if(mysql_num_rows($res)>0)
{
	while($bar=mysql_fetch_object($res))
	{
		if($bar->tutupbuku==0)
		{
			$periode='benar';
		}
		else
		{
			$periode='salah';
		}
	}
}
else
{
	$periode='salah';
}
//==========================================  
  
//cel apakah sudah posting semua pada periode tersebut;
$str="select count(tanggal) as tgl from ".$dbname.".log_transaksiht
      where kodegudang='".$gudang."' and tanggal>=".$awal." and tanggal<=".$akhir."
	  and post=0";  
$res=mysql_query($str);
$jlhNotPost=0;
while($bar=mysql_fetch_object($res))
{
	$jlhNotPost=$bar->tgl;
}

if($jlhNotPost>0)
{
	echo " Error: ".$_SESSION['lang']['belumposting']." > 0";
}  
else if($periode=='salah')
{
	echo " Error: Transaction period not defined or closed";
} 
else
{
   //ambil semua daftar barang dari log5_masterbarangdt berdasarkan gudang
   $str="select a.*,b.namabarang,b.satuan from ".$dbname.".log_5masterbarangdt a left join
         ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang 
          where kodegudang='".$gudang."' order by namabarang";
   $res=mysql_query($str);
   $r=mysql_num_rows($res);
   if($r>0)
   {	
   echo "<button class=mybutton onclick=saveSaldoFisik(".$r.");>".$_SESSION['lang']['proses']."</button>
         <button style='display:none;' onclick=lanjut(); id=lanjut>Lanjut</button>
         <table class=sortable cellspacing=1 border=0>
         <thead>
		   <tr class=rowheader>
		     <td>No</td>
			 <td>".$_SESSION['lang']['periode']."</td>
			 <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
			 <td>".$_SESSION['lang']['sloc']."</td>
			 <td>".$_SESSION['lang']['kodebarang']."</td>
			 <td>".$_SESSION['lang']['namabarang']."</td>
			 <td>".$_SESSION['lang']['satuan']."</td>
		   </tr>
		 </thead>
		 <tbody>
		";

   $no=0;
   while($bar=mysql_fetch_object($res))
   {
 	$no+=1;
	echo"<tr class=rowcontent id=row".$no.">
		     <td>".$no."</td>
			 <td id=period".$no.">".$period."</td>
			 <td id=pt".$no.">".$bar->kodeorg."</td>
			 <td id=gudang".$no.">".$gudang."</td>
			 <td id=kodebarang".$no.">".$bar->kodebarang."</td>
			 <td>".$bar->namabarang."</td>
			 <td>".$bar->satuan."</td>
		   </tr>";   
   }
	echo"</tbody><tfoot></tfoot></table>";
   }
   else
   {
   	echo "No data";
   }
 }
}
else
{
	echo " Error: Transaction Period missing";
}
?>