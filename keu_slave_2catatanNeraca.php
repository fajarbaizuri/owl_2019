<?php
// file creator: dhyaz aug 3, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_POST['pt'];
$periode=$_POST['periode'];
$akundari=$_POST['akundari'];
$akunsampai=$_POST['akunsampai'];

//check, one-two
if($akundari==''){
    echo "WARNING: silakan memilih akun."; exit;
}
if($akunsampai==''){
    echo "WARNING: silakan memilih akun."; exit;
}

$qwe=explode("-",$periode);
$periode=$qwe[0].$qwe[1];
$bulan= $qwe[1];       
//          echo $pt." ".$gudang." ".$tanggal1." ".$tanggal2." ".$akundari." ".$akunsampai."<br>";

// kamus akun
$str="select noakun,namaakun from ".$dbname.".keu_5akun
                        where level = '5'
                        order by noakun
                        ";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $namaakun[$bar->noakun]=$bar->namaakun;

        }

//ambil saldo awal
if($gudang==''){
    $str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'";
    $wheregudang='';
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
	$wheregudang.="'".strtoupper($bar->kodeorganisasi)."',";
    }
    $wheregudang="and kodeorg in (".substr($wheregudang,0,-1).") ";
}else{
    $wheregudang="and kodeorg = '".$gudang."' ";
}
$str="select * from ".$dbname.".keu_saldobulanan where periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' ".$wheregudang." order by noakun, kodeorg";
//$saldoawal=0;
$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $qweawal="awal".$bulan;
    $qwedebet="debet".$bulan;
    $qwekredit="kredit".$bulan;
    $saldoawal=$bar->$qweawal; $totalawal+=$saldoawal;
    $saldodebet=$bar->$qwedebet; $totaldebet+=$saldodebet; 
    $saldokredit=$bar->$qwekredit; $totalkredit+=$saldokredit;
    $saldoakhir=$saldoawal+$saldodebet-$saldokredit; $totalakhir+=$saldoakhir;
    echo"<tr class=rowcontent>";
        echo"<td>".$no."</td>";
        echo"<td>".$bar->noakun."</td>";
        echo"<td>".$namaakun[$bar->noakun]."</td>";
        echo"<td>".$bar->kodeorg."</td>";
        echo"<td align=right>".number_format($saldoawal)."</td>";
        echo"<td align=right>".number_format($saldodebet)."</td>";
        echo"<td align=right>".number_format($saldokredit)."</td>";
        echo"<td align=right>".number_format($saldoakhir)."</td>";
     echo"</tr>";
} 
    echo"<tr>";
        echo"<td align=center colspan=4>Total</td>";
        echo"<td align=right>".number_format($totalawal)."</td>";
        echo"<td align=right>".number_format($totaldebet)."</td>";
        echo"<td align=right>".number_format($totalkredit)."</td>";
        echo"<td align=right>".number_format($totalakhir)."</td>";
     echo"</tr>";
echo"</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";

