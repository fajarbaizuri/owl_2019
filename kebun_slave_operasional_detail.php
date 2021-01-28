<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
?>
<?

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
		#== Prep Tab
		$headFrame = array(
			$_SESSION['lang']['prestasi'],
			$_SESSION['lang']['absensi']
			//$_SESSION['lang']['material']
		);
		$contentFrame = array();
		
		$blokStatus = $_SESSION['tmp']['actStat'];
##====================================================================================================
##====================================================================================================		
		//,299,161,159,74,251
		//if($_SESSION['empl']['lokasitugas']=="TDAE"){
		//		$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
		//	"tipekaryawan in (3,4) and kodejabatan in (250) order by a.subbagian,a.kodegolongan,a.namakaryawan";
		//}else if(($_SESSION['empl']['lokasitugas']=="TDBE")||($_SESSION['empl']['lokasitugas']=="USJE")){
				$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
			"tipekaryawan in (3,4) and kodejabatan in (250) and (tanggalkeluar >= CURDATE() or tanggalkeluar='0000-00-00') and not idfinger is null order by a.subbagian,a.kodegolongan,a.namakaryawan";
		//}	
##====================================================================================================
##====================================================================================================		
		# Options
		
		//$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in('2','3','4')";
		//$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00')";
		//$whereKary .= " and subbagian<>''";
		$whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
		switch($blokStatus) {
			case 'lc':
			$whereKeg = "(kelompok='TB' And pilih in ('3','6','8','10','11','13','14','15'))";
			break;
			case 'bibit':
			$whereKeg = "((kelompok='BBT' or kelompok='PN' or kelompok='MN') And pilih in ('3','6','8','10','11','13','14','15'))";
			break;
			case 'tbm':
			$whereKeg = "(kelompok='TBM' And pilih in ('3','6','8','10','11','13','14','15'))";
			break;
			case 'tm':
			$whereKeg = "(kelompok='TM' And pilih in ('3','6','8','10','11','13','14','15'))";
			break;
			default:
			break;
		}
		if($blokStatus=='bibit')
		   $whereOrg = " tipe='BIBITAN' and left(kodeorganisasi,4)='".$param['afdeling']."'";
		else    
			$whereOrg = "(tipe='BLOK' or tipe='AFDELING') and left(kodeorganisasi,4)='".$param['afdeling']."'";
		
			$qKary = "select a.karyawanid,a.namakaryawan,a.subbagian,a.kodegolongan,b.tipe from ".
			$dbname.".datakaryawan a left join ".$dbname.".sdm_5tipekaryawan b ".
			"on a.tipekaryawan=b.id where ".$whereKary;
	
		$resKary = fetchData($qKary);
		$optKary = array();
		foreach($resKary as $r) {
			$optKary[$r['karyawanid']] = $r['subbagian']."-".$r['kodegolongan']."-".$r['namakaryawan'];
			#$optKary[$r['karyawanid']] = $r['namakaryawan']." - ".$r['subbagian'].(".$r['tipe'].")";
		}
		//$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian',$whereKary,'5');
		//$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',$whereKeg,'6');
		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,kodekegiatan',$whereKeg,'6');
		$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
		$optAbs = makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan');
		#$optOrg = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas'],false,'kebun'); 
		$optBin = array('1'=>'Ya','0'=>'Tidak');
		
		#================ Prestasi Tab =============================
		# Get Data
		$where = "notransaksi='".$param['notransaksi']."'";
		$cols = "kodekegiatan,kodeorg,hasilkerja,jumlahhk";
		$query = selectQuery($dbname,'kebun_prestasi',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			#$dataShow[$key]['nik'] = $optKary[$row['nik']];
			$dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
			$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
			#$dataShow[$key]['pekerjaanpremi'] = $optBin[$row['pekerjaanpremi']];
		}
		
		$firstKeg = getFirstContent($optKeg);
		$satuan = explode('(',$firstKeg);
		$satuan = str_replace(')','',$satuan[1]);
		
		# Form
		$theForm2 = new uForm('prestasiForm','Form Prestasi',2);
		$theForm2->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','select','L',25,$optKeg);
		$theForm2->_elements[0]->_attr['onchange'] = 'getSatuan()';
		$theForm2->addEls('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',25,$optOrg);
		$theForm2->_elements[1]->_attr['onchange'] = 'changeOrg()';
		$theForm2->_elements[1]->_attr['title'] = 'Usahakan pilih Blok';
		$theForm2->addEls('hasilkerja',$_SESSION['lang']['hasilkerjajumlah'],'0','textnumwsatuan','R',10,array(),$satuan);
		$theForm2->addEls('jumlahhk',$_SESSION['lang']['jumlahhk'],'0','textnum','R',10);
		$theForm2->_elements[3]->_attr['onfocus'] =
			"document.getElementById('tmpValHk').value = this.value";
		$theForm2->_elements[3]->_attr['onkeyup'] = "totalVal();cekVal(this,'Pres','Hk')";
		//$theForm2->addEls('upahkerja',$_SESSION['lang']['upahkerja'],'0','textnum','R',10);
		//$theForm2->_elements[4]->_attr['disabled'] = 'disabled';
		//$theForm2->addEls('umr',$_SESSION['lang']['umr'],'0','textnum','R',10);
		//$theForm2->_elements[5]->_attr['disabled'] = 'disabled';
		//$theForm2->_elements[5]->_attr['onfocus'] =
		//	"document.getElementById('tmpValUmr').value = this.value";
		//$theForm2->_elements[5]->_attr['onkeyup'] = "totalVal();cekVal(this,'Pres','Umr')";
		//$theForm2->addEls('upahpremi',$_SESSION['lang']['upahpremi'],'0','textnum','R',10);
		//$theForm2->_elements[4]->_attr['disabled'] = 'disabled';
		//$theForm2->_elements[4]->_attr['onfocus'] =
		//	"document.getElementById('tmpValIns').value = this.value";
		//$theForm2->_elements[4]->_attr['onkeyup'] = "totalVal();cekVal(this,'Pres','Ins')";
		
		# Table
		$theTable2 = new uTable('prestasiTable','Tabel Prestasi',$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,array('notransaksi'));
		$formTab2->_addActions = array('material'=>array(
			'img'=>'detail1.png',
			'onclick'=>'showMaterial'
		));
		$formTab2->_target = "kebun_slave_operasional_prestasi";
		$formTab2->_befDelete = "totalVal";
		$formTab2->_afterDelete = "totalVal";
		########## UNTUK KUNCIAN HANYA BISA INPUT 1 SAJA ###########################
		########## KOMEN TAG UNTUK BUKA KUNCI ######################################
		#$formTab2->_onedata = true;
		########## AWAS JANGAN SALAH ###############################################
		########## JANGAN LUPA UNTUK KOMEN HIDE ACTION JUGA ########################
		if(!empty($data)) {
			##################################### Flag untuk hide action dan form ##
			//$formTab2->_noaction = true;
			$theBlok = $data[0]['kodeorg'];
		} else {
			$theBlok = "";
		}
		#$formTab2->setFreezeEls("##kodekegiatan##kodeorg");
		
		$contentFrame[0] = $formTab2->prep();
		
		#================ Absensi Tab =============================
		# Get Data
		$where = "notransaksi='".$param['notransaksi']."'";
		$cols = "nourut,nik,absensi,jhk,umr,insentif";
		$query = selectQuery($dbname,'kebun_kehadiran',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			$dataShow[$key]['nik'] = $optKary[$row['nik']];
			$dataShow[$key]['absensi'] = $optAbs[$row['absensi']];
			$dataShow[$key]['umr'] = $row['umr'];
		}
		
		#=============================== Get UMR ==============================
		/*$firstKary = getFirstKey($optKary);
		$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
			"karyawanid=".$firstKary." and idkomponen in (1,31)");
		$Umr = fetchData($qUMR);*/
		#=============================== Get UMR ==============================
		
		# Form
		$theForm1 = new uForm('absensiForm','Form Absensi',2);
		$theForm1->addEls('nourut',$_SESSION['lang']['nourut'],'0','textnum','R',3);
		$theForm1->_elements[0]->_attr['disabled'] = 'disabled';
			$theForm1->addEls('nik',$_SESSION['lang']['nik'],'','select','L',25,$optKary);
		$theForm1->_elements[1]->_attr['onchange'] = 'updateUMR(this)';
		$theForm1->addEls('absensi',$_SESSION['lang']['absensi'],'H','select','L',25,$optAbs);
		$theForm1->addEls('jhk',$_SESSION['lang']['jhk'],'0','textnum','R',10);
		$theForm1->_elements[3]->_attr['onblur'] = "cekVal(this,'Abs','Hk');";
		$theForm1->addEls('umr',$_SESSION['lang']['umrhari'],'0','textnum','R',10);
		#$theForm1->addEls('umr',$_SESSION['lang']['umrhari'],$Umr[0]['nilai']/25,'textnum','R',10);
		#$theForm1->_elements[4]->_attr['onkeyup'] = "totalVal();cekVal(this,'Abs','Umr')";
		$theForm1->_elements[4]->_attr['onkeyup'] = "totalVal();";
		$theForm1->addEls('insentif',$_SESSION['lang']['insentif'],'0','textnum','R',10);
		#$theForm1->_elements[5]->_attr['onkeyup'] = "totalVal();cekVal(this,'Abs','Ins')";
		$theForm1->_elements[5]->_attr['onkeyup'] = "totalVal();";
		
		# Table
		$theTable1 = new uTable('absensiTable','Tabel Absensi',$cols,$data,$dataShow);
		
		# FormTable
		$formTab1 = new uFormTable('ftAbsensi',$theForm1,$theTable1,null,array('notransaksi'));
		$formTab1->_target = "kebun_slave_operasional_absensi";
		$formTab1->_noEnable = '##nourut';
		$formTab1->_befDelete = "totalVal";
		$formTab1->_afterDelete = "totalVal";
		//$formTab1->_defValue = '##umr='.$Umr[0]['nilai']/25;
		
		$contentFrame[1] ="<input type=checkbox id=filternik onclick=filterKaryawan('nik',this) title='Saring karyawan'>Filter Karyawan</checkbox>";
			$contentFrame[1] .= $formTab1->prep();
		
		#================ Material Tab =============================
		# Get Data
		/*$where = "notransaksi='".$param['notransaksi']."'";
		$cols = "kodeorg,kodebarang,kwantitas,kwantitasha";
		$query = selectQuery($dbname,'kebun_pakaimaterial',$cols,$where);
		$data = fetchData($query);
		
		if(!empty($data)) {
			$whereBarang = "";
			$i=0;
			foreach($data as $row) {
			if($i==0) {
				$whereBarang .= "kodebarang='".$row['kodebarang']."'";
			} else {
				$whereBarang .= " or kodebarang='".$row['kodebarang']."'";
			}
			$i++;
			}
			$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whereBarang);
		} else {
			$optBarang = array();
		}
		
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
			$dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
			$dataShow[$key]['kwantitas'] = number_format($row['kwantitas'],2);
			$dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
		}
		
		# Form
		$theForm3 = new uForm('materialForm','Form Pakai Material',2);
		$theForm3->addEls('kodeorg',$_SESSION['lang']['kodeorg'],$theBlok,'select','L',25,$optOrg);
		//$theForm3->_elements[0]->_attr['disabled'] = 'disabled';
		$theForm3->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20);
		$theForm3->addEls('kwantitas',$_SESSION['lang']['kwantitas'],'0','textnum','R',10);
		$theForm3->addEls('kwantitasha',$_SESSION['lang']['kwantitasha'],'0','textnum','R',10);
		#$theForm3->addEls('hargasatuan',$_SESSION['lang']['hargasatuan'],'0','textnum','R',10);
		
		# Table
		$theTable3 = new uTable('materialTable','Tabel Pakai Material',$cols,$data,$dataShow);
		
		# FormTable
		$formTab3 = new uFormTable('ftMaterial',$theForm3,$theTable3,null,array('notransaksi'));
		$formTab3->_target = "kebun_slave_operasional_material";
		$formTab3->_noClearField = '##kodebarang';
		$formTab3->_noEnable = '##kodebarang##kodeorg';
		
		$contentFrame[2] = $formTab3->prep();
		*/
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		drawTab('FRM',$headFrame,$contentFrame,150,'100%');
		echo "</fieldset>";
		break;
    case 'updateUMR':
		$firstKary = $param['nik'];
		$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
			"karyawanid=".$firstKary." and idkomponen in (1,31)");
		$Umr = fetchData($qUMR);
		echo $Umr[0]['nilai']/25;
		break;
    case 'gatKarywanAFD':
        if($param['tipe']=='afdeling')
        {
            $subbagian=substr($param['kodeorg'],0,6);
            $str="select karyawanid,namakaryawan,subbagian,kodegolongan from ".$dbname.".datakaryawan where subbagian='".$subbagian."' 
                and tipekaryawan in (3,4) and kodejabatan in (250) and (tanggalkeluar >= CURDATE() or tanggalkeluar='0000-00-00') and not idfinger is null order by namakaryawan asc";
        }
        else
        {    
            $subbagian=substr($param['kodeorg'],0,4);
            $str="select karyawanid,namakaryawan,subbagian,kodegolongan from ".$dbname.".datakaryawan where lokasitugas='".$subbagian."' 
                and tipekaryawan in (3,4) and kodejabatan in (250) and (tanggalkeluar >= CURDATE() or tanggalkeluar='0000-00-00') and not idfinger is null order by subbagian,namakaryawan asc";
				
	
        }   
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
			//$optKary[$r['karyawanid']] = $r['subbagian']."-".$r['kodegolongan']."-".$r['namakaryawan'];
            echo"<option value='".$bar->karyawanid."'>".$bar->namakaryawan." - ".$bar->subbagian."</option>";
			//echo"<option value='".$bar->karyawanid."'>".$bar->subbagian." - ".$bar->kodegolongan." - ".$bar->namakaryawan."</option>";
        }
		break;
	case 'showMaterial':
		$param = $_POST;
		
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['kodekegiatan']."' and kodeorg='".$param['kodeorg']."'";
		$cols = "kodebarang,kwantitas,kwantitasha,hargasatuan";
		$query = selectQuery($dbname,'kebun_pakaimaterial',$cols,$where);
		$data = fetchData($query);
		
		if(!empty($data)) {
			$whereBarang = "";
			$i=0;
			foreach($data as $row) {
			if($i==0) {
				$whereBarang .= "kodebarang='".$row['kodebarang']."'";
			} else {
				$whereBarang .= " or kodebarang='".$row['kodebarang']."'";
			}
			$i++;
			}
			$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whereBarang);
		} else {
			$optBarang = array();
		}
		
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			//$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
			$dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
			$dataShow[$key]['kwantitas'] = number_format($row['kwantitas'],2);
			$dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
			$dataShow[$key]['hargasatuan'] = number_format($row['hargasatuan'],2);
		}
		
		# Form
		$theForm3 = new uForm('materialForm','Form Pakai Material',1);
		$theForm3->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20,null,null,'kwantitas_satuan','hargasatuan');
		$theForm3->addEls('kwantitas',$_SESSION['lang']['kwantitas'],'0','textnumwsatuan','R',10);
		$theForm3->addEls('kwantitasha',$_SESSION['lang']['kwantitasha'],$param['hasilkerja'],'textnum','R',10);
		$theForm3->addEls('hargasatuan','Harga Rata2',$param['hargasatuan'],'textnum','R',10);
		$theForm3->_elements[2]->_attr['disabled'] = 'disabled';
		$theForm3->_elements[3]->_attr['disabled'] = 'disabled';
		
		# Table
		$theTable3 = new uTable('materialTable','Tabel Pakai Material',$cols,$data,$dataShow);
		
		# FormTable
		$formTab3 = new uFormTable('ftMaterial',$theForm3,$theTable3,null,
			array('notransaksi','tanggal','ftPrestasi_kodekegiatan_'.$param['numRow'],
				'ftPrestasi_kodeorg_'.$param['numRow']));
		$formTab3->_target = "kebun_slave_operasional_material";
		$formTab3->_noClearField = '##kodebarang##kwantitasha##hargasatuan';
		$formTab3->_noEnable = '##kodebarang##kwantitasha##hargasatuan';
		$formTab3->_defValue = '##kwantitasha='.$param['hasilkerja'];
		
		$formTab3->render();
		break;
    default:
	break;
}
?>