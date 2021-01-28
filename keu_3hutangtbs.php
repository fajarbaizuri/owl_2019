<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src='js/zTools.js'></script>
<script language=javascript1.2 src='js/keu_3hutangtbs.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$bulantahun = $_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
$optJenisData=array('int'=>'Pendapatan Tbs Internal(Normal)','int01'=>'Pendapatan Tbs Internal(Netto)','ext'=>'Hutang Supplier Tbs');

# Fields
$els = array();
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('tgl','label','Tanggal'),
  makeElement('tgl1','rangedate','',array('style'=>'width:85px'))
  //makeElement('periode','select','',array('style'=>'width:200px'),$optPeriod)
);
$els[] = array(
  makeElement('jenisData','label','Pengakuan'),
  makeElement('jenisData','select','',array('style'=>'width:200px'),$optJenisData)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listCari()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
echo genElTitle('Proses Pengakuan TBS',$els);
CLOSE_BOX();

# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listPosting',null,true);
CLOSE_BOX();

#=== End ===
close_body();
?>