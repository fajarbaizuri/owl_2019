<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt'];
	$unit=$_GET['unit'];
	$periode=$_GET['periode'];
	$jenis=$_GET['jenis'];
	$kodebarang=$_GET['kodebarang'];

if($unit==''){
	echo "Warning: silakan mengisi gudang"; exit;
}
if($periode==''){
	echo "Warning: silakan mengisi periode"; exit;
}
if($jenis==''){
	echo "Warning: silakan mengisi tipe transaksi"; exit;
}
$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where periode ='".$periode."' and kodeorg='".$unit."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$tanggalmulai=$bar->tanggalmulai;
	$tanggalsampai=$bar->tanggalsampai;
}

/*
$str="select distinct kodebarang, namabarang from ".$dbname.".log_5masterbarang";
$res=mysql_query($str);
$optper="";
while($bar=mysql_fetch_object($res))
{
	$barang[$bar->kodebarang]=$bar->namabarang;
}	

*/	
	if($kodebarang=='')
	$str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, c.namasupplier, a.kodeblok,left(a.kodeblok,6) as afd, a.kodemesin, a.notransaksi, d.gudangx ,e.namaorganisasi as mesin ,f.namaorganisasi as stasiun from ".$dbname.".log_transaksi_vw a
	      left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
	      left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
	      left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
		  left join ".$dbname.".organisasi e on a.kodeblok=e.kodeorganisasi
		  left join ".$dbname.".organisasi f on left(a.kodeblok,6)=f.kodeorganisasi
	      where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.tipetransaksi = '".$jenis."' and a.kodegudang = '".$unit."'
		  order by a.tanggal";
	else
	$str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, c.namasupplier, a.kodeblok,left(a.kodeblok,6) as afd, a.kodemesin, a.notransaksi, d.gudangx ,e.namaorganisasi as mesin ,f.namaorganisasi as stasiun from ".$dbname.".log_transaksi_vw a
	      left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
	      left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
	      left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi 
		  left join ".$dbname.".organisasi e on a.kodeblok=e.kodeorganisasi
		  left join ".$dbname.".organisasi f on left(a.kodeblok,6)=f.kodeorganisasi		  
	      where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.tipetransaksi = '".$jenis."' and a.kodegudang = '".$unit."' and a.kodebarang = '".$kodebarang."'
		  order by a.tanggal";

//echo"str :".$str;
//=================================================
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=14>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		$stream.=$_SESSION['lang']['transaksigudang'].": ".$jenis." : ".$unit." : ".$periode." (".tanggalnormal($tanggalmulai)." - ".tanggalnormal($tanggalsampai).")<br>
		<table border=1>
				    <tr>
			  <td bgcolor=#DEDEDE align=center>No.</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipetransaksi']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['totalharga']."</td>";
			  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopo']."</td>";
			  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>";
			  if($jenis=='7')$stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tujuan']."</td>";
			  if(($jenis=='6')){
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeblok']."</td>";
			   $stream.="<td bgcolor=#DEDEDE align=center>Afdeling</td>";
			  }
			  if(($jenis=='5')){
			  $stream.="<td bgcolor=#DEDEDE align=center>Mesin</td>";
			   $stream.="<td bgcolor=#DEDEDE align=center>Stasiun</td>";
			  }
			  if(($jenis=='5')or($jenis=='6'))$stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']."</td>";
			  $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>";
					$stream.="</tr>";
	while($bar=mysql_fetch_object($res))
	{
		$no+=1; $total=0;
			if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3')) $total=$bar->jumlah*$bar->hargasatuan; else
			$total=$bar->jumlah*$bar->hargarata;
		$stream.="<tr>";
				  $stream.="<td align=right>".$no."</td>";
				  $stream.="<td align=right>".$jenis."</td>";
				  $stream.="<td>".$bar->tanggal."</td>";
				  $stream.="<td>".$bar->kodebarang."</td>";
				  $stream.="<td nowrap>".$bar->namabarang."</td>";
				  $stream.="<td align=right>".number_format($bar->jumlah,2)."</td>";
				  $stream.="<td>".$bar->satuan."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td align=right>".number_format($bar->hargasatuan)."</td>"; else
				  	$stream.="<td align=right>".number_format($bar->hargarata)."</td>";
				  $stream.="<td align=right>".number_format($total)."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td nowrap>".$bar->nopo."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))$stream.="<td nowrap>".$bar->namasupplier."</td>";
				  if($jenis=='7')$stream.="<td>".$bar->gudangx."</td>";
				  if(($jenis=='6')){
				  $stream.="<td>".$bar->kodeblok."</td>";
				  $stream.="<td>".$bar->afd."</td>";
				  }
				  if(($jenis=='5')){
				  $stream.="<td>".$bar->mesin."</td>";
				  $stream.="<td>".$bar->stasiun."</td>";
				  }
				  if(($jenis=='5')or($jenis=='6'))$stream.="<td>".$bar->kodemesin."</td>";
				  $stream.="<td nowrap>".$bar->notransaksi."</td>";
			$stream.="</tr>"; 	
	}

	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
  }
	
$nop_="TransaksiGudang_".$jenis."".$unit."_".$periode;
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