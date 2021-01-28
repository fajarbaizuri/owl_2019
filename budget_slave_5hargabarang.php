<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$regional=$_POST['regional'];
$sumberharga=$_POST['sumberharga'];
$kelompokbarang=$_POST['kelompokbarang'];

//check, one-two
if($tahunbudget==''){
    echo "WARNING: silakan mengisi tahun budget."; exit;
}
if(strlen($tahunbudget)!=4){
    echo "WARNING: silakan mengisi tahun budget dengan benar."; exit;
}
if($regional==''){
    echo "WARNING: silakan mengisi region."; exit;
}
if($sumberharga==''){
    echo "WARNING: silakan memilih sumberharga."; exit;
}
if($kelompokbarang==''){
    echo "WARNING: silakan memilih kelompokbarang."; exit;
}

//bila sudah ada data, masukkan ke dalam array
//$str2="select * from ".$dbname.".bgt_masterbarang
//    where regional = '".$regional."' and tahunbudget = '".$tahunbudget."' and sumberharga = '".$sumberharga."'
//    and kodebarang like '".$kelompokbarang."%' order by kodebarang";
//$res2=mysql_query($str2);
//while($bar2= mysql_fetch_object($res2))
//{
//   $isidata[$bar2->kodebarang][kodebarang]=$bar2->kodebarang;
//   $isidata[$bar2->kodebarang][kodeorg]=$bar2->kodeorg;
//   $isidata[$bar2->kodebarang][harga]=$bar2->hargasatuan;
//   $isidata[$bar2->kodebarang][varian]=$bar2->variant;
//   $kobar.="'".$bar2->kodebarang."',";
//}
//$kobar=substr($kobar,0,-1);



//ambil data dari saldo bulanan, masukkan ke dalam array
//$str="select kodeorg, kodebarang, hargalastin from ".$dbname.".log_5masterbarangdt
//    where kodeorg = '".$sumberharga."' and kodebarang like '".$kelompokbarang."%' order by kodebarang";
$str="SELECT distinct a.*,(select b.hargarata from ".$dbname.".log_5saldobulanan b 
    where b.kodebarang=a.kodebarang and b.hargarata>0 and left(b.kodegudang,4) in 
        (select kodeunit from ".$dbname.".bgt_regional_assignment where regional = '".$sumberharga."')
    order by lastupdate desc limit 1) as hargarata 
    FROM ".$dbname.".log_5masterbarang a where a.kodebarang like '".$kelompokbarang."%' order by a.kodebarang";
//echo $str;
//        (select kodeunit from ".$dbname.".bgt_regional_assignment where regional = '".$sumberharga."') // = daftar pt diganti regional
$kobar='';
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $isidata[$bar->kodebarang][kodebarang]=$bar->kodebarang;
   $isidata[$bar->kodebarang][kodeorg]=$sumberharga;
   $isidata[$bar->kodebarang][hargarata]=$bar->hargarata;
   $kobar.="'".$bar->kodebarang."',";
}
$kobar=substr($kobar,0,-1);

//cari nama barang, yang dalam array kobar
$str="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang
    where kodebarang in (".$kobar.")";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $isidata[$bar->kodebarang][namabarang]=$bar->namabarang;
   $isidata[$bar->kodebarang][satuan]=$bar->satuan;
}

echo"<table width=100%><tr id=baris_0 class=rowheader>";
    echo"<td align=left>Set ".$_SESSION['lang']['varian']."";
    echo"<input type=text id=varianall size=5 value='0.00' maxlength=5 class=myinputtext onkeypress=\"return angka_doangsamaminus(event);\">
            <button class=mybutton id=proses onclick=updateHargaall()>".$_SESSION['lang']['proses']."</button></td>";
    echo"<td align=right><button class=mybutton id=simpan onclick=simpanHarga(1)>".$_SESSION['lang']['save']."</button></td>";
echo"</tr></table>";
echo"<table id=container9 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['nomor']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['regional']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['sumberHarga']."</td>
            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>
            <td align=center>".$_SESSION['lang']['varian']."</td>
            <td align=center>".$_SESSION['lang']['hargabudget']."</td>
       </tr>  
     </thead>
     <tbody>";

//tampilkan data dalam array
if(empty($isidata)){
    
}else
foreach($isidata as $baris)
{
    $no+=1;
    echo"<tr id=baris_".$no." class=rowcontent>";
        echo"<td>".$no."</td>";
        echo"<td>".$tahunbudget."</td>";
        echo"<td>".$regional."</td>";
        echo"<td><label id=kode_".$no.">".$baris[kodebarang]."</label></td>";
        echo"<td>".$baris[namabarang]."</td>";
        echo"<td>".$baris[satuan]."</td>";
        echo"<td>".$sumberharga."</td>";
        echo"<td align=right><label id=rata_".$no.">".number_format($baris[hargarata],2)."</label></td>";
        echo"<td><input type=text id=varian_".$no." size=5 value='0.00' maxlength=5 class=myinputtext onkeyup=\"hitungharga(".$baris[hargarata].",this.value,".$no.")\" onkeypress=\"return angka_doangsamaminus(event);\"></td>";
        $hargarata=$baris[hargarata]+0; $hargarata=round($hargarata*100)/100;
        echo"<td><input type=text id=harga_".$no." size=15 value='".$hargarata."' maxlength=15 class=myinputtext onkeyup=\"hitungpersen(".$baris[hargarata].",this.value,".$no.")\" onkeypress=\"return angka_doang(event);\"></td>";
    echo"</tr>";
}    

echo "     </tbody>
     <tfoot>
     </tfoot>		 
     </table>";