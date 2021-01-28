<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdsup=$_POST['kdsup'];
$kdkg=$_POST['kdkg'];
//exit("$kdsup");
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	$kdkg=$_GET['kdkg'];
	$kdsup=$_GET['kdsup'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
	
}

$optnmor=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optjnvhc=makeOption($dbname, 'vhc_5jenisvhc','jenisvhc,namajenisvhc');
$optnmbar=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optnamacostumer=makeOption($dbname,'log_5supplier','supplierid,namasupplier');



$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);



if(($proses=='preview')or($proses=='excel')or($proses=='pdf'))
{

    if(($tgl1_=='')or($tgl2_==''))
	{
		echo"Error: Tanggal tidak boleh kosong"; 
		exit;
    }

    else if($tgl1>$tgl2)
	{
        echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua"; 
		exit;
    }
	
}

##ambil data SQL untuk global view 
//$str=" SELECT * FROM ".$dbname.".vhc_penggantianht where kodevhc='".$kdorgs."'";
//$str="select a.kodecustomer,a.beratbersih as netto,a.substr(tanggal,1,10) as tanggal from ".$dbname.".pabrik_timbangan join where tanggal between '".$tgl1."' and '".$tgl2."'";


              if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                 <tr>
				 	<td align=center>No</td>
					<td align=center>Supplier</td>
					<td align=center>Tanggal</td>
					<td align=center>BJR</td>
					<td align=center>Harga Satuan<br />(RP)</td>
					<td align=center>Netto<br />(Kg)</td>
					<td align=center>Sortasi <br />(Kg)</td>
					<td align=center>Berat Normal <br />(Kg)</td>
					<td align=center>Total <br />(Rp)</td>
  				</tr></thead>
                <tbody>";
				
$sIan="select kodetimbangan from ".$dbname.". log_5supplier where supplierid='".$kdsup."'";
                           $qIan=mysql_query($sIan) or die(mysql_error());
                            $rIan=mysql_fetch_assoc($qIan);
                            $nmIam=$rIan['kodetimbangan'];
							
if($kdsup=='')
{
			$str=" 	SELECT b.namasupplier as nama,a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,a.tanggal as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5supplier b ON a.kodecustomer=b.supplierid
			WHERE a.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59'  order by a.tanggal,a.kodecustomer ";	
	//		echo $str;
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs  order by tanggal desc";
}		
else if(substr($kdsup,3,1)=='E' && strlen($kdsup)==4)
{
			$str=" SELECT b.namaorganisasi as nama, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,a.tanggal as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".organisasi b ON a.kodeorg=b.kodeorganisasi
			WHERE a.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' and a.kodeorg='".$kdsup."' order by a.tanggal,a.kodeorg ";	
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='INTERNAL' order by tanggal desc";
}
else{
	/*$str=" 	SELECT a.kodecustomer,a.beratbersih as netto,substr(a.tanggal,1,10) as tanggal,b.harga,(beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a
			JOIN ".$dbname.".pabrik_5hargatbs b
			ON  a.tanggal = b.tanggal
			WHERE a.tanggal between '".$tgl1."' and '".$tgl2."' and kodecustomer='".$kdsup."'  ";*/
			/*
			$str=" 	SELECT b.namasupplier as nama, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,a.tanggal as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a  left join ".$dbname.".log_5supplier b ON a.kodecustomer=b.supplierid
			WHERE a.tanggal between '".$tgl1."' and '".$tgl2."' and a.kodecustomer='".$kdsup."' order by a.tanggal ";
			*/
			
			
			
			
			$str=" 	SELECT IFNULL(c.namasupplier,b.namasupplier) as nama, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,a.tanggal as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5supplier b ON a.kodecustomer=b.supplierid
			left join ".$dbname.".log_5supplier c ON a.kodecustomer=c.kodetimbangan
			WHERE a.tanggal between '".$tgl1." 00:00:00' and '".$tgl2."  23:59:59' and a.kodecustomer in ('".$kdsup."','".$nmIam."')  and a.kodeorg='' order by a.tanggal ";
			
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid ='".$kdsup."' order by tanggal desc";
			
			//echo $str;
			
			//echo $str;
}

//echo $str;
//exit;
				
$no=0;
$ttl=0;



$HARGA = array();
$i = 0;

$res1=mysql_query($str1);	
while($bar1=mysql_fetch_object($res1))
{
$HARGA[$i]['tgl']=$bar1->tanggal;
$HARGA[$i]['bjr']=$bar1->bjr;
$HARGA[$i]['kg']=$bar1->kg;
$HARGA[$i]['harga']=$bar1->harga;
$i++;
}

$HASIL = array();
$j = 0;
$res=mysql_query($str);	
while($bar=mysql_fetch_object($res))
{ 
//S001120180
//S001120180
		


$HASIL[$j]['no']=$j+1;
$HASIL[$j]['nama']=$bar->nama;
$HASIL[$j]['tgl']=$bar->tanggal;
$HASIL[$j]['bjr']=$bar->bjr;
$HASIL[$j]['netto']=$bar->netto;
$HASIL[$j]['sortasi']=$bar->kgpotsortasi;
$HASIL[$j]['normal']=$bar->netto - $bar->kgpotsortasi;
$j++;
}
$total=0;
$k=0;
		$tonetto=0;
		$tosortasi=0;
		$toberatnormal=0;
		$tototal=0;
foreach ($HASIL as $rowH) {
	if($proses=='excel')
	$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
	else $stream.="<tr class=rowcontent>";
	$stream.="
	<td>".$rowH['no']."</td>
	<td>".$rowH['nama']."</td>
	<td>".tanggalnormal($rowH['tgl'])."</td>
	<td align=right>".number_format($rowH['bjr'],2)."</td>";
	
	
   foreach ($HARGA as $key=>$val) {
		$Ktgl1=date('Ymd', strtotime($val['tgl']));
		$Ktgl2=date('Ymd', strtotime($rowH['tgl']));
		//cek tanggal
	  if ($Ktgl1 <= $Ktgl2) {
		//cek bjr
		if ($val['bjr'] == 1) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] > 0){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] >= 5000 ){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}
			
		}else if ($val['bjr'] == 5) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] > 0){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] >= 5000 ){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 6) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] > 0){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] >= 5000 ){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 2) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] > 0){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] >= 5000 ){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 3) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] > 0){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] >= 5000 ){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 4) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] > 0){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] >= 5000 ){
					
					if ($kdkg=="NO"){
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['normal'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['normal'];
					}else{
						$stream.="
						<td align=right>Rp. ".number_format($val['harga'])."</td>
						<td align=right>".number_format($rowH['netto'],2)." Kg</td>
						<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
						<td align=right>".number_format($rowH['normal'],2)." Kg</td>
						<td align=right>".number_format($val['harga']*$rowH['netto'])."</td>
						</tr>";		
						$total=$val['harga']*$rowH['netto'];
					}
					
					
					goto DD;
				}
			}
		
		}
		
		
	  }
	  
	                  
   }
   $stream.="
					<td align=right>Rp. 0</td>
					<td align=right>".number_format($rowH['netto'],2)." Kg</td>
					<td align=right>".number_format($rowH['sortasi'],2)." Kg</td>
					<td align=right>".number_format($rowH['normal'],2)." Kg</td>
					<td align=right>0</td>
					</tr>";		
					$total=0;
   DD:
   #untuk total
		$tonetto+=$rowH['netto'];
		$tosortasi+=$rowH['sortasi'];
		$toberatnormal+=$rowH['normal'];
		$tototal+=$total;
		
		$k++;
}
 
/*
$res=mysql_query($str);	
while($bar=mysql_fetch_assoc($res))
{

	$bjr=$bar['bjr'];
	if($proses=='excel')
	$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
	else $stream.="<tr class=rowcontent>";
	
	$beratnormal=$bar['netto']-$bar['kgpotsortasi'];//and supplierid='".$bar['kodecustomer']."'
	//$total=$c['harga']*$beratnormal;//<td>".$optnamacostumer[$bar['kodecustomer']]."</td>
	$no+=1;
	$stream.="
	<td>".$no."</td>
	<td>".$bar['nama']."</td>
	<td>".tanggalnormal($bar['tanggal'])."</td>
	<td align=right>".number_format($bjr,2)."</td>";
	
	$stream.="<td align=left>Belum menginput harga</td>";
	
	if($proses=='excel'){
		$stream.="<td align=right>".number_format($bar['netto'],2)." Kg</td>
		<td align=right>".number_format($bar['kgpotsortasi'],2)." Kg</td>
		<td align=right>".number_format($beratnormal,2)." Kg</td>
		<td align=right>".number_format($total)."</td>
		</tr>";		
	}else{
		$stream.="<td align=right>".number_format($bar['netto'],2)." Kg</td>
		<td align=right>".number_format($bar['kgpotsortasi'],2)." Kg</td>
		<td align=right>".number_format($beratnormal,2)." Kg</td>
		<td align=right>Rp. ".number_format($total)."</td>
		</tr>";		
	}                                     

	if(substr($kdsup,3,1)=='E')
	 {
	   $bar['kodecustomer']='INTERNAL';
	  }
	
	$bjr=$bar['bjr'];
	//echo $bjr._;
	$fortgl=date('Ymd', strtotime($bar['tanggal']));
	if ($fortgl < 20140417){
		if($bjr<3){
			$a="select 0 as harga,'0000-00-00' as tanggal";
		}
		elseif($bjr>=3 && $bjr<5)
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='1' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
		}
		else if($bjr>=5 && $bjr<7)
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='2' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
			//echo $a;
		}
		else
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='3' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
			//echo $a;
		}
	}else{
		if($bjr<5){
			$a="select 0 as harga,'0000-00-00' as tanggal";
		}
		elseif($bjr>=5 && $bjr<=8)
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='5' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
		}
		else
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='6' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
			//echo $a;
		}
	}
	$b=mysql_query($a);
	$c=mysql_fetch_assoc($b);
	//$harga=$c['harga'];
	
	$la="select disticnt kodetimbangan from ".$dbname.".log_5supplier";
	//echo $la;
	$li=mysql_query($la);
	$lu=mysql_fetch_assoc($li);
		$supz=$lu['kodetimbangan'];
	
	if($supz=='')
	{
		$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$bar['kodecustomer']."'  ";
	}
	else
	{
		$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$bar['kodecustomer']."'  ";
	}	
	$qNm=mysql_query($sNm) or die(mysql_error());
	$rNm=mysql_fetch_assoc($qNm);
		$nm=$rNm['namasupplier'];	

	if(substr($kdsup,3,1)=='E')
	 {
	   $nm=$kdsup;
	  }
	  
		
	if($proses=='excel')
						$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
					else $stream.="<tr class=rowcontent>";
		$no+=1;
		
		$beratnormal=$bar['netto']-$bar['kgpotsortasi'];//and supplierid='".$bar['kodecustomer']."'
		$total=$c['harga']*$beratnormal;//<td>".$optnamacostumer[$bar['kodecustomer']]."</td>
		
		//echo $c['harga'].___;
			$stream.="
				<td>".$no."</td>
				<td>".$nm."</td>
				<td>".tanggalnormal($bar['tanggal'])."</td>
				<td align=right>".number_format($bar['bjr'],2)."</td>";
				if($c['harga']==0)
				{
				$stream.="<td align=left>Tidak Dibayar</td>";
					
				}elseif($c['harga']=='')
				{
				$stream.="<td align=left>Belum menginput harga</td>";
				}
				else
				{
					$stream.="<td align=right>Rp. ".number_format($c['harga'])."</td>";
				}
				if($proses=='excel'){
				$stream.="<td align=right>".number_format($bar['netto'],2)." Kg</td>
				<td align=right>".number_format($bar['kgpotsortasi'],2)." Kg</td>
				<td align=right>".number_format($beratnormal,2)." Kg</td>
				<td align=right>".number_format($total)."</td>
				</tr>";		
				}else{
				$stream.="<td align=right>".number_format($bar['netto'],2)." Kg</td>
				<td align=right>".number_format($bar['kgpotsortasi'],2)." Kg</td>
				<td align=right>".number_format($beratnormal,2)." Kg</td>
				<td align=right>Rp. ".number_format($total)."</td>
				</tr>";		
				}
		
		#untuk total
		$tonetto+=$bar['netto'];
		$tosortasi+=$bar['kgpotsortasi'];
		$toberatnormal+=$beratnormal;
		$tototal+=$total;
		
	}*/
				if($proses=='excel'){
				$stream.="
							<thead><tr>
							<td align=center colspan=5>Total</td>
							<td align=right>".number_format($tonetto,2)." Kg</td>
							<td align=right>".number_format($tosortasi,2)." Kg</td>
							<td align=right>".number_format($toberatnormal,2)." Kg</td>
							<td align=right>".number_format($tototal)."</td></tr>
							</tbody></table>";
				}else{
				$stream.="
							<thead><tr>
							<td align=center colspan=5>Total</td>
							<td align=right>".number_format($tonetto,2)." Kg</td>
							<td align=right>".number_format($tosortasi,2)." Kg</td>
							<td align=right>".number_format($toberatnormal,2)." Kg</td>
							<td align=right>Rp. ".number_format($tototal)."</td>
							</tr>	
							</tbody></table>";
				}
	
/*}
else
{
	exit("Tidak ada data silahkan input");
}*/

#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
	case 'preview':
		echo $stream;
    break;
 
######EXCEL	
	case 'excel':
		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="LAPORAN_HARGA_TBS".$tglSkrg;
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
	
	
	
###############	
#panggil PDFnya
###############
	
		case'pdf':

            class PDF extends FPDF
                    {
                        function Header() {
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
							global $kdorg;
							global $kdAfd;
							global $tgl1;
							global $tgl2;
							global $where;
							global $nmOrg;
							global $lok;
							global $notrans;
							

                            //$cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);

                            $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 20;
                            $path='images/logo.jpg';
                            //$this->Image($path,$this->lMargin,$this->tMargin,50);	
							$this->Image($path,30,15,55);
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(90); 
							  
                            $this->Cell($width-80,12,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',9);
							$height = 12;
                            $this->Cell($width-80,$height,$orgData[0]['alamat'],0,1,'L');	
                            $this->SetX(90); 			
                            $this->Cell($width-80,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                            $this->Ln();
                            $this->Line($this->lMargin,$this->tMargin+($height*4),
                            $this->lMargin+$width,$this->tMargin+($height*4));

                            $this->SetFont('Arial','B',12);
                                            $this->Ln();
                            $height = 15;
                                            $this->Cell($width,$height,"Laporan Harga TBS ".$kdorg,'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." : ". tanggalnormal($tgl1)." S/D ". tanggalnormal($tgl2),'',0,'C');
											//$this->Ln();
                                            $this->Ln(30);
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(3/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(15/100*$width,15,'Supplier',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Tanggal',1,0,'C',1);
											$this->Cell(10/100*$width,15,'BJR',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Harga Satuan',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Netto',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Sortasi',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Berat Normal',1,0,'C',1);
											$this->Cell(10/100*$width,15,'Total',1,1,'C',1);	
											
											
		
											//$this->Ln();
                       }

                        function Footer()
                        {
                            $this->SetY(-15);
                            $this->SetFont('Arial','I',8);
                            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('P','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 15;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',7);
		
		$res=mysql_query($str);//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		$ttl=0;
		while($bar=mysql_fetch_assoc($res))
		{	
			
			$bjr=$bar['bjr'];
	//echo $bjr._;
	if ($fortgl < 20140417){
		if($bjr<3){
			$a="select 0 as harga,'0000-00-00' as tanggal";
		}
		elseif($bjr>=3 && $bjr<5)
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='1' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
		}
		else if($bjr>=5 && $bjr<7)
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='2' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
			//echo $a;
		}
		else
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='3' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
			//echo $a;
		}
	}else{
		if($bjr<5){
			$a="select 0 as harga,'0000-00-00' as tanggal";
		}
		elseif($bjr>=5 && $bjr<=8)
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='5' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
		}
		else
		{
			$a="select harga,tanggal from ".$dbname.".pabrik_5hargatbs where bjr='6' and tanggal<='".$bar['tanggal']."' and supplierid='".$bar['kodecustomer']."' order by tanggal desc";
			//echo $a;
		}
	}
	$b=mysql_query($a);
	$c=mysql_fetch_assoc($b);
	//$harga=$c['harga'];
	
	$la="select disticnt kodetimbangan from ".$dbname.".log_5supplier";
	//echo $la;
	$li=mysql_query($la);
	$lu=mysql_fetch_assoc($li);
		$supz=$lu['kodetimbangan'];
	
	if($supz=='')
	{
		$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$bar['kodecustomer']."'  ";
	}
	else
	{
		$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$bar['kodecustomer']."'  ";
	}	
	$qNm=mysql_query($sNm) or die(mysql_error());
	$rNm=mysql_fetch_assoc($qNm);
		$nm=$rNm['namasupplier'];
		
			if ($kdkg=="NO"){
				$beratnormal=$bar['netto']-$bar['kgpotsortasi'];//and supplierid='".$bar['kodecustomer']."'
				$total=$c['harga']*$beratnormal;//<td>".$optnamacostumer[$bar['kodecustomer']]."</td>
			}else{
				$beratnormal=$bar['netto'];//and supplierid='".$bar['kodecustomer']."'
				$total=$c['harga']*$beratnormal;//<td>".$optnamacostumer[$bar['kodecustomer']]."</td>
			}
				
		
		
		
				//echo $sNm;			
			$no+=1;	
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$nm,1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,tanggalnormal($bar['tanggal']),1,0,'L',1);		
			$pdf->Cell(10/100*$width,$height,number_format($bar['bjr'],2),1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,number_format($c['harga']),1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,$bar['netto'],1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,$bar['kgpotsortasi'],1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,$beratnormal,1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,number_format($total),1,1,'R',1);	
		
		$tonetto+=$bar['netto'];
		$tosortasi+=$bar['kgpotsortasi'];
		$toberatnormal+=$beratnormal;
		$tototal+=$total;
											/*
				<td>".$no."</td>
				<td>".$nm."</td>
				<td>".tanggalnormal($bar['tanggal'])."</td>
				<td align=right>".number_format($bar['bjr'],2)."</td>
				<td align=right>".number_format($c['harga'])."</td>
				<td align=right>".number_format($bar['netto'],2)."</td>
				
				<td align=right>".$bar['kgpotsortasi']."</td>
				<td align=right>".$beratnormal."</td>
				<td align=right>".number_format($total)."</td>
											
											
											*/
		
		
		/*
		$tonetto+=$bar['netto'];
		$tosortasi+=$bar['kgpotsortasi'];
		$toberatnormal+=$beratnormal;
		$tototal+=$total;
					
	}
	$stream.="
				<thead><tr>
					<td align=center colspan=5>Total</td>
					<td align=right>".number_format($tonetto,2)."</td>
					<td align=right>".number_format($tosortasi,2)."</td>
					<td align=right>".number_format($toberatnormal,2)."</td>
					<td align=right>".number_format($tototal)."</td>
		
		*/
		//$totnet+=$bar['netto'];
		
		}
			$pdf->SetFillColor(220,220,220);
			//$pdf->SetFont('arial','B',10);
			$pdf->Cell(48/100*$width,$height,strtoupper('Total'),1,0,'C',1);
					
			$pdf->Cell(10/100*$width,$height,number_format($tonetto,2),1,0,'R',1);	
			$pdf->Cell(10/100*$width,$height,number_format($tosortasi,2),1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,number_format($toberatnormal,2),1,0,'R',1);
			$pdf->Cell(10/100*$width,$height,number_format($tototal),1,1,'R',1);
//		
		
		$pdf->Output();
            
	break;

	
	
	default:
	break;
}


?>