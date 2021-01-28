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
	
	
		
		$limit=30;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		


		
		$ql2="select count(*) as jmlhrow from ".$dbname.".bgt_budget a left join ".
			$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan where ".
			"tipebudget='ESTATE'  and kodebudget!='UMUM' ".
			" and b.kelompok in ('TBM','TM','PNN') 
            and tahunbudget=".$thn." and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by a.tahunbudget desc ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}					
							
							
							
							
							
		$query="select a.tahunbudget,a.kodeorg, a.kodebudget, b.namakegiatan, ".
			"a.volume, a.satuanv,a.rotasi,a.jumlah,a.satuanj,a.rupiah from ".$dbname.".bgt_budget a left join ".
			$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan where ".
			"tipebudget='ESTATE'  and kodebudget!='UMUM' ".
			" and b.kelompok in ('TBM','TM','PNN') 
            and tahunbudget=".$thn." and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by a.tahunbudget desc limit ".$offset.",".$limit."";
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
		
		
		$res.="
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBastListRawat(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBastListRawat(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		
		$res .= "</tbody>";
		$res .= "</table>";
		
		
		
		
		echo $res;
		break;
	case 'listLain':
                        if(isset($param['thnBudget']) and $param['thnBudget']!='')
                            $thn=$param['thnBudget']; 
                        else 
                            $thn=date('Y');
	
	
		
		$limit=30;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		


		
		$ql2="select count(*) as jmlhrow from ".$dbname.".bgt_budget a left join ".
			$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan where ".
			"tipebudget='ESTATE'  and kodebudget='OTHER' ".
			" and b.kelompok in ('TBM','TM','PNN') 
            and tahunbudget=".$thn." and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by a.tahunbudget desc ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}					
							
							
							
							
							
		$query="select a.tahunbudget,a.kodeorg, a.kodebudget, b.namakegiatan, ".
			"a.volume, a.satuanv,a.rotasi,a.jumlah,a.satuanj,a.rupiah from ".$dbname.".bgt_budget a left join ".
			$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan where ".
			"tipebudget='ESTATE'  and kodebudget='OTHER' ".
			" and b.kelompok in ('TBM','TM','PNN') 
            and tahunbudget=".$thn." and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by a.tahunbudget desc limit ".$offset.",".$limit."";
                                        $resData = fetchData($query);
		$cols = array('Tahun Budget','Kode Blok','Kode Budget','Kegiatan','Volume','Satuan','Rotasi','Jumlah','Satuan','Harga Satuan');
		 
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
		
		
		$res.="
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBastListRawat(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBastListRawat(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		
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
		$query = "SELECT a.kodeblok,a.hathnini,a.pokokthnini FROM `".$dbname."`.`bgt_blok` a LEFT JOIN `".
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
		//exit("Error:".$listBarang);
		//exit("Error:".$param['thnBudget']);
		if($listBarang=='') {
			$optBarang = array();
			$optBrgRp = array();
		} else {
			$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,satuan',
				"kodebarang in (".$listBarang.")");
			$optBrgRp = makeOption($dbname,'bgt_masterbarang','kodebarang,hargasatuan',
				"regional='".$optRegion[$_SESSION['empl']['lokasitugas']].
				"' and kodebarang in (".$listBarang.") and tahunbudget='".$param['thnBudget']."'");
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
			
				
if(($param['kegId']=='611010101')||($param['kegId']=='611010102')||($param['kegId']=='611020101')||($param['kegId']=='611020201')||($param['kegId']=='611090201')||($param['kegId']=='611090202')||($param['kegId']=='123041601')||($param['kegId']=='123041602')||($param['kegId']=='124041601')||($param['kegId']=='124041602')||($param['kegId']=='125041601')||($param['kegId']=='125041602')||($param['kegId']=='621061901')||($param['kegId']=='621061902')||($param['kegId']=='611010301')||($param['kegId']=='611020102')||
($param['kegId']=='621061501')||($param['kegId']=='621061502')||($param['kegId']=='621061604'))

				//if(($param['kegId']=='611010101')||($param['kegId']=='611010103'))
				//{ 
					$sat = explode('/',$norm['satuan']);
					if ($sat[1] == 'Kg' || $sat[1] == 'Ha'){
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
						if ($sat[1] == 'Kg'){   
						$jumlah = $norm['jumlah']*$hu['kgsetahun']*$param['rotThn']*$param['persen']/100;
						}else{
						$jumlah = $norm['jumlah']*$hu['luas']*$param['rotThn']*$param['persen']/100;
						}
						  
					}	
					//exit("Error:$jumlah"); 
					//$rupiah = $jumlah*$optUpah[$norm['tipeanggaran']];
					}else if ($sat[1] == 'Pkk'){
						$jumlah = $norm['jumlah']*$row['pokokthnini']*$param['rotThn']*$param['persen']/100;
					}else{
						$jumlah = $norm['jumlah']*$row['hathnini']*$param['rotThn']*$param['persen']/100;
					}
					
				//}  
				//else
				//{
					/*
					$sat = explode('/',$norm['satuan']);
					
					if ($sat[1] == 'Pkk'){
						$jumlah = $norm['jumlah']*$row['pokokthnini']*$param['rotThn']*$param['persen']/100;
					}else{
						$jumlah = $norm['jumlah']*$row['hathnini']*$param['rotThn']*$param['persen']/100;
					}
					*/
					
					 
				//} 
				
				
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
				if ($sat[1] == 'Pkk'){
					$Hss=$row['pokokthnini'];
				}else if ($sat[1] == 'Kg'){ 
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
						$Hss = $hu['kgsetahun'];
					}	
				}else{
					$Hss=$row['hathnini'];
				}
				//exit("Error:$sat[1]");
				
				$data[$i] = array(
					'tahunbudget'=>$param['thnBudget'],
					'kodeorg'=>$row['kodeblok'],
					'tipebudget'=>$param['tpBudget'],
					'kodebudget'=>$norm['tipeanggaran'],
					'kegiatan'=>$norm['kodekegiatan'],
					'noakun'=>$param['noAkun'],
					'volume'=>$Hss*$param['persen']/100,
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
		case 'saveLain':
		## Validasi
		/*
		if($param['thnBudget']=='') {exit("Warning: Tahun Budget harus diisi");}
		if($param['tahuntanam']=='') {exit("Warning: Tahun Tanam harus diisi");}
		if($param['kegId']=='') {exit("Warning: Kegiatan harus dipilih");}
		if($param['volKeg']=='') {exit("Warning: Volume Kegiatan harus dipilih");}
		if($param['HSatuan']=='') {exit("Warning: Harga Satuan harus dipilih");}	
		  
		$ql2="insert into ".$dbname.".bgt_budget(tahunbudget,kodeorg,tipebudget,kodebudget,kegiatan,noakun,volume,satuanv,satuanj,rupiah,rotasi,updateby,jumlah,keterangan,persentase)values('".$param['thnBudget']."','".$param['afdeling']."','".$param['tpBudget']."','OTHER','".$param['kegId']."','".$param['noAkun']."','".$param['volKeg']*($param['persen']/100)."','".$param['satuan']."','".$param['satuan']."','".$param['Total']."','".$param['rotThn']."','".$_SESSION['standard']['userid']."','".$param['HSatuan']."','','".$param['persen']."');";
		//echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error()); 
		*/
		## DARI SINI
		## Validasi
		if($param['thnBudget']=='') {exit("Warning: Tahun Budget harus diisi");}
		if($param['tahuntanam']=='') {exit("Warning: Tahun Tanam harus diisi");}
		if($param['kegId']=='') {exit("Warning: Kegiatan harus dipilih");}
		if($param['volKeg']=='') {exit("Warning: Volume Kegiatan harus dipilih");}
		if($param['HSatuan']=='') {exit("Warning: Harga Satuan harus dipilih");}
		

		## Get Blok List
		$query = "SELECT a.kodeblok,a.hathnini,a.pokokthnini FROM `".$dbname."`.`bgt_blok` a LEFT JOIN `".
			$dbname."`.`setup_blok` b ON a.kodeblok=b.kodeorg WHERE left(a.kodeblok,6)='".$param['afdeling'].
			"' and b.tahuntanam=".$param['tahuntanam'];
		$resBlok = fetchData($query);
		if(empty($resBlok)) {
			exit("Warning: Tidak ada Blok pada Afdeling ".$param['afdeling'].
				" di tahun tanam ".$param['tahuntanam']);
		}
		
		
		$optRegion = makeOption($dbname,'bgt_regional_assignment','kodeunit,regional',
			"kodeunit = '".$_SESSION['empl']['lokasitugas']."'");
		
		$optUpah = makeOption($dbname,'bgt_upah','golongan,jumlah',
			"tahunbudget=".$param['thnBudget']." and kodeorg='".substr($param['afdeling'],0,4)."'");
		
		$optKontrak = makeOption($dbname,'bgt_hargakontrakhm','tipe,hargasatuan',
			"tahunbudget=".$param['thnBudget']);
		
		$data=array();$i=0;
		foreach($resBlok as $row) {




/*
SELECT tahunbudget, kodeunit, left(kodeblok,6) as afd, thntnm, sum(bjr) as bjr,
sum(pokokproduksi) as pokokproduksi, sum(luas) as luas, sum(kgsetahun) as kgsetahun,sum(kg01) as kg01
,sum(kg02) as kg02,sum(kg05) as kg05,sum(kg07) as kg07,sum(kg09) as kg09,sum(kg11) as kg11
,sum(kg03) as kg03,sum(kg06) as kg06,sum(kg08) as kg08,sum(kg10) as kg10,sum(kg12) as kg12
,sum(kg04) as kg04
FROM  `bgt_produksi_kbn_kg_vw`  where left(kodeblok,6)='TDAE01' and thntnm ='1992' group by tahunbudget,kodeunit,left(kodeblok,6),thntnm

SELECT left(kodeblok,6) as afd,sum(a.hathnini) as hathnini, sum(a.pokokthnini) as pokokthnini  FROM `".$dbname."`.`bgt_blok` a LEFT JOIN `".$dbname."`.`setup_blok` b ON a.kodeblok=b.kodeorg WHERE left(a.kodeblok,6)='".substr($row['kodeblok'], 6)."' and b.tahuntanam='".$param['tahuntanam']."' group by left(kodeblok,6)

*/

					
					//proporsi
					$proha1 = "SELECT left(kodeblok,6) as afd,sum(a.hathnini) as hathnini, sum(a.pokokthnini) as pokokthnini  FROM `".$dbname."`.`bgt_blok` a LEFT JOIN `".$dbname."`.`setup_blok` b ON a.kodeblok=b.kodeorg WHERE left(a.kodeblok,6)='".substr($row['kodeblok'],0, 6)."' and b.tahuntanam='".$param['tahuntanam']."' group by left(kodeblok,6)";
					//exit("Error:$proha1");
					$prohi1 = fetchData($proha1);
					if(empty($prohi1)) {
							$proporB = 0;
					}else{
						foreach($prohi1 as $prohu1)
						{
							if ($sat[1] == 'Pkk'){   
								$proporB = $prohu1['pokokthnini'];
							}else{
								$proporB = $prohu1['hathnini'];
							}
						}
					}
					
					$sat = explode('/',$param['satuan']);
					if ($sat[1] == 'Kg' || $sat[1] == 'Ha' || $sat[1] == 'HA' || $sat[1] == 'KG' || $sat[1] == 'ha' || $sat[1] == 'kg'){
					$ha = selectQuery($dbname,'bgt_produksi_kbn_kg_vw','*',"kodeblok='".$row['kodeblok']."' and tahunbudget=".$param['thnBudget']." and  thntnm ='".$param['tahuntanam']."'");
					//exit("Error:$ha");
					$hi = fetchData($ha);
					if(empty($hi)) {
						exit("Warning: Tidak ada data budget produksi untuk blok ".$row['kodeblok']." di tahun budget ".$param['thnBudget']); 
					}
					
					//proporsi
					
					$proha = "SELECT tahunbudget, kodeunit, left(kodeblok,6) as afd, thntnm, sum(bjr) as bjr,
sum(pokokproduksi) as pokokproduksi, sum(luas) as luas, sum(kgsetahun) as kgsetahun,sum(kg01) as kg01
,sum(kg02) as kg02,sum(kg05) as kg05,sum(kg07) as kg07,sum(kg09) as kg09,sum(kg11) as kg11
,sum(kg03) as kg03,sum(kg06) as kg06,sum(kg08) as kg08,sum(kg10) as kg10,sum(kg12) as kg12
,sum(kg04) as kg04
FROM  `".$dbname."`.`bgt_produksi_kbn_kg_vw`  where left(kodeblok,6)='".substr($row['kodeblok'], 0,6)."' and tahunbudget=".$param['thnBudget']." and  thntnm ='".$param['tahuntanam']."' group by tahunbudget,kodeunit,left(kodeblok,6),thntnm";
 
					   
					$prohi = fetchData($proha);
					if(empty($prohi)) {
							$proporA = 0;
					}else{
						foreach($prohi as $prohu)
						{
							if ($sat[1] == 'Kg'){   
								$proporA = $prohu['kgsetahun'];
							}else{
								$proporA = $prohu['luas'];
							}
						}
					}
					
					
					foreach($hi as $hu)
					{
						if ($sat[1] == 'Kg'){   
							/*
							$jumlah = ($param['volKeg']*($hu['kgsetahun']/$proporA))*$param['rotThn']*$param['persen']/100;
							$volume=($param['volKeg']*($hu['kgsetahun']/$proporA))*$param['persen']/100;
							*/
							$volume = ($param['volKeg']*($hu['kgsetahun']/$proporA))*$param['rotThn']*$param['persen']/100;
							$jumlah=1;
						}else{
							/*
							$jumlah = ($param['volKeg']*($hu['luas']/$proporA))*$param['rotThn']*$param['persen']/100;
							$volume=($param['volKeg']*($hu['luas']/$proporA))*$param['persen']/100;
							*/
							$volume = ($param['volKeg']*($hu['luas']/$proporA))*$param['rotThn']*$param['persen']/100;
							$jumlah=1;
						}
					}	
					
						
					
					}else if ($sat[1] == 'Pkk' || $sat[1] == 'PKK'){
						/*
						$jumlah = ($param['volKeg']*($row['pokokthnini']/$proporB))*$param['rotThn']*$param['persen']/100;
						$volume = ($param['volKeg']*($row['pokokthnini']/$proporB))*$param['persen']/100;
						*/
						$volume = ($param['volKeg']*($row['pokokthnini']/$proporB))*$param['rotThn']*$param['persen']/100;
						$jumlah = 1;
						
					}else{
						/* 
						$jumlah = ($param['volKeg']*($row['hathnini']/$proporB))*$param['rotThn']*$param['persen']/100;
						$volume = ($param['volKeg']*($row['hathnini']/$proporB))*$param['persen']/100;
						*/
						$volume = ($param['volKeg']*($row['hathnini']/$proporB))*$param['rotThn']*$param['persen']/100;
						$jumlah = 1;
						
						
					}
					
				
						if ($sat[0] == 'LS' || $sat[0] == 'ls' || $sat[0] == 'Ls')
						{
							$rupiah = ($volume/$param['volKeg'])*$param['Total'];
						}else{
							$rupiah = $volume*$optUpah["SDM-BHL"];
						}
				
				
				
				//$rupiah = $volume*$optUpah["SDM-BHL"];
				//$rupiah = $volume;
				//exit("Error:$sat[1]");
				 
				$data[$i] = array( 
					'tahunbudget'=>$param['thnBudget'], 
					'kodeorg'=>$row['kodeblok'],
					'tipebudget'=>$param['tpBudget'],
					'kodebudget'=>'OTHER',
					'kegiatan'=>$param['kegId'],
					'noakun'=>$param['noAkun'],
					'volume'=>$volume,
					'satuanv'=>$sat[1],
					'satuanj'=>$sat[0],
					'rupiah'=>$rupiah,
					'kodebarang'=>'',
					'rotasi'=>$param['rotThn'],
					'updateby'=>$_SESSION['standard']['userid'],
					'jumlah'=>0,
					'keterangan'=>'',
					'persentase'=>$param['persen']
					  
				);	
				$cekdel[$i]= $row['kodeblok'];
				if($data[$i]['kodebarang']==0 or $data[$i]['kodebarang']=='') {
					unset($data[$i]['kodebarang']);
				}
				
				// If Tipe Anggaran VHC
				/*
				if($norm['tipeanggaran']=='VHC') {
					$data[$i]['kodevhc'] = $norm['kodebarang'];
					unset($data[$i]['kodebarang']);
				}
				*/
				
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
			//}
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