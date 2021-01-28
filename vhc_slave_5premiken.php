<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$method=$_POST['method'];

		
switch($method) {
	case 'Cariken':

		$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$optKendaran.="<option value='KD'>SEMUA KENDARAAN</option>";
    if ($_POST['tipekategori']=='1'){
		$sqlB ="select jenisvhc as kode,namajenisvhc as nama from `".$dbname."`.`vhc_5jenisvhc`   where  jenisvhc like 'KD%' ";
	}else{
		$sqlB ="select kodevhc as kode,nopol as nama from `".$dbname."`.`vhc_5master` where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisvhc like 'KD%' ";
	}
	
				$qryB = mysql_query($sqlB) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qryB))
		{
			$optKendaran.="<option value=".$data['kode']." >".$data['nama']." </option>";
		}
			
	
		echo $optKendaran;
	break;
	case 'insert':
	
		$kdTipe=isset($_POST['kdTipe'])?$_POST['kdTipe']:'';
		$kdOrg =isset($_POST['kd_org'])?$_POST['kd_org']:'';
		$kdKeg =isset($_POST['kdKeg'])?$_POST['kdKeg']:'';
		$kdKen =isset($_POST['kdKen'])?$_POST['kdKen']:'';
		$kdpremi =isset($_POST['kdpremi'])?$_POST['kdpremi']:'';
		$uadb =isset($_POST['uadb'])?$_POST['uadb']:'';
		$ualb =isset($_POST['ualb'])?$_POST['ualb']:'';
		$uains1 =isset($_POST['uains1'])?$_POST['uains1']:'';
		$uains2 =isset($_POST['uains2'])?$_POST['uains2']:'';
		$satuan =isset($_POST['satuan'])?$_POST['satuan']:'';
		
		
		$sRicek="select * from ".$dbname.".vhc_kendaraan_premi where `kategori`='2' and `kodeorg`='".$kdOrg."' and `kegiatan`='".$kdKeg."' and `kendaraan`='".$kdKen."'  and  `operator`='".$kdpremi."' ";
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		if($rRicek>0) {
			$sDel="delete from ".$dbname.".vhc_kendaraan_premi
					where `kategori`='2' and `kodeorg`='".$kdOrg."' and `kegiatan`='".$kdKeg."' and `kendaraan`='".$kdKen."'  and  `operator`='".$kdpremi."' ";	    
			if(mysql_query($sDel)) {
				$sDel2="insert into ".$dbname.".vhc_kendaraan_premi ( `kategori`, `kodeorg`,`kegiatan`, `kendaraan`, `operator`, `satuan`, `insentif_1`, `insentif_2`, `b_trep_1`, `lb_trep_1`, `b_trep_2`, `lb_trep_2`, `b_trep_3`, `lb_trep_3`, `b_trep_4`, `lb_trep_4`,`jenis`)
					values ('2','".$kdOrg."','".$kdKeg."','".$kdKen."','".$kdpremi."','".$satuan."',0,0,'".$uadb."',0,'".$ualb."', 0,'".$uains1."',0,'".$uains2."',0,'".$kdTipe."')";
				if(mysql_query($sDel2))
					echo"";
				else
					echo " Gagal,".addslashes(mysql_error($conn));
			} else {
				echo "Gagal,".addslashes(mysql_error($conn));
			}
		} else {
			$sDel2="insert into ".$dbname.".vhc_kendaraan_premi (`kategori`, `kodeorg`, `kegiatan`, `kendaraan`, `operator`, `satuan`, `insentif_1`, `insentif_2`, `b_trep_1`, `lb_trep_1`, `b_trep_2`, `lb_trep_2`, `b_trep_3`, `lb_trep_3`, `b_trep_4`, `lb_trep_4`,`jenis`)
					values ('2','".$kdOrg."','".$kdKeg."','".$kdKen."','".$kdpremi."','".$satuan."',0,0,'".$uadb."',0,'".$ualb."', 0,'".$uains1."',0,'".$uains2."',0,'".$kdTipe."')";
			if(mysql_query($sDel2))
			echo"";
			else
			echo "Gagal,".addslashes(mysql_error($conn));
		}



	break;
	case 'loadData':
		$no=0;
		$str="SELECT kodeorg,namaorganisasi ,kegiatan, concat(kegiatan,': ',nmkeg) as nmkeg,
		kendaraan,nmken, operator,nmort, 
		b_trep_1,b_trep_2, b_trep_3 ,b_trep_4,
		kategori,satuan FROM ".$dbname.".vhc_kendaraan_premi_vw  where kategori='2' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kegiatan asc " ;
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
				
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=center>".$bar1['kodeorg']."</td>";
			$tab.="<td>".$bar1['nmkeg']."</td>";
			$tab.="<td align=center>".$bar1['nmken']."</td>";
			$tab.="<td align=center>".$bar1['nmort']."</td>";
			$tab.="<td align=center>".$bar1['satuan']."</td>";
			$tab.="<td align=right>".$bar1['b_trep_1']."</td>";
			$tab.="<td align=right>".$bar1['b_trep_2']."</td>";
			$tab.="<td align=right>".$bar1['b_trep_3']."</td>";
			$tab.="<td align=right>".$bar1['b_trep_4']."</td>";
			$tab.="<td align=center>
				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodeorg']."#".$bar1['kategori']."#".$bar1['kegiatan']."#".$bar1['kendaraan']."#".$bar1['operator']."');\"></td>";
		echo $tab;
		}
		break;

	case 'delete':
		$kode =isset($_POST['kode'])?$_POST['kode']:'';
		$tab="delete from ".$dbname.".vhc_kendaraan_premi where concat(`kodeorg`,'#',`kategori`,'#', `kegiatan`,'#', `kendaraan`,'#', `operator`)='".$kode."'";
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