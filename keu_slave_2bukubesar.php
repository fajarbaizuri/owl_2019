<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];
        $periode1=$_POST['periode1'];
        
//cek periode dan periode1
if($periode1<$periode)
{  #ditukar
    $z=$periode;
    $periode=$periode1;
    $periode1=$z;
}
	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
//ambil namagudang
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=strtoupper($bar->namaorganisasi);
}

//ambil semua noakun dari bulan lalu dan bulan ini
$lmperiode=mktime(0,0,0,substr($periode,5,2)-1,4,substr($periode,0,4));
$lmperiode=date('Y-m',$lmperiode);
$str="select distinct noakun,namaakun from ".$dbname.".keu_5akun order by noakun";
$res=mysql_query($str);
$TAB=Array();
while($bar=mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['noakun']=$bar->noakun;
    $TAB[$bar->noakun]['namaakun']=$bar->namaakun;
    $TAB[$bar->noakun]['sawal']=0;
    $TAB[$bar->noakun]['salak']=0;
}
//ambil saldo awal
if($gudang=='' and $pt!='')
{
    $where =" and kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
}
else if($gudang!='')
{
    $where =" and kodeorg ='".$gudang."'";
}
else
{
  $where='';  
}   
    
$str="select sum(awal".substr(str_replace("-","",$periode),4,2).") as sawal,noakun from ".$dbname.".keu_saldobulanan 
      where periode ='".str_replace("-","",$periode)."' ".$where." 
      and noakun!='3110400' group by noakun order by noakun";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['sawal']=$bar->sawal;
    $TAB[$bar->noakun]['salak']=$bar->sawal;
}
//ambil mutasi-----------------------

if($gudang=='' and $pt=='')
{
        $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
              where periode>='".$periode."' and periode<='".$periode1."' 
              and noakun!='3110400' group by noakun"; #tidak sama dengan laba/rugi berjalan
}
else if($gudang=='' and $pt!='')
{
        $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
              where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi 
              from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)
              and noakun!='3110400' group by noakun"; #tidak sama dengan laba/rugi berjalan
}
else
{
        $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
              where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'
              and noakun!='3110400' group by noakun"; #tidak sama dengan laba/rugi berjalan 
}   

//=================================================
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $TAB[$bar->noakun]['debet']=$bar->debet;
        $TAB[$bar->noakun]['kredit']=$bar->kredit;
        $TAB[$bar->noakun]['salak']=$TAB[$bar->noakun]['sawal']+$bar->debet-$bar->kredit;
    } 
    $no=0;
     $sal_awal=0;
    $sal_debet=0;
    $sal_kredit=0;
    $sal_salak=0;     
foreach($TAB as $baris => $data)
{
    if($data['sawal']==0 && $data['debet']==0 && $data['kredit']==0 && $data['salak']==0)
    {
        
    }
    else
    {    
         $no+=1;
        echo"<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetail('".$data['noakun']."','".$periode."','".$periode1."','".$lmperiode."','".$pt."','".$gudang."',event);\">
               <td>".$no."</td>
               <td>".$data['noakun']."</td>    
               <td>".$data['namaakun']."</td>
               <td align=right>".number_format($data['sawal'])."</td>
               <td align=right>".number_format($data['debet'])."</td>
               <td align=right>".number_format($data['kredit'])."</td>   
               <td align=right>".number_format($data['salak'])."</td>    
             </tr>";
    }
    $sal_awal+=$data['sawal'];
    $sal_debet+=$data['debet'];
    $sal_kredit+=$data['kredit'];
    $sal_salak+=$data['salak']; 
}   
echo"<tr class=rowcontent>
           <td colspan=3 align=center>TOTAL</td>
           <td align=right>".number_format($sal_awal)."</td>
           <td align=right>".number_format($sal_debet)."</td>
           <td align=right>".number_format($sal_kredit)."</td>   
           <td align=right>".number_format($sal_salak)."</td> 
          </tr>"

    
?>