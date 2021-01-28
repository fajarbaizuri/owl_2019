<!--ind-->
<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php'); 
echo open_body();
?>

<script language=javascript1.2 src='js/vhc_5operator.js'></script>



<?php
include('master_mainMenu.php');			

$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optorg.="<option value='L'>- SOPIR/OPERATOR PIHAK LUAR -</option>";
$sql ="select namakaryawan,karyawanid,lokasitugas from ".$dbname.".datakaryawan where alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tanggalkeluar='0000-00-00' and kodejabatan in 
('254','72','181','215','216','217','218','219','221','231','55','61','95','175','185','199','204','205','206','207','208','210','211','212','213','220','310','49','244','263','287','302','310') order by namakaryawan asc ";
//('49','55','56','59','61','95','151','156','175','185','189','190','191','192','193','194','195','196','197','198','199','200','201','202','203','204','205','206','207','208','209','210','211','212','213','215','216','217','218','219','220','221','238','244','263','287','302')  

$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optorg.="<option value=".$data['karyawanid']." >".$data['namakaryawan']."-"."[".$data['lokasitugas']."] </option>";
			}	
			
$optKendaran="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql ="select kodevhc,kodeorg,nopol from ".$dbname.".vhc_5master where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and kelompokvhc in ('AB','KD') order by kodevhc desc";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optKendaran.="<option value=".$data['kodevhc'].">".$data['kodevhc']." [".$data['kodeorg']."] [".$data['nopol']."] </option>";
			}			
$arrPos=array("0"=>"NonAktif","1"=>"Aktif");
$optStatus="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrPos as $brs => $isi)
{
	$optStatus.="<option value=".$brs.">".$isi."</option>";
}
$optPosisi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>


<?php
OPEN_BOX();

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>				
				<tr>
					<td>".$_SESSION['lang']['namakaryawan']."</td>
					<td>:</td>
					<td><select id=kd_karyawan style=\"width:250px;\" onchange=\"cariOL(this);\">".$optorg."</select></td>
				</tr>	
				<tr>
					<td></td>
					<td></td>
					<td><input type=\"hidden\" style=\"width:246px; class=\"myinputtext\" id=\"karyawanluar\" size=\"26\" maxlength=\"40\" onkeypress=\"return tanpa_kutip(event);\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['kodevhc']."</td>
					<td>:</td>
					<td><select id=kdVhc style=\"width:250px;\" onchange=\"cariPosisi(this.value);\">".$optKendaran."</select></td>
				</tr>
				<tr>
					<td>Posisi</td>
					<td>:</td>
					<td><select id=kdposisi style=\"width:250px;\" >".$optPosisi."</select></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['status']."</td>
					<td>:</td>
					<td><select id=status style=\"width:250px;\" >".$optStatus."</select></td>
				</tr>
				<tr>
					<td>Uang Makan (Rp/HK)</td>
					<td>:</td>
					<td><input id=\"uamk\" name=\"uamk\" class=\"myinputtextnumber\" onkeypress=\"return tanpa_kutip(event)\" type=\"text\" value=\"0\" style=\"width:65px\" onchange=\"this.value=remove_comma(this);this.value = _formatted(this)\"></td>
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

echo" <div id=container style='width:750px;height:400px;overflow:scroll'>";	
$no=0;
		$str="select a.*,b.nopol,if(left(vhc,2)='AB',if(`status`='1','OPERATOR','HELPER'),if(`status`='1','SOPIR','KERNET')) AS posi from ".$dbname.".vhc_5operator a
	left join ".$dbname.".vhc_5master b on a.vhc=b.kodevhc where a.karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."') or a.karyawanid like 'L".$_SESSION['empl']['lokasitugas']."%' order by karyawanid asc ";
		$str2=mysql_query($str) or die(mysql_error());


	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
				 <td align=center style='width:5px;'>No</td>
				 <td align=center>".$_SESSION['lang']['nokaryawan']."</td>
				 <td align=center>Posisi</td>
				 <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
				 <td align=center>".$_SESSION['lang']['status']."</td>
				 <td align=center>".$_SESSION['lang']['kodevhc']."</td>
				 <td align=center>No. Polisi</td>
				 <td align=center>Uang Makan</td>
				 <td align=center>Aksi</td>
				 
			 </tr>
		 </thead>
		 <tbody id='containerData'>";
   
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td>".$bar1['karyawanid']."</td>";
			$tab.="<td >".$bar1['nama']."</td>";
			$tab.="<td align=center>".$bar1['posi']."</td>";
			$tab.="<td align=center>".$bar1['aktif']."</td>";
			$tab.="<td>".$bar1['vhc']."</td>";
			$tab.="<td>".$bar1['nopol']."</td>";
			$tab.="<td align=right>".$bar1['um']."</td>";
			$tab.="<td align=center>
				 <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$bar1['karyawanid']."','".$bar1['vhc']."');\"></td></tr>";
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