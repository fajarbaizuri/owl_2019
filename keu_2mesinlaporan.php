<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/keu_laporan.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['mesinlaporan']).'</b>');

//get existing period
$str="select distinct periode from ".$dbname.".keu_jurnalsum_vw
      order by periode desc";
  
	  
$res=mysql_query($str);
$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
while($bar=mysql_fetch_object($res))
{
#	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
	  order by namaorganisasi";
$res=mysql_query($str);
$optpt="";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

//=================ambil gudang;  
$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
      left join ".$dbname.".organisasi b
	  on a.kodeorg=b.kodeorganisasi
      where b.tipe='GUDANG'
	  order by namaorganisasi";
$res=mysql_query($str);
$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
#	$optgudang.="<option value='".$bar->kodeorg."'>".$bar->namaorganisasi."</option>";

}

echo"<fieldset>
     <legend>".$_SESSION['lang']['mesinlaporan']."</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;' onchange=hideById('printPanel')>".$optpt."</select>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getMesinLaporan()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'keu_laporanNeraca_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDF(event,'keu_laporanNeraca_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>".$_SESSION['lang']['perusahaan']."</td>
			  <td align=center>".$_SESSION['lang']['nomor']."</td>
			  <td align=center>".$_SESSION['lang']['namaakun']."</td>
			  <td align=center>".$_SESSION['lang']['tipe']."</td>
			  <td align=center>".$_SESSION['lang']['noakundari']."</td>
			  <td align=center>".$_SESSION['lang']['noakunsampai']."</td>
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