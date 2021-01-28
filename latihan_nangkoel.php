<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2>
function kirimEmailSatu()
{
 //mengambil isi combo yang dippilih
 email=document.getElementById('email');
 email=email.options[document.getElementById('email').selectedIndex].value;
 
 param='email='+email;//membentuk parameter yang akan di kirim
 tujuan='slave_test_kirim_email.php';//file tujuan yang akan menerima parameter dan mengirim email
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
  tujuan='slave_simpan_gaji.php';
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
  $optionEmail.="<option value='".$bar->email."'>".$bar->namakaryawan."</option>";
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
 $optionNama.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."-".$bar->lokasitugas."</option>";
}
//pembentukan frame dari form, agar form ada dalam bingkai
echo"<fieldset style='width:500px;'><legend>Form Gaji</legend>";
echo"<table>
        <thead></thead>
		<tbody>
		   <tr><td>Nama</td><td><select id=karyawanid>".$optionNama."</select></td></tr>
		   <tr><td>Tahun</td><td><input id=tahun type=text onkeypress=\"return angka_doang(event);\" maxlength=4></td></tr>
		   <tr><td>Jumlah</td><td><input id=jumlah type=text onkeypress=\"return angka_doang(event);\" maxlength=15></td></tr>
		 </tbody>
         <tfoot><tr><td colspan=2 align=center><button onclick=simpanGaji()>Save</button></td></tfoot>
    </table>"; 
echo"<fieldset><legend>List</legend>
     <table class=sortable border=0 cellspacing=1><thead><tr><td>Nama</td><td>Tahun</td><td>Gaji</td></tr></thead>
	 <tbody id=table></tbody>
	 <tfoot></tfoot>
	 </table>";	
print"</fieldset>";     
//===============================end form


CLOSE_BOX();
echo close_body();
?>