<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/log_transaksi.js></script>
<?
include('master_mainMenu.php');
if(isTransactionPeriod())//check if transaction period is normal
{

/*
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
*/	

OPEN_BOX('',"<b>".$_SESSION['lang']['penerimaanbarang'].":</b>");

$frm[0]='';
$frm[1]='';
echo "<fieldset><legend>";
echo" <b>".$_SESSION['lang']['periode'].": <span id=displayperiod>".tanggalnormal($_SESSION['org']['period']['start'])." - ".tanggalnormal($_SESSION['org']['period']['end'])."</span></b>";
echo"</legend>";

if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='GUDANG' order by namaorganisasi";
else
   $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where (induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe='GUDANG' order by namaorganisasi";

$res=mysql_query($str);
$optsloc="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optsloc.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

echo"<fieldset>
     <legend>
	 ".$_SESSION['lang']['daftargudang']."
     </legend>
	  ".$_SESSION['lang']['pilihgudang'].": <select id=sloc>".$optsloc."</select>
	   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>
	   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>
	  
	 </fieldset>";

$frm[0].="<fieldset><legend>".$_SESSION['lang']['header']."</legend>";

$frm[0].=$_SESSION['lang']['peringatanretur']."
     <table cellspacing=1 border=0>
     <tr>
		<td>".$_SESSION['lang']['momordok']."</td>
		<td><input type=text id=nodok size=25 disabled class=myinputtext></td>	 
	    <td>".$_SESSION['lang']['tanggal']."</td><td>
		     <input type=text class=myinputtext id=tanggal size=25 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>
		</td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['supplier']."</td><td><input type=hidden value='' id=idsupplier><input type=text id=supplier class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled></td>
	 <td>".$_SESSION['lang']['suratjalan']."</td><td><input type=text id=nosj class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>
	 </tr>
	 <tr>
	 <td>".$_SESSION['lang']['faktur']."</td><td><input type=text id=nofaktur class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>
	 <td>".$_SESSION['lang']['nopo']."</td><td><input type=text id=nopo class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\">
	    <img src=images/zoom.png title='".$_SESSION['lang']['find']."' class=resicon onclick=cariPO('".$_SESSION['lang']['find']."',event)>
	    <button class=mybutton onclick=getPOSupplier() id=btnheader>".$_SESSION['lang']['tampilkan']."</button>
	 </td>
	 <td></td>
	 </tr>
	 </table>";
//==================masukkan variable periode gudang
//$sess=$_SESSION['gudang'];
foreach($_SESSION['gudang'] as $key=>$val)
{
 //  echo	$sess[$key]['start'];

	$frm[0].="<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>
	     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>
		";
}	 
$frm[0].="</fieldset>
    <fieldset>
	   <legend>".$_SESSION['lang']['detail']."</legend>
	   <div id=container>
	   </div>
	 </fieldset>
	 ";
	 
$frm[1].="<fieldset>
	   <legend>".$_SESSION['lang']['list']."</legend>
	  <fieldset><legend></legend>
	  ".$_SESSION['lang']['cari_transaksi']."
	  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>
	  <button class=mybutton onclick=cariBapb()>".$_SESSION['lang']['find']."</button>
	  </fieldset>
	  <table class=sortable cellspacing=1 border=0>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['sloc']."</td>
	  <td>".$_SESSION['lang']['tipe']."</td>
	  <td>".$_SESSION['lang']['momordok']."</td>
	  <td>".$_SESSION['lang']['tanggal']."</td>
	  <td>".$_SESSION['lang']['pt']."</td>
	  <td>".$_SESSION['lang']['nopo']."</td>	
	  <td>".$_SESSION['lang']['supplier']."</td> 
	  <td>".$_SESSION['lang']['dbuat_oleh']."</td>
	  <td>".$_SESSION['lang']['posted']."</td>
	  <td></td>
	  </tr>
	  </head>
	   <tbody id=containerlist>
	   </tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	 </fieldset>	 
	 ";	 
//========================
$hfrm[0]=$_SESSION['lang']['penerimaanbarang'];
$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//===============================================	 
}
else
{
	echo " Error: Transaction Period missing";
}
CLOSE_BOX();
close_body();
?>