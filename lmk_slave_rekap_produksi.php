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
#$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];

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
$panenA="select LEFT(A.blok,4) AS kebun ,A.blok,B.tahuntanam,B.luasareaproduktif, SUM(A.jjg) as jjg,SUM(A.kgwb) as kgwb,SUM(A.kgwb)/SUM(A.jjg) as bjr,B.jumlahpokok from ".$dbname.".kebun_spb_vw A LEFT JOIN ".$dbname.".setup_blok B ON A.blok=B.kodeorg where A.tanggal like '".$periode."%'
and A.kodeorg='".$kdUnit."'  GROUP BY A.blok order by A.blok asc";
//echo $panen;
$queryA=mysql_query($panenA) or die(mysql_error($conn));
while($resA=mysql_fetch_assoc($queryA))
{
	$AksiA[$resA['blok']]['Blok']=$resA['blok'];
	$AksiA[$resA['blok']]['ThnTnm']=$resA['tahuntanam'];
	$AksiA[$resA['blok']]['Luas']=$resA['luasareaproduktif'];
	$AksiA[$resA['blok']]['Pokok']=$resA['jumlahpokok'];
	$AksiA[$resA['blok']]['Kg']=$resA['kgwb'];
	$AksiA[$resA['blok']]['Jjg']=$resA['jjg'];
	$AksiA[$resA['blok']]['BJR']=$resA['bjr'];
	$AksiB[$resA['tahuntanam']]['Luas']+=$resA['luasareaproduktif'];
	$AksiB[$resA['tahuntanam']]['Pokok']+=$resA['jumlahpokok'];
	$AksiC[$resA['kebun']]['Luas']+=$resA['luasareaproduktif'];
	$AksiC[$resA['kebun']]['Pokok']+=$resA['jumlahpokok'];
	
}   


// data panen bulan ini
$panen="select left(A.blok,4) as blok,B.tahuntanam, SUM(A.jjg) as jjg,sum(A.kgwb) as kgwb,SUM(A.kgwb)/SUM(A.jjg) as bjr from ".$dbname.".kebun_spb_vw A LEFT JOIN ".$dbname.".setup_blok B ON A.blok=B.kodeorg where A.tanggal like '".$periode."%'
and A.kodeorg='".$kdUnit."'  GROUP BY LEFT(A.blok,4),B.tahuntanam order by B.tahuntanam asc";
//echo $panen;
$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
	$Aksi[$res['tahuntanam']]['Blok']=$res['blok'];
	$Aksi[$res['tahuntanam']]['ThnTnm']=$res['tahuntanam'];
	$Aksi[$res['tahuntanam']]['Luas']=$AksiB[$res['tahuntanam']]['Luas'];
	$Aksi[$res['tahuntanam']]['Pokok']=$AksiB[$res['tahuntanam']]['Pokok'];
	$Aksi[$res['tahuntanam']]['Kg']=$res['kgwb'];
	$Aksi[$res['tahuntanam']]['Jjg']=$res['jjg'];
	$Aksi[$res['tahuntanam']]['BJR']=$res['bjr'];
	
	$Aksi[$res['tahuntanam']]['PHA']=$res['kgwb']/$AksiB[$res['tahuntanam']]['Luas'];
	$Aksi[$res['tahuntanam']]['PPK']=$res['kgwb']/$AksiB[$res['tahuntanam']]['Pokok'];
	$Aksi[$res['tahuntanam']]['JPK']=$res['jjg']/$AksiB[$res['tahuntanam']]['Pokok'];
}   

$panen2="select left(A.blok,4) as blok, SUM(A.jjg) as jjg,sum(A.kgwb) as kgwb,SUM(A.kgwb)/SUM(A.jjg) as bjr from ".$dbname.".kebun_spb_vw A LEFT JOIN ".$dbname.".setup_blok B ON A.blok=B.kodeorg where A.tanggal like '".$periode."%'
and A.kodeorg='".$kdUnit."'  GROUP BY LEFT(A.blok,4) order by A.blok asc";
//echo $panen2;
$query2=mysql_query($panen2) or die(mysql_error($conn));
while($res2=mysql_fetch_assoc($query2))
{
	$Aksi1[$res2['blok']]['Blok']=$res2['blok'];
	$Aksi1[$res2['blok']]['Luas']=$AksiC[$res2['blok']]['Luas'];
	$Aksi1[$res2['blok']]['Pokok']=$AksiC[$res2['blok']]['Pokok'];
	$Aksi1[$res2['blok']]['Kg']=$res2['kgwb'];
	$Aksi1[$res2['blok']]['Jjg']=$res2['jjg'];
	$Aksi1[$res2['blok']]['BJR']=$res2['bjr'];
	
	$Aksi1[$res2['blok']]['PHA']=$res2['kgwb']/$AksiC[$res2['blok']]['Luas'];
	$Aksi1[$res2['blok']]['PPK']=$res2['kgwb']/$AksiC[$res2['blok']]['Pokok'];
	$Aksi1[$res2['blok']]['JPK']=$res2['jjg']/$AksiC[$res2['blok']]['Pokok'];
} 

$brdr=0;
$bgcoloraja='';
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tabA="
    <table>
    <tr><td colspan=5 align=left><b>REKAPITULASI PRODUKSI TAHUN TANAM PERBLOK</b></td><td colspan=4 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=5 align=left>Kebun : ".$optNmOrg[$kdUnit]." </td></tr>";
    $tabA.="<tr><td colspan=5 align=left>&nbsp;</td></tr>
    </table>";
	
	
}
    $tabA.="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
		<td rowspan=3 ".$bgcoloraja." >Tahun Tanam</td>
		<td rowspan=3 ".$bgcoloraja." >Luas (Ha)</td>
		<td rowspan=3 ".$bgcoloraja." >Jumlah Pokok (Pkk)</td>
		<td colspan=6 ".$bgcoloraja." >".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</td>
	</tr>	
	<tr>
		<td rowspan=2 ".$bgcoloraja." >Produksi (Kg)</td>
		<td rowspan=2 ".$bgcoloraja." >Janjang (Jjg)</td>
		<td rowspan=2 ".$bgcoloraja." >BJR</td>
		<td colspan=2 ".$bgcoloraja." >Produksi</td>
		<td rowspan=2 ".$bgcoloraja." >Jjg/Pkk</td>
	</tr>	
	<tr>
		<td  ".$bgcoloraja." >Per Ha (Kg)</td>
		<td  ".$bgcoloraja." >Per Pkk (Kg)</td>
	</tr>";
        $tabA.="</thead>
	<tbody>";
	
		
        foreach($Aksi as $lsBlok)
        {
				
				
				
					$tabA.="<tr class=rowcontent>";
					$tabA.="<td align=center >".$lsBlok['ThnTnm']."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Luas'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Pokok'])."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Kg'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['Jjg'])."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['BJR'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['PHA'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['PPK'],2)."</td>";
					$tabA.="<td align=right>".number_format($lsBlok['JPK'],2)."</td>";
					$tabA.="</tr>";
					
        }
					
					$tabA.="<tr  class=rowcontent>";
					$tabA.="<td ><b>Total</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['Luas'],2)."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['Pokok'])."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['Kg'],2)."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['Jjg'])."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['BJR'],2)."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['PHA'],2)."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['PPK'],2)."</td>";
					$tabA.="<td align=right>".number_format($Aksi1[$kdUnit]['JPK'],2)."</td>";
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
		$nop_="REKAPITULASI_PRODUKSI_KEBUN_".$tglSkrg;
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