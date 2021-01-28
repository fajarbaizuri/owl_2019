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

// cari tanggal buat mencari apakah tanggal terakhir sebelum bulan lalu panen?
$tanggalakhir = strtotime('-2 month',strtotime($periode.'-01'));
$tanggalakhir = date('Y-m-t',$tanggalakhir);
//-----------------------------------------------------------------
$panen="SELECT kodeorg,tahuntanam,luasareaproduktif FROM  ".$dbname.".`setup_blok` where kodeorg like '".$kdUnit."%' and statusblok='TM' order by kodeorg asc,tahuntanam desc";

$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
	$llArr[$res['kodeorg']]=$res['luasareaproduktif'];	
    $kodeorgArr[$res['kodeorg']]=$res['kodeorg'];
    $ttArr[$res['kodeorg']]=$res['tahuntanam'];
	$tanggallaluArr[$res['kodeorg']]=0;
	$tanggalArr[$res['kodeorg']]=0;
	$tanggalsdArr[$res['kodeorg']]=0;
}   
//-----------------------------------------------------------------
// data panen hari terakhir 2 bulan lalu
/*
$panen="select kodeorg,tanggal from ".$dbname.".kebun_interval_panen_vw
    where  tanggal = '".$tanggalakhir."' and kodeorg like '".$kdUnit."%'";

$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kodeorg']][$res['tanggal']]='P';
}  
*/
// data panen bulan lalu
/*
$panen="select kodeorg,tanggal from ".$dbname.".kebun_interval_panen_vw
    where  tanggal like '".$periodelalu."%' and kodeorg like '".$kdUnit."%'";

$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kodeorgArr[$res['kodeorg']]=$res['kodeorg'];
    $tanggallaluArr[$res['tanggal']]=$res['tanggal'];
    $dzArr[$res['kodeorg']][$res['tanggal']]='P';
}   
*/
//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periodelalu."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periodelalu."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";

$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
 // $kodeorgArr[$res['kodeorg']]=$res['kodeorg'];
    //$tanggallaluArr[$res['tanggal']]=$res['tanggal'];
    //$dzArr[$res['kodeorg']][$res['tanggal']]='P';
	$tanggallaluArr[$res['kodeorg']]=$res['jum'];
}   

// data panen bulan ini
/*
$panen="select kodeorg,tanggal from ".$dbname.".kebun_interval_panen_vw
    where  tanggal like '".$periode."%' and kodeorg like '".$kdUnit."%'";

$query=mysql_query($panen) or die(mysql_error($conn));
*/
//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periode."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periode."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
	
$queryA=mysql_query($panen) or die(mysql_error($conn));
while($resA=mysql_fetch_assoc($queryA))
{
    //$tanggalArr[$res['tanggal']]=$res['tanggal'];
	$tanggalArr[$resA['kodeorg']]=$resA['jum'];
} 
/*
while($res=mysql_fetch_assoc($query))
{
    $tanggalArr[$res['tanggal']]=$res['tanggal'];

	
}   
*/
// data panen sd bulan ini
/*
$panen="select kodeorg,tanggal,tahuntanam from ".$dbname.".kebun_interval_panen_vw
    where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$kdUnit."%'";

$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kodeorgArr[$res['kodeorg']]=$res['kodeorg'];
    $tanggalsdArr[$res['tanggal']]=$res['tanggal'];
    $dzArr[$res['kodeorg']][$res['tanggal']]='P';
    $ttArr[$res['kodeorg']]=$res['tahuntanam'];
}   
*/

//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$kdUnit."%' group by kodeorg ;";
$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$kdUnit."%' group by kodeorg ;";
if($afdId!='')
{
   //$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%' group by kodeorg ;";
	$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%' group by kodeorg ;";
}
$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
   // $kodeorgArr[$res['kodeorg']]=$res['kodeorg'];
   // $tanggalsdArr[$res['tanggal']]=$res['tanggal'];
   // $dzArr[$res['kodeorg']][$res['tanggal']]='P';
   // $ttArr[$res['kodeorg']]=$res['tahuntanam'];
   $tanggalsdArr[$res['kodeorg']]=$res['jum'];
}   
/*
$AREALST="select kodeorg,luasareaproduktif from ".$dbname.".setup_blok where kodeorg like '%".$kdUnit."%'";
$queryAB=mysql_query($AREALST) or die(mysql_error($conn));
while($resAB=mysql_fetch_assoc($queryAB))
{
    $llArr[$resAB['kodeorg']]=$resAB['luasareaproduktif'];
}   
*/

$dzRot=array();
// susun rotasi
if(!empty($kodeorgArr))foreach($kodeorgArr as $koko){

    // bulan ini
	/*
    if(!empty($tanggalArr))foreach($tanggalArr as $tata){
        $kemarin = strtotime('-1 day',strtotime($tata));
        $kemarin = date('Y-m-d',$kemarin);
        if(($dzArr[$koko][$tata]=='P')and($dzArr[$koko][$kemarin]!='P'))
            $dzRot[$koko]['bi']+=1;
    } 
*/	
	$dzRot[$koko]['bi']=$tanggalArr[$koko];
 
}




$sRotasiBudget="select distinct rotasi,kodeorg from ".$dbname.".bgt_budget 
                where tahunbudget='".$tahun."' and kodeorg like '".$kdUnit."%' and kegiatan=611010101";

//exit("Error:".$sRotasiBudget);
$qRotasiBudget=mysql_query($sRotasiBudget) or die(mysql_error());
while($rRotasiBudget=mysql_fetch_assoc($qRotasiBudget))
{
    @$dzRot[$rRotasiBudget['kodeorg']]['bib']+=$rRotasiBudget['rotasi']/12;
    $dzRot[$rRotasiBudget['kodeorg']]['sdb']+=$dzRot[$rRotasiBudget['kodeorg']]['bib']*(intval($bulan));
}

// urut blok
if(!empty($kodeorgArr))asort($kodeorgArr);

$varCek=count($kodeorgArr);
//if($varCek<1)
//{
    //exit("Error:Data Kosong");
//}
$brdr=0;
$bgcoloraja='';
$cols=count($dataAfd)*3;
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tabA="
    <table>
    <tr><td colspan=5 align=left><b>RINGKASAN EVALUASI MONITORING ROTASI PANEN</b></td><td colspan=6 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
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
		<td ".$bgcoloraja." rowspan=3>AFD</td>
		<td ".$bgcoloraja." rowspan=3>Luas Areal(Ha)</td>
		<td ".$bgcoloraja." colspan=8>Monitoring Rotasi Panen (Ha)</td>
		<td ".$bgcoloraja." rowspan=3>Keterangan</td>
	</tr>
	<tr>
		<td ".$bgcoloraja." colspan=2>≥4X</td>
		<td ".$bgcoloraja." colspan=2>3X</td>
		<td ".$bgcoloraja." colspan=2>2X</td>
		<td ".$bgcoloraja." colspan=2>≤1X</td>
	</tr>
	<tr>
		<td ".$bgcoloraja." >Ha</td>
		<td ".$bgcoloraja." >%</td>
		<td ".$bgcoloraja." >Ha</td>
		<td ".$bgcoloraja." >%</td>
		<td ".$bgcoloraja." >Ha</td>
		<td ".$bgcoloraja." >%</td>
		<td ".$bgcoloraja." >Ha</td>
		<td ".$bgcoloraja." >%</td>
	</tr>
	
	";
     
    $tabA.="</thead>
	<tbody>";
    
	
	
	
	
	
        foreach($kodeorgArr as $lsBlok)
        {
			$Tes[substr($lsBlok,0,6)]['afd']= substr($lsBlok,0,6);
			@$Tes[substr($lsBlok,0,6)]['luas']+= $llArr[$lsBlok];
			if($dzRot[$lsBlok]['bi'] >= 4){
				@$Tes[substr($lsBlok,0,6)]['4HA']+= $llArr[$lsBlok];
			}else if($dzRot[$lsBlok]['bi'] == 3){
				@$Tes[substr($lsBlok,0,6)]['3HA']+= $llArr[$lsBlok];
			}else if($dzRot[$lsBlok]['bi'] == 2){
				@$Tes[substr($lsBlok,0,6)]['2HA']+= $llArr[$lsBlok];
			}else {
				@$Tes[substr($lsBlok,0,6)]['1HA']+= $llArr[$lsBlok];
			}
		}
		
			foreach($Tes as $lsBlok){
				
				$tabA.="<tr class=rowcontent>";
				$tabA.="<td align=right>".$lsBlok['afd']."</td>";
				$tabA.="<td align=right>".number_format($lsBlok['luas'],2)."</td>";
				@$A4A=($lsBlok['4HA']/$lsBlok['luas'])*100;
				@$A3A=($lsBlok['3HA']/$lsBlok['luas'])*100;
				@$A2A=($lsBlok['2HA']/$lsBlok['luas'])*100;
				@$A1A=($lsBlok['1HA']/$lsBlok['luas'])*100;
				$tabA.="<td align=right>".number_format($lsBlok['4HA'],2)."</td>";
				$tabA.="<td align=right>".number_format($A4A,2)."</td>";
				$tabA.="<td align=right>".number_format($lsBlok['3HA'],2)."</td>";
				$tabA.="<td align=right>".number_format($A3A,2)."</td>";
				$tabA.="<td align=right>".number_format($lsBlok['2HA'],2)."</td>";
				$tabA.="<td align=right>".number_format($A2A,2)."</td>";
				$tabA.="<td align=right>".number_format($lsBlok['1HA'],2)."</td>";
				$tabA.="<td align=right>".number_format($A1A,2)."</td>";
				$tabA.="<td align=right></td></tr>";
				$tot4A+=$lsBlok['4HA'];
				$tot3A+=$lsBlok['3HA'];
				$tot2A+=$lsBlok['2HA'];
				$tot1A+=$lsBlok['1HA'];
				$totRata+=$lsBlok['luas'];
				/*
				$tabA.="<tr class=rowcontent>";
				$tabA.="<td>".$lsBlok."</td>";
				$tabA.="<td align=right>".$llArr[$lsBlok]."</td>";
				$tabA.="<td align=right>".$ttArr[$lsBlok]."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['bi'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
				@$rtBln[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
				$tabA.="<td align=right>".number_format($rtBln[$lsBlok],2)."</td></tr>";
				$totBlnLaluA+=$dzRot[$lsBlok]['bl'];
				$totBudgetBiA+=$dzRot[$lsBlok]['bib'];
				$totBudgetSbiA+=$dzRot[$lsBlok]['sdb'];
				$totRealBiA+=$dzRot[$lsBlok]['bi'];
				$totRealSbiA+=$dzRot[$lsBlok]['sd'];
				$totRataA+=$rtBln[$lsBlok];
				$totLuasA+=$llArr[$lsBlok];
				*/
			
			/*
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$lsBlok."</td>";
            $tab.="<td align=right>".$ttArr[$lsBlok]."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['bi'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
            @$rtBln[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
            $tab.="<td align=right>".number_format($rtBln[$lsBlok],2)."</td></tr>";
            $totBlnLalu+=$dzRot[$lsBlok]['bl'];
            $totBudgetBi+=$dzRot[$lsBlok]['bib'];
            $totBudgetSbi+=$dzRot[$lsBlok]['sdb'];
            $totRealBi+=$dzRot[$lsBlok]['bi'];
            $totRealSbi+=$dzRot[$lsBlok]['sd'];
            $totRata+=$rtBln[$lsBlok];
			*/
        }
				@$A4B=($tot4A/$totRata)*100;
				@$A3B=($tot3A/$totRata)*100;
				@$A2B=($tot2A/$totRata)*100;
				@$A1B=($tot1A/$totRata)*100;
				
        $tabA.="<tr class=rowcontent>";
        $tabA.="<td >".$_SESSION['lang']['total']."</td>";
        $tabA.="<td  align=right>".number_format($totRata,2)."</td>";
		$tabA.="<td  align=right>".number_format($tot4A,2)."</td>";
		$tabA.="<td  align=right>".number_format($A4B,2)."</td>";
		$tabA.="<td  align=right>".number_format($tot3A,2)."</td>";
		$tabA.="<td  align=right>".number_format($A3B,2)."</td>";
		$tabA.="<td  align=right>".number_format($tot2A,2)."</td>";
		$tabA.="<td  align=right>".number_format($A2B,2)."</td>";
		$tabA.="<td  align=right>".number_format($tot1A,2)."</td>";
		$tabA.="<td  align=right>".number_format($A1B,2)."</td>";
        $tabA.="<td ></td></tr>";
        $tabA.="</tbody></table>";
		
	
		
		
       
switch($proses)
{
	case'preview':
	echo $tabA;
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$data=$tabA;
		$tglSkrg=date("Ymd");
		$nop_="ringkasan_rotasi_panen_".$tglSkrg;
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