<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
      //  echo " Error:".$_POST['induk'];
		$blok=$_POST['blok'];
	if($blok!='')
	{ 	
		$str="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$blok."'";
		
		$res=mysql_query($str);
		$tipe='';
		while($bar=mysql_fetch_object($res))
		{
		 	$tipe=$bar->tipe;
		}
		A:
		if($tipe=='STENGINE' or $tipe=='STATION')
		{
			
			$optKegiatan="<option value=''></option>";
			$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='MIL' and pilih in ('1','5','6','7','11','12','13','15')  order by kelompok,namakegiatan";
			$resf=mysql_query($strf);
			while($barf=mysql_fetch_object($resf))
			{
				 $optKegiatan.="<option value='".$barf->kodekegiatan."'>".$barf->kodekegiatan." - ".$barf->namakegiatan."</option>";
			} 
			echo $optKegiatan;
			if($blok=='CBGM00' || $blok=='CBGM70'){
				goto C;
			}
		}
		else if($tipe==''){
			switch ($_SESSION['empl']['lokasitugas']){
				case "CBGM":
				$tipe='STENGINE';
				break;
				case "TDAE":
				case "TDBE":
				case "USJE":
				$tipe='BLOK';
				break;
				case "FBHO":
				$tipe='BLOK';
				break;
				case "TKFB":
				 $tipe='TRAKSI';
				break;
			}
			goto A;
			
			
		}
		else if($tipe=='BLOK')
		{	
			
	        $blehh="<option value=''></option>";
			$blehh.=getKegiatanBlok('option',$blok);
			echo $blehh;
		}
		else if( $tipe=='TRAKSI'){
			
			$optKegiatan="<option value=''></option>";
			$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='TRK' and pilih in ('1','5','6','7','11','12','13','15') order by kelompok,namakegiatan";	   
			$resf=mysql_query($strf);
			while($barf=mysql_fetch_object($resf))
			{
				 $optKegiatan.="<option value='".$barf->kodekegiatan."'>".$barf->kodekegiatan." - ".$barf->namakegiatan."</option>";
			} 
			//echo "<option value=''>".$tipe."</option>";
			echo $optKegiatan;			
			
		}
		else if($tipe=='BIBITAN'){
			$optKegiatan="<option value=''></option>";
			$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where  kelompok in ('BBT','MN','PN') and pilih in ('1','5','6','7','11','12','13','15') order by kelompok,namakegiatan";	   
			$resf=mysql_query($strf);
			while($barf=mysql_fetch_object($resf))
			{
				 $optKegiatan.="<option value='".$barf->kodekegiatan."'>".$barf->kodekegiatan." - ".$barf->namakegiatan."</option>";
			} 
			echo $optKegiatan;			
		}
                else
                {
					
			$optKegiatan="<option value=''></option>";
			$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='KNT' and pilih in ('1','5','6','7','11','12','13','15') order by kelompok,namakegiatan";
			$resf=mysql_query($strf);
			while($barf=mysql_fetch_object($resf))
			{
				 $optKegiatan.="<option value='".$barf->kodekegiatan."'>".$barf->kodekegiatan." - ".$barf->namakegiatan."</option>";
			} 
			echo $optKegiatan;                    
                }    
                    
	}
	else
	{
			$optKegiatan="<option value=''></option>";
			C:
			$strf="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan 
			       where kelompok='KNT' and pilih in ('1','5','6','7','11','12','13','15') order by kelompok,namakegiatan";
			$resf=mysql_query($strf);
			while($barf=mysql_fetch_object($resf))
			{
				 $optKegiatan.="<option value='".$barf->kodekegiatan."'>".$barf->kodekegiatan." - ".$barf->namakegiatan."</option>";
			} 
			echo $optKegiatan;		
	}
}
else
{
	echo " Error: Transaction Period missing";
}
?>