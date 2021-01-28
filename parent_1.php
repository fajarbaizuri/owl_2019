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
 //mengambil isi combo yang dipilih
 email=document.getElementById('email').options[document.getElementById('email').selectedIndex].value;
 param='email='+email;//membentuk parameter yang akan di kirim
 tujuan='test_kirim_email.php';//file tujuan yang akan menerima parameter dan mengirim email
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
</script>
<?

OPEN_BOX("","WAS IST DEIN ");//parameter pertama bisa diganti dengan width:300px(lebar dari box nya) 
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


CLOSE_BOX();
echo close_body();
?>