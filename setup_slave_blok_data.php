<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once ('lib/zLib.php');

# Get POST Data
$afdeling = $_POST['afdeling'];

#========Get Blok Ids============
# Create Condition
$where1 = "(tipe='BLOK' or tipe='BIBITAN') and induk='".$afdeling."'";

# Get Org Data
$query = selectQuery($dbname,'organisasi',"kodeorganisasi",$where1);
$data = fetchData($query);
# Create Condition for Table
$where2 = array();
foreach($data as $key=>$row) {
    $where2[] = array('kodeorg'=>$row['kodeorganisasi']);
}
if(count($where2)<1)
{
    exit("Error:Tidak ada data");
}
$where2['sep'] = 'OR';

#========Start Make Table
# Prep
$fieldStr = '##kodeorg##tahuntanam##luasareaproduktif##luasareanonproduktif'.
    '##cadangan##okupasi##rendahan##sungai##rumah##kantor##pabrik##jalan##kolam##umum'.
    '##jumlahpokok##statusblok##bulanmulaipanen##tahunmulaipanen##kodetanah'.
    '##klasifikasitanah##topografi##intiplasma##jenisbibit##tanggalpengakuan';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Get Data
#$query = selectQuery($dbname,'setup_blok',"*",$where2);
#$data = fetchData($query);

# Set Header Name
$head = array();
$head[0]['name'] = $_SESSION['lang']['kodeorg'];
$head[1]['name'] = $_SESSION['lang']['tahuntanam'];
$head[2]['name'] = $_SESSION['lang']['luasareaproduktif'];
$head[3]['name'] = $_SESSION['lang']['luasareanonproduktif'];
$head[4]['name'] = $_SESSION['lang']['arealcadangan'];
$head[5]['name'] = $_SESSION['lang']['arealokupasi'];
$head[6]['name'] = $_SESSION['lang']['arealrendahan'];
$head[7]['name'] = $_SESSION['lang']['arealsungai'];
$head[8]['name'] = $_SESSION['lang']['arealrumah'];
$head[9]['name'] = $_SESSION['lang']['arealkantor'];
$head[10]['name'] = $_SESSION['lang']['arealpabrik'];
$head[11]['name'] = $_SESSION['lang']['arealjalan'];
$head[12]['name'] = $_SESSION['lang']['arealkolam'];
$head[13]['name'] = $_SESSION['lang']['arealumum'];
$head[14]['name'] = $_SESSION['lang']['jumlahpokok'];
$head[15]['name'] = $_SESSION['lang']['statusblok'];
$head[16]['name'] = $_SESSION['lang']['mulaipanen'];
$head[17]['name'] = $_SESSION['lang']['kodetanah'];
$head[18]['name'] = $_SESSION['lang']['klasifikasitanah'];
$head[19]['name'] = $_SESSION['lang']['topografi'];
$head[20]['name'] = $_SESSION['lang']['intiplasma'];
$head[21]['name'] = $_SESSION['lang']['jenisbibit'];
$head[22]['name'] = $_SESSION['lang']['tanggal'];


# Set Span
$head[16]['span'] = '2';

# Set Display Type
$conSetting = array();
$conSetting['luasareaproduktif']['type'] = 'currency';
$conSetting['luasareanonproduktif']['type'] = 'currency';
$conSetting['jumlahpokok']['type'] = 'numeric';
$conSetting['bulanmulaipanen']['type'] = 'month';

# Display Table
$master = masterTableBlok($dbname,'setup_blok',1,$fieldArr,$head,$conSetting,$where2,
    array(),'setup_slave_blok_pdf');
try {
    echo $master;
} catch(Exception $e) {
    echo "Create Table Error";
}
?>