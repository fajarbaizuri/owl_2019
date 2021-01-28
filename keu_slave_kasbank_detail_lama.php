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
		$whereAKB = "kodeaplikasi='GL' and aktif=1";
		$queryAKB = selectQuery($dbname,'keu_5parameterjurnal',
			'jurnalid,noakundebet,sampaidebet,noakunkredit,sampaikredit',$whereAKB);
		$optAKB = fetchData($queryAKB);
		$tipe = "";
		foreach($optAKB as $row) {
			if($param['tipetransaksi']=='K') {
			if($param['noakun']>=$row['noakunkredit'] and $param['noakun']<=$row['sampaikredit']) {
				$tipe = $row['jurnalid'];
			}
			} else {
			if($param['noakun']>=$row['noakundebet'] and $param['noakun']<=$row['sampaidebet']) {
				$tipe = $row['jurnalid'];
			}
			}
		}
		
		# Cek Kelompok Jurnal
		$whereKel = "kodeorg='".$_SESSION['org']['kodeorganisasi'].
			"' and kodekelompok='".$tipe."'";
		$optKel = makeOption($dbname,'keu_5kelompokjurnal','kodekelompok,keterangan',$whereKel);
		if(empty($optKel)) {
			echo "Warning : Belum ada Kelompok Jurnal ".$tipe." untuk PT anda\n";
			echo "Mohon hubungi pihak IT";
			exit;
		}
		
		# Options
		$whereJam=" detail=1 and noakun <> '".$param['noakun']."' and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
		$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
		$whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		$optAsset = makeOption($dbname,'sdm_daftarasset','kodeasset,namasset',$whereAsset,'2',true);
		$optMataUang = makeOption($dbname,'setup_matauang','kode,matauang');
		$optSupplier = makeOption($dbname,'log_5supplier','supplierid,namasupplier',null,'0',true);
		$optCustomer = makeOption($dbname,'pmn_4customer','kodecustomer,namacustomer',null,'0',true);
		$optKegiatan = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan,satuan,noakun',null,'6',true);
		$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary,'0',true);
		$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereJam,'2');
		$optVhc = makeOption($dbname,'vhc_5master','kodevhc,kodeorg','','2',true);
		$optOrgAl = getOrgBelow($dbname,$param['kodeorg'],false,'all',true);
		$optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay',
			"tipe='Detail' and namalaporan='CASH FLOW DIRECT'",'2');
		
		$wheredz= "kodeorganisasi !='".$_SESSION['empl']['lokasitugas']."' and length(kodeorganisasi)=4";
		$wheredzx="(noakun like '%211%' or noakun like '%212%') and length(noakun)=7 ";
		$optPemilikHutang=makeOption($dbname,'organisasi','kodeorganisasi,kodeorganisasi',$wheredz);
		$optNoakunHutang=makeOption($dbname,'keu_5akun','noakun,namaakun',$wheredzx);
		
		//tambahan		
		$optHutangUnit=array('0'=>'Tidak','1'=>'Ya');		
			
		if($param['tipetransaksi']=='K') {
			$invTab = 'keu_tagihanht';
		} else {
			$invTab = 'keu_penagihanht';
		}
		$optInvoice = makeOption($dbname,$invTab,'noinvoice,noinvoice',
			"kodeorg='".$_SESSION['org']['kodeorganisasi']."'",'0',true);
		
		# Field Aktif
		$optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
			"noakun='".end(array_reverse(array_keys($optAkun)))."'");
		$fieldAktif = $optField[end(array_reverse(array_keys($optAkun)))];
		
		# Get Data
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and noakun2a='".$param['noakun']."'";
		$cols = "kode,keterangan1,noakun,noaruskas,matauang,kurs,keterangan2,jumlah,".
			"kodekegiatan,kodeasset,kodebarang,nik,kodecustomer,kodesupplier,kodevhc,orgalokasi,nodok,hutangunit1,pemilikhutang,noakunhutang";
			
			
			
			
		$query = selectQuery($dbname,'keu_kasbankdt',$cols,$where);
		$data = fetchData($query);
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
			$dataShow[$key]['kode'] = $optKel[$row['kode']];
			$dataShow[$key]['nik'] = $optKary[$row['nik']];
			$dataShow[$key]['noaruskas'] = $optCashFlow[$row['noaruskas']];
			$dataShow[$key]['kodekegiatan'] = $optKegiatan[$row['kodekegiatan']];
			$dataShow[$key]['kodecustomer'] = $optCustomer[$row['kodecustomer']];
			$dataShow[$key]['kodesupplier'] = $optSupplier[$row['kodesupplier']];
			$dataShow[$key]['kodevhc'] = $optVhc[$row['kodevhc']];
			$dataShow[$key]['matauang'] = $optMataUang[$row['matauang']];
			$dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
			$dataShow[$key]['orgalokasi'] = $optOrgAl[$row['orgalokasi']];
			$dataShow[$key]['hutangunit1'] = $optHutangUnit[$row['hutangunit1']];
		}
		
		# Form
		$theForm2 = new uForm('kasbankForm','Form Kas Bank',2);
		$theForm2->addEls('kode',$_SESSION['lang']['kode'],'','select','L',25,$optKel);
		$theForm2->addEls('keterangan1',$_SESSION['lang']['noinvoice'],'','text','L',25);
		$theForm2->_elements[1]->_attr['onclick'] = "searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']."','<div id=formPencariandata></div>',event)";
		$theForm2->addEls('noakun',$_SESSION['lang']['noakun'],'','select','L',25,$optAkun);
		$theForm2->_elements[2]->_attr['onchange'] = 'updFieldAktif()';
		$theForm2->addEls('noaruskas',$_SESSION['lang']['noaruskas'],'','select','L',25,$optCashFlow);
		$theForm2->addEls('matauang',$_SESSION['lang']['matauang'],'IDR','select','L',25,$optMataUang);
		$theForm2->addEls('kurs',$_SESSION['lang']['kurs'],'1','textnum','L',10);
		$theForm2->addEls('keterangan2',$_SESSION['lang']['keterangan2'],'','text','L',40);
		$theForm2->addEls('jumlah',$_SESSION['lang']['jumlah'],'0','textnum','R',10);
		$theForm2->_elements[7]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
		$theForm2->addEls('kodekegiatan',$_SESSION['lang']['kodekegiatan'],'','select','L',35,$optKegiatan);
		if($fieldAktif[0]=='0') {
			$theForm2->_elements[8]->_attr['disabled'] = 'disabled';
		}
			
		$theForm2->addEls('kodeasset',$_SESSION['lang']['kodeasset'],'','select','L',35,$optAsset);
		if($fieldAktif[1]=='0') {
			$theForm2->_elements[9]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',10);
		if($fieldAktif[2]=='0') {
			$theForm2->_elements[10]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('nik',$_SESSION['lang']['nik'],'','select','L',35,$optKary);
		if($fieldAktif[3]=='0') {
			$theForm2->_elements[11]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodecustomer',$_SESSION['lang']['kodecustomer'],'','select','L',35,$optCustomer);
		if($fieldAktif[4]=='0') {
			$theForm2->_elements[12]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodesupplier',$_SESSION['lang']['kodesupplier'],'','select','L',35,$optSupplier);
		if($fieldAktif[5]=='0') {
			$theForm2->_elements[13]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('kodevhc',$_SESSION['lang']['kodevhc'],'','select','L',35,$optVhc);
		if($fieldAktif[6]=='0') {
			$theForm2->_elements[14]->_attr['disabled'] = 'disabled';
		}
		$theForm2->addEls('orgalokasi',$_SESSION['lang']['kodeorg'],'','select','L',35,$optOrgAl);
		$theForm2->addEls('nodok',$_SESSION['lang']['nodok'],'','text','L',35);
		$theForm2->addEls('hutangunit1',$_SESSION['lang']['hutangunit'],'','select','L',25,$optHutangUnit);
		$theForm2->addEls('pemilikhutang',$_SESSION['lang']['pemilikhutang'],'','select','L',25,$optPemilikHutang);
		$theForm2->addEls('noakunhutang',$_SESSION['lang']['noakunhutang'],'','select','L',25,$optNoakunHutang);
		
		# Table
		$theTable2 = new uTable('kasbankTable','Tabel Kas Bank',$cols,$data,$dataShow);
		
		# FormTable
		$formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,
			array('notransaksi','kodeorg','noakun2a','tipetransaksi'));
		$formTab2->_target = "keu_slave_kasbank_detail";
		$formTab2->_defValue = '##matauang=IDR';
		$formTab2->_numberFormat = '##jumlah';
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		$formTab2->render();
		echo "</fieldset>";
		break;
    case 'add':
		$cols = array(
			'kode','keterangan1','noakun','noaruskas','matauang','kurs','keterangan2',
			'jumlah','kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
			'kodesupplier','kodevhc','orgalokasi','nodok','hutangunit1','pemilikhutang',
			'noakunhutang','notransaksi','kodeorg','noakun2a','tipetransaksi'
		);
		$data = $param;
		unset($data['numRow']);
		
		# Additional Default Data
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
		
		$query = insertQuery($dbname,'keu_kasbankdt',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['notransaksi']);unset($data['kodeorg']);
		unset($data['noakun2a']);unset($data['tipetransaksi']);
		
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
		foreach($data as $key=>$cont) {
			if(substr($key,0,5)=='cond_') {
			unset($data[$key]);
			}
		}
		$data['jumlah'] = str_replace(',','',$data['jumlah']);
		
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun2a='".$param['noakun2a'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and noakun='".$param['cond_noakun'].
			"' and keterangan2='".$param['cond_keterangan2']."'";
		$query = updateQuery($dbname,'keu_kasbankdt',$data,$where);
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi'].
			"' and kodeorg='".$param['kodeorg'].
			"' and noakun='".$param['noakun'].
			"' and noakun2a='".$param['noakun2a'].
			"' and tipetransaksi='".$param['tipetransaksi'].
			"' and keterangan1='".$param['keterangan1']."'
				 and keterangan2='".$param['keterangan2']."'";
		$query = "delete from `".$dbname."`.`keu_kasbankdt` where ".$where;
			if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    case 'updField':
		$optField = makeOption($dbname,'keu_5akun','noakun,fieldaktif',
			"noakun='".$param['noakun']."'");
		echo $optField[$param['noakun']];
		break;
    case'getForminvoice':
        $optSupplierCr="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sSuplier="select distinct supplierid,namasupplier,substr(kodekelompok,1,1) as status from ".$dbname.".log_5supplier order by namasupplier asc";
        $qSupplier=mysql_query($sSuplier) or die(mysql_error($sSupplier));
        while($rSupplier=mysql_fetch_assoc($qSupplier))
        {
            $optSupplierCr.="<option value='".$rSupplier['supplierid']."'>".$rSupplier['namasupplier']." [".$rSupplier['status']."]</option>";
        }
        $form="<fieldset style=float: left;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['noinvoice']."</legend>".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg>&nbsp;".$_SESSION['lang']['namasupplier']."<select id=supplierIdcr style=width:150px>".$optSupplierCr."</select><button class=mybutton onclick=findNoinvoice()>Find</button></fieldset><div id=container2><fieldset><legend>".$_SESSION['lang']['result']."</legend></fieldset></div>";
        echo $form;
		break;
    case'getInvoice':
        $optNmsupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
        $arrTipe=array("p"=>"Pembelian","k"=>"Kontrak");
        $dat.="<fieldset><legend>".$_SESSION['lang']['result']."</legend>";
        $dat.="<div style=overflow:auto;width:100%;height:500px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $dat.="<td>".$_SESSION['lang']['noinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nopo']."</td>";
        $dat.="<td>".$_SESSION['lang']['namasupplier']."</td>";
        $dat.="<td>".$_SESSION['lang']['tipeinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nilaiinvoice']."</td>";
        $dat.="<td>".$_SESSION['lang']['nilaippn']."</td>";
        $dat.="<td>".$_SESSION['lang']['noakun']."</td>";
        $dat.="</tr></thead><tbody>";
        if($param['txtfind']!='')
        {
            $whereCr=" and noinvoice like '%".$param['txtfind']."%'";
        }
        else
        {
//            $whereCr=" and noinvoice in (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar is null or dibayar=0) and kodeorg='".$_SESSION['org']['kodeorganisasi']."')";
            $whereCr=" and noinvoice in (select distinct noinvoice from ".$dbname.".aging_sch_vw where (dibayar is null or dibayar=0) and kodeorg like '%%')";
        } 
        if($param['idSupplier']!='')
        {
            $whereCr.=" and kodesupplier='".$param['idSupplier']."'";
        }
//        $sPo="select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,noakun,keterangan from ".$dbname.".keu_tagihanht where kodeorg='".$_SESSION['org']['kodeorganisasi']."' ".$whereCr." order by tanggal asc";
        $sPo="select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,noakun,keterangan from ".$dbname.".keu_tagihanht where kodeorg like '%%' ".$whereCr." order by tanggal asc";
        //echo $sPo;
        $qPo=mysql_query($sPo) or die(mysql_error($conn));
        while($rPo=mysql_fetch_assoc($qPo))
        {
            $no+=1;
            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rPo['nilaiinvoice']."','".$rPo['noakun']."','".$rPo['keterangan']."','".$rPo['kodesupplier']."','".$rPo['nopo']."')\" style='pointer:cursor;'><td>".$no."</td>";
            $dat.="<td>".$rPo['noinvoice']."</td>";
            $dat.="<td>".$rPo['nopo']."</td>";
            $dat.="<td>".$optNmsupp[$rPo['kodesupplier']]."</td>";
            $dat.="<td>".$arrTipe[$rPo['tipeinvoice']]."</td>";
            $dat.="<td>".number_format($rPo['nilaiinvoice'],2)."</td>";
            $dat.="<td>".$rPo['nilaippn']."</td>";
            $dat.="<td>".$rPo['noakun']."</td></tr>";
        }
        $dat.="</tbody></table></div>#Status S atau K mewakili S=Supplier,K=Kontraktor</fieldset>";
        echo $dat;
		break;
    default:
	break;
}
?>