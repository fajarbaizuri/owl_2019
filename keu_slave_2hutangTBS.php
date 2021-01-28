<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


if(isset($_POST['kodeorg'])){
  $param=$_POST;
}
else
{
  $param=$_GET;
}

#ambil kodeparameter hutang TBS
$akuntbs='';
$str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='HTBS'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
   $akuntbs=$bar->nilai;
}
if($akuntbs=='')
{
  exit("Error: Akun hutang supplier TBS dengan kode HTBS belum terdaftar pada parameter aplikasi");
}

#ambil saldo awal basing-masing supplier dari jurnal
$str="select sum(debet-kredit) as salwal,kodesupplier,namasupplier from ".$dbname.".keu_jurnaldt_vw a 
      left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid
	  where tanggal < '".tanggalsystem($param['tgl1'])."' and a.noakun='".$akuntbs."' group by kodesupplier order by namasupplier";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
	{
	  $sawal[$bar->kodesupplier]=$bar->salwal;
    }	


$str="select sum(kredit) as hutang, sum(debet) as bayar,kodesupplier,namasupplier from ".$dbname.".keu_jurnaldt_vw a 
      left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid
	  where tanggal between '".tanggalsystem($param['tgl1'])."' and '".tanggalsystem($param['tgl2'])."' and a.noakun='".$akuntbs."' 
	  group by kodesupplier order by namasupplier";
	  
//=================================================
$stream.="Hutang Supplier TBS periode ".$param['tgl1']."s/d".$param['tgl2'].":<br>
<table border=1 cellspacing=0>
			<tr class=ruheader>
			  <td bgcolor=#DEDEDE align=center>No.</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['unit']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodesupplier']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namasupplier']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nilaihutang']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dibayar']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sisa']."</td>
			</tr>";
			
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=loadDetailHutangTBS('".$bar->kodesupplier."','".tanggalsystem($param['tgl1'])."','".tanggalsystem($param['tgl2'])."','".$akuntbs."',event)>
			  <td>".$no."</td>
			  <td>".$param['kodeorg']."</td>
			  <td>".$bar->kodesupplier."</td>
			  <td>".$bar->namasupplier."</td>
			  <td align=right>".number_format($sawal[$bar->kodesupplier])."</td>
			  <td align=right>".number_format($bar->hutang)."</td>
			  <td align=right>".number_format($bar->bayar)."</td>
			  <td align=right>".number_format($sawal[$bar->kodesupplier]+($bar->hutang-$bar->bayar))."</td>		   
			</tr>";
			$tawal+=$sawal[$bar->kodesupplier];
			$thutang+=$bar->hutang;
			$tbayar+=$bar->bayar;
			$tsisa+=$sawal[$bar->kodesupplier]+($bar->hutang-$bar->bayar);
	}
		$stream.="<tr class=rowcontent>
			  <td colspan=4>Total</td>
			  <td align=right>".number_format($tawal)."</td>
			  <td align=right>".number_format($thutang)."</td>
			  <td align=right>".number_format($tbayar)."</td>
			  <td align=right>".number_format($tsisa)."</td>		   
			</tr>";	
  $stream.="</table>";	

$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

if(isset($_POST['kodeorg'])){
echo $stream;
}
else{
	$nop_="Hutang Supplier TBS ".$param['tgl1'];
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
}
?>