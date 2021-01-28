<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');

$email=$_POST['email'];
$subject="Kirim_1";
$body='<html><body>
	Tes kirim pertama
	</body></html>';
$cek=kirimEmail($email,$subject,$body,$mailType='text/html');
if($cek!=1)
echo "Warning:".$check;
?>