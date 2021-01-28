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

// cari tanggal buat mencari apakah tanggal terakhir sebelum bulan lalu panen?
$tanggalakhir = strtotime('-2 month',strtotime($periode.'-01'));
$tanggalakhir = date('Y-m-t',$tanggalakhir);
//-----------------------------------------------------------------
$panen="SELECT kodeorg,tahuntanam,luasareaproduktif FROM  ".$dbname.".`setup_blok` where kodeorg like '".$kdUnit."%' and statusblok='TM' order by kodeorg asc,tahuntanam desc";
if($afdId!='')
{
	$panen="SELECT kodeorg,tahuntanam,luasareaproduktif FROM  ".$dbname.".`setup_blok` where kodeorg like '".$afdId."%' and statusblok='TM' order by kodeorg asc,tahuntanam desc";
}
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

//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periodelalu."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periodelalu."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";

	
if($afdId!='')
{
	//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where DATE_FORMAT(tanggal, '%Y-%m') ='".$periodelalu."' and kodeorg like '".$afdId."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
	$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where DATE_FORMAT(tanggal, '%Y-%m') ='".$periodelalu."' and kodeorg like '".$afdId."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
}

$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
	$tanggallaluArr[$res['kodeorg']]=$res['jum'];
}   

// data panen bulan ini
//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periode."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periode."' and kodeorg like '".$kdUnit."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
	
if($afdId!='')
{
   //$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periode."' and kodeorg like '".$afdId."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
   $panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where  DATE_FORMAT(tanggal, '%Y-%m') ='".$periode."' and kodeorg like '".$afdId."%' group by kodeorg,DATE_FORMAT(tanggal, '%Y-%m') ;";
}

 //$tabA=$panen;

$queryA=mysql_query($panen) or die(mysql_error($conn));
while($resA=mysql_fetch_assoc($queryA))
{
	$tanggalArr[$resA['kodeorg']]=$resA['jum'];
}   

// data panen sd bulan ini
//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$kdUnit."%' group by kodeorg ;";
$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$kdUnit."%' group by kodeorg ;";
if($afdId!='')
{
	//$panen="select kodeorg,count(kodeorg) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%' group by kodeorg ;";
	$panen="select kodeorg,sum(rotasiawal) as jum from ".$dbname.".kebun_interval_panen_vw where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%' group by kodeorg ;";
}

//$tabA=$panen;
$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
   $tanggalsdArr[$res['kodeorg']]=$res['jum'];
}   


$dzRot=array();
// susun rotasi
if(!empty($kodeorgArr))foreach($kodeorgArr as $koko){
    // bulan lalu
	
	$dzRot[$koko]['bl']=$tanggallaluArr[$koko];
    // bulan ini


	$dzRot[$koko]['bin']=$tanggalArr[$koko];
	/*
    // sd bulan ini
*/
	 $dzRot[$koko]['sd']=$tanggalsdArr[$koko];
}




$sRotasiBudget="select distinct rotasi,kodeorg from ".$dbname.".bgt_budget 
                where tahunbudget='".$tahun."' and kodeorg like '".$kdUnit."%' and kegiatan=611010101";
if($afdId!='')
{
    $sRotasiBudget="select distinct rotasi,kodeorg from ".$dbname.".bgt_budget 
                where tahunbudget='".$tahun."' and kodeorg like '".$afdId."%' and kegiatan=611010101";
}
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
    <tr><td colspan=5 align=left><b>ROTASI PANEN</b></td><td colspan=4 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
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
        <td ".$bgcoloraja." colspan=9>ROTASI PANEN ( ≥ 4 KALI)</td></tr>";
        $tabA.="<tr>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['blok']."</td>
		<td ".$bgcoloraja." rowspan=2>luas Blok</td>
        <td ".$bgcoloraja." rowspan=2>Tahun Tanam</td><td ".$bgcoloraja." rowspan=2>BULAN LALU</td>";
        $tabA.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['budget']."</td><td ".$bgcoloraja." colspan=2>REALISASI</td>";
        $tabA.="<td  ".$bgcoloraja." rowspan=2>ROTASI RATA-RATA PER BULAN</td></tr>";
       
        $tabA.="<tr><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td></tr>";
        $tabA.="</thead>
	<tbody>";
	
	$tabB="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." colspan=9>ROTASI PANEN (3 KALI)</td></tr>";
        $tabB.="<tr>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['blok']."</td>
		<td ".$bgcoloraja." rowspan=2>luas Blok</td>
        <td ".$bgcoloraja." rowspan=2>Tahun Tanam</td><td ".$bgcoloraja." rowspan=2>BULAN LALU</td>";
        $tabB.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['budget']."</td><td ".$bgcoloraja." colspan=2>REALISASI</td>";
        $tabB.="<td  ".$bgcoloraja." rowspan=2>ROTASI RATA-RATA PER BULAN</td></tr>";
       
        $tabB.="<tr><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td></tr>";
        $tabB.="</thead>
	<tbody>";
	
	$tabC="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." colspan=9>ROTASI PANEN (2 KALI)</td></tr>";
        $tabC.="<tr>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['blok']."</td>
		<td ".$bgcoloraja." rowspan=2>luas Blok</td>
        <td ".$bgcoloraja." rowspan=2>Tahun Tanam</td><td ".$bgcoloraja." rowspan=2>BULAN LALU</td>";
        $tabC.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['budget']."</td><td ".$bgcoloraja." colspan=2>REALISASI</td>";
        $tabC.="<td  ".$bgcoloraja." rowspan=2>ROTASI RATA-RATA PER BULAN</td></tr>";
       
        $tabC.="<tr><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td></tr>";
        $tabC.="</thead>
	<tbody>";
	
	
	$tabD="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." colspan=9>ROTASI PANEN (≤ 1 KALI)</td></tr>";
        $tabD.="<tr>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['blok']."</td>
		<td ".$bgcoloraja." rowspan=2>luas Blok</td>
        <td ".$bgcoloraja." rowspan=2>Tahun Tanam</td><td ".$bgcoloraja." rowspan=2>BULAN LALU</td>";
        $tabD.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['budget']."</td><td ".$bgcoloraja." colspan=2>REALISASI</td>";
        $tabD.="<td  ".$bgcoloraja." rowspan=2>ROTASI RATA-RATA PER BULAN</td></tr>";
       
        $tabD.="<tr><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td><td ".$bgcoloraja.">B.I</td><td ".$bgcoloraja.">s/d BI</td></tr>";
        $tabD.="</thead>
	<tbody>";
	
	
	
	
        foreach($kodeorgArr as $lsBlok)
        {
			if ($dzRot[$lsBlok]['bin'] >= 4){
				$tabA.="<tr class=rowcontent>";
				$tabA.="<td>".$lsBlok."</td>";
				$tabA.="<td align=right>".$llArr[$lsBlok]."</td>";
				$tabA.="<td align=right>".$ttArr[$lsBlok]."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['bin'],2)."</td>";
				$tabA.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
				@$rtBln[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
				$tabA.="<td align=right>".number_format($rtBln[$lsBlok],2)."</td></tr>";
				$totBlnLaluA+=$dzRot[$lsBlok]['bl'];
				$totBudgetBiA+=$dzRot[$lsBlok]['bib'];
				$totBudgetSbiA+=$dzRot[$lsBlok]['sdb'];
				$totRealBiA+=$dzRot[$lsBlok]['bin'];
				$totRealSbiA+=$dzRot[$lsBlok]['sd'];
				$totRataA+=$rtBln[$lsBlok];
				$totLuasA+=$llArr[$lsBlok];
			
			}else if ($dzRot[$lsBlok]['bin'] == 3){
				$tabB.="<tr class=rowcontent>";
				$tabB.="<td>".$lsBlok."</td>";
				$tabB.="<td align=right>".$llArr[$lsBlok]."</td>";
				$tabB.="<td align=right>".$ttArr[$lsBlok]."</td>";
				$tabB.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
				$tabB.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
				$tabB.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
				$tabB.="<td align=right>".number_format($dzRot[$lsBlok]['bin'],2)."</td>";
				$tabB.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
				@$rtBlnB[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
				$tabB.="<td align=right>".number_format($rtBlnB[$lsBlok],2)."</td></tr>";
				$totBlnLaluB+=$dzRot[$lsBlok]['bl'];
				$totBudgetBiB+=$dzRot[$lsBlok]['bib'];
				$totBudgetSbiB+=$dzRot[$lsBlok]['sdb'];
				$totRealBiB+=$dzRot[$lsBlok]['bin'];
				$totRealSbiB+=$dzRot[$lsBlok]['sd'];
				$totRataB+=$rtBlnB[$lsBlok];
				$totLuasB+=$llArr[$lsBlok];
			}else if ($dzRot[$lsBlok]['bin'] == 2){
				$tabC.="<tr class=rowcontent>";
				$tabC.="<td>".$lsBlok."</td>";
				$tabC.="<td align=right>".$llArr[$lsBlok]."</td>";
				$tabC.="<td align=right>".$ttArr[$lsBlok]."</td>";
				$tabC.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
				$tabC.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
				$tabC.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
				$tabC.="<td align=right>".number_format($dzRot[$lsBlok]['bin'],2)."</td>";
				$tabC.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
				@$rtBlnC[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
				$tabC.="<td align=right>".number_format($rtBlnC[$lsBlok],2)."</td></tr>";
				$totBlnLaluC+=$dzRot[$lsBlok]['bl'];
				$totBudgetBiC+=$dzRot[$lsBlok]['bib'];
				$totBudgetSbiC+=$dzRot[$lsBlok]['sdb'];
				$totRealBiC+=$dzRot[$lsBlok]['bin'];
				$totRealSbiC+=$dzRot[$lsBlok]['sd'];
				$totRataC+=$rtBlnC[$lsBlok];
				$totLuasC+=$llArr[$lsBlok];
			}else{
				$tabD.="<tr class=rowcontent>";
				$tabD.="<td>".$lsBlok."</td>";
				$tabD.="<td align=right>".$llArr[$lsBlok]."</td>";
				$tabD.="<td align=right>".$ttArr[$lsBlok]."</td>";
				$tabD.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
				$tabD.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
				$tabD.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
				$tabD.="<td align=right>".number_format($dzRot[$lsBlok]['bin'],2)."</td>";
				$tabD.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
				@$rtBlnD[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
				$tabD.="<td align=right>".number_format($rtBlnD[$lsBlok],2)."</td></tr>";
				$totBlnLaluD+=$dzRot[$lsBlok]['bl'];
				$totBudgetBiD+=$dzRot[$lsBlok]['bib'];
				$totBudgetSbiD+=$dzRot[$lsBlok]['sdb'];
				$totRealBiD+=$dzRot[$lsBlok]['bin'];
				$totRealSbiD+=$dzRot[$lsBlok]['sd'];
				$totRataD+=$rtBlnD[$lsBlok];
				$totLuasD+=$llArr[$lsBlok];
			};
		
			/*
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$lsBlok."</td>";
            $tab.="<td align=right>".$ttArr[$lsBlok]."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['bl'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['bib'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['sdb'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['bin'],2)."</td>";
            $tab.="<td align=right>".number_format($dzRot[$lsBlok]['sd'],2)."</td>";
            @$rtBln[$lsBlok]=$dzRot[$lsBlok]['sd']/(intval($bulan));
            $tab.="<td align=right>".number_format($rtBln[$lsBlok],2)."</td></tr>";
            $totBlnLalu+=$dzRot[$lsBlok]['bl'];
            $totBudgetBi+=$dzRot[$lsBlok]['bib'];
            $totBudgetSbi+=$dzRot[$lsBlok]['sdb'];
            $totRealBi+=$dzRot[$lsBlok]['bin'];
            $totRealSbi+=$dzRot[$lsBlok]['sd'];
            $totRata+=$rtBln[$lsBlok];
			*/
        }
        $tabA.="<tr class=rowcontent>";
        $tabA.="<td >".$_SESSION['lang']['total']."</td>";
        $tabA.="<td  align=right>".number_format($totLuasA,2)."</td>";
        $tabA.="<td colspan=7 ></td></tr>";
        $tabA.="</tbody></table>";
		
		$tabB.="<tr class=rowcontent>";
        $tabB.="<td >".$_SESSION['lang']['total']."</td>";
        $tabB.="<td  align=right>".number_format($totLuasB,2)."</td>";
        $tabB.="<td colspan=7 ></td></tr>";
        $tabB.="</tbody></table>";
		
		$tabC.="<tr class=rowcontent>";
        $tabC.="<td >".$_SESSION['lang']['total']."</td>";
        $tabC.="<td  align=right>".number_format($totLuasC,2)."</td>";
        $tabC.="<td colspan=7 ></td></tr>";
        $tabC.="</tbody></table>";
		
		$tabD.="<tr class=rowcontent>";
        $tabD.="<td >".$_SESSION['lang']['total']."</td>";
        $tabD.="<td  align=right>".number_format($totLuasD,2)."</td>";
        $tabD.="<td colspan=7 ></td></tr>";
        $tabD.="</tbody></table>";
		
		
       
switch($proses)
{
	case'preview':
	echo $tabA.$tabB.$tabC.$tabD;
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$data=$tabA.$tabB.$tabC.$tabD;
		$tglSkrg=date("Ymd");
		$nop_="rotasipotongbuah_".$tglSkrg;
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