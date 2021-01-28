<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');

$proses=$_GET['proses'];
$kodeorg=$_POST['kodeorg'];
$tahun=$_POST['tahun'];
if(($proses=='excel')or($proses=='pdf')){
    $kodeorg=$_GET['kodeorg'];
    $tahun=$_GET['tahun'];
}

if(($proses=='preview')or($proses=='excel')){
    if($kodeorg==''){
        echo"Error: Unit tidak boleh kosong."; exit;
    }	
    if($tahun==''){
        echo"Error: Tahun tidak boleh kosong."; exit;
    }	
}
        
if($proses=='excel')
    $stream.="<table border='1'>";
else {
    $stream.="<table cellspacing='1' border='0' class='sortable' width=100%>";
}
$stream.="<thead>
<tr class=rowheader>
<td align=center>".$_SESSION['lang']['nomor']."</td>
<td align=center>".$_SESSION['lang']['kodeorg']."</td>    
<td align=center>".$_SESSION['lang']['id']."</td>
<td align=center>".$_SESSION['lang']['namakaryawan']."</td>            
<td align=center>".$_SESSION['lang']['tipekaryawan']."</td>
<td align=center>".$_SESSION['lang']['statuspajak']."</td>
<td align=center>".$_SESSION['lang']['tahun']."</td>
<td align=center>".$_SESSION['lang']['jan']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".01</td>    
<td align=center>".$_SESSION['lang']['peb']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".02</td>    
<td align=center>".$_SESSION['lang']['mar']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".03</td>    
<td align=center>".$_SESSION['lang']['apr']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".04</td>    
<td align=center>".$_SESSION['lang']['mei']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".05</td>    
<td align=center>".$_SESSION['lang']['jun']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".06</td>    
<td align=center>".$_SESSION['lang']['jul']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".07</td>    
<td align=center>".$_SESSION['lang']['agt']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".08</td>    
<td align=center>".$_SESSION['lang']['sep']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".09</td>    
<td align=center>".$_SESSION['lang']['okt']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".10</td>    
<td align=center>".$_SESSION['lang']['nov']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".11</td>    
<td align=center>".$_SESSION['lang']['dec']."</td>
<td align=center>".$_SESSION['lang']['pph12'].".12</td>    
<td align=center>".$_SESSION['lang']['total']."</td>
<td align=center>PPh21 Tahunan</td>    
</tr>   
</thead>
<tbody>";

// kamus tipe karyawan
$str="select id, tipe from ".$dbname.".sdm_5tipekaryawan
    ";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $kamusTipe[$bar->id]=$bar->tipe;
    }

// kamus data karyawan
$str="select nik, karyawanid, namakaryawan, tipekaryawan, statuspajak, lokasitugas, subbagian from ".$dbname.".datakaryawan 
    where lokasitugas like '".$kodeorg."%' ";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $kamusKar[$bar->karyawanid]['nik']=$bar->nik;
        $kamusKar[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $kamusKar[$bar->karyawanid]['tipe']=$bar->tipekaryawan;
        $kamusKar[$bar->karyawanid]['status']=$bar->statuspajak;
        $kamusKar[$bar->karyawanid]['lokasi']=$bar->lokasitugas;
        $kamusKar[$bar->karyawanid]['bagian']=$bar->subbagian;
    }
//ambil porsi JMS dari perusahaan yang kena pajak
    $plusJMS=0;
    $str="select value from ".$dbname.".sdm_ho_hr_jms_porsi where id='pph21'";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
      $plusJMS=$bar->value;
    }    
//ambil biaya jabatan    
    $jabPersen=0;
    $jabMax=0;
    $str="select persen,max from ".$dbname.".sdm_ho_pph21jabatan";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $jabPersen=$bar->persen/100;
        $jabMax=$bar->max*12;
    }    
    
//Ambil PTKP:
    $ptkp=Array();
    $str="select id,value from ".$dbname.".sdm_ho_pph21_ptkp";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $ptkp[$bar->id]=$bar->value;
    } 
    
//ambil tarif pph21
  $pphtarif=Array();  
  $pphpercent=Array();  
  $str="select level,percent,upto from ".$dbname.".sdm_ho_pph21_kontribusi order by level";
  $res=mysql_query($str);    
  $urut=0;
  while($bar=mysql_fetch_object($res))
    {
        $pphtarif[$urut]    =$bar->upto;
        $pphpercent[$urut]  =$bar->percent/100;      
        $urut+=1;  
    }   
//ambil gaji pokok yang akan dikali dengan porsi jms dari perusahaan
$str="select sum(jumlah) as gaji, karyawanid, substr(periodegaji,6,2) as bulan from ".$dbname.".sdm_gaji 
    where idkomponen=1 and periodegaji like '".$tahun."%'
    and kodeorg like '".$kodeorg."%' group by karyawanid, periodegaji order by karyawanid";
$res=mysql_query($str);        
$dJMS=Array();  
while($bar=mysql_fetch_object($res))
{
    $dJMS[$bar->karyawanid][$bar->bulan]=$bar->gaji*$plusJMS/100;
    $dJMS[$bar->karyawanid]['gptahunnan']+=$bar->gaji;//gaji pokok tahunan
} 

    
// total gaji yang kena pph
$str="select sum(jumlah) as gaji, karyawanid, substr(periodegaji,6,2) as bulan from ".$dbname.".sdm_gaji 
    where idkomponen in (select id from ".$dbname.".sdm_ho_component where pph21=1)
    and periodegaji like '".$tahun."%'
    and kodeorg like '".$kodeorg."%' group by karyawanid, periodegaji order by karyawanid";

//echo $str;
$res=mysql_query($str);        
$dzKar=Array();  
$dzArr=Array();  
while($bar=mysql_fetch_object($res))
{
    $dzKar[$bar->karyawanid]=$bar->karyawanid;
    $dzArr[$bar->karyawanid]['karyawanid']=$bar->karyawanid;
    $dzArr[$bar->karyawanid][$bar->bulan]=$bar->gaji;
    $dzArr[$bar->karyawanid]['total']+=$bar->gaji;
    //hitung PPH21====================================================
    //penghasilan disetahunkan
    $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]=(($bar->gaji+$dJMS[$bar->karyawanid][$bar->bulan])*12);//disetahunkan
    
    //periksa By jab dan kurangkan
    $dzArr[$bar->karyawanid]['byjab'][$bar->bulan]=$jabPersen*$dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan];
    if($dzArr[$bar->karyawanid]['byjab'][$bar->bulan]>$jabMax){//jika lebih dari max maka dibatasi sebesar max
        $dzArr[$bar->karyawanid]['byjab'][$bar->bulan]=$jabMax;
    }    
    //penghasilan setela kurang By Jabatan
    $dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]=$dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]-$dzArr[$bar->karyawanid]['byjab'][$bar->bulan];
    //kurangi dengan PTKP sehingga menghasilkan pkp:
    $dzArr[$bar->karyawanid]['pkp'][$bar->bulan]=$dzArr[$bar->karyawanid]['penghasilan'][$bar->bulan]-$ptkp[str_replace("K","",$kamusKar[$bar->karyawanid]['status'])]; 
    $zz=0;
    $sisazz=0;

    if($dzArr[$bar->karyawanid]['pkp'][$bar->bulan]>0){         
    #tahap 1: 
    if($dzArr[$bar->karyawanid]['pkp'][$bar->bulan]<$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$dzArr[$bar->karyawanid]['pkp'][$bar->bulan];
        $sisazz=0; 
    }
    else if($dzArr[$bar->karyawanid]['pkp'][$bar->bulan]>=$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$pphtarif[0];
        $sisazz=$dzArr[$bar->karyawanid]['pkp'][$bar->bulan]-$pphtarif[0];
        #level 2
            if($sisazz<($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*$sisazz;
                $sisazz=0;        
            }    
            else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                $sisazz=$dzArr[$bar->karyawanid]['pkp'][$bar->bulan]-$pphtarif[1]; 
                #level 3   
                    if($sisazz<($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*$sisazz;
                        $sisazz=0;        
                    }    
                    else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                        $sisazz=$dzArr[$bar->karyawanid]['pkp'][$bar->bulan]-$pphtarif[2];
                         // print_r($sisazz);exit();
                            if($sisazz>0){
                            #level 4  sisanya kali 30% 
                                $zz+=$pphpercent[3]*$sisazz;  
                            }                          
                    } 
            }   
                   
    }
    }
    
    //masukkan ke array utama
    $dzArr[$bar->karyawanid]['pph21'][$bar->bulan]=$zz/12;
    // end hitungan PPh 21 bulanan===================================================================================
   
}

//==========================================================================
$no=0;
// display data
if(!empty($dzKar))foreach($dzKar as $karid){
    $no+=1;
    $stream.="<tr class=rowcontent>
    <td align=right>".$no."</td>";
    if($kamusKar[$karid]['bagian']!='')$stream.="<td align=left>".$kamusKar[$karid]['bagian']."</td>"; else $stream.="<td align=left>".$kamusKar[$karid]['lokasi']."</td>";
    $stream.="<td align=left>".$kamusKar[$karid]['nik']."</td>
    <td align=left>".$kamusKar[$karid]['nama']."</td>
    <td align=left>".$kamusTipe[$kamusKar[$karid]['tipe']]."</td>
    <td align=left>".$kamusKar[$karid]['status']."</td>
    <td align=center>".$tahun."</td>
    <td align=right>".number_format($dzArr[$karid]['01'],0)."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['01'])."</td>        
    <td align=right>".number_format($dzArr[$karid]['02'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['02'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['03'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['03'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['04'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['04'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['05'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['05'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['06'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['06'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['07'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['07'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['08'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['08'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['09'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['09'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['10'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['10'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['11'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['11'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['12'])."</td>
    <td align=right>".number_format($dzArr[$karid]['pph21']['12'])."</td>         
    <td align=right>".number_format($dzArr[$karid]['total'])."</td>";
    //pph 21 tahunan (setelah setahun)============================================================
        $dzArr[$karid]['tpenghasilan']=($dzArr[$karid]['total']+($dJMS[$karid]['gptahunnan']*$plusJMS/100));
    //periksa By jab dan kurangkan
    $dzArr[$karid]['tbyjab']=$jabPersen*$dzArr[$karid]['tpenghasilan'];
    if($dzArr[$karid]['tbyjab']>$jabMax){//jika lebih dari max maka dibatasi sebesar max
        $dzArr[$karid]['tbyjab']=$jabMax;
    }    
    //penghasilan setela kurang By Jabatan
    $dzArr[$karid]['tpenghasilan']=$dzArr[$karid]['tpenghasilan']-$dzArr[$karid]['tbyjab'];
    //kurangi dengan PTKP sehingga menghasilkan pkp:
    $dzArr[$karid]['tpkp']=$dzArr[$karid]['tpenghasilan']-$ptkp[str_replace("K","",$kamusKar[$karid]['status'])]; 
    $zz=0;
    $sisazz=0;
    if($dzArr[$karid]['tpkp']>0){
    #tahap 1:
    if($dzArr[$karid]['tpkp']<$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$dzArr[$karid]['tpkp'];
        $sisazz=0;
    }
    else if($dzArr[$karid]['tpkp']>=$pphtarif[0])
    {
        $zz+=$pphpercent[0]*$pphtarif[0];
        $sisazz=$dzArr[$karid]['tpkp']-$pphtarif[0];
            #level 2
            if($sisazz<($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*$sisazz;
                $sisazz=0;        
            }    
            else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
            {
                $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                $sisazz=$dzArr[$karid]['tpkp']-$pphtarif[1];        
                    #level 3   
                    if($sisazz<($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*$sisazz;
                        $sisazz=0;        
                    }    
                    else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                    {
                        $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                        $sisazz=$dzArr[$karid]['tpkp']-$pphtarif[2];  
                            if($sisazz>0){
                            #level 4  sisanya kali 30% 
                                $zz+=$pphpercent[3]*$sisazz; 
                            }
                    } 
            }          
    }
    }
    //masukkan ke array utama
    $dzArr[$karid]['tpph21']=$zz;
    //================================end pph tahunan===================================================================
    
    
    $stream.="<td align=right>".number_format($dzArr[$karid]['tpph21'])."</td>
    </tr>";
    $total['pph01']+=$dzArr[$karid]['pph21']['01'];
    $total['pph02']+=$dzArr[$karid]['pph21']['02'];
    $total['pph03']+=$dzArr[$karid]['pph21']['03'];
    $total['pph04']+=$dzArr[$karid]['pph21']['04'];
    $total['pph05']+=$dzArr[$karid]['pph21']['05'];
    $total['pph06']+=$dzArr[$karid]['pph21']['06'];
    $total['pph07']+=$dzArr[$karid]['pph21']['07'];
    $total['pph08']+=$dzArr[$karid]['pph21']['08'];
    $total['pph09']+=$dzArr[$karid]['pph21']['09'];
    $total['pph10']+=$dzArr[$karid]['pph21']['10'];
    $total['pph11']+=$dzArr[$karid]['pph21']['11'];
    $total['pph12']+=$dzArr[$karid]['pph21']['12'];
    
    $total['01']+=$dzArr[$karid]['01'];
    $total['02']+=$dzArr[$karid]['02'];
    $total['03']+=$dzArr[$karid]['03'];
    $total['04']+=$dzArr[$karid]['04'];
    $total['05']+=$dzArr[$karid]['05'];
    $total['06']+=$dzArr[$karid]['06'];
    $total['07']+=$dzArr[$karid]['07'];
    $total['08']+=$dzArr[$karid]['08'];
    $total['09']+=$dzArr[$karid]['09'];
    $total['10']+=$dzArr[$karid]['10'];
    $total['11']+=$dzArr[$karid]['11'];
    $total['12']+=$dzArr[$karid]['12'];
    $total['total']+=$dzArr[$karid]['total'];
    $total['pph']+=$dzArr[$karid]['tpph21'];
}

// total
$stream.="<thead>
<tr class=rowheader>
<td colspan=7 align=center>Total</td>
<td align=right>".number_format($total['01'])."</td>
<td align=right>".number_format($total['pph01'])."</td>    
<td align=right>".number_format($total['02'])."</td>
<td align=right>".number_format($total['pph02'])."</td>     
<td align=right>".number_format($total['03'])."</td>
<td align=right>".number_format($total['pph03'])."</td>     
<td align=right>".number_format($total['04'])."</td>
<td align=right>".number_format($total['pph04'])."</td>     
<td align=right>".number_format($total['05'])."</td>
<td align=right>".number_format($total['pph05'])."</td>     
<td align=right>".number_format($total['06'])."</td>
<td align=right>".number_format($total['pph06'])."</td>     
<td align=right>".number_format($total['07'])."</td>
<td align=right>".number_format($total['pph07'])."</td>     
<td align=right>".number_format($total['08'])."</td>
<td align=right>".number_format($total['pph08'])."</td>     
<td align=right>".number_format($total['09'])."</td>
<td align=right>".number_format($total['pph09'])."</td>     
<td align=right>".number_format($total['10'])."</td>
<td align=right>".number_format($total['pph10'])."</td>     
<td align=right>".number_format($total['11'])."</td>
<td align=right>".number_format($total['pph11'])."</td>     
<td align=right>".number_format($total['12'])."</td>
<td align=right>".number_format($total['pph12'])."</td>     
<td align=right>".number_format($total['total'])."</td>
<td align=right>".number_format($total['pph'])."</td>
</tr>";
$stream.="</tbody></table>";

if($proses=='preview'){
    echo $stream;    
}

if($proses=='excel'){
    $stream.="</table><br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHms");
    $nop_="pph21_".$kodeorg."_".$tahun."_".$dte;
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";            
}

    
?>