<?
	//@Copy nangkoelframework
	require_once('master_validation.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
	echo open_body();
	include('master_mainMenu.php');
?>

<style type="text/css">
<!--
	.pageselected {
    	color: #FF0000;
    	font-weight: bold;
	}
-->
</style>

<script language=javascript1.2>
function kirimEmailSatu()
{
 //mengambil isi combo yang dippilih
 email=document.getElementById('email');
 email=email.options[document.getElementById('email').selectedIndex].value;
 
 param='email='+email;//membentuk parameter yang akan di kirim
 tujuan='ian_test_kirim_email.php';//file tujuan yang akan menerima parameter dan mengirim email
 post_response_text(tujuan, param, respog);//pemanggilan file
	
	function respog(){//fungsi hasil dari pemanggilan
		if (con.readyState == 4) {//status terkirim
			if (con.status == 200) {//status sudah ada respon dan berhasil
				busy_off();//menghilangkan notifikasi "Wait...."
				if (!isSaveResponse(con.responseText)) {//jika error dalam data(bukan system atau jaringan)
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					 alert("Kirim Email Sukses");
				}
			}
			else {//jika status sudah ada respon tetapi tidak berhasil
				busy_off();//menghilangkan notifikasi "Wait...."
				error_catch(con.status);//tampilkan jenis error
			}
		}
	}
}

function simpanGaji()
{
  karyawanid=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;
  tahun=document.getElementById('tahun').value;
  jumlah=document.getElementById('jumlah').value;
  
  param='karyawanid='+karyawanid+'&tahun='+tahun+'&jumlah='+jumlah;
  tujuan='ian_simpan_gaji.php';

  post_response_text(tujuan, param, respog);//pemanggilan file
	
	function respog(){//fungsi hasil dari pemanggilan
		if (con.readyState == 4) {//status terkirim
			if (con.status == 200) {//status sudah ada respon dan berhasil
				busy_off();//menghilangkan notifikasi "Wait...."
				if (!isSaveResponse(con.responseText)) {//jika error dalam data(bukan system atau jaringan)
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					 document.getElementById('table').innerHTML=con.responseText;
				}
			}
			else {//jika status sudah ada respon tetapi tidak berhasil
				busy_off();//menghilangkan notifikasi "Wait...."
				error_catch(con.status);//tampilkan jenis error
			}
		}
	}
}

function hapusGaji(karyawanid,tahun)
{
  
  param='karyawanid='+karyawanid+'&tahun='+tahun;
  tujuan='ian_hapus_gaji.php';

  post_response_text(tujuan, param, respog);//pemanggilan file
	
	function respog(){//fungsi hasil dari pemanggilan
		if (con.readyState == 4) {//status terkirim
			if (con.status == 200) {//status sudah ada respon dan berhasil
				busy_off();//menghilangkan notifikasi "Wait...."
				if (!isSaveResponse(con.responseText)) {//jika error dalam data(bukan system atau jaringan)
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					 document.getElementById('table').innerHTML=con.responseText;
				}
			}
			else {//jika status sudah ada respon tetapi tidak berhasil
				busy_off();//menghilangkan notifikasi "Wait...."
				error_catch(con.status);//tampilkan jenis error
			}
		}
	}
}



</script>
<?
    

OPEN_BOX("","THIS IS TEST");//parameter pertama bisa diganti dengan width:300px(lebar dari box nya) 
//===========================================================================================
$str="select namakaryawan,email from ".$dbname.".datakaryawan
      where email is not null and email!=''";// ini adalah query untuk mengambil nama dan email dari table datakaryawan
											 // var $dbname dapat dipanggil dari semua lini, karena variable tersebut sudah global
											 
$res=mysql_query($str);//execute query
while($bar=mysql_fetch_object($res))//looping query untuk membentuk option(combobox)
{
  $optionEmail.="<option value='".$bar->email."' title='".$bar->namakaryawan."'>".$bar->namakaryawan."</option>";
}
//tulis ke layar	  
echo"Nama :<select id=email>".$optionEmail.".</select> <button onclick=kirimEmailSatu()>Kirim</button>";
//==========================================================================================
echo"<hr>";


//start form===============================
//pengambilan karyawan id dan nama karyawan
$str="select karyawanid,namakaryawan,lokasitugas from ".$dbname.".datakaryawan order by namakaryawan";
$res=mysql_query($str);
//pembentukan option dengan variasi tambahan lokasi tugas karyawan
while($bar=mysql_fetch_object($res))
{
 $optionNama.="<option value='".$bar->karyawanid."' title='".$bar->namakaryawan."-".$bar->lokasitugas."'>".$bar->namakaryawan."-".$bar->lokasitugas."</option>";
}

//pembentukan frame dari form, agar form ada dalam bingkai
echo"<fieldset style='width:500px;'><legend>Form Gaji</legend>";
echo"<table>
        <thead></thead>
		<tbody>
		<tr><td>Nama</td><td><select id=karyawanid>".$optionNama."</select></td></tr>
		<tr><td>Tahun</td><td><input id=tahun type=text title='Silakan isi Tahun' onkeypress=\"return angka_doang(event);\" maxlength=4></td></tr>
		   <tr><td>Jumlah</td><td><input id=jumlah type=text title='Silakan isi Jumlah' onkeypress=\"return angka_doang(event);\" maxlength=15></td></tr>
		 </tbody>
         <tfoot><tr><td colspan=2 align=center><button onclick=simpanGaji()>Save</button></td></tfoot>
    </table>"; 

$str="select a.karyawanid,b.namakaryawan,a.tahun,a.gaji from ".$dbname.".test_gaji a left join ".$dbname.".datakaryawan b
        on a.karyawanid=b.karyawanid
		order by namakaryawan,tahun";
  $res=mysql_query($str);
  $result='';
  while($bar=mysql_fetch_object($res))
  {
    $result.="<tr class=rowcontent><td>".$bar->namakaryawan."</td>
<td>".$bar->tahun."</td>
<td>".number_format($bar->gaji,2,",",".")."</td>	
<td><img src=images/delete1.jpg onclick=hapusGaji('".$bar->karyawanid."','".$bar->tahun."') title='Hapus' style='cursor:pointer'></td>
										</tr>";
  }
  //echo $result;
echo"<fieldset><legend>List</legend>
     <table class=sortable border=0 cellspacing=1><thead>
	<tr>
<td>Nama</td>
<td>Tahun</td>
<td>Gaji</td>
<td>Aksi</td>
</tr></thead>
	 <tbody id=table>".$result."</tbody>
	 <tfoot></tfoot>
	 </table>";	
print"</fieldset>";     
//===============================end form
//=================> array 


echo"<fieldset><legend>Array</legend>";
//default aray
//karyawan
$arrKar=Array();
//gaji
$arrGaji=Array();
//jabatan
$arrJab=Array();
// Data Perhalaman
$dataPerPage = 20;

// apabila $_GET['page'] sudah didefinisikan, gunakan nomor halaman tersebut, 
// sedangkan apabila belum, nomor halamannya 1.
if(isset($_GET['page']))
{
    $noPage = $_GET['page'];
} 
else $noPage = 1;

// perhitungan offset
$offset = ($noPage - 1) * $dataPerPage;

$str4="select karyawanid,namakaryawan, kodejabatan  from ".$dbname.".datakaryawan ORDER BY karyawanid LIMIT ".$offset.",".$dataPerPage.";";
$res4=mysql_query($str4);
while($bar=mysql_fetch_object($res4))
{
 $arrKar[$bar->karyawanid]['nama']=$bar->namakaryawan;
 $arrKar[$bar->karyawanid]['jabatan']=$bar->kodejabatan;
}

$str1="select karyawanid,gaji  from ".$dbname.".test_gaji where tahun=2012";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
 $arrGaji[$bar1->karyawanid]['gaji']=$bar1->gaji;
}

$str2="select kodejabatan, namajabatan  from ".$dbname.".sdm_5jabatan";
$res2=mysql_query($str2);
while($bar2=mysql_fetch_object($res2))
{
 $arrJab[$bar2->kodejabatan]=$bar2->namajabatan;
}


foreach($arrKar as $kar=>$val)
{
$result1.="<tr><td>".$arrKar[$kar]['nama']."</td>";
$result1.="<td>".$arrJab[$arrKar[$kar]['jabatan']]."</td>";
$result1.="<td align=right>".number_format($arrGaji[$kar]['gaji'],2,",",".")."</td>";
$result1.="<td align=right>".number_format(($arrGaji[$kar]['gaji']*2/100),2,",",".")."</td></tr>";
}


// mencari jumlah semua data dalam tabel karyawan

$query   = "SELECT COUNT(*) AS jumData FROM ".$dbname.".datakaryawan";
$hasil  = mysql_query($query);
$data     = mysql_fetch_object($hasil);

$jumData = $data->jumData;

// menentukan jumlah halaman yang muncul berdasarkan jumlah semua data
$jumPage = ceil($jumData/$dataPerPage);

// menampilkan link previous

if ($noPage > 1) echo  "<a href='".$_SERVER['PHP_SELF']."?page=".($noPage-1)."'>&lt;&lt; Prev</a>";
// memunculkan nomor halaman dan linknya

for($page = 1; $page <= $jumPage; $page++)
{
         if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)) 
         {   
            if (($showPage == 1) && ($page != 2))  echo "..."; 
            if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  echo "...";
            if ($page == $noPage) echo " <b>".$page."</b> ";
            else echo " <a href='".$_SERVER['PHP_SELF']."?page=".$page."'>".$page."</a> ";
            $showPage = $page;          
         }
}

// menampilkan link next
if ($noPage < $jumPage) echo "<a href='".$_SERVER['PHP_SELF']."?page=".($noPage+1)."'>Next &gt;&gt;</a>";

    

echo"<table class=sortable border=0 cellspacing=1><thead><tr><td align=center valign=middle bgcolor=#EAEAEA>"; 
echo"<tr><td>Nama</td>
<td>Jabatan</td>
<td>Gaji</td>
<td>Jamsostek</td>
</tr></thead>
	 <tbody>".$result1."</tbody>
	 <tfoot></tfoot>
	 </table>";	
print"</fieldset>";     

CLOSE_BOX();
echo close_body();
?>
