<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$method=$_POST['method'];

$thnbudget=isset($_POST['thnbudget'])?$_POST['thnbudget']:'';
$kdorg=isset($_POST['kdorg'])?$_POST['kdorg']:'';
$jenis=isset($_POST['jenis'])?$_POST['jenis']:'';
$thntnm=isset($_POST['thntnm'])?$_POST['thntnm']:'';
$barang=isset($_POST['barang'])?$_POST['barang']:'';
$norma=isset($_POST['norma'])?$_POST['norma']:'';
$jenis=isset($_POST['jenis'])?$_POST['jenis']:'';

$thnclose=isset($_POST['thnclose'])?$_POST['thnclose']:'';
$lokasiTugas=$lkstgs=isset($_POST['lkstgs'])?$_POST['lkstgs']:'';
$tahunTutup=$thnttp=isset($_POST['thnttp'])?$_POST['thnttp']:'';
$thnbudgetHeader=isset($_POST['thnbudgetHeader'])?$_POST['thnbudgetHeader']:'';
$kdorgHeader=isset($_POST['kdorgHeader'])?$_POST['kdorgHeader']:'';

$namakeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$optnamabarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');


switch($method)
{
	case'getbarang':
		if($jenis=='pupuk')
		{
			$ha="select kodebarang from ".$dbname.".log_5masterbarang WHERE kelompokbarang='311' order by namabarang ";
			//exit("Error:$sOpt");
				$hi=mysql_query($ha) or die(mysql_error());
				$optbarang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
				while($hu=mysql_fetch_assoc($hi))
				{
							$optbarang.="<option value=".$hu['kodebarang'].">".$optnamabarang[$hu['kodebarang']]."</option>";
				}
			echo $optbarang;
		}
		else if($jenis=='bibit')
		{
			$ha="select distinct jenisbibit from ".$dbname.".setup_blok order by jenisbibit ";
			//exit("Error:$sOpt");
				$hi=mysql_query($ha) or die(mysql_error());
				$optbarang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
				while($hu=mysql_fetch_assoc($hi))
				{
							$optbarang.="<option value=".$hu['jenisbibit'].">".$hu['jenisbibit']."</option>";
				}
			echo $optbarang;
		}
		break;
	case 'insert':
		$aCek="select distinct tutup from ".$dbname.".bgt_norma_fisik where tahunbudget='".$thnbudget."' and afdeling='".$kdorg."' ";
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
		
		$str="insert into ".$dbname.".bgt_norma_fisik (`tahunbudget`,`afdeling`,`jenismaterial`,`tahuntanam`,`kodebarangsupp`,`norma`)
		values ('".$thnbudget."','".$kdorg."','".$jenis."','".$thntnm."','".$barang."','".$norma."')";
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
		$str="select * from ".$dbname.".bgt_norma_fisik where afdeling like '%".$_SESSION['empl']['lokasitugas']."%' ".$tmbh." ".$tmbh2." order by tahunbudget desc ";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{	
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=right>".$bar1['tahunbudget']."</td>";
			$tab.="<td>".$bar1['afdeling']."</td>";
			$tab.="<td>".$bar1['jenismaterial']."</td>";
			$tab.="<td align=right>".$bar1['tahuntanam']."</td>";
			if(substr($bar1['kodebarangsupp'],0,3)==311)
			{
				$tab.="<td>".$optnamabarang[$bar1['kodebarangsupp']]."</td>";
			}else
				$tab.="<td>".$bar1['kodebarangsupp']."</td>";
			$tab.="<td align=right>".$bar1['norma']."</td>";
			if($bar1['tutup']==1)
			{
				$tab.="<td>Tutup</td>";
			}
			else
			$tab.="<td align=center><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['tahunbudget']."','".$bar1['afdeling']."','".$bar1['jenismaterial']."','".$bar1['tahuntanam']."','".$bar1['kodebarangsupp']."');\"></td></tr>";
			echo $tab;
		}
		break;
	case 'delete':
		$tab="delete from ".$dbname.".bgt_norma_fisik where tahunbudget='".$thnbudget."' and afdeling='".$kdorg."' and jenismaterial='".$jenis."' and tahuntanam='".$thntnm."' and kodebarangsupp='".$barang."'";	
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
		$sThn = "SELECT distinct tahunbudget FROM ".$dbname.".bgt_norma_fisik where afdeling like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunbudget desc";
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
		$sThn = "SELECT distinct afdeling FROM ".$dbname.".bgt_norma_fisik where afdeling like '%".$_SESSION['empl']['lokasitugas']."%' order by afdeling";
		//exit ("Error:$sThn");
		$qThn=mysql_query($sThn) or die(mysql_error($conn));
		while($rThn=mysql_fetch_assoc($qThn))
		{
			$optkdorgHeader.="<option value='".$rThn['afdeling']."'>".$optNm[$rThn['afdeling']]."</option>";
		}
		echo $optkdorgHeader;
		break;	
	case 'getThn':
		$optthnttp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sql = "SELECT distinct tahunbudget FROM ".$dbname.".bgt_norma_fisik where tutup=0 and afdeling like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunbudget desc";
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
		$sql = "SELECT distinct afdeling FROM ".$dbname.".bgt_norma_fisik where tutup=0 and afdeling like '%".$_SESSION['empl']['lokasitugas']."%' ";
		$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qry))
		{
			$optorgclose.="<option value=".$data['afdeling'].">".$optNm[$data['afdeling']]."</option>";
		}
		echo $optorgclose;
		break;
	case'tutup':
		#### Proses Budget Fisik ####
		// Get Norma
		$qNorma = selectQuery($dbname,'bgt_norma_fisik',"*","tahunbudget='".$thnttp."' and afdeling='".$lkstgs."' and tutup=0");
		$resNorma = fetchData($qNorma);
		
		$norm = array();$listTT='';
		foreach($resNorma as $n) {
			if($listTT!='') {
				$listTT.=',';
			}
			$listTT.=$n['tahuntanam'];
			$norm[$n['tahuntanam']] = array(
				'kodebarangsupp'=>$n['kodebarangsupp'],
				'norma'=>$n['norma']
			);
			$material = $n['jenismaterial'];
		}
		
		// Get Pokok
		$data = array();
		if($material=='pupuk') {
			$qBlok = selectQuery($dbname,'setup_blok',"kodeorg,jumlahpokok,tahuntanam",
				"kodeorg like '".$lkstgs."%' and tahuntanam in (".$listTT.")");
			$resBlok = fetchData($qBlok);
			foreach($resBlok as $blok) {
				$data[] = array(
					'tahunbudget'=>$thnttp,
					'jenismaterial'=>$material,
					'kodeblok'=>$blok['kodeorg'],
					'kodebarangsupp'=>$norm[$blok['tahuntanam']]['kodebarangsupp'],
					'jumlah'=>$norm[$blok['tahuntanam']]['norma']*$blok['jumlahpokok'],
					'tutup'=>1
				);
			}
		} else {
			$data[] = array(
				'tahunbudget'=>$thnttp,
				'jenismaterial'=>$material,
				'kodeblok'=>$lkstgs,
				'kodebarangsupp'=>$norm[$blok['tahuntanam']]['kodebarangsupp'],
				'jumlah'=>$norm[$blok['tahuntanam']]['norma']*$blok['jumlahpokok'],
				'tutup'=>1
			);
		}
		
		// Insert
		if(isset($data[0])) {
			foreach($data as $d) {
				$qIns = insertQuery($dbname,'bgt_budget_fisik',$d);
				if(!mysql_query($qIns)) {
					exit("DB Error: ".mysql_error());
				}
			}
		} else {
			$qIns = insertQuery($dbname,'bgt_budget_fisik',$data);
			if(!mysql_query($qIns)) {
				exit("DB Error: ".mysql_error());
			}
		}
		#### /Proses Budget Fisik ####
		
		// Tutup Norma
		$sQl="select distinct tutup from ".$dbname.".bgt_norma_fisik where tahunbudget='".$thnttp."' and afdeling='".$lkstgs."' and tutup=1 ";
		$qQl=mysql_query($sQl) or die(mysql_error($conn));
		$row=mysql_num_rows($qQl);
		if($row!=1)
		{
			$sUpdate="update ".$dbname.".bgt_norma_fisik set tutup=1 where tahunbudget='".$thnttp."' and afdeling='".$lkstgs."'  ";
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