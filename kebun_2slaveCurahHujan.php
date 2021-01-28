<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];
$cmpId=$_POST['cmpId'];
$period=$_POST['period'];


	switch($proses)
	{
		case'GetData':
		echo"
		<table cellspacing=1 border=0>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>".$_SESSION['lang']['tanggal']."</td> 
		<td>".$_SESSION['lang']['pagi']."</td>
		<td>".$_SESSION['lang']['sore']."</td>	 
		<td>".$_SESSION['lang']['note']."</td>
		</tr>
		</thead>
		";

		$sCrh="select * from ".$dbname.".kebun_curahhujan where kodeorg='".$cmpId."' and tanggal like '%".$period."%'";
		$qCrh=mysql_query($sCrh) or die(mysql_error());
		$rCek=mysql_num_rows($qCrh);
		if($rCek>0)
		{
			while($rCrh=mysql_fetch_assoc($qCrh))
			{
				$no+=1;
				echo"<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$rCrh['tanggal']."</td> 
				<td>".$rCrh['pagi']."</td>
				<td>".$rCrh['sore']."</td>	 
				<td>".$rCrh['catatan']."</td>
				</tr>
				";
			}
		}
		else
		{
			echo"<tr class='rowcontent'><td colspan=5>Not Found</td></tr>";
		}
		echo"</table>";
		break;
		default:
		break;
	}

?>