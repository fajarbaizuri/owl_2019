<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/pabrik_5hpp.js'></script>


<?php


#untuk bulan
for($b=1;$b<13;)
{
	if(strlen($b)<2)
	{
		$b="0".$b;
	}
	$bulan.="<option value=".$b.">".$b."</option>";
	$b++;
}

#tahun

$tahunini=date('Y');
$thndpn=$tahunini+5;
$thnkmrn=$tahunini-5;
#+
for($t=$thnkmrn;$t<$tahunini;)
{
	$tahun.="<option value=".$t.">".$t."</option>";
	$t++;
}
#-
for($t=$tahunini;$t<$thndpn;)
{
	$tahun.="<option value=".$t.">".$t."</option>";
	$t++;
}

$optproduk="<option value='CPO'>CPO</option>";
$optproduk.="<option value='PK'>Kernel</option>";									
?>


<?php
OPEN_BOX();

echo"<br /><fieldset style='float:left;'>
		<legend>HPP</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>Produk</td> 
					<td>:</td>
					<td><select id=produk style=\"width:150px;\">".$optproduk."</select></td>
				</tr>
				
				<tr>
					<td>Periode</td>
					<td>:</td>
					<td><select id=tahun>".$tahun."</select><select id=bulan name=bulan>".$bulan."</select></td>

				</tr>
				
				<tr>
					<td>Jumlah</td>
					<td>:</td>
					<td><input type=text id=jumlah onkeypress=\"return angka_doang(event);\" class=myinputtext style=\"width:150px;\">%</td>
				</tr>

				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=cancel()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";


echo"
<fieldset style='float:left;'><legend>Filter data tersimpan</legend>
<table>
		<tr>
			<td>Tahun</td>
			<td>:<select id='thnsort' onchange='ubah_list()'>".$tahun."</select></td>
		</tr>
	</table>
</fieldset>
";
CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
//echo "<div id=container>";
echo"


	<div id=container style='width:750px;height:400px;overflow:scroll'>
		<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center>No</td>
			 <td align=center>Produk</td>
			 <td align=center>Periode</td>
			 <td align=center>Jumlah</td>
			 <td align=center style='width:20px;'>*</td></tr>
		 </thead>
		 <tbody id='containerData'><script>loadData()</script>
		 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>
	</div>";
echo close_theme();
echo "</fieldset>";
CLOSE_BOX();
echo close_body();					
?>