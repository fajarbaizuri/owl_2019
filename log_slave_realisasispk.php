<?//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    # Daftar Header
    case 'showHeadList':
                if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
                        $_SESSION['empl']['tipelokasitugas']=='HOLDING' or
                        $_SESSION['empl']['tipelokasitugas']=='KANWIL') {
                        $where = "length(kodeorg)=4";
                } else {
                        $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
                }
                if(isset($param['where'])) {
                        $arrWhere =json_decode(str_replace('\\','',$param['where']),true);
                        if(!empty($arrWhere)) {
                        foreach($arrWhere as $key=>$r1) {
                                $where .= " and ".$r1[0]." like '%".$r1[1]."%'";
                        }
                        }
                }

                # Header
                $header = array(
                        $_SESSION['lang']['kebun'],
                        $_SESSION['lang']['notransaksi'],
                        $_SESSION['lang']['tanggal'],
                        $_SESSION['lang']['subunit'],
                        $_SESSION['lang']['koderekanan'],
                        $_SESSION['lang']['nilaikontrak'],
                        $_SESSION['lang']['jumlahrealisasi']
                );

                # Content
                $cols = "kodeorg,notransaksi,tanggal,divisi,koderekanan,nilaikontrak";
                $query = selectQuery($dbname,'log_spkht',$cols,$where." and posting=1 order by tanggal desc","",false,$param['shows'],$param['page']);
                $data = fetchData($query);
                $totalRow = getTotalRow($dbname,'log_spkht',$where);
                foreach($data as $key=>$row) {
                        $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
                        $data[$key]['nilaikontrak'] = number_format($row['nilaikontrak']);
                        //=================ambil realisasi
                        $data[$key]['realisasi'] =0;
                        $strx="select jumlahrealisasi,statusjurnal from ".$dbname.".log_baspk 
                                  where notransaksi='".$data[$key]['notransaksi']."'";
                        $resx=fetchData($strx);
                        $posted=1;
                        foreach($resx as $row) {
                                $data[$key]['realisasi']+= $row['jumlahrealisasi'];
                                if($row['statusjurnal']==0) {
                                        $posted=0;
                                }
                        }
                        $data[$key]['realisasi']=number_format($data[$key]['realisasi']);

                        if($posted==1) {
                                $data[$key]['switched'] = 1;
                        }
                }

                # Options
                if(!empty($data)) {
                        $whereSupp = "supplierid in (";
                        foreach($data as $key=>$row) {
                          if($key==0) {
                        $whereSupp .= "'".$row['koderekanan']."'";
                          } else {
                        $whereSupp .= ",'".$row['koderekanan']."'";
                          }
                        }
                        $whereSupp .= ")";
                } else {
                        $whereSupp = null;
                }
                $optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',
                        $whereSupp);

                # Data Show
                $dataShow = $data;
                foreach($dataShow as $key=>$row) {
                        $dataShow[$key]['koderekanan'] = $optSupp[$row['koderekanan']];
                }

                # Make Table
                $tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
                $tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
                #$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
                $tHeader->addAction('posting','Posting','images/'.$_SESSION['theme']."/posting.png");
                $tHeader->_actions[1]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
                $tHeader->_actions[1]->addAttr($_SESSION['theme']);
                $tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
                $tHeader->_actions[2]->addAttr('event');
                $tHeader->_switchException = array('detailPDF','showEdit');
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
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."'";
                $query = selectQuery($dbname,'log_spkht',"*",$where);
        $tmpData = fetchData($query);
                $data = $tmpData[0];
                $data['tanggal'] = tanggalnormal($data['tanggal']);
                echo formHeader('edit',$data);
                echo "<div id='detailField' style='clear:both'></div>";
                break;
    # Proses Add Header
    case 'add':
                $data = $_POST;

                // Error Trap
                $warning = "";
                if($data['notransaksi']=='') {$warning .= "No SPK harus diisi\n";}
                if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
                if($warning!=''){echo "Warning :\n".$warning;exit;}

                $data['tanggal'] = tanggalsystem($data['tanggal']);
                $cols = array('kodeorg','notransaksi','tanggal','divisi',
                        'koderekanan');
                $query = insertQuery($dbname,'log_spkht',$data,$cols);

                if(!mysql_query($query)) {
                        echo "DB Error : ".mysql_error();
                }
                break;
    # Proses Edit Header
    case 'edit':
                $data = $_POST;
                $where = "nopengolahan='".$data['nopengolahan']."'";
                unset($data['nopengolahan']);
                $data['tanggal'] = tanggalsystem($data['tanggal']);
                $query = updateQuery($dbname,'log_spkht',$data,$where);
                if(!mysql_query($query)) {
                        echo "DB Error : ".mysql_error();
                }
                break;
    case 'delete':
                $where = "notransaksi='".$param['notransaksi']."'";
                $query = "delete from `".$dbname."`.`log_spkht` where ".$where;
                if(!mysql_query($query)) {
                        echo "DB Error : ".mysql_error();
                        exit;
                }
                break;
        case 'posting':
                $query = selectQuery($dbname,'log_baspk',"*","notransaksi='".
                        $param['notransaksi']."' and kodeblok like '%".$param['kodeorg']."%'");
                $resData = fetchData($query);

                foreach($resData as $d) {
                        if($d['statusjurnal']=='0') {
                                $_POST = $param;
                                foreach($d as $key=>$row) {
                                        $_POST[$key] = $row;
                                }
                                $_POST['blokalokasi'] = $d['kodeblok'];
                                $_POST['tanggal'] = tanggalnormal($d['tanggal']);
                                include('log_slave_realisasispk_posting.php');
                        }
                }
                break;
    default:
        break;
}

function formHeader($mode,$data) {
    global $dbname;
    //print_r($data);
    //exit("Error");

    # Default Value
    if(empty($data)) {
        $data['kodeorg'] = '';
        $data['notransaksi'] = '0';
        $data['tanggal'] = '';
        $data['divisi'] = '';
        $data['koderekanan'] = '';
    }

    # Disabled Primary
    if($mode=='edit') {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    # Options
    $whereOrg = "kodeorganisasi='".$data['kodeorg']."'";
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg);
    $whereDiv = "kodeorganisasi='".$data['divisi']."'";
    $optDiv = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereDiv);
    $optSup = makeOption($dbname,'log_5supplier','supplierid,namasupplier',"supplierid='".$data['koderekanan']."'");

    $els = array();
    $els[] = array(
        makeElement('kodeorg','label',$_SESSION['lang']['kebun']),
        makeElement('kodeorg','select',$data['kodeorg'],
            array('style'=>'width:150px','disabled'=>'disabled'),$optOrg)
    );
    $els[] = array(
        makeElement('notransaksi','label',$_SESSION['lang']['notransaksi']),
        makeElement('notransaksi','text',$data['notransaksi'],
            array('style'=>'width:150px','disabled'=>'disabled'))
    );
    $els[] = array(
        makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
        makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
        'disabled'=>'disabled'))
    );
    $els[] = array(
        makeElement('divisi','label',$_SESSION['lang']['subunit']),
        makeElement('divisi','select',$data['divisi'],
            array('style'=>'width:150px','disabled'=>'disabled'),$optDiv)
    );
    $els[] = array(
        makeElement('koderekanan','label',$_SESSION['lang']['koderekanan']),
        makeElement('koderekanan','select',$data['koderekanan'],
            array('style'=>'width:150px','disabled'=>'disabled'),$optSup)
    );

    return genElementMultiDim($_SESSION['lang']['header'],$els,2);
}
?>