<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
//ind
?>


<script language='JavaScript1.2' src='js/supplier.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<u><b><font face=Verdana size=4 color=#000080>'.$_SESSION['lang']['data'].' '.$_SESSION['lang']['supplier'].' </font></b></u>');
 echo"<fieldset>
      <legend>Input ".$_SESSION['lang']['supplier']."/".$_SESSION['lang']['kontraktor']."</legend>
	  <table>
	  <tr>
	     <td>".$_SESSION['lang']['Type']."</td><td><select id=tipe onchange=\"getKelompokSupplier(this.options[this.selectedIndex].value)\"><option value=''></option><option value=SUPPLIER>Supplier</option><option value=KONTRAKTOR>Kontraktor</option></select></td>
	     <td>".$_SESSION['lang']['telp']."</td><td><input type=text class=myinputtext id=telp onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
		 <td>Bank</td><td><input type=text class=myinputtext id=bank onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
		 
		 
	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['kodekelompok']."</td><td><select id=kdkelompok onchange=\"getSupplierNumber(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text);\"><option value=''></option></select></td>
          <td>".$_SESSION['lang']['fax']."</td><td><input type=text class=myinputtext id=fax onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>	
		  
		 <td>No. Rekening</td><td><input type=text class=myinputtext id=rek onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td> 
		    
 	  </tr>
	  <tr>
	      <td>Id.".$_SESSION['lang']['supplier']."/".$_SESSION['lang']['kontraktor']."</td><td><input type=text class=myinputtext disabled id=idsupplier></td>
	      <td>".$_SESSION['lang']['email']."</td><td><input type=text class=myinputtext id=email onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
		  <td>Atas Nama</td><td><input type=text class=myinputtext id=an onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>

	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['namasupplier']."</td><td><input type=text class=myinputtext onchange=cek() id=namasupplier onkeypress=\return tanpa_kutip(event);\" size=20 maxlength=45></td>
	      <td>".$_SESSION['lang']['npwp']."</td><td><input type=text class=myinputtext id=npwp onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
		  
		   <td>Kode Timbangan (Hanya untuk Supplier Pabrik)</td><td><input type=text class=myinputtext id=kdtim onkeypress=\"return angka_doang(event);\" size=30 maxlength=6></td>
		  
	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['alamat']."</td><td><input type=text class=myinputtext id=alamat onkeypress=\"return tanpa_kutip(event);\" size=50 maxlength=100></td>
	      <td>".$_SESSION['lang']['cperson']."</td><td><input type=text class=myinputtext id=cperson onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
	  </tr>
	  <tr>
	      <td>".$_SESSION['lang']['kota']."</td><td><input type=text class=myinputtext id=kota onkeypress=\"return tanpa_kutip(event);\" size=20 maxlength=30></td>
	      <td>".$_SESSION['lang']['plafon']."</td><td><input type=text  onblur=\"change_number(this);\"class=myinputtextnumber id=plafon onkeypress=\"return angka_doang(event);\" size=15 maxlength=15 value=0></td>
	  </tr>
	  </table>
	  <input type=hidden id=method value=insert>
	<button class=mybutton onclick=saveSupplier()>".$_SESSION['lang']['save']."</button>
	<button class=mybutton onclick=cancelSupplier()>".$_SESSION['lang']['cancel']."</button>	  
	  </fieldset>";
?>
<?
CLOSE_BOX();
OPEN_BOX('',$_SESSION['lang']['plafon'].': <span id=captiontipe></span> '.$_SESSION['lang']['namakelompok'].':<span id=captionkelompok></span>');
	     
	echo"<div style='width:1500px; height:350px;overflow:scroll'>
	     <table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=header>
	     <td>".$_SESSION['lang']['kodekelompok']."</td>
		 <td>Id.".$_SESSION['lang']['supplier']."</td>
		 <td>".$_SESSION['lang']['namasupplier']."</td>
		 <td>".$_SESSION['lang']['alamat']."</td>
		 <td>".$_SESSION['lang']['cperson']."</td>
		 <td>".$_SESSION['lang']['kota']."</td>
		 <td>".$_SESSION['lang']['telp']."</td>		 
		 <td>".$_SESSION['lang']['fax']."</td>	
		 
		 <td>Bank</td>	
		 <td>No. Rekening</td>	
		 <td>Atas Nama</td>	
		 
		 	 
		 <td>".$_SESSION['lang']['email']."</td>		 
		 <td>".$_SESSION['lang']['npwp']."</td>	 
		 <td>".$_SESSION['lang']['plafon']."</td>
		 <td>Kode Timbangan</td>
		 <td align=20>*</td>
		 </tr>
		 <tbody id=container>
		 </tbody>
		 <tfoot></tfoot>
		 </table>
		 </div>
		 ";

CLOSE_BOX();
echo close_body();
?>