<?//@Copy ANGELWHITE
require_once('master_validation.php');
include('lib/nangkoelib.php');

echo open_body();
?>
<script language=javascript1.2 src="js/keu_hubrk_ian.js"></script>
<script language=javascript1.2 src="js/wz_tooltip.js"></script>
<?
include('master_mainMenu.php');
//OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanbukubesar']).'</b>');
OPEN_BOX();

//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".keu_jurnaldt
      order by periode desc";
	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
$optper="";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}
/*
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{   
 * 
 */
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
		
//echo"<pre>";
//print_r($_SESSION['empl']['kodeorganisasi']);
//echo"</pre>";

        //=================ambil gudang;  
	

        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                        where induk='".$_SESSION['empl']['kodeorganisasi']."' ";

        $res=mysql_query($str);
        $optgudang="<option value='".$_SESSION['empl']['kodeorganisasi']."'>Seluruhnya</option>";
        while($bar=mysql_fetch_object($res))
        {
                $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
				
        }
	 
	 

?>
<fieldset style="float: left;">
<legend><b><?php echo "Hubungan Rekening Koran"?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td>
<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value,document.getElementById('gudang1').value,document.getElementById('gudang2').value)>
<?php echo $optpt; ?></select></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']." "?></label></td><td><select id=gudang1 style='width:200px;' ><?php echo $optgudang; ?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']." R/K"?></label></td><td><select id=gudang2 style='width:200px;' ><?php echo $optgudang; ?></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td><td><input type="text" class="myinputtext" id="tgl1" name="tgl1"   onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;"  /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type="text" class="myinputtext" id="tgl2" name="tgl2"   onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>


<!--<tr height="20"><td colspan="2">&nbsp;</td></tr>-->
<tr height="20"><td colspan="2"> <button class=mybutton onclick="getLapHubRK()"><?php echo $_SESSION['lang']['proses'] ?></button></td></tr>

<!--<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2rekapabsen','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2rekapabsen.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>-->

</table>
</fieldset>
<?
/*

echo"<fieldset>
     <legend>".$_SESSION['lang']['laporanbukubesar']." v1</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select><br>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select><br>
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
         ".$_SESSION['lang']['tglcutisampai']."
         ".$_SESSION['lang']['periode']." : "."<select id=periode1 onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getLaporanBukuBesar()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
 */
CLOSE_BOX();
OPEN_BOX('','Result:');
/*
echo"<span id=printPanel style='display:none;'>
     <img onclick=jurnalv1KeExcel(event,'keu_laporanBukuBesarv1_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=jurnalv1KePDF(event,'keu_laporanBukuBesarv1_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center>".$_SESSION['lang']['nomor']."</td>
			  <td align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['noakun']."</td>
			  <td align=center>".$_SESSION['lang']['keterangan']."</td>
			  <td align=center>".$_SESSION['lang']['saldoawal']."</td>
			  <td align=center>".$_SESSION['lang']['debet']."</td>
			  <td align=center>".$_SESSION['lang']['kredit']."</td>
			  <td align=center>".$_SESSION['lang']['saldoakhir']."</td>
			  <td align=center>".$_SESSION['lang']['kodeblok']."</td>
			</tr>  
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
*/
echo"<span id=printPanel style='display:none;'>
     <img onclick=jurnalv1KeExcel(event,'keu_laporanhubrkvIAN_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table  cellspacing=1 border=0 width=100%>
	     <thead>
			<tr class=rowcontent>
				<td align=center >No.</td>
				<td align=center >No.Ref</td>
				<td align=center >Tanggal</td>
				<td align=center >Keterangan</td>
				<td align=center >Debet</td>
				<td align=center >Kredit</td>
				<td align=center >Saldo</td>
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