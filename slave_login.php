<?
session_start();
require_once('config/connection.php');
require_once('lib/detailSession.php');
 $strj="select * from ".$dbname.".tipeakses where status=1";
 $resj=mysql_query($strj);
 echo mysql_error($conn);
 if(mysql_num_rows($resj)>0)
 {
	$_SESSION['security']='on';
 }
 else
 {
	$_SESSION['security']='off'; 	
 }
//load local ini++++++++++++++++++++++++++++++++
$ini_array = parse_ini_file("lib/nangkoel.ini");
$_SESSION['MAXLIFETIME']=$ini_array['MAXLIFETIME'];
$_SESSION['DIE']=time()+$_SESSION['MAXLIFETIME'];
//++++++++++++++++++++++++++++++++++
$uname   =addslashes($_POST['uname']);
$password=addslashes($_POST['password']);
$language=$_POST['language'];
$str1    ="select * from ".$dbname.".user
		   where namauser='".$uname."'
		   and password=MD5('".$password."') order by namauser limit 1";
$uid=0;
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

if($res1=mysql_query($str1))
{
	if(mysql_num_rows($res1)>0)
	{
		//update login status
		$stra="update ".$dbname.".user set 
		       logged=1,
			   lastip='".$_SERVER['REMOTE_ADDR']."',
			   lastcomp='".$hostname."'
			   where namauser='".$uname."'";
		mysql_query($stra);
		//set standard session
		while($bar1=mysql_fetch_object($res1))
		{
			$_SESSION['standard']['username']=$bar1->namauser;
			$_SESSION['standard']['access_level']=$bar1->hak;
			$_SESSION['standard']['lastupdate']=$bar1->lastupdate;			
			$_SESSION['standard']['userid']=$bar1->karyawanid;
			$_SESSION['standard']['status']=$bar1->status;
			$_SESSION['standard']['logged']=$bar1->logged;
			$_SESSION['standard']['lastip']=$bar1->lastip;
			$_SESSION['standard']['lastcomp']=$bar1->lastcomp;
		}
		if($_SESSION['standard']['status']==0)//if user status is inactive
		{
			 echo" Gagal, Your Account is inactive";
			 session_destroy();
			 exit;
		}
		//set language session
		$_SESSION['language']=$language;
		$strlang="select legend,".$language." from ".$dbname.".bahasa order by legend";
		$reslang=mysql_query($strlang);
		while($barlang=mysql_fetch_array($reslang))
		{
			$_SESSION['lang'][$barlang[0]]=$barlang[1];
		}
		//set other sessio and  variables
		if(isset($_SESSION['standard']['username']))
		{
			//get all data from user_empl table
			setEmplSession($conn,$_SESSION['standard']['userid'],$dbname);

			if($isPrivillaged=getPrivillageType($conn,$dbname))//get access_type, if nothong then kick
			{}
			else
			{
				 if($_SESSION['security']=='on')//if turned on
				 {
					 echo" Gagal, Sorry, No Privillage available for all\ncontact Administrator";
					 session_destroy();
					 exit;
				 }
				 else
				 {
				 	
				 }
			}
			
			$privable=getPrivillages($conn,$_SESSION['standard']['username'],$dbname);//get user privillages
			if(!$privable AND $_SESSION['access_type']=='detail')// if nothong then kick
			{
				 echo" Gagal, Sorry, No Privillage available for your account";
				 session_destroy();
				 exit;				
			}		
			else if($_SESSION['standard']['access_level']==0 AND $_SESSION['access_type']!='detail')
			{
				 if($_SESSION['security']=='on'){//if security is turned on
				 echo" Gagal, Sorry, System uses Levelization Privillages, but you don't have any.\nContact your Administrator";
				 session_destroy();
				 exit;
				 }
				 else
				 {
				 	//if turned off, grant all privillages
				 }
			}
				
			setEmployer($conn,$dbname);//get employer detail and active transaction periode
		}
	}
	else
	{
		echo "<font color=#AA3322 style='background-color:#FFFFFF'>Wrong username and/or password</font><br><span   style='background-color:#FFFFFF'>Att: This uses case-sensitif</span>";
	}	
}
else
{
     echo " Gagal, System meet some difficulties to preform your request.\n
	        Please contact administrator regarding your login problem";	
}
?>