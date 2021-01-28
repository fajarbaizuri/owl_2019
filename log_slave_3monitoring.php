<?php 
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}else{
	$proses=$_GET['proses'];
}
//$arr="##klmpkBrg##kdBrg##tglDr##tanggalSampai";
//$arr="##thn##gudang##status";

$_POST['thn']==''?$thn=$_GET['thn']:$thn=$_POST['thn'];
$_POST['gudang']==''?$gudang=$_GET['gudang']:$gudang=$_POST['gudang'];
$_POST['status']==''?$status=$_GET['status']:$status=$_POST['status'];

$data=array();
$dataA=array();

//realisasi keluar dan masuk;

$sData="select left(a.periode,4) as tahun,a.kodebarang, a.kodegudang,sum(a.qtymasuk) as masukqty,sum(a.qtykeluar) as keluarqty from ".$dbname.".log_5saldobulanan a where left(a.periode,4) like '".$thn."' and a.kodegudang like '".$gudang."' group by left(a.periode,4),a.kodebarang,a.kodegudang order by a.kodebarang";
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData))
{
	 $data[$rData['tahun']][$rData['kodebarang']][$rData['kodegudang']]['masuk']=$rData['masukqty'];
	 $data[$rData['tahun']][$rData['kodebarang']][$rData['kodegudang']]['keluar']=$rData['keluarqty'];
}

//saldo awal; 
 if($thn=="%%"){
	 $thnA="____";
 }else{
	 $thnA=$thn;
 }
$sDataA="select left(a.periode,4) as tahun,a.kodebarang, a.kodegudang,a.saldoawalqty as sawalqty from ".$dbname.".log_5saldobulanan a where a.periode like '".$thnA."-01' and a.kodegudang like '".$gudang."'  order by a.kodebarang";
$qDataA=mysql_query($sDataA) or die(mysql_error($conn));
while($rDataA=mysql_fetch_assoc($qDataA))
{
	 $data[$rDataA['tahun']][$rDataA['kodebarang']][$rDataA['kodegudang']]['awal']=$rDataA['sawalqty'];
}

$sDataB="select * from ".$dbname.".log_5monitoring_vw where kodegudang like '".$gudang."' and tahun like '".$thn."' and kdstatus like '".$status."' order by tahun desc,gudang asc, namabarang asc";
$no=0;
$qDataB=mysql_query($sDataB) or die(mysql_error($conn));
while($rDataB=mysql_fetch_assoc($qDataB))
{
	 $no++;
	 $dataA[$no][1]=$no;
	 $dataA[$no][2]=$rDataB['tahun'];
	 $dataA[$no][3]=$rDataB['gudang'];
	 $dataA[$no][4]=$rDataB['kodebarang'];
	 $dataA[$no][5]=$rDataB['namabarang'];
	 $dataA[$no][6]=$rDataB['satuan'];
	 $dataA[$no][7]=$rDataB['status'];
	 $dataA[$no][8]=$rDataB['lifetime'];
	 $dataA[$no][9]=$rDataB['qtythn'];
	 $dataA[$no][10]=$rDataB['qtymin'];
	 $awal=(!empty($data[$rDataB['tahun']][$rDataB['kodebarang']][$rDataB['kodegudang']]['awal']))?$data[$rDataB['tahun']][$rDataB['kodebarang']][$rDataB['kodegudang']]['awal']:0;
	 $masuk=(!empty($data[$rDataB['tahun']][$rDataB['kodebarang']][$rDataB['kodegudang']]['masuk']))?$data[$rDataB['tahun']][$rDataB['kodebarang']][$rDataB['kodegudang']]['masuk']:0;
	 $keluar=(!empty($data[$rDataB['tahun']][$rDataB['kodebarang']][$rDataB['kodegudang']]['keluar']))?$data[$rDataB['tahun']][$rDataB['kodebarang']][$rDataB['kodegudang']]['keluar']:0;
	 $saldo=(($awal+$masuk)-$keluar);
	 $dataA[$no][11]=$awal;
	 $dataA[$no][12]=$masuk;
	 $dataA[$no][13]=$keluar;
	 $dataA[$no][14]=$saldo;
	 $dataA[$no][15]=$saldo-$rDataB['qtymin'];
	 
}

//echo $sDataB;
//echo $dataA[1][15];
//exit;
    
switch($proses)
{
	case 'preview':
	$tab="<table cellspacing=1 border=0 class=sortable>
	<thead >
	<tr class=rowheader >
	   <td rowspan=\"2\" align=center>No</td>
	   <td rowspan=\"2\" align=center>Tahun</td>
	   <td rowspan=\"2\" align=center>Gudang</td>
	   <td rowspan=\"2\" align=center>Kode</td>
	   <td rowspan=\"2\" align=center>Barang</td>
	   <td rowspan=\"2\" align=center>Satuan</td>
	   <td rowspan=\"2\" align=center>Status</td>
	   <td rowspan=\"2\" align=center>Life Time (Jam)</td>
	   <td colspan=\"2\" align=center>Quantity</td>
	   <td colspan=\"3\" align=center>Persediaan</td>
	   <td colspan=\"2\" align=center>Stock</td>
	 </tr>";
	 $tab.="<tr  class=rowheader>
	   <td align=center>Pertahun</td>
	   <td align=center>Minimal</td>
	   <td align=center>Awal</td>
	   <td align=center>Masuk</td>
	   <td align=center>Keluar</td>
	   <td align=center>Tersedia</td>
	   <td align=center>Kekurangan</td>
	 </tr>";
	$tab.="
	</thead>
	<tbody>";
	
		//if (!empty($dataA)){
			foreach($dataA as $row => $rList)
			{
				$tab.="<tr class='rowcontent'><td align=center>".$rList[1]."</td>
				<td align=center>".$rList[2]."</td>
				<td align=center>".$rList[3]."</td>
				<td align=center>".$rList[4]."</td>
				<td align=left>".$rList[5]."</td>
				<td align=center>".$rList[6]."</td>
				<td align=center>".$rList[7]."</td>
				<td align=center>".$rList[8]."</td>
				<td align=center>".$rList[9]."</td>
				<td align=center>".$rList[10]."</td>
				<td align=right>".number_format($rList[11],2)."</td>
				<td align=right>".number_format($rList[12],2)."</td>
				<td align=right>".number_format($rList[13],2)."</td>
				<td align=right>".number_format($rList[14],2)."</td>
				<td align=right>".number_format($rList[15],2)."</td>";
				$tab.="</tr>";
			}
		//}
     
		$tab.="</tbody></table>";
		echo $tab;
	break;
	case'excel':
         
	$tab="<table cellspacing=1 border=1 class=sortable>
	<thead >
	<tr class=rowheader border=0>
	   <td colspan=\"15\" align=center >LAPORAN MONITORING PERSEDIAAN</td>
	 </tr>";
	$tab.="<tr class=rowheader >
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>No</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Tahun</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Gudang</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Kode</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Barang</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Satuan</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Status</td>
	   <td rowspan=\"2\" align=center bgcolor=#DEDEDE>Life Time (Jam)</td>
	   <td colspan=\"2\" align=center bgcolor=#DEDEDE>Quantity</td>
	   <td colspan=\"3\" align=center bgcolor=#DEDEDE>Persediaan</td>
	   <td colspan=\"2\" align=center bgcolor=#DEDEDE>Stock</td>
	 </tr>";
	 $tab.="<tr class=rowheader >
	   <td align=center  bgcolor=#DEDEDE>Pertahun</td>
	   <td align=center  bgcolor=#DEDEDE>Minimal</td>
	   <td align=center bgcolor=#DEDEDE>Awal</td>
	   <td align=center  bgcolor=#DEDEDE>Masuk</td>
	   <td align=center  bgcolor=#DEDEDE>Keluar</td>
	   <td align=center  bgcolor=#DEDEDE>Tersedia</td>
	   <td align=center  bgcolor=#DEDEDE>Kekurangan</td>
	 </tr>";
	$tab.="
	</thead>
	<tbody>";
	if (!empty($dataA)){
			foreach($dataA as $row => $rList)
			{
				$tab.="<tr><td align=center>".$rList[1]."</td>
				<td align=center>".$rList[2]."</td>
				<td align=center>".$rList[3]."</td>
				<td align=center>".$rList[4]."</td>
				<td align=left>".$rList[5]."</td>
				<td align=center>".$rList[6]."</td>
				<td align=center>".$rList[7]."</td>
				<td align=center>".$rList[8]."</td>
				<td align=center>".$rList[9]."</td>
				<td align=center>".$rList[10]."</td>
				<td align=right>".number_format($rList[11],2)."</td>
				<td align=right>".number_format($rList[12],2)."</td>
				<td align=right>".number_format($rList[13],2)."</td>
				<td align=right>".number_format($rList[14],2)."</td>
				<td align=right>".number_format($rList[15],2)."</td>";
				$tab.="</tr>";
			}
		}
     
		$tab.="</tbody></table>";
		echo $tab;
          
            
	
		$tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                        $thisDate=date("YmdHms");
			//$nop_="Laporan_Pembelian";
			$nop_="Laporan_Monitoring_Persediaan_".$thisDate;
        if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    @unlink('tempExcel/'.$file);
                }
            }	
           closedir($handle);
        }
        $handle=fopen("tempExcel/".$nop_.".xls",'w');
        if(!fwrite($handle,$tab))
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
            
	
	break;
	default:
	break;
}
?>