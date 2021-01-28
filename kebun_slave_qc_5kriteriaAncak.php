<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$KbnId=$_POST['KbnId'];
$nm_ancak=$_POST['nm_ancak'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$user_entry=$_SESSION['standard']['userid'];
$idAncak=$_POST['idAncak'];
$nilDari=$_POST['nilDari'];
$nilSmp=$_POST['nilSmp'];
$nilai=$_POST['nilai'];
$grNilDr=$_POST['grNilDr'];
$grNilSmp=$_POST['grNilSmp'];
$grKet=$_POST['grKet'];
$proses=$_POST['proses'];
$grSmpOld=$_POST['grSmpOld'];
$grDrOld=$_POST['grDrOld'];
switch($proses)
{
	case'getKdDt':
	$optKdvhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sql="select distinct kode,nama from ".$dbname.".kebun_qc_5stancak order by nama asc";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{  
            if($idAncak!='')
            {
		$optKdvhc.="<option value='".$res['kode']."'>".$res['nama']." - ".$res['nama']."</option>";
            }
            else
            {
                $optKdvhc.="<option value='".$res['kode']."' ".($res['kode']==$idAncak?'selected=selected':'').">".$res['kode']." - ".$res['nama']."</option>";
            }
	}
	echo $optKdvhc;
	break;
	
	
	case'insert_header'://insert untuk kriteria ancak
       
	if($nm_ancak==''||$KbnId=='')
	{
		exit("Error:Field Tidak Boleh Kosong");
	}
	
        $str="select * from ".$dbname.".kebun_qc_5stancak where kode='".$KbnId."'";
        //exit("Error".$str) ;
        $res=mysql_query($str);
        if(mysql_num_rows($res)>0)
        $aktif=true;
        else
        $aktif=false;
        if($aktif==true)
        {
        exit("Error:Data Sudah Ada");
        }
	
	$sqlCek="select kode from ".$dbname.".kebun_qc_5stancak where kode='".$KbnId."'"; 
	$queryCek=mysql_query($sqlCek) or die(mysql_error());
	$rowCek=mysql_fetch_row($queryCek);
	if($rowCek<1)
	{
		$sql="insert into ".$dbname.".kebun_qc_5stancak 
		(`kode`,`nama`) 
		values ('".$KbnId."','".$nm_ancak."')";
		if(mysql_query($sql))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	}
	else
	{
		echo"warning:Data Sudah Ada";
		exit();
	}
	break;
	
	case 'update'://update untuk kriteria ancak
	if($nm_ancak==''||$KbnId=='')
	{
		exit("Error:Field Tidak Boleh Kosong");
	}
	$sql="update ".$dbname.".kebun_qc_5stancak set nama='".$nm_ancak."' where kode='".$KbnId."'";
	//echo "warning:".$sql;
	if(mysql_query($sql))
	echo"";
	else
	echo "DB Error : ".mysql_error($conn);
	break;
	
	
	case'load_data_header'://load data untuk kriteria ancak
	$limit=20;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_5stancak order by kode asc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}

	//$sql="select * from ".$dbname.".kebun_qc_5stancak order by nama desc limit ".$offset.",".$limit."";
        $sql="select * from ".$dbname.".kebun_qc_5stancak order by kode asc";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=center>".$res['kode']."</td>
		<td align=left>".$res['nama']."</td>
		";
                echo"
                <td><img src=images/application/application_edit.png class=resicon  title='Edit' 
                onclick=\"fillField('". $res['kode']."','".$res['nama']."');\">
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delHead('". $res['kode']."');\" >";

	}
//	echo" </tr><tr class=rowheader><td colspan=11 align=center>
//				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
//				<br />
//				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//				</td>
//				</tr>";
	break;
        case'insert_pekerjaan'://insert untuk nilai ancak
        
	if($idAncak==''||$nilDari=='0'||$nilSmp=='0'||$nilai=='0')
	{
		exit("Error:Field Tidak Boleh Kosong");
	}
	
	
	$sqlCek="select kode from ".$dbname.".kebun_qc_5nilaiancak where kode='".$idAncak."'"; 
	$queryCek=mysql_query($sqlCek) or die(mysql_error());
	$rowCek=mysql_fetch_row($queryCek);
	if($rowCek<1)
	{
		$sql="insert into ".$dbname.".kebun_qc_5nilaiancak 
		(`kode`,`dari`,`sampai`,`nilai`) 
		values ('".$idAncak."','".$nilDari."','".$nilSmp."','".$nilai."')";
		if(mysql_query($sql))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	}
	else
	{
		echo"warning:Data Sudah Ada";
		exit();
	}
        break;
        case'update_pekerjaan'://update untuk nilai ancak
        if($idAncak==''||$nilDari=='0'||$nilSmp=='0'||$nilai=='0')
	{
		exit("Error:Field Tidak Boleh Kosong");
	}
        $sql="update ".$dbname.".kebun_qc_5nilaiancak set `dari`='".$nilDari."',`sampai`='".$nilSmp."',`nilai`='".$nilai."' 
              where `kode`='".$idAncak."'";
        if(mysql_query($sql))
        echo"";
        else
        echo "DB Error : ".mysql_error($conn);
        
        break;
        case'delDataNilai'://delete untuk nilai ancak
        $sql="delete from ".$dbname.".kebun_qc_5nilaiancak  where `kode`='".$idAncak."'";
        if(mysql_query($sql))
        echo"";
        else
        echo "DB Error : ".mysql_error($conn);
        break;
    
        case'load_data_nilai'://load data untuk nilai ancak
	$limit=20;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_5nilaiancak order by kode asc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}

	//$sql="select * from ".$dbname.".kebun_qc_5nilaiancak order by kebun_qc_5nilaiancak desc limit ".$offset.",".$limit."";
        $sql="select * from ".$dbname.".kebun_qc_5nilaiancak order by kode asc";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=left>".$res['kode']."</td>
                <td align=right>".$res['dari']."</td>
                <td align=right>".$res['sampai']."</td>
                <td align=right>".$res['nilai']."</td>
		";
                echo"
                <td><img src=images/application/application_edit.png class=resicon  title='Edit' 
                onclick=\"fillData('". $res['kode']."','".$res['dari']."','".$res['sampai']."','".$res['nilai']."');\">
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['kode']."');\" >";

	}
//	echo" </tr><tr class=rowheader><td colspan=11 align=center>
//				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
//				<br />
//				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//				</td>
//				</tr>";
	break;
	case'insert_grade':
        //exit("Error:masuk");
	if($grNilDr=='0'||$grNilSmp=='0'||$grKet=='')
	{
		exit("Error:Field Tidak Boleh Kosong");
	}
	
	
	$sqlCek="select dari,sampai from ".$dbname.".kebun_qc_5gradepanen where dari='".$grNilDr."' and sampai='".$grNilSmp."'"; 
	$queryCek=mysql_query($sqlCek) or die(mysql_error());
	$rowCek=mysql_fetch_row($queryCek);
	if($rowCek<1)
	{
		$sql="insert into ".$dbname.".kebun_qc_5gradepanen 
		(`dari`,`sampai`,`keterangan`) 
		values ('".$grNilDr."','".$grNilSmp."','".$grKet."')";
                //echo $sql;
		if(mysql_query($sql))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
	}
	else
	{
		echo"warning:Data Sudah Ada";
		exit();
	}
        break;
	case'load_data_grade'://load data untuk grade panen
	$limit=20;
	$page=0;
	if(isset($_POST['page']))
	{
	$page=$_POST['page'];
	if($page<0)
	$page=0;
	}
	$offset=$page*$limit;
	
	$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_5gradepanen order by dari,sampai asc";// echo $ql2;
	$query2=mysql_query($ql2) or die(mysql_error());
	while($jsl=mysql_fetch_object($query2)){
	$jlhbrs= $jsl->jmlhrow;
	}

	//$sql="select * from ".$dbname.".kebun_qc_5nilaiancak order by kebun_qc_5nilaiancak desc limit ".$offset.",".$limit."";
        $sql="select * from ".$dbname.".kebun_qc_5gradepanen order by dari,sampai asc";
	$query=mysql_query($sql) or die(mysql_error());
	while($res=mysql_fetch_assoc($query))
	{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
                <td align=right>".$res['dari']."</td>
                <td align=right>".$res['sampai']."</td>
                <td align=left>".$res['keterangan']."</td>
		";
                echo"
                <td><img src=images/application/application_edit.png class=resicon  title='Edit' 
                onclick=\"fillDataGrade('".$res['dari']."','".$res['sampai']."','".$res['keterangan']."');\">
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataGrade('". $res['dari']."','".$res['sampai']."');\" >";

	}
//	echo" </tr><tr class=rowheader><td colspan=11 align=center>
//				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
//				<br />
//				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//				</td>
//				</tr>";
	break;
        case'update_grade':
        if($grNilDr=='0'||$grNilSmp=='0'||$grKet=='')
        {
        exit("Error:Field Tidak Boleh Kosong");
        }
            $sql="update ".$dbname.".kebun_qc_5gradepanen set
            (`dari`='".$grNilDr."',`sampai`='".$grNilSmp."',`keterangan`='".$grKet."' where dari='".$grDrOld."' and sampai='".$grSmpOld."'";
            //echo $sql;
            if(mysql_query($sql))
            echo"";
            else
            echo "DB Error : ".mysql_error($conn);
        break;
        case'delGradePanen':
        $sql="delete from ".$dbname.".kebun_qc_5gradepanen where dari='".$grNilDr."' and sampai='".$grNilSmp."'";
        if(mysql_query($sql))
        echo"";
        else
        echo "DB Error : ".mysql_error($conn);
        break;
	default:
	break;
}
?>