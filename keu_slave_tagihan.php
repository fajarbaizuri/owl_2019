<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>

<?

$proses = $_GET['proses'];
$param = $_POST;

//cari nama orang
$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $nama[$bar->karyawanid]=$bar->namakaryawan;
}    

switch($proses) {
    # Daftar Header
    case 'showHeadList':
	$where = "unit='".$_SESSION['empl']['lokasitugas']."' ";
	if(isset($param['where'])) {
        $tmpW = str_replace('\\','',$param['where']);
	    $arrWhere = json_decode($tmpW,true);
	    if(!empty($arrWhere)) {
			foreach($arrWhere as $key=>$r1) {
				$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
			}
	    } 
	}
        
	# Header
	$header = array(
	    'No. Invoice','PT','Tanggal','Jatuh Tempo','Last Update','No PO','Keterangan','Sub Total','Unit'
	);
	
	# Content
	$cols = "noinvoice,kodeorg,tanggal,jatuhtempo,updateby,nopo,keterangan,nilaiinvoice,posting,unit";
        $order="tanggal desc";
	$query = selectQuery($dbname,'keu_tagihanht',$cols,$where,$order,false,$param['shows'],$param['page']);
   
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'keu_tagihanht',$where);
	foreach($data as $key=>$row) {
        if($row['posting']==1) {
			$data[$key]['switched']=true;
	    }
        unset($data[$key]['posting']);            
	    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		$data[$key]['jatuhtempo'] = tanggalnormal($row['jatuhtempo']);
	    $data[$key]['nilaiinvoice'] = number_format($row['nilaiinvoice'],2);
	    $data[$key]['updateby'] = $nama[$row['updateby']];
        //$data[$key]['postingby'] = $nama[$row['postingby']];
	}
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data);
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
    $tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
	$tHeader->_actions[3]->addAttr('event');
    $tHeader->_switchException = array('detailPDF');
	
	$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	if(isset($param['where'])) {
	    $tHeader->setWhere($arrWhere);
	}
	
	# View
	$tHeader->renderTable();
	break;
    # Form Add Header
    case 'showAdd':
	// View
	echo formHeader('add',array());
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Form Edit Header
    case 'showEdit':
	$query = selectQuery($dbname,'keu_tagihanht',"*","noinvoice='".$param['noinvoice']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	$data['jatuhtempo'] = tanggalnormal($data['jatuhtempo']);
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
		$data = $_POST;
		
		if($data['tipeinvoice']=='po') {
			$optPO = makeOption($dbname,'log_poht','nopo,kodesupplier',"stat_release=1");
		} else {
			$optPO = makeOption($dbname,'log_spkht','notransaksi,koderekanan');
		}
		#$optPO = makeOption($dbname,'log_poht','nopo,kodesupplier',"nopo='".$data['nopo']."'");
		// Error Trap
		$warning = "";
		if($data['noinvoice']=='') {$warning .= "No Tagihan harus diisi\n";}
		if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tipeinvoice'] = substr($data['tipeinvoice'],0,1);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['nilaiinvoice'] = 0;
		$data['uangmuka'] = str_replace(',','',$data['uangmuka']);
		$data['nilaippn'] = str_replace(',','',$data['nilaippn']);
		if($data['jatuhtempo']!='') {
			$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
		} else {
			$data['jatuhtempo'] = '0000-00-00';
		}
		$data['kodesupplier'] = $optPO[$data['nopo']];
		$data['updateby'] = $_SESSION['standard']['userid'];
		
		$cols = array();
		foreach($data as $key=>$row) {
			$cols[] = $key;
		}
		$query = insertQuery($dbname,'keu_tagihanht',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "noinvoice='".$data['noinvoice']."'";
	unset($data['noinvoice']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['jatuhtempo'] = tanggalsystem($data['jatuhtempo']);
	$data['tipeinvoice'] = substr($data['tipeinvoice'],0,1);
	//$data['nilaiinvoice'] = str_replace(',','',$data['nilaiinvoice']);
	$data['uangmuka'] = str_replace(',','',$data['uangmuka']);
	$data['nilaippn'] = str_replace(',','',$data['nilaippn']);
		$data['updateby'] = $_SESSION['standard']['userid'];
	
	$query = updateQuery($dbname,'keu_tagihanht',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "noinvoice='".$param['noinvoice']."'";
	$query = "delete from `".$dbname."`.`keu_tagihanht` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    case 'updpo':
	$pokontrak = $_POST['pokontrak'];
	if($pokontrak=='po') {
	    $resPO = makeOption($dbname,'log_poht','nopo,nopo',"stat_release=1",'0',true);
	} else {
	    $resPO = makeOption($dbname,'log_spkht','notransaksi,notransaksi',
		"kodeorg='".$_SESSION['empl']['lokasitugas']."'",'0',true);
	}
	
	echo json_encode($resPO);
	break;
    case 'updInvoice':
	# Check existing PO
	$query = selectQuery($dbname,'keu_tagihanht','nilaiinvoice',"nopo='".$_POST['nopo']."'");
	$res = fetchData($query);
	if(!empty($res)) {
	    echo $res[0]['nilaiinvoice'];
	}
	break;
	case'getPo':
		$optNmsupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
        $dat="<fieldset><legend>".$_SESSION['lang']['result']."</legend>";
        $dat.="<div style=overflow:auto;width:100%;height:310px;>";
        $dat.="<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat.="<tr class='rowheader'><td>No.</td>";
        $dat.="<td>".$_SESSION['lang']['nopo']."</td>";
		$dat.="<td>".$_SESSION['lang']['namasupplier']."</td>";
        $dat.="<td>Realisasi (Rp)</td></tr></thead><tbody>";
        if($param['jnsInvoice']=='po')
        {
            if($param['txtfind']!='') {
                $where=" and a.nopo like '%".$param['txtfind']."%'";
            } else {
				$where="";
			}
			//print_r($_SESSION['empl']);
			
			/*if ($_SESSION['empl']['tipelokasitugas']==HOLDING)
			{
				$sPo="SELECT DISTINCT a.nopo,SUM(b.hargasatuan*jumlah) AS nilaipo,a.kodesupplier FROM ".
				//$sPo="SELECT DISTINCT a.nopo,SUM(b.hartot*jumlah) AS nilaipo,a.kodesupplier FROM ".
				$dbname.".log_poht a "."LEFT JOIN ".$dbname.".log_transaksi_vw b ON a.nopo=b.nopo ".
				"WHERE stat_release=1 AND lokalpusat in ('0','1') ".$where." AND kodeorg='".
				$_SESSION['org']['kodeorganisasi']."'  GROUP BY a.nopo,a.kodesupplier having nilaipo>0 ORDER BY a.tanggal desc";
			}
			else
			{
				if($_SESSION['empl']['lokasitugas']==TDAE)
				{
					$sPo="SELECT DISTINCT a.nopo,SUM(b.hargasatuan*jumlah) AS nilaipo,a.kodesupplier FROM ".
					$dbname.".log_poht a "."LEFT JOIN ".$dbname.".log_transaksi_vw b ON a.nopo=b.nopo ".
					"WHERE stat_release=1 AND lokalpusat in ('0','1') ".$where." AND kodeorg='".
					$_SESSION['org']['kodeorganisasi']."' and b.kodegudang in('TDAE60','TKFB22') GROUP BY a.nopo,a.kodesupplier having nilaipo>0 ORDER BY a.tanggal desc";
				}
				else
				{
					$sPo="SELECT DISTINCT a.nopo,SUM(b.hargasatuan*jumlah) AS nilaipo,a.kodesupplier FROM ".
					$dbname.".log_poht a "."LEFT JOIN ".$dbname.".log_transaksi_vw b ON a.nopo=b.nopo ".
					"WHERE stat_release=1 AND lokalpusat in ('0','1') ".$where." AND kodeorg='".
					$_SESSION['org']['kodeorganisasi']."' and b.kodegudang like '%".$_SESSION['empl']['lokasitugas']."%' GROUP BY a.nopo,a.kodesupplier having nilaipo>0 ORDER BY a.tanggal desc";
				}
				//echo $sPo;
			}*/
			
			
			
			if ($_SESSION['empl']['tipelokasitugas']==HOLDING)
			{
				$sPo="SELECT DISTINCT a.nopo,SUM(b.hargasatuan*jumlah) AS nilaipo,a.kodesupplier FROM ".
				//$sPo="SELECT DISTINCT a.nopo,SUM(b.hartot*jumlah) AS nilaipo,a.kodesupplier FROM ".
				$dbname.".log_poht a "."LEFT JOIN ".$dbname.".log_transaksi_vw b ON a.nopo=b.nopo ".
				"WHERE stat_release=1 AND lokalpusat='0'  ".$where." AND kodeorg='".
				$_SESSION['org']['kodeorganisasi']."'  GROUP BY a.nopo,a.kodesupplier having nilaipo>0 ORDER BY a.tanggal desc";
			}
			else
			{
				if($_SESSION['empl']['lokasitugas']==TDAE)
				{
					
					$sPo="SELECT DISTINCT a.nopo,SUM(b.hargasatuan*jumlah) AS nilaipo,a.kodesupplier 
					FROM ".$dbname.".log_poht a 
					"."LEFT JOIN ".$dbname.".log_transaksi_vw b 
					ON a.nopo=b.nopo ".
					"WHERE stat_release=1 AND lokalpusat='1' ".$where." 
					AND kodeorg='".$_SESSION['org']['kodeorganisasi']."' and b.kodegudang in('TDAE60','TKFB22') 
					GROUP BY a.nopo,a.kodesupplier having nilaipo>0 ORDER BY a.tanggal desc";
				}
				else
				{
					$sPo="SELECT DISTINCT a.nopo,SUM(b.hargasatuan*jumlah) AS nilaipo,a.kodesupplier FROM ".
					$dbname.".log_poht a "."LEFT JOIN ".$dbname.".log_transaksi_vw b ON a.nopo=b.nopo ".
					"WHERE stat_release=1 AND lokalpusat='1' ".$where."  AND kodeorg='".
					$_SESSION['org']['kodeorganisasi']."' and b.kodegudang like '%".$_SESSION['empl']['lokasitugas']."%' GROUP BY a.nopo,a.kodesupplier having nilaipo>0 ORDER BY a.tanggal desc";
				}
				//echo $sPo;
			}
			
			
			
				//echo $sPo;
        }
        else
        {
            if($param['txtfind']!='') {
                $where=" and a.notransaksi like '%".$param['txtfind']."%'";
            } else {
				$where="";
			}
			//$sPo="select distinct notransaksi as nopo,nilaikontrak as nilaipo,koderekanan as kodesupplier from ".$dbname.".log_spkht where kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."')  ".$where."  order by tanggal desc";
			$sPo="SELECT DISTINCT a.notransaksi AS nopo,SUM(b.jumlahrealisasi) AS nilaipo".
				",a.koderekanan AS kodesupplier FROM ".$dbname.".log_spkht a LEFT JOIN ".$dbname.".log_baspk b ".
				"ON a.notransaksi=b.notransaksi WHERE kodeorg IN (SELECT DISTINCT kodeorganisasi ".
				"FROM ".$dbname.".organisasi WHERE induk='".$_SESSION['org']['kodeorganisasi']."') ".
				$where." GROUP BY a.notransaksi,a.koderekanan HAVING nilaipo>0 ORDER BY a.tanggal desc";
				//echo $sPo;
        }
		
		$qPo = fetchData($sPo);$no=0;
        foreach ($qPo as $rPo) {
            $no+=1;
            $dat.="<tr class='rowcontent' onclick=\"setPo('".$rPo['nopo']."','".$rPo['nilaipo']."','".$param['jnsInvoice']."')\" style='pointer:cursor;'><td>".$no."</td>";
            $dat.="<td>".$rPo['nopo']."</td>";
            $dat.="<td>".$optNmsupp[$rPo['kodesupplier']]."</td>";
			$dat.="<td align='right'>".number_format($rPo['nilaipo'])."</td></tr>";
        }
        $dat.="</tbody></table></div></fieldset>";
        echo $dat;
        break;
	case 'posting':
		// Update Header
		$data = array('posting'=>1);
		$where = "noinvoice='".$param['noinvoice']."'";
		$qUpd = updateQuery($dbname,'keu_tagihanht',$data,$where);
		if(!mysql_query($qUpd)) {
			exit("DB Error: ".mysql_error());
		}
		break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$data['noinvoice'] = date('Ymdhis');
	$data['nilaiinvoice'] = '0';
	$data['noakun'] = '';
	$data['tanggal'] = '';
	$data['tipeinvoice'] = 'po';
	$data['nopo'] = '';
	$data['jatuhtempo'] = '';
	$data['nofp'] = '';
	$data['keterangan'] = '';
	$data['uangmuka'] = '0';
	$data['nilaippn'] = '0';
	$data['kodeorg'] = '';
	
    } else {
	$data['nilaiinvoice'] = number_format($data['nilaiinvoice'],0);
	$data['uangmuka'] = number_format($data['uangmuka'],0);
	$data['nilaippn'] = number_format($data['nilaippn'],0);
	$tmpNopo = explode('/',$data['nopo']);
	if(count($tmpNopo)>5 and $tmpNopo[3]=='PO') {
	    $data['tipeinvoice']='po';
	} else {
	    $data['tipeinvoice']='kontrak';
	}
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
	$optUnit = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',"kasbank=1 and detail=1");
    if($data['tipeinvoice']=='po') {
	$optPO = makeOption($dbname,'log_poht','nopo,nopo',"stat_release=1",'0',true);
    } else {
	$optPO = makeOption($dbname,'log_spkht','notransaksi,notransaksi',null,'0',true);
    }
    $optCgt = getEnum($dbname,'keu_kasbankht','cgttu');
    $optYn = array(0=>'Belum Posting',1=>'Sudah Posting');
    
    $els = array();
    $els[] = array(
	makeElement('noinvoice','label','Nota Tagihan'),
	makeElement('noinvoice','text',$data['noinvoice'],
	    array('style'=>'width:150px','maxlength'=>'20',$disabled=>$disabled))
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px'),$optOrg)
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('tipeinvoice','label',$_SESSION['lang']['jenis']),
	makeElement('tipeinvoice','select',$data['tipeinvoice'],
	    array('style'=>'width:150px',$disabled=>$disabled,'onchange'=>'updPO()'),
	    array('po'=>'PO','kontrak'=>'Kontrak'))
    );
//    $els[] = array(
//	makeElement('nopo','label',$_SESSION['lang']['nopo']),
//	makeElement('nopo','select',$data['nopo'],
//	    array('style'=>'width:150px','onchange'=>'updInvoice()',$disabled=>$disabled),$optPO)
//    );
    $els[] = array(
	makeElement('nopo','label',$_SESSION['lang']['nopo']),
	makeElement('nopo','text',$data['nopo'],array('style'=>'width:150px',$disabled=>$disabled,'onclick'=>"searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."</legend>".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findNopo()>Find</button></fieldset><div id=container2></div>',event)"))
    );
    $els[] = array(
	makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
	makeElement('keterangan','text',$data['keterangan'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('jatuhtempo','label',$_SESSION['lang']['jatuhtempo']),
	makeElement('jatuhtempo','text',$data['jatuhtempo'],
	    array('style'=>'width:150px','readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('nofp','label',$_SESSION['lang']['nofp']),
	makeElement('nofp','text',$data['nofp'],
	    array('style'=>'width:150px','maxlength'=>'50'))
    );
//    $els[] = array(
//	makeElement('nilaiinvoice','label',$_SESSION['lang']['nilaiinvoice']),
//	makeElement('nilaiinvoice','textnum',$data['nilaiinvoice'],
//	    array('style'=>'width:150px','disabled'=>'disabled','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
//    );
    $els[] = array(
	makeElement('noakun','label',$_SESSION['lang']['noakun']),
	makeElement('noakun','select',$data['noakun'],
	    array('style'=>'width:150px'),$optAkun)
    );
    $els[] = array(
	makeElement('uangmuka','label',$_SESSION['lang']['uangmuka']),
	makeElement('uangmuka','textnum',$data['uangmuka'],
	    array('style'=>'width:150px','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );
//    if($mode!='edit')
//    {
//     $els[] =array(
//       makeElement('gmbrCari','gambar','images/search.png',
//	    array('title'=>'Cari PO/Kontrak','onclick'=>"searchNopo('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['nopo']."</legend>".$_SESSION['lang']['find']."<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findNopo()>Find</button></fieldset><div id=container2></div>',event)"))  
//    );
//    }
    $els[] = array(
	makeElement('nilaippn','label',$_SESSION['lang']['nilaippn']),
	makeElement('nilaippn','textnum',$data['nilaippn'],
	    array('style'=>'width:150px','onchange'=>'this.value=remove_comma(this);this.value = _formatted(this)'))
    );
	
	
	$els[] = array(
	makeElement('unit','label','Unit'),
	makeElement('unit','select',$data['unit'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optUnit)
    );
	
	############
	
	
    
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable()"))
	);
    } elseif($mode=='edit') {
	$els['btn'] = array(
	    makeElement('editHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"editDataTable()"))
	);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>