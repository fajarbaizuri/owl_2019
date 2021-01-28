<?
require_once('master_validation.php');
//require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['tbs']==''?$tbs=$_GET['tbs']:$tbs=$_POST['tbs'];

	
switch($proses)
{
    
    case'getTBS1':
	
	$optPH3="";
	 $sOrg="SELECT a.kodecustomer,b.namasupplier FROM  ".$dbname.".`pabrik_timbangan` a left join ".$dbname.".log_5supplier b ON a.kodecustomer =b.kodetimbangan where a.kodeorg='' and a.kodebarang ='40000003' group by a.kodecustomer order by b.namasupplier asc";
    //exit("Error:".$sOrg);
    $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
    while($rOrg=mysql_fetch_assoc($qOrg))
    {
        $optPH3.="<option value=".$rOrg['kodecustomer'].">".$rOrg['namasupplier']."</option>";
    }
	$optUNIT2="<option value='TDAE'>KEBUN TADU A</option>";
	$optUNIT2.="<option value='TDBE'>KEBUN TADU B</option>";
	$optUNIT3="<option value='USJE'>KEBUN USJ</option>";
	
    $optUNIT="<option value=''>".$_SESSION['lang']['all']."</option>";
	
	if ($tbs=="0"){
		$optUNIT.=$optUNIT2;
		$optUNIT.=$optUNIT3;
		$optUNIT.=$optPH3;
		
	}elseif ($tbs=="2"){
		$optUNIT.=$optUNIT2;
		
	}elseif ($tbs=="3"){	
		$optUNIT.=$optUNIT3;
		
	}else{
		$optUNIT.=$optPH3;
	}
	
   
	
    echo $optUNIT;
    break;
    default:
    break;
}
	






?>