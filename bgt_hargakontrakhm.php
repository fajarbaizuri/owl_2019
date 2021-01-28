<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/bgt_hargakontrakhm.js'></script>

<?php
$opt="<option value='KONTRAK'>Kontrak</option>";
$opt.="<option value='KEND'>Kendaraan</option>";
$opt.="<option value='AB'>Alat Berat</option>";
									
OPEN_BOX('',"<b>Budget Kendaraan HM</b>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				
				<tr><td width=100>".$_SESSION['lang']['budgetyear']."</td><td>:</td></td><td><input type=text id=thn size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:100px;\"></td></tr>
				
				<tr><td>Tipe<td>:</td></td><td><select id=tipe style=\"width:100px;\" >".$opt."</select></td></tr>
				
				<tr><td>Harga Satuan</td><td>:</td></td><td><input type=text id=hs size=10 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=6 style=\"width:100px;\"> </td></tr>
				
				<tr><td></td></tr>
				<tr><td></td></tr>
				<tr><td></td></tr>
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=cancel()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";

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
			 <td align=center style='width:25px;'>No</td>
			 <td align=center style='width:100px;'>Tahun Budget</td>
			 <td align=center>Tipe</td>
			 <td align=center style='width:50px;'>Harga Satuan</td>
			 <td align=center style='width:20px;'>Action</td></tr>
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