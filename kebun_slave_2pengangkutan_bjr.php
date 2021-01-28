<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$param = $_GET;

######################################################################## Tabel 1
$stream = "BJR Aktual per Tahun Tanam per Afdeling per Tanggal <table border=1>
<thead>
<tr class=rowheader>
<td>".substr($_SESSION['lang']['nomor'],0,2).".</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['afdeling']."</td>
<td>".$_SESSION['lang']['tahuntanam']."</td>
<td>".$_SESSION['lang']['jjg']."</td>
<td>".$_SESSION['lang']['beratBersih']."</td>
<td>".$_SESSION['lang']['bjr']."</td>
</tr>
</thead><tbody>";
	
$sql="select sum(a.jjg) as janjang,sum(a.kgwb) as kgwb,b.tahuntanam,left(a.blok,6) as afdeling, sum(a.kgwb)/sum(a.jjg) as bjr,a.tanggal
	from ".$dbname.".kebun_spb_vw a left join ".$dbname.".setup_blok b on a.blok=b.kodeorg
	where tanggal like '".$param['periode']."%' and a.blok like '".$param['idKebun']."%' group by left(a.blok,6),a.tanggal,b.tahuntanam 
	order by b.tahuntanam";
$res1 = fetchData($sql);

$no=1;
foreach($res1 as $row) {
	$stream .= "<tr class=rowcontent>
	<td>".$no."</td>
	<td>".$row['tanggal']."</td>
	<td>".$row['afdeling']."</td>
	<td>".$row['tahuntanam']."</td>
	<td>".$row['janjang']."</td>
	<td>".number_format($row['kgwb'],2)."</td>
	<td>".number_format($row['bjr'],2)."</td>
	</tr>";
	$no++;
}
$stream.="</table><br>";

######################################################################## Tabel 2
$stream .= "BJR Aktual per Tahun Tanam per Blok per Tanggal<table border=1>
<thead>
<tr class=rowheader>
<td>".substr($_SESSION['lang']['nomor'],0,2).".</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['blok']."</td>
<td>".$_SESSION['lang']['tahuntanam']."</td>
<td>".$_SESSION['lang']['jjg']."</td>
<td>".$_SESSION['lang']['beratBersih']."</td>
<td>".$_SESSION['lang']['bjr']."</td>
</tr>
</thead><tbody>";
	
$sql="select sum(a.jjg) as janjang,sum(a.kgwb) as kgwb,b.tahuntanam,a.blok, sum(a.kgwb)/sum(a.jjg) as bjr,a.tanggal
	from ".$dbname.".kebun_spb_vw a left join ".$dbname.".setup_blok b on a.blok=b.kodeorg
	where tanggal like '".$param['periode']."%' and a.blok like '".$param['idKebun']."%' group by a.blok,a.tanggal,b.tahuntanam 
	order by b.tahuntanam";
$res1 = fetchData($sql);

$no=1;
foreach($res1 as $row) {
	$stream .= "<tr class=rowcontent>
	<td>".$no."</td>
	<td>".$row['tanggal']."</td>
	<td>".$row['blok']."</td>
	<td>".$row['tahuntanam']."</td>
	<td>".$row['janjang']."</td>
	<td>".number_format($row['kgwb'],2)."</td>
	<td>".number_format($row['bjr'],2)."</td>
	</tr>";
	$no++;
}
$stream.="</table><br>";

######################################################################## Tabel 3
$stream .= "KG Aktual berdasarkan Timbangan<table border=1>
<thead>
<tr class=rowheader>
<td>".substr($_SESSION['lang']['nomor'],0,2).".</td>
<td>".$_SESSION['lang']['tanggal']."</td>
<td>".$_SESSION['lang']['nospb']."</td>
<td>".$_SESSION['lang']['blok']."</td>
<td>".$_SESSION['lang']['tahuntanam']."</td>
<td>".$_SESSION['lang']['jjg']."</td>
<td>".$_SESSION['lang']['beratBersih']."</td>
<td>".$_SESSION['lang']['bjr']."</td>
</tr>
</thead><tbody>";
	
$sql="select a.nospb,a.jjg as janjang,a.kgwb,b.tahuntanam,a.blok, a.kgwb/a.jjg as bjr,a.tanggal
	from ".$dbname.".kebun_spb_vw a left join ".$dbname.".setup_blok b on a.blok=b.kodeorg
	where tanggal like '".$param['periode']."%' and a.blok like '".$param['idKebun']."%' 
	order by b.tahuntanam";
$res1 = fetchData($sql);

$no=1;
foreach($res1 as $row) {
	$stream .= "<tr class=rowcontent>
	<td>".$no."</td>
	<td>".$row['tanggal']."</td>
	<td>".$row['nospb']."</td>
	<td>".$row['blok']."</td>
	<td>".$row['tahuntanam']."</td>
	<td>".$row['janjang']."</td>
	<td>".number_format($row['kgwb'],2)."</td>
	<td>".number_format($row['bjr'],2)."</td>
	</tr>";
	$no++;
}
//=================================================
$stream.="</table><br>";

$stream.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

$nop_="bjrAktualHarian";
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