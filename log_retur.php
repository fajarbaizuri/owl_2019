<?php
//@Copy nangkoelframework
//--IND--
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b>Retur Supplier</b>");
//print_r($_SESSION['empl']);
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/log_retur.js"></script>
<input type="hidden" id="method" name="method" value="insert"  />

<?php
//validasi user :D
$user_online=$_SESSION['standard']['userid'];
if($user_online=='' or $user_online==0000000000)
{
	echo "Error : Maaf data karyawan anda tidak terdaftar dalam system, silahkan hubungi pihak IT";
	CLOSE_BOX();
	echo close_body();
	exit;
}

##untuk pilihan organisasi 	
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT' ";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry)) {
	$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}	

##untuk pilihan transaksi 	
$notran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="select notransaksi from ".$dbname.".log_transaksiht where YEAR(tanggal)='".
	$_SESSION['org']['period']['tahun']."' and MONTH(tanggal)='".
	$_SESSION['org']['period']['bulan']."' and tipetransaksi='1'";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$notran.="<option value=".$data['notransaksi'].">".$data['notransaksi']."</option>";
			}					

## HEADER UNTUK BUAT BARU SAMA LIST
echo"<div id=action_list>";//buka div
echo"<table>
     <tr valign=moiddle>
	 
	 <td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo "No. Transaksi : <input type=text class=myinputtext id=scnotran />";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tglcari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cari()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
CLOSE_BOX();
echo "</div>";//tutup div

## UNTUK BUAT FORM INPUT HEADER
echo "<div id=headher style=display:none>";//buka diff
OPEN_BOX();//<td><select id=kdorg disabled style=\"width:150px;\"><option  value='".$kdor."'>".$nmor."</option></select></td>
echo "
<fieldset>
<legend>Header</legend>
<table cellspacing=1 border=0>
	<tr>	
		<td>No Transaksi Lama</td>
		<td>:</td>
		<td><select id=notranlama onchange=get_isi(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text) style=width:175px;>".$notran."</select></td>
	</tr>
	<tr>
		<td>No. Transaksi Baru</td>
		<td>:</td>		
		<td><input type=text id=notran size=10 class=myinputtext style=\"width:175px;\"></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td align=left><input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  maxlength=10 style=width:175px;>		</td>
	</tr>
	<tr>
		<td>PT asal</td>
		<td>:</td>
		<td align=left><input type=text class=myinputtextnumber id=pta name=pta  style=width:175px; readyonly disabled></td>
	</tr>
	<tr>
		<td>No PO</td>
		<td>:</td>
		<td align=left><input type=text class=myinputtextnumber id=nopo name=nopo  style=width:175px; readyonly disabled></td>
	</tr>
	<tr>
		<td>Nama Gudang</td>
		<td>:</td>
		<td align=left><input type=text class=myinputtextnumber id=kdgudang name=kdgudang  style=width:175px; readyonly disabled> -</td>
		<td align=left><input type=text class=myinputtextnumber id=nmgudang nnmame=nmgudang  style=width:175px; readyonly disabled></td>
	</tr>
	<tr>
		<td>Supplier</td>
		<td>:</td>
		<td align=left><input type=text class=myinputtextnumber id=supp name=supp  style=width:175px; readyonly disabled> -</td>
		<td align=left><input type=text class=myinputtextnumber id=nmsupp nnmame=supp  style=width:175px; readyonly disabled></td>
	</tr>
	<tr>
		<td colspan=2></td>
		<td><button id=savehead class=mybutton onclick=add_detail()>Simpan</button><button id=batal class=mybutton onclick=bukaform()>Batal</button></td>
		<input type=hidden id=method value='inserthead'>
		
	</tr>
</table>
</fieldset>";
CLOSE_BOX();
echo"</div>";//tutup div 
?>



<!--UNTUK LIST DATA,, PADA SAAT MASUK MENU TAMPILIN INI YG ADA SETELAH HEADER-->
<?php echo"
<div id=listData style=display:block>";//buka list data
OPEN_BOX();
	echo "
	<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=contain  style=display:block> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo "</div>";//tutup list data
?>






<!--UNTUK DETAIL-->
<?php
echo"<div id=detailEntry style=display:none>";
OPEN_BOX();
echo "<fieldset><legend>Detail</legend>";
/*<tr>	
		<td>No Transaksi Lama</td>
		<td>:</td>
		<td><select id=notranlamaedit disabled onchange=getdetedit(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text) style=width:175px;></select></td>
	</tr>*/

echo"<table cellspacing=1 border=0>
	
	
	
		
	<tr>	
		<td>Barang</td>
		<td>:</td>
		<td><select id=barang onchange=getdetbarang() style=width:150px;>".$optbarang."</select></td>
	</tr>
	
	<tr>	
		<td>Satuan</td>
		<td>:</td>
		<td><input type=text class=myinputtextnumber id=satuan name=satuan  style=width:175px; readyonly disabled></td>
	</tr>

	<tr>
		<td>Jumlah</td>
		<td>:</td>
		<td><input type=text id=jumlah size=10 onchange=cekstokbarang() onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	</tr>
	
	<tr>
		<td>Harga Satuan</td>
		<td>:</td>
		<td align=left><input type=text class=myinputtextnumber id=hargasat name=hargasat  style=width:175px; readyonly disabled></td>
	</tr>
	
	
	<tr><td colspan=2></td>
		<td><button class=mybutton onclick=savedetail()>Simpan</button>
		<td>
		<input type=hidden id=method value='insertdetail'>
	</tr>
	
</table></fieldset>";//<button class=mybutton onclick=displayList()>SELESAI</button>

echo"";
/*
<tr>	
		<td>No Transaksi</td>
		<td>:</td>
		<td><select id=notrandet onchange=getbarang() style=width:150px;>".$notran."</select></td>
	</tr>
*/
//UNTUK LIST DETAIL NAMA
echo"<fieldset><legend>Data Tersimpan</legend>
<table cellspacing=1 border=0>
    <thead>
        <tr class=rowheader>
        <td>No</td>
        <td>Nama Barang</td>
		<td>Satuan</td>
		<td>Jumlah</td>
		<td>Harga Satuan</td>
        <td>Action</td>
        </tr>
    </thead>
    	<tbody id=contentDetail></tbody>
    </table>
</fieldset> ";
CLOSE_BOX();
echo"<div>"; 
echo close_body();
?>
