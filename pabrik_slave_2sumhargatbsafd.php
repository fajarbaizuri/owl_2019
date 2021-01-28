<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdsup=$_POST['kdsup'];
$kdkg=$_POST['kdkg'];
$afdId=$_POST['afdId'];
//exit("$kdsup");
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	$afdId=$_GET['afdId'];
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



              if($proses=='excel')$stream="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                 <tr>
				 	<td align=center>No</td>
					<td align=center>Kebun</td>
					<td align=center>Afd</td>
					<td align=center>Tanggal</td>
					<td align=center>BJR</td>
					<td align=center>Harga Satuan<br />(RP)</td>
					<td align=center>Netto<br />(Kg)</td>
					<td align=center>Sortasi <br />(Kg)</td>
					<td align=center>Berat Normal <br />(Kg)</td>
					<td align=center>Total <br />(Rp)</td>
  				</tr></thead>
                <tbody>";
			if ($kdsup!="___E"){
				if ($afdId=="___E__"){
					$afdId=$kdsup;
				}
			}
			$str=" SELECT b.namaorganisasi as nama,left(right(a.nospb,14),6) as afd, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,a.tanggal as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".organisasi b ON a.kodeorg=b.kodeorganisasi
			WHERE a.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' and a.nospb like'%".$afdId."%' order by left(right(a.nospb,14),6),a.tanggal ";	
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='INTERNAL' order by tanggal desc";
				
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
$HASIL[$j]['afd']=$bar->afd;
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
	<td>".$rowH['afd']."</td>
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
 
				if($proses=='excel'){
				$stream.="
							<thead><tr>
							<td align=center colspan=6>Total</td>
							<td align=right>".number_format($tonetto,2)." Kg</td>
							<td align=right>".number_format($tosortasi,2)." Kg</td>
							<td align=right>".number_format($toberatnormal,2)." Kg</td>
							<td align=right>".number_format($tototal)."</td></tr>
							</tbody></table>";
				}else{
				$stream.="
							<thead><tr>
							<td align=center colspan=6>Total</td>
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
	
	
	

	
	
	default:
	break;
}


?>