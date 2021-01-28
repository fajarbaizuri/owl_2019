<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt =isset($_POST['pt'])?$_POST['pt']:$_GET['pt'];
	$gudang =isset($_POST['gudang'])?$_POST['gudang']:$_GET['gudang'];
	$periode =isset($_POST['periode'])?$_POST['periode']:$_GET['periode'];
	$type =isset($_POST['type'])?$_POST['type']:$_GET['type'];

	
        

	$str="select 
posting,
pemilikalat,
idkaryawan,namakaryawan,namajabatan,if(kodegolongan='BHL','KHL','KHT') AS kodegolongan,
sum(DISTINCT upahpro) AS `upah`,
sum(DISTINCT premipro) AS `premi`,
sum(DISTINCT umknpro) AS `umkn`,
sum(DISTINCT insentivpro) AS `insentiv`,
sum(DISTINCT upahpro+premipro+umknpro+insentivpro) AS `total`
 FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$periode."' and pemilikalat like '".$gudang."'  and posting=1 group BY pemilikalat,idkaryawan order by tanggal,idkaryawan asc";

//=================================================
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $TAB[$bar->pemilikalat][$bar->idkaryawan]['namakaryawan']=$bar->namakaryawan;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['namajabatan']=$bar->namajabatan;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['kodegolongan']=$bar->kodegolongan;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['pemilikalat']=$bar->pemilikalat;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['upah']=$bar->upah;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['premi']=$bar->premi;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['umkn']=$bar->umkn;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['insentiv']=$bar->insentiv;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['total']=$bar->total;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['A']=$bar->pemilikalat;
		$TAB[$bar->pemilikalat][$bar->idkaryawan]['B']=$bar->idkaryawan;
    } 
   $namakend=array('KD'=>'Kendaraan','AB'=>'Alat Berat');
    
     $sal_waktu=0;
    $sal_bbm=0;
    $stream="";
	//$stream.=$str;
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
			  <td align=center 	colspan=9>Laporan Monitoring Penghasilan Tenaga Alat Berat/Kendaraan</td>	
			</tr>
			<tr>
			  <td align=center colspan=9></td>	
			</tr>
			<tr>
			  <td align=left >Unit</td>	
			  <td align=left colspan=2>: ".$gudang."</td>	
			</tr>
			<tr>
			  <td align=left >Periode</td>	
			  <td align=left colspan=2>: ".$periode."</td>		
			</tr>
			<tr>
			  <td align=center colspan=9></td>		
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
    $A=0;
    $B=0;
	$C=0;
	$D=0;
	$E=0;
foreach($TAB as $anak => $sub1)
{
  foreach($sub1 as $anak1 => $data)
  {

	
			$stream .="<tr class=rowcontent  \">
               <td align=center>".$anak."</td>
			   <td align=center>".$data['namakaryawan']."</td>
			   <td align=center>".$data['namajabatan']."</td>
			   <td align=center>".$data['kodegolongan']."</td>
			   <td align=right>".number_format($data['upah'],2)."</td>
			   <td align=right>".number_format($data['premi'],2)."</td>
			   <td align=right>".number_format($data['umkn'],2)."</td>
			   <td align=right>".number_format($data['insentiv'],2)."</td>
			   <td align=right>".number_format($data['total'],2)."</td>
			   ";
			   if($type!='excel'){
			   $stream .="<td >
					<button style='cursor:pointer;width:180px;' title='Click untuk melihat Penghasilan Tenaga Alat Berat/Kendaraan Perhari' class=mybutton onclick=lihatPenghasilanTenagaPerhari('".$data['A']."','".$data['B']."','".$periode."',event)>Penghasilan Perhari</button>
					<button style='cursor:pointer;width:180px;' title='Click untuk melihat Penghasilan Tenaga Alat Berat/Kendaraan PerKegiatan' class=mybutton onclick=lihatPenghasilanTenagaKegiatan('".$data['A']."','".$data['B']."','".$periode."',event)>Penghasilan Perkegiatan</button>
			   </td>";
				}		   
             $stream .="</tr>";

    $A+=$data['upah'];
	$B+=$data['premi'];
	$C+=$data['umkn'];
	$D+=$data['insentiv'];
	$E+=$data['total'];
    
		
	
  }
}   


 $stream .="<tr class=rowcontent>
           <td colspan=4 align=center>SUBTOTAL</td>
           <td align=right>".number_format($A,2)."</td>
		   <td align=right>".number_format($B,2)."</td>
		   <td align=right>".number_format($C,2)."</td>
		   <td align=right>".number_format($D,2)."</td>
		   <td align=right>".number_format($E,2)."</td>";
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