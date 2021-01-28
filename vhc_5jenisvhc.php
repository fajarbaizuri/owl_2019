<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>

<script language=javascript1.2 src=js/vhc.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['jenkendabmes']."</b>");


$optakun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optakun.="<option value='NULL'>TIDAK ADA</option>";
$sql="SELECT *FROM ".$dbname.".keu_5akun  WHERE detail=1 and noakun like '4%'";//4110205 ,//4110199
// and noakun in ('4110205','4110199') 
//echo $sql;
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optakun.="<option value=".$data['noakun'].">".$data['noakun']." - ".$data['namaakun']."</option>";
			}

//get enum untuk kelompok vhc;
	$optklvhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$arrklvhc=getEnum($dbname,'vhc_5master','kelompokvhc');
	foreach($arrklvhc as $kei=>$fal)
	{
		switch($kei)
		{
			case 'AB':
			$fal='Alat Berat';
			break;
			case 'KD':
			$fal='Kendaraan';
			break;
			case 'MS':
			$fal='Mesin';
			break;			
		}
		$optklvhc.="<option value='".$kei."'>".$fal."</option>";
	} 
echo"<fieldset style='width:450px;'><table>
	<tr>
		<td>".$_SESSION['lang']['kodekelompok']."</td>
		<td>
			<select id=kelompokvhc  style='width:255px;'>".$optklvhc."</select>
		</td>
	</tr>     

	<tr>
		<td>".$_SESSION['lang']['namajenisvhc']."</td>
		<td><input style='width:253px;' type=text id=namajenisvhc size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=45></td>
	</tr>
	
	<tr>
		<td>Akun Transit Upah</td>
	 	<td><select id=noakun1 style='width:255px;'>".$optakun."</select></td>
	</tr>
	<tr>
		<td>Akun Transit Premi</td>
	 	<td><select id=noakun2 style='width:255px;'>".$optakun."</select></td>
	</tr>
	<tr>
		<td>Akun Transit Uang makan</td>
	 	<td><select id=noakun3 style='width:255px;'>".$optakun."</select></td>
	</tr>
	<tr>
		<td>Akun Transit BBM</td>
	 	<td><select id=noakun4 style='width:255px;'>".$optakun."</select></td>
	</tr>
	<tr>
		<td>Akun Transit Sparepart</td>
	 	<td><select id=noakun5 style='width:255px;'>".$optakun."</select></td>
	</tr>
	<tr>
		<td>Akun Transit Service</td>
	 	<td><select id=noakun6 style='width:255px;'>".$optakun."</select></td>
	</tr>
	 
	
	 
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanVhc()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelVhc()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo "<div>";
	$str1="select * from ".$dbname.".vhc_5jenisvhc order by jenisvhc";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:100%;'>
	     <thead>
		 <tr class=rowheader>
		   <td style='width:150px;'>".$_SESSION['lang']['tipe']."</td>
		   <td>".$_SESSION['lang']['kodekelompok']."</td>
		   <td>".$_SESSION['lang']['namajenisvhc']."</td>
		   <td>Akun Transit Upah</td>		   
		   <td>Akun Transit Premi</td>		   
		   <td>Akun Transit Uang Makan</td>		   
		   <td>Akun Transit BBM</td>		   
		   <td>Akun Transit Sparepart</td>		   
		   <td>Akun Transit Service</td>		   
		   <td>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
			 <td align=center>".$bar1->jenisvhc."</td>
		     <td align=center>".$bar1->kelompokvhc."</td>
			 <td >".$bar1->namajenisvhc."</td>
			 <td align=center>".$bar1->noakun_upah."</td>
			 <td align=center>".$bar1->noakun_premi."</td>
			 <td align=center>".$bar1->noakun_umkn."</td>
			 <td align=center>".$bar1->noakun_bbm."</td>
			 <td align=center>".$bar1->noakun_spart."</td>
			 <td align=center>".$bar1->noakun_service."</td>
			 <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->jenisvhc."','".$bar1->namajenisvhc."','".$bar1->noakun."','".$bar1->kelompokvhc."');\">
			 <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"Del('".$bar1->jenisvhc."');\"></td></tr>";
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