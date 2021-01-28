<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
?>

<?
$method=$_POST['method'];
$tgl=tanggalsystem($_POST['tgl']);
$kdorg=$_POST['kdorg'];
$ki=$_POST['ki'];
$ul=$_POST['ul'];
$um=$_POST['um'];
$ut=$_POST['ut'];
$scnama=$_POST['scnama'];
$tglcari=tanggalsystem($_POST['tglcari']);	
$optNm=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNik=makeOption($dbname, 'datakaryawan', 'karyawanid,nik');


/*if(isset($_POST['scnama']))
{
	$scnama=$_POST['scnama'];
	$tglcari=tanggalsystem($_POST['tglcari']);	
}
else
{
	$txtsearch='';
	$tglcari='';	
}

$where='';
if($scnama!='')
   $where= " and karyawanid like '%".$scnama."%' ";
 echo $where;
if($tglcari!='')
   $where .=" and tanggal='".$tglcari."') ";    
	else
	{} */


?>


<?
switch($method)
{
	######case load data
	
	
	case 'loadData';
	
	$tmbh='';
                if($scnama!='')
                {
                    $tmbh=" and karyawanid='".$scnama."' ";
                }
	
	$tmbh2='';
                if($tglcari!='')
                {
                    $tmbh2=" and tanggal='".$tglcari."' ";
                }			
	
		$no=0;
		$str="select * from ".$dbname.".sdm_spl where kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$tmbh." ".$tmbh2." ";
		//exit("Error:$str");
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$optNm[$bar1['karyawanid']]."</td>";
			$tab.="<td align=right>".tanggalnormal($bar1['tanggal'])."</td>";
			$tab.="<td align=left>".$optNik[$bar1['karyawanid']]."</td>";
			$tab.="<td align=right>".$bar1['kodeorg']."</td>";
			$tab.="<td align=right>".number_format($bar1['rplembur'])."</td>";
			$tab.="<td align=right>".number_format($bar1['rpmakan'])."</td>";
			$tab.="<td align=right>".number_format($bar1['rptransport'])."</td>";
			$cekstatus=$bar1['lock'];
			$tab.="<td align=center>";
				//$tab.="<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".tanggalnormal($bar1['tanggal'])."','".$bar1['kodeorg']."','".$bar1['jumlahjam']."','".$bar1['jenis']."');\">";
			if ($cekstatus==0)
			{
				$tab.="<img src=images/delete1.png class=resicon onclick=\"Del('".$bar1['karyawanid']."','".tanggalnormal($bar1['tanggal'])."');\"   title=Hapus >";
				$tab.="<img src=images/key_64.png class=resicon onclick=Lock('".$bar1['karyawanid']."','".tanggalnormal($bar1['tanggal'])."') title=Kunci>";							
			}
			else
			{
				$tab.="<img src=images/box/hmenu-lock.png class=resicon title=Terkunci>";
			}
			
			$tab.="<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_spl','".$bar1['karyawanid']."','','sdm_slave_sdm_spl_detail',event);\">";
			$tab.="</td>";
		echo $tab;
		}
	break;
	
	##########case delete
	case 'delete':
		$tab="delete from ".$dbname.".sdm_spl where karyawanid='".$ki."' and tanggal='".$tgl."' ";
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
	##########case kunci
	case 'lock':
		$tab="update ".$dbname.".sdm_spl set `lock`='1' where karyawanid='".$ki."' and tanggal='".$tgl."' ";
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;
	
	case 'insert':
		$indra="insert into ".$dbname.".sdm_spl (`karyawanid`,`tanggal`,`kodeorg`,`rplembur`,`rpmakan`,`rptransport`)
		values ('".$ki."','".$tgl."','".$kdorg."','".$ul."','".$um."','".$ut."')";
		//exit("Error.$sDel2");
		if(mysql_query($indra))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		
	
	
	break;
	
default;
}

?>