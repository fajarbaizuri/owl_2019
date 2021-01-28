<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
$kali=$_GET['kali'];   
$noarus=$_GET['noarus'];   


	
	

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
    $noakun=$_GET['cashflow'];
	
	$periodesaldo=str_replace("-", "", $periode);
	$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
	$captionCUR=date('M-Y',$t);
	$lastmonth=date('M-Y', strtotime('-1 days', strtotime(date('Y-M',$t)."-01")));

	

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

$ArrMskKotak=array();
// masuk kotak array
$str="select nojurnal,noakun,keterangan,jumlah,noreferensi,tanggal from ".$dbname.".keu_jurnaldt where  nojurnal in ('".implode("','",$ArrHJurnal['INI'])."') and noaruskas ='".$noarus."' ";

	
//=================================================

echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"parent.detailKeExcel(event,'keu_slave_getAKLangsungDetail.php?type=excel&kali=".$kali."&noarus=".$noarus."&pt=".$pt."&gudang=".$gudang."&periode=".$periode."&noakun=".$noakun."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
if($_GET['type']=='excel')$border=1; else $border=0;
/*
$stream="<table class=sortable border=".$border." cellspacing=1>
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>No.Transaksi</td>
          <td>Tanggal</td>
          <td>No.Akun</td>
          <td>Keterangan</td>
          <td>Debet</td>
          <td>Kredit</td>
          <td>Karyawan</td>
          <td>Mesin</td>
          <td>Blok</td>
        </tr>
      </thead>
      <tbody>";
*/
$stream="<table class=sortable border=".$border." cellspacing=1>
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>No.Jurnal</td>
          <td>Tanggal</td>
          <td>No.Akun</td>
          <td>Keterangan</td>
          <td>Jumlah</td>
          <td>No. Referensi</td>
        </tr>
      </thead>
      <tbody>";
    $res=mysql_query($str);
    $no=0;
    $tjumlah=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
        $debet=0;
        
	$stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->nojurnal."</td>               
           <td>".tanggalnormal($bar->tanggal)."</td>    
           <td>".$bar->noakun."</td>    
           <td>".$bar->keterangan."</td>
           <td align=right>".number_format($bar->jumlah * $kali)."</td>
           <td align=right>".$bar->noreferensi."</td>  
         </tr>";
		 
    $tjumlah+=$bar->jumlah * $kali;  
    } 
	/*
      $stream.="<tr class=rowcontent>
           <td colspan=5>TOTAL</td>
           <td align=right>".number_format($tdebet)."</td>
           <td align=right>".number_format($tkredit)."</td>
           <td></td>
           <td></td>
           <td></td>
         </tr>";  
	*/
	  $stream.="<tr class=rowcontent>
           <td colspan=5>TOTAL</td>
           <td align=right>".number_format($tjumlah)."</td>
            <td align=right></td>
         </tr>";  
   $stream.="</tbody><tfoot></tfoot></table>";
   if($_GET['type']=='excel')
   {
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$nop_="Detail_Arus_Kas_Langsung_".$noarus."_".$TglAwalINI."_".$TglAkhirINI;
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
   }
   else
   {
       echo $stream;
   }    
       
?>