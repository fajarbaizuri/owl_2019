<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];


//echo $periode.___.$tahun.___.$bulan.____;

$awalper=$tahun.'-01';//echo $awalper;


$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($unit==''||$periode=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];

// building array: dzArr (main data) =========================================================================
// as seen on sdm_slave_2prasarana.php
$dzArr=array();

// tbs diolah bulan ini
$aresta="SELECT sum(tbsdiolah) as tbs FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbs=$res['tbs'];
}   

// tbs diolah bulan ini budget
$aresta="SELECT sum(olah".$bulan.") as tbsbudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbsbudget=$res['tbsbudget'];
}

$tbsselisih=$tbsbudget-$tbs;

// tbs diolah sd bulan ini
$aresta="SELECT sum(tbsdiolah) as tbs FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbssd=$res['tbs'];
}

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="olah0".$W;
    else $jack="olah".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

// tbs diolah sd bulan ini budget
$aresta="SELECT sum(".$addstr.") as tbsbudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tbsbudgetsd=$res['tbsbudget'];
}

$tbsselisihsd=$tbsbudgetsd-$tbssd;

#indra
// cpo dihasilkan bulan ini tambahan kernel jg
$aresta="SELECT sum(oer) as cpo,sum(oerpk) as oerpk FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cpo=$res['cpo'];
	$oerpk=$res['oerpk'];
	#ind
	
}   

// cpo dihasilkan bulan ini budget
$aresta="SELECT sum(kgcpo".$bulan.") as cpobudget,sum(kgker".$bulan.") as kernelbudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cpobudget=$res['cpobudget'];
	$kernelbudget=$res['kernelbudget'];
}

$cposelisih=$cpobudget-$cpo;
$kernelselisih=$kernelbudget-$oerpk;

// cpo dihasilkan sd bulan ini
$aresta="SELECT sum(oer) as cpo,sum(oerpk) as oerpk FROM ".$dbname.".pabrik_produksi
    WHERE kodeorg like '".$unit."%' and tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cposd=$res['cpo'];
	$kernelsd=$res['oerpk'];
}

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="kgcpo0".$W;
    else $jack="kgcpo".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

$addstr2="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack2="kgker0".$W;
    else $jack2="kgker".$W;
    if($W<intval($bulan))$addstr2.=$jack2."+";
    else $addstr2.=$jack2;
}
$addstr2.=")";

// cpo dihasilkan sd bulan ini budget
$aresta="SELECT sum(".$addstr.") as cpobudget,sum(".$addstr2.") as kernelbudget FROM ".$dbname.".bgt_produksi_pks_vw
    WHERE millcode like '".$unit."%' and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $cpobudgetsd=$res['cpobudget'];
	$kernelbudgetsd=$res['kernelbudget'];
}

$cposelisihsd=$cpobudgetsd-$cposd;
$kernelselisihsd=$kernelbudgetsd-$kernelsd;

// biaya bulan ini
$aresta="SELECT noakun,sum(jumlah) as biaya FROM ".$dbname.".keu_jurnaldt_vw
    WHERE kodeorg like '".$unit."%' and tanggal like '".$periode."%' and 
        (noakun like '7%' or noakun like '63%'  or noakun like '64101%')
    GROUP BY noakun";
	
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if(substr($res['noakun'],0,1)=='7')
	{
		$akun7[$res['noakun']]=$res['noakun'];
	}
    if(substr($res['noakun'],0,2)=='63')$akun63[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='64101')$akun64[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63101')$akun631[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63102')$akun632[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,1)=='8')$akun8[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['biaya']=$res['biaya'];
}   

//

// budget bulan ini
$aresta="SELECT noakun,sum(rp".$bulan.") as budget FROM ".$dbname.".bgt_budget_detail
    WHERE kodeorg like '".$unit."%' and tahunbudget = '".$tahun."' and 
        (noakun like '7%' or noakun like '63%'  or noakun like '64101%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if(substr($res['noakun'],0,1)=='7')$akun7[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,2)=='63')$akun63[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='64101')$akun64[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63101')$akun631[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63102')$akun632[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,1)=='8')$akun8[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['budget']=$res['budget'];
}   

// biaya sd bulan ini
$aresta="SELECT noakun,sum(jumlah) as biaya FROM ".$dbname.".keu_jurnaldt_vw
    WHERE kodeorg like '".$unit."%' and tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15') and 
         (noakun like '7%' or noakun like '63%'  or noakun like '64101%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if(substr($res['noakun'],0,1)=='7')$akun7[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,2)=='63')$akun63[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='64101')$akun64[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63101')$akun631[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63102')$akun632[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,1)=='8')$akun8[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['biayasd']=$res['biaya'];
}   

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="rp0".$W;
    else $jack="rp".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

// budget sd bulan ini
$aresta="SELECT noakun,sum(".$addstr.") as budget FROM ".$dbname.".bgt_budget_detail
    WHERE kodeorg like '".$unit."%' and tahunbudget = '".$tahun."' and 
        (noakun like '7%' or noakun like '63%'  or noakun like '64101%')
    GROUP BY noakun";
//echo $aresta;
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if(substr($res['noakun'],0,1)=='7')$akun7[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,2)=='63')$akun63[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='64101')$akun64[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63101')$akun631[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,5)=='63102')$akun632[$res['noakun']]=$res['noakun'];
    if(substr($res['noakun'],0,1)=='8')$akun8[$res['noakun']]=$res['noakun'];
    $dzArr[$res['noakun']]['budgetsd']=$res['budget'];
}   



	
	
	
//echo $hppCpo.__.$hppKernel;exit();


//echo "<pre>";
//print_r($akun7);
//echo "</pre>";

//echo $tbsolah.'<br>';
//echo $tbsolahbudget.'<br>';
//exit;

// kamus akun
$aresta="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
    WHERE length(noakun)=7 and 
        (noakun like '7%' or noakun like '63%'  or noakun like '64%')
    ORDER BY noakun";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kamusakun[$res['noakun']]['no']=$res['noakun'];
    $kamusakun[$res['noakun']]['nama']=$res['namaakun'];
}   

// jumlah dan total biaya administrasi 7
if(!empty($akun7))foreach($akun7 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total7['biaya']+=$dzArr[$akyun]['biaya'];
    $total7['budget']+=$dzArr[$akyun]['budget'];
    $total7['selisih']+=$dzArr[$akyun]['selisih'];
    $total7['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total7['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total7['selisihsd']+=$dzArr[$akyun]['selisihsd'];
    $subtotal['biaya']+=$dzArr[$akyun]['biaya'];
    $subtotal['budget']+=$dzArr[$akyun]['budget'];
    $subtotal['selisih']+=$dzArr[$akyun]['selisih'];
    $subtotal['biayasd']+=$dzArr[$akyun]['biayasd'];
    $subtotal['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $subtotal['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}

// jumlah dan total biaya manufacture 63
if(!empty($akun63))foreach($akun63 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total6364['biaya']+=$dzArr[$akyun]['biaya'];
    $total6364['budget']+=$dzArr[$akyun]['budget'];
    $total6364['selisih']+=$dzArr[$akyun]['selisih'];
    $total6364['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total6364['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total6364['selisihsd']+=$dzArr[$akyun]['selisihsd'];
    $subtotal['biaya']+=$dzArr[$akyun]['biaya'];
    $subtotal['budget']+=$dzArr[$akyun]['budget'];
    $subtotal['selisih']+=$dzArr[$akyun]['selisih'];
    $subtotal['biayasd']+=$dzArr[$akyun]['biayasd'];
    $subtotal['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $subtotal['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}

// jumlah dan total biaya manufacture 64
if(!empty($akun64))foreach($akun64 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total6364['biaya']+=$dzArr[$akyun]['biaya'];
    $total6364['budget']+=$dzArr[$akyun]['budget'];
    $total6364['selisih']+=$dzArr[$akyun]['selisih'];
    $total6364['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total6364['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total6364['selisihsd']+=$dzArr[$akyun]['selisihsd'];
    $subtotal['biaya']+=$dzArr[$akyun]['biaya'];
    $subtotal['budget']+=$dzArr[$akyun]['budget'];
    $subtotal['selisih']+=$dzArr[$akyun]['selisih'];
    $subtotal['biayasd']+=$dzArr[$akyun]['biayasd'];
    $subtotal['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $subtotal['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}

// jumlah dan total biaya processing 631
if(!empty($akun631))foreach($akun631 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total631['biaya']+=$dzArr[$akyun]['biaya'];
    $total631['budget']+=$dzArr[$akyun]['budget'];
    $total631['selisih']+=$dzArr[$akyun]['selisih'];
    $total631['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total631['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total631['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}

// jumlah dan total biaya maintenance 632
if(!empty($akun632))foreach($akun632 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total632['biaya']+=$dzArr[$akyun]['biaya'];
    $total632['budget']+=$dzArr[$akyun]['budget'];
    $total632['selisih']+=$dzArr[$akyun]['selisih'];
    $total632['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total632['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total632['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}


//$tab.= "<td align=right>".number_format($total64['biaya'])."</td>";

// jumlah dan total biaya bahan baku 64
if(!empty($akun64))foreach($akun64 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total64['biaya']+=$dzArr[$akyun]['biaya'];
    $total64['budget']+=$dzArr[$akyun]['budget'];
    $total64['selisih']+=$dzArr[$akyun]['selisih'];
    $total64['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total64['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total64['selisihsd']+=$dzArr[$akyun]['selisihsd'];
	
}

//echo $total64['biaya'];

///echo "<pre>";
//print_r($akun7);
//echo "</pre>";**/

/*echo "<pre>";
print_r($akun64);
echo "</pre>";
*/
    $total['biaya']=$subtotal['biaya'];
    $total['budget']=$subtotal['budget'];
    $total['selisih']=$subtotal['selisih'];
    $total['biayasd']=$subtotal['biayasd'];
    $total['budgetsd']=$subtotal['budgetsd'];
    $total['selisihsd']=$subtotal['selisihsd'];
	

// jumlah dan total biaya administrasi 8
if(!empty($akun8))foreach($akun8 as $akyun){
    $dzArr[$akyun]['selisih']=$dzArr[$akyun]['budget']-$dzArr[$akyun]['biaya'];
    $dzArr[$akyun]['selisihsd']=$dzArr[$akyun]['budgetsd']-$dzArr[$akyun]['biayasd'];
    $total8['biaya']+=$dzArr[$akyun]['biaya'];
    $total8['budget']+=$dzArr[$akyun]['budget'];
    $total8['selisih']+=$dzArr[$akyun]['selisih'];
    $total8['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total8['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total8['selisihsd']+=$dzArr[$akyun]['selisihsd'];
    $total['biaya']+=$dzArr[$akyun]['biaya'];
    $total['budget']+=$dzArr[$akyun]['budget'];
    $total['selisih']+=$dzArr[$akyun]['selisih'];
    $total['biayasd']+=$dzArr[$akyun]['biayasd'];
    $total['budgetsd']+=$dzArr[$akyun]['budgetsd'];
    $total['selisihsd']+=$dzArr[$akyun]['selisihsd'];
}

@$costpertbs['biaya']=$total['biaya']/$tbs;
@$costpertbs['budget']=$total['budget']/$tbsbudget;
@$costpertbs['selisih']=$costpertbs['budget']-$costpertbs['biaya'];
@$costpertbs['biayasd']=$total['biayasd']/$tbssd;
@$costpertbs['budgetsd']=$total['budgetsd']/$tbsbudgetsd;
@$costpertbs['selisihsd']=$costpertbs['budgetsd']-$costpertbs['biayasd'];



@$costpercpo['biaya']=$total['biaya']/$cpo;
@$costpercpo['budget']=$total['budget']/$cpobudget;
@$costpercpo['selisih']=$costpercpo['budget']-$costpercpo['biaya'];
@$costpercpo['biayasd']=$total['biayasd']/$cposd;
@$costpercpo['budgetsd']=$total['budgetsd']/$cpobudgetsd;
@$costpercpo['selisihsd']=$costpercpo['budgetsd']-$costpercpo['biayasd'];


##inddd
@$costperkernel['biaya']=$total['biaya']/$kernel;
@$costperkernel['budget']=$total['budget']/$kernelbudget;
@$costperkernel['selisih']=$costperkernel['budget']-$costperkernel['biaya'];
@$costperkernel['biayasd']=$total['biayasd']/$kernelsd;
@$costperkernel['budgetsd']=$total['budgetsd']/$kernelbudgetsd;
@$costperkernel['selisihsd']=$costperkernel['budgetsd']-$costperkernel['biayasd'];


 
@$admpertbs['biaya']=$total7['biaya']/$tbs;
@$admpertbs['budget']=$total7['budget']/$tbsbudget;
@$admpertbs['selisih']=$admpertbs['budget']-$admpertbs['biaya'];
@$admpertbs['biayasd']=$total7['biayasd']/$tbssd;
@$admpertbs['budgetsd']=$total7['budgetsd']/$tbsbudgetsd;
@$admpertbs['selisihsd']=$admpertbs['budgetsd']-$admpertbs['biayasd'];

@$procpertbs['biaya']=$total631['biaya']/$tbs;
@$procpertbs['budget']=$total631['budget']/$tbsbudget;
@$procpertbs['selisih']=$procpertbs['budget']-$procpertbs['biaya'];
@$procpertbs['biayasd']=$total631['biayasd']/$tbssd;
@$procpertbs['budgetsd']=$total631['budgetsd']/$tbsbudgetsd;
@$procpertbs['selisihsd']=$procpertbs['budgetsd']-$procpertbs['biayasd'];

@$mainpertbs['biaya']=$total632['biaya']/$tbs;
@$mainpertbs['budget']=$total632['budget']/$tbsbudget;
@$mainpertbs['selisih']=$mainpertbs['budget']-$mainpertbs['biaya'];
@$mainpertbs['biayasd']=$total632['biayasd']/$tbssd;
@$mainpertbs['budgetsd']=$total632['budgetsd']/$tbsbudgetsd;
@$mainpertbs['selisihsd']=$mainpertbs['budgetsd']-$mainpertbs['biayasd'];

@$salespertbs['biaya']=$total8['biaya']/$tbs;
@$salespertbs['budget']=$total8['budget']/$tbsbudget;
@$salespertbs['selisih']=$salespertbs['budget']-$salespertbs['biaya'];
@$salespertbs['biayasd']=$total8['biayasd']/$tbssd;
@$salespertbs['budgetsd']=$total8['budgetsd']/$tbsbudgetsd;
@$salespertbs['selisihsd']=$salespertbs['budgetsd']-$salespertbs['biayasd'];


@$tbiayaolahpertbs['biaya']=$mainpertbs['biaya']+$procpertbs['biaya']+$admpertbs['biaya'];
@$tbiayaolahpertbs['budget']=$mainpertbs['budget']+$procpertbs['budget']+$admpertbs['budget'];
@$tbiayaolahpertbs['selisih']=$mainpertbs['selisih']+$procpertbs['selisih']+$admpertbs['selisih'];
@$tbiayaolahpertbs['biayasd']=$mainpertbs['biayasd']+$procpertbs['biayasd']+$admpertbs['biayasd'];
@$tbiayaolahpertbs['budgetsd']=$mainpertbs['budgetsd']+$procpertbs['budgetsd']+$admpertbs['budgetsd'];
@$tbiayaolahpertbs['selisihsd']=$mainpertbs['selisihsd']+$procpertbs['selisihsd']+$admpertbs['selisihsd'];

@$biayabhnbaku['biaya']=$total64['biaya']/$tbs;
@$biayabhnbaku['budget']=$total64['budget']/$tbsbudget;
@$biayabhnbaku['selisih']=$total64['selisih']/$tbsselisih;
@$biayabhnbaku['biayasd']=$total64['biayasd']/$tbssd;
@$biayabhnbaku['budgetsd']=$total64['budgetsd']/$tbsbudgetsd;
@$biayabhnbaku['selisihsd']=$total64['selisihsd']/$tbsselisihsd;






#tarik HPPnya bulan ini
#cpo
$ahppCpo="select jumlah from ".$dbname.".pabrik_porsihpp where kodeproduk='CPO' and periode like '".$periode."%' ";

$bhppCpo=mysql_query($ahppCpo) or die (mysql_error($conn));
$chppCpo=mysql_fetch_assoc($bhppCpo);
	$hppCpo=$chppCpo['jumlah'];
	
#kernel
$ahppKernel="select jumlah from ".$dbname.".pabrik_porsihpp where kodeproduk='PK' and periode like '".$periode."%' ";

$bhppKernel=mysql_query($ahppKernel) or die (mysql_error($conn));
$chppKernel=mysql_fetch_assoc($bhppKernel);
	$hppKernel=$chppKernel['jumlah'];

#UBAH DI SINI
#tarik HPPnya sd bulan ini
#cpo
$ahppCposd="select avg(jumlah) as jumlah from ".$dbname.".pabrik_porsihpp where kodeproduk='CPO' and periode between '".$awalper."' and '".$periode."' ";
//echo $ahppCposd;
$bhppCposd=mysql_query($ahppCposd) or die (mysql_error($conn));
$chppCposd=mysql_fetch_assoc($bhppCposd);
	$hppCposd=$chppCposd['jumlah'];
	
//echo $hppCposd;exit();
#kernel
$ahppKernelsd="select avg(jumlah) as jumlah from ".$dbname.".pabrik_porsihpp where kodeproduk='PK' and periode between '".$awalper."' and '".$periode."' ";

$bhppKernelsd=mysql_query($ahppKernelsd) or die (mysql_error($conn));
$chppKernelsd=mysql_fetch_assoc($bhppKernelsd);
	$hppKernelsd=$chppKernelsd['jumlah'];


// urut akun
if(!empty($akun))asort($akun);

if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=4 align=left><font size=3>".$judul."</font></td>
        <td colspan=3 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=14 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>   
</table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">Uraian</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    <tr>
    <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['selisih']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['selisih']."</td>
    </tr>
    </thead>
    <tbody>
";
        
    $dummy='';
    $no=1;
// excel array content =========================================================================
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>".$_SESSION['lang']['tbsdiolah']." (Kg)</td>"; 
    $tab.= "<td align=right>".number_format($tbs,2)."</td>"; 
    $tab.= "<td align=right>".number_format($tbsbudget,2)."</td>"; 
    $tab.= "<td align=right>".number_format($tbsselisih,2)."</td>"; 
    $tab.= "<td align=right>".number_format($tbssd,2)."</td>"; 
    $tab.= "<td align=right>".number_format($tbsbudgetsd,2)."</td>"; 
    $tab.= "<td align=right>".number_format($tbsselisihsd,2)."</td>"; 
    $tab.= "</tr>";
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>".$_SESSION['lang']['cpokuantitas']." (Kg)</td>"; 
    $tab.= "<td align=right>".number_format($cpo,2)."</td>"; 
    $tab.= "<td align=right>".number_format($cpobudget,2)."</td>"; 
    $tab.= "<td align=right>".number_format($cposelisih,2)."</td>"; 
    $tab.= "<td align=right>".number_format($cposd,2)."</td>"; 
    $tab.= "<td align=right>".number_format($cpobudgetsd,2)."</td>"; 
    $tab.= "<td align=right>".number_format($cposelisihsd,2)."</td>"; 
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Kernel (Kg)</td>"; 
    $tab.= "<td align=right>".number_format($oerpk,2)."</td>"; 
    $tab.= "<td align=right>".number_format($kernelbudget,2)."</td>"; //d
    $tab.= "<td align=right>".number_format($kernelselisih,2)."</td>"; 
    $tab.= "<td align=right>".number_format($kernelsd,2)."</td>"; 
	$tab.= "<td align=right>".number_format($kernelbudgetsd,2)."</td>"; 
	$tab.= "<td align=right>".number_format($kernelselisihsd,2)."</td>";	
	#ind
	
    $tab.= "</tr><tr><td colspan=7>&nbsp;</td></tr>";
	
	
	$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Biaya Bahan Baku</td>";
    $tab.= "<td align=right>".number_format($total64['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total64['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total64['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total64['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total64['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total64['selisihsd'])."</td>";
    $tab.= "</tr>";
	
	$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Biaya Produksi</td>";
/*    $tab.= "<td align=right>".number_format($total6364['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total6364['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total6364['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total6364['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total6364['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total6364['selisihsd'])."</td>";*/
	$tab.= "<td align=right>".number_format($total631['biaya']+$total632['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total631['budget']+$total632['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total631['selisih']+$total632['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total631['biayasd']+$total632['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total631['budgetsd']+$total632['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total631['selisihsd']+$total632['selisihsd'])."</td>";	
    $tab.= "</tr>";
	
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>- Pengolahan</td>";
    $tab.= "<td align=right>".number_format($total631['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total631['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total631['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total631['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total631['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total631['selisihsd'])."</td>";
    $tab.= "</tr>";
	
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>- Perawatan</td>";
    $tab.= "<td align=right>".number_format($total632['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total632['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total632['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total632['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total632['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total632['selisihsd'])."</td>";
    $tab.= "</tr>";
	
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Biaya Administrasi</td>";
    $tab.= "<td align=right>".number_format($total7['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total7['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total7['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total7['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total7['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total7['selisihsd'])."</td>";
    $tab.= "</tr>";
	
    
	
    
	
    //$tab.= "<tr class=title>";
    $tab.= "<tr class=rowcontent>";
	$tab.= "<td align=left>Harga Pokok Produksi</td>";
    $tab.= "<td align=right>".number_format($subtotal['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($subtotal['budget'])."</td>";
    $tab.= "<td align=right>".number_format($subtotal['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($subtotal['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($subtotal['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($subtotal['selisihsd'])."</td>";
    $tab.= "</tr>";
	
	$tab.= "</tr><tr><td colspan=7>&nbsp;</td></tr>";
	
	#indra hpp #indzzz
    $tab.= "<tr class=rowcontent>";
	$tab.= "<td align=left>Harga Pokok CPO</td>";//hppCposd
		$x1=$hppCpo/100*$subtotal['biaya'];
		$x2=$hppCpo/100*$subtotal['budget'];
		$x3=($hppCpo/100*$subtotal['budget'])-($hppCpo/100*$subtotal['biaya']);
		$x4=$hppCposd/100*$subtotal['biayasd'];
		$x5=$hppCposd/100*$subtotal['budgetsd'];
		
		$x6=($hppCposd/100*$subtotal['budgetsd'])-($hppCposd/100*$subtotal['biayasd']);
    $tab.= "<td align=right>".number_format($x1)."</td>";
    $tab.= "<td align=right>".number_format($x2)."</td>";
    $tab.= "<td align=right>".number_format($x3)."</td>";
    $tab.= "<td align=right>".number_format($x4)."</td>";//ini
    $tab.= "<td align=right>".number_format($x5)."</td>";
    $tab.= "<td align=right>".number_format($x6)."</td>";
    $tab.= "</tr>";	
	
	$tab.= "<tr class=rowcontent>";
	$tab.= "<td align=left>Harga Pokok Kernel</td>";
		$y1=$hppKernel/100*$subtotal['biaya'];
		$y2=$hppKernel/100*$subtotal['budget'];
		$y3=($hppKernel/100*$subtotal['budget'])-($hppKernel/100*$subtotal['biaya']);
		$y4=$hppKernelsd/100*$subtotal['biayasd'];
		$y5=$hppKernelsd/100*$subtotal['budgetsd'];
		$y6=($hppKernelsd/100*$subtotal['budgetsd'])-($hppKernelsd/100*$subtotal['biayasd']);
    $tab.= "<td align=right>".number_format($y1)."</td>";
    $tab.= "<td align=right>".number_format($y2)."</td>";
    $tab.= "<td align=right>".number_format($y3)."</td>";	
    $tab.= "<td align=right>".number_format($y4)."</td>";
    $tab.= "<td align=right>".number_format($y5)."</td>";
	$tab.= "<td align=right>".number_format($y6)."</td>";
    $tab.= "</tr>";
	
	$tab.= "</tr><tr><td colspan=7>&nbsp;</td></tr>";
/*    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Sales Expenses</td>";
    $tab.= "<td align=right>".number_format($total8['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total8['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total8['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total8['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total8['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total8['selisihsd'])."</td>";
    $tab.= "</tr>";*/
/*    $tab.= "<tr class=title>";
    $tab.= "<td align=left>Grand Total</td>";
    $tab.= "<td align=right>".number_format($total['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($total['budget'])."</td>";
    $tab.= "<td align=right>".number_format($total['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($total['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($total['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($total['selisihsd'])."</td>";
    $tab.= "</tr>";*/
	
	
	
	#ind
	
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Total Cost / Kg TBS</td>";
    $tab.= "<td align=right>".number_format($costpertbs['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($costpertbs['budget'])."</td>";
    $tab.= "<td align=right>".number_format($costpertbs['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($costpertbs['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($costpertbs['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($costpertbs['selisihsd'])."</td>";
    $tab.= "</tr>";
   
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Biaya Adm / Kg TBS</td>";
    $tab.= "<td align=right>".number_format($admpertbs['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($admpertbs['budget'])."</td>";
    $tab.= "<td align=right>".number_format($admpertbs['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($admpertbs['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($admpertbs['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($admpertbs['selisihsd'])."</td>";
    $tab.= "</tr>";
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Mill Proc / Kg TBS</td>";
    $tab.= "<td align=right>".number_format($procpertbs['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($procpertbs['budget'])."</td>";
    $tab.= "<td align=right>".number_format($procpertbs['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($procpertbs['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($procpertbs['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($procpertbs['selisihsd'])."</td>";
    $tab.= "</tr>";
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Mill Maint / Kg TBS</td>";
    $tab.= "<td align=right>".number_format($mainpertbs['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($mainpertbs['budget'])."</td>";
    $tab.= "<td align=right>".number_format($mainpertbs['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($mainpertbs['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($mainpertbs['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($mainpertbs['selisihsd'])."</td>";
    $tab.= "</tr>";
	
/*    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Sales Exp / Ton TBS</td>";
    $tab.= "<td align=right>".number_format($salespertbs['biaya']*1000)."</td>";
    $tab.= "<td align=right>".number_format($salespertbs['budget']*1000)."</td>";
    $tab.= "<td align=right>".number_format($salespertbs['selisih']*1000)."</td>";
    $tab.= "<td align=right>".number_format($salespertbs['biayasd']*1000)."</td>";
    $tab.= "<td align=right>".number_format($salespertbs['budgetsd']*1000)."</td>";
    $tab.= "<td align=right>".number_format($salespertbs['selisihsd']*1000)."</td>";
    $tab.= "</tr>";*/
	
	#ind tambahan pak idris
	
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Total Biaya Olah/Kg TBS</td>";
    $tab.= "<td align=right>".number_format($tbiayaolahpertbs['biaya'],2)."</td>";
    $tab.= "<td align=right>".number_format($tbiayaolahpertbs['budget'],2)."</td>";
    $tab.= "<td align=right>".number_format($tbiayaolahpertbs['selisih'],2)."</td>";
    $tab.= "<td align=right>".number_format($tbiayaolahpertbs['biayasd'],2)."</td>";
    $tab.= "<td align=right>".number_format($tbiayaolahpertbs['budgetsd'],2)."</td>";
    $tab.= "<td align=right>".number_format($tbiayaolahpertbs['selisihsd'],2)."</td>";
    $tab.= "</tr>";	
	
	$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Biaya Bahan Baku</td>";
    $tab.= "<td align=right>".number_format($biayabhnbaku['biaya'],2)."</td>";
    $tab.= "<td align=right>".number_format($biayabhnbaku['budget'],2)."</td>";
    $tab.= "<td align=right>".number_format($biayabhnbaku['selisih'],2)."</td>";
    $tab.= "<td align=right>".number_format($biayabhnbaku['biayasd'],2)."</td>";
    $tab.= "<td align=right>".number_format($biayabhnbaku['budgetsd'],2)."</td>";
    $tab.= "<td align=right>".number_format($biayabhnbaku['selisihsd'],2)."</td>";
    $tab.= "</tr>";	
	
	$tab.= "</tr><tr><td colspan=7>&nbsp;</td></tr>";
	
	$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Total Cost / Kg CPO</td>";
/*    $tab.= "<td align=right>".number_format($costpercpo['biaya'])."</td>";
    $tab.= "<td align=right>".number_format($costpercpo['budget'])."</td>";
    $tab.= "<td align=right>".number_format($costpercpo['selisih'])."</td>";
    $tab.= "<td align=right>".number_format($costpercpo['biayasd'])."</td>";
    $tab.= "<td align=right>".number_format($costpercpo['budgetsd'])."</td>";
    $tab.= "<td align=right>".number_format($costpercpo['selisihsd'])."</td>";*/
	
	/*    $tab.= "<td align=left>".$_SESSION['lang']['cpokuantitas']." (Ton)</td>"; 
    $tab.= "<td align=right>".number_format($cpo/1000)."</td>"; 
    $tab.= "<td align=right>".number_format($cpobudget/1000)."</td>"; 
    $tab.= "<td align=right>".number_format($cposelisih/1000)."</td>"; 
    $tab.= "<td align=right>".number_format($cposd/1000)."</td>"; 
    $tab.= "<td align=right>".number_format($cpobudgetsd/1000)."</td>"; 
    $tab.= "<td align=right>".number_format($cposelisihsd/1000)."</td>"; */
	$tab.= "<td align=right>".number_format($x1/$cpo,2)."</td>";
    $tab.= "<td align=right>".number_format($x2/$cpobudget,2)."</td>";
    $tab.= "<td align=right>".number_format($x3/$cposelisih,2)."</td>";
    $tab.= "<td align=right>".number_format($x4/$cposd,2)."</td>";
    $tab.= "<td align=right>".number_format($x5/$cpobudgetsd,2)."</td>";
    $tab.= "<td align=right>".number_format($x6/$cposelisihsd,2)."</td>";
    $tab.= "</tr>";
	
	$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Total Cost / Kg Kernel</td>";
	$tab.= "<td align=right>".number_format($y1/$oerpk,2)."</td>";
    $tab.= "<td align=right>".number_format($y2/$kernelbudget,2)."</td>";
    $tab.= "<td align=right>".number_format($y3/$kernelselisih,2)."</td>";
    $tab.= "<td align=right>".number_format($y4/$kernelsd,2)."</td>";
    $tab.= "<td align=right>".number_format($y5/$kernelbudgetsd,2)."</td>";
    $tab.= "<td align=right>".number_format($y6/$kernelselisihsd,2)."</td>";
    $tab.= "</tr>";
	
	
    $tab.="</tbody></table>";
			
switch($proses)
{
    case'preview':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    echo $tab;
    break;

    case'excel':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_=$judul."_".$unit."_".$periode;
    if(strlen($tab)>0)
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
    }
    break;

    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

            $cols=247.5;
            $wkiri=30;
            $wlain=11.5;

    class PDF extends FPDF {
    function Header() {
        global $periode,$judul;
        global $unit;
        global $optNm;
        global $optBulan;
        global $tahun;
        global $bulan;
        global $dbname;
        global $luas;
        global $wkiri, $wlain;
        global $luasbudg, $luasreal;
            $width = $this->w - $this->lMargin - $this->rMargin;
  
        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);

        $this->Cell($width/2,$height,$judul,NULL,0,'L',1);
        $this->Cell($width/2,$height,$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun,NULL,0,'R',1);
        $this->Ln();
        $this->Cell($width,$height,$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")",NULL,0,'L',1);
        $this->Ln();
        $this->Ln();

        $height = 15;
        $this->SetFont('Arial','B',10);
        $this->Cell($wkiri/100*$width,$height,'Uraian',TRL,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
        $this->Ln();
        $this->Cell($wkiri/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['selisih'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['selisih'],1,0,'C',1);	
        $this->Ln();
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
}
    //================================

    $pdf=new PDF('L','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 15;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
    
    $no=1;
// pdf array content =========================================================================
    $pdf->Cell($wkiri/100*$width,$height,$_SESSION['lang']['tbsdiolah'].' (Kg)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbs,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsbudget,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsselisih,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbssd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsbudgetsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbsselisihsd,2),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,$_SESSION['lang']['cpokuantitas'].' (Kg)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cpo,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cpobudget,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cposelisih,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cposd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cpobudgetsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($cposelisihsd,2),1,0,'R',1);	
    $pdf->Ln();
/*	    $tab.= "<td align=left>Kernel (Kg)</td>"; 
    $tab.= "<td align=right>".number_format($oerpk,2)."</td>"; 
    $tab.= "<td align=right>".number_format($kernelbudget,2)."</td>"; //d
    $tab.= "<td align=right>".number_format($kernelselisih,2)."</td>"; 
    $tab.= "<td align=right>".number_format($kernelsd,2)."</td>"; 
	$tab.= "<td align=right>".number_format($kernelbudgetsd,2)."</td>"; 
	$tab.= "<td align=right>".number_format($kernelselisihsd,2)."</td>";	*/
	$pdf->Cell($wkiri/100*$width,$height,'Kernel (Kg)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($oerpk,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($kernelbudget,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($kernelselisih,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($kernelsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($kernelbudgetsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($kernelselisihsd,2),1,0,'R',1);	
	$pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Biaya Administrasi',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total7['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total7['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total7['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total7['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total7['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total7['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Biaya Produksi',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total6364['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total6364['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total6364['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total6364['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total6364['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total6364['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'- Processing',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total631['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total631['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total631['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total631['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total631['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total631['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'- Maintenance',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total632['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total632['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total632['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total632['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total632['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total632['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Biaya Bahan Baku',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total64['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total64['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total64['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total64['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total64['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total64['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Harga Pokok Produksi',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($subtotal['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($subtotal['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($subtotal['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($subtotal['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($subtotal['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($subtotal['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
	
	    
    $pdf->Cell($wkiri/100*$width,$height,'',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
	$pdf->Ln();
/*    $pdf->Cell($wkiri/100*$width,$height,'Sales Expenses',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total8['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total8['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total8['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total8['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total8['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total8['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Grand Total',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($total['selisihsd']),1,0,'R',1);	
    $pdf->Ln();*/
	
	#ind
	$pdf->Cell($wkiri/100*$width,$height,'Harga Pokok CPO',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x1,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x2,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x3,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x4,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x5,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x6,2),1,0,'R',1);	
    $pdf->Ln();
	$pdf->Cell($wkiri/100*$width,$height,'Harga Pokok Kernel',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y1,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y2,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y3,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y4,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y5,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y6,2),1,0,'R',1);	
	$pdf->Ln();	
	
	$pdf->Cell($wkiri/100*$width,$height,'',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
	$pdf->Ln();
	
    $pdf->Cell($wkiri/100*$width,$height,'Total Cost / Kg TBS',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpertbs['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpertbs['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpertbs['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpertbs['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpertbs['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpertbs['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
	
	
/*    $pdf->Cell($wkiri/100*$width,$height,'Total Cost / Kg CPO',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpercpo['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpercpo['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpercpo['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpercpo['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpercpo['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($costpercpo['selisihsd']),1,0,'R',1);	
    $pdf->Ln();*/
    $pdf->Cell($wkiri/100*$width,$height,'Biaya Adm / Kg TBS',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($admpertbs['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($admpertbs['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($admpertbs['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($admpertbs['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($admpertbs['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($admpertbs['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Mill Proc / Kg TBS',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($procpertbs['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($procpertbs['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($procpertbs['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($procpertbs['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($procpertbs['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($procpertbs['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wkiri/100*$width,$height,'Mill Maint / Kg TBS',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($mainpertbs['biaya']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($mainpertbs['budget']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($mainpertbs['selisih']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($mainpertbs['biayasd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($mainpertbs['budgetsd']),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($mainpertbs['selisihsd']),1,0,'R',1);	
    $pdf->Ln();
    /*$pdf->Cell($wkiri/100*$width,$height,'Sales Exp / Ton TBS',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($salespertbs['biaya']*1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($salespertbs['budget']*1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($salespertbs['selisih']*1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($salespertbs['biayasd']*1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($salespertbs['budgetsd']*1000),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($salespertbs['selisihsd']*1000),1,0,'R',1);	
    $pdf->Ln();*/
	
	$pdf->Cell($wkiri/100*$width,$height,'Total Biaya Olah/Kg TBS',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbiayaolahpertbs['biaya'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbiayaolahpertbs['budget'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbiayaolahpertbs['selisih'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbiayaolahpertbs['biayasd'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbiayaolahpertbs['budgetsd'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($tbiayaolahpertbs['selisihsd'],2),1,0,'R',1);	
    $pdf->Ln();
	
	$pdf->Cell($wkiri/100*$width,$height,'Biaya Bahan Baku',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['biaya'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['budget'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['selisih'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['biayasd'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['budgetsd'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['selisihsd'],2),1,0,'R',1);	
    $pdf->Ln();
	
	$pdf->Cell($wkiri/100*$width,$height,'Biaya Bahan Baku',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['biaya'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['budget'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['selisih'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['biayasd'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['budgetsd'],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($biayabhnbaku['selisihsd'],2),1,0,'R',1);	
    $pdf->Ln();
	
		
	$pdf->Cell($wkiri/100*$width,$height,'',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
	$pdf->Ln();
	
	$pdf->Cell($wkiri/100*$width,$height,'Total Cost / Kg CPO',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x1/$cpo,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x2/$cpobudget,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x3/$cposelisih,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x5/$cpobudgetsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x5/$cpobudgetsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($x6/$cposelisihsd,2),1,0,'R',1);	
    $pdf->Ln();
	
	$pdf->Cell($wkiri/100*$width,$height,'Total Cost / Kg Kernel',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y1/$oerpk,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y2/$kernelbudget,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y3/$kernelselisih,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y4/$kernelsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y5/$kernelbudgetsd,2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,number_format($y6/$kernelselisihsd,2),1,0,'R',1);	
    $pdf->Ln();
	
	
	/*$tab.= "<td align=right>".number_format($x1/$cpo,2)."</td>";
    $tab.= "<td align=right>".number_format($x2/$cpobudget,2)."</td>";
    $tab.= "<td align=right>".number_format($x3/$cposelisih,2)."</td>";
    $tab.= "<td align=right>".number_format($x4/$cposd,2)."</td>";
    $tab.= "<td align=right>".number_format($x5/$cpobudgetsd,2)."</td>";
    $tab.= "<td align=right>".number_format($x6/$cposelisihsd,2)."</td>";
    $tab.= "</tr>";
	
	$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>Total Cost / Kg Kernel</td>";
	$tab.= "<td align=right>".number_format($y1/$oerpk,2)."</td>";
    $tab.= "<td align=right>".number_format($y2/$kernelbudget,2)."</td>";
    $tab.= "<td align=right>".number_format($y3/$kernelselisih,2)."</td>";
    $tab.= "<td align=right>".number_format($y4/$kernelsd,2)."</td>";
    $tab.= "<td align=right>".number_format($y5/$kernelbudgetsd,2)."</td>";
    $tab.= "<td align=right>".number_format($y6/$kernelselisihsd,2)."</td>";*/
    
    $pdf->Output();	 
    break;

    default:
    break;
}
	
?>
