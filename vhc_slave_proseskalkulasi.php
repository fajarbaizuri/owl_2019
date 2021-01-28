<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
#---------------------------------------------------------------
#DATA GUDANG UNTUK MASING-MASING UNIT
#---------------------------------------------------------------
#1. Ambil gudang unit bersangkutan
#1. Cek Data yg belum posting





 $str="select posting from ".$dbname.".vhc_kendaraan_detail_vw where posting=0 and date_format(tanggal,'%Y-%m')='".$param['periode']."' and 
(kodeorg='".$param['kodeorg']."' or pemilikalat='".$param['kodeorg']."')";
	  //echo $str;
 $res=  mysql_query($str);
 $hasil=mysql_num_rows ($res);
 if($hasil>=1)
	 exit('Error: Masih ada data kendaraan dan alat berat yang belum di posting');
 else {
    
     $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi
           where kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'";
	//echo $str;
     $res=mysql_query($str);
     $periodeawal="";
     $periodeakhir="";
     while($bar=mysql_fetch_object($res))
     {
         $periodeawal =$bar->tanggalmulai;
         $periodeakhir=$bar->tanggalsampai;
     }
     if($periodeakhir=='' or $periodeawal=='')
         exit('Error: Invalid inventory control period');
     else{
			$str="select posting,pemilikalat,idkaryawan,
	namakaryawan,namajabatan,if(kodegolongan='BHL','KHL','KHT') AS kodegolongan,`tgl`,`tanggal`,
kodevhc,namajenisvhc,CONCAT (`kodevhc`,' [',`nopol`,']') AS kendaraan,
sum(DISTINCT ifnull(`total`,0)) AS `total`,sum(DISTINCT ifnull(`convwaktu`,0)) AS `convwaktu`,
(CASE   WHEN (sum(DISTINCT ifnull(`convwaktu`,0)) >= 125 AND sum(DISTINCT ifnull(`convwaktu`,0)) <=174)  THEN 200000 WHEN (sum(DISTINCT ifnull(`convwaktu`,0)) > 175) THEN 350000 ELSE 0 END) AS hasil
 FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where tanggal >='".$periodeawal."' and tanggal<='".$periodeakhir."' and pemilikalat like '".$param['kodeorg']."' and posting='1' AND kelompok='1' 
group BY pemilikalat,idkaryawan,kodevhc order by tanggal,idkaryawan asc";

          
				 // echo $str;
				  
             $res=mysql_query($str);             
             if(mysql_num_rows($res)>0)
             {
             echo"<button class=mybutton onclick=prosesHitung(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
					<td>ID</td>
					<td>Karyawan</td>
					<td>Jabatan</td>
					<td>Plat No Alat Berat</td>
					<td>Tipe</td>
					<td>Total HM</td>
					<td>Insentiv</td>
                    </tr>
                  </thead>
                  <tbody>";

             $no=0;
             while($bar=mysql_fetch_object($res))
             { 
                   $no+=1;
                 echo"<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
					<td  id='ids".$no."'>".$bar->idkaryawan."</td>
                    <td id='karyawan".$no."'>".$bar->namakaryawan."</td>
                    <td id='jabatan".$no."'>".$bar->namajabatan."</td>    
                    <td id='plat".$no."'>".$bar->kodevhc."</td>
                    <td id='tipe".$no."'>".$bar->namajenisvhc."</td>
                    <td align=right id='hm".$no."'>".number_format($bar->convwaktu,2)."</td>
					<td align=right id='insentiv".$no."'>".number_format($bar->hasil)."</td>
                    </tr>";       
             }
             echo"</tbody><tfoot></tfoot></table>";
             }
             else
             {
                 echo "No. Data";
             }
         
     }
}
#----------------------------------------------------------------
?>