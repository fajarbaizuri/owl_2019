<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$tipe=$_POST['tipe'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
$kdunit=$_POST['kdunit'];




if(($proses=='excel')or($proses=='pdf')){
	$kdorg=$_GET['kdorg'];
	$tipe=$_GET['tipe'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	$kdunit=$_GET['kdunit'];
}

$optnmor=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjnvhc=makeOption($dbname, 'vhc_5jenisvhc','jenisvhc,namajenisvhc');
$optnmbar=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optnamacostumer=makeOption($dbname,'log_5supplier','supplierid,namasupplier');

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);


//exit("Error:$kdunit");

if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

	if ($_SESSION['empl']['tipelokasitugas']=='HOLDING')
	{
		if($kdorg=='')
		{
			echo"Error: Organisasi tidak boleh kosong"; 
			exit;
		}
		else if(($tgl1_=='')or($tgl2_==''))
		{
			echo"Error: Tanggal tidak boleh kosong"; 
			exit;
		}
	
		else if($tgl1>$tgl2)
		{
			echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua"; 
			exit;
		
		}
	}
	else
	{
		if($kdorg=='')
		{
			echo"Error: Organisasi tidak boleh kosong"; 
			exit;
		}
		else if(($tgl1_=='')or($tgl2_==''))
		{
			echo"Error: Tanggal tidak boleh kosong"; 
			exit;
		}
	
		else if($tgl1>$tgl2)
		{
			echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua"; 
			exit;
		
		}
		else if($kdunit=='')
		{
			echo"Error: Unit organisasi tidak boleh kosong"; 
			exit;
		
		}
	}
}


if($kdunit=='')
{
	$where.=" and kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$kdorg."')";
}
else
	$where.=" and kodeorg='".$kdunit."'";	

              if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                 <tr>
				 	<td align=center>No</td>
					<td align=center>No Transaksi</td>
					<td align=center>Kode Organisasi</td>
					<td align=center>Tanggal</td>
					<td align=center>Keterangan</td>
					<td align=center>Jumlah (Rp)</td>				
  				</tr></thead>
                <tbody>";

if ($tipe==BK) 
{
	//tipe KK
	$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%BK%' ".$where." order by notransaksi";
}
else if($tipe==BM)
{
	$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%BM%' ".$where." order by notransaksi";
//echo $str;
}
else if ($tipe==KK)
{
	$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%KK%' ".$where." order by notransaksi";
}
else if ($tipe==KM)
{
	$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%KM%' ".$where." order by notransaksi";
}
else
{
	$str="select * from ".$dbname.".keu_kasbankht where tanggal between '".$tgl1."' and '".$tgl2."' ".$where." order by notransaksi";
}
$res=mysql_query($str);					
$no=0;
$ttl=0;
while($bar=mysql_fetch_assoc($res))
{
	if($proses=='excel')
					$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
		$no+=1;
			$stream.="
				<td align=center>".$no."</td>
				<td align=left>".$bar['notransaksi']."</td>
				<td align=left>".$bar['kodeorg']."</td>
				<td align=left>".tanggalnormal($bar['tanggal'])."</td>
				<td align=left>".$bar['keterangan']."</td>
				<td align=right>".number_format($bar['jumlah'])."</td>
		</tr>";		
		#untuk total
		$total+=$bar['jumlah'];				
}
	$stream.="
				<thead><tr>
					<td align=center colspan=5>Total Pengeluaran</td>
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