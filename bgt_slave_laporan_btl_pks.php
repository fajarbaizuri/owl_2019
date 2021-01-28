<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$thnbudget=$_POST['thnbudget'];
#ambil produksi pks
$prd=0;
$str="select sum(kgcpo) as cpo,sum(kgkernel) as kernel,sum(kgolah)  as tbs from ".$dbname.".bgt_produksi_pks_vw 
      where tahunbudget=".$thnbudget." and millcode = '".$kodeorg."'";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    $prd=$bar->cpo+$bar->kernel;
    $kgcpo=$bar->cpo;
    $kgkernel=$bar->kernel;
    $totTbs=$bar->tbs;
}
#ambil proporsi biaya produk
$persencpo=100;
$persenpk=0;
$str1="select a.* from ".$dbname.".bgt_prproduk a  where a.kodeorg='".$kodeorg."' 
            and tahunbudget=".$thnbudget." order by a.tahunbudget desc";
$res=mysql_query($str1);
while($bar=mysql_fetch_object($res))
{
     $persenpk=$bar->proporsibiayapk;
}   
$persencpo=$persencpo-$persenpk;


$str="select a.*,b.namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where (a.kodebudget='UMUM') and tahunbudget=".$thnbudget." and a.kodeorg='".$kodeorg."'";
//echo $str;
$res=mysql_query($str);
$no=0;
$rpperha=0;
$rptbs=0;
$str2="select sum(kgolah) as tbs,sum(kgcpo) as cpo,sum(kgkernel) as kernel from ".$dbname.".bgt_produksi_pks_vw 
      where tahunbudget=".$thnbudget." and millcode = '".$kodeorg."'";
$res2=mysql_query($str2);
//echo mysql_error($conn);
while($bar2=mysql_fetch_object($res2))
{
    $tbs=$bar2->tbs;
    $cpo=$bar2->cpo;
    $pk=$bar2->kernel;
    
    $totTbs=$bar2->tbs;
    $prd=$bar2->cpo+$bar2->kernel;
    $totCpo=$bar2->cpo;
    $totKer=$bar2->kernel;
}
$oil=$cpo+$pk;
$stream="<fieldset><legend>".$_SESSION['lang']['produksipabrik']." </legend>
<table class=sortable cellspacing=1 border=0 width=300px>
     <thead>
         <tr class=rowheader>
           <td align=center>Proporsi</td>
           <td align=center>".$_SESSION['lang']['cpo']."(Ton)</td>
           <td align=center>".$_SESSION['lang']['kernel']."(Ton)</td> 
           <td align=center>".$_SESSION['lang']['tbs']."(Ton)</td>    
         </tr>
         </thead>
         <tbody>
         <tr class=rowcontent>
           <td align=right>CPO(".$persencpo."%) PK(".$persenpk."%) </td>
           <td align=right>".@number_format($kgcpo/1000,0,".",",")."</td>
           <td align=right>".@number_format($kgkernel/1000,0,".",",")."</td>       
           <td align=right>".number_format($totTbs/1000,0,".",",")."</td>
         </tr>     
     </tbody>
     <tfoot>
     </tfoot>
     </table>
     </fieldset>"; 
$stream.="<fieldset><legend>".$_SESSION['lang']['list']."
            Result:
            <img onclick=\"fisikKeExcel(event,'bgt_laporan_biaya_tdk_lngs_pks_excel.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
            </legend>
             Unit:".$kodeorg." Tahun Budget:".$thnbudget."
             <table class=sortable cellspacing=1 border=0' width=1600px>
	     <thead>
	<tr class=rowheader>
                   <td align=center>".$_SESSION['lang']['nourut']."</td>
                   <td align=center>".$_SESSION['lang']['noakun']."</td>
                   <td align=center>".$_SESSION['lang']['namaakun']."</td>
                   <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
                    <td align=center>".$_SESSION['lang']['rpperkg']."-CPO</td>
                    <td align=center>".$_SESSION['lang']['rpperkg']."-PK</td>  
                   <td align=center>".$_SESSION['lang']['rpperkg']."-TBS</td>
                   <td align=center>01(Rp)</td>
                   <td align=center>02(Rp)</td>
                   <td align=center>03(Rp)</td>
                   <td align=center>04(Rp)</td>
                   <td align=center>05(Rp)</td>
                   <td align=center>06(Rp)</td>
                   <td align=center>07(Rp)</td>
                   <td align=center>08(Rp)</td>
                   <td align=center>09(Rp)</td>
                   <td align=center>10(Rp)</td>
                   <td align=center>11(Rp)</td>
                   <td align=center>12(Rp)</td>
                 </tr>
                </thead>
                <tbody>"; 

while($bar=mysql_fetch_object($res))
{
    $prd=$cpo+$pk;
    @$rptbs=$bar->rupiah/$totTbs;
     @$rpperkgcpo=$bar->rupiah*($persencpo/100)/$kgcpo;
    @$rpperkgpk=$bar->rupiah*($persenpk/100)/$kgkernel;
    
    $no+=1;
    $stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperkgcpo,2,'.',',')."</td>
            <td align=right>".number_format($rpperkgpk,2,'.',',')."</td>  
           <td align=right>".number_format($rptbs,2,'.',',')."</td> 
           <td align=right>".number_format($bar->rp01,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp02,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp03,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp04,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp05,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp06,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp07,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp08,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp09,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp10,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp11,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp12,0,'.',',')."</td>
         </tr>";
    $totRup+=$bar->rupiah;
    $grTotRp+=$rpperha;
    $grTotTbs+=$rptbs;
    $tot[1]+=$bar->rp02;$tot[2]+=$bar->rp02;$tot[3]+=$bar->rp03;
    $tot[4]+=$bar->rp04;$tot[5]+=$bar->rp05;$tot[6]+=$bar->rp06;
    $tot[7]+=$bar->rp07;$tot[8]+=$bar->rp08;$tot[9]+=$bar->rp09;
    $tot[10]+=$bar->rp10;$tot[11]+=$bar->rp11;$tot[12]+=$bar->rp12;
}
$trpperkgcpo=$totRup*($persencpo/100)/$kgcpo;
$trpperkgpk=$totRup*($persenpk/100)/$kgkernel;

$stream.="<tr><td colspan=3>".$_SESSION['lang']['total']."</td>";
$stream.="<td align=right>".number_format($totRup,2)."</td>
                   <td align=right>".number_format($trpperkgcpo,2)."</td>
                   <td align=right>".number_format($trpperkgpk,2)."</td>    
                   <td align=right>".number_format($grTotTbs,2)."</td>";
for($rd=1;$rd<=12;$rd++)
{
    $stream.="<td align=right>".number_format($tot[$rd],0)."</td>";
}
$stream.="</tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo $stream; 
?>