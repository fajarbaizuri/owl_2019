<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/keu_2agingSchedule.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['usiahutang']).'</b>');

//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";

//get existing period
//$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".keu_jurnaldt
//     order by periode desc";
  
	  
//$res=mysql_query($str);
//$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
//$optper='';
#while($bar=mysql_fetch_object($res))
{
#	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='PT'
	  order by namaorganisasi";
$res=mysql_query($str);
$optpt="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

//=================ambil gudang;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
		where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
		or tipe='HOLDING')  and induk!=''";
//$str="select distinct a.kodeorg,b.namaorganisasi from ".$dbname.".setup_periodeakuntansi a
//      left join ".$dbname.".organisasi b
//	  on a.kodeorg=b.kodeorganisasi
//     where b.tipe='KEBUN'
//	  order by namaorganisasi";
$res=mysql_query($str);
$optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
#	$optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

/*
echo"<fieldset>
     <legend>".$_SESSION['lang']['usiahutang']."</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getUsiaHutang()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";

	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>

*/
echo"<fieldset>
     <legend>".$_SESSION['lang']['usiahutang']."</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>
<input type=\"text\" value=\"".$tanggalpivot=date('d-m-Y')."\" class=\"myinputtext\" id=\"tanggalpivot\" name=\"tanggalpivot\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />
	 <button class=mybutton onclick=getUsiaHutang()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
//			  <td rowspan=2 align=center width=60>".$_SESSION['lang']['nilaiinvoice']."</td>
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcel(event,'keu_laporanUsiaHutang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=fisikKePDF(event,'keu_laporanUsiaHutang_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
	     <thead>
		    <tr>
			  <td rowspan=2 align=center width=50>".$_SESSION['lang']['nourut']."</td>
			  <td rowspan=2 align=center width=50>".$_SESSION['lang']['tanggal']."</td>
			  <td rowspan=2 align=center width=200>".$_SESSION['lang']['noinvoice']."<br>".$_SESSION['lang']['namasupplier']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['jatuhtempo']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nopokontrak']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nilaipokontrak']."</td>
			  <td rowspan=2 align=center width=75>".$_SESSION['lang']['nilaiinvoice']."</td>
			  <td rowspan=2 align=center width=100>Belum Jatuh Tempo</td>
			  <td align=center colspan=4 width=400>Sudah Jatuh Tempo</td>
			  <td rowspan=2 align=center width=100>".$_SESSION['lang']['dibayar']."</td>
			  <td rowspan=2 align=center width=50>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>
			</tr>  
		    <tr>
			  <td align=center width=50>1-15 Hari</td>
			  <td align=center width=50>16-30 Hari</td>
			  <td align=center width=50>31-45 Hari</td>
			  <td align=center width=50>over 46 Hari</td>
			</tr>  
		 </thead>
		 <tbody id=container>
			<script>getUsiaHutang()</script>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
	
CLOSE_BOX();

close_body();
?>