<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<!--<link rel=stylesheet type=text/css href=style/zTable.css>-->
<script language="javascript" src="js/sdm_5klprasarana.js"></script>
<fieldset>
<legend><b><?php echo "Kelompok Prasarana"?></b></legend>
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td><?php echo "Kode Prasarana"?></td>
		<td>:</td>
		<td><input type="text" class="myinputtext" id="kode_grp_cus" onkeypress="return tanpa_kutip(event);" /></td>
	</tr>
	<tr>
		<td><?php echo "Nama Prasarana"?></td>
		<td>:</td>
		<td><input type="text" class="myinputtext" id="klmpk_cust" onkeypress="return tanpa_kutip(event);"  /></td>
	</tr>
	<input type="hidden" value="insert" id="method" />
	<tr>
		<td colspan="3" align="center">
		<button class=mybutton onclick=simpanKlmpkpra()><?php echo $_SESSION['lang']['save']?></button>
	 <button class=mybutton onclick=batalKlmpkpra()><?php echo $_SESSION['lang']['cancel']?></button>
		</td>
	</tr>
</table>
</fieldset>
<?php CLOSE_BOX();
?>

<?php OPEN_BOX();?>
<fieldset>
     <!--<legend><b><?php //echo $_SESSION['lang']['pmn_4klcustomer']; ?></b></legend>-->
	 <table class=sortable cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo "Kode Prasarana"?></td>
	 <td><?php echo "Nama Prasarana" ?></td>
	 <td colspan="2">Action</td>
	 </tr>
	 </thead>
	 <tbody id=containersatuan>
	 
	 <?php
	 
	 
	  $str="select * from ".$dbname.".sdm_5kl_prasarana order by kode desc";
	  if($res=mysql_query($str))
	  {
		while($bar=mysql_fetch_object($res))
		{
		
			$no+=1;
			echo"<tr class=rowcontent>
				  <td>".$no."</td>
				  <td>".$bar->kode."</td>
				  <td>".$bar->nama."</td>
				  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->nama."');\"></td>
				  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlmpkpra('".$bar->kode."','".$bar->nama."');\"></td>
				 </tr>";
		}	 	   	
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}

	?>	
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>
     </fieldset>
<!--<FORM NAME = "kelompok supplier">
<p align="center"><u><b><font face="Verdana" size="4" color="#000080">Kelompok Customer</font></b></u></p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="87%" id="AutoNumber1" height="115">
  <tr>
    <td width="24%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Kode</font></td>
    <td width="46%" height="1">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="12" name="koderekening">&nbsp; </font>
    </td>
    <td width="16%" height="1">
    <p style="margin-top: 0; margin-bottom: 0">
    </td>
  </tr>
  <tr>
    <td width="24%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">Kelompok</font></td>
    <td width="46%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys"> 
    <input type=text size="56" name="tanggal"></font></td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"></td>
  </tr>
  <tr>
    <td width="24%" height="22">
    <p style="margin-top: 0; margin-bottom: 0">
    <font face="Arial">No Akun</font></td>
    <td width="46%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"><select size="1" name="D1"></select></td>
    <td width="16%" height="22">
    <p style="margin-top: 0; margin-bottom: 0"></td>
  </tr>
  <tr>
    <td width="24%" height="22">&nbsp;</td>
    <td width="46%" height="22">&nbsp;</td>
    <td width="16%" height="22">&nbsp;</td>
  </tr>
  </table>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<p style="margin-top: 0; margin-bottom: 0"><font face="Fixedsys">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Simpan" name="Simpan">
<input type="reset" value="Batal" name="Batal"></font></p>
<p style="margin-top: 0; margin-bottom: 0">&nbsp;</p>
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber2"><tr><td width="16%" align="center">Kode</td><td width="16%" align="center">Kelompok</td><td width="17%" align="center">No Akun</td></tr><tr><td width="16%">&nbsp;</td><td width="16%">&nbsp;</td><td width="17%">&nbsp;</td>
</tr></table>
<p><font face="Fixedsys">&nbsp;&nbsp;&nbsp; &nbsp;</font></p>-->

<?
CLOSE_BOX();
echo close_body();
?>