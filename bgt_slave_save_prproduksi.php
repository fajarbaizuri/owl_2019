<?
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg =$_POST['kodeorg'];	
$tahun   =$_POST['tahun'];	
$stokcpo=$_POST['stokcpo'];	
$stokpk=$_POST['stokpk'];	
$hargacpo=$_POST['hargacpo'];	
$hargapk=$_POST['hargapk'];	
$porsicpo=$_POST['porsicpo'];	
$porsipk=$_POST['porsipk'];	
$method=$_POST['method'];	

switch($method)
{
case 'update':	
    $str="update ".$dbname.".bgt_prproduk set 
                    `rupiahstokawalcpo`=".$stokcpo.",
                    `rupiahstokawalpk`=".$stokpk.",
                    `hargasatuancpo`=".$hargacpo.",
                    `hargasatuanpk`=".$hargapk.",
                    `peroporsibiayacpo`=".$porsicpo.",
                    `proporsibiayapk`=".$porsipk.",
                    `updateby`=".$_SESSION['standard']['userid']."
          where tahunbudget=".$tahun." and kodeorg='".$kodeorg."'";
    if(mysql_query($str))
    {}
    else
        {echo " Gagal,".addslashes(mysql_error($conn));}
    break;
    
case 'insert':
      $str="insert into ".$dbname.".bgt_prproduk
       (`tahunbudget`,`kodeorg`,`rupiahstokawalcpo`,
        `rupiahstokawalpk`,`hargasatuancpo`,
        `hargasatuanpk`,`peroporsibiayacpo`,
        `proporsibiayapk`,`updateby`)
        values(".$tahun.",'".$kodeorg."',".$stokcpo.",".$stokpk.",".$hargacpo.",
            ".$hargapk.",".$porsicpo.",".$porsipk.",".$_SESSION['standard']['userid'].")";
    if(mysql_query($str))
    {}
    else
    {echo " Gagal,".addslashes(mysql_error($conn));}	
    break;
    
case 'delete':
    $str="delete from ".$dbname.".bgt_prproduk
    where kodeorg='".$kodeorg."' and tahunbudget=".$tahun;
    if(mysql_query($str))
    {}
    else
    {echo " Gagal,".addslashes(mysql_error($conn));}
    break;
default:
break;					
}

$str1="select a.*,b.namakaryawan from ".$dbname.".bgt_prproduk a left join ".$dbname.".datakaryawan b
     on a.updateby=b.karyawanid where a.kodeorg='".$_SESSION['empl']['lokasitugas']."' order by a.tahunbudget desc";
if($res1=mysql_query($str1))
{
        $no=0;
      while($bar1=mysql_fetch_object($res1))
      {
          $no+=1;
              echo"<tr class=rowcontent>
                         <td >".$no."</td>
                                 <td>".$bar1->tahunbudget."</td>
                                 <td>".$bar1->kodeorg."</td>
                                 <td>".$bar1->rupiahstokawalcpo."</td>
                                 <td>".$bar1->rupiahstokawalpk."</td>
                                 <td>".$bar1->hargasatuancpo."</td>
                                  <td>".$bar1->hargasatuanpk."</td>
                                   <td>".$bar1->peroporsibiayacpo."</td>
                                   <td>".$bar1->proporsibiayapk."</td>
                                   <td>".$bar1->namakaryawan."</td>    
                                 <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->kodeorg."','".$bar1->rupiahstokawalcpo."','".$bar1->rupiahstokawalpk."','".$bar1->hargasatuancpo."','".$bar1->hargasatuanpk."','".$bar1->peroporsibiayacpo."','".$bar1->proporsibiayapk."');\"></td></tr>";
      }
}      
?>
