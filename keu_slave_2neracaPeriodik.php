<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');

	$pt=$_POST['pt'];
	$unit=$_POST['unit'];//kebun
	$periode=$_POST['periode'];

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
#++++++++++++++++++++++++++++++++++++++++++
$kodelaporan='BALANCE SHEET';
$periodesaldo=str_replace("-", "", $periode);

$tahun=substr($periodesaldo,0,4);
$tahunlalu=$tahun-1;

#ambil format mesinlaporan==========
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);

#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";

$stream="<table class=sortable border=0 cellspacing=1>
          <thead>
           <tr class=rowheader>
            <td>Keterangan</td>
            <td align=center>Dec ".$tahunlalu."</td>    
            <td align=center>Jan</td>
            <td align=center>Feb</td>
            <td align=center>Mar</td>
            <td align=center>Apr</td>
            <td align=center>May</td>
            <td align=center>Jun</td>
            <td align=center>Jul</td>
            <td align=center>Aug</td>
            <td align=center>Sep</td>
            <td align=center>Oct</td>
            <td align=center>Nov</td>
            <td align=center>Dec</td>
            </tr>
         </thead><tbody>";
while($bar=mysql_fetch_object($res))
{
    $tampildari=$bar->variableoutput;
    
    if($bar->tipe=='Header')
      {
          $stream.="<tr class=rowcontent><td colspan=14><b>".$bar->keterangandisplay."</b></td></tr>";  
      }
    else
    {
        $st12="select sum(awal01) as awal01, sum(awal02) as awal02, sum(awal03) as awal03, sum(awal04) as awal04,
            sum(awal05) as awal05, sum(awal06) as awal06, sum(awal07) as awal07, sum(awal08) as awal08,
                sum(awal09) as awal09, sum(awal10) as awal10, sum(awal11) as awal11, sum(awal12) as awal12
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and ".$where." and periode like '".$tahun."%'";
//        echo $st12."<br>";
        $res12=mysql_query($st12);
        $awal01=0;
        $awal02=0;
        $awal03=0;
        $awal04=0;
        $awal05=0;
        $awal06=0;
        $awal07=0;
        $awal08=0;
        $awal09=0;
        $awal10=0;
        $awal11=0;
        $awal12=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $awal01=$ba12->awal01;
            $awal02=$ba12->awal02;
            $awal03=$ba12->awal03;
            $awal04=$ba12->awal04;
            $awal05=$ba12->awal05;
            $awal06=$ba12->awal06;
            $awal07=$ba12->awal07;
            $awal08=$ba12->awal08;
            $awal09=$ba12->awal09;
            $awal10=$ba12->awal10;
            $awal11=$ba12->awal11;
            $awal12=$ba12->awal12;
        }
        
        if($bar->tipe=='Total'){
            $stream.="<tr class=rowcontent>
                        <td><b>".$bar->keterangandisplay."</b></td>
                        <td align=right><b>".number_format($awal01)."</b></td>
                        <td align=right><b>".number_format($awal02)."</b></td>    
                        <td align=right><b>".number_format($awal03)."</b></td>
                        <td align=right><b>".number_format($awal04)."</b></td>    
                        <td align=right><b>".number_format($awal05)."</b></td>
                        <td align=right><b>".number_format($awal06)."</b></td>    
                        <td align=right><b>".number_format($awal07)."</b></td>
                        <td align=right><b>".number_format($awal08)."</b></td>    
                        <td align=right><b>".number_format($awal09)."</b></td>
                        <td align=right><b>".number_format($awal10)."</b></td>    
                        <td align=right><b>".number_format($awal11)."</b></td>
                        <td align=right><b>".number_format($awal12)."</b></td>    
                        <td align=right><b>0</b></td>    
                     </tr>
                     "; 
            } //  end of Total
        
        else
        { // not Total
            $stream.="
                    <tr class=rowcontent>
                    <td>".$bar->keterangandisplay."</td>
                    <td align=right>".number_format($awal01)."</td>
                    <td align=right>".number_format($awal02)."</td>    
                    <td align=right>".number_format($awal03)."</td>
                    <td align=right>".number_format($awal04)."</td>    
                    <td align=right>".number_format($awal05)."</td>
                    <td align=right>".number_format($awal06)."</td>    
                    <td align=right>".number_format($awal07)."</td>
                    <td align=right>".number_format($awal08)."</td>    
                    <td align=right>".number_format($awal09)."</td>
                    <td align=right>".number_format($awal10)."</td>    
                    <td align=right>".number_format($awal11)."</td>
                    <td align=right>".number_format($awal12)."</td>    
                    <td align=right>0</td>    
                     </tr>";             
        } // end of not Total   
    }   
}


 



$stream.= "</tbody></tfoot></tfoot></table>";
echo $stream;

?>