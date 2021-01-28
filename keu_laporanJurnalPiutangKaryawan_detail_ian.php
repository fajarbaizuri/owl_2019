<?php
//IND
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   

$noakun=$_GET['noakun'];

$kdsup=$_GET['supplier'];
$tgl=$_GET['tgl'];
$tgl1=$_GET['tgl1'];
$gudang=$_GET['gudang'];

#ambil saldo awal supplier
$str="select (a.debet-a.kredit) as sawal,a.noakun,a.nourut, b.namaakun,a.kodesupplier,c.namasupplier,a.tanggal,a.nojurnal,a.noreferensi,a.keterangan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid
      where a.tanggal<'".$tgl."'  and a.noakun = '".$noakun."' and kodesupplier='".$kdsup."' and kodesupplier is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' 
";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	
	$noj=$bar->nojurnal;
	$nour=$bar->nourut;
	$nopk=$noj."#".$nour;
			
	
	
	
			
	$nojur[$nopk]=$bar->nojurnal;
	$tanggal[$nopk]=$bar->tanggal;
	$keterangan[$nopk]=$bar->keterangan;
    $supplier[$nopk]=$bar->namasupplier;
	$ref[$nopk]=$bar->noreferensi;
    $akun[$nopk]=$bar->noakun;
	
	if (substr($bar->noreferensi, 0, 5)=="SALAK"){
		if ($bar->noakun=="2110101" || $bar->noakun=="2110102" || $bar->noakun=="2110201" || $bar->noakun=="2110202" || $bar->noakun=="2110301" || $bar->noakun=="2110302"|| $bar->noakun=="2110303" || $bar->noakun=="2110304" ){
			$sawal[$nopk]=$bar->sawal * -1;
		}else{
			$sawal[$nopk]=$bar->sawal;
		}
	}else{
		$sawal[$nopk]=$bar->sawal;
	}
}

#ambil saldo awal  karyawan
$str1="select (a.debet-a.kredit) as sawal,a.noakun,a.nourut, b.namaakun,a.nik,c.namakaryawan,a.tanggal,a.nojurnal,a.noreferensi,a.keterangan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     
      where a.tanggal<'".$tgl."'  and a.noakun = '".$noakun."' and a.nik='".$kdsup."' and a.nik is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' 
";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
	
	$noj=$bar1->nojurnal;
	$nour=$bar1->nourut;
	$nopk=$noj."#".$nour;
	$sawal[$nopk]=$bar1->sawal;
		
	$nojur[$nopk]=$bar1->nojurnal;
	$tanggal[$nopk]=$bar1->tanggal;
	$keterangan[$nopk]=$bar1->keterangan;
    $supplier[$nopk]=$bar1->namakaryawan;
	$ref[$nopk]=$bar1->noreferensi;
    $akun[$nopk]=$bar1->noakun;
}


#ambil  transaksi dalam periode supplier
$str2="select a.debet as debet,a.kredit as kredit,a.noakun,a.nourut, b.namaakun,a.kodesupplier,c.namasupplier,a.tanggal,a.nojurnal,a.noreferensi,a.keterangan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid
      where a.tanggal between'".$tgl."' and '".$tgl1."' 
      and a.noakun = '".$noakun."' and kodesupplier='".$kdsup."' and kodesupplier is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'   
";
$res2=mysql_query($str2);
while($bar2=mysql_fetch_object($res2))
{
	//hutang
	
	
	
	$noj=$bar2->nojurnal;
	$nour=$bar2->nourut;
	$nopk=$noj."#".$nour;
	
	$nojur[$nopk]=$bar2->nojurnal;
	$tanggal[$nopk]=$bar2->tanggal;
	$keterangan[$nopk]=$bar2->keterangan;
	
    $supplier[$nopk]=$bar2->namasupplier;
	$ref[$nopk]=$bar2->noreferensi;
	$akun[$nopk]=$bar2->noakun;
	
	
	
		$debet[$nopk]=$bar2->debet;
		$kredit[$nopk]=$bar2->kredit;		
	
	
   
	
}

#ambil saldo transaksi  karyawan
$str3="select a.debet as debet,a.kredit as kredit,a.noakun,a.nourut, b.namaakun,a.nik,c.namakaryawan,a.tanggal,a.nojurnal,a.noreferensi,a.keterangan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     
      where a.tanggal between'".$tgl."' and '".$tgl1."'  
      and a.noakun = '".$noakun."' and a.nik='".$kdsup."' and a.nik is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' 
";

$res3=mysql_query($str3);
while($bar3=mysql_fetch_object($res3))
{
	
	$noj=$bar3->nojurnal;
	$nour=$bar3->nourut;
	$nopk=$noj."#".$nour;
	
	$nojur[$nopk]=$bar3->nojurnal;
	$tanggal[$nopk]=$bar3->tanggal;
	$keterangan[$nopk]=$bar3->keterangan;
	$debet[$nopk]=$bar3->debet;
	$kredit[$nopk]=$bar3->kredit;
    $supplier[$nopk]=$bar3->namakaryawan;
	$ref[$nopk]=$bar3->noreferensi;
    $akun[$nopk]=$bar3->noakun;
	

}


//=================================================																
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"parent.detailKeExcel(event,'keu_laporanJurnalPiutangKaryawan_detail_ian.php?type=excel&noakun=".$noakun."&supplier=".$kdsup."&tgl=".$tgl."&tgl1=".$tgl1."&gudang=".$gudang."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
if($_GET['type']=='excel')$border=1; else $border=0;
$stream="<table class=sortable border=".$border." cellspacing=1>
      <thead>
        <tr class=rowcontent>
          <td>No</td>
          <td>No.Jurnal</td>
          <td>Tanggal</td>
          <td>No.Akun</td>
          <td>Keterangan</td>
          <td>Debet</td>
          <td>Kredit</td>
		  <td>Saldo</td>
          <td>Supplier/Karyawan</td>
		  <td>No. Ref</td>
        </tr>
      </thead>
      <tbody>";
	  
	  
	$no=0;

    foreach($supplier as $kdsupp =>$val){
            $no+=1;
			
			
			
			
			//if ($noakun=='2110102')
			//{
				//$saldoakhir=($sawal[$kdsupp]-$debet[$kdsupp]+$kredit[$kdsupp]);
					//$ian+=$saldoakhir;
			//}
			//else
			//{
				$saldoakhir=($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);
				$ian+=$saldoakhir;
			//}
			
			
			
			 $stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$nojur[$kdsupp]."</td>
		   <td>".$tanggal[$kdsupp]."</td>
		   <td>".$akun[$kdsupp]."</td>
		   <td>".$keterangan[$kdsupp]."</td>
		   <td align=right>".number_format($debet[$kdsupp],2)."</td>
		   <td align=right>".number_format($kredit[$kdsupp],2)."</td>
		   <td align=right>".number_format($ian,2)."</td>
		   <td>".$val."</td>
		   <td>".$ref[$kdsupp]."</td>
		   </tr>";
          $tsa+=$sawal[$kdsupp];//<td align=right width=100>".number_format($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp],2)."</td>
          $td+=$debet[$kdsupp];
          $tk+=$kredit[$kdsupp];
         // $tak+=($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);  
		 $tak+=$saldoakhir;
		 
		 
		 
		        
    }	
$stream.="<tr class=rowcontent>
      <td align=center colspan=5>Total</td>
      <td align=right width=100>".number_format($td,2)."</td>
      <td align=right width=100>".number_format($tk,2)."</td>
      <td align=right width=100>".number_format($tak,2)."</td>
	  <td></td>
	  <td></td>
     </tr>"; 	
	 
   $stream.="</tbody><tfoot></tfoot></table>";
	  
	  
   
   
   
   
   if($_GET['type']=='excel')
   {
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$nop_="Detail_jurnal_".$_GET['gudang']."_".$_GET['periode'];
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