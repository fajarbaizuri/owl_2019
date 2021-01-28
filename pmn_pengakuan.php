<!--ind-->

<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pmn_pengakuan.js'></script>



<?php
include('master_mainMenu.php');					


$optsup="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT nokontrak FROM ".$dbname.".pmn_kontrakjual";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optsup.="<option value=".$data['nokontrak'].">".$data['nokontrak']."</option>";
			}	
			
$opteks="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT supplierid,namasupplier FROM ".$dbname.".log_5supplier where supplierid like '%K%' ";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$opteks.="<option value=".$data['supplierid'].">".$data['namasupplier']."</option>";
			}			
			
?>


<?php
OPEN_BOX();
//OPEN_BOX('',"<b>Pengakuan Penjualan </b>");

echo"<fieldset style='float:left;'>
		<legend><b>Pengakuan Penjualan</b></legend> 
			<table border=0 cellpadding=1 cellspacing=0>
								
				<tr>
					<td>No. Kontrak</td>
					<td>:</td>
					<td><select id=nokon style=\"width:125px;\" >".$optsup."</select></td>
					<td width=25></td>
					
					<td>Internal ALB</td>
					<td>:</td>
					<td><input type=text id=intalb  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
					<td width=25></td>
					
					<td>".$_SESSION['lang']['tanggal']."</td>
					<td>:</td>
					<td><input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  maxlength=10 style=width:150px;/>
				</tr>
				
				<tr>
					<td>Ekspeditor</td>
					<td>:</td>
					<td><select id=eks style=\"width:125px;\" >".$opteks."</select></td>
					<td></td>
					
					<td>Netto VHC</td>
					<td>:</td>
					<td><input type=text id=netvhc  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
				</tr>
				
				<tr>
					<td>VHC</td>
					<td>:</td>
					<td><input type=text id=vhc  class=myinputtext onkeypress=\"return tanpa)kutip(event);\"  style=\"width:125px;\"></td>
					<td></td>
					
					<td>Netto Eksternal</td>
					<td>:</td>
					<td><input type=text id=neteks  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
					
				</tr>
				
				<tr>
					<td>Netto Internal</td>
					<td>:</td>
					<td><input type=text id=netint  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
					<td></td>
					
					<td>Eksternal Air</td>
					<td>:</td>
					<td><input type=text id=eksair  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
				</tr>
				
				<tr>
					<td>Internal Air</td>
					<td>:</td>
					<td><input type=text id=intair  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
					<td></td>
					
					<td>Eksternal Kotoran</td>
					<td>:</td>
					<td><input type=text id=ekskot  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
				</tr>
				
				<tr>
					<td>Internal Kotoran</td>
					<td>:</td>
					<td><input type=text id=intkot  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
					<td></td>
					
					<td>Eksternal ALB</td>
					<td>:</td>
					<td><input type=text id=eksalb  class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:125px;\"></td>
				</tr>
				
				<tr>
					<td></td><td></td>
					<td>
					<br />
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=hapus()>Hapus</button></td>
					</tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>";
CLOSE_BOX();
?>



<?php
OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
//echo "<div id=container>";
echo" <div id=container style='width:1200px;height:450px;overflow:scroll'>";	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center>No</td>
			 <td align=center>No. Kontrak</td>
			 <td align=center>Ekspeditor</td>
			 <td align=center>Tanggal</td>
			 <td align=center>VHC</td>
			 <td align=center>Netto Internal</td>
			 <td align=center>Internal Air</td>
			 <td align=center>Internal Kotoran</td>
			 <td align=center>Internal ALB</td>
			 <td align=center>Netto VHC</td>
			 <td align=center>Netto Eksternal</td>
			 <td align=center>Eksternal Air</td>
			 <td align=center>Eksternal Kotoran</td>
			 <td align=center>Eksternal ALB</td>
			 <td align=center>Aksi</td>
		 </tr>
		 </thead>
		 <tbody id='containerData'><script>loadData()</script>";
        
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
echo "</fieldset>";
CLOSE_BOX();
echo close_body();					
?>