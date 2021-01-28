<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kdorg=$_POST['kdorg'];
$kdOrg=$_POST['kdOrg'];
$kdunit=$_POST['kdunit'];

switch($proses)
{
	case'getunit':
	//exit("Error:HAHA");
		if ($_SESSION['empl']['tipelokasitugas']=='HOLDING')
		{
			$a="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi WHERE induk='".$kdorg."' ";
		}
		else
		{
			$a="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi WHERE kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ";
		}//exit("Error:$a");
			
		if ($_SESSION['empl']['tipelokasitugas']=='HOLDING')
		{	
			$optunit="<option value=''>".$_SESSION['lang']['all']."</option>";
		}
		else
		{
			$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		}
		$b=mysql_query($a) or die(mysql_error());
		while($c=mysql_fetch_assoc($b))
		{
			$optunit.="<option value=".$c['kodeorganisasi'].">".$c['namaorganisasi']."</option>";
		}
		echo $optunit;
	break;
	
	case'getunitrk':
	//exit("Error:HAHA");
		
		$a="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi WHERE induk='".$kdorg."' ";
		$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$b=mysql_query($a) or die(mysql_error());
		while($c=mysql_fetch_assoc($b))
		{
			$optunit.="<option value=".$c['kodeorganisasi'].">".$c['namaorganisasi']."</option>";
		}
		echo $optunit;
	break;
	
		case'getunit2':
	//exit("Error:HAHA");

		$a="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi WHERE induk='".$kdOrg."' ";
		$optunit="<option value=''>".$_SESSION['lang']['all']."</option>";
		$b=mysql_query($a) or die(mysql_error());
		while($c=mysql_fetch_assoc($b))
		{
			$optunit.="<option value=".$c['kodeorganisasi'].">".$c['namaorganisasi']."</option>";
		}
		echo $optunit;
	break;
	
}
?>