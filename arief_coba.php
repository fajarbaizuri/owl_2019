<?
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2>
function kirimEmailSatu()
{
	//ambil isi combo yg terpilih
	email=document.getElementById('email');
	email=email.options[document.getElementById('email').selectedIndex].value;
	param='email='+email;
	tujuan='slave_arief_test_kirim_email.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
							alert("Kirim sukses");
						}
					}
					else {
					busy_off();
					error_catch(con.status);
					}
				}
			}
}
function simpanGaji()
{
	karyawanid=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;
	tahun=document.getElementById('tahun').value;
	jumlah=document.getElementById('jumlah').value;
	param='karyawanid='+karyawanid+'&tahun='+tahun;
	param+='&jumlah='+jumlah;
	tujuan='slave_arief_simpan_gaji.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION, \n' + con.responseText);
				}
				else {
						document.getElementById('table').innerHTML=con.responseText;
					}
				}
				else {
						busy_off();
						error_catch(con.status);
					}
            }
		}
}
function hapusGaji(karyawanid,tahun)
{
	param='karyawanid='+karyawanid+'&tahun='+tahun;
	tujuan='slave_arief_hapus_gaji.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION, \n' + con.responseText);
				}
				else {
						alert('Berhasil');
						document.getElementById('table').innerHTML=con.responseText;
					}
				}
				else {
						busy_off();
						error_catch(con.status);
					}
			}
		}
}
</script>
<?
OPEN_BOX("","Coba Aja");
$str="select namakaryawan,email from ".$dbname.".datakaryawan where email is not null and email!=''";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optionEmail.="<option value='".$bar->email."'>".$bar->namakaryawan."</option>";
}
echo"Nama :<select id=email>".$optionEmail.".</select> <button onclick=kirimEmailSatu()>Kirim</button>";
//===================== latihan join tabel pkl 17.08 ==============
echo"<hr>";
$str="select karyawanid,namakaryawan,lokasitugas from ".$dbname.".datakaryawan order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optionNama.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."-".$bar->lokasitugas."</option>";
}
echo"<fieldset style='width:500px;'><legend>Form Gaji</legend>";
echo"<table>
	<thead></thead>
	<tbody>
		<tr><td>".$_SESSION['lang']['nama']."</td><td><select id=karyawanid>".$optionNama."</select></td></tr>
		<tr><td>".$_SESSION['lang']['tahun']."</td><td><input id=tahun type=text onkeypress=\"return angka_doang(event);\" maxlength=4></td></tr>
		<tr><td>".$_SESSION['lang']['jumlah']."</td><td><input id=jumlah type=text onkeypress=\"return angka_doang(event);\" maxlength=15></td></tr>
	</tbody>
	<tfoot><tr><td colspan=2 align=center><button onclick=simpanGaji()>Save</button></td></tfoot>
	</table>";
//test====menampilkan isi tabel
	$str="select a.karyawanid,b.namakaryawan,a.tahun,a.gaji from ".$dbname.".test_gaji a left join ".$dbname.".datakaryawan b
		on a.karyawanid=b.karyawanid
			order by namakaryawan,tahun";
	$res=mysql_query($str);
	$result='';
	while($bar=mysql_fetch_object($res))
	{
		$result.="<tr class=rowcontent><td>".$bar->namakaryawan."</td>
										<td>".$bar->tahun."</td>
										<td align='right'>".number_format($bar->gaji,2,",",".")."</td>	
										<td><img src=images/delete1.jpg onclick=hapusGaji('".$bar->karyawanid."','".$bar->tahun."') title='Hapus' style='cursor:pointer'></td>
										</tr>";
	}
//end test===	menampilkan isi tabel
echo"<fieldset><legend>List</legend>
	<table class=sortable border=0 cellspacing=1>
	<thead><tr>
		<td>Nama</td>
		<td>Tahun</td>
		<td>Gaji</td>
		<td>Aksi</td>
	</tr></thead>
	<tbody id=table>".$result."</tbody>
	<tfoot></tfoot>
	</table>";
print"</fieldset>";
//=======ARRAY=========
 //======membagi per halaman bag 1 ==========
$halaman = $_GET['halaman'];
if($halaman == 0)
 $halaman = 1;

$query = "SELECT * FROM ".$dbname.".datakaryawan"; //digunakan utk menghitung jumlah data, tp kyknya masih keliru
$run = mysql_query($query);

//bikin pembatasan data per halaman
$DataPerHal = 20; //jumlah data per halaman
$jumDat = mysql_num_rows($run); //jumlah data keseluruhan
$jumHal = ceil($jumDat / $DataPerHal); //jumlah halaman = jumlah data keseluruhan dibagi jumlah data per halaman
$dataMulai = ($halaman * $DataPerHal)-$DataPerHal; //data awal tiap halaman
 
  //======membagi per halaman bag 1 ==========END 
//Belajar Array
//echo"<fieldset><Legend>Test Array</legend>";
#default array
#data karyawan
	$arrDTKar=Array();
#gaji
	$arrGaji=Array();
#golongan	
	$arrGolongan=Array();
#1. ambil semua nama dan idkaryawan	
//$str="select karyawanid,namakaryawan,kodejabatan from ".$dbname.".datakaryawan";
$str="select karyawanid,namakaryawan,kodejabatan from ".$dbname.".datakaryawan LIMIT $dataMulai, $DataPerHal";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$arrDTKar[$bar->karyawanid]['nama']=$bar->namakaryawan;
	$arrDTKar[$bar->karyawanid]['jabatan']=$bar->kodejabatan;
}
#2. 
$str="select karyawanid,gaji from ".$dbname.".test_gaji where tahun=2012";
//$str="select karyawanid,gaji from ".$dbname.".test_gaji where tahun=2012 LIMIT $dataMulai, $DataPerHal";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$arrGaji[$bar->karyawanid]['gaji']=$bar->gaji;
}
#3. ambil nama jabatan dari tabel jabatan
$str="select kodejabatan,namajabatan from ".$dbname.".sdm_5jabatan";
//$str="select kodejabatan,namajabatan from ".$dbname.".sdm_5jabatan LIMIT $dataMulai, $DataPerHal";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$arrGolongan[$bar->kodejabatan]=$bar->namajabatan;
}
foreach($arrDTKar as $karID=>$val)
{
	$hasil.="<tr class=rowcontent><td>".$arrDTKar[$karID]['nama']."</td>";
	$hasil.="<td>".$arrGolongan[$arrDTKar[$karID]['jabatan']]."</td>";
	$hasil.="<td align=right>".number_format($arrGaji[$karID]['gaji'],2,",",".")."</td>";
	$hasil.="<td align=right>".number_format(($arrGaji[$karID]['gaji']*2/100),2,",",".")."</td>";
}
 
echo"<fieldset style='width:500px;'><legend>Belajarr Array</legend>";
echo"<table>
	<thead>
	<tr><td>".$_SESSION['lang']['nama']."</td>
            <td>".$_SESSION['lang']['jabatan']."</td>
            <td>".$_SESSION['lang']['gaji']."</td>
            <td>".$_SESSION['lang']['jamsostek']."</td></tr>
	</thead>
	<tbody>
		".$hasil."
	</tbody>
	<tfoot></tfoot>
	</table>";
  //======membagi per halaman bag 2 ==========
if($halaman>=2){
 echo "<a href=\"?halaman=".($halaman - 1)."\">Sebelumnya</a>";
}
 
echo " | ";

if($halaman < $jumHal){
 echo "<a href=\"?halaman=".($halaman + 1)."\">Berikutnya</a>";
}
 echo ".$halaman.";
   //======membagi per halaman bag 2 ==========END 
//===================================================================
CLOSE_BOX();
echo close_body();
?>