<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
$akun1=$_POST['akun1'];
$akun2=$_POST['akun2'];
$kdunit=$_POST['kdunit'];

//echo $akun1;exit();

if(($proses=='excel')or($proses=='pdf')){
	$kdorg=$_GET['kdorg'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	$akun1=$_GET['akun1'];
	$akun2=$_GET['akun2'];
	$kdunit=$_GET['kdunit'];
}

$optnmor=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjnvhc=makeOption($dbname, 'vhc_5jenisvhc','jenisvhc,namajenisvhc');
$optnmbar=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optnamacostumer=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
$namaakun=makeOption($dbname,'keu_5akun','noakun,namaakun');

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);


//exit("Error:$kdunit");

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{
	if($kdorg=='')
	{
		echo"Error: Organisasi tidak boleh kosong"; 
		exit;
	}
	else if($kdunit=='')
	{
		echo"Error: Unit organisasi tidak boleh kosong"; 
		exit;
	}
	else if(($tgl1_=='')or($tgl2_==''))
	{
		echo"Error: Tanggal tidak boleh kosong"; 
		exit;
	}		
}

  if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
	else $stream.="<table cellspacing='1' border='0' class='sortable'>";
	$stream.="<thead class=rowheader>
	 <tr>
		<td align=center>No</td>
		<td align=center>No Transaksi</td>
		<td align=center>No. Akun</td>
		<td align=center>Nanam Akun</td>
		<td align=center>Kode Organisasi</td>
		<td align=center>Tanggal</td>
		<td align=center>No. Tagihan</td>
		<td align=center>Keterangan</td>
		<td align=center>Supplier</td>
		<td align=center>No. Dokumen</td>
		<td align=center>Jumlah (Rp)</td>				
	</tr></thead>
	<tbody>";

$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' and kodeorg='".$kdunit."'";
//echo $str;
$res=mysql_query($str);					
$no=0;
$ttl=0;
while($bar=mysql_fetch_assoc($res))
{
	$aDet="select * from ".$dbname.".keu_kasbankdt where notransaksi='".$bar['notransaksi']."' and noakun between '".$akun1."' and '".$akun2."' and left(noakun,3)='116' order by notransaksi";
	//echo $aDet; 
	$bDet=mysql_query($aDet) or die (mysql_error($conn));
	while($cDet=mysql_fetch_assoc($bDet))
	{
		if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
						else $stream.="<tr class=rowcontent>";
			$no+=1;
				$stream.="
					<td align=center>".$no."</td>
					<td align=left>".$cDet['notransaksi']."</td>
					<td align=left>".$cDet['noakun']."</td>
					<td align=left>".$namaakun[$cDet['noakun']]."</td>
					<td align=left>".$cDet['kodeorg']."</td>
					<td align=left>".tanggalnormal($bar['tanggal'])."</td>
					<td align=left>".$cDet['keterangan1']."</td>
					<td align=left>".$cDet['keterangan2']."</td>
					<td align=left>".$optnamacostumer[$cDet['kodesupplier']]."</td>
					<td align=left>".$cDet['nodok']."</td>
					<td align=right>".number_format(abs($cDet['jumlah']))."</td>
			</tr>";	
			$total+=$cDet['jumlah'];		
		#untuk total
	}
					
}
	$stream.="
				<thead><tr>
					<td align=center colspan=10>Total Pengeluaran</td>
					<td align=right>".number_format($total)."</td>
				</tr>
	</tbody></table><br />
";

#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Penerimaan_dan_Pengeluaran".$tglSkrg;
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
		break;
	
	
	default:
	break;
}

?>