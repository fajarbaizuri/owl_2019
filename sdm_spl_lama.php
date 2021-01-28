<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/sdm_spl_lama.js" /></script>


<!--deklarasi untuk option-->
<?php

$user_id=$_SESSION['standard']['userid'];
if($user_id=='' or $user_id==0000000000)
{
	echo "Error : User tidak boleh membuat Permintaan Perintah Lembur";
	CLOSE_BOX();
	echo close_body();
	exit;
}

##untuk pilihan organisasi 	
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)=4 ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}	

##untuk pilihan nama karyawan
$optkar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optkar2="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql = "SELECT karyawanid,namakaryawan,nik FROM ".$dbname.".datakaryawan where lokasitugas like '%".$_SESSION['empl']['lokasitugas']."%' ORDER BY namakaryawan";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optkar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']." [nik : ".$data['nik']."]</option>";
			$optkar2.="<option value=".$data['karyawanid'].">".$data['namakaryawan']." [nik : ".$data['nik']."]</option>";
			}
	
?>


<!--untuk header atas yg isinya buat baru sama list dan juga searching-->
<?php
echo"<div id=action_list>";
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";exit();
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo "Nama Karyawan :<select id=scnama style=\"width:225px;\" >".$optkar2."</select>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cari()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
CLOSE_BOX();
 //1 C	 
?>
</div>

<!--untuk list data-->
<?php
echo "<div id=\"list\">";
OPEN_BOX();//<img src=images/pdf.jpg onclick=masterPDF('log_prapoht','','','log_print_pdf_pp',event) width=20 height=20 />
echo "
<fieldset>
<legend><b>Surat Perintah Lembur</b></legend>

<div style='width:800px;height:500px;overflow:scroll'>
	 <table class=sortable cellspacing=1 border=0>
	 <thead>
	 <tr class=rowheader>
		 <td>No.</td>
		 <td>Nama Karyawan</td>
		 <td>Tanggal Lembur</td>
		 <td>Nik Karyawan</td>
		 <td>Kode Organisasi</td>
		 <td>Uang Lembur</td>
		 <td>Uang Makan</td>
		 <td>Uang Transport</td>
		 <td>Status</td>
	 </tr>
	 </thead>
	 <tbody id=contain>
	<script>loadData()</script>

	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table></div>
</fieldset>";
CLOSE_BOX();
echo "</div>";
?>

<!--untuk form input-->
<?php

echo "<div id=\"input\" style=display:none;>";
OPEN_BOX();
echo "
<fieldset>
<legend><b>Surat Perintah Lembur</b></legend>

<table>
	
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=width:75px; /></td>
	<tr>
				
	<tr>
		<td>".$_SESSION['lang']['kodeorganisasi']." </td>
		<td>:</td>
		<td><select id=kdorg style=\"width:225px;\" >".$optOrg."</select></td>
	</tr>
				
	<tr>
		<td>".$_SESSION['lang']['namakaryawan']."</td>
		<td>:</td>
		<td><select id=ki style=\"width:225px;\" >".$optkar."</select></td>
	</tr>
	
	<tr>
		<td>Uang Lembur</td>
		<td>:</td>
		<td><input type=text id=ul size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	</tr>
	
	<tr>
		<td>Uang Makan</td>
		<td>:</td>
		<td><input type=text id=um size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	</tr>
	
	<tr>
		<td>Uang Transport</td>
		<td>:</td>
		<td><input type=text id=ut size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>

	<tr><td colspan=2></td>
		<td><button class=mybutton onclick=simpan()>Simpan</button>
		<button class=mybutton onclick=batal()>Batal</button></td>
		<input type=hidden id=method value='insert'>
	</tr>
</table>
</fieldset>";
CLOSE_BOX();
echo "</div>";
?>
<?php echo close_body(); ?>