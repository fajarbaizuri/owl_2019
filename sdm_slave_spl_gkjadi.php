<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('master_validation.php');
require_once('config/connection.php');

$method=$_POST['method'];
$idOrg=substr($_SESSION['empl']['lokasitugas'],0,4);

$notran=$_POST['notran'];
$kdorg=$_POST['kdorg'];
$tgl=tanggalsystem($_POST['tgl']);
$jam1=$_POST['jm1'].":".$_POST['mn1'].":00";
//exit("Error:$jam1");
$jam2=$_POST['jm2'].":".$_POST['mn2'].":00";
$pembuat=$_POST['pembuat'];
$penyetuju=$_POST['penyetuju'];
$pemeriksa=$_POST['pemeriksa'];
$divisi=$_POST['divisi'];

$tpl=$_POST['tpl'];
$ki=$_POST['ki'];
$ul=$_POST['ul'];
$um=$_POST['um'];
$ut=$_POST['ut'];
$tugas=$_POST['tugas'];

$scnotran=$_POST['scnotran'];
$tglcari=tanggalsystem($_POST['tglcari']);	

$optNm=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNik=makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$optDiv=makeOption($dbname, 'sdm_5departemen','kode,nama');

$arrTipeLembur=array($_SESSION['lang']['haribiasa'],$_SESSION['lang']['hariminggu'],$_SESSION['lang']['harilibur'],$_SESSION['lang']['hariraya']);

switch($method)
{
	case 'getnomor':
		if(isset($_POST['kdorg'])){
		$kodeorg=trim($_POST['kdorg']);
		if($_POST['kdorg']=='')
		{
			echo "warning:Kode Organisasi Inconsistent";
			exit();
		}
		else
		{
			$tgl=  date('Ymd');
			$bln = substr($tgl,4,2);
			$thn = substr($tgl,0,4);
				$notransaksi="/".date('Y')."/SPL/".$kodeorg;
				
				$ql="select `notransaksi` from ".$dbname.".`sdm_splht` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
				$qr=mysql_query($ql) or die(mysql_error());
				$rp=mysql_fetch_object($qr);
				$awal=substr($rp->notransaksi,0,3);
				$awal=intval($awal);
				$cekbln=substr($rp->notransaksi,4,2);
				$cekthn=substr($rp->notransaksi,7,4);
				
				//if(($bln!=$cekbln)&&($thn!=$cekthn))
				if($thn!=$cekthn)
				{
				//echo $awal; exit();
					$awal=1;
				}
				else
				{
					$awal++;
				}
				$counter=addZero($awal,3);
				$notransaksi=$counter."/".$bln."/".$thn."/SPL/".$kodeorg;
				echo $notransaksi;
			}
		}
	break;
	
	/*case'update':
	exit("Error:MASUK");
		$indra="update ".$dbname.".sdm_splht set `lock`='1' where notransaksi='".$notran."' ";
			if(mysql_query($indra))
			{
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
				//echo "a";
			}
	break;*/
	
	case'loadNewData':
		echo"
		<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>No. Transaksi</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>Divisi</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>Lembur Dari</td>
		<td>Sampai Dari</td>
		
		<td>Pembuat</td>
		
		<td>Penyetuju</td>
		<td>Status Persetujuan</td>
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
		
		$tmbh='';
                if($scnotran!='')
                {
                    $tmbh=" and notransaksi='".$scnotran."' ";
					//echo $tmbh;
                }
		
		$tmbh2='';
                if($tglcari!='')
                {
                    $tmbh2=" and tanggal='".$tglcari."' ";
					//echo $tmbh2;
                }
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_splht where substring(kodeorg,1,4)='".$idOrg."' ".$tmbh." ".$tmbh2." order by `notransaksi` desc";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$dr="select * from ".$dbname.".sdm_splht where  substring(kodeorg,1,4)='".$idOrg."' ".$tmbh." ".$tmbh2." order by `notransaksi` desc limit ".$offset.",".$limit."";
		$ra=mysql_query($dr) or die(mysql_error());
		//$user_online=$_SESSION['standard']['userid'];
		while($wib=mysql_fetch_assoc($ra))
		{
		$ind=$wib['lock'];			
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$wib['notransaksi']."</td>
		<td>".$wib['kodeorg']."</td>
		<td>".$optDiv[$wib['kode']]."</td>
		<td>".tanggalnormal($wib['tanggal'])."</td>
		<td align=center>".substr($wib['lemburdari'],0,5)."</td>
		<td align=center>".substr($wib['lembursampai'],0,5)."</td>
		<td>".$optNm[$wib['updateby']]."</td>
	
		<td>".$optNm[$wib['approvedby']]."</td>";
		
		if($ind==0)
		{
			echo "<td><a href=# onclick=Lock('".$wib['notransaksi']."')>Belum Disetujui</a></td>";	
		}
		else
		{
			echo"<td>Disetujui</td>";	
		}
				
		echo"
		
		<td align=center>";/*'".optNm($wib['approvedby'])."',
			'".optNm($wib['checkedby'])."',
			'".optNm($wib['updateby'])."'
			'".$wib['lembursampai']."',
			'".$wib['lembursampai']."',*/
		if($ind==0)
		{
		echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$wib['notransaksi']."','".$wib['kodeorg']."','".$wib['kode']."','".tanggalnormal($wib['tanggal'])."','".substr($wib['lemburdari'],0,2)."','".substr($wib['lemburdari'],3,2)."','".substr($wib['lembursampai'],0,2)."','".substr($wib['lembursampai'],3,2)."','".$wib['approvedby']."','".$wib['updateby']."');\">
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$wib['notransaksi']."');\" >
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_splht','".$wib['notransaksi']."','','sdm_spl_pdf',event)\">
		";
		}
		//
		//<img src=images/key_64.png class=resicon onclick=Lock('".$wib['notransaksi']."') title=Kunci>	
		//<img src=images/box/hmenu-lock.png class=resicon title=Terkunci>
		else
		{
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_splht','".$wib['notransaksi']."','','sdm_spl_pdf',event)\">";
		}
		echo"</td>
		
		</tr>
		
		";
		}
		echo"
		<tr class=rowheader><td colspan=12 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
		break;
		
		
	/*case'loadSch':
	//exit("Error:MASUK WOI");
		echo"
		<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>No. Transaksi</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>Lembur Dari</td>
		<td>Sampai Dari</td>
		<td>Action</td>
		</tr>
		</thead>
		<tbody>
		";
		
		$tmbh='';
                if($tglcari!='')
                {
                    $tmbh=" and tanggal='".$tglcari."' ";
					//echo $tmbh;
                }
		
		$dr="select * from ".$dbname.".sdm_splht where  kodeorg='".$idOrg."' ".$tmbh." ";
		//exit("Error:$dr");
		//echo $dr;
		$ra=mysql_query($dr) or die(mysql_error());
		$user_online=$_SESSION['standard']['userid'];
		while($wib=mysql_fetch_assoc($ra))
		{
		$ind=$wib['lock'];			
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$wib['notransaksi']."</td>
		<td>".$wib['kodeorg']."</td>
		<td>".tanggalnormal($wib['tanggal'])."</td>
		<td>".$wib['lemburdari']."</td>
		<td>".$wib['lembursampai']."</td>
		<td>";
		if($ind==0)
		{
		echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$wib['notransaksi']."','".$wib['kodeorg']."','".tanggalnormal($wib['tanggal'])."','".substr($wib['lemburdari'],0,2)."','".substr($wib['lemburdari'],3,2)."','".substr($wib['lembursampai'],0,2)."','".substr($wib['lembursampai'],3,2)."','".$wib['approvedby']."','".$wib['checkedby']."','".$wib['updateby']."');\">
		
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$wib['notransaksi']."');\" >
		<img src=images/key_64.png class=resicon onclick=Lock('".$wib['notransaksi']."') title=Kunci>	
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_splht','".$wib['notransaksi']."','','sdm_spl_pdf',event)\">";
		}
		else
		{
			echo"<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_absensiht','".$wib['notransaksi']."','','sdm_absensiPdf',event)\">
			<img src=images/box/hmenu-lock.png class=resicon title=Terkunci>";
		}
		echo"</td>
		
		</tr>
		
		";
		}

		echo"</tbody></table>";
		break;	*/
		
		
	##########case delete
	case 'delete':
		//exit("Error:Masuk Oi");
		$indra="delete from ".$dbname.".sdm_splht where notransaksi='".$notran."' ";
		//echo $indra;
		//exit($indra);
		if(mysql_query($indra))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
	
	##########case kunci
	case 'lock':
		$str="select * from ".$dbname.".sdm_splht where notransaksi='".$notran."' ";
		$res1 = fetchData($str);
		
		$post=$res1[0]['approvedby'];//approvedby,,checkedby
		$sama=$_SESSION['standard']['userid'];
		
		if($post==$sama)
		{
			$str2 = selectQuery($dbname,'sdm_spldt','*',"notransaksi='".$notran."'");
			$res2 = fetchData($str2);
			
			$dataH = array(
				'tanggal'=>$res1[0]['tanggal'],
				'kodeorg'=>$res1[0]['kodeorg'],
				'posting'=>0,
				'postingby'=>$post
			);
			$jam1 = explode(':',$res1[0]['lemburdari']);
			$jam2 = explode(':',$res1[0]['lembursampai']);
			if($jam2[0]<$jam1[0]) {
				$jam2[0]+=24;
			}
			$jam = $jam2[0]-$jam1[0];
			$menit = ($jam2[1]-$jam1[1])/60;
			$jamaktual = $jam+$menit;
			
			$dataD = array();
			foreach($res2 as $row) {
				$dataD[] = array(
					'kodeorg'=>$row['kodeorg'],
					'tanggal'=>$row['tanggal'],
					'karyawanid'=>$row['karyawanid'],
					'tipelembur'=>$row['tipelembur'],
					'jamaktual'=>$jamaktual,
					'uangmakan'=>$row['rpmakan'],
					'uangtransport'=>$row['rptransport'],
					'uangkelebihanjam'=>$row['rplembur']
				);
			}
			
			// Post ke Lembur
			$insHead = insertQuery($dbname,'sdm_lemburht',$dataH);
			if(!mysql_query($insHead)) {
				exit("DB Error: ".mysql_error());
			}
			
			$dbError = "";
			foreach($dataD as $det) {
				$insDet = insertQuery($dbname,'sdm_lemburdt',$det);
				if(!mysql_query($insDet)) {
					$dbError = "Detail Error: ".mysql_error()."\n";
				}
			}
			
			if(empty($dbError)) {
				$indra="update ".$dbname.".sdm_splht set `lock`='1',lockedby=".$sama." where notransaksi='".$notran."' ";
				if(mysql_query($indra))
				{
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
					//echo "a";
				}
			} else {
				// Rollback
				$whereRB = "notransaksi='".$notran."'";
				$del1 = deleteQuery($dbname,'sdm_lemburht',$whereRB);
				if(!mysql_query($del1)) {
					exit("Rollback Error: ".mysql_error());
				}
				$del2 = deleteQuery($dbname,'sdm_lemburdt',$whereRB);
				if(!mysql_query($del2)) {
					exit("Rollback Error: ".mysql_error());
				}
			}
		}
		else
		{
			exit("Error:Anda bukan penyetuju dokumen ini !");
			//echo "Anda tidak bisa mengunci data ini";
		}
		
		
		/*$indra="update ".$dbname.".sdm_splht set `lock`='1' where notransaksi='".$notran."' ";
			if(mysql_query($indra))
			{
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}*/
		
		
	break;
	
	
	########### case insert header
	case 'simpanheader':
	
		//exit("Error:masuk");
		
		/*$iCek="select distinct notransaksi,tanggal,kodeorg from ".$dbname.".sdm_splht where notransaksi='".$notran."' and tanggal='".$tgl."' and kodeorg='".$kdorg."' ";
		//echo $iCek;
		$ada=true;
		$nCek=mysql_query($iCek)or die(mysql_error());
		while($dCek=mysql_fetch_assoc($nCek))
		{ 
			if ($ada==true)
			{
				echo "warning : No. Transaksi untuk '".$notran."' atau Tanggal untuk '".$tgl."' sudah ada";
				exit();	
			}
			else
			{
			}	
		}*/
		
		$indra="insert into ".$dbname.".sdm_splht (`notransaksi`,`kodeorg`,`kode`,`tanggal`,`lemburdari`,`lembursampai`,`approvedby`,`updateby`)
		values ('".$notran."','".$kdorg."','".$divisi."','".$tgl."','".$jam1."','".$jam2."','".$penyetuju."','".$_SESSION['standard']['userid']."')";
		
		//'".$_SESSION['standard']['userid']."'
		//exit("Error.$indra");
		if(mysql_query($indra))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	########### case insert detail
	case 'simpandetail':
		//exit("Error:masuk");
		$ul = str_replace(',','',$ul);
		$indra="insert into ".$dbname.".sdm_spldt (`notransaksi`,`karyawanid`,`tipelembur`,`kodeorg`,`tanggal`,`rplembur`,`rpmakan`,`rptransport`,`keterangan`)
		values ('".$notran."','".$ki."','".$tpl."','".$kdorg."','".$tgl."','".$ul."','".$um."','".$ut."','".$tugas."')";
		
		//$indra="insert into ".$dbname.".sdm_spldt (`tanggal`,`rplembur`,`rpmakan`,`rptransport`,`keterangan`)
		//values ('".$tgl."','".$ul."','".$um."','".$ut."','".$tugas."')";
		
		//'".$_SESSION['standard']['userid']."'
		//exit("Error.$indra");
		if(mysql_query($indra))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
		
	#####LOAD DETAIL DATA	
	case 'loadDetail';	
		//exit("Error:MASUK OI");
		$no=0;
		$str="select * from ".$dbname.".sdm_spldt where notransaksi='".$notran."' ";
		//echo $str;
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$optNm[$bar1['karyawanid']]."</td>";
			$tab.="<td align=left>".$optNik[$bar1['karyawanid']]."</td>";
			
			$tab.="<td align=left>".$arrTipeLembur[$bar1['tipelembur']]."</td>";
			
			
			$tab.="<td align=right>".number_format($bar1['rplembur'])."</td>";
			$tab.="<td align=right>".number_format($bar1['rpmakan'])."</td>";
			$tab.="<td align=right>".number_format($bar1['rptransport'])."</td>";
			$tab.="<td align=left>".$bar1['keterangan']."</td>";
			$tab.="<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".$bar1['notransaksi']."','".$bar1['karyawanid']."');\" ></td>";
		echo $tab;
		}
	break;


	
	##########case delete detail
		case 'deletedetail':
			//exit("Error:Masuk");
			$tab="delete from ".$dbname.".sdm_spldt where notransaksi='".$notran."' and karyawanid='".$ki."' ";
			//echo $tab;
			//exit("Error:$tab");
			if(mysql_query($tab))
			{
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}			
		break;	
		
	####################
	case 'uangLembur':
		$param = $_POST;
		$tgl = explode('-',$param['tgl']);
		
		## Get Gaji Pokok
		$q1 = selectQuery($dbname,'sdm_5gajipokok',"*","idkomponen=1 and tahun=".$tgl[2].
			" and karyawanid='".$param['ki']."'");
		$res1 = fetchData($q1);
		$gaji=$res1[0]['jumlah']/173;
		
		## Hitung Jam
		$min1 = $param['mn1'];
		$min2 = $param['mn2'];
		$min = $min2-$min1;
		if($min<0) {
			$jam2-=1;
			$min+=60;
		}
		$jam1 = $param['jm1'];
		$jam2 = $param['jm2'];
		if($jam2<$jam1) {
			$jam2+=24;
		}
		$jam = $jam2-$jam1;
		$menit = ($min)/60;
		$jamaktual = $jam+$menit;
		
		## Get Pengali
		$q2 = selectQuery($dbname,'sdm_5lembur',"jamaktual,jamlembur",
			"kodeorg='".$param['kdorg']."' and tipelembur='".$param['tpl'].
			"' and jamaktual<".$jamaktual,'jamaktual desc',false,1,1);
		$res2 = fetchData($q2);
		$uanglembur = $res2[0]['jamlembur']*$gaji;
		//echo round($gaji).'##'.$res2[0]['jamlembur'];
		echo round($uanglembur);
	break;
}
?>	