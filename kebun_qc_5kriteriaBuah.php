<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/kebun_qc_5kriteriaBuah.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$optOrg2.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sField="select * from ".$dbname.".kebun_qc_panendt";
//exit("Error".$sField);
$qField=mysql_query($sField) or die(mysql_error());
while($rField=mysql_fetch_field($qField))
{
    if($rField->name!='notransaksi'&&$rField->name!='notph'&&$rField->name!='karyawanid')
    {
        $optOrg2.="<option value='".$rField->name."'>".$rField->name."</option>";
    }
}
        

OPEN_BOX('',"<b>Kriteria Buah</b>");
$frm[0].="<fieldset><legend>Kriteria Buah</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeabs']."</td><td>:</td><td><select id=KbnId name=KbnId>".$optOrg2."</select>
</td></tr>
<tr><td>".$_SESSION['lang']['nama']." </td><td>:</td><td>
<input type=text id=nm_ancak name=nm_ancak  onkeypress='return tanpa_kutip(event)' class=myinputtext style=width:150px; maxlength=45 />
</td></tr>
<tr><td colspan=3>
<button class=mybutton id=save_kepala name=save_kepala onclick=save_ancak()>".$_SESSION['lang']['save']."</button>
<button class=mybutton id=cancel_kepala name=cancel_kepala onclick=clear_form()>".$_SESSION['lang']['cancel']."</button>
<input type=hidden id=proses name=proses value=insert_header >
</td></tr></table>";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>

<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeabs']."</td>
		<td>".$_SESSION['lang']['nama']."</td>
		<td>Action</td>
		</tr></thead><tbody id=contain>
		<script>load_data()</script>
		";
$frm[0].="</tbody></table></fieldset>";

//B. Nilai Ancak
$optAncak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$frm[1].="<fieldset><legend>Nilai Ancak</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>Kode Buah</td><td>:</td><td>
<select id=idAncak name=idAncak  style=width:150px;>".$optAncak."</select> </td></tr>
<tr><td>".$_SESSION['lang']['dari']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=nilDari name=nilDari maxlength=10 onkeypress=\"return angka_doang(event);\" style=width:150px; value=0 /> 
</td></tr>
<tr><td>".$_SESSION['lang']['tglcutisampai']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=nilSmp name=nilSmp maxlength=10 onkeypress=\"return angka_doang(event);\" style=width:150px; value=0 /> 
</td></tr>

<tr><td>".$_SESSION['lang']['nilai']."</td><td>:</td>
<td>
<input type=text class=myinputtextnumber id=nilai name=nilai maxlength=10 onkeypress=\"return angka_doang(event);\" style=width:150px; value=0 /></td> </tr>


<tr><td colspan=3>
<button class=mybutton onclick=save_data() >".$_SESSION['lang']['save']."</button>
<button class=mybutton onclick=bersih_form() >".$_SESSION['lang']['cancel']."</button>
<input type=hidden id=proses_pekerjaan name=proses_pekerjaan value=insert_pekerjaan />
</table>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>Nilai Buah</td>
		<td>".$_SESSION['lang']['dari']."</td>
		<td>".$_SESSION['lang']['tglcutisampai']."</td>
		<td>".$_SESSION['lang']['nilai']."</td>
		<td>Action</td>
		</tr></thead><tbody id=containPekerja>
		";
$frm[1].="</tbody></table></fieldset>";


//========================
$hfrm[0]="Kriteria Buah";
$hfrm[1]="Nilai Panen";

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>



<?php
CLOSE_BOX();
echo close_body();
?>