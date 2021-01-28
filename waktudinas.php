<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/sdm_waktudinas.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('',"Jam Waktu Dinas");
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 
      order by namaorganisasi asc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $opt_unit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
echo"<fieldset style='width:700px;'><table>
	<input type=hidden id=method value='insert'>
	<input type=hidden id='kode' value=''>
	<tr>
    <td>".$_SESSION['lang']['unit']."</td><td>:<select id=kodeorg>".$opt_unit."</select></td>
    </tr>
	<tr>
	<td>NAMA</td><td>:<input class='myinputtext' name='nama' id='nama' type='text' maxlength='50' ></td>
	</tr>
	<tr>
	<td>JAM DATANG</td><td>:<input class='myinputtext' name='jdat' id='jdat' type='text' maxlength='5' ></td>
	</tr>
	<tr>
	<td>JAM PULANG</td><td>:<input class='myinputtext' name='jpul' id='jpul' type='text' maxlength='5' ></td>
	</tr>
	<tr>
	<td>ISTIRAHAT KELUAR</td><td>:<input class='myinputtext' name='iskel' id='iskel' type='text' maxlength='5' ></td>
	</tr>
	<tr>
	<td>ISTIRAHAT MASUK</td><td>:<input class='myinputtext' name='ismas' id='ismas' type='text' maxlength='5' ></td>
	</tr>
	</tr>
	<tr>
	<td></td><td><input type='checkbox' name='dhil' id='dhil' value='1'>Istirahat dihitung Lembur</td>
	</tr>
	<tr>
	<td>HARI KERJA</td><td>
	<input type='checkbox' id='senin' name='senin' value='1' checked>Senin
	<input type='checkbox'id='selasa'  name='selasa' value='1' checked>Selasa
	<input type='checkbox'  id='rabu' name='rabu' value='1' checked>Rabu
	</td>
	</tr>
	<tr>
	<td></td><td><input type='checkbox' id='kamis' name='kamis' value='1' checked>Kamis
	<input type='checkbox' id='jumat' name='jumat' value='1' checked>Jumat
	<input type='checkbox' id='sabtu' name='sabtu' value='1' checked>Sabtu
	<input type='checkbox' id='minggu' name='minggu' value='1' checked>Minggu
	</td>
	</tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['datatersimpan']);


	$str1="select * from ".$dbname.".sdm_5waktudinas order by idjad desc";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:700px;'>
	     <thead>
		 <tr class=rowheader>
		<td align='center' rowspan='2'>No</td>
		<td align='center' rowspan='2'>Nama</td>
		<td align='center' rowspan='2'>Organisasi</td>
		<td align='center' colspan='2'>Jam Dinas</td>
		<td align='center' colspan='2'>Istirahat</td>
		<td align='center' rowspan='2'>Jam Dinas</td>
		<td align='center' rowspan='2'>Is Lembur</td>
		<td align='center' rowspan='2'>Jumlah Lembur</td>
		<td align='center' rowspan='2'>Control</td>
	</tr>
	<tr class=rowheader>
		<td align='center' >Datang</td>
		<td align='center' >Pulang</td>
		<td align='center' >Keluar</td>
		<td align='center' >Masuk</td>
	</tr>
	
		 
		 </thead>
		 <tbody id=container>";
		  $no=1;
	while($bar1=mysql_fetch_object($res1))
	{
		//echo"<tr class=rowcontent><td align=center>".$bar1->regional."</td><td>".$bar1->nama."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->regional."','".$bar1->nama."');\"></td></tr>";
		 echo "<tr class=rowcontent>
    <td align=center>$no</td>
    <td >$bar1->nama</td>
	<td align=center>$bar1->lokasi</td>
	<td align=center>$bar1->jammasuk</td>
    <td align=center>$bar1->jampulang</td>
	<td align=center>$bar1->istirahatkeluar</td>
	<td align=center>$bar1->istirahatmasuk</td>
	<td align=center>$bar1->jamdinas</td>";
	if ($bar1->ihlembur=='1'){
	echo"<td align=center>YA</td>";
	}else{
	echo"<td align=center>TIDAK</td>";
	}
	
	echo"
	<td align=center>$bar1->totallembur</td>
	
	<td>
	<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->idjad."','".$bar1->lokasi."','".$bar1->nama."','".$bar1->jammasuk."','".$bar1->jampulang."','".$bar1->istirahatkeluar."','".$bar1->istirahatmasuk."','".$bar1->ihlembur."','".$bar1->senin."','".$bar1->selasa."','".$bar1->rabu."','".$bar1->kamis."','".$bar1->jumat."','".$bar1->sabtu."','".$bar1->minggu."');\">
	<img src=images/delete_32.png class=resicon  caption='Hapus' onclick=\"delField('".$bar1->idjad."');\"></td>
  </tr>";
		 $no++;
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";

echo close_theme();
CLOSE_BOX();
echo close_body();
?>