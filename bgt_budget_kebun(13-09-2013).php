<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';

?>
<script>
pilh=" <? echo $_SESSION['lang']['pilihdata'] ?>";
</script>
<script language="javascript" src="js/zMaster.js"></script>
<script language="javascript" src="js/zTools.js"></script>
<script type="text/javascript" src="js/bgt_budget_kebun.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$optKdbdgt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%SDM%' order by nama asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
        $optKdbdgt.="<option value=".$rOrg2['kodebudget'].">".$rOrg2['nama']."</option>";
}

$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKeg1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sKeg="select distinct kodekegiatan,namakegiatan,kelompok from ".$dbname.".setup_kegiatan where  kelompok='".$rStatus['statusblok']."'  order by kodekegiatan asc";
$sKeg="select distinct kodekegiatan,namakegiatan,kelompok from ".$dbname.".setup_kegiatan where  kelompok in ('PNN','TBM','TM','BBT','TB')  order by kodekegiatan asc";
$qKeg=mysql_query($sKeg) or die(mysql_error());
while($rKeg=mysql_fetch_assoc($qKeg))
{
        if(isset($kegId) and $kegId!='')
    {
        if($rKeg['kelompok']=='TBM' or $rKeg['kelompok']=='TM'  or $rKeg['kelompok']=='PNN') {
                        $optKeg1.="<option value=".$rKeg['kodekegiatan']." ".($rKeg['kodekegiatan']==$kegId?'selected':'').">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
                       
                } else if($rKeg['kelompok']=='TB' or $rKeg['kelompok']=='BBT') {
                        $optKeg.="<option value=".$rKeg['kodekegiatan']." ".($rKeg['kodekegiatan']==$kegId?'selected':'').">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
                }
    }

        else
    {
        if($rKeg['kelompok']=='TBM' or $rKeg['kelompok']=='TM'  or $rKeg['kelompok']=='PNN') {
                        $optKeg1.="<option value=".$rKeg['kodekegiatan'].">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
                } else {
                        $optKeg.="<option value=".$rKeg['kodekegiatan'].">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
                }
    }
}

####################################################################### Title ##
OPEN_BOX('',"<b>".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['kebun']."</b>");
CLOSE_BOX();

################################################################# Setting Tab ##

$contentFrame = array();

################################################################################
###################################################################### Tab LC ##
################################################################################
$contentFrame[0] = OPEN_BOX2();
$contentFrame[0] .= "<fieldset style='float:left;'><legend>".$_SESSION['lang']['entryForm']."</legend> <table border=0 cellpadding=1 cellspacing=1>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type='text' class='myinputtextnumber' id='thnBudget' style='width:150px;' maxlength='4' onkeypress='return angka_doang(event)' onblur='getKodeblok(0,0,0)' /></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['tipe']."</td><td><input type='text' class='myinputtext' disabled value='ESTATE' id='tipeBudget' style=width:150px; /></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['kodeblok']."</td><td><select style='width:150px;' id='kdBlok' onchange=isiLuas(this)>".$optBlok."</select></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['kegiatan']."</td><td><select style='width:150px;' id='kegId' onchange='getSatuan()'>".$optKeg."</select></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['noakun']."</td><td><input type='text' class='myinputtextnumber' id='noAkun' disabled style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td><td><input type='text' class='myinputtextnumber' id='rotThn' style='width:150px;' onkeypress='return tanpa_kutip(event)' value='1' /></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['volume']."</td><td><input type='text' class='myinputtextnumber' id='volKeg'  style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['satuan']."</td><td><input type='text' class='myinputtext' id='satKeg' style='width:150px;' onkeypress='return tanpa_kutip(event)' /></td></tr>";
$contentFrame[0] .= "<tr><td>".$_SESSION['lang']['persen']."</td><td><input type='text' class='myinputtextnumber' id='persen' style='width:137px;' onkeypress='return angka_doang(event)' onblur=ubahvolume(this.value) value='100' />%</td></tr>";
$contentFrame[0] .= "<input type=hidden id=defaultVol>";
$contentFrame[0] .= "<tr><td colspan='2'><button class=\"mybutton\"  id=\"saveData\" onclick='saveData()'>".$_SESSION['lang']['save']."</button><button  class=\"mybutton\"  id=\"newData\" onclick='newData()'>".$_SESSION['lang']['baru']."</button></td></tr>";
$contentFrame[0] .= "</table></fieldset>";
$optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$contentFrame[0] .= CLOSE_BOX2();

$frm[0].="<fieldset><legend>".$_SESSION['lang']['sdm']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudget' style='width:150px;' onchange='jumlahkan(1)'>".$optKdbdgt."</select><input type='hidden' class='myinputtextnumber'  style='width:150px;' id='hkEfektif' /></td></tr>
<tr><td>".$_SESSION['lang']['jhk']."</td><td>:</td><td><input type='text' class='myinputtextnumber' style='width:150px;' id='jmlh_1' onblur='jumlahkan(1)' onkeypress='return angka_doang(event)' value='0' /></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='totBiaya' value='0' onkeypress='return false' /></td></tr>
<tr><td colspan=3>

<button class=mybutton id=btlTmbl name=btlTmbl onclick=saveBudget(1)  >".$_SESSION['lang']['save']."</button></td></tr></table><br /><br />

";
$frm[0].="</fieldset>";

$contentFrame[0] .= OPEN_BOX2();
$contentFrame[0] .= "<div id='listDatHeader' style='display:block'>";
$contentFrame[0] .= "";
$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
$sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipebudget='ESTATE' and kodebudget!='UMUM' order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
    $optTahunBudgetHeader.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
$optBlok="<option value=''>".$_SESSION['lang']['all']."</option>";
$sBlok="select distinct kodeblok,thntnm from ".$dbname.".bgt_blok where kodeblok like '".$_SESSION['empl']['lokasitugas']."%'order by kodeblok asc";
$qBlok=mysql_query($sBlok) or die(mysql_error());
while($rBlok=mysql_fetch_assoc($qBlok))
{
    $optBlok.="<option value='".$rBlok['kodeblok']."'>".$rBlok['kodeblok']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['all']."</option>";
$sAkun="select distinct a.noakun,b.namaakun from ".$dbname.".bgt_budget a
        left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tipebudget='ESTATE' and kodebudget!='UMUM' order by noakun asc";
$qAkun=mysql_query($sAkun) or die(mysql_error($sAkun));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optAkun.="<option value='".$rAkun['noakun']."'>".$rAkun['noakun']."-".$rAkun['namaakun']."</option>";
}

$contentFrame[0] .= "<div>";
$contentFrame[0] .= "<table><tr><td>".$_SESSION['lang']['budgetyear'].": <select id='thnbudgetHeader' style='width:150px;' onchange='ubah_list()'>".$optTahunBudgetHeader."</select></td>
    <td>".$_SESSION['lang']['blok'].":<select id=kdBlokCari style='width:150px;' onchange='ubah_list()'>".$optBlok."</select></td><td>".$_SESSION['lang']['noakun'].":<select id=noakunCari style='width:150px;' onchange='ubah_list()'>".$optAkun."</select></td></tr></table></div>";
$contentFrame[0] .= "<div id='listDatHeader2'>";
$contentFrame[0] .= "<script>dataHeader()</script></div>";
$contentFrame[0] .= "</div>";

$contentFrame[0] .= "<div id='formIsian' style='display:none;'>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataSDM> 
                ";
$frm[0].="</tbody></table></fieldset>";
$optKdbdgtM="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sOrgm="select kodebudget,nama from ".$dbname.".bgt_kode where substr(kodebudget,1,1)='M' order by kodebudget asc";
$sOrgm="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget in('M-311','M-312','M-313','M-312') order by kodebudget asc";
$qOrgm=mysql_query($sOrgm) or die(mysql_error());
while($rOrgm=mysql_fetch_assoc($qOrgm))
{
        $optKdbdgtM.="<option value='".$rOrgm['kodebudget']."'>".$rOrgm['kodebudget']." [".$rOrgm['nama']."]</option>";
}
$frm[1].="<fieldset><legend>".$_SESSION['lang']['material']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetM' style='width:150px;' onchange='getKlmpkbrg()'>".$optKdbdgtM."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td><input type='text' class='myinputtext' id='kdBarang' style='width:150px;' onkeypress='return angka_doang(event)' />&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."' onclick=\"searchBrg('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);\">
    <span id='namaBrg'></span></td>



    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_2' style='width:150px;' onkeypress='return angka_doang(event)' onchange='jumlahkan(2)' />&nbsp;<span id='satuan'></span></td></tr>

            <tr><td>Norma</td><td>:</td><td><input type='text' class='myinputtextnumber' id='normax' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkannorma(this.value)' /></td>

        </tr>

<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHarga' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        


<tr><td colspan=3>
<button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick='saveBudget(2)'   >".$_SESSION['lang']['save']."</button></td></tr></table><br />

<input type=hidden id=prosesBr name=prosesBr value=insert_baru >
";
//$frm[0].="</fieldset>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataBrg>
                ";
$frm[1].="</tbody></table></fieldset>";
##indra

$sOrgm="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget in('M-361','M-362') order by kodebudget asc";
$qOrgm=mysql_query($sOrgm) or die(mysql_error());
//$optKdbdgtL='';
$optKdbdgtL="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($rOrgm=mysql_fetch_assoc($qOrgm))
{
        $optKdbdgtL.="<option value='".$rOrgm['kodebudget']."'>".$rOrgm['kodebudget']." [".$rOrgm['nama']."]</option>";
}

$frm[2].="<fieldset><legend>".$_SESSION['lang']['peralatan']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetL' style='width:150px;' >".$optKdbdgtL."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td><input type='text' class='myinputtext' id='kdBarangL' style='width:150px;' onkeypress='return angka_doang(event)' />&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."' onclick=\"searchBrgL('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmBrgL><button class=mybutton onclick=findBrgL()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerBarangL style=overflow=auto;height=380;width=485></div>',event);\">
    <span id='namaBrgL'></span></td></tr>
    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_3' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkan(3)' />&nbsp;<span id='satuanL'></span></td></tr>

                    <tr><td>Norma</td><td>:</td><td><input type='text' class='myinputtextnumber' id='normay' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkannormay(this.value)' /></td>

        </tr>

<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHargaL' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        


<tr><td colspan=3>
<button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick='saveBudget(3)'   >".$_SESSION['lang']['save']."</button></td></tr></table><br />

<input type=hidden id=prosesBr name=prosesBr value=insert_baru >
";
//$frm[0].="</fieldset>";

$frm[2].="</fieldset>";
$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataTool>
                ";
$frm[2].="</tbody></table></fieldset>";

$sOrgB="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%KONTRAK%' order by nama asc";
//echo $sOrgs;
$qOrgB=mysql_query($sOrgB) or die(mysql_error());
$optKdbdgt_B='';
while($rOrgB=mysql_fetch_assoc($qOrgB))
{
        $optKdbdgt_B.="<option value='".$rOrgB['kodebudget']."'>".$rOrgB['nama']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJns="select noakun,namaakun from ".$dbname.".keu_5akun where detail=1 and tipeakun='BIAYA' order by noakun asc";
$qJns=mysql_query($sJns) or die(mysql_error($conn));
while($rJns=mysql_fetch_assoc($qJns))
{
    $optAkun.="<option value='".$rJns['noakun']."'>".$rJns['noakun']." - [".$rJns['namaakun']."]</option>";
}
$frm[3]="<fieldset><legend>".$_SESSION['lang']['kontrak']."</legend>";
$frm[3].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetK' style='width:150px;' disabled>".$optKdbdgt_B."</select></td></tr>
<tr><td>".$_SESSION['lang']['volume']."</td><td>:</td><td><input type='text' id='volKontrak' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px;' /></td></tr>
<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td><input type='text' id='satKontrak' class='myinputtextnumber' onkeypress='return tanpa_kutip(event)' style='width:150px;' /></td></tr>

<tr><td>Harga Satuan</td><td>:</td><td><input type='text' class='myinputtextnumber' id='hargasat' style='width:150px;' onkeypress='return angka_doang(event)' onblur=jumlahkanK(this.value) value='0' /></td></tr>        

<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totBiayaK' style='width:150px;' onkeypress='return angka_doang(event)' value='0' /></td></tr> 

<tr><td colspan=3>
<button class=mybutton onclick=saveBudget(4) >".$_SESSION['lang']['save']."</button>
<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />
</td></tr>
</table>";

$frm[3].="</fieldset>";
$frm[3].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataLain>
                ";
$frm[3].="</tbody></table></fieldset>";

$sOrgv="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%VHC%' order by nama asc";
//echo $sOrgs;
$qOrgv=mysql_query($sOrgv) or die(mysql_error());
$optKdbdgt_V='';
while($rOrgv=mysql_fetch_assoc($qOrgv))
{
        $optKdbdgt_V.="<option value='".$rOrgv['kodebudget']."'>".$rOrgv['nama']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJns="select noakun,namaakun from ".$dbname.".keu_5akun where detail=1 and tipeakun='BIAYA' order by noakun asc";
$qJns=mysql_query($sJns) or die(mysql_error($conn));
while($rJns=mysql_fetch_assoc($qJns))
{
    $optAkun.="<option value='".$rJns['noakun']."'>".$rJns['noakun']." - [".$rJns['namaakun']."]</option>";
}
$optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$frm[4]="<fieldset><legend>".$_SESSION['lang']['kndran']."</legend>";
$frm[4].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetV' style='width:150px;' disabled>".$optKdbdgt_V."</select></td></tr>



<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td><select id='kdVhc' style='width:150px;' onchange='ambil_biaya()'>".$optVhc."</select></td></tr>

<tr>
        <td>Norma</td>
        <td>:</td>
        <td><input type='text' class='myinputtext' id=rasiodetail onchange=ambil()  style='width:150px;' onkeypress='return angka_doang(event)'  /></td>

</tr>


<tr><td>Volume</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlhJam' style='width:150px;' onkeypress='return angka_doang(event)','return false'   onblur='ambil_biaya()' disabled /></td></tr>        
<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td><input type='text' id='satVhc' class='myinputtextnumber' disabled value='HM/KM' style='width:150px;' /></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  id='totBiayaKend' style='width:150px;' onkeypress='return false' value=0 /></td></tr>        


<tr><td colspan=3>
<button class=mybutton onclick=saveBudget(5) >".$_SESSION['lang']['save']."</button>

</td></tr>
</table>";//indraaaa pending di sini

$frm[4].="</fieldset>";
$frm[4].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>         
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
           <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataKend>
                ";
$frm[4].="</tbody></table></fieldset>";

$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
$frm[5]="<fieldset><legend>Sebaran</legend>
    <table><tr>";
    foreach($arrBln as $brsBulan =>$listBln)
        {
            $frm[5].="<td>".$listBln."</td>";
        }

$sNamaAkun58="select distinct noakun,namaakun  from ".$dbname.".keu_5akun order by namaakun asc";
$qNamaAkun58=mysql_query($sNamaAkun58) or die(mysql_error());
while($rNamaAkun58=  mysql_fetch_assoc($qNamaAkun58))
{
    $namaAkun58[$rNamaAkun58['noakun']]=$rNamaAkun58['namaakun'];
}


$optNoakunData58="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOptNoakun58="select distinct noakun from ".$dbname.".bgt_budget where tipebudget='ESTATE' and kodebudget!='UMUM' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by noakun asc";
$qOptNoakun58=mysql_query($sOptNoakun58) or die(mysql_error($sOptNoakun58));
while($rOptNoakun58=mysql_fetch_assoc($qOptNoakun58))
{
    $optNoakunData58.="<option value='".$rOptNoakun58['noakun']."'>".$rOptNoakun58['noakun']."-".$namaAkun58[$rOptNoakun58['noakun']]."</option>";
}
$frm[5].="<td></td></tr>
    <tr>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss1 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss2 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss3 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss4 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss5 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss6 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss7 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss8 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss9 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss10 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss11 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss12 value=1></td>
    <td><img src=images/clear.png onclick=bersihkanDonk() style='height:30px;cursor:pointer' title='bersihkan'></td>
    </tr>
    </table>  ";


    $frm[5].="<table><tr class=rowcontent><td>".$_SESSION['lang']['kodeblok']."</td><td><select id=kdblokSebaran onchange='loadDetailTotal()'>".$optBlok."</select></td><td>".$_SESSION['lang']['noakun']."</td><td><select id=kdNoakunData onchange='loadDetailTotal()'>".$optNoakunData58."</select></td><td>Goto Page</td><td id='pagingDrop'>&nbsp;<select id='pageSebaran' onchange='loadDetailTotal()'><option value=''></option></select><span id=awalPageSebaran></span> &nbsp;".$_SESSION['lang']['dari']." &nbsp;<span id=totalPageSebaran></span></td></tr></table>";

   $frm[5].="<div id='detailDataSebaran'style=overflow:auto;width:1030px;height:300px;>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%>
            <thead>
            <tr class=rowheader>
            <td></td>               
            <td>No</td>
            <td>".$_SESSION['lang']['kodeblok']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['namakegiatan']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['total']."</td>";
foreach($arrBln as $brsBulan =>$listBln)
{
    $frm[5].="<td>".$listBln." Rp</td>";
}

     $frm[5].="<td>Action</td>
            </tr>
            </thead><tbody id=containDataTotal>
                ";
$frm[5].="</tbody></table></div></fieldset>";


//========================
$hfrm[0]=$_SESSION['lang']['sdm'];
$hfrm[1]=$_SESSION['lang']['material'];
$hfrm[2]=$_SESSION['lang']['peralatan'];
$hfrm[3]=$_SESSION['lang']['kontrak'];
$hfrm[4]=$_SESSION['lang']['kndran'];
$hfrm[5]="Sebaran";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
$contentFrame[0] .= drawTab('FRM',$hfrm,$frm,100,1100,true);
//===============================================	
$contentFrame[0] .= "</div>";
$contentFrame[0] .= CLOSE_BOX2();
################################################################################
##################################################################### /Tab LC ##
################################################################################

################################################################################
############################################################ Tab Pemeliharaan ##
################################################################################
$optAfd = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "induk='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING'");

$contentFrame[1] = OPEN_BOX2();
$contentFrame[1] .= "<fieldset style='float:left;clear:right'><legend>".$_SESSION['lang']['entryForm']."</legend> <table border=0 cellpadding=1 cellspacing=1>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type='text' class='myinputtextnumber' id='thnBudget2' style='width:150px;' maxlength='4' onkeypress='return angka_doang(event)' /></td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['tipe']."</td><td><input type='text' class='myinputtext' disabled value='ESTATE' id='tipeBudget' style=width:150px; /></td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['afdeling']."</td><td>".makeElement('afd','select','',array('style'=>'width:150px;'),$optAfd)."</td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['tahuntanam']."</td><td>".makeElement('tahuntanam','textnum','',array('style'=>'width:150px;','maxlength'=>4))."</td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['kegiatan']."</td><td><select style='width:150px;' id='kegId2' onchange='getSatuan(\"rawat\")'>".$optKeg1."</select></td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['noakun']."</td><td><input type='text' class='myinputtextnumber' id='noAkun2' disabled style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td><td><input type='text' class='myinputtextnumber' id='rotThn2' style='width:150px;' onkeypress='return tanpa_kutip(event)' value='1' /></td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['persen']."</td><td><input type='text' maxlength='3' class='myinputtextnumber' id='persen2' style='width:135px;' onkeypress='return angka_doang(event)' onblue=ubahvolume(this.value) value=100 />&nbsp;%</td></tr>";
$contentFrame[1] .= "<tr><td>".$_SESSION['lang']['satuan']."</td><td><input type='text' class='myinputtext' id='satKeg2' style='width:150px;' onkeypress='return tanpa_kutip(event)' /></td></tr>";
$contentFrame[1] .= "<tr><td colspan='2'><button class=\"mybutton\"  id=\"saveData2\" onclick='saveDataRawat()'>".$_SESSION['lang']['save']."</button></td></tr>";
$contentFrame[1] .= "</table></fieldset>";

## Detail Tab
$detailHeader = array($_SESSION['lang']['list'],$_SESSION['lang']['sebaran']);
$detailContent = array();
$detailContent[0] = "<div id='rawatList'></div>";

##################################################################### Sebaran ##
$detailContent[1] = "<fieldset><legend>Sebaran</legend>".
    "<table><tr>";
$detailContent[1] .= "<tr><td colspan=2>Tahun Budget</td>";
$detailContent[1] .= "<td colspan=9>".
	makeElement("tahunBgt3","textnum",date('Y'),array('maxlength'=>4,'style'=>'width: 70px')).
	makeElement("tahunBgt3Btn","btn",'Filter',array('onclick'=>"sebaranPemeliharaan()"))."</td></tr>";
$detailContent[1] .= "<tr>";
foreach($arrBln as $brsBulan =>$listBln) {
    $detailContent[1].="<td>".$listBln."</td>";
}

$detailContent[1].="<td></td></tr>
    <tr>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s1 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s2 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s3 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s4 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s5 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s6 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s7 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s8 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s9 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s10 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s11 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=s2s12 value=1></td>
    <td><img src=images/clear.png onclick=bersihkanAja() style='height:30px;cursor:pointer' title='bersihkan'></td>
    </tr>
    </table>  ";

//$detailContent[1].="<table><tr class=rowcontent><td>".$_SESSION['lang']['kodeblok']."</td><td><select id=kdblokSebaran onchange='loadDetailTotal()'>".$optBlok."</select></td><td>".$_SESSION['lang']['noakun']."</td><td><select id=kdNoakunData onchange='loadDetailTotal()'>".$optNoakunData58."</select></td><td>Goto Page</td><td id='pagingDrop'>&nbsp;<select id='pageSebaran' onchange='loadDetailTotal()'><option value=''></option></select><span id=awalPageSebaran></span> &nbsp;".$_SESSION['lang']['dari']." &nbsp;<span id=totalPageSebaran></span></td></tr></table>";

$detailContent[1].="<div id='detailDataSebaran2' style='overflow:auto;width:1030px;height:300px;'>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%>
                <thead>
                <tr class=rowheader>
                <td></td>               
                <td>No</td>
                <td>Tahun Budget</td>
                <td>".$_SESSION['lang']['kodeblok']."</td>
                <td>".$_SESSION['lang']['kodeanggaran']."</td>
                <td>".$_SESSION['lang']['namakegiatan']."</td>
                <td>".$_SESSION['lang']['namabarang']."</td>
                <td>".$_SESSION['lang']['kodevhc']."</td>
                <td>".$_SESSION['lang']['total']."</td>";
foreach($arrBln as $brsBulan =>$listBln) {
    $detailContent[1].="<td>".$listBln." Rp</td>";
}

$sLoad="select * from ".$dbname.".bgt_budget_detail a left join ".$dbname.".setup_kegiatan b ".
    "on a.kegiatan=b.kodekegiatan where b.kelompok in ('TB','LC','PNN','TBM','TM') and a.kodeorg like '".
        $_SESSION['empl']['lokasitugas']."%' and kodebudget!='UMUM' and tipebudget='ESTATE' and tutup=0 and tahunbudget=".date('Y')." limit 20";
		
		//exit("Error:$sLoad");
$resSebaran = fetchData($sLoad);

## Option
$keg = '';$brg='';
foreach($resSebaran as $row) {
        if($keg!='') {
                $keg.=',';
        }
        if($brg!='') {
                $brg.=',';
        }
        $keg.="'".$row['kegiatan']."'";
        $brg.="'".$row['kodebarang']."'";
}
if($keg=='') {
        $optNmKeg = array();
} else {
        $optNmKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
                "kodekegiatan in (".$keg.")");
}
if($brg=='') {
        $optNmBrg = array();
} else {
        $optNmBrg = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
                "kodebarang in (".$brg.")",'0',true);
}

$detailContent[1].="<td>Action</td></tr>
        </thead><tbody id=containDataTotal>";
foreach($resSebaran as $key=>$row) {
        $detailContent[1].="<tr class=rowcontent style='cursor:pointer;' id=barisSebaran".$key.">";
//        $dtClik="onclick=\"getForm('Sebaran','<fieldset style=\'width:520px;height:400px;\'>".
//                "<legend>Sebaran Per Bulan</legend><div id=containerForm style=\'overflow:auto;height:450px;".
//                "width:480px\'></div><input type=hidden id=keyId2 value=\'".$row['kunci']."\'></fieldset>',".
//                $row['rupiah'].",".$row['jumlah'].",'".$row['kodebudget']."',event,true);\"";

        $detailContent[1].="<td><input type=checkbox onclick=sebarkanRawat('".$row['kunci']."',".
                $key.",this,".$row['rupiah'].",".$row['jumlah']."); title='Sebarkan sesuai proporsi diatas'></td>";
        $detailContent[1].="<td >".($key+1)."</td>";
        $detailContent[1].="<td align='center' ".$dtClik.">".$row['tahunbudget']."</td>";
        $detailContent[1].="<td align='center' ".$dtClik.">".$row['kodeorg']."</td>";
        $detailContent[1].="<td align='center' ".$dtClik.">".$row['kodebudget']."</td>";
        $detailContent[1].="<td align='center' ".$dtClik.">".$optNmKeg[$row['kegiatan']]."</td>";
        $detailContent[1].="<td align='center' ".$dtClik.">".$optNmBrg[$row['kodebarang']]."</td>";
        $detailContent[1].="<td align='right' ".$dtClik.">".$row['kodevhc']."</td>";
        $detailContent[1].="<td align='center' ".$dtClik.">".number_format($row['rupiah'],2)."</td>";
        foreach($arrBln as $brsBln =>$listData) {
                if(strlen($brsBln)<2) {
                        $b="0".$brsBln;
                } else {
                        $b=$brsBln;
                }
                $detailContent[1].="<td align='right'>".number_format($row['rp'.$b],2)."</td>";
        }

        $detailContent[1].="<td align=center  style='cursor:pointer;'><img src=\"images/zoom.png\"".
                " class=\"resicon\" title='sebarang_".$row['kunci']."' ".$dtClik." /></td>";
        $detailContent[1].="</tr>";
}
$detailContent[1].="</tbody></table></div></fieldset>";
#################################################################### /Sebaran ##

$contentFrame[1] .= "<fieldset style='float:left;clear:left'><legend>".$_SESSION['lang']['entryForm']."</legend><div id='rawatDetail'>";
$contentFrame[1] .= drawTab('FRM2',$detailHeader,$detailContent,100,'100%',true);
$contentFrame[1] .= "</div></fieldset>";
$contentFrame[1] .= CLOSE_BOX2();

################################################################################
########################################################### /Tab Pemeliharaan ##
################################################################################

################################################################################
##################################### Tab Pengangkuatan dan Perawatan Mekanis ##
################################################################################
$contentFrame[2] = OPEN_BOX2();

$contentFrame[2] .= "<fieldset style='float:left;'><legend>".$_SESSION['lang']['entryForm']."</legend> <table border=0 cellpadding=1 cellspacing=1>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type='text' class='myinputtextnumber' id='thnBudget3' style='width:150px;' maxlength='4' onkeypress='return angka_doang(event)' onblur='getKodeblok3(0,0,0)' /></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['tipe']."</td><td><input type='text' class='myinputtext' disabled value='ESTATE' id='tipeBudget3' style=width:150px; /></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['kodeblok']."</td><td><select style='width:150px;' id='kdBlok3' onchange=isiLuas3(this)>".$optBlok."</select></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['kegiatan']."</td><td><select style='width:150px;' id='kegId3' onchange='getSatuan3()'>".$optKeg1."</select></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['noakun']."</td><td><input type='text' class='myinputtextnumber' id='noAkun3' disabled style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td><td><input type='text' class='myinputtextnumber' id='rotThn3' style='width:150px;' onkeypress='return tanpa_kutip(event)' value='1' /></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['volume']."</td><td><input type='text' class='myinputtextnumber' id='volKeg3'  style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['satuan']."</td><td><input type='text' class='myinputtext' id='satKeg3' style='width:150px;' onkeypress='return tanpa_kutip(event)' /></td></tr>";
$contentFrame[2] .= "<tr><td>".$_SESSION['lang']['persen']."</td><td><input type='text' class='myinputtextnumber' id='persen3' style='width:137px;' onkeypress='return angka_doang(event)' onblur=ubahvolume3(this.value) value='100' />%</td></tr>";
$contentFrame[2] .= "<input type=hidden id=defaultVol3>";
$contentFrame[2] .= "<tr><td colspan='2'><button class=\"mybutton\"  id=\"saveData3\" onclick='saveData3()'>".$_SESSION['lang']['save']."</button><button  class=\"mybutton\"  id=\"newData33\" onclick='newData3()'>".$_SESSION['lang']['baru']."</button></td></tr>";
$contentFrame[2] .= "</table></fieldset>";
$optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$contentFrame[2] .= CLOSE_BOX2();

$contentFrame[2] .= OPEN_BOX2();
$contentFrame[2] .= "<div id='listDatHeader3' style='display:block'>";
$contentFrame[2] .= "";

$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
$sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipebudget='ESTATE' and kodebudget!='UMUM' order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
    $optTahunBudgetHeader.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
$optBlok="<option value=''>".$_SESSION['lang']['all']."</option>";
$sBlok="select distinct kodeblok,thntnm from ".$dbname.".bgt_blok where kodeblok like '".$_SESSION['empl']['lokasitugas']."%'order by kodeblok asc";
$qBlok=mysql_query($sBlok) or die(mysql_error());
while($rBlok=mysql_fetch_assoc($qBlok))
{
    $optBlok.="<option value='".$rBlok['kodeblok']."'>".$rBlok['kodeblok']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['all']."</option>";
$sAkun="select distinct a.noakun,b.namaakun from ".$dbname.".bgt_budget a
        left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tipebudget='ESTATE' and kodebudget!='UMUM' order by noakun asc";
$qAkun=mysql_query($sAkun) or die(mysql_error($sAkun));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optAkun.="<option value='".$rAkun['noakun']."'>".$rAkun['noakun']."-".$rAkun['namaakun']."</option>";
}

$contentFrame[2] .= "<div>";
$contentFrame[2] .= "<table><tr><td>".$_SESSION['lang']['budgetyear'].": <select id='thnbudgetHeader3' style='width:150px;' onchange='ubah_list3()'>".$optTahunBudgetHeader."</select></td>
    <td>".$_SESSION['lang']['blok'].":<select id=kdBlokCari3 style='width:150px;' onchange='ubah_list3()'>".$optBlok."</select></td><td>".$_SESSION['lang']['noakun'].":<select id=noakunCari3 style='width:150px;' onchange='ubah_list3()'>".$optAkun."</select></td></tr></table></div>";
$contentFrame[2] .= "<div id='listDatHeader4'>";
$contentFrame[2] .= "</div>";
$contentFrame[2] .= "</div>";

$contentFrame[2] .= "<div id='formIsian3' style='display:none;'>";

#================================================================================
$frm=array();
$sOrgv="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%VHC%' order by nama asc";
//echo $sOrgs;
$qOrgv=mysql_query($sOrgv) or die(mysql_error());
$optKdbdgt_V='';
while($rOrgv=mysql_fetch_assoc($qOrgv))
{
        $optKdbdgt_V.="<option value='".$rOrgv['kodebudget']."'>".$rOrgv['nama']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJns="select noakun,namaakun from ".$dbname.".keu_5akun where detail=1 and tipeakun='BIAYA' order by noakun asc";
$qJns=mysql_query($sJns) or die(mysql_error($conn));
while($rJns=mysql_fetch_assoc($qJns))
{
    $optAkun.="<option value='".$rJns['noakun']."'>".$rJns['noakun']." - [".$rJns['namaakun']."]</option>";
}
$optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$q = selectQuery($dbname,'vhc_5master','kodevhc');
$resVhc = '';

$frm[0]="<fieldset><legend>".$_SESSION['lang']['kndran']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetV3' style='width:150px;' disabled>".$optKdbdgt_V."</select></td></tr>



<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td><select id='kdVhc3' style='width:150px;' onchange='ambil_biaya()'>".$optVhc."</select></td></tr>

<tr>
        <td>Norma</td>
        <td>:</td>
        <td><input type='text' class='myinputtext' id=rasiodetail3 onchange=ambil3()  style='width:150px;' onkeypress='return angka_doang(event)'  /></td>

</tr>


<tr><td>Volume</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlhJam3' style='width:150px;' onkeypress='return angka_doang(event)','return false'   onblur='ambil_biaya()' disabled /></td></tr>        
<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td><input type='text' id='satVhc3' class='myinputtextnumber' disabled value='HM/KM' style='width:150px;' /></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  id='totBiayaKend3' style='width:150px;' onkeypress='return false' value=0 /></td></tr>        


<tr><td colspan=3>
<button class=mybutton onclick=saveBudget(6) >".$_SESSION['lang']['save']."</button>

</td></tr>
</table>";//indraaaa pending di sini

$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>         
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
           <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataKend3>
                ";
$frm[0].="</tbody></table></fieldset>";

$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");

//========================
$hfrm=array();
$hfrm[0]=$_SESSION['lang']['kndran'];
//$hfrm[1]="Sebaran";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
$contentFrame[2] .= drawTab('FRM2',$hfrm,$frm,100,1100,true);
//===============================================	
$contentFrame[2] .= "</div>";
$contentFrame[2] .= CLOSE_BOX2();
################################################################################
##################################### /Tab Pengangkutan dan Perawatan Mekanis ##
################################################################################

################################################################################
############################################################ Tab Tutup Budget ##
################################################################################
$optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='ESTATE' and tutup=0 order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
 $optThnTtp.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
//echo $optThnTtp;

$contentFrame[3] = "<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>
    <div><table><tr><td>".$_SESSION['lang']['budgetyear']."</td><td><select id='thnBudgetTutup' style='width:150px'>".$optThnTtp."</select></td></tr>";
$contentFrame[3] .= "<tr><td colspan=2 align=center><button class=\"mybutton\"  id=\"saveData\" onclick='closeBudget()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
$contentFrame[3] .= "</div></fieldset>";
################################################################################
########################################################### /Tab Tutup Budget ##
################################################################################
//echo mysql_error();

$headFrame = array("LC","Pemeliharaan","Pengangkutan dan Perawatan Mekanis","Tutup");
drawTab('FRM1',$headFrame,$contentFrame,150,'100%');
echo close_body();
?>