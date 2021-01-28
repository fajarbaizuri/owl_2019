<?
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/help_tambah.js"></script>
<link rel=stylesheet type=text/css href=style/generic.css>

<?      
$proses = $_GET['proses'];
$param = $_GET;

$where = "kode='".$param['index']."' and modul='".$param['modul']."'";
$query = selectQuery($dbname,'owl_help','*',$where);
$res=mysql_query($query);

//$stream="".$_SESSION['lang']['isi']."";

while($bar=mysql_fetch_object($res))
{
    $isi = $bar->isi;      
}
$isi=str_replace("<##","<image src='image/",$isi);
$isi=str_replace("##>","'>",$isi);
$stream="$isi";  
echo $stream;      
?>
