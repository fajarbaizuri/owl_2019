<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/sdm_pengobatan.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?
OPEN_BOX('',$_SESSION['lang']['adm_peng']);
$optthn="<option value=''></option>";
for($x=-1;$x<4;$x++)
{
	$optthn.="<option value='".(date('Y')-$x)."'>".(date('Y')-$x)."</option>";
}

//ambil data karyawan=============================

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where
	      alokasi=1 order by namakaryawan";
}
else
{
	$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where
	      alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";	
}
$optKar="<option value=''></option>";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}
//===================================
//ambil jenis pengobatan
$str="select * from ".$dbname.".sdm_5jenisbiayapengobatan order by nama";
$res=mysql_query($str);
$optJns='';
while($bar=mysql_fetch_object($res))
{
	$optJns.="<option value='".$bar->kode."'>".$bar->nama."</option>";
}

//=====================================
//ambil rumah sakit
$str="select id,namars,kota from ".$dbname.".sdm_5rs order by namars";
$res=mysql_query($str);
$optRs='';
while($bar=mysql_fetch_object($res))
{
	$optRs.="<option value='".$bar->id."'>".$bar->namars."[".$bar->kota."]</option>";
}
//================================================
//ambil list diagnosa
$optDiagnosa='';
$str="select * from ".$dbname.".sdm_5diagnosa order by diagnosa";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optDiagnosa.="<option value='".$bar->id."'>".$bar->diagnosa."</option>";
}
//===============================================
//jenis klaim
$optklaim="<option value=0>".$_SESSION['lang']['karyawan']."</option>
          <option value=1>".$_SESSION['lang']['rumahsakit']."</option>";

//================================================
//option periode akuntansi
$optx='';
for($x=-1;$x<13;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optx.="<option value='".date('Y-m',$dt)."'>".date('m-Y',$dt)."</option>";
}
$arr[0]="<fieldset><legend>".$_SESSION['lang']['form']."</legend>
     <table>
	 <tr>
	  <td>".$_SESSION['lang']['thnplafon']."</td>
	      <td><select id=thnplafon onchange=getTrxNumber(this.options[this.selectedIndex].value)>".$optthn."</select>
		  ".$_SESSION['lang']['periode']."<select id=periode>".$optx."</select>
		  </td>
	  <td>".$_SESSION['lang']['notransaksi']."</td>
	      <td>
		    <input type=text class=myinputtext id=notransaksi maxlength=20 disabled>
		  </td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
	  <td><select id=jenisbiaya  style='width:200px;'>".$optJns."</select> </td>	 
	  <td>".$_SESSION['lang']['keluarga']."</td>
	  <td><select id=karyawanid style='width:200px;' onchange=\"getFamily(this.options[this.selectedIndex].value);\">".$optKar."</select>
	      <input type=hidden value=insert id=method>
	  </td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['yangberobat']."</td>
	  <td><select id=ygberobat style='width:200px;'><option></option></select> </td>	 
	  <td>".$_SESSION['lang']['rumahsakit']."</td>
	  <td><select id=rs style='width:200px;'>".$optRs."</select></td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['diagnosa']."</td>
	  <td><select id=diagnosa style='width:200px;'>".$optDiagnosa."</select></td>	  	 
	  <td>".$_SESSION['lang']['klaim']."</td>
	  <td><select id=klaim style='width:200px;'>".$optklaim."</select></td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['hariistirahat']."</td>
	  <td><input type=text class=myinputtext id=hariistirahat value=1 onkeypress=\"return angka_doang(event);\" maxlength=2 style='width:50px;'>
	      ".$_SESSION['lang']['tanggal']."<input type=text id=tanggal value='".date('d-m-Y')."' size=10 maxlength=10 onkeypress=\"return false\" onmouseover=setCalendar(this) class=myinputtext>
	  </td>	  	 
	  <td>".$_SESSION['lang']['keterangan']."</td>
	  <td><input type=text class=myinputtext id=keterangan onkeypress=\"return tanpa_kutip(event);\" style='width:200px'; ></td>
	 </tr>
	 </table>
	 <fieldset>
	  <legend>".$_SESSION['lang']['biayabiaya']."</legend>
	  <table>
	  <tr>
	    <td>".$_SESSION['lang']['biayars']."</td><td><input type=text id=byrs class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>
	    <td>".$_SESSION['lang']['biayapendaftaran']."</td><td><input type=text id=byadmin class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>		
	  </tr>
	  <tr>
	    <td>".$_SESSION['lang']['biayalab']."</td><td><input type=text id=bylab class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>
	    <td>".$_SESSION['lang']['biayaobat']."</td><td><input type=text id=byobat class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>		 
	  </tr>	
	  <tr>
	    <td>".$_SESSION['lang']['biayadr']."</td><td><input type=text id=bydr class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>	 
		<td>".$_SESSION['lang']['totalbiaya']."</td><td><input type=text id=total disabled class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=15 value=0 onblur=\"change_number(this);calculateTotal();\"></td>	 
	  </tr>		  	   
	  </table>
	  
	 </fieldset> 
	<input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=savePengobatan() id=mainsavebtn>
	<input type=button  class=mybutton value='".$_SESSION['lang']['new']."' onclick=clearForm();>
	</fieldset>";
$arr[1]="<fieldset>
	  <legend>".$_SESSION['lang']['obatobat']."</legend>
	  <table>
	  <tr>
	    <td>".$_SESSION['lang']['namaobat']."</td><td><input type=text id=namaobat class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=45></td>	  </tr>	  	   
	  </table>
	  <input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=saveObat()>
	  <fieldset>
	    <legend>".$_SESSION['lang']['list']."</legend>
		<div>
		 <table class=sortable cellspacing=1 border=0>
		  <thead>
		   <tr class=rowheader>
		    <td>No.</td>
			<td>".$_SESSION['lang']['notransaksi']."</td>
			<td>".$_SESSION['lang']['namaobat']."</td>
			<td></td>
		   </tr>
		  </thead>
		  <tbody id=container1>
		  </tbody>
		  <tfoot>
		  </tfoot>
		 </table>
		</div>
	  </fieldset>
	 </fieldset> 	 
	 ";
//ambil daftar pengobatan dengan tahun sekarang
$str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id
	  where a.tahunplafon=".date('Y')." 
	  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  order by a.updatetime desc, a.tanggal desc";
$res=mysql_query($str);

$arr[2]="<fieldset>
	  <legend>".$_SESSION['lang']['list']."</legend>
      <div style='width:900px;height:350px;overflow:scroll;'>
	  ".$_SESSION['lang']['thnplafon'].":<select id=optplafon onchange=loadPengobatan(this.options[this.selectedIndex].value)>".$optthn."</select>
	  <table class=sortable cellspacing=1 border=0 width=1500px>
	  <thead>
	    <tr class=rowheader>
		<td width=50></td>
		  <td>No</td>
		  <td width=100>".$_SESSION['lang']['notransaksi']."</td>
		  <td width=50>".$_SESSION['lang']['periode']."</td>
		  <td width=30>".$_SESSION['lang']['tanggal']."</td>
		  <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
		  <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
		  <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
		  <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
		  <td>".$_SESSION['lang']['dibayar']."</td>
		  <td>".$_SESSION['lang']['diagnosa']."</td>
		  <td>".$_SESSION['lang']['keterangan']."</td>
		</tr>
	  </thead>
	  <tbody id='container'>";
	  $no=0;
	  while($bar=mysql_fetch_object($res))
	  {
	   $no+=1;
	   $arr[2].="<tr class=rowcontent>
	   <td>";
	   
	   if($bar->posting==0)
	   {
	   	 $arr[2].="<img src=images/close.png class=resicon  title='delete' onclick=deletePengobatan('".$bar->notransaksi."')>";
	   }
	     $arr[2].="&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)>";
	   
	   $arr[2].="</td><td>".$no."</td>
		  <td>".$bar->notransaksi."</td>
		  <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
		  <td>".tanggalnormal($bar->tanggal)."</td>
		  <td>".$bar->namakaryawan."</td>
		  <td>".$bar->namars."[".$bar->kota."]"."</td>
		  <td>".$bar->kodebiaya."</td>
		  <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>
		  <td align=right>".number_format($bar->jlhbayar,2,'.',',')."</td>
		  <td>".$bar->ketdiag."</td>
		  <td>".$bar->keterangan."</td>
		</tr>";	  	
	  }
$arr[2].="</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 </div>
	 </fieldset> 	 
	 ";	 
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['obatobat'];
$hfrm[2]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$arr,100,900);
CLOSE_BOX();
echo close_body();
?>