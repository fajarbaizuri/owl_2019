<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$nik			=$_POST['nik'];
$namakaryawan	=$_POST['namakaryawan'];
$tempatlahir	=$_POST['tempatlahir'];
$tanggallahir	=tanggalsystem($_POST['tanggallahir']);
$noktp		=$_POST['noktp'];	
$nopassport	=$_POST['nopassport'];
$npwp		=$_POST['npwp'];
$kodepos	=$_POST['kodepos'];
$alamataktif	=$_POST['alamataktif'];
$kota		=$_POST['kota'];
$noteleponrumah	=$_POST['noteleponrumah'];
$nohp		=$_POST['nohp'];
$norekeningbank	=$_POST['norekeningbank'];
$namabank	=$_POST['namabank'];
$alokasi	=$_POST['alokasi'];
$jms            =$_POST['jms'];

$tanggalmasuk	=tanggalsystem($_POST['tanggalmasuk']);
if($_POST['tanggalkeluar']=='')
    $_POST['tanggalkeluar']='00-00-0000';
$tanggalkeluar	=tanggalsystem($_POST['tanggalkeluar']);
$jumlahanak		=$_POST['jumlahanak'];
if($jumlahanak=='')
  $jumlahanak=0;
$jumlahtanggungan=$_POST['jumlahtanggungan'];
if($jumlahtanggungan=='')
   $jumlahtanggungan=0;
if($_POST['tanggalmenikah']=='')
    $_POST['tanggalmenikah']='00-00-0000';
$tanggalmenikah	=tanggalsystem($_POST['tanggalmenikah']);
$notelepondarurat=$_POST['notelepondarurat'];
$email			=$_POST['email'];
$jeniskelamin	=$_POST['jeniskelamin'];
$agama			=$_POST['agama'];
$bagian			=$_POST['bagian'];
$kodejabatan	=$_POST['kodejabatan'];
$kodegolongan	=$_POST['kodegolongan'];
$lokasitugas	=$_POST['lokasitugas'];
$kodeorganisasi	=$_POST['kodeorganisasi'];
$tipekaryawan	=$_POST['tipekaryawan'];
$warganegara	=$_POST['warganegara'];
$lokasipenerimaan=$_POST['lokasipenerimaan'];
$statuspajak	=$_POST['statuspajak'];
$provinsi		=$_POST['provinsi'];
$sistemgaji		=$_POST['sistemgaji'];
$golongandarah	=$_POST['golongandarah'];
$statusperkawinan=$_POST['statusperkawinan'];
$levelpendidikan=$_POST['levelpendidikan'];	
$method			=$_POST['method'];
$karyawanid		=$_POST['karyawanid'];
$subbagian		=$_POST['subbagian'];
if($subbagian=='0')
{
    $subbagian='';
}

switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".datakaryawan where karyawanid=".$karyawanid;
		break;
		case 'update':
                  
			$strx="update ".$dbname.".datakaryawan set 
			       `nik`			='".$nik."',
				   `namakaryawan`	='".$namakaryawan."',
			  	   `tempatlahir`	='".$tempatlahir."',
				   `tanggallahir`	=".$tanggallahir.",
			       `warganegara`            ='".$warganegara."',
				   `jeniskelamin`	='".$jeniskelamin."',
			       `statusperkawinan`       ='".$statusperkawinan."',
				   `tanggalmenikah`	=".$tanggalmenikah.",
			       `agama`			='".$agama."',
				   `golongandarah`	='".$golongandarah."',
			       `levelpendidikan`        =".$levelpendidikan.",
				   `alamataktif`	='".$alamataktif."',
			       `provinsi`		='".$provinsi."',
				   `kota`		='".$kota."',
				   `kodepos`		='".$kodepos."',
			       `noteleponrumah`         ='".$noteleponrumah."',
				   `nohp`		='".$nohp."',
			       `norekeningbank`         ='".$norekeningbank."',
				   `namabank`		='".$namabank."',
			       `sistemgaji`		='".$sistemgaji."',
				   `nopaspor`		='".$nopaspor."',
			       `noktp`			='".$noktp."',
				   `notelepondarurat`   ='".$notelepondarurat."',
			       `tanggalmasuk`           =".$tanggalmasuk.",
				   `tanggalkeluar`	=".$tanggalkeluar.",
			       `tipekaryawan`           =".$tipekaryawan.",
				   `jumlahanak`		=".$jumlahanak.",
			       `jumlahtanggungan`       =".$jumlahtanggungan.",
				   `statuspajak`	='".$statuspajak."',
			       `npwp`			='".$npwp."',
				   `lokasipenerimaan`   ='".$lokasipenerimaan."',
				   `kodeorganisasi`	='".$kodeorganisasi."',
			       `bagian`			='".$bagian."',
				   `kodejabatan`	=".$kodejabatan.",
				   `kodegolongan`	='".$kodegolongan."',
			       `lokasitugas`            ='".$lokasitugas."',
				   `email`		='".$email."',
				   `alokasi`		=".$alokasi.",
				   `subbagian`		='".$subbagian."',
                                   `jms`                ='".$jms."'    
				   where karyawanid=".$karyawanid;
		break;	
		case 'insert':
		
			$iCek="select distinct nik from ".$dbname.".datakaryawan where nik='".$nik."'";
			$ada=true;
			$nCek=mysql_query($iCek)or die(mysql_error());
			while($dCek=mysql_fetch_assoc($nCek))
			{
				if ($ada==true)
				{
					echo "warning : Nik karyawan dengan ".$nik." sudah dipakai";
					exit();	
				}
				else
				{
				}	
			}
                  
			$strx="insert into ".$dbname.".datakaryawan(
			  `nik`,`namakaryawan`,
			  `tempatlahir`,`tanggallahir`,
			  `warganegara`,`jeniskelamin`,
			  `statusperkawinan`,`tanggalmenikah`,
			  `agama`,`golongandarah`,
			  `levelpendidikan`,`alamataktif`,
			  `provinsi`,`kota`,`kodepos`,
			  `noteleponrumah`,`nohp`,
			  `norekeningbank`,`namabank`,
			  `sistemgaji`,`nopaspor`,
			  `noktp`,`notelepondarurat`,
			  `tanggalmasuk`,`tanggalkeluar`,
			  `tipekaryawan`,`jumlahanak`,
			  `jumlahtanggungan`,`statuspajak`,
			  `npwp`,`lokasipenerimaan`,`kodeorganisasi`,
			  `bagian`,`kodejabatan`,`kodegolongan`,
			  `lokasitugas`,`email`,`alokasi`,`subbagian`,`jms`)
			values('".$nik."','".$namakaryawan."',
			  '".$tempatlahir."',".$tanggallahir.",
			  '".$warganegara."','".$jeniskelamin."',
			  '".$statusperkawinan."',".$tanggalmenikah.",
			  '".$agama."','".$golongandarah."',
			  ".$levelpendidikan.",'".$alamataktif."',
			  '".$provinsi."','".$kota."','".$kodepos."',
			  '".$noteleponrumah."','".$nohp."',
			  '".$norekeningbank."','".$namabank."',
			  '".$sistemgaji."','".$nopaspor."',
			  '".$noktp."','".$notelepondarurat."',
			  ".$tanggalmasuk.",".$tanggalkeluar.",
			  ".$tipekaryawan.",".$jumlahanak.",
			  ".$jumlahtanggungan.",'".$statuspajak."',
			  '".$npwp."','".$lokasipenerimaan."','".$kodeorganisasi."',
			  '".$bagian."',".$kodejabatan.",'".$kodegolongan."',
			  '".$lokasitugas."','".$email."',".$alokasi.",
			  '".$subbagian."','".$jms."')";	   
		break;
		
		
		
		case'cek'://untuk cek 
		
		//echo "aaaaa";
		//if($totalSum>$total)
		// sdm_karyawanpendidikan
			
	
		
		//$d="";
		//$levelpendidikan2="select levelpendidikan from ".$dbname.".sdm_karyawanpendidikan ";
		
		//$levelpendidikan=$_POST['levelpendidikan'];
		$aCek="select levelpendidikan from ".$dbname.".sdm_karyawanpendidikan where levelpendidikan ='".$levelpendidikan."' ";
		$bCek=mysql_query($aCek) or die(mysql_error());
		while ($cCek=mysql_fetch_assoc($bCek))
		{
			exit("error:$aCek");
		/*	if ($levelpendidikan<$cCek['levelpendidikan'])
			{
				echo "warning : qweqwe";
				exit();	
			}*/
		}
		
		
	
		/*##UNTUK VALIDASI DATA YANG UDAH DI TUTUP GK BISA INSERT LAGI
		$aCek="select distinct tutup from ".$dbname.".bgt_produksi_kebun where tahunbudget='".$thnbudget."' ";
		$bCek=mysql_query($aCek) or die(mysql_error());
		while ($cCek=mysql_fetch_assoc($bCek))
		{
			//exit("error:$aCek");
			if($cCek['tutup']==1)
			{
				echo "warning : Input untuk tahun ".$thnbudget." tidak bisa dilakukan karena telah di tutup";
				exit();	
			}
		}
		
		##UNTUK VALIDASI DATA DI BGT BLOK ADA APA TIDAK UNTUK THN TANAM DAN KODE BLOKNY
		$xCek="select tahunbudget,kodeblok from ".$dbname.".bgt_blok where tahunbudget='".$thnbudget."' and kodeblok='".$kdblok."' ";
		//exit("Error:$xCek");
		$ada=false;
		
		$yCek=mysql_query($xCek)or die(mysql_error());
		while($zCek=mysql_fetch_assoc($yCek))
		{
			$ada=true;
		}
		if ($ada==false)
		{
			echo "warning : Tahun Budget ".$thnbudget." atau Blok ".$kdblok." belum terdapat di Blok Anggaran (Anggaran->Transaksi->Kebun->Blok Anggaran) ";
			exit();	
		}
		*/
		
		
		
	break;
		
		
		
		
		
		default:
          $strx="select 1=1";
        break;	
		
		
		
		
		
		
	}
    if(mysql_query($strx))
	{
	   //whenever not deleting, return value as below to javascript
		if($method!='delete')
		{
			$karid='';
			$nama='';
			$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where
			      namakaryawan='".$namakaryawan."' and tanggallahir=".$tanggallahir;
			$res=mysql_query($str);
			//echo $str;
			while($bar=mysql_fetch_object($res))
			{
				$karid=$bar->karyawanid;
				$nama=$bar->namakaryawan;
			}
			//return XML format
			echo"<?xml version='1.0' ?>
			     <karyawan>
				 <karyawanid>".$karid."</karyawanid>
				 <namakaryawan>".$nama."</namakaryawan>
				 </karyawan>";
		}
	}
	else
	{
		echo " Gagal:".addslashes(mysql_error($conn)).$strx;
	}
	
	

?>
