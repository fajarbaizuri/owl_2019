<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$pt=$_POST['pt'];
	$tipe=$_POST['tipe'];


$hasil="<option value=''>".$_SESSION['lang']['all']."</option>";
//cari Anggota dari PT
	$strA1="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' and tipe in ('KEBUN','PABRIK','HOLDING','TRAKSI','LINE') order by kodeorganisasi asc";
	$resA1=mysql_query($strA1);
	$hasilA1=mysql_num_rows($resA1);

	if ($hasilA1==0){
		
		exit;
	}else{
		$ArrAnkOrg=array();
		while($barA1=mysql_fetch_object($resA1))
		{
			$ArrAnkOrg[]=$barA1->kodeorganisasi;	
		}
		$AnakOrg=implode("','",$ArrAnkOrg);
	}
	
	//cari Akun dari Organisasi
	if ($tipe!=""){
		$str="select noakun,namaakun from ".$dbname.".keu_5akun where pemilik ='".$tipe."' and level=5 and (noakun like '11101%' or noakun like '11102%') order by namaakun desc";
		
	}else{
		$str="select noakun,namaakun from ".$dbname.".keu_5akun where pemilik in ('".$AnakOrg."') and level=5 and (noakun like '11101%' or noakun like '11102%') order by namaakun desc";
		
	}
	 
	
 
        $res=mysql_query($str);
      
        while($bar=mysql_fetch_object($res))
        {
                $hasil.="<option value='".$bar->noakun."'>".$bar->namaakun."</option>";

        }    


echo $hasil;
?>