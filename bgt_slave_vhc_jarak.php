<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$method=$_POST['method'];

$thnbudget=isset($_POST['thnbudget'])?$_POST['thnbudget']:'';
$kdorg=isset($_POST['kdorg'])?$_POST['kdorg']:'';
$pengguna=isset($_POST['pengguna'])?$_POST['pengguna']:'';
$kdvhc=isset($_POST['kdvhc'])?$_POST['kdvhc']:'';
$jarak=isset($_POST['jarak'])?$_POST['jarak']:'';

$thnclose=isset($_POST['thnclose'])?$_POST['thnclose']:'';
$lkstgs=isset($_POST['lkstgs'])?$_POST['lkstgs']:'';
$thnttp=isset($_POST['thnttp'])?$_POST['thnttp']:'';
$kdorgHeader=isset($_POST['kdorgHeader'])?$_POST['kdorgHeader']:'';
$thnbudgetHeader=isset($_POST['thnbudgetHeader'])?$_POST['thnbudgetHeader']:'';

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

switch($method)
{
	
	case 'insert':
	
		$aCek="select distinct tutup from ".$dbname.".bgt_vhc_jarak where tahunbudget='".$thnbudget."' and afdeling='".$kdorg."' ";
		$bCek=mysql_query($aCek) or die(mysql_error());
		//exit("error:$aCek");
		while ($cCek=mysql_fetch_assoc($bCek))
		{
			
			if($cCek['tutup']==1)
			{
				echo "warning : Input untuk tahun ".$thnbudget." dengan Organisasi ".$optNm[$kdorg]." tidak bisa dilakukan karena telah di tutup";
				exit();	
			}
		}
	
		$str="insert into ".$dbname.".bgt_vhc_jarak (`tahunbudget`,`afdeling`,`penggunaan`,`kodevhc`,`jarak`)
		values ('".$thnbudget."','".$kdorg."','".$pengguna."','".$kdvhc."','".$jarak."')";
		//exit("Error.$sDel2");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	
	case'loadData':
		$tmbh='';
                if($thnbudgetHeader!='')
                {
                    $tmbh=" and tahunbudget='".$thnbudgetHeader."' ";
                }
				
		$tmbh2='';
                if($kdorgHeader!='')
                {
                    $tmbh2=" and afdeling='".$kdorgHeader."' ";
                }

		$no=0;
		$str="select * from ".$dbname.".bgt_vhc_jarak where afdeling like '%".$_SESSION['empl']['lokasitugas']."%' ".$tmbh." ".$tmbh2." order by tahunbudget desc ";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{	
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=right>".$bar1['tahunbudget']."</td>";
			$tab.="<td>".$bar1['afdeling']."</td>";
			$tab.="<td>".$bar1['penggunaan']."</td>";
			$tab.="<td>".$bar1['kodevhc']."</td>";
			$tab.="<td align=right>".$bar1['jarak']."</td>";
			if($bar1['tutup']==1)
			{
				$tab.="<td>Tutup</td>";
			}
			else
			$tab.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['tahunbudget']."','".$bar1['afdeling']."','".$bar1['penggunaan']."','".$bar1['kodevhc']."');\"></td></tr>";
		echo $tab;
		}
	
	
	case 'delete':
		$tab="delete from ".$dbname.".bgt_vhc_jarak where tahunbudget='".$thnbudget."' and afdeling='".$kdorg."' and penggunaan='".$pengguna."' and kodevhc='".$kdvhc."'";	
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
	
	
	case 'getthnbudgetHeader':
		//$bjr="select bjr from ".$dbname.".bgt_bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct tahunbudget FROM ".$dbname.".bgt_vhc_jarak where afdeling like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunbudget desc";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optTahunBudgetHeader.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
		}
		echo $optTahunBudgetHeader;
	break;
	
	case 'getkdorgHeader':
		//$bjr="select bjr from ".$dbname.".bgt_bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
		$optkdorgHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sThn = "SELECT distinct afdeling FROM ".$dbname.".bgt_vhc_jarak where afdeling like '%".$_SESSION['empl']['lokasitugas']."%' order by afdeling";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optkdorgHeader.="<option value='".$rThn['afdeling']."'>".$optNm[$rThn['afdeling']]."</option>";
		}
		echo $optkdorgHeader;
	break;
	
	
	case 'getThn':
	//exit("Error:MASUK");
		//$bjr="select bjr from ".$dbname.".bgt_bjr WHERE kodeorg='".substr($kdblok,0,4)."'";
			$optthnttp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
			$sql = "SELECT distinct tahunbudget FROM ".$dbname.".bgt_vhc_jarak where tutup=0 and afdeling like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunbudget desc";
			//exit("Error:$sql");
			$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
			while ($data=mysql_fetch_assoc($qry))
						{
							$optthnttp.="<option value=".$data['tahunbudget'].">".$data['tahunbudget']."</option>";
						}
			echo $optthnttp;
	break;
	
	
	case 'getOrg':
		$optorgclose="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sql = "SELECT distinct afdeling FROM ".$dbname.".bgt_vhc_jarak where tutup=0 and afdeling like '%".$_SESSION['empl']['lokasitugas']."%' ";
		$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qry))
					{
					$optorgclose.="<option value=".$data['afdeling'].">".$optNm[$data['afdeling']]."</option>";
					}
		echo $optorgclose;
	break;
	
	case'tutup':
		### Proses Norma VHC ###
		// Get Budget Fisik
		$qFisik = selectQuery($dbname,'bgt_budget_fisik','*',"tahunbudget='".$thnttp."' and kodeblok like '".$lkstgs."%' and tutup=1");
		$resFisik = fetchData($qFisik);
		$listBlok= "";
		foreach($resFisik as $fisik) {
			if($listBlok!='') {
				$listBlok.=',';
			}
			$listBlok.="'".$fisik['kodeblok']."'";
		}
		if(empty($resFisik)) {
			exit("Warning: Norma Fisik untuk Tahun ".$thnttp." Afdeling ".$lkstgs." belum ditutup atau belum ada");
		}
		
		// Get Jarak
		$qJarak = selectQuery($dbname,'bgt_vhc_jarak','*',"tahunbudget='".$thnttp."' and afdeling='".$lkstgs."' and tutup=0");
		$resJarak = fetchData($qJarak);
		if(empty($resJarak)) {
			exit("Warning: Setup Jarak VHC untuk Tahun ".$thnttp." Afdeling ".$lkstgs." belum ada");
		}
		$listKend = "";$optJarak=array();
		foreach($resJarak as $jarak) {
			if($listKend!='') {
				$listKend.=",";
			}
			$listKend.="'".$jarak['kodevhc']."'";
			$optJarak[$jarak['tahunbudget']][$jarak['afdeling']][] = array(
				'kodevhc'=>$jarak['kodevhc'],
				'penggunaan'=>$jarak['penggunaan'],
				'jarak'=>$jarak['jarak']
			);
		}
		
		// Get Kapasitas
		$qCap = selectQuery($dbname,'bgt_vhc_kapasitas','*',"kodevhc in (".$listKend.")");
		$resCap = fetchData($qCap);
		if(empty($resCap)) {
			exit("Warning: Data Kapasitas untuk Kendaraan ".$listKend." belum ada");
		}
		$optCap=array();
		foreach($resCap as $cap) {
			$optCap[$cap['kodevhc']][$cap['material']] = $cap['kapasitas'];
		}
		
		// Get Pokok
		$optPokok = makeOption($dbname,'setup_blok','kodeorg,jumlahpokok',"kodeorg in (".$listBlok.")");
		
		// Prepare Data Array
		$data = array();$rateTraksi=50000;
		foreach($resFisik as $fisik) {
			foreach($optJarak[$fisik['tahunbudget']][substr($fisik['kodeblok'],0,6)] as $opt) {
				if(strtoupper($fisik['jenismaterial'])==$opt['penggunaan']) {
					switch($opt['penggunaan']) {
						case 'PUPUK':
							$satuan = 'POKOK';
							$guna = 'PUPUK';
							break;
						case 'BIBIT':
							$satuan = 'BBT';
							$guna = 'BIBIT';
							break;
						case 'INT':
						case 'EXT':
							$satuan = $guna = 'BUAH';
							break;
					}
					$norma = $fisik['jumlah']*$opt['jarak']/$optCap[$opt['kodevhc']][$guna]*$rateTraksi;
					if($opt['penggunaan']=='PUPUK') {
						$norma = $norma/$optPokok[$fisik['kodeblok']];
					} elseif($opt['penggunaan']=='BIBIT') {
						$norma = $norma/143;
					}
					$data[] = array(
						'tahunbudget'=>$thnttp,
						'kodeblok'=>$fisik['kodeblok'],
						'kodevhc'=>$opt['kodevhc'],
						'kodebarangsupp'=>$fisik['kodebarangsupp'],
						'penggunaan'=>$opt['penggunaan'],
						'norma'=>$norma,
						'satuanv'=>$satuan
					);
				}
			}
		}
		
		// Insert
		foreach($data as $d) {
			$qIns = insertQuery($dbname,'bgt_vhc_norma',$d);
			if(!mysql_query($qIns)) {
				exit("DB Error: ".mysql_error());
			}
		}
		### /Proses Norma VHC ###
		
		$sQl="select distinct tutup from ".$dbname.".bgt_vhc_jarak where tahunbudget='".$thnttp."' and afdeling='".$lkstgs."' and tutup=1 ";
	    //exit("error".$sQl);
		$qQl=mysql_query($sQl) or die(mysql_error($conn));
		$row=mysql_num_rows($qQl);
		if($row!=1)
		{
			$sUpdate="update ".$dbname.".bgt_vhc_jarak set tutup=1 where tahunbudget='".$thnttp."' and afdeling='".$lkstgs."'  ";
		    //exit("error".$sUpdate);
			if(mysql_query($sUpdate))
				echo"";
			else
				 echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
		}
		else
		{
			exit("Error:Data sudah di Tutup");
		}
		break;
	default:
}	

	
?>