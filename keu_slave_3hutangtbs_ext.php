<?php

require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');


if ($_POST["tgl1"]=="" || $_POST['tgl2']==""){
	exit('Error: Tanggal Harus Diisi..');
}

$TglFrom=explode('-',$_POST['tgl1']);
$TglTo=explode('-',$_POST['tgl2']);


if ($TglFrom[2]."-".$TglFrom[1]."-".$TglFrom[0]>$TglTo[2]."-".$TglTo[1]."-".$TglTo[0]){
	exit('Error: Interval Tanggal Salah..');
}


$tglSeleksiIn1=$TglFrom[2].$TglFrom[1].$TglFrom[0];
$tglSeleksiIn1A = $TglFrom[2]."-".$TglFrom[1]."-".$TglFrom[0];



			
$tglSeleksiIn2A = $TglTo[2]."-".$TglTo[1]."-".$TglTo[0];

$tglSeleksiIn2=$TglTo[2].$TglTo[1].$TglTo[0];

#1. Cek Pengakuan Tbs yg Sudah Ada
$str="select `keterangan` from ".$dbname.". keu_jurnaldt_vw where `noreferensi` like 'TBS_EXT%' and noakun='1150101'";
	 
 $res=  mysql_query($str);
 while($bar=mysql_fetch_array($res))
 {
	$HasilT=explode('#',$bar[0]);
	$DbTglFrom=explode('-',$HasilT[1]);
	$DbTglTo=explode('-',$HasilT[2]);
	$tgldb1=$DbTglFrom[2].$DbTglFrom[1].$DbTglFrom[0];
	$tgldb2=$DbTglTo[2].$DbTglTo[1].$DbTglTo[0];
	if (($tglSeleksiIn1>= $tgldb1 && $tglSeleksiIn1<= $tgldb2) || ($tglSeleksiIn2>= $tgldb1 && $tglSeleksiIn2<= $tgldb2)){
		exit('Error: Pada range tanggal tsb. Pengakuan Hutang TBS Sudah dilakukan.');
	}
 }
 
		
			$str2=" 	SELECT b.namasupplier as nama, a.kgpotsortasi,ifnull(b.supplierid,a.kodecustomer) as supplierid ,a.beratbersih as netto,a.tanggal as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5supplier b ON (a.kodecustomer = b.supplierid OR a.kodecustomer = b.kodetimbangan)
			WHERE a.tanggal between '".$TglFrom[2]."-".$TglFrom[1]."-".$TglFrom[0]."  00:00:00' and  '".$TglTo[2]."-".$TglTo[1]."-".$TglTo[0]."  23:59:59' and  a.kodeorg=''  order by a.kodecustomer,a.tanggal ";
			
			$str1="select  supplierid, bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs  where supplierid!='INTERNAL' order by tanggal desc";
			
		
$no=0;
$ttl=0;
  


$HARGA = array();
$i = 0;

$res1=mysql_query($str1);	
while($bar1=mysql_fetch_object($res1))
{
$HARGA[$bar1->supplierid][$i]['tgl']=$bar1->tanggal;
$HARGA[$bar1->supplierid][$i]['bjr']=$bar1->bjr;
$HARGA[$bar1->supplierid][$i]['kg']=$bar1->kg;
$HARGA[$bar1->supplierid][$i]['harga']=$bar1->harga;
$i++;
}

$HASIL = array();
$j = 0;
$res=mysql_query($str2);	
while($bar=mysql_fetch_object($res))
{ 
//S001120180
//S001120180
		


$HASIL[$j]['no']=$j+1;

$HASIL[$j]['kode']=$bar->supplierid;
$HASIL[$j]['nama']=$bar->nama;
$HASIL[$j]['tgl']=$bar->tanggal;
$HASIL[$j]['bjr']=$bar->bjr;
$HASIL[$j]['netto']=$bar->netto;
$HASIL[$j]['sortasi']=$bar->kgpotsortasi;
$HASIL[$j]['normal']=$bar->netto - $bar->kgpotsortasi;
$j++;
} 
$total=0;
$k=1;
		$tonetto=0;
		$tosortasi=0;
		$toberatnormal=0;
		$tototal=0;
		$POINTER = array();
	$AYRA = array();
foreach ($HASIL as $rowH) {
	  

	
	
	
   foreach ($HARGA[$rowH['kode']] as $key=>$val) {
		 
		$Ktgl1=date('Ymd', strtotime($val['tgl']));
		$Ktgl2=date('Ymd', strtotime($rowH['tgl']));
		//cek tanggal
	  if ($Ktgl1 <= $Ktgl2) {
		 
		//cek bjr
		if ($val['bjr'] == 1) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] > 0){

					
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){

					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] >= 5000 ){

					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}
			
		}else if ($val['bjr'] == 5) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] > 0){
		
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
	
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] >= 5000 ){

					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 6) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] > 0){

					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){

					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] >= 5000 ){

					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 2) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] > 0){
	
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
		
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] >= 5000 ){
	
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 3) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] > 0){
			
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] >= 5000 ){
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 4) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] > 0){
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] >= 5000 ){
					$POINTER[$rowH['kode']]=$rowH['kode'];
					$AYRA[$rowH['kode']][$k]['nm']=$rowH['nama'];
					$AYRA[$rowH['kode']][$k]['tgl']=tanggalnormal($rowH['tgl']);
					$AYRA[$rowH['kode']][$k]['kg']=$rowH['normal'];
					$AYRA[$rowH['kode']][$k]['rp']=$val['harga']*$rowH['normal'];
					$k++;
					goto DD;
				}
			}
		
		}
		
		
	  }
	  
	                  
   }
   


   DD:
   #untuk total 
//$stream.="</tr>";
        
		
}


	$FIX = array();
$a = datediff($tglSeleksiIn1A, $tglSeleksiIn2A);


$XSE1 = intval($TglFrom[2].$TglFrom[1]);			
$XSE2 = intval($TglTo[2].$TglTo[1]);
$totalBulan=$XSE2-$XSE1;
$Z=1;
$F=0;
foreach($POINTER as $key) {
	 $tempkey="";
	
	foreach($AYRA[$key] as $view=>$key1) {
						
						
						$dbn=explode('-',$key1['tgl']);
							$FIX[$Z]['no']=$Z;
							$FIX[$Z]['nm']=$key;
							$FIX[$Z]['kd']=$key1['nm'];
							     if ($Z==1){
									if ($totalBulan==0){
											$TGLTEMP="#".$TglFrom[0]."-".$TglFrom[1]."-".$TglFrom[2]."#".$TglTo[0]."-".$TglTo[1]."-".$TglTo[2]."#";
									}else{
										   $TGLTEMP="#".$TglFrom[0]."-".$TglFrom[1]."-".$TglFrom[2]."#".jumlah_hari($TglFrom[1],$TglFrom[2])."-".$TglFrom[1]."-".$TglFrom[2]."#";
									} 
								 }
								 
								 $FIX[$Z]['tgl']=$TGLTEMP;
									
								 
								 
								  
									
								
								//$FIX[$Z]['tgl']=$key1['tgl']; 
							
						
							$FIX[$Z]['kg']+=$key1['kg']; 
							$FIX[$Z]['rp']+=$key1['rp']; 
						    
						    
						    
						if ($tempkey!=$dbn[2].$dbn[1] && $tempkey!=""){
								$Z++;	
								
						}						
						$dbn1=explode('-',$key1['tgl']);
						$tempkey=$dbn1[2].$dbn1[1] ;
						
								if ($dbn1[2].$dbn1[1]==$TglTo[2].$TglTo[1]){
									  
									  $ww=$XSE1-intval($dbn1[2].$dbn1[1]);
									  	if ($ww==0){
											$TGLTEMP="#".$TglFrom[0]."-".$TglFrom[1]."-".$TglFrom[2]."#".$TglTo[0]."-".$TglTo[1]."-".$TglTo[2]."#";
										}else{
										   $TGLTEMP="#01-".$TglTo[1]."-".$TglTo[2]."#".$TglTo[0]."-".$TglTo[1]."-".$TglTo[2]."#";
										} 
									  
								}else{
										  $ww=$XSE1-intval($dbn1[2].$dbn1[1]);
										if ($ww==0){
											$TGLTEMP="#".$TglFrom[0]."-".$TglFrom[1]."-".$TglFrom[2]."#".$TglTo[0]."-".$TglTo[1]."-".$TglTo[2]."#";
										}else{
										   $TGLTEMP="#01-".$dbn1[1]."-".$dbn1[2]."#".jumlah_hari($dbn1[1],$dbn1[2])."-".$dbn1[1]."-".$dbn1[2]."#";
										} 
									 
								}
						 
			 	  
				
	}
				
				$Z++;
					$F++;
}
	

		

  
 

foreach($FIX as $view) { 
	$stream.="<tr class=rowcontent id='row".$view['no']."'>
					<td id='no".$view['no']."'>".$view['no']."</td>
					<td id='kode".$view['no']."'>".$view['nm']."</td>
					<td id='kebun".$view['no']."'>".$view['kd']."</td>
					<td id='tgl".$view['no']."'>".$view['tgl']."</td>
					<td id='kg".$view['no']."' align=right>".$view['kg']."</td>
					<td id='rp".$view['no']."' align=right>".$view['rp']."</td>
					</tr>";		
}

	 

echo"<button class=mybutton onclick=prosesEksternal(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
					<td>Kode</td>
                    <td>Kebun/Supplier</td>
                    <td>Tanggal</td>
                    <td>Total Kg</td>
                    <td>Total Rupiah</td>
                    </tr>
                  </thead>
                  <tbody>";
echo $stream; 
echo"</tbody><tfoot></tfoot></table>"; 

 

function datediff($tgl1, $tgl2){
$tgl1 = (is_string($tgl1) ? strtotime($tgl1) : $tgl1);
$tgl2 = (is_string($tgl2) ? strtotime($tgl2) : $tgl2);
$diff_secs = abs($tgl1 - $tgl2);
$base_year = min(date("Y", $tgl1), date("Y", $tgl2));
$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
return array( 
"years" => date("Y", $diff) - $base_year,
"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
"months" => date("n", $diff) - 1,
"days_total" => floor($diff_secs / (3600 * 24)),
"days" => date("j", $diff) - 1,
"hours_total" => floor($diff_secs / 3600),
"hours" => date("G", $diff),
"minutes_total" => floor($diff_secs / 60),
"minutes" => (int) date("i", $diff),
"seconds_total" => $diff_secs,
"seconds" => (int) date("s", $diff ));
}

function jumlah_hari($bulan = 0, $tahun ="") { 
if ($bulan < 1 OR $bulan > 12) {
	return 0; 
} 
if ( ! is_numeric($tahun) OR strlen($tahun) != 4) {
	$tahun = date('Y'); 
} 
if ($bulan == 2) { 
	if ($tahun % 400 == 0 OR ($tahun % 4 == 0 AND $tahun % 100 != 0)) {
		return 29; 
		} 
} $jumlah_hari = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
return $jumlah_hari[$bulan - 1]; 
} 
 
?>