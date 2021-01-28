<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/tipeasset.js></script>
<?
include('master_mainMenu.php');
//ambil akun penyusutan
$str="select noakundebet as noakun,
     keterangan as namaakun from ".$dbname.".keu_5parameterjurnal 
     where kodeaplikasi='DEP' order by keterangan";
$res=mysql_query($str);
$optAkun="<option value=''></option>";
if(mysql_num_rows($res)<1)
{
    echo "Error: Parameter jurnal untuk `DEP` belum ada";
}
 else {
while($bar=mysql_fetch_object($res))
{
	$optAkun.="<option value='".$bar->noakun."'>[".$bar->noakun."]-".$bar->namaakun."</option>";
}    
}

$stru="select noakun,namaakun from ".$dbname.".keu_5akun";
$res=mysql_query($stru);
while($bar=mysql_fetch_object($res))
{
    $namaakun[$bar->noakun]=$bar->namaakun;
    $optAkunak.="<option value='".$bar->noakun."'>[".$bar->noakun."]-".$bar->namaakun."</option>";
}



OPEN_BOX('',$_SESSION['lang']['tipeasset']);

echo"<fieldset style='width:600px;'><table>
     <tr><td>".$_SESSION['lang']['kode']."</td><td><input type=text id=kodetipe size=4 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['namakelompok']."</td><td><input type=text id=namatipe size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['namaakun']."</td><td><select id=noakun>".$optAkun."</select></td></tr>
                     <tr><td>".$_SESSION['lang']['aktivadalam']."</td><td><select id=noakunak>".$optAkunak."</select></td></tr>             
     </table>

	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanTipeAset()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelTipeAsset()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo "<div>";
	$str1="select * from ".$dbname.".sdm_5tipeasset 
		   order by namatipe";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:700px;'>
	     <thead>
		 <tr class=rowheader>
		 <td style='width:150px;'>".$_SESSION['lang']['kode']."</td>
		 <td>".$_SESSION['lang']['namakelompok']."</td>
		 <td>".$_SESSION['lang']['namaakun']."</td>
                                         <td>".$_SESSION['lang']['aktivadalam']."</td>
		 <td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
		     <td align=center>".$bar1->kodetipe."</td>
			 <td>".$bar1->namatipe."</td>
			 <td>".$namaakun[$bar1->noakun]."</td>
                                                             <td>".$namaakun[$bar1->akunak]."</td>
			 <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodetipe."','".$bar1->namatipe."','".$bar1->noakun."','".$bar1->akunak."');\"></td></tr>";
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