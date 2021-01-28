<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##periodeGaji##kdOrg##idKaryawan##jmlhHk##kdeBarang##hrgSatuan##jmlh";

$method=$_POST['method'];
$kodeOrg=$_POST['kodeOrg'];
if($_POST['kryId']=='')
{
	$_POST['kryId']=$_POST['idKaryawan'];
}
$kryId=$_POST['kryId'];
$txtfind=$_POST['txtfind'];
$periodeGaji=$_POST['periodeGaji'];
$kdeBarang=$_POST['kdeBarang'];
$kdOrg=$_POST['kdOrg'];
$idKaryawan=$_POST['idKaryawan'];
$jmlhHk=$_POST['jmlhHk'];
$hrgSatuan=$_POST['hrgSatuan'];
$jmlh=$_POST['jmlh'];
$satuan=$_POST['satuan'];
$where=" karyawanid='".$kryId."' and periodegaji='".$periodeGaji."' and kodebarang='".$kdeBarang."' and kodeorg='".$kdOrg."'";

//$arrPlokal="##txtsearch##kodeLksi##prdGaji";
$txtsearch=$_POST['txtsearch'];
$kodeLksi=$_POST['kodeLksi'];
$prdGaji=$_POST['prdGaji'];
	switch($method)
	{
		case'insert':
		$sCek="select karyawanid from ".$dbname.".sdm_catu where ".$where." ";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Karyawan ini sudah terinput";
			exit();
		}
		else
		{
			$sIns="insert into ".$dbname.".sdm_catu (`karyawanid`,`periodegaji`,`jumlahhk`,`kodebarang`,`jumlah`,`hargasatuan`,`satuan`,	`kodeorg`,`updateby`) values ('".$idKaryawan."','".$periodeGaji."','".$jmlhHk."','".$kdeBarang."','".$jmlh."','".$hrgSatuan."','".$satuan."','".$kdOrg."','".$_SESSION['standard']['userid']."')";
			//echo "warning".$sIns;exit();
			if(!mysql_query($sIns))
			{
				echo"Gagal".mysql_error($conn);
			}
		}
		break;
		case'loadData':
		$no=0;	 
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$sql2="select count(*) as jmlhrow from ".$dbname.".sdm_catu where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'  order by updateTime desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$str="select * from ".$dbname.".sdm_catu  where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by updateTime desc";
		$res=mysql_query($str);
		while($bar=mysql_fetch_assoc($res))
		{
			$sKry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar['karyawanid']."'";
			$qKry=mysql_query($sKry) or die(mysql_error());
			$rKry=mysql_fetch_assoc($qKry);
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$bar['periodegaji']."</td>
		<td>".$bar['kodeorg']."</td>
		<td>".$rKry['namakaryawan']."</td>
		<td>".$rBrg['namabarang']."</td>
		<td>".number_format($bar['hargasatuan'],2)."</td>
		<td>".number_format($bar['jumlah'],2)."</td>
		<td>
			  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['karyawanid']."','".$bar['periodegaji']."','".$bar['kodebarang']."','".$bar['kodeorg']."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['karyawanid']."','".$bar['periodegaji']."','".$bar['kodebarang']."','".$bar['kodeorg']."');\">
		  </td>
		
		</tr>";	
		}     
		echo"
				 <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>"; 
				echo"</tbody> </table>";
		break;
		case'getDataNm':
		//"##txtsearch##kodeLksi##prdGaji";
		$no=0;	 
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		if($kodeLksi!="")
		{
			$whereSrc.=" and kodeorg='".$kodeLksi."'";	
		}
		if($prdGaji!="")
		{
			$whereSrc.=" and periodegaji='".$prdGaji."'";
		}
			$sql2="select count(a.karyawanid) as jmlhrow from ".$dbname.".sdm_catu a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid  where b.namakaryawan like '%".$txtsearch."%' " .$whereSrc."  order by a.updateTime desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
	
		$str="select a.* from ".$dbname.".sdm_catu a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$txtsearch."%'  ".$whereSrc." order by a.updateTime desc";
		//echo"warning".$str;exit();
		
		$res=mysql_query($str);
		
		while($bar=mysql_fetch_assoc($res))
		{
			$sKry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar['karyawanid']."'";
			$qKry=mysql_query($sKry) or die(mysql_error());
			$rKry=mysql_fetch_assoc($qKry);
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$bar['periodegaji']."</td>
		<td>".$bar['kodeorg']."</td>
		<td>".$rKry['namakaryawan']."</td>
		<td>".$rBrg['namabarang']."</td>
		<td>".number_format($bar['hargasatuan'],2)."</td>
		<td>".number_format($bar['jumlah'],2)."</td>
		<td>
			  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['karyawanid']."','".$bar['periodegaji']."','".$bar['kodebarang']."','".$bar['kodeorg']."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['karyawanid']."','".$bar['periodegaji']."','".$bar['kodebarang']."','".$bar['kodeorg']."');\">
		  </td>
		
		</tr>";	
		}     
		echo"
				 <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariHal(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariHal(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>"; 
				echo"</tbody> </table>";
		break;
		case'update':
		$sUpd="update ".$dbname.".sdm_catu set `jumlah`='".$jmlh."',`hargasatuan`='".$hrgSatuan."' where ".$where."";
		//echo"warning".$sUpd;exit();
		if(!mysql_query($sUpd))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'delData':
		$sDel="delete from ".$dbname.".sdm_catu where ".$where."";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		//`karyawanid`,`periodegaji`,`jumlahhk`,`kodebarang`,`jumlah`,`hargasatuan`,`satuan`,	`kodeorg`,`updateby`
		$sDt="select * from ".$dbname.".sdm_catu where ".$where." ";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
		
		$sNm="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rDet['kodebarang']."'";
		$qNm=mysql_query($sNm) or die(mysql_error());
		$rNm=mysql_fetch_assoc($qNm);
		$sKry="select tipekaryawan,statuspajak from ".$dbname.".datakaryawan where karyawanid='".$rDet['karyawanid']."'";
		$qKry=mysql_query($sKry) or die(mysql_error());
		$rKry=mysql_fetch_assoc($qKry);
		
		$sJmlhSatuan="select jumlah from ".$dbname.".sdm_5natura where kodeorg='".substr($rDet['kodeorg'],0,4)."' and kodebarang='".$rDet['kodebarang']."' and tipekaryawan='".$rKry['tipekaryawan']."' and statuspajak='".$rKry['statuspajak']."'";
		$qJmlhSatuan=mysql_query($sJmlhSatuan) or die(mysql_error());
		$rJmhsatuan=mysql_fetch_assoc($qJmlhSatuan);
		echo $rDet['jumlahhk']."###".$rDet['jumlah']."###".$rDet['satuan']."###".$rNm['namabarang']."###".$rDet['hargasatuan']."###".$rJmhsatuan['jumlah'];
		break;
		case'getKry':
		$optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		if(strlen($kodeOrg)>4)
		{
			$where=" subbagian='".$kodeOrg."'";
		}
		else
		{
			$where=" lokasitugas='".$kodeOrg."' and (subbagian='0' or subbagian is null)";	
		}
		$sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
		//echo "warning".$sKry;
		$qKry=mysql_query($sKry) or die(mysql_error());
		while($rKry=mysql_fetch_assoc($qKry))
		{
			$optKry.="<option value=".$rKry['karyawanid']." ".($rKry['karyawanid']==$kryId?"selected":"").">".$rKry['namakaryawan']."</option>";
		}
		echo $optKry;
		break;
		case'getHK':
		$a=0;
		$sAbsni="select count(a.absensi) as jmhAbsensi from ".$dbname.".sdm_absensidt a left join ".$dbname.".sdm_absensiht b on a.tanggal=b.tanggal where a.karyawanid='".$kryId."' and b.kodeorg='".$kodeOrg."' and b.periode='".$periodeGaji."' and absensi in (select kodeabsen from ".$dbname.".sdm_5absensi where kelompok='1') ";
		$qAbsni=mysql_query($sAbsni) or die(mysql_error());
		$rAbsni=mysql_fetch_assoc($qAbsni);
		$a=$rAbsni['jmhAbsensi'];
		
		$b=0;
		$sOrg="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$kodeOrg."'";
		$qOrg=mysql_query($sOrg) or die(mysql_error());
		$rOrg=mysql_fetch_assoc($qOrg);
	
		$stglPrd="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where periode='".$periodeGaji."'";
		$qTglPrd=mysql_query($stglPrd) or die(mysql_error());
		$rTglPrd=mysql_fetch_assoc($qTglPrd);
	
		if($rOrg['tipe']=='KEBUN')
		{		
			$sHdr="select sum(a.jhk) as jmhHk from ".$dbname.".kebun_kehadiran a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi  where a.nik='".$kryId."' and a.absensi in (select kodeabsen from ".$dbname.".sdm_5absensi where kelompok='1') and b.tanggal between '".$rTglPrd['tanggalmulai']."' and '".$rTglPrd['tanggalsampai']."'";		
			$qHdr=mysql_query($sHdr) or die(mysql_error());
			$rHdr=mysql_fetch_assoc($qHdr);
			$b=$rHdr['jmhHk'];
		}
		//else
//		{
//			$sHdr="select sum";
//		}
//		
		//echo"warning".$sHdr."__".$b;exit();
		$jmlhHktot=$a+$b;
		//echo "warning".$jmlhHktot."__".$b,"___".$sAbsni;exit();
		echo $jmlhHktot;
		break;
		
		case'getNmbarang':
		$sKry="select tipekaryawan,statuspajak from ".$dbname.".datakaryawan where karyawanid='".$kryId."'";
		$qKry=mysql_query($sKry) or die(mysql_error());
		$rKry=mysql_fetch_assoc($qKry);
		echo"<div style=\"overflow:auto;height:450px;width:350px;margin-left:10px;\">
		<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=rowheader><td>No</td><td>".$_SESSION['lang']['kodebarang']."</td><td>".$_SESSION['lang']['namabarang']."</td><td>".$_SESSION['lang']['satuan']."</td></tr>
		</thead><tbody>";
		//$sBrg="select a.kodebarang,a.namabarang,b.satuan,b.jumlah,b.rupiahkonversi from ".$dbname.".log_5masterbarang a left join ".$dbname.".sdm_5natura b  on a.kodebarang=b.kodebarang where a.namabarang like '%".$txtfind."%' and b.tipekaryawan='".$rKry['tipekaryawan']."' and b.statuspajak='".$rKry['statuspajak']."'";
		$sBrg="select b.kodebarang,a.namabarang,b.satuan,b.jumlah,b.rupiahkonversi from ".$dbname.".log_5masterbarang a inner join ".$dbname.".sdm_5natura b  on a.kodebarang=b.kodebarang where  a.namabarang like '%".$txtfind."%'  and (b.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and b.tipekaryawan='".$rKry['tipekaryawan']."' and b.statuspajak='".$rKry['statuspajak']."') ";
//	
//	echo $sBrg;
		$qBrg=mysql_query($sBrg) or die(mysql_error());
		$rCek=mysql_num_rows($qBrg);
		if($rCek>0)
		{
			while($rBrg=mysql_fetch_assoc($qBrg))
			{
			$no+=1;
			echo"<tr class=rowcontent onclick=\"setBrg('".$rBrg['kodebarang']."','".$rBrg['namabarang']."','".$rBrg['satuan']."','".$rBrg['jumlah']."','".$rBrg['rupiahkonversi']."')\"><td>".$no."</td><td>".$rBrg['kodebarang']."</td><td>".$rBrg['namabarang']."</td><td>".$rBrg['satuan']."</td></tr>";
	
			}
		}
		else
		{
			echo"<tr class=rowcontent><td colspan='4'>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
		}
		echo"</tbody></table></div>";
		break;
		default:
		break;
	}
?>