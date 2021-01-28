<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_5natura.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['setupnatura']);
$tipekaryawan='';
$str="select * from ".$dbname.".sdm_5tipekaryawan order by tipe";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$tipekaryawan.="<option value='".$bar->id."'>".$bar->tipe."</option>";
}

$st='';
$str="select * from ".$dbname.".sdm_5statuspajak order by nama";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$st.="<option value='".$bar->kode."'>".$bar->nama."</option>";
}

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg><option value='".substr($_SESSION['empl']['lokasitugas'],0,4)."'>".substr($_SESSION['empl']['lokasitugas'],0,4)."</option></select></td></tr>
	 <tr><td>".$_SESSION['lang']['tipekaryawan']."</td><td><select id=tipekaryawan>".$tipekaryawan."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['statuspajak']."</td><td><select id=statuspajak>".$st."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['kodebarang']."</td><td><input type=text id=kodebarang size=12 onkeypress=\"return false;\" class=myinputtextnumber maxlength=11 value='' onclick=\"showWindowBarang('Cari Barang',event)\"></td></tr>
     <tr><td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jumlah size=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=6 value=0></td></tr>
	 <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=8 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=8 disabled></td></tr>
	 <tr><td>".$_SESSION['lang']['hargasatuan']."</td><td><input type=text id=hargasatuan size=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=12 value=0 onblur=change_number(this)></td></tr>
	 <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=keterangan size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=45></td></tr>

	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo "<div>";
	$str1="select a.*,b.tipe
	     from ".$dbname.".sdm_5natura a left join
		 ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id
		   where LEFT(a.kodeorg,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  order by kodebarang"; 
	$res1=mysql_query($str1);

	echo"<table class=sortable cellspacing=1 border=0 style='width:700px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
			<td>".$_SESSION['lang']['tipekaryawan']."</td>
			<td>".$_SESSION['lang']['statuspajak']."</td>
			<td>".$_SESSION['lang']['namabarang']."</td>
			<td>".$_SESSION['lang']['jumlah']."</td>
			<td>".$_SESSION['lang']['satuan']."</td>
			<td>".$_SESSION['lang']['hargasatuan']."</td>
			<td>".$_SESSION['lang']['keterangan']."</td>
			<td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>"; 
	while($bar1=mysql_fetch_object($res1))
	{
		$strx="select namabarang from ".$dbname.".log_5masterbarang where
		       kodebarang='".$bar1->kodebarang."'";
		$resx=mysql_query($strx);
		$namabarang='';
		while($barx=mysql_fetch_object($resx))
		{
			$namabarang=$barx->namabarang;
		}	   
		$strx="select nama from ".$dbname.".sdm_5statuspajak where
		       kode='".$bar1->statuspajak."'";
		$resx=mysql_query($strx);
		$stp='';
		while($barx=mysql_fetch_object($resx))
		{
			$stp=$barx->nama;
		}
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeorg."</td>
				   <td>".$bar1->tipe."</td>
				   <td>".$stp."</td>
				   <td>".$namabarang."</td>
				   <td align=center>".$bar1->jumlah."</td>
				   <td align=center>".$bar1->satuan."</td>
				   <td align=center>".number_format($bar1->totalrupiah,2,'.',',')."</td>
				   <td align=center>".$bar1->keterangan."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tipekaryawan."','".$bar1->kodebarang."','".$bar1->jumlah."','".$bar1->satuan."','".$bar1->rupiahkonversi."','".$bar1->keterangan."','".$bar1->statuspajak."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>