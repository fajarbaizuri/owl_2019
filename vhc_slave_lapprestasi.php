<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt =isset($_POST['pt'])?$_POST['pt']:$_GET['pt'];
	$gudang =isset($_POST['gudang'])?$_POST['gudang']:$_GET['gudang'];
	$periode =isset($_POST['periode'])?$_POST['periode']:$_GET['periode'];
	$kelompok =isset($_POST['kelompok'])?$_POST['kelompok']:$_GET['kelompok'];
	$type =isset($_POST['type'])?$_POST['type']:$_GET['type'];

	
        
/*
	$str="select posting,kodeorg,`tgl`,`tanggal`,kodevhc,namajenisvhc,CONCAT (`kodevhc`,' [',`nopol`,']') AS kendaraan,`pemilikalat`,`kondisi`,
`kondisinm`,sum(ifnull(`total`,0)) AS `total`,sum(ifnull(`convwaktu`,0)) AS `convwaktu`,
ifnull(`satuk`,'-') AS `satuk`,sum(`upahpro`+`premipro`+`umknpro`+`insentivpro`) AS `btenaga`,sum(`bbmpro`) AS `bbm` FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$periode."' and kodeorg like '".$gudang."' and kodevhc like '".$kelompok."%' and posting=1 group BY kodeorg,`tanggal`,`kodevhc` order by tanggal,kodekegiatan asc";
*/
$str="select posting,kodeorg,kodevhc,namajenisvhc,`nopol`,CONCAT (`kodevhc`,' [',`nopol`,']') AS kendaraan,`pemilikalat`,`kondisi`,
`kondisinm`,sum(DISTINCT ifnull(`total`,0)) AS `total`,sum(DISTINCT ifnull(`convwaktu`,0)) AS `convwaktu`,
ifnull(`satuk`,'-') AS `satuk`,sum(DISTINCT `upahpro`+`premipro`+`umknpro`+`insentivpro`) AS `btenaga`,sum(DISTINCT `bbmpro`) AS `bbm` FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$periode."' and kodeorg like '".$gudang."' and kodevhc like '".$kelompok."%' and posting=1 group BY kodeorg,`kodevhc` order by tanggal,kodekegiatan asc";

	/*
        $str="select 
posting, `pemilik`,
`kodevhc`,
`nopol`,
 `kondisi`,
`kondisinm`,ifnull(`kodekegiatan`,'-') AS `kodekegiatan`,ifnull(`namakegiatan`,'-') AS `namakegiatan`,
`tgl`,
`tanggal`,
count(`rit`) AS `totalrit`,
sum(if((`rit` = 1),`volume`,0)) AS `volrit1`,
sum(if((`rit` = 2),`volume`,0)) AS `volrit2`,
sum(if((`rit` = 3),`volume`,0)) AS `volrit3`,
sum(if((`rit` = 4),`volume`,0)) AS `volrit4`,
sum(ifnull(`volume`,0)) AS `volume`,
ifnull(`satuan`,'-') AS `satuan`,
sum(if((`rit` = 1),`total`,0)) AS `totrit1`,
sum(if((`rit` = 2),`total`,0)) AS `totrit2`,
sum(if((`rit` = 3),`total`,0)) AS `totrit3`,
sum(if((`rit` = 4),`total`,0)) AS `totrit4`,
sum(ifnull(`total`,0)) AS `total`,
sum(ifnull(`convwaktu`,0)) AS `convwaktu`,
ifnull(`satuk`,'-') AS `satuk`,
sum(`upahpro`+`premipro`+`umknpro`+`insentivpro`) AS `btenaga`,
sum(`bbmpro`) AS `bbm` FROM 
".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$periode."' and pemilik like '".$gudang."'
              and kodevhc='".$plat."' and posting=1 group BY `tanggal`,`kodevhc`,`pemilik`,`kondisi`,`kodekegiatan` order by tanggal,kodekegiatan asc"; #tidak sama dengan laba/rugi berjalan 

*/
//=================================================
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['tgl']=$bar->tgl;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['kendaraan']=$bar->kendaraan;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['namajenisvhc']=$bar->namajenisvhc;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['pemilikalat']=$bar->pemilikalat;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['total']=$bar->total;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['satuk']=$bar->satuk;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['bbm']=$bar->bbm;
		$namaPlat=$bar->kendaraan;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['A']=$bar->kodeorg;
		$TAB[$bar->kodeorg][$bar->tanggal][$bar->kodevhc]['B']=$bar->kodevhc;
    } 
   $namakend=array('KD'=>'Kendaraan','AB'=>'Alat Berat');
    
     $sal_waktu=0;
    $sal_bbm=0;
    $stream="";
	
	if($type=='excel')
   {
	   /*
	   $stream.="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>
			  <td align=center 	colspan=9>Laporan Monitoring Prestasi Alat Berat/Kendaraan</td>	
			</tr>
			<tr>
			  <td align=center colspan=9></td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Kelompok</td>	
			  <td align=left colspan=2>: ".$namakend[$kelompok]."</td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Plat No.</td>	
			  <td align=left colspan=2>: ".$namaPlat."</td>		
			</tr>
			<tr>
			  <td align=left colspan=2>Periode</td>	
			  <td align=left colspan=2>: ".$periode."</td>		
			</tr>
			<tr>
			  <td align=center colspan=9></td>		
			</tr>";
		*/
		$stream.="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>
			  <td align=center 	colspan=7>Laporan Monitoring Prestasi Alat Berat/Kendaraan</td>	
			</tr>
			<tr>
			  <td align=center colspan=7></td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Kelompok</td>	
			  <td align=left colspan=2>: ".$namakend[$kelompok]."</td>	
			</tr>
			<tr>
			  <td align=left colspan=2>Periode</td>	
			  <td align=left colspan=2>: ".$periode."</td>		
			</tr>
			<tr>
			  <td align=center colspan=7></td>		
			</tr>";
			
		$stream .="</thead><tbody></tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";	
		 $stream.="<table class=sortable cellspacing=1 border=1 width=100%>
	     <thead>
			<tr>
			  <td align=center rowspan=2>Unit</td>	
			  <td align=center rowspan=2>Plat No Alat Berat/Kendaraan</td>
			  <td align=center rowspan=2>Type</td>
			  <td align=center rowspan=2>Pemilik Alat</td>
			  <td align=center colspan=2>Waktu Operasional Alat Berat/kendaraan</td>
			  <td align=center rowspan=2>Pemakaian BBM(ltr)</td>
			</tr>  
			 <tr>
			  <td align=center >Total</td>
			  <td align=center >Satuan</td>
			</tr>  
		 </thead>
		 <tbody id=container>";
   }
   $sal_waktu=0;
    $sal_bbm=0;
	$sal_btenaga=0;
foreach($TAB as $anak => $sub1)
{
  foreach($sub1 as $anak1 => $sub2)
  {
	foreach($sub2 as $anak2 => $data)
    {
	
			$stream .="<tr class=rowcontent  \">
               <td align=center>".$anak."</td>
			   <td align=center>".$data['kendaraan']."</td>
			   <td align=center>".$data['namajenisvhc']."</td>
			   <td align=center>".$data['pemilikalat']."</td>
			   <td align=right>".number_format($data['total'],2)."</td>
			   <td align=center>".$data['satuk']."</td>
			   <td align=right>".number_format($data['bbm'],2)."</td>
			   ";
			   if($type!='excel'){
			   $stream .="<td>
					<button style='cursor:pointer;width:150px;' title='Click untuk melihat Detail Perhari Prestasi Alat Berat/Kendaraan' class=mybutton onclick=lihatDetailKondisi('".$data['A']."','".$data['B']."','".substr($data['B'],0,2)."','".$periode."',event)>Monitoring Kondisi</button>
					<button style='cursor:pointer;width:150px;' title='Click untuk melihat Detail Perkegiatan Alat Berat/Kendaraan' class=mybutton onclick=lihatDetailPrestasi('".$data['A']."','".$data['B']."','".substr($data['B'],0,2)."','".$periode."',event)>Monitoring Prestasi</button>
			   </td>";
   }		   
             $stream .="</tr>";

    $sal_waktu+=$data['total'];
    $sal_bbm+=$data['bbm'];
	$sal_btenaga+=$data['btenaga'];
		
	}
  }
}   
if ($kelompok=='AB'){
	$hass='HM';
}else{
	$hass='KMH';
}

 $stream .="<tr class=rowcontent>
           <td colspan=4 align=center>SUBTOTAL</td>
           <td align=right>".number_format($sal_waktu,2)."</td>
		   <td align=center>".$hass."</td>
		   <td align=right>".number_format($sal_bbm,2)."</td>";
		   if($type!='excel'){
		   $stream .="<td align=right></td>";
		   }
          $stream .="</tr>";

if($type=='excel')
   {
		$stream .="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
	   
	   
		$tglSkrg=date("Ymd");
		$nop_="Monitoring_Alat_Berat_Kendaraan".$tglSkrg;
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