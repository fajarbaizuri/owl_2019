<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$unit=$_POST['gudang'];
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
$kodelaporan='INCOME STATEMENT';

$periodesaldo=str_replace("-", "", $periode);
$tahunini=substr($periodesaldo,0,4);

#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);#periode saldoakhir bulan berjalan
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);

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
            <td colspan=3></td>
            <td align=center>".$captionCUR."</td>
            <td align=center>YTD</td>    
            </tr>
         </thead><tbody>";
$tnow2=0;
$ttill2=0;
$tnow3=0;
$ttill3=0;
while($bar=mysql_fetch_object($res))
{
    if($bar->tipe=='Header')
      {
        $stream.="<tr class=rowcontent><td colspan=5><b>".$bar->keterangandisplay."</b></td></tr>";  
      }
    else
    {
       /* 
       #ambil saldo akhir periode barjalan sebagai akumulasi
        $st12="select sum(".$kolomCUR.") as akumilasi
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periodCUR."' and ".$where;
      */  
        $st12="select sum(awal".substr($periodesaldo,4,2).")+sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as akumilasi
                from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
                and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where; 
        $res12=mysql_query($st12);        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
        #mutasi bulan berjalan
        $st13="select sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as sekarang
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where;
        $res13=mysql_query($st13);
        $jlhsekarang=0;
        while($ba13=mysql_fetch_object($res13))
        {
            $jlhsekarang=$ba13->sekarang;
        }
        $tnow2+=$jlhsekarang;
        $ttill2+=$akumulasi;
        $tnow3+=$jlhsekarang;
        $ttill3+=$akumulasi;
        
        if($bar->tipe=='Total'){
                if($bar->noakundari=='' or $bar->noakunsampai=='')
                {
                    if($bar->variableoutput=='2')
                    {
                        $jlhsekarang=$tnow2;
                        $akumulasi=$ttill2; 
                        $tnow2=0;
                        $ttill2=0;
                    }
                    if($bar->variableoutput=='3')
                    {
                        $jlhsekarang=$tnow3;
                        $akumulasi=$ttill3; 
                        $tnow3=0;
                        $ttill3=0;
                    }                                        
                }   
  #==============================================================
				  $TT1=number_format(abs($jlhsekarang));  
				  $TT2=number_format(abs($akumulasi));						  
				if($bar->noakundari=='5110101' and substr($bar->keterangandisplay,0,4)=='LABA' and $jlhsekarang>0)
				{
				   $TT1="(".number_format(abs($jlhsekarang)).")";
				}
				else
				{
					  $TT1=number_format(abs($jlhsekarang));		
				}
				if($bar->noakundari=='5110101' and substr($bar->keterangandisplay,0,4)=='LABA' and $akumulasi>0)
				{
				   $TT2="(".number_format(abs($akumulasi)).")";
				}
				else
				{
					  $TT2=number_format(abs($akumulasi));		
				}
  #======================================================================				
            $stream.="<tr class=rowcontent>
                        <td><td>
                        <td></td>
                        <td colspan=2>------------------------------------------------------------</td></tr>
                    <tr class=rowcontent>
                        <td></td>
                        <td colspan=2><b>".$bar->keterangandisplay."</b></td>
                        <td align=right><b>".$TT1."</b></td>
                        <td align=right><b>".$TT2."</b></td>    
                     </tr>
                     <tr class=rowcontent><td colspan=5>.</td></tr>
                     "; 
        }
        else
        {
		  $TT1=number_format(abs($jlhsekarang));
		  $TT2=number_format(abs($akumulasi));
	   
            $stream.="
                    <tr class=rowcontent>
                    <td style='width:30px'></td><td style='width:30px'></td>
                    <td>".$bar->keterangandisplay."</td>
                    <td align=right>".$TT1."</td>
                    <td align=right>".$TT2."</td>    
                     </tr>";             
        }   
    }   
}
$stream.= "</tbody></tfoot></tfoot></table>";

echo $stream;

?>