<?//@Copy nangkoelframework 
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/monitoring_stock.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX();

//pengambilan kelompok biaya sari table keu_5komponenbiaya
//kelompok biaya tsb di ENUNM pada table(Tidak memiliki table sendiri)
if ($_SESSION['empl']['lokasitugas'] == 'FBHO'){
		$str="SELECT * FROM  ".$dbname.".`organisasi` WHERE  `tipe` =  'GUDANG' order by namaorganisasi asc";
}else{
		$str="SELECT * FROM  ".$dbname.".`organisasi` WHERE  `tipe` =  'GUDANG' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
}
$res=mysql_query($str);
$opt1="";
while($bar=mysql_fetch_object($res))
{
	$opt1.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}


$str="SELECT * FROM  ".$dbname.".`log_5masterbarang` order by namabarang asc";
$res=mysql_query($str);
$opt2="";
while($bar=mysql_fetch_object($res))
{
	$opt2.="<option value='".$bar->kodebarang."'>".$bar->namabarang."(".$bar->kodebarang.")</option>";
}


echo"<fieldset>
     <legend>Monitoring Stock</legend>
	 <table>
	 <tr>
	  
	   <td>Tahun</td>
	   <td><input style=\"text-align:center;\" type=text class=myinputtext id=thn size=4 maxlength=4 onkeypress=\"return angka_doang(event);\">
	   </td>
	 </tr>
	 <tr>
	   <td>Gudang</td>
	   <td><select id=gudang>".$opt1."</select></td>
	 </tr>
	 <tr>
	   <td>Barang</td>
	   <td><select id=barang>".$opt2."</select></td>
	 </tr>
	 <tr>
	   <td>Status</td>
	   <td>
	   <select id=sts>
	   <option value='1'>Fast Moving</option>
	   <option value='2'>Slow Moving</option>
	   <option value='3'>Consumable</option>
	   </select>
	   </td>
	 </tr>
	 <tr>
	   <td>Life Time</td>
	   <td><input style=\"text-align:center;\" type=text class=myinputtext id=lifetime size=10 maxlength=10 onkeypress=\"return angka_doang(event);\"> Jam</td>
	 </tr>
	 <tr>
	   <td>Qty Pertahun</td>
	   <td><input style=\"text-align:center;\" type=text class=myinputtext id=qtythn size=10 maxlength=10 onkeypress=\"return angka_doang(event);\"></td>
	 </tr>
	 <tr>
	   <td>Qty Minimal</td>
	   <td><input style=\"text-align:center;\" type=text class=myinputtext id=qtymin size=10 maxlength=10 onkeypress=\"return angka_doang(event);\"></td>
	 </tr>
	  
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveMonitoringStock()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelMonitoringStock()>".$_SESSION['lang']['cancel']."</button>
	 <input type=hidden id=thnlm>
	 <input type=hidden id=gudanglm>
	 <input type=hidden id=baranglm>
     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
if ($_SESSION['empl']['lokasitugas'] == 'FBHO'){
		$str="select * from ".$dbname.".log_5monitoring_vw order by tahun desc,gudang asc, namabarang asc";
}else{
		$str="select * from ".$dbname.".log_5monitoring_vw where kodegudang like '".$_SESSION['empl']['lokasitugas']."%' order by tahun desc,gudang asc, namabarang asc";
}

$res=mysql_query($str);
echo"<table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td rowspan=\"2\">No</td>
	   <td rowspan=\"2\">Tahun</td>
	   <td rowspan=\"2\">Gudang</td>
	   <td rowspan=\"2\">Kode</td>
	   <td rowspan=\"2\">Barang</td>
	   <td rowspan=\"2\">Satuan</td>
	   <td rowspan=\"2\">Status</td>
	   <td rowspan=\"2\">Life Time (Jam)</td>
	   <td colspan=\"2\">Quantity</td>
	   <td rowspan=\"2\">Aksi</td>
	  </tr>
	  <tr class=rowheader>
	   <td>Pertahun</td>
	   <td>Minimal</td>
	  </tr>
	  
	 </thead>
	 <tbody id=container>";
$no=0;	  
while($bar=mysql_fetch_object($res))
{
  $no+=1;	
  echo"<tr class=rowcontent>
	   <td>".$no."</td>
	   <td>".$bar->tahun."</td>
	   <td>".$bar->gudang."</td>
	   <td>".$bar->kodebarang."</td>
	   <td>".$bar->namabarang."</td>
	   <td style=\"text-align:center;\">".$bar->satuan."</td>
	   <td style=\"text-align:center;\">".$bar->status."</td>
	   <td style=\"text-align:center;\">".$bar->lifetime."</td>
	   <td style=\"text-align:center;\">".$bar->qtythn."</td>
	   <td style=\"text-align:center;\">".$bar->qtymin."</td>
		  <td>
		      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->tahun."','".$bar->kodegudang."','".$bar->kodebarang."','".$bar->kdstatus."','".$bar->lifetime."','".$bar->qtythn."','".$bar->qtymin."'
			  );\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delMonitoring('".$bar->tahun."','".$bar->kodegudang."','".$bar->kodebarang."');\">
		  </td>
	   
	  </tr>";	
}     
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table>";
CLOSE_BOX();
echo close_body();
?>