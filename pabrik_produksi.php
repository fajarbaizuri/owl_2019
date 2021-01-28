<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/pabrik_produksi.js></script>
<?
include('master_mainMenu.php');


OPEN_BOX('',"<b>".$_SESSION['lang']['produksipabrik'].":</b>");
//get org
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' and tipe='PABRIK'";
$res=mysql_query($str);
$optorg='';
while($bar=mysql_fetch_object($res))
{
	$optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
echo "<fieldset style='width:700px;'>
        <legend>".$_SESSION['lang']['form'].":</legend>
		<table><tr><td>
		
		<table>
		   <tr>
		     <td>
			    ".$_SESSION['lang']['kodeorganisasi']."
			 </td>
		     <td>
			    <select id=kodeorg>".$optorg."</select>
			 </td>
		   </tr>
		   <tr> 
			 <td>".$_SESSION['lang']['tanggal']."</td>
			 <td><input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" onchange=\"getBeratBersih()\">
			 </td>	
		     <td>		 
		 </tr>
		   <tr>
		     <td>
			    ".$_SESSION['lang']['sisatbskemarin']."
			 </td>
		     <td>
			    <input type=text id=sisatbskemarin value=0 class=myinputtextnumber onblur=hitungSisa() readonly maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg.
			 </td>
		   </tr>
		   <tr> 
		     <td>
			    ".$_SESSION['lang']['tbsmasuk']."
			 </td>
			 <td>
			    <input type=text id=tbsmasuk value=0  class=myinputtextnumber onblur=hitungSisa()  maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg. 
			 </td>	 		 
		 </tr>		
		 <tr>
		     <td>
			    ".$_SESSION['lang']['tbsdiolah']."
			 </td>
		     <td>
			    <input type=text id=tbsdiolah value=0  class=myinputtextnumber onblur=hitungSisa() maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">Kg. 
			 </td>		 
		 </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['sisa']."
			 </td>
		     <td>
			    <input type=text id=sisa  value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>Kg. 
			 </td>		 
		 </tr>		  
	  </table>	  
	  </td>
	  <td>  
  	<table>
		<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['cpo']."</legend>
		 <table>
		 <tr><td>
			    ".$_SESSION['lang']['cpo']."(Kg)
			 </td>
			 <td>
			    <input type=text id=oercpo  value=0 onblur=periksaOERCPO(this) class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">Kg. 
			 </td>
		  </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kotoran']."
			 </td>
		     <td>
			    <input type=text id=dirtcpo value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kadarair']."
			 </td>
			 <td>
			    <input type=text id=kadaraircpo value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    FFa
			 </td>
		     <td>
			    <input type=text id=ffacpo value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>		   	   
		</table>
		</fieldset>
		
		</td>
		</tr>
		</table>	
    </td>
	<td>
  	<table>
		<tr>
		<td> 
		 <fieldset><legend>".$_SESSION['lang']['kernel']."</legend>
		 <table>
		 <tr><td>
			    ".$_SESSION['lang']['kernel']."(Kg)
			 </td>
			 <td>
			    <input type=text id=oerpk  value=0 onblur=periksaOERPK(this)  class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">Kg.. 
			 </td>
		  </tr>
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kotoran']."
			 </td>
		     <td>
			    <input type=text id=dirtpk  value=0 onblur=periksaPK(this)  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    ".$_SESSION['lang']['kadarair']."
			 </td>
			 <td>
			    <input type=text id=kadarairpk  value=0 onblur=periksaPK(this)  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>
		 </tr>	
		 <tr>
		     <td>
			    FFa
			 </td>
		     <td>
			    <input type=text id=ffapk  value=0 onblur=periksaPK(this)  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">%. 
			 </td>			 
		 </tr>		   	   
		</table>
		</fieldset>
		
		</td>
		</tr>
		</table>	
			
	
	</td>
	</tr>	  
	  
	</table>	
	
		<center>
		<button id=simpan class=mybutton onclick=simpanProduksi()>".$_SESSION['lang']['save']."</button>
		<button id=edit class=mybutton onclick=update() disabled>Edit</button>
		<button id=batal class=mybutton onclick=bersihkanForm()>Batal</button></center>
	  </fieldset>
	  <input type=hidden id=method>
	  
	 ";
	 
CLOSE_BOX();

OPEN_BOX();
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend>
      <table class=sortable cellspacing=1 border=0 width=100%>
	    <thead>
		  <tr class=rowheader>
		   <td rowspan=2 align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['sisatbskemarin']."</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tbsmasuk']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['tbsdiolah']." (Kg.)</td>
		   <td rowspan=2 align=center>".$_SESSION['lang']['sisa']." (Kg.)</td>
		   <td colspan=5 align=center>".$_SESSION['lang']['cpo']."
		   </td>
		   <td colspan=5 align=center>".$_SESSION['lang']['kernel']."
		   </td>
		   <td rowspan=2 align=center></td>	   
		  </tr>  
		  <tr class=rowheader> 
		   <td align=center>".$_SESSION['lang']['cpo']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa)(%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>
		   
		   <td align=center>".$_SESSION['lang']['kernel']." (Kg)</td>
		   <td align=center>".$_SESSION['lang']['oer']." (%)</td>
		   <td align=center>(FFa) (%)</td>
		   <td align=center>".$_SESSION['lang']['kotoran']." (%)</td>
		   <td align=center>".$_SESSION['lang']['kadarair']." (%)</td>
		  </tr>
		</thead>
		<tbody id=container>";
$str="select a.* from ".$dbname.".pabrik_produksi a
      where kodeorg='".$_SESSION['empl']['lokasitugas']."'
      order by a.tanggal desc limit 31";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
		 echo"<tr class=rowcontent>
		   <td>".$bar->kodeorg."</td>
		   <td>".tanggalnormal($bar->tanggal)."</td>
		   <td align=right>".number_format($bar->sisatbskemarin,0,'.',',')."</td>
		   <td align=right>".number_format($bar->tbsmasuk,0,'.',',')."</td>
		   <td align=right>".number_format($bar->tbsdiolah,0,'.',',.')."</td>
		   <td align=right>".number_format($bar->sisahariini,0,'.',',')."</td>
		   
		   <td align=right>".number_format($bar->oer,2,'.',',')."</td>
		   <td align=right>".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".$bar->ffa."</td>
		   <td align=right>".$bar->kadarkotoran."</td>
		   <td align=right>".$bar->kadarair."</td>
		   
		   <td align=right>".number_format($bar->oerpk,2,'.',',')."</td>
		   <td align=right>".(@number_format(@$bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right>".$bar->ffapk."</td>
		   <td align=right>".$bar->kadarkotoranpk."</td>
		   <td align=right>".$bar->kadarairpk."</td>
		   		   
		   <td>
		     <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delProduksi('".$bar->kodeorg."','".$bar->tanggal."','".$bar->kodebarang."');\">
		   <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar->kodeorg."','".$bar->tanggal."','".$bar->sisatbskemarin."'
		   ,'".$bar->tbsmasuk."','".$bar->tbsdiolah."','".$bar->sisahariini."','".$bar->oer."','".$bar->ffa."','".$bar->kadarkotoran."','".$bar->kadarair."','".$bar->oerpk."',
		   '".$bar->ffapk."','".$bar->kadarkotoranpk."','".$bar->kadarairpk."');\">
		   </td>
		  </tr>";	
}	  
	
$hariini=date("Y");
$awaltahun=$hariini.'-01-01';
$ha="select * from ".$dbname.".pabrik_produksi where tanggal='".$awaltahun."'";
$he=mysql_query($ha);
$hu=mysql_fetch_assoc($he);

echo "Saldo awal TBS tahun ini (Tanggal : ".tanggalnormal($awaltahun).") = ".number_format($hu['sisatbskemarin'])." Kg";		
		
echo"	
		</tbody>
		<tfoot>
		</tfoot>
	  </table>
	  </fieldset>";
CLOSE_BOX();

close_body();
?>