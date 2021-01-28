<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src=js/sdm_pembagianCatu.js></script>
<?

$arr="##periodeGaji##kdOrg##idKaryawan##jmlhHk##kdeBarang##hrgSatuan##jmlh";
include('master_mainMenu.php');
OPEN_BOX();
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPrd="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$qPrd=mysql_query($sPrd) or die(mysql_error());
while($rPrd=mysql_fetch_assoc($qPrd))
{
	$optPeriode.="<option value=".$rPrd['periode'].">".substr(tanggalnormal($rPrd['periode']),1,7)."</option>";
}

	$optORg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' or induk='".$_SESSION['empl']['lokasitugas']."'";
	$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$optORg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
	}
echo"
<fieldset>
     <legend><b>Pembagian Catu</b></legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['periode']."</td>
	   <td><select id='periodeGaji' name='periodeGaji' style=\"width:150px;\" >".$optPeriode."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['lokasitugas']."</td>
	   <td><select id='kdOrg' name='kdOrg' style=\"width:150px;\" onchange=\"getKary(0)\" >".$optORg."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['namakaryawan']."</td>
	   <td><select id='idKaryawan' name='idKaryawan'style=\"width:150px;\" onchange=\"getHk()\" ><option value=''></option></select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['jumlahhk']."</td>
	   <td> <input type=text id=jmlhHk name=jmlhHk class=myinputtext style=\"width:150px;\" onblur=\"calCulate()\" onkeypress=\"return angka_doang(event);\" maxlength=2 /><input type=\"hidden\" id=oldHk name=oldHk /></td>
	 </tr> 
	 <tr>
	   <td>".$_SESSION['lang']['namabarang']."</td>
	   <td><input type=text class=myinputtext id=nmBrng name=nmBrng onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" onclick=\"searchBrg('".$_SESSION['lang']['find']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=cariBrg></div>',event)\"; readonly /> <input type='hidden' id='kdeBarang' name='kdeBarang' /></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['hargasatuan']."</td>
	   <td><input type=text class=myinputtext id=hrgSatuan name=hrgSatuan  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['jumlah']."</td>
	   <td><input type=text class=myinputtext id=jmlh   name=jmlh  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 disabled >&nbsp;<span id=satuan name=satuan></span><input type=hidden id=jmlhSatuan name=jmlhSatuan /></td>
	 </tr> 
	   
	 </table>
	
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveIsi('sdm_slave_pembagianCatu','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();
$optPeriode2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPrd2="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
$qPrd2=mysql_query($sPrd2) or die(mysql_error());
while($rPrd2=mysql_fetch_assoc($qPrd2))
{
	$optPeriode2.="<option value=".$rPrd2['periode'].">".substr(tanggalnormal($rPrd2['periode']),1,7)."</option>";
}
$arrPlokal="##txtsearch##kodeLksi##prdGaji";
echo"
<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>  <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."' style='width:100px;cursor:pointer;' onclick=displayList()></td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['namakaryawan'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['lokasitugas'].":<select id='kodeLksi' name='kodeLksi' style=\"width:150px;\" >".$optORg."</select>";
			echo $_SESSION['lang']['periode'].":<select id=prdGaji name=prdGaji>".$optPeriode2."</select>";
			echo"<button class=mybutton onclick=cariData()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> 
<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['periode']."</td>
	   <td>".$_SESSION['lang']['lokasitugas']."</td>
	   <td>".$_SESSION['lang']['namakaryawan']."</td>
	   <td>".$_SESSION['lang']['namabarang']."</td>
	   <td>".$_SESSION['lang']['hargasatuan']."</td>
	   <td>".$_SESSION['lang']['jumlah']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
echo"<script>loadData()</script>";
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>