<?php
//@Copy nangkoelframework
//--IND--
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('',"<b> Surat Perintah Lembur</b>");
//print_r($_SESSION['temp']);
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/sdm_spl.js"></script>
<input type="hidden" id="method" name="method" value="insert"  />


<!--deklarasi untuk option-->
<?php

//validasi user :D
$user_online=$_SESSION['standard']['userid'];
if($user_online=='' or $user_online==0000000000)
{
	echo "Error : User tidak boleh membuat Permintaan Perintah Lembur";
	CLOSE_BOX();
	echo close_body();
	exit;
}

##tipelembur
$optTipelembur="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrsstk=array("0"=>"normal","1"=>"minggu","2"=>"hari libur bukan minggu","3"=>"hari raya");
foreach($arrsstk as $kei=>$fal)
{
	//print_r($kei);exit();
	$optTipelembur.="<option value='".$kei."'>".ucfirst($fal)."</option>";
} 

##untuk pilihan organisasi 	
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)=4 ORDER BY kodeorganisasi";
$sql="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}	
			
##LOCK AJA TUGASNYA
//$kdorg=$_SESSION['empl']['lokasitugas'];	
$str1="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi
      where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
//	  echo $str1;	  
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
	$nmor=$bar1->namaorganisasi;
	$kdor=$bar1->kodeorganisasi;
}		

##untuk pilihan nama karyawan
$optkar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT karyawanid,namakaryawan,nik FROM ".$dbname.".datakaryawan where lokasitugas like '%".$_SESSION['empl']['lokasitugas']."%' ORDER BY namakaryawan";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optkar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']." [nik : ".$data['nik']."]</option>";
			}
			
##untuk pilihan nama karyawan penyetuju dan pengecek
$optkarstaf="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT karyawanid,namakaryawan,nik,tipekaryawan FROM ".$dbname.".datakaryawan where tipekaryawan='0' ORDER BY namakaryawan";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optkarstaf.="<option value=".$data['karyawanid'].">".$data['namakaryawan']." [nik : ".$data['nik']."]</option>";
			}
			
##untuk pilihan divisi
$divisi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT * FROM ".$dbname.".sdm_5departemen order by nama";
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$divisi.="<option value=".$data['kode'].">".$data['nama']."</option>";
			}									
			
##untuk jam dan menit option			
for($t=0;$t<24;)
{
	if(strlen($t)<2)
	{
		$t="0".$t;
	}
	$jm.="<option value=".$t." ".($t==00?'selected':'').">".$t."</option>";
	$t++;
}
for($y=0;$y<60;)
{
	if(strlen($y)<2)
	{
		$y="0".$y;
	}
	$mnt.="<option value=".$y." ".($y==00?'selected':'').">".$y."</option>";
	$y++;
}	
		
## untuk lock nama pembuat		
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where karyawanid=".$_SESSION['standard']['userid'];	  
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namakaryawan=$bar->namakaryawan;
	$karyawanid=$bar->karyawanid;
}				
	
?>

<!--HEADER UNTUK BUAT BARU SAMA LIST-->
<?php
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

<!--UNTUK BUAT FORM INPUT HEADER-->
<?php
echo "<div id=headher style=display:none>";//buka diff
OPEN_BOX();//<td><select id=kdorg disabled style=\"width:150px;\"><option  value='".$kdor."'>".$nmor."</option></select></td>
echo "
<fieldset>
<legend>Header</legend>
<table cellspacing=1 border=0>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>		
		<td><select id=kdorg onchange=get_isi(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text) style=\"width:150px;\">'".$optOrg."'</select></td>
	</tr>
	<tr>	
		<td>Divisi</td>
		<td>:</td>
		<td><select id=divisi style=width:150px;>".$divisi."</select></td>
	</td>
	<tr>
		<td>No. Dokumen</td>
		<td>:</td>		
		<td><input type=text id=notran size=10 disabled class=myinputtext style=\"width:150px;\"></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  maxlength=10 style=width:150px;/></td>
	</tr>
	<tr>
		<td>Jam Mulai</td>
		<td>:</td>
		<td><select id=jm1 name=jmId >".$jm."</select>:<select id=mn1>".$mnt."</select></td>
	</tr>
	<tr>
		<td>Jam Selesai</td>
		<td>:</td>
		<td><select id=jm2 name=jmId2 >".$jm."</select>:<select id=mn2>".$mnt."</select></td>
	</tr>
	<tr>	
		<td>Penyetuju</td>
		<td>:</td>
		<td><select id=penyetuju style=width:150px;>".$optkarstaf."</select></td>
	</td>

	<tr>	
		<td>Dibuat Oleh</td>
		<td>:</td>
		<td><select id=pembuat disabled><option value='".$karyawanid."'>".$namakaryawan."</option></select></td>
	</tr>
	<tr>
		<td colspan=2></td>
		<td><button id=savehead class=mybutton onclick=add_detail()>Simpan</button><button id=batal class=mybutton onclick=bukaform()>Batal</button></td>
		<input type=hidden id=method value='inserthead'>
		
	</tr>
	
</table>
</fieldset>";
CLOSE_BOX();/*	<tr>	
		<td>Diperiksa</td>
		<td>:</td>
		<td><select id=pemeriksa style=width:150px;>".$optkarstaf."</select></td>
	</td>*/
echo"</div>";//tutup div 
?>


<!--UNTUKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK DETAILLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL-->
<?php
echo"<div id=detailEntry style=display:none>";
OPEN_BOX();
echo "<fieldset><legend>Detail</legend>";


/*<table cellspacing=1 border=0>
	<tr>
		<td>".$_SESSION['lang']['namakaryawan']."</td>
		<td>:</td>
		<td><select id=ki style=\"width:225px;\" >".$optkar."</select></td>
		
		
		<td>Uang Lembur</td>
		<td>:</td>
		<td><input type=text id=ul size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	
		<td>Uang Makan</td>
		<td>:</td>
		<td><input type=text id=um size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	
	
		<td>Uang Transport</td>
		<td>:</td>
		<td><input type=text id=ut size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:75px;\"></td>
	</tr>
	
	<tr>	
		<td>Tugas</td>
		<td>:</td>
		<td colspan=12><input type=text id=tugas size=10 id=tugas style=\"width:715px;\"></td>
	</tr>
	
	<tr><td colspan=2></td>
		<td><button class=mybutton onclick=savedetail()>Simpan</button>
		<td>
		<input type=hidden id=method value='insertdetail'>
	</tr>
	
</table>*/






echo"<table class=sortable border=0 cellspacing=1 cellpadding=1>
<thead>
	<tr class=rowheader >
		<td>".$_SESSION['lang']['namakaryawan']."</td>
		<td>Tugas</td>
		<td>".$_SESSION['lang']['action']."</td>
	</tr>
</thead>

<tbody>
	<tr class=rowcontent>
		<td><select id=ki style=\"width:200px;\" >".$optkar."</select></td>
		<td><input type=text id=tugas size=10 id=tugas style=\"width:300px;\"></td>
		<td align=center style='cursor:pointer;'><img id=method title='Simpan' class=resicon onclick=savedetail() src='images/save.png'/></td></tr></tbody></table>

</fieldset>";//<button class=mybutton onclick=displayList()>SELESAI</button>











echo"";

//UNTUK LIST DETAIL NAMA
echo"<fieldset><legend>Data Tersimpan</legend>
<table cellspacing=1 border=0>
    <thead>
        <tr class=rowheader>
        <td>No</td>
        <td>Nama Karyawan</td>
		<td>NIK</td>
		<td>Tugas</td>
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
