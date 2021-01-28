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
$kodelaporan='BALANCE SHEET';

$periodesaldo=str_replace("-", "", $periode);
$tahunini=substr($periodesaldo,0,4);

#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);#periode saldoakhir bulan berjalan
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);
$lastmonth=date('M-Y', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));

$periodeCUR=date('Ym',$t);
$periodeLAST=date('Ym', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));

$bulanCUR=date('m',$t);
$bulanLAST=date('m', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));




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
			<td align=center>".$lastmonth."</td>
            <td align=center>".$captionCUR."</td>
            <td align=center>(Naik/Turun)</td>    
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
        $stream.="<tr class=rowcontent><td colspan=6><b>".$bar->keterangandisplay."</b></td></tr>";  
      }
    else
    {
		/*
        $st12="select sum(awal".substr($periodesaldo,4,2).")+sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as akumilasi
                from ".$dbname.".keu_saldobulanan where noakun >= '".$bar->noakundari."' 
                and noakun <= '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where; 
        $res12=mysql_query($st12);        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
		*/
		
		#mutasi bulan sebelumnya
		$st12="select sum(awal".$bulanLAST.")+sum(debet".$bulanLAST.") - sum(kredit".$bulanLAST.") as akumilasi
                from ".$dbname.".keu_saldobulanan where noakun >= '".$bar->noakundari."' 
                and noakun <= '".$bar->noakunsampai."' and  periode='".$periodeLAST."' and ".$where; 
        $res12=mysql_query($st12);        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
		
        #mutasi bulan berjalan
        $st13="select sum(awal".$bulanCUR.")+sum(debet".$bulanCUR.") - sum(kredit".$bulanCUR.") as sekarang
               from ".$dbname.".keu_saldobulanan where noakun  >= '".$bar->noakundari."' 
               and noakun <='".$bar->noakunsampai."' and  periode='".$periodeCUR."' and ".$where;
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
		
		//jumlah alokasi
		/*
		if ($bar->nourut=='1058'){
			$CUR_AL=$jlhsekarang;
			$CUR_LASHT=$akumulasi;
			
		}
        
*/		
		
		
        if($bar->tipe=='Total'){
                if($bar->noakundari=='' or $bar->noakunsampai=='')
                {
                    if($bar->variableoutput=='1')
                    {
                        $jlhsekarang=$tnow2;
                        $akumulasi=$ttill2; 
                        $tnow2=0;
                        $ttill2=0;
                    }
                    if($bar->variableoutput=='2')
                    {
                        $jlhsekarang=$tnow3;
                        $akumulasi=$ttill3; 
                        $tnow3=0;
                        $ttill3=0;
                    }                 
					
                }   
				/*
				if ($bar->nourut=='1060'){
				 $jlhsekarang=$jlhsekarang+$CUR_AL;
                 $akumulasi=$akumulasi+$CUR_LASHT; 	
				}
				*/
  #==============================================================
				  //$TT1=number_format(abs(ceil($jlhsekarang)));  
				  //$TT2=number_format((ceil($akumulasi)));
				  
				  $TT1=number_format(round($jlhsekarang));  
				  $TT2=number_format(round($akumulasi));
				  
				  //$TT3=number_format(bs(ceil($jlhsekarang))-ceil($akumulasi));
				  if($bar->noakundari >=2110101 and $bar->noakundari <=3110104) {
						$TT2=number_format(round($akumulasi * -1));
						$TT1=number_format(round($jlhsekarang * -1));  
				  }
				  
				  
				  
				  $TT3=$jlhsekarang-$akumulasi;
				  $TT3=number_format(round($TT3));  
				/*
				if($bar->noakundari=='5110101' and substr($bar->keterangandisplay,0,4)=='Laba' and $jlhsekarang>0)
				{
				   $TT1="(".number_format(abs($jlhsekarang)).")";
				}
				else
				{
					  $TT1=number_format(($jlhsekarang));		
				}
				if($bar->noakundari=='5110101' and substr($bar->keterangandisplay,0,4)=='Laba' and $akumulasi>0)
				{
				   $TT2="(".number_format(abs($akumulasi)).")";
				}
				else
				{
						
					  $TT2=number_format(($akumulasi));		
				}
				
				if($bar->keterangandisplay=='Jumlah Ekuitas' and $akumulasi<0) {
					$TT2="".number_format(abs($akumulasi))."";
				}else if($bar->keterangandisplay=='JUMLAH KEWAJIBAN DAN EKUITAS' and $akumulasi<0) {
					$TT2="".number_format(abs($akumulasi))."";
				}else{
					$TT2="".number_format(($akumulasi))."";
				}
				
				*/
			#======================================================================	 
            $stream.="<tr class=rowcontent>
                        <td><td>
                        <td></td>
                        <td colspan=3>------------------------------------------------------------</td></tr>
                    <tr class=rowcontent>
                        <td></td>
                        <td colspan=2><b>".$bar->keterangandisplay."</b></td>
                        <td align=right><b>".$TT2."</b></td>
                        <td align=right><b>".$TT1."</b></td>    
						<td align=right><b>".$TT3."</b></td>
                     </tr>
                     <tr class=rowcontent><td colspan=6>.</td></tr>
                     "; 
			
			if($bar->keterangandisplay=='JUMLAH KEWAJIBAN DAN EKUITAS'){
			$PASIVATT1=$akumulasi;
			$PASIVATT2=$jlhsekarang;
			$PASIVATT3=$PASIVATT2-$PASIVATT1;
			}
			
			if($bar->keterangandisplay=='JUMLAH AKTIVA'){
			$AKTIVATT1=$akumulasi;
			$AKTIVATT2=$jlhsekarang;
			$AKTIVATT3=$AKTIVATT2-$AKTIVATT1;
			}
			
			$total1=ceil($AKTIVATT1)+ceil($PASIVATT1);
			$total2=ceil($AKTIVATT2)+ceil($PASIVATT2);
			$total3=ceil($AKTIVATT3)+ceil($PASIVATT3);
			
			
			
        }
        else
        {
				//$TT1=number_format(abs(ceil($jlhsekarang)));		
				//$TT2=number_format((ceil($akumulasi)));
				 $TT1=number_format(round($jlhsekarang));  
				  $TT2=number_format(round($akumulasi));
				  
				  //$TT3=number_format(bs(ceil($jlhsekarang))-ceil($akumulasi));
				  if($bar->noakundari >=2110101 and $bar->noakundari <=3110104) {
						$TT2=number_format(round($akumulasi * -1));
						$TT1=number_format(round($jlhsekarang * -1));  
						
						
						
				  
				  }
				  $TT3=$jlhsekarang-$akumulasi;
				  $TT3=number_format(round($TT3));  
				  
				/*
			if($bar->noakundari=='3110101' and $akumulasi < 0) {
				$TT2="".number_format(abs($akumulasi))."";
			}else if($bar->noakundari=='3110102' and $akumulasi>0) {
				$TT2="(".number_format(($akumulasi)).")";
			}else if($bar->noakundari=='3110104' and $akumulasi<0) {
				$TT2="".number_format(abs($akumulasi))."";
			}else if($bar->noakundari=='2110101' and $akumulasi<0) {
				$TT2="".number_format(abs($akumulasi))."";
			}else if($bar->noakundari=='2120101' and $akumulasi<0) {
				$TT2="".number_format(abs($akumulasi))."";	
			}else if($bar->noakundari=='2150101' and $akumulasi>0) {
				$TT2="(".number_format(abs($akumulasi)).")";	
			}else{
				$TT2="".number_format(abs($akumulasi))."";
			}
			*/
			
            $stream.="<tr class=rowcontent>
                    <td style='width:30px'></td><td style='width:30px'></td>
                    <td >".$bar->keterangandisplay."</td>
					<td align=right>".$TT2."</td> 
                    <td align=right>".$TT1."</td>
                    <td align=right>".$TT3."</td>   
                     </tr>";             
        }   
    }   
}
//kontrol

 $stream.="<tr class=rowcontent>
                    <td style='width:30px'></td><td style='width:30px'></td>
                    <td></td>
					<td align=right>".$total1."</td> 
                    <td align=right>".$total2."</td>
					<td align=right>".$total3."</td> 
                     </tr>";  
$stream.= "</tbody></tfoot></tfoot></table>";

echo $stream;

?>