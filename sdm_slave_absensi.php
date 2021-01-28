<?php
session_start(); 
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=isset($_POST['proses'])?$_POST['proses']:$_GET['proses'];
$txtFind=isset($_POST['txtfind'])?$_POST['txtfind']:'';
$absnId=isset($_POST['absnId'])?explode("###",$_POST['absnId']):array('','');
$tgl=tanggalsystem($absnId[1]);
$kdOrg=$absnId[0];
$krywnId=isset($_POST['krywnId'])?$_POST['krywnId']:'';
$shifTid=isset($_POST['shifTid'])?$_POST['shifTid']:'';
$asbensiId=isset($_POST['asbensiId'])?$_POST['asbensiId']:'';
$Jam=isset($_POST['Jam'])?$_POST['Jam']:'';
$Jam2=isset($_POST['Jam2'])?$_POST['Jam2']:'';
$ket=isset($_POST['ket'])?$_POST['ket']:'';
$periode=isset($_POST['period'])?$_POST['period']:'';
$idOrg=substr($_SESSION['empl']['lokasitugas'],0,4);
$param = $_POST;
foreach($_GET as $key=>$row) {
	$param[$key]=$row;
}

switch($proses) {
	case 'excel':
		$param['kodeorg']=$param['kdOrg'];
		$param['tanggal']=$param['tglAbsen'];
		//$qAbs = selectQuery($dbname,'sdm_absensidt',"*","left(kodeorg,4)='".
		//	substr($param['kodeorg'])."' and tanggal='".$param['tanggal']."'");
		$qAbs = "select a.*,b.jamaktual,c.namakaryawan from ".$dbname.".sdm_absensidt a left join ".
			$dbname.".sdm_lemburdt b on a.kodeorg=b.kodeorg and a.tanggal=b.tanggal and a.karyawanid=b.karyawanid".
			" left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid where left(a.kodeorg,4)='".
			substr($param['kodeorg'],0,4)."' and a.tanggal='".tanggalsystem($param['tanggal'])."'";
		//echo $qAbs;
		$resAbs = fetchData($qAbs);
		if(empty($resAbs)) {
			exit("Warning: Absensi belum ada");
		}
		
		// Get Jam Dinas
		$optDinas = makeOption($dbname,'sdm_5jamefektif','shift,jumlahjam',
			"left(kodeorg,4)='".substr($param['kodeorg'],0,4)."' and tanggal='".tanggalsystem($param['tanggal'])."'");
		$optAbsen = makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan');
		
		foreach($resAbs as $key=>$row) {
			################################################### Hitung Jam Kerja
			$rDet = $row;
			## Jam Bruto
			$jm1 = explode(':',$rDet['jam']);
			$jm2 = explode(':',$rDet['jamPlg']);
			if($jm2[0]<$jm1[0] or (($jm2[0]==$jm1[0]) and $jm2[1]<$jm1[1])) {
				$jm2[0]+=24;
			}
			if($jm2[1]<$jm1[1]) {
				$jm2[1]+=60;
				$jm2[0]-=1;
			}
			$jambruto = $jm2[0]-$jm1[0];
			$mnbruto = $jm2[1]-$jm1[1];
			
			## Istirahat 1
			$jm1 = explode(':',$rDet['jamkeluar1']);
			$jm2 = explode(':',$rDet['jammasuk1']);
			if($jm2[0]<$jm1[0] or (($jm2[0]==$jm1[0]) and $jm2[1]<$jm1[1])) {
				$jm2[0]+=24;
			}
			if($jm2[1]<$jm1[1]) {
				$jm2[1]+=60;
				$jm2[0]-=1;
			}
			$jamist1 = $jm2[0]-$jm1[0];
			$mnist1 = $jm2[1]-$jm1[1];
			
			## Istirahat 2
			$jm1 = explode(':',$rDet['jamkeluar2']);
			$jm2 = explode(':',$rDet['jammasuk2']);
			if($jm2[0]<$jm1[0] or (($jm2[0]==$jm1[0]) and $jm2[1]<$jm1[1])) {
				$jm2[0]+=24;
			}
			if($jm2[1]<$jm1[1]) {
				$jm2[1]+=60;
				$jm2[0]-=1;
			}
			$jamist2 = $jm2[0]-$jm1[0];
			$mnist2 = $jm2[1]-$jm1[1];
			
			## Istirahat 3
			$jm1 = explode(':',$rDet['jamkeluar3']);
			$jm2 = explode(':',$rDet['jammasuk3']);
			if($jm2[0]<$jm1[0] or (($jm2[0]==$jm1[0]) and $jm2[1]<$jm1[1])) {
				$jm2[0]+=24;
			}
			if($jm2[1]<$jm1[1]) {
				$jm2[1]+=60;
				$jm2[0]-=1;
			}
			$jamist3 = $jm2[0]-$jm1[0];
			$mnist3 = $jm2[1]-$jm1[1];
			
			$jamist = $jamist1+$jamist2+$jamist3;
			$mnist = $mnist1+$mnist2+$mnist3;
			$jamist += floor($mnist/60);
			$mnist = $mnist%60;
			
			$jameff = $jambruto-$jamist;
			$mneff = $mnbruto-$mnist;
			if($mneff<0) {
				$jameff -= 1;
				$mneff += 60;
			}
			
			$jamkerja = addZero($jameff,2).":".addZero($mneff,2);
			################################################## /Hitung Jam Kerja
			
			######################################################### Jam Lembur
			$jamDinas = $optDinas[$rDet['shift']];
			if(($jameff-$jamDinas)<0) {
				$jamLembur = "00:00";
			} else {
				$jamLembur = addZero(($jameff-$jamDinas),2).":".addZero($mneff,2);
			}
			######################################################## /Jam Lembur
			$resAbs[$key]['jamefektif'] = $jamkerja;
			$resAbs[$key]['jamlembur'] = $jamLembur;
		}
		$kodeorg=$param['kodeorg'];
		$stream = "<table border=1 colspan=5>";
		$stream .= "<tr><td>Tanggal: ".tanggalnormal($param['tanggal'])."</td>";
		$stream .= "<td>Unit: ".substr($param['kodeorg'],0,4)."</td></tr>";
		$stream .= "<tr><td>No</td>";
		$stream .= "<td>Nama Karyawan</td>";
		$stream .= "<td>Shift</td>";
		$stream .= "<td>Kehadiran</td>";
		$stream .= "<td>Jam Msk</td>";
		$stream .= "<td>Jam Plg</td>";
		$stream .= "<td>Jumlah Jam Kerja Efektif</td>";
		$stream .= "<td>Jam Dinas</td>";
		$stream .= "<td>Jam Lembur</td>";
		$stream .= "<td>Keterangan</td>";
		$stream .= "<td>Jam Keluar 1</td>";
		$stream .= "<td>Keterangan 1</td>";
		$stream .= "<td>Jam Masuk 1</td>";
		$stream .= "<td>Jam Keluar 2</td>";
		$stream .= "<td>Keterangan 2</td>";
		$stream .= "<td>Jam Masuk 2</td>";
		$stream .= "<td>Jam Keluar 3</td>";
		$stream .= "<td>Keterangan 3</td>";
		$stream .= "<td>Jam Masuk 3</td>";
		$stream .= "</tr>";
		$no=1;
		foreach($resAbs as $row) {
			$stream .= "<tr><td>".$no."</td>";
			$stream .= "<td>".$row['namakaryawan']."</td>";
			$stream .= "<td>".$row['shift']."</td>";
			$stream .= "<td>".$optAbsen[$row['absensi']]."</td>";
			$stream .= "<td>".$row['jam']."</td>";
			$stream .= "<td>".$row['jamPlg']."</td>";
			$stream .= "<td>".$row['jamefektif']."</td>";
			$stream .= "<td>".$optDinas[$row['shift']]."</td>";
			$stream .= "<td>".$row['jamlembur']."</td>";
			$stream .= "<td>".$row['penjelasan']."</td>";
			$stream .= "<td>".$row['jamkeluar1']."</td>";
			$stream .= "<td>".$row['ket1']."</td>";
			$stream .= "<td>".$row['jammasuk1']."</td>";
			$stream .= "<td>".$row['jamkeluar2']."</td>";
			$stream .= "<td>".$row['ket2']."</td>";
			$stream .= "<td>".$row['jammasuk2']."</td>";
			$stream .= "<td>".$row['jamkeluar3']."</td>";
			$stream .= "<td>".$row['ket3']."</td>";
			$stream .= "<td>".$row['jammasuk3']."</td>";
			$stream .= "</tr>";
			$no++;
		}
		$stream .= "</table>";
		$nop_="Absensi_".$kodeorg;
		if(strlen($stream)>0) {
			# Delete if exist
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						@unlink('tempExcel/'.$file);
					}
				}	
				closedir($handle);
			}
			
			# Write to File
			$gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
			gzwrite($gztralala, $stream);
			gzclose($gztralala);
			echo "<script language=javascript1.2>
			   window.location='tempExcel/".$nop_.".xls.gz';
			   </script>";
		}
		exit;
		break;
	case'cariOrg':
	//echo"warning:masuk";
	$str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where namaorganisasi like '%".$txtFind."%' or kodeorganisasi like '%".$txtFind."%' "; //echo "warning:".$str;exit();
	if($res=mysql_query($str))
	{
		echo"
	  <fieldset>
	<legend>Result</legend>
	<div style=\"overflow:auto; height:300px;\" >
	<table class=data cellspacing=1 cellpadding=2  border=0>
			 <thead>
			 <tr class=rowheader>
			 <td class=firsttd>
			 No.
			 </td>
			 <td>".$_SESSION['lang']['kodeorg']."</td>
			 <td>".$_SESSION['lang']['namaorganisasi']."</td>
			 </tr>
			 </thead>
			 <tbody>";
		$no=0;	 
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >
				  <td class=firsttd>".$no."</td>
				  <td>".$bar->kodeorganisasi."</td>
				  <td>".$bar->namaorganisasi."</td>
				 </tr>";
		}	 
		echo "</tbody>
			  <tfoot>
			  </tfoot>
			  </table></div></fieldset>";
	  }	
	  else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}	
	break;
	case'cariOrg2':
	//echo"warning:masuk";
	$str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where namaorganisasi like '%".$txtFind."%' or kodeorganisasi like '%".$txtFind."%' "; //echo "warning:".$str;exit();
	if($res=mysql_query($str))
	{
		echo"
	  <fieldset>
	<legend>Result</legend>
	<div style=\"overflow:auto; height:300px;\" >
	<table class=data cellspacing=1 cellpadding=2  border=0>
			 <thead>
			 <tr class=rowheader>
			 <td class=firsttd>
			 No.
			 </td>
			 <td>".$_SESSION['lang']['kodeorg']."</td>
			 <td>".$_SESSION['lang']['namaorganisasi']."</td>
			 </tr>
			 </thead>
			 <tbody>";
		$no=0;	 
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setOrg2('".$bar->kodeorganisasi."','".$bar->namaorganisasi."')\" title='Click' >
				  <td class=firsttd>".$no."</td>
				  <td>".$bar->kodeorganisasi."</td>
				  <td>".$bar->namaorganisasi."</td>
				 </tr>";
		}	 
		echo "</tbody>
			  <tfoot>
			  </tfoot>
			  </table></div></fieldset>";
	  }	
	  else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}	
	break;
	case'cekData':
	
	//echo"warning:masuk";
	//SELECT * FROM `sdm_5periodegaji` WHERE `kodeorg`='SOGE' and `sudahproses`=0 and `tanggalmulai`<'20110112' and `tanggalsampai`>'20110112'
	$sCek="select DISTINCT tanggalmulai,tanggalsampai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_num_rows($qCek);
	if($rCek>0)
	{
	
	$sCek="select kodeorg,tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;nospb
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_fetch_row($qCek);
	if($rCek<1)
	{
		$sIns="insert into ".$dbname.".sdm_absensiht (`kodeorg`,`tanggal`,`periode`) values ('".$kdOrg."','".$tgl."','".$periode."')"; //echo"warning:".$sIns;
		if(mysql_query($sIns))
		{
			$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`,`jamkeluar1`,`ket1`,`jammasuk1`,`jamkeluar2`,`ket2`,`jammasuk2`,`jamkeluar3`,`ket3`,`jammasuk3`, `penjelasan`) values ".
				"('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".
				$param['keluar1']."','".$param['ket1']."','".$param['masuk1']."','".
				$param['keluar2']."','".$param['ket2']."','".$param['masuk2']."','".
				$param['keluar3']."','".$param['ket3']."','".$param['masuk3']."','".$ket."')";
			if(mysql_query($sDetIns))
			{
				echo"";
			}
			else
			{echo "DB Error : ".mysql_error($conn);}
		}
		else
		{
			echo "DB Error : ".mysql_error($conn);
		}
	}
	else
	{
		$sDetIns="insert into ".$dbname.".sdm_absensidt (`kodeorg`,`tanggal`, `karyawanid`, `shift`, `absensi`, `jam`,`jamPlg`,`jamkeluar1`,`ket1`,`jammasuk1`,`jamkeluar2`,`ket2`,`jammasuk2`,`jamkeluar3`,`ket3`,`jammasuk3`, `penjelasan`) values ".
			"('".$kdOrg."','".$tgl."','".$krywnId."','".$shifTid."','".$asbensiId."','".$Jam."','".$Jam2."','".
			$param['keluar1']."','".$param['ket1']."','".$param['masuk1']."','".
			$param['keluar2']."','".$param['ket2']."','".$param['masuk2']."','".
			$param['keluar3']."','".$param['ket3']."','".$param['masuk3']."','".$ket."')";
		if(mysql_query($sDetIns)) {
			echo"";
		} else {
			echo "DB Error : ".mysql_error($conn);
		}
	}
	} else {
		echo"warning:Diluar Periode Gaji";
		exit();
	}
	
	break;
	case'loadNewData':
	echo"
	<table cellspacing=1 border=0 class=sortable>
	<thead>
	<tr class=rowheader>
	<td>No.</td>
	<td>".$_SESSION['lang']['kodeorg']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['periode']."</td>
	<td>Action</td>
	</tr>
	</thead>
	<tbody>
	";
	$limit=20;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."' order by `tanggal` desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
	
	
	$slvhc="select *,DATE_FORMAT(tanggal, '%Y%m%d') as tglAA,DATE_FORMAT(NOW()-interval 1 day, '%Y%m%d') as kemaren,DATE_FORMAT(NOW(), '%Y%m%d') as sekarang,DATE_FORMAT(NOW()-interval 3 day, '%Y%m%d') as kemaren3,DATE_FORMAT(NOW()-interval 2 day, '%Y%m%d') as kemaren2,DATE_FORMAT(NOW(), '%W') AS hHARI from ".$dbname.".sdm_absensiht where substring(kodeorg,1,4)='".$idOrg."' order by `tanggal` desc limit ".$offset.",".$limit."";
	$qlvhc=mysql_query($slvhc) or die(mysql_error());
	$user_online=$_SESSION['standard']['userid'];
	$no=0;
	while($rlvhc=mysql_fetch_assoc($qlvhc))
	{
		$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
		$qOrg=mysql_query($sOrg) or die(mysql_error());
		$rOrg=mysql_fetch_assoc($qOrg);
	$sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$rlvhc['kodeorg']."' and `periode`='".$rlvhc['periode']."'";
	$qGp=mysql_query($sGp) or die(mysql_error());
	$rGp=mysql_fetch_assoc($qGp);
	
	$no+=1;
	echo"
	<tr class=rowcontent>
	<td>".$no."</td>
	<td>".$rlvhc['kodeorg']."</td>
	<td>".tanggalnormal($rlvhc['tanggal'])."</td>
	<td>".substr(tanggalnormal($rlvhc['periode']),1,7)."</td>
	<td>";
	//if($rGp['sudahproses']==0)
		
	if ($_SESSION['empl']['lokasitugas'] == "CBGM" || $_SESSION['empl']['lokasitugas'] == "FBHO"){
		/*
		if ($rlvhc['hHARI']=="Monday"){
			if ($rlvhc['tglAA']>=$rlvhc['kemaren2']  && $rlvhc['tglAA']<=$rlvhc['sekarang'])
			{
				echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
				<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
				<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}else{
				echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			} 
		}else{
			if ($rlvhc['tglAA']>=$rlvhc['kemaren']  && $rlvhc['tglAA']<=$rlvhc['sekarang'])
			{
				echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
				<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
				<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}else{
				echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}
		} 
		
		 */
		
		////------------------------------------------------------------------------PKS-HOLDING
		 
		if ($rlvhc['tglAA']>="20191201"  && $rlvhc['tglAA']<="20191231") 
		{
			echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
		}else{
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
		}
		
		////------------------------------------------------------------------------PKS-HOLDING
		 
	}else{
		////------------------------------------------------------------------------KEBUN
		if ($_SESSION['empl']['lokasitugas'] == "TDBE" || $_SESSION['empl']['lokasitugas'] == "USJE" || $_SESSION['empl']['lokasitugas'] == "TDAE" || $_SESSION['empl']['lokasitugas'] == "FBAO" || $_SESSION['empl']['lokasitugas'] == "TKFB" ){
			if ($rlvhc['tglAA']>="20191201"  && $rlvhc['tglAA']<="20191231")
			{
		echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
			else
			{
		echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}
			
		}else{
			
			if ($rlvhc['tglAA']>=$rlvhc['kemaren3']  && $rlvhc['tglAA']<=$rlvhc['sekarang'])
			{
		echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
			else
			{
		echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}
			
		}
		
		
	}
	echo"</td>
	</tr>
	";
	}
	echo"
	<tr class=rowheader><td colspan=5 align=center>
	".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
	<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	</td>
	</tr>";
	echo"</tbody></table>";
	break;
	case'delData':
	$sCek="select posting from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;;
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_fetch_assoc($qCek);
	if($rCek['posting']=='1')
	{
		echo"warning:Already Post This Data";
		exit();
	}
	$sDel="delete from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";// echo "___".$sDel;exit();
	if(mysql_query($sDel))
	{
		$sDelDetail="delete from ".$dbname.".sdm_absensidt where tanggal='".$tgl."' and kodeorg='".$kdOrg."'";
		if(mysql_query($sDelDetail))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	}
	else
	{echo "DB Error : ".mysql_error($conn);}
	
	break;
	case'cekHeader':
	
	 
	
	
		$sCek1="select DATE_FORMAT(NOW()-interval 1 day, '%Y%m%d') as kemaren,DATE_FORMAT(NOW()-interval 3 day, '%Y%m%d') as kemaren3,DATE_FORMAT(NOW(), '%Y%m%d') as sekarang,DATE_FORMAT(NOW()-interval 1 day, '%d-%m-%Y') as kemaren1,DATE_FORMAT(NOW()-interval 2 day, '%d-%m-%Y') as kemaren2A,DATE_FORMAT(NOW()-interval 2 day, '%Y%m%d') as kemaren2,DATE_FORMAT(NOW(), '%d-%m-%Y') as sekarang1,DATE_FORMAT(NOW()-interval 1 day, '%d-%m-%Y') as kemaren4,DATE_FORMAT(NOW(), '%d-%m-%Y') as sekarang4,DATE_FORMAT(NOW(), '%W') AS hHARI";
		$qCek1=mysql_query($sCek1) or die(mysql_error());
		$rCek1=mysql_fetch_object($qCek1);
		  
		if ($_SESSION['empl']['lokasitugas'] == "CBGM" || $_SESSION['empl']['lokasitugas'] == "FBHO"){
			/*
			if($rCek1->hHARI=="Monday"){
				if ($tgl>=$rCek1->kemaren2  && $tgl<=$rCek1->sekarang){
				
				}else{
					echo"warning:Range Tanggal Penginputan Harus ".$rCek1->kemaren2A." S/D ".$rCek1->sekarang1;
					exit();
				}
			}else{
				if ($tgl>=$rCek1->kemaren  && $tgl<=$rCek1->sekarang){
				
				}else{
					echo"warning:Range Tanggal Penginputan Harus ".$rCek1->kemaren1." S/D ".$rCek1->sekarang1;
					exit();
				}
			}
				
		*/
		////------------------------------------------------------------------------PKS-HOLDING
		
		if ($tgl>="20191201"  && $tgl<="20191231") {
				
		}else{
				echo"warning:Range Tanggal Penginputan Harus 23-10-2015 S/D 24-10-2015"; 
				exit();
		}
		
		
		}else{
			////------------------------------------------------------------------------KEBUN
			if ($_SESSION['empl']['lokasitugas'] == "TDBE" || $_SESSION['empl']['lokasitugas'] == "USJE" || $_SESSION['empl']['lokasitugas'] == "TDAE" || $_SESSION['empl']['lokasitugas'] == "FBAO" || $_SESSION['empl']['lokasitugas'] == "TKFB"){
				if ($tgl>="20191201"  && $tgl<="20191231"){
					
				}else{
					echo"warning:Range Tanggal Penginputan Harus 23-10-2015 S/D 24-10-2015"; 
					exit();
				}
			}else{
				if ($tgl>=$rCek1->kemaren3  && $tgl<=$rCek1->sekarang){
				
				}else{
				echo"warning:Range Tanggal Penginputan Harus ".$rCek1->kemaren4." S/D ".$rCek1->sekarang4;
				exit();
			
				}
			}

		}
	
	
	
		
	
	// Get Jam Dinas
	$optDinas = makeOption($dbname,'sdm_5jamefektif','shift,jumlahjam',
		"kodeorg='".substr($kdOrg,0,4)."' and tanggal='".$tgl."'");
	if(empty($optDinas)) {
		exit("Warning: Jam Dinas untuk hari ini belum ada");
	}
	
	$sCek="select DISTINCT tanggalmulai,tanggalsampai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and sudahproses=0 and tanggalmulai<='".$tgl."' and tanggalsampai>='".$tgl."'";
			//    $sCek="select DISTINCT tanggalmulai,tanggalsampai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and sudahproses=0";
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_num_rows($qCek);
			//$rCek=mysql_fetch_assoc($qCek);
	if($rCek<1)
		   // if($rCek['tanggalmulai']<=$tgl || $rCek['tanggalsampai']>=$tgl)
	{
		echo"warning:Tanggal Diluar Periode Gaji";
		exit();
	}
			//echo"warning:masuk".$aktif;exit();
	$sCek="select kodeorg,tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tgl."' and kodeorg='".$kdOrg."'"; //echo "warning".$sCek;nospb
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_fetch_row($qCek);
	if($rCek>0)
	{
		echo"warning:This Date And Organization Name Already Input";
		exit();
	}
	
	
			$str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and
			kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
		   // exit("Error".$str) ;
			$res=mysql_query($str);
			if(mysql_num_rows($res)>0)
			$aktif=true;
			else
			$aktif=false;
			if($aktif==true)
			{
			exit("Error:Periode sudah tutup buku");
			}
	break;
	case'cariAbsn':
	
	
	echo"
	<div style=overflow:auto; height:350px;>
	<table cellspacing=1 border=0>
	<thead>
	<tr class=rowheader>
	<td>No.</td>
	<td>".$_SESSION['lang']['kodeorg']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['periode']."</td>
	<td>Action</td>
	</tr>
	</thead>
	<tbody>
	";
	//echo"warning:".$tgl."___".$kdOrg;
	if(($tgl!='')&&($kdOrg!=''))
	{
		$where=" kodeorg like '".$kdOrg."' and tanggal='".$tgl."' " ;
	}
	elseif(($tgl!='')&&($kdOrg!=''))
	{
		$where=" tanggal='".$tgl."' and kodeorg like '".$kdOrg."' ";	
	}
	elseif(($tgl!='')&&($kdOrg==''))
	{
		$where=" tanggal='".$tgl."' and kodeorg like '".$idOrg."%'";	
	}
	elseif(($tgl=='')&&($kdOrg==''))
	{
		echo"warning:Please Insert Data";
		exit();
	}
	$sCek="select * from ".$dbname.".sdm_absensiht where ".$where."";//echo "warning".$sCek;exit();
	$qCek=mysql_query($sCek) or die(mysql_error());
	$rCek=mysql_num_rows($qCek);
	if($rCek>0)
	{
		
		
		$slvhc="select *,DATE_FORMAT(tanggal, '%Y%m%d') as tglAA,DATE_FORMAT(NOW()-interval 1 day, '%Y%m%d') as kemaren,DATE_FORMAT(NOW()-interval 3 day, '%Y%m%d') as kemaren3,DATE_FORMAT(NOW()-interval 2 day, '%Y%m%d') as kemaren2,DATE_FORMAT(NOW(), '%Y%m%d') as sekarang,DATE_FORMAT(NOW(), '%W') AS hHARI from ".$dbname.".sdm_absensiht where ".$where."  order by `tanggal` desc ";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		$user_online=$_SESSION['standard']['userid'];
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
			$sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rlvhc['kodeorg']."'";
			$qOrg=mysql_query($sOrg) or die(mysql_error());
			$rOrg=mysql_fetch_assoc($qOrg);
			$sGp="select DISTINCT sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$rlvhc['kodeorg']."' and `periode`='".$rlvhc['periode']."'";
	$qGp=mysql_query($sGp) or die(mysql_error());
	$rGp=mysql_fetch_assoc($qGp);
		$no+=1; 
	echo"
	<tr class=rowcontent>
	<td>".$no."</td>
	<td>".$rlvhc['kodeorg']."</td>
	<td>".tanggalnormal($rlvhc['tanggal'])."</td>
	<td>".substr(tanggalnormal($rlvhc['periode']),1,7)."</td>
	<td>";
	//if($rGp['sudahproses']==0)
	if ($_SESSION['empl']['lokasitugas'] == "CBGM" || $_SESSION['empl']['lokasitugas'] == "FBHO"){	
		/*
		if($rlvhc['hHARI']=="Monday"){
			if ($rlvhc['tglAA']>=$rlvhc['kemaren2']  && $rlvhc['tglAA']<=$rlvhc['sekarang'])
			{
		////------------------------------------------------------------------------PKS-HOLDING
			echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".	$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
			else
			{
			//echo $rlvhc['tglAA']."#".$rlvhc['kemaren']."#".$rlvhc['sekarang'];
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
		}else{
			if ($rlvhc['tglAA']>=$rlvhc['kemaren']  && $rlvhc['tglAA']<=$rlvhc['sekarang'])
			{
		////------------------------------------------------------------------------PKS-HOLDING
			echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".	$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
			else
			{
			//echo $rlvhc['tglAA']."#".$rlvhc['kemaren']."#".$rlvhc['sekarang'];
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
		}
		
		*/
		
	
		 if ($rlvhc['tglAA']>="20191201"  && $rlvhc['tglAA']<="20191231") 
		{
	echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
	<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
	<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
		}
	else
		{
		
		echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
		
		} 
	 
		
	}else{ 	
		////------------------------------------------------------------------------KEBUN
		if ($_SESSION['empl']['lokasitugas'] == "TDBE" || $_SESSION['empl']['lokasitugas'] == "USJE" || $_SESSION['empl']['lokasitugas'] == "TDAE" || $_SESSION['empl']['lokasitugas'] == "FBAO" || $_SESSION['empl']['lokasitugas'] == "TKFB" ){
			if ($rlvhc['tglAA']>="20191201"  && $rlvhc['tglAA']<="20191231")
			{
		echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
			else
			{
		echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}
			
		}else{
			
			if ($rlvhc['tglAA']>=$rlvhc['kemaren3']  && $rlvhc['tglAA']<=$rlvhc['sekarang'])
			{
		echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['periode']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".tanggalnormal($rlvhc['tanggal'])."');\" >	
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
	
			}
			else
			{
		echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$rlvhc['kodeorg'].",".tanggalnormal($rlvhc['tanggal'])."','','sdm_absensiPdf',event)\">";
			}
			
		}


	}
	echo"</td>
	</tr>
	";
		}
		
		echo"</tbody></table></div>";
	}
	else
	{
		echo"<tr class=rowcontent><td colspan=5 align=center>Not Found</td></tr></tbody></table></div>";
	}
	break;
	case'updateData':
	$sUpd="update ".$dbname.".sdm_absensidt set shift='".$shifTid."',absensi='".$asbensiId."',jam='".$Jam."',jamPlg='".$Jam2.
		"',jamkeluar1='".$param['keluar1']."',ket1='".$param['ket1']."',jammasuk1='".$param['masuk1'].
		"',jamkeluar2='".$param['keluar2']."',ket2='".$param['ket2']."',jammasuk2='".$param['masuk2'].
		"',jamkeluar3='".$param['keluar3']."',ket3='".$param['ket3']."',jammasuk3='".$param['masuk3'].
		"',penjelasan='".$ket."' where kodeorg='".$kdOrg."' and tanggal='".$tgl."' and karyawanid='".$krywnId."'";
		if(mysql_query($sUpd))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	break;
	case'delDetail':
		$sDelDetail="delete from ".$dbname.".sdm_absensidt where tanggal='".$tgl."' and kodeorg='".$kdOrg."' and karyawanid='".$krywnId."'";
		if(mysql_query($sDelDetail))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	break;
	default:
	break;
}
?>