<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$where = "`tipe`='HOLDING'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');
$optCurr = makeOption($dbname,'setup_matauang','kode,matauang');
$optTipeAkun = array(
  'Aktiva'=>'Aktiva',
  'Passiva'=>'Passiva',
  'Modal'=>'Modal',
  'Penjualan'=>'Penjualan',
  'Biaya'=>'Biaya',
  'Lain-lain'=>'Lain-lain'
);
$optFieldAktif = array(
	'Kegiatan','Asset','Barang','Karyawan','Pelanggan','Supplier','Kendaraan'
);
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg'].'  '),
  makeElement('kodeorg','select','',array('style'=>'width:250px'),$optOrg)
);
$els[] = array(
  makeElement('noakun','label',$_SESSION['lang']['noakun'].'  '),
  makeElement('noakun','text','',array('style'=>'width:80px','maxlength'=>'16',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('namaakun','label',$_SESSION['lang']['namaakun'].'  '),
  makeElement('namaakun','text','',array('style'=>'width:250px','maxlength'=>'80',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('tipeakun','label',$_SESSION['lang']['tipeakun']),
  makeElement('tipeakun','select','',array('style'=>'width:80px'),$optTipeAkun)
);
$els[] = array(
  makeElement('level','label',$_SESSION['lang']['level']),
  makeElement('level','text','',array('style'=>'width:15px','maxlength'=>'2',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('matauang','label',$_SESSION['lang']['matauang']),
  makeElement('matauang','select','',array('style'=>'width:80px'),$optCurr)
);
$els[] = array(
  makeElement('detail','label',$_SESSION['lang']['detail']),
  makeElement('detail','check','',array())
);
$els[] = array(
  makeElement('kasbank','label',$_SESSION['lang']['kasbank']),
  makeElement('kasbank','check','0',array())
);
$els[] = array(
  makeElement('fieldaktif','label',$_SESSION['lang']['fieldaktif']),
  makeElement('fieldaktif','multichk','0000000',array('style'=>'width:80px','maxlength'=>'7',
    'onkeypress'=>'return angka_doang(event)'),$optFieldAktif)
);
$els[] = array(
  makeElement('pemilik','label',$_SESSION['lang']['namapemilik']),
  makeElement('pemilik','text','',array('style'=>'width:80px','maxlength'=>'7',
    'onkeypress'=>'return angka_doang(event)'))
);

# Fields
$fieldStr = '##noakun##namaakun##tipeakun##kasbank##level##matauang##kodeorg##detail##fieldaktif##pemilik';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5akun',"##noakun##kodeorg",null,null,true)
);

# Generate Field
echo genElTitle('Daftar Perkiraan',$els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
$kolom = 'noakun,namaakun';
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname,'keu_5akun',"*",array(),array(),array(),array(),'keu_slave_5daftarperkiraan_pdf');
#echo masterTable($dbname,'keu_5akun',"*",array(),array(),array(),array(),'keu_slave_5daftarperkiraan_pdf');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>