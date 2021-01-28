<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
    $noakun=$_GET['cashflow'];
	
	$periodesaldo=str_replace("-", "", $periode);
	$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
	$captionCUR=date('M-Y',$t);
	$lastmonth=date('M-Y', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));

		 
	$stream= "<table class=sortable border=1 cellspacing=1>
          <thead>
           <tr class=rowheader>
			  <td align=center>No. Arus</td>
			  <td align=center>Nama Arus Kas</td>
			  <td align=center>".$lastmonth."</td>
			  <td align=center>".$captionCUR."</td>
            </tr>
         </thead><tbody>";

	//cari Anggota dari PT
	$strA1="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' and tipe in ('KEBUN','PABRIK','HOLDING','TRAKSI','LINE') order by kodeorganisasi asc";
	$resA1=mysql_query($strA1);
	$hasilA1=mysql_num_rows($resA1);

	if ($hasilA1==0){
		echo "Error :Tidak Ada Organisasi di dalam Perusahaan ='".$pt."' .. !";
		exit;
	}else{
		$ArrAnkOrg=array();
		while($barA1=mysql_fetch_object($resA1))
		{
			$ArrAnkOrg[]=$barA1->kodeorganisasi;	
		}
		$AnakOrg=implode("','",$ArrAnkOrg);
	}
	
	//cari Akun dari Organisasi
	if ($gudang!=""){
		$strC="select noakun from ".$dbname.".keu_5akun where pemilik ='".$gudang."' and level=5 and (noakun like '11101%' or noakun like '11102%') ";
		
	}else{
		$strC="select noakun from ".$dbname.".keu_5akun where pemilik in ('".$AnakOrg."') and level=5 and (noakun like '11101%' or noakun like '11102%')";
		
	}
	
	$resC=mysql_query($strC);
	$ArrAnkAkun=array();
		while($barC=mysql_fetch_object($resC))
		{
			$ArrAnkAkun[]=$barC->noakun;	
		}
		$AnakAkun=implode("','",$ArrAnkAkun);
	
	
  
	
	//Seluruhnya    
	if ($gudang==""){
		$AtrOrg="kodeorg in ('".$AnakOrg."') and";
		if ($noakun!=""){
			$AtrAkunBank="noakun='".$noakun."' and ";
			
		}else{
			$AtrAkunBank="noakun in ('".$AnakAkun."') and ";
		}	
	}else{
		$AtrOrg="kodeorg='".$gudang."' and";
		if ($noakun!=""){
			$AtrAkunBank="noakun='".$noakun."' and ";
			
		}else{
			$AtrAkunBank="noakun in ('".$AnakAkun."') and ";
		}	
	
	}		 


// Ambil Range Periode SAAT INI Sesuai Pilihan
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where ".$AtrOrg." periode='".$periode."' and tutupbuku=0 order by periode asc limit 1";

	//echo "Error:".$str;
	//exit;
$res=mysql_query($str);
$hasil=mysql_num_rows($res);
if ($hasil==0){
	echo "Error :Periode Akuntansi Belum Tersedia/Masih Tertutup";
	exit;
}else{
	while($bar=mysql_fetch_object($res))
	{
	$TglAwalINI=$bar->tanggalmulai;
	$TglAkhirINI=$bar->tanggalsampai;
	}
	list($thnAwal,$blnAwal,$TglAwal)=explode("-", $TglAwalINI);
	list($thnAkhir,$blnAkhir,$TglAkhir)=explode("-", $TglAkhirINI);
}


// Ambil Range Periode Bulan Kemarin Sesuai Pilihan
$strN="SELECT '".$thnAwal."-".$blnAwal."-01' - INTERVAL '1' MONTH as awal,'".$thnAkhir."-".$blnAkhir."-31' - INTERVAL '1' MONTH as akhir ";
$resN=mysql_query($strN);
$barN=mysql_fetch_object($resN);
$TglAwalKMR=$barN->awal;
$TglAkhirKMR=$barN->akhir;



$ArrHJurnal=array();
//Cari Arus Kas di Jurnal
$strB="select  nojurnal from ".$dbname.".keu_jurnaldt where tanggal >='".$TglAwalINI."' and tanggal <='".$TglAkhirINI."' and ".$AtrOrg." ".$AtrAkunBank." noaruskas=''";

$resB=mysql_query($strB);
while($barB=mysql_fetch_object($resB))
{
	$ArrHJurnal['INI'][]=$barB->nojurnal;
}



$strB1="select  nojurnal from ".$dbname.".keu_jurnaldt where tanggal >='".$TglAwalKMR."' and tanggal <='".$TglAkhirKMR."' and ".$AtrOrg." ".$AtrAkunBank." noaruskas=''";
$resB1=mysql_query($strB1);
while($barB1=mysql_fetch_object($resB1))
{
	$ArrHJurnal['KMR'][]=$barB1->nojurnal;
}


$ArrMskKotak=array();
// masuk kotak array
$strD="select noaruskas,sum(jumlah) as jum from ".$dbname.".keu_jurnaldt where  nojurnal in ('".implode("','",$ArrHJurnal['INI'])."') and noaruskas !='' group by noaruskas";

$resD=mysql_query($strD);
	while($barD=mysql_fetch_object($resD))
	{
			$ArrMskKotak['INI'][$barD->noaruskas]=$barD->jum;
	}

$strD1="select noaruskas,sum(jumlah) as jum from ".$dbname.".keu_jurnaldt where nojurnal in ('".implode("','",$ArrHJurnal['KMR'])."') and noaruskas !=''  group by noaruskas";
$resD1=mysql_query($strD1);
	while($barD1=mysql_fetch_object($resD1))
	{
			$ArrMskKotak['KMR'][$barD1->noaruskas]=$barD1->jum;
	}	

$ArrSaldoAwal=array();
//cari saldo awal bulan ini 
$strF="select sum(awal".$blnAwal.") as jum from ".$dbname.".keu_saldobulanan where   ".$AtrOrg." ".$AtrAkunBank." periode='".$thnAwal.$blnAwal."'";

$resF=mysql_query($strF);
	while($barF=mysql_fetch_object($resF))
	{
			$ArrSaldoAwal['INI']=$barF->jum;
	}
list($thnAwalKMR,$blnAwalKMR,$TglAwalKMR)=explode("-", $TglAwalKMR);
$strF1="select sum(awal".$blnAwalKMR.") as jum from ".$dbname.".keu_saldobulanan where   ".$AtrOrg." ".$AtrAkunBank." periode='".$thnAwalKMR.$blnAwalKMR."'";

$resF1=mysql_query($strF1);
	while($barF1=mysql_fetch_object($resF1))
	{
			$ArrSaldoAwal['KMR']=$barF1->jum;
	}
	
	
$stream.="<tr class=rowcontent>
     <td colspan=4 align=left><b>AKTIVITAS OPERASIONAL</b></td>
	 </tr>
	 <tr class=rowcontent>
	 <td colspan=4 align=left>Arus Kas Masuk</td>
     </tr>";

$strE="select noarus, uraian, tipe, kelompok from ".$dbname.".aruskas_vw where   ".$AtrOrg." ".$AtrAkunBank." `metode` =  'DIRECT' and tipe='M' and kelompok ='O'    group by noarus, tipe, kelompok order by noarus asc";
$resE=mysql_query($strE);
$TOTMOINI=0;
$TOTMOKMR=0;
while($barE=mysql_fetch_object($resE))
{
			$stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailArus(-1,'".$barE->noarus."','".$pt."','".$gudang."','".$periode."','".$noakun."',event);\">
               <td>".$barE->noarus."</td>
               <td>".$barE->uraian."</td>
               <td align=right>".number_format($ArrMskKotak['KMR'][$barE->noarus] * -1)."</td>   
               <td align=right>".number_format($ArrMskKotak['INI'][$barE->noarus] * -1)."</td>    
             </tr>";
			 
			$TOTMOINI+=($ArrMskKotak['INI'][$barE->noarus]* -1);
			$TOTMOKMR+=($ArrMskKotak['KMR'][$barE->noarus]* -1);
}
	 
$stream.="<tr class=rowcontent>
     <td colspan=2 align=right>Jumlah Arus Kas Masuk</td>
	 <td align=right>".number_format($TOTMOKMR)."</td>
     <td align=right>".number_format($TOTMOINI)."</td> 
     </tr>
	 <tr class=rowcontent>
	 <td colspan=4 align=left>Arus Kas Keluar</td>
     </tr>
	 ";	 
$strE1="select noarus, uraian, tipe, kelompok from ".$dbname.".aruskas_vw where   ".$AtrOrg." ".$AtrAkunBank." `metode` =  'DIRECT' and tipe='K' and kelompok ='O'    group by noarus, tipe, kelompok order by noarus asc";
$resE1=mysql_query($strE1);
$TOTKOINI=0;
$TOTKOKMR=0;
while($barE1=mysql_fetch_object($resE1))
{
			$stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailArus(1,'".$barE1->noarus."','".$pt."','".$gudang."','".$periode."','".$noakun."',event);\">
               <td>".$barE1->noarus."</td>
               <td>".$barE1->uraian."</td>
               <td align=right>".number_format($ArrMskKotak['KMR'][$barE1->noarus])."</td>   
               <td align=right>".number_format($ArrMskKotak['INI'][$barE1->noarus])."</td>    
             </tr>";
			 
			$TOTKOINI+=$ArrMskKotak['INI'][$barE1->noarus]; 
			$TOTKOKMR+=$ArrMskKotak['KMR'][$barE1->noarus];
}
$TOTOKMR=$TOTMOKMR-$TOTKOKMR;
$TOTOINI=$TOTMOINI-$TOTKOINI;
$stream.="<tr class=rowcontent>
     <td colspan=2 align=right>Jumlah Arus Kas Keluar</td>
	 <td align=right>".number_format($TOTKOKMR)."</td>
     <td align=right>".number_format($TOTKOINI)."</td> 
     </tr>
	 <tr class=rowcontent>
	 <td colspan=2 align=right><b>ARUS KAS BERSIH AKTIVITAS OPERASIONAL</b></td>";
	 if ($TOTOKMR < 0){
			$stream.="<td align=right><b>(".number_format($TOTOKMR * -1 ).")</b></td>";
	 }else{
		   $stream.="<td align=right><b>".number_format($TOTOKMR)."</b></td>";
	 }
	 
	 if ($TOTOINI < 0){
			$stream.="<td align=right><b>(".number_format($TOTOINI * -1 ).")</b></td>";
	 }else{
			$stream.="<td align=right><b>".number_format($TOTOINI)."</b></td>";
	 }
	 
	 
	 
	 echo "</tr>";	 	 

$stream.="<tr class=rowcontent><td colspan=4 align=left></td></tr>
<tr class=rowcontent>
     <td colspan=4 align=left><b>AKTIVITAS INVESTASI</b></td>
	 </tr>
	 <tr class=rowcontent>
	 <td colspan=4 align=left>Arus Kas Masuk</td>
     </tr>";
$strE2="select noarus, uraian, tipe, kelompok from ".$dbname.".aruskas_vw where   ".$AtrOrg." ".$AtrAkunBank." `metode` =  'DIRECT' and tipe='M' and kelompok ='I'    group by noarus, tipe, kelompok order by noarus asc";
$resE2=mysql_query($strE2);
$TOTMIINI=0;
$TOTMIKMR=0;
while($barE2=mysql_fetch_object($resE2))
{
			$stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailArus(-1,'".$barE2->noarus."','".$pt."','".$gudang."','".$periode."','".$noakun."',event);\">
               <td>".$barE2->noarus."</td>
               <td>".$barE2->uraian."</td>
               <td align=right>".number_format($ArrMskKotak['KMR'][$barE2->noarus] * -1)."</td>   
               <td align=right>".number_format($ArrMskKotak['INI'][$barE2->noarus] * -1)."</td>    
             </tr>";
			 
			$TOTMIINI+=($ArrMskKotak['INI'][$barE2->noarus] * -1); 
			$TOTMIKMR+=($ArrMskKotak['KMR'][$barE2->noarus] * -1);
}

$stream.="<tr class=rowcontent>
     <td colspan=2 align=right>Jumlah Arus Kas Masuk</td>
	 <td align=right>".number_format($TOTMIKMR)."</td>
     <td align=right>".number_format($TOTMIINI)."</td> 
     </tr>
	 <tr class=rowcontent>
	 <td colspan=4 align=left>Arus Kas Keluar</td>
     </tr>
	 ";	 
$strE3="select noarus, uraian, tipe, kelompok from ".$dbname.".aruskas_vw where   ".$AtrOrg." ".$AtrAkunBank." `metode` =  'DIRECT' and tipe='K' and kelompok ='I'    group by noarus, tipe, kelompok order by noarus asc";
$resE3=mysql_query($strE3);
$TOTKIINI=0;
$TOTKIKMR=0;
while($barE3=mysql_fetch_object($resE3))
{
			$stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailArus(1,'".$barE3->noarus."','".$pt."','".$gudang."','".$periode."','".$noakun."',event);\">
               <td>".$barE3->noarus."</td>
               <td>".$barE3->uraian."</td>
               <td align=right>".number_format($ArrMskKotak['KMR'][$barE3->noarus])."</td>   
               <td align=right>".number_format($ArrMskKotak['INI'][$barE3->noarus])."</td>    
             </tr>";
			 
			$TOTKIINI+=$ArrMskKotak['INI'][$barE3->noarus]; 
			$TOTKIKMR+=$ArrMskKotak['KMR'][$barE3->noarus];
}
$TOTIKMR=$TOTMIKMR-$TOTKIKMR;
$TOTIINI=$TOTMIINI-$TOTKIINI;	 
echo"<tr class=rowcontent>
     <td colspan=2 align=right>Jumlah Arus Kas Keluar</td>
	 <td align=right>".number_format($TOTKIKMR)."</td>
     <td align=right>".number_format($TOTKIINI)."</td> 
     </tr>
	 <tr class=rowcontent>
	 <td colspan=2 align=right><b>ARUS KAS BERSIH AKTIVITAS INVESTASI</b></td>
	 ";
	 if ($TOTIKMR < 0){
			$stream.="<td align=right><b>(".number_format($TOTIKMR * -1 ).")</b></td>";
	 }else{
		   $stream.="<td align=right><b>".number_format($TOTIKMR)."</b></td>";
	 }
	 
	 if ($TOTIINI < 0){
			$stream.="<td align=right><b>(".number_format($TOTIINI * -1 ).")</b></td>";
	 }else{
			$stream.="<td align=right><b>".number_format($TOTIINI)."</b></td>";
	 }
	 
	 
	 
	 $stream.= "</tr>";	 	 
	 
	 

$stream.="<tr class=rowcontent><td colspan=4 align=left></td></tr>
<tr class=rowcontent>
     <td colspan=4 align=left><b>AKTIVITAS PENDANAAN</b></td>
	 </tr>
	 <tr class=rowcontent>
	 <td colspan=4 align=left>Arus Kas Masuk</td>
     </tr>";
$strE4="select noarus, uraian, tipe, kelompok from ".$dbname.".aruskas_vw where   ".$AtrOrg." ".$AtrAkunBank." `metode` =  'DIRECT' and tipe='M' and kelompok ='P'    group by noarus, tipe, kelompok order by noarus asc";
$resE4=mysql_query($strE4);
$TOTMPINI=0;
$TOTMPKMR=0;
while($barE4=mysql_fetch_object($resE4))
{
			echo"<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailArus(-1,'".$barE4->noarus."','".$pt."','".$gudang."','".$periode."','".$noakun."',event);\">
               <td>".$barE4->noarus."</td>
               <td>".$barE4->uraian."</td>
               <td align=right>".number_format($ArrMskKotak['KMR'][$barE4->noarus] * -1)."</td>   
               <td align=right>".number_format($ArrMskKotak['INI'][$barE4->noarus] * -1)."</td>    
             </tr>";
			 
			$TOTMPINI+=($ArrMskKotak['INI'][$barE4->noarus]* -1); 
			$TOTMPKMR+=($ArrMskKotak['KMR'][$barE4->noarus]* -1);
}
$stream.="<tr class=rowcontent>
     <td colspan=2 align=right>Jumlah Arus Kas Masuk</td>
	 <td align=right>".number_format($TOTMPKMR)."</td>
     <td align=right>".number_format($TOTMPINI)."</td> 
     </tr>
	 <tr class=rowcontent>
	 <td colspan=4 align=left>Arus Kas Keluar</td>
     </tr>
	 ";	 
$strE5="select noarus, uraian, tipe, kelompok from ".$dbname.".aruskas_vw where   ".$AtrOrg." ".$AtrAkunBank." `metode` =  'DIRECT' and tipe='K' and kelompok ='P'    group by noarus, tipe, kelompok order by noarus asc";
$resE5=mysql_query($strE5);
$TOTKPINI=0;
$TOTKPKMR=0;
while($barE5=mysql_fetch_object($resE5))
{
			$stream.="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailArus(1,'".$barE5->noarus."','".$pt."','".$gudang."','".$periode."','".$noakun."',event);\">
               <td>".$barE5->noarus."</td>
               <td>".$barE5->uraian."</td>
               <td align=right>".number_format($ArrMskKotak['KMR'][$barE5->noarus])."</td>   
               <td align=right>".number_format($ArrMskKotak['INI'][$barE5->noarus])."</td>    
             </tr>";
			 
			$TOTKPINI+=$ArrMskKotak['INI'][$barE5->noarus]; 
			$TOTKPKMR+=$ArrMskKotak['KMR'][$barE5->noarus];
}
$TOTPKMR=$TOTMPKMR-$TOTKPKMR;
$TOTPINI=$TOTMPINI-$TOTKPINI;		 
$stream.="<tr class=rowcontent>
     <td colspan=2 align=right>Jumlah Arus Kas Keluar</td>
	 <td align=right>".number_format($TOTKPKMR)."</td>
     <td align=right>".number_format($TOTKPINI)."</td> 
     </tr>
	 <tr class=rowcontent>
	 <td colspan=2 align=right><b>ARUS KAS BERSIH AKTIVITAS PENDANAAN</b></td>
	  ";
	 if ($TOTPKMR < 0){
			$stream.="<td align=right><b>(".number_format($TOTPKMR * -1 ).")</b></td>";
	 }else{
		   $stream.="<td align=right><b>".number_format($TOTPKMR)."</b></td>";
	 }
	 
	 if ($TOTPINI < 0){
			$stream.="<td align=right><b>(".number_format($TOTPINI * -1 ).")</b></td>";
	 }else{
			$stream.="<td align=right><b>".number_format($TOTPINI)."</b></td>";
	 }
	 
	 
	 
	 $stream.= "</tr>";	 	 
	 
$TOTKMRALL=$TOTOKMR+$TOTIKMR+$TOTPKMR;
$TOTINIALL=$TOTOINI+$TOTIINI+$TOTPINI;
$stream.="<tr class=rowcontent><td colspan=4 align=left></td></tr>";
		  
$stream.="<tr class=rowcontent>
           <td colspan=2 align=right><b>KENAIKAN (PENURUNAN) KAS</b></td>
		    ";
	 if ($TOTKMRALL < 0){
			$stream.="<td align=right><b>(".number_format($TOTKMRALL * -1 ).")</b></td>";
	 }else{
		   $stream.="<td align=right><b>".number_format($TOTKMRALL)."</b></td>";
	 }
	 
	 if ($TOTINIALL < 0){
			$stream.="<td align=right><b>(".number_format($TOTINIALL * -1 ).")</b></td>";
	 }else{
			$stream.="<td align=right><b>".number_format($TOTINIALL)."</b></td>";
	 }
	 
	 
	 
	 $stream.= "</tr>";	 	 
	 
           $stream.="
		  <tr class=rowcontent>
           <td colspan=2 align=right><b>SALDO AWAL KASBANK</b></td>
		    ";
	 if ($ArrSaldoAwal['KMR'] < 0){
			$stream.="<td align=right><b>(".number_format($ArrSaldoAwal['KMR'] * -1 ).")</b></td>";
	 }else{
		   $stream.="<td align=right><b>".number_format($ArrSaldoAwal['KMR'])."</b></td>";
	 }
	 
	 if ($ArrSaldoAwal['INI'] < 0){
			$stream.="<td align=right><b>(".number_format($ArrSaldoAwal['INI'] * -1 ).")</b></td>";
	 }else{
			$stream.="<td align=right><b>".number_format($ArrSaldoAwal['INI'])."</b></td>";
	 }
	 
	 
	 
	 $stream.= "</tr>";	 
	 $ArrSaldoAkhir=array();
	 $ArrSaldoAkhir['INI']=$ArrSaldoAwal['INI']+$TOTINIALL;
	 $ArrSaldoAkhir['KMR']=$ArrSaldoAwal['KMR']+$TOTKMRALL;
          $stream.="
		  <tr class=rowcontent>
           <td colspan=2 align=right><b>SALDO AKHIR KASBANK</b></td>
            ";
	 if ($ArrSaldoAkhir['KMR'] < 0){
			$stream.="<td align=right><b>(".number_format($ArrSaldoAkhir['KMR'] * -1 ).")</b></td>";
	 }else{
		   $stream.="<td align=right><b>".number_format($ArrSaldoAkhir['KMR'])."</b></td>";
	 }
	 
	 if ($ArrSaldoAkhir['INI'] < 0){
			$stream.="<td align=right><b>(".number_format($ArrSaldoAkhir['INI'] * -1 ).")</b></td>";
	 }else{
			$stream.="<td align=right><b>".number_format($ArrSaldoAkhir['INI'])."</b></td>";
	 }
	 
	 
	 
	 $stream.= "</tr>";	 

	   
	   
	   
$qwe=date("YmdHms");

$nop_="LP_AKL_".$gudang.$periode." ".$qwe;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>