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
}
else
{
    $proses=$_GET['proses'];
}

$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];

$qwe=explode('-',$periode);
$tahun=$qwe[0]; $bulan=$qwe[1];

if($bulan=='01'){
    $tahunlalu=$tahun-1;
    $bulanlalu='12';
}else{
    $tahunlalu=$tahun;
    $bulanlalu=$bulan-1;
    if(strlen($bulanlalu)==1)$bulanlalu='0'.$bulanlalu;
}

$periodelalu=$tahunlalu.'-'.$bulanlalu;

if($periode=='')
{
    exit("Error: ".$_SESSION['lang']['periode']." tidak boleh kosong");
}
if($kdUnit!='')
{
    $unitId=$optNmOrg[$kdUnit];
}
else
{
    exit("Error:".$_SESSION['lang']['unit']." Tidak boleh kosong");
}

$AREALST="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi like '%".$kdUnit."%'";
$queryAB=mysql_query($AREALST) or die(mysql_error($conn));
while($resAB=mysql_fetch_assoc($queryAB))
{
    $llArr[$resAB['kodeorganisasi']]=$resAB['namaorganisasi'];
}   

// data panen bulan ini
$panen="select A.blok,B.tahuntanam,B.luasareaproduktif, SUM(A.jjg) as jjg,SUM(A.kgwb) as kgwb,SUM(A.kgwb)/SUM(A.jjg) as bjr,B.jumlahpokok from ".$dbname.".kebun_spb_vw A LEFT JOIN ".$dbname.".setup_blok B ON A.blok=B.kodeorg where A.tanggal like '".$periode."%'
and A.kodeorg='".$kdUnit."' and  A.blok like '%".$afdId."%' GROUP BY A.blok order by A.blok asc";
//echo $panen;
$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
	$Aksi[$res['blok']]['Blok']=$res['blok'];
	$Aksi[$res['blok']]['ThnTnm']=$res['tahuntanam'];
	$Aksi[$res['blok']]['Luas']=$res['luasareaproduktif'];
	$Aksi[$res['blok']]['Pokok']=$res['jumlahpokok'];
	$Aksi[$res['blok']]['Kg']=$res['kgwb'];
	$Aksi[$res['blok']]['Jjg']=$res['jjg'];
	$Aksi[$res['blok']]['BJR']=$res['bjr'];
	$Aksi2[substr($res['blok'],0,6)]['Luas']+=$res['luasareaproduktif'];
	$Aksi2[substr($res['blok'],0,6)]['Pokok']+=$res['jumlahpokok'];
	$Aksi3[substr($res['blok'],0,4)]['Luas']+=$res['luasareaproduktif'];
	$Aksi3[substr($res['blok'],0,4)]['Pokok']+=$res['jumlahpokok'];
}   

$panen1="select left(A.blok,6) as blok, SUM(A.jjg) as jjg,sum(A.kgwb) as kgwb,SUM(A.kgwb)/SUM(A.jjg) as bjr from ".$dbname.".kebun_spb_vw A LEFT JOIN ".$dbname.".setup_blok B ON A.blok=B.kodeorg where A.tanggal like '".$periode."%'
and A.kodeorg='".$kdUnit."' and  A.blok like '%".$afdId."%' GROUP BY LEFT(A.blok,6) order by A.blok asc";
//echo $panen1;
$query1=mysql_query($panen1) or die(mysql_error($conn));
while($res1=mysql_fetch_assoc($query1))
{
	$Aksi2[$res1['blok']]['Blok']=$res1['blok'];
	

	
	$Aksi2[$res1['blok']]['Kg']=$res1['kgwb'];
	$Aksi2[$res1['blok']]['Jjg']=$res1['jjg'];
	$Aksi2[$res1['blok']]['BJR']=$res1['bjr'];
}   


$panen2="select left(A.blok,4) as blok, SUM(A.jjg) as jjg,sum(A.kgwb) as kgwb,SUM(A.kgwb)/SUM(A.jjg) as bjr from ".$dbname.".kebun_spb_vw A LEFT JOIN ".$dbname.".setup_blok B ON A.blok=B.kodeorg where A.tanggal like '".$periode."%'
and A.kodeorg='".$kdUnit."' and  A.blok like '%".$afdId."%' GROUP BY LEFT(A.blok,4) order by A.blok asc";
//echo $panen;
$query2=mysql_query($panen2) or die(mysql_error($conn));
while($res2=mysql_fetch_assoc($query2))
{
	$Aksi3[$res2['blok']]['Blok']=$res2['blok'];
	
	
	
	$Aksi3[$res2['blok']]['Kg']=$res2['kgwb'];
	$Aksi3[$res2['blok']]['Jjg']=$res2['jjg'];
	$Aksi3[$res2['blok']]['BJR']=$res2['bjr'];
} 

$brdr=0;
$bgcoloraja='';
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tabA="
    <table>
    <tr><td colspan=5 align=left><b>DATA PRODUKSI TAHUN TANAM PERBLOK</b></td><td colspan=2 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=5 align=left>Kebun : ".$optNmOrg[$kdUnit]." </td></tr>";
    if($afdId!='')
    {
        $tabA.="<tr><td colspan=5 align=left>".$_SESSION['lang']['afdeling']." : ".$optNmOrg[$afdId]." </td></tr>";
    }
    $tabA.="<tr><td colspan=5 align=left>&nbsp;</td></tr>
    </table>";
	
	
}
    $tabA.="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." >Blok</td>
		<td ".$bgcoloraja." >Tahun Tanam</td>
		<td ".$bgcoloraja." >Luas Areal</td>
		<td ".$bgcoloraja." >Jumlah Pokok</td>
		<td ".$bgcoloraja." >Produksi (Kg)</td>
		<td ".$bgcoloraja." >Janjang (Jjg)</td>
		<td ".$bgcoloraja." >BJR</td>
	</tr>";
        $tabA.="</thead>
	<tbody>";
	
		$LIS="A";
        foreach($Aksi as $lsBlok)
        {
				
				
				if ($LIS=="A"){
					B:
					$tabA.="<tr >";
					$tabA.="<td  colspan=7></td>";
					$tabA.="</tr>";
					$tabA.="<tr >";
					$tabA.="<td  colspan=7><B>".substr($lsBlok['Blok'],0,6)." : ".$llArr[substr($lsBlok['Blok'],0,6)]."</td>";
					$tabA.="</tr>";
					
					goto A;
				}else if($LIS==substr($lsBlok['Blok'],0,6)){
					A:
					$tabA.="<tr class=rowcontent>";
					$tabA.="<td >".$lsBlok['Blok']."</td>";
					$tabA.="<td align=center >".$lsBlok['ThnTnm']."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Luas'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Pokok'])."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Kg'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Jjg'])."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['BJR'],2)."</td>";
					$tabA.="</tr>";
					$LIS=substr($lsBlok['Blok'],0,6);
				}else{
					$tabA.="<tr  class=rowcontent>";
					$tabA.="<td  colspan=2><b>SubTotal ".$LIS."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Luas'],2)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Pokok'])."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Kg'],2)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Jjg'])."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['BJR'],2)."</td>";
					$tabA.="</tr>";
					goto B;
				}
				
			
        }
					$tabA.="<tr  class=rowcontent>";
					$tabA.="<td  colspan=2><b>SubTotal ".$LIS."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Luas'],2)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Pokok'])."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Kg'],2)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['Jjg'])."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi2[$LIS]['BJR'],2)."</td>";
					$tabA.="</tr>";
					
					$tabA.="<tr  class=rowcontent>";
					$tabA.="<td  colspan=2><b>Total ".substr($LIS,0,4)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi3[substr($LIS,0,4)]['Luas'],2)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi3[substr($LIS,0,4)]['Pokok'])."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi3[substr($LIS,0,4)]['Kg'],2)."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi3[substr($LIS,0,4)]['Jjg'])."</td>";
					$tabA.="<td align=right><b>".number_format($Aksi3[substr($LIS,0,4)]['BJR'],2)."</td>";
					$tabA.="</tr>";
					
					$tabA.="</tbody></table>";
		
		
		
       
switch($proses)
{
	case'preview':
	echo $tabA;
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$data=$tabA;
		$tglSkrg=date("Ymd");
		$nop_="PRODUKSI_KEBUN_".$tglSkrg;
		if(strlen($data)>0)
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
			if(!fwrite($handle,$data))
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