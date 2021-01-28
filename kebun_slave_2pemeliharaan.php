<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
$kdAfd=$_POST['kdAfd'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	$kdOrg=$_GET['kdOrg'];
	$kdAfd=$_GET['kdAfd'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];     
}
if($kdAfd=='')
    $kdAfd=$kdOrg;

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);
//echo"Error: ".$tgl1_;

 
 
 
if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($kdOrg==''){
            echo"Error: Kebun dan Afdeling tidak boleh kosong."; exit;
    }

    if(($tgl1_=='')or($tgl2_=='')){
            echo"Error: Tanggal tidak boleh kosong."; exit;
    }

    if($tgl1>$tgl2){
            echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua."; exit;
    }
	
}
if ($proses=='excel' or $proses=='preview')
{
 
$str="select a.notransaksi, 'BKM' as sumber, b.tanggal,a.kodeorg,a.kodekegiatan,c.namakegiatan,a.hasilkerja,'HA' as satuan,a.jumlahhk,
((a.jumlahhk/d.hk)*e.tumr) as upah,((a.jumlahhk/d.hk)*e.tpremi) as premi,
f.kodebarang,g.namabarang,f.kwantitas,g.satuan as satuanbrg
from ".$dbname.".`kebun_prestasi` a left join ".$dbname.".`kebun_aktifitas` b ON a.notransaksi=b.notransaksi 
left join ".$dbname.".setup_kegiatan c ON a.kodekegiatan=c.kodekegiatan
left join ".$dbname.".kebun_pakaimaterial f ON a.notransaksi=f.notransaksi and a.kodekegiatan=f.kodekegiatan and a.kodeorg=f.kodeorg
left join ".$dbname.".log_5masterbarang g ON f.kodebarang=g.kodebarang
left join (select notransaksi,sum(umr) as tumr,sum(insentif) as tpremi from ".$dbname.".`kebun_kehadiran` group by notransaksi) e ON a.notransaksi=e.notransaksi
left join (select notransaksi,sum(jumlahhk) as hk from ".$dbname.".`kebun_prestasi` group by notransaksi) d ON a.notransaksi=d.notransaksi
 where b.tipetransaksi in ('LC','BBT','TBM','TM') and a.kodeorg like '".$kdAfd."%' and b.tanggal between '".$tgl1_."' and '".$tgl2_."';";
 
	$no=1;
    $oldnotrans='';
	$Item=array();
	$res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
		
		$Item['A'][]=$no;
		$Item['B'][]=$bar->notransaksi;
		$Item['C'][]=$bar->sumber;
		$Item['D'][]=tanggalnormal($bar->tanggal);
		$Item['E'][]=$bar->kodeorg;
		$Item['F'][]=$bar->kodekegiatan;
		$Item['G'][]=$bar->namakegiatan;
		$Item['H'][]=$bar->hasilkerja;
		$Item['I'][]=$bar->satuan;
		$Item['J'][]=$bar->jumlahhk;
		$Item['K'][]=$bar->upah;
		$Item['L'][]=$bar->premi;
		$Item['M'][]=$bar->kodebarang;
		$Item['N'][]=$bar->namabarang;
		$Item['O'][]=$bar->kwantitas;
		$Item['P'][]=$bar->satuanbrg;
		$no++;
	}
	
	$no=1;
		
 $stream.="<table cellspacing='1' border='0' class='sortable'>
	<thead> 
	<tr class=rowheader>
    <td>".$_SESSION['lang']['nomor']."</td>
    <td>".$_SESSION['lang']['notransaksi']."</td>    
	<td>".$_SESSION['lang']['sumber']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['lokasi']."</td>
	<td>".$_SESSION['lang']['kodekegiatan']."</td>            
	<td>".$_SESSION['lang']['kegiatan']."</td>
	<td>".$_SESSION['lang']['hasilkerjarealisasi']."</td>
	<td>".$_SESSION['lang']['satuan']."</td>
    <td>".$_SESSION['lang']['jumlahhk']."</td>
	<td>".$_SESSION['lang']['upahkerja']."</td>
	<td>".$_SESSION['lang']['insentif']."</td>
    <td>".$_SESSION['lang']['kodebarang']."</td> 
    <td>".$_SESSION['lang']['namabarang']."</td>
    <td>".$_SESSION['lang']['jumlah']."</td>  
    <td>".$_SESSION['lang']['satuan']."</td>     
    </tr></thead>
	<tbody>";
	 foreach($Item['A'] as $key =>$val){
		 
		 $stream.="<tr class=rowcontent>
            <td align=center >".$val."</td>
            <td align=center>".$Item['B'][$key]."</td>   
            <td align=center>".$Item['C'][$key]."</td>   
            <td align=center>".$Item['D'][$key]."</td>   
            <td align=left>".$Item['E'][$key]."</td>   
            <td align=center>".$Item['F'][$key]."</td>   
            <td align=left>".$Item['G'][$key]."</td>   
            <td align=right>".$Item['H'][$key]."</td>   
            <td align=right>".$Item['I'][$key]."</td>   			
            <td align=right>".$Item['J'][$key]."</td>   
			<td align=right>".number_format($Item['K'][$key],2)."</td>  
			<td align=right>".number_format($Item['L'][$key],2)."</td>   			
			<td align=center>".$Item['M'][$key]."</td>   			
			<td>".$Item['N'][$key]."</td>   			
			<td align=right>".$Item['O'][$key]."</td>   			
			<td>".$Item['P'][$key]."</td>   			
			
            </tr>";
			
		$thk+=$Item['J'][$key];
	 }
        
		
		
			
		
  $stream.="
	<tr class=rowcontent>
	<td colspan=9>Total</td>
	<td align=right>".$thk."</td>
	<td colspan=6 align=right></td>
	<tr>
     </tbody></table>";
 
 
 
 
 /*
	
//ambil material
    $str="select a.notransaksi,a.kwantitas,a.kodebarang, b.namabarang,b.satuan from
          ".$dbname.".kebun_pakai_material_vw a left join ".$dbname.".log_5masterbarang b
          on a.kodebarang=b.kodebarang    
          where  a.kodeorg like '".$kdAfd."%' and a.tanggal between '".$tgl1_."' and '".$tgl2_."'";
		 
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $barang[$bar->notransaksi]['kodebarang'][]=$bar->kodebarang;
        $barang[$bar->notransaksi]['namabarang'][]=$bar->namabarang;
        $barang[$bar->notransaksi]['satuan'][]=$bar->satuan;
        $barang[$bar->notransaksi]['jumlah'][]=$bar->kwantitas;
    }
 

   /* $str=" select distinct upah,sum(jumlahhk) as jumlahhk,sumber,unit,notransaksi,kodekegiatan,kodeorg,hasilkerja,jumlahhk,tanggal,upah,premi,namakegiatan,satuan,kelompok from ".$dbname.".kebun_perawatan_dan_spk_vw where kodeorg like '".$kdAfd."%' 
             and tanggal between '".$tgl1_."' and '".$tgl2_."' group by notransaksi,jumlahhk";// group by kodekegiatan
	*/		 
	
	
	/*
	$str="select * from ".$dbname.".kebun_perawatan_dan_spk_vw where kodeorg like '".$kdAfd."%' 
            and tanggal between '".$tgl1_."' and '".$tgl2_."' ";//kodekegiatan		 
	//echo $str;		 
        $res=mysql_query($str);
		 
		//echo $str;
	$stream.="<table cellspacing='1' border='0' class='sortable'>
	<thead>
	<tr class=rowheader>
        <td>".$_SESSION['lang']['nomor']."</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>    
	<td>".$_SESSION['lang']['sumber']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['lokasi']."</td>
	<td>".$_SESSION['lang']['kodekegiatan']."</td>            
	<td>".$_SESSION['lang']['kegiatan']."</td>
	<td>".$_SESSION['lang']['hasilkerjarealisasi']."</td>
	<td>".$_SESSION['lang']['satuan']."</td>
        <td>".$_SESSION['lang']['jumlahhk']."</td>
	<td>".$_SESSION['lang']['upahkerja']."</td>
	<td>".$_SESSION['lang']['insentif']."</td>
        <td>".$_SESSION['lang']['kodebarang']."</td> 
        <td>".$_SESSION['lang']['namabarang']."</td>
        <td>".$_SESSION['lang']['jumlah']."</td>  
        <td>".$_SESSION['lang']['satuan']."</td>     
        </tr></thead>
	<tbody>";
        $no=0;
        $oldnotrans='';
        while($bar=mysql_fetch_object($res))
        {
			/*
			$ha="select distinct upah,sum(jumlahhk) as jhk from ".$dbname.".kebun_perawatan_dan_spk_vw where notransaksi='".$bar->notransaksi."'";
			*/
			/*
			$ha="select notransaksi, sum(upah) as upah,sum(jumlahhk) as jhk from ".$dbname.".kebun_perawatan_dan_spk_vw where notransaksi='".$bar->notransaksi."'";
			
			//echo $ha;
			$hi=mysql_query($ha) or die (mysql_error());
			$hu=mysql_fetch_assoc($hi);
			
				$hkpro=$hu['jhk'];
				$upahpro=$hu['upah'];
				$nilaiupah=$upahpro/$hkpro;
				$upah=$nilaiupah*$bar->jumlahhk;
				
				
			
            $no+=1;
            $notran=$bar->notransaksi;
            if($notran!=$oldnotrans and $no!=1)
            {
                if(is_array($barang[$oldnotrans]['kodebarang'])){
                foreach($barang[$oldnotrans]['kodebarang'] as $key =>$val){
                $stream.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$oldnotrans."</td>    
                <td>BKM</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>         
                <td align=right></td>                 
                <td></td>
                <td align=right></td>
                <td align=right></td>
                <td align=right></td>
                <td>".$barang[$oldnotrans]['kodebarang'][$key]."</td> 
                <td>".$barang[$oldnotrans]['namabarang'][$key]."</td>
                <td>".$barang[$oldnotrans]['jumlah'][$key]."</td>  
                <td>".$barang[$oldnotrans]['satuan'][$key]."</td>  
                </tr>";  
                }
                }
            }   
            $stream.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>    
            <td>".$bar->sumber."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->kodeorg."</td>
            <td>".$bar->kodekegiatan."</td>
            <td>".$bar->namakegiatan."</td>         
            <td align=right>".number_format($bar->hasilkerja,2)."</td>                 
            <td>".$bar->satuan."</td>
            <td align=right>".number_format($bar->jumlahhk,2)."</td>
            <td align=right>".number_format($upah,2)."</td>
            <td align=right>".number_format($bar->premi)."</td>
            <td>-</td> 
            <td>-</td>
            <td>-</td>  
            <td>-</td>                  
            </tr>";
            
            $oldnotrans=$notran;
            $thk+=$bar->jumlahhk;
           //$tupah+=$bar->upah;
		   $tupah+=$upah;
            $tpremi+=$bar->premi;
        }
		
       $stream.="
	<tr class=rowcontent>
	<td colspan=9>Total</td>
	<td align=right>".number_format($thk,2)."</td>
	<td align=right>".number_format($tupah,2)."</td>
	<td align=right>".number_format($tpremi,2)."</td>
        <td>-</td> 
        <td>-</td>
        <td>-</td>  
        <td>-</td>  
        </tbody></table>";
 //echo "error: ".mysql_error(); exit;
 */
}  
switch($proses)
{
      case 'getAfdAll':
          $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                where kodeorganisasi like '".$kdAfd."%' and length(kodeorganisasi)=6 order by namaorganisasi";
          $op="<option value=''>".$_SESSION['lang']['all']."</option>";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $op.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
          }
          echo $op;
          exit();
      break; 
       case'preview':
            echo $stream;    
			exit();
	break;
        case 'excel':
            $stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHms");
            $nop_="Laporan_perawatan".$kdAfd.$tgl1_."-".$tgl2_;
             $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
             gzwrite($gztralala, $stream);
             gzclose($gztralala);
             echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls.gz';
                </script>";  
		exit();				
        break;    
}

?>