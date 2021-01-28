<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
?>

<?
$proses = $_GET['proses'];
$param = $_POST;



switch($proses) {
    case 'showDetail':
		# Options
		$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"induk='".$param['divisi']."' or kodeorganisasi='".$param['divisi']."'");
		$optAct = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,kodekegiatan',"(pilih in ('4','7','9','10','12','13','14','15'))",'6');
		
		
		# Get Data
		$where = "notransaksi='".$param['notransaksi']."'";
		$cols = "kodeblok,kodekegiatan,hk,hasilkerjajumlah,hasilkerjalalu,satuan,hargasatuan,jumlahrp";
		$query = selectQuery($dbname,'log_spkdt',$cols,$where);
			$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			$dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
			$dataShow[$key]['kodekegiatan'] = $optAct[$row['kodekegiatan']];
		}
		
		# Form
		$theForm1 = new uForm('detailForm','Form Detail',2);
		$theForm1->addEls('kodeblok',$_SESSION['lang']['subunit'],'','select','L',25,$optBlok);
		$theForm1->_elements[0]->_attr['onchange'] = "updKegiatan()";
		$theForm1->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','select','L',25,$optAct);
		$theForm1->addEls('hk',$_SESSION['lang']['hk'],'1','textnum','R',10);
		$theForm1->addEls('hasilkerjajumlah',$_SESSION['lang']['hasilkerjajumlah'],'0','textnum','R',10);
		$theForm1->_elements[3]->_attr['onchange'] = "calcJumlah()";
		$theForm1->addEls('hasilkerjalalu',$_SESSION['lang']['hasilkerjalalu'],'0','textnum','R',10);
		$theForm1->addEls('satuan',$_SESSION['lang']['satuan'],'','text','L',10);
		$theForm1->addEls('hargasatuan',$_SESSION['lang']['hargasatuan'],'0','textnum','R',10);
		$theForm1->_elements[6]->_attr['onchange'] = "calcJumlah()";
		$theForm1->addEls('jumlahrp',$_SESSION['lang']['jumlah'],'0','textnum','R',10);
		$theForm1->_elements[7]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
		
		# Table
		$theTable1 = new uTable('detailTable','Tabel Detail',$cols,$data,$dataShow);
		
		# FormTable
		$formTab1 = new uFormTable('ftDetail',$theForm1,$theTable1,null,array('notransaksi'));
		$formTab1->_target = "log_slave_spk_detail";
		$formTab1->_numberFormat = '##jumlah';
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		$formTab1->render();
		echo "</fieldset>";
		break;
    case 'add':
	//exit ("Error:MASUK");
        $cols = array(
			'kodeblok','kodekegiatan','hk','hasilkerjajumlah','hasilkerjalalu','satuan',
			'hargasatuan','jumlahrp','notransaksi',
		);
		$data = $param;
		unset($data['numRow']);
		$data['jumlahrp'] = str_replace(',','',$data['jumlahrp']);
		
		
		$query = insertQuery($dbname,'log_spkdt',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['notransaksi']);
		$res = "";
		foreach($data as $cont) {
			$res .= "##".$cont;
		}
		$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
		echo $result;
		break;
		
		
    case 'edit':
	$data = $param;
        unset($data['notransaksi']);
	$data['jumlahrp'] = str_replace(',','',$data['jumlahrp']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	$where = "notransaksi='".$param['notransaksi']."' and kodeblok='".
		$param['cond_kodeblok']."' and kodekegiatan='".$param['cond_kodekegiatan']."'";
	$query = updateQuery($dbname,'log_spkdt',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
    case 'delete':
                //================periksa realisasi
             $m =0;
            $strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
                  where notransaksi='".$param['notransaksi']."'";
            $resx=mysql_query($strx);
            while($barx=mysql_fetch_array($resx))
            {
              $m= $barx[0]; 
            }   
            //lihat postingan-=============================
            $n ='';
            $strx="select statusjurnal from ".$dbname.".log_baspk 
                  where notransaksi='".$param['notransaksi']."' and statusjurnal=0";
            $resx=mysql_query($strx);           
            if(mysql_num_rows($resx)>0)
                $n ='?';
            
            if($n=='' and $m==0)
            {     
        //=================================
			$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
				$param['kodekegiatan']."'";
			$where = "notransaksi='".$param['notransaksi']."' and kodeblok='".
				$param['kodeblok']."' and kodekegiatan='".$param['kodekegiatan']."'";
			$query = "delete from `".$dbname."`.`log_spkdt` where ".$where;
			if(!mysql_query($query)) {
				echo "DB Error : ".mysql_error();
				exit;
			}
                    }
            else
            {
                exit('Error:Realisasi sudah terisi');
            } 
	break;
    case 'updKegiatan':
	$optBlokStat = makeOption($dbname,'setup_blok','kodeorg,statusblok',"kodeorg='".$param['kodeblok']."'");
	if(strlen(getFirstKey($optBlokStat))>8) {
	    $whereAct = "pilih in ('4','7','9','10','12','13','14','15') and kelompok='".getFirstContent($optBlokStat)."'";
		if(getFirstContent($optBlokStat)=='TM') {
			$whereAct .= " or kelompok='PNN' ";
		}
	} else {
	    $whereAct = "pilih in ('4','7','9','10','12','13','14','15')";
	}
	$optAct = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,kodekegiatan',$whereAct,'6');
	echo json_encode($optAct);
	break;
    default:
	break;
}
?>