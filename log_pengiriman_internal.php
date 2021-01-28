<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/log_pengiriman_internal.js'></script>
<?
$arrData="##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method##nomor_id";
$sql="select namasupplier,supplierid from ".$dbname.".log_5supplier order by namasupplier asc";
    $optSupplier="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $query=mysql_query($sql) or die(mysql_error());
    while($res=mysql_fetch_assoc($query))
    {
       $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
    }
    $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sKary="select  karyawanid,namakaryawan,lokasitugas from ".$dbname.".datakaryawan where tipekaryawan in (0,1,2) order by namakaryawan asc";
    $qKary=mysql_query($sKary) or die(mysql_error());
    while($rKary=mysql_fetch_assoc($qKary))
    {
        $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."-".$rKary['lokasitugas']."</option>";
    }
include('master_mainMenu.php');
OPEN_BOX();

echo"<input type=hidden value=insert id=method><fieldset style=width:350px;float:left;>
     <legend>PENGIRIMAN DOKUMEN/BARANG INTERNAL</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['expeditor']."Expeditor</td>
	   <td><select id=\"id_supplier\" name=\"id_supplier\" style=\"width:150px;\" >".$optSupplier."</select>&nbsp;
         <img src='images/search.png' class=dellicon title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['findRkn']."</legend>".$_SESSION['lang']['namasupplier']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['tgl_kirim']."</td>
	   <td><input type=text class=myinputtext id=tglKrm onmousemove=setCalendar(this.id) onkeypress='return false';  size=10 maxlength=10 style=\"width:150px;\" /></td>
	 </tr>
	 <tr>
	   <td>Jlh.Koli</td>
	   <td><input type=text class=myinputtextnumber id=jlhKoli name=jlhKoli onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" /> </td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['kepada']."</td>
	   <td><select id=\"kpd\" name=\"kpd\" style=\"width:150px;\" onchange=getLok() >".$optKary."</select></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['lokasipenerimaan']."</td>
	   <td><input type=text class=myinputtext id=lokPenerimaan name=lokPenerimaan onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" disabled /></td>
	 </tr> 
         <tr>
	   <td>".$_SESSION['lang']['suratjalan']."</td>
	   <td><input type=text class=myinputtext id=srtJalan name=srtJalan onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\"  /></td>
	 </tr> 
         <tr>
	   <td>".$_SESSION['lang']['biaya']."</td>
	   <td><input type=text class=myinputtextnumber id=biaya name=biaya onkeypress=\"return angka_doang(event);\" style=\"width:150px;\"  /></td>
	 </tr> 
         <tr>
	   <td>".$_SESSION['lang']['keterangan']."</td>
	   <td><input type=text class=myinputtext id=ket name=ket onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\"  /></td>
	 </tr> 
	 </table>
	 
	 <button class=mybutton onclick=saveFranco()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type=hidden id=nomor_id />";
CLOSE_BOX();
OPEN_BOX();

echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['expeditor']."</td>
	   <td>".$_SESSION['lang']['tgl_kirim']."</td>
	   <td>Jlh.Koli</td>
	   <td>".$_SESSION['lang']['kepada']."</td>
	   <td>".$_SESSION['lang']['lokasipenerimaan']."</td>
           <td>".$_SESSION['lang']['suratjalan']."</td>
           <td>".$_SESSION['lang']['biaya']."</td>
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