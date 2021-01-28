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
			'Bahan dan Material'
		);
		$contentFrame = array();
		
		#================ Tab 0 ===============================================
		
	
			// Get Data
		$qDetailPres = "SELECT * FROM ".$dbname.".vhc_servicedt_vw WHERE notransaksi='".
			$param['notransaksi']."' ";
		$resDetailPres = fetchData($qDetailPres);
		
		$taba = "<fieldset><legend style='font-weight:bold'>Aksi</legend>";
		$taba .= makeElement('btnTambahPres','btn','Tambah Baris',array('onclick'=>'addBarisPres()'));
		$taba .= makeElement('btnSavePres','btn','Simpan Semua',array('onclick'=>'saveListPres()'));
		$taba .= makeElement('btnClearPres','btn','Hapus Semua',array('onclick'=>'delPres()'));
		$taba .= "</fieldset>";
		$taba .= "<fieldset><legend style='font-weight:bold'>Table</legend>";
	
		
		
		$taba .= "<table class=data border=1 cellspacing=0><thead><tr class=rowheader>";
		$taba .= "<td>Kode Barang</td>";
		$taba .= "<td>Nama barang</td>";
		$taba .= "<td>Jumlah</td>";
		$taba .= "<td>Satuan</td>";
		$taba .= "<td>Keterangan</td>";

		
		$taba .= "</tr></thead><tbody id='bodyDetailPres'>";
		$els[] = array(
	makeElement('noref','label','No. Ref Kegiatan'),
	makeElement('noref','text',$data['noref'],array('style'=>'width:150px',$disabled=>$disabled,))
    );
	
		foreach($resDetailPres as $key=>$row){
			$taba .= "<tr id='detailpres_".$key."' class=rowcontent>";
			
			
			$taba .= "<td>".makeElement('kodebrg_'.$key,'text',$row['kodebarang'],
				array('disabled'=>'disabled','style'=>'width:80px'))."<img id=imgbrg_".$key." src=images/search.png class=dellicon title=".$_SESSION['lang']['find']."></td>";
			$taba .= "<td>".makeElement('nmbrg_'.$key,'text',$row['namabarang'],
				array('disabled'=>'disabled','style'=>'width:250px'))."</td>";
			$taba .= "<td>".makeElement('qty_'.$key,'textnumeric',number_format($row['jumlah']),
				array('disabled'=>'disabled','style'=>'width:50px'))."</td>";
			$taba .= "<td>".makeElement('satuanvolume_'.$key,'text',$row['satuan'],
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$taba .= "<td>".makeElement('ketbrg_'.$key,'text',$row['keterangan'],
				array('disabled'=>'disabled','style'=>'width:250px'))."</td>";
			$taba .= "</tr>";
		}
		$taba .= "</tbody></table></fieldset>";
		$contentFrame[0] = $taba;
		
		
		echo "<fieldset><legend><b>Detail</b></legend>";
		drawTab('FRM',$headFrame,$contentFrame,150,'100%');
		echo "</fieldset>";
		break;
	
	case 'newRowPres':
		$key = $param['numRow'];
		
		$tab = "";
			
			$tab .= "<td>".makeElement("kodebrg_".$key."",'text','',array('style'=>'width:80px','disabled'=>'disabled','class=myinputtext'))."<img id=imgbrg_".$key." src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg('".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg(".$key.")>Find</button></fieldset><div id=container2></div>',event,".$key.")\";></td>";
			$tab .= "<td>".makeElement('nmbrg_'.$key,'text','',
				array('disabled'=>'disabled','style'=>'width:250px'))."</td>";
			$tab .= "<td>".makeElement('qty_'.$key,'textnumeric','0',
				array('style'=>'width:50px'))."</td>";
			$tab .= "<td>".makeElement('satuanvolume_'.$key,'text','',
				array('disabled'=>'disabled','style'=>'width:50px;text-align:center;'))."</td>";
			$tab .= "<td>".makeElement('ketbrg_'.$key,'text','',
				array('style'=>'width:250px'))."</td>";
	
		echo $tab; 	 
		break;		
	case 'delPres':
		$where = "notransaksi='".$param['notransaksi']. 	"' ";
		$query = "delete from `".$dbname."`.`vhc_servicedt` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;		

	case 'saveDataPres':
		// Prepare Data
		$dataD = array();
		foreach($param['data'] as $row) {
			$dataD[] = array(
				'notransaksi'=>$param['notransaksi'],
				'kodebarang'=>$row['kodebarang'],
				'jumlah'=>$row['jumlah'],
				'satuan'=>$row['satuan'],
				'keterangan'=>$row['keterangan']
			);
		}
		
		// Delete Detail
		$qDel = deleteQuery($dbname,'vhc_servicedt',"notransaksi='".
			$param['notransaksi']."' ");
		
		// Insert New
		if(mysql_query($qDel)) {
			foreach($dataD as $d) {
				$query = insertQuery($dbname,'vhc_servicedt',$d);
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