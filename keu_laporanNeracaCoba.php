<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];
	$tahun1 = substr($periode,0,4);
	$bulan1 = substr($periode,5,2);
	$periode1 = $periode;
list($thn, $bln) =split("-", $periode, 2);
	if($gudang=='')
	{

		$str="Select z.kodeorg,z.periode,z.noakun,z.awal".$bln." as awal, ifnull(a.debet,0) as debet,ifnull(a.kredit,0) as kredit, (z.awal".$bln." + ifnull(a.debet,0)-ifnull(a.kredit,0)) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.periode='".$thn.$bln."' order by z.noakun, z.periode";
		$str1="Select z.kodeorg,z.periode,z.noakun, sum(ifnull(a.debet,0)) as debet,sum(ifnull(a.kredit,0)) as kredit, ( sum(ifnull(a.debet,0))-sum(ifnull(a.kredit,0))) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.periode <='".$thn.$bln."' GROUP BY z.kodeorg, z.noakun order by z.noakun, z.periode";

	}
	else
	{

		$str="Select z.kodeorg,z.periode,z.noakun,z.awal".$bln." as awal, ifnull(a.debet,0) as debet,ifnull(a.kredit,0) as kredit, (z.awal".$bln." + ifnull(a.debet,0)-ifnull(a.kredit,0)) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.kodeorg = '".$gudang."' and z.periode='".$thn.$bln."' order by z.noakun, z.periode";
		$str1="Select z.kodeorg,z.periode,z.noakun, sum(ifnull(a.debet,0)) as debet,sum(ifnull(a.kredit,0)) as kredit, (sum(ifnull(a.debet,0))-sum(ifnull(a.kredit,0))) as akhir,z.kodeorg as bussunitcode,b.namaakun,c.induk from ".$dbname.".keu_saldobulanan z left join ".$dbname.".keu_jurnalsum_vw a on z.noakun=a.noakun AND z.kodeorg=a.kodeorg AND z.periode=REPLACE(a.periode, '-', '') left join ".$dbname.".keu_5akun b on z.noakun=b.noakun left join ".$dbname.".organisasi c on substr(z.kodeorg,1,4)=c.kodeorganisasi where c.induk = '".$pt."' and z.kodeorg = '".$gudang."' and z.periode <='".$thn.$bln."' GROUP BY z.kodeorg, z.noakun order by z.noakun, z.periode";
	


		
		
	}	

//=================================================
$tawal=0;
$tdebet=0;
$tkredit=0;
$tsalak=0;
if($periode=='')
{
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('Y-m-d H:i:s');
			$noakun 		=$bar->noakun;
			$namaakun 		=$bar->namaakun;
			$periode 		=$bar->periode;
			//$tahun		=substr($periode,0,4);
			//$bulan 		=substr($periode,5,2);
			$kodeorg		=$bar->kodeorg; 
			$bussunitcode	=$bar->bussunitcode; 
			$induk			=$bar->induk; 
			$debet 			=$bar->debet; 
			$kredit 		=$bar->kredit;
            //$sawal		=0;
			$sawal			=$bar->awal;
			$sakhir			=$bar->akhir;
			/*
			$strx="select awal".$bulan." from ".$dbname.".keu_saldobulanan where 
                              noakun='".$noakun."' and kodeorg='".$bussunitcode."' 
                              and periode='".$tahun.$bulan."'";
                        $resx=mysql_query($strx);
                        while($barx=mysql_fetch_array($resx)){
                        $sawal=$barx[0];	
                        }
             */           
			echo"<tr class=rowcontent  style='cursor:pointer;'>
				  <td>".$noakun."</td>
				  <td>".$namaakun."</td>
				 <td align=center>".$thn."</td>
                  <td align=center>".$bln."</td>
				  <td align=center>".$kodeorg."</td>
				  <td align=center>".$bussunitcode."</td>
				  <td align=center>".$induk."</td>
                  <td align=right>".number_format($sawal,2)."</td>   
				  <td align=right>".number_format($debet,2)."</td>
				  <td align=right>".number_format($kredit,2)."</td>
                  <td align=right>".number_format($sakhir,2)."</td>    
				</tr>";
				//<td align=right>".number_format($sawal+$debet-$kredit,2)."</td>    
                    $tawal+=$sawal;
                    $tdebet+=$debet;
                    $tkredit+=$kredit; 
                    $tsalak+=$sakhir;
		}
	}
}
else
	{
	$res=mysql_query($str);
    $res4=mysql_query($str1);      
	
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
           echo"<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center width=100>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['namaakun']."</td>
			  <td align=center>".$_SESSION['lang']['tahun']."</td>
			  <td align=center>".$_SESSION['lang']['bulan']."</td>
			  <td align=center width=100>".$_SESSION['lang']['organisasi']."</td>
			  <td align=center width=80>".$_SESSION['lang']['unitkerja']."</td>
			  <td align=center width=80>".$_SESSION['lang']['perusahaan']."</td>
              <td align=center width=80>".$_SESSION['lang']['saldoawal']."</td>    
			  <td align=center width=150>".$_SESSION['lang']['debet']."</td>
			  <td align=center width=150>".$_SESSION['lang']['kredit']."</td>
              <td align=center width=150>".$_SESSION['lang']['saldoakhir']."</td>                                  
			</tr>  
		 </thead>
		 <tbody>";
            while($bar=mysql_fetch_object($res))
            {
                    $no+=1;
                    if($bar->noakun<4000000){
                            $periode=date('Y-m-d H:i:s');
                            $noakun =$bar->noakun;
                            $namaakun =$bar->namaakun;
                            $periode 	=$bar->periode;
                            //$tahun		=substr($periode,0,4);
                            //$bulan 		=substr($periode,5,2);
                            $kodeorg	=$bar->kodeorg; 
                            $induk		=$bar->induk; 
                            $bussunitcode	=$bar->bussunitcode; 
                            $debet 		=$bar->debet; 
                            $kredit 	=$bar->kredit;
							$sawal		=$bar->awal;
							$sakhir		=$bar->akhir;
							/*
                            $sawal=0;
                            $strx="select awal".$bulan." from ".$dbname.".keu_saldobulanan where 
                                  noakun='".$noakun."' and kodeorg='".$bussunitcode."' 
                                  and periode='".$tahun.$bulan."'";
                            $resx=mysql_query($strx);
                            while($barx=mysql_fetch_array($resx)){
                            $sawal=$barx[0];	
                            }
							<td align=center>".$tahun."</td>
                                      <td align=center>".$bulan."</td>
							*/
							
                    echo"<tr class=rowcontent style='cursor:pointer;'>
                                      <td>".$noakun."</td>
                                      <td>".$namaakun."</td>
                                      <td align=center>".$thn."</td>
                                      <td align=center>".$bln."</td>
                                      <td align=center>".$kodeorg."</td>
                                      <td align=center>".$bussunitcode."</td>
                                      <td align=center>".$induk."</td>
                                      <td align=right>".number_format($sawal,2)."</td>     
                                      <td align=right>".number_format($debet,2)."</td>
                                      <td align=right>".number_format($kredit,2)."</td>
                                      <td align=right>".number_format($sakhir,2)."</td>    
                            </tr>"; 	
                        $tawal+=$sawal;
                        $tdebet+=$debet;
                        $tkredit+=$kredit;   
                        $tsalak+=$sakhir;
                    }       
            }
                    echo"<tr class=rowcontent>
                              <td colspan=7 align=center>TOTAL</td>
                              <td align=right>".number_format($tawal,2)."</td>     
                              <td align=right>".number_format($tdebet,2)."</td>
                              <td align=right>".number_format($tkredit,2)."</td>
                              <td align=right>".number_format($tsalak,2)."</td>
                            </tr></tbody><tfoot></tfoot></table>"; 
           echo"<br><hr><br>";
           echo"<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center width=100>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['namaakun']."</td>
			  <td align=center>".$_SESSION['lang']['tahun']."</td>
			  <td align=center>".$_SESSION['lang']['bulan']."</td>
			  <td align=center width=100>".$_SESSION['lang']['organisasi']."</td>
			  <td align=center width=80>".$_SESSION['lang']['unitkerja']."</td>
			  <td align=center width=80>".$_SESSION['lang']['perusahaan']."</td>
                          <td align=center width=80>".$_SESSION['lang']['saldoawal']."</td>    
			  <td align=center width=150>".$_SESSION['lang']['debet']."</td>
			  <td align=center width=150>".$_SESSION['lang']['kredit']."</td>
                          <td align=center width=150>".$_SESSION['lang']['saldoakhir']."</td>                                  
			</tr>  
		 </thead>
		 <tbody>";
            $tawal=0;
            $tdebet=0;
            $tkredit=0;
            $tsalak=0;
           #kepalan 4++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            while($bar=mysql_fetch_object($res4))
            {
              if($bar->noakun>3999999){
                  $no+=1;
                            $periode=date('Y-m-d H:i:s');
                            $noakun =$bar->noakun;
                            $namaakun =$bar->namaakun;
                            $periode 	=$bar->periode;

                            $kodeorg	=$bar->kodeorg; 
                            $induk		=$bar->induk; 
                            $bussunitcode	=$bar->bussunitcode; 
                            $debet 		=$bar->debet; 
                            $kredit 	=$bar->kredit;
							//$sawal		=$bar->awal;
							$sakhir		=$bar->akhir;
		//<td align=right>".number_format($sawal,2)."</td>
                    echo"<tr class=rowcontent style='cursor:pointer;'>
                                      <td>".$noakun."</td>
                                      <td>".$namaakun."</td>
                                      <td align=center>".$thn."</td>
                                      <td align=center>".$bln."</td>
                                      <td align=center>".$kodeorg."</td>
                                      <td align=center>".$bussunitcode."</td>
                                      <td align=center>".$induk."</td>
                                      <td align=right>".number_format(0,2)."</td>     
                                      <td align=right>".number_format($debet,2)."</td>
                                      <td align=right>".number_format($kredit,2)."</td>
                                      <td align=right>".number_format($sakhir,2)."</td>    
                        </tr>"; 	
                       // $tawal+=$sawal;
					    $tawal+=0;
                        $tdebet+=$debet;
                        $tkredit+=$kredit;   
                        $tsalak+=$sakhir;
              }        
            }
                    echo"<tr class=rowcontent>
                              <td colspan=7 align=center>TOTAL</td>
                              <td align=right>".number_format(0,2)."</td>     
                              <td align=right>".number_format($tdebet,2)."</td>
                              <td align=right>".number_format($tkredit,2)."</td>
                              <td align=right>".number_format($tsalak,2)."</td>
                            </tr>";    
               echo" </tbody>
		 <tfoot>
		 </tfoot>		 
	        </table> ";     
              #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++      
                
  }
}	

?>