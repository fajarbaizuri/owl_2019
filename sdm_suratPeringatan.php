<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/sdm_sp.js></script>
<?
OPEN_BOX('',$_SESSION['lang']['sutarperingatan']);
$opts='';
$str="select * from ".$dbname.".sdm_5jenissp order by kode";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	
	
	$opts.="<option value='".$bar->kode."'>".$bar->keterangan."</option>";
}

//get karyawan
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
  $str=" select karyawanid,namakaryawan,bagian from ".$dbname.".datakaryawan
       where alokasi=1 order by namakaryawan";	
}
else
{
 $str=" select karyawanid,namakaryawan,bagian from ".$dbname.".datakaryawan
       where left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and (tanggalkeluar>='".date('Y-m-d')."' or tanggalkeluar='0000-00-00')
	   order by namakaryawan";
}
$optkar="<option value=''></option>";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optkar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." [".$bar->karyawanid."] -[".$bar->bagian."]</option>";
}

$paragraf3=readTextFile('config/sp_paragraf3.lst');
$paragraf4=readTextFile('config/sp_paragraf4.lst');
//=========================
$frm[0].="
     <fieldset>
	  <legend>".$_SESSION['lang']['form']."</legend>
     <table>
	  <tr> 	 
		 <td>
		 <input type=hidden value='insert' id=method>
		 <input type=hidden value='' id=nosp>
		 ".$_SESSION['lang']['jenissp']."</td><td><select id=jenissp>".$opts."</select></td>
	  </tr>
	  <tr> 	 
		 <td>".$_SESSION['lang']['karyawan']."</td><td><select id=karyawanid>".$optkar."</select></td>
	  </tr>
	  <tr> 	 
		 <td>".$_SESSION['lang']['tanggalsurat']."</td><td><input type=text id=tanggalsp size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)></td>
	  </tr>
	  <tr> 	 
		 <td>".$_SESSION['lang']['masaberlaku']."</td><td><select id=masaberlaku>
		       <option value='1'>1</option>
			   <option value='2'>2</option>
			   <option value='3'>3</option>
			   <option value='6'>6</option>
			   <option value='9'>9</option>
			   <option value='12'>12</option>
			   </select>".$_SESSION['lang']['bulan']."
			   
			   </td>
	  </tr>
     </table>
	 <table>
	   <tr>
	    <td style='valign:top'>Paragraf 1</td><td><textarea id=paragraf1 onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3></textarea></td>
	   </tr>	
	   <tr>	
		<td style='valign:top'>".$_SESSION['lang']['pelanggaran']."</td><td><textarea id=pelanggaran onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3></textarea></td>
	   </tr>	
	   <tr>
		<td style='valign:top'>Paragraf 3</td><td><textarea id=paragraf3 onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3>".$paragraf3."</textarea></td>
	   </tr>	
	   <tr>
	   	<td style='valign:top'>Paragraf 4</td><td><textarea id=paragraf4 onkeypress=\"return tanpa_kutip(event);\" cols=80 rows=3>".$paragraf4."</textarea></td>
	   </tr>
	 </table>
	 <table>
	 <tr>
	 <td>
	     ".$_SESSION['lang']['penandatangan']."
	 </td>
	 <td>
	    <input type=text class=myinputtext id=penandatangan size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
	 </td> 
	 </tr>
	 <tr>
	 <td>
	     ".$_SESSION['lang']['functionname']."
	 </td>
	 <td>
	    <input type=text class=myinputtext id=jabatan size=25 maxlength=50 onkeypress=\"return tanpa_kutip(event);\">
	 </td> 
	 </tr>	 
	 <tr>
	 <td>
	    ".$_SESSION['lang']['tembusan']."(i)
	 </td>
	 <td>
	    <input type=text class=myinputtext id=tembusan1 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
	 </td> 
	 </tr>
	 <tr>
	 <td>
	     ".$_SESSION['lang']['tembusan']."(ii)
	 </td>
	 <td>
	    <input type=text class=myinputtext id=tembusan2 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
	 </td> 
	 </tr>
	 <tr>
	 <td>
	     ".$_SESSION['lang']['tembusan']."(iii)
	 </td>
	 <td>
	    <input type=text class=myinputtext id=tembusan3 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
	 </td> 
	 </tr>
	 <tr>
	 <td>
	     ".$_SESSION['lang']['tembusan']."(iiii)
	 </td>
	 <td>
	    <input type=text class=myinputtext id=tembusan4 size=25 maxlength=35 onkeypress=\"return tanpa_kutip(event);\">
	 </td> 
	 </tr>	 	 	 	 
	 </table>
	 <center>
	   <button class=mybutton onclick=saveSP()>".$_SESSION['lang']['save']."</button>
	   <button class=mybutton onclick=window.location.reload()>".$_SESSION['lang']['new']."</button>
	 </center>
	 </fieldset>";

$frm[1]="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
	  <button class=mybutton onclick=cariSP(0)>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['nomorsk']."</td>
	  <td>".$_SESSION['lang']['karyawan']."</td>
	  <td>".$_SESSION['lang']['tanggalsurat']."</td>
	  <td>".$_SESSION['lang']['tanggalsampai']."</td>
	  <td>".$_SESSION['lang']['tipetransaksi']."</td>
	  <td>".$_SESSION['lang']['dbuat_oleh']."</td>
	  <td></td>
	  </tr>
	  </head>
	   <tbody id=containerlist>
	   </tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>";

$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];
	 
drawTab('FRM',$hfrm,$frm,100,900);
CLOSE_BOX();
echo close_body('');
?>