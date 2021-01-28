<?php 
// file creator: dhyaz aug 3, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$kodeorg=$_POST['kodeorg'];

$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
    where tipe in('KEBUN','PABRIK','GUDANG','TRAKSI','KANWIL') or (tipe='HOLDING' and length(kodeorganisasi)=4)
    order by kodeorganisasi";
$res=mysql_query($str);
$kamus=array();
while($bar=mysql_fetch_object($res))
{
    $kamus[$bar->kodeorganisasi]=$bar->namaorganisasi;
}

// ambil data
$str="select * from ".$dbname.".setup_periodeakuntansi where kodeorg = '".$kodeorg."' order by periode desc, kodeorg";
if ($kodeorg=='')$str="select * from ".$dbname.".setup_periodeakuntansi order by periode desc, kodeorg";
$no=1;
$res=mysql_query($str);
while($baris= mysql_fetch_object($res))
{
    echo"<tr class=rowcontent>";
    echo"<td align=right>".$no."</td>";
    echo"<td>".$baris->kodeorg." (".$kamus[$baris->kodeorg].")</td>";
    $qwe=explode('-',$baris->periode);
    echo"<td align=center>".$qwe[1]."-".$qwe[0]."</td>";
    echo"<td align=center>".tanggalnormal($baris->tanggalmulai)."</td>";
    echo"<td align=center>".tanggalnormal($baris->tanggalsampai)."</td>";
    $tutup=$baris->tutupbuku;
    if($tutup=='1')$tutup='Closed';
    echo"<td align=center>".$tutup."</td>";
    echo"</tr>";
    $no+=1;
}

?>
