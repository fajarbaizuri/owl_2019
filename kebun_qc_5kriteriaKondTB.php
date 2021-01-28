<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/kebun_qc_5kriteriaKondTB.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$optKode.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sField="select * from ".$dbname.".kebun_qc_kondisitbdt";
$qField=mysql_query($sField) or die(mysql_error());
while($rField=mysql_fetch_field($qField))
{
    if(($rField->name!='notransaksi')and($rField->name!='kodeorg'))$optKode.="<option value='".$rField->name."'>".$rField->name."</option>";
}      

// dah di-load di js loadtab3
//$str="select * from ".$dbname.".kebun_qc_5stlapangantbm order by kode"; 
//$res=mysql_query($str);
//$optPekerjaan.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//while($bar=mysql_fetch_object($res))
//{
//    $optPekerjaan.="<option value='".$bar->kode."'>".$bar->kode." - ".$bar->nama."</option>";
//}

//$optTanah="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$x=readCountry('config/topografi.lst');
//foreach($x as $bar=>$val)
//{                    
//    $optTanah.="<option value='".$val[0]."'>".$val[0]."</option>";
//}

$str="select * from ".$dbname.".setup_topografi order by topografi"; 
$res=mysql_query($str);
$optTanah.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
    $optTanah.="<option value='".$bar->topografi."'>".$bar->keterangan."</option>";
}

OPEN_BOX('',"<b>Kriteria Kondisi Lapangan TB</b>");
$frm[0].="<fieldset><legend>Kriteria Kondisi Lapangan TB</legend>";
$frm[0].="<table cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['kodeabs']."</td><td>:</td>
        <td><select id=kode name=kode>".$optKode."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['nama']." </td><td>:</td>
        <td><input type=text id=nama name=nama onkeypress='return tanpa_kutip(event)' class=myinputtext style=width:150px; maxlength=45 /></td>
    </tr>
    <tr>
        <td colspan=3>
        <button class=mybutton id=save1 name=save1 onclick=savetab1()>".$_SESSION['lang']['save']."</button>
        <button class=mybutton id=cancel1 name=cancel1 onclick=cleartab1()>".$_SESSION['lang']['cancel']."</button>
        <input type=hidden id=proses1 name=proses1 value='insert1' >
        </td>
    </tr>
</table>";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>

<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeabs']."</td>
		<td>".$_SESSION['lang']['nama']."</td>
		<td>".$_SESSION['lang']['action']."</td>
		</tr></thead><tbody id=contain1>
		<script>loadtab1()</script>
		";
$frm[0].="</tbody></table></fieldset>";

//B. Nilai Ancak
$optAncak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$frm[1].="<fieldset><legend>Bobot Nilai</legend>";
$frm[1].="<table cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['pekerjaan']."</td><td>:</td>
        <td><select id=pekerjaan name=pekerjaan style=width:150px;>".$optPekerjaan."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['topografi']."</td><td>:</td>
        <td><select id=kelas name=kelas style=width:150px;>".$optTanah."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['bobot']."</td><td>:</td>
        <td><input type=text class=myinputtextnumber id=bobot name=bobot maxlength=10 onkeypress=\"return angka_doang(event);\" style=width:150px; value=0 /></td> 
    </tr>
    <tr>
        <td colspan=3>
        <button class=mybutton onclick=savetab2() >".$_SESSION['lang']['save']."</button>
        <button class=mybutton onclick=cleartab2() >".$_SESSION['lang']['cancel']."</button>
        <input type=hidden id=proses2 name=proses2 value=insert2 />
        </td>
    </tr>
</table>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['pekerjaan']."</td>
		<td>".$_SESSION['lang']['kelastanah']."</td>
		<td>".$_SESSION['lang']['bobot']."</td>
		<td>".$_SESSION['lang']['action']."</td>
		</tr></thead><tbody id=contain2>
		";
$frm[1].="</tbody></table></fieldset>";

//$frm[2].="<fieldset><legend>Grade Panen</legend>";
//$frm[2].="<table cellspacing=1 border=0>
//<tr><td>".$_SESSION['lang']['dari']."</td><td>:</td><td>
//<input type=text id=dari name=dari class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' value=0 /></td></tr>
//<tr><td>".$_SESSION['lang']['sampai']."</td><td>:</td><td>
//<input type=text id=sampai name=sampai class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' value=0 /></td></tr>
//<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td>
//<input type=text id=keterangan name=keterangan class=myinputtextnumber style=width:150px; onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>
//<tr><td colspan=3>
//<button class=mybutton onclick=savetab3() >".$_SESSION['lang']['save']."</button>
//<button class=mybutton onclick=cleartab3() >".$_SESSION['lang']['cancel']."</button>
//<input type=hidden name=proses3 id=proses3 value='insert3' />
//<input type=hidden name=dariold id=dariold />
//<input type=hidden name=sampaiold id=sampaiold  />
//
//</td></tr>
//</table>";
//
//$frm[2].="</fieldset>";
//$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0 class=sortable>
//		<thead>
//		<tr class=\"rowheader\">
//		<td>No.</td>
//		<td>".$_SESSION['lang']['dari']."</td>
//		<td>".$_SESSION['lang']['sampai']."</td>
//		<td>".$_SESSION['lang']['keterangan']."</td>
//		<td>Action</td>
//		</tr></thead><tbody id=contain3>
//		";
//$frm[2].="</tbody></table></fieldset>";

//========================
$hfrm[0]="Kriteria Kondisi Lapangan TB";
$hfrm[1]="Bobot Nilai";
//$hfrm[2]="Grade Penilaian";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>

<?php
CLOSE_BOX();
echo close_body();
?>