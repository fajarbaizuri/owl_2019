<?
require_once('master_validation.php');
include('lib/nangkoelib.php');

$email=$_POST['email'];
$subject="Test";
$body="<html><body>
       Ini Body email
	   </body></html>";

$cek=kirimEmail($email,$subject,$body,$mailType='text/html');	   
if($cek!=1)
echo " Warning:".$cek;
?>