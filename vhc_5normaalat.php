<!--ind-->
<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/vhc_5normaalat.js'></script>



<?php
include('master_mainMenu.php');			

		
		
		
		

$optOrganisasi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where  tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and `kodeorganisasi` LIKE '".$_SESSION['empl']['lokasitugas']."%'
	  order by kodeorganisasi";
$qry = mysql_query($str) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optOrganisasi.="<option value=".$data['kodeorganisasi']." >".$data['namaorganisasi']."</option>";
			}	

$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis.="<option value='1'>Pertipe Alat Berat/Kendaraan</option>";
$optJenis.="<option value='2'>Peritem Alat Berat/Kendaraan</option>";	
			
$optKategori="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKategori.="<option value='1'>Alat Berat</option>";
$optKategori.="<option value='2'>Kendaraan</option>";				
	
$optKegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
/*
$sqlA ="SELECT kodekegiatan,namakegiatan FROM ".$dbname.".`vhc_kegiatan_vw` order by namakegiatan asc";
$qryA = mysql_query($sqlA) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qryA))
			{
				$optKegiatan.="<option value=".$data['kodekegiatan']." >".$data['kodekegiatan'].": ".$data['namakegiatan']."</option>";
			}	
*/
$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
/*
$sqlB ="select kodevhc,nopol,satuk from `".$dbname."`.`vhc_5master` where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kelompokvhc='AB' ";
$qryB = mysql_query($sqlB) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qryB))
			{
				$optKendaran.="<option value=".$data['kodevhc']." >".$data['nopol']." [".$data['satuk']."]</option>";
			}
			
*/
		
		

?>


<?php
OPEN_BOX();

echo"<br /><br /><fieldset style='float:left;'>
		<legend>Norma Alat Berat/Kendaraan</legend> 
			<table border=0 cellpadding=1 cellspacing=1>				
				<tr>
					<td>Organisasi</td>
					<td>:</td>
					<td><select id=kd_org style=\"width:250px;\" >".$optOrganisasi."</select></td>
				</tr>	
				<tr>
					<td>Jenis Norma</td>
					<td>:</td>
					<td><select id=kdTipe style=\"width:250px;\" onchange=\"CariKat()\">".$optJenis."</select></td>
				</tr>
				<tr>
					<td>Kategori</td>
					<td>:</td>
					<td><select id=kdKat style=\"width:250px;\" onchange=\"CariKeg(this)\">".$optKategori."</select></td>
				</tr>
				<tr>
					<td>Kegiatan</td>
					<td>:</td>
					<td><select id=kdKeg style=\"width:250px;\" onchange=\"CariSat()\">".$optKegiatan."</select></td>
				</tr>
				<tr>
					<td>Kendaraan</td>
					<td>:</td>
					<td><select id=kdKen style=\"width:250px;\" >".$optKendaran."</select></td>
				</tr>
				<tr>
					<td>Satuan Norma</td>
					<td>:</td>
					<td><input id=\"satuan\" name=\"satuan\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"\" style=\"width:65px;text-align:center;\" Disabled></td>
				</tr>
				<tr>
					<td>Norma</td>
					<td>:</td>
					<td><input id=\"uadb\" name=\"uadb\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"0\" style=\"width:65px\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this)\"></td>
				</tr>
				
				<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=hapus()>Batal</button></td></tr>
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
echo" <div id=container style='width:100%;height:400px;overflow:scroll'>";	
$no=0;
		$str="SELECT kodeorg,nmorganisasi,kategori,kegiatan, concat(kegiatan,': ',nmkeg) as nmkeg,
		kendaraan, nmken, 
		format(norma,2) AS norma,
		satuan FROM ".$dbname.".vhc_kendaraan_norma_vw  where  kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kegiatan asc" ;
		$str2=mysql_query($str) or die(mysql_error());
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
				 <td align=center style='width:5px;'>No</td>
				 <td align=center>Organisasi</td>
				 <td align=center>Kegiatan</td>
				 <td align=center>Kendaraan</td>
				 <td align=center>Satuan</td>
				 <td align=center>Norma</td>
				 <td align=center>Aksi</td>
				 
			 </tr>
		 </thead>
		 <tbody id='containerData' >";

    while($bar1=mysql_fetch_assoc($str2))
		{

				
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td>".$bar1['nmorganisasi']."</td>";
			$tab.="<td >".$bar1['nmkeg']."</td>";
			$tab.="<td align=center>".$bar1['nmken']."</td>";
			$tab.="<td align=center>".$bar1['satuan']."</td>";
			$tab.="<td align=right>".$bar1['norma']."</td>";
			$tab.="<td align=center>

				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodeorg']."#".$bar1['kategori']."#".$bar1['kegiatan']."#".$bar1['kendaraan']."');\"></td></tr>";
		echo $tab;
		}
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