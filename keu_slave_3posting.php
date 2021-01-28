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
 $str="select kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG' and 
       kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
	  //echo $str;
 $res=  mysql_query($str);
 $gudang='';
 while($bar=mysql_fetch_array($res))
 {
     $gudang=$bar[0];
 }
 if($gudang=='')
     exit('Error: You have no inventory control');
 else {
    #ambil tanggal awal dan akhir periode gudang bersangkutan
     $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi
           where kodeorg='".$gudang."' and periode='".$param['periode']."'";
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
         #periksa apakah masih ada yang belum posting
         $str="select * from ".$dbname.".log_transaksi_vw 
               where kodegudang='".$gudang."' and tanggal >='".$periodeawal."' 
               and tanggal<='".$periodeakhir."' and post=0 limit 1";
         $res=mysql_query($str);
         if(mysql_num_rows($res)>0)
             exit('Error: Masih ada data gudang yang belum di posting');
         else{
             #ambil transaksi pada periode berjalan===================================
             $str="select * from ".$dbname.".log_transaksi_vw where kodegudang='".$gudang."' 
                   and tanggal >='".$periodeawal."' and tanggal<='".$periodeakhir."'
                   and statusjurnal=0 and kodebarang<40000000 order by tanggal";
				   //echo $str;
             $res=mysql_query($str);             
             if(mysql_num_rows($res)>0)
             {
             echo"<button class=mybutton onclick=prosesGudang(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>TipeTransaksi</td>
                    <td>Tanggal</td>
                    <td>No.Transaksi</td>
                    <td>Kodebarang</td>
                    <td>Jumlah</td>
                    <td>Satuan</td>
                    <td>Supplier</td>
                    <td>Dari/Ke</td>
                    <td>Untuk</td>
                    <td>Blok</td>
                    <td>Mesin/Kend</td>
                    <td>Kegiatan</td>
                    <td>Harga</td>
                    <td>No.PO</td>
                    <td>GUDANG</td>
                    <td>Keterangan</td>
                    </tr>
                  </thead>
                  <tbody>";

             $no=0;
             while($bar=mysql_fetch_object($res))
             { 
                   $no+=1;
                 $nilaitotal=0;
                 if($bar->tipetransaksi==1)
                         $nilaitotal=$bar->hargasatuan*$bar->jumlah;
                 if($nilaitotal==0)
                     $nilaitotal=$bar->hartot;
                 

                     //jika penerimaan walaupun aktiva (>399) maka akan dicatat sebagi hutang
                 echo"<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
                    <td id='tipetransaksi".$no."'>".$bar->tipetransaksi."</td>
                    <td id='tanggal".$no."'>".$bar->tanggal."</td>    
                    <td id='notransaksi".$no."'>".$bar->notransaksi."</td>
                    <td id='kodebarang".$no."'>".$bar->kodebarang."</td>
                    <td align=right id='jumlah".$no."'>".$bar->jumlah."</td>
                    <td id='satuan".$no."'>".$bar->satuan."</td>
                    <td id='idsupplier".$no."'>".$bar->idsupplier."</td>
                    <td id='gudangx".$no."'>".$bar->gudangx."</td>    
                    <td id='untukunit".$no."'>".$bar->untukunit."</td>
                    <td id='kodeblok".$no."'>".$bar->kodeblok."</td>
                    <td id='kodemesin".$no."'>".$bar->kodemesin."</td>
                    <td id='kodekegiatan".$no."'>".$bar->kodekegiatan."</td>    
                    <td align=right id='hartot".$no."'>".number_format($nilaitotal,2,'.','')."</td>
                    <td id='nopo".$no."'>".$bar->nopo."</td>
                    <td id='kodegudang".$no."'>".$bar->kodegudang."</td>
                    <td id='keterangan".$no."'>".$bar->keterangan."</td>    
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
}
#----------------------------------------------------------------
?>