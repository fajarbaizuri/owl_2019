<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
//hilangkan koma
$param['hm']=str_replace(",","",$param['hm']);
$param['insentiv']=str_replace(",","",$param['insentiv']);

if($_POST['insentiv']==0 || $_POST['insentiv']==0.00)
{
    
}else{
	$str="select 
posting,
notransaksi,
pemilikalat,
idkaryawan,namakaryawan,`tanggal`,
kodevhc,sum(DISTINCT ifnull(`convwaktu`,0)) AS `convwaktu`
 FROM ".$dbname.".`vhc_kendaraan_detail_vw` 
where date_format(tanggal,'%Y-%m')='".$param['periode']."' and pemilikalat like '".$param['kodeorg']."' AND idkaryawan='".$param['ids']."' AND kodevhc='".$param['plat']."' and posting=1 group BY pemilikalat,tanggal,idkaryawan order by tanggal,idkaryawan asc";

	$res=mysql_query($str);
	$namabarang='';
	while($bar=mysql_fetch_object($res))
	{
		
						if ($bar->convwaktu > 0){
							$total=($bar->convwaktu / $param['hm']) * $param['insentiv']; 
						}else{
							$total=0;
						}
						
						$updTrans = updateQuery($dbname,'vhc_kendaraan_tenaga',array('insentiv'=>$total),"notransaksi='".$bar->notransaksi."' and idkaryawan='".$bar->idkaryawan."'");
                        if(!mysql_query($updTrans)) {
                            echo "Update Status Jurnal Error : ".mysql_error()."\n";
                        }
	}
						
}

                   
?>