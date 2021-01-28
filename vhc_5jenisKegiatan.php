<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/vhc_kegiatan.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['vhc_kegiatan']);

$str1="select kodekegiatan,namakegiatan from ".$dbname.".setup_kegiatan order by kodekegiatan asc";
$optkeg="<option value=''>Pilih Kegiatan</option>";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
    $optkeg.="<option value='".$bar1->kodekegiatan."'>".$bar1->kodekegiatan.": ".$bar1->namakegiatan."</option>";
}

$optkel="<option value=''>Pilih Kegiatan</option>";
$optkel.="<option value='0'>Alat Berat dan Kendaraan</option>";
$optkel.="<option value='1'>Alat Berat</option>";
$optkel.="<option value='2'>Kendaraan</option>";


echo"<fieldset style='width:1024px;'><table>
     <tr><td>".$_SESSION['lang']['kodekegiatan']."</td><td><select id=nokegiatan style='width:550px;'>".$optkeg."</select></td></tr>
     <tr><td>Kategori</td><td><select id=nokategori style='width:550px;'>".$optkel."</select></td></tr>        
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanKegiatan()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelKegiatan()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";

echo open_theme($_SESSION['lang']['daftarkegiatan']);
echo "<div id=container>";
	$str2="select * from ".$dbname.".vhc_kegiatan_vw order by kodekegiatan";
	$res2=mysql_query($str2);
	echo"<table class=sortable cellspacing=1 border=0 style='width:1024px;'>
	     <thead>
		 <tr class=rowheader>
			<td style='width:150px;'>".$_SESSION['lang']['kodekegiatan']."</td>
			<td>kategori</td>
            <td style='width:250px;'>".$_SESSION['lang']['namakegiatan']."</td>
			<td>Satuan</td>
			<td>Akun Alokasi</td>
			<td style='width:30px;'>Aksi</td></tr>
		 </thead>
		 <tbody>";
	while($bar2=mysql_fetch_object($res2))
	{
		echo"<tr class=rowcontent>
					<td align=center>".$bar2->kodekegiatan."</td>
                    <td>".$bar2->kelompoknm."</td>
					<td style='width:250px;'>".$bar2->namakegiatan."</td>
                    <td>".$bar2->satuan."</td>    
					<td>".$bar2->noakun."</td>    
                    <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar2->kodekegiatan."','".$bar2->kelompok."');\"></td></tr>";
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