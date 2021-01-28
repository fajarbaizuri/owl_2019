<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$noSpb=$_POST['noSpb'];
$userOnline=$_SESSION['standard']['userid'];

switch($proses)
{
	case'loadNewData':
		$lokasi=$_SESSION['empl']['lokasitugas'];
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$slvhc="select * from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc limit ".$offset.",".$limit."";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
		
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rlvhc['nospb']."</td>
			<td>".tanggalnormal($rlvhc['tanggal'])."</td>";
			if($rlvhc['updateby']!=$_SESSION['standard']['userid'])
			{
			echo"
			<td>
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event);\">";
			if($rlvhc['posting']<1)
			{
			echo"&nbsp;<a href=# onClick=\"postingData('".$rlvhc['nospb']."');\">".$_SESSION['lang']['belumposting']."</a>";
			}
			else
			{
			echo "&nbsp;".$_SESSION['lang']['posting'];
			}
			echo"</td>";}
			else
			{
			echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event);\"></td>";
			}
	
		
		}
		echo"</tr>
		<tr class=rowheader><td colspan=5 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		break;
		
		
		case'cariNospb':
		$lokasi=$_SESSION['empl']['lokasitugas'];
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
			if(isset($_POST['txtSearch']))
		{
			$txt_search=$_POST['txtSearch'];
			$txt_tgl=tanggalsystem($_POST['txtTgl']);
			$txt_tgl_a=substr($txt_tgl,0,4);
			$txt_tgl_b=substr($txt_tgl,4,2);
			$txt_tgl_c=substr($txt_tgl,6,2);
			$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
		}
		else
		{
			$txt_search='';
			$txt_tgl='';			
		}
			if($txt_search!='')
			{
				$where="and nospb LIKE  '%".$txt_search."%'";
			}
			elseif($txt_tgl!='')
			{
				$where.="and tanggal LIKE '".$txt_tgl."'";
			}
			elseif(($txt_tgl!='')&&($txt_search!=''))
			{
				$where.="and nospb LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'";
			}
		//echo $strx; exit();
		if($txt_search==''&&$txt_tgl=='')
		{
			$slvhc="select * from ".$dbname.".kebun_spbht where kodeorg='".$lokasi."' ".$where." order by nospb desc limit ".$offset.",".$limit."";
			$ql2="select count(*) jmlhrow from ".$dbname.".kebun_spbht 	where  kodeorg='".$lokasi."' ".$where." order by nospb desc";			 
		}
		else
		{
				$slvhc="select * from ".$dbname.".kebun_spbht where  kodeorg='".$lokasi."' ".$where." order by nospb desc 
				limit ".$offset.",".$limit."";
				$ql2="select count(*) jmlhrow from ".$dbname.".kebun_spbht where  kodeorg='".$lokasi."' ".$where." order by nospb desc";
		}
		//$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		//$slvhc="select * from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc limit ".$offset.",".$limit."";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());	
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
		
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rlvhc['nospb']."</td>
			<td>".tanggalnormal($rlvhc['tanggal'])."</td>";
			if($rlvhc['updateby']!=$_SESSION['standard']['userid'])
			{
			echo"
			<td>
			<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$rlvhc['nospb']."','','kebun_spbPdf',event);\">";
			if($rlvhc['posting']<1)
			{
			echo"&nbsp;<a href=# onClick=\"postingData('".$rlvhc['nospb']."');\">".$_SESSION['lang']['belumposting']."</a>";
			}
			else
			{
			echo "&nbsp;".$_SESSION['lang']['posting'];
			}
			echo"</td>";}
			else
			{
			echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_spbht','".$res['nospb']."','','kebun_spbPdf',event);\"></td>";
			}
	
		
		}
		echo"</tr>
		<tr class=rowheader><td colspan=5 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		break;
		case'postingData':
		$sCek="select posting from ".$dbname.".kebun_spbht where nospb='".$noSpb."'";
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_assoc($qCek);
		if($rCek['posting']==0)
		{
			$sUp="update ".$dbname.".kebun_spbht set posting='1', postingby='".$userOnline."' where nospb='".$noSpb."'";
			if(mysql_query($sUp))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);	
		}
		else
		{
			echo"warning:This Data Already Posting";
		}
		break;
	default:
	break;
}

?>