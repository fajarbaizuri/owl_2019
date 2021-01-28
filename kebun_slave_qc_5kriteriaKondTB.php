<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$kode=$_POST['kode'];
$nama=$_POST['nama'];
$pekerjaan=$_POST['pekerjaan'];
$kelas=$_POST['kelas'];
$bobot=$_POST['bobot'];
//$dari=$_POST['dari'];
//$sampai=$_POST['sampai'];
//$keterangan=$_POST['keterangan'];
//$dariold=$_POST['dariold'];
//$sampaiold=$_POST['sampaiold'];

$proses=$_POST['proses'];

//$x=readCountry('config/topografi.lst');
//foreach($x as $bar=>$val)
//{                    
//    $kamusTanah[$val[0]]=$val[0];
//}
$str="select * from ".$dbname.".setup_topografi order by topografi"; 
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kamusTanah[$bar->topografi].=$bar->keterangan;
}

switch($proses)
{
    case'insert1': //insert tab1
        if($kode==''||$nama=='')
        {
            exit("Error: Field Tidak Boleh Kosong");
        }
        $str="select * from ".$dbname.".kebun_qc_5stlapangantb where kode='".$kode."'";
        $res=mysql_query($str);
        if(mysql_num_rows($res)>0)exit("Error: Data Sudah Ada");
        $sql="insert into ".$dbname.".kebun_qc_5stlapangantb 
        (`kode`,`nama`) 
        values 
        ('".$kode."','".$nama."')";
        if(mysql_query($sql)){
        }
        else
        echo "DB Error : ".mysql_error($conn);
    break;    
    case 'update1': //update tab1
        if($kode==''||$nama=='')
        {
            exit("Error: Field Tidak Boleh Kosong");
        }
        $sql="update ".$dbname.".kebun_qc_5stlapangantb set nama='".$nama."' where kode='".$kode."'";
        if(mysql_query($sql)){
        }
        else
        echo "DB Error : ".mysql_error($conn);
    break;    
    case'delete1': //delete tab1
        $sql="delete from ".$dbname.".kebun_qc_5stlapangantb where `kode`='".$kode."'";
        if(mysql_query($sql)){
        }
        else
        echo "DB Error : ".mysql_error($conn);
    break;
    case'loadtab1': //load tab1
        $sql="select * from ".$dbname.".kebun_qc_5stlapangantb order by kode asc";
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
            $no+=1;
            echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td align=left>".$res['kode']."</td>
            <td align=left>".$res['nama']."</td>
            <td>
                <img src=images/application/application_edit.png class=resicon title='Edit' onclick=\"filltab1('". $res['kode']."','".$res['nama']."');\">
                <img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"deltab1('". $res['kode']."');\" >
            </td></tr>";
        }
    break;    
    case'getPekerjaan':
        $str="select * from ".$dbname.".kebun_qc_5stlapangantb order by kode"; 
        $query=mysql_query($str) or die(mysql_error());
        $optPekerjaan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        while($res=mysql_fetch_assoc($query))
        {  
            if($pekerjaan!='')
            {
                $optPekerjaan.="<option value='".$res['kode']."'>".$res['nama']." - ".$res['nama']."</option>";
            }
            else
            {
                $optPekerjaan.="<option value='".$res['kode']."' ".($res['kode']==$pekerjaan?'selected=selected':'').">".$res['kode']." - ".$res['nama']."</option>";
            }
        }
        echo $optPekerjaan;
    break;    
    case'insert2'://save tab2
        if($pekerjaan==''||$kelas==''||$bobot=='')
        {
            exit("Error:Field Tidak Boleh Kosong");
        }
        $str="select * from ".$dbname.".kebun_qc_5bobottb where pekerjaan='".$pekerjaan."' and topografi = '".$kelas."'";
        $res=mysql_query($str);
        if(mysql_num_rows($res)>0)exit("Error: Data Sudah Ada");
        $sql="insert into ".$dbname.".kebun_qc_5bobottb 
        (`pekerjaan`,`topografi`,`bobot`) 
        values 
        ('".$pekerjaan."','".$kelas."','".$bobot."')";
        if(mysql_query($sql)){
        }
        else
        echo "DB Error : ".mysql_error($conn);
    break;    
    case'loadtab2': //load tab2
        $sql="select * from ".$dbname.".kebun_qc_5bobottb order by pekerjaan asc";
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
            $no+=1;
            echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td align=left>".$res['pekerjaan']."</td>
            <td align=left>".$kamusTanah[$res['topografi']]."</td>
            <td align=right>".number_format($res['bobot'])."</td>
            <td>
                <img src=images/application/application_edit.png class=resicon title='Edit' onclick=\"filltab2('". $res['pekerjaan']."','".$res['topografi']."','".$res['bobot']."');\">
                <img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"deltab2('". $res['pekerjaan']."','". $res['topografi']."');\" >
            </td></tr>";
        }
    break;    
    case'delete2': //delete tab2
        $sql="delete from ".$dbname.".kebun_qc_5bobottb where `pekerjaan`='".$pekerjaan."' and `topografi`='".$kelas."'";
        if(mysql_query($sql)){
        }
        else
        echo "DB Error : ".mysql_error($conn);
    break;
    case 'update2': //update tab2
        if($bobot==''||$bobot=='')
        {
            exit("Error: Field Tidak Boleh Kosong");
        }
        $sql="update ".$dbname.".kebun_qc_5bobottb set bobot='".$bobot."' where pekerjaan='".$pekerjaan."' and topografi='".$kelas."'";
        if(mysql_query($sql)){
        }
        else
        echo "DB Error : ".mysql_error($conn);
    break;        
//    case'insert3'://insert tab3
//        if($dari==''||$sampai==''||$keterangan=='')
//        {
//            exit("Error:Field Tidak Boleh Kosong");
//        }
//        $str="select * from ".$dbname.".kebun_qc_5gradekondisi where dari='".$dari."' and sampai = '".$sampai."'";
//        $res=mysql_query($str);
//        if(mysql_num_rows($res)>0)exit("Error: Data Sudah Ada");
//        $sql="insert into ".$dbname.".kebun_qc_5gradekondisi 
//        (`dari`,`sampai`,`keterangan`) 
//        values 
//        ('".$dari."','".$sampai."','".$keterangan."')";
//        if(mysql_query($sql)){
//        }
//        else
//        echo "DB Error : ".mysql_error($conn);
//    break;        
//    case'loadtab3': //load tab3
//        $sql="select * from ".$dbname.".kebun_qc_5gradekondisi order by dari, sampai";
//        $query=mysql_query($sql) or die(mysql_error());
//        while($res=mysql_fetch_assoc($query))
//        {
//            $no+=1;
//            echo"<tr class=rowcontent>
//            <td>".$no."</td>
//            <td align=right>".number_format($res['dari'])."</td>
//            <td align=right>".number_format($res['sampai'])."</td>
//            <td align=left>".$res['keterangan']."</td>
//            <td>
//                <img src=images/application/application_edit.png class=resicon title='Edit' onclick=\"filltab3('". $res['dari']."','".$res['sampai']."','".$res['keterangan']."');\">
//                <img src=images/application/application_delete.png class=resicon title='Delete' onclick=\"deltab3('". $res['dari']."','". $res['sampai']."');\" >
//            </td></tr>";
//        }
//    break;        
//    case 'update3': //update tab3
//        if($dari==''||$sampai==''||$keterangan=='')
//        {
//            exit("Error: Field Tidak Boleh Kosong");
//        }
//
//        $sql="update ".$dbname.".kebun_qc_5gradekondisi set dari='".$dari."', sampai='".$sampai."', keterangan='".$keterangan."' where dari='".$dariold."' and sampai='".$sampaiold."'";
//        if(mysql_query($sql)){
//        }
//        else
//        echo "DB Error : ".mysql_error($conn);
//    break;            
//    case'delete3': //delete tab3
//        $sql="delete from ".$dbname.".kebun_qc_5gradekondisi where `dari`='".$dari."' and `sampai`='".$sampai."'";
//        if(mysql_query($sql)){
//        }
//        else
//        echo "DB Error : ".mysql_error($conn);
//    break;


	default:
	break;
}
?>