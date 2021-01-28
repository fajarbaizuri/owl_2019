<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_GET['proses'];
$param = $_POST;

switch($proses) {
	case 'listRawat':
                        if(isset($param['thnBudget']) and $param['thnBudget']!='')
                            $thn=$param['thnBudget']; 
                        else 
                            $thn=date('Y');
		$query="select a.tahunbudget,a.kodeorg, a.kodebudget, b.namakegiatan, ".
			"a.volume, a.satuanv,a.rotasi,a.jumlah,a.satuanj,a.rupiah from ".$dbname.".bgt_budget a left join ".
			$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan where ".
			"tipebudget='ESTATE'  and kodebudget!='UMUM' ".
			" and b.kelompok in ('TBM','TM','PNN') 
                                                             and tahunbudget=".$thn." and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by a.tahunbudget desc limit 20";
                                        $resData = fetchData($query);
		$cols = array('Tahun Budget','Kode Blok','Kode Budget','Kegiatan','Volume','Satuan','Rotasi','Jumlah','Satuan','Rupiah');
		
		$res = "<table class='sortable' border=0 cellspacing=1 cellpadding=1>";
		$res .= "<thead><tr class=rowheader>";
		foreach($cols as $head) {
			$res .= "<td>".$head."</td>";
		}
		$res .= "</tr></thead>";
		
		$res .= "<tbody>";
		foreach($resData as $row) {
			$res .= "<tr class='rowcontent'>";
			foreach($row as $head=>$val) {
				if($head=='rupiah') {
					//$res .= "<td align=right>".number_format($val,2)."</td>";
					$res .= "<td align=right>".number_format($val,0)."</td>";
				} else {
					$res .= "<td>".$val."</td>";
				}
			}
			$res .= "</tr>";
		}
		$res .= "</tbody>";
		$res .= "</table>";
		
		echo $res;
		break;
	case 'saveRawat':
		## Validasi

		if($param['thnBudget']=='') {exit("Warning: Tahun Budget harus diisi");}
		if($param['tahuntanam']=='') {exit("Warning: Tahun Tanam harus diisi");}
		if($param['kegId']=='') {exit("Warning: Kegiatan harus dipilih");}
		
		## Get Blok List
		$query = "SELECT a.kodeblok,a.hathnini FROM `".$dbname."`.`bgt_blok` a LEFT JOIN `".
			$dbname."`.`setup_blok` b ON a.kodeblok=b.kodeorg WHERE left(a.kodeblok,6)='".$param['afdeling'].
			"' and b.tahuntanam=".$param['tahuntanam'];
		$resBlok = fetchData($query);
		if(empty($resBlok)) {
			exit("Warning: Tidak ada Blok pada Afdeling ".$param['afdeling'].
				" di tahun tanam ".$param['tahuntanam']);
		}
		
		## Get Norma
		$qNorma = selectQuery($dbname,'setup_kegiatannorma','*',
			"kodeorg='".$param['afdeling']."' and tahuntanam=".$param['tahuntanam'].
			" and kodekegiatan='".$param['kegId']."'");
		$resNorma = fetchData($qNorma);
		if(empty($resNorma)) {
			exit("Warning: Tidak ada Norma pada Afdeling ".$param['afdeling'].
				" di tahun tanam ".$param['tahuntanam']." untuk kode kegiatan ".$param['kegId']);
		}
		
		## Get Region
		$optRegion = makeOption($dbname,'bgt_regional_assignment','kodeunit,regional',
			"kodeunit = '".$_SESSION['empl']['lokasitugas']."'");
		
		## Get Barang
		$listBarang = '';
		foreach($resNorma as $norm) {
			if($norm['kodebarang']!=0) {
				if($listBarang!='') {
					$listBarang.=',';
				}
				$listBarang.="'".$norm['kodebarang']."'";
			}
		}
		if($listBarang=='') {
			$optBarang = array();
			$optBrgRp = array();
		} else {
			$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,satuan',
				"kodebarang in (".$listBarang.")");
			$optBrgRp = makeOption($dbname,'bgt_masterbarang','kodebarang,hargasatuan',
				"regional='".$optRegion[$_SESSION['empl']['lokasitugas']].
				"' and kodebarang in (".$listBarang.") and tahunbudget=".$param['thnBudget']);
		}
		
		## Get Upah
		$listGolongan = '';
		foreach($resNorma as $norm) {
			if($norm['kodebarang']!=0) {
				if($listGolongan!='') {
					$listGolongan.=',';
				}
				$listGolongan.="'".$norm['tipeanggaran']."'";
			}
		}
		$optUpah = makeOption($dbname,'bgt_upah','golongan,jumlah',
			"tahunbudget=".$param['thnBudget']." and kodeorg='".substr($param['afdeling'],0,4)."'");
		
		## Harga Kontrak, HM, KM
		$optKontrak = makeOption($dbname,'bgt_hargakontrakhm','tipe,hargasatuan',
			"tahunbudget=".$param['thnBudget']);
		
		## Harga VHC
		//$optVhc = makeOption($dbname,'bgt_biaya_ken_per_jam','tipe,hargasatuan',
		//	"tahunbudget=".$param['thnBudget']);
		//$asd=$param['kegId'];
		//exit("Error:$asd");
		
		
		#get produksi dari bgt_produksi
		
		
		
		$data=array();$i=0;
		foreach($resBlok as $row) {
			foreach($resNorma as $norm) {////jumlah di setup_kegiatannorma * hathnini di bgt_blok * rotasi thn ini (inputan) * persen (inputan)
				if(($param['kegId']=='611010101')||($param['kegId']=='611010103'))
				{
					$ha = selectQuery($dbname,'bgt_produksi_kbn_kg_vw','*',"kodeblok='".$row['kodeblok']."' and tahunbudget=".$param['thnBudget']." ");
					//exit("Error:$ha");
					$hi = fetchData($ha);
					if(empty($hi)) {
						exit("Warning: Tidak ada data budget produksi untuk blok ".$row['kodeblok']." di tahun budget ".$param['thnBudget']);
					}
					
					foreach($hi as $hu)
					{
						//$asd=$hu['kgsetahun'];
						//exit("Error:$asd");
						$jumlah = $norm['jumlah']*$hu['kgsetahun']*$param['rotThn']*$param['persen']/100;
						
					}	
				}
				else
				{
					$jumlah = $norm['jumlah']*$row['hathnini']*$param['rotThn']*$param['persen']/100;
				}
				if(substr($norm['tipeanggaran'],0,4)=='SDM-') {
					if(isset($optUpah[$norm['tipeanggaran']])) {
						$rupiah = $jumlah*$optUpah[$norm['tipeanggaran']];
					} else {
						$rupiah = 0;
					}
				} elseif(substr($norm['tipeanggaran'],0,2)=='M-') {
					if(isset($optBrgRp[$norm['kodebarang']])) {
						$rupiah = $jumlah*$optBrgRp[$norm['kodebarang']];
					} else {
						$rupiah = 0;
					}
				} else {
					$rupiah = 0;
				}

				$sat = explode('/',$norm['satuan']);
				$data[$i] = array(
					'tahunbudget'=>$param['thnBudget'],
					'kodeorg'=>$row['kodeblok'],
					'tipebudget'=>$param['tpBudget'],
					'kodebudget'=>$norm['tipeanggaran'],
					'kegiatan'=>$norm['kodekegiatan'],
					'noakun'=>$param['noAkun'],
					'volume'=>$row['hathnini']*$param['persen']/100,
					'satuanv'=>$sat[1],
					'satuanj'=>$sat[0],
					'rupiah'=>$rupiah,
					'kodebarang'=>$norm['kodebarang'],
					'rotasi'=>$param['rotThn'],
					'updateby'=>$_SESSION['standard']['userid'],
					'jumlah'=>$jumlah,
					'keterangan'=>'',
					'persentase'=>$param['persen']
					
				);	
				$cekdel[$i]= $row['kodeblok'];
				if($data[$i]['kodebarang']==0 or $data[$i]['kodebarang']=='') {
					unset($data[$i]['kodebarang']);
				}
				
				// If Tipe Anggaran VHC
				if($norm['tipeanggaran']=='VHC') {
					$data[$i]['kodevhc'] = $norm['kodebarang'];
					unset($data[$i]['kodebarang']);
				}
				
				// Case per Kode Budget
				//if($norm['tipeanggaran']=='KONTRAK') {
				//	$data[$i]['satuanj'] = $param['satuan'];
				//	if(isset($optKontrak[$norm['tipeanggaran']])) {
				//		$data[$i]['rupiah'] = $optKontrak[$norm['tipeanggaran']];
				//	} else {
				//		$data[$i]['rupiah'] = 0;
				//	}
				//} elseif($norm['tipeanggaran']=='KEND') {
				//	$data[$i]['satuanj'] = 'KM';
				//	$data[$i]['rupiah'] = $optKontrak[$norm['tipeanggaran']];
				//} elseif($norm['tipeanggaran']=='AB') {
				//	$data[$i]['satuanj'] = 'HM';
				//	$data[$i]['rupiah'] = $optKontrak[$norm['tipeanggaran']];
				//} elseif(substr($norm['tipeanggaran'],0,1)=='M') {
				//	if(isset($optBarang[$norm['kodebarang']])) {
				//		$data[$i]['satuanj'] = $optBarang[$norm['kodebarang']];
				//		$data[$i]['rupiah'] = $optBrgRp[$norm['kodebarang']];
				//	} else {
				//		$data[$i]['satuanj'] = '';
				//		$data[$i]['rupiah'] = '0';
				//	}
				//} elseif(substr($norm['tipeanggaran'],0,3)=='SDM') {
				//	$data[$i]['satuanj'] = 'HK';
				//	$data[$i]['rupiah'] = $optUpah[$norm['tipeanggaran']];
				//}
				
				$i++;
			}
		}
		//print_r($data);
		//exit('error');
		## Delete
		
		
		
		
		foreach($cekdel as $key1=>$row1) {
				$qDel = deleteQuery($dbname,'bgt_budget',"tahunbudget=".$param['thnBudget'].
			" and kodeorg like '".$row1."%' and kegiatan='".$param['kegId']."'");
				
		}
		
		if(mysql_query($qDel)) {
			## Insert
			$err='';
			foreach($data as $key=>$row) {
				$cols = array();
				foreach($row as $field=>$dat) {
					$cols[] = $field;
				}
				
				$qIns = insertQuery($dbname,'bgt_budget',$row,$cols);
				if(!mysql_query($qIns)) {
					$err .= "- ".mysql_error()."\n";
				}
			}
		} else {
			exit('DB Error:'.mysql_error());
		}
		break;
	default:
}
?>