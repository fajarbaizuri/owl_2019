<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$notransaksi_head=isset($_POST['notrans'])?$_POST['notrans']:'';
$notransaksi=isset($_POST['noOptrans'])?$_POST['noOptrans']:'';
$proses=isset($_POST['proses'])?$_POST['proses']:'';
$lokasi=$_SESSION['empl']['lokasitugas'];
$jnsPekerjaan=isset($_POST['jnsPekerjaan'])?$_POST['jnsPekerjaan']:'';
$lokKerja=isset($_POST['locationKerja'])?$_POST['locationKerja']:'';
$muatan=isset($_POST['muatan'])?$_POST['muatan']:'';
$brtMuatan=isset($_POST['brtmuatan'])?$_POST['brtmuatan']:'';
$jmlhRit=isset($_POST['jmlhRit'])?$_POST['jmlhRit']:'';
$ket=isset($_POST['ket'])?$_POST['ket']:'';
$posisi=isset($_POST['posisi'])?$_POST['posisi']:'';
$kdKry=isset($_POST['kdKry'])?$_POST['kdKry']:'';
$oldjnsPekerjaan=isset($_POST['oldjnsPekerjaan'])?$_POST['oldjnsPekerjaan']:'';
$uphOprt=isset($_POST['uphOprt'])?$_POST['uphOprt']:'';
$prmiOprt=isset($_POST['prmiOprt'])?$_POST['prmiOprt']:'';
$pnltyOprt=isset($_POST['pnltyOprt'])?$_POST['pnltyOprt']:'';
$tglTrans=isset($_POST['tglTrans'])?tanggalsystem($_POST['tglTrans']):'';
$thnKntrk=isset($_POST['thnKntrk'])?$_POST['thnKntrk']:'';
//$lksiTgs=substr($_SESSION['empl']['lokasitugas'],0,4);
$noKntrak=isset($_POST['noKntrak'])?$_POST['noKntrak']:'';
$biaya=isset($_POST['biaya'])?$_POST['biaya']:'';
$Blok=isset($_POST['Blok'])?$_POST['Blok']:'';
$oldBlok=isset($_POST['oldBlok'])?$_POST['oldBlok']:'';
$old_lokKerja=isset($_POST['old_lokKerja'])?$_POST['old_lokKerja']:'';
if($notransaksi_head!='')
{
	$sKode="select kodeorg from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
	$qKode=mysql_query($sKode) or die(mysql_error());
	$rKode=mysql_fetch_assoc($qKode);
}
switch($proses)
{
	case 'load_data_kerjaan':
		$sql="select * from ".$dbname.".vhc_rundt where substring(notransaksi,1,4)='".$rKode['kodeorg']."' and notransaksi='".$notransaksi_head."' order by notransaksi desc";// echo $sql;
		$query=mysql_query($sql) or die(mysql_error());
		$no=0;
		while($res=mysql_fetch_assoc($query))
		{
			$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$res['notransaksi']."</td>
			<td>".$res['jenispekerjaan']."</td>
			<td>".number_format($res['kmhmawal'],2)."</td>
			<td>".number_format($res['kmhmakhir'],2)."</td>
			<td>".$res['satuan']."</td>
			<td>".$res['alokasibiaya']."</td>
			<td>".number_format($res['jumlahrit'],2)."</td>
			<td>".number_format($res['beratmuatan'],2)."</td>
			<td>".number_format($res['biaya'],2)."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' 
			onclick=\"fillFieldKrj('".$res['jenispekerjaan']."','".$res['alokasibiaya']."','". $res['beratmuatan']."','". $res['jumlahrit']."','". $res['keterangan']."','". $res['biaya']."','". $res['kmhmawal']."','". $res['kmhmakhir']."','". $res['satuan']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataKrj('". $res['notransaksi']."','". $res['jenispekerjaan']."');\" >	
			</td>
			</tr>
			";
		}
		break;
	
	case'insert_pekerjaan':
		if($notransaksi_head=='')
		{
			echo"warning:Input data header terlebih dahulu";
			exit();
		}
			if($jnsPekerjaan=='')
			{
				echo"warning:Jenis Pekerjaan Tidak Boleh Kosong";
				exit();
				
			}
			if($lokKerja=='')
			{
				echo"warning:Alokasi Biaya Tidak Boleh Kosong";
				exit();
				
			}
			
			
		$sCekHt="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
	
		$qCekHt=mysql_query($sCekHt) or die(mysql_error());
		$rCekHt=mysql_num_rows($qCekHt);
		if($rCekHt<1)
		{
			echo"warning:Please Input Data On Header Entry";
			exit();
		}
		/*$sCekDt="select count(jenispekerjaan) as jmlh from ".$dbname.".vhc_rundt where notransaksi='".$notransaksi_head."'";
		//echo"warning:".$sCekDt;
		$qCekDt=mysql_query($sCekDt) or die(mysql_error());
		$rCekDt=mysql_fetch_assoc($qCekDt);
		if($rCekDt['jmlh']==0)
		{*/
		if($Blok!='')
		{
			$lokKerja=$Blok;
		}
		
        if($biaya=='')
            $biaya=0;
        $sins="insert into ".$dbname.".vhc_rundt (`notransaksi`,`jenispekerjaan`,".
			"`alokasibiaya`,`beratmuatan`,`jumlahrit`,`keterangan`,`biaya`,`kmhmawal`,`kmhmakhir`,`satuan`,`jumlah`)".
			" values ('".$notransaksi_head."','".$jnsPekerjaan."','".$lokKerja."','".
			$brtMuatan."','".$jmlhRit."','".$ket."','".$biaya."','".$param['kmhmAwal'].
			"','".$param['kmhmAkhir']."','".$param['satuan']."','".($param['kmhmAkhir']-$param['kmhmAwal'])."')";

        if(mysql_query($sins))
		echo"";
		else
			echo "DB Error : ".mysql_error($conn);	 
		break;
	
	case'update_kerja':
	if(($brtMuatan=='')||($jmlhRit==''))
	{
		echo"warning:Please Complete The Form";
		exit();
	}
	$where='';
	if($Blok!='')
	{
		$lokKerja=$Blok;
		if($lokKerja!=$oldBlok)
		{
			$where.=" and alokasibiaya='".$oldBlok."'";
		}
		else
		{
			$where.=" and alokasibiaya='".$lokKerja."'";
		}
	}
	else
	{
		if($old_lokKerja!=$lokKerja)
		{
			$where.=" and alokasibiaya='".$old_lokKerja."'";
		}
		else
		{
			$where.=" and alokasibiaya='".$lokKerja."'";
		}
	}
	if($oldjnsPekerjaan!='')
	{
		if($jnsPekerjaan!=$oldjnsPekerjaan)
		{
			$where.="  and jenispekerjaan='".$oldjnsPekerjaan."'";
		}
		else
		{
			$where.="  and jenispekerjaan='".$jnsPekerjaan."'";
		}
	}
	$sup="update ".$dbname.".vhc_rundt set jenispekerjaan='".$jnsPekerjaan.
		"',alokasibiaya='".$lokKerja."',beratmuatan='".$brtMuatan."',jumlahrit='".
		$jmlhRit."',keterangan='".$ket."',biaya='".$biaya."',kmhmawal='".$param['kmhmAwal'].
		"',kmhmakhir='".$param['kmhmAkhir']."',satuan='".$param['satuan']."',jumlah='".
		($param['kmhmAkhir']-$param['kmhmAwal'])."' where notransaksi='".$notransaksi_head."' ".$where."";
	//exit("Error:".$sup);
	if(mysql_query($sup))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);	 
	break;
	
	case'deleteKrj':
	$delKrj="delete from ".$dbname.".vhc_rundt where notransaksi='".$notransaksi_head."' and jenispekerjaan='".$jnsPekerjaan."'";
	if(mysql_query($delKrj))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);	 
	
	break;
	case'insert_operator':
	$sCekHt="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
//	echo"warning:".$sCekHt;
	$qCekHt=mysql_query($sCekHt) or die(mysql_error());
	$rCekHt=mysql_num_rows($qCekHt);
	if($rCekHt<1)
	{
		echo"warning:Input data header terlebih dahulu";
		exit();
        }
        
	$sPeriode="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($rKode['kodeorg'],0,4)."' and periode='".substr($tglTrans,0,4)."-".substr($tglTrans,4,2)."'";# tanggalmulai<".$tglTrans." and tanggalsampai>=".$tglTrans;
	//echo $sPeriode;
        //exit("Error:");
	$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
	$rPeriode=mysql_fetch_assoc($qPeriode);
	//echo"warning".$rPeriode['periode'];exit();
	if($rPeriode['periode']=='')
	{
	echo"warning:Tanggal Transaksi Di luar Periode Gaji";
	exit();
	}
	else
	{
		$sKd="select lokasitugas,subbagian from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";
		$qKd=mysql_query($sKd) or die(mysql_error());
		$rKd=mysql_fetch_assoc($qKd);
		if(is_null($rKd['subbagian'])||$rKd['subbagian']==0)
		{
		$lokasiTugas=$rKd['lokasitugas'];
		}
		else
		{
		$lokasiTugas=$rKd['subbagian'];
		}
	}

	if($posisi==1)
	{
		$sCek="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='1'";
		//echo "warning:".$sCek;
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		
			if($rCek['jmlh']!=4)
			{
				$sqlIns="insert into ".$dbname.".vhc_runhk (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`) values ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."')";
				//echo"warning:".$sqlIns;
				if(mysql_query($sqlIns))
				{									
					//cek tanggal dan periode sudah ada di header atau blm
					$sInsAbsnC="select tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tglTrans."' and periode='".$rPeriode['periode']."'";
					$qInsAbsnC=mysql_query($sInsAbsnC) or die(mysql_error($conn));
					$rInsAbsnC=mysql_num_rows($qInsAbsnC);
					if($rInsAbsnC>0)
					{
					//echo"warning:Masuk aja A";
						$sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$rKode['kodeorg']."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";
						$qCek=mysql_query($sCek) or die(mysql_error($conn));
						$rCek=mysql_num_rows($qCek);
						if($rCek<1)
						{
						$sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";
						//echo"warning".$sUpdAbns;
							if(!mysql_query($sUpdAbns))
							{
							echo "DB Error : ".mysql_error($conn);
							}
						}
					}
					elseif($rInsAbsnC<1)
					{
						//echo"warning:Masuk aja B";
						$sInshead="insert into ".$dbname.".sdm_absensiht (`tanggal`, `kodeorg`, `periode`, `posting`) values('".$tglTrans."','".$lokasiTugas."','".$rPeriode['periode']."','0')";
						//echo"warning".$sInshead;
						if(mysql_query($sInshead))
						{
						$sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";
							if(!mysql_query($sUpdAbns))
							{
								echo "DB Error : ".mysql_error($conn);
							}	
						}
						else
						{
						echo "DB Error : ".mysql_error($conn);
						}					
					}
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);	
				}
			}
			else
			{
				echo"warning: Can`t Insert Kondektur, Already Reach Limit";
				exit();
			}
	}
	elseif($posisi==0)
	{
		$sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='0'";
		//echo "warning:".$sCekSop;
		$qCekSop=mysql_query($sCekSop) or die(mysql_error());
		$rCekSop=mysql_fetch_assoc($qCekSop);
		if($rCekSop['jmlh']==1)
		{
			echo"warning: Sopir tidah boleh lebih dari satu";
			break;
			exit();
		}
		elseif($rCekSop['jmlh']==0)
		{
					
				$sqlIns="insert into ".$dbname.".vhc_runhk (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`) values ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."')";
					//echo"warning:".$sqlIns;
				if(mysql_query($sqlIns))
				{
					//cek tanggal dan periode sudah ada di header atau blm
					$sInsAbsnC="select tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tglTrans."' and periode='".$rPeriode['periode']."'";
					$qInsAbsnC=mysql_query($sInsAbsnC) or die(mysql_error($conn));
					$rInsAbsnC=mysql_num_rows($qInsAbsnC);
					if($rInsAbsnC>0)
					{
					//echo"warning:Masuk aja A";
						$sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$rKode['kodeorg']."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";
						$qCek=mysql_query($sCek) or die(mysql_error($conn));
						$rCek=mysql_num_rows($qCek);
						if($rCek<1)
						{
						$sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";
						//echo"warning".$sUpdAbns;
							if(!mysql_query($sUpdAbns))
							{
							echo "DB Error : ".mysql_error($conn);
							}
						}
					}
					elseif($rInsAbsnC<1)
					{
						//echo"warning:Masuk aja B";
						$sInshead="insert into ".$dbname.".sdm_absensiht (`tanggal`, `kodeorg`, `periode`, `posting`) values('".$tglTrans."','".$lokasiTugas."','".$rPeriode['periode']."','0')";
						//echo"warning".$sInshead;
						if(mysql_query($sInshead))
						{
						$sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";
							if(!mysql_query($sUpdAbns))
							{
								echo "DB Error : ".mysql_error($conn);
							}	
						}
						else
						{
						echo "DB Error : ".mysql_error($conn);
						}					
					}
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);
				}
			}
	}
	break;
	case 'update_operator':
	if($posisi==1)
	{
		$sCek="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='1'";
		//echo "warning:".$sCek;
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
	}
	elseif($posisi==0)
	{
		$sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='0'";
		//echo "warning:".$sCekSop;
		$qCekSop=mysql_query($sCekSop) or die(mysql_error());
		$rCekSop=mysql_fetch_assoc($qCekSop);
	}
	if($rCek['jmlh']>4)
	{
		echo"warning: Can`t Insert Kondektur, Already Reach Limit";
		exit();
	}
	if($rCekSop['jmlh']>1)
	{
		echo"warning: Can`t Insert Sopir, Already Reach Limit";
		exit();
	}
	$skry="select a.`alokasi`,b.tipe from ".$dbname.".datakaryawan a inner join ".$dbname.".sdm_5tipekaryawan b on 
	a.tipekaryawan=b.id where karyawanid='".$kdKry."'"; 
	//echo "warning:".$skry;
	$qkry=mysql_query($skry) or die(mysql_error());
	$rkry=mysql_fetch_assoc($qkry);
	

	$sup_op="update ".$dbname.".vhc_runhk set posisi='".$posisi."',tanggal='".$tglTrans."',statuskaryawan='".$rkry['tipe']."',upah='".$uphOprt."',premi='".$prmiOprt."',penalty='".$pnltyOprt."' where notransaksi='".$notransaksi_head."' and idkaryawan='".$kdKry."'";
	if(mysql_query($sup_op))
	echo"";
	else
		echo "DB Error : ".mysql_error($conn);
	break;
	case'getUmr':
            if($_POST['tahun']!='')
                    $tahun=$_POST['tahun'];
            else {
                    $tahun=date('Y');
            }
	$sUmr="select sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok 
            where karyawanid='".$kdKry."' and tahun=".$tahun."  and idkomponen in (1,31)";
	$qUmr=mysql_query($sUmr) or die(mysql_error());
	$rUmr=mysql_fetch_assoc($qUmr);
	$umr=$rUmr['jumlah']/25;
	echo intval($umr);
	//$sUmr="select nilai from ".$dbname.".setup_parameterappl where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and kodeaplikasi='UP' and kodeparameter='UMR'";
//	//echo "warning".$sUmr;exit();
//	$qUmr=mysql_query($sUmr) or die(mysql_error());
//	$rUmr=mysql_fetch_assoc($qUmr);
//	$test=$rUmr['nilai'];
//	echo $tests;
	break;

	case'load_data_opt':
	$arrPos=array("Sopir","Kondektur");
	$sql="select * from ".$dbname.".vhc_runhk where substring(notransaksi,1,4)='".$rKode['kodeorg']."' and notransaksi='".$notransaksi_head."' order by notransaksi desc"; //echo "warning:".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	$no=0;
	while($res=mysql_fetch_assoc($query))
	{
		$skry="select `namakaryawan` from ".$dbname.".datakaryawan where karyawanid='".$res['idkaryawan']."'";
		$qkry=mysql_query($skry) or die(mysql_error());
		$rkry=mysql_fetch_assoc($qkry);
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$res['notransaksi']."</td>
		<td>".$rkry['namakaryawan']."</td>
		<td>".$arrPos[$res['posisi']]."</td>
		<td>".number_format($res['upah'],2)."</td>".
		"<td>".number_format($res['premi'],2)."</td>".
		"<td>".number_format($res['penalty'],2)."</td>
		<td align=center>
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."','". $res['idkaryawan']."');\" >	
		</td>
		</tr>
		";
	}
	break;
	case'getKntrk':
	$optKntrk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sSpk="select notransaksi from ".$dbname.".log_spkht where kodeorg='".$lokasi."' and posting<>'0' and tanggal like '%".$thnKntrk."%'";
	//echo "warning:".$sSpk;
	$qSpk=mysql_query($sSpk) or die(mysql_error());
	$rSpk=mysql_num_rows($qSpk);
	if($rSpk>0)
	{
		while($rSpk=mysql_fetch_assoc($qSpk))
		{
			$optKntrk.="<option value=".$rSpk['notransaksi']." ".($rSpk['notransaksi']==$noKntrak?'selected':'').">".$rSpk['notransaksi']."</option>";
		}
		
	}
	else
	{
		$optKntrk="<option value=''></option>";
		//echo $optKntrk;
	}
	echo $optKntrk;
	break;
	
	case'delete_opt':
	$sdel="delete from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi."' and idkaryawan='".$kdKry."'";
	//echo "warning:".$sdel;
	if(mysql_query($sdel))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);
	break;
	case'getBlok':
	$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	//$sBlok="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk like '%".$lokKerja."%' and tipe='BLOK'";
	$sBlok="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk like '%".$lokKerja."%' and tipe in ('BLOK','AFDELING')";
	$qBlok=mysql_query($sBlok) or die(mysql_error());
	while($rBlok=mysql_fetch_assoc($qBlok))
	{
		if($Blok!="")
		{
			$optBlok.="<option value=".$rBlok['kodeorganisasi']." ".($rBlok['kodeorganisasi']==$Blok?"selected":"").">".$rBlok['namaorganisasi']."</option>";
		}
		else
		{
			$optBlok.="<option value=".$rBlok['kodeorganisasi'].">".$rBlok['namaorganisasi']."</option>";
		}
	}
	echo $optBlok;
	break;
	case 'getPremi':
		$query = selectQuery($dbname,'vhc_5premi',"kelompok,basis,siapborong,lebihborong",
			"kodeorg='".$param['kodeorg']."' and kodekegiatan='".$param['kodekegiatan']."'");
		$resData = fetchData($query);
		
		if(empty($resData)) {
			$premi=0;
		} else {
			if($resData[0]['kelompok']=='prestasi') {
				if($param['prestasi']>=$resData[0]['basis']) {
					$premi=$resData[0]['siapborong']+
						(($param['prestasi']-$resData[0]['basis'])*$resData[0]['lebihborong']);
				} else {
					$premi=0;
				}
			} else {
				if($param['hmkm']>=$resData[0]['basis']) {
					$premi=$resData[0]['siapborong']+
						(($param['hmkm']-$resData[0]['basis'])*$resData[0]['lebihborong']);
				} else {
					$premi=0;
				}
			}
		}
		echo $premi;
	default:
	break;
}
?>
	