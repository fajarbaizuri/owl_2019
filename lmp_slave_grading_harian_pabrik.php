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
$_POST['kdtbs']==''?$kdtbs=$_GET['kdtbs']:$kdtbs=$_POST['kdtbs'];


if($periode=='')
{
    exit("Error: ".$_SESSION['lang']['periode']." tidak boleh kosong");
}

//cari jumlah hari

$SQL_H="SELECT DAY(LAST_DAY( '".$periode."-01' ) ) as hari";
$queryH=mysql_query($SQL_H) or die(mysql_error($conn));
$resH=mysql_fetch_assoc($queryH);

$JHari=$resH['hari'];

$HASIL_ARRAY=array();
if ($kdtbs=="0" || $kdtbs=="1"){
// CARI GRADING EKTERNAL
	if ($kdUnit==""){
		$SQL_A="SELECT DATE_FORMAT(a.tanggal, '%e') as periode,a.kodecustomer,b.namasupplier,sum(jumlahtandan1) as jjg, sum(beratmasuk-beratkeluar) as netto, sum(kgpotsortasi) as grading,sum((beratmasuk-beratkeluar)-kgpotsortasi) as normal FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".log_5supplier b ON a.kodecustomer =b.kodetimbangan where a.kodeorg='' and a.kodebarang ='40000003' and DATE_FORMAT(a.tanggal, '%Y-%m') ='".$periode."' group by DATE_FORMAT(a.tanggal, '%Y-%m-%e'),a.kodecustomer order by b.namasupplier asc;";
	}else{
		$SQL_A="SELECT DATE_FORMAT(a.tanggal, '%e') as periode,a.kodecustomer,b.namasupplier,sum(jumlahtandan1) as jjg, sum(beratmasuk-beratkeluar) as netto, sum(kgpotsortasi) as grading,sum((beratmasuk-beratkeluar)-kgpotsortasi) as normal FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".log_5supplier b ON a.kodecustomer =b.kodetimbangan where a.kodeorg='' and a.kodebarang ='40000003' and DATE_FORMAT(a.tanggal, '%Y-%m') ='".$periode."' and a.kodecustomer='".$kdUnit."' group by DATE_FORMAT(a.tanggal, '%Y-%m-%e'),a.kodecustomer order by b.namasupplier asc;";
	}
	
	$queryA=mysql_query($SQL_A) or die(mysql_error($conn));
	while($resA=mysql_fetch_assoc($queryA))
	{
		$HASIL_ARRAY[$resA['kodecustomer']]['KEBUN'] = $resA['namasupplier'];
		$HASIL_ARRAY[$resA['kodecustomer']]['AFD'] = "";
		$HASIL_ARRAY[$resA['kodecustomer']][$resA['periode']]['GRADING'] = $resA['grading'];
		
	}   
}
if ($kdtbs=="0" || $kdtbs=="2"){
// CARI GRADING INTERNAL
	if ($kdUnit==""){
		$SQL_B="SELECT DATE_FORMAT(a.tanggal, '%e') as periode,SUBSTR(a.nospb, -14, 6) as afd,c.namaorganisasi as nmafd,a.kodeorg, b.namaorganisasi,sum(jumlahtandan1) as jjg, sum(beratmasuk-beratkeluar) as netto, sum(kgpotsortasi) as grading,sum((beratmasuk-beratkeluar)-kgpotsortasi) as normal FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".organisasi b ON a.kodeorg =b.kodeorganisasi left join ".$dbname.".organisasi c ON SUBSTR(a.nospb, -14, 6) =c.kodeorganisasi where a.kodeorg<>'' and a.kodebarang ='40000003' and DATE_FORMAT(a.tanggal, '%Y-%m') ='".$periode."' and a.kodeorg in ('TDAE','TDBE') group by DATE_FORMAT(a.tanggal, '%Y-%m-%e'),SUBSTR(a.nospb, -14, 6) order by SUBSTR(a.nospb, -14, 6) asc;";
	}else{
		$SQL_B="SELECT DATE_FORMAT(a.tanggal, '%e') as periode,SUBSTR(a.nospb, -14, 6) as afd,c.namaorganisasi as nmafd,a.kodeorg, b.namaorganisasi,sum(jumlahtandan1) as jjg, sum(beratmasuk-beratkeluar) as netto, sum(kgpotsortasi) as grading,sum((beratmasuk-beratkeluar)-kgpotsortasi) as normal FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".organisasi b ON a.kodeorg =b.kodeorganisasi left join ".$dbname.".organisasi c ON SUBSTR(a.nospb, -14, 6) =c.kodeorganisasi where a.kodeorg<>'' and a.kodebarang ='40000003' and DATE_FORMAT(a.tanggal, '%Y-%m') ='".$periode."' and a.kodeorg ='".$kdUnit."' group by DATE_FORMAT(a.tanggal, '%Y-%m-%e'),SUBSTR(a.nospb, -14, 6) order by SUBSTR(a.nospb, -14, 6) asc;";
	}
	
	$queryB=mysql_query($SQL_B) or die(mysql_error($conn));
	while($resB=mysql_fetch_assoc($queryB))
	{	
		$HASIL_ARRAY[$resB['afd']]['KEBUN'] = $resB['namaorganisasi'];
		$HASIL_ARRAY[$resB['afd']]['AFD'] = $resB['nmafd'];
		$HASIL_ARRAY[$resB['afd']][$resB['periode']]['GRADING'] = $resB['grading'];
		
	}   
}

if ($kdtbs=="0" || $kdtbs=="3"){
// CARI GRADING AFILIASI
	if ($kdUnit==""){
		$SQL_C="SELECT DATE_FORMAT(a.tanggal, '%e') as periode,SUBSTR(a.nospb, -14, 6) as afd,c.namaorganisasi as nmafd,a.kodeorg, b.namaorganisasi,sum(jumlahtandan1) as jjg, sum(beratmasuk-beratkeluar) as netto, sum(kgpotsortasi) as grading,sum((beratmasuk-beratkeluar)-kgpotsortasi) as normal FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".organisasi b ON a.kodeorg =b.kodeorganisasi left join ".$dbname.".organisasi c ON SUBSTR(a.nospb, -14, 6) =c.kodeorganisasi where a.kodeorg<>'' and a.kodebarang ='40000003' and DATE_FORMAT(a.tanggal, '%Y-%m') ='".$periode."' and a.kodeorg in ('USJE') group by DATE_FORMAT(a.tanggal, '%Y-%m-%e'),SUBSTR(a.nospb, -14, 6) order by SUBSTR(a.nospb, -14, 6) asc;";
	}else{
		$SQL_C="SELECT DATE_FORMAT(a.tanggal, '%e') as periode,SUBSTR(a.nospb, -14, 6) as afd,c.namaorganisasi as nmafd,a.kodeorg, b.namaorganisasi,sum(jumlahtandan1) as jjg, sum(beratmasuk-beratkeluar) as netto, sum(kgpotsortasi) as grading,sum((beratmasuk-beratkeluar)-kgpotsortasi) as normal FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".organisasi b ON a.kodeorg =b.kodeorganisasi left join ".$dbname.".organisasi c ON SUBSTR(a.nospb, -14, 6) =c.kodeorganisasi where a.kodeorg<>'' and a.kodebarang ='40000003' and DATE_FORMAT(a.tanggal, '%Y-%m') ='".$periode."' and a.kodeorg ='".$kdUnit."' group by DATE_FORMAT(a.tanggal, '%Y-%m-%e'),SUBSTR(a.nospb, -14, 6) order by SUBSTR(a.nospb, -14, 6) asc;";
	}
	
	$queryC=mysql_query($SQL_C) or die(mysql_error($conn));
	while($resC=mysql_fetch_assoc($queryC))
	{
		$HASIL_ARRAY[$resC['afd']]['KEBUN'] = $resC['namaorganisasi'];
		$HASIL_ARRAY[$resC['afd']]['AFD'] = $resC['nmafd'];
		$HASIL_ARRAY[$resC['afd']][$resC['periode']]['GRADING'] = $resC['grading'];
	
	}   
}


$brdr=0;
$bgcoloraja='';
$cols=count($dataAfd)*3;
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tabA="
    <table>
    <tr>
		<td colspan=6 align=left><b>RINCIAN HARIAN GRADING PABRIK</b></td>
	</tr>
	 <tr>
		<td colspan=6 align=left><b>Periode: $periode</b></td>
	</tr>
    </table>";	
}
    
	 
	$tabA.="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tabA.="<tr>
        <td ".$bgcoloraja." rowspan=2>Kebun / Supplier</td>
		<td ".$bgcoloraja." rowspan=2>AFD</td>";
		
        $tabA.="<td  ".$bgcoloraja." colspan='".$resH['hari']."'>RINCIAN GRADING PABRIK (KG) PERTANGGAL</td>";
		$tabA.="<td  ".$bgcoloraja." rowspan=2>Total</td></tr>";
		
        $tabA.="<tr>";
		for ($x = 1; $x <= $resH['hari']; $x++) {
			$tabA.="<td ".$bgcoloraja.">".$x."</td>";
		} 
		
		$tabA.="</tr>";
        $tabA.="</thead>
	<tbody>";
	
	
		for ($x = 1; $x <= $resH['hari']; $x++) {
			$data[$x]=array();
			$data[$x]=0;		
		} 
				
				
        foreach($HASIL_ARRAY as $lsAFD => $x_value)
        {
				$tabA.="<tr class=rowcontent>";
				$tabA.="<td>".$HASIL_ARRAY[$lsAFD]['KEBUN']."</td>";
				$tabA.="<td>".$HASIL_ARRAY[$lsAFD]['AFD']."</td>";
				$total=0;
				for ($x = 1; $x <= $resH['hari']; $x++) {
					$tabA.="<td align=right>".number_format($HASIL_ARRAY[$lsAFD][$x]['GRADING'])."</td>";
					$total+=$HASIL_ARRAY[$lsAFD][$x]['GRADING'];
					$data[$x]+=$HASIL_ARRAY[$lsAFD][$x]['GRADING'];
				} 
				$tabA.="<td align=right>".number_format($total)."</td>";
				

        }
		$totalz=0;
        $tabA.="<tr class=rowcontent>";
        $tabA.="<td colspan=2><b>Subtotal</b></td>";
		for ($x = 1; $x <= $resH['hari']; $x++) {
					 $tabA.="<td  align=right><b>".number_format($data[$x])."</b></td>";
					 $totalz+=$data[$x];
		} 
		$tabA.="<td  align=right><b>".number_format($totalz)."</b></td></tr>";
        $tabA.="</tbody></table>";
		
		
		
		
       
switch($proses)
{
	case'preview':
	echo $tabA;
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$data=$tabA.$tabB.$tabC.$tabD;
		$tglSkrg=date("Ymd");
		$nop_="gradingharianpabrik_".$tglSkrg;
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