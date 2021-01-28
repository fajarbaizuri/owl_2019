<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['tglAkhir']==''?$tglAkhir=$_GET['tglAkhir']:$tglAkhir=$_POST['tglAkhir'];
$_POST['tglAwal']==''?$tglAwal=$_GET['tglAwal']:$tglAwal=$_POST['tglAwal'];

$_POST['kodekegiatan']==''?$kodekegiatan=$_GET['kodekegiatan']:$kodekegiatan=$_POST['kodekegiatan'];
$_POST['supplierid']==''?$supplierid=$_GET['supplierid']:$supplierid=$_POST['supplierid'];
$_POST['kodeblok']==''?$kodeblok=$_GET['kodeblok']:$kodeblok=$_POST['kodeblok'];
$_POST['kodeafd']==''?$kodeafd=$_GET['kodeafd']:$kodeafd=$_POST['kodeafd'];


$param = $_POST;
foreach($_GET as $key=>$row){
	$param[$key]=$row;
}

/*
 <td>No.</td>
                       <td>No.SPK</td>
					   <td>Kontraktor</td>
                       <td>Blok SPK</td>
					   <td>Afdeling</td>
                       <td>Akun SPK</td>
                       <td>kegiatan</td>                       
                       <td>HK-SPK</td>
                       <td>Volume-SPK</td>
                       <td>satuan</td>
					   <td>Harga Satuan</td>
                       <td>Rupiah SPK</td>
                       <td>Akun Realisasi</td>
                       <td>tanggal</td>
                       <td>Hasil Realisasi</td>
                       <td>HK-Realisasi</td>
                       <td>Rupiah Realisasi</td>
                       <td>Blok Realisasi</td>
*/
$stream="
       <table class=sortable cellspacing=1 border=0>
	     <thead>
		    <tr class=rowheader>
                       <td>No.</td>
                       <td>No.SPK</td>
					   <td>Kontraktor</td>
                       <td>Blok SPK</td>
					   <td>Afdeling</td>
                       
                       <td>kegiatan</td>                       
                       <td>HK-SPK</td>
                       <td>Volume-SPK</td>
                       <td>satuan</td>
					   <td>Harga Satuan</td>
                       <td>Rupiah SPK</td>
                       
                       <td>tanggal</td>
                       <td>Hasil Realisasi</td>
                       <td>HK-Realisasi</td>
                       <td>Rupiah Realisasi</td>
                       

		     </tr>  
		 </thead>
		 <tbody>";
$where = "";
if($param['kodekegiatan']!='') {
	$where .= " and a.kodekegiatan='".$param['kodekegiatan']."'";
}
if($param['supplierid']!='') {
	$where .= " and e.supplierid='".$param['supplierid']."'";
}
if($param['kodeblok']!='') {
	$where .= " and a.kodeblok='".$param['kodeblok']."'";
} else {
	$where .= " and a.kodeblok like '".$unit."%'";
}

if($param['kodeafd']!='') {
	$where .= " and left(a.kodeblok,6)='".$param['kodeafd']."'";
} else {
	$where .= " and left(a.kodeblok,6) like '".$unit."%'";
}

$str="SELECT a.notransaksi, a.kodeblok as blokspk,left(a.kodeblok,6) as afdeling ,a.kodekegiatan as kegspk, a.hk as hkspk, a.hasilkerjajumlah as hasilspk, a.satuan, a.hargasatuan,a.jumlahrp as rpspk,
       b.kodekegiatan as kegrealisasi, b.tanggal, b.hasilkerjarealisasi as hasilrealisasi, b.hkrealisasi, b.jumlahrealisasi as rprealisasi,
       b.kodeblok as blokrealisasi,c.namakegiatan,d.koderekanan,e.namasupplier
 FROM ".$dbname.".log_spkdt a left join ".$dbname.".log_baspk b on a.notransaksi=b.notransaksi and a.kodeblok=b.kodeblok
 and a.kodekegiatan=b.kodekegiatan left join ".$dbname.".setup_kegiatan c on a.kodekegiatan=c.kodekegiatan
 left join ".$dbname.".log_spkht d on a.notransaksi=d.notransaksi
 left join ".$dbname.".log_5supplier e on d.koderekanan=e.supplierid
 where b.tanggal between '".tanggalsystem($tglAwal)."' and '".tanggalsystem($tglAkhir)."' ".$where."
 order by a.notransaksi,a.kodekegiatan,a.kodeblok,b.tanggal";
 //echo $str;

$res=mysql_query($str);
$no=0;
$oldspk='';
 $kgr=0;
 $hsr=0;
 $hkr=0;
 $rpr=0;
 
 $total = array(
	'hkspk'=>0,
	'hasilspk'=>0,
	'rpspk'=>0,
	'hasilrealisasi'=>0,
	'hkrealisasi'=>0,
	'rprealisasi'=>0
 );
while($bar=mysql_fetch_object($res))
{
    $no+=1;
         $kgr+=$bar->kegrealisasi;
         $hsr+=$bar->hasilrealisasi;
         $hkr+=$bar->hkrealisasi;
         $rpr+=$bar->rprealisasi;

    $newspk=$bar->notransaksi.$bar->blokspk.$bar->kegspk;
	/*
    if($oldspk==$newspk){
    $stream.="<tr class=rowcontent>
                       <td>".$no."</td>".
					   "<td colspan=10></td>".
//					   "<td>".$bar->notransaksi."</td>
//                       <td>".$bar->blokspk."</td>
//                       <td>".$bar->kegspk."</td>
//                       <td>-</td>
//                       <td>-</td>
//                       <td>-</td>
//                       <td>-</td>
//                       <td>-</td>".
                       "<td align=right>".$bar->kegrealisasi."</td>
                       <td>".tanggalnormal($bar->tanggal)."</td>
                       <td align=right>".number_format($bar->hasilrealisasi,2)."</td>
                       <td align=right>".number_format($bar->hkrealisasi,2)."</td>
                       <td align=right>".number_format($bar->rprealisasi,2)."</td>
                      
		     </tr>";    
    }
    else{    
	*/
		/*
		$stream.="<tr class=rowcontent>
                       <td>".$no."</td>
                       <td>".$bar->notransaksi."</td>
					   <td>".$bar->namasupplier."</td>
                       <td>".$bar->blokspk."</td>
					   <td>".$bar->afdeling."</td>
                       <td>".$bar->kegspk."</td>
                       <td>".$bar->namakegiatan."</td>                           
                       <td align=right>".number_format($bar->hkspk,2)."</td>
                       <td align=right>".number_format($bar->hasilspk,2)."</td>
                       <td>".$bar->satuan."</td>
					   <td align=right>".number_format($bar->hargasatuan,2)."</td>
                       <td align=right>".number_format($bar->rpspk,2)."</td>
                       <td align=right>".$bar->kegrealisasi."</td>
                       <td>".tanggalnormal($bar->tanggal)."</td>
                       <td align=right>".number_format($bar->hasilrealisasi,2)."</td>
                       <td align=right>".number_format($bar->hkrealisasi,2)."</td>
                       <td align=right>".number_format($bar->rprealisasi,2)."</td>
                       <td>".$bar->blokrealisasi."</td>
		     </tr>";
		*/
        $stream.="<tr class=rowcontent>
                       <td>".$no."</td>
                       <td>".$bar->notransaksi."</td>
					   <td>".$bar->namasupplier."</td>
                       <td>".$bar->blokspk."</td>
					   <td>".$bar->afdeling."</td>
                      
                       <td>".$bar->namakegiatan."</td>                           
                       <td align=right>".number_format($bar->hkspk,2)."</td>
                       <td align=right>".number_format($bar->hasilspk,2)."</td>
                       <td>".$bar->satuan."</td>
					   <td align=right>".number_format($bar->hargasatuan,2)."</td>
                       <td align=right>".number_format($bar->rpspk,2)."</td>
                       
                       <td>".tanggalnormal($bar->tanggal)."</td>
                       <td align=right>".number_format($bar->hasilrealisasi,2)."</td>
                       <td align=right>".number_format($bar->hkrealisasi,2)."</td>
                       <td align=right>".number_format($bar->rprealisasi,2)."</td>
                       
		     </tr>";
    //}
	$total['hkspk']+=$bar->hkspk;
	$total['hasilspk']+=$bar->hasilspk;
	$total['rpspk']+=$bar->rpspk;
	$total['hasilrealisasi']+=$bar->hasilrealisasi;
	$total['hkrealisasi']+=$bar->hkrealisasi;
	$total['rprealisasi']+=$bar->rprealisasi;
    $oldspk=$newspk;
}
$stream.="<tr class=rowcontent style='font-weight:bold'>
			<td colspan=10 align=center>TOTAL</td>
			<td align=right>".number_format($total['rpspk'],2)."</td>
			<td align=right></td>
			<td align=right>".number_format($total['hasilrealisasi'],2)."</td>
			<td align=right>".number_format($total['hkrealisasi'],2)."</td>
			<td align=right>".number_format($total['rprealisasi'],2)."</td>
			
  </tr>";

$stream.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";
    
switch ($proses){
    case 'html':
        echo  $stream;
	break;
   case 'excel':
        $nop_="RealisasiSPK_".$unit."_".tanggalsystem($tglAwal)."_".tanggalsystem($tglAkhir);
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
	break;
	
	default:
	break;
}
?>