<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('config/connection.php');

$proses=$_POST['proses'];
$kdOrg=$_POST['kdDiv'];
$noSpb=$_POST['noSpb'];

$nopol=isset($_POST['nopol'])?$_POST['nopol']:'';
$op=isset($_POST['op'])?$_POST['op']:'';

//exit($nopol.$op);

$periode=isset($_POST['periode'])?$_POST['periode']:'';
$tgl=isset($_POST['tgl'])?explode('-',$_POST['tgl']):array('','','');
$tglThn=$tgl[2];
$tglBln=$tgl[1];
$periodeB=$tglThn."-".$tglBln;

switch($proses)
{
	case 'createTable':
	//$kodeOrg=substr($id,8,6);
	//echo"warning:".$periode."___".$periodeB;exit();
	if($periode!=$periodeB)
	{
		echo"warning:Tanggal dan Periode tidak sama";
		exit();
	}
	
	
	$where=" induk='".$kdOrg."' and tipe='BLOK'"; //echo"warning:".$where;exit();
	$optBlok=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0',true);
	//$table .= "<table id='ppDetailTable'>";
	//echo"warning:".$table;
    # Header
	$table = "<thead>";
    $table .= "<tr class='rowheader'>";
    $table .= "<td>".$_SESSION['lang']['blok']."</td>";
 	$table .= "<td>".$_SESSION['lang']['bjr']."</td>";
  	$table .= "<td>".$_SESSION['lang']['janjang']."</td>";
    $table .= "<td>".$_SESSION['lang']['brondolan']."</td>";
  	$table .= "<td>".$_SESSION['lang']['mentah']."</td>";
	$table .= "<td>".$_SESSION['lang']['busuk']."</td>";
	$table .= "<td>".$_SESSION['lang']['matang']."</td>";
	$table .= "<td>".$_SESSION['lang']['lewatmatang']."</td>";
    $table .= "<td colspan=3>Action</td>";
    $table .= "</tr>";
    $table .= "</thead>";
	$table .= "<tbody id='detailBody'>";
	$table .= "<tr id='detail_tr' class='rowcontent'>";
	$table .= "<td>".makeElement("blok",'select','',
	array('style'=>'width:150px','onchange'=>"getBjr()"),$optBlok)."<input type=hidden id=oldBlok name=oldBlok value='' /></td>";
	$table .= "<td>".makeElement("bjr",'textnum','',
	array('style'=>'width:120px','disabled'=>'disabled',))."</td>";
	$table .= "<td>".makeElement("jjng",'textnum','0',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'5'))."</td>";
	$table .= "<td>".makeElement("brondln",'textnum','0',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'5'))."</td>";
	$table .= "<td>".makeElement("mnth",'textnum','0',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'5'))."</td>";
	$table .= "<td>".makeElement("bsk",'textnum','0',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'5'))."</td>";
	$table .= "<td>".makeElement("mtng",'textnum','0',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'5'))."</td>";
	$table .= "<td>".makeElement("lwtmtng",'textnum','0',
	array('style'=>'width:100px','onkeypress'=>'return angka_doang(event)','maxlength'=>'5'))."</td>";
	
    # Add, Container Delete
    $table .= "<td><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/>";
    $table .= "&nbsp;<img id='detail_delete' /></td>";
    $table .= "</tr>";
    $table .= "</tbody>";
  //  $table .= "</table>";
    echo $table;
	break;
	case 'detail_add' :
			$lokasi=$_SESSION['empl']['lokasitugas'];
			$lokasi=substr($lokasi,0,4);
			$entry_by=$_SESSION['standard']['userid'];
			#Check Header
			
			if(($data['jjng']=='') or ($data['brondolan']=='') or ($data['bjr']=='')) {
                echo "Error : Tolong lengkap data detail, data tidak boleh kosong";
                exit();
            }
			/*if(($data['jjng']==0) or ($data['bjr']==0) ) {
                echo "Error : ".$_SESSION['lang']['bjr'].",".$_SESSION['lang']['jjg']." tidak boleh kosong atau nol";
                exit();
            }*/
			$sql="select nospb from ".$dbname.".kebun_spbht where nospb='".$_POST['noSpb']."'";
			$query=mysql_query($sql) or die(mysql_error());
			$res=mysql_fetch_row($query);
			//echo "warning:".$res;exit();`
			if($res<1)
			{
				$sins="insert into ".$dbname.".kebun_spbht (`nospb`, `kodeorg`, `tanggal`, `nopolisi`, `operator`,`updateby`) values 
				('".$_POST['noSpb']."','".$_POST['kodeOrg']."','".tanggalsystem($_POST['tgl'])."','".$_POST['nopol']."','".$_POST['op']."','".$entry_by."')";
				//exit("Error:$sins");	
			
				if(mysql_query($sins))
				{
					$kgBjr=intval($_POST['jjng'])*intval($_POST['bjr']);
					$dins="insert into ".$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan,  mentah, busuk, matang, lewatmatang,kgbjr) 
					values ('".$_POST['noSpb']."','".$_POST['blok']."','".$_POST['jjng']."','".$_POST['bjr']."',
					'".$_POST['brondolan']."','".$_POST['mentah']."','".$_POST['busuk']."','".$_POST['matang']."','".$_POST['lwtmatang']."','".$kgBjr."')";
					//echo "warning:test".$dins;
					if(mysql_query($dins))
					{
						echo"";
					}
					else
					{
					//echo "warning:masuk";
					echo "DB Error : ".mysql_error($conn);
					}
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);
				}
			}
			else
			{
				$kgBjr=intval($_POST['jjng'])*intval($_POST['bjr']);
				$dins="insert into ".$dbname.".kebun_spbdt (nospb, blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang,kgbjr) 
				values ('".$_POST['noSpb']."','".$_POST['blok']."','".$_POST['jjng']."','".$_POST['bjr']."',
					'".$_POST['brondolan']."','".$_POST['mentah']."','".$_POST['busuk']."','".$_POST['matang']."','".$_POST['lwtmatang']."','".$kgBjr."')";
				//echo "warning:test".$dins;
				if(mysql_query($dins))
				{
					echo"";
				}
				else
				{
					echo "DB Error : ".mysql_error($conn);
				}
			}
            break;
	case'loadDetail':
	$sDet="select * from ".$dbname.".kebun_spbdt where nospb='".$noSpb."' order by blok desc";
	//echo $sDet;
	$qDet=mysql_query($sDet) or die(mysql_error());
	$no=0;
	while($rDet=mysql_fetch_assoc($qDet))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$rDet['blok']."</td>
		<td>".$rDet['bjr']."</td>
		<td>".$rDet['jjg']."</td>
		<td>".$rDet['brondolan']."</td>
		<td>".$rDet['mentah']."</td>
		<td>".$rDet['busuk']."</td>
		<td>".$rDet['matang']."</td>
		<td>".$rDet['lewatmatang']."</td>
		<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDetail('".$rDet['nospb']."','".$rDet['blok']."','".$rDet['jjg']."','".$rDet['bjr']."','".$rDet['brondolan']."','".$rDet['mentah']."','".$rDet['busuk']."','".$rDet['matang']."','".$rDet['lewatmatang']."');\">
			<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['nospb']."','".$rDet['blok']."');\" ></td>
		</tr>
		";
	}
	break;
	default:
	break;
}

?>