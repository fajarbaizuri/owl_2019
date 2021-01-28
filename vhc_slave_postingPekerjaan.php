<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$kode_jns=$_POST['jns_id'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$user_entry=$_SESSION['standard']['userid'];
$kode_vhc=$_POST['kode_vhc'];
$tgl_kerja=tanggalsystem($_POST['tglKerja']);
$kmhmAwal=$_POST['kmhmAwal'];
$kmhmAkhir=$_POST['kmhmAkhir'];
$satuan=$_POST['satuan'];
$jnsBbm=$_POST['jnsBbm'];
$jumlahBbm=$_POST['jumlah'];
$notransaksi_head=$_POST['no_trans'];
$proses=$_POST['proses'];
$kdVhc=$_POST['kdVhc'];
$statKary=0;
	$sOrg="select kodeorganisasi from ".$dbname.".organisasi where  kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";//tipe in ('KEBUN','KANWIL','TRAKSI')";
	
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$kodeOrg.="'".$rOrg['kodeorganisasi']."',";
	}
$pnjgn=strlen($kodeOrg)-1;
switch($proses)
{
	case'load_data_header':
	echo"
	<table cellspacing='1' border='0' class=sortable>
	<thead>
	<tr class=\"rowheader\">
	<td>No.</td>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['jenisvch']."</td>
	<td>".$_SESSION['lang']['kodevhc']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>".
	//"<td>".$_SESSION['lang']['vhc_kmhm_awal']."</td>
	//<td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
	//<td>".$_SESSION['lang']['satuan']."</td>".
	"<td>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
	<td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
	<td>Action</td>
	</tr></thead><tbody>";
	
	
	//exit("Error".substr($inKodeorg,0,$pnjgn));
	$limit=20;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	//$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where kodeorg='".$lokasi."' order by notransaksi desc";// echo $ql2;
	$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where kodeorg in (".substr($kodeOrg,0,$pnjgn).")  order by notransaksi desc";// echo $ql2;
	//$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where kodeorg in (".substr($kodeOrg,0,$pnjgn).")  order by posting desc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}
	
	//$sql="select * from ".$dbname.".vhc_runht where kodeorg='".$lokasi."' order by notransaksi desc limit ".$offset.",".$limit."";
	$sql="select * from ".$dbname.".vhc_runht where kodeorg in (".substr($kodeOrg,0,$pnjgn).") order by notransaksi desc limit ".$offset.",".$limit."";
	//$sql="select * from ".$dbname.".vhc_runht where kodeorg in (".substr($kodeOrg,0,$pnjgn).") order by posting asc limit ".$offset.",".$limit."";
	//exit("Error".$sql);
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
		$qbrg=mysql_query($sbrg) or die(mysql_error());
		$rbrg=mysql_fetch_assoc($qbrg);
		$rbrg['namabarang'];
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=center>".$res['notransaksi']."</td>
		<td align=center>".$res['jenisvhc']."</td>
		<td align=center>".$res['kodevhc']."</td>
		<td align=center>".tanggalnormal($res['tanggal'])."</td>".
		//"<td align=center>".$res['kmhmawal']."</td>
		//<td align=center>".$res['kmhmakhir']."</td>
		//<td align=center>".$res['satuan']."</td>".
		"<td align=center>".$rbrg['namabarang']."</td>
		<td align=center>".$res['jlhbbm']."</td>
		";
		$sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['kodejabatan']==120)
		{
		echo"
		<td>
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">";
		if($res['posting']<1  and $_SESSION['empl']['lokasitugas']==substr($res['notransaksi'],0,4))
		{
			echo"&nbsp;<a href=# onClick=postingData('".$res['notransaksi']."')>".$_SESSION['lang']['belumposting']."</a>";
		}
		else
		{
			echo "&nbsp;".$_SESSION['lang']['posting'];
		}
		echo"</td>";}
		else
		{
			echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\"></td>";
		}
		echo"</tr>";
	
	}
	echo" <tr><td colspan=11 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
				<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr></tbody>
</table>";
	break;
	case 'cari_transaksi':
	//echo"warning :masuk";
	
	///exit("Error".$sOrg);
	echo"
	<div style='overflow:auto; height:550px;'>
	<table cellspacing='1' border='0' class=\"sortable\">
	<thead>
	<tr class=\"rowheader\">
	<td>No.</td>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['jenisvch']."</td>
	<td>".$_SESSION['lang']['kodevhc']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['vhc_kmhm_awal']."</td>
	<td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
	<td>".$_SESSION['lang']['satuan']."</td>
	<td>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
	<td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
	<td>Action</td>
	</tr></thead><tbody>";		
		if(isset($_POST['txtSearch']))
		{
			$txt_search=$_POST['txtSearch'];
			$txt_tgl=tanggalsystem($_POST['txtTgl']);
			$txt_tgl_a=substr($txt_tgl,0,4);
			$txt_tgl_b=substr($txt_tgl,4,2);
			$txt_tgl_c=substr($txt_tgl,6,2);
			$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
		}
		else
		{
			$txt_search='';
			$txt_tgl='';			
		}
			if($_POST['txtSearch']!='')
			{
				//echo"warning:notransaksi tidak boleh koso";
				$where="and notransaksi LIKE  '%".$txt_search."%'";
				
			}
		
			if($_POST['txtTgl']!='')
			{
				$where.="and tanggal = '".$txt_tgl."'";
			}
			
		//echo $strx; exit();
		//exit("Error".$_SESSION['org']['kodeorganisasi']);
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		
		
			//$strx="select * from ".$dbname.".vhc_runht where kodeorg='".$lokasi."' ".$where." order by notransaksi desc";
			$strx="select * from ".$dbname.".vhc_runht where  kodeorg in (".substr($kodeOrg,0,$pnjgn).") ".$where." order by notransaksi desc limit ".$offset.",".$limit."";
			$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where  kodeorg in (".substr($kodeOrg,0,$pnjgn).") ".$where." order by notransaksi desc";
				//echo "warning:".$strx; exit();
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
			if($qres=mysql_query($strx))
			{
				$numrows=mysql_num_rows($qres);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=11>Not Found</td></tr>";
				}
				else
				{
	while($res=mysql_fetch_assoc($qres))
	{
		$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
		$qbrg=mysql_query($sbrg) or die(mysql_error());
		$rbrg=mysql_fetch_assoc($qbrg);
		$rbrg['namabarang'];
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=center>".$res['notransaksi']."</td>
		<td align=center>".$res['jenisvhc']."</td>
		<td align=center>".$res['kodevhc']."</td>
		<td align=center>".tanggalnormal($res['tanggal'])."</td>
		<td align=center>".$res['kmhmawal']."</td>
		<td align=center>".$res['kmhmakhir']."</td>
		<td align=center>".$res['satuan']."</td>
		<td align=center>".$rbrg['namabarang']."</td>
		<td align=center>".$res['jlhbbm']."</td>
		";
		$sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['kodejabatan']==120)
		{
		//if($res['updateby']!=$_SESSION['standard']['userid'])
//		{
		echo"
		<td>
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">";
		if($res['posting']<1  and $_SESSION['empl']['lokasitugas']==substr($res['notransaksi'],0,4))
		{
			echo"&nbsp;<a href=# onClick=postingData('".$res['notransaksi']."')>".$_SESSION['lang']['belumposting']."</a>";
		}
		else
		{
			echo "&nbsp;".$_SESSION['lang']['posting'];
		}
		echo"</td>";}
		else
		{
			echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\"></td>";
		}
	
	}
	echo" <tr><td colspan=11 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
				<br />
				<button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";
	echo" </tbody></table></div>";
				}
			 }	
			else
			{
				echo "Gagal,".(mysql_error($conn));
			}	
		break;
	case'postData':
	//echo "warning:masuk";
		$scek="select kodeorg,updateby from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";//echo "warning".$scek;
		$qcek=mysql_query($scek) or die(mysql_error());
		$rcek=mysql_fetch_assoc($qcek);
		$sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['kodejabatan']!=120)
		{
			echo"warning:Anda tidak memiliki autorisasi!!";
			exit();
		}		
		$sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";
		if(mysql_query($sudPost))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	break;
	
	
	case'postingDa':
	//echo "warning:masuk";
	//$scek="select a.tanggal,a.updateby,b.idkaryawan from ".$dbname.".vhc_runht a left join ".$dbname.".vhc_runhk b where notransaksi='".$notransaksi_head."'";//echo "warning".$scek;
	/*$scek="select a.updateby,b.idkaryawan,a.satuan,a.tanggal,a.kodevhc from ".$dbname.".vhc_runht a left join  ".$dbname.".vhc_runhk b on a.notransaksi=b.notransaksi where a.notransaksi='".$notransaksi_head."' and a.`posting`=0 ";*/
	//echo"warning".$scek;exit();
	$sCek="select tanggal,kodevhc from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."' and `posting`=0 ";
	//echo $sCek;
	$qcek=mysql_query($sCek) or die(mysql_error());
	$baris=mysql_num_rows($qcek);
	//echo "warning".$baris;exit();
	if($baris!=1)
	{
		echo"warning: No.Transaksi :".$notransaksi_head." sudah terposting";
		exit();
	}
	else
	{
	// data dari header
	$rcek=mysql_fetch_assoc($qcek);
	$tgl=substr($rcek['tanggal'],0,4);
	$tglm=substr($rcek['tanggal'],5,2);
	$period=$tgl."-".$tglm;
	//cek kendaraan untuk //cek operator jika kendaraan bukan sewa
	$sStatKend="select kepemilikan from ".$dbname.".vhc_5master where kodevhc='".$rCek['kodevhc']."'";
	$qStatKend=mysql_query($sStatKend) or die(mysql_error());
	$rStatKend=mysql_fetch_assoc($qStatKend);
		if($rStatKend['kepemilikan']!=0)
		{	
			$sOpt="select idkaryawan from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."'";
			$qOpt=mysql_query($sOpt) or die(mysql_error());
			$rOpt=mysql_num_rows($qOpt);
			if($rOpt==0)
			{
				echo"warning:Tidak dapat diposting, operator kosong!!";
				exit();
			}
			else
			{
				//while($rCopt=mysql_fetch_assoc($qOpt))
				//{
				//	$sPost="select a.satuan from ".$dbname.".vhc_runht a left join ".$dbname.".vhc_runhk b on a.notransaksi=b.notransaksi where b.idkaryawan='".$rCopt['idkaryawan']."' and a.tanggal like '%".$period."%' group by a.satuan";
				//	//echo"warning".$sPost;exit();
				//	$qPost=mysql_query($sPost) or die(mysql_error());
				//	$rPost=mysql_num_rows($qPost);
				//	if($rPost>1)
				//	{
				//		$statKary+=1;
				//	}
				//}
				//if($statKary!=0)
				//{
				//	echo"warning: Posting Gagal, ada ".$statKary." orang, berada di dua satuan !!!";
				//	exit();
				//}
				//else
				//{
					$sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";
					//echo"warning".$sudPost;exit();
					if(mysql_query($sudPost))
					echo"";
					else
					echo "DB Error : ".mysql_error($conn);
				//}
			
			}
		}
		else
		{
			$sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";
			//echo"warning".$sudPost;exit();
			if(mysql_query($sudPost))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		//echo"warning".$sPost;exit();
	}
	break;
	case'postingByTrip':
	//echo "warning:masuk";
	$sNotrans="select a.*,b.idkaryawan,b.posisi,c.alokasibiaya,c.jenispekerjaan,c.jumlahrit,c.beratmuatan from 
	".$dbname.".vhc_runht a inner join ".$dbname.".vhc_runhk b on a.notransaksi=b.notransaksi 
	inner join ".$dbname.".vhc_rundt c on c.notransaksi=b.notransaksi
	where a.notransaksi='".$notransaksi_head."'"; //echo"warning:".$sNotrans;
	$qNotrans=mysql_query($sNotrans) or die(mysql_error());
	while($rNotrans=mysql_fetch_assoc($qNotrans))
	{
		$rNotrans['alokasibiaya']=substr($rNotrans['alokasibiaya'],0,4);
		$sPremi="select keycode from ".$dbname.".setup_mappremi where kodeorg='".$rNotrans['alokasibiaya']."'";//	echo"warning:".$sPremi;
		$qPremi=mysql_query($sPremi) or die(mysql_error());
		$rPremi=mysql_fetch_assoc($qPremi);
		if($rNotrans['premi']=='1')
		{	
			if($rPremi['keycode']=='TRANS02')
			{
				//$data=array();
				//Cek jumlah minimum,nomor (berkaitan dengan premi dan penalty) pada setup premi tansport yang berdasarkan jumlah trip
				$sKbn="select keycode,jumlahtrip,nomor,rate from ".$dbname.".kebun_5ratetransport where keycode='".$rPremi['keycode']."' 
				and tipeangkutan='".$rNotrans['jenispekerjaan']."' and jobposition='".$rNotrans['posisi']."'";
				$qKbn=mysql_query($sKbn) or die(mysql_error());
				$rKbn=mysql_fetch_assoc($qKbn);
				if($rNotrans['jumlahrit']>=$rKbn['jumlahtrip'])
				{
					$set=" premi='".$rKbn['rate']."'";
					//echo "warning:masuk a".$set;
				}
				else if($rNotrans['jumlahrit']<$rKbn['jumlahtrip'])
				{
					$set=" premi='0'";
					//echo "warning:masuk b".$set;
				}
				
				/*//get penalty dari detail setup premi transport
				$sKbnDet="select proporsipenalty from ".$dbname.".kebun_5ratetransport2 
				where nomor='".$rKbn['nomor']."' and jobposition='".$rNotrans['posisi']."'";
				//echo "warning".$sKbnDet."___";
				$qKbnDet=mysql_query($sKbnDet) or die(mysql_error());
				$rKbnDet=mysql_fetch_assoc($qKbnDet);
				if(($rNotrans['jumlahrit']<$rKbn['jumlahtrip'])&&($rNotrans['beratmuatan']<$rKbn['jumlahbasis']))
				{
					$set=" penalty='".$rKbnDet['proporsipenalty']."'";
				}
				else if(($rNotrans['jumlahrit']>=$rKbn['jumlahtrip'])&&($rNotrans['beratmuatan']>=$rKbn['jumlahbasis']))
				{
					$set=" premi='".$rKbn['rate']."'";
				}
				else if(($rNotrans['jumlahrit']<$rKbn['jumlahtrip'])&&($rNotrans['beratmuatan']>=$rKbn['jumlahbasis']))
				{
					$set=" premi='".$rKbn['rate']."' and penalty='".$rKbnDet['proporsipenalty']."'";
				}
				else if(($rNotrans['jumlahrit']>=$rKbn['jumlahtrip'])&&($rNotrans['beratmuatan']<$rKbn['jumlahbasis']))
				{
					$set=" penalty='".$rKbnDet['proporsipenalty']."' and premi='".$rKbn['rate']."'";
				}
				*/
			
				$sIsi="update ".$dbname.".vhc_runhk set ".$set." where notransaksi='".$rNotrans['notransaksi']."' 
				and idkaryawan='".$rNotrans['idkaryawan']."' and posisi='".$rNotrans['posisi']."'";
				//echo "warning:".$sIsi."____";
				if(mysql_query($sIsi))
				{
					$sHead="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";	
					if(mysql_query($sHead))
					echo"";
					else
					echo "DB Error : ".mysql_error($conn);
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);		
				}
			}
			elseif($rPremi['keycode']=='TRANS01')
			{
				
				$sKbn="select keycode,jaraksampai,jarakdari,nomor,rate from ".$dbname.".kebun_5ratetransport where keycode='".$rPremi['keycode']."' 
				and tipeangkutan='".$rNotrans['jenispekerjaan']."' and jobposition='".$rNotrans['posisi']."'";
				$qKbn=mysql_query($sKbn) or die(mysql_error());
				$rKbn=mysql_fetch_assoc($qKbn);
				if($rNotrans['jumlah']>=$rKbn['jaraksampai'])
				{
					$setBasis=" premi='".$rKbn['rate']."'";
				}
				else if($rNotrans['jumlah']<$rKbn['jarakdari'])
				{
					$setBasis=" premi='0'";
				}
				else if(($rNotrans['jumlah']>$rKbn['jarakdari'])&&($rNotrans['jumlah']<$rKbn['jaraksampai']))
				{
					$setBasis=" premi='".$rKbn['rate']."'";
				}
				
				/*$sKbn="select nomor from ".$dbname.".kebun_5ratetransport where keycode='".$rPremi['keycode']."' 
				and tipeangkutan='".$rNotrans['jenispekerjaan']."' and (".$rNotrans['jumlah']." BETWEEN jarakdari and jaraksampai) and jumlahbasis<='".$rNotrans['beratmuatan']."'";
				//echo "warning:".$sKbn."____";
				$qKbn=mysql_query($sKbn) or die(mysql_error());
				$rKbn=mysql_fetch_assoc($qKbn);*/
				/*$sKbn="select jaraksampai,jarakdari,nomor,jumlahbasis from ".$dbname.".kebun_5ratetransport where keycode='".$rPremi['keycode']."' 
				and tipeangkutan='".$rNotrans['jenispekerjaan']."'";
				$qKbn=mysql_query($sKbn) or die(mysql_error());
				$rKbn=mysql_fetch_assoc($qKbn);
				
				$sKbnDet="select premilebihbasis,proporsipenalty from ".$dbname.".kebun_5ratetransport2 where nomor='".$rKbn['nomor']."' and jobposition='".$rNotrans['posisi']."'";
				//echo "warning".$sKbnDet."___6";
				$qKbnDet=mysql_query($sKbnDet) or die(mysql_error());
				$rKbnDet=mysql_fetch_assoc($qKbnDet);
				
				if(($rNotrans['jumlah']<$rKbn['jarakdari'])&&($rNotrans['beratmuatan']<$rKbn['jumlahbasis']))
				{
					$setBasis=" penalty='".$rKbnDet['proporsipenalty']."'";
					//echo"warning:b".$setBasis;
				}
				else if(($rNotrans['jumlah']>=$rKbn['jaraksampai'])&&($rNotrans['beratmuatan']>=$rKbn['jumlahbasis']))
				{
					$setBasis=" premi='".$rKbnDet['premilebihbasis']."'";
					//echo"warning:a".$setBasis;
				}
				else if(($rNotrans['jumlah']>$rKbn['jarakdari'])&&($rNotrans['jumlah']<$rKbn['jaraksampai'])&&($rNotrans['beratmuatan']>=$rKbn['jumlahbasis']))
				{
					$setBasis=" premi='".$rKbnDet['premilebihbasis']."'";
					//echo"warning:c".$setBasis;
				}
				else if(($rNotrans['jumlah']>=$rKbn['jaraksampai'])&&($rNotrans['beratmuatan']<$rKbn['jumlahbasis']))
				{
					$setBasis=" penalty='".$rKbnDet['proporsipenalty']."'";
					//echo"warning:d".$setBasis;	
				}
				else if(($rNotrans['jumlah']>$rKbn['jarakdari'])&&($rNotrans['jumlah']<$rKbn['jaraksampai'])&&($rNotrans['beratmuatan']<$rKbn['jumlahbasis']))
				{
					$setBasis=" penalty='".$rKbnDet['proporsipenalty']."'";
					//echo"warning:e".$setBasis;
				}
			*/
				$sIsi="update ".$dbname.".vhc_runhk set ".$setBasis." where notransaksi='".$rNotrans['notransaksi']."' 
				and idkaryawan='".$rNotrans['idkaryawan']."' and posisi='".$rNotrans['posisi']."'";//;echo "warning:".$sIsi."____2";exit();
				
				if(mysql_query($sIsi))
				{
					$data=array();
					/*$sCek="select premi,penalty from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."'";
					$qCek=mysql_query($sCek) or die(mysql_error());
					while($rCek=mysql_fetch_assoc($qCek))
					{
						if(
					}*/
					
					$sHead="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";	
					if(mysql_query($sHead))
					echo"";
					else
					echo "DB Error : ".mysql_error($conn);
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);		
				}
			}
		}
	}
	break;
	default:
	break;	
}


?>