<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
	$tahun1 = substr($periode,0,4);
	$bulan1 = substr($periode,5,2);
	$periode1 = $periode;
    $stream='';
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

	$res=mysql_query($str);
	$res4=mysql_query($str1);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		$stream.=$_SESSION['lang']['laporanneracacoba'].":<br>
		<table border=1>
                    <tr>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoakhir']."</td>
                        </tr>";

                while($bar=mysql_fetch_object($res))
		{
			if($bar->noakun<4000000){
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
                        
                        $stream.="<tr>
                          <td>".$noakun."</td>
                          <td>".$namaakun."</td>
                           <td align=right class=firsttd>".number_format($sawal,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($debet,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($kredit,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($sakhir,2,'.','')."</td>
                        </tr>";
		}
		}
		
		 $stream.="<tr><td align=center></td><td align=center></td><td align=center></td><td align=center></td><td align=center></td><td align=center></td></tr>";
		 $stream.="<tr><td align=center></td><td align=center></td><td align=center></td><td align=center></td><td align=center></td><td align=center></td></tr><tr>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>
                          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoakhir']."</td>
                        </tr>";
		 while($bar=mysql_fetch_object($res4))
		{
			 if($bar->noakun>3999999){
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
			//$sawal			=$bar->awal;
			$sakhir			=$bar->akhir;    
                        
                        $stream.="<tr>
                          <td>".$noakun."</td>
                          <td>".$namaakun."</td>
                           <td align=right class=firsttd>".number_format(0,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($debet,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($kredit,2,'.','')."</td>
                           <td align=right class=firsttd>".number_format($sakhir,2,'.','')."</td>
                        </tr>";
		}	}
		
		
	  $stream.="</table>";	
	}

$nop_="NeracaPercobaan".$gudang.$tahun1.$bulan1;
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}
?>