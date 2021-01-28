<?php
require_once('master_validation.php');
require_once('config/connection.php');

if(isset($_POST['kodeorg'])){
    $kodeorg=$_POST['kodeorg'];
    $thnbudget=$_POST['thnbudget'];
    $jenis='preview';
}
else
{
    $kodeorg=$_GET['kodeorg'];
    $thnbudget=$_GET['thnbudget'];    
    $jenis='excel';
}
#ambil produksi pabrik

#ambil proporsi biaya produk
$persencpo=100;
$persenpk=0;
$hargacpo=0;
$hargapk=0;     
$str1="select a.* from ".$dbname.".bgt_prproduk a  where a.kodeorg='".$kodeorg."' 
            and tahunbudget=".$thnbudget." order by a.tahunbudget desc";
$res=mysql_query($str1);
while($bar=mysql_fetch_object($res))
{
     $persenpk=$bar->proporsibiayapk;
     $hargacpo=$bar->hargasatuancpo;
     $hargapk=$bar->hargasatuanpk;   
     $rpawalcpo=$bar->rupiahstokawalcpo;  
     $rpawalpk=$bar->rupiahstokawalpk;
}   
$persencpo=$persencpo-$persenpk;

if($hargacpo==0 or $hargacpo=='')
{
    exit('Mohon mengisi Proporsi Biaya CPO dan PK dari menu Anggaran->Setup');
}

#ambil jumlah CPO dan PK budget
$str="select  sum(kgcpo) as tcpo, sum(kgkernel) as tpk, 
           sum(kgcpo01) as cp1, 
           sum(kgcpo02) as cp2, 
           sum(kgcpo03) as cp3, 
           sum(kgcpo04) as cp4, 
           sum(kgcpo05) as cp5, 
           sum(kgcpo06) as cp6, 
           sum(kgcpo07) as cp7, 
           sum(kgcpo08) as cp8, 
           sum(kgcpo09) as cp9, 
           sum(kgcpo10) as cp10, 
           sum(kgcpo11) as cp11, 
           sum(kgcpo12) as cp12, 
           sum(kgker01) as pk1, 
           sum(kgker02) as pk2, 
           sum(kgker03) as pk3, 
           sum(kgker04) as pk4, 
           sum(kgker05) as pk5, 
           sum(kgker06) as pk6, 
           sum(kgker07) as pk7, 
           sum(kgker08) as pk8, 
           sum(kgker09) as pk9, 
           sum(kgker10) as pk10, 
           sum(kgker11) as pk12, 
           sum(kgker12) as pk12
from ".$dbname.".bgt_produksi_pks_vw
where tahunbudget =".$thnbudget." and millcode='".$kodeorg."'";
$res=mysql_query($str);
while($bar=mysql_fetch_array($res))
{
    for($x=1;$x<13;$x++){
     $rpcpo[]=$bar['cp'.$x]*$hargacpo;
     $rppk[]=$bar['pk'.$x]*$hargapk;
    }
}
#ambil biaya penjualan
$str="select a.noakun,b.namaakun,
         sum(rp01) as penj1,
         sum(rp02) as penj2,
         sum(rp03) as penj3,
         sum(rp04) as penj4,
         sum(rp05) as penj5,
         sum(rp06) as penj6,
         sum(rp07) as penj7,
         sum(rp08) as penj8,
         sum(rp09) as penj9,
         sum(rp10) as penj10,
         sum(rp11) as penj11,
         sum(rp12) as penj12
         from ".$dbname.".bgt_budget_detail a left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tahunbudget=".$thnbudget."
         and a.kodeorg like '".$kodeorg."%' and kodebudget='PENJUALAN'
         and a.noakun like '81%' group by a.noakun";
$res=mysql_query($str);
while($bar=mysql_fetch_array($res))
{
    $ket1[$bar['noakun']]=$bar['namaakun'];
    for($x=1;$x<13;$x++){
     $penj[$bar['noakun']][]=$bar['penj'.$x];
    }
}        

#ambil biaya angsung
$str="select a.noakun,b.namaakun,
         sum(rp01) as penj1,
         sum(rp02) as penj2,
         sum(rp03) as penj3,
         sum(rp04) as penj4,
         sum(rp05) as penj5,
         sum(rp06) as penj6,
         sum(rp07) as penj7,
         sum(rp08) as penj8,
         sum(rp09) as penj9,
         sum(rp10) as penj10,
         sum(rp11) as penj11,
         sum(rp12) as penj12
         from ".$dbname.".bgt_budget_detail a left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tahunbudget=".$thnbudget."
         and a.kodeorg like '".$kodeorg."%' and kodebudget not in('PENJUALAN','UMUM')
         and a.noakun like '63%' group by a.noakun";
$res=mysql_query($str);
while($bar=mysql_fetch_array($res))
{
    $ket2[$bar['noakun']]=$bar['namaakun'];
    for($x=1;$x<13;$x++){
     $bl[$bar['noakun']][]=$bar['penj'.$x];
    }
} 

#ambil biaya biaya tidak langsung
$str="select a.noakun,b.namaakun,
         sum(rp01) as penj1,
         sum(rp02) as penj2,
         sum(rp03) as penj3,
         sum(rp04) as penj4,
         sum(rp05) as penj5,
         sum(rp06) as penj6,
         sum(rp07) as penj7,
         sum(rp08) as penj8,
         sum(rp09) as penj9,
         sum(rp10) as penj10,
         sum(rp11) as penj11,
         sum(rp12) as penj12
         from ".$dbname.".bgt_budget_detail a left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tahunbudget=".$thnbudget."
         and a.kodeorg like '".$kodeorg."%' and kodebudget ='UMUM'
         and a.noakun like '7%' group by a.noakun";
$res=mysql_query($str);

while($bar=mysql_fetch_array($res))
{
    $ket3[$bar['noakun']]=$bar['namaakun'];
    for($x=1;$x<13;$x++){
     $btl[$bar['noakun']][]=$bar['penj'.$x];
    }
} 

#ambil biaya adm umum
$str="select a.noakun,b.namaakun,
         sum(rp01) as penj1,
         sum(rp02) as penj2,
         sum(rp03) as penj3,
         sum(rp04) as penj4,
         sum(rp05) as penj5,
         sum(rp06) as penj6,
         sum(rp07) as penj7,
         sum(rp08) as penj8,
         sum(rp09) as penj9,
         sum(rp10) as penj10,
         sum(rp11) as penj11,
         sum(rp12) as penj12
         from ".$dbname.".bgt_budget_detail a left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tahunbudget=".$thnbudget."
         and a.kodeorg like '".$kodeorg."%' and kodebudget  in ('UMUM','PENJUALAN')
         and a.noakun like '82%' group by a.noakun";
$res=mysql_query($str);
while($bar=mysql_fetch_array($res))
{
    $ket4[$bar['noakun']]=$bar['namaakun'];
    for($x=1;$x<13;$x++){
     $bau[$bar['noakun']][]=$bar['penj'.$x];
    }
}

if($jenis=='excel'){
$stream="Laporan Laba Rugi Menurut Budget Tahun ".$thnbudget." Unit:".$kodeorg."
                 <table border=1>";    
}
else{
$stream="Laporan Laba Rugi Menurut Budget Tahun ".$thnbudget." Unit:".$kodeorg."
                <img onclick=\"fisikKeExcel(event,'bgt_slave_laporan_LrPks.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
                 <table class=sortabe cellspacing=1 border=0>";
}
$stream.="<thead>
                 <tr>
                    <td align=center>Noakun</td>
                    <td align=center>Keterangan</td>
                    <td align=center>Jan".$thnbudget."</td>
                    <td align=center>Feb".$thnbudget."</td>
                    <td align=center>Mar".$thnbudget."</td>
                    <td align=center>Apr".$thnbudget."</td>
                    <td align=center>Mei".$thnbudget."</td>
                    <td align=center>Jun".$thnbudget."</td>
                    <td align=center>Jul".$thnbudget."</td>
                    <td align=center>Aug".$thnbudget."</td>
                    <td align=center>Sep".$thnbudget."</td>
                    <td align=center>Okt".$thnbudget."</td>
                    <td align=center>Nov".$thnbudget."</td>
                    <td align=center>Dec".$thnbudget."</td>
                    <td align=center>Total</td>    
                 </tr>
                 </thead>
                 <tbody>
                 ";

#==============pendapatan
$stream.="<tr class=rowcontent><td colspan=15>Pendapatan</td></tr>";
#penjualan CPO
$stream.="<tr class=rowcontent><td width=50px></td>
                      <td>Penjualan CPO</td>";
$tt=0;
foreach($rpcpo as $key =>$val){
      $stream.="<td align=right>".number_format($val,2)."</td>";
      $tt+=$val;
}
$stream.="<td align=right>".number_format($tt,2)."</td></tr>";
#penjualan PK
$stream.="<tr class=rowcontent><td width=50px></td>
                      <td>Penjualan PK</td>";
$tt=0;
foreach($rppk as $key =>$val){
      $stream.="<td align=right>".number_format($val,2)."</td>";
      $tt+=$val;
}
$stream.="<td align=right>".number_format($tt,2)."</td></tr>";
#total penjualan cpo dan pk
$stream.="<tr class=rowcontent><td colspan=2><b>Total Penjualan</b></td>";
$tt=0;
foreach($rppk as $key =>$val){
      $stream.="<td align=right><b>".number_format(($val+$rpcpo[$key]),2)."</b></td>";
      $tt+=$val+$rpcpo[$key];
}
$stream.="<td align=right><b>".number_format($tt,2)."</b></td></tr>";

#==============harga pokok penjualan (stok awal)
$stream.="<tr class=rowcontent><td colspan=15>Harga Pokok Penjualan</td></tr>";
#stok awal CPO
$stream.="<tr class=rowcontent><td width=50px></td>
                      <td>Stok  CPO</td>";
for($x=1; $x<=13;$x++){
      $stream.="<td align=right>".number_format($rpawalcpo,2)."</td>";
}
$stream.="</tr>";
#stok PK
$stream.="<tr class=rowcontent><td width=50px></td>
                      <td>Stok  PK</td>";
for($x=1; $x<=13;$x++){
      $stream.="<td align=right>".number_format($rpawalpk,2)."</td>";
}
$stream.="</tr>";
#total stok cpo dan pk
$stream.="<tr class=rowcontent><td colspan=2><b>Total Harga Pokok Penjualan</b></td>";
for($x=1; $x<=13;$x++){
      $stream.="<td align=right><b>".number_format(($rpawalpk+$rpawalcpo),2)."</b></td>";
}
$stream.="</tr>";
#laba rugi kotor (penjualan+stok awal
$stream.="<tr class=rowcontent><td colspan=2><b>Laba(Rugi) Kotor</b></td>";
$tt=0;
foreach($rppk as $key =>$val){
      $stream.="<td align=right><b>".number_format(($val+$rpcpo[$key]+$rpawalpk+$rpawalcpo),2)."</b></td>";
      $tt+=$val+$rpcpo[$key];
}
$stream.="<td align=right><b>".number_format(($tt+$rpawalpk+$rpawalcpo),2)."</b></td></tr>";

#biaya langsung
$stream.="<tr class=rowcontent><td colspan=15>Beban Usaha</td></tr>";
if(isset($ket2)){
        foreach($ket2 as $key =>$val){
        $tt=0;    
            $stream.="<tr class=rowcontent><td width=50px>".$key."</td>
                              <td>".$val."</td>";
             foreach($bl[$key] as $idx=>$VV){
              $stream.="<td align=right>".number_format($VV,2)."</td>";
              $tt+=$VV;
              $tbl[$idx]+=$VV;
              $gtt+=$VV;
             }
        $stream.="<td align=right>".number_format($tt,2)."</td></tr>";     
        }
        #total biaya langsug
        $stream.="<tr class=rowcontent><td colspan=2><b>Total Biaya Langsung</b></td>";
        for($x=0; $x<12;$x++){
              $stream.="<td align=right><b>".number_format($tbl[$x],2)."</b></td>";
        }
        $stream.="<td align=right><b>".number_format($gtt,2)."</b></td></tr>";
}
if(isset($ket3)){
    #biaya tidak langsung
    foreach($ket3 as $key =>$val){
    $tt=0;    
        $stream.="<tr class=rowcontent><td width=50px>".$key."</td>
                          <td>".$val."</td>";
         foreach($btl[$key] as $idx=>$VV){
          $stream.="<td align=right>".number_format($VV,2)."</td>";
          $tt+=$VV;
          $tbtl[$idx]+=$VV;
          $gttl+=$VV;
         }
    $stream.="<td align=right>".number_format($tt,2)."</td></tr>";     
    }

    #total biaya tidak langsug
    $stream.="<tr class=rowcontent><td colspan=2><b>Total BiayaTidak Langsung</b></td>";
    for($x=0; $x<12;$x++){
          $stream.="<td align=right><b>".number_format($tbtl[$x],2)."</b></td>";
    }
    $stream.="<td align=right><b>".number_format($gttl,2)."</b></td></tr>";
}
#biaya Admin UMUM
if(isset($ket4)){
    foreach($ket4 as $key =>$val){
    $tt=0;    
        $stream.="<tr class=rowcontent><td width=50px>".$key."</td>
                          <td>".$val."</td>";
         foreach($bau[$key] as $idx=>$VV){
          $stream.="<td align=right>".number_format($VV,2)."</td>";
          $tt+=$VV;
          $tbau[$idx]+=$VV;
          $gtbau+=$VV;
         }
    $stream.="<td align=right>".number_format($tt,2)."</td></tr>";     
    }

    #total biaya Admin
    $stream.="<tr class=rowcontent><td colspan=2><b>Total Biaya Administrasi Umum</b></td>";
    for($x=0; $x<12;$x++){
          $stream.="<td align=right><b>".number_format($tbau[$x],2)."</b></td>";
    }
    $stream.="<td align=right><b>".number_format($gtbau,2)."</b></td></tr>";
}

#biaya Penjualan
if(isset($ket1)){
    foreach($ket1 as $key =>$val){
    $tt=0;    
        $stream.="<tr class=rowcontent><td width=50px>".$key."</td>
                          <td>".$val."</td>";
         foreach($penj[$key] as $idx=>$VV){
          $stream.="<td align=right>".number_format($VV,2)."</td>";
          $tt+=$VV;
          $tpenj[$idx]+=$VV;
          $gtpenj+=$VV;
         }
    $stream.="<td align=right>".number_format($tt,2)."</td></tr>";     
    }
    #total Penjualan
    $stream.="<tr class=rowcontent><td colspan=2><b>Total Biaya Pemasaran</b></td>";
    for($x=0; $x<12;$x++){
          $stream.="<td align=right><b>".number_format($tpenj[$x],2)."</b></td>";
    }
    $stream.="<td align=right><b>".number_format($gtpenj,2)."</b></td></tr>";
}

#total Beban Usaha
    $stream.="<tr class=rowcontent><td colspan=2><b>Total Beban Usaha</b></td>";
    for($x=0; $x<12;$x++){
        $gr=$tbl[$x]+$tbtl[$x]+$tbau[$x]+$tpenj[$x];
          $stream.="<td align=right><b>".number_format($gr,2)."</b></td>";
        $gtgr+= $gr; 
    }
    $stream.="<td align=right><b>".number_format($gtgr,2)."</b></td></tr>";


#stok akhir Produk (sama dengan stok awal
#==============harga pokok penjualan (stok awal)
$stream.="<tr class=rowcontent><td colspan=15>Persediaan Akhir</td></tr>";
#stok awal CPO
$stream.="<tr class=rowcontent><td width=50px></td>
                      <td>CPO</td>";
for($x=1; $x<=13;$x++){
      $stream.="<td align=right>".number_format($rpawalcpo,2)."</td>";
}
$stream.="</tr>";
#stok PK
$stream.="<tr class=rowcontent><td width=50px></td>
                      <td>PK</td>";
for($x=1; $x<=13;$x++){
      $stream.="<td align=right>".number_format($rpawalpk,2)."</td>";
}
$stream.="</tr>";
#total stok cpo dan pk
$stream.="<tr class=rowcontent><td colspan=2><b>Total Persediaan Akhir</b></td>";
for($x=1; $x<=13;$x++){
      $stream.="<td align=right><b>".number_format(($rpawalpk+$rpawalcpo),2)."</b></td>";
}
$stream.="</tr>";

#laba Lugi Bersih
    $stream.="<tr class=rowcontent><td colspan=2><b>Laba(Rugi) Bersih:</b></td>";
    for($x=0; $x<12;$x++){
        $gr=($rpcpo[$x]+$rppk[$x])-($tbl[$x]+$tbtl[$x]+$tbau[$x]+$tpenj[$x]);
          $stream.="<td align=right><b>".number_format($gr,2)."</b></td>";
        $gtX+= $gr; 
    }
    $stream.="<td align=right><b>".number_format($gtX,2)."</b></td></tr>";

#end table
$stream.="</tbody><tfoot></tfoot></table>";

if($jenis=='preview')
 echo $stream;
else
{
 $stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="Laba Rugi Budget_".$koedorg." ".$thnbudget." ".$qwe;
    if(strlen($stream)>0)
    {
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $stream);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";
    }       
}
?>