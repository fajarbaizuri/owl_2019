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
	$kdsup=$_GET['kdsup'];
	$kdkg=$_GET['kdkg'];
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
    }else if($tgl1>$tgl2)
	{
        echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua"; 
		exit;
    }
	
}

##ambil data SQL untuk global view 
//$str=" SELECT * FROM ".$dbname.".vhc_penggantianht where kodevhc='".$kdorgs."'";
//$str="select a.kodecustomer,a.beratbersih as netto,a.substr(tanggal,1,10) as tanggal from ".$dbname.".pabrik_timbangan join where tanggal between '".$tgl1."' and '".$tgl2."'";
		$sNm="select * from ".$dbname.".log_5supplier where supplierid='".$kdsup."'  ";
		$qNm=mysql_query($sNm) or die(mysql_error());
		$rNm=mysql_fetch_assoc($qNm);
		if($kdsup=='TDAE')
		{
			$nm=$kdsup;
			$atasnm="PT. Fajar Baizury & Brothers";
			$bank="BRI UNIT JATINEGARA - JAKARTA";
			$kota="DKI JAKARTA";
			$rek="0122-01-001722-30-2";
		}elseif($kdsup=='TDBE'){
			$nm=$kdsup;
			$atasnm="PT. Fajar Baizury & Brothers";
			$bank="BRI UNIT JATINEGARA - JAKARTA";
			$kota="DKI JAKARTA";
			$rek="0122-01-002112-30-6";
		}elseif($kdsup=='USJE'){
			$nm=$kdsup;
			$atasnm="PT. Usaha Semesta Jaya";
			$bank="BRI UNIT JATINEGARA - JAKARTA";
			$kota="DKI JAKARTA";
			$rek="0122-01-001709-30-4";
		}else{
			$nm=$rNm['namasupplier'];
			$atasnm=$rNm['an'];
			$bank=$rNm['bank'];
			$kota=$rNm['kota'];
			$rek=$rNm['rekening'];
		}
        if($proses=='excel'){
			$stream="<table cellspacing='1' border='0' class='sortable' bgcolor=#CCCCCC>";
			$stream.="
				<thead ><tr bgcolor=#FFFFFF>
					<td align=center colspan=5><b>PT. Fajar Baizury & Brother</b></td>
				</tr>
	            <tr bgcolor=#FFFFFF>
					<td align=center colspan=5>Pelunasan TBS - ".$nm."</td>
				</tr>
				<tr bgcolor=#FFFFFF>
					<td align=center colspan=5>Periode (".$tgl1." s/d ".$tgl2.")</td>
				</tr>
				<tr bgcolor=#FFFFFF><td align=center colspan=5></td></tr>
				
	</thead>";
	$stream.="<table cellspacing='1' border='1' class='sortable' bgcolor=#CCCCCC><tbody>";
		}else $stream.="<table cellspacing='1' border='0' class='sortable'>";
		
              $stream.="<thead class=rowheader>
				 <tr>
					<td align=center rowspan=2>&nbsp;BJR Range&nbsp;</td>
					
					<td align=center rowspan=2>Tanggal</td>
					<td align=center>&nbsp;&nbsp;Rupiah&nbsp;&nbsp;</td>
					
					<td align=center>Jumlah (KG)</td>
					<td align=center>Total (Rp.)</td>
				 </tr>
				 <tr>
					<td align=center> (I) </td>
					<td align=center>(II)</td>
					<td align=center>(I x II)</td>
				 </tr>
                </thead>
                <tbody>";
//harga tbs
/*
if($kdsup=='')
{
$str="SELECT kgpotsortasi,kodecustomer,beratbersih as netto,substr(tanggal,1,10) as tanggal,(beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3)) as bjr,
IFNULL(if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=3 AND (beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))<5,'BJR 3-5',if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=5 AND (beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))<7,'BJR 5-7',if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=7 ,'BJR > 7','Diluar Range'))),0) AS nma  
			FROM ".$dbname.".pabrik_timbangan 
			WHERE tanggal between '".$tgl1."' and '".$tgl2."' and kodecustomer like '%S%' order by tanggal,kodecustomer ";
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='".$kdsup."' order by tanggal desc";

}
else if(substr($kdsup,3,1)=='E' && strlen($kdsup)==4)
{
			
			
			$str="SELECT kgpotsortasi,kodecustomer,beratbersih as netto,substr(tanggal,1,10) as tanggal,(beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3)) as bjr,
IFNULL(if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=3 AND (beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))<5,'BJR 3-5',if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=5 AND (beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))<7,'BJR 5-7',if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=7 ,'BJR > 7','Diluar Range'))),0) AS nma  
			FROM ".$dbname.".pabrik_timbangan 
			WHERE tanggal between '".$tgl1."' and '".$tgl2."' and kodeorg='".$kdsup."'  order by tanggal ";
			
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='INTERNAL' order by tanggal desc";
}
else{
$str="SELECT kgpotsortasi,kodecustomer,beratbersih as netto,substr(tanggal,1,10) as tanggal,(beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3)) as bjr,
IFNULL(if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=3 AND (beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))<5,'BJR 3-5',if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=5 AND (beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))<7,'BJR 5-7',if((beratbersih/(jumlahtandan1+jumlahtandan2+jumlahtandan3))>=7 ,'BJR > 7','Diluar Range'))),0) AS nma  
			FROM ".$dbname.".pabrik_timbangan 
			WHERE tanggal between '".$tgl1."' and '".$tgl2."' and kodecustomer='".$kdsup."'  order by tanggal,kodecustomer ";
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='".$kdsup."' order by tanggal desc";
}
	
*/

$sIan="select kodetimbangan from ".$dbname.". log_5supplier where supplierid='".$kdsup."'";
                           $qIan=mysql_query($sIan) or die(mysql_error());
                            $rIan=mysql_fetch_assoc($qIan);
                            $nmIam=$rIan['kodetimbangan'];
							
if($kdsup=='')
{
			//$str=" 	SELECT b.namasupplier as nama,a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,substr(a.tanggal,1,10) as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			//FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5supplier b ON a.kodecustomer=b.supplierid
			//WHERE a.tanggal between '".$tgl1."' and '".$tgl2."' and a.kodecustomer like '%S%' order by a.tanggal,a.kodecustomer ";	
	//		echo $str;
			//$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='".$kdsup."' order by tanggal desc";
}		
else if(substr($kdsup,3,1)=='E' && strlen($kdsup)==4)
{
			$str=" 	SELECT b.namaorganisasi as nama, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,substr(a.tanggal,1,10) as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
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
			//$str=" 	SELECT b.namasupplier as nama, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,substr(a.tanggal,1,10) as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			//FROM ".$dbname.".pabrik_timbangan a  left join ".$dbname.".log_5supplier b ON a.kodecustomer=b.supplierid
			//WHERE a.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' and a.kodecustomer='".$kdsup."' order by a.tanggal ";
			
			//$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='".$kdsup."' order by tanggal desc";
			
			$str=" 	SELECT IFNULL(c.namasupplier,b.namasupplier) as nama, a.kgpotsortasi,a.kodecustomer,a.beratbersih as netto,substr(a.tanggal,1,10) as tanggal,(a.beratbersih/(a.jumlahtandan1+a.jumlahtandan2+a.jumlahtandan3)) as bjr 
			FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".log_5supplier b ON a.kodecustomer=b.supplierid
			left join ".$dbname.".log_5supplier c ON a.kodecustomer=c.kodetimbangan
			WHERE a.tanggal between '".$tgl1." 00:00:00' and '".$tgl2." 23:59:59' and a.kodecustomer in ('".$kdsup."','".$nmIam."') and a.kodeorg=''  order by a.tanggal ";
			
			$str1="select supplierid,bjr,kg,harga,tanggal from ".$dbname.".pabrik_5hargatbs where supplierid='".$kdsup."' order by tanggal desc";
			
			
			
			
			//echo $str;
			
			//echo $str;
}

$no=0;
$ttl=0;

//echo $str;
//exit;

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
$HASIL[$j]['no']=$j+1;
$HASIL[$j]['nama']=$bar->nama;
$HASIL[$j]['tgl']=$bar->tanggal;
$HASIL[$j]['bjr']=$bar->bjr;
$HASIL[$j]['netto']=$bar->netto;
$HASIL[$j]['sortasi']=$bar->kgpotsortasi;
if ($kdkg=='NO'){
$HASIL[$j]['normal']=$bar->netto - $bar->kgpotsortasi;	
}else{
$HASIL[$j]['normal']=$bar->netto; 
}

$j++;
}
$total=0;
$k=0;
		$tonetto=0;
		$tosortasi=0;
		$toberatnormal=0;
		$tototal=0;
foreach ($HASIL as $rowH) {
		$simpan[$k][nama]=$rowH['nama'];
		$simpan[$k][waktu]=tanggalnormal($rowH['tgl']);
		
		
		
	
	
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
					$simpan[$k][bjr]="3-5";
					$simpan[$k][kg]=">0";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$simpan[$k][bjr]="3-5";
					$simpan[$k][kg]="<5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];		
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 3 && $rowH['bjr'] < 5 && $rowH['normal'] >= 5000 ){
					$simpan[$k][bjr]="3-5";
					$simpan[$k][kg]=">=5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}
			
		}else if ($val['bjr'] == 5) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] > 0){
					$simpan[$k][bjr]="5-8";
					$simpan[$k][kg]=">0";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$simpan[$k][bjr]="5-8";
					$simpan[$k][kg]="<5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] < 8 && $rowH['normal'] >= 5000 ){
					$simpan[$k][bjr]="5-8";
					$simpan[$k][kg]=">=5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 6) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] > 0){
					$simpan[$k][bjr]=">8";
					$simpan[$k][kg]=">0";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$simpan[$k][bjr]=">8";
					$simpan[$k][kg]="<5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 8 && $rowH['normal'] >= 5000 ){
					$simpan[$k][bjr]=">8";
					$simpan[$k][kg]=">=5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 2) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] > 0){
					$simpan[$k][bjr]="5-7";
					$simpan[$k][kg]=">0";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$simpan[$k][bjr]="5-7";
					$simpan[$k][kg]="<5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 5 && $rowH['bjr'] <7 && $rowH['normal'] >= 5000 ){
					$simpan[$k][bjr]="5-7";
					$simpan[$k][kg]=">=5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 3) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] > 0){
					$simpan[$k][bjr]=">7";
					$simpan[$k][kg]=">0";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$simpan[$k][bjr]=">7";
					$simpan[$k][kg]="<5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] >= 7 && $rowH['normal'] >= 5000 ){
					$simpan[$k][bjr]=">7";
					$simpan[$k][kg]=">=5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}
		
		}else if ($val['bjr'] == 4) {
			//cek kg
			if ($val['kg'] == 1) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] > 0){
					$simpan[$k][bjr]="<5";
					$simpan[$k][kg]=">0";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 2) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] > 0 && $rowH['normal'] < 5000){
					$simpan[$k][bjr]="<5";
					$simpan[$k][kg]="<5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}else if ($val['kg'] == 3) {
				if ($rowH['bjr'] > 0 && $rowH['bjr'] <5 && $rowH['normal'] >= 5000 ){
					$simpan[$k][bjr]="<5";
					$simpan[$k][kg]=">=5000";
					$simpan[$k][rupiah]=$val['harga'];
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=$val['harga']*$rowH['normal'];	
					$total=$val['harga']*$rowH['normal'];
					
					goto DD;
				}
			}
		
		}
		
		
	  }
	  
	                  
   }
					$simpan[$k][rupiah]=0;
					$simpan[$k][benor]=$rowH['normal'];
					$simpan[$k][total]=0;
					$total=0;
   DD:
   #untuk total
		$tonetto+=$rowH['netto'];
		$tosortasi+=$rowH['sortasi'];
		$toberatnormal+=$rowH['normal'];
		$tototal+=$total;
		
		$k++;
}

foreach($simpan as $c=>$key) {
				$total1+=$key['benor'];
				$total2+=$key['total'];
				$sortirASS[$key['bjr']][def]=$key['bjr'];
				//$sortir2[$key['bjr']][$key['waktu']][$key['rupiah']][sortgl]=$key['waktu'];
				
				$sortirASS1[$key['bjr']][$key['rupiah']][$key['waktu']]=$key['waktu'];
				
				
				//$sortir2[$key['bjr']][$key['waktu']][$key['rupiah']][sorrupiah]=$key['rupiah'];
				
				$sortirASS2[$key['bjr']][$key['rupiah']]=$key['rupiah'];
				
				$sortir3[$key['bjr']][$key['rupiah']][sorkg]=$key['benor'];
				$sortir3[$key['bjr']][$key['rupiah']][sorrupiah]=$key['total'];
				
				$totsortir1[$key['bjr']][$key['rupiah']]+=$sortir3[$key['bjr']][$key['rupiah']][sorkg];
				$totsortir2[$key['bjr']][$key['rupiah']]+=$sortir3[$key['bjr']][$key['rupiah']][sorrupiah];
	
}

$hut=0;
if(!empty($sortirASS)) foreach($sortirASS as $c=>$key1) {
if($proses=='excel')$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
		else $stream.="<tr class=rowcontent>";
	$stream1="";
	$stream2="";
	foreach($sortirASS2[$key1['def']] as $c =>$key2) {
	$hut++;
	
		if ($hut > 1){
			if($proses=='excel')$stream2="<tr bgcolor=#FFFFFF>";
			else $stream2="<tr class=rowcontent>";
		}else{
			$stream2="";
		}
		//$stream1.="<td align=center>".$key2."</td>";
				
				foreach($sortirASS1[$key1['def']][$key2] as $c =>$key3) {
				   $gab[$key1['def']][$key2].=$key3.",";
				   
				}
				$stream2.="<td align=center>".$gab[$key1['def']][$key2]."</td>";
		if ($hut > 1){
		
		$stream1.=$stream2."<td align=center>".number_format($key2,0,',','.')."</td>
				<td align=right>".number_format($totsortir1[$key1['def']][$key2],0,',','.')."</td>
				<td align=right>".number_format($totsortir2[$key1['def']][$key2],2,',','.')."</td></tr>
				";
		
		}else{
		
		$stream1.=$stream2."<td align=center>".number_format($key2,0,',','.')."</td>
				<td align=right>".number_format($totsortir1[$key1['def']][$key2],0,',','.')."</td>
				<td align=right>".number_format($totsortir2[$key1['def']][$key2],2,',','.')."</td>
				";
		}
		

		

				
				
	}	
	
	$stream.="<td align=center rowspan=".$hut.">".$key1['def']."</td>";
	
	$stream.=$stream1;
	$hut=0;
					
}else{
        $stream.="<td colspan=5 align=center>Data Tidak Ada</td>";
}
/*	

$res=mysql_query($str);	
$benortran=0;
$hargattbs=0;
$tot=0;
$no=0;
while($bar=mysql_fetch_assoc($res))
{
	if(substr($kdsup,3,1)=='E')
	 {
	   $bar['kodecustomer']='INTERNAL';
	  }
	  
	if ($bar['nma'] !="Diluar Range"){
	$bjr=$bar['bjr'];
	//echo $bjr._;
	
	// sesudah ada range baru 
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
	// akhir sesudah ada range baru
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
	  
		
	
		$no+=1;
		
		$beratnormal=$bar['netto']-$bar['kgpotsortasi'];//and supplierid='".$bar['kodecustomer']."'
		$total=$c['harga']*$beratnormal;//<td>".$optnamacostumer[$bar['kodecustomer']]."</td>
		
		//echo $c['harga'].___;
		if($c['harga']==0)
				{
					$simpan[$no][rupiah]="Blm input harga";
				}
				else
				{
					$simpan[$no][rupiah]=$c['harga'];
				}
		
		$simpan[$no][def2]=$bar['nma'];
		$simpan[$no][waktu]=tanggalnormal($bar['tanggal']);
		$simpan[$no][benor]=$beratnormal;
		$simpan[$no][total]=$total;
	
	}
}






foreach($simpan as $c=>$key) {
//if($proses=='excel')$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
		//			else $stream.="<tr class=rowcontent>";
	//	$stream.="
		//		<td align=center>".$key['def2']."</td>
			//	<td align=center>".$key['waktu']."</td>
				//<td align=center>".$key['rupiah']."</td>
				//<td align=right>".number_format($key['benor'],2)." Kg</td>
				//<td align=right>".number_format($key['total'])."</td></tr>
				//";
				$total1+=$key['benor'];
				$total2+=$key['total'];
				$sortirASS[$key['def2']][def]=$key['def2'];
				$sortir2[$key['def2']][$key['waktu']][$key['rupiah']][sortgl]=$key['waktu'];
				
				$sortirASS1[$key['def2']][$key['rupiah']][$key['waktu']]=$key['waktu'];
				
				
				
				$sortir2[$key['def2']][$key['waktu']][$key['rupiah']][sorrupiah]=$key['rupiah'];
				
				$sortirASS2[$key['def2']][$key['rupiah']]=$key['rupiah'];
				
				$sortir3[$key['def2']][$key['rupiah']][sorkg]=$key['benor'];
				$sortir3[$key['def2']][$key['rupiah']][sorrupiah]=$key['total'];
				
				$totsortir1[$key['def2']][$key['rupiah']]+=$sortir3[$key['def2']][$key['rupiah']][sorkg];
				$totsortir2[$key['def2']][$key['rupiah']]+=$sortir3[$key['def2']][$key['rupiah']][sorrupiah];
				
				
				
				
				
				
				
}


$hut=0;
if(!empty($sortirASS)) foreach($sortirASS as $c=>$key1) {
if($proses=='excel')$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
		else $stream.="<tr class=rowcontent>";
	$stream1="";
	$stream2="";
	foreach($sortirASS2[$key1['def']] as $c =>$key2) {
	$hut++;
	
		if ($hut > 1){
			if($proses=='excel')$stream2="<tr bgcolor=#FFFFFF>";
			else $stream2="<tr class=rowcontent>";
		}else{
			$stream2="";
		}
		//$stream1.="<td align=center>".$key2."</td>";
				
				foreach($sortirASS1[$key1['def']][$key2] as $c =>$key3) {
				   $gab[$key1['def']][$key2].=$key3.",";
				   
				}
				$stream2.="<td align=center>".$gab[$key1['def']][$key2]."</td>";
		if ($hut > 1){
		
		$stream1.=$stream2."<td align=center>".number_format($key2,0,',','.')."</td>
				<td align=right>".number_format($totsortir1[$key1['def']][$key2],0,',','.')."</td>
				<td align=right>".number_format($totsortir2[$key1['def']][$key2],2,',','.')."</td></tr>
				";
		
		}else{
		
		$stream1.=$stream2."<td align=center>".number_format($key2,0,',','.')."</td>
				<td align=right>".number_format($totsortir1[$key1['def']][$key2],0,',','.')."</td>
				<td align=right>".number_format($totsortir2[$key1['def']][$key2],2,',','.')."</td>
				";
		}
		

		

				
				
	}	
	
	$stream.="<td align=center rowspan=".$hut.">".$key1['def']."</td>";
	
	$stream.=$stream1;
	$hut=0;
					
}else{
        $stream.="<td colspan=5 align=center>Data Tidak Ada</td>";
}

*/
$stream.="<thead><tr>
<td align=center colspan=3>Total</td>
<td align=right>".number_format($total1,0,',','.')."</td>
<td align=right>".number_format($total2,2,',','.')."</td>
</tr>";
/*

if(!empty($hslakhir)) foreach($hslakhir as $c=>$key) {
		if($proses=='excel')$stream.="<tr bgcolor=#FFFFFF>";//<td align=right>".$bar['kmjams']."</td>
		else $stream.="<tr class=rowcontent>";
	$stream.="
			<td align=center>".$key['def2']."</td>
			<td align=center>".$key[$key['def2']]."</td>
			<td align=center>".$key[$key['def2']][1100]['rupiah']."</td>
			<td align=right>".number_format($key['benor'],2)." Kg</td>
			<td align=right>".number_format($key['total'])."</td></tr>
			";
}else{
        $stream.="<td colspan=5 align=center>Data Tidak Ada</td>";
}

		$stream.="<thead><tr>
					<td align=center colspan=3>Total</td>
					<td align=right>".number_format($total1,2)." Kg</td>
					<td align=right>Rp. ".number_format($total2)."</td>
				</tr></tbody></table>";

	
*/	
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
	$stream.="<tr bgcolor=#FFFFFF>
					<td align=center colspan=4></td>
					<td align=right></td>
				</tr >";
				$stream.="<tr bgcolor=#FFFFFF>
					<td align=center colspan=4></td>
					<td align=right></td>
				</tr>";
	$stream.="<thead><tr>
					<td align=center colspan=4>Jumlah yang dibayar</td>
					<td align=right></td>
				</tr>";
	$stream.="<tr bgcolor=#FFFFFF>
					<td align=center colspan=5></td>
					
				</tr>";			
	$stream.="<tr bgcolor=#FFFFFF>
	<td align=center colspan=2>a/n : ".$atasnm."</td>
	<td align=center></td>
	<td align=center colspan=2>Jakarta, ".date('d M Y')."</td>
	</tr>";			
	$stream.="<tr bgcolor=#FFFFFF>
	<td align=center colspan=2>".$bank."</td>
	<td align=center colspan=3></td>
	</tr>";			
	$stream.="<tr bgcolor=#FFFFFF>
	<td align=center colspan=2>".$kota."</td>
	<td align=center colspan=3></td>
	</tr>";	
	$stream.="<tr bgcolor=#FFFFFF>
	<td align=center colspan=2>rek : ".$rek."</td>
	<td align=center></td>
	<td align=center colspan=2>( ".$_SESSION['empl']['name']." )</td>
	</tr></tbody></table>";		
	
	
				
		
		$tglSkrg=date("Ymd");
		$nop_="LAPORAN_SUMARY_HARGA_TBS_SUPPLIER".$tglSkrg;
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
	
}
	
	

?>