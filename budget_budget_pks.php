<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
include('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';

?>
<script>
    var plh='';
    plh="<?php echo $_SESSION['lang']['pilihdata'];?>";
</script>
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/zTools.js"></script>
<script type="text/javascript" src="js/budget_budget_pks.js"></script>
<?php


//no akun di tab material
$optnoakun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$in="select *  from ".$dbname.".keu_5akun where left(noakun,2) in('63','64') and detail=1";
//echo $in;
$dr=mysql_query($in);
while($ra=mysql_fetch_assoc($dr))
{
	$optnoakun.="<option value='".$ra['noakun']."'>'".$ra['namaakun']."' ";
}




//pilihan station
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where (tipe='STATION') and induk = '".$_SESSION['empl']['lokasitugas']."'
        order by kodeorganisasi
        ";
    $optstation="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optstation.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }

//pilihan pabrik
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
        where (tipe='PABRIK') and kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."'
        order by kodeorganisasi
        ";
    $optpabrik="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optpabrik.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
    }
    
//pilihan tahun tutup
    $str="select distinct tahunbudget from ".$dbname.".bgt_budget
        where tutup = '0' and kodebudget != 'UMUM' and tipebudget = 'MILL' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'
        order by tahunbudget desc
        ";
    //echo $str;
    $opttahun="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    $optmesin="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//pilihan kodebudget tab0
//    $str="select kodebudget,nama from ".$dbname.".bgt_kode
//        where kodebudget like 'SDM%'
//        ";
//    $optkodebudget0="";
//    $res=mysql_query($str);
//    while($bar=mysql_fetch_object($res))
//    {
//        $optkodebudget0.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
//    }

//pilihan kodebudget tab0
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'EXPL%'
        ";
    $optkodebudget0="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optkodebudget0.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }

//pilihan kodebudget tab1

/* $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'M%'x`*/

		 $str="select kodebudget,nama from ".$dbname.".bgt_kode WHERE kodebudget NOT LIKE  'EXPL%'
AND kodebudget NOT LIKE  'SDM%' and kodebudget not in('SUPERVISI','M-377','M-378','M-381','313','311') order by kodebudget asc
        ";
    $optmaterial1="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $optmaterial1.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }
    $optjenis1="";
    $optjenis1.="<option value='consumables'>Consumables</option>";
    $optjenis1.="<option value='recurrent'>Recurrent</option>";
    $optjenis1.="<option value='nonrecurrent'>Non Recurrent</option>";

    
//pilihan kodebudget tab2    
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
        where kodebudget like 'TOOL%'
        ";
    $opttool2="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $opttool2.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }
	
	
    
//pilihan kodebudget tab3    
    $str="select kodebudget,nama from ".$dbname.".bgt_kode
                    where kodebudget like 'VHC%'
                    ";
    $optkode3="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $optkode3.="<option value='".$bar->kodebudget."'>".$bar->nama."</option>";
    }

//pilihan vhc tab3    
    $optvhc3="";
    
//atas
OPEN_BOX('',"<b>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['pabrik']."</b>");
echo"<br /><fieldset style='float:left;width:325px;'><legend>".$_SESSION['lang']['form']."</legend><table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['tipeanggaran']." </td><td>:</td><td>
        <input type=text class=myinputtext id=tipebudget name=tipebudget onkeypress=\"return angka_doang(event);\" maxlength=2 disabled=true style=width:150px; value=\"MILL\"/></td>
        
    </tr>
    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
        <input type=text class=myinputtext id=tahunbudget name=tahunbudget  onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td>
    
    </tr>
    <tr><td>".$_SESSION['lang']['station']."</td><td>:</td><td colspan=3>
        <select name=station id=station onchange=\"load_mesin();\" style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optstation."</select></td></tr>
    <tr><td>".$_SESSION['lang']['mesin']."</td><td>:</td><td colspan=3>
        <select name=mesin id=mesin style='width:150px;'>".$optmesin."</select></td></tr>
		
	<tr>
		<td>Jam Operasional Setahun</td>
		<td>:</td>
		<td><input type=text class=myinputtext id=jos name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=6 style=width:150px; /></td>
	</tr>	
		
    <tr>
		<td colspan=2></td>
		<td>
        <button class=mybutton id=simpan name=simpan onclick=prosesSimpan()>".$_SESSION['lang']['save']."</button>
        <button class=mybutton id=baru name=baru onclick=prosesBaru()>".$_SESSION['lang']['baru']."</button>
        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >
    </td></tr>
	</table></fieldset>
	
	<br />

	
	
    <fieldset style='width:300px;'><legend>".$_SESSION['lang']['tutup']."</legend>
        <table>
        <tr><td>".$_SESSION['lang']['pabrik']." </td><td>:</td><td>
        <select name=pabrik id=pabrik style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optpabrik."</select></td>
        </tr>
        <tr><td>".$_SESSION['lang']['budgetyear']." </td><td>:</td><td>
        <select name=tahuntutup id=tahuntutup style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</opion>".$opttahun."</select></td></tr>
        <tr><td colspan=3 align=center>
        <button class=mybutton id=tutup name=tutup onclick=prosesTutup()>".$_SESSION['lang']['close']."</button></td></tr>
        </table></fieldset><br /><br /><br /><br />";

//tab0
$frm[0].="<fieldset id=tab0 disabled=true><legend>".$_SESSION['lang']['eksploitasi']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
        <select id=kodebudget0 onchange=\"bersihkan(0);\" name=kodebudget0 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optkodebudget0."</select></td></tr>
    <tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>
        <input type=text class=myinputtext id=jumlahpertahun0 name=jumlahpertahun0 onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; /></td></tr>
    <tr><td colspan=3>
        <button class=mybutton id=simpan0 name=simpan0 onclick=simpan0()>".$_SESSION['lang']['save']."</button>
        <input type=hidden id=tersembunyi0 name=tersembunyi0 value=tersembunyi >
    </td></tr></table>";
$frm[0].="</fieldset>";
//box dalam tab0, daftar table
$frm[0].="<fieldset><legend>".$_SESSION['lang']['list']."</legend>    
<div id=container0></div>
    ";
$frm[0].="</fieldset>";



//tab 1
$frm[1].="<fieldset id=tab1 disabled=true><legend>".$_SESSION['lang']['material']."</legend>";
$frm[1].="<table cellspacing=1 border=0><thead>
    </thead>
	<tr>
		<td>No Akun</td>
		<td>:</td>
		<td><select id=akun style=\"width:200px;\" >".$optnoakun."</select></td>
	</tr>
	
    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
        <select id=kodebudget1 onchange=\"bersihkan(1);\" name=kodebudget1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optmaterial1."</select></td></tr>
    <tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td>
        <input type=text class=myinputtext id=kodebarang1 name=kodebarang1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>
        <input type=\"image\" id=search1 disabled=true src=images/search.png class=dellicon title=".$_SESSION['lang']['find']." onclick=\"searchBrg(1,'".$_SESSION['lang']['findBrg']."','<fieldset><legend>".$_SESSION['lang']['findnoBrg']."</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg(1)>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor >',event)\";>    
        <label id=namabarang1></label></td></tr>
    <tr><td>".$_SESSION['lang']['jenis']."</td><td>:</td><td>
        <select id=jenis1 name=jenis1 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optjenis1."</select></td></tr>
    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td>
        <input type=text class=myinputtext onblur=\"jumlahkan1();\" id=jumlah1 name=jumlah1 onkeypress=\"return angka_doang(event);\" maxlength=10 style=width:150px; disabled=true/>
        <label id=satuan1></td></tr>
    <tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td>
        <input type=text class=myinputtext id=totalharga1 name=totalharga1 onkeypress=\"return false;\" maxlength=10 style=width:150px; /></td></tr>
    <tr><td colspan=3>
        <button class=mybutton id=simpan1 name=simpan1 onclick=simpan1()>".$_SESSION['lang']['save']."</button>
        <input type=hidden id=regional1 name=regional1 value=>
    </td></tr></table>";
$frm[1].="</fieldset>";
//box dalam tab1, daftar table
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
<div id=container1></div>    
    ";
$frm[1].="</fieldset>";

//tab2


##ambil rp jam service pertahun
	
	//$ind="select rpperjam from ".$dbname.".bgt_biaya_ws_per_jam where tahunbudget='".$tahunbudget."' ";


$frm[2]="<fieldset id=tab2 disabled=true><legend>".$_SESSION['lang']['pemeliharaan']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
        <input type=text class=myinputtext id=kodebudget2 name=kodebudget2 value=\"PKSM\" maxlength=10 style=width:150px; disabled=true /></td></tr>
    <tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>
        <input type=text class=myinputtext id=jumlahpertahun2 name=jumlahpertahun2  onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; /></td></tr>
	<tr>
		<td>Jam Service Pertahun</td>
		<td>:</td>
		<td><input type=text class=myinputtext id=jsp name=jsp value=''  onkeypress=\"return angka_doang(event);\"  style=width:150px; /></td>
	</tr>	
		
		
    <tr><td colspan=3>
        <button class=mybutton id=simpan2 name=simpan2 onclick=simpan2()>".$_SESSION['lang']['save']."</button>
        <input type=hidden id=tersembunyi2 name=tersembunyi2 value=tersembunyi >
    </td></tr></table>";
	
	
	
	
$frm[2].="</fieldset>";
//box dalam tab0, daftar table
$frm[2].="<fieldset><legend>".$_SESSION['lang']['list']."</legend>    
<div id=container2></div>
    ";
$frm[2].="</fieldset>";

//tab3
$frm[3]="<fieldset id=tab3 disabled=true><legend>".$_SESSION['lang']['abkend']."</legend>";
$frm[3].="<table cellspacing=1 border=0><thead>
    </thead>
    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
        <select id=kodebudget3 name=kodebudget3 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optkode3."</select></td></tr>
    <tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td>
        <select id=kodevhc3 onblur=\"jumlahkan3();\" name=kodevhc3 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optvhc3."</select>
            </td></tr>
    <tr><td>".$_SESSION['lang']['jmljamkerja']."</td><td>:</td><td>
        <input type=text class=myinputtext id=jumlahjam3 name=jumlahjam3 onblur=\"jumlahkan3();\" onkeypress=\"return angka_doang(event);\" maxlength=15 style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td>
        <input type=text class=myinputtext id=satuan3 name=satuan3 value=\"jam\" maxlength=15 style=width:150px; disabled=true/></td></tr>
    <tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td>
        <input type=text class=myinputtext id=totalbiaya3 name=totalbiaya3 onkeypress=\"return false;\" maxlength=15 style=width:150px; /></td></tr>
    <tr><td colspan=3>
        <button class=mybutton id=simpan3 name=simpan3 onclick=simpan3()>".$_SESSION['lang']['save']."</button>
        <input type=hidden id=regional3 name=regional3 value=>
    </td></tr></table>";
$frm[3].="</fieldset>";
//box dalam tab3, daftar table
$frm[3].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
<div id=container3></div>    
    ";
$frm[3].="</fieldset>";

/*** Tab Kontrak **********************************************************/
$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',"noakun like '632%' and detail=1",'2');
$cont4 = "<fieldset id=tab5 disabled><legend>Kontrak</legend>";
$cont4 .= "<table><tr><td>Akun</td><td>:</td><td>".
	makeElement('noakun5','select','',array(),$optAkun)."</td></tr>";
$cont4 .= "<tr><td>Jumlah (Rp.)</td><td>:</td><td>".
	makeElement('jumlah5','textnum','0',array('style'=>'width: 100px'))."</td></tr>";
$cont4 .= "<tr><td colspan=2>".makeElement('simpan5','btn',$_SESSION['lang']['save'],array('onclick'=>'simpan5()'))."</td></tr></table>";
$cont4 .= "</fieldset>";
$cont4 .="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
<div id=container5></div></fieldset>";
$frm[4]=$cont4;

/*** /Tab Kontrak *********************************************************/

//tab4
$frm[5]="<fieldset id=tab4 disabled=true><legend>".$_SESSION['lang']['sebaran']."</legend><div style=overflow:auto;width:100%;height:300px; id=container4></div>";
$frm[5].="</fieldset>";

//========================
//tab title
$hfrm[0]=$_SESSION['lang']['eksploitasi'];
$hfrm[1]=$_SESSION['lang']['material'];
$hfrm[2]=$_SESSION['lang']['pemeliharaan'];
$hfrm[3]=$_SESSION['lang']['abkend'];
$hfrm[4]=$_SESSION['lang']['kontrak'];
$hfrm[5]=$_SESSION['lang']['sebaran'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>

<?php
CLOSE_BOX();

echo close_body();
?>