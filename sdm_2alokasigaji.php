<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_2alokasigaji.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['alokasigaji']).'</b>');
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";

//get existing period
if($_SESSION['empl']['tipelokasitugas']=='HOLDING') 
$str="select distinct periode from ".$dbname.".sdm_5periodegaji
      order by periode desc"; else
$str="select distinct periode from ".$dbname.".sdm_5periodegaji
      where kodeorg = '".$_SESSION['empl']['lokasitugas']."'
      order by periode desc";
$res=mysql_query($str);
//$optper='';
while($bar=mysql_fetch_object($res))
{
//	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
/*
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
	  order by namaorganisasi";
$res=mysql_query($str);
$optpt="";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

*/
//=================ambil unit;  
if($_SESSION['empl']['tipelokasitugas']=='HOLDING') 
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where length(kodeorganisasi) = 4
	  order by namaorganisasi"; else
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where length(kodeorganisasi) = 4 and kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."'
	  order by namaorganisasi";

$res=mysql_query($str);
//$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
$optunit="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
//	 ".$_SESSION['lang']['pt']."<select id=pt style='width:150px;' onchange=hideById('printPanel')>".$optpt."</select>

echo"<fieldset>
     <legend>".$_SESSION['lang']['alokasigaji']."</legend>
	 ".$_SESSION['lang']['unit']."<select id=unit style='width:150px;' onchange=ambilPeriode2(this.options[this.selectedIndex].value)>".$optunit."</select>
	 ".$_SESSION['lang']['periode']."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getAlokasiGaji()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
//	 <img onclick=hutangSupplierKePDF(event,'log_laporanhutangsupplier_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>

echo"<span id=printPanel style='display:none;'>
     <img onclick=alokasiGajiKeExcel(event,'sdm_laporanAlokasiGaji_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['namaakun']."</td>
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['kodeblok']."</td>
			  <td align=center>".$_SESSION['lang']['kodevhc']."</td>
			</tr>  
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>