<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
	$unit=$_POST['unit'];
	$periode=$_POST['periode'];
	$jenis=$_POST['jenis'];
	$kodebarang=$_POST['kodebarang'];

//echo "unit: ".$unit." periode: ".$periode." jenis: ".$jenis." kodebarang: ".$kodebarang;

if($unit==''){
	echo "Warning: silakan mengisi gudang"; exit;
}
if($periode==''){
	echo "Warning: silakan mengisi periode"; exit;
}
if($jenis==''){
	echo "Warning: silakan mengisi tipe transaksi"; exit;
}

/*
$str="select induk from ".$dbname.".organisasi
      where kodeorganisasi ='".$unit."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$induk=$bar->induk;
	$hasil.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}

*/
$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where periode ='".$periode."' and kodeorg='".$unit."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$tanggalmulai=$bar->tanggalmulai;
	$tanggalsampai=$bar->tanggalsampai;
}

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

//echo"<br>str :".$str; exit;
//=================================================
	 
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=14>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		    echo"<thead><tr>
			  <td align=center>No.</td>";
			  echo"<td align=center>".$_SESSION['lang']['tipetransaksi']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['tanggal']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['kodebarang']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['namabarang']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['jumlah']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['satuan']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['hargasatuan']."</td>";
			  echo"<td align=center>".$_SESSION['lang']['totalharga']."</td>";
			  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td align=center>".$_SESSION['lang']['nopo']."</td>";
			  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td align=center>".$_SESSION['lang']['supplier']."</td>";
			  if($jenis=='7')echo"<td align=center>".$_SESSION['lang']['tujuan']."</td>";
			  if(($jenis=='6')){
			  echo"<td align=center>".$_SESSION['lang']['kodeblok']."</td>";
			  echo"<td align=center>Afdeling</td>";
			  }
			  if(($jenis=='5')){
			  echo"<td align=center>Mesin</td>";
			  echo"<td align=center>Stasiun</td>";
			  }
			  if(($jenis=='5')or($jenis=='6'))echo"<td align=center>".$_SESSION['lang']['kodevhc']."</td>";
			  
			  echo"<td align=center>".$_SESSION['lang']['notransaksi']."</td>";
			echo"</tr></thead><tbody>";  
			// 0 = Mutasi dalam perjalanan
			// 1 = Masuk
			// 2 = Pengembalian pengeluaran
			// 3 = Penerimaan mutasi
			// 5 = Pengeluaran
			// 6 = Pengembalian penerimaan
			// 7 = Pengeluaran mutasi
		while($bar=mysql_fetch_object($res))
		{
			$no+=1; $total=0;
//			$periode=date('Y-m-d H:i:s');
//			$kodebarang=$bar->kodebarang;
//			$namabarang=$bar->namabarang; 
//			$kuantitas =$bar->kuan;
//			echo"<tr class=rowcontent  class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarangHarga(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
//			$total=$bar->jumlah*$bar->hargasatuan;
			if(($jenis=='0')or($jenis=='1')or($jenis=='2')) 
                            $total=$bar->jumlah*$bar->hargasatuan; 
                        else
			$total=$bar->jumlah*$bar->hargarata;
//			echo"<tr class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"viewDetail(event,'".$bar->kodevhc."','".$tanggalmulai."','".$tanggalsampai."','".$unit."','".$periode."');\">
			
//			if($jenis='1')
			echo"<tr class=rowcontent>
				  <td align=right>".$no."</td>";
				  echo"<td align=right>".$jenis."</td>";
				  echo"<td>".tanggalnormal($bar->tanggal)."</td>";
				  echo"<td>".$bar->kodebarang."</td>";
				  echo"<td nowrap>".$bar->namabarang."</td>";
				  echo"<td align=right>".number_format($bar->jumlah,2)."</td>";
				  echo"<td>".$bar->satuan."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2'))
                                       echo"<td align=right>".number_format($bar->hargasatuan)."</td>"; 
                                  else
				  	echo"<td align=right>".number_format($bar->hargarata)."</td>";
				  echo"<td align=right>".number_format($total)."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td nowrap align=center>".$bar->nopo."</td>";
				  if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td nowrap>".$bar->namasupplier."</td>";
				  if($jenis=='7')echo"<td>".$bar->gudangx."</td>";
				  if(($jenis=='6')){
				  echo"<td>".$bar->kodeblok."</td>";
				  echo"<td>".$bar->afd."</td>";
				  }
				  if(($jenis=='5')){
				  echo"<td>".$bar->mesin."</td>";
				  echo"<td>".$bar->stasiun."</td>";
				  }
				  if(($jenis=='5')or($jenis=='6'))echo"<td>".$bar->kodemesin."</td>";
				  echo"<td nowrap>".$bar->notransaksi."</td>";
				echo"</tr>";
//				$gtotal+=$total;
		}
				echo"</tbody<tfoot></tfoot>";
/*
			echo"<tr class=rowheader>
				  <td colspan=9 align=right>TOTAL</td>
				  <td align=right>".number_format($gtotal)."</td>
				</tr>";

*/	}
?>