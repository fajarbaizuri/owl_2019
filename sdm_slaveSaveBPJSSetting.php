<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

  $perusahaan=$_POST['perusahaan'];
  $karyawan =$_POST['karyawan'];

   $stra="update ".$dbname.".sdm_ho_hr_jms_porsi
                        set `value`=".$perusahaan." where `id`='perusahaanbpjs'";
                if(mysql_query($stra,$conn))
                {		
                }
                else
                {
                        echo " Error: ".addslashes(mysql_error($conn));
                } 
     //  echo $stra;
      // exit("Error");
        $stra="update ".$dbname.".sdm_ho_hr_jms_porsi
                        set `value`=".$karyawan." where `id`='karyawanbpjs'";
                if(mysql_query($stra,$conn))
                {		
                }
                else
                {
                        echo " Error: ".addslashes(mysql_error($conn));
                } 			
?>
