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
		#== Prep Tab
		$headFrame = array(
			$_SESSION['lang']['prestasi'],
			$_SESSION['lang']['absensi']
		);
		$contentFrame = array();
		
		#================ Tab 0 ===============================================
		if ($param['kelompok']=='AB'){
			$optWaktu=array('JAM'=>'Jam','HM'=>'Jam Operasi Alat(HM)');
		}else{
			$optWaktu=array('JAM'=>'Jam','KMH'=>'Waktu Tempuh(KMH)');
		}
		array_unshift($optWaktu, 'Pilih Data');
		
		
		$whereOrgA = "notransaksi='".$param['notransaksi']."' ";
		$optOrgA = makeOption($dbname,'vhc_kendaraan_vw','notransaksi,kodeorg',$whereOrgA);
		$vhcB= $optOrgA[$param['notransaksi']];
		
		if ($vhcB == "TKFB"){
				$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') ";
		}else{
				$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and kodeorganisasi like '".$vhcB."%'";
		}
		
		$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
		array_unshift($optOrg, 'Pilih Lokasi');
		$whereVhc = "notransaksi='".trim($param['notransaksi'])."' ";
		$optVhc = makeOption($dbname,'vhc_kendaraan_vw','notransaksi,kelompokvhc',$whereVhc);
		$vhcA= $optVhc[$param['notransaksi']];
		//$whereKeg = "kelompok='".$vhcA."' ";
		
		$queryKaryA = "SELECT distinct kegiatan,nmkeg,satuankeg from `".$dbname."`.vhc_kendaraan_premi_vw WHERE kodeorg LIKE '".$_SESSION['empl']['lokasitugas']."%' AND (kendaraan LIKE '".$param['plat']."' OR kendaraan LIKE '".substr($param['plat'],0,4)."' OR kendaraan LIKE '".substr($param['plat'],0,2)."') order by nmkeg asc";
		$query = fetchData($queryKaryA);
		$optKeg = array('0'=>'Pilih Kegiatan');
		foreach($query as $row){
			$optKeg[$row['kegiatan']] = $row['nmkeg'].' ['.$row['satuankeg'].']';
		}
		
		
	
			// Get Data
		$qDetailPres = "SELECT * FROM ".$dbname.".vhc_kendaraan_kegiatan WHERE notransaksi='".
			$param['notransaksi']."' ";
		$resDetailPres = fetchData($qDetailPres);
		
		$taba = "<fieldset><legend style='font-weight:bold'>Aksi</legend>";
		if (trim($param['kondisi'])=='B'){
			$taba .= makeElement('btnTambahPres','btn','Tambah Baris',array('onclick'=>'addBarisPres()'));
			//$taba .= makeElement('btnSavePres','btn','Simpan Semua',array('onclick'=>'saveListPres()'));
			//$taba .= makeElement('btnClearPres','btn','Hapus Semua',array('onclick'=>'delPres()'));
		}else{
			$taba .= makeElement('btnTambahPres','btn','Tambah Baris',array('onclick'=>'addBarisPres()','disabled'=>'disabled'));
			//$taba .= makeElement('btnSavePres','btn','Simpan Semua',array('onclick'=>'saveListPres()','disabled'=>'disabled'));
			//$taba .= makeElement('btnClearPres','btn','Hapus Semua',array('onclick'=>'delPres()','disabled'=>'disabled'));
		}
		
		$taba .= "</fieldset>";
		$taba .= "<fieldset><legend style='font-weight:bold'>Table</legend>";
	
		
		
		$taba .= "<table class=data border=1 cellspacing=0><thead><tr class=rowheader>";
		$taba .= "<td>Waktu Operasional</td>";
		$taba .= "<td>Awal</td>";
		$taba .= "<td>Akhir</td>";
		$taba .= "<td>Total</td>";
		$taba .= "<td>Konversi</td>";
		$taba .= "<td>Satuan</td>";
		
		
		$taba .= "<td>Kegiatan</td>";
		$taba .= "<td>Lokasi</td>";
		$taba .= "<td>Trep Ke-</td>";
		$taba .= "<td>Volume</td>";
		$taba .= "<td>Satuan</td>";
		$taba .= "<td>Aksi</td>";	
		$taba .= "</tr></thead><tbody id='bodyDetailPres'>";
		foreach($resDetailPres as $key=>$row){
			$taba .= "<tr id='detailpres_".$key."' class=rowcontent>";
			$taba .= "<td>".makeElement('satk_'.$key,'select',$row['satuk'],
				array('disabled'=>'disabled','style'=>'width:150px','onchange'=>'getSatWaktu('.$key.',this.value)'),$optWaktu)."</td>";
			$taba .= "<td>".makeElement('awal_'.$key,'textnumeric',number_format($row['awal'],2),
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$taba .= "<td>".makeElement('akhir_'.$key,'textnumeric',number_format($row['akhir'],2),
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$taba .= "<td>".makeElement('total_'.$key,'textnumeric',number_format($row['total'],2),
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$taba .= "<td>".makeElement('konversi_'.$key,'textnumeric',number_format($row['convwaktu'],2),
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$taba .= "<td>".makeElement('satuan_'.$key,'text',$row['satuk'],
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$taba .= "<td>".makeElement('kegiatan_'.$key,'select',$row['kodekegiatan'],
				array('disabled'=>'disabled','style'=>'width:230px','onchange'=>'getSatuan('.$key.',this)'),$optKeg)."</td>";	
			$taba .= "<td>".makeElement('lokasi_'.$key,'select',$row['lokasi'],
				array('disabled'=>'disabled','style'=>'width:200px'),$optOrg)."</td>";
		
			
			$taba .= "<td>".makeElement('rit_'.$key,'textnumeric',$row['rit'],
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$taba .= "<td>".makeElement('volume_'.$key,'textnumeric',number_format($row['volume'],2),
				array('disabled'=>'disabled','style'=>'width:50px;'))."</td>";
			$taba .= "<td>".makeElement('satuanvolume_'.$key,'text',$row['satuan'],
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";			
			$taba .= "<td>";	
		    $taba .= makeElement('btnSavePres_'.$key,'btn','Simpan',array('onclick'=>'saveListPres('.$key.')'));
			$taba .= makeElement('btnClearPres_'.$key,'btn','Hapus',array('onclick'=>'delPres('.$key.')'));
			$taba .= "</td>";
			$taba .= "</tr>";
		}
		$taba .= "</tbody></table></fieldset>";
		$contentFrame[0] = $taba;
		# Draw Tab
		#================ Tab 1 ===============================================
		$optKar = array('0'=>'Pilih Karyawan');
		$optKar = makeOption($dbname,'vhc_5operator_vw','karyawanid,nama',
			"(lokasitugas='".$param['pemilik']."' or karyawanid like 'L".$_SESSION['empl']['lokasitugas']."%') and aktif=1 and vhc='".$param['plat']."'");
			array_unshift($optKar, 'Pilih Karyawan');
			
		// Get Data
		$qDetail = "SELECT * FROM ".$dbname.".vhc_kendaraan_tenaga WHERE notransaksi='".
			$param['notransaksi']."' ";
		$resDetail = fetchData($qDetail);
		
		
		
		
		$tab = "<fieldset><legend style='font-weight:bold'>Aksi</legend>";
		$tab .= makeElement('btnTambah','btn','Tambah Baris',array('onclick'=>'addBaris()'));
		$tab .= makeElement('btnHitung','btn','Hitung Premi',array('onclick'=>'hitungPremi()'));
		$tab .= makeElement('btnSave','btn','Simpan Semua',array('onclick'=>'saveList()','disabled'=>'disabled'));
		$tab .= makeElement('btnClear','btn','Hapus Semua',array('onclick'=>'delPerKary()'));
		$tab .= "</fieldset>";
		$tab .= "<fieldset><legend style='font-weight:bold'>Table</legend>";

		
	
		$tab .= "<table class=data border=1 cellspacing=0><thead><tr class=rowheader>";
		$tab .= "<td>".$_SESSION['lang']['karyawan']."</td>";
		$tab .= "<td>".$_SESSION['lang']['upahkerja']."</td>";
		$tab .= "<td>Premi</td>";
		$tab .= "<td>".$_SESSION['lang']['uangmakan']."</td>";
		/*
		$tab .= "<td>".$_SESSION['lang']['action']."</td>";
		*/
		
		
		$tab .= "</tr></thead><tbody id='bodyDetail'>";
		foreach($resDetail as $key=>$row){
			$tab .= "<tr id='detail_".$key."' class=rowcontent>";
			$tab .= "<td>".makeElement('karyawan_'.$key,'select',$row['idkaryawan'],
				array('disabled'=>'disabled','style'=>'width:200px','onchange'=>'getUpah('.$key.')','onclick'=>'getUpah('.$key.')'),$optKar)."</td>";
			$tab .= "<td>".makeElement('upah_'.$key,'textnum',number_format($row['upah']),
				array('disabled'=>'disabled','style'=>'width:100px'))."</td>";
			$tab .= "<td>".makeElement('premi_'.$key,'textnum',number_format($row['premi']),
				array('disabled'=>'disabled','style'=>'width:100px'))."</td>";
			$tab .= "<td>".makeElement('umkn_'.$key,'textnum',number_format($row['umkn']),
				array('disabled'=>'disabled','style'=>'width:100px'))."</td>";
				/*
			$tab .= "<td><img src='images/delete.png' style='width:15px;cursor:pointer' ".
				"onclick='delPerKary(".$key.")' title='Delete'></td>";
				*/
			$tab .= "</tr>";
		}
		$tab .= "</tbody></table></fieldset>";
		
		$contentFrame[1] = $tab;
		#================ Tab 2 ===============================================
		
		
		echo "<fieldset><legend><b>Detail</b></legend>";
		drawTab('FRM',$headFrame,$contentFrame,150,'100%');
		echo "</fieldset>";
		break;
	
    case 'CheckCountRit':
		$queryKaryA = "select notransaksi from `".$dbname."`.`vhc_kendaraan_kegiatan` where notransaksi='".$param['b']."' and kodekegiatan like '".$param['a']."' ";
		$query = mysql_query($queryKaryA);
		echo mysql_num_rows($query)+1;
		break;
	case 'CheckKonversi':
	    if ($param['kelompok']=='AB'){
			$kdalat='1';
		}else{
			$kdalat='2';
		}
        $queryKaryA = "select norma,satuan from `".$dbname."`.`vhc_kendaraan_norma` where 
		kategori like '".$kdalat."' and kegiatan like '".$param['keg']."' AND 
		(kendaraan like '".$param['kelompok']."' or kendaraan like '".substr($param['plat'],0,4)."' OR kendaraan like '".$param['plat']."')
		and (kodeorg like '".$_SESSION['empl']['lokasitugas']."' or kodeorg like '".substr($param['lokasi'],0,6)."' or kodeorg like '".$param['lokasi']."') limit 1";
		/*
		$queryKaryA = "select norma,satuan from `".$dbname."`.`vhc_kendaraan_norma` where 
		kodeorg='".$param['pemilik']."' and kategori like '".$param['kelompok']."' and kegiatan like '".$param['keg']."' and kendaraan like '".$param['plat']."' and (kodeorg like '".substr($param['lokasi'],0,4)."' or kodeorg like '".substr($param['lokasi'],0,6)."' or kodeorg like '".$param['lokasi']."') ";
		*/
		$query = mysql_query($queryKaryA);
		if (mysql_num_rows($query)==0){
			$hasil= $param['total'];
		}else{
			$hasil=0;
			while($bar3=mysql_fetch_object($query))
			{
				$hasil=$bar3->norma;
			}
			if ($hasil==0){
				$hasil= $param['total'];
			}else{
				$hasil= $hasil;
			}
			//$hasil=$hasil/$param['volume'];
			//echo number_format($hasil/$param['volume'],2);
			$hasil= number_format($param['volume']/$hasil,2);
			if($hasil>=$param['total']){
				$hasil= $param['total'];
			}else{
				$hasil= $hasil;
			}
			
		}
		echo $hasil;
		break;
    case 'gatKarywanAFD':
        if($param['tipe']=='afdeling')
        {
            $subbagian=substr($param['kodeorg'],0,6);
            $str="select karyawanid,namakaryawan,subbagian from ".$dbname.".datakaryawan where subbagian='".$subbagian."' 
                and tipekaryawan in('2','3','4') order by namakaryawan";
        }
        else
        {    
            $subbagian=substr($param['kodeorg'],0,4);
            $str="select karyawanid,namakaryawan,subbagian from ".$dbname.".datakaryawan where lokasitugas='".$subbagian."' 
                and tipekaryawan in('2','3','4') order by namakaryawan";
        }   
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            echo"<option value='".$bar->karyawanid."'>".$bar->namakaryawan." - ".$bar->subbagian."</option>";
        }
		break;
	case 'showMaterial':
		$param = $_POST;
		
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['kodekegiatan']."' and kodeorg='".$param['kodeorg']."'";
		$cols = "kodebarang,kwantitas,kwantitasha";
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
		}
		
		# Form
		$theForm3 = new uForm('materialForm','Form Pakai Material',1);
		$theForm3->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20,null,null,'kwantitas_satuan');
		$theForm3->addEls('kwantitas',$_SESSION['lang']['kwantitas'],'0','textnumwsatuan','R',10);
		$theForm3->addEls('kwantitasha',$_SESSION['lang']['kwantitasha'],$param['hasilkerja'],'textnum','R',10);
		$theForm3->_elements[2]->_attr['disabled'] = 'disabled';
		
		# Table
		$theTable3 = new uTable('materialTable','Tabel Pakai Material',$cols,$data,$dataShow);
		
		# FormTable
		$formTab3 = new uFormTable('ftMaterial',$theForm3,$theTable3,null,
			array('notransaksi','tanggal','ftPrestasi_kodekegiatan_'.$param['numRow'],
				'ftPrestasi_kodeorg_'.$param['numRow']));
		$formTab3->_target = "kebun_slave_operasional_material";
		$formTab3->_noClearField = '##kodebarang##kwantitasha';
		$formTab3->_noEnable = '##kodebarang##kwantitasha';
		$formTab3->_defValue = '##kwantitasha='.$param['hasilkerja'];
		
		$formTab3->render();
		break;
	case 'newRowPres':
	

		
	    
	
		$key = $param['numRow'];
		
		if ($param['kelompok']=='AB'){
			$optWaktu=array('JAM'=>'Jam','HM'=>'Jam Operasi Alat(HM)');
		
		}else{
			$optWaktu=array('JAM'=>'Jam','KMH'=>'Waktu Tempuh(KMH)');
			
		}
		array_unshift($optWaktu, 'Pilih Data');
		/*
		$whereOrgA = "kodevhc='".$param['plat']."' ";
		$optWaktu = makeOption($dbname,'vhc_5master','kodevhc,satuk',$whereOrgA);
		$hasd= $optWaktu[$param['plat']];
		if ($hasd=='HM'){
			$optWaktu=array('HM'=>'Jam Operasi Alat(HM)');
		}else if ($hasd=='JAM'){
			$optWaktu=array('JAM'=>'Jam');
		}else if ($hasd=='KMH'){
			$optWaktu=array('KMH'=>'Waktu Tempuh(KMH)');
		}
		array_unshift($optWaktu, 'Pilih Data');
		
		/*
		$whereOrgA = "notransaksi='".$param['notransaksi']."' ";
		$optOrgA = makeOption($dbname,'vhc_kendaraan_vw','notransaksi,kodeorg',$whereOrgA);
		$vhcB= $optOrgA[$param['notransaksi']];
		
		if ($vhcB == "TKFB"){
				$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') ";
		}else{
				$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and kodeorganisasi like '".$vhcB."%'";
		}
		
		$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
		array_unshift($optOrg, 'Pilih Lokasi');
		*/
		$optOrg = array('0'=>'Pilih Lokasi');
		
		

		
		
		$queryKaryA = "SELECT distinct kegiatan,concat(kegiatan,': ',nmkeg) as nmkeg,satuankeg from `".$dbname."`.vhc_kendaraan_premi_vw WHERE kodeorg LIKE '".$_SESSION['empl']['lokasitugas']."%' AND (kendaraan LIKE '".$param['plat']."' OR kendaraan LIKE '".substr($param['plat'],0,4)."' OR kendaraan LIKE '".substr($param['plat'],0,2)."') order by kegiatan asc";
			$query = fetchData($queryKaryA);
			$optKeg = array('0'=>'Pilih Kegiatan');
		foreach($query as $row){
			$optKeg[$row['kegiatan']] = $row['nmkeg'].' ['.$row['satuankeg'].']';
		}
	

		
		
			$tab = "";
			$tab .= "<td>".makeElement('satk_'.$key,'select','0',
			
				array('style'=>'width:150px','onchange'=>'getSatWaktu('.$key.',this.value)'),$optWaktu)."</td>";
			
				
			$tab .= "<td>".makeElement('awal_'.$key,'textnumeric','0',
				array('style'=>'width:50px'))."</td>";
			$tab .= "<td>".makeElement('akhir_'.$key,'textnumeric','0',
				array('style'=>'width:50px'))."</td>";
			$tab .= "<td>".makeElement('total_'.$key,'textnumeric','0',
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$tab .= "<td>".makeElement('konversi_'.$key,'textnumeric','0',
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$tab .= "<td>".makeElement('satuan_'.$key,'text','',
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$tab .= "<td>".makeElement('kegiatan_'.$key,'select','0',
				array('style'=>'width:230px','onchange'=>'getSatuan('.$key.',this)'),$optKeg)."</td>";	
			$tab .= "<td>".makeElement('lokasi_'.$key,'select','0',
				array('style'=>'width:200px','onchange'=>'getKonversi('.$key.')'),$optOrg)."</td>";
			
			$tab .= "<td>".makeElement('rit_'.$key,'textnumeric','0',
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$tab .= "<td>".makeElement('volume_'.$key,'textnumeric','0',
				array('style'=>'width:50px;','onchange'=>'getKonversi('.$key.')','onkeyup'=>'getKonversi('.$key.')'))."</td>";
			$tab .= "<td>".makeElement('satuanvolume_'.$key,'text','',
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$tab .= "<td>";	
			$tab .= makeElement('btnSavePres_'.$key,'btn','Simpan',array('onclick'=>'saveListPres('.$key.')'));
			$tab .= makeElement('btnClearPres_'.$key,'btn','Hapus',array('onclick'=>'delPres('.$key.')'));
			$tab .= "</td>";	
	
		echo $tab; 	 
		break;
	case 'delKary':
		$where = "notransaksi='".$param['notransaksi']."' ";
		$query = "delete from `".$dbname."`.`vhc_kendaraan_tenaga` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;		
	case 'delPres':
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".$param['kegiatan']."' and rit='".$param['rit']."' ";
		$query = "delete from `".$dbname."`.`vhc_kendaraan_kegiatan` where ".$where;
		
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		break;		
	case 'newRow':
		$key = $param['numRow'];
		// Options
		
		$optOrg = array('0'=>'Pilih Karyawan');
		$optOrg = makeOption($dbname,'vhc_5operator_vw','karyawanid,nama',
			"(lokasitugas='".$param['pemilik']."' or karyawanid like 'L".$param['pemilik']."%') and aktif=1 and vhc='".$param['plat']."'");
			array_unshift($optOrg, 'Pilih Karyawan');
		
			
	
			
		$tab = "";
		$tab .= "<td>".makeElement('karyawan_'.$key,'select','0',
				array('style'=>'width:200px','onchange'=>'getUpah('.$key.')'),$optOrg)."</td>";
		$tab .= "<td>".makeElement('upah_'.$key,'textnum','0',
				array('disabled'=>'disabled','style'=>'width:100px'))."</td>";
		$tab .= "<td>".makeElement('premi_'.$key,'textnum','0',
				array('disabled'=>'disabled','style'=>'width:100px'))."</td>";
		$tab .= "<td>".makeElement('umkn_'.$key,'textnum','0',
				array('disabled'=>'disabled','style'=>'width:100px'))."</td>";
		echo $tab; 	 
		break;
	case 'delKary':
		$where = "notransaksi='".$param['notransaksi']."' ";
		$query = "delete from `".$dbname."`.`vhc_kendaraan_tenaga` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;	
	
	case 'getUpah':
		
		
	
		// Cari Status Karyawan
    	$query1 = selectQuery($dbname,'datakaryawan',' kodegolongan',"karyawanid='".$param['karyawan']."'");
		$res1 = fetchData($query1);
		if(!empty($res1)) {
			$sts = $res1[0]['kodegolongan'];
		} else {
			$sts = '0';
		}
		
		if ($sts=="BHL"){
		// Cari Status Karyawan
    	$query3 = selectQuery($dbname,'sdm_5golongan',' upah',
			"kodegolongan='".$sts."'");
		$res3 = fetchData($query3);
		if(!empty($res3)) {
			$uph = $res3[0]['upah'];
		} else {
			$uph = '0';
		}
		}else{
		// Cari gajipokok
    	$query3 = selectQuery($dbname,'sdm_5gajipokok',' jumlah',
			"karyawanid='".$param['karyawan']."' and idkomponen='1' and tahun='".substr($param['notransaksi'],0,4)."'");
		$res3 = fetchData($query3);
		if(!empty($res3)) {
			$uph = $res3[0]['jumlah']/25;
		} else {
			$uph = '0';
		}
		}
		
		
		// Get Uang Makan
		//$query2 = selectQuery($dbname,'kebun_5bjr','bjr',
		//	"kodeorg='".substr($param['kodeorg'],0,6)."' and thntanam=".$tt.
		//	" and tahunproduksi=".$tgl[2]);
		$query2 = selectQuery($dbname,'vhc_5operator',' um',"karyawanid='".$param['karyawan']."'");
		$res2 = fetchData($query2);
		if(empty($res2)) {
			$umkn=0;
		} else {
			$umkn=$res2[0]['um'];
		}
		// 
		
		
		// Prep Output
		$res = array(
			'upah'=>number_format($uph),
			'umkn'=>number_format($umkn)
		);
		echo json_encode($res);
		break;
	case 'hitungPremi':
		
		$res = array();
		
		//cari basis jam
		$daftar_hari = array(
			'Sunday' => '0',
			'Monday' => '6',
			'Tuesday' => '6',
			'Wednesday' => '6',
			'Thursday' => '6',
			'Friday' => '6',
			'Saturday' => '4'
		);

			$pecah = explode("-", $param['tanggal']);
			$date=$pecah[2]."/".$pecah[1]."/".$pecah[0];
			$namahari = date('l', strtotime($date));
		if ($param['libur']==1){
			$basisjam= 0;
		}else{
			$basisjam= $daftar_hari[$namahari];
		}
		//cek apakah dia kernet atau operator
		$Whoiam=array();
		
		$whereKary = "and lokasitugas='".$param['pemilik']."' and vhc ='".$param['plat']."'  ";
		$str3 = "SELECT karyawanid FROM ".$dbname.".vhc_5operator_vw where status='1' ".$whereKary;
		$res3=mysql_query($str3);
		if (mysql_num_rows($res3)>0){
			while($bar3=mysql_fetch_object($res3))
			{
				$Whoiam[$bar3->karyawanid]=0;
			}
		}else{
			$Whoiam[0]=0;
		}
				
		$str4 = "SELECT karyawanid FROM ".$dbname.".vhc_5operator_vw where status='2' ".$whereKary;
		$res4=mysql_query($str4);
		if (mysql_num_rows($res4)>0){
			while($bar4=mysql_fetch_object($res4))
			{
				$Whoiam[$bar4->karyawanid]=1;
			}
		}else{
			$Whoiam[0]=0;
		}
				
		//--Export Data------------------------------------------------------
		$ConvData=array();
		$ConvDataA=array();
		for($x=0;$x<$param['maxRow'];$x++) {
			$ConvDataA[$x]['karyawan']=$param['karyawan'.$x];
		}
			$TotalKes=0;
			$VolumeKes=0;
			$TotalTrip=0;
		for($x=0;$x<$param['maxRowPres'];$x++) {
			//$ConvData[$x]['total']=$param['total'.$x];
			$ConvData[$x]['konversi']=$param['konversi'.$x];
			$ConvData[$x]['lokasi']=$param['lokasi'.$x];
			$ConvData[$x]['kegiatan']=$param['kegiatan'.$x];
			$ConvData[$x]['rit']=$param['rit'.$x];
			$ConvData[$x]['volume']=$param['volume'.$x];
			
			$TotalKes+=$param['konversi'.$x];
			//$TotalKes+=$param['total'.$x];
			$VolumeKes+=$param['volume'.$x];
			$TotalTrip++;
		}
		
		//--basis trip------------------------------------------------------
				$BasisTrip=Array();
				$str2 = "SELECT * FROM ".$dbname.".vhc_kendaraan_premi where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and kendaraan like '".$param['kelompok']."%' ; ";
				
				$res2=mysql_query($str2);
				while($bar2=mysql_fetch_object($res2))
				{
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['BTREP1']=$bar2->b_trep_1;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['LBTREP1']=$bar2->lb_trep_1;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['BTREP2']=$bar2->b_trep_2;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['LBTREP2']=$bar2->lb_trep_2;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['BTREP3']=$bar2->b_trep_3;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['LBTREP3']=$bar2->lb_trep_3;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['BTREP4']=$bar2->b_trep_4;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['LBTREP4']=$bar2->lb_trep_4;
						$BasisTrip[$bar2->kategori][$bar2->kegiatan][$bar2->kendaraan][$bar2->kodeorg][$bar2->operator]['SATUAN']=$bar2->satuan;
				}
						
				
				
						
				$NewBasis=array();
				foreach($BasisTrip as $anak => $sub1) {
					foreach($sub1 as $anak1 => $sub2) {
						foreach($sub2 as $anak2 => $sub3) {
							foreach($sub3 as $anak3 => $sub4) {
								foreach($sub4 as $anak4 => $sub5) {
									foreach($sub5 as $anak5 => $hasil) {
										if ($anak1=='A' || $anak1=='D' || $anak2=='AB' || $anak2=='KD' || $anak3==$_SESSION['empl']['lokasitugas'] ){
											for($i=0;$i<$param['maxRowPres'];$i++) {
												if ($anak1=='A' || $anak1=='D'){
													$hasil_kegiatan=$ConvData[$i]['kegiatan'];
												}else{
													$hasil_kegiatan=$anak1;
												}
												if ( $anak2==substr($param['plat'],0,2) || $anak2==substr($param['plat'],0,4) ){
													$hasil_kendaraan=$param['plat'];
												}else{
													$hasil_kendaraan=$anak2;
												}
												if ((strlen($anak3)==4 && $anak3==substr($ConvData[$i]['lokasi'],0,4)) ||
												   (strlen($anak3)==6 && $anak3==substr($ConvData[$i]['lokasi'],0,6)) || 
												   (strlen($anak3)>6 && $anak3==$ConvData[$i]['lokasi'])){
													$hasil_lokasi=$ConvData[$i]['lokasi'];
												}else{
													$hasil_lokasi=$anak3;
												}
												
													$NewBasis[$anak][$hasil_kegiatan][$hasil_kendaraan][$hasil_lokasi][$anak4][$anak5]=$hasil;
												
											}
										}else{
											for($i=0;$i<$param['maxRowPres'];$i++) {
												
												if ( $anak2==substr($param['plat'],0,2) || $anak2==substr($param['plat'],0,4) ){
													$hasil_kendaraan=$param['plat'];
												}else{
													$hasil_kendaraan=$anak2;
												}
												if ((strlen($anak3)==4 && $anak3==substr($ConvData[$i]['lokasi'],0,4)) ||
												   (strlen($anak3)==6 && $anak3==substr($ConvData[$i]['lokasi'],0,6)) || 
												   (strlen($anak3)>6 && $anak3==$ConvData[$i]['lokasi'])){
													$hasil_lokasi=$ConvData[$i]['lokasi'];
												}else{
													$hasil_lokasi=$anak3;
												}
												
												$NewBasis[$anak][$anak1][$hasil_kendaraan][$hasil_lokasi][$anak4][$anak5]=$hasil;
												
											}
										}
									}
								}
							}
						}
					}
				}
				
				
			
						
					
				
			for($x=0;$x<$param['maxRow'];$x++) {	
				$rpbasis=0;
				$rplebih=0;
				$hasilkend=0;
				$hasil=0;
				for($i=0;$i<$param['maxRowPres'];$i++) {
					if($param['kelompok']=='AB'){
						$satbasis=$NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['SATUAN'];
						/*
						if ($x==1){
							echo '<pre>'; print_r(trim($ConvDataA[$x]['karyawan'])); echo '</pre>';
								exit;

						}
						*/
						
						if (trim($satbasis)==''){
							//echo '<pre>'; print_r(trim($ConvDataA[$x]['karyawan'])); echo '</pre>';
							//exit;
							$rpbasis= 0;
							$rplebih=0;
							//break;
						}elseif (strtoupper($satbasis)=='HM'){
							if ($TotalKes>=$basisjam){
								
								
						
								
								$rpbasis= ($ConvData[$i]['konversi']/$TotalKes) * ($NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1']);
								$rplebih=(($ConvData[$i]['konversi']/$TotalKes)*($TotalKes-$basisjam))*$NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['LBTREP1'];
								//$rplebih=($ConvData[$i]['konversi']-$basisjam)*$NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['LBTREP1'];
								///$rplebih=$basisjam;
								//$rpbasis=0;
							}else{
								$rpbasis=($ConvData[$i]['konversi']/$basisjam) * $NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1'];;
								$rplebih=0;
							}
						}else{
							
							$basis= ($ConvData[$i]['konversi']/$TotalKes) * ($NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1']);
							if ($ConvData[$x]['volume']>=$basis){
								$rpbasis= 0;
								$rplebih=($ConvData[$i]['volume']-$basis)*$NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['LBTREP1'];
							}else{
								$rpbasis=0;
								$rplebih=($ConvData[$i]['volume']/$basis)*$NewBasis[1][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['LBTREP1'];
							}
						}
						
						$hasil+=$rpbasis+$rplebih;
						
					}else{
						$satbasis=$NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['SATUAN'];
						
						//echo '<pre>'; print_r($NewBasis); echo '</pre>';
								//exit;
							if (trim($satbasis)==''){
								$hasilkend=0;
							}else if (strstr($satbasis, '/' )){
									if ($ConvData[$i]['rit']=='1'){
									$hasilkend= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1'];
									}elseif ($ConvData[$i]['rit']=='2'){
									$hasilkend= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP2'];
									}elseif ($ConvData[$i]['rit']=='3'){
									$hasilkend= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP3'];
									}elseif ($ConvData[$i]['rit']=='4'){					
									$hasilkend= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP4'];
									}else{
									$hasilkend=0;
									}
							}else{
									$hasilkend=  $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1'];
								
							}
							/*
							if (strtoupper($satbasis)=='TRIP' || strtoupper($satbasis)=='TREP'){
								
								
									$hasil+=  $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1'];
								
							}else{
								
								if ($ConvData[$i]['rit']=='1'){
									$hasil= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP1'];
								}elseif ($ConvData[$i]['rit']=='2'){
									$hasil= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP2'];
								}elseif ($ConvData[$i]['rit']=='3'){
									$hasil= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP3'];
								}elseif ($ConvData[$i]['rit']=='4'){					
									$hasil= $ConvData[$i]['volume'] * $NewBasis[2][trim($ConvData[$i]['kegiatan'])][trim($param['plat'])][trim($ConvData[$i]['lokasi'])][$Whoiam[trim($ConvDataA[$x]['karyawan'])]]['BTREP4'];
								}else{
									$hasil=0;
								}
							}
							*/
							$hasil+=$hasilkend;
							//echo '<pre>'; print_r($ConvData); echo '</pre>';
							
							//exit;
						}	
						
						
				}
			$res[$x]['premi']=number_format($hasil);
			//$res[$x]['premi']=$param['kelompok'];
					
				//$rpbasis=
			}		
					
				//echo '<pre>'; print_r($hasil); echo '</pre>';
				
				
		echo json_encode($res);
		break;
	case 'saveData':
		// Prepare Data
		$dataD = array();
		foreach($param['data'] as $row) {
			$dataD[] = array(
				'notransaksi'=>$param['notransaksi'],
				'idkaryawan'=>$row['idkaryawan'],
				'upah'=>round(str_replace(',','',$row['upah'])),
				'umkn'=>round(str_replace(',','',$row['umkn'])),
				'premi'=>round(str_replace(',','',$row['premi'])),
				'insentiv'=>'0'
			);
		}
		
		// Delete Detail
		$qDel = deleteQuery($dbname,'vhc_kendaraan_tenaga',"notransaksi='".
			$param['notransaksi']."' ");
		
		// Insert New
		if(mysql_query($qDel)) {
			foreach($dataD as $d) {
				$query = insertQuery($dbname,'vhc_kendaraan_tenaga',$d);
				if(!mysql_query($query)) {
					echo "DB Error : ".mysql_error();
					exit;
				}
			}
		} else {
			echo "DB Error : ".mysql_error();
		}
		break;
	case 'saveDataPres':
		// Prepare Data
		$dataD = array();
		foreach($param['data'] as $row) {
			$dataD[] = array(
				'notransaksi'=>$param['notransaksi'],
				'kodekegiatan'=>$row['kdkeg'],
				'rit'=>$row['kdrit'],
				'lokasi'=>$row['kdlok'],
				'volume'=>$row['kdvol'],
				'satuan'=>$row['kdsatk'],
				'awal'=>$row['kdaw'],
				'akhir'=>$row['kdak'],
				'total'=>$row['kdtot'],
				'convwaktu'=>$row['convwaktu'],
				'satuk'=>$row['kdsat']
			);
		}
		
		// Delete Detail
		$qDel = deleteQuery($dbname,'vhc_kendaraan_kegiatan',"notransaksi='".
			$param['notransaksi']."' ");
		
		// Insert New
		if(mysql_query($qDel)) {
			foreach($dataD as $d) {
				$query = insertQuery($dbname,'vhc_kendaraan_kegiatan',$d);
				if(!mysql_query($query)) {
					echo "DB Error : ".mysql_error();
					exit;
				}
			}
		} else {
			echo "DB Error : ".mysql_error();
		}
		break;
    default:
	break;
}



		
?>