<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php'); 
require_once('lib/zLib.php');
$method=$_POST['method'];

$kd_karyawan=isset($_POST['kd_karyawan'])?$_POST['kd_karyawan']:'';
$kdVhc=isset($_POST['kdVhc'])?$_POST['kdVhc']:'';
$sts=isset($_POST['sts'])?$_POST['sts']:'';
$uamk=isset($_POST['uamk'])?$_POST['uamk']:'';
$karyawanluar=isset($_POST['karyawanluar'])?$_POST['karyawanluar']:'';

$nama=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

switch($method) {
	case 'CarPosisi':

		$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		
    if (substr($_POST['kendaraan'],0,2)=='AB'){
		$optKendaran.="<option value='1'>OPERATOR</option>";
		$optKendaran.="<option value='2'>HELPER</option>";
	}else{
		$optKendaran.="<option value='1'>SOPIR</option>";
		$optKendaran.="<option value='2'>KERNET</option>";
	}
	
		
	
		echo $optKendaran;
	break;
	case 'insert':
		//LTDAE00001
		if ($kd_karyawan=="L"){
			$kd_karyawan=kdauto($dbname, "L".$_SESSION['empl']['lokasitugas']);
			$karyawan=$karyawanluar;
		}else{
			$kd_karyawan=$kd_karyawan;
			$karyawan=$nama[$kd_karyawan];
		}
		$sRicek="select * from ".$dbname.".vhc_5operator where karyawanid='".$kd_karyawan."' and vhc='".$kdVhc."'  ";
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		if($rRicek>0) {
			$sDel="delete from ".$dbname.".vhc_5operator
					where karyawanid='".$kd_karyawan."' and vhc='".$kdVhc."'";	    
			if(mysql_query($sDel)) {
				$sDel2="insert into ".$dbname.".vhc_5operator (`karyawanid`,`nama`,`aktif`,`vhc`,`um`,`status`)
					values ('".$kd_karyawan."','".$karyawan."','".$sts."','".$kdVhc."','".$uamk."','".$_POST['posi']."')";
				if(mysql_query($sDel2))
					echo"";
				else
					echo " Gagal,".addslashes(mysql_error($conn));
			} else {
				echo "Gagal,".addslashes(mysql_error($conn));
			}
		} else {
			$sDel2="insert into ".$dbname.".vhc_5operator (`karyawanid`,`nama`,`aktif`,`vhc`,`um`,`status`)
					values ('".$kd_karyawan."','".$karyawan."','".$sts."','".$kdVhc."','".$uamk."','".$_POST['posi']."')";
			if(mysql_query($sDel2))
			echo"";
			else
			echo "Gagal,".addslashes(mysql_error($conn));
		}
	break;
case'loadData':
		$no=0;
		$str="select a.*,b.nopol,if(left(vhc,2)='AB',if(`status`='1','OPERATOR','HELPER'),if(`status`='1','SOPIR','KERNET')) AS posi from ".$dbname.".vhc_5operator a
	left join ".$dbname.".vhc_5master b on a.vhc=b.kodevhc where a.karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') or a.karyawanid like 'L".$_SESSION['empl']['lokasitugas']."%' order by karyawanid asc ";
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{

				
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td>".$bar1['karyawanid']."</td>";
			$tab.="<td>".$bar1['nama']."</td>";
			$tab.="<td align=center>".$bar1['posi']."</td>";
			$tab.="<td align=center>".$bar1['aktif']."</td>";
			$tab.="<td>".$bar1['vhc']."</td>";
			$tab.="<td>".$bar1['nopol']."</td>";
			$tab.="<td align=right>".$bar1['um']."</td>";
			/*
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1['kodeorg']."','".$bar1['kodekegiatan']."','".$bar1['kelompok']."','".$bar1['basis']."','".$bar1['siapborong']."','".$bar1['lebihborong']."','".$bar1['tipe']."');\">*/
				$tab.="<td align=center>

				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['karyawanid']."','".$bar1['vhc']."');\"></td>";
		echo $tab;
		}
	break;
	case 'delete':
		$tab="delete from ".$dbname.".vhc_5operator where karyawanid='".$kd_karyawan."' and vhc='".$kdVhc."'";
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
function kdauto($dbname, $inisial){
    $struktur   = mysql_query("SELECT * FROM ".$dbname.".vhc_5operator");
    $field      = mysql_field_name($struktur,0);
    $panjang    = mysql_field_len($struktur,0);
    $qry  = mysql_query("SELECT max(".$field.") FROM ".$dbname.".vhc_5operator");
    $row  = mysql_fetch_array($qry);
    if ($row[0]=="") {
    $angka=10000;
    }
    else {
    $angka= substr($row[0], strlen($inisial));
    }
    $angka++;
    $angka      =strval($angka);
    $tmp  ="";
    for($i=1; $i<=($panjang-strlen($inisial)-strlen($angka)); $i++) {
    $tmp=$tmp."0";
    }
    return $inisial.$tmp.$angka;
    }
?>