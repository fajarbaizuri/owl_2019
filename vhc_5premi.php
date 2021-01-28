<!--ind-->
<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/vhc_5premi.js'></script>



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
$optKegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKegiatan.="<option value='A'>Semua Kegiatan Alat Berat</option>";
$sqlA ="SELECT kodekegiatan,namakegiatan FROM ".$dbname.".`vhc_kegiatan_vw` where kelompok='1' or kelompok='0' order by kodekegiatan asc";
		$qryA = mysql_query($sqlA) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qryA))
			{
				$optKegiatan.="<option value=".$data['kodekegiatan']." >".$data['kodekegiatan'].": ".$data['namakegiatan']."</option>";
			}	

$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
/*
$optKendaran.="<option value='AB'>Semua Alat Berat</option>";
$sqlB ="select kodevhc,nopol,satuk from `".$dbname."`.`vhc_5master` where kodeorg='".$_SESSION['empl']['lokasitugas']."' and kelompokvhc='AB' ";
		$qryB = mysql_query($sqlB) or die ("SQL ERR : ".mysql_error());
		while ($data=mysql_fetch_assoc($qryB))
		{
			$optKendaran.="<option value=".$data['kodevhc']." >".$data['nopol']." [".$data['satuk']."]</option>";
		}
*/
		$optJenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis.="<option value='1'>Pertipe Alat Berat</option>";
$optJenis.="<option value='2'>Peritem Alat Berat</option>";	
		
$arrPos=array("0"=>"Operator","1"=>"Helper");
$optPremi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrPos as $brs => $isi)
{
	$optPremi.="<option value=".$brs.">".$isi."</option>";
}
?>


<?php
OPEN_BOX();

echo"<br /><br /><fieldset style='float:left;'>
		<legend>Premi Alat Berat</legend> 
			<table border=0 cellpadding=1 cellspacing=1>				
				<tr>
					<td>Organisasi</td>
					<td>:</td>
					<td><select id=kd_org style=\"width:250px;\" >".$optOrganisasi."</select></td>
				</tr>	
				<tr>
					<td>Jenis Premi</td>
					<td>:</td>
					<td><select id=kdTipe style=\"width:250px;\" onchange=\"Cariken()\">".$optJenis."</select></td>
				</tr>
				<tr>
					<td>Kegiatan</td>
					<td>:</td>
					<td><select id=kdKeg style=\"width:250px;\" >".$optKegiatan."</select></td>
				</tr>
				
				<tr>
					<td>Kendaraan</td>
					<td>:</td>
					<td><select id=kdKen style=\"width:250px;\" >".$optKendaran."</select></td>
				</tr>
				<tr>
					<td>Premi</td>
					<td>:</td>
					<td><select id=kdpremi style=\"width:250px;\" >".$optPremi."</select></td>
				</tr>
				<tr>
					<td>Satuan Basis</td>
					<td>:</td>
					<td><input id=\"satuan\" name=\"satuan\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"\" style=\"width:65px\" ></td>
				</tr>
				<tr>
					<td>Dapat Basis (Rp/Satuan)</td>
					<td>:</td>
					<td><input id=\"uadb\" name=\"uadb\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"0\" style=\"width:65px\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this)\"></td>
				</tr>
				<tr>
					<td>Lebih Basis (Rp/Satuan)</td>
					<td>:</td>
					<td><input id=\"ualb\" name=\"ualb\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"0\" style=\"width:65px\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this)\"></td>
				</tr>
				<tr>
					<td>Insentive 125 s/d 174 HM/Bulan</td>
					<td>:</td>
					<td><input id=\"uains1\" name=\"uains1\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"0\" style=\"width:65px\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this)\"></td>
				</tr>
				<tr>
					<td>Insentive > 174 HM/Bulan</td>
					<td>:</td>
					<td><input id=\"uains2\" name=\"uains2\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"0\" style=\"width:65px\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this)\"></td>
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
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
				 <td align=center style='width:5px;'>No</td>
				 <td align=center>Organisasi</td>
				 <td align=center>Kegiatan</td>
				 <td align=center>Kendaraan</td>
				 <td align=center>Premi</td>
				 <td align=center>Satuan</td>
				 <td align=center>Dapat Basis (Rp/Satuan)</td>
				 <td align=center>Lebih Basis (Rp/Satuan)</td>
				 <td align=center>Insentive 125 s/d 174 HM/Bulan</td>
				 <td align=center>Insentive > 174 HM/Bulan</td>
				 <td align=center>Aksi</td>
				 
			 </tr>
		 </thead>
		 <tbody id='containerData'></script>";
    $no=0;
		$str="SELECT kodeorg,namaorganisasi ,kegiatan, concat(kegiatan,': ',nmkeg) as nmkeg,
		kendaraan,nmken, operator,nmort, 
		dptbasis,lbhbasis, insentive1 ,insentive2,
		kategori,satuan FROM ".$dbname.".vhc_kendaraan_premi_vw  where kategori='1' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kegiatan asc" ;
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
				
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=center>".$bar1['kodeorg']."</td>";
			$tab.="<td>".$bar1['nmkeg']."</td>";
			$tab.="<td align=center>".$bar1['nmken']."</td>";
			$tab.="<td align=center>".$bar1['nmort']."</td>";
			$tab.="<td align=center>".$bar1['satuan']."</td>";
			$tab.="<td align=right>".$bar1['dptbasis']."</td>";
			$tab.="<td align=right>".$bar1['lbhbasis']."</td>";
			$tab.="<td align=right>".$bar1['insentive1']."</td>";
			$tab.="<td align=right>".$bar1['insentive2']."</td>";
			$tab.="<td align=center>

				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['kodeorg']."#".$bar1['kategori']."#".$bar1['kegiatan']."#".$bar1['kendaraan']."#".$bar1['operator']."');\"></td></tr>";
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