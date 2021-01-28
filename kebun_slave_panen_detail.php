<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses){
    case 'showDetail':
		#== Prep Tab
		$headFrame = array(
			$_SESSION['lang']['prestasi'],
			$_SESSION['lang']['list']
		);
		$contentFrame = array();
		
		# Options
		#============== KHT, KHL dan Kontrak ======================
		#==========================================================
		#==========================================================
		#==========================================================
		//if($_SESSION['empl']['lokasitugas']=="TDAE"){
		//		$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
		//	"tipekaryawan in (2,3,4) and kodejabatan in (64,305)";
		//}else if(($_SESSION['empl']['lokasitugas']=="TDBE")||($_SESSION['empl']['lokasitugas']=="USJE")){
				$whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
			"tipekaryawan in (2,3,4) and kodejabatan in (64,305) and (tanggalkeluar >= CURDATE() or tanggalkeluar='0000-00-00') and not idfinger is null ";
		//}	
		#==========================================================
		#==========================================================
		#==========================================================
		#==========================================================
		#============== KHT, KHL dan Kontrak ======================
		$whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
		$whereKeg .= "kelompok='PNN' And pilih in ('3','6','8','9')";
		
		$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian,kodegolongan',$whereKary,'8');
		$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',$whereKeg);
		$optOrg = getOrgBelow($dbname,$param['afdeling'],false,'blok');
		$optThTanam= makeOption($dbname,'setup_blok','kodeorg,tahuntanam',
			"kodeorg='".end(array_reverse(array_keys($optOrg)))."'");
		$optBin = array('1'=>'Ya','0'=>'Tidak');
		$thTanam = $optThTanam[end(array_reverse(array_keys($optOrg)))];
		
		#=============================== Get UMR ==============================
		if(empty($optKary)) {
			echo "Warning: Belum ada karyawan Kontrak atau KHT di Lokasi Tugas terkait";
			exit;
		}
		$firstKary = getFirstKey($optKary);
		$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
			"karyawanid=".$firstKary." and tahun=".date('Y')." and idkomponen in (1,31)");
		$Umr = fetchData($qUMR);
		#=============================== Get UMR ==============================
		
		#================ Tab 1 ===============================================
		## List Karyawan
		$tab1 = "<fieldset><legend style='font-weight:bold'>Nama Karyawan</legend>";
		$tab1 .= makeElement('nik','select','',array(),$optKary);
		$tab1 .= makeElement('btnBlok','btn','List Blok',array('onclick'=>'showBlok()'));
		$tab1 .= makeElement('btnChange','btn','Ganti',array('disabled'=>'disabled','onclick'=>'openKary()'));
		$tab1 .= "</fieldset>";
		
		## List Blok
		$tab1 .= "<fieldset><legend style='font-weight:bold'>List Blok</legend>";
		$tab1 .= "<div id='listBlok'></div></fieldset>";
		
		## Content Frame
		$contentFrame[0] = $tab1;
		#================ /Tab 1 ==============================================
		
		#================ Tab 2 ===============================================
		$qDetail = "SELECT DISTINCT a.nik,concat(b.subbagian,'-',b.kodegolongan,'-',b.namakaryawan) as namakaryawan  FROM ".$dbname.".kebun_prestasi a LEFT JOIN ".
			$dbname.".datakaryawan b ON a.nik=b.karyawanid WHERE a.notransaksi='".$param['notransaksi']."' order by b.subbagian,b.kodegolongan,b.namakaryawan";
		$resDetail = fetchData($qDetail);
		
		$tab2 = "<fieldset><legend style='font-weight:bold'>List Detail</legend>";
		$tab2 .= "<table class=data border=1 cellspacing=0><thead><tr class=rowheader>";
		$tab2 .= "<td>ID</td>";
		$tab2 .= "<td>Nama Karyawan</td>";
		$tab2 .= "<td colspan=2>Aksi</td>";
		$tab2 .= "</tr></thead><tbody id='contentTab2'>";
		foreach($resDetail as $key=>$row) {
			$tab2 .= "<tr id='detailKary_".$key."' class=rowcontent>";
			$tab2 .= "<td id='nik_".$key."'>".$row['nik']."</td>";
			$tab2 .= "<td id='nama_".$key."'>".$row['namakaryawan']."</td>";
			$tab2 .= "<td><img src='images/edit.png' style='width:15px;cursor:pointer' ".
				"onclick='editPerKary(".$key.")' title='Edit'></td>".
				"<td><img src='images/delete.png' style='width:15px;cursor:pointer' ".
				"onclick='delPerKary(".$key.")' title='Delete'></td>";
			$tab2 .= "</tr>";
		}
		$tab2 .= "</tbody></table></fieldset>";
		
		$contentFrame[1] = $tab2;
		#================ /Tab 2 ==============================================
		
		#== Display View
		# Draw Tab
		echo "<fieldset><legend><b>Detail</b></legend>";
		drawTab('FRM',$headFrame,$contentFrame,150,'100%');
		echo "</fieldset>";
		break;
	case 'loadTab2':
		$qDetail = "SELECT DISTINCT a.nik,b.namakaryawan FROM ".$dbname.".kebun_prestasi a LEFT JOIN ".
			$dbname.".datakaryawan b ON a.nik=b.karyawanid WHERE a.notransaksi='".$param['notransaksi']."'";
		$resDetail = fetchData($qDetail);
		
		$tab2 = "";
		foreach($resDetail as $key=>$row) {
			$tab2 .= "<tr id='detailKary_".$key."' class=rowcontent>";
			$tab2 .= "<td id='nik_".$key."'>".$row['nik']."</td>";
			$tab2 .= "<td id='nama_".$key."'>".$row['namakaryawan']."</td>";
			$tab2 .= "<td><img src='images/edit.png' style='width:15px;cursor:pointer' ".
				"onclick='editPerKary(".$key.")' title='Edit'></td>".
				"<td><img src='images/delete.png' style='width:15px;cursor:pointer' ".
				"onclick='delPerKary(".$key.")' title='Delete'></td>";
			$tab2 .= "</tr>";
		}
		
		echo $tab2;
		break;
	case 'listBlok':
		// Break No Transaksi
		$noTrans = explode('/',$param['notransaksi']);
		
		// Options
		$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"left(kodeorganisasi,4)='".$noTrans[1]."' and tipe='BLOK'");
		
		// Get Upah Kerja
		$optUpah = makeOption($dbname,'sdm_5gajipokok','karyawanid,jumlah',
			"karyawanid='".$param['nik']."' and idkomponen=1");
		if(empty($optUpah)) {
			$upah = 0;
		} else {
			$upah = $optUpah[$param['nik']]/25;
		}
		
		// Get Data
		$qDetail = "SELECT * FROM ".$dbname.".kebun_prestasi WHERE notransaksi='".
			$param['notransaksi']."' and nik='".$param['nik']."'";
		$resDetail = fetchData($qDetail);
		
		$tab = "<fieldset><legend style='font-weight:bold'>Aksi</legend>";
		$tab .= makeElement('btnTambah','btn','Tambah Baris',array('onclick'=>'addBaris()'));
		$tab .= makeElement('btnHitung','btn','Hitung Premi',array('onclick'=>'hitungPremi()'));
		$tab .= makeElement('btnSave','btn','Simpan Semua',array('onclick'=>'saveList()','disabled'=>'disabled'));
		$tab .= "</fieldset>";
		$tab .= "<fieldset><legend style='font-weight:bold'>Table</legend>";
		$tab .= "<div>".makeElement('upahkerja','label',$_SESSION['lang']['upahkerja'])."&nbsp;";
		$tab .= makeElement('upahkerja','text',$upah)."&nbsp;";
		
		$tab .= "&nbsp;".makeElement('kontanan','label',$_SESSION['lang']['kontanan'])."&nbsp;";
		$tab .= makeElement('kontanan','check','0',array('onClick'=>'getKontanan()'))."</div>&nbsp;";
		
		
		$tab .= "<table class=data border=1 cellspacing=0><thead><tr class=rowheader>";
		$tab .= "<td>".$_SESSION['lang']['kodeorg']."</td>";
		$tab .= "<td>".$_SESSION['lang']['tahuntanam']."</td>";
		$tab .= "<td>".$_SESSION['lang']['hasilkerja']."(JJg)</td>";
		$tab .= "<td>".$_SESSION['lang']['hasilkerjakg']."</td>";
		$tab .= "<td>".$_SESSION['lang']['upahpremi']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti1']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti2']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti3']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti4']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti5']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti6']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti7']."</td>";
		$tab .= "<td>".$_SESSION['lang']['penalti8']."</td>";
		$tab .= "<td>".$_SESSION['lang']['rupiahpenalty']."</td>";
		$tab .= "<td>".$_SESSION['lang']['dendabasis']."</td>";
		$tab .= "<td>Kontanan (Rp/Kg)</td>";
		$tab .= "<td>Pusingan Awal</td>";
		//-------------------------------------------------------------------------------------------------------------
		//----------------------------------------------HILANGKAN DISINI---------------------------------------------------
		//-------------------------------------------------------------------------------------------------------------
		#$tab .= "<td>UPAH KERJA</td>";
		//-------------------------------------------------------------------------------------------------------------
		
		$tab .= "</tr></thead><tbody id='bodyDetail'>";
		foreach($resDetail as $key=>$row){
			$tab .= "<tr id='detail_".$key."' class=rowcontent>";
			$tab .= "<td>".makeElement('kodeorg_'.$key,'select',$row['kodeorg'],
				array('style'=>'width:200px','onchange'=>'getKg('.$key.')'),$optOrg)."</td>";
			$tab .= "<td>".makeElement('tahuntanam_'.$key,'text',$row['tahuntanam'],
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$tab .= "<td>".makeElement('hasilkerja_'.$key,'textnum',$row['hasilkerja'],
				array('style'=>'width:70px','onkeyup'=>'getKg('.$key.')'))."</td>";
			$tab .= "<td>".makeElement('hasilkerjakg_'.$key,'textnum',$row['hasilkerjakg'],
				array('disabled'=>'disabled','style'=>'width:70px'))."</td>";
			$tab .= "<td>".makeElement('upahpremi_'.$key,'textnum',$row['upahpremi'],
				array('disabled'=>'disabled','style'=>'width:70px'))."</td>";
			$tab .= "<td>".makeElement('penalti1_'.$key,'textnum',$row['penalti1'],
				array('style'=>'width:70px','onkeyup'=>'getDenda(1,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('penalti2_'.$key,'textnum',$row['penalti2'],
				array('style'=>'width:70px','onkeyup'=>'getDenda(2,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('penalti3_'.$key,'textnum',$row['penalti3'],
				array('style'=>'width:80px','onkeyup'=>'getDenda(3,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('penalti4_'.$key,'textnum',$row['penalti4'],
				array('style'=>'width:70px','onkeyup'=>'getDenda(4,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('penalti5_'.$key,'textnum',$row['penalti5'],
				array('style'=>'width:130px','onkeyup'=>'getDenda(5,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('penalti6_'.$key,'textnum',$row['penalti6'],
				array('style'=>'width:90px','onkeyup'=>'getDenda(6,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('penalti7_'.$key,'textnum',$row['penalti7'],
				array('style'=>'width:90px','onkeyup'=>'getDenda(7,'.$key.')'))."</td>";
				$tab .= "<td>".makeElement('penalti8_'.$key,'textnum',$row['penalti8'],
				array('style'=>'width:90px','onkeyup'=>'getDenda(8,'.$key.')'))."</td>";
			$tab .= "<td>".makeElement('rupiahpenalty_'.$key,'textnum',$row['rupiahpenalty'],
				array('disabled'=>'disabled','style'=>'width:70px'))."</td>";	
			$tab .= "<td>".makeElement('dendabasis_'.$key,'textnum',$row['dendabasis'],
				array('disabled'=>'disabled','style'=>'width:70px'))."";
				if ($row['rpkgkontanan']==0){
			$tab .= "<td>".makeElement('rpkgkontanan_'.$key,'textnum',$row['rpkgkontanan'],
				array('disabled'=>'disabled','style'=>'width:70px'))."";
				}else{
			$tab .= "<td>".makeElement('rpkgkontanan_'.$key,'textnum',$row['rpkgkontanan'],
				array('style'=>'width:70px'))."";		
				}
				if ($row['rotasiawal']==0){
			$tab .= "<td>".makeElement('pusawal_'.$key,'check','0',
				array('style'=>'width:50px'))."";		
				}else{
			$tab .= "<td>".makeElement('pusawal_'.$key,'check','0',
				array('style'=>'width:50px','checked'=>'checked'))."";		
				}
			
			$tab .= "".makeElement('luaspanen_'.$key,'hidden',$row['luaspanen'],
				array('style'=>'width:0px'))."";
			//-------------------------------------------------------------------------------------------------------------
			//----------------------------------------------RUBAH DISINI---------------------------------------------------
			//-------------------------------------------------------------------------------------------------------------
			$tab .= "".makeElement('upahhk_'.$key,'hidden',$row['upahkerja'],
			array('style'=>'width:0px'))."</td>";
			//-------------------------------------------------------------------------------------------------------------
			#$tab .= "</td>"."<td>".makeElement('upahhk_'.$key,'textnum',$row['upahkerja'],
			
			#array('style'=>'width:80px'))."</td>";
			
				
				
			$tab .= "</tr>";
		}
		$tab .= "</tbody></table></fieldset>";
		echo $tab;
		break;
	case 'newRow':
		$key = $param['numRow'];
		// Options
		$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
			"left(kodeorganisasi,4)='".$param['kodeorg']."' and tipe='BLOK'");
		$firstBlok = getFirstKey($optOrg);
		
		// Get Tahun Tanam
		$optTT = makeOption($dbname,'setup_blok','kodeorg,tahuntanam',
			"kodeorg='".$firstBlok."'");
		
		$tab = "";
		$tab .= "<td>".makeElement('kodeorg_'.$key,'select',$firstBlok,
			array('style'=>'width:200px','onchange'=>'getKg('.$key.')'),$optOrg)."</td>";
		$tab .= "<td>".makeElement('tahuntanam_'.$key,'text',$optTT[$firstBlok],
			array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
		$tab .= "<td>".makeElement('hasilkerja_'.$key,'textnum','0',
			array('style'=>'width:70px','onkeyup'=>'getKg('.$key.')'))."</td>";
		$tab .= "<td>".makeElement('hasilkerjakg_'.$key,'textnum','0',
			array('disabled'=>'disabled','style'=>'width:70px'))."</td>";
		$tab .= "<td>".makeElement('upahpremi_'.$key,'textnum','0',
			array('disabled'=>'disabled','style'=>'width:70px'))."</td>";
		$tab .= "<td>".makeElement('penalti1_'.$key,'textnum','0',
			array('style'=>'width:70px','onkeyup'=>'getDenda(1,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti2_'.$key,'textnum','0',
			array('style'=>'width:70px','onkeyup'=>'getDenda(2,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti3_'.$key,'textnum','0',
			array('style'=>'width:80px','onkeyup'=>'getDenda(3,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti4_'.$key,'textnum','0',
			array('style'=>'width:70px','onkeyup'=>'getDenda(4,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti5_'.$key,'textnum','0',
			array('style'=>'width:130px','onkeyup'=>'getDenda(5,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti6_'.$key,'textnum','0',
			array('style'=>'width:90px','onkeyup'=>'getDenda(6,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti7_'.$key,'textnum','0',
			array('style'=>'width:90px','onkeyup'=>'getDenda(7,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('penalti8_'.$key,'textnum','0',
			array('style'=>'width:90px','onkeyup'=>'getDenda(8,'.$key.')'))."</td>";
		$tab .= "<td>".makeElement('rupiahpenalty_'.$key,'textnum','0',
			array('disabled'=>'disabled','style'=>'width:70px'))."</td>";
		$tab .= "<td>".makeElement('dendabasis_'.$key,'textnum','0',
			array('disabled'=>'disabled','style'=>'width:70px'))."";
			if ($param['kontanan']==0){
		$tab .= "<td>".makeElement('rpkgkontanan_'.$key,'textnum','0',
			array('disabled'=>'disabled','style'=>'width:70px'))."";
			}else{
		$tab .= "<td>".makeElement('rpkgkontanan_'.$key,'textnum','0',
			array('style'=>'width:70px'))."";
			}
			
			
		$tab .= "<td>".makeElement('pusawal_'.$key,'check','0',
				array('style'=>'width:50px'))."";		
		$tab .= "".makeElement('luaspanen_'.$key,'hidden','0',
			array('style'=>'width:0px'))."";
		//-------------------------------------------------------------------------------------------------------------
		//----------------------------------------------RUBAH DISINI---------------------------------------------------
		//-------------------------------------------------------------------------------------------------------------
		$tab .= "".makeElement('upahhk_'.$key,'hidden','0',
		array('style'=>'width:0px'))."</td>";
		//-------------------------------------------------------------------------------------------------------------
		#$tab .= "</td>"."<td>".makeElement('upahhk_'.$key,'textnum','0',
			#array('style'=>'width:80px'))."</td>";
	
			
		echo $tab; 	 
		break;
    case 'delKary':
		$where = "notransaksi='".$param['notransaksi']."' and nik='".$param['nik']."'";
		$query = "delete from `".$dbname."`.`kebun_prestasi` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
	case 'getKg':
		// Break Tanggal
		$tgl = explode('-',$param['tanggal']);
		
		// Get Tahun Tanam
    	$query1 = selectQuery($dbname,'setup_blok','kodeorg,tahuntanam',
			"kodeorg='".$param['kodeorg']."'");
		$res1 = fetchData($query1);
		if(!empty($res1)) {
			$tt = $res1[0]['tahuntanam'];
		} else {
			$tt = '0';
		}
		
		// Get BJR
		//$query2 = selectQuery($dbname,'kebun_5bjr','bjr',
		//	"kodeorg='".substr($param['kodeorg'],0,6)."' and thntanam=".$tt.
		//	" and tahunproduksi=".$tgl[2]);
		$query2 = selectQuery($dbname,'kebun_5bjrharian','bjr',
			"afdeling='".substr($param['kodeorg'],0,6)."' and tahuntanam=".$tt.
			" and tanggal=".tanggalsystem($param['tanggal']));
		$res2 = fetchData($query2);
		if(empty($res2)) {
			$bjr=0;
		} else {
			$bjr=$res2[0]['bjr'];
		}
		
		// Get KG
		$kg = $param['hasilkerja']*$bjr;
		
		// Prep Output
		$res = array(
			'hasilkerjakg'=>$kg,
			'tahuntanam'=>$tt,
		);
		echo json_encode($res);
		break;
	case 'getDenda':
		$simpan=0;
		$qDetail = "SELECT * FROM ".$dbname.".kebun_5dendapanen order by kode asc; ";
		$res1 = fetchData($qDetail);
		foreach($res1 as $key=>$row) {
			$isi='hasilpenalti'.$row['kode'];
			if(!empty($param)) {
				$simpan+=($param[$isi]*$row['rupiah']);
			}else{
				$simpan = 'Error';
				echo $simpan;
				exit;
			}
			
		}
		
		
		// Prep Output
		$res = array(
			'hasilDenda'=>$simpan
		);
		echo json_encode($res);
		break;	
    case 'updUpah':
		$firstKary = $param['nik'];
		$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
			"karyawanid=".$firstKary." and tahun=".$param['tahun']." and idkomponen in (1,31)");
		$Umr = fetchData($qUMR);
		echo $Umr[0]['nilai']/25;
		break;
	case 'hitungPremi':
	/*
		$tgl = explode('-',$param['tanggal']);
		$hari = date('w',mktime(0,0,0,$tgl[1],$tgl[0],$tgl[2]));
		
		## Hasil Kerja
		$hslKerja = 0;
		for($i=0;$i<$param['maxRow'];$i++) {
			$hslKerja += $param['hasilkerja'.$i];
		}
		
		## Data Basis Panen
		$q = selectQuery($dbname,'kebun_5basispanen','*',
			"kodeorg='".$param['afdeling']."' and tahuntanam=".
			$param['tahuntanam']);
		$res1 = fetchData($q);
		$hasil = $res1[0];
		
		## Data BJR
		//$q2 = selectQuery($dbname,'kebun_5bjr','*',
		//	"kodeorg='".$param['afdeling']."' and thntanam=".
		//	$param['tahuntanam']." and tahunproduksi=".$tgl[2]);
		$q2 = selectQuery($dbname,'kebun_5bjrharian','bjr',
			"afdeling='".$param['afdeling']."' and tahuntanam=".$param['tahuntanam'].
			" and tanggal=".tanggalsystem($param['tanggal']));
		$res2 = fetchData($q2);
		if(empty($res2)) {
			$bjr=0;
		} else {
			$bjr=$res2[0]['bjr'];
		}
		
		## Amankan Janjang
		$hslKerjaJjg = $hslKerja;
		
		## Penentuan Hasil Kerja JJG atau KG
		if($hasil['tipe']=='KG') {
			$hslKerja = $hslKerja*$bjr; 
		}else{
           $hslKerja = $hslKerja ; 
		}
		//echo $hasil['tipe'].'#'.$hslKerja.'#'.$bjr;exit('error');
		## Get Basis
		if($hari==5) {
			$basis = $hasil['basisjumat'];
		} else {
			$basis = $hasil['basisborong'];
		}
		$basis2 = $hasil['lebihborong2'];
		$basis3 = $hasil['lebihborong3'];
		
		## Hitung Premi
		if($hslKerja>=$basis) {
			$premi = $hasil['rpsiapborong'];
			
			## Cek Basis 3
			if($basis3>0 and $hslKerja>$basis3) {
				$premi+=($hslKerja-$basis3)*$hasil['rplebihborong3'];
				$premi+=($basis3-$basis2)*$hasil['rplebihborong2'];
				$premi+=($basis2-$basis)*$hasil['rplebihborong1'];
			} else {
				if($basis2>0 and $hslKerja>$basis2) {
					$premi+=($hslKerja-$basis2)*$hasil['rplebihborong2'];
					$premi+=($basis2-$basis)*$hasil['rplebihborong1'];
				} else {
					if($hslKerja>$basis) {
						$premi+=($hslKerja-$basis)*$hasil['rplebihborong1'];
					}
				}
			}
		} else {
			$premi=0;
		}
		
		$res = array();
		for($i=0;$i<$param['maxRow'];$i++) {
			$res[$i]['premi'] = ($param['hasilkerja'.$i]/$hslKerjaJjg)*$premi;
		}
		echo json_encode($res);
		break;
	*/
		##TENTUKAN KONTANAN APA BUKAN
		
		IF ($param['kontanan']=="true"){
			$KONTANAN_DEFAULT=1;
			
		}else{
			$KONTANAN_DEFAULT=0;
		}
		
		
		$tgl = explode('-',$param['tanggal']);
		$hari = date('w',mktime(0,0,0,$tgl[1],$tgl[0],$tgl[2]));
		##cek status karyawan kht atau bhl (3:KHT 4:bhl)
		$q = selectQuery($dbname,'datakaryawan','*',"karyawanid='".$param['karnik']."'");
		$res1 = fetchData($q);
		$KAR = $res1[0];
		$STATUS_DEFAULT=$KAR['kodegolongan'];
		
		$b = selectQuery($dbname,'sdm_5golongan','*',"kodegolongan	='".$STATUS_DEFAULT."'");
		$res2 = fetchData($b);
		$KAR2 = $res2[0];
		$UPAH_DEFAULT=$KAR2['upah'];
		
		
		## Hasil Kerja
		$hslKerjaJJG = 0;
		$hslKerjaKg = 0;
		$KONTANAN_RPKG=Array();
		$HasilRinci=Array();
		for($i=0;$i<$param['maxRow'];$i++) {
			$KONTANAN_RPKG[$i]=$param['rpkgkontanan'.$i];
			$hslKerjaJJG += $param['hasilkerja'.$i];
			$hslKerjaKg += $param['hasilkerjakg'.$i];
			$HasilRinci[$i]['JJG']=$param['hasilkerja'.$i];
			$HasilRinci[$i]['KG']=$param['hasilkerjakg'.$i];
		}
		$BASIS_FIX=Array();
		## Data Basis Panen
		$str2 = "SELECT * FROM ".$dbname.".kebun_5basispanen where kodeorg='".$param['afdeling']."' ; ";
		$res2=mysql_query($str2);
		while($bar2=mysql_fetch_object($res2))
		{
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam]['SATUAN']=$bar2->tipe;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam]['SIAPBORONG']=$bar2->rpsiapborong;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['BASISJUMAT']=$bar2->basisjumat;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['BASISBORONG']=$bar2->basisborong;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['RPBORONG']=$bar2->rplebihborong1;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['BASISBORONG2']=$bar2->lebihborong2;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['RPBORONG2']=$bar2->rplebihborong2;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['BASISBORONG3']=$bar2->lebihborong3;
			$BASIS_FIX[$bar2->kodeorg][$bar2->tahuntanam][$bar2->tipe]['RPBORONG3']=$bar2->rplebihborong3;
		}
		
		## TENTUKAN TIPE DAN BASIS BORONG	DEFAULT (SESUAI TAHUN TANAM TUA)
			$TIPE_DEFAULT=$BASIS_FIX[$param['afdeling']][$param['tahuntanam']]['SATUAN'];
			$SB_DEFAULT=$BASIS_FIX[$param['afdeling']][$param['tahuntanam']]['SIAPBORONG'];
		
		## PENENTUAN TOTAL Hasil Kerja JJG atau KG
		if($TIPE_DEFAULT=='KG') {
			$TOTAL_HASIL = $hslKerjaKg; 
		}else{
            $TOTAL_HASIL = $hslKerjaJJG ; 
		}
		
		## Get Basis
			if($hari==5) {
				$BARONG_DEFAULT=$BASIS_FIX[$param['afdeling']][$param['tahuntanam']][$TIPE_DEFAULT]['BASISJUMAT'];
			} else {
				$BARONG_DEFAULT=$BASIS_FIX[$param['afdeling']][$param['tahuntanam']][$TIPE_DEFAULT]['BASISBORONG'];
			}
			$BARONG2_DEFAULT=$BASIS_FIX[$param['afdeling']][$param['tahuntanam']][$TIPE_DEFAULT]['BASISBORONG2'];
			$BARONG3_DEFAULT=$BASIS_FIX[$param['afdeling']][$param['tahuntanam']][$TIPE_DEFAULT]['BASISBORONG3'];
		//echo $TIPE_DEFAULT.'#'.$UPAH_DEFAULT;exit('error');
		$res = array();
		## Hitung Premi
		IF ($KONTANAN_DEFAULT==1){
				for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] = 0;
					
					$res[$i]['upah'] =(($HasilRinci[$i][$TIPE_DEFAULT])*$KONTANAN_RPKG[$i]);
					$res[0]['upahkerja']+=$res[$i]['upah'];
					$res[$i]['denda'] =0;
					
				}
		}ELSE{
		
		if ($BARONG_DEFAULT > 0){
			##LEBIH BASIS
			
			if($TOTAL_HASIL>=$BARONG_DEFAULT) {
				//lebih basis
				$LB=$TOTAL_HASIL-$BARONG_DEFAULT;
				for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$SB_DEFAULT)+((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)* $LB) * $BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG']);	
					$res[0]['upahkerja']=$UPAH_DEFAULT;
					$res[$i]['upah'] =(($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$UPAH_DEFAULT);
					$res[$i]['denda'] =0;
				}
				/*
				for($i=0;$i<$param['maxRow'];$i++) {
					//lebih basis
					$LB=((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)* $TOTAL_HASIL)-$BARONG_DEFAULT );
					if ($LB > 0){
						$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$SB_DEFAULT)+($LB*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG']);	
					}else{
						$LB=($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)* ($TOTAL_HASIL-$BARONG_DEFAULT );
						$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$SB_DEFAULT)+($LB*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG']);	
					}
					
					$res[0]['upahkerja']=$UPAH_DEFAULT;
					$res[$i]['upah'] =(($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$UPAH_DEFAULT);
					$res[$i]['denda'] =0;
				}
				*/
			}else{
				$tot=0;
				for($i=0;$i<$param['maxRow'];$i++) {
				$res[$i]['premi']=0;
				##cek status kht
				if (($STATUS_DEFAULT=="KHT")||($STATUS_DEFAULT=="KNT")){
					$res[0]['upahkerja']=$UPAH_DEFAULT;
					$res[$i]['upah'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$UPAH_DEFAULT);
					$res[$i]['denda'] = (($UPAH_DEFAULT-(($TOTAL_HASIL/$BARONG_DEFAULT)*$UPAH_DEFAULT))/$param['maxRow']);
				}else{
					$tot=(($TOTAL_HASIL/$BARONG_DEFAULT)*$UPAH_DEFAULT);
					$res[0]['upahkerja']=$tot;
					$res[$i]['upah'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL)*$tot);
					$res[$i]['denda'] = 0;
				}
					
				}
			}
		}ELSE{
			for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] =0;
					$res[0]['upahkerja']=0;
					$res[$i]['upah'] =0;
					$res[$i]['denda'] =0;
			}
		}
		
		
		if ($BARONG2_DEFAULT > 0){
			$TOTAL_HASIL2=$TOTAL_HASIL-$BARONG_DEFAULT;
			##LEBIH BASIS
			if($TOTAL_HASIL2>=$BARONG2_DEFAULT) {
				$LB=$TOTAL_HASIL2-$BARONG2_DEFAULT;
				for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$SB_DEFAULT)+((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)* $LB) * $BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG2']);	
					$res[0]['upahkerja']=$UPAH_DEFAULT;
					$res[$i]['upah'] =(($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$UPAH_DEFAULT);
					$res[$i]['denda'] =0;
				}
				/*
				for($i=0;$i<$param['maxRow'];$i++) {
					//lebih basis
					$LB=((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)* $TOTAL_HASIL2)-$BARONG2_DEFAULT );
					if ($LB > 0){
						$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$SB_DEFAULT)+($LB*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG2']);	
					}else{
						$LB=($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)* ($TOTAL_HASIL2-$BARONG2_DEFAULT );
						$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$SB_DEFAULT)+($LB*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG2']);	
					}
					
					//$res[$i]['premi'] += (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$SB_DEFAULT)+(((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)* $TOTAL_HASIL2)-$BARONG2_DEFAULT )*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG2']);
					$res[0]['upahkerja']+=$UPAH_DEFAULT;
					$res[$i]['upah'] +=(($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$UPAH_DEFAULT);
					$res[$i]['denda'] +=0;
				}
				*/
			}else{
				$tot=0;
				for($i=0;$i<$param['maxRow'];$i++) {
				$res[$i]['premi']+=0;
				##cek status kht
				if (($STATUS_DEFAULT=="KHT")||($STATUS_DEFAULT=="KNT")){
					$res[0]['upahkerja']+=$UPAH_DEFAULT;
					$res[$i]['upah'] += (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$UPAH_DEFAULT);
					$res[$i]['denda'] += (($UPAH_DEFAULT-(($TOTAL_HASIL2/$BARONG2_DEFAULT)*$UPAH_DEFAULT))/$param['maxRow']);
				}else{
					$tot=(($TOTAL_HASIL2/$BARONG2_DEFAULT)*$UPAH_DEFAULT);
					$res[0]['upahkerja']+=$tot;
					$res[$i]['upah'] += (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL2)*$tot);
					$res[$i]['denda'] += 0;
				}
					
				}
			}
		}ELSE{
			for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] +=0;
					$res[0]['upahkerja']+=0;
					$res[$i]['upah'] +=0;
					$res[$i]['denda'] +=0;
			}
			$TOTAL_HASIL2=0;
		}
		
		
		if ($BARONG3_DEFAULT > 0){
			$TOTAL_HASIL3=$TOTAL_HASIL2-$BARONG_DEFAULT2;
			##LEBIH BASIS
			if($TOTAL_HASIL3>=$BARONG3_DEFAULT) {
				$LB=$TOTAL_HASIL3-$BARONG3_DEFAULT;
				for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$SB_DEFAULT)+((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)* $LB) * $BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG3']);	
					$res[0]['upahkerja']=$UPAH_DEFAULT;
					$res[$i]['upah'] =(($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$UPAH_DEFAULT);
					$res[$i]['denda'] =0;
				}
				/*
				for($i=0;$i<$param['maxRow'];$i++) {
						//lebih basis
					$LB=((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)* $TOTAL_HASIL3)-$BARONG3_DEFAULT );
					if ($LB > 0){
						$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$SB_DEFAULT)+($LB*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG3']);	
					}else{
						$LB=($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)* ($TOTAL_HASIL3-$BARONG3_DEFAULT );
						$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$SB_DEFAULT)+($LB*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG3']);	
					}
					//$res[$i]['premi'] = (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$SB_DEFAULT)+(((($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)* $TOTAL_HASIL3)-$BARONG3_DEFAULT )*$BASIS_FIX[$param['afdeling']][$param['thntanam'.$i]][$TIPE_DEFAULT]['RPBORONG3']);
					$res[0]['upahkerja']+=$UPAH_DEFAULT;
					$res[$i]['upah'] +=(($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$UPAH_DEFAULT);
					$res[$i]['denda'] +=0;
				}
				*/
			}else{
				$tot=0;
				for($i=0;$i<$param['maxRow'];$i++) {
				$res[$i]['premi']+=0;
				##cek status kht
				if (($STATUS_DEFAULT=="KHT")||($STATUS_DEFAULT=="KNT")){
					$res[0]['upahkerja']+=$UPAH_DEFAULT;
					$res[$i]['upah'] += (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$UPAH_DEFAULT);
					$res[$i]['denda'] += (($UPAH_DEFAULT-(($TOTAL_HASIL3/$BARONG3_DEFAULT)*$UPAH_DEFAULT))/$param['maxRow']);
				}else{
					$tot=(($TOTAL_HASIL3/$BARONG3_DEFAULT)*$UPAH_DEFAULT);
					$res[0]['upahkerja']+=$tot;
					$res[$i]['upah'] += (($HasilRinci[$i][$TIPE_DEFAULT]/$TOTAL_HASIL3)*$tot);
					$res[$i]['denda'] += 0;
				}
					
				}
			}
		}ELSE{
			for($i=0;$i<$param['maxRow'];$i++) {
					$res[$i]['premi'] +=0;
					$res[0]['upahkerja']+=0;
					$res[$i]['upah'] +=0;
					$res[$i]['denda'] +=0;
			}
			$TOTAL_HASIL3=0;
		}
		
		}
		echo json_encode($res);
		break;
	case 'saveList':
		// Get Basis Panen
		$q = selectQuery($dbname,'kebun_5basispanen','*',
			"kodeorg='".substr($param['data'][0]['kodeorg'],0,6)."' and tahuntanam=".
			$param['data'][0]['tahuntanam']);
		$res1 = fetchData($q);
		if(empty($res1)) {
			$norma = 0;
		} else {
			$norma = $res1[0]['basisborong'];
		}
		
		// Prepare Data
		$dataD = array();
		foreach($param['data'] as $row) {
			$dataD[] = array(
				'notransaksi'=>$param['notransaksi'],
				'nik'=>$param['nik'],
				'kodekegiatan'=>'0',
				'kodeorg'=>$row['kodeorg'],
				'tahuntanam'=>$row['tahuntanam'],
				'hasilkerja'=>$row['hasilkerja'],
				'hasilkerjakg'=>$row['hasilkerjakg'],
				'jumlahhk'=>0,
				'norma'=>$norma,
				'upahkerja'=>round(str_replace(',','',$row['upahhk'])),
				'upahpremi'=>round(str_replace(',','',$row['upahpremi'])),
				'umr'=>0,'statusblok'=>0,'pekerjaanpremi'=>0,
				'penalti1'=>$row['penalti1'],
				'penalti2'=>$row['penalti2'],
				'penalti3'=>$row['penalti3'],
				'penalti4'=>$row['penalti4'],
				'penalti5'=>$row['penalti5'],
				'penalti6'=>$row['penalti6'],
				'penalti7'=>$row['penalti7'],
				'penalti8'=>$row['penalti8'],
				'rupiahpenalty'=>round($row['rupiahpenalty']),
				'dendabasis'=>round($row['dendabasis']),
				'kodebatch'=>'',
				'luaspanen'=>$row['luaspanen'],
				'rpkgkontanan'=>round($row['rpkgkontanan']),
				'rotasiawal'=>$row['pusawal']
			);
		}
		
		// Delete Detail
		$qDel = deleteQuery($dbname,'kebun_prestasi',"notransaksi='".
			$param['notransaksi']."' and nik='".$param['nik']."'");
		
		// Insert New
		if(mysql_query($qDel)) {
			foreach($dataD as $d) {
				$query = insertQuery($dbname,'kebun_prestasi',$d);
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