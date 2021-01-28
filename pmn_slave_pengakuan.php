<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('master_validation.php');
require_once('config/connection.php');


$nokon=$_POST['nokon'];
$intalb=$_POST['intalb'];
$eks=$_POST['eks'];
$netvhc=$_POST['netvhc'];
$vhc=$_POST['vhc'];

$neteks=$_POST['neteks'];
$netint=$_POST['netint'];
$eksair=$_POST['eksair'];
$intair=$_POST['intair'];
$ekskot=$_POST['ekskot'];
$intkot=$_POST['intkot'];
$eksalb=$_POST['eksalb'];

$tgl=tanggalsystem($_POST['tgl']);
//exit("Error:$tgl");

$method=$_POST['method'];

$optnm=makeOption($dbname,'log_5supplier','supplierid,namasupplier');


switch($method)
{
	case 'insert':
		$str="insert into ".$dbname.".pmn_pengakuan (
				nokontrak,ekspeditor,vhc,nettoint,intair,
				intkotoran,intalb,nettovhc,nettoext,extair,
				extkotoran,extalb,tanggal)
			  values(
			  '".$nokon."','".$eks."','".$vhc."','".$netint."','".$intair."',
			  '".$intkot."','".$intalb."','".$netvhc."','".$neteks."','".$eksair."',
			  '".$ekskot."','".$eksalb."','".$tgl."')";
		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}	
	break;
	
	case 'update':	
		$str="update ".$dbname.".pmn_pengakuan set nokontrak='".$nokon."',ekspeditor='".$eks."',vhc='".$vhc."',nettoint='".$netint."',intair='".$intair."',intkotoran='".$intkot."',intalb='".$intalb."',nettovhc='".$netvhc."',nettoext='".$neteks."',extair='".$eksair."',extkotoran='".$ekskot."',extalb='".$eksalb."',tanggal='".$tgl."'   where nokontrak='".$nokon."' and ekspeditor='".$eks."' ";
		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	case 'delete':
		$str="delete from ".$dbname.".pmn_pengakuan where nokontrak='".$nokon."' and ekspeditor='".$eks."'";
		if(mysql_query($str))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	
	case'loadData':
		
	
		
		$no=0;
		$str="select * from ".$dbname.".pmn_pengakuan";
		//echo $str;
		$res=mysql_query($str) or die(mysql_error());
		while($bar=mysql_fetch_assoc($res))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$bar['nokontrak']."</td>";
			$tab.="<td align=left>".$optnm[$bar['ekspeditor']]."</td>";
			$tab.="<td align=left>".tanggalnormal($bar['tanggal'])."</td>";
			$tab.="<td align=right>".$bar['vhc']."</td>";
			$tab.="<td align=right>".$bar['nettoint']."</td>";
			$tab.="<td align=right>".$bar['intair']."</td>";
			$tab.="<td align=right>".$bar['intkotoran']."</td>";
			$tab.="<td align=right>".$bar['intalb']."</td>";
			$tab.="<td align=right>".$bar['nettovhc']."</td>";
			$tab.="<td align=right>".$bar['nettoext']."</td>";
			$tab.="<td align=right>".$bar['extair']."</td>";
			$tab.="<td align=right>".$bar['extkotoran']."</td>";
			$tab.="<td align=right>".$bar['extalb']."</td>";
			$tab.="<td align=center>
				<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField(
					'".$bar['nokontrak']."',
					'".$bar['ekspeditor']."',
					'".$bar['vhc']."',
					'".$bar['nettoint']."',
					'".$bar['intair']."',
					'".$bar['intkotoran']."',
					'".$bar['intalb']."',
					'".$bar['nettovhc']."',
					'".$bar['nettoext']."',
					'".$bar['extair']."',
					'".$bar['extkotoran']."',
					'".$bar['extalb']."',
					'".tanggalnormal($bar['tanggal'])."'
					);\">
				<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"Del('".$bar['nokontrak']."','".$bar['ekspeditor']."');\">
			</td>";
		echo $tab;	
		}
		
	default;
}

?>