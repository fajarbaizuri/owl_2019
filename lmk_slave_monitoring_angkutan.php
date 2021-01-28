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
$_POST['KMonitor']==''?$KMonitor=$_GET['KMonitor']:$KMonitor=$_POST['KMonitor'];

#$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];

$qwe=explode('-',$periode);
$tahun=$qwe[0]; $bulan=$qwe[1];
$jumhari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
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
if($KMonitor=='')
{
    exit("Error: Monitoring tidak boleh kosong");
}


if ($KMonitor==4){
	$AZ=1;
	$panen="SELECT  date_format(a.tanggal,'%Y-%m-%d') as tanggal,a.jammasuk,a.jamkeluar,a.beratbersih,a.notransaksi,UPPER(a.nokendaraan) as nokendaraan,UPPER(a.supir) as supir,a.nospb,b.nospb as nospbkebun,b.tanggal as tanggalkebun,b.nopolisi,b.operator,c.namakaryawan 
	FROM ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".kebun_spbht b ON a.nospb=b.nospb left join ".$dbname.".datakaryawan c ON b.operator=c.karyawanid WHERE a.tanggal LIKE '".$periode."%' AND a.kodeorg LIKE '".$kdUnit."' and a.kodebarang='40000003' order by 
	a.nokendaraan asc ,a.tanggal asc";
	$query=mysql_query($panen) or die(mysql_error($conn));
	while($res=mysql_fetch_assoc($query)){
		$Aksi[$AZ]['A']=$res['tanggal'];
		$Aksi[$AZ]['B']=$res['jammasuk'];
		$Aksi[$AZ]['C']=$res['jamkeluar'];
		$Aksi[$AZ]['D']=$res['beratbersih'];
		$Aksi[$AZ]['E']=$res['notransaksi'];
		$Aksi[$AZ]['F']=$res['nokendaraan'];
		$Aksi[$AZ]['G']=$res['supir'];
		$Aksi[$AZ]['H']=$res['nospbkebun'];
		$Aksi[$AZ]['I']=$res['nopolisi'];
		$Aksi[$AZ]['J']=$res['namakaryawan'];
		$AZ++;
	} 
}else if($KMonitor==5 || $KMonitor==6 || $KMonitor==7){
	 $panen="SELECT Count(a.notransaksi)as rit, DATE_FORMAT(a.tanggal,'%e') as tanggal,sum(a.jumlahtandan1) as jjg,sum(a.beratbersih) as kg,UPPER(a.nokendaraan) as kendaraan,UPPER(a.supir)as supir,b.nopolisi FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".kebun_spbht b
	 ON a.nospb=b.nospb WHERE a.`tanggal` LIKE '".$periode."%' AND a.`kodeorg` LIKE '".$kdUnit."' and a.kodebarang='40000003' group by DATE_FORMAT(a.tanggal,'%Y-%m-%d'),UPPER(b.nopolisi) order by b.nopolisi asc ,a.tanggal asc";
	$query=mysql_query($panen) or die(mysql_error($conn));
	while($res=mysql_fetch_assoc($query)){
		$Aksi[$res['nopolisi']][$res['tanggal']]['Kg']=$res['kg'];
		$Aksi[$res['nopolisi']][$res['tanggal']]['Trip']=$res['rit'];	
	}   
}else{
	$panen="SELECT Count(a.notransaksi)as rit, DATE_FORMAT(a.tanggal,'%e') as tanggal,sum(a.jumlahtandan1) as jjg,sum(a.beratbersih) as kg,UPPER(a.nokendaraan) as kendaraan,UPPER(a.supir)as supir,b.nopolisi,b.operator,c.namakaryawan FROM ".$dbname.".pabrik_timbangan a 
	left join ".$dbname.".kebun_spbht b ON a.nospb=b.nospb left join ".$dbname.".datakaryawan c ON b.operator=c.karyawanid WHERE a.`tanggal` LIKE '".$periode."%' AND a.`kodeorg` LIKE '".$kdUnit."' and a.kodebarang='40000003' group by DATE_FORMAT(a.tanggal,'%Y-%m-%d'),
	UPPER(b.nopolisi),UPPER(b.operator) order by b.nopolisi asc ,a.tanggal asc";
	$query=mysql_query($panen) or die(mysql_error($conn));
	while($res=mysql_fetch_assoc($query)){
		$Aksi[$res['operator']][$res['nopolisi']][$res['tanggal']]['Kg']=$res['kg'];
		$Aksi[$res['operator']][$res['nopolisi']][$res['tanggal']]['Trip']=$res['rit'];	
	}   
}

//echo $panen;

$G=1;
if ($KMonitor!=5 && $KMonitor!=6 && $KMonitor!=7){
	$panenA="SELECT sum(a.jumlahtandan1) as jjg,sum(a.beratbersih) as kg,UPPER(a.nokendaraan) as kendaraan,UPPER(a.supir)as supir,b.nopolisi,b.operator,c.namakaryawan FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".kebun_spbht b ON a.nospb=b.nospb 
	left join ".$dbname.".datakaryawan c ON b.operator=c.karyawanid WHERE a.`tanggal` LIKE '".$periode."%' AND a.`kodeorg` LIKE '".$kdUnit."' and a.kodebarang='40000003' group by DATE_FORMAT(a.tanggal,'%Y-%m'),UPPER(b.nopolisi),UPPER(b.operator) order by 
	b.nopolisi asc ,a.tanggal asc"; 
	$queryA=mysql_query($panenA) or die(mysql_error($conn));
	while($resA=mysql_fetch_assoc($queryA)){
		$AksiA[$G]['Kendaraan']=$resA['nopolisi'];
		$AksiA[$G]['Supir']=$resA['operator'];
		$AksiA[$G]['Kg']=$resA['kg'];
		$NMDRIVER[$resA['operator']]=$resA['namakaryawan'];
		$G++;
	}   
}else{
	$panenA="SELECT sum(a.jumlahtandan1) as jjg,sum(a.beratbersih) as kg,UPPER(a.nokendaraan) as kendaraan,UPPER(a.supir)as supir,b.nopolisi FROM ".$dbname.".pabrik_timbangan a left join ".$dbname.".kebun_spbht b ON a.nospb=b.nospb WHERE a.`tanggal` LIKE '".$periode."%' AND
	a.`kodeorg` LIKE '".$kdUnit."' and a.kodebarang='40000003' group by DATE_FORMAT(a.tanggal,'%Y-%m'),UPPER(b.nopolisi) order by b.nopolisi asc ,a.tanggal asc";
	$queryA=mysql_query($panenA) or die(mysql_error($conn));
	while($resA=mysql_fetch_assoc($queryA)){
		$AksiA[$G]['Kendaraan']=$resA['nopolisi'];
		$AksiA[$G]['Kg']=$resA['kg'];
		$G++;
	}   
}

//echo $panenA;


$brdr=0;
$bgcoloraja='';
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tabA="
    <table>
    <tr><td colspan=5 align=left><b>MONITORING INTERNAL ANGKUTAN PANEN</b></td><td colspan=2 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=5 align=left>Kebun : ".$optNmOrg[$kdUnit]." </td></tr>";
    $tabA.="<tr><td colspan=5 align=left>&nbsp;</td></tr>
    </table>";
	
	
}
   if($KMonitor==1){
	   $JudulA="Kapasitas Angkutan Pertanggal (Kg)";
	   $SatuanA="Kg";
   }else if($KMonitor==2){
	   $JudulA="Trip Angkutan Pertanggal (rit)";
	   $SatuanA="rit";
   }else if($KMonitor==3){	  
		$JudulA="Rata - Rata Angkutan Pertrip Pertanggal (Kg/rit)";
		$SatuanA="Kg/rit";
   }else if($KMonitor==5){	  
		$JudulA="Kapasitas Angkutan Perkendaraan Pertanggal (Kg)";
		$SatuanA="Kg";
   }else if($KMonitor==6){
	   $JudulA="Trip Angkutan Perkendaraan Pertanggal (rit)";
	   $SatuanA="rit";
   }else if($KMonitor==7){	  
		$JudulA="Rata - Rata Angkutan Pertrip Perkendaraan Pertanggal (Kg/rit)";
		$SatuanA="Kg/rit";
   }
	$AC=$jumhari;
	
	  $tabA.="<BR><table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
	if ($KMonitor!=4){
		$tabA.="<tr>
		<td rowspan=2 ".$bgcoloraja." >No.</td>
		<td rowspan=2 ".$bgcoloraja." >Kendaraan</td>";
		if ($KMonitor!=5 && $KMonitor!=6 && $KMonitor!=7){
			$tabA.="<td rowspan=2  ".$bgcoloraja." >Sopir</td>";
		}
		$tabA.="
		<td colspan=".$AC." ".$bgcoloraja." >".$JudulA."</td>
		<td rowspan=2  ".$bgcoloraja." >Total Perbulan (".$SatuanA.")</td>
		</tr>";
		$tabA.="<tr>";
		for ($i=1; $i < $jumhari+1; $i++) { 
			$tabA.= "<td  ".$bgcoloraja." >".$i."</td>";
		}
		$tabA.="</tr>";
	}else{
		$tabA.="<tr>
		<td rowspan=2 ".$bgcoloraja." >No.</td>
		<td rowspan=2 ".$bgcoloraja." >Tanggal</td>
		<td rowspan=2  ".$bgcoloraja." >Jam Masuk</td>
		<td rowspan=2  ".$bgcoloraja." >Jam Keluar</td>
		<td rowspan=2  ".$bgcoloraja." >Berat Bersih</td>
		<td colspan=3 ".$bgcoloraja." >Timbangan Pabrik</td>
		<td colspan=3 ".$bgcoloraja." >Kebun</td>
		</tr>";
		$tabA.="<tr>
		<td ".$bgcoloraja." >Tiket No.</td>
		<td ".$bgcoloraja." >Plat No.</td>
		<td  ".$bgcoloraja." >Sopir</td>
		<td ".$bgcoloraja." >SPB No.</td>
		<td ".$bgcoloraja." >Plat No.</td>
		<td  ".$bgcoloraja." >Sopir</td>
		</tr>";
	}
	
    $tabA.="</thead><tbody>";
	if ($KMonitor!=4){
	
		
        foreach($AksiA as $lsBlok=>$value)
        {
				$tabA.="<tr class=rowcontent>";
				$tabA.="<td align=center >".$lsBlok."</td>";
				$tabA.="<td align=left  >".$value['Kendaraan']."</td>";
				if ( $KMonitor!=5 && $KMonitor!=6 && $KMonitor!=7){ 
				$tabA.="<td align=left  >".$NMDRIVER[$value['Supir']]."</td>";
				}
				$TotalA=0;
				$TotalB=0;
				for ($i=1; $i < $jumhari+1; $i++) { 
					if ( $KMonitor!=5 && $KMonitor!=6 && $KMonitor!=7){ 
					$GT[$i]['Kg']+=$Aksi[$value['Supir']][$value['Kendaraan']][$i]['Kg'];
					number_format($Aksi[$value['Supir']][$value['Kendaraan']][$i]['Kg'])==0?$KgA='':$KgA=number_format($Aksi[$value['Supir']][$value['Kendaraan']][$i]['Kg']);
					$GT[$i]['Trip']+=$Aksi[$value['Supir']][$value['Kendaraan']][$i]['Trip'];
					number_format($Aksi[$value['Supir']][$value['Kendaraan']][$i]['Trip'])==0?$RitA='':$RitA=number_format($Aksi[$value['Supir']][$value['Kendaraan']][$i]['Trip']);
					$KRA=number_format($Aksi[$value['Supir']][$value['Kendaraan']][$i]['Kg']/$Aksi[$value['Supir']][$value['Kendaraan']][$i]['Trip']);
					$KRA==0?$KRA='':$KRA=$KRA;
					if ($KMonitor==1){
						$tabA.="<td align=right>".$KgA."</td>";
					}else if ($KMonitor==2){
						$tabA.="<td align=center >".$RitA."</td>";
					}else if ($KMonitor==3){	  
						$tabA.="<td align=right >".$KRA."</td>";
					}
					$TotalA+=$Aksi[$value['Supir']][$value['Kendaraan']][$i]['Kg'];
					$TotalB+=$Aksi[$value['Supir']][$value['Kendaraan']][$i]['Trip'];
					}else{
					$GT[$i]['Kg']+=$Aksi[$value['Kendaraan']][$i]['Kg'];
					number_format($Aksi[$value['Kendaraan']][$i]['Kg'])==0?$KgA='':$KgA=number_format($Aksi[$value['Kendaraan']][$i]['Kg']);
					$GT[$i]['Trip']+=$Aksi[$value['Kendaraan']][$i]['Trip'];
					number_format($Aksi[$value['Kendaraan']][$i]['Trip'])==0?$RitA='':$RitA=number_format($Aksi[$value['Kendaraan']][$i]['Trip']);
					$KRA=number_format($Aksi[$value['Kendaraan']][$i]['Kg']/$Aksi[$value['Kendaraan']][$i]['Trip']);
					$KRA==0?$KRA='':$KRA=$KRA;
					if ($KMonitor==5){
						$tabA.="<td align=right>".$KgA."</td>";
					}else if ($KMonitor==6){
						$tabA.="<td align=center >".$RitA."</td>";
					}else if ($KMonitor==7){	  
						$tabA.="<td align=right >".$KRA."</td>";
					}
					$TotalA+=$Aksi[$value['Kendaraan']][$i]['Kg'];
					$TotalB+=$Aksi[$value['Kendaraan']][$i]['Trip'];
					}
				}

				$TotalC=number_format($TotalA/$TotalB);
				$TotalC==0?$TotalC='':$TotalC=$TotalC;
				number_format($TotalA)==0?$TotalA='':$TotalA=number_format($TotalA);
				number_format($TotalB)==0?$TotalB='':$TotalB=number_format($TotalB);
					if ($KMonitor==1 || $KMonitor==5){
						$tabA.="<td align=right>".$TotalA."</td>";
					}else if ($KMonitor==2 || $KMonitor==6){
						$tabA.="<td align=center >".$TotalB."</td>";
					}else if ($KMonitor==3 || $KMonitor==7){	  
						$tabA.="<td align=right>".$TotalC."</td>";
					}
					$tabA.="</tr>"; 
				
        }
					$TotalA2=0;
					$TotalB2=0;
					$tabA.="<tr  class=rowcontent>";
					if ($KMonitor!=5 && $KMonitor!=6 && $KMonitor!=7){
					$tabA.="<td colspan=3><b>Total</td>";
					}else{
						$tabA.="<td colspan=2><b>Total</td>";
					}
					for ($j=1; $j < $jumhari+1; $j++) { 
						$TotalA2+=$GT[$j]['Kg'];
						$TotalB2+=$GT[$j]['Trip'];
						number_format($GT[$j]['Kg'])==0?$ST='':$ST=number_format($GT[$j]['Kg']);
						number_format($GT[$j]['Trip'])==0?$KW='':$KW=number_format($GT[$j]['Trip']);
						number_format($GT[$j]['Kg']/$GT[$j]['Trip'])==0?$SK='':$SK=number_format($GT[$j]['Kg']/$GT[$j]['Trip']);
						
							if($KMonitor==1 || $KMonitor==5){
								$tabA.="<td align=right><b>".$ST."</td>";
							}else if($KMonitor==2 || $KMonitor==6){
								$tabA.="<td align=center ><b>".$KW."</td>";
							}else if($KMonitor==3 || $KMonitor==7){		  
								$tabA.="<td align=right><b>".$SK."</td>";
							}
						
						
						
						
						
					}
							if($KMonitor==1 || $KMonitor==5){
								$tabA.="<td align=right><b>".number_format($TotalA2)."</td>";
							}else if($KMonitor==2 || $KMonitor==6){
								$tabA.="<td align=center ><b>".number_format($TotalB2)."</td>";
							}else if($KMonitor==3 || $KMonitor==7){	
								$tabA.="<td align=right><b>".number_format($TotalA2/$TotalB2)."</td>";
							}
					
					$tabA.="</tr>";
	    }else{
			$totalDD=0;
			 foreach($Aksi as $lsBlok=>$value)
        {
			$tabA.="<tr class=rowcontent>";
				$tabA.="<td align=center >".$lsBlok."</td>";
				$tabA.="<td align=center >".$value['A']."</td>";
				$tabA.="<td align=center >".$value['B']."</td>";
				$tabA.="<td align=center >".$value['C']."</td>";
				$tabA.="<td align=right >".number_format($value['D'])."</td>";
				$tabA.="<td align=center >".$value['E']."</td>";
				$tabA.="<td align=center >".$value['F']."</td>";
				$tabA.="<td align=left >".$value['G']."</td>";
				$tabA.="<td align=center >".$value['H']."</td>";
				$tabA.="<td align=center >".$value['I']."</td>";
				$tabA.="<td align=left >".$value['J']."</td>";
			$tabA.="</tr>";	
			$totalDD+=$value['D'];
		}	
			$tabA.="<tr class=rowcontent>";
				$tabA.="<td colspan=4 align=center ><b>Total</td>";
				$tabA.="<td align=right ><b>".number_format($totalDD)."</td>";
				$tabA.="<td colspan=6 align=center ><b></td>";
			$tabA.="</tr>";
			
	}			
					$tabA.="</tbody></table>";
		
		
		
       
switch($proses)
{
	case'preview':
	echo $tabA;
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$data=$tabA;
		$tglSkrg=date("Ymd");
		$nop_="MONITORING_ANGKUTAN_PANEN_".$tglSkrg;
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