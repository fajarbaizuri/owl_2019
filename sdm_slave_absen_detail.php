<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('config/connection.php');

$proses=$_POST['proses'];
$absnId=isset($_POST['absnId'])?$_POST['absnId']:'';
$kdOrg=isset($_POST['kdOrg'])?$_POST['kdOrg']:'';
$tgAbsn=isset($_POST['tgAbsn'])?tanggalsystem($_POST['tgAbsn']):'';

	switch($proses)
	{
		case'createTable':
		//$thisDate=date("Y-m-d");
		$table = "<table id='ppDetailTable'>";
		//echo"warning:".$table;
		# Header
		$table .= "<thead>";
		$table .= "<tr class=rowheader>";
		$table .= "<td>".$_SESSION['lang']['namakaryawan']."</td>";
		$table .= "<td>".$_SESSION['lang']['shift']."</td>";
		$table .= "<td>".$_SESSION['lang']['absensi']."</td>";
		$table .= "<td>".$_SESSION['lang']['jamMsk']."</td>";
		$table .= "<td>".$_SESSION['lang']['jamPlg']."</td>";
		$table .= "<td>".$_SESSION['lang']['keterangan']."</td>";
		$table .= "<td>Jam Keluar 1</td><td>Keterangan 1</td><td>Jam Masuk 1</td>";
		$table .= "<td>Jam Keluar 2</td><td>Keterangan 2</td><td>Jam Masuk 2</td>";
		$table .= "<td>Jam Keluar 3</td><td>Keterangan 3</td><td>Jam Masuk 3</td>";
		$table .= "<td>Action</td>";
		$table .= "</tr>";
		$table .= "</thead>";
		
		# Data
		$table .= "<tbody id='detailBody'>";
		$idAbn=explode("###",$absnId);
		$tgl=tanggalsystem($idAbn[1]);
		if(strlen($idAbn[0])>4)
		{
			$where=" subbagian='".$idAbn[0]."'  and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
		}
		else
		{
			//$where=" lokasitugas='".$idAbn[0]."' and (subbagian IS NULL or subbagian='0' or subbagian='') and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
			#update indra
			if($_SESSION['empl']['lokasitugas']=='CBGM')
			{
				$where=" lokasitugas='".$idAbn[0]."' and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
			}
			else
			{
				$where=" lokasitugas='".$idAbn[0]."' and (subbagian IS NULL or subbagian='0' or subbagian='') and (tanggalkeluar>".$tgl." or tanggalkeluar='0000-00-00')";
			}
		}
	//	echo $where;
//		$where=" lokasitugas='".$idAbn[0]."' or subbagian='".$idAbn[0]."'"; 
//	echo"warning:".$where."__".$sPil."___".$idAbn[0]."___=".$rPil['subbagian'];exit();
	$optKry=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$where,0);
	
		
			//$_SESSION['temp']['OrgKd']=$idAbn[0];
		$whre=" kodeorg='".$idAbn[0]."'";
		//$optShift=makeOption($dbname,'pabrik_5shift','shift,shift',$whre);
		//$optShift=makeOption($dbname,'pabrik_5shift','shift,shift',$whre);
		$optAbsen=makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan');
		$jm='';$mnt='';
		for($t=0;$t<24;)
		{
			if(strlen($t)<2)
			{
				$t="0".$t;
			}
			$jm.="<option value=".$t." ".($t==00?'selected':'').">".$t."</option>";
			$t++;
		}
		for($y=0;$y<60;)
		{
			if(strlen($y)<2)
			{
				$y="0".$y;
			}
			$mnt.="<option value=".$y." ".($y==00?'selected':'').">".$y."</option>";
			$y++;
		}
		
		$optShift = makeOption($dbname,'sdm_5jamefektif','shift,shift',
			"kodeorg='".substr($idAbn[0],0,4)."' and tanggal='".$tgl."'");
		$table .= "<tr id='detail_tr' class='rowcontent'>";
		$table .= "<td>".makeElement("krywnId",'select','',
		array('style'=>'width:150px'),$optKry)."</td>";
		$table .= "<td>".makeElement("shiftId",'select','',
		array('style'=>'width:120px','onkeypress'=>'return tanpa_kutip(event)'),$optShift)."</td>";
		$table .= "<td>".makeElement("absniId",'select','',
		array('style'=>'width:100px'),$optAbsen)."</td>";
		$table .= "<td><select id=jmId name=jmId >".$jm."</select>:<select id=mntId name=mntId>".$mnt."</select></td>";
		$table .= "<td><select id=jmId2 name=jmId2 >".$jm."</select>:<select id=mntId2 name=mntId2>".$mnt."</select></td>";
		$table .= "<td>".makeElement("ktrng",'text','',
		array('style'=>'width:120px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
		$table .= "<td><select id=jk1 name=jk1 >".$jm."</select>:<select id=mk1 name=mk1>".$mnt."</select></td>";
		$table .= "<td>".makeElement("ket1",'text','',array('style'=>'width:60px'))."</td>";
		$table .= "<td><select id=jm1 name=jm1 >".$jm."</select>:<select id=mm1 name=mm1>".$mnt."</select></td>";
		$table .= "<td><select id=jk2 name=jk2 >".$jm."</select>:<select id=mk2 name=mk2>".$mnt."</select></td>";
		$table .= "<td>".makeElement("ket2",'text','',array('style'=>'width:60px'))."</td>";
		$table .= "<td><select id=jm2 name=jm2 >".$jm."</select>:<select id=mm2 name=mm2>".$mnt."</select></td>";
		$table .= "<td><select id=jk3 name=jk3 >".$jm."</select>:<select id=mk3 name=mk3>".$mnt."</select></td>";
		$table .= "<td>".makeElement("ket3",'text','',array('style'=>'width:60px'))."</td>";
		$table .= "<td><select id=jm3 name=jm3 >".$jm."</select>:<select id=mm3 name=mm3>".$mnt."</select></td>";
		# Add, Container Delete
		$table .= "<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/>";
		$table .= "&nbsp;<img id='detail_delete' /></td>";
		$table .= "</tr>";
		$table .= "</tbody>";
		$table .= "</table>";
		echo $table;
		break;
		case'loadDetail':
		$sDt="select * from ".$dbname.".sdm_absensidt where kodeorg='".$kdOrg."' and tanggal='".$tgAbsn."'";
		$qDt=mysql_query($sDt) or die(mysql_error());
		$no=0;
		
		// Get Jam Dinas
		$optDinas = makeOption($dbname,'sdm_5jamefektif','shift,jumlahjam',
			"kodeorg='".substr($kdOrg,0,4)."' and tanggal='".$tgAbsn."'");
		
		while($rDet=mysql_fetch_assoc($qDt))
		{
			################################################### Hitung Jam Kerja
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
			
			$ha="select karyawanid from ".$dbname.".sdm_spldt where karyawanid='".$rDet['karyawanid']."' and kodeorg='".$kdOrg."' and tanggal='".$tgAbsn."'";
			//echo $ha;
			$hi=mysql_query($ha);
			$hu=mysql_fetch_assoc($hi);
				$ada=$hu['karyawanid'];
				//echo $ada;
				
			if(($jameff-$jamDinas)<0) {
				$jamLembur = "00:00";
			} else {
				if($ada=='')
				{$jamLembur = "00:00";}
				else
				$jamLembur = addZero(($jameff-$jamDinas),2).":".addZero($mneff,2);
			}
			######################################################## /Jam Lembur
			
			$sNm="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$rDet['karyawanid']."'";
			$qNm=mysql_query($sNm) or die(mysql_error());
			$rNm=mysql_fetch_assoc($qNm);
			
			$sAbsn="select keterangan from ".$dbname.".sdm_5absensi where kodeabsen='".$rDet['absensi']."'";
			$qAbsn=mysql_query($sAbsn) or die(mysql_error());
			$rAbsn=mysql_fetch_assoc($qAbsn);
			$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rNm['namakaryawan']."</td>
			<td>".$rDet['shift']."</td>
			<td>".$rAbsn['keterangan']."</td>
			<td>".$rDet['jam']."</td>
			<td>".$rDet['jamPlg']."</td>
			<td>".$jamkerja."</td>
			<td>".$jamDinas."</td>
			<td>".$jamLembur."</td>
			<td>".$rDet['penjelasan']."</td>
			<td>".$rDet['jamkeluar1']."</td>
			<td>".$rDet['ket1']."</td>
			<td>".$rDet['jammasuk1']."</td>
			<td>".$rDet['jamkeluar2']."</td>
			<td>".$rDet['ket2']."</td>
			<td>".$rDet['jammasuk2']."</td>
			<td>".$rDet['jamkeluar3']."</td>
			<td>".$rDet['ket3']."</td>
			<td>".$rDet['jammasuk3']."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDetail('".$rDet['karyawanid']."','".$rDet['shift']."','".$rDet['absensi']."','".$rDet['jam']."','".$rDet['jamPlg'].
			"','".$rDet['jamkeluar1']."','".$rDet['ket1']."','".$rDet['jammasuk1'].
			"','".$rDet['jamkeluar2']."','".$rDet['ket2']."','".$rDet['jammasuk2'].
			"','".$rDet['jamkeluar3']."','".$rDet['ket3']."','".$rDet['jammasuk3'].
			"','".$rDet['penjelasan']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeorg']."','".tanggalnormal($rDet['tanggal'])."','".$rDet['karyawanid']."');\" ></td>
			</tr>
			";
		}
		
		break;
		default:
		break;
	}

?>