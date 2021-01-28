<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
?><?

$proses = $_GET['proses'];
$param = $_POST;

//echo $param['tipeinvoice'];exit();
//echo $param['nopo'];exit();
//nopo
switch($proses) {
    case 'showDetail':
		# Options
		if($param['tipeinvoice']==po)
			{
				$optSj = makeOption($dbname,'log_transaksiht','notransaksi,notransaksi',"nopo='".$param['nopo']."'");
			if(empty($optSj)) {
				$optSj = array(''=>'');
			} else {
				$firstSj = getFirstKey($optSj);
				$q1 = selectQuery($dbname,'log_transaksidt',"sum(jumlah*hargasatuan) as nilai",
					"notransaksi='".$firstSj."'")." group by notransaksi";
				$resNilai = fetchData($q1);
				$nilai = number_format($resNilai[0]['nilai'],0);
			}
		}
		else
		{
			//echo a;exit();//perubahan log_baspk
			$optSj = makeOption($dbname,'log_baspk','notransaksi,notransaksi',"notransaksi='".$param['nopo']."'");
		if(empty($optSj)) {
			$optSj = array(''=>'');
		} else {
			/*$firstSj = getFirstKey($optSj);
			$q1 = selectQuery($dbname,'log_baspk',"jumlahrealisasi",
				"notransaksi='".$firstSj."'")." ";
			$resNilai = fetchData($q1);
			$nilai = number_format($resNilai[0]['jumlahrealisasi'],0);*/
			
			
			
			$nilai=makeOption($dbname,'log_baspk','jumlahrealisasi,jumlahrealisasi,kodeblok,kodekegiatan,tanggal',"notransaksi='".$param['nopo']."' and statusjurnal='1'",$mode='7');
			
			// function makeOption($dbname,$tableName,$column,$where=null,$mode='0',$empty=false) {
			
			
			/*$a="select * from ".$dbname.".log_baspk where notransaksi='".$param['nopo']."' and statusjurnal='1' ";
			$b=mysql_query($a);
			while($c=mysql_fetch_assoc($b))
			{
				$nilai.="<option value=".$c['jumlahrealisasi'].">".$c['jumlahrealisasi']."</option>";
				
			}
			echo $nilai;*/
			
			
		/*	$q1 = selectQuery($dbname,'log_baspk','jumlahrealisasi',
			"notransaksi='".$param['nopo']."'")."";
		$resNilai = fetchData($q1);
		$nilai = number_format($resNilai[0]['nilai'],0);
		echo $nilai;
			*/
			
			
		}
		}
		
		
		
		
		
		
		
		
		
		# Get Data
		$where = "noinvoice='".$param['noinvoice']."'";
		$cols = "nosj,nilai,kodeblok,kodekegiatan,tanggal2";
		$query = selectQuery($dbname,'keu_tagihandt',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		
		# Form
		$theForm2 = new uForm('transForm','Form Tagihan');
		$theForm2->addEls('nosj','BAPP/BAST','','select','L',25,$optSj);
		$theForm2->_elements[0]->_attr['onchange'] = "getNilaiBAPP()";
		if($param['tipeinvoice']==po)
		{
			$theForm2->addEls('nilai',$_SESSION['lang']['nilai'],$nilai,'textnum','L',25);
			$theForm2->_elements[1]->_attr['disabled'] = 'disabled';
		}
		else
		{
			$theForm2->addEls('nilai','Nilai','','select','L',25,$nilai);
			$theForm2->_elements[1]->_attr['onchange'] = "getbawah()";
		}
		
		$theForm2->addEls('kodeblok',$_SESSION['lang']['kodeblok'],'','text','L',25);
		$theForm2->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','text','L',25);
		$theForm2->addEls('tanggal2',$_SESSION['lang']['tanggal2'],'','textnum','L',25);
		
		//$theForm2->addEls('nilai',$_SESSION['lang']['nilai'],$nilai,'textnum','L',25);
		//$theForm2->_elements[1]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
		//$theForm2->_elements[1]->_attr['disabled'] = 'disabled';
			
		# Table
		$theTable2 = new uTable('transTable','Tabel Tagihan',$cols,$data,$dataShow);
		
		
	
		# FormTable
		$formTab2 = new uFormTable('transFT',$theForm2,$theTable2,null,array('noinvoice'));
		$formTab2->_target = "keu_slave_tagihan_detail";
		$formTab2->_numberFormat = '##nilai';
		$formTab2->_noClearField= '##nilai';
		$formTab2->_noEnable= '##nilai';
		#$formTab2->_nourut = true;
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		$formTab2->render();
		echo "</fieldset>";
		break;
    case 'add':
		$cols = array(
			'nosj','nilai','kodeblok','kodekegiatan','tanggal2','noinvoice'
		);
		$data = $param;
		unset($data['numRow']);
		$data['nilai'] = str_replace(',','',$data['nilai']);
		
		$query = insertQuery($dbname,'keu_tagihandt',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		} else {
			updateNilai($param['noinvoice']);
		}
		
		unset($data['noinvoice']);
		
		$res = "";
		foreach($data as $cont) {
			$res .= "##".$cont;
		}
		
		$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
		echo $result;
		break;
    case 'edit':
		$data = $param;
		unset($data['noinvoice']);
		$data['nilai'] = str_replace(',','',$data['nilai']);
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}
		$where = "noinvoice='".$param['noinvoice']."' and nosj='".
			$param['cond_nosj']."' ";
		$query = updateQuery($dbname,'keu_tagihandt',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		} else {
			updateNilai($param['noinvoice']);
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "noinvoice='".$param['noinvoice']."' and nosj='".$param['nosj']."' and nilai='".$param['nilai']."'";
		$query = "delete from `".$dbname."`.`keu_tagihandt` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		} else {
			updateNilai($param['noinvoice']);
		}
		break;
		








case 'getbawah':
	//exit("Error:MASUK");
		$sql="select kodeblok,kodekegiatan,tanggal from ".$dbname.".log_baspk WHERE notransaksi='".$param['nosj']."' and jumlahrealisasi='".$param['nilai']."' ";	
		$qry=mysql_query($sql) or die(mysql_error());
		$data=mysql_fetch_assoc($qry);
		echo $data['kodeblok']."###".$data['kodekegiatan']."###".$data['tanggal'];
	break;









		
		
	case 'getBAPP':
	
	if($param['tipeinvoice']==po)
		{
			$q1 = selectQuery($dbname,'log_transaksidt',"sum(jumlah*hargasatuan) as nilai",
			"notransaksi='".$param['nosj']."'")." group by notransaksi";
		$resNilai = fetchData($q1);
		$nilai = number_format($resNilai[0]['nilai'],0);
		echo $nilai;
		
		}
		
	else
	{
		$q1 = selectQuery($dbname,'log_spkht',"jumlahrp",
			"notransaksi='".$param['nosj']."'")." group by notransaksi";
			echo $q1;exit("Error:$q1");
		$resNilai = fetchData($q1);	
		$nilai = number_format($resNilai[0]['jumlahrp'],0);
		
		/*$a="select jumlahrealisasi from ".$dbname.".log_baspk where notransaksi='".$param['nosj']."'";
		echo $a;exit();
		$b=mysql_query($a);
		$c=mysql_fetch_assoc($b);
		$harga=$c['jumlahrealisasi'];
		echo $harga;*/
		
	}
		
		
		break;
    default:
	break;
}

function updateNilai($noInv) {
	global $dbname;
	// Sum Detail
	$qDet = selectQuery($dbname,'keu_tagihandt','sum(nilai) as nilai',"noinvoice='".$noInv."'").
		" group by noinvoice";
	$resDet = fetchData($qDet);
	if(empty($resDet)) {
		$nilai=0;
	} else {
		$nilai=$resDet[0]['nilai'];
	}
	
	// Update Header
	$data = array('nilaiinvoice'=>$nilai);
	$where = "noinvoice='".$noInv."'";
	$qUpd = updateQuery($dbname,'keu_tagihanht',$data,$where);
	if(!mysql_query($qUpd)) {
		exit("DB Error: ".mysql_error());
	}
}
?>