<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$method=$_POST['method'];

		
		

switch($method) {
	
	case 'Cariken':
	$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	if ($_POST['kodekategori']=='1'){
		$optKendaran.="<option value='AB'>SEMUA ALAT BERAT</option>";
		$vhc='AB';
	}else{
		$optKendaran.="<option value='KD'>SEMUA KENDARAAN</option>";
		$vhc='KD';
	}
		
    if ($_POST['tipekategori']=='1'){
		$sqlB ="select jenisvhc as kode,namajenisvhc as nama from `".$dbname."`.`vhc_5jenisvhc`   where  jenisvhc like '".$vhc."%' ";
	}else{
		$sqlB ="select kodevhc as kode,nopol as nama from `".$dbname."`.`vhc_5master` where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisvhc like '".$vhc."%' ";
	}
	
				$qryB = mysql_query($sqlB) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qryB))
		{
			$optKendaran.="<option value=".$data['kode']." >".$data['nama']." </option>";
		}
			
	
		echo $optKendaran;
	break;
	case 'CariKeg':
		
		$optKegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sqlA ="SELECT kodekegiatan,namakegiatan FROM ".$dbname.".`vhc_kegiatan_vw` where kelompok='".$_POST['kategori']."' or kelompok='0' order by kodekegiatan asc";
		$qryA = mysql_query($sqlA) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qryA))
			{
				$optKegiatan.="<option value=".$data['kodekegiatan']." >".$data['kodekegiatan'].": ".$data['namakegiatan']."</option>";
			}	
			
	
		echo $optKegiatan;
	break;
	case 'CariSat':
		$optKdvhc='';
		$queryKaryA ="select if(kelompok='2',concat(satuan,'/KMH'),concat(satuan,'/HM')) as sat from `".$dbname."`.`vhc_kegiatan_vw` where kodekegiatan='".$_POST['kodekegiatan']."' and kelompok='".$_POST['kodekategori']."' limit 1 ";
		$query=mysql_query($queryKaryA) or die(mysql_error());
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc=$res['sat'];
		}
		if (strtoupper($optKdvhc)=="HM/HM"){
			echo "HM";	
		}else{
			echo $optKdvhc;
		}
		
	break;
 
	case 'insert':
		$kdOrg =isset($_POST['kd_org'])?$_POST['kd_org']:'';
		$kdTipe =isset($_POST['kdTipe'])?$_POST['kdTipe']:'';
		$kdKat =isset($_POST['kdKat'])?$_POST['kdKat']:'';
		$kdKeg =isset($_POST['kdKeg'])?$_POST['kdKeg']:'';
		$kdKen =isset($_POST['kdKen'])?$_POST['kdKen']:'';
		$uadb =isset($_POST['uadb'])?$_POST['uadb']:'';
		$satuan =isset($_POST['satuan'])?$_POST['satuan']:'';
		
		


		$sRicek="select * from ".$dbname.".vhc_kendaraan_norma where `kategori`='".$kdKat."' and `kodeorg`='".$kdOrg."' and `kegiatan`='".$kdKeg."' and `kendaraan`='".$kdKen."'   ";
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		if($rRicek>0) {
			$sDel="delete from ".$dbname.".vhc_kendaraan_norma
					where `kategori`='".$kdKat."' and `kodeorg`='".$kdOrg."' and `kegiatan`='".$kdKeg."' and `kendaraan`='".$kdKen."'  ";	    
			if(mysql_query($sDel)) {
				$sDel2="insert into ".$dbname.".vhc_kendaraan_norma ( `kategori`, `kodeorg`,`kegiatan`, `kendaraan`,`jenis`,  `satuan`, `norma`)
					values ('".$kdKat."','".$kdOrg."','".$kdKeg."','".$kdKen."','".$kdTipe."','".$satuan."','".$uadb."')";
				if(mysql_query($sDel2))
					echo"";
				else
					echo " Gagal,".addslashes(mysql_error($conn));
			} else {
				echo "Gagal,".addslashes(mysql_error($conn));
			}
		} else {
			$sDel2="insert into ".$dbname.".vhc_kendaraan_norma (`kategori`, `kodeorg`,`kegiatan`, `kendaraan`,`jenis`,  `satuan`, `norma`)
					values ('".$kdKat."','".$kdOrg."','".$kdKeg."','".$kdKen."','".$kdTipe."','".$satuan."','".$uadb."')";
			if(mysql_query($sDel2))
			echo"";
			else
			echo "Gagal,".addslashes(mysql_error($conn));
		}
	break;
	case'loadData':
		$no=0;
		$str="SELECT kodeorg,nmorganisasi,kategori,kegiatan, concat(kegiatan,': ',nmkeg) as nmkeg,
		kendaraan, nmken, 
		format(norma,2) AS norma,
		satuan FROM ".$dbname.".vhc_kendaraan_norma_vw  where  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kegiatan asc" ;
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{

				
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td>".$bar1['nmorganisasi']."</td>";
			$tab.="<td>".$bar1['nmkeg']."</td>";
			$tab.="<td align=center>".$bar1['nmken']."</td>";
			$tab.="<td align=center>".$bar1['satuan']."</td>";
			$tab.="<td align=right>".$bar1['norma']."</td>";
			$tab.="<td align=center>

				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodeorg']."#".$bar1['kategori']."#".$bar1['kegiatan']."#".$bar1['kendaraan']."');\"></td></tr>";
		echo $tab;
		}

	case 'delete':
		$kode =isset($_POST['kode'])?$_POST['kode']:'';
		$tab="delete from ".$dbname.".vhc_kendaraan_norma where concat(`kodeorg`,'#',`kategori`,'#', `kegiatan`,'#', `kendaraan`)='".$kode."'";
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	default:
}

?>