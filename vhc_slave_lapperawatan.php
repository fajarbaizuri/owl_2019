<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt =isset($_POST['pt'])?$_POST['pt']:$_GET['pt'];
	$gudang =isset($_POST['gudang'])?$_POST['gudang']:$_GET['gudang'];
	$periode =isset($_POST['periode'])?$_POST['periode']:$_GET['periode'];
	$kelompok =isset($_POST['kelompok'])?$_POST['kelompok']:$_GET['kelompok'];
	$plat =isset($_POST['plat'])?$_POST['plat']:$_GET['plat'];
	$type =isset($_POST['type'])?$_POST['type']:$_GET['type'];

	
        

	

	
        $str="select * from ".$dbname.".vhc_service_vw
              where date_format(tanggal,'%Y-%m')='".$periode."' and kodeorg like '".$gudang."'
              and kodevhc='".$plat."' and posting=1"; #tidak sama dengan laba/rugi berjalan 


//=================================================
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $TAB[$bar->notransaksi]['kodeorg']=$bar->kodeorg;
        $TAB[$bar->notransaksi]['tgl']=$bar->tgl;
		$TAB[$bar->notransaksi]['jenisnm']=$bar->jenisnm;
		$TAB[$bar->notransaksi]['catatan']=$bar->catatan;
		$TAB[$bar->notransaksi]['waktu']=$bar->waktu;
		$TAB[$bar->notransaksi]['nmmekanik']=$bar->nmmekanik;
		$namaPlat=$bar->nopol;
        
    } 
   $namakend=array('KD'=>'Kendaraan','AB'=>'Alat Berat');
    
     $sal_waktu=0;
    $stream="";
	if($type=='excel')
   {
	   $stream.="<table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
			<tr>
			  <td align=center 	colspan=6>Laporan Monitoring Perbaikan dan Perawatan Alat Berat/Kendaraan</td>	
			</tr>
			<tr>
			  <td align=center colspan=6></td>	
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
			  <td align=center colspan=6></td>		
			</tr>";
		$stream .="</thead><tbody></tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";	
		 $stream.="<table class=sortable cellspacing=1 border=1 width=100%>
	     <thead>
		    <tr>
			  <td align=center style='width:80px'>Unit</td>	
			  <td align=center style='width:100px'>Tanggal</td>
			  <td align=center style='width:150px'>Jenis</td>
			  <td align=center style='width:300px'>Keterangan</td>
			  <td align=center style='width:150px'>Waktu Pengerjaan (Jam)</td>
			  <td align=center style='width:200px'>Mekanik</td>
			</tr>  
		 </thead>
		 <tbody id=container>";
   }
foreach($TAB as $anak => $data)
{
        
        $stream .="<tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailService('".$anak."',event);\">
               <td align=center>".$data['kodeorg']."</td>
               <td align=center>".$data['tgl']."</td>    
               <td align=center>".$data['jenisnm']."</td>
			   <td>".$data['catatan']."</td>
			   <td align=right>".$data['waktu']."</td>
			   <td>".$data['nmmekanik']."</td>
             </tr>";

    $sal_waktu+=$data['waktu'];

}   
 $stream .="<tr class=rowcontent>
           <td colspan=4 align=center>TOTAL</td>
           <td align=right>".$sal_waktu."</td>
		   <td align=right></td>
          </tr>";

if($type=='excel')
   {
		$stream .="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
	   
	   
		$tglSkrg=date("Ymd");
		$nop_="Monitoring_Perawatan_Perbaikan_Alat_Berat_Kendaraan".$tglSkrg;
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