<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	case 'getBeratBersih':  
	    //sesuai IM050/FBG-DIR/IM/IX/2016  
		$query = selectQuery($dbname,'pabrik_timbangan','sum(beratbersih)-sum(kgpotsortasi) as beratbersih',
			"date(tanggal) ='".tanggalsystem($param['tanggal'])."' and kodebarang='40000003'");
			/*
	    if(tanggalsystem($param['tanggal']) >="20161001"){
		
			$query = selectQuery($dbname,'pabrik_timbangan','sum(if(kodeorg="",beratbersih-kgpotsortasi,beratbersih)) as beratbersih',
			"date(tanggal) ='".tanggalsystem($param['tanggal'])."' and kodebarang='40000003'");	
			
		}else{
			//$query = selectQuery($dbname,'pabrik_timbangan','sum(beratbersih) as beratbersih',
			$query = selectQuery($dbname,'pabrik_timbangan','sum(beratbersih)-sum(kgpotsortasi) as beratbersih',
			"date(tanggal) ='".tanggalsystem($param['tanggal'])."' and kodebarang='40000003'");
		}
		*/
		$res = fetchData($query);//(a.beratbersih/a.jjgsortasi) as bjr
		$q2 = selectQuery($dbname,'pabrik_produksi','sisahariini',
			"date(tanggal)<'".tanggalsystem($param['tanggal'])."'","tanggal desc",false,1,1);
		$res2 = fetchData($q2);
		
		$q3 = selectQuery($dbname,'pabrik_pengolahan','sum(tbsdiolah) as tbsdiolah',
			"tanggal = '".tanggalsystem($param['tanggal'])."'");
		$res3 = fetchData($q3);
		
		$res = array(
			'tbsmasuk'=>$res[0]['beratbersih'],
			'tbsdiolah'=>$res3[0]['tbsdiolah'],
			'sisahariini'=>$res2[0]['sisahariini']
		);
		echo json_encode($res);
		break;
}
?>