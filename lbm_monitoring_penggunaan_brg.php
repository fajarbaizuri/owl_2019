<?
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$arr="##unit##berdasarkan##find##judul";  
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

//echo'<script type="text/javascript" src="js/lbm_karyawan_perumahan.js"></script>'; // taken from bgt_laporan_kapital

$optunit="<option value=''></option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
    where  tipe='GUDANG'
    order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optunit.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$optberdasarkan="<option value='A.notransaksi'>No Transaksi</option>";
$optberdasarkan.="<option value='A.tanggal'>Tanggal</option>";
$optberdasarkan.="<option value='A.kodebarang'>Kode Barang</option>";
$optberdasarkan.="<option value='B.namabarang'>Nama Barang</option>";
$optberdasarkan.="<option value='A.jumlah'>Jumlah</option>";
$optberdasarkan.="<option value='A.satuan'>Satuan</option>";
$optberdasarkan.="<option value='A.keterangan'>Keterangan</option>";

 
$optperiode="<option value=''></option>";
$sOrg="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optperiode.="<option value=".$rOrg['periode'].">".$rOrg['periode']."</option>";
}
/*
echo" 
<table cellspacing=\"1\" border=\"0\" >
    <tr><td colspan=2>".$judul."</td></tr>
    <tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id='unit' style=\"width:200px;\">".$optunit."</select></td></tr>
	<tr><td><label>Berdasarkan</label></td><td><select id='berdasarkan' style=\"width:200px;\">".$optberdasarkan."</select></td></tr>
    <tr><td><label>Kata Dicari</label></td><td><input style=\"width:195px;\" type=text id=find name=find ></td></tr> 
    <tr height=\"20\"><td colspan=\"2\"><input  type=hidden id=judul name=judul value='".$judul."'></td></tr>
    <tr><td colspan=\"2\"> 
    <button onclick=\"zPreview('lbm_slave_pks_rekap_byproduksi','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'lbm_slave_pks_rekap_byproduksi.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
    <button onclick=\"zPdf('lbm_slave_pks_rekap_byproduksi','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>
    <!--<button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>--></td></tr>
</table>
";
*/
echo" 
<table cellspacing=\"1\" border=\"0\" >
    <tr><td colspan=2>".$judul."</td></tr>
    <tr><td><label>Gudang</label></td><td><select id='unit' style=\"width:200px;\">".$optunit."</select></td></tr>
	<tr><td><label>Berdasarkan</label></td><td><select id='berdasarkan' style=\"width:200px;\">".$optberdasarkan."</select></td></tr>
    <tr><td><label>Kata Dicari</label></td><td><input style=\"width:195px;\" type=text id=find name=find ></td></tr> 
    <tr height=\"20\"><td colspan=\"2\"><input  type=hidden id=judul name=judul value='".$judul."'></td></tr>
    <tr><td colspan=\"2\"> 
    <button onclick=\"zPreview('lbm_slave_monitoring_penggunaan_brg','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    </td></tr>
</table>
";
?>